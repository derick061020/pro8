<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Ecommerce\Http\Requests\DeliveryZoneRequest;
use Modules\Ecommerce\Http\Resources\DeliveryZoneCollection;
use Modules\Ecommerce\Http\Resources\DeliveryZoneResource;
use Modules\Ecommerce\Models\Tenant\DeliveryZone;
use Modules\Ecommerce\Models\Tenant\DeliveryZoneLocation;

/**
 * zonas de delivery del ecommerce.
 */
class DeliveryZoneController extends Controller
{
    /**
     * Vista principal del listado (no se usa actualmente — el tab está embebido en configuración).
     */
    public function index()
    {
        return view('ecommerce::configuration.index');
    }

    /**
     * Retorna el registro puntual para el formulario de edición.
     */
    public function record(Request $request)
    {
        $record = DeliveryZone::with('locations.department', 'locations.province', 'locations.district')
            ->findOrFail($request->id);

        return new DeliveryZoneResource($record);
    }

    /**
     * Colección paginada con filtros de búsqueda.
     * Filtros soportados: q (nombre), active (true/false).
     */
    public function records(Request $request)
    {
        $query = DeliveryZone::with('locations.department', 'locations.province', 'locations.district');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('active') && $request->active !== '') {
            $query->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
        }

        $query->orderByDesc('id');

        return new DeliveryZoneCollection($query->paginate(config('tenant.items_per_page')));
    }

    /**
     * Crea o actualiza una zona de delivery, sincronizando sus ubicaciones.
     */
    public function store(DeliveryZoneRequest $request)
    {
        $id   = $request->input('id');
        $zone = DeliveryZone::firstOrNew(['id' => $id]);
        $zone->fill($request->validated());
        $zone->save();

        // Sincronización limpia: eliminar ubicaciones anteriores e insertar las nuevas
        DeliveryZoneLocation::where('delivery_zone_id', $zone->id)->delete();

        $locations = collect($request->input('locations', []))->map(fn ($loc) => [
            'delivery_zone_id' => $zone->id,
            'department_id'    => $loc['department_id'],
            'province_id'      => $loc['province_id']  ?? null,
            'district_id'      => $loc['district_id']  ?? null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ])->toArray();

        DeliveryZoneLocation::insert($locations);

        $zone->load('locations.department', 'locations.province', 'locations.district');

        return new DeliveryZoneResource($zone);
    }

    /**
     * Activa o desactiva una zona.
     */
    public function updateStatus(Request $request, int $id)
    {
        $zone = DeliveryZone::findOrFail($id);
        $zone->active = (bool) $request->input('active', !$zone->active);
        $zone->save();

        return ['success' => true, 'active' => $zone->active];
    }

    /**
     * Elimina una zona y sus ubicaciones (cascade en DB).
     */
    public function destroy(int $id)
    {
        $zone = DeliveryZone::findOrFail($id);
        $zone->delete();

        return ['success' => true, 'message' => 'Zona eliminada correctamente'];
    }
}
