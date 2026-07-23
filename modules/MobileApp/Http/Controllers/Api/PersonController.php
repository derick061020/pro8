<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Tenant\Person;
use Illuminate\Support\Facades\Cache;
use Modules\MobileApp\Http\Resources\Api\{
    PersonCollection,
    PersonResource,
};
use App\Http\Controllers\Tenant\PersonController as PersonControllerWeb;
use App\Http\Requests\Tenant\PersonRequest;
use App\Models\Tenant\Consigned;

class PersonController extends Controller
{
    /**
     *
     * Obtener registros paginados
     *
     * @param  Request $request
     * @return array
     */
    public function records($type, Request $request)
    {
        $records = Person::whereFilterRecordsApi($request->input, $type);

        return new PersonCollection($records->paginate(config('tenant.items_per_page')));
    }

    /**
     *
     * Crear un nuevo cliente/proveedor
     * Payload requerido:
     * - name: nombre
     * - number: DNI/RUC
     * - identity_document_type_id: ID del tipo de documento
     * - address: dirección principal
     * - telephone: teléfono (opcional)
     * - email: correo (opcional)
     * - location_id: array [department_id, province_id, district_id]
     * - enabled: booleano (default: true)
     * - addresses: array de direcciones adicionales (opcional)
     *
     * @param  PersonRequest $request
     * @param  string $type
     * @return array
     */
    public function store(PersonRequest $request, $type)
    {
        // Crear persona con tipo (customers/suppliers)
        $record = new Person();
        $record->type = $type;
        $record->fill($request->only('identity_document_type_id', 'number', 'name', 'trade_name', 'address', 'telephone', 'email', 'country_id'));
        $record->enabled = $request->input('enabled', true);

        // Procesar ubicación principal
        $location_id = $request->input('location_id');
        if (is_array($location_id) && count($location_id) === 3) {
            $record->district_id = $location_id[2];
            $record->province_id = $location_id[1];
            $record->department_id = $location_id[0];
        }

        $record->save();

        // Procesar direcciones adicionales
        $addresses = $request->input('addresses');
        if (isset($addresses)) {
            foreach ($addresses as $row) {
                if (is_array($row["location_id"]) && count($row["location_id"]) === 3 && $row["address"]) {
                    $row["district_id"] = $row["location_id"][2];
                    $row["province_id"] = $row["location_id"][1];
                    $row["department_id"] = $row["location_id"][0];
                } else {
                    continue;
                }

                // Manejar consignado si aplica
                if ($row['has_consigned']) {
                    if ($row['consigned_id']) {
                        $consigned = Consigned::where('id', $row['consigned_id'])->first();
                        if ($consigned) {
                            $consigned->telephone = $row["phone"];
                            $consigned->save();
                        }
                    }
                } else {
                    $row['consigned_id'] = null;
                }

                $record->addresses()->create($row);
            }
        }

        return [
            'success' => true,
            'message' => $record->getTitlePersonDescription() . ' creado exitosamente',
            'data' => new PersonResource($record)
        ];
    }

    /**
     *
     * Obtener registros para scroll infinito
     * Se usa cursor-based pagination para mejor rendimiento
     *
     * Parámetros soportados:
     * - limit: cantidad de registros (máximo 100, default 15)
     * - cursor: posición actual (null en primera petición)
     * - type: tipo de persona (customers/suppliers) - requerido en la ruta
     * - input: búsqueda por nombre o número (DNI/RUC)
     * - identity_document_type_id: filtrar por tipo de documento (ID)
     * - enabled: filtrar por estado habilitado (true/false, opcional)
     *
     * @param  string $type
     * @param  Request $request
     * @return array
     */
    public function byScroll($type, Request $request)
    {
        $limit = $request->input('limit', config('tenant.items_per_page', 15));
        $cursor = $request->input('cursor');
        $input = $request->input('input', '');
        $enabled = $request->input('enabled');
        $identity_document_type_id = $request->input('identity_document_type_id');

        // Validar límite máximo
        $limit = min($limit, 100);

        $query = Person::whereFilterRecordsApi($input, $type)
            ->filterByDocumentType($identity_document_type_id)
            ->orderBy('name', 'asc');

        // Filtro opcional por estado habilitado
        if ($enabled !== null) {
            $query->where('enabled', (bool) $enabled);
        }

        if ($cursor) {
            $records = $query->cursorPaginate($limit, ['*'], 'cursor', $cursor);
        } else {
            $records = $query->cursorPaginate($limit);
        }

        return [
            'data' => PersonCollection::make($records),
            'pagination' => [
                'next_cursor' => $records->nextCursor()?->encode() ?? null,
                'has_more' => $records->hasMorePages(),
            ]
        ];
    }


