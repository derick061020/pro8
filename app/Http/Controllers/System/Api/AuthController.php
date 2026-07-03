<?php

namespace App\Http\Controllers\System\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Authenticate system user and return api_token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Buscar el usuario por email
        $user = User::where('email', $request->email)->first();

        // Verificar si el usuario existe y la contraseña es correcta
        if ($user && Hash::check($request->password, $user->password)) {

            // Verificar si tiene token, si no, generarlo
            if (empty($user->api_token)) {
                $user->api_token = Str::random(60);
                $user->save();
            }

            // Retornar respuesta con token y datos del usuario
            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'token' => $user->api_token,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        // Retornar respuesta de error si las credenciales son incorrectas
        return response()->json([
            'success' => false,
            'message' => 'Las credenciales proporcionadas son incorrectas.',
        ], 401);
    }
}