<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\Client;
use App\Models\System\Plan;
use App\Models\Tenant\Company;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\AccountPayment;
use App\Models\System\ClientPayment;
use App\Models\System\PaymentOrder;
use App\Models\System\PaymentOrderState;
use Culqi\Culqi;
use Culqi\CulqiException;
use Illuminate\Support\Facades\Mail;
use App\Mail\Tenant\CulqiEmail;
use stdClass;
use App\Models\System\Configuration as ConfigurationAdmin;



use Exception;


class AccountController extends Controller
{
    public function index()
    {
        return view('tenant.account.configuration' );
    }

    public function tables()
    {
        $plans = Plan::all();
        $configuration = Configuration::first();


        return compact('plans', 'configuration');
    }

    public function paymentIndex()
    {
        $configuration = ConfigurationAdmin::first();
        $token_public_culqui = $configuration->token_public_culqui;
        $token_private_culqui = $configuration->token_private_culqui;

        return view('tenant.account.payment_index', compact("token_public_culqui", "token_private_culqui"));
    }

    public function paymentRecords()
    {
        $client = $this->getCurrentSystemClient();

        $newOrders = $client
            ? PaymentOrder::where('client_id', $client->id)
                ->get()
                ->map(function ($order) {
                    $data = $order->getCollectionData();
                    $data['payment_url'] = $this->publicPaymentUrl($order->uuid);
                    return $data;
                })
            : collect();

        $legacyOrders = AccountPayment::all()->map(function ($row) {
            return $this->mapLegacyToOrderShape($row);
        });

        $records = $newOrders
            ->concat($legacyOrders)
            ->sortByDesc('due_date')
            ->values();

        $config = ConfigurationAdmin::select('enabled_culqi', 'enabled_izipay')->first();
        $gateway_enabled = (bool) ($config && ($config->enabled_culqi || $config->enabled_izipay));

        return [
            'data' => $records,
            'pays' => $this->buildStateTotals($records),
            'gateway_enabled' => $gateway_enabled,
        ];
    }

    private function getCurrentSystemClient()
    {
        $company = Company::active();
        if (!$company) {
            return null;
        }

        return Client::where('number', $company->number)->first();
    }

    private function publicPaymentUrl(string $uuid): string
    {
        $prefix = env('PREFIX_URL');
        $prefix = !empty($prefix) ? $prefix . '.' : '';
        $host = $prefix . env('APP_URL_BASE');
        $scheme = request()->isSecure() ? 'https' : 'http';

        return sprintf('%s://%s/pago/%s', $scheme, $host, $uuid);
    }

    private function mapLegacyToOrderShape(AccountPayment $row)
    {
        $today = date('Y-m-d');
        $dueDate = $row->date_of_payment ? $row->date_of_payment->format('Y-m-d') : null;

        if ($row->state) {
            $stateId = 2;
            $stateName = 'Pagado';
        } elseif ($dueDate && $dueDate < $today) {
            $stateId = 3;
            $stateName = 'Vencido';
        } else {
            $stateId = 1;
            $stateName = 'Pendiente';
        }

        return [
            'id' => 'legacy-' . $row->id,
            'order' => 'L-' . str_pad($row->id, 4, '0', STR_PAD_LEFT),
            'due_date' => $dueDate,
            'notifications' => 0,
            'amount' => $row->payment,
            'order_state_id' => $stateId,
            'order_state' => $stateName,
            'date_of_payment' => $row->date_of_payment_real ? $row->date_of_payment_real->format('Y-m-d') : null,
            'diff_notification' => 'Sin notificar',
            'created_by' => 'Histórico',
            'description' => $row->reference ?: 'Pago registrado',
            'payment_url' => null,
        ];
    }

    private function buildStateTotals($records)
    {
        return PaymentOrderState::all()->map(function ($state) use ($records) {
            return (object) [
                'id' => $state->id,
                'name' => strtoupper($state->name),
                'total' => $records->where('order_state_id', $state->id)->sum('amount'),
            ];
        });
    }

    public function planChangeTables()
    {
        $client = $this->getCurrentSystemClient();
        $plans = Plan::orderBy('pricing')->get();

        return [
            'plans' => $plans,
            'current_plan_id' => $client ? $client->plan_id : null,
        ];
    }

