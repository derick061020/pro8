<?php

namespace App\Http\Controllers\System\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ForgotPasswordController extends Controller
{
    /**
     * Mostrar el formulario para solicitar el enlace de recuperación.
     */
    public function showLinkRequestForm()
    {
        return view('system.auth.passwords.email');
    }

    /**
     * Procesar la solicitud y enviar el correo con el enlace de recuperación.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // ── 1. ESTABLECER CLAVE ÚNICA PARA EL LIMITADOR ────────────
        // Crear un identificador único basado en la IP del usuario para rastrear sus intentos.
        $throttleKey = 'pwd-reset:' . $request->ip();

        // ── 2. EVALUAR EXCESO DE INTENTOS ───────────────────────────
        // Si el usuario supera los 5 intentos en un minuto, entrar aquí.
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            // Obtener los segundos exactos que faltan para poder intentar de nuevo.
            $segundos = RateLimiter::availableIn($throttleKey);

            // Retornar a la vista con el error. El JS de la vista leerá los segundos y bloqueará el campo.
            return back()->withErrors([
                'email' => "Demasiados intentos. Espera {$segundos} segundos.",
            ]);
        }

        // Si no ha superado el límite, registrar un nuevo intento que durará 60 segundos.
        RateLimiter::hit($throttleKey, 60);

        // ── 3. EVALUAR FORMATO ──────────────────────────────────────
        // Verificar que el campo de correo sea válido y no supere la longitud máxima.
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // ── 4. VERIFICAR EXISTENCIA DEL USUARIO ─────────────────────
        // Buscar manualmente si el correo existe en la tabla de usuarios del sistema.
        $userExists = \App\Models\System\User::where('email', $request->email)->exists();

        // Si el correo no existe, devolver un error en rojo.
        if (!$userExists) {
            return back()->withErrors([
                'email' => 'El correo electrónico ingresado no está registrado.'
            ]); 
        }

        try {
            // ── 5. ENVIAR EL ENLACE DE RECUPERACIÓN ──────────────────
            // El broker 'admins' se encarga de crear el token y enviar el correo.
            $status = Password::broker('admins')->sendResetLink(
                $request->only('email')
            );

            // Si el correo se envió con éxito, limpiar el contador de la IP para no esperar.
            RateLimiter::clear($throttleKey);

            return back()->with([
                'status' => 'Enlace de recuperación enviado a tu correo electrónico.',
                'alert_dark' => true
            ]);

        } catch (\Exception $e) {

            // ── 6. CONTROLAR ERRORES (SMTP / BD) ──────────────────
            // Si falla la conexión con el servidor de correos, registrar aquí.
            Log::error('Reset password error', [
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'email' => '⚠️ Error: ' . $e->getMessage()
            ]);
        }
    }
}