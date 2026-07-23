<?php

namespace Modules\WhatsAppBot\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\WhatsAppBot\Models\BotMessage;
use Modules\WhatsAppBot\Services\BotOrchestrator;

class WebhookController extends Controller
{
    private const LOCK_TTL_SECONDS = 60;

    public function __construct(private BotOrchestrator $orchestrator)
    {
    }

    public function receive(Request $request, string $token)
    {
        $configuration = Configuration::first();

        if (!$configuration || !$configuration->evolution_webhook_token || !hash_equals($configuration->evolution_webhook_token, $token)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        // Toggle global del bot (Configuracion del Bot > tab Bot conversacional).
        // Si esta OFF, devolvemos 200 sin procesar para que Evolution no reintente.
        if (!$configuration->whatsapp_bot_enabled) {
            return response()->json(['ok' => true, 'ignored' => 'bot_disabled']);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? null;

        if (!in_array($event, ['messages.upsert', 'send.message'], true)) {
            return response()->json(['ok' => true, 'ignored' => $event]);
        }

        $data = $payload['data'] ?? [];
        $remoteJid = data_get($data, 'key.remoteJid', '');

        // Ignorar grupos (JID termina en @g.us) y broadcast (@broadcast)
        if ($remoteJid && (str_ends_with($remoteJid, '@g.us') || str_ends_with($remoteJid, '@broadcast'))) {
            return response()->json(['ok' => true, 'ignored' => 'group_or_broadcast']);
        }

        // Direccionamiento LID: el remitente llega como <id>@lid sin numero real.
        // El proxy bot-proxy-v2 inyecta key.senderPn con el numero real.
        $senderPn = data_get($data, 'key.senderPn', '');
        $remoteJidAlt = data_get($data, 'key.remoteJidAlt', '');
        $phoneJid = str_ends_with($remoteJid, '@lid') ? ($senderPn ?: $remoteJidAlt) : $remoteJid;

        $phone = $phoneJid ? explode('@', $phoneJid)[0] : null;
        $fromMe = (bool) data_get($data, 'key.fromMe', false);
        $messageId = data_get($data, 'key.id');
        $body = data_get($data, 'message.conversation')
            ?? data_get($data, 'message.extendedTextMessage.text')
            ?? null;

        if ($messageId && BotMessage::where('message_id', $messageId)->exists()) {
            Log::info('[WhatsAppBot] Webhook duplicado ignorado', [
                'message_id' => $messageId,
                'phone' => $phone,
            ]);
            return response()->json(['ok' => true, 'deduped' => true]);
        }

        // Self-chat: el dueño habla consigo mismo en su propio numero conectado.
        // Evolution lo manda con fromMe=true y remoteJid igual al numero del bot.
        // En ese caso procesamos como mensaje del dueño (autorizado via
        // evolution_owner_user_id). En cualquier otro fromMe (mensaje del dueño
        // a un cliente) lo ignoramos — no usamos eso como señal de nada.
        $connectedPhone = $configuration->evolution_connected_phone;
        $isSelfChat = $fromMe && $connectedPhone && $phone === $connectedPhone;

        if ($fromMe && !$isSelfChat) {
            return response()->json(['ok' => true, 'ignored' => 'from_me_to_other']);
        }

        BotMessage::create([
            'session_id' => null,
            'direction' => 'in',
            'phone' => $phone,
            'body' => $body,
            'message_id' => $messageId,
            'from_me' => $fromMe,
            'raw_payload' => $payload,
        ]);

        Log::info('[WhatsAppBot] webhook received', [
            'phone' => $phone,
            'message_id' => $messageId,
            'self_chat' => $isSelfChat,
        ]);

        if (empty($phone) || empty($body)) {
            return response()->json(['ok' => true]);
        }

        $lock = Cache::lock("whatsapp-bot:phone:{$phone}", self::LOCK_TTL_SECONDS);

        if (!$lock->get()) {
            Log::warning('[WhatsAppBot] Otro mensaje del phone ya está siendo procesado, ignorado', [
                'phone' => $phone,
                'message_id' => $messageId,
            ]);
            return response()->json(['ok' => true, 'locked' => true]);
        }

        try {
            $this->orchestrator->handleIncoming($phone, $body, $isSelfChat);
        } catch (\Throwable $e) {
            Log::error('[WhatsAppBot] Orchestrator falló', [
                'exception' => $e->getMessage(),
                'phone' => $phone,
            ]);
        } finally {
            $lock->release();
        }

        return response()->json(['ok' => true]);
    }
}
