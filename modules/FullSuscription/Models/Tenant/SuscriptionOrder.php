<?php

namespace Modules\FullSuscription\Models\Tenant;

use App\Models\Tenant\Company;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Document;
use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Person;
use GuzzleHttp\Client as HttpClient;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Modules\FullSuscription\Emails\SendPaymentReminders;
use Modules\FullSuscription\Services\SuscriptionDocumentService;

/**
 * @property string   $status
 * @property float    $amount
 * @property string   $number
 * @property string   $external_id
 * @property \Carbon\Carbon $date_of_issue
 * @property \Carbon\Carbon $date_of_due
 * @property \Carbon\Carbon $date_of_payment
 * @property UserRelSuscriptionPlan $suscription
 */
class SuscriptionOrder extends ModelTenant
{
    use SuscriptionDocumentService;

    const STATUS_PENDING  = 'pending';
    const STATUS_PAID     = 'paid';
    const STATUS_CANCELED = 'canceled';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED  = 'expired';

    const TYPE_NEW_SUSCRIPTION   = 'new_suscription';
    const TYPE_SUSCRIPTION_ORDER = 'suscription_order';

    protected $fillable = [
        'suscription_id',
        'type',
        'external_id',
        'person_number',
        'number',
        'count_rejected_payments',
        'amount',
        'date_of_payment',
        'date_of_due',
        'date_of_issue',
        'status',
        'documentable_type',
        'documentable_id',
        'count_notification',
        'date_notification',
    ];

    protected $casts = [
        'date_of_payment'   => 'datetime',
        'date_of_issue'     => 'datetime',
        'date_of_due'       => 'datetime',
        'date_notification' => 'datetime',
    ];

