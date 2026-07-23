<?php

namespace Modules\WhatsAppBot\Services;

use App\Models\Tenant\Company;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\User;
use Illuminate\Support\Facades\Log;
use Modules\WhatsAppBot\Models\BotSession;
use Modules\WhatsAppBot\Prompts\SystemPrompt;
use Modules\WhatsAppBot\Services\Evolution\EvolutionSender;
use Modules\WhatsAppBot\Services\OpenAi\OpenAiClient;
use Modules\WhatsAppBot\Services\Session\SessionMachine;
use Modules\WhatsAppBot\Services\Tools\CreateDocumentTool;
use Modules\WhatsAppBot\Services\Tools\GetCompanyInfoTool;
use Modules\WhatsAppBot\Services\Tools\ListRecentDocumentsTool;
use Modules\WhatsAppBot\Services\Tools\LookupPersonTool;
use Modules\WhatsAppBot\Services\Tools\QueryDocumentStatusTool;
use Modules\WhatsAppBot\Services\Tools\RegisterPersonFromReniecTool;
use Modules\WhatsAppBot\Services\Tools\SearchItemsTool;
use Modules\WhatsAppBot\Services\Tools\SendDocumentPdfTool;
use Modules\WhatsAppBot\Services\Tools\ToolRegistry;
use Modules\WhatsAppBot\Services\Validators\AntiHallucinationValidator;
use Modules\WhatsAppBot\Services\Validators\PolicyValidator;
use Modules\WhatsAppBot\Services\Validators\SystemGuardValidator;
use Modules\WhatsAppBot\Services\Validators\ValidatorChain;

class BotOrchestrator
{
    public function __construct(
        private SessionMachine $sessions,
        private EvolutionSender $sender,
        private IntentClassifier $intent,
    ) {
    }

    private function matchesCommand(string $message, ?string $configured): bool
    {
        if (empty($configured)) {
            return false;
        }
        return mb_strtolower(trim($message)) === mb_strtolower(trim($configured));
    }

