<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    use SoftDeletes;

    protected $table = 'leads';

    protected $fillable = [
        'lead_id',
        'base_lead_id',
        'site_sequence',
        'sites_count',
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

    protected $appends = ['display_id'];

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
    public function activities()
    {
        return $this->hasMany(LeadActivity::class)->latest();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LeadLog::class)->latest();
    }

    public function pricings(): HasMany
    {
        return $this->hasMany(LeadPricing::class)->latest();
    }

    /**
     * The most recently created pricing record is the current one -
     * new pricing is always added as a new row rather than
     * overwriting the last, so this simply reflects that ordering
     * instead of needing a separate "is_current" flag.
     */
    public function currentPricing(): HasOne
    {
        return $this->hasOne(LeadPricing::class)->latestOfMany();
    }

    /**
     * Other leads created in the same multisite batch (same
     * base_lead_id), including this one. There is no separate
     * "parent" lead row for the base ID - the site leads themselves
     * carry the relationship.
     */
    public function siblingSites(): HasMany
    {
        return $this->hasMany(Lead::class, 'base_lead_id', 'base_lead_id');
    }

    public function isMultisite(): bool
    {
        return !is_null($this->base_lead_id);
    }

    /**
     * Pricing is only applicable to the AU Savers product - see
     * Product::AU_SAVERS_ID.
     */
    public function isAuSavers(): bool
    {
        return (int) $this->product_id === Product::AU_SAVERS_ID;
    }

    /**
     * A "Multiple Site" lead saved as a draft, not yet expanded into
     * its batch of site leads - that only happens once it's
     * published (see LeadController::expandMultisiteBatch()).
     */
    public function isPendingMultisite(): bool
    {
        return $this->number_of_sites === 'Multiple Site'
            && is_null($this->base_lead_id);
    }

    /**
     * The business-facing Lead ID where one exists, falling back to
     * the internal id for legacy leads created before lead_id existed.
     */
    public function getDisplayIdAttribute(): string
    {
        return $this->lead_id ?? (string) $this->id;
    }

    /**
     * Route-model binding by the business-facing lead_id first
     * (e.g. "1500-3"), falling back to the internal id for the
     * handful of legacy leads created before lead_id existed.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('lead_id', $value)->first()
            ?? $this->where('id', $value)->first();
    }

    /**
     * The other half of route-model binding: what route() /
     * url() generate into "/leads/{lead}" when a Lead instance
     * (rather than a raw id) is passed in - e.g. route('leads.edit',
     * $lead). Kept in sync with resolveRouteBinding() above via
     * display_id, so a generated URL always resolves back to the
     * same lead.
     */
    public function getRouteKey()
    {
        return $this->display_id;
    }
}
