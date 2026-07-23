<?php

namespace Modules\WhatsAppBot\Services\Evolution;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Models\Tenant\Configuration;
use Illuminate\Support\Facades\Log;
use Modules\WhatsAppBot\Models\BotMessage;

class EvolutionSender
{
    use StorageDocument;

    public function sendTyping(string $toPhone, int $delay = 1200): void
    {
        $config = Configuration::first();
        if (!$config || empty($config->evolution_instance)) {
            return;
        }

        try {
            (new EvolutionClient())->sendPresence($config->evolution_instance, $toPhone, 'composing', $delay);
        } catch (\Throwable $e) {
            // silent: presence is best-effort
        }
    }

    public function sendText(string $toPhone, string $text, ?int $sessionId = null): ?array
    {
        $config = Configuration::first();

        if (!$config || empty($config->evolution_instance)) {
            Log::error('[WhatsAppBot] EvolutionSender: tenant sin instance configurada');
            return null;
        }

        try {
            $data = (new EvolutionClient())->sendText($config->evolution_instance, $toPhone, $text);
        } catch (\Throwable $e) {
            Log::error('[WhatsAppBot] Evolution sendText exception', [
                'exception' => $e->getMessage(),
                'to' => $toPhone,
            ]);
            return null;
        }

        $messageId = data_get($data, 'key.id');

        BotMessage::create([
            'session_id' => $sessionId,
            'direction' => 'out',
            'phone' => $toPhone,
            'body' => $text,
            'message_id' => $messageId,
            'from_me' => true,
            'raw_payload' => $data,
        ]);

        return $data;
    }

    public function sendDocumentPdf(string $toPhone, string $filenameBase, string $displayFilename, ?string $caption = null, ?int $sessionId = null): ?array
    {
        $config = Configuration::first();

        if (!$config || empty($config->evolution_instance)) {
            Log::error('[WhatsAppBot] EvolutionSender PDF: tenant sin instance configurada');
            return null;
        }

        try {
            $pdfBinary = $this->getStorage($filenameBase, 'pdf');
        } catch (\Throwable $e) {
            Log::error('[WhatsAppBot] No se pudo leer el PDF del storage', [
                'filename' => $filenameBase,
                'exception' => $e->getMessage(),
            ]);
            return null;
        }

        try {
            $data = (new EvolutionClient())->sendMedia($config->evolution_instance, $toPhone, [
                'mediatype' => 'document',
                'mimetype' => 'application/pdf',
                'media' => base64_encode($pdfBinary),
                'fileName' => $displayFilename,
                'caption' => $caption ?: $displayFilename,
            ]);
        } catch (\Throwable $e) {
            Log::error('[WhatsAppBot] Evolution sendMedia (PDF) exception', [
                'exception' => $e->getMessage(),
                'to' => $toPhone,
            ]);
            return null;
        }

        $messageId = data_get($data, 'key.id');

        BotMessage::create([
            'session_id' => $sessionId,
            'direction' => 'out',
            'phone' => $toPhone,
            'body' => '[PDF] ' . $displayFilename . ($caption ? ' — ' . $caption : ''),
            'message_id' => $messageId,
            'from_me' => true,
            'raw_payload' => $data,
        ]);

        return $data;
    }
}
