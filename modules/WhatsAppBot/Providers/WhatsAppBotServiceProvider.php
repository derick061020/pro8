<?php

namespace Modules\WhatsAppBot\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class WhatsAppBotServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->commands([
            \Modules\WhatsAppBot\Console\Commands\TestBotCommand::class,
        ]);
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('whatsappbot.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'whatsappbot'
        );
    }

    public function registerViews()
    {
        $viewPath = resource_path('views/modules/whatsappbot');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/whatsappbot';
        }, Config::get('view.paths')), [$sourcePath]), 'whatsappbot');
    }

    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/whatsappbot');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'whatsappbot');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'whatsappbot');
        }
    }

    public function provides()
    {
        return [];
    }
}
