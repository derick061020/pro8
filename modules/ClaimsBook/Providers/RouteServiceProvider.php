<?php

// Modules/ClaimsBook/Providers/RouteServiceProvider.php

namespace Modules\ClaimsBook\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Namespace base de los controladores del módulo.
     */
    protected $moduleNamespace = 'Modules\ClaimsBook\Http\Controllers';

    public function boot()
    {
        parent::boot();
    }

    /**
     * Registra las rutas web y API del módulo.
     */
    public function map()
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    /**
     * Rutas web (con estado de sesión y protección CSRF).
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(__DIR__ . '/../Routes/web.php');
    }

    /**
     * Rutas API (stateless).
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
