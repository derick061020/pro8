<?php

namespace App\Models\System;

use Carbon\Carbon;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class PaymentOrder extends Model
{
    use UsesSystemConnection;

    public $timestamps = false;
    protected $fillable = [
        'uuid',
        'order',
        'date_of_due',
        'notifications',
        'amount',
        'order_state_id',
        'client_id',
        'plan_id',
        'date_of_payment',
        'date_of_notification',
        'description',
        'created_by'
    ];

    protected static function booted()
    {
        static::creating(function (PaymentOrder $order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $casts = [
        'date_of_due' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function order_state()
    {
        return $this->belongsTo(PaymentOrderState::class, 'order_state_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function getCollectionData()
    {
        $date_of_payment = $this->date_of_payment ? Carbon::parse($this->date_of_payment)->format('Y-m-d'): null;
        $date_of_notification = $this->date_of_notification ? Carbon::parse($this->date_of_notification)->diffForHumans() : 'Sin notificar';
        return [
            'id' => $this->id,
            'order' => $this->order,
            'due_date' => $this->date_of_due->format('Y-m-d'),
            'notifications' => $this->notifications,
            'amount' => $this->amount,
            'order_state_id' => $this->order_state_id,
            'order_state' => $this->order_state ? $this->order_state->name : '',
            'client' => $this->client ? $this->client->name : '',
            'date_of_payment' => $date_of_payment,
            'diff_notification' => $date_of_notification, 
            'created_by' => $this->created_by,
            'description' => $this->description ?? 'Pago de servicio',
            // La ruta solo existe en el dominio del sistema; en contexto tenant no esta registrada
            'url_view' => ($this->order_state_id !== 2 && Route::has('system.payments-view.index'))
                ? route('system.payments-view.index', ['uuid' => $this->uuid])
                : null
        ]; 

    }

    /**
     *
     * @param  builder $query
     * @return builder
     */
    public function scopePaymentsTotals($query, $client_id = null)
    {
        if ($client_id) {
            $query->where('client_id', $client_id);
        }

        return $query
            ->join('payment_order_states', 'payment_orders.order_state_id', '=', 'payment_order_states.id')
        ->select('payment_order_states.name', 'payment_order_states.id', DB::raw('SUM(amount) as total'))
        ->groupBy('payment_order_states.name', 'payment_order_states.id');

    }


}
