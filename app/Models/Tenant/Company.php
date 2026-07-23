<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Models\Tenant\SoapType;
use Modules\LevelAccess\Models\SystemActivityLog;


/**
 * Class Company
 *
 * @package App\Models\Tenant
 * @mixin  ModelTenant
 */
class Company extends ModelTenant
{
    protected $with = ['identity_document_type'];
    protected $fillable = [
        'user_id',
        'identity_document_type_id',
        'number',
        'name',
        'trade_name',
        'soap_send_id',
        'soap_type_id',
        'soap_username',
        'soap_password',
        'soap_url',
        'certificate',
        'digital_certificate_qztray',
        'private_certificate_qztray',
        'certificate_due',
        'logo',
        'logo_dark',
        'detraction_account',
        'operation_amazonia',
        'img_firm',
        'cod_digemid',
        'integrated_query_client_id',
        'integrated_query_client_secret',
        'app_logo',
        'send_document_to_pse',
        'url_send_cdr_pse',
        'url_signature_pse',
        'client_id_pse',
        'password_pse',
        'url_login_pse',
        'user_pse',
        'ws_api_token',
        'ws_api_phone_number_id',
        'soap_sunat_username',
        'soap_sunat_password',
        'api_sunat_id',
        'api_sunat_secret',
        'title_web',
        'pse_provider_id',
        'mtc_code'
    ];

    protected $casts = [
        'send_document_to_pse' => 'bool',
    ];

    /**
     * @return mixed
     */
    public function getCodDigemid()
    {
        return $this->cod_digemid;
    }

    /**
     * @param mixed $cod_digemid
     *
     * @return Company
     */
    public function setCodDigemid($cod_digemid)
    {
        $this->cod_digemid = $cod_digemid;
        return $this;
    }

    public function identity_document_type()
    {
        return $this->belongsTo(IdentityDocumentType::class, 'identity_document_type_id');
    }

    public static function active()
    {
        $company = Company::first();

        // Optimiza logos pesados para que al incrustarlos en el PDF (base64) no inflen el tamaño/memoria.
        // Se hace de forma "lazy" para que logos antiguos también queden protegidos.
        if ($company) {
            self::optimizeLogoFieldIfNeeded($company, 'logo');
            self::optimizeLogoFieldIfNeeded($company, 'logo_dark');
            self::optimizeLogoFieldIfNeeded($company, 'app_logo');
        }

        return $company;
    }

    protected static function optimizeLogoFieldIfNeeded(Company $company, string $field): void
    {
        $filename = $company->$field ?? null;
        if (!$filename) return;

        $absolutePath = public_path('storage/uploads/logos/' . $filename);
        if (!is_file($absolutePath)) return;

        $triggerBytes = 1024 * 1024; // ~1MB (cuando el logo ya pesa mucho)
        $currentSize = @filesize($absolutePath);
        if ($currentSize === false || $currentSize <= $triggerBytes) return;

        $mime = mime_content_type($absolutePath) ?: '';
        if (str_contains($mime, 'svg')) {
            // SVG: no lo convertimos/optimiza aquí para evitar problemas de compatibilidad.
            return;
        }

        try {
            $maxWidth = 300;
            $targetBytes = 100 * 1024; // ~100KB

            $optimized = UploadFileHelper::optimizeRasterImageToTargetJpg(
                $absolutePath,
                $maxWidth,
                $targetBytes,
                60,
                10
            );

            if (!$optimized || empty($optimized['bytes'])) return;

            $optimizedBytes = (string) $optimized['bytes'];
            $ext = $optimized['extension'] ?? 'jpg';

            // Si la extensión resultante coincide con la actual, sobreescribimos en el mismo archivo;
            // si cambia (p. ej. png->jpg), creamos uno nuevo con la extensión correcta.
            $pathInfo = pathinfo($absolutePath);
            $currentExt = strtolower($pathInfo['extension'] ?? '');
            $newFilename = $pathInfo['filename'] . '.' . $ext;

            if ($currentExt === $ext || ($currentExt === 'jpeg' && $ext === 'jpg')) {
                file_put_contents($absolutePath, $optimizedBytes);
                return;
            }

            $newAbsolutePath = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $newFilename;
            file_put_contents($newAbsolutePath, $optimizedBytes);

            if (is_file($newAbsolutePath)) {
                $company->$field = $newFilename;
                $company->save();
            }
        } catch (\Throwable $e) {
            // No rompemos el flujo si la optimización falla; el PDF igual se generará con el logo original.
            return;
        }
    }

