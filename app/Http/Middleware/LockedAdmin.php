<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\System\Configuration;
use Illuminate\Support\Facades\Auth;

class LockedAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $configuration = Configuration::first();

        // 1. Verificación global de panel bloqueado
        if ($configuration && $configuration->locked_admin) {
            abort(403);
        }

        // 2. Verificación de estado de cuenta
        $user = Auth::guard('admin')->user() ?? Auth::user();

        if ($user) {
            $isMaster = is_null($user->reseller_id) || $user->id === 1;

            if (!$isMaster) {
                // Si el usuario no es maestro, verificamos su estado activo
                if (isset($user->status) && !$user->status) {
                    abort(403, 'Su cuenta está inactiva y no tienes los permisos necesarios para acceder a este recurso.');
                }
            }
        }

        return $next($request);
    }
}
