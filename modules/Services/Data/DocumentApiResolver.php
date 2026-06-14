<?php

namespace Modules\Services\Data;

use App\Models\System\Configuration as SystemConfiguration;
use App\Models\Tenant\Configuration as TenantConfiguration;
use Modules\ApiPeruDev\Data\ServiceData;

/**
 * Orquestador de consultas de RUC/DNI.
 *
 * Resuelve, segun la configuracion del tenant (definida desde el admin), que API
 * usar como principal:
 *   - 'apiperu' => Modules\ApiPeruDev\Data\ServiceData  (la que ya esta en el sistema)
 *   - 'sunat'   => Modules\Services\Data\SunatDirectData (consulta directa a SUNAT)
 *
 * Si la API principal no encuentra datos, automaticamente intenta con la otra
 * (y viceversa).
 */
class DocumentApiResolver
{
    const PROVIDER_APIPERU = 'apiperu';
    const PROVIDER_SUNAT = 'sunat';

    /**
     * @param string $type 'ruc' | 'dni'
     * @param string $number
     *
     * @return array {success: bool, data?: array, message?: string}
     */
    public function service($type, $number)
    {
        $primary = $this->resolveProvider();
        $secondary = ($primary === self::PROVIDER_SUNAT)
            ? self::PROVIDER_APIPERU
            : self::PROVIDER_SUNAT;

        $response = $this->callProvider($primary, $type, $number);
        if ($this->isSuccessful($response)) {
            return $response;
        }

        // Fallback automatico a la otra API.
        $fallback = $this->callProvider($secondary, $type, $number);
        if ($this->isSuccessful($fallback)) {
            return $fallback;
        }

        // Ninguna encontro datos: se devuelve la respuesta de la API principal.
        return $response;
    }

    /**
     * Determina el proveedor configurado para el contexto actual (tenant o admin).
     *
     * @return string
     */
    protected function resolveProvider()
    {
        $prefix = env('PREFIX_URL', null);
        $prefix = !empty($prefix) ? $prefix . '.' : '';
        $app_url = $prefix . config('configuration.app_url_base');
        $host = $_SERVER['HTTP_HOST'] ?? null;

        $provider = null;

        if ($host !== null && $host !== $app_url) {
            // Contexto del tenant/cliente.
            $configuration = TenantConfiguration::query()->first();
            $provider = $configuration->ruc_api_provider ?? null;
        }

        if (empty($provider)) {
            // Contexto admin o tenant sin valor definido: usa el del sistema.
            $systemConfiguration = SystemConfiguration::query()->first();
            $provider = $systemConfiguration->ruc_api_provider ?? null;
        }

        return ($provider === self::PROVIDER_SUNAT)
            ? self::PROVIDER_SUNAT
            : self::PROVIDER_APIPERU;
    }

    /**
     * @return array
     */
    protected function callProvider($provider, $type, $number)
    {
        try {
            if ($provider === self::PROVIDER_SUNAT) {
                return (new SunatDirectData())->service($type, $number);
            }

            return (new ServiceData())->service($type, $number);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function isSuccessful($response)
    {
        return is_array($response)
            && !empty($response['success'])
            && !empty($response['data']);
    }
}
