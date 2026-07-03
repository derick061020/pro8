<?php

// Modules/ClaimsBook/Providers/ClaimsBookServiceProvider.php

namespace Modules\ClaimsBook\Providers;

use Illuminate\Support\ServiceProvider;

class ClaimsBookServiceProvider extends ServiceProvider
{
    /**
     * Inicialización del módulo: registra vistas, traducciones y configuración.
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
    }

    /**
     * Registra el proveedor de rutas del módulo.
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Registra la configuración del módulo.
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('claimsbook.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'claimsbook'
        );
    }

    /**
     * Registra las vistas del módulo bajo el alias 'claimsbook'.
     */
    protected function registerViews()
    {
        $viewPath   = resource_path('views/modules/claimsbook');
        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(
            array_merge(
                array_map(fn ($path) => $path . '/modules/claimsbook', \Config::get('view.paths')),
                [$sourcePath]
            ),
            'claimsbook'
        );
    }

    /**
     * Registra las traducciones del módulo.
     */
    protected function registerTranslations()
    {
        $langPath = resource_path('lang/modules/claimsbook');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'claimsbook');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'claimsbook');
        }
    }

    public function provides(): array
    {
        return [];
    }
}