    /**
     * obtener registro
     *
     * @param  int $id
     * @return PersonResource
     *
     */
    public function record($id)
    {
        return new PersonResource(Person::findOrFail($id));
    }


    /**
     *
     * Actualizar registro
     *
     * @param  PersonRequest $request
     * @return array
     */
    public function update(PersonRequest $request)
    {

        $record = Person::findOrFail($request->id);
        $record->fill($request->only('identity_document_type_id', 'number', 'name', 'trade_name', 'address', 'telephone', 'email'));

        $location_id = $request->input('location_id');
        if (is_array($location_id) && count($location_id) === 3) {
            $record->district_id = $location_id[2];
            $record->province_id = $location_id[1];
            $record->department_id = $location_id[0];
        }
        $addresses = $request->input('addresses');
        $record->addresses()->delete();
        if (isset($addresses)) {
            foreach ($addresses as $row) {
                if (is_array($row["location_id"]) && count($row["location_id"]) === 3 && $row["address"]) {
                    $row["district_id"] = $row["location_id"][2];
                    $row["province_id"] = $row["location_id"][1];
                    $row["department_id"] = $row["location_id"][0];
                } else {
                    continue;
                }
                if($row['has_consigned']){
                    if($row['consigned_id']){
                        $consigned = Consigned::where('id', $row['consigned_id'])->first();
                        $consigned->telephone = $row["phone"];
                        $consigned->save();
                    }
                }else{
                    $row['consigned_id'] = null;
                }
                $record->addresses()->updateOrCreate(['id' => $row['id']], $row);
            }
        }
        $record->update();

        return [
            'success' => true,
            'message' => $record->getTitlePersonDescription().' actualizado',
        ];

    }


    /**
     *
     * Obtener cliente por defecto configurado en establecimiento o clientes varios
     *
     * Usado en:
     * App
     *
     * @return array
     */
    public function getDefaultCustomer($document_type_id = null)
    {
        $customer = null;

        $establishment = auth()->user()->establishment;
        if($establishment->customer_id)
        {
            $customer = Person::findOrFail($establishment->customer_id);
        }
        elseif(in_array($document_type_id, ['03', '80'], true))
        {
            $customer = Person::whereFilterVariousClients()->first();
        }

        if($customer)
        {
            return [
                'success' => true,
                'data' => $customer->getApiRowResource()
            ];
        }

        return [
            'success' => false,
            'data' => null
        ];
    }


    /**
     *
     * Activar/Desactivar registro
     *
     * @param  int $id
     * @param  bool $enabled
     * @return array
     */
    public function changeEnabled($id, $enabled)
    {
        $record = Person::findOrFail($id);
        $record->enabled = $enabled;
        $record->save();
        $type = $record->getTitlePersonDescription();

        return [
            'success' => true,
            'message' => $enabled ? $type.' habilitado con éxito' : $type.' inhabilitado con éxito'
        ];
    }


    /**
     *
     * Eliminar registro, usa método del proceso por web
     *
     * @param  int $id
     * @return array
     */
    public function destroy($id)
    {
        return app(PersonControllerWeb::class)->destroy($id);
    }

    public function locations()
    {
        $locations = Cache::remember('locations_api', 2592000, function () {
            return func_get_locations();
        });

        return [
            'locations' => $locations
        ];
    }
}
