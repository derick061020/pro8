<?php

namespace Modules\Webhook\Services;

use Hyn\Tenancy\Environment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Webhook\Jobs\SendWebhookJob;
use Modules\Webhook\Models\WebhookDelivery;
use Modules\Webhook\Models\WebhookSubscription;

class WebhookDispatcher
{
    const API_VERSION = '1.0';

    /**
     * Punto de entrada único desde los observers. Registra las entregas y
     * encola el envío después del commit de la transacción tenant (si no hay
     * transacción activa, encola de inmediato).
     *
     * @param mixed $model
     */
    public function dispatch(string $event, $model): void
    {
        try {
            $subscriptions = WebhookSubscription::activeForEvent($event)->get();

            if ($subscriptions->isEmpty()) {
                return;
            }

            $website = app(Environment::class)->tenant();

            if (!$website) {
                return; // sin contexto tenant no es posible encolar el envío
            }

            $websiteId = $website->id;

            $envelope = [
                'id' => (string) Str::uuid(),
                'event' => $event,
                'api_version' => self::API_VERSION,
                'created_at' => now()->toIso8601String(),
                'data' => WebhookEvents::payloadBuilder($event)->build($model),
            ];

            $deliveryIds = [];

            foreach ($subscriptions as $subscription) {
                $delivery = WebhookDelivery::create([
                    'webhook_subscription_id' => $subscription->id,
                    'event' => $event,
                    'event_id' => $envelope['id'],
                    'payload' => $envelope,
                    'status' => WebhookDelivery::STATUS_PENDING,
                ]);

                $deliveryIds[] = $delivery->id;
            }

            DB::connection('tenant')->afterCommit(function () use ($websiteId, $deliveryIds) {
                foreach ($deliveryIds as $deliveryId) {
                    SendWebhookJob::dispatch($websiteId, $deliveryId);
                }
            });
        } catch (\Throwable $e) {
            // un fallo en el webhook jamás debe romper la operación de origen
            report($e);
        }
    }
}