    public function handleIncoming(string $fromPhone, string $body, bool $isOwnerSelfChat = false): void
    {
        $config = Configuration::first();
        // El proveedor de IA vive en el system (lo configura el reseller para
        // todos los tenants). Si no hay key configurada, no podemos procesar.
        $systemConfig = \App\Models\System\Configuration::first();
        if (!$config || !$systemConfig || empty($systemConfig->openai_api_key)) {
            Log::info('[WhatsAppBot] Orchestrator: openai_api_key (system) vacio, ignorando');
            return;
        }

        // Self-chat del dueño: salta el lookup por phone. Usamos evolution_owner_user_id
        // (seteado al conectar la instancia) como user del bot. En cualquier otro caso
        // buscamos al user del tenant que tenga ese personal_cell_phone con bot_enabled=true.
        // Si el user esta locked (suspendido), el bot lo rechaza aunque bot_enabled=true.
        if ($isOwnerSelfChat && $config->evolution_owner_user_id) {
            $user = User::find($config->evolution_owner_user_id);
        } else {
            // personal_cell_phone puede estar guardado con prefijo de pais o sin el.
            // Comparamos contra digitos completos y nacional (ultimos 9).
            $digits = preg_replace('/\D+/', '', $fromPhone);
            $candidates = array_values(array_unique(array_filter([$digits, substr($digits, -9)])));
            $user = User::whereIn('personal_cell_phone', $candidates)
                ->where('bot_enabled', true)
                ->where('locked', false)
                ->first();
        }

        if (!$user) {
            Log::info('[WhatsAppBot] Sender no autorizado', ['phone' => $fromPhone, 'self_chat' => $isOwnerSelfChat]);
            return;
        }

        // Defensa: si el usuario quedo locked despues de autorizarse, lo bloqueamos.
        if ($user->locked) {
            Log::info('[WhatsAppBot] User suspendido (locked), rechazando', ['user_id' => $user->id]);
            return;
        }
        $company = Company::first();

        if (!$company) {
            Log::error('[WhatsAppBot] Company no encontrado en tenant');
            return;
        }

        $session = $this->sessions->findOrCreate($fromPhone);

        $trigger = $config->bot_trigger_command ?: '/bot';
        $exit = $config->bot_exit_command ?: '/fin';
        $pause = $config->bot_pause_command ?: '/p';
        $trimmed = trim($body);

        if ($this->matchesCommand($trimmed, $exit)) {
            $this->sessions->close($session);
            $this->sender->sendText($fromPhone, "Sesión cerrada. Escribe {$trigger} cuando quieras volver.", $session->id);
            return;
        }

        if ($this->matchesCommand($trimmed, $pause)) {
            if ($session->state === SessionMachine::STATE_ACTIVE || $session->state === SessionMachine::STATE_AWAITING_CONFIRM) {
                $this->sessions->pause($session);
                $this->sender->sendText($fromPhone, "Hago una pausa. Cuando quieras retomar, escribe {$trigger} y continuamos donde estábamos.", $session->id);
            }
            return;
        }

        if ($session->state === SessionMachine::STATE_AWAITING_CONFIRM) {
            $this->handleConfirmation($session, $fromPhone, $trimmed, $user);
            return;
        }

        $promptForLlm = null;

        if (str_starts_with(mb_strtolower($trimmed), mb_strtolower($trigger))) {
            $resuming = ($session->state === SessionMachine::STATE_PAUSED && !empty($session->context_json));
            $hadPendingDraft = $resuming && !empty($session->pending_document_json);

            if ($hadPendingDraft) {
                $session->state = SessionMachine::STATE_AWAITING_CONFIRM;
                $session->last_activity_at = now();
                $session->save();
            } else {
                $this->sessions->activate($session);
            }

            $afterTrigger = trim(mb_substr($trimmed, mb_strlen($trigger)));

            if ($afterTrigger === '') {
                if ($hadPendingDraft) {
                    $draft = $session->pending_document_json;
                    $total = number_format($draft['total'] ?? 0, 2);
                    $type = ucfirst($draft['document_type_label'] ?? 'comprobante');
                    $customer = $draft['customer_name'] ?? 'consumidor final';
                    $greeting = "Retomamos. Tenías una {$type} a {$customer} por S/ {$total} pendiente de confirmar.\n"
                        . "¿Confirmas la emisión con \"sí\" o cancelas con \"no\"?";
                } elseif ($resuming) {
                    $greeting = "Retomamos donde lo dejamos. ¿En qué te ayudo?";
                } else {
                    $greeting = "Hola {$user->name}. Soy el asistente de {$company->trade_name}. "
                        . "Puedo buscarte productos, clientes registrados, información del negocio y preparar boletas/facturas. "
                        . "Cuando termines, escribe {$exit}.";
                }
                $this->sender->sendText($fromPhone, $greeting, $session->id);
                $this->sessions->appendToContext($session, 'assistant', $greeting);
                return;
            }

            $promptForLlm = $afterTrigger;
        } elseif ($session->state === SessionMachine::STATE_PAUSED) {
            Log::info('[WhatsAppBot] Mensaje ignorado: sesión pausada por intervención humana', [
                'phone' => $fromPhone,
            ]);
            return;
        } else {
            if ($session->state !== SessionMachine::STATE_ACTIVE) {
                Log::info('[WhatsAppBot] Mensaje fuera de sesión activa, ignorando', [
                    'phone' => $fromPhone,
                    'state' => $session->state,
                ]);
                return;
            }
            $promptForLlm = $trimmed;
        }

        $this->runConversation($session, $fromPhone, $promptForLlm, $config, $user, $company);
    }

