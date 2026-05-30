<?php

namespace Modules\Restaurant\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Restaurant\Models\PrintOrder;
use Modules\Restaurant\Models\RestaurantConfiguration;
use App\Models\Tenant\User;

class PrintOrderController extends Controller
{
    /**
     * Registrar una nueva orden de impresión.
     */
    public function store(Request $request)
    {
        // Validación básica
        $data = $request->validate([
            'name_printer' => 'required|string|max:255',
            'pdf_b64' => 'nullable|string',
        ]);
        $data['status'] = false;
        $order = PrintOrder::create($data);
        return response()->json($order, 201);
    }

    /**
     * Actualizar una orden de impresión existente.
     */
    public function update(Request $request, $id)
    {
        $order = PrintOrder::findOrFail($id);
        $data = $request->validate([
            'status' => 'required|integer|in:0,1,2,3',
            'pdf_b64' => 'nullable|string',
        ]);
        $order->update($data);
        return response()->json($order);
    }

    /**
     * Emitir órdenes de impresión pendientes (status = false) vía SSE.
     *
     * IMPORTANTE: Para el correcto funcionamiento de SSE detrás de Nginx,
     * es necesario modificar la configuración del archivo default.conf y reiniciar el proxy y el contenedor nginx.
     *
     * Configuración requerida:
     *
     * server {
     *     ...
     *     fastcgi_buffering off;
     *     proxy_buffering off;
     *     gzip off;
     *     ...
     *     location ~ \.php$ {
     *         ...
     *         fastcgi_read_timeout 3600;
     *         fastcgi_send_timeout 3600;
     *     }
     * }
     *
     * @return \Illuminate\Http\Response
     */
    public function streamPendingOrders(Request $request)
    {
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', false);
        set_time_limit(0);

        $token = $request->query('token');
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            abort(401, 'Token inválido');
        }

        $config = RestaurantConfiguration::first();
        if (!$config || !$config->enabled_server_print) {
            abort(403, 'Impresión por servidor deshabilitada');
        }

        $includeFailed = $request->query('include_failed', false);

        return response()->stream(function () use ($includeFailed) {
            echo "data: " . json_encode(['init' => true, 'message' => 'Stream iniciado']) . "\n\n";
            flush();

            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            // retry SSE
            echo "retry: 5000\n\n";

            // evento init
            echo "event: init\n";
            echo "data: " . json_encode([
                'init' => true,
                'message' => 'Stream iniciado'
            ]) . "\n\n";
            flush();

            // dar tiempo al cliente
            usleep(300000);

            while (true) {
                // Consultar órdenes pendientes (status = 0) y fallidas (status = 2) si corresponde
                $orders = $includeFailed
                    ? PrintOrder::whereIn('status', [0, 2])->get()
                    : PrintOrder::where('status', 0)->get();

                foreach ($orders as $order) {
                    // Marcar como procesando (1) solo si estaba pendiente
                    if ($order->status === 0) {
                        $order->status = 1;
                        $order->save();
                    }
                    // Enviar el registro por SSE
                    echo "data: " . json_encode($order->toArray()) . "\n\n";
                    flush();
                }

                sleep(2);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
