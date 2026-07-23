<?php

namespace App\Models\System;
use Hyn\Tenancy\Traits\UsesSystemConnection;

use Illuminate\Database\Eloquent\Model;

/**
 * @property bool test_days_enabled
 */
class Plan extends Model
{
    use UsesSystemConnection;


    protected $fillable = [
        'name',
        'pricing',
        'is_popular',
        'limit_users',
        'limit_documents',
        'plan_documents',
        'locked',
        'establishments_limit',
        'establishments_unlimited',
        'test_days',
        'test_days_enabled',
        'sales_limit',
        'sales_unlimited',
        'include_sale_notes_sales_limit',
        'include_sale_notes_limit_documents',
        'module_permissions',
        'whatsapp_messages_limit',
        'whatsapp_messages_unlimited',
    ];


    protected $casts = [
        'is_popular' => 'boolean',
        'establishments_unlimited' => 'boolean',
        'establishments_limit' => 'int',
        'sales_unlimited' => 'boolean',
        'sales_limit' => 'float',
        'include_sale_notes_sales_limit' => 'boolean',
        'include_sale_notes_limit_documents' => 'boolean',
        'module_permissions' => 'array',
        'test_days' => 'int',
        'test_days_enabled' => 'boolean',
        'whatsapp_messages_limit' => 'int',
        'whatsapp_messages_unlimited' => 'boolean',
    ];


    public function setPlanDocumentsAttribute($value)
    {
        $this->attributes['plan_documents'] = (is_null($value))?null:json_encode($value);
    }

    public function getModulePermissionsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function getPlanDocumentsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }


    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    
    /**
     *
     * @return bool
     */
    public function isEstablishmentsUnlimited()
    {
        return $this->establishments_unlimited;
    }

    
    /**
     *
     * @return bool
     */
    public function isSalesUnlimited()
    {
        return $this->sales_unlimited;
    }
    

    /**
     *
     * @return bool
     */
    public function includeSaleNotesSalesLimit()
    {
        return $this->include_sale_notes_sales_limit;
    }
    

    /**
     *
     * @return bool
     */
    public function includeSaleNotesLimitDocuments()
    {
        return $this->include_sale_notes_limit_documents;
    }

    
    /**
     *
     * @return bool
     */
    public function isUnlimitedUsers()
    {
        return $this->limit_users === 0;
    }


    /**
     *
     * @return bool
     */
    public function isWhatsappMessagesUnlimited()
    {
        return $this->whatsapp_messages_unlimited;
    }

}