    private function runConversation(
        BotSession $session,
        string $fromPhone,
        string $userMessage,
        $config,
        $user,
        $company
    ): void {
        $this->sender->sendTyping($fromPhone);
        $this->sessions->appendToContext($session, 'user', $userMessage);

        $registry = new ToolRegistry();
        $registry->register(new SearchItemsTool());
        $registry->register(new LookupPersonTool());
        $registry->register(new RegisterPersonFromReniecTool());
        $registry->register(new GetCompanyInfoTool());
        $registry->register(new CreateDocumentTool());
        $registry->register(new ListRecentDocumentsTool());
        $registry->register(new QueryDocumentStatusTool());
        $registry->register(new SendDocumentPdfTool($this->sender, $fromPhone, $session->id));

        $systemPrompt = SystemPrompt::build([
            'company_name' => $company->trade_name ?: $company->name,
            'seller_name' => $user->name ?: 'el vendedor',
            'today' => now()->format('Y-m-d'),
        ]);

        // Si el vendedor esta modificando un borrador pendiente, inyectamos un
        // anclaje explicito con los IDs reales. Sin esto el LLM tiende a agarrar
        // item_ids de busquedas viejas del history y alucinar items distintos.
        $pendingDraft = $session->pending_document_json ?: null;
        if ($pendingDraft && !empty($pendingDraft['items'])) {
            $systemPrompt .= "\n\n" . $this->buildPendingDraftAnchor($pendingDraft);
        }

        // El proveedor de IA vive en el system (no en el tenant).
        $systemConfig = \App\Models\System\Configuration::first();
        $client = new OpenAiClient(
            apiKey: $systemConfig->openai_api_key,
            model: $systemConfig->openai_model ?: 'gpt-4o-mini',
            registry: $registry,
        );

        $history = is_array($session->context_json) ? $session->context_json : [];

        try {
            $result = $client->chat($systemPrompt, $userMessage, $history);
        } catch (\Throwable $e) {
            Log::error('[WhatsAppBot] OpenAI falló', ['exception' => $e->getMessage()]);
            $this->sender->sendText($fromPhone, 'Disculpa, hubo un error procesando tu mensaje. Intenta de nuevo.', $session->id);
            return;
        }

        $createResult = $this->extractCreateDocumentResult($result['tool_calls'] ?? []);

        if ($createResult) {
            $session->pending_document_json = $createResult['draft'];
            $session->state = SessionMachine::STATE_AWAITING_CONFIRM;
            $session->save();
            Log::info('[WhatsAppBot] Sesión en awaiting_confirm', ['session_id' => $session->id]);
        }

        // Si hubo create_document, enviamos el summary_text TAL CUAL de la tool,
        // ignorando el reply parafraseado del LLM. Esto garantiza que lo que ve
        // el vendedor coincide exacto con el draft que va a emitir (sin que el
        // LLM pueda alucinar items o totales distintos a los reales).
        if ($createResult) {
            $reply = trim($createResult['summary_text'] ?? '');
            $message = trim($createResult['message_to_seller'] ?? '');
            if ($message !== '') {
                $reply .= "\n\n" . $message;
            }
        } else {
            $reply = trim($result['reply'] ?? '');

            // Defensa: si el LLM intenta mostrar un summary fake (sin haber
            // llamado create_document), lo bloqueamos y avisamos al vendedor.
            // Si no lo hacemos, el draft viejo queda guardado y se emite ese
            // (no el que el vendedor cree que ve).
            $looksLikeFakeSummary = preg_match('/===.*?(?:emitir|boleta|factura|comprobante)/iu', $reply)
                || (str_contains(mb_strtolower($reply), 'subtotal') && str_contains(mb_strtolower($reply), 'total'));
            if ($looksLikeFakeSummary) {
                Log::warning('[WhatsAppBot] LLM mostro summary sin llamar create_document', [
                    'session_id' => $session->id,
                    'reply_preview' => mb_substr($reply, 0, 200),
                ]);
                $reply = 'Tuve un problema al actualizar el borrador. Por favor, dime de nuevo qué quieres modificar (ej. "agrégale 3 yogures", "ponle descuento de 5", "cambia el cliente a X").';
            }
        }

        if ($reply === '') {
            $reply = 'No tengo una respuesta clara. ¿Puedes reformular?';
        }

        $this->sessions->appendToContext($session, 'assistant', $reply);
        $this->sender->sendText($fromPhone, $reply, $session->id);
    }

