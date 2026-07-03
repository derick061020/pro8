<?php

namespace Modules\Restaurant\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Restaurant\Models\PrintOrder;
use Modules\Restaurant\Models\Printer;
use Modules\Restaurant\Models\RestaurantConfiguration;

class PrintOrderController extends Controller
{
    /**
     * Registrar una nueva orden de impresión.
     * El observer la publica en Redis automáticamente al persistir.
     * Si no se proporciona nombre de impresora, se usa la impresora predeterminada.
     * Si no hay ninguna impresora configurada, retorna error 422.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name_printer'      => 'nullable|string|max:255',
            'pdf_b64'           => 'nullable|string',
            'client_public_ip'  => 'nullable|ip',
        ]);

        // Validar impresión local: solo permite órdenes desde la misma red que BuhoPrinter
        $config = RestaurantConfiguration::first();
        if (
            $config &&
            $config->print_local_enabled &&
            $config->printer_public_ip
        ) {
            $clientIp = $data['client_public_ip'] ?? null;

            if ($clientIp !== $config->printer_public_ip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impresión local activa: esta terminal no está en la red del servicio de impresión.',
                ], 403);
            }
        }

        // Resolver impresora: usar la enviada o buscar la predeterminada
        if (empty($data['name_printer'])) {
            $defaultPrinter = Printer::where('active', true)
                ->where('is_default', true)
                ->first();

            if (!$defaultPrinter) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay impresoras configuradas. Configure una impresora predeterminada en BuhoPrinter.',
                ], 422);
            }

            $data['name_printer'] = $defaultPrinter->name;
        }

        $data['status'] = 0;
        $order = PrintOrder::create($data);

        return response()->json($order, 201);
    }

    /**
     * BuhoPrinter confirma el resultado de impresión.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $order = PrintOrder::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|integer|in:0,1,2,3',
        ]);

        $order->update($data);

        return response()->json($order);
    }

    /**
     * Fallback: órdenes pendientes para BuhoPrinter al reconectar.
     * Devuelve trabajos que llegaron mientras Redis estaba desconectado.
     */
    public function pending(): JsonResponse
    {
        $orders = PrintOrder::where('status', 0)
            ->orderBy('created_at')
            ->get();

        return response()->json($orders);
    }
}
