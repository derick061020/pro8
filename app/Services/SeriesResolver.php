<?php

namespace App\Services;

use App\Models\Tenant\Configuration;
use App\Models\Tenant\Series;
use App\Models\Tenant\SeriesDeviceGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cookie;

/**
 * Resolutor central de visibilidad de series (ver prompts/series-grupos-dedicados-plan.md §2-§4.3).
 *
 * Regla:
 *   - Si hay un grupo dedicado activo (vinculado a este equipo) -> SOLO las series de ese grupo.
 *   - Si no -> series NO dedicadas (las dedicadas quedan ocultas en emisión).
 *
 * Identidad del equipo (§4.2): el nombre del equipo viaja en una COOKIE `series_device_name`
 * que escribe el backend (Cookie::queue, cifrada por Laravel) al vincular y se re-confirma en el
 * boot del header (profileState -> restoreFromDevice) a partir del localStorage. El navegador la
 * envía en cada request, así el resolutor determina el grupo activo por request (sin sesión).
 *
 * No usar este resolutor en reportes/consultas administrativas (§9-E).
 */
class SeriesResolver
{
    /** Cookie con el nombre del equipo (clave de vínculo). */
    public const COOKIE_KEY = 'series_device_name';

    /**
     * ¿La función de series dedicadas está habilitada para el tenant?
     *
     * @return bool
     */
    public function dedicatedEnabled(): bool
    {
        return (bool) optional(Configuration::first())->enable_dedicated_series;
    }

    /**
     * ¿La empresa actual usa el régimen NRUS?
     *
     * @return bool
     */
    public function isNrus(): bool
    {
        return (bool) optional(Configuration::first())->isNrus();
    }

    /**
     * Nombre del equipo tomado de la cookie (o cadena vacía).
     *
     * @return string
     */
    public function deviceName(): string
    {
        return trim((string) request()->cookie(self::COOKIE_KEY, ''));
    }

    /**
     * Id del grupo dedicado activo para este equipo (según la cookie), o null.
     *
     * @return int|null
     */
    public function activeGroupId(): ?int
    {
        if (! $this->dedicatedEnabled() || ! auth()->check()) {
            return null;
        }

        $device_name = $this->deviceName();
        if ($device_name === '') {
            return null;
        }

        $id = SeriesDeviceGroup::where('establishment_id', auth()->user()->establishment_id)
            ->forDevice($device_name)
            ->value('id');

        return $id ? (int) $id : null;
    }

    /**
     * Recordar el equipo en la cookie (al vincular o al restaurar). Persistencia ~1 año.
     *
     * @param  string $device_name
     * @return void
     */
    public function rememberDevice(string $device_name): void
    {
        Cookie::queue(self::COOKIE_KEY, $device_name, 60 * 24 * 365);
    }

    /**
     * Olvidar el equipo (al desvincular este equipo).
     *
     * @return void
     */
    public function forgetDevice(): void
    {
        Cookie::queue(Cookie::forget(self::COOKIE_KEY));
    }

    /**
     * Sincronizar la cookie a partir del nombre del equipo (localStorage del front, en boot).
     * Devuelve el grupo vinculado a este equipo si existe.
     *
     * @param  string $device_name
     * @param  int    $establishment_id
     * @return SeriesDeviceGroup|null
     */
    public function restoreFromDevice(string $device_name, int $establishment_id): ?SeriesDeviceGroup
    {
        $device_name = trim($device_name);

        if (! $this->dedicatedEnabled() || $device_name === '') {
            $this->forgetDevice();
            return null;
        }

        $group = SeriesDeviceGroup::with('series')
            ->where('establishment_id', $establishment_id)
            ->forDevice($device_name)
            ->first();

        if ($group) {
            $this->rememberDevice($device_name);
        } else {
            $this->forgetDevice();
        }

        return $group;
    }

    /**
     * Aplicar la regla de contexto (§2) sobre un query de series ya armado por el llamador.
     *
     * @param  Builder        $query
     * @param  int|false|null $force_group_id  false = usar grupo del equipo (cookie);
     *                                          null  = forzar "sin grupo";
     *                                          int   = forzar grupo (uso explícito).
     * @return Builder
     */
    public function applyContext(Builder $query, $force_group_id = false): Builder
    {
        $group_id = ($force_group_id === false) ? $this->activeGroupId() : $force_group_id;

        if ($this->isNrus()) {
            $query->whereIn('document_type_id', SeriesCodeGenerator::nrusDocumentTypeIds());
        }

        return $query->usableInContext($group_id);
    }

    /**
     * Query de series de un establecimiento ya filtrado por contexto (atajo de applyContext).
     *
     * @param  int            $establishment_id
     * @param  int|false|null $force_group_id  ver applyContext().
     * @return Builder
     */
    public function usableQuery(int $establishment_id, $force_group_id = false): Builder
    {
        return $this->applyContext(
            Series::where('establishment_id', $establishment_id),
            $force_group_id
        );
    }
}
