<?php

namespace Modules\Payment\Models;

use App\Models\Tenant\ModelTenant;

class PaymentConfiguration extends ModelTenant
{

    protected $fillable = [
        'enabled_yape',
        'qrcode_yape',
        'name_yape',
        'telephone_yape',

        'enabled_mp',
        'access_token_mp',
        'public_key_mp',
        'enabled_culqi',
        'publickey_culqi',
        'privatekey_culqi',
        'idrsa_culqi',
        'rsa_culqi',
        'enabled_izipay',
        'username_izipay',
        'password_izipay',
        'publickey_izipay',
        'sha256key_izipay',
    ];

    protected $hidden = [
        'access_token_mp',
        'privatekey_culqi',
        'sha256key_izipay',
    ];


    protected $casts = [
        'enabled_yape' => 'bool',
        'enabled_mp' => 'bool',
        'enabled_culqi' => 'bool',
        'enabled_izipay' => 'bool',
    ];


    public function getImageUrlYapeAttribute()
    {
        return $this->qrcode_yape ? asset('storage'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'payment_configurations'.DIRECTORY_SEPARATOR.$this->qrcode_yape) : null;
    }

    public function getRowResource()
    {
        return [
            'id' => $this->id,
            'enabled_yape' => $this->enabled_yape,
            'qrcode_yape' => $this->qrcode_yape,
            'name_yape' => $this->name_yape,
            'telephone_yape' => $this->telephone_yape,
            'image_url_yape' => $this->image_url_yape,
            'enabled_mp' => $this->enabled_mp,
            'public_key_mp' => $this->public_key_mp,
            'enabled_culqi' => $this->enabled_culqi,
            'publickey_culqi' => $this->publickey_culqi,
            'privatekey_culqi' => $this->privatekey_culqi,
            'idrsa_culqi' => $this->idrsa_culqi,
            'rsa_culqi' => $this->rsa_culqi,
            'enabled_izipay' => $this->enabled_izipay,
            'username_izipay' => $this->username_izipay,
            'password_izipay' => $this->password_izipay,
            'publickey_izipay' => $this->publickey_izipay,
            'sha256key_izipay' => $this->sha256key_izipay
        ];
    }


    public static function getPublicRowResource()
    {

        $record = PaymentConfiguration::first();

        return [
            'name_yape' => $record->name_yape,
            'telephone_yape' => $record->telephone_yape,
            'image_url_yape' => $record->image_url_yape,
        ];

    }

    public static function getPaymentPermissions()
    {

        $record = PaymentConfiguration::firstOrFail();

        return [
            'enabled_yape' => $record->enabled_yape,
            'enabled_mp' => $record->enabled_mp,
            'qrcode_yape' => $record->enabled_yape ? $record->getImageUrlYapeAttribute() : '',
            'name_yape' => $record->enabled_yape ? $record->name_yape : '',
            'telephone_yape' => $record->enabled_yape ? $record->telephone_yape : '',
        ];

    }


    /**
     * Obtener llave publica de mercado pago
     *
     * @return string
     */
    public static function getPublicKeyMp()
    {
        return PaymentConfiguration::select('public_key_mp')->firstOrFail()->public_key_mp;
    }

    /**
     * Obtener llave publica de mercado pago
     *
     * @return string
     */
    public static function getAccessTokenMp()
    {
        return PaymentConfiguration::select('access_token_mp')->firstOrFail()->access_token_mp;
    }

    public function scopeAccessIzipay($query)
    {
        return $query->where('enabled_izipay', true)
            ->select('username_izipay', 'password_izipay', 'publickey_izipay', 'sha256key_izipay')->first()->toArray();
    }

    public function scopeEnabledCheckout($query)
    {
        $record = $query->where('enabled_izipay', true)->first();

        if ($record) {
            return 'izipay';
        }

        return 'culqi';
    }

    public function scopeGetPublicKey($query)
    {
        $record = $query->where('enabled_mp', true)
            ->orWhere('enabled_culqi', true)
            ->orWhere('enabled_izipay', true)
            ->first();

        if (!$record) return null;

        if ($record->enabled_mp) return $record->public_key_mp;
        if ($record->enabled_culqi) return $record->publickey_culqi;
        if ($record->enabled_izipay) return $record->publickey_izipay;

    }

}
