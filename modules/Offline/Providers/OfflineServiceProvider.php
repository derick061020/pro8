<?php

namespace Modules\Offline\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Modules\Offline\Console\OfflineDaemonCommand;
use Modules\Offline\Console\OfflinePairCommand;
use Modules\Offline\Console\OfflineStatusCommand;
use Modules\Offline\Console\OfflineSyncCommand;
use Modules\Offline\Console\OfflineUpdateCommand;
use Modules\Offline\Http\Middleware\EnsureTerminalIsRegistered;
use Modules\Offline\Services\SyncWatcher;
// use Illuminate\Database\Eloquent\Factory;

class OfflineServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
    // $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->registerMiddleware();
        $this->registerCommands();

        // Deja la bandeja de salida escuchando los cambios del sistema.
        // Internamente no hace nada si esta instalación no es un terminal.
        SyncWatcher::register();
    }

    /**
     * Alias del middleware que valida el terminal en la API de sincronización.
     */
    protected function registerMiddleware()
    {
        /** @var Router $router */
        $router = $this->app['router'];

        $router->aliasMiddleware('offline.terminal', EnsureTerminalIsRegistered::class);
    }

    protected function registerCommands()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            OfflinePairCommand::class,
            OfflineSyncCommand::class,
            OfflineDaemonCommand::class,
            OfflineStatusCommand::class,
            OfflineUpdateCommand::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('offline.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'offline'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/offline');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/offline';
        }, \Config::get('view.paths')), [$sourcePath]), 'offline');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/offline');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'offline');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'offline');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
