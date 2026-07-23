<?php

namespace App\Services;

use App\Models\Tenant\Series;

/**
 * Codificación estándar de series (ver prompts/series-grupos-dedicados-plan.md §4.4-§4.5).
 *
 * Esquema: prefijo de 2 letras + correlativo. La auto-propuesta (Auto) escanea TODAS las
 * sucursales para el prefijo y propone el siguiente correlativo libre (FF01, FF02, FF03 -> FF04).
 *
 * Compatibilidad: las series antiguas (F001, B001, ...) NO se re-codifican (§9-B); este esquema
 * aplica a tenants nuevos (siembra) y a series nuevas creadas desde la UI.
 */
class SeriesCodeGenerator
{
    /**
     * Tipos de serie disponibles para empresas NRUS.
     */
    public const NRUS_SERIES_KEYS = ['receipt', 'sale_note'];

    /**
     * Catálogo canónico de tipos de serie con su prefijo y categoría (tabs de la UI).
     * Fuente única para la siembra de tenant, la UI (Etapa 5) y la auto-propuesta.
     *
     * category: basic | advanced | internal
     *
     * @var array<int, array<string, string>>
     */
    public const SERIES_TYPES = [
        ['key' => 'invoice',             'document_type_id' => '01', 'prefix' => 'FF', 'category' => 'basic',    'label' => 'FACTURA ELECTRÓNICA'],
        ['key' => 'receipt',             'document_type_id' => '03', 'prefix' => 'BB', 'category' => 'basic',    'label' => 'BOLETA DE VENTA ELECTRÓNICA'],
        ['key' => 'credit_note_invoice', 'document_type_id' => '07', 'prefix' => 'FC', 'category' => 'basic',    'label' => 'NOTA DE CRÉDITO (factura)'],
        ['key' => 'credit_note_receipt', 'document_type_id' => '07', 'prefix' => 'BC', 'category' => 'basic',    'label' => 'NOTA DE CRÉDITO (boleta)'],
        ['key' => 'debit_note_invoice',  'document_type_id' => '08', 'prefix' => 'FD', 'category' => 'basic',    'label' => 'NOTA DE DÉBITO (factura)'],
        ['key' => 'debit_note_receipt',  'document_type_id' => '08', 'prefix' => 'BD', 'category' => 'basic',    'label' => 'NOTA DE DÉBITO (boleta)'],
        ['key' => 'retention',           'document_type_id' => '20', 'prefix' => 'RR', 'category' => 'advanced', 'label' => 'COMPROBANTE DE RETENCIÓN ELECTRÓNICA'],
        ['key' => 'perception',          'document_type_id' => '40', 'prefix' => 'PP', 'category' => 'advanced', 'label' => 'COMPROBANTE DE PERCEPCIÓN ELECTRÓNICA'],
        ['key' => 'dispatch_sender',     'document_type_id' => '09', 'prefix' => 'TT', 'category' => 'advanced', 'label' => 'GUÍA DE REMISIÓN REMITENTE'],
        ['key' => 'dispatch_carrier',    'document_type_id' => '31', 'prefix' => 'VV', 'category' => 'advanced', 'label' => 'GUÍA DE REMISIÓN TRANSPORTISTA'],
        ['key' => 'purchase_settlement', 'document_type_id' => '04', 'prefix' => 'LL', 'category' => 'advanced', 'label' => 'LIQUIDACIÓN DE COMPRA'],
        ['key' => 'sale_note',           'document_type_id' => '80', 'prefix' => 'NV', 'category' => 'internal', 'label' => 'NOTA DE VENTA'],
        ['key' => 'warehouse_entry',     'document_type_id' => 'U2', 'prefix' => 'AI', 'category' => 'internal', 'label' => 'GUÍA DE INGRESO ALMACÉN'],
        ['key' => 'warehouse_exit',      'document_type_id' => 'U3', 'prefix' => 'AS', 'category' => 'internal', 'label' => 'GUÍA DE SALIDA ALMACÉN'],
        ['key' => 'warehouse_transfer',  'document_type_id' => 'U4', 'prefix' => 'AT', 'category' => 'internal', 'label' => 'GUÍA DE TRANSFERENCIA ALMACÉN'],
    ];

