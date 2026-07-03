<?php

namespace App\Http\Controllers\System\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRules;

class ResetPasswordController extends Controller
{
    /**
     * Mostrar el formulario para escribir la nueva contraseña.
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('system.auth.passwords.reset', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Procesar el cambio de contraseña en la base de datos.
     */
    public function reset(Request $request)
    {
        // ── 1. FRENAR FUERZA BRUTA ──────────────────────────────────
        // Establecer un identificador único por IP para rastrear ataques.
        $throttleKey = 'pwd-update:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $segundos = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Demasiados intentos. Espera {$segundos} segundos.",
            ]);
        }

        RateLimiter::hit($throttleKey, 60);

        // ── 2. EVALUAR REGLAS DE SEGURIDAD ──────────────────────────
        // Validar que el token sea correcto y forzar el uso de una clave fuerte.
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|max:255',
            // Exigir mínimo 8 caracteres, letras y al menos un número.
            'password' => ['required', 'confirmed', PasswordRules::min(8)->letters()->numbers()],
        ]);

        // ── 3. GUARDAR NUEVA CONTRASEÑA ─────────────────────────────
        // Ejecutar el proceso nativo de Laravel para actualizar la clave.
        $status = Password::broker('admins')->reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                Log::info('Password reset successfully', [
                    'user' => $user->email
                ]);
            }
        );

        // ── 4. RESPONDER Y LIMPIAR ──────────────────────────────────
        // Evaluar el resultado final de la operación.
        if ($status === Password::PASSWORD_RESET) {
            // Limpiar los intentos de bloqueo de la IP al tener éxito.
            RateLimiter::clear($throttleKey);
            
            return redirect()
                ->route('login')
                ->with('status', 'Contraseña actualizada con éxito.');
        }

        // Devolver un error genérico para no dar pistas del fallo a extraños.
        return back()->withErrors([
            'email' => 'El enlace de recuperación no es válido o ya ha caducado.'
        ]);
    }
}