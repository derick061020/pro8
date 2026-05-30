<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant\User;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class HotelRentChange extends Model
{
    use UsesTenantConnection;
    
    protected $table = 'hotel_rent_changes';
    
    protected $fillable = [
        'hotel_rent_id',
        'change_type',
        'old_values',
        'new_values',
        'notes',
        'price_difference',
        'user_id'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'price_difference' => 'decimal:2'
    ];

    /**
     * Get the rent that owns the change.
     */
    public function rent()
    {
        return $this->belongsTo(HotelRent::class, 'hotel_rent_id');
    }

    /**
     * Get the user that made the change.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the change type label.
     */
    public function getChangeTypeLabelAttribute()
    {
        $labels = [
            'CHECKIN' => 'Check-In',
            'CHECKOUT' => 'Check-Out',
            'EXTENSION' => 'Extensión',
            'DATE_EDIT' => 'Edición de Fechas',
            'ROOM_CHANGE' => 'Cambio de Habitación',
            'PRICE_CHANGE' => 'Cambio de Precio'
        ];

        return $labels[$this->change_type] ?? $this->change_type;
    }

    /**
     * Get the change type icon.
     */
    public function getChangeTypeIconAttribute()
    {
        $icons = [
            'CHECKIN' => 'fa fa-sign-in-alt',
            'CHECKOUT' => 'fa fa-sign-out-alt',
            'EXTENSION' => 'fa fa-calendar-plus',
            'DATE_EDIT' => 'fa fa-calendar-alt',
            'ROOM_CHANGE' => 'fa fa-exchange-alt',
            'PRICE_CHANGE' => 'fa fa-dollar-sign'
        ];

        return $icons[$this->change_type] ?? 'fa fa-info-circle';
    }

    /**
     * Get the change type class for styling.
     */
    public function getChangeTypeClassAttribute()
    {
        $classes = [
            'CHECKIN' => 'timeline-checkin',
            'CHECKOUT' => 'timeline-checkout',
            'EXTENSION' => 'timeline-extension',
            'DATE_EDIT' => 'timeline-date-edit',
            'ROOM_CHANGE' => 'timeline-room-change',
            'PRICE_CHANGE' => 'timeline-price-change'
        ];

        return $classes[$this->change_type] ?? 'timeline-default';
    }

    /**
     * Scope to get changes by type.
     */
    public function scopeByChangeType($query, $changeType)
    {
        return $query->where('change_type', $changeType);
    }

    /**
     * Scope to get recent changes.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
