<?php

    namespace Modules\FullSuscription\Http\Controllers;

    use App\Http\Controllers\SearchCustomerController;
    use Carbon\Carbon;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Foundation\Application;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\View\View;
    use Modules\FullSuscription\Http\Requests\PaymentsFullSuscriptionRequest;
    use Modules\FullSuscription\Http\Resources\UserRelSuscriptionPlansCollection;
    use Modules\FullSuscription\Http\Resources\UserRelSuscriptionPlansResource;
    use Modules\FullSuscription\Models\Tenant\CatPeriod;
use Modules\FullSuscription\Models\Tenant\SuscriptionOrder;
use Modules\FullSuscription\Models\Tenant\SuscriptionPlan;
    use Modules\FullSuscription\Models\Tenant\UserRelSuscriptionPlan;

    class PaymentsFullSuscriptionController extends FullSuscriptionController
    {
        public function Columns()
        {
            return [
                'cat_period_id' => 'Periodos',
                'name' => 'Descripción',
                'description' => 'Nombre',
            ];

        }

        public function Record(Request $request)
        {

            return new UserRelSuscriptionPlansResource(UserRelSuscriptionPlan::findOrFail($request->person));
        }

        public function Records(Request $request)
        {
            $query = UserRelSuscriptionPlan::with(['suscription_plan', 'cat_period']);

            // Filtro por plan
            if ($request->filled('plan_id')) {
                $query->where('suscription_plan_id', (int) $request->plan_id);
            }

            // Filtro por texto: busca en name, number y description dentro del JSON parent_customer
            if ($request->filled('q')) {
                $q = $request->q;
                $query->where(function ($sub) use ($q) {
                    $sub->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(parent_customer, '$.name')) LIKE ?",        ["%{$q}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(parent_customer, '$.number')) LIKE ?",      ["%{$q}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(parent_customer, '$.description')) LIKE ?", ["%{$q}%"]);
                });
            }

            // Filtros que requieren cálculo en PHP: status y vencimiento
            $needsPhpFilter = $request->filled('status') || $request->filled('vencimiento');

            if ($needsPhpFilter) {
                $records = $query->get();

                // Filtrar por estado calculado
                if ($request->filled('status')) {
                    $status = $request->status;
                    $records = $records->filter(fn ($r) => $r->calculateStatus() === $status);
                }

                // Filtrar por fecha de vencimiento calculada
                if ($request->filled('vencimiento')) {
                    $today      = Carbon::today();
                    $vencimiento = $request->vencimiento;
                    $records = $records->filter(function ($r) use ($vencimiento, $today) {
                        $endDate = $r->calculateEndDate();
                        if ($endDate === null) {
                            // Plan ilimitado: no aparece en filtros de fecha de vencimiento
                            return false;
                        }
                        $end = Carbon::parse($endDate);
                        return match ($vencimiento) {
                            'vence_hoy'   => $end->isSameDay($today),
                            'proximos_7'  => $end->between($today, $today->copy()->addDays(7)),
                            'proximos_30' => $end->between($today, $today->copy()->addDays(30)),
                            'ya_vencidos' => $end->lt($today),
                            default       => true,
                        };
                    });
                }

                // Paginación manual sobre la colección filtrada
                $perPage = (int) config('tenant.items_per_page', 20);
                $page    = max(1, (int) ($request->page ?? 1));
                $total   = $records->count();
                $items   = $records->values()->forPage($page, $perPage);

                $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                    $items,
                    $total,
                    $perPage,
                    $page,
                    ['path' => $request->url()]
                );

                return new UserRelSuscriptionPlansCollection($paginator);
            }

            return new UserRelSuscriptionPlansCollection($query->paginate(config('tenant.items_per_page')));
        }

        public function Tables()
        {

            $customers = SearchCustomerController::getSuscriptionCustomers();
            $periods = CatPeriod::where('active', 1)->get();
            $startDate = Carbon::createFromFormat('Y-m-d', '2022-01-01')->format('Y-m-d');
            $plans = SuscriptionPlan::where('id', '!=', 0)
                ->get()
                ->transform(function ($row) {
                    return $row->getCollectionData();
                });

            $establishment_id = auth()->user()->establishment_id;

            return compact(
                'periods',
                'customers',
                'startDate',
                'plans',
                'establishment_id'
            );

        }

        /**
         * Remove the specified resource from storage.
         *
         * @param int $id
         *
         * @return Factory|Application|Response|View
         */
        public function destroy($id)
        {

            $record = UserRelSuscriptionPlan::findOrFail($id);
            $record->delete();

            return [
                'success' => true,
                'message' => 'Matrícula eliminada con éxito'
            ];

        }


        /**
         * Show the form for editing the specified resource.
         *
         * @param int $id
         *
         * @return Factory|Application|Response|View
         */
        public function edit($id)
        {
            return view('full_suscription::edit');
        }

        /**
         * Display a listing of the resource.
         *
         * @return Factory|Application|Response|View
         */
        public function index()
        {
            return view('full_suscription::payments.index');
        }

        /**
         * Show the specified resource.
         *
         * @param int $id
         *
         * @return Factory|Application|Response|View
         */
        public function show($id)
        {
            return view('full_suscription::show');
        }

        public function searchCustomer(Request $request)
        {
            $customers = SearchCustomerController::getSuscriptionCustomers($request);

            return ['customers' => $customers];
        }

        public function noSendLink($id): array
        {
            $record               = UserRelSuscriptionPlan::findOrFail($id);
            $record->no_send_link = true;
            $record->save();

            return [
                'success' => true,
                'message' => 'Configuración actualizada. El link de suscripción dejará de ser reenviado al cliente.',
            ];
        }

        public function changeStatus($id, Request $request): array
        {
            $record                    = UserRelSuscriptionPlan::findOrFail($id);
            $record->subscription_status = $request->input('status');
            $record->save();

            return [
                'success' => true,
                'message' => 'Estado de suscripción actualizado con éxito',
            ];
        }

        /**
         * Update the specified resource in storage.
         *
         * @param PaymentsFullSuscriptionRequest $request
         * @param int                            $id
         *
         * @return Factory|Application|Response|View
         */
        public function update(PaymentsFullSuscriptionRequest $request, $id)
        {
            //
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param PaymentsFullSuscriptionRequest $request
         *
         * @return UserRelSuscriptionPlansResource
         */
        public function store(PaymentsFullSuscriptionRequest $request)
        {
            $id = null;
            if ($request->has('id')) $id = (int)$request->id;

            $plan = UserRelSuscriptionPlan::firstOrNew(['id' => $id], []);
            $plan->fill($request->all());
            $plan->subscription_status = UserRelSuscriptionPlan::STATUS_AUTHORIZED;
            $plan->push();


            $now = Carbon::now()->format('Y-m-d');

            $order = SuscriptionOrder::create([
                'suscription_id' => $plan->id,
                'type' => SuscriptionOrder::TYPE_SUSCRIPTION_ORDER,
                'status' => SuscriptionOrder::STATUS_PENDING,
                'amount' => $request->total,
                'date_of_issue' => $now,
                'date_of_due' => $plan->getCurrentDateOfDue(),
                'count_notifications' => 0,
            ]);

            $plan->orders_created = 1;
            $plan->save();


            return [
                'success' => true,
                'message' => $id ? 'Suscripción actualizada con éxito' : 'Suscripción registrada con éxito',
                'data' => new UserRelSuscriptionPlansResource($plan)
            ];

        }

    }
