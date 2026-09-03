<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'product_id',

        'company_type',
        'company_business_name',
        'business_start_date',
        'business_type',
        'business_registered_address',
        'business_trading_address',
        'same_as_registered_address',
        'customer_name',
        'contact_person',
        'date_of_birth',
        'phone_no',
        'mobile_no',
        'email',

        'gross_sales',
        'funds_required',
        'funds_term_months',
        'home_owner',
        'vat_registered',

        'supply_address',
        'postcode',
        'number_of_sites',
        'mpan',
        'mprn',
        'spid',

        'status',
        'notes',
    ];

    protected $casts = [
        'business_start_date' => 'date',
        'date_of_birth' => 'date',
        'same_as_registered_address' => 'boolean',
        'gross_sales' => 'decimal:2',
        'funds_required' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
