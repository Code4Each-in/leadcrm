<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use SoftDeletes;

    protected $table = 'leads';

    protected $fillable = [
        'product_id',
        'company_type',
        'company_business_name',
        'company_number',
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
        'loan_purpose',
        'funds_usage_details',
        'supply_address',
        'postcode',
        'number_of_sites',
        'mpan',
        'mprn',
        'spid',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'business_start_date' => 'date',
        'date_of_birth' => 'date',
        'same_as_registered_address' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function reminders(): HasMany
    {
        return $this->hasMany(LeadReminder::class);
    }
}