    /**
     * Construye un anclaje con los datos REALES del borrador pendiente para
     * inyectar en el system prompt. El LLM tiende a agarrar item_ids de
     * busquedas viejas en el history; con este anclaje le damos los IDs
     * exactos que debe reusar al modificar.
     */
    private function buildPendingDraftAnchor(array $draft): string
    {
        $lines = [];
        $lines[] = "# Borrador pendiente de confirmar (estado actual)";
        $lines[] = "El vendedor tiene un borrador en espera. Si pide modificarlo, llama `create_document` reusando EXACTAMENTE estos `item_id` y `customer_id`. NO inventes otros IDs ni busques de nuevo lo que ya esta aqui — solo busca con `search_items` para items nuevos que el vendedor quiera AGREGAR.";
        $lines[] = "";
        $lines[] = "Tipo: " . ($draft['document_type_label'] ?? '?');
        $lines[] = "customer_id: " . ($draft['customer_id'] !== null ? (string) $draft['customer_id'] : 'null (consumidor final)');
        if (!empty($draft['customer_name'])) {
            $lines[] = "customer_name: " . $draft['customer_name'];
        }
        $lines[] = "discount_amount: " . (float) ($draft['discount_amount'] ?? 0);
        $lines[] = "items:";
        foreach ($draft['items'] ?? [] as $line) {
            $lines[] = sprintf(
                "  - item_id=%d, quantity=%s, name=\"%s\"",
                (int) ($line['item_id'] ?? 0),
                (string) ($line['quantity'] ?? 0),
                (string) ($line['item_name'] ?? '?')
            );
        }
        return implode("\n", $lines);
    }

    /**
     * Devuelve el result completo del ultimo create_document del turno que tenga
     * status ready_for_confirmation (incluye draft, summary_text, message_to_seller).
     * Si el LLM hizo varios create_document, gana el ultimo — coherente con que
     * `pending_document_json` tambien guarda el ultimo.
     */
    private function extractCreateDocumentResult(array $toolCalls): ?array
    {
        foreach (array_reverse($toolCalls) as $call) {
            if (($call['name'] ?? '') !== 'create_document') {
                continue;
            }
            $result = $call['result'] ?? [];
            if (($result['status'] ?? '') === 'ready_for_confirmation' && !empty($result['draft'])) {
                return $result;
            }
        }
        return null;
    }

