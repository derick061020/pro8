<?php

namespace App\Support;

/**
 * Claves de permisos del panel sistema para administradores creados por reseller.
 * Cada clave coincide con el primer segmento de la URL (path) del módulo.
 */
class ResellerSystemAdminModules
{
    public const DEFINITIONS = [
        'admin-reseller' => 'Administradores',
        'clients' => 'Dashboard',
        'configurations' => 'Configuración',
        'payment-orders' => 'Pagos',
        'multi-users' => 'Multi Usuarios',
        'plans' => 'Planes',
        'massive-invoice' => 'Facturación Masiva',
        'accounting' => 'Contabilidad',
        'auto-update' => 'Actualización',
        'backup' => 'Backup',
        'information' => 'Información',
        'logs' => 'Logs',
        'reports' => 'Reportes',
    ];

    public static function allowedKeys(): array
    {
        return array_keys(self::DEFINITIONS);
    }

    public static function normalizePermissions(?array $input): array
    {
        if ($input === null || $input === []) {
            return [];
        }

        $allowed = self::allowedKeys();

        return array_values(array_unique(array_filter($input, function ($key) use ($allowed) {
            return is_string($key) && in_array($key, $allowed, true);
        })));
    }
}
