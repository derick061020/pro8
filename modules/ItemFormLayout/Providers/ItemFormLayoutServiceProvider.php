<?php

namespace Modules\ItemFormLayout\Providers;

use Illuminate\Support\ServiceProvider;

class ItemFormLayoutServiceProvider extends ServiceProvider
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
            __DIR__.'/../Config/config.php' => config_path('itemformlayout.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'itemformlayout'
        );
    }

    public function provides()
    {
        return [];
    }
}
