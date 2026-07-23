<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Order;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class StatusOrder extends ModelTenant
{
    use UsesTenantConnection;

    protected $fillable = [
        'description',
        'color',
        'sort_order',
        'is_initial',
        'is_final',
        'is_payment_status',
        'is_order_status',
        'is_shipping_status',
        'action_generate_document',
        'action_discount_stock',
        'action_mark_payment',
        'action_send_email',
        'action_notify_dispatch',
        'action_generate_remission',
        'action_free_reserved_stock',
        'action_block_returns',
        'action_void_order',
    ];

    protected $casts = [
        'sort_order'               => 'integer',
        'is_initial'               => 'boolean',
        'is_final'                 => 'boolean',
        'is_payment_status'        => 'boolean',
        'is_order_status'          => 'boolean',
        'is_shipping_status'       => 'boolean',
        'action_generate_document' => 'boolean',
        'action_discount_stock'    => 'boolean',
        'action_mark_payment'      => 'boolean',
        'action_send_email'        => 'boolean',
        'action_notify_dispatch'   => 'boolean',
        'action_generate_remission'=> 'boolean',
        'action_free_reserved_stock' => 'boolean',
        'action_block_returns'     => 'boolean',
        'action_void_order'        => 'boolean',
    ];

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function payment_order()
    {
        return $this->hasMany(Order::class, 'payment_status_order_id');
    }

    public function shipping_order()
    {
        return $this->hasMany(Order::class, 'shipping_status_order_id');
    }
}

