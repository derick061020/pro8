<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfigurationImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimetypes:image/*|max:3072',
        ]);
        if (! $request->hasFile('image')) {
            return response()->json([
                'message' => 'No se recibió archivo de imagen.'
            ], 400);
        }

        $file = $request->file('image');

        if (! $file->isValid()) {
            return response()->json([
                'message' => 'El archivo de imagen no es válido.'
            ], 400);
        }

        $extension = $file->getClientOriginalExtension();
        $imageName = 'default_image.' . $extension;

        try {
            $realPath = $file->getRealPath();

            // En algunos entornos el realPath puede venir vacío; usar fallback
            if (empty($realPath) || !file_exists($realPath)) {
                $contents = $file->get();
                Storage::put('public/defaults/'.$imageName, $contents);
            } else {
                $file->storeAs('public/defaults', $imageName);
            }

            DB::connection('tenant')->table('configurations')->update([
                'product_default_image' => $imageName,
            ]);

            return response()->json([
                'message' => 'Imagen subida correctamente.',
                'file' => $imageName,
            ]);
        }
        catch (\Throwable $e) {
            Log::error('ConfigurationImageController::upload - exception al guardar', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Error al guardar la imagen en el servidor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
