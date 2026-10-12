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
 *   - 'apisu'   => Modules\Services\Data\ApisuData      (API SUNAT propia, la de por defecto)
 *   - 'sunat'   => Modules\Services\Data\SunatDirectData (consulta directa a SUNAT)
 *
 * 'apiperu' (Modules\ApiPeruDev\Data\ServiceData) ya no se elige como principal: los
 * tenants que la tenian guardada pasan a apisu. Queda como ultimo respaldo.
 *
 * Si la API principal no encuentra datos, automaticamente intenta con las demas en orden.
 */
class DocumentApiResolver
{
    const PROVIDER_APISU = 'apisu';
    const PROVIDER_APIPERU = 'apiperu';
    const PROVIDER_SUNAT = 'sunat';

    /** Proveedores que se pueden elegir desde el admin */
    const SELECTABLE = [self::PROVIDER_APISU, self::PROVIDER_SUNAT];

    /** Orden de respaldo despues del principal */
    const FALLBACK_ORDER = [self::PROVIDER_APISU, self::PROVIDER_SUNAT, self::PROVIDER_APIPERU];

    /**
     * @param string $type 'ruc' | 'dni'
     * @param string $number
     *
     * @return array {success: bool, data?: array, message?: string}
     */
    public function service($type, $number)
    {
        $primary = $this->resolveProvider();
        $providers = array_unique(array_merge([$primary], self::FALLBACK_ORDER));

        $first = null;
        foreach ($providers as $provider) {
            $response = $this->callProvider($provider, $type, $number);
            if ($this->isSuccessful($response)) {
                return $response;
            }
            $first = $first ?? $response;
        }

        // Ninguna encontro datos: se devuelve la respuesta de la API principal.
        return $first;
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

        return self::normalizeProvider($provider);
    }

    /**
     * Valor guardado => proveedor elegible. Lo que no sea 'sunat' (incluido el antiguo
     * 'apiperu') es apisu.
     *
     * @return string
     */
    public static function normalizeProvider($provider)
    {
        return in_array($provider, self::SELECTABLE, true) ? $provider : self::PROVIDER_APISU;
    }

    /**
     * @return array
     */
    protected function callProvider($provider, $type, $number)
    {
        try {
            if ($provider === self::PROVIDER_APISU) {
                return (new ApisuData())->service($type, $number);
            }

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
