<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\WhatsAppBot\Services\Evolution\EvolutionClient;

class WhatsAppBotController extends Controller
{
    private const NO_INSTANCE_MSG = 'No hay instancia activa.';

    private function buildWebhookUrl(string $token): string
    {
        $override = env('WHATSAPP_WEBHOOK_BASE_URL');
        $base = $override ? rtrim($override, '/') : rtrim(url('/'), '/');
        return $base . '/api/whatsapp-bot/webhook/' . $token;
    }

    public function configuration()
    {
        $configuration = Configuration::first();
        if ($configuration && empty($configuration->evolution_webhook_token)) {
            $configuration->evolution_webhook_token = Str::random(40);
            $configuration->save();
        }

        return view('tenant.whatsapp_bot.configuration', compact('configuration'));
    }

    public function connect(Request $request)
    {
        $data = $request->validate([
            'instance_name' => ['required', 'string', 'regex:/^[A-Za-z0-9_\-]{3,40}$/'],
        ]);

        $instance = $data['instance_name'];

        $config = Configuration::first();
        if (empty($config->evolution_webhook_token)) {
            $config->evolution_webhook_token = Str::random(40);
        }

        try {
            $client = new EvolutionClient();
            $webhookUrl = $this->buildWebhookUrl($config->evolution_webhook_token);
            $client->createInstance($instance, $webhookUrl);
        } catch (\Throwable $e) {
            Log::error('[WhatsAppBot] No se pudo crear instancia', [
                'instance' => $instance,
                'exception' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => 'No se pudo iniciar la conexión: ' . $e->getMessage(),
            ];
        }

        $config->evolution_instance = $instance;
        $config->evolution_connected_phone = null;
        $config->evolution_profile_name = null;
        $config->evolution_connection_state = 'connecting';
        $config->evolution_enabled = true;
        // Quien conecta la instancia queda como "dueño" del bot — el self-chat
        // (cuando el dueño habla consigo mismo) se autoriza usando este id.
        $config->evolution_owner_user_id = auth()->id();
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
        if (empty($config?->evolution_instance)) {
            return ['success' => false, 'message' => self::NO_INSTANCE_MSG];
        }

        try {
            $response = (new EvolutionClient())->connect($config->evolution_instance);
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
        if (empty($config?->evolution_instance)) {
            return ['success' => false, 'connected' => false, 'state' => 'disconnected'];
        }

        try {
            $client = new EvolutionClient();
            $response = $client->connectionState($config->evolution_instance);
            $connected = EvolutionClient::isConnected($response);
            $state = data_get($response, 'instance.state') ?: data_get($response, 'state') ?: 'unknown';

            if ($connected) {
                if ($config->evolution_connection_state !== 'open') {
                    $config->evolution_connection_state = 'open';
                    $config->evolution_connected_at = now();
                }

                if (empty($config->evolution_connected_phone) || empty($config->evolution_profile_name)) {
                    try {
                        $info = $client->fetchInstance($config->evolution_instance);
                        $first = is_array($info) && isset($info[0]) ? $info[0] : $info;
                        // Evolution v2 expone el campo como `ownerJid`. Las
                        // versiones anteriores usaban `owner` o `instance.owner`.
                        $owner = data_get($first, 'instance.owner')
                            ?: data_get($first, 'owner')
                            ?: data_get($first, 'ownerJid');
                        $profileName = data_get($first, 'instance.profileName') ?: data_get($first, 'profileName');

                        if ($owner) {
                            $config->evolution_connected_phone = preg_replace('/\D/', '', explode('@', (string) $owner)[0]);
                        }
                        if ($profileName) {
                            $config->evolution_profile_name = $profileName;
                        }
                    } catch (\Throwable $e) {
                        Log::warning('[WhatsAppBot] No se pudo obtener fetchInstance', ['exception' => $e->getMessage()]);
                    }
                }

                $config->save();

                // Auto-marcar al dueño como usuario habilitado para el bot. La
                // autorizacion vive en users.personal_cell_phone + users.bot_enabled.
                if ($config->evolution_connected_phone && $config->evolution_owner_user_id) {
                    $owner = \App\Models\Tenant\User::find($config->evolution_owner_user_id);
                    if ($owner) {
                        if (empty($owner->personal_cell_phone)) {
                            $owner->personal_cell_phone = $config->evolution_connected_phone;
                        }
                        $owner->bot_enabled = true;
                        $owner->save();
                    }
                }
            } elseif ($config->evolution_connection_state !== $state) {
                $config->evolution_connection_state = $state;
                $config->save();
            }

            return [
                'success' => true,
                'connected' => $connected,
                'state' => $state,
                'instance_name' => $config->evolution_instance,
                'connected_phone' => $config->evolution_connected_phone,
                'profile_name' => $config->evolution_profile_name,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Error al consultar estado: ' . $e->getMessage()];
        }
    }

    public function disconnect()
    {
        $config = Configuration::first();
        if (empty($config?->evolution_instance)) {
            return ['success' => false, 'message' => self::NO_INSTANCE_MSG];
        }

        try {
            (new EvolutionClient())->deleteInstance($config->evolution_instance);
        } catch (\Throwable $e) {
            Log::warning('[WhatsAppBot] deleteInstance fallo (continuo limpiando local)', [
                'exception' => $e->getMessage(),
            ]);
        }

        $config->evolution_instance = null;
        $config->evolution_connected_phone = null;
        $config->evolution_profile_name = null;
        $config->evolution_connection_state = 'disconnected';
        $config->evolution_connected_at = null;
        $config->evolution_enabled = false;
        $config->save();

        return ['success' => true, 'message' => 'Número desconectado.'];
    }

    public function restart()
    {
        $config = Configuration::first();
        if (empty($config?->evolution_instance)) {
            return ['success' => false, 'message' => self::NO_INSTANCE_MSG];
        }

        try {
            (new EvolutionClient())->restartInstance($config->evolution_instance);
            return ['success' => true, 'message' => 'Instancia reiniciada.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'No se pudo reiniciar: ' . $e->getMessage()];
        }
    }

    public function renew()
    {
        $config = Configuration::first();
        if (empty($config?->evolution_instance)) {
            return ['success' => false, 'message' => self::NO_INSTANCE_MSG];
        }

        $instance = $config->evolution_instance;
        $client = new EvolutionClient();

        try {
            $client->deleteInstance($instance);
        } catch (\Throwable $e) {
            Log::warning('[WhatsAppBot] renew: deleteInstance fallo', ['exception' => $e->getMessage()]);
        }

        sleep(2);

        try {
            $client->createInstance($instance);
            $webhookUrl = $this->buildWebhookUrl($config->evolution_webhook_token);
            $client->setWebhook($instance, $webhookUrl);
        } catch (\Throwable $e) {
            Log::error('[WhatsAppBot] renew: no se pudo recrear instancia', ['exception' => $e->getMessage()]);
            return ['success' => false, 'message' => 'No se pudo renovar la instancia: ' . $e->getMessage()];
        }

        $config->evolution_connected_phone = null;
        $config->evolution_profile_name = null;
        $config->evolution_connection_state = 'connecting';
        $config->evolution_connected_at = null;
        $config->save();

        return [
            'success' => true,
            'message' => 'Instancia renovada. Escanea el nuevo QR para reconectar.',
        ];
    }

    public function toggleEnabled(Request $request)
    {
        $request->validate(['enabled' => 'required|boolean']);
        $config = Configuration::first();
        $config->whatsapp_bot_enabled = (bool) $request->enabled;
        $config->save();

        return [
            'success' => true,
            'enabled' => (bool) $config->whatsapp_bot_enabled,
            'message' => $config->whatsapp_bot_enabled
                ? 'Bot activado. Volverá a procesar mensajes.'
                : 'Bot desactivado. No procesará mensajes hasta reactivarlo.',
        ];
    }

    public function storeCommands(Request $request)
    {
        $data = $request->validate([
            'bot_trigger_command' => ['required', 'string', 'max:20', 'regex:/^\S+$/'],
            'bot_pause_command' => ['required', 'string', 'max:20', 'regex:/^\S+$/'],
            'bot_exit_command' => ['required', 'string', 'max:20', 'regex:/^\S+$/'],
        ]);

        $trigger = mb_strtolower(trim($data['bot_trigger_command']));
        $pause = mb_strtolower(trim($data['bot_pause_command']));
        $exit = mb_strtolower(trim($data['bot_exit_command']));

        if (count(array_unique([$trigger, $pause, $exit])) !== 3) {
            return [
                'success' => false,
                'message' => 'Los tres comandos deben ser distintos entre sí.',
            ];
        }

        $record = Configuration::first();
        $record->bot_trigger_command = $trigger;
        $record->bot_pause_command = $pause;
        $record->bot_exit_command = $exit;
        $record->save();

        return [
            'success' => true,
            'message' => 'Comandos actualizados.',
        ];
    }
}
