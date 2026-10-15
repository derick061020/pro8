<?php

namespace Modules\Services\Data;

use App\Models\System\Configuration as SystemConfiguration;
use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\Catalogs\District;
use App\Models\Tenant\Catalogs\Province;
use GuzzleHttp\Client;

/**
 * API SUNAT propia (proyecto sunat-api, publicada en apisu.hotelbuendia.net).
 *
 * RUC:  GET /api/ruc/{ruc}
 * DNI:  GET /api/dni/{dni}
 * T.C.: GET /api/tipo-cambio?fecha=AAAA-MM-DD
 *
 * Devuelve la misma estructura normalizada que Modules\ApiPeruDev\Data\ServiceData::service()
 * para que sea intercambiable con las otras APIs del sistema.
 */
class ApisuData
{
    /** @var Client */
    protected $client;

    public function __construct()
    {
        [$url, $key] = self::credentials();

        $headers = ['Accept' => 'application/json'];
        if (!empty($key)) {
            // Token del panel de apisu: identifica la cuenta y su plan
            $headers['X-API-Key'] = $key;
        }

        $this->client = new Client([
            'base_uri' => rtrim($url, '/'),
            'http_errors' => false,
            'timeout' => 30,
            'connect_timeout' => 10,
            'headers' => $headers,
        ]);
    }

    /**
     * URL y token: los de Sistema → Configuración → Consulta RUC/DNIe; si están
     * vacíos, los del .env (APISU_URL / APISU_KEY).
     *
     * @return array{0: string, 1: ?string}
     */
    public static function credentials()
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $url = null;
        $key = null;
        try {
            $configuration = SystemConfiguration::query()->first(['apisu_url', 'apisu_key']);
            $url = $configuration->apisu_url ?? null;
            $key = $configuration->apisu_key ?? null;
        } catch (\Throwable $e) {
            // Columnas aún sin migrar: se usa el .env
        }

