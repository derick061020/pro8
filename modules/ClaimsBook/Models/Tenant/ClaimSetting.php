<?php

namespace Modules\ClaimsBook\Models\Tenant;

use App\Models\Tenant\ModelTenant;

class ClaimSetting extends ModelTenant
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setValue(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
