<?php

namespace Modules\MobileApp\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Document;
use App\Models\Tenant\DocumentPayment;
use App\Models\Tenant\User;

/**
 * Pago recibido (Yape / Plin) capturado por el equipo-oido.
 *
 * Bandeja compartida del negocio: cualquier dispositivo del tenant lista los
 * pagos pendientes y puede reclamarlos para asignarlos a un comprobante.
 *
 * @property int         $id
 * @property string      $provider
 * @property float       $amount
 * @property string|null $sender_name
 * @property string      $raw_text
 * @property string      $package_name
 * @property \Carbon\Carbon $posted_at
 * @property string      $device_id
 * @property string      $dedup_hash
 * @property string      $status
 * @property int|null    $matched_document_id
 * @property int|null    $matched_document_payment_id
 * @property int|null    $claimed_by_user_id
 */
class ReceivedPayment extends ModelTenant
{
    public const PROVIDER_YAPE = 'YAPE';
    public const PROVIDER_PLIN = 'PLIN';

    public const STATUS_PENDING   = 'pending';
    public const STATUS_MATCHED   = 'matched';
    public const STATUS_DISCARDED = 'discarded';

    protected $table = 'received_payments';

    protected $fillable = [
        'provider',
        'amount',
        'sender_name',
        'raw_text',
        'package_name',
        'posted_at',
        'device_id',
        'dedup_hash',
        'status',
        'matched_document_id',
        'matched_document_payment_id',
        'claimed_by_user_id',
    ];

    protected $casts = [
        'amount'    => 'float',
        'posted_at' => 'datetime',
    ];

    /**
     * Comprobante al que se asigno el pago (vinculo logico, sin FK).
     */
    public function matched_document()
    {
        return $this->belongsTo(Document::class, 'matched_document_id');
    }

    /**
     * Pago del comprobante generado al reclamar (vinculo logico, sin FK).
     */
    public function matched_document_payment()
    {
        return $this->belongsTo(DocumentPayment::class, 'matched_document_payment_id');
    }

    /**
     * Usuario que reclamo el pago (vinculo logico, sin FK).
     */
    public function claimed_by_user()
    {
        return $this->belongsTo(User::class, 'claimed_by_user_id');
    }

    /**
     * Filtros para el listado de la bandeja (scroll infinito).
     *
     * Parametros soportados (desde la request):
     * - status: pending | matched | discarded
     * - provider: YAPE | PLIN
     * - device_id: equipo-oido que capturo el pago
     * - input: busqueda parcial por remitente o texto de la notificacion
     *
     * @param  Builder $query
     * @param  \Illuminate\Http\Request $request
     * @return Builder
     */
    public function scopeWhereFilterApi($query, $request)
    {
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('provider')) {
            $query->where('provider', $request->input('provider'));
        }

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->input('device_id'));
        }

        if ($request->filled('input')) {
            $value = $request->input('input');
            $query->where(function ($q) use ($value) {
                $q->where('sender_name', 'like', "%{$value}%")
                  ->orWhere('raw_text', 'like', "%{$value}%");
            });
        }

        return $query;
    }

    /**
     * Indica si el pago sigue disponible para reclamar.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Indica si el pago ya fue reclamado y asignado a un comprobante.
     *
     * @return bool
     */
    public function isMatched()
    {
        return $this->status === self::STATUS_MATCHED;
    }

    /**
     * Estructura de salida para el API mobile.
     *
     * @return array
     */
    public function getApiRowResource()
    {
        return [
            'id'                          => $this->id,
            'provider'                    => $this->provider,
            'amount'                      => (float) $this->amount,
            'sender_name'                 => $this->sender_name,
            'raw_text'                    => $this->raw_text,
            'package_name'                => $this->package_name,
            'posted_at'                   => optional($this->posted_at)->format('Y-m-d H:i:s'),
            'device_id'                   => $this->device_id,
            'dedup_hash'                  => $this->dedup_hash,
            'status'                      => $this->status,
            'matched_document_id'         => $this->matched_document_id,
            'matched_document_payment_id' => $this->matched_document_payment_id,
            'claimed_by_user_id'          => $this->claimed_by_user_id,
            'created_at'                  => optional($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
