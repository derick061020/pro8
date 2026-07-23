<?php

namespace Modules\WhatsAppBot\Services\OpenAi;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\WhatsAppBot\Services\Tools\ToolRegistry;
use RuntimeException;

class OpenAiClient
{
    private const ENDPOINT = 'https://api.openai.com/v1/chat/completions';
    private const MAX_LOOPS = 8;

    public function __construct(
        private string $apiKey,
        private string $model,
        private ToolRegistry $registry
    ) {
    }

    public function chat(string $systemPrompt, string $userMessage, array $history = []): array
    {
        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($history as $turn) {
            $role = $turn['role'] ?? null;
            $content = $turn['content'] ?? null;
            if (in_array($role, ['user', 'assistant'], true) && is_string($content) && $content !== '') {
                $messages[] = ['role' => $role, 'content' => $content];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $tools = $this->registry->definitions();
        $toolCallsLog = [];

        for ($i = 0; $i < self::MAX_LOOPS; $i++) {
            $response = $this->callApi($messages, $tools);
            $assistantMessage = $response['choices'][0]['message'] ?? null;

            if (!$assistantMessage) {
                throw new RuntimeException('Respuesta inválida de OpenAI');
            }

            $messages[] = $assistantMessage;

            $toolCalls = $assistantMessage['tool_calls'] ?? [];

            if (empty($toolCalls)) {
                return [
                    'reply' => $assistantMessage['content'] ?? '',
                    'tool_calls' => $toolCallsLog,
                    'iterations' => $i + 1,
                ];
            }

            foreach ($toolCalls as $call) {
                $toolName = $call['function']['name'] ?? '';
                $arguments = json_decode($call['function']['arguments'] ?? '{}', true) ?: [];

                $result = $this->executeTool($toolName, $arguments);
                $toolCallsLog[] = [
                    'name' => $toolName,
                    'arguments' => $arguments,
                    'result' => $result,
                    'result_preview' => mb_substr(json_encode($result, JSON_UNESCAPED_UNICODE), 0, 300),
                ];

                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $call['id'] ?? '',
                    'content' => json_encode($result, JSON_UNESCAPED_UNICODE),
                ];
            }
        }

        throw new RuntimeException('Se excedió el número máximo de iteraciones con OpenAI');
    }

    private function callApi(array $messages, array $tools): array
    {
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.2,
        ];

        if (!empty($tools)) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = 'auto';
        }

        $http = Http::timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ]);

        if (app()->environment('local')) {
            $http = $http->withOptions(['verify' => false]);
        }

        $response = $http->post(self::ENDPOINT, $payload);

        if (!$response->successful()) {
            Log::error('[WhatsAppBot] OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('OpenAI API error: ' . $response->status() . ' ' . $response->body());
        }

        return $response->json();
    }

    private function executeTool(string $name, array $arguments): array
    {
        if (!$this->registry->has($name)) {
            return ['error' => "Tool [{$name}] no existe"];
        }

        try {
            return $this->registry->get($name)->execute($arguments);
        } catch (\Throwable $e) {
            Log::error('[WhatsAppBot] Tool execution failed', [
                'tool' => $name,
                'arguments' => $arguments,
                'exception' => $e->getMessage(),
            ]);
            return ['error' => 'Tool execution failed: ' . $e->getMessage()];
        }
    }
}
