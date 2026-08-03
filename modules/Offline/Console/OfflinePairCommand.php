<?php

namespace Modules\Offline\Console;

use App\Models\Tenant\Series;
use Illuminate\Console\Command;
use Modules\Offline\Models\OfflineConfiguration;
use Modules\Offline\Services\NumberRangeService;
use Modules\Offline\Services\SyncClient;
use Throwable;

/**
 * Pareo inicial de un terminal Windows con el servidor online.
 *
 * Es lo primero que se corre después de instalar: deja la instalación en modo
 * cliente, la da de alta en el servidor, baja los datos maestros y reserva el
 * primer bloque de correlativos.
 */
class OfflinePairCommand extends Command
{
    protected $signature = 'offline:pair
                            {--url= : URL del servidor online (https://mi-empresa.com)}
                            {--token= : Token de API del usuario en el servidor}
                            {--code= : Código corto del terminal (ej. T01)}
                            {--name= : Nombre visible del terminal}
                            {--series=* : Series para las que reservar numeración (ej. B001)}
                            {--size=500 : Tamaño del bloque de correlativos}
                            {--no-pull : No bajar los datos maestros ahora}';

    protected $description = 'Parea esta instalación como terminal offline de un servidor pro8 online';

    public function handle(): int
    {
        $configuration = OfflineConfiguration::current();

        $url   = $this->option('url') ?: $configuration->url_server;
        $token = $this->option('token') ?: $configuration->token_server;
        $code  = $this->option('code') ?: $configuration->terminal_code;
        $name  = $this->option('name') ?: $configuration->terminal_name;

        if (!$url || !$token || !$code) {
            $this->error('Faltan datos: se necesitan --url, --token y --code.');

            return self::FAILURE;
        }

        $configuration->fill([
            'is_client'     => true,
            'mode'          => OfflineConfiguration::MODE_CLIENT,
            'url_server'    => rtrim($url, '/'),
            'token_server'  => $token,
            'terminal_code' => $code,
            'terminal_name' => $name ?: $code,
            'sync_enabled'  => true,
        ])->save();

        $this->info("Terminal configurado como cliente: {$code} -> {$configuration->serverUrl()}");

        $client = new SyncClient($configuration->fresh());

        // 1. Alta en el servidor
        try {
            $response = $client->handshake();
        } catch (Throwable $e) {
            $this->error('No se pudo contactar al servidor: ' . $e->getMessage());
            $this->line('Revisá la URL y el token; la configuración local quedó guardada igual.');

            return self::FAILURE;
        }

        if (empty($response['success'])) {
            $this->error($response['message'] ?? 'El servidor rechazó el pareo.');

            return self::FAILURE;
        }

        $this->info('Terminal dado de alta en el servidor.');

        // 2. Datos maestros
        if (!$this->option('no-pull')) {
            $this->line('Bajando datos maestros...');
            $pull = $client->pull();

            $pull['success']
                ? $this->info("Se bajaron {$pull['records']} registros.")
                : $this->warn('La bajada terminó con errores: ' . $pull['message']);
        }

        // 3. Correlativos
        $this->allocateSeries($client);

        $this->newLine();
        $this->info('Pareo completo. A partir de ahora este equipo puede vender sin internet.');

        return self::SUCCESS;
    }

    /**
     * Reserva bloques para las series indicadas, o para todas las activas.
     */
    private function allocateSeries(SyncClient $client): void
    {
        $series = $this->option('series');

        if (empty($series)) {
            $series = $this->guessSeries();
        }

        if (empty($series)) {
            $this->warn('No se reservó numeración: indicá las series con --series=B001 --series=F001.');

            return;
        }

        $size = (int)$this->option('size') ?: NumberRangeService::DEFAULT_SIZE;

        foreach ($series as $number) {
            try {
                $range = $client->allocateRange('document', null, $number, $size);
                $this->info("Serie {$number}: reservados los números {$range->from_number} al {$range->to_number}.");
            } catch (Throwable $e) {
                $this->error("Serie {$number}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Series de comprobantes que ya existen en la base local.
     *
     * @return array<int, string>
     */
    private function guessSeries(): array
    {
        try {
            return Series::query()->pluck('number')->unique()->values()->all();
        } catch (Throwable $e) {
            return [];
        }
    }
}
