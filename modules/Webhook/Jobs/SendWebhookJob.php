<?php

namespace Modules\Webhook\Jobs;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use Modules\Webhook\Models\WebhookDelivery;
use Modules\Webhook\Services\WebhookSignature;

class SendWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    const HTTP_TIMEOUT = 10;

    public int $website_id;
    public int $delivery_id;

    public $tries = 6;
    public $backoff = [60, 600, 3600, 21600, 86400];
    public $timeout = 30;

    public function __construct(int $website_id, int $delivery_id)
    {
        $this->website_id = $website_id;
        $this->delivery_id = $delivery_id;
    }

    public function handle()
    {
        $this->switchTenant();

        $delivery = WebhookDelivery::with('subscription')->find($this->delivery_id);

        if (!$delivery || $delivery->status === WebhookDelivery::STATUS_SUCCESS) {
            return;
        }

        $subscription = $delivery->subscription;

        if (!$subscription || !$subscription->is_active) {
            return;
        }

        // El body se firma tal cual se envía: el receptor verifica contra el crudo
        $body = json_encode($delivery->payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $delivery->increment('attempts');

        try {
            $client = new Client([
                'timeout' => self::HTTP_TIMEOUT,
                'connect_timeout' => self::HTTP_TIMEOUT,
                'allow_redirects' => false,
                'http_errors' => false,
            ]);

            $response = $client->post($subscription->url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'FacturaloPeru-Webhooks/1.0',
                    'X-Webhook-Event' => $delivery->event,
                    'X-Webhook-Id' => $delivery->event_id,
                    'X-Webhook-Signature' => WebhookSignature::sign($body, $subscription->secret),
                ],
                'body' => $body,
            ]);

            $code = $response->getStatusCode();
            $responseBody = (string) $response->getBody();

            if ($code >= 200 && $code < 300) {
                $delivery->markSuccess($code, $responseBody);
                $subscription->resetFailures();

                return;
            }

            $delivery->update([
                'response_code' => $code,
                'response_body' => Str::limit($responseBody, WebhookDelivery::RESPONSE_BODY_LIMIT),
                'error_message' => "El receptor respondió HTTP {$code}",
            ]);

            throw new Exception("Webhook delivery {$delivery->id}: el receptor respondió HTTP {$code}");
        } catch (TransferException $e) {
            // error de red: timeout, DNS, conexión rechazada, etc.
            $delivery->update([
                'error_message' => Str::limit($e->getMessage(), WebhookDelivery::RESPONSE_BODY_LIMIT),
            ]);

            throw new Exception("Webhook delivery {$delivery->id}: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Se ejecuta al agotar los reintentos. Corre en un proceso del worker
     * sin contexto, por eso vuelve a hacer el switch de tenant.
     */
    public function failed(\Throwable $exception)
    {
        $this->switchTenant();

        $delivery = WebhookDelivery::with('subscription')->find($this->delivery_id);

        if (!$delivery) {
            return;
        }

        $delivery->markFailed($delivery->response_code, $exception->getMessage());

        if ($delivery->subscription) {
            $delivery->subscription->registerFailure();
        }
    }

    private function switchTenant(): void
    {
        $website = Website::find($this->website_id);

        if (!$website) {
            throw new Exception("Webhook: website {$this->website_id} no encontrado");
        }

        app(Environment::class)->tenant($website);
    }
}
