<?php

namespace App\Console\Commands;

use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

/**
 * Autoenvío de comprobantes a SUNAT para todas las empresas.
 *
 * Lo programa app/Console/Kernel.php, así que no depende de que cada empresa tenga
 * una tarea en su tabla `tasks` (en la práctica ninguna la tenía y el autoenvío no
 * corría nunca). A diferencia de `tenancy:run`, una empresa con la base incompleta o
 * con un error no corta el recorrido: se anota en el log y se sigue con la siguiente.
 *
 *   facturas   → online:send-all  (facturas y notas pendientes, con reintento)
 *   resumenes  → summary:send     (resumen diario de las boletas pendientes)
 *   tickets    → summary:query    (consulta los tickets de resúmenes ya enviados)
 *
 * Respeta el interruptor `cron` de la configuración de cada empresa.
 */
class AutoEnvioSunatCommand extends Command
{
    protected $signature = 'sunat:auto-envio {tipo=facturas : facturas | resumenes | tickets}';

    protected $description = 'Autoenvío a SUNAT en todas las empresas (facturas, resúmenes de boletas o tickets)';

    private const COMMANDS = [
        'facturas' => 'online:send-all',
        'resumenes' => 'summary:send',
        'tickets' => 'summary:query',
    ];

    public function handle()
    {
        $tipo = $this->argument('tipo');
        $command = self::COMMANDS[$tipo] ?? null;
        if (!$command) {
            $this->error('Tipo inválido: usa facturas, resumenes o tickets');

            return 1;
        }

        $this->line('['.now()->format('Y-m-d H:i:s')."] {$tipo}");
        $environment = app(Environment::class);

        foreach (Website::query()->orderBy('id')->get() as $website) {
            try {
                $environment->tenant($website);

                // Bases de prueba sin migrar (p. ej. tenancy_prueba en pro9)
                if (!Schema::connection('tenant')->hasTable('configurations') || !Schema::connection('tenant')->hasTable('documents')) {
                    continue;
                }

                Artisan::call($command);
                $output = trim(Artisan::output());
                // Solo se anota lo que dice algo: "The command is finished" a secas no aporta
                $lines = array_filter(array_map('trim', explode("\n", $output)), function ($l) {
                    return $l !== '' && !in_array($l, ['The command is finished', 'The command was started'], true);
                });
                if ($lines) {
                    $this->line("  {$website->uuid}: ".implode(' | ', $lines));
                }
            } catch (\Throwable $e) {
                $this->line("  {$website->uuid}: ERROR ".$e->getMessage());
                report($e);
            }
        }

        return 0;
    }
}
