<?php

// Modules/ClaimsBook/Models/Tenant/Claim.php

namespace Modules\ClaimsBook\Models\Tenant;

use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Catalogs\District;
use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Models\Tenant\User;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * Modelo principal del libro de reclamaciones.
 * Representa un reclamo o queja registrado por un consumidor,
 * con sus datos de reclamante, producto/servicio, detalle del caso y estado.
 */
class Claim extends ModelTenant
{
    use UsesTenantConnection;

    protected $table = 'claims';

    protected $fillable = [
        // Identificación
        'code',
        'tracking_number',
        'parent_code',
        'public_code',

        // Paso 1: datos del reclamante
        'identity_document_type',
        'identity_document_number',
        'name',
        'district_id',
        'address',
        'email',
        'phone',

        // Paso 2: producto/servicio
        'asset_type',
        'asset_description',
        'asset_date',
        'has_receipt',
        'receipt_series',
        'receipt_number',
        'receipt_amount',
        'receipt_currency',

        // Paso 3: detalles del caso
        'claim_type',
        'detail',
        'expected_result',
        'channel',
        'attachments',
        'terms_accepted',

        // Gestión interna
        'status_claim_id',
        'assigned_user_id',
        'resolution',
        'response_attachments',
        'pdf_path',
        'is_closed',
        'closed_at',
        'due_date',
    ];

    protected $casts = [
        'tracking_number'      => 'integer',
        'has_receipt'          => 'boolean',
        'receipt_amount'       => 'float',
        'terms_accepted'       => 'boolean',
        'is_closed'            => 'boolean',
        'closed_at'            => 'datetime',
        'asset_date'           => 'date',
        'due_date'             => 'date',
        'attachments'          => 'array',
        'response_attachments' => 'array',
    ];

    // ──────────────────────────────────────────────────────────────
    // Eventos del modelo
    // ──────────────────────────────────────────────────────────────

    /**
     * Genera automáticamente public_code y due_date al crear un nuevo reclamo.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function (self $claim) {
            // Generar código público único de 5 caracteres (alfabeto sin ambigüedades)
            if (empty($claim->public_code)) {
                $chars = 'ACDEFGHJKMNPRTVWXYZ34679';
                do {
                    $code = '';
                    for ($i = 0; $i < 5; $i++) {
                        $code .= $chars[random_int(0, strlen($chars) - 1)];
                    }
                } while (static::where('public_code', $code)->exists());

                $claim->public_code = $code;
            }

            // Calcular fecha límite: 15 días hábiles (sin sábados ni domingos)
            if (empty($claim->due_date)) {
                $claim->due_date = static::calculateDueDate(Carbon::now());
            }
        });
    }

    /**
     * Calcula la fecha límite sumando $businessDays días hábiles a la fecha dada,
     * excluyendo sábados y domingos.
     */
    protected static function calculateDueDate(Carbon $from, int $businessDays = 15): Carbon
    {
        $date = $from->copy();
        $count = 0;

        while ($count < $businessDays) {
            $date->addDay();
            if (!$date->isWeekend()) {
                $count++;
            }
        }

        return $date;
    }

    /**
     * Calcula los días hábiles restantes desde hoy hasta `due_date`.
     * - Devuelve null si `due_date` no está definido.
     * - Devuelve 0 si `due_date` es igual o anterior a la fecha actual.
     */
    protected function calculateRemainingBusinessDays(): ?int
    {
        if (!$this->due_date) {
            return null;
        }

        $start = Carbon::now()->startOfDay();
        $end = $this->due_date->copy()->startOfDay();

        if ($end->lte($start)) {
            return 0;
        }

        $count = 0;
        // Contar días hábiles entre start (hoy) y end (due_date), excluyendo sábados y domingos
        while ($start->lt($end)) {
            $start->addDay();
            if (!$start->isWeekend()) {
                $count++;
            }
        }

        return $count;
    }

    // ──────────────────────────────────────────────────────────────
    // Relaciones
    // ──────────────────────────────────────────────────────────────

    /**
     * Usuario asignado para atender el reclamo.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * Estado actual del reclamo.
     */
    public function statusClaim()
    {
        return $this->belongsTo(StatusClaim::class, 'status_claim_id');
    }

    /**
     * Distrito (ubigeo) del domicilio del reclamante.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /**
     * Tipo de documento de identidad del reclamante.
     * Usa la columna identity_document_type como FK al catálogo (PK string, sin migración adicional).
     */
    public function identityDocumentType()
    {
        return $this->belongsTo(IdentityDocumentType::class, 'identity_document_type', 'id');
    }

    // ──────────────────────────────────────────────────────────────
    // Datos para API / colecciones Vue
    // ──────────────────────────────────────────────────────────────

