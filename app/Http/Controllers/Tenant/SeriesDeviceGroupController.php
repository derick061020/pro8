<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Series;
use App\Models\Tenant\SeriesDeviceGroup;
use App\Services\SeriesCodeGenerator;
use App\Services\SeriesResolver;
use Illuminate\Http\Request;

/**
 * Grupos de series dedicadas por equipo.
 *
 * Contexto de sesión (Etapa 2):
 * - current : estado del grupo activo en la sesión (para hidratar el front en boot).
 * - restore : dado el nombre del equipo (localStorage), restaura el grupo en la sesión.
 *
 * CRUD de grupos (Etapa 6):
 * - records / tables : listar grupos y series dedicadas sin agrupar + catálogo de módulos.
 * - store            : crear/editar grupo (nombre, módulo, series asignadas).
 * - destroy / unbind : eliminar grupo / desvincular el equipo.
 *
 * El vínculo desde el perfil del usuario (bind) vive en la Etapa 7.
 */
class SeriesDeviceGroupController extends Controller
{
    /** @var SeriesResolver */
    protected $resolver;

    public function __construct(SeriesResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Estado del grupo dedicado activo en la sesión actual.
     *
     * @return array
     */
    public function current()
    {
        $group_id = $this->resolver->activeGroupId();
        $group = $group_id ? SeriesDeviceGroup::with('series')->find($group_id) : null;

        return [
            'enabled'      => $this->resolver->dedicatedEnabled(),
            'active_group' => $group ? $this->present($group) : null,
        ];
    }

    /**
     * Restaurar el grupo activo a partir del nombre del equipo (localStorage).
     *
     * @param  Request $request
     * @return array
     */
    public function restore(Request $request)
    {
        $device_name = (string) $request->input('device_name', '');
        $establishment_id = auth()->user()->establishment_id;

        $group = $this->resolver->restoreFromDevice($device_name, $establishment_id);

        return [
            'success'      => true,
            'active_group' => $group ? $this->present($group) : null,
        ];
    }

    /**
     * Listar los grupos del establecimiento con sus series y estado de vínculo.
     *
     * @param  int $establishmentId
     * @return array
     */
    public function records($establishmentId)
    {
        $groups = SeriesDeviceGroup::with('series')
            ->where('establishment_id', $establishmentId)
            ->orderBy('name')
            ->get()
            ->map(function (SeriesDeviceGroup $group) {
                $data = $this->present($group);
                $data['is_bound'] = $group->is_bound;

                return $data;
            });

        return ['data' => $groups];
    }

    /**
     * Series dedicadas sin agrupar (para asignar) + catálogo de módulos asignables.
     *
     * @param  int $establishmentId
     * @return array
     */
    public function tables($establishmentId)
    {
        $available_series = Series::where('establishment_id', $establishmentId)
            ->dedicated()
            ->when($this->resolver->isNrus(), function ($query) {
                $query->whereIn('document_type_id', SeriesCodeGenerator::nrusDocumentTypeIds());
            })
            ->whereNull('series_device_group_id')
            ->get()
            ->map(fn (Series $serie) => [
                'id'               => $serie->id,
                'document_type_id' => $serie->document_type_id,
                'number'           => $serie->number,
            ])
            ->values();

        $modules = collect(SeriesDeviceGroup::ASSIGNABLE_MODULES)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
            ->values();

        return compact('available_series', 'modules');
    }

    /**
     * Crear o editar un grupo y (re)asignar sus series dedicadas.
     *
     * @param  Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $request->validate([
            'establishment_id' => 'required',
            'name'             => 'required|string|max:255',
            'series_ids'       => 'array',
        ]);

        $module_value = $request->input('module_value');
        if ($module_value !== null && ! array_key_exists($module_value, SeriesDeviceGroup::ASSIGNABLE_MODULES)) {
            return ['success' => false, 'message' => 'Módulo no válido.'];
        }

        $id = $request->input('id');
        $group = SeriesDeviceGroup::firstOrNew(['id' => $id]);
        $group->establishment_id = $request->input('establishment_id');
        $group->name             = $request->input('name');
        $group->module_value     = $module_value;
        $group->save();

        $series_ids = $request->input('series_ids', []);

        // Quitar del grupo las series que ya no están seleccionadas.
        Series::where('series_device_group_id', $group->id)
            ->whereNotIn('id', $series_ids ?: [0])
            ->update(['series_device_group_id' => null]);

        // Asignar solo series dedicadas del establecimiento que no estén en otro grupo.
        if (! empty($series_ids)) {
            Series::whereIn('id', $series_ids)
                ->where('dedicated', true)
                ->where('establishment_id', $group->establishment_id)
                ->when($this->resolver->isNrus(), function ($query) {
                    $query->whereIn('document_type_id', SeriesCodeGenerator::nrusDocumentTypeIds());
                })
                ->where(function ($query) use ($group) {
                    $query->whereNull('series_device_group_id')->orWhere('series_device_group_id', $group->id);
                })
                ->update(['series_device_group_id' => $group->id]);
        }

        return ['success' => true, 'message' => $id ? 'Grupo actualizado' : 'Grupo creado'];
    }

    /**
     * Desvincular el equipo del grupo (queda disponible para otros equipos).
     *
     * @param  int $id
     * @return array
     */
    public function unbind($id)
    {
        $group = SeriesDeviceGroup::findOrFail($id);
        $was_this_device = $this->resolver->deviceName() === (string) $group->bound_device_name;
        $group->unbind();

        if ($was_this_device) {
            $this->resolver->forgetDevice();
        }

        return ['success' => true, 'message' => 'Equipo desvinculado'];
    }

    /**
     * Eliminar el grupo y liberar sus series (vuelven a "sin grupo").
     *
     * @param  int $id
     * @return array
     */
    public function destroy($id)
    {
        $group = SeriesDeviceGroup::findOrFail($id);
        $was_this_device = $this->resolver->deviceName() === (string) $group->bound_device_name;

        Series::where('series_device_group_id', $group->id)->update(['series_device_group_id' => null]);

        if ($was_this_device) {
            $this->resolver->forgetDevice();
        }

        $group->delete();

        return ['success' => true, 'message' => 'Grupo eliminado'];
    }

    /**
     * Estado para el selector del perfil del usuario (Etapa 7): grupo vinculado a este equipo,
     * grupos disponibles para vincular y si el usuario tiene series predefinidas (punto 9).
     *
     * @param  Request $request
     * @return array
     */
    public function profileState(Request $request)
    {
        if (! $this->resolver->dedicatedEnabled()) {
            return ['enabled' => false];
        }

        $user = auth()->user();
        $device_name = trim((string) $request->input('device_name', ''));

        // Grupo vinculado a este equipo; de paso re-confirma la cookie del equipo desde el localStorage.
        $bound = $this->resolver->restoreFromDevice($device_name, $user->establishment_id);

        $available = SeriesDeviceGroup::with('series')
            ->where('establishment_id', $user->establishment_id)
            ->availableForDevice($device_name)
            ->orderBy('name')
            ->get()
            ->map(fn (SeriesDeviceGroup $group) => $this->presentWithBinding($group));

        return [
            'enabled'               => true,
            'has_predefined_series' => $this->userHasPredefinedSeries($user),
            'device_name'           => $device_name,
            'bound_group'           => $bound ? $this->presentWithBinding($bound) : null,
            'available_groups'      => $available->values(),
        ];
    }

    /**
     * Vincular un grupo a este equipo desde el perfil del usuario (punto 8 y 9).
     *
     * @param  Request $request
     * @return array
     */
    public function bind(Request $request)
    {
        $request->validate([
            'group_id'    => 'required',
            'device_name' => 'required|string|max:255',
        ]);

        if (! $this->resolver->dedicatedEnabled()) {
            return ['success' => false, 'message' => 'La función dedicada no está habilitada.'];
        }

        $user = auth()->user();
        $device_name = trim($request->input('device_name'));

        $group = SeriesDeviceGroup::where('establishment_id', $user->establishment_id)
            ->find($request->input('group_id'));

        if (! $group) {
            return ['success' => false, 'message' => 'Grupo no encontrado.'];
        }

        if ($group->bound_device_name && $group->bound_device_name !== $device_name) {
            return ['success' => false, 'message' => 'El grupo ya está en uso por el equipo "' . $group->bound_device_name . '".'];
        }

        // Punto 9: si el usuario tiene series predefinidas, requiere confirmación para eliminarlas.
        if ($this->userHasPredefinedSeries($user) && ! $request->boolean('remove_predefined')) {
            return [
                'success'               => false,
                'requires_confirmation' => true,
                'message'               => 'Tu usuario tiene series predefinidas. Para activar un grupo dedicado se eliminarán esas relaciones.',
            ];
        }

        if ($request->boolean('remove_predefined')) {
            $this->removeUserPredefinedSeries($user);
        }

        $group->bindToDevice($device_name, $user->id);
        $this->resolver->rememberDevice($device_name);

        return [
            'success'     => true,
            'message'     => 'Grupo dedicado activado en este equipo.',
            'bound_group' => $this->presentWithBinding($group->fresh('series')),
        ];
    }

    /**
     * ¿El usuario tiene series predefinidas (relación o columnas por defecto)?
     *
     * @param  \App\Models\Tenant\User $user
     * @return bool
     */
    protected function userHasPredefinedSeries($user): bool
    {
        return $user->default_document_types()->exists()
            || ! empty($user->series_id)
            || ! empty($user->document_id);
    }

    /**
     * Eliminar las series predefinidas del usuario (punto 9).
     *
     * @param  \App\Models\Tenant\User $user
     * @return void
     */
    protected function removeUserPredefinedSeries($user): void
    {
        $user->default_document_types()->delete();
        $user->series_id = null;
        $user->document_id = null;
        $user->save();
    }

    /**
     * present() + estado de vínculo.
     *
     * @param  SeriesDeviceGroup $group
     * @return array
     */
    protected function presentWithBinding(SeriesDeviceGroup $group): array
    {
        return $this->present($group) + [
            'is_bound'          => $group->is_bound,
            'bound_device_name' => $group->bound_device_name,
        ];
    }

    /**
     * Estructura de respuesta para un grupo + sus series.
     *
     * @param  SeriesDeviceGroup $group
     * @return array
     */
    protected function present(SeriesDeviceGroup $group): array
    {
        return [
            'id'                => $group->id,
            'name'              => $group->name,
            'module_value'      => $group->module_value,
            'module_label'      => $group->module_label,
            'bound_device_name' => $group->bound_device_name,
            'series'            => $group->series
                ->when($this->resolver->isNrus(), function ($series) {
                    return $series->whereIn('document_type_id', SeriesCodeGenerator::nrusDocumentTypeIds());
                })
                ->map(function ($serie) {
                    return [
                        'id'               => $serie->id,
                        'document_type_id' => $serie->document_type_id,
                        'number'           => $serie->number,
                    ];
                })->values(),
        ];
    }
}
