<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Sistema → SSL: estado del certificado y de su renovación automática.
 *
 * La renovación la hace /root/renovar-ssl.sh en el servidor (cron diario 04:15, fuera
 * del contenedor). Al terminar deja su estado en storage/app/ssl/, que es lo que se lee
 * aquí. Además se revisa en vivo qué certificado sirve el proxy para cada dominio.
 */
class SslController extends Controller
{
    /** certbot renueva cuando faltan menos de estos días */
    private const RENEW_BEFORE_DAYS = 30;

    /** Si el cron no deja rastro en este tiempo, se avisa */
    private const STALE_HOURS = 26;

    private const FIXED = ['www', 'sistema', 'admin', 'apisu'];

    public function index(Request $request)
    {
        $status = $this->readStatus();
        $live = $this->liveCheck($request->boolean('refresh'));

        // Vencimiento real: el del certificado servido (si se pudo leer), si no el del último estado
        $expiresAt = collect($live['domains'])->pluck('expires_at')->filter()->min();
        if (!$expiresAt && !empty($status['vence'])) {
            $expiresAt = Carbon::parse($status['vence'])->setTimezone(config('app.timezone'));
        }

        $renewFrom = $expiresAt ? $expiresAt->copy()->subDays(self::RENEW_BEFORE_DAYS) : null;

        $lastRun = !empty($status['fin']) ? Carbon::parse($status['fin'])->setTimezone(config('app.timezone')) : null;
        $nextRun = $this->nextCronRun();

        return view('system.ssl.index', [
            'status' => $status,
            'log' => $this->readLog(),
            'live' => $live,
            'expiresAt' => $expiresAt,
            'daysLeft' => $expiresAt ? (int) floor(now()->diffInHours($expiresAt, false) / 24) : null,
            'renewFrom' => $renewFrom,
            'renewAt' => $renewFrom ? $this->nextCronRun($renewFrom->isPast() ? now() : $renewFrom) : null,
            'lastRun' => $lastRun,
            'nextRun' => $nextRun,
            'stale' => $lastRun ? $lastRun->lt(now()->subHours(self::STALE_HOURS)) : false,
            'installed' => $status !== null,
        ]);
    }

    /** Siguiente 04:15 (hora del servidor, Lima) a partir de $from */
    private function nextCronRun(?Carbon $from = null): Carbon
    {
        $from = ($from ?? now())->copy()->setTimezone(config('app.timezone'));
        $run = $from->copy()->setTime(4, 15);

        return $run->lte($from) ? $run->addDay() : $run;
    }

    private function readStatus(): ?array
    {
        $file = storage_path('app/ssl/estado.json');
        if (!is_readable($file)) {
            return null;
        }

        $data = json_decode((string) file_get_contents($file), true);

        return is_array($data) ? $data : null;
    }

    private function readLog(): string
    {
        $file = storage_path('app/ssl/renovar-ssl.log');
        if (!is_readable($file)) {
            return '';
        }

        // Lo más reciente primero, por bloques de ejecución
        $lines = array_slice(file($file, FILE_IGNORE_NEW_LINES) ?: [], -150);

        return implode("\n", $lines);
    }

    /**
     * Pregunta a cada dominio qué certificado entrega (5 min en caché).
     *
     * @return array{checked_at: string, domains: array<int, array>}
     */
    private function liveCheck(bool $refresh): array
    {
        if ($refresh) {
            Cache::forget('system.ssl.live');
        }

        return Cache::remember('system.ssl.live', 300, function () {
            $domains = [];
            foreach ($this->domains() as $host) {
                $domains[] = ['host' => $host] + $this->inspect($host);
            }

            return ['checked_at' => now()->toIso8601String(), 'domains' => $domains];
        });
    }

    /** Dominio base + fijos + hostnames de los clientes, sin repetir */
    private function domains(): array
    {
        $base = config('configuration.app_url_base') ?: 'hotelbuendia.net';
        $hosts = [$base];
        foreach (self::FIXED as $sub) {
            $hosts[] = "$sub.$base";
        }

        try {
            foreach (Hostname::query()->pluck('fqdn') as $fqdn) {
                $hosts[] = strtolower(trim($fqdn));
            }
        } catch (\Throwable $e) {
            // Sin acceso a la tabla de hostnames: se muestran solo los fijos
        }

        return array_values(array_unique(array_filter($hosts, function ($h) use ($base) {
            return $h === $base || substr($h, -strlen(".$base")) === ".$base";
        })));
    }

    private function inspect(string $host): array
    {
        $context = stream_context_create(['ssl' => [
            'capture_peer_cert' => true,
            'verify_peer' => false,
            'verify_peer_name' => false,
            'SNI_enabled' => true,
            'peer_name' => $host,
        ]]);

        $client = @stream_socket_client("ssl://$host:443", $errno, $errstr, 6, STREAM_CLIENT_CONNECT, $context);
        if (!$client) {
            return ['ok' => false, 'error' => $errstr ?: 'Sin conexión', 'expires_at' => null, 'covered' => false, 'issuer' => null, 'names' => []];
        }

        $cert = stream_context_get_params($client)['options']['ssl']['peer_certificate'] ?? null;
        fclose($client);
        $info = $cert ? openssl_x509_parse($cert) : null;
        if (!$info) {
            return ['ok' => false, 'error' => 'No se pudo leer el certificado', 'expires_at' => null, 'covered' => false, 'issuer' => null, 'names' => []];
        }

        $names = [];
        foreach (explode(',', $info['extensions']['subjectAltName'] ?? '') as $entry) {
            $entry = trim($entry);
            if (stripos($entry, 'DNS:') === 0) {
                $names[] = strtolower(substr($entry, 4));
            }
        }

        $covered = false;
        foreach ($names as $name) {
            if ($name === $host || (strpos($name, '*.') === 0 && substr_count($host, '.') === substr_count($name, '.') && substr($host, -strlen(substr($name, 1))) === substr($name, 1))) {
                $covered = true;
            }
        }

        $expiresAt = Carbon::createFromTimestamp($info['validTo_time_t'])->setTimezone(config('app.timezone'));

        return [
            'ok' => $covered && $expiresAt->isFuture(),
            'error' => !$covered ? 'El certificado no incluye este dominio' : ($expiresAt->isPast() ? 'Certificado vencido' : null),
            'expires_at' => $expiresAt,
            'covered' => $covered,
            'issuer' => trim(($info['issuer']['O'] ?? '').' '.($info['issuer']['CN'] ?? '')),
            'names' => $names,
        ];
    }
}