    /**
     * Datos resumidos para listar en la tabla de la vista index.
     */
    public function getCollectionData(): array
    {
        return [
            'id'                      => $this->id,
            'code'                    => $this->code,
            'tracking_number'         => $this->tracking_number,
            'parent_code'             => $this->parent_code,

            // Reclamante
            'identity_document_type'        => $this->identity_document_type,
            'identity_document_type_label'  => $this->identityDocumentType
                ? $this->identityDocumentType->description
                : $this->identity_document_type,
            'identity_document_number'      => $this->identity_document_number,
            'name'                          => $this->name,
            'email'                   => $this->email,
            'phone'                   => $this->phone,

            // Tipo de caso
            'claim_type'              => $this->claim_type,
            'claim_type_label'        => $this->claim_type === 'queja' ? 'Queja' : 'Reclamo',

            // Fechas
            'date'                    => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'date_formatted'          => $this->created_at ? $this->created_at->format('d/m/Y') : null,
            'asset_date'              => $this->asset_date ? $this->asset_date->format('Y-m-d') : null,
            'created_at'              => $this->created_at ? $this->created_at->toDateTimeString() : null,

            // Comprobante
            'has_receipt'             => $this->has_receipt,
            'receipt_series'          => $this->receipt_series,
            'receipt_number'          => $this->receipt_number,
            'receipt_amount'          => $this->receipt_amount,
            'receipt_currency'        => $this->receipt_currency,
            'receipt_label'           => $this->has_receipt
                ? "{$this->receipt_series}-{$this->receipt_number}"
                : null,

            // Estado
            'status_claim_id'         => $this->status_claim_id,
            'status_claim'            => $this->statusClaim
                ? $this->statusClaim->getCollectionData()
                : null,

            // Canal
            'channel'                 => $this->channel,

            // Responsable asignado
            'assigned_user_id'        => $this->assigned_user_id,
            'assigned_user_name'      => $this->assignedUser
                ? $this->assignedUser->name
                : null,

            // Gestión
            'is_closed'               => $this->is_closed,
            'closed_at'               => $this->closed_at ? $this->closed_at->toDateTimeString() : null,
            'days_to_resolve'         => $this->is_closed && $this->closed_at && $this->created_at
                ? (int) ceil($this->created_at->diffInHours($this->closed_at) / 24)
                : null,
            'resolution'              => $this->resolution,

            // Archivos adjuntos al reclamo (convertir a URL pública cuando aplique)
            'attachments' => collect($this->attachments ?? [])->map(function ($path) {
                return $this->pathToUrl($path);
            })->filter()->values()->all(),

            // Adjuntos a la respuesta oficial (convertir a URL pública cuando aplique)
            'response_attachments' => collect($this->response_attachments ?? [])->map(function ($path) {
                return $this->pathToUrl($path);
            })->filter()->values()->all(),

            // PDF de constancia
            'pdf_path' => $this->pdf_path,
            'pdf_url'  => $this->pathToUrl($this->pdf_path),

            // Código público de consulta
            'public_code'             => $this->public_code,

            // Fecha límite de atención (15 días hábiles)
            'due_date'                => $this->due_date ? $this->due_date->format('Y-m-d') : null,
            'due_date_formatted'      => $this->due_date ? $this->due_date->format('d/m/Y') : null,
            // Días hábiles restantes hasta la fecha límite
            'remaining_business_days' => $this->calculateRemainingBusinessDays(),
        ];
    }

    /**
     * Datos completos para el modal de detalle (incluye todos los campos del formulario).
     */
    public function getDetailData(): array
    {
        return array_merge($this->getCollectionData(), [
            // Paso 1 completo
            'district_id'             => $this->district_id,
            'district_name'           => $this->district ? $this->district->description : null,
            'address'                 => $this->address,

            // Paso 2 completo
            'asset_type'              => $this->asset_type,
            'asset_description'       => $this->asset_description,

            // Paso 3 completo
            'detail'                  => $this->detail,
            'expected_result'         => $this->expected_result,
            'channel'                 => $this->channel,
            'terms_accepted'          => $this->terms_accepted,
        ]);
    }

    /**
     * Convert a storage path or URL to a public-accessible URL.
     * - If given an absolute URL or path starting with '/', returns it unchanged.
     * - If exists in tenant disk, returns the claim file route.
     * - If exists in public disk, returns Storage::disk('public')->url(...).
     * - Otherwise returns null.
     */
    protected function pathToUrl($path)
    {
        if (empty($path)) return null;

        if (Str::startsWith($path, ['http://', 'https://', '/'])) return $path;

        // If path is like 'claims/{folder}/{filename}', build friendly route
        if (preg_match('#^claims/([^/]+)/(.+)$#', $path, $m)) {
            $folder = $m[1];
            $filename = basename($m[2]);
            if (Storage::disk('tenant')->exists($path)) {
                return route('tenant.claims.file', ['folder' => $folder, 'filename' => $filename]);
            }
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        // As a last resort, if file exists on tenant disk but not matching 'claims/...', try raw existence
        if (Storage::disk('tenant')->exists($path)) {
            // try to extract folder and filename
            $parts = explode('/', $path);
            $filename = array_pop($parts);
            $folder = array_pop($parts) ?? '';
            return route('tenant.claims.file', ['folder' => $folder, 'filename' => $filename]);
        }

        return null;
    }
}
