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
        'assigned_to',
        'assigned_by',
        'account_executive_id',
        'ae_assigned_at',
        'account_manager_id',
        'am_assigned_at',
        'process_started_at',
        'process_started_by',
        'closed_at',
        'closed_by',
        'hold_at',
        'hold_by',
        'lost_at',
        'lost_by',
    ];

    protected $casts = [
        'ae_assigned_at' => 'datetime',
        'am_assigned_at' => 'datetime',
        'process_started_at' => 'datetime',
        'closed_at' => 'datetime',
        'hold_at' => 'datetime',
        'lost_at' => 'datetime',
        'business_start_date' => 'date',
        'date_of_birth' => 'date',
        'same_as_registered_address' => 'boolean',
    ];

    protected $appends = ['display_id', 'status_label'];

    /**
     * Stored status values. STATUS_PUBLISHED is what the UI calls
     * "Open" - the stored value is deliberately left as "published"
     * (only the label changes, see statusLabel()), and STATUS_ASSIGNED
     * is set when MIS/Admin assigns an open lead to an Account
     * Executive.
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ASSIGNED = 'assigned';

    // MIS -> AE -> Account Manager workflow (see LeadWorkflowService).
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_WITH_ACCOUNT_MANAGER = 'with_account_manager';
    public const STATUS_SENT_BACK = 'sent_back';
    public const STATUS_HOLD = 'hold';
    public const STATUS_LOST = 'lost';
    public const STATUS_CLOSED = 'closed';

    public const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PUBLISHED => 'Open',
        self::STATUS_ASSIGNED => 'Assigned',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_WITH_ACCOUNT_MANAGER => 'With Account Manager',
        self::STATUS_SENT_BACK => 'Sent Back to AE',
        self::STATUS_HOLD => 'Hold',
        self::STATUS_LOST => 'Lost',
        self::STATUS_CLOSED => 'Closed',
    ];

    /**
     * Statuses in which an AE holds the lead / an Account Manager
     * holds it. (Closed leads have no owner.)
     */
    public const AE_STAGE_STATUSES = [
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_SENT_BACK,
    ];

    /**
     * Every status a lead can be in after Open, i.e. anything set by
     * the assignment workflow rather than by publishing.
     */
    public const WORKFLOW_STATUSES = [
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_WITH_ACCOUNT_MANAGER,
        self::STATUS_SENT_BACK,
        self::STATUS_HOLD,
        self::STATUS_LOST,
        self::STATUS_CLOSED,
    ];

    /**
     * Statuses that end a lead's journey - nothing more can be done.
     */
    public const FINISHED_STATUSES = [
        self::STATUS_LOST,
        self::STATUS_CLOSED,
    ];

    /**
     * Workflow statuses in which the lead is still live (not Lost or
     * Closed). A lead on Hold is paused, not finished.
     */
    public const ACTIVE_WORKFLOW_STATUSES = [
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_WITH_ACCOUNT_MANAGER,
        self::STATUS_SENT_BACK,
        self::STATUS_HOLD,
    ];

    /**
     * Display label for a stored status value ("published" -> "Open").
     */
    public static function statusLabel(?string $status): string
    {
        return self::STATUS_LABELS[$status] ?? ucfirst((string) $status);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel($this->status);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * True for both Open (published) and Assigned - i.e. anything
     * that has been through the one-way Draft -> Publish handoff.
     */
    public function isPublishedOrBeyond(): bool
    {
        return $this->status !== self::STATUS_DRAFT;
    }

    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    /**
     * True once the assignment workflow has taken over the status
     * (Assigned, In Progress, With Account Manager, Sent Back,
     * Closed). Publishing/editing must never move such a lead back
     * to Open - only the workflow changes its status.
     */
    public function isInWorkflow(): bool
    {
        return in_array($this->status, self::WORKFLOW_STATUSES, true);
    }

    public function isWithAe(): bool
    {
        return in_array($this->status, self::AE_STAGE_STATUSES, true);
    }

    /**
     * The Account Manager holds the lead - reviewing it, or having
     * put it on Hold.
     */
    public function isWithAccountManager(): bool
    {
        return in_array($this->status, [self::STATUS_WITH_ACCOUNT_MANAGER, self::STATUS_HOLD], true);
    }

    public function isLost(): bool
    {
        return $this->status === self::STATUS_LOST;
    }

    public function isOnHold(): bool
    {
        return $this->status === self::STATUS_HOLD;
    }

    /**
     * Lost or Closed - the lead's journey is over.
     */
    public function isFinished(): bool
    {
        return in_array($this->status, self::FINISHED_STATUSES, true);
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * Whether $user is (or once was) the lead's AE / Account Manager,
     * i.e. part of the team that has worked on it.
     */
    public function isTeamMember(User $user): bool
    {
        return in_array($user->id, array_filter([
            $this->assigned_to,
            $this->account_executive_id,
            $this->account_manager_id,
        ]), true);
    }

    /**
     * The Account Executive this lead is currently assigned to.
     * (Named assignee, not assignedTo, so its JSON key doesn't
     * collide with the assigned_to id attribute.)
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * The MIS / Admin user who first assigned the lead.
     */
    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by')->withTrashed();
    }

    public function accountExecutive()
    {
        return $this->belongsTo(User::class, 'account_executive_id')->withTrashed();
    }

    public function accountManager()
    {
        return $this->belongsTo(User::class, 'account_manager_id')->withTrashed();
    }

    public function processStarter()
    {
        return $this->belongsTo(User::class, 'process_started_by')->withTrashed();
    }

    public function holder()
    {
        return $this->belongsTo(User::class, 'hold_by')->withTrashed();
    }

    public function lostBy()
    {
        return $this->belongsTo(User::class, 'lost_by')->withTrashed();
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by')->withTrashed();
    }

    /**
     * Full assignment / movement history, oldest first (it reads as
     * a timeline).
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(LeadAssignment::class)->orderBy('created_at')->orderBy('id');
    }

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
