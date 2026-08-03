<?php

namespace Modules\Offline\Console;

use Illuminate\Console\Command;
use Modules\Offline\Models\OfflineConfiguration;
use Modules\Offline\Models\SyncLog;
use Modules\Offline\Services\SyncQueue;
use Symfony\Component\Process\Process;

/**
 * Actualiza el código del terminal desde el repositorio git.
 *
 * El terminal apunta a una rama de despliegue que ya trae `vendor/` y los
 * assets compilados, así que en la PC del cliente no hace falta composer ni
 * node: alcanza con traer los archivos y correr las migraciones.
 *
 * La copia local se considera descartable: cualquier cambio hecho a mano en
 * los archivos del sistema se pisa (`git reset --hard`). Lo que no se toca
 * nunca es la base de datos ni el .env.
 */
class OfflineUpdateCommand extends Command
{
    protected $signature = 'offline:update
                            {--branch= : Rama de despliegue (por defecto la configurada)}
                            {--remote=origin : Remoto de git}
                            {--no-migrate : No correr migraciones}
                            {--composer : Correr composer install (si el despliegue no trae vendor)}
                            {--force : Actualizar aunque queden cambios sin sincronizar}';

    protected $description = 'Trae la última versión del sistema desde git y aplica las migraciones';

    /** Minutos que puede tardar cada paso antes de darse por colgado. */
    private const STEP_TIMEOUT = 900;

    public function handle(): int
    {
        $configuration = OfflineConfiguration::current();
        $root          = base_path();

        if (!is_dir($root . '/.git')) {
            $this->error('Esta instalación no es una copia de git, no se puede actualizar así.');

            return self::FAILURE;
        }

        // Actualizar con ventas sin subir es riesgoso: una migración podría
        // cambiar la forma de lo que está en la cola.
        $pending = SyncQueue::pendingCount();

        if ($pending > 0 && !$this->option('force')) {
            $this->error("Hay {$pending} cambios sin sincronizar. Sincronizá primero o usá --force.");

            return self::FAILURE;
        }

        $branch = $this->option('branch') ?: $configuration->git_branch ?: 'main';
        $remote = $this->option('remote');

        $this->info("Actualizando desde {$remote}/{$branch}...");

        $steps = [
            ['Descargando cambios', ['git', 'fetch', '--prune', $remote, $branch]],
            ['Aplicando versión',   ['git', 'reset', '--hard', "{$remote}/{$branch}"]],
        ];

        if ($this->option('composer')) {
            $steps[] = ['Instalando dependencias', ['composer', 'install', '--no-dev', '--optimize-autoloader', '--no-interaction']];
        }

        if (!$this->option('no-migrate')) {
            $steps[] = ['Migrando base central', ['php', 'artisan', 'migrate', '--force']];
            $steps[] = ['Migrando base del negocio', ['php', 'artisan', 'tenancy:migrate', '--force']];
        }

        $steps[] = ['Limpiando cachés', ['php', 'artisan', 'optimize:clear']];

        foreach ($steps as [$label, $command]) {
            if (!$this->runStep($label, $command, $root)) {
                SyncLog::record([
                    'terminal_code' => $configuration->terminal_code,
                    'direction'     => 'update',
                    'success'       => false,
                    'message'       => "Falló: {$label}",
                ]);

                return self::FAILURE;
            }
        }

        $version = $this->currentVersion($root);

        $configuration->forceFill([
            'git_branch'  => $branch,
            'app_version' => $version,
        ])->save();

        SyncLog::record([
            'terminal_code' => $configuration->terminal_code,
            'direction'     => 'update',
            'success'       => true,
            'message'       => "Actualizado a {$version}",
        ]);

        $this->newLine();
        $this->info("Sistema actualizado a la versión {$version}.");

        return self::SUCCESS;
    }

    private function runStep(string $label, array $command, string $root): bool
    {
        $this->line("→ {$label}...");

        $process = new Process($command, $root, null, null, self::STEP_TIMEOUT);
        $process->run(function ($type, $buffer) {
            $this->getOutput()->write($buffer);
        });

        if ($process->isSuccessful()) {
            return true;
        }

        $this->error("{$label} falló: " . trim($process->getErrorOutput() ?: $process->getOutput()));

        return false;
    }

    /**
     * Hash corto del commit actual, que se guarda como versión instalada.
     */
    private function currentVersion(string $root): string
    {
        $process = new Process(['git', 'rev-parse', '--short', 'HEAD'], $root);
        $process->run();

        return $process->isSuccessful() ? trim($process->getOutput()) : 'desconocida';
    }
}
