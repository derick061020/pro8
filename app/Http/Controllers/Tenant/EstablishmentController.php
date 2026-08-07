<?php
namespace App\Http\Controllers\Tenant;

use App\Models\Tenant\Catalogs\Country;
use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\Catalogs\District;
use App\Models\Tenant\Catalogs\Province;
use App\Models\Tenant\Establishment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\EstablishmentRequest;
use App\Http\Resources\Tenant\EstablishmentResource;
use App\Http\Resources\Tenant\EstablishmentCollection;
use App\Models\Tenant\Warehouse;
use App\Models\Tenant\Person;
use App\Models\Tenant\User;
use Illuminate\Http\Request;
use Modules\Finance\Helpers\UploadFileHelper;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Exception;

class EstablishmentController extends Controller
{
    public function index()
    {
        return view('tenant.establishments.index');
    }

    public function create()
    {
        return view('tenant.establishments.form');
    }

    public function tables()
    {
        $countries = Country::whereActive()->orderByDescription()->get();
        $departments = Department::whereActive()->orderByDescription()->get();
        $provinces = Province::whereActive()->orderByDescription()->get();
        $districts = District::whereActive()->orderByDescription()->get();
        $locations = func_get_locations();

        $customers = Person::whereType('customers')->orderBy('name')->take(1)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
            ];
        });

        return compact('countries', 'departments', 'provinces', 'districts', 'customers','locations');
    }

    public function record($id)
    {
        $record = new EstablishmentResource(Establishment::findOrFail($id));

        return $record;
    }


    /**
     *
     * @param  EstablishmentRequest $request
     * @return array
     */
    public function store(EstablishmentRequest $request)
    {
        try
        {
            $id = $request->input('id');
            $has_igv_31556 = ($request->input('has_igv_31556') === 'true');
            $addresses = ($request->input('addresses'))??[];
            $establishment = Establishment::firstOrNew(['id' => $id]);
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $request->validate(['file' => 'mimes:jpeg,png,jpg,webp|max:1024']);
                $file = $request->file('file');
                $basename = (string) time();
                $ext = strtolower($file->getClientOriginalExtension());
                $filenameForCheck = $basename . '.' . $ext;

                UploadFileHelper::checkIfValidFile($filenameForCheck, $file->getRealPath(), true);

                $image = Image::make($file->getRealPath());
                $image->orientate();
                $image->resize(450, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                if ($image->mime() === 'image/png') {
                    $canvas = Image::canvas($image->width(), $image->height(), '#ffffff');
                    $canvas->insert($image, 'top-left', 0, 0);
                    $image = $canvas;
                }

                $outputFilename = $basename . '.jpg';
                $binary = (string) $image->encode('jpg', 70);

                Storage::put('public/uploads/logos/' . $outputFilename, $binary);
                $path = 'storage/uploads/logos/' . $outputFilename;
                $request->merge(['logo' => $path]);
            }
            if ($request->hasFile('file')) {
                $establishment->fill($request->all());
            } else {
                $establishment->fill($request->except('logo'));
            }
            $establishment->has_igv_31556 = $has_igv_31556;
            $establishment->email = $request->email;
            $establishment->save();
            
            if(!$id) {
                $warehouse = new Warehouse();
                $warehouse->establishment_id = $establishment->id;
                $warehouse->description = 'Almacén - '.$establishment->description;
                $warehouse->save();

                foreach ($addresses as $row) {
                    $establishment->addresses()->create($row);
                }
            } else {
                $warehouse = Warehouse::where('establishment_id', $id)->first();
                $warehouse->description = 'Almacén - '.$establishment->description;
                $warehouse->save();

                $establishment->addresses()->delete();
                foreach ($addresses as $row) {
                    $establishment->addresses()->updateOrCreate(['id' => $row['id']], $row);
                }
            }

            return [
                'success' => true,
                'message' => ($id)?'Establecimiento actualizado':'Establecimiento registrado'
            ];
        }
        catch(Exception $e)
        {
            $this->generalWriteErrorLog($e);

            return $this->generalResponse(false, 'Error desconocido: '.$e->getMessage());
        }
    }


    public function records()
    {
        $records = Establishment::all();

        return new EstablishmentCollection($records);
    }

    public function destroy($id)
    {
        $establishment = Establishment::findOrFail($id);
        $establishment->delete();

        return [
            'success' => true,
            'message' => 'Establecimiento eliminado con éxito'
        ];
    }

    public function getEstablishmentActive()
    {
        $establishment = auth()->user()->establishment;
        return [
            'success' => true,
            'establishment' => $establishment
        ];
    }

    public function getCodes()
    {
        $establishments = Establishment::select('id', 'code')->get();
        return response()->json($establishments);
    }

    public function changeUserEstablishment(Request $request)
    {
        $request->validate([
            'establishment_id' => ['required', 'integer', 'exists:tenant.establishments,id'],
        ]);

        $user = User::findOrFail(auth()->user()->id);
        $establishment_id = (int) $request->input('establishment_id');

        // Si ya está en esa sucursal no se escribe nada: evita que un evento
        // "change" no provocado por el usuario (restauración de formularios del
        // navegador al despertar/restaurar la pestaña) reescriba la sucursal.
        if ((int) $user->establishment_id === $establishment_id) {
            return response()->json([
                'success' => true,
                'changed' => false,
                'establishment_id' => $establishment_id,
                'message' => 'Ya se encuentra en esa sucursal',
            ], 200);
        }

        $user->establishment_id = $establishment_id;
        $user->save();

        return response()->json([
            'success' => true,
            'changed' => true,
            'establishment_id' => $establishment_id,
            'message' => 'Establecimiento actualizado con éxito',
        ], 200);
    }
}
