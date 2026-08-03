<?php

namespace Modules\Offline\Console;

use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use PDO;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * Primer arranque de un terminal Windows.
 *
 * Un terminal no se instala "desde cero": tiene que partir exactamente de los
 * mismos datos que el servidor online, y sobre todo de los mismos ids. Si los
 * productos o las habitaciones tuvieran ids distintos de los del servidor,
 * cada venta subida apuntaría a otra cosa.
 *
 * Por eso la instalación se hace restaurando un respaldo de la base del
 * negocio tomado del servidor, y no corriendo el alta de un cliente nuevo.
 *
 * Se ejecuta una sola vez, desde el launcher, con los datos que cargó el
 * instalador en config/pairing.json.
 */
class OfflineInstallCommand extends Command
{
    protected $signature = 'offline:install
                            {--dump= : Ruta al respaldo .sql de la base del negocio (tomado del servidor)}
                            {--uuid= : Nombre de la base del negocio en el servidor (ej. tenant_miempresa)}
                            {--fqdn=localhost : Dominio local con el que se accede al sistema}
                            {--url= : URL del servidor online}
                            {--token= : Token de API del servidor}
                            {--code= : Código de este terminal}
                            {--name= : Nombre visible del terminal}
                            {--series=* : Series para reservar numeración}';

    protected $description = 'Prepara este equipo como terminal offline restaurando un respaldo del servidor';

    public function handle(): int
    {
        $dump = $this->option('dump');
        $uuid = $this->option('uuid');

        if (!$dump || !is_file($dump)) {
            $this->error('Indicá con --dump la ruta al respaldo .sql tomado del servidor.');

            return self::FAILURE;
        }

        if (!$uuid) {
            $this->error('Indicá con --uuid el nombre de la base del negocio en el servidor.');

            return self::FAILURE;
        }

        try {
            $this->createDatabases($uuid);
            $this->migrateCentral();
            $website = $this->registerTenant($uuid);
            $this->restoreDump($uuid, $dump);
            $this->pair($website);
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Terminal instalado. Ya puede operar sin internet.');

        return self::SUCCESS;
    }

    /**
     * Crea la base central y la del negocio si todavía no existen.
     *
     * Se usa PDO directo porque Laravel necesita que la base exista antes de
     * poder conectarse a ella.
     */
    private function createDatabases(string $uuid): void
    {
        $this->line('→ Creando bases de datos...');

        $config = config('database.connections.system');

        $pdo = new PDO(
            "mysql:host={$config['host']};port={$config['port']}",
            $config['username'],
            $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        foreach ([$config['database'], $uuid] as $database) {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
    }

    private function migrateCentral(): void
    {
        $this->line('→ Migrando base central...');

        Artisan::call('migrate', ['--force' => true], $this->getOutput());
    }

    /**
     * Registra el tenant local apuntando a la base que ya existe.
     *
     * Se crea el Website con el uuid del servidor para que hyn use esa misma
     * base, y el Hostname con el dominio local del launcher.
     */
    private function registerTenant(string $uuid): Website
    {
        $this->line('→ Registrando el negocio en esta instalación...');

        $website = Website::where('uuid', $uuid)->first();

        if (!$website) {
            $website = new Website();
            $website->uuid = $uuid;
            app(WebsiteRepository::class)->create($website);
        }

        $fqdn = $this->option('fqdn');

        $hostname = Hostname::where('fqdn', $fqdn)->first();

        if (!$hostname) {
            $hostname = new Hostname();
            $hostname->fqdn = $fqdn;
            $hostname = app(HostnameRepository::class)->create($hostname);
        }

        app(HostnameRepository::class)->attach($hostname, $website);

        return $website;
    }

    /**
     * Restaura el respaldo del servidor sobre la base del negocio.
     *
     * Se usa el cliente mysql y no PHP porque un respaldo real puede pesar
     * cientos de megas y cargarlo en memoria no es viable.
     */
    private function restoreDump(string $uuid, string $dump): void
    {
        $this->line('→ Restaurando el respaldo del servidor (puede tardar varios minutos)...');

        $config = config('database.connections.system');

        $command = [
            $this->mysqlClient(),
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['username'],
        ];

        if (!empty($config['password'])) {
            $command[] = '--password=' . $config['password'];
        }

        $command[] = $uuid;

        $process = new Process($command, base_path(), null, fopen($dump, 'r'), 3600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Falló la restauración: ' . trim($process->getErrorOutput()));
        }
    }

    /**
     * Ruta al cliente mysql. En el terminal viene con el MariaDB embebido.
     */
    private function mysqlClient(): string
    {
        $bundled = base_path('..' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR
            . 'mariadb' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'mysql.exe');

        return is_file($bundled) ? $bundled : 'mysql';
    }

    /**
     * Deja el terminal pareado con el servidor y con numeración reservada.
     */
    private function pair(Website $website): void
    {
        if (!$this->option('url') || !$this->option('token') || !$this->option('code')) {
            $this->warn('Faltan datos de pareo: ejecutá después "php artisan tenancy:run offline:pair".');

            return;
        }

        $this->line('→ Pareando con el servidor...');

        // El pareo escribe en la base del negocio, así que hay que activar el
        // tenant antes de llamarlo.
        app(Environment::class)->tenant($website);

        $arguments = [
            '--url'   => $this->option('url'),
            '--token' => $this->option('token'),
            '--code'  => $this->option('code'),
            '--name'  => $this->option('name'),
        ];

        if ($series = $this->option('series')) {
            $arguments['--series'] = $series;
        }

        Artisan::call('offline:pair', $arguments, $this->getOutput());
    }
}
