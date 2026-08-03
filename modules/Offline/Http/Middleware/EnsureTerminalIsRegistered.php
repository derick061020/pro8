<?php

namespace Modules\Offline\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Offline\Models\Terminal;

/**
 * Solo dejan pasar terminales dados de alta y activos.
 *
 * El token de `auth:api` dice *quién* es, esto dice *desde dónde*. Sirve para
 * poder cortar una PC concreta desde el servidor (por robo, baja del local o
 * una instalación mal configurada) sin tener que rotar el token de todos.
 */
class EnsureTerminalIsRegistered
{
    public function handle(Request $request, Closure $next)
    {
        $code = $request->header('X-Terminal-Code') ?: $request->input('terminal_code');

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Falta identificar el terminal en la petición.',
            ], 422);
        }

        $terminal = Terminal::where('code', $code)->first();

        if (!$terminal) {
            return response()->json([
                'success' => false,
                'message' => 'Este terminal no está dado de alta en el servidor. Ejecutá el pareo.',
            ], 403);
        }

        if (!$terminal->active) {
            return response()->json([
                'success' => false,
                'message' => 'Este terminal fue desactivado desde el servidor.',
            ], 403);
        }

        $request->attributes->set('offline_terminal', $terminal);

        return $next($request);
    }
}
