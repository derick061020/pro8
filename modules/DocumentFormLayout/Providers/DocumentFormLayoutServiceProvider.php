<?php

namespace Modules\DocumentFormLayout\Providers;

use Illuminate\Support\ServiceProvider;

class DocumentFormLayoutServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerConfig();
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('documentformlayout.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'documentformlayout'
        );
    }

    public function provides()
    {
        return [];
    }
}