    public $timestamps = false;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $order) {
            if (empty($order->number) && $order->type === self::TYPE_SUSCRIPTION_ORDER) {
                $last  = static::whereNotNull('number')->orderByDesc('id')->value('number');
                $next  = $last ? (intval(substr($last, 3)) + 1) : 1;
                $order->number = 'SO-' . str_pad($next, 4, '0', STR_PAD_LEFT);
            }

            if (empty($order->external_id) && $order->type === self::TYPE_SUSCRIPTION_ORDER) {
                do {
                    $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                } while (static::where('external_id', $code)->exists());
                $order->external_id = $code;
            }
        });
    }

    public function suscription()
    {
        return $this->belongsTo(UserRelSuscriptionPlan::class, 'suscription_id');
    }

    public function documentable()
    {
        return $this->morphTo('documentable');
    }

    public static function getStatusTypes(): array
    {
        return [
            ['value' => self::STATUS_PENDING,  'description' => 'Pendiente'],
            ['value' => self::STATUS_PAID,      'description' => 'Pagado'],
            ['value' => self::STATUS_CANCELED,  'description' => 'Cancelado'],
            ['value' => self::STATUS_REJECTED,  'description' => 'Rechazado'],
            ['value' => self::STATUS_EXPIRED,   'description' => 'Expirado'],
        ];
    }

    public static function getOrderTypes(): array
    {
        return [
            ['value' => self::TYPE_NEW_SUSCRIPTION,   'description' => 'Nueva suscripción'],
            ['value' => self::TYPE_SUSCRIPTION_ORDER, 'description' => 'Orden de suscripción'],
        ];
    }

    public function generate(): ?array
    {
        try {
            if (!is_null($this->documentable)) {
                return null;
            }

            $response = $this->document($this->suscription);

            if (!$response['success'] ) {
                return $response;
            }

            $document = Document::find($response['document_id']);

            $this->documentable()->associate($document);
            $this->save();

            return [
                'success' => $response['success'],
                'message' => $response['response'],
            ];
        } catch (\Throwable $th) {
            Log::error('Error al generar el documento para la orden de suscripción:', [
                'suscription_order_id' => $this->id,
                'suscription_id'       => $this->suscription_id,
                'error_message'        => $th->getMessage(),
                'line'                 => $th->getLine(),
                'file'                 => $th->getFile(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al generar el documento. Por favor, intente nuevamente o contacte con soporte.' . $th->getMessage(),
            ];
        }
    }

    public function notification(array $medium): void
    {
        $suscription = $this->suscription;
        $person      = Person::find($suscription->parent_customer_id);
        $message     = $this->transformMessage();

        if (in_array('email', $medium) && $person && $person->email) {
            Configuration::setConfigSmtpMail();
            Mail::to($person->email)->send(new SendPaymentReminders($this, $message));
        }

        if (in_array('whatsapp', $medium)) {
            $client  = new HttpClient();
            $company = Company::first();

            $client->post(
                $company->qr_api_url_ws . '/api/message/send-text',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $company->qr_api_key_ws,
                        'Accept'        => 'application/json',
                    ],
                    'json'   => [
                        'number'  => '51' . $person->telephone,
                        'message' => $message,
                    ],
                    'verify' => false,
                ]
            );
        }

        $this->count_notification = ($this->count_notification ?? 0) + 1;
        $this->date_notification  = now();
        $this->save();
    }

    public function getUrl(): string
    {
        $scheme   = parse_url(config('app.url'), PHP_URL_SCHEME);
        $tenant   = app(Environment::class)->tenant();
        $hostname = Hostname::where('website_id', $tenant->id)->first();

        if (Route::has('tenant.full_suscription.pending-payments.view-order')) {
            return route('tenant.full_suscription.pending-payments.view-order', ['external_id' => $this->external_id]);
        }

        return $scheme . '://' . $hostname->fqdn . '/full-suscription/pending-payments/order/' . $this->external_id;
    }

    public function getVariablesMessage(): array
    {
        return [
            '{{ plan_nombre }}'      => $this->suscription->suscription_plan->name,
            '{{ cliente }}'          => data_get($this->suscription->parent_customer, 'name', ''),
            '{{ order_id }}'         => $this->number,
            '{{ order_total }}'      => $this->amount,
            '{{ order_payment_url }}' => $this->getUrl(),
            '{{ link_pago }}'        => $this->getUrl(),
        ];
    }

    public function transformMessage(): string
    {
        $message = Configuration::first()->message_notify_to_suscription_orders ?? '';

        foreach ($this->getVariablesMessage() as $key => $value) {
            $message = str_replace($key, $value, $message);
        }

        return $message;
    }

    public function getCollectionData(): array
    {
        return [
            'id'                 => $this->id,
            'order_number'       => $this->number,
            'number'             => $this->documentable ? $this->documentable->number_full : null,
            'date_of_issue'      => $this->date_of_issue ? $this->date_of_issue->format('Y-m-d') : null,
            'date_of_payment'    => $this->date_of_payment ? $this->date_of_payment->format('Y-m-d') : null,
            'plan_name'          => $this->suscription?->suscription_plan?->name,
            'subscriptor_name'   => data_get($this->suscription?->parent_customer, 'name'),
            'status'             => $this->getStatus($this->status),
            'status_value'       => $this->status,
            'number_full'        => $this->documentable ? $this->documentable->number_full : null,
            'download_pdf_a4'    => $this->documentable ? $this->documentable->download_external_pdf : null,
            'total'              => $this->amount,
            'checkout_url'       => $this->external_id ?  $this->getUrl() : null,
            'state_type_id'      => $this->documentable ? $this->documentable->state_type_id : null,
            'count_notification' => $this->count_notification ?? 0,
            'date_notification'  => $this->date_notification ? $this->date_notification->format('d/m/Y H:i') : null,
        ];
    }

    public function getStatus(?string $status): string
    {
        return match ($status) {
            'paid'     => 'Pagado',
            'canceled' => 'Cancelado',
            'rejected' => 'Rechazado',
            'expired'  => 'Expirado',
            default    => 'Pendiente',
        };
    }

    public function scopePast($builder)
    {
        return $builder->where('date_of_due', '<=', now());
    }
}