    /**
     * @return string|null
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @param string|null $logo
     *
     * @return Company
     */
    public function setLogo(?string $logo): Company
    {
        $this->logo = $logo;
        return $this;
    }


    public function system_activity_logs()
    {
        return $this->morphMany(SystemActivityLog::class, 'origin');
    }


    /**
     *
     * Obtener soap_type_id para registro de entorno en tablas relacionadas
     *
     * @return string
     */
    public static function getCompanySoapTypeId()
    {
        return Company::select('soap_type_id')->withOut(['identity_document_type'])->firstOrFail()->soap_type_id;
    }


    /**
     *
     * Obtener campos para cabecera de reportes
     *
     * @return string
     */
    public static function getDataForReportHeader()
    {
        return self::select(['number', 'name'])->withOut(['identity_document_type'])->firstOrFail();
    }


    /**
     *
     * Obtener campo individual
     *
     * @param  Builder $query
     * @param  string $column
     * @return Builder
     */
    public function scopeGetRecordIndividualColumn($query, $column)
    {
        return $query->select($column)->firstOrFail()->{$column};
    }


    /**
     *
     * Obtener logo de la app
     *
     * @param  Builder $query
     * @return string
     */
    public static function getAppUrlLogo()
    {
        $app_logo = self::select('app_logo')->firstOrFail()->app_logo;

        if ($app_logo) {
            $app_logo = asset('storage/uploads/logos/' . $app_logo);
        }

        return $app_logo;
    }


    /**
     *
     * Filtrar datos para whatsapp api
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeSelectDataWhatsAppApi($query)
    {
        return $query->select('ws_api_token', 'ws_api_phone_number_id');
    }

    /**
     * Obtener unicamente los noombre del archivos de Qz Tray
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeSelectCertificateQzTray($query)
    {
        return $query->select('digital_certificate_qztray', 'private_certificate_qztray')->withOut(['identity_document_type']);
    }

    public function scopeGetTypeSoap($query)
    {
        return $query->select('soap_type_id')->first();
    }
    /**
     *
     * Descripción  del tipo de transaccion asociado al modelo
     *
     * @param  string $column
     * @return string
     */
    // public function getDescriptionColumnForSystemActivity($column)
    // {
    //     $key = "validation.attributes.{$column}";
    //     $trans = __($key);
    //     $description = ($trans == $key) ? $column : $trans;

    //     return 'Actualización del campo '.$description.' en configuración de empresa';
    // }


    /**
     *
     * Descripción de los tipos de transacción para cada actividad
     *
     * @return array
     */
    // public function getTransactionTypesForSystemActivity()
    // {
    //     $data = [];

    //     foreach ($this->getCheckColumnsForSystemActivity() as $column)
    //     {
    //         $data [$this->getTransactionTypeForSystemActivity($column)] = $this->getDescriptionColumnForSystemActivity($column);
    //     }

    //     return $data;
    // }


    /**
     *
     * Columnas a verificar para registro de actividad
     *
     * @return array
     */
    public function getCheckColumnsForSystemActivity()
    {
        return ['number', 'name', 'soap_send_id', 'soap_type_id', 'soap_username', 'soap_password', 'soap_url', 'certificate'];
    }


    /**
     *
     * @param  string $column
     * @return string
     */
    public function getTransactionTypeForSystemActivity($column)
    {
        return "{$this->getTable()}_{$column}";
    }

    public function soap_type()
    {
        return $this->belongsTo(SoapType::class);
    }

    public function scopeGetInformationCompany($query)
    {
        return $query->select('number', 'name')->first();
    }
}
