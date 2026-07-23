<?php

namespace Modules\WhatsAppBot\Services\Evolution;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Cliente HTTP del bot-proxy de BuhoLa.
 *
 * Pro8 ya no habla directamente con Evolution. Las llamadas pasan por el
 * proxy autenticado de BuhoLa (https://bot-proxy.buho.xyz) que expone una
 * API mas amigable y oculta la APIkey master del servidor Evolution.
 *
 * Mantiene el nombre EvolutionClient por compatibilidad con el resto del
 * modulo; internamente apunta al proxy.
 */
class EvolutionClient
{
    private string $baseUrl;
    private string $authHeader;
    private int $timeout;

    public function __construct()
    {
        $url = config('buho_proxy.url');
        $clientId = config('buho_proxy.client_id');
        $clientSecret = config('buho_proxy.client_secret');
        $this->timeout = (int) (config('buho_proxy.timeout') ?? 30);

        if (empty($url) || empty($clientId) || empty($clientSecret)) {
            throw new RuntimeException('Proxy del bot no configurado. Revisa config/buho_proxy.php.');
        }

        $this->baseUrl = rtrim($url, '/');
        $this->authHeader = 'Basic ' . base64_encode($clientId . ':' . $clientSecret);
    }

    private function http(?int $timeout = null): PendingRequest
    {
        $http = Http::baseUrl($this->baseUrl)
            ->withHeaders(['Authorization' => $this->authHeader])
            ->acceptJson()
            ->timeout($timeout ?? $this->timeout);

        if (app()->environment('local')) {
            $http = $http->withOptions(['verify' => false]);
        }

        return $http;
    }

    /**
     * Crea la instancia y registra el webhook en un solo paso.
     * El proxy se encarga de registrar el webhook contra si mismo y
     * reenviar los eventos al webhook_url del tenant.
     *
     * Devuelve { instance_name, qr, webhook_url } u otros campos segun
     * la respuesta del proxy.
     */
    public function createInstance(string $instance, ?string $webhookUrl = null): array
    {
        $payload = ['instance_name' => $instance];
        if ($webhookUrl) {
            $payload['webhook_url'] = $webhookUrl;
        }

        $response = $this->http()->post('/api/bot/instances', $payload);

        if ($response->successful()) {
            return $response->json() ?: [];
        }

        $body = strtolower($response->body());
        if (str_contains($body, 'already exists') || str_contains($body, 'already in use')) {
            return ['ok' => true, 'already_exists' => true];
        }

        throw new RuntimeException("No se pudo crear la instancia. HTTP {$response->status()}: {$response->body()}");
    }

    public function connect(string $instance): array
    {
        return $this->http()->get("/api/bot/instances/{$instance}/qr")->json() ?: [];
    }

    public function connectionState(string $instance): array
    {
        return $this->http()->get("/api/bot/instances/{$instance}/state")->json() ?: [];
    }

    public function fetchInstance(string $instance): array
    {
        return $this->http()->get("/api/bot/instances/{$instance}/info")->json() ?: [];
    }

    /**
     * Actualiza el webhook URL del tenant. El proxy lo reenvia
     * contra Evolution con su propio endpoint receptor en el medio.
     */
    public function setWebhook(string $instance, string $url, array $events = []): array
    {
        return $this->http()->put("/api/bot/instances/{$instance}/webhook", [
            'url' => $url,
        ])->json() ?: [];
    }

    public function sendPresence(string $instance, string $number, string $presence = 'composing', int $delay = 1200): array
    {
        try {
            return $this->http(10)->post("/api/bot/instances/{$instance}/presence", [
                'number' => preg_replace('/\D/', '', $number),
                'presence' => $presence,
                'delay' => $delay,
            ])->json() ?: [];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function sendText(string $instance, string $number, string $text): array
    {
        return $this->http()->post("/api/bot/instances/{$instance}/messages/text", [
            'number' => preg_replace('/\D/', '', $number),
            'text' => $text,
            'delay' => 0,
            'linkPreview' => true,
        ])->json() ?: [];
    }

    public function sendMedia(string $instance, string $number, array $media): array
    {
        return $this->http()->post("/api/bot/instances/{$instance}/messages/media", array_merge([
            'number' => preg_replace('/\D/', '', $number),
            'delay' => 0,
        ], $media))->json() ?: [];
    }

    public function deleteInstance(string $instance): array
    {
        try {
            return $this->http()->delete("/api/bot/instances/{$instance}")->json() ?: [];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function restartInstance(string $instance): array
    {
        return $this->http()->post("/api/bot/instances/{$instance}/restart")->json() ?: [];
    }

    public static function extractQr(array $response): ?string
    {
        // El proxy ya normaliza el QR pero mantenemos los candidatos por compat.
        $candidates = [
            $response['qr'] ?? null,
            $response['qrcode']['base64'] ?? null,
            $response['base64'] ?? null,
            is_string($response['qrcode'] ?? null) ? $response['qrcode'] : null,
            $response['code'] ?? null,
            $response['qr_code'] ?? null,
            $response['image'] ?? null,
        ];

        foreach ($candidates as $qr) {
            if (!empty($qr) && is_string($qr)) {
                return str_starts_with($qr, 'data:image/') ? $qr : 'data:image/png;base64,' . $qr;
            }
        }

        return null;
    }

    public static function isConnected(array $response): bool
    {
        return ($response['state'] ?? null) === 'open'
            || ($response['status'] ?? null) === 'connected'
            || ($response['instance']['state'] ?? null) === 'open'
            || ($response['instance']['status'] ?? null) === 'connected'
            || ($response['connected'] ?? false) === true;
    }
}
