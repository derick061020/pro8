<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\System\Configuration;
use App\Models\System\PaymentOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Payment\Traits\CulqiTrait;
use Modules\Payment\Traits\IzipayTrait;

class PaymentOrderPublicController extends Controller
{
    use CulqiTrait;
    use IzipayTrait;

    public function show(string $uuid)
    {
        $order = $this->resolveOrder($uuid);

        return view('system.payment.show', [
            'order' => [
                'uuid' => $order->uuid,
                'order' => $order->order,
                'amount' => $order->amount,
                'date_of_due' => $order->date_of_due ? Carbon::parse($order->date_of_due)->isoFormat('D [de] MMMM [de] YYYY') : null,
                'description' => $order->description ?? 'Pago de servicio',
                'state' => $order->order_state ? $order->order_state->name : null,
                'state_id' => $order->order_state_id,
                'is_paid' => (int) $order->order_state_id === 2,
            ],
            'client' => [
                'name' => optional($order->client)->name,
                'number' => optional($order->client)->number,
                'email' => optional($order->client)->email,
            ],
        ]);
    }

    public function success(string $uuid)
    {
        $order = $this->resolveOrder($uuid);

        return view('system.payment.success', [
            'order_number' => $order->order,
            'amount' => $order->amount,
            'client_email' => optional($order->client)->email,
            'is_autoregistro' => $order->created_by === 'Autoregistro',
        ]);
    }

    public function enabledCheckout(string $uuid)
    {
        $this->resolveOrder($uuid);

        $config = Configuration::select('enabled_culqi', 'enabled_izipay')->first();

        $checkout = null;
        if ($config && $config->enabled_izipay) {
            $checkout = 'izipay';
        } elseif ($config && $config->enabled_culqi) {
            $checkout = 'culqi';
        }

        return [
            'checkout' => $checkout,
        ];
    }

    public function culqiRecord(string $uuid)
    {
        $this->resolveOrder($uuid);

        $config = Configuration::select('token_public_culqui')->first();

        return [
            'publickey_culqi' => $config->token_public_culqui ?? null,
        ];
    }

    public function culqiCharge(string $uuid, Request $request)
    {
        $order = $this->resolveOrder($uuid);
        $this->ensureNotPaid($order);

        $validated = $request->validate([
            'amount' => 'required|numeric',
            'currency_code' => 'required|string',
            'email' => 'nullable|email',
            'source_id' => 'required|string',
        ]);

        $privateKey = optional(Configuration::select('token_private_culqui')->first())->token_private_culqui;

        try {
            $charge = $this->charge(
                ['private_key' => $privateKey],
                [
                    'amount' => $validated['amount'],
                    'currency_code' => $validated['currency_code'],
                    'email' => $validated['email'] ?? optional($order->client)->email,
                    'source_id' => $validated['source_id'],
                    'capture' => true,
                ]
            );

            $paid = $charge && $charge->outcome->type === 'venta_exitosa';

            if ($paid) {
                $this->markOrderAsPaid($order);
            }

            return response()->json([
                'success' => true,
                'paid' => $paid,
                'result' => $charge,
                'redirect' => $paid ? route('payment.public.success', ['uuid' => $order->uuid]) : null,
            ]);

        } catch (\Culqi\Error\UnhandledError $e) {
            $error = json_decode($e->getMessage());
            Log::error('Public payment culqi charge error', ['body' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'paid' => false,
                'merchant_message' => $error->merchant_message ?? 'Error al procesar el cobro',
                'user_message' => $error->user_message ?? 'La compra no pudo ser procesada',
                'result' => $error,
            ], 400);

        } catch (\Culqi\Error\CulqiException $e) {
            Log::error('Public payment culqi exception', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'paid' => false,
                'user_message' => $e->getMessage(),
            ], 400);
        }
    }

    public function izipayRecord(string $uuid)
    {
        $this->resolveOrder($uuid);

        $config = Configuration::select('publickey_izipay')->first();

        return [
            'publickey_izipay' => $config->publickey_izipay ?? null,
        ];
    }

    public function izipayPayment(string $uuid, Request $request)
    {
        $order = $this->resolveOrder($uuid);
        $this->ensureNotPaid($order);

        $validated = $request->validate([
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'orderId' => 'nullable|string',
            'customer.email' => 'nullable|email',
            'customer.billingDetails.firstName' => 'nullable|string',
            'customer.billingDetails.lastName' => 'nullable|string',
            'customer.billingDetails.phoneNumber' => 'nullable',
        ]);

        $credentials = Configuration::accessIzipay();
        $result = $this->createPayment($credentials, $validated);

        return [
            'success' => $result ? true : false,
            'formToken' => $result,
        ];
    }

    public function izipayTransaction(string $uuid, Request $request)
    {
        $order = $this->resolveOrder($uuid);

        $transactionUuid = $request->validate([
            'uuid' => 'required|string',
        ])['uuid'];

        $credentials = Configuration::accessIzipay();
        $result = $this->getTransaction($credentials, $transactionUuid);
        $paid = isset($result['answer']['status']) && $result['answer']['status'] === 'PAID';

        if ($paid) {
            $this->markOrderAsPaid($order);
        }

        return [
            'success' => $result ? true : false,
            'paid' => $paid,
            'result' => $result,
            'redirect' => $paid ? route('payment.public.success', ['uuid' => $order->uuid]) : null,
        ];
    }

    private function resolveOrder(string $uuid): PaymentOrder
    {
        return PaymentOrder::with(['client', 'order_state'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    private function ensureNotPaid(PaymentOrder $order): void
    {
        if ((int) $order->order_state_id === 2) {
            abort(response()->json([
                'success' => false,
                'paid' => true,
                'user_message' => 'Esta orden ya fue pagada.',
            ], 409));
        }
    }

    private function markOrderAsPaid(PaymentOrder $order): void
    {
        $order->order_state_id = 2;
        $order->date_of_payment = now();
        $order->save();
    }
}