    private function handleConfirmation(BotSession $session, string $fromPhone, string $message, $user): void
    {
        $intent = $this->intent->classify($message);
        $draft = $session->pending_document_json ?: [];
        $awaitingCancelConfirm = !empty($draft['_cancel_pending']);

        // Modo "confirmando cancelacion": el turno anterior el vendedor dijo
        // algo que parecia cancelacion y le pedimos confirmar. Aqui resolvemos.
        if ($awaitingCancelConfirm) {
            if ($intent === IntentClassifier::INTENT_YES) {
                $session->pending_document_json = null;
                $session->state = SessionMachine::STATE_ACTIVE;
                $session->save();
                $this->sender->sendText($fromPhone, 'Comprobante cancelado. ¿En qué más te ayudo?', $session->id);
                $this->sessions->appendToContext($session, 'assistant', 'Comprobante cancelado.');
                return;
            }
            if ($intent === IntentClassifier::INTENT_NO) {
                unset($draft['_cancel_pending']);
                $session->pending_document_json = $draft;
                $session->save();
                $msg = 'Ok, dejo el borrador pendiente. ¿Confirmas emitir con "sí" o dime qué quieres cambiar?';
                $this->sender->sendText($fromPhone, $msg, $session->id);
                $this->sessions->appendToContext($session, 'assistant', $msg);
                return;
            }
            // UNCLEAR mientras esperabamos confirmacion de cancelacion: el
            // vendedor probablemente esta dando instrucciones de modificacion.
            // Quitamos el flag y caemos al flujo UNCLEAR normal (delegar al LLM).
            unset($draft['_cancel_pending']);
            $session->pending_document_json = $draft;
            $session->save();
        }

        // UNCLEAR puede ser una intencion de modificar el draft ("agregale 10
        // galletas", "cambia el cliente"). Delegamos al LLM con el contexto
        // intacto. Si NO genera un draft nuevo, restauramos el estado para
        // que el draft anterior siga vivo y el siguiente "si" lo emita.
        if ($intent === IntentClassifier::INTENT_UNCLEAR) {
            $previousDraft = $session->pending_document_json;
            $session->state = SessionMachine::STATE_ACTIVE;
            $session->save();

            $config = \App\Models\Tenant\Configuration::first();
            $company = \App\Models\Tenant\Company::first();
            $this->runConversation($session, $fromPhone, $message, $config, $user, $company);

            $session->refresh();
            $sameDraft = json_encode($session->pending_document_json) === json_encode($previousDraft);
            if ($sameDraft && $previousDraft) {
                $session->state = SessionMachine::STATE_AWAITING_CONFIRM;
                $session->save();
            }
            return;
        }

        // NO: no cancelamos directo, pedimos confirmacion. Asi "espera" o
        // "no, agregale algo" no destruye el draft por error.
        if ($intent === IntentClassifier::INTENT_NO) {
            if (empty($draft)) {
                $session->state = SessionMachine::STATE_ACTIVE;
                $session->save();
                $this->sender->sendText($fromPhone, 'No hay un comprobante pendiente de confirmar.', $session->id);
                return;
            }
            $draft['_cancel_pending'] = true;
            $session->pending_document_json = $draft;
            $session->save();
            $msg = '¿Confirmas cancelar el comprobante? Responde "sí" para cancelar, "no" para mantenerlo, o dime qué quieres modificar.';
            $this->sender->sendText($fromPhone, $msg, $session->id);
            $this->sessions->appendToContext($session, 'assistant', $msg);
            return;
        }

        if (empty($draft)) {
            $session->state = SessionMachine::STATE_ACTIVE;
            $session->save();
            $this->sender->sendText($fromPhone, 'No hay un comprobante pendiente de confirmar.', $session->id);
            return;
        }

        $chain = (new ValidatorChain())
            ->add(new PolicyValidator())
            ->add(new SystemGuardValidator())
            ->add(new AntiHallucinationValidator());

        $validation = $chain->validate($draft);

        if (!$validation->passed) {
            Log::warning('[WhatsAppBot] Validación falló', [
                'session_id' => $session->id,
                'code' => $validation->code,
                'reason' => $validation->reason,
            ]);
            $session->pending_document_json = null;
            $session->state = SessionMachine::STATE_ACTIVE;
            $session->save();
            $this->sender->sendText($fromPhone, "No se puede emitir: {$validation->reason}", $session->id);
            return;
        }

        $this->sender->sendTyping($fromPhone, 3000);
        $emission = $this->emitDocument($draft, $user, $fromPhone, $session->id);

        // Solo limpiamos el draft si la emision fue exitosa. Si fallo (stock,
        // caja, etc.) mantenemos el draft pendiente para que el vendedor pueda
        // ajustarlo ("reduce a 1", "cambia el item", etc.) sin perder contexto.
        if ($emission['success']) {
            $session->pending_document_json = null;
            $session->state = SessionMachine::STATE_ACTIVE;
            $session->save();
        }

        $this->sender->sendText($fromPhone, $emission['message'], $session->id);
        $this->sessions->appendToContext($session, 'assistant', $emission['message']);
    }

