<?php

namespace Modules\WhatsAppBot\Services\Validators;

use App\Models\Tenant\Company;
use App\Models\Tenant\Configuration as TenantConfiguration;

class SystemGuardValidator implements DocumentValidator
{
    public function validate(array $draft): ValidationResult
    {
        $tenantConfig = TenantConfiguration::first();
        if ($tenantConfig && isset($tenantConfig->locked_tenant) && $tenantConfig->locked_tenant) {
            return ValidationResult::fail('El tenant está bloqueado por falta de pago.', 'tenant_locked');
        }

        $company = Company::first();
        if (!$company) {
            return ValidationResult::fail('No hay datos del negocio configurados.', 'no_company');
        }

        if (empty($company->number) || strlen($company->number) !== 11) {
            return ValidationResult::fail('El RUC del negocio no es válido.', 'invalid_company_ruc');
        }

        if ($company->soap_type_id === '02' && (empty($company->soap_username) || empty($company->soap_password))) {
            return ValidationResult::fail('SOAP está en modo Producción pero faltan las credenciales SUNAT.', 'sunat_credentials_missing_in_prod');
        }

        return ValidationResult::ok();
    }
}
