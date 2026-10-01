<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    use SoftDeletes;

    protected $table = 'leads';

    protected $fillable = [
        'lead_id',
        'lead_date',
        'base_lead_id',
        'site_sequence',
        'sites_count',
        'pending_sites',
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
        'intended_account_manager_id',
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
        'lead_date' => 'date',
        'pending_sites' => 'array',
        'date_of_birth' => 'date',
        'same_as_registered_address' => 'boolean',
    ];

    protected $appends = ['display_id', 'status_label'];

    // Per-site CSV data for a pending Multiple Site draft - internal
    // until the batch is expanded, never needed by the listing JSON.
    protected $hidden = ['pending_sites'];

    /**
     * Stored status values. STATUS_PUBLISHED is what the UI calls
     * "Open" - the stored value is deliberately left as "published"
     * (only the label changes, see statusLabel()). STATUS_ASSIGNED
     * was set when MIS/Admin assigned an open lead to an Account
     * Executive under the old MIS -> AE -> Account Manager workflow;
     * it is kept only for leads that were assigned that way.
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ASSIGNED = 'assigned';

    // Old MIS -> AE -> Account Manager workflow - no longer set, kept
    // so leads still in these stages display and can be reassigned.
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_SENT_BACK = 'sent_back';

    // MIS -> Account Manager workflow (see LeadWorkflowService). A
    // declined pricing doesn't change the lead's status - the lead
    // stays with its Account Manager; see pricingStage().
    public const STATUS_WITH_ACCOUNT_MANAGER = 'with_account_manager';
    public const STATUS_HOLD = 'hold';
    public const STATUS_LOST = 'lost';
    public const STATUS_CLOSED = 'closed';

    /**
     * Lead Staging (AU Savers only) - the stages of the pricing /
     * customer journey. They are ordinary values of the one status
     * column, not a separate field: an AU Savers lead goes straight
     * from Draft to STATUS_PRICING_REQUEST_RECEIVED when published
     * (see LeadObserver::saving()) instead of Open, and once assigned
     * a staged lead is "with the Account Manager" (see
     * isWithAccountManager()). STATUS_SENT_BACK is the old workflow's
     * stored value, reused - the lead stays where it is while the AE
     * (its creator) supplies the missing information through Notes &
     * Documents. STATUS_REFRESH_QUOTES_REQUESTED is the "revision"
     * stage: the Account Manager declining the pricing sets it.
     */
    public const STATUS_PRICING_REQUEST_RECEIVED = 'pricing_request_received';
    public const STATUS_PRICING_IN_PROGRESS = 'pricing_in_progress';
    public const STATUS_QUOTES_SENT_TO_CUSTOMER = 'quotes_sent_to_customer';
    public const STATUS_REFRESH_QUOTES_REQUESTED = 'refresh_quotes_requested';

    public const STATUS_NO_QUOTES_AVAILABLE = 'no_quotes_available';
    public const STATUS_DECLINED_BY_SUPPLIER = 'declined_by_supplier';
    public const STATUS_SUPPLIER_NOT_AVAILABLE = 'supplier_not_available';
    public const STATUS_METER_PROFILE_SUPPLIER_NA = 'meter_profile_supplier_na';
    public const STATUS_METER_PROFILE_ISSUE = 'meter_profile_issue';
    public const STATUS_METER_INFO_INCORRECT = 'meter_info_incorrect';
    public const STATUS_OUT_OF_SUPPLIER_CRITERIA = 'out_of_supplier_criteria';

    public const STATUS_TENDER_SUBMITTED = 'tender_submitted';
    public const STATUS_TENDER_AWAITING_QUOTES = 'tender_awaiting_quotes';
    public const STATUS_TENDER_QUOTES_RECEIVED = 'tender_quotes_received';
    public const STATUS_TENDER_QUOTES_SENT = 'tender_quotes_sent';
    public const STATUS_TENDER_REFRESH_REQUESTED = 'tender_refresh_requested';

    public const STATUS_LOA_ISSUED = 'loa_issued';
    public const STATUS_LOA_SIGNED = 'loa_signed';
    public const STATUS_LOA_SUBMITTED = 'loa_submitted';
    public const STATUS_LOA_REJECTED = 'loa_rejected';

    public const STATUS_CONTRACTS_ISSUED = 'contracts_issued';
    public const STATUS_CONTRACTS_SIGNED = 'contracts_signed';
    public const STATUS_CONTRACTS_SUBMITTED = 'contracts_submitted';
    public const STATUS_CONTRACT_REJECTED = 'contract_rejected';
    // A stage like any other - it does not close the lead.
    public const STATUS_CONTRACT_LIVE = 'contract_live';

    /**
     * The Lead Staging dropdown: optgroup label => [stored value =>
     * label]. The single source for the stage options, their labels
     * and their validation.
     */
    public const STAGE_GROUPS = [
        'Pricing' => [
            self::STATUS_PRICING_REQUEST_RECEIVED => 'Pricing Request Received',
            self::STATUS_SENT_BACK => 'Sent back to AE',
            self::STATUS_PRICING_IN_PROGRESS => 'Pricing in Progress',
            self::STATUS_QUOTES_SENT_TO_CUSTOMER => 'Quotes Sent to the Customer',
            self::STATUS_REFRESH_QUOTES_REQUESTED => 'Refresh Quotes Requested',
        ],
        'Supplier / Pricing Issues' => [
            self::STATUS_NO_QUOTES_AVAILABLE => 'No Quotes Available',
            self::STATUS_DECLINED_BY_SUPPLIER => 'Declined by the Supplier',
            self::STATUS_SUPPLIER_NOT_AVAILABLE => 'Supplier Not Available',
            self::STATUS_METER_PROFILE_SUPPLIER_NA => 'Meter Profile/Supplier Not Available',
            self::STATUS_METER_PROFILE_ISSUE => 'Issue with Meter Profile',
            self::STATUS_METER_INFO_INCORRECT => 'Meter Information - Incorrect/Incomplete',
            self::STATUS_OUT_OF_SUPPLIER_CRITERIA => "Out of Supplier's Criteria",
        ],
        'Tender' => [
            self::STATUS_TENDER_SUBMITTED => 'Tender - Submitted',
            self::STATUS_TENDER_AWAITING_QUOTES => 'Tender - Awaiting Quotes',
            self::STATUS_TENDER_QUOTES_RECEIVED => 'Tender - Quotes Received',
            self::STATUS_TENDER_QUOTES_SENT => 'Tender - Quotes Sent to the Customer',
            self::STATUS_TENDER_REFRESH_REQUESTED => 'Tender - Refresh Quotes Requested',
        ],
        'LOA' => [
            self::STATUS_LOA_ISSUED => 'LOA Issued',
            self::STATUS_LOA_SIGNED => 'LOA Signed',
            self::STATUS_LOA_SUBMITTED => 'LOA Submitted',
            self::STATUS_LOA_REJECTED => 'LOA Rejected',
        ],
        'Contracts' => [
            self::STATUS_CONTRACTS_ISSUED => 'Contracts Issued',
            self::STATUS_CONTRACTS_SIGNED => 'Contracts Signed',
            self::STATUS_CONTRACTS_SUBMITTED => 'Contracts Submitted',
            self::STATUS_CONTRACT_REJECTED => 'Contract Rejected',
            self::STATUS_CONTRACT_LIVE => 'Contract Live',
        ],
    ];

    /**
     * Every stage value (flattened STAGE_GROUPS).
     */
    public const STAGE_STATUSES = [
        self::STATUS_PRICING_REQUEST_RECEIVED,
        self::STATUS_SENT_BACK,
        self::STATUS_PRICING_IN_PROGRESS,
        self::STATUS_QUOTES_SENT_TO_CUSTOMER,
        self::STATUS_REFRESH_QUOTES_REQUESTED,
        self::STATUS_NO_QUOTES_AVAILABLE,
        self::STATUS_DECLINED_BY_SUPPLIER,
        self::STATUS_SUPPLIER_NOT_AVAILABLE,
        self::STATUS_METER_PROFILE_SUPPLIER_NA,
        self::STATUS_METER_PROFILE_ISSUE,
        self::STATUS_METER_INFO_INCORRECT,
        self::STATUS_OUT_OF_SUPPLIER_CRITERIA,
        self::STATUS_TENDER_SUBMITTED,
        self::STATUS_TENDER_AWAITING_QUOTES,
        self::STATUS_TENDER_QUOTES_RECEIVED,
        self::STATUS_TENDER_QUOTES_SENT,
        self::STATUS_TENDER_REFRESH_REQUESTED,
        self::STATUS_LOA_ISSUED,
        self::STATUS_LOA_SIGNED,
        self::STATUS_LOA_SUBMITTED,
        self::STATUS_LOA_REJECTED,
        self::STATUS_CONTRACTS_ISSUED,
        self::STATUS_CONTRACTS_SIGNED,
        self::STATUS_CONTRACTS_SUBMITTED,
        self::STATUS_CONTRACT_REJECTED,
        self::STATUS_CONTRACT_LIVE,
    ];

    public const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PUBLISHED => 'Open',
        self::STATUS_ASSIGNED => 'Assigned',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_WITH_ACCOUNT_MANAGER => 'With Account Manager',
        self::STATUS_HOLD => 'Hold',
        self::STATUS_LOST => 'Lost',
        self::STATUS_CLOSED => 'Closed',
    ]
        + self::STAGE_GROUPS['Pricing']
        + self::STAGE_GROUPS['Supplier / Pricing Issues']
        + self::STAGE_GROUPS['Tender']
        + self::STAGE_GROUPS['LOA']
        + self::STAGE_GROUPS['Contracts'];

    /**
     * Statuses in which an AE holds the lead (old workflow only).
     * STATUS_SENT_BACK is no longer one of them - see STAGE_GROUPS.
     */
    public const AE_STAGE_STATUSES = [
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
    ];

    /**
     * Every status a lead can be in after Open, i.e. anything set by
     * the assignment workflow rather than by publishing.
     */
    public const WORKFLOW_STATUSES = [
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_WITH_ACCOUNT_MANAGER,
        self::STATUS_HOLD,
        self::STATUS_LOST,
        self::STATUS_CLOSED,
        ...self::STAGE_STATUSES,
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
        self::STATUS_HOLD,
        ...self::STAGE_STATUSES,
    ];

    /**
     * The Account Manager is working the lead (when it is assigned -
     * see isWithAccountManager()): just assigned with no stage, or at
     * any Lead Staging stage. Hold is not included.
     */
    public const ACCOUNT_MANAGER_ACTIVE_STATUSES = [
        self::STATUS_WITH_ACCOUNT_MANAGER,
        ...self::STAGE_STATUSES,
    ];

    /**
     * Where a lead's current pricing is in the MIS -> Account Manager
     * review (see pricingStage()). Worked out from the lead status and
     * the current pricing record's status - not stored separately.
     */
    public const PRICING_STAGE_NONE = 'none';
    public const PRICING_STAGE_DRAFT = 'draft';
    public const PRICING_STAGE_READY = 'ready';
    public const PRICING_STAGE_AWAITING_APPROVAL = 'awaiting_approval';
    public const PRICING_STAGE_APPROVED = 'approved';
    public const PRICING_STAGE_DECLINED = 'declined';

    public const PRICING_STAGE_LABELS = [
        self::PRICING_STAGE_NONE => 'No Pricing Yet',
        self::PRICING_STAGE_DRAFT => 'Pricing in Draft',
        self::PRICING_STAGE_READY => 'Pricing Published',
        self::PRICING_STAGE_AWAITING_APPROVAL => 'Awaiting Account Manager Approval',
        self::PRICING_STAGE_APPROVED => 'Pricing Approved',
        self::PRICING_STAGE_DECLINED => 'Declined - Awaiting MIS Re-pricing',
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
     * The Account Manager holds the lead - reviewing it, at any Lead
     * Staging stage, or having put it on Hold. A staged lead nobody
     * has been assigned yet is not with anyone.
     */
    public function isWithAccountManager(): bool
    {
        if (in_array($this->status, [self::STATUS_WITH_ACCOUNT_MANAGER, self::STATUS_HOLD], true)) {
            return true;
        }

        return $this->isStaged() && $this->assigned_to !== null;
    }

    public function isStaged(): bool
    {
        return in_array($this->status, self::STAGE_STATUSES, true);
    }

    public function isSentBackToAe(): bool
    {
        return $this->status === self::STATUS_SENT_BACK;
    }

    /**
     * Whether the Lead Staging section applies to this lead: a
     * product with a Pricing section (AU Savers - see
     * requiresPricing()), whether or not it is assigned.
     */
    public function hasStaging(): bool
    {
        return $this->requiresPricing();
    }

    /**
     * Whether a stage can be picked right now: published, and not on
     * Hold (picking a stage would overwrite it), Lost or Closed.
     */
    public function canChangeStage(): bool
    {
        return $this->hasStaging()
            && !$this->isDraft()
            && !$this->isOnHold()
            && !$this->isFinished();
    }

    /**
     * The lead's AE - there is no separate AE assignment: it is the
     * user who created the lead, when they have the AE role. Null
     * otherwise (no "Sent back to AE" possible).
     */
    public function aeCreator(): ?User
    {
        $creator = $this->relationLoaded('creator') ? $this->creator : $this->creator()->first();

        return $creator && $creator->isAe() ? $creator : null;
    }

    public static function stageLabel(?string $stage): ?string
    {
        return in_array($stage, self::STAGE_STATUSES, true) ? self::STATUS_LABELS[$stage] : null;
    }

    /**
     * Products with a Pricing section (AU Savers) need published
     * pricing before the lead can go to an Account Manager, and the
     * Account Manager approves / declines it. Every other product is
     * assigned without any pricing step.
     */
    public function requiresPricing(): bool
    {
        return $this->isAuSavers();
    }

    /**
     * One of the PRICING_STAGE_* values, or null for a product with
     * no pricing.
     */
    public function pricingStage(): ?string
    {
        if (!$this->requiresPricing()) {
            return null;
        }

        $pricing = $this->currentPricing;

        return match (true) {
            !$pricing => self::PRICING_STAGE_NONE,
            $pricing->isDraft() => self::PRICING_STAGE_DRAFT,
            $pricing->isApproved() => self::PRICING_STAGE_APPROVED,
            $pricing->isDeclined() => self::PRICING_STAGE_DECLINED,
            $this->isWithAccountManager() => self::PRICING_STAGE_AWAITING_APPROVAL,
            default => self::PRICING_STAGE_READY,
        };
    }

    /**
     * The listing's "Open" stat card: published and not yet with
     * anyone - Open itself, or an AU Savers lead at a stage that
     * hasn't been assigned.
     */
    public function scopeOpenUnassigned($query)
    {
        return $query->where(function ($q) {
            $q->where('status', self::STATUS_PUBLISHED)
                ->orWhere(fn ($q) => $q->whereIn('status', self::STAGE_STATUSES)->whereNull('assigned_to'));
        });
    }

    /**
     * The listing's "Assigned" stat card: live and with someone (an
     * Account Manager, or an AE on an old-workflow lead).
     */
    public function scopeAssignedActive($query)
    {
        return $query->whereIn('status', self::ACTIVE_WORKFLOW_STATUSES)->whereNotNull('assigned_to');
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
     * Whether $user holds the lead now, or is (or was) its Account
     * Manager. The AE of an old-workflow lead deliberately no longer
     * counts - Account Executives aren't part of the workflow.
     */
    public function isTeamMember(User $user): bool
    {
        return in_array($user->id, array_map('intval', array_filter([
            $this->assigned_to,
            $this->account_manager_id,
        ])), true);
    }

    /**
     * Everyone linked to the lead through its own columns - creator,
     * current owner, the MIS / Admin user who assigned it, its Account
     * Manager and (old-workflow leads) its AE - active users only, each
     * once. Who hears about a Lead Staging change.
     */
    public function linkedUsers(): Collection
    {
        return collect([
            $this->creator,
            $this->assignee,
            $this->assigner,
            $this->accountManager,
            $this->accountExecutive,
        ])
            ->filter(fn (?User $user) => $user && !$user->trashed() && (int) $user->status === 1)
            ->unique('id')
            ->values();
    }

    /**
     * Whoever holds the lead right now - the Account Manager it is
     * assigned to (or the AE, on an old-workflow lead).
     * (Named assignee, not assignedTo, so its JSON key doesn't
     * collide with the assigned_to id attribute.)
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * The MIS / Admin user who last assigned the lead to an Account
     * Manager - who hears about the pricing decision.
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

    /**
     * The Account Manager named on import ("User Name" column), still
     * waiting for the lead to become assignable - see
     * LeadWorkflowService::assignIntendedAccountManager().
     */
    public function intendedAccountManager()
    {
        return $this->belongsTo(User::class, 'intended_account_manager_id')->withTrashed();
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

    /**
     * Contract documents (the Contract section under Pricing), newest
     * first - see LeadContractController.
     */
    public function contractDocuments(): HasMany
    {
        return $this->hasMany(LeadDocument::class)->latest()->latest('id');
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
     * Whether a pending Multiple Site draft holds per-site data (from
     * its sites CSV) for exactly as many sites as it will expand into
     * - the precondition for publishing it.
     */
    public function hasValidPendingSites(): bool
    {
        return is_array($this->pending_sites)
            && (int) $this->sites_count > 0
            && count($this->pending_sites) === (int) $this->sites_count;
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
