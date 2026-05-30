<?php

namespace App\Providers;

use Illuminate\Cache\RedisStore;
use Illuminate\Cache\DatabaseStore;
use Illuminate\Cache\FileStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class CacheServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $namespace = function($app) {

            if (PHP_SAPI === 'cli') {
                return $app['config']['cache.default'];
            }

            $fqdn = request()->getHost();

            $uuid = DB::table('hostnames')
                ->select('websites.uuid')
                ->join('websites', 'hostnames.website_id', '=', 'websites.id')
                ->where('fqdn', $fqdn)
                ->value('uuid');

            return $uuid;
        };

        $cacheDriver = config('cache.default');

        switch ($cacheDriver) {
            case 'file':
                Cache::extend($cacheDriver, function ($app) use ($namespace){
                    $namespace = $namespace($app);

                    return Cache::repository(new FileStore(
                        $app['files'],
                        $app['config']['cache.stores.file.path'].$namespace
                    ));
                });
                break;
            case 'database':
                Cache::extend($cacheDriver, function ($app) use ($namespace){
                    $namespace = $namespace($app);

                    return Cache::repository(new DatabaseStore(
                        $app['db.connection'],
                        'cache',
                        $namespace
                    ));
                });
                break;
            case 'redis':
                // But if not yet instantiated, then we are able to redifine namespace (prefix). Works for Redis only
                if (PHP_SAPI === 'cli') {
                    $namespace = Str::slug(env('APP_NAME', 'laravel'), '_').'_cache';
                } else {
                    $fqdn = request()->getHost();
                    $namespace = DB::table('hostnames')
                        ->select('websites.uuid')
                        ->join('websites', 'hostnames.website_id', '=', 'websites.id')
                        ->where('fqdn', $fqdn)
                        ->value('uuid');
                }
                Cache::setPrefix($namespace);
                break;
            default:
        }
        // Cache::extend('redis_tenancy', function ($app) {
        //     if (PHP_SAPI === 'cli') {
        //         return $app['config']['cache.default'];
        //     } else {
        //         // ok, this is basically a hack to set the redis cache store
        //         // prefix to the UUID of the current website being called
        //         $fqdn = $_SERVER['SERVER_NAME'];

        //         $uuid = DB::table('hostnames')
        //             ->select('websites.uuid')
        //             ->join('websites', 'hostnames.website_id', '=', 'websites.id')
        //             ->where('fqdn', $fqdn)
        //             ->value('uuid');
        //     }
        //     return Cache::repository(new RedisStore(
        //         $app['redis'],
        //         $uuid,
        //         $app['config']['cache.stores.redis.connection']
        //     ));
        // });
    }
}