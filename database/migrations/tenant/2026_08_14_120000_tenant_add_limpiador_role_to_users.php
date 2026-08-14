<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Añade el perfil `limpiador` al ENUM de `users.type`.
 *
 * El módulo Hotel ofrece el perfil "Limpieza" en el alta de usuarios
 * (UserController::tables) y lo usa para filtrar habitaciones asignadas y para
 * el selector de limpiadores de recepción, pero NINGUNA migración lo había
 * añadido a la columna: la última que la tocó la dejó en
 * ENUM('admin','seller','integrator'). Al guardar, MySQL rechazaba el valor con
 * «Data truncated for column 'type'», así que no se podía crear el usuario de
 * limpieza (el resto de perfiles sí funcionaba).
 *
 * Se reconstruye el ENUM a partir de los valores que la tabla YA tiene, para no
 * borrar perfiles que algún tenant hubiera añadido a mano (p. ej. tenants donde
 * se parcheó `limpiador` pero se perdió `integrator`).
 */
class TenantAddLimpiadorRoleToUsers extends Migration
{
    /** Perfiles que el sistema necesita tener siempre disponibles. */
    const REQUIRED_ROLES = ['admin', 'seller', 'integrator', 'limpiador'];

    public function up()
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'type')) {
            return;
        }

        $values = $this->currentEnumValues();

        // Si la columna no es un ENUM (algún tenant la tiene como varchar) no
        // hay nada que corregir: cualquier valor es válido.
        if ($values === null) {
            return;
        }

        $merged = array_values(array_unique(array_merge($values, self::REQUIRED_ROLES)));

        // Ya están todos: no tocar la tabla (la migración es reejecutable).
        if (count($merged) === count($values)) {
            return;
        }

        $list = implode(',', array_map(fn ($v) => "'" . str_replace("'", "''", $v) . "'", $merged));

        DB::connection('tenant')->statement(
            "ALTER TABLE users MODIFY type ENUM({$list}) NOT NULL DEFAULT 'admin'"
        );
    }

    /**
     * Valores actuales del ENUM `users.type`, o null si la columna no es ENUM.
     *
     * @return array<int, string>|null
     */
    private function currentEnumValues()
    {
        $connection = DB::connection('tenant');

        // Vía information_schema: `SHOW COLUMNS ... LIKE ?` no admite
        // parámetros enlazados en MariaDB.
        $definition = $connection->selectOne(
            'SELECT COLUMN_TYPE AS column_type
               FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$connection->getDatabaseName(), 'users', 'type']
        );

        $type = $definition->column_type ?? null;

        if (!$type || stripos($type, 'enum(') !== 0) {
            return null;
        }

        preg_match_all("/'((?:[^']|'')*)'/", $type, $matches);

        return array_map(fn ($v) => str_replace("''", "'", $v), $matches[1]);
    }

    public function down()
    {
        // No se revierte: quitar `limpiador` rompería los usuarios de limpieza
        // ya creados (MySQL los truncaría a cadena vacía).
    }
}