    /**
     * Series que se siembran al crear un tenant (§4.5 / §9-F): básicas + NV.
     * Las internas de almacén (U2/U3/U4) quedan FUERA por ahora (no confirmadas).
     * Las avanzadas (RR/PP/TT/VV/LL) se crean a demanda desde la UI.
     *
     * @param  int $establishment_id
     * @return array<int, array<string, mixed>>
     */
    public static function defaultTenantSeries(int $establishment_id): array
    {
        $keys = ['invoice', 'receipt', 'credit_note_invoice', 'credit_note_receipt', 'debit_note_invoice', 'debit_note_receipt', 'sale_note'];
        $rows = [];

        foreach (self::SERIES_TYPES as $type) {
            if (! in_array($type['key'], $keys, true)) {
                continue;
            }
            $rows[] = [
                'establishment_id' => $establishment_id,
                'document_type_id' => $type['document_type_id'],
                'number'           => $type['prefix'] . '01',
            ];
        }

        return $rows;
    }

    /**
     * Categoría (basic|advanced|internal) de un tipo de documento.
     *
     * @param  string $document_type_id
     * @return string|null
     */
    public static function categoryForDocumentType(string $document_type_id): ?string
    {
        foreach (self::SERIES_TYPES as $type) {
            if ($type['document_type_id'] === $document_type_id) {
                return $type['category'];
            }
        }

        return null;
    }

    /**
     * Catálogo de tipos permitido según el régimen de la empresa.
     *
     * @param  bool $is_nrus
     * @return array<int, array<string, string>>
     */
    public static function availableTypes(bool $is_nrus = false): array
    {
        if (! $is_nrus) {
            return self::SERIES_TYPES;
        }

        return array_values(array_filter(self::SERIES_TYPES, function ($type) {
            return in_array($type['key'], self::NRUS_SERIES_KEYS, true);
        }));
    }

    /**
     * IDs de documento permitidos para empresas NRUS.
     *
     * @return array<int, string>
     */
    public static function nrusDocumentTypeIds(): array
    {
        return array_values(array_unique(array_map(function ($type) {
            return $type['document_type_id'];
        }, self::availableTypes(true))));
    }

    /**
     * Entrada del catálogo que corresponde a una serie, identificada por su prefijo (2 letras).
     * Permite distinguir NC/ND de factura vs boleta (FC/BC, FD/BD). Fallback por tipo de documento.
     *
     * @param  string $number            código de la serie (ej. FF01, BC01).
     * @param  string $document_type_id
     * @return array<string, string>|null
     */
    public static function typeByNumber(string $number, string $document_type_id): ?array
    {
        $prefix = strtoupper(substr($number, 0, 2));

        foreach (self::SERIES_TYPES as $type) {
            if ($type['prefix'] === $prefix) {
                return $type;
            }
        }

        // Fallback: primera entrada con el mismo tipo de documento (series antiguas F001/B001...).
        foreach (self::SERIES_TYPES as $type) {
            if ($type['document_type_id'] === $document_type_id) {
                return $type;
            }
        }

        return null;
    }

    /**
     * Siguiente código libre para un prefijo, escaneando TODAS las sucursales (§4.5-bis).
     * Ej: si existen FF01, FF02, FF03 (en cualquier sucursal) -> propone FF04.
     *
     * @param  string $prefix  prefijo de 2 letras (FF, BB, ...).
     * @return string
     */
    public function nextCode(string $prefix): string
    {
        $prefix = strtoupper(trim($prefix));
        $length = strlen($prefix);

        $max = 0;
        $numbers = Series::where('number', 'like', $prefix . '%')->pluck('number');

        foreach ($numbers as $number) {
            $suffix = substr($number, $length);
            if ($suffix !== '' && ctype_digit($suffix)) {
                $max = max($max, (int) $suffix);
            }
        }

        // Mantener 4 caracteres totales (prefijo 2 + correlativo 2) cuando el prefijo sea de 2 letras.
        $pad = max(2, 4 - $length);

        return $prefix . str_pad((string) ($max + 1), $pad, '0', STR_PAD_LEFT);
    }
}
