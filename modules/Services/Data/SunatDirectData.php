<?php

namespace Modules\Services\Data;

use GuzzleHttp\Client;

/**
 * Consulta directa a SUNAT (sin token / sin intermediario).
 *
 * RUC: https://ww1.sunat.gob.pe/ol-ti-itfisdenreg/itfisdenreg.htm?accion=obtenerDatosRuc&nroRuc={ruc}
 * DNI: https://ww1.sunat.gob.pe/ol-ti-itfisdenreg/itfisdenreg.htm?accion=obtenerDatosDni&numDocumento={dni}
 *
 * Devuelve la misma estructura normalizada que Modules\ApiPeruDev\Data\ServiceData::service()
 * para que sea intercambiable con la API que ya esta en el sistema.
 */
class SunatDirectData
{
    const BASE_URI = 'https://ww1.sunat.gob.pe';
    const PATH = '/ol-ti-itfisdenreg/itfisdenreg.htm';

    /** @var Client */
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URI,
            'verify' => false,
            'http_errors' => false,
            'timeout' => 15,
            'connect_timeout' => 10,
        ]);
    }

    /**
     * @param string $type 'ruc' | 'dni'
     * @param string $number
     *
     * @return array {success: bool, data?: array, message?: string}
     */
    public function service($type, $number)
    {
        try {
            if ($type === 'ruc') {
                return $this->ruc($number);
            }

            if ($type === 'dni') {
                return $this->dni($number);
            }

            return [
                'success' => false,
                'message' => 'Tipo de documento no soportado',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'No se pudo conectar con SUNAT: ' . $e->getMessage(),
            ];
        }
    }

    protected function ruc($number)
    {
        $row = $this->fetchFirstRow([
            'accion' => 'obtenerDatosRuc',
            'nroRuc' => $number,
        ]);

        if ($row === null) {
            return $this->notFound();
        }

        $department_id = $this->clean($row['iddepartamento'] ?? '');
        $province_code = $this->clean($row['idprovincia'] ?? '');
        $district_code = $this->clean($row['iddistrito'] ?? '');

        $location_id = $this->buildLocationId($department_id, $province_code, $district_code);

        return [
            'success' => true,
            'data' => [
                'name' => $this->clean($row['apenomdenunciado'] ?? ''),
                'trade_name' => '',
                'address' => $this->clean($row['direstablecimiento'] ?? ''),
                'location_id' => $location_id,
                'condition' => '',
                'state' => '',
                'is_agent_retention' => false,
            ],
            'source' => 'sunat',
        ];
    }

    protected function dni($number)
    {
        $row = $this->fetchFirstRow([
            'accion' => 'obtenerDatosDni',
            'numDocumento' => $number,
        ]);

        if ($row === null) {
            return $this->notFound();
        }

        return [
            'success' => true,
            'data' => [
                'name' => $this->formatPersonName($this->clean($row['nombresapellidos'] ?? '')),
                'trade_name' => '',
                'location_id' => [],
                'address' => null,
                'department_id' => '',
                'province_id' => null,
                'district_id' => null,
                'condition' => '',
                'state' => '',
            ],
            'source' => 'sunat',
        ];
    }

    /**
     * @return array|null Primera fila de "lista" o null si no hay datos.
     */
    protected function fetchFirstRow(array $query)
    {
        $res = $this->client->request('GET', self::PATH, ['query' => $query]);
        $response = json_decode($res->getBody()->getContents(), true);

        if (!is_array($response)) {
            return null;
        }

        if (($response['message'] ?? null) !== 'success') {
            return null;
        }

        $lista = $response['lista'] ?? [];
        if (empty($lista) || !isset($lista[0])) {
            return null;
        }

        return $lista[0];
    }

    /**
     * Construye el ubigeo en el formato del catalogo: [dep(2), prov(4), dist(6)].
     * SUNAT entrega idprovincia/iddistrito como codigos de 2 digitos relativos.
     */
    protected function buildLocationId($department_id, $province_code, $district_code)
    {
        if ($department_id === '') {
            return [];
        }

        $province_id = ($province_code !== '') ? $department_id . $province_code : null;
        $district_id = ($province_id !== null && $district_code !== '') ? $province_id . $district_code : null;

        return array_values(array_filter([
            $department_id,
            $province_id,
            $district_id,
        ], function ($value) {
            return $value !== null && $value !== '';
        }));
    }

    /**
     * "APELLIDOS,NOMBRES" => "NOMBRES APELLIDOS"
     */
    protected function formatPersonName($value)
    {
        if (strpos($value, ',') === false) {
            return $value;
        }

        [$apellidos, $nombres] = array_pad(explode(',', $value, 2), 2, '');

        return trim(trim($nombres) . ' ' . trim($apellidos));
    }

    protected function clean($value)
    {
        return trim(preg_replace('/\s+/', ' ', (string)$value));
    }

    protected function notFound()
    {
        return [
            'success' => false,
            'message' => 'No se encontraron datos en SUNAT',
        ];
    }
}
