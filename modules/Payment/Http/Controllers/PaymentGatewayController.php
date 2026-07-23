<?php
namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\System\Configuration;
use Hyn\Tenancy\Contracts\CurrentHostname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\Resources\Payment;
use Modules\Payment\Models\PaymentConfiguration;
use Modules\Payment\Traits\CulqiTrait;
use Modules\Payment\Traits\IzipayTrait;

class PaymentGatewayController extends Controller 
{
    use CulqiTrait;
    use IzipayTrait;

    public function enabledCheckouts(Request $request)
    {

        $is_tenant = $request->boolean('isTenant', false);
        $checkout = $is_tenant ? PaymentConfiguration::enabledCheckout() : Configuration::enabledCheckout();
        return [
            'checkout' => $checkout,
            // 'is_tenant' => app(CurrentHostname::class) ? true : false
        ];
    }

    //Culqi 

    /**
     * Crear cargo en Culqi (flujo de checkout)
     */
    public function culqiCreateCharge(Request $request)
    {
        $is_tenant = $request->boolean('isTenant', false);
        $validated = $request->validate([
            'amount'        => 'required|numeric',
            'currency_code' => 'required|string',
            'email'         => 'nullable|email',
            'source_id'     => 'required|string',
        ]);

        $privateKey = $this->culqiCredentials($is_tenant);

        try {
            $charge = $this->charge(
                ['private_key' => $privateKey],
                [
                    'amount'        => $validated['amount'],
                    'currency_code' => $validated['currency_code'],
                    'email'         => $validated['email'] ?? 'admin@gmail.com',
                    'source_id'     => $validated['source_id'],
                    'capture'       => true,
                ]
            );

            $paid = $charge && $charge->outcome->type === 'venta_exitosa';

            return response()->json([
                'success' => true,
                'result' => $charge,
                'pending' => $charge->status === 'pending' ? true : false,
                'paid'    => $paid,
            ]);

        } catch (\Culqi\Error\UnhandledError $e) {
            // dd($e);
            $error = json_decode($e->getMessage());
            Log::error('Culqi charge error', ['body' => $e->getMessage()]);

            return response()->json([
                'success'          => false,
                'paid'             => false,
                'merchant_message' => $error->merchant_message ?? 'Error al procesar el cobro',
                'user_message'     => $error->user_message     ?? 'La compra no pudo ser procesada',
                'result' => $error
            ], 400);

        } catch (\Culqi\Error\CulqiException $e) {
            Log::error('Culqi exception', ['message' => $e->getMessage()]);

            return response()->json([
                'success'      => false,
                'paid'         => false,
                'user_message' => $e->getMessage(),
            ], 400);
        }
    }

    public function culqiRecord(Request $request)
    {
        $is_tenant = $request->boolean('isTenant', false);
        $publickey_culqi =  $is_tenant ? 
            PaymentConfiguration::select('publickey_culqi')->first() : 
            Configuration::select('token_public_culqui')->first();
        return [
            'publickey_culqi' => $publickey_culqi->publickey_culqi ?? $publickey_culqi->token_public_culqui
        ];
    }

    /**
     * Private key de culqi
     */
    private function culqiCredentials(bool $is_tenant = false)
    {
        if (
            $is_tenant
        ) {
            $private_key_culqi = PaymentConfiguration::select('privatekey_culqi')->first()->privatekey_culqi;
        } else {
            $private_key_culqi = Configuration::select('token_private_culqui')->first()->token_private_culqui;
        }

        return $private_key_culqi;

    }


    //Izipay


    public function izipayCreatePayment(Request $request)
    {

        $is_tenant = $request->boolean('isTenant', false);
        $validated = $request->validate([
            'amount'                              => 'required|numeric',
            'currency'                            => 'required|string',
            'orderId'                             => 'nullable|string',
            'customer.email'                      => 'nullable|email',
            'customer.billingDetails.firstName'   => 'nullable|string',
            'customer.billingDetails.lastName'    => 'nullable|string',
            'customer.billingDetails.phoneNumber' => 'nullable',
        ]);


        $credentials = $this->izipayCredentials($is_tenant);
        $result = $this->createPayment($credentials, $validated);

        return [
            'success' => $result ? true : false,
            'formToken' => $result
        ];
    }


    private function izipayCredentials(bool $is_tenant = false)
    {
        if (
            $is_tenant
        ) {
            $credentials = PaymentConfiguration::accessIzipay();
        } else {
            $credentials = Configuration::accessIzipay();
        }
    
        return $credentials;
    }


