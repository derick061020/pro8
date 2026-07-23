<?php

namespace Modules\WhatsAppBot\Services\Tools;

use App\Models\Tenant\Company;

class GetCompanyInfoTool implements ToolInterface
{
    public function name(): string
    {
        return 'get_company_info';
    }

    public function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name(),
                'description' => 'Devuelve los datos básicos del negocio: RUC, razón social y nombre comercial.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
            ],
        ];
    }

    public function execute(array $arguments): array
    {
        $company = Company::query()->select(['number', 'name', 'trade_name'])->first();

        if (!$company) {
            return ['error' => 'No se encontró la información del negocio.'];
        }

        return [
            'ruc' => $company->number,
            'business_name' => $company->name,
            'trade_name' => $company->trade_name,
        ];
    }
}