        return $cached = [
            $url ?: config('configuration.apisu_url'),
            $key ?: config('configuration.apisu_key'),
        ];
    }

    /**
     * @param string $type 'ruc' | 'dni'
     * @param string $number
     *
     * @return array {success: bool, data?: array, message?: string}
     */
    public function service($type, $number)
    {
        if (!in_array($type, ['ruc', 'dni'], true)) {
            return [
                'success' => false,
                'message' => 'Tipo de documento no soportado',
            ];
        }

        try {
            $datos = $this->get('/api/' . $type . '/' . rawurlencode($number));
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'No se pudo conectar con la API SUNAT: ' . $e->getMessage(),
            ];
        }

        if (!is_array($datos)) {
            return [
                'success' => false,
                'message' => $datos ?: 'No se encontraron datos en SUNAT',
            ];
        }

        return [
            'success' => true,
            'data' => ($type === 'ruc') ? $this->ruc($datos) : $this->dni($datos),
            'source' => 'apisu',
        ];
    }

    /**
     * @param string $date AAAA-MM-DD
     *
     * @return array|null ['date', 'date_original', 'purchase', 'sale'] o null si no hay datos
     */
    public function exchange($date)
    {
        try {
            $datos = $this->get('/api/tipo-cambio', ['fecha' => $date]);
        } catch (\Throwable $e) {
            return null;
        }

        if (!is_array($datos) || empty($datos['compra']) || empty($datos['venta'])) {
            return null;
        }

        return [
            'date' => $date,
            'date_original' => $datos['publicado'] ?? $datos['fecha'] ?? $date,
            'purchase' => $datos['compra'],
            'sale' => $datos['venta'],
        ];
    }

    /**
     * @return array|string Los "datos" de la respuesta o el mensaje de error.
     */
    protected function get($path, array $query = [])
    {
        $res = $this->client->request('GET', $path, ['query' => $query]);
        $response = json_decode($res->getBody()->getContents(), true);

        if (!is_array($response)) {
            return 'Respuesta inválida de la API SUNAT (HTTP ' . $res->getStatusCode() . ')';
        }

        if (empty($response['ok']) || empty($response['datos'])) {
            return $response['error'] ?? '';
        }

        return $response['datos'];
    }

    protected function ruc(array $datos)
    {
        $address = '';
        $location_id = [];

        // Solo los RUC 20 traen dirección: "CAL.MORELLI NRO. 181 LIMA - LIMA - SAN BORJA"
        if (!empty($datos['direccion']) && $datos['direccion'] !== '-') {
            [$address, $location_id] = $this->splitAddress(
                $datos['direccion'],
                $datos['provincia'] ?? '',
                $datos['distrito'] ?? ''
            );
        }

        $is_agent_retention = false;
        foreach ($datos['padrones'] ?? [] as $padron) {
            if (stripos($this->normalize($padron), 'AGENTES DE RETENCION') !== false) {
                $is_agent_retention = true;
            }
        }

        return [
            'name' => $datos['razonSocial'] ?? '',
            'trade_name' => '',
            'address' => $address,
            'location_id' => $location_id,
            'condition' => $datos['condicion'] ?? '',
            'state' => $datos['estado'] ?? '',
            'is_agent_retention' => $is_agent_retention,
        ];
    }

    protected function dni(array $datos)
    {
        return [
            'name' => $datos['nombreCompleto'] ?? trim(($datos['nombres'] ?? '') . ' ' . ($datos['apellidos'] ?? '')),
            'trade_name' => '',
            'location_id' => [],
            'address' => null,
            'department_id' => '',
            'province_id' => null,
            'district_id' => null,
            'condition' => '',
            'state' => '',
        ];
    }

    /**
     * Separa la dirección fiscal del sufijo "DEPARTAMENTO - PROVINCIA - DISTRITO" y
     * busca el ubigeo en el catálogo: [dep(2), prov(4), dist(6)].
     *
     * @return array [direccion, location_id]
     */
    protected function splitAddress($direccion, $provincia, $distrito)
    {
        $direccion = trim(preg_replace('/\s+/', ' ', $direccion));
        $sufijo = '/\s*-\s*' . preg_quote($provincia, '/') . '\s*-\s*' . preg_quote($distrito, '/') . '\s*$/u';

        if ($provincia === '' || $distrito === '' || !preg_match($sufijo, $direccion)) {
            return [$direccion, []];
        }

        $cabeza = trim(preg_replace($sufijo, '', $direccion));
        $cabezaNormal = $this->normalize($cabeza);

        // El departamento va pegado al final de la calle y puede tener varias palabras
        $department = null;
        $departmentName = '';
        foreach (Department::query()->get() as $row) {
            $nombres = [$this->normalize($row->description)];
            if ($row->id === '07') {
                // SUNAT escribe el Callao como "PROV. CONST. DEL CALLAO" también en el departamento
                $nombres[] = 'PROV. CONST. DEL CALLAO';
            }
            foreach ($nombres as $nombre) {
                $coincide = $cabezaNormal === $nombre || substr($cabezaNormal, -strlen($nombre) - 1) === ' ' . $nombre;
                if ($coincide && strlen($nombre) > strlen($departmentName)) {
                    $department = $row;
                    $departmentName = $nombre;
                }
            }
        }

        if ($department === null) {
            return [$direccion, []];
        }

        $address = trim(mb_substr($cabeza, 0, mb_strlen($cabeza) - mb_strlen($departmentName)));

        // La colación del catálogo ignora mayúsculas y tildes
        $provinces = Province::query()->where('department_id', $department->id)->get();
        $province = $provinces->first(function ($row) use ($provincia) {
            return $this->normalize($row->description) === $this->normalize($provincia);
        });
        if ($province === null && $provinces->count() === 1) {
            // Callao: SUNAT y el catálogo no escriben igual su única provincia
            $province = $provinces->first();
        }
        if ($province === null) {
            return [$address, [$department->id]];
        }

        $district = District::query()
            ->where('province_id', $province->id)
            ->get()
            ->first(function ($row) use ($distrito) {
                return $this->normalize($row->description) === $this->normalize($distrito);
            });

        return [
            $address,
            array_values(array_filter([$department->id, $province->id, $district->id ?? null])),
        ];
    }

    /**
     * Mayúsculas y sin tildes, para comparar nombres de SUNAT con los del catálogo.
     */
    protected function normalize($value)
    {
        $value = mb_strtoupper(trim(preg_replace('/\s+/', ' ', (string)$value)), 'UTF-8');

        return strtr($value, ['Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U']);
    }
}