    public function izipayRecord(Request $request)
    {

        $is_tenant = $request->boolean('isTenant', false);
        $publickey_izipay = $is_tenant ? 
            PaymentConfiguration::select('publickey_izipay')->first()->publickey_izipay : 
            Configuration::select('publickey_izipay')->first()->publickey_izipay;

        return [
            'publickey_izipay' => $publickey_izipay,
        ];
    }

    public function izipayTransaction(Request $request)
    {
        $is_tenant = $request->boolean('isTenant', false);
        $credentials = $this->izipayCredentials($is_tenant);

        $uuid = $request->validate([
            'uuid' => 'required|string'
        ])['uuid'];

        $result = $this->getTransaction($credentials, $uuid);
        $status = $result['answer']['status'] ;
        $paid = $status === 'PAID' ? true : false;
    
        return [
            'success' => $result ? true : false,
            'result' => $result,
            'pending' =>  $status === 'RUNNING' ? true : false ,
            'paid' => $paid,
        ];
    }

    public function mercadoPagoCredentials(bool $is_tenant = false)
    {
        if (
            $is_tenant
        ) {
            $access_token = PaymentConfiguration::select('access_token_mp')->first()->access_token_mp;
        } else {
            $access_token = Configuration::select('access_token_mp')->first()->access_token_mp;
        }
    
        return $access_token;
    }

    /**
     * Public key de MercadoPago (para inicializar el SDK en el front)
     */
    public function mercadoPagoRecord(Request $request)
    {
        $is_tenant = $request->boolean('isTenant', false);
        $public_key = $is_tenant
            ? optional(PaymentConfiguration::select('public_key_mp')->first())->public_key_mp
            : optional(Configuration::select('public_key_mp')->first())->public_key_mp;

        return [
            'public_key_mp' => $public_key,
        ];
    }

    public function mercadoPagoCreatePayment(Request $request)
    {
        $is_tenant = $request->boolean('isTenant', false);
        $access_token = $this->mercadoPagoCredentials($is_tenant);

        try {
            $validated = $request->validate([
                'identity_document_type_id' => 'nullable|string',
                'number' => 'nullable|string',
                'email' =>  'nullable|email',
                'plan_id' => 'nullable',
                'order_id' => 'nullable',
                'form_data'                        => 'required|array',
                'form_data.token'                  => 'required|string',
                'form_data.issuer_id'              => 'required',
                'form_data.payment_method_id'      => 'required|string',
                'form_data.transaction_amount'     => 'required|numeric',
                'form_data.installments'           => 'required|integer',
                'form_data.payer'                  => 'required|array',
                'form_data.payer.email'            => 'required|email',
                'form_data.payer.identification'   => 'required|array',
                'form_data.payer.identification.type'   => 'required|string',
                'form_data.payer.identification.number' => 'required|string',
            ]);


            $requestOption = new RequestOptions($access_token);
            $client = new PaymentClient();

            $payment = $client->create([
                'token'               => $request->input('form_data.token'),           // token de la tarjeta
                'issuer_id'           => $request->input('form_data.issuer_id'),
                'payment_method_id'   => $request->input('form_data.payment_method_id'),
                'transaction_amount'  => (float) $request->input('form_data.transaction_amount'),
                'installments'        => (int) $request->input('form_data.installments'),
                'payer'               => $request->input('form_data.payer'),
            ], $requestOption);

            $paid = $this->getStatusPaymentMP($payment->status);
            
            return [
                'success' => true,
                'paid' => $paid,
                'pending' => $payment->status === 'in_process',
                'result' => $payment,
            ];

        } catch (\Throwable $th) {
            if ($th instanceof MPApiException) {
                return [
                    'success' => false,
                    'result' => null,
                    'details' => [
                        'status' => $th->getApiResponse()->getStatusCode(),
                        'body' => $th->getApiResponse()->getContent(),
                    ],
                    'paid' => false
                ];
            }

            return [
                'success' => false,
                'result' => null,
                'message' => $th->getMessage(),
                'paid' => false
            ];

        }


    }
    private function getStatusPaymentMP($status)
    {
        return match ($status) {
            'approved', 'authorized'            => true,
            'rejected'                          => false,
            'cancelled'                         => false,
            'refunded', 'charged_back'          => false,
            'pending',
            'in_process', 'in_mediation'        => false,
            default                             => false,
        };
     }

}