<?php

// Modules/ClaimsBook/Http/Controllers/ClaimChannelController.php

namespace Modules\ClaimsBook\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Establishment;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Environment;
use Modules\ClaimsBook\Models\Tenant\ClaimChannel;
use Modules\ClaimsBook\Http\Resources\ClaimChannelCollection;

/**
 * CRUD de canales de recepción de reclamos.
 * Los canales son configurables por tenant desde el modal de la vista index.
 */
class ClaimChannelController extends Controller
{
    /**
     * Retorna todos los canales del tenant ordenados alfabéticamente.
     */
    public function records()
    {
        return response()->json(
            $this->buildEnrichedChannels()->values()
        );
    }

    /**
     * Endpoint público que retorna los canales del tenant identificado por slug.
     * Utilizado por el widget embebible para cargar el selector de canales.
     */
    public function publicRecords($slug)
    {
        $hostname = Hostname::where('fqdn', 'like', "%{$slug}%")->first();

        if (! $hostname) {
            return response()->json([], 404);
        }

        app(Environment::class)->tenant($hostname->website);

        return response()->json(
            $this->buildEnrichedChannels()->values()
        );
    }

    /**
     * Crea un nuevo canal de recepción.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $channel = ClaimChannel::create([
            'name'        => trim($request->name),
            'description' => $request->description ? trim($request->description) : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Canal creado correctamente',
            'data'    => $channel->getCollectionData(),
        ]);
    }

    /**
     * Actualiza el nombre de un canal existente.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $channel = ClaimChannel::findOrFail($id);
        $channel->update(['name' => trim($request->name)]);

        return response()->json([
            'success' => true,
            'message' => 'Canal actualizado correctamente',
            'data'    => $channel->fresh()->getCollectionData(),
        ]);
    }

    /**
     * Elimina un canal.
     * El campo channel en claims guarda el nombre como string, por lo que
     * eliminar el canal no afecta los reclamos ya registrados.
     */
    public function destroy($id)
    {
        $channel = ClaimChannel::findOrFail($id);
        $channel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Canal eliminado correctamente',
        ]);
    }

    /**
     * Sincroniza los establecimientos del tenant como canales de recepción.
     * Crea un canal por cada establecimiento que aún no exista (por nombre).
     * Los canales ya existentes no se duplican ni modifican.
     */
    public function sync()
    {
        $establishments = Establishment::all(['description']);

        $created = 0;
        foreach ($establishments as $est) {
            $name = trim($est->description);
            if ($name && ! ClaimChannel::where('name', $name)->exists()) {
                ClaimChannel::create(['name' => $name]);
                $created++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => $created > 0
                ? "{$created} canal(es) sincronizado(s) correctamente"
                : 'Los canales ya están sincronizados con los establecimientos',
        ]);
    }

    private function buildEnrichedChannels(): \Illuminate\Support\Collection
    {
        $estMap = [];
        foreach (Establishment::all() as $est) {
            $parts = array_filter([
                ($est->address && $est->address !== '-') ? $est->address : null,
                optional($est->department)->description,
                optional($est->province)->description,
                optional($est->district)->description,
            ]);
            $estMap[strtolower(trim($est->description))] = [
                'address'   => $parts ? implode(', ', $parts) : null,
                'telephone' => ($est->telephone && $est->telephone !== '-') ? $est->telephone : null,
                'email'     => ($est->email && $est->email !== '-') ? $est->email : null,
            ];
        }

        return ClaimChannel::orderBy('name')->get()->map(function ($ch) use ($estMap) {
            return $ch->getCollectionData($estMap);
        });
    }
}
