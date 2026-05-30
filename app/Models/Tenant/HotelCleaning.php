<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class HotelCleaning extends ModelTenant
{
    use UsesTenantConnection;
    
    protected $table = 'hotel_cleanings';
    
    protected $fillable = [
        'hotel_room_id',
        'user_id',
        'start_time',
        'end_time',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Obtener la habitación asociada
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(HotelRoom::class, 'hotel_room_id');
    }

    /**
     * Obtener el limpiador asignado
     */
    public function cleaner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope para obtener limpiezas pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para obtener limpiezas en progreso
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope para obtener limpiezas completadas
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Verificar si la limpieza está en progreso
     */
    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    /**
     * Verificar si la limpieza está completada
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Marcar limpieza como en progreso
     */
    public function markAsInProgress()
    {
        $this->status = 'in_progress';
        $this->start_time = now();
        $this->save();
    }

    /**
     * Marcar limpieza como completada
     */
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->end_time = now();
        $this->save();
    }
}
