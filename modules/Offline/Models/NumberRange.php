<?php

namespace Modules\Offline\Models;

use App\Models\Tenant\ModelTenant;
use Illuminate\Database\Eloquent\Builder;

/**
 * Bloque de correlativos reservado para un terminal offline.
 *
 * Ejemplo: al terminal T01 se le reservan los números 500 al 999 de la serie
 * B001. El servidor no emitirá números dentro de ese bloque, así que cuando el
 * terminal sube sus ventas no puede haber duplicados.
 *
 * @property string   $terminal_code
 * @property string   $series
 * @property int      $from_number
 * @property int      $to_number
 * @property int|null $current_number
 * @property string   $status
 */
class NumberRange extends ModelTenant
{
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_EXHAUSTED = 'exhausted';
    public const STATUS_RELEASED  = 'released';

    protected $table = 'offline_number_ranges';

    protected $fillable = [
        'uuid',
        'terminal_code',
        'model_alias',
        'document_type_id',
        'series',
        'from_number',
        'to_number',
        'current_number',
        'status',
        'allocated_at',
        'exhausted_at',
        'reported_at',
    ];

    protected $casts = [
        'from_number'    => 'int',
        'to_number'      => 'int',
        'current_number' => 'int',
        'allocated_at'   => 'datetime',
        'exhausted_at'   => 'datetime',
        'reported_at'    => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForSeries(Builder $query, string $modelAlias, ?string $documentTypeId, string $series): Builder
    {
        return $query
            ->where('model_alias', $modelAlias)
            ->where('series', $series)
            ->when($documentTypeId !== null, fn (Builder $q) => $q->where('document_type_id', $documentTypeId));
    }

    /**
     * Próximo número disponible del bloque, o null si ya se agotó.
     */
    public function nextNumber(): ?int
    {
        $next = $this->current_number === null
            ? $this->from_number
            : $this->current_number + 1;

        return $next > $this->to_number ? null : $next;
    }

    public function remaining(): int
    {
        $used = $this->current_number === null ? 0 : ($this->current_number - $this->from_number + 1);

        return max(0, ($this->to_number - $this->from_number + 1) - $used);
    }

    public function total(): int
    {
        return $this->to_number - $this->from_number + 1;
    }

    /**
     * Registra que se consumió un número y cierra el bloque si fue el último.
     */
    public function consume(int $number): void
    {
        $this->current_number = $number;

        if ($number >= $this->to_number) {
            $this->status       = self::STATUS_EXHAUSTED;
            $this->exhausted_at = now();
        }

        $this->save();
    }
}