    /**
     * @return array{success: bool, message: string}
     */
    private function emitDocument(array $draft, $user, string $toPhone, ?int $sessionId): array
    {
        $type = ucfirst($draft['document_type_label'] ?? 'comprobante');

        try {
            $mapper = new \Modules\WhatsAppBot\Services\Facturalo\FacturaloDraftMapper();
            $payload = $mapper->map($draft, $user);

            auth()->setUser($user);

            $controller = new \App\Http\Controllers\Tenant\DocumentController();
            $result = $controller->storeWithData($payload);

            if (empty($result['success']) || empty($result['data']['id'])) {
                Log::warning('[WhatsAppBot] Emisión falló', ['result' => $result]);
                $errMsg = $result['message'] ?? '';
                return [
                    'success' => false,
                    'message' => $this->humanizeEmissionError($errMsg, $type),
                ];
            }

            $numberFull = $result['data']['number_full'] ?? '?';
            $documentId = $result['data']['id'];

            $document = \App\Models\Tenant\Document::find($documentId);
            if ($document && !empty($document->filename)) {
                $displayName = $numberFull . '.pdf';
                $caption = "{$type} {$numberFull} — Total S/ " . number_format($draft['total'], 2);
                $this->sender->sendDocumentPdf($toPhone, $document->filename, $displayName, $caption, $sessionId);
            }

            return [
                'success' => true,
                'message' => "{$type} emitida correctamente.\nNúmero: {$numberFull}\nTotal: S/ " . number_format($draft['total'], 2),
            ];
        } catch (\Throwable $e) {
            Log::error('[WhatsAppBot] Excepción al emitir', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'message' => $this->humanizeEmissionError($e->getMessage(), $type),
            ];
        }
    }

    /**
     * Traduce mensajes técnicos de error a textos amigables para el vendedor.
     * Si el mensaje contiene patrones conocidos (stock, caja, RUC, etc.), devuelve
     * un texto claro y accionable. Si no, devuelve el mensaje genérico.
     */
    private function humanizeEmissionError(string $message, string $type): string
    {
        $lower = mb_strtolower($message);

        // Stock insuficiente
        if (str_contains($lower, 'no tiene suficiente stock') || str_contains($lower, 'stock insuficiente') || str_contains($lower, 'insufficient stock')) {
            if (preg_match('/producto\s+([\w\s\-áéíóúñÁÉÍÓÚÑ]+?)\s+no tiene suficiente stock/iu', $message, $m)) {
                $producto = trim($m[1]);
                return "No se puede emitir la {$type}: el producto \"{$producto}\" no tiene suficiente stock disponible. Reduce la cantidad o repón inventario.";
            }
            return "No se puede emitir la {$type}: uno de los productos no tiene suficiente stock disponible. Reduce la cantidad o repón inventario.";
        }

        // Caja no abierta
        if (str_contains($lower, 'no query results for model') && str_contains($lower, 'cash')) {
            return "Para emitir comprobantes primero debes abrir caja en el sistema. Apertura tu caja desde Pro8 y vuelve a intentar.";
        }

        // RUC / cliente
        if (str_contains($lower, 'ruc') && (str_contains($lower, 'inválido') || str_contains($lower, 'invalido') || str_contains($lower, 'no válido') || str_contains($lower, 'invalid'))) {
            return "No se puede emitir la {$type}: el RUC del cliente no es válido. Verifica el documento del cliente.";
        }

        // Configuración SUNAT
        if (str_contains($lower, 'soap') || str_contains($lower, 'certificado') || str_contains($lower, 'sunat')) {
            return "No se pudo emitir la {$type}: hay un problema con la configuración SUNAT del negocio. Contacta al administrador.";
        }

        // Serie / correlativo
        if (str_contains($lower, 'serie') || str_contains($lower, 'duplicate entry') || str_contains($lower, 'correlativo')) {
            return "No se pudo emitir la {$type}: hay un problema con la serie o numeración del comprobante. Contacta al administrador.";
        }

        // Genérico (último recurso)
        return "Ocurrió un problema al emitir el comprobante. Por favor, contacta con el administrador.";
    }
}
