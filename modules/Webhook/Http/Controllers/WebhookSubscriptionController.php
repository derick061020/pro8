<?php

namespace Modules\Webhook\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Webhook\Http\Requests\WebhookSubscriptionRequest;
use Modules\Webhook\Http\Resources\WebhookSubscriptionResource;
use Modules\Webhook\Models\WebhookSubscription;
use Modules\Webhook\Services\WebhookEvents;

class WebhookSubscriptionController extends Controller
{
    /**
     * Vista principal del módulo (suscripciones + log de entregas)
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('webhook::index');
    }

    /**
     * Catálogo de eventos disponibles (checkboxes del formulario)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function events()
    {
        return response()->json([
            'data' => WebhookEvents::all(),
        ]);
    }

    /**
     * Listado paginado de suscripciones
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function records(Request $request)
    {
        $value = $request->value ?? '';

        $records = WebhookSubscription::where(function ($query) use ($value) {
                $query->where('name', 'like', "%{$value}%")
                    ->orWhere('url', 'like', "%{$value}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(config('tenant.items_per_page'));

        return response()->json([
            'data' => WebhookSubscriptionResource::collection($records),
            'current_page' => $records->currentPage(),
            'per_page' => $records->perPage(),
            'total' => $records->total(),
            'last_page' => $records->lastPage(),
        ]);
    }

    /**
     * Obtener una suscripción específica
     *
     * @param int $id
     * @return WebhookSubscriptionResource
     */
    public function record($id)
    {
        $record = WebhookSubscription::findOrFail($id);

        return new WebhookSubscriptionResource($record);
    }

    /**
     * Crear o actualizar una suscripción. El secreto se genera solo al
     * crear y no se regenera en ediciones.
     *
     * @param WebhookSubscriptionRequest $request
     * @return array
     */
    public function store(WebhookSubscriptionRequest $request)
    {
        $id = $request->input('id');
        $record = WebhookSubscription::firstOrNew(['id' => $id]);
        $record->fill($request->validated());

        $isNew = !$record->exists;
        if ($isNew) {
            $record->secret = 'whsec_'.Str::random(40);
        }

        $record->save();

        return [
            'success' => true,
            'message' => ($id) ? 'Webhook editado con éxito' : 'Webhook registrado con éxito',
            'data' => new WebhookSubscriptionResource($record),
            // el secret solo se muestra completo al momento de crear
            'secret' => $isNew ? $record->secret : null,
        ];
    }

    /**
     * Activar/desactivar una suscripción. Al reactivar se resetean los
     * contadores de fallos.
     *
     * @param int $id
     * @return array
     */
    public function toggle($id)
    {
        $record = WebhookSubscription::findOrFail($id);

        $record->is_active = !$record->is_active;

        if ($record->is_active) {
            $record->consecutive_failures = 0;
            $record->disabled_at = null;
        } else {
            $record->disabled_at = now();
        }

        $record->save();

        return [
            'success' => true,
            'message' => $record->is_active ? 'Webhook activado' : 'Webhook desactivado',
            'data' => new WebhookSubscriptionResource($record),
        ];
    }

    /**
     * Eliminar una suscripción (y sus entregas, por FK cascade)
     *
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        $record = WebhookSubscription::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Webhook eliminado con éxito',
        ];
    }
}
