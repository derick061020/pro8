<?php

namespace Modules\Offline\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Modules\Offline\Models\OfflineConfiguration;
use Throwable;

/**
 * Detección de conexión con el servidor online.
 *
 * No alcanza con "hay internet": lo que importa es si el servidor de pro8
 * responde. Por eso se consulta su propio endpoint de ping.
 */
class Connectivity
{
    private const CACHE_KEY = 'offline.connectivity';

    /** Segundos que se reutiliza el resultado antes de volver a preguntar. */
    private const CACHE_TTL = 20;

    /** Segundos de espera antes de dar el servidor por caído. */
    private const TIMEOUT = 8;

    /**
     * ¿El servidor responde? El resultado se cachea unos segundos para que la
     * interfaz pueda consultarlo seguido sin castigar la red.
     */
    public static function isOnline(bool $fresh = false): bool
    {
        if (!$fresh) {
            $cached = Cache::get(self::CACHE_KEY);

            if ($cached !== null) {
                return (bool)$cached;
            }
        }

        $online = self::check();

        Cache::put(self::CACHE_KEY, $online, self::CACHE_TTL);

        return $online;
    }

    /**
     * Consulta real al servidor, sin caché.
     */
    public static function check(): bool
    {
        $configuration = OfflineConfiguration::current();

        if (!$configuration->canSync()) {
            return false;
        }

        try {
            $client = new Client([
                'base_uri'    => $configuration->serverUrl(),
                'timeout'     => self::TIMEOUT,
                'verify'      => false,
                'http_errors' => false,
            ]);

            $response = $client->get('/api/offline/ping', [
                'headers' => [
                    'Authorization'  => 'Bearer ' . $configuration->token_server,
                    'Accept'         => 'application/json',
                    'X-Terminal-Code' => (string)$configuration->terminal_code,
                ],
            ]);

            $online = $response->getStatusCode() === 200;
        } catch (Throwable $e) {
            $online = false;
        }

        self::remember($configuration, $online);

        return $online;
    }

    /**
     * Deja registrado el último estado conocido para mostrarlo en la interfaz
     * aunque el proceso que lo detectó haya sido el demonio de fondo.
     */
    private static function remember(OfflineConfiguration $configuration, bool $online): void
    {
        try {
            $configuration->forceFill([
                'is_online'    => $online,
                'last_ping_at' => now(),
            ])->save();
        } catch (Throwable $e) {
            // Si no se puede escribir el estado, no vale la pena romper nada.
        }
    }

    /**
     * Invalida la caché: se usa después de un envío para reflejar el estado
     * real de inmediato.
     */
    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
