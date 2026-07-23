<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\System\Configuration;

class TermsController extends Controller
{
    /**
     * Página pública de Términos y Condiciones (dominio system).
     *
     * - disabled / sin configurar  -> redirige al login (no muestra nada).
     * - url                        -> redirección 302 a la URL externa.
     * - content                    -> renderiza el contenido propio.
     */
    public function show()
    {
        $configuration = Configuration::first();

        if (! $configuration || $configuration->terms_mode === 'url') {
            if ($configuration && $configuration->terms_mode === 'url' && ! empty($configuration->terms_url)) {
                return redirect()->away($configuration->terms_url);
            }
            return redirect()->route('login');
        }

        if ($configuration->terms_mode === 'content' && ! empty($configuration->terms_content)) {
            return view('system.terms.show', [
                'content' => $configuration->terms_content,
            ]);
        }

        // disabled o cualquier estado incompleto
        return redirect()->route('login');
    }
}
