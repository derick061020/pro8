<?php

namespace Modules\Account\Models;

use App\Models\Tenant\BankAccount;
use App\Models\Tenant\ModelTenant;

/**
 * @property BankAccount $bank_account_pen
 * @property BankAccount $bank_account_usd
 */
class EjbReportConfiguration extends ModelTenant
{
    protected $table = 'ejb_report_configurations';

    protected $fillable = [
        'document_type_id',
        'bank_account_pen_id',
        'bank_account_usd_id',
    ];

    public function bank_account_pen() 
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function bank_account_usd()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
