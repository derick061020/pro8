<?php

namespace App\Http\Controllers\AdminReseller;

use App\Http\Controllers\Controller;
use App\Models\System\Client;
use App\Models\System\User;
use App\Support\ResellerSystemAdminModules;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserController extends Controller
{
    /**
     * Página Vue del módulo (vista Blade).
     */
    public function index()
    {
        return view('system.admin_reseller.administrators.index');
    }

    /**
     * Listado JSON para la tabla (AJAX).
     */
    public function records()
    {
        $resellerId = auth()->user()->reseller_id ?? auth()->user()->id;

        $users = User::where('reseller_id', $resellerId)
            ->orderBy('id')
            ->get();

        $assignableClients = Client::withoutGlobalScopes()
            ->orderBy('name')
            ->get(['id', 'number', 'name']);

        $pivotRows = DB::table('reseller_admin_clients')
            ->whereIn('admin_user_id', $users->pluck('id'))
            ->get();

        $clientIdsByAdmin = [];
        foreach ($pivotRows as $row) {
            $aid = (int) $row->admin_user_id;
            if (!isset($clientIdsByAdmin[$aid])) {
                $clientIdsByAdmin[$aid] = [];
            }
            $clientIdsByAdmin[$aid][] = (int) $row->client_id;
        }

        foreach ($users as $user) {
            $user->setAttribute('assigned_client_ids', $clientIdsByAdmin[(int) $user->id] ?? []);
        }

        return response()->json([
            'success' => true,
            'data' => $users,
            'module_definitions' => ResellerSystemAdminModules::DEFINITIONS,
            'assignable_clients' => $assignableClients,
            'auth_user_id' => (int) auth()->user()->id,
        ]);
    }

    public function store(Request $request)
    {
        $allowed = ResellerSystemAdminModules::allowedKeys();
        $resellerId = auth()->user()->reseller_id ?? auth()->user()->id;

        $data = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', $this->systemUserUniqueEmailRule()],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'status' => ['nullable', 'boolean'],
                'can_create_clients' => ['nullable', 'boolean'],
                'module_permissions' => ['present', 'array'],
                'module_permissions.*' => ['string', Rule::in($allowed)],
                'client_ids' => ['present', 'array'],
                'client_ids.*' => ['integer'],
            ],
            [
                'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
            ]
        );

        $this->assertClientIdsExistInSystem($data['client_ids']);

        try {
            $token = $this->generateUniqueApiToken();

            $isFirstAdministrator = User::query()
                ->where('reseller_id', $resellerId)
                ->count() === 0;

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'api_token' => $token,
                'reseller_id' => $resellerId,
                'status' => $data['status'] ?? true,
                'can_create_clients' => $data['can_create_clients'] ?? false,
                'is_master' => $isFirstAdministrator,
                'module_permissions' => ResellerSystemAdminModules::normalizePermissions($data['module_permissions']),
            ]);

            $user->assignedClients()->sync($data['client_ids']);

            $user->setAttribute('assigned_client_ids', $data['client_ids']);
        } catch (Throwable $e) {
            report($e);

            return $this->jsonAdministratorError($e);
        }

        return response()->json([
            'success' => true,
            'message' => 'Administrador creado correctamente.',
            'data' => $user,
        ]);
    }

    public function update(Request $request, User $administrator)
    {
        $masterId = auth()->user()->reseller_id ?? auth()->user()->id;
        if ((int) $administrator->reseller_id !== (int) $masterId) {
            abort(403);
        }

        $allowed = ResellerSystemAdminModules::allowedKeys();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $this->systemUserUniqueEmailRule($administrator)],
            'status' => ['required', 'boolean'],
            'can_create_clients' => ['nullable', 'boolean'],
            'module_permissions' => ['present', 'array'],
            'module_permissions.*' => ['string', Rule::in($allowed)],
            'client_ids' => ['present', 'array'],
            'client_ids.*' => ['integer'],
        ];

        /** Switches Estado / Crear cliente no envían password; el formulario de edición sí. */
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
        }

        try {
            $data = $request->validate(
                $rules,
                [
                    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
                    'password.confirmed' => 'Las contraseñas no coinciden.',
                ]
            );
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);

            return $this->jsonAdministratorError($e);
        }

        $this->assertClientIdsExistInSystem($data['client_ids']);

        try {
            $administrator->name = $data['name'];
            $administrator->email = $data['email'];
            $administrator->status = $data['status'];
            $administrator->can_create_clients = $data['can_create_clients'] ?? false;
            $administrator->module_permissions = ResellerSystemAdminModules::normalizePermissions($data['module_permissions']);

            if (!empty($data['password'] ?? null)) {
                $administrator->password = Hash::make($data['password']);
            }

            $administrator->save();

            $administrator->assignedClients()->sync($data['client_ids']);
            $administrator->setAttribute('assigned_client_ids', $data['client_ids']);
        } catch (Throwable $e) {
            report($e);

            return $this->jsonAdministratorError($e);
        }

        return response()->json([
            'success' => true,
            'message' => 'Administrador actualizado correctamente.',
            'data' => $administrator,
        ]);
    }

    public function destroy(User $administrator)
    {
        $masterId = auth()->user()->reseller_id ?? auth()->user()->id;
        if ((int) $administrator->reseller_id !== (int) $masterId) {
            abort(403);
        }

        if ($administrator->isResellerSystemMasterAdministrator()) {
            abort(403, 'No autorizado: no se puede eliminar al usuario maestro.');
        }

        $administrator->delete();

        return response()->json([
            'success' => true,
            'message' => 'Administrador eliminado correctamente.',
        ]);
    }

    /**
     * Valida que los IDs existan en la tabla clients (todas las empresas del sistema son asignables).
     */
    protected function assertClientIdsExistInSystem(array $clientIds): void
    {
        if ($clientIds === []) {
            return;
        }

        $unique = array_values(array_unique(array_map('intval', $clientIds)));
        $count = Client::withoutGlobalScopes()->whereIn('id', $unique)->count();
        if ($count !== count($unique)) {
            throw ValidationException::withMessages([
                'client_ids' => ['Uno o más clientes no existen en el sistema.'],
            ]);
        }
    }

    protected function generateUniqueApiToken(): string
    {
        do {
            $token = Str::random(40);
        } while (User::where('api_token', $token)->exists());

        return $token;
    }

    /**
     * unique con conexión y tabla física del modelo System\User (users vs system_users).
     * Rule::unique(Model::class) serializa "conexión.tabla" para el verificador de Laravel;
     * no usar ->connection() sobre Unique (no existe en el framework).
     */
    protected function systemUserUniqueEmailRule(?User $ignoreUser = null): Unique
    {
        $rule = Rule::unique(User::class, 'email');

        if ($ignoreUser !== null) {
            $rule->ignore($ignoreUser->id, $ignoreUser->getKeyName());
        }

        return $rule;
    }

    /**
     * Respuesta JSON con el mensaje real de la excepción (diagnóstico de tabla/columna/SQL).
     */
    protected function jsonAdministratorError(Throwable $e, int $status = 500)
    {
        $payload = [
            'success' => false,
            'message' => $e->getMessage(),
        ];

        if ($e instanceof QueryException) {
            $payload['error'] = 'database';
            if (config('app.debug')) {
                $payload['sql'] = $e->getSql();
                $payload['bindings'] = $e->getBindings();
            }
        } else {
            $payload['error'] = class_basename($e);
        }

        if (config('app.debug')) {
            $payload['exception'] = $e::class;
            $payload['file'] = $e->getFile();
            $payload['line'] = $e->getLine();
        }

        return response()->json($payload, $status);
    }
}
