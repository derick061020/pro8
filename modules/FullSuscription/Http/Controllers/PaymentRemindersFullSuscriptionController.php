<?php

namespace Modules\FullSuscription\Http\Controllers;

use App\Models\Tenant\Configuration;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\FullSuscription\Models\Tenant\SuscriptionPaymentReminder;

class PaymentRemindersFullSuscriptionController extends Controller
{
    public function index()
    {
        return view('full_suscription::payment-reminders.index');
    }

    public function configuration()
    {
        $cfg = Configuration::select('before_day_creation_suscription_order')->first();

        return [
            'before_day_creation_suscription_order' => $cfg->before_day_creation_suscription_order,
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reminders'                     => 'required|array',
            'reminders.*.id'                => 'nullable',
            'reminders.*.shipping_medium'   => 'required|in:email,whatsapp',
            'reminders.*.reminder_type'     => 'required|in:before,same_day,after',
            'reminders.*.reminder_days'     => 'required|integer|min:0',
            'reminders.*.reminder_time'     => 'required|date_format:H:i',
        ]);

        foreach ($validated['reminders'] as $reminder) {
            SuscriptionPaymentReminder::updateOrCreate(['id' => $reminder['id']], $reminder);
        }

        return [
            'success' => true,
            'message' => 'Recordatorios de pago guardados exitosamente',
        ];
    }

    public function saveConfiguration(Request $request)
    {
        $validated = $request->validate([
            'before_day_creation_suscription_order' => 'required|integer|min:0',
        ]);

        $cfg = Configuration::first();
        $cfg->before_day_creation_suscription_order = $validated['before_day_creation_suscription_order'];
        $cfg->save();

        return [
            'success' => true,
            'message' => 'Configuración guardada exitosamente',
        ];
    }

    public function records()
    {
        return SuscriptionPaymentReminder::get()->map(function ($reminder) {
            return [
                'id'              => $reminder->id,
                'shipping_medium' => $reminder->shipping_medium,
                'reminder_type'   => $reminder->reminder_type,
                'reminder_days'   => $reminder->reminder_days,
                'reminder_time'   => $reminder->reminder_time->format('H:i'),
            ];
        });
    }

    public function destroy($id)
    {
        $reminder = SuscriptionPaymentReminder::findOrFail($id);
        $reminder->delete();

        return [
            'success' => true,
            'message' => 'Recordatorio de pago eliminado exitosamente',
        ];
    }
}
