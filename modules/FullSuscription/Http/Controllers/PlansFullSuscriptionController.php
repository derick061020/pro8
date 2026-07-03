<?php

    namespace Modules\FullSuscription\Http\Controllers;

use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Models\Tenant\Company;
use App\Models\Tenant\Person;
use Exception;
use Illuminate\Database\Query\Builder;
    use Illuminate\Foundation\Application;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\ApiPeruDev\Data\ServiceData;
use Modules\FullSuscription\Http\Requests\PlanFullSuscriptionRequest;
    use Modules\FullSuscription\Http\Resources\SuscriptionPlansCollection;
    use Modules\FullSuscription\Http\Resources\SuscriptionPlansResource;
    use Modules\FullSuscription\Models\Tenant\CatPeriod;
use Modules\FullSuscription\Models\Tenant\SuscriptionOrder;
use Modules\FullSuscription\Models\Tenant\SuscriptionPlan;
use Modules\FullSuscription\Models\Tenant\UserRelSuscriptionPlan;
use Modules\Payment\Models\PaymentConfiguration;

    class PlansFullSuscriptionController extends FullSuscriptionController
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

            return new SuscriptionPlansResource(SuscriptionPlan::findOrFail($request->person));
        }

        public function Records(Request $request)
        {
            $records = SuscriptionPlan::query();

            // Búsqueda por nombre o descripción
            if ($request->filled('q')) {
                $q = $request->q;
                $records->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            }

            // Filtro por frecuencia (período)
            if ($request->filled('frequency') && $request->frequency !== 'all') {
                $records->where('cat_period_id', $request->frequency);
            }

            // Filtro por estado
            if ($request->filled('status') && $request->status !== 'all') {
                $records->where('status', (bool)($request->status));
            }

            // Filtro por período de prueba
            if ($request->filled('trial') && $request->trial !== 'all') {
                if ($request->trial === 'with') {
                    $records->whereNotNull('trial_days')->where('trial_days', '>', 0);
                } else {
                    $records->whereNull('trial_days')->orWhere('trial_days', '<=', 0);
                }
            }

            /** @var Builder $records */
            $records->orderBy('name');

            return new SuscriptionPlansCollection($records->paginate(config('tenant.items_per_page')));
        }

        public function Tables()
        {

            $periods = CatPeriod::where('active', 1)->get();
            $establishment_id = auth()->user()->establishment_id;

            return compact(
                'periods',
                'establishment_id'
            );

        }

        public function destroy($id)
        {

            $record = SuscriptionPlan::findOrFail($id);
            $record->delete();

            return [
                'success' => true,
                'message' => 'Plan eliminado con éxito'
            ];

        }

        /**
         * Actualiza el estado `status` de un plan.
         */
        public function updateStatus(Request $request, $id)
        {
            $plan = SuscriptionPlan::findOrFail($id);
            if ($request->has('status')) {
                $plan->status = (bool)$request->status;
                $plan->save();
            }

            return [
                'success' => true,
                'status' => (bool)$plan->status
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
            return view('full_suscription::plans.index');
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


        /**
         * Store a newly created resource in storage.
         *
         * @param PlanFullSuscriptionRequest $request
         *
         * @return SuscriptionPlansResource
         */
        public function store(PlanFullSuscriptionRequest $request)
        {
            $id = null;
            $requestItems = $request->items;
            if ($request->has('id')) $id = (int)$request->id;
            $period = CatPeriod::where('period', 'Y')->first();
            if ($request->has('periods')) {
                if (CatPeriod::where('period', $request->periods)->first() != null) {
                    $period = CatPeriod::where('period', $request->periods)->first();
                }
            }
            $plan = SuscriptionPlan::firstOrNew(['id' => $id], $request->all());

            $plan->fill($request->all());
            $plan->setName($request->name)
                ->setDescription($request->description)
                ->setCatPeriod($period)
                ->push();
            foreach ($plan->items as $i) {
                $i->delete();
            }
            foreach ($requestItems as $item) {
                $plan->items()->create($item);
            }

            return new SuscriptionPlansResource($plan);
        }

        public function plan_view_client(Request $request, string $plan_id)
        {
            $plan    = SuscriptionPlan::with('cat_period')->findOrFail($plan_id);
            $company = Company::select('name', 'trade_name', 'number', 'logo')->first();
            $public_key = PaymentConfiguration::GetPublicKey();

            if ($plan->status) {
                return view('full_suscription::plans.client', [
                    'plan_id'    => $plan_id,
                    'plan_data'  => $plan,
                    'public_key' => $public_key,
                    'company'    => [
                        'name' => $company->trade_name ?: $company->name,
                        'ruc'  => $company->number,
                        'logo' => $company->logo ? asset('storage/uploads/logos/' . $company->logo) : null,
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'El plan no está activo',
                ], 400);
            }

        }


        public function createSuscription(Request $request)
        {

            try {
                DB::connection('tenant')->beginTransaction();   
                
                $validated = $request->validate([
                    'plan_id' => 'required',
                    'status' => 'required', // va a venir el status del pago del mismo checkout
                    'customer' => 'required|array',
                    'customer.identity_document_type_id' => 'required',
                    'customer.number' => 'required|string',
                    'customer.email' => 'nullable|email',
                    'customer.phone' => 'nullable|string',
                ]);

                $date_of_issue = now()->toDateTimeString();
                $plan = SuscriptionPlan::findOrFail($validated['plan_id']);

                $person = $this->createPerson($validated['customer']);


                $order = SuscriptionOrder::where('person_number', $validated['customer']['number'])
                                        ->whereNull('suscription_id')
                                        ->where('type', SuscriptionOrder::TYPE_NEW_SUSCRIPTION)->first();
                
                
                if (!$order) {
                    $_data_order = [
                            'amount' => $plan->total,
                            'date_of_payment' => null,
                            'person_number' => $validated['customer']['number'],
                            'currency' => 'PEN',
                            'status' => $this->returnStatusOrder($validated['status']),
                            'type' => SuscriptionOrder::TYPE_NEW_SUSCRIPTION,
                            'date_of_issue' => $date_of_issue,

                    ];
                    $order = SuscriptionOrder::create($_data_order);
                }


                if ($this->returnStatusOrder($validated['status']) === SuscriptionOrder::STATUS_PAID) {
                    $_data = $this->createSuscriptionData($plan, $person, [
                        'date_of_issue' => $date_of_issue,
                        'status' => $validated['status'],
                    ]);

                    $suscription = UserRelSuscriptionPlan::create($_data);

                    $_data_order = [
                            'amount' => $plan->total,
                            'date_of_payment' => now()->toDateTimeString(),
                            'suscription_id' => $suscription->id,
                            'currency' => 'PEN',
                            'status' => SuscriptionOrder::STATUS_PAID,
                            'type' => SuscriptionOrder::TYPE_SUSCRIPTION_ORDER,
                            'date_of_due' => $suscription->getCurrentDateOfDue(),
                            'date_of_issue' => $date_of_issue,
                    ];

                    if ($order) {
                        $order->update([
                            'suscription_id' => $suscription->id,
                            'status' => SuscriptionOrder::STATUS_PAID,
                            'date_of_payment' => now()->toDateTimeString(),
                        ]);
                    }
                    
                    SuscriptionOrder::create($_data_order);

                }

                DB::connection('tenant')->commit();

                return [
                    'success' => true,
                    'message' => 'Suscripción creada con éxito',
                    'data' => [
                        'suscription' => isset($suscription) ? $suscription : null,
                        'order' => $order,
                    ]
                ];
            } catch (\Throwable $th) {
                DB::connection('tenant')->rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la suscripción: ' . $th->getMessage(),
                ], 500);
            }
        }
        private function createSuscriptionData(SuscriptionPlan $plan, Person $person, $array) : array
        {

            $total = $plan->total;
            $taxed = $total / 1.18;
            $igv = $total - $taxed;

            $total = number_format($total, 2, '.', '');
            $taxed = number_format($taxed, 2, '.', '');
            $igv = number_format($igv, 2, '.', '');
            return  [
                'total_prepayment' => 0,
                'quantity_period' => $plan->quantity_period,
                'total_charge' => 0,
                'total_discount' => 0,
                'total_exportation' => 0,
                'start_date' => $array['date_of_issue'],
                'created_at' => $array['date_of_issue'],
                'total_free' => 0,
                'suscription_plan_id' => $plan->id,
                'total_taxed' => $taxed,
                'total_unaffected' => 0,
                'total' => $total,
                'total_exonerated' => 0,
                'total_igv' => $igv,
                'total_igv_free' => 0,
                'total_isc' => 0,
                'total_other_taxes' => 0,
                'total_prepayment' => 0,
                'total_taxes' => $igv,
                'total_value' => $taxed,
                'parent_customer_id' => $person->id,
                'customer_id' => $person->id,
                'user_id' => $person->id,
                'parent_customer_id' => $person->id,
                'parent_customer' => $person->toArray(),
                'customer' => $person->toArray(),
                'set_mp' => false,
                'items' => null,
                'cat_period_id' => $plan->cat_period_id,
                'subscription_status' => $this->returnStatus($array['status']),
                ];
        }


        private function createPerson(array $data): Person
    {

        $person = Person::where('number', $data['number'])
                        ->where('identity_document_type_id', $data['identity_document_type_id'])
                        ->first();
        if (!$person) {
            $type = strtolower(IdentityDocumentType::find($data['identity_document_type_id'])->description);
            $service = new ServiceData;
            $person_data = $service->service($type, $data['number']);

            if (!$person_data['success']) {
                throw new Exception('No existe el cliente ingresado', 1);
            }

            $_data = [
                'identity_document_type_id' => $data['identity_document_type_id'],
                'number' => $data['number'],
                'name' => $person_data['data']['name'],
                'email' => $data['email'],
                'telephone' => $data['phone'],
                'country_id' => 'PE',
                'nationality_id' => 'PE',
                'type' => 'customers',
            ];

            $person = new Person;
            $person->fill($_data);
            $person->save();

        }

        return $person;
    }

        public function returnStatus(string $status)
        {
            return match($status) {
                'PAID' => UserRelSuscriptionPlan::STATUS_AUTHORIZED,
                'venta_exitosa' => UserRelSuscriptionPlan::STATUS_AUTHORIZED,
                default => UserRelSuscriptionPlan::STATUS_PAUSED,
            };
        }

        public function returnStatusOrder(string $status)
        {
            // Izipay y Culqi
            return match($status) {
                'venta_exitosa' => SuscriptionOrder::STATUS_PAID,
                'operacion_denegada' => SuscriptionOrder::STATUS_REJECTED,
                'PAID' => SuscriptionOrder::STATUS_PAID,
                default => SuscriptionOrder::STATUS_PENDING,
            };
        }

    }
