<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Establishment;
use Illuminate\Http\Request;
use Modules\Ecommerce\Models\Tenant\PickupBranch;

/**
 * Controller para gestión de sucursales de recojo en tienda (Ecommerce).
 * Permite crear, listar, eliminar y sincronizar sucursales desde los establecimientos del sistema.
 */
class PickupBranchController extends Controller
{
    /**
     * Retorna todas las sucursales (para el panel de administración).
     */
    public function records()
    {
        $branches = PickupBranch::orderBy('name')->get();

        return response()->json([
            'data' => $branches->map(fn($b) => $b->getCollectionData()),
        ]);
    }

    /**
     * Retorna solo las sucursales activas (para el checkout público).
     */
    public function publicRecords()
    {
        $branches = PickupBranch::active()->orderBy('name')->get();

        return response()->json([
            'data' => $branches->map(fn($b) => [
                'id'      => $b->id,
                'name'    => $b->name,
                'address' => $b->address,
            ]),
        ]);
    }

    /**
     * Crea una nueva sucursal de recojo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $branch = PickupBranch::create([
            'name'    => $request->name,
            'address' => $request->address,
            'active'  => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sucursal creada correctamente.',
            'data'    => $branch->getCollectionData(),
        ]);
    }

    /**
     * Actualiza el estado activo/inactivo de una sucursal.
     */
    public function updateStatus(int $id, Request $request)
    {
        $branch = PickupBranch::findOrFail($id);
        $branch->active = (bool) $request->input('active', true);
        $branch->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado.',
            'data'    => $branch->getCollectionData(),
        ]);
    }

    /**
     * Elimina una sucursal de recojo.
     */
    public function destroy(int $id)
    {
        $branch = PickupBranch::findOrFail($id);
        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sucursal eliminada correctamente.',
        ]);
    }

    /**
     * Sincroniza los establecimientos del sistema como sucursales de recojo.
     * Solo crea sucursales nuevas; no sobrescribe las existentes.
     */
    public function sync()
    {
        $establishments = Establishment::all(['description', 'trade_address']);

        $created = 0;
        foreach ($establishments as $establishment) {
            $name = trim($establishment->description ?? '');
            if (!$name) {
                continue;
            }

            // Evitar duplicados por nombre
            $exists = PickupBranch::where('name', $name)->exists();
            if (!$exists) {
                PickupBranch::create([
                    'name'    => $name,
                    'address' => $establishment->trade_address ?? null,
                    'active'  => true,
                ]);
                $created++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Sincronización completada. {$created} sucursal(es) añadida(s).",
        ]);
    }
}
