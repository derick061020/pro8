<?php

    namespace App\Models\Tenant;

    use App\Models\Tenant\Catalogs\DocumentType;
    use Carbon\Carbon;
    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\HasOne;
    use Modules\Document\Models\SeriesConfiguration;
    use Hyn\Tenancy\Traits\UsesTenantConnection;
    use Illuminate\Database\Eloquent\Collection;


    /**
     * Class Series
     *
     * @property int                              $id
     * @property int                              $establishment_id
     * @property string                           $document_type_id
     * @property string                           $number
     * @property bool                             $contingency
     * @property bool                             $dedicated
     * @property int|null                         $series_device_group_id
     * @property bool                             $in_use
     * @property SeriesDeviceGroup|null           $device_group
     * @property Carbon|null                      $created_at
     * @property Carbon|null                      $updated_at
     * @property DocumentType                     $document_type
     * @property Establishment                    $establishment
     * @property Collection|SeriesConfiguration[] $series_configurations
     * @package App\Models
     * @property Collection|Document[]            $documents
     * @property int|null                         $documents_count
     * @method static Builder|Series filterDocumentType($document_type_id = 0)
     * @method static Builder|Series filterEstablishment($establishment_id = 0)
     * @method static Builder|Series newModelQuery()
     * @method static Builder|Series newQuery()
     * @method static Builder|Series query()
     * @mixin ModelTenant
     * @mixin Eloquent
     * @method static Builder|Series filterSeries($establishment_id = 0)
     */
    class Series extends ModelTenant
    {

        use UsesTenantConnection;

        protected $table = 'series';

        protected $fillable = [
            'establishment_id',
            'document_type_id',
            'number',
            'contingency',
            'dedicated',
            'series_device_group_id',
            'in_use',
        ];

        protected $casts = [
            'contingency' => 'bool',
            'dedicated'   => 'bool',
            'in_use'      => 'bool',
        ];

        /**
         * @return BelongsTo
         */
        public function establishment()
        {
            return $this->belongsTo(Establishment::class);
        }

        /**
         * Grupo de dispositivo al que pertenece la serie (solo dedicadas).
         *
         * @return BelongsTo
         */
        public function device_group()
        {
            return $this->belongsTo(SeriesDeviceGroup::class, 'series_device_group_id');
        }

        /**
         * @return BelongsTo
         */
        public function document_type()
        {
            return $this->belongsTo(DocumentType::class, 'document_type_id');
        }

        /**
         * @param $value
         */
        public function setNumberAttribute($value)
        {
            $this->attributes['number'] = strtoupper($value);
        }

        /**
         * @return HasMany
         */
        public function documents()
        {
            return $this->hasMany(Document::class, 'series', 'number');
        }

        /**
         * @return HasOne
         */
        public function series_configurations()
        {
            return $this->hasOne(SeriesConfiguration::class);
        }


        /**
         * @param int $establishment_id
         *
         * @return Builder
         */
        public function scopeFilterEstablishment($query, $establishment_id = 0)
        {
            return $query->where('establishment_id', $establishment_id);
        }

        /**
         * @param int $document_type_id
         *
         * @return Builder
         */
        public function scopeFilterDocumentType($query, $document_type_id = 0)
        {
            return $query->where('document_type_id', $document_type_id);
        }

        /**
         * @param Builder $query
         * @param int     $establishment_id
         *
         * @return Builder
         */
        public function scopeFilterSeries(Builder $query, $establishment_id = 0)
        {
            $query->where('establishment_id', $establishment_id);
            return $query;
        }

        /**
         * Devuelve un array de datos con estrcutura unificada para series
         *
         * @param int|null    $document_id
         * @param int|null    $series_id
         * @param string|null $userType
         *
         * @return array
         */
        public function getCollectionData(?int $document_id = 0, ?int  $series_id = 0, ?string $userType = 'seller'): array
        {
            $document_id = (int)$document_id;
            $series_id = (int)$series_id;
            $disabled = false;
            if ($document_id == $this->document_type_id  && $userType !== 'admin') {
                $disabled = !(($series_id == $this->id));
            }
            return [
                'id' => $this->id,
                'contingency' => (bool)$this->contingency,
                'document_type_id' => $this->document_type_id,
                'establishment_id' => $this->establishment_id,
                'number' => $this->number,
                'disabled' => $disabled,
            ];

        }


        /**
         *
         * Validar y determinar serie por defecto para el usuario
         *
         * @return bool
         */
        public function getIsDefaultAttribute()
        {
            $is_default = false;
            $user = auth()->user();
            $default_series_id = $user->series_id;
            $default_document_type_id = $user->document_id;

            if($default_document_type_id === $this->document_type_id && $default_series_id === $this->id)
            {
                $is_default = true;
            }

            return $is_default;
        }


        /**
         *
         * Obtener datos para api (app)
         *
         * @return array
         */
        public function getApiRowResource()
        {
            return [
                'id' => $this->id,
                'document_type_id' => $this->document_type_id,
                'number' => $this->number,
                'is_default' => $this->is_default,
                'establishment_id' => $this->establishment_id,
            ];
        }

        /**
         *
         * Filtrar series para documentos de venta, cpe y nv - modo pos app
         *
         * @param  Builder $query
         * @return Builder
         */
        public function scopeOnlySaleDocuments($query)
        {
            return $query->where('establishment_id', auth()->user()->establishment_id)
                    ->whereIn('document_type_id', DocumentType::SALE_DOCUMENT_TYPES);
        }


        /**
         * Solo series dedicadas.
         *
         * @param  Builder $query
         * @return Builder
         */
        public function scopeDedicated(Builder $query)
        {
            return $query->where('dedicated', true);
        }


        /**
         * Solo series NO dedicadas (comportamiento estandar de emision).
         *
         * @param  Builder $query
         * @return Builder
         */
        public function scopeNotDedicated(Builder $query)
        {
            return $query->where('dedicated', false);
        }


        /**
         * Solo series que ya tienen comprobantes emitidos.
         *
         * @param  Builder $query
         * @return Builder
         */
        public function scopeInUse(Builder $query)
        {
            return $query->where('in_use', true);
        }


        /**
         * Marcar como "en uso" la serie que coincide con (tipo de documento, número) al emitir
         * el primer comprobante. Flag denormalizado (§4.7) para bloquear correlativo y borrado
         * sin consultar las tablas de comprobantes. Idempotente y barato (update indexado).
         *
         * @param  string $document_type_id
         * @param  string $number
         * @return void
         */
        public static function markInUse($document_type_id, $number): void
        {
            if (empty($number)) {
                return;
            }

            static::where('document_type_id', $document_type_id)
                ->where('number', $number)
                ->where('in_use', false)
                ->update(['in_use' => true]);
        }


        /**
         * Regla central de visibilidad de series (ver docs §2).
         *
         * Aplica SOLO el filtro dedicado/grupo sobre el query recibido (componible: el
         * establecimiento y los tipos de documento los mantiene cada llamador):
         * - Con grupo dedicado activo ($group_id): SOLO las series de ese grupo.
         * - Sin grupo activo: series NO dedicadas (las dedicadas quedan ocultas).
         *
         * No aplicar en reportes/consultas administrativas (§9-E), donde se listan todas.
         *
         * @param  Builder  $query
         * @param  int|null $group_id
         * @return Builder
         */
        public function scopeUsableInContext(Builder $query, $group_id = null)
        {
            if ($group_id) {
                return $query->where('series_device_group_id', $group_id);
            }

            return $query->where('dedicated', false);
        }

    }
