<?php

namespace Modules\WhatsAppBot\Console\Commands;

use App\Models\System\Configuration as SystemConfiguration;
use App\Models\Tenant\Company;
use App\Models\Tenant\User;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Console\Command;
use Modules\WhatsAppBot\Prompts\SystemPrompt;
use Modules\WhatsAppBot\Services\OpenAi\OpenAiClient;
use Modules\WhatsAppBot\Services\Tools\CreateDocumentTool;
use Modules\WhatsAppBot\Services\Tools\GetCompanyInfoTool;
use Modules\WhatsAppBot\Services\Tools\LookupPersonTool;
use Modules\WhatsAppBot\Services\Tools\RegisterPersonFromReniecTool;
use Modules\WhatsAppBot\Services\Tools\SearchItemsTool;
use Modules\WhatsAppBot\Services\Tools\ToolRegistry;

class TestBotCommand extends Command
{
    protected $signature = 'whatsapp-bot:test
                            {prompt : Mensaje del vendedor para enviar al bot}
                            {--phone= : Número del vendedor (busca tenant+user_id en bot_whitelist)}
                            {--tenant= : Hostname del tenant (alternativa a --phone)}
                            {--user-id= : ID del User dentro del tenant (alternativa a --phone)}';

    protected $description = 'Ejecuta una conversación de prueba contra el bot de WhatsApp sin pasar por WhatsApp.';

    public function handle(): int
    {
        $prompt = (string) $this->argument('prompt');

        $config = SystemConfiguration::first();
        if (!$config || empty($config->openai_api_key)) {
            $this->error('Falta openai_api_key en system Configuration. Configúralo desde pro8.test/configurations (Configuración de IA).');
            return self::FAILURE;
        }

        [$tenantHostname, $userId] = $this->resolveTenantAndUser();
        if (!$tenantHostname || !$userId) {
            return self::FAILURE;
        }

        $hostname = Hostname::where('fqdn', $tenantHostname)->first();
        if (!$hostname) {
            $this->error("No se encontró el hostname [{$tenantHostname}] en system.hostnames.");
            return self::FAILURE;
        }

        app(Environment::class)->tenant($hostname->website);

        $user = User::find($userId);
        if (!$user) {
            $this->error("No se encontró User id={$userId} en el tenant [{$tenantHostname}].");
            return self::FAILURE;
        }

        $company = Company::first();
        if (!$company) {
            $this->error("El tenant [{$tenantHostname}] no tiene Company configurada.");
            return self::FAILURE;
        }

        $registry = new ToolRegistry();
        $registry->register(new SearchItemsTool());
        $registry->register(new LookupPersonTool());
        $registry->register(new RegisterPersonFromReniecTool());
        $registry->register(new GetCompanyInfoTool());
        $registry->register(new CreateDocumentTool());
        $registry->register(new \Modules\WhatsAppBot\Services\Tools\ListRecentDocumentsTool());
        $registry->register(new \Modules\WhatsAppBot\Services\Tools\QueryDocumentStatusTool());

        $systemPrompt = SystemPrompt::build([
            'company_name' => $company->trade_name ?: $company->name,
            'seller_name' => $user->name ?: 'el vendedor',
            'today' => now()->format('Y-m-d'),
        ]);

        $client = new OpenAiClient(
            apiKey: $config->openai_api_key,
            model: $config->openai_model ?: 'gpt-4o-mini',
            registry: $registry,
        );

        $this->info("Tenant: {$tenantHostname}");
        $this->info("Vendedor: {$user->name} (id={$user->id})");
        $this->info("Modelo: " . ($config->openai_model ?: 'gpt-4o-mini'));
        $this->newLine();
        $this->line("Usuario: {$prompt}");
        $this->newLine();

        try {
            $result = $client->chat($systemPrompt, $prompt);
        } catch (\Throwable $e) {
            $this->error('Falló la llamada a OpenAI: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (!empty($result['tool_calls'])) {
            $this->comment('Tools usadas:');
            foreach ($result['tool_calls'] as $call) {
                $this->line("  - {$call['name']}(" . json_encode($call['arguments'], JSON_UNESCAPED_UNICODE) . ')');
                $this->line("    → {$call['result_preview']}");
            }
            $this->newLine();
        }

        $this->info('Bot:');
        $this->line($result['reply']);
        $this->newLine();
        $this->comment("(iteraciones: {$result['iterations']})");

        return self::SUCCESS;
    }

    private function resolveTenantAndUser(): array
    {
        $tenant = $this->option('tenant');
        $userId = $this->option('user-id');

        if ($tenant && $userId) {
            return [$tenant, (int) $userId];
        }

        $this->error('Provee --phone=<numero> o ambos --tenant=<hostname> y --user-id=<id>.');
        return [null, null];
    }
}
