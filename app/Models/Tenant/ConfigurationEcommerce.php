<?php

namespace App\Models\Tenant;

class ConfigurationEcommerce extends ModelTenant
{
    protected $table = "configuration_ecommerce";

    protected $fillable = [
        'information_contact_name',
        'information_contact_email',
        'information_contact_phone',
        'information_contact_address',
        'script_paypal',
        'token_private_culqui',
        'token_public_culqui',
        'link_youtube',
        'link_twitter',
        'link_facebook',
        'link_tiktok',
        'link_instagram',
        'tag_shipping',
        'tag_dollar',
        'tag_support',
        'phone_whatsapp',
        'title_one_customised_link',
        'title_two_customised_link',
        'title_three_customised_link',
        'customised_link_one',
        'customised_link_two',
        'customised_link_three',
        'color_ecommerce',
        'preferences',
        'terms_conditions',
        'privacy_policy',
        'about_us',
        'delivery_no_coverage_message',
        'enable_electronic_documents',
        'enable_store_pickup',
        'enable_yape',
        'enable_transfer',
        'publicidad_activa',
        'publicidad_texto',
        'publicidad_color_fondo',
        'publicidad_link'
    ];

    protected $casts = [
        'preferences'                  => 'array',
        'enable_electronic_documents'  => 'boolean',
        'enable_store_pickup'          => 'boolean',
        'enable_yape'                  => 'boolean',
        'enable_transfer'              => 'boolean',
    ];
    /**
     * Devuelve los enlaces personalizados para el header
     */
    public static function getCustomLinks()
    {
        $config = self::first();
        return [
            'title_one' => $config->title_one_customised_link ?? null,
            'link_one' => $config->customised_link_one ?? null,
            'title_two' => $config->title_two_customised_link ?? null,
            'link_two' => $config->customised_link_two ?? null,
            'title_three' => $config->title_three_customised_link ?? null,
            'link_three' => $config->customised_link_three ?? null,
        ];
    }

}
