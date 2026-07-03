<?php

namespace Modules\Ecommerce\Models\Tenant;

use App\Models\Tenant\ModelTenant;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use App\Models\Tenant\Person;
use App\Models\Tenant\Order;

class DiscountCouponUsage extends ModelTenant
{
    use UsesTenantConnection;

    protected $table = 'discount_coupon_usages';

    protected $fillable = [
        'discount_coupon_id',
        'person_id',
        'order_id',
    ];

    public function coupon()
    {
        return $this->belongsTo(DiscountCoupon::class, 'discount_coupon_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}
