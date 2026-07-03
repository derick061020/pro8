<?php

namespace Modules\FullSuscription\Http\Controllers;

use App\Models\Tenant\Company;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Person;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\FullSuscription\Models\Tenant\SuscriptionOrder;
use Modules\Payment\Models\PaymentConfiguration;

class PendingPaymentFullSuscriptionController extends Controller
{
    public function index()
    {
        return view('full_suscription::pending_payments.index');
    }

    public function tables()
    {
        $status_types = SuscriptionOrder::getStatusTypes();
        $message      = Configuration::first()->message_notify_to_suscription_orders;

        return [
            'status_types' => $status_types,
            'message'      => $message,
        ];
    }

    public function getMessage()
    {
        $message = Configuration::first()->message_notify_to_suscription_orders;

        return response()->json(['message' => $message]);
    }

    public function saveMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $configuration = Configuration::first();
        $configuration->message_notify_to_suscription_orders = $request->input('message');
        $configuration->save();

        return response()->json(['success' => true]);
    }

    public function changeStatus(string $id, Request $request)
    {
        $order         = SuscriptionOrder::findOrFail($id);
        $order->status = $request->input('status');

        if ($order->status === SuscriptionOrder::STATUS_PAID) {
            $order->date_of_payment = now();
            $order->generate();
        }

        $order->save();

        return response()->json(['success' => true]);
    }

    public function changeStatusOrders(string $id, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required',
            'order_id' => 'required', // Este es el id de la orden que me devuelve los checkouts
        ]);

        $order= SuscriptionOrder::findOrFail($validated['order_id']);

        if ($this->returnStatusOrder($validated['status']) === SuscriptionOrder::STATUS_PAID) {
            $order->status = SuscriptionOrder::STATUS_PAID;
            $order->date_of_payment = now();
            $order->generate();
            $order->save();
        } else if ($this->returnStatusOrder($validated['status']) === SuscriptionOrder::STATUS_REJECTED) {
            $order->status = SuscriptionOrder::STATUS_REJECTED;
            $order->count_rejected_payments += 1;
            $order->save();
        }

        return response()->json(['success' => true]);
    }

    public function viewOrder(Request $request, string $external_id)
    {
        $order = SuscriptionOrder::with('suscription', 'suscription.suscription_plan')
            ->where('external_id', $external_id)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Orden no encontrada'], 404);
        }

        $co      = Company::select('name', 'trade_name', 'number', 'logo')->first();
        $company = [
            'name' => $co->trade_name ?: $co->name,
            'ruc'  => $co->number,
            'logo' => $co->logo ? asset('storage/uploads/logos/' . $co->logo) : null,
        ];
        $public_key = PaymentConfiguration::GetPublicKey();
        $suscription = $order->suscription;
        $person = Person::where('id', $suscription->parent_customer_id)
                        ->orWhere('id', $suscription->customer_id)->first();

        return view('full_suscription::pending_payments.view-order', compact('order', 'company', 'public_key', 'suscription', 'person'));
    }

    public function sendNotify(Request $request)
    {
        $orderIds = $request->input('multipleIds');
        $orders   = SuscriptionOrder::whereIn('id', $orderIds)->get();

        foreach ($orders as $order) {
            $order->notification(['email']);
        }

        return response()->json(['success' => true, 'message' => 'Notificaciones enviadas correctamente']);
    }

    public function sendNotifyOne(string $id)
    {
        $order = SuscriptionOrder::findOrFail($id);
        $order->notification(['email']);

        return response()->json(['success' => true]);
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