    public function createPlanChangeOrder(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
        ]);

        $client = $this->getCurrentSystemClient();
        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el cliente asociado a esta empresa.',
            ], 422);
        }

        $targetPlan = Plan::find($request->plan_id);
        if ((int) $client->plan_id === (int) $targetPlan->id) {
            return response()->json([
                'success' => false,
                'message' => 'Ya cuenta con este plan activo.',
            ], 422);
        }

        $currentPlan = $client->plan;
        if ($currentPlan && (float) $targetPlan->pricing < (float) $currentPlan->pricing) {
            return response()->json([
                'success' => false,
                'message' => 'No es posible bajar de plan desde aquí. Contacta a soporte para gestionar el cambio.',
            ], 422);
        }

        $previousPlanName = optional($currentPlan)->name ?? 'Sin plan';

        $order = PaymentOrder::create([
            'order' => str_pad((PaymentOrder::count() + 1), 6, '0', STR_PAD_LEFT),
            'date_of_due' => now()->toDateString(),
            'amount' => $targetPlan->pricing,
            'order_state_id' => 1,
            'client_id' => $client->id,
            'plan_id' => $targetPlan->id,
            'description' => "Cambio de plan: {$previousPlanName} → {$targetPlan->name}",
            'created_by' => 'Cambio de plan',
        ]);

        return [
            'success' => true,
            'order_uuid' => $order->uuid,
            'payment_url' => $this->publicPaymentUrl($order->uuid),
        ];
    }

    public function updatePlan(Request $request)
    {
        try{

            $company = Company::active();
            $client = Client::where('number', $company->number)->first();
            $configuration = Configuration::first();

            $configuration->plan = Plan::find($request->plan_id);
            $configuration->save();

            $client->plan_id = $request->plan_id;
            $client->save();

            return [
                'success' => true,
                'message' => 'Cliente Actualizado satisfactoriamente'
            ];

        }catch(Exception $e)
        {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

    }

    public function paymentCulqui(Request $request)
    {


            $configuration = ConfigurationAdmin::first();
            $token_private_culqui = $configuration->token_private_culqui;

            if(!$token_private_culqui)
            {
                return [
                    'success' => false,
                    'message' =>  'token private culqi no defined'
                ];
            }

            $user = auth()->user();

            $SECRET_API_KEY = $token_private_culqui;
            $culqi = new Culqi(array('api_key' => $SECRET_API_KEY));


            try{

                $charge = $culqi->Charges->create(
                    array(
                        "amount" => $request->precio,
                        "currency_code" => "PEN",
                        "email" => $request->email,
                        "description" =>  $request->producto,
                        "source_id" => $request->token,
                        "installments" => $request->installments
                      )
                );

            }catch(Exception $e)
            {
              return [
                  'success' => false,
                  'message' =>  $e->getMessage()
              ];
            }

            /**
             * Todo
             *  definir estados de pago en accunpayment
             */

            $account_payment = AccountPayment::find($request->id_payment_account);
            $account_payment->state = 1; // 1 ees pagado, 2 es pendiente
            $account_payment->date_of_payment_real = date('Y-m-d');
            $account_payment->save();


            $system_client_payment =  ClientPayment::find($account_payment->reference_id);
            $system_client_payment->state = 1;
            $system_client_payment->save();


            $customer_email = $request->email;
            $document = new stdClass;
            $document->client = $user->name;
            $document->product = $request->producto;
            $document->total = $request->precio_culqi;
            $document->items = json_decode($request->items, true);
            $email = $customer_email;
            $mailable =new CulqiEmail($document);
            $id =  $document->id;
            $model = __FILE__."::".__LINE__;
            $sendIt = EmailController::SendMail($email, $mailable, $id, $model);
            /*
            Configuration::setConfigSmtpMail();
        $array_email = explode(',', $customer_email);
        if (count($array_email) > 1) {
            foreach ($array_email as $email_to) {
                $email_to = trim($email_to);
                if(!empty($email_to)) {
                    Mail::to($email_to)->send(new CulqiEmail($document));
                }
            }
        } else {
            Mail::to($customer_email)->send(new CulqiEmail($document));
        }*/

            return [
                'success' => true,
                'culqui' => $charge,
                'message' => 'Pago efectuado correctamente'
            ];
    }








}
