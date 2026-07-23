<?php

namespace Modules\QrApi\Http\Controllers;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\WhatsappMessageLog;
use App\Traits\LockedEmissionTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\WhatsAppBot\Services\Evolution\EvolutionClient;

class QrApiController extends Controller
{
    use StorageDocument;
    use LockedEmissionTrait;

    private const NO_INSTANCE_MSG = 'QR Api no tiene una instancia conectada.';

    private function buildWebhookUrl(string $token): string
    {
        $override = env('WHATSAPP_WEBHOOK_BASE_URL');
        $base = $override ? rtrim($override, '/') : rtrim(url('/'), '/');
        return $base . '/api/qr-api/webhook/' . $token;
    }

    private function effectiveInstance(Configuration $config): ?string
    {
        if ($config->qr_api_use_bot_instance) {
            return $config->evolution_instance;
        }
        return $config->qr_api_instance;
    }

    public function getConfig()
    {
        $data = Configuration::first();
        $whatsapp_usage = $this->getWhatsappMessagesUsage();
        return [
            'qr_api_enable_ws' => (bool) ($data->qr_api_enable ?? false),
            'qr_api_use_bot_instance' => (bool) ($data->qr_api_use_bot_instance ?? false),
            'qr_api_pdf_format' => $data->qr_api_pdf_format ?? 'ticket',
            'qr_api_instance' => $data->qr_api_instance,
            'qr_api_connected_phone' => $data->qr_api_connected_phone,
            'qr_api_profile_name' => $data->qr_api_profile_name,
            'qr_api_connection_state' => $data->qr_api_connection_state ?? 'disconnected',
            'evolution_instance' => $data->evolution_instance,
            'evolution_connected_phone' => $data->evolution_connected_phone,
            'whatsapp_messages_used' => $whatsapp_usage['used'],
            'whatsapp_messages_limit' => $whatsapp_usage['limit'],
            'whatsapp_messages_unlimited' => $whatsapp_usage['unlimited'],
        ];
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'qr_api_enable_ws' => 'required|boolean',
            'qr_api_use_bot_instance' => 'sometimes|boolean',
            'qr_api_pdf_format' => 'sometimes|in:a4,ticket',
        ]);

        $config = Configuration::first();
        $config->qr_api_enable = (bool) $request->qr_api_enable_ws;

        if ($request->has('qr_api_pdf_format')) {
            $config->qr_api_pdf_format = $request->qr_api_pdf_format;
        }

        if ($request->has('qr_api_use_bot_instance')) {
            $newValue = (bool) $request->qr_api_use_bot_instance;

            // Si está activando "usar el mismo del bot" y hay una instancia propia,
            // la desconectamos para no dejar instancias zombie en Evolution.
            if ($newValue && !$config->qr_api_use_bot_instance && !empty($config->qr_api_instance)) {
                try {
                    (new EvolutionClient())->deleteInstance($config->qr_api_instance);
                } catch (\Throwable $e) {
                    Log::warning('[QrApi] No se pudo borrar instancia previa al cambiar a modo bot', [
                        'exception' => $e->getMessage(),
                    ]);
                }
                $config->qr_api_instance = null;
                $config->qr_api_connected_phone = null;
                $config->qr_api_profile_name = null;
                $config->qr_api_connection_state = 'disconnected';
                $config->qr_api_connected_at = null;
            }

            $config->qr_api_use_bot_instance = $newValue;
        }

        $config->save();

        return ['success' => true, 'message' => 'Configuración actualizada.'];
    }

    public function connect(Request $request)
    {
        $data = $request->validate([
            'instance_name' => ['required', 'string', 'regex:/^[A-Za-z0-9_\-]{3,40}$/'],
        ]);

        $instance = $data['instance_name'];

        $config = Configuration::first();
        if (empty($config->qr_api_webhook_token)) {
            $config->qr_api_webhook_token = Str::random(40);
        }

        try {
            $client = new EvolutionClient();
            $webhookUrl = $this->buildWebhookUrl($config->qr_api_webhook_token);
            $client->createInstance($instance, $webhookUrl);
        } catch (\Throwable $e) {
            Log::error('[QrApi] No se pudo crear instancia', [
                'instance' => $instance,
                'exception' => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => 'No se pudo iniciar la conexión: ' . $e->getMessage()];
        }

        $config->qr_api_instance = $instance;
        $config->qr_api_connected_phone = null;
        $config->qr_api_profile_name = null;
        $config->qr_api_connection_state = 'connecting';
        $config->qr_api_use_bot_instance = false;
        $config->qr_api_enable = true;
        $config->save();

        return [
            'success' => true,
            'instance_name' => $instance,
            'message' => 'Instancia creada. Escanea el código QR para vincular el WhatsApp.',
        ];
    }

    public function qr()
    {
        $config = Configuration::first();
        $instance = $this->effectiveInstance($config);
        if (empty($instance)) {
            return ['success' => false, 'message' => self::NO_INSTANCE_MSG];
        }

        try {
            $response = (new EvolutionClient())->connect($instance);
            $qr = EvolutionClient::extractQr($response);
            return [
                'success' => $qr !== null,
                'qr' => $qr,
                'message' => $qr ? null : 'Evolution no devolvió un QR válido.',
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Error al obtener QR: ' . $e->getMessage()];
        }
    }

    public function state()
    {
        $config = Configuration::first();
        $instance = $this->effectiveInstance($config);
        if (empty($instance)) {
            return ['success' => false, 'connected' => false, 'state' => 'disconnected'];
        }

        try {
            $client = new EvolutionClient();
            $response = $client->connectionState($instance);
            $connected = EvolutionClient::isConnected($response);
            $state = data_get($response, 'instance.state') ?: data_get($response, 'state') ?: 'unknown';

            // Si esta instancia es propia (no compartida con el bot), guardamos los datos.
            if (!$config->qr_api_use_bot_instance) {
                if ($connected && $config->qr_api_connection_state !== 'open') {
                    $config->qr_api_connection_state = 'open';
                    $config->qr_api_connected_at = now();
                }
                if ($connected && (empty($config->qr_api_connected_phone) || empty($config->qr_api_profile_name))) {
                    try {
                        $info = $client->fetchInstance($instance);
                        $first = is_array($info) && isset($info[0]) ? $info[0] : $info;
                        $owner = data_get($first, 'instance.owner') ?: data_get($first, 'owner');
                        $profileName = data_get($first, 'instance.profileName') ?: data_get($first, 'profileName');
                        if ($owner) {
                            $config->qr_api_connected_phone = preg_replace('/\D/', '', explode('@', (string) $owner)[0]);
                        }
                        if ($profileName) {
                            $config->qr_api_profile_name = $profileName;
                        }
                    } catch (\Throwable $e) {
                        Log::warning('[QrApi] fetchInstance fallo', ['exception' => $e->getMessage()]);
                    }
                }
                if (!$connected && $config->qr_api_connection_state !== $state) {
                    $config->qr_api_connection_state = $state;
                }
                $config->save();
            }

            return [
                'success' => true,
                'connected' => $connected,
                'state' => $state,
                'instance_name' => $instance,
                'connected_phone' => $config->qr_api_use_bot_instance
                    ? $config->evolution_connected_phone
                    : $config->qr_api_connected_phone,
                'profile_name' => $config->qr_api_use_bot_instance
                    ? $config->evolution_profile_name
                    : $config->qr_api_profile_name,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Error al consultar estado: ' . $e->getMessage()];
        }
    }

    public function disconnect()
    {
        $config = Configuration::first();
        if (empty($config->qr_api_instance)) {
            return ['success' => false, 'message' => self::NO_INSTANCE_MSG];
        }

        try {
            (new EvolutionClient())->deleteInstance($config->qr_api_instance);
        } catch (\Throwable $e) {
            Log::warning('[QrApi] deleteInstance fallo (continuo limpiando local)', [
                'exception' => $e->getMessage(),
            ]);
        }

        $config->qr_api_instance = null;
        $config->qr_api_connected_phone = null;
        $config->qr_api_profile_name = null;
        $config->qr_api_connection_state = 'disconnected';
        $config->qr_api_connected_at = null;
        $config->qr_api_enable = false;
        $config->save();

        return ['success' => true, 'message' => 'Número desconectado.'];
    }

    public function restart()
    {
        $config = Configuration::first();
        $instance = $this->effectiveInstance($config);
        if (empty($instance)) {
            return ['success' => false, 'message' => self::NO_INSTANCE_MSG];
        }

        try {
            (new EvolutionClient())->restartInstance($instance);
            return ['success' => true, 'message' => 'Instancia reiniciada.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'No se pudo reiniciar: ' . $e->getMessage()];
        }
    }

    public function renew()
    {
        $config = Configuration::first();
        if (empty($config->qr_api_instance)) {
            return ['success' => false, 'message' => self::NO_INSTANCE_MSG];
        }

        $instance = $config->qr_api_instance;
        $client = new EvolutionClient();

        try {
            $client->deleteInstance($instance);
        } catch (\Throwable $e) {
            Log::warning('[QrApi] renew: deleteInstance fallo', ['exception' => $e->getMessage()]);
        }

        sleep(2);

        try {
            $webhookUrl = $this->buildWebhookUrl($config->qr_api_webhook_token);
            $client->createInstance($instance, $webhookUrl);
        } catch (\Throwable $e) {
            Log::error('[QrApi] renew: no se pudo recrear instancia', ['exception' => $e->getMessage()]);
            return ['success' => false, 'message' => 'No se pudo renovar la instancia: ' . $e->getMessage()];
        }

        $config->qr_api_connected_phone = null;
        $config->qr_api_profile_name = null;
        $config->qr_api_connection_state = 'connecting';
        $config->qr_api_connected_at = null;
        $config->save();

        return ['success' => true, 'message' => 'Instancia renovada. Escanea el nuevo QR para reconectar.'];
    }

    public function encodeBase64(Request $request)
    {
        $request->validate([
            'filename_only' => 'required',
            'extension_only' => 'required',
        ]);

        $filename = $request->filename_only;
        $content_file = $this->getStorage($filename, 'pdf');
        return base64_encode($content_file);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'file' => 'required',
            'number' => 'required',
            'message' => 'required',
            'filename' => 'required',
            'link' => 'sometimes|nullable|string',
        ]);

        $exceed_limit = $this->exceedLimitWhatsappMessages();
        if ($exceed_limit['success']) {
            return response()->json([
                'success' => false,
                'message' => $exceed_limit['message'],
            ], 422);
        }

        $config = Configuration::first();

        if(!$config->qr_api_enable) {
            return response()->json([
                'success' => false,
                'message' => 'La opción de enviar mensajes no está habilitada'
            ], 422);
        }

        $instance = $this->effectiveInstance($config);

        if (empty($instance)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Api no tiene un número conectado. Configúralo en Configuración → QR Api.',
            ], 422);
        }

        $evolution = new EvolutionClient();

        $mediaResponse = null;
        $mediaOk = false;
        try {
            $mediaResponse = $evolution->sendMedia($instance, $request->number, [
                'mediatype' => 'document',
                'mimetype' => 'application/pdf',
                'media' => $request->file,
                'fileName' => $request->filename,
                'caption' => $request->message,
            ]);
            $mediaOk = true;
        } catch (\Throwable $e) {
            Log::error('[QrApi] sendMessage sendMedia fallo', ['exception' => $e->getMessage()]);
        }

        $textOk = false;
        if ($request->filled('link')) {
            try {
                $text = $request->message . ': puede revisarlo en el siguiente enlace ' . $request->link;
                $evolution->sendText($instance, $request->number, $text);
                $textOk = true;
            } catch (\Throwable $e) {
                Log::error('[QrApi] sendMessage sendText fallo', ['exception' => $e->getMessage()]);
            }
        }

        if ($mediaOk || $textOk) {
            WhatsappMessageLog::create([
                'phone' => $request->number,
                'filename' => $request->filename,
                'status' => 'sent',
            ]);

            return response()->json([
                'success' => true,
                'data' => $mediaResponse,
                'media_sent' => $mediaOk,
                'link_sent' => $textOk,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se pudo enviar el comprobante por WhatsApp.',
        ], 500);
    }
}
