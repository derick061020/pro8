<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureResellerSystemAdminPermissions
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->guard('admin')->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->reseller_id === null) {
            return $next($request);
        }

        $path = trim($request->path(), '/');
        $first = $path === '' ? '' : explode('/', $path)[0];

        if (!$user->canAccessSystemPath($first)) {
            abort(403, 'No tienes permiso para acceder a este módulo.');
        }

        return $next($request);
    }
}
