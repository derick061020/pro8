<?php

namespace Modules\Restaurant\Http\Controllers;

use Exception;
use Hyn\Tenancy\Contracts\CurrentHostname;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Restaurant\Models\Printer;
use Modules\Restaurant\Models\RestaurantConfiguration;

class PrinterController extends Controller
{
    /**
     * Devuelve la configuración de impresión guardada en BD.
     * Incluye la lista de impresoras activas y los campos de configuración.
     *
     * Se usa firstOrNew() en lugar de first() para evitar que crashee en PHP 8
     * cuando el tenant aún no tiene registro en restaurant_configurations.
     * Con first() + $config->propiedad el resultado era un fatal error (null object),
     * el frontend lo veía como 500 silencioso y el panel siempre arrancaba en blanco.
     * firstOrNew() devuelve un modelo vacío (sin persistir) que permite aplicar ?? normalmente.
     */
    public function getConfig(): JsonResponse
    {
        $config = RestaurantConfiguration::firstOrNew([]);

        $printers = Printer::where('active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id', 'name', 'is_default', 'last_seen_at']);

        return response()->json([
            'success' => true,
            'data'    => [
                'printer_enabled'        => (bool) ($config->printer_enabled ?? false),
                'printer_host'           => $config->printer_host ?? '',
                'printer_status'         => $config->printer_status ?? null,
                'printer_public_ip'      => $config->printer_public_ip ?? null,
                'print_local_enabled'    => (bool) ($config->print_local_enabled ?? false),
                'printer_name_comanda'    => $config->printer_name_comanda ?? null,
                'printer_name_documents'  => $config->printer_name_documents ?? null,
                'printer_name_precuenta'  => $config->printer_name_precuenta ?? null,
                'printer_per_area_enabled' => (bool) ($config->printer_per_area_enabled ?? false),
                'print_destination'        => (bool) ($config->print_destination ?? false),
                'printers'                 => $printers,
            ],
        ]);
    }

    /**
     * Guarda la configuración de impresión (campos escalares).
     * El campo printer_status se actualiza por separado vía updateStatus().
     */
    public function saveConfig(Request $request): JsonResponse
    {
        $data = $request->validate([
            'printer_enabled'        => 'nullable|boolean',
            'print_local_enabled'    => 'nullable|boolean',
            'printer_name_comanda'    => 'nullable|string|max:255',
            'printer_name_documents'  => 'nullable|string|max:255',
            'printer_name_precuenta'  => 'nullable|string|max:255',
            'printer_per_area_enabled' => 'nullable|boolean',
            'print_destination'        => 'nullable|boolean',
        ]);

        $config = RestaurantConfiguration::firstOrNew([]);
        $config->fill($data);
        $config->save();

        return response()->json([
            'success' => true,
            'message' => 'Configuración de impresión guardada.',
        ]);
    }

    /**
     * Actualiza el campo printer_status.
     * Este campo es puramente informativo: registra si el Facturador pudo
     * contactar a BuhoPrinter en la última verificación desde este panel.
     * No representa el estado en tiempo real del servicio de impresión.
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $data = $request->validate([
            'printer_status'    => 'required|string|in:connected,disconnected',
            'printer_public_ip' => 'nullable|ip',
        ]);

        $config = RestaurantConfiguration::firstOrNew([]);
        $config->printer_status = $data['printer_status'];

        // Registrar la IP pública del cliente solo cuando la conexión es exitosa
        if ($data['printer_status'] === 'connected' && !empty($data['printer_public_ip'])) {
            $config->printer_public_ip = $data['printer_public_ip'];
        }

        $config->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado registrado.',
        ]);
    }

    /**
     * Sincroniza la lista de impresoras obtenida desde BuhoPrinter.
     * El frontend llama directamente a BuhoPrinter (/printers) y envía
     * el resultado aquí para persistirlo. Las impresoras no presentes en
     * la lista se marcan como inactivas.
     */
    public function syncPrinters(Request $request): JsonResponse
    {
        $data = $request->validate([
            'printers'              => 'required|array',
            'printers.*.name'       => 'required|string|max:255',
            'printers.*.is_default' => 'required|boolean',
        ]);

        $incomingNames = collect($data['printers'])->pluck('name');

        foreach ($data['printers'] as $printer) {
            Printer::updateOrCreate(
                ['name' => $printer['name']],
                [
                    'is_default'   => $printer['is_default'],
                    'active'       => true,
                    'last_seen_at' => now(),
                ]
            );
        }

        // Marcar como inactivas las impresoras que ya no reporta BuhoPrinter
        Printer::whereNotIn('name', $incomingNames)->update(['active' => false]);

        $printers = Printer::where('active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id', 'name', 'is_default', 'last_seen_at']);

        return response()->json([
            'success'  => true,
            'message'  => 'Impresoras sincronizadas correctamente.',
            'printers' => $printers,
        ]);
    }

    /**
     * Lista las impresoras activas registradas en BD.
     * Utilizado por Mozo para construir el selector de impresora al crear una orden de impresión.
     */
    public function index(): JsonResponse
    {
        try {
            $printers = Printer::where('active', true)
                ->orderByDesc('is_default')
                ->orderBy('name')
                ->get(['id', 'name', 'is_default']);

            return response()->json([
                'success' => true,
                'data'    => $printers,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Genera el payload de configuración para BuhoPrinter y persiste el host del agente.
     *
     * Flujo:
     *  1. El frontend descubre el puerto de BuhoPrinter (mixin buhoprinter, localhost).
     *  2. El frontend llama a este endpoint con el host descubierto.
     *  3. Este método devuelve el payload como base64(JSON) — string opaco para el browser.
     *  4. El frontend reenvía el blob directamente a BuhoPrinter POST /configure.
     *  5. BuhoPrinter hace base64_decode → JSON → objeto de configuración. Sin dependencias extras.
     *
     * La razón por la que el backend NO hace el POST a BuhoPrinter directamente es que
     * BuhoPrinter corre en localhost del cliente — no es accesible desde el servidor VPS.
     * El tránsito Facturador ↔ browser está protegido por HTTPS.
     */
    public function configure(Request $request): JsonResponse
    {
        $data = $request->validate([
            'buhoprinter_host' => 'required|string|max:255',
        ]);

        // Persistir el host del agente para consultas futuras
        $config = RestaurantConfiguration::firstOrNew([]);
        $config->printer_host = $data['buhoprinter_host'];
        $config->save();

        $fqdn       = app(CurrentHostname::class)?->fqdn ?? $request->getHost();
        $protocol   = config('tenant.force_https', false) ? 'https' : $request->getScheme();
        $apiBaseUrl = $protocol . '://' . $fqdn;

        // Base64 del JSON — BuhoPrinter solo necesita base64_decode para obtener el objeto.
        // El tránsito está protegido por HTTPS; base64 evita que sea legible a simple vista.
        $encryptedBlob = base64_encode(json_encode([
            'redis_port'   => (int) config('database.redis.default.port', env('REDIS_PORT', 6379)),
            'fqdn_tenant'  => $fqdn,
            'api_base_url' => $apiBaseUrl,
            'api_token'    => auth()->user()?->api_token,
        ]));

        return response()->json([
            'success' => true,
            'payload' => $encryptedBlob,
        ]);
    }
}
