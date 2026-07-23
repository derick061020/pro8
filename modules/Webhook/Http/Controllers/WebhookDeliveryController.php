<?php

namespace Modules\Webhook\Http\Controllers;

use Hyn\Tenancy\Environment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Webhook\Http\Resources\WebhookDeliveryResource;
use Modules\Webhook\Jobs\SendWebhookJob;
use Modules\Webhook\Models\WebhookDelivery;

class WebhookDeliveryController extends Controller
{
    /**
     * Log paginado de entregas, con filtros opcionales
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function records(Request $request)
    {
        $records = WebhookDelivery::with('subscription:id,name,url')
            ->when($request->subscription_id, function ($query, $subscriptionId) {
                $query->where('webhook_subscription_id', $subscriptionId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->event, function ($query, $event) {
                $query->where('event', $event);
            })
            ->orderBy('id', 'desc')
            ->paginate(config('tenant.items_per_page'));

        return response()->json([
            'data' => WebhookDeliveryResource::collection($records),
            'current_page' => $records->currentPage(),
            'per_page' => $records->perPage(),
            'total' => $records->total(),
            'last_page' => $records->lastPage(),
        ]);
    }

    /**
     * Reenviar una entrega manualmente. Reutiliza la misma fila (mismo
     * event_id, idempotencia para el receptor) y reinicia el ciclo de envío.
     *
     * @param int $id
     * @return array
     */
    public function resend($id)
    {
        $delivery = WebhookDelivery::with('subscription')->findOrFail($id);

        if (!$delivery->subscription) {
            return [
                'success' => false,
                'message' => 'La suscripción de esta entrega ya no existe',
            ];
        }

        $delivery->update([
            'status' => WebhookDelivery::STATUS_PENDING,
            'error_message' => null,
        ]);

        $websiteId = app(Environment::class)->tenant()->id;
        SendWebhookJob::dispatch($websiteId, $delivery->id);

        return [
            'success' => true,
            'message' => 'Reenvío encolado con éxito',
        ];
    }
}
