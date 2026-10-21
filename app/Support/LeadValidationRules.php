<?php

namespace App\Support;

use App\Models\Lead;
use App\Models\Product;
use App\Rules\AtLeastOneMeter;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * Single source of truth for Lead field validation - previously
 * duplicated between LeadController::store() and ::update(). Also
 * used by the CSV importer (LeadCsvController) so an imported row can
 * never pass validation that a manually-submitted row would fail.
 */
class LeadValidationRules
{
    public const MPAN_TAKEN_MESSAGE =
        'This MPAN is already used by another lead.';

    public const SITES_COUNT_MISMATCH_MESSAGE =
        'The number of MPANs must match the number of sites';

    public const DUPLICATE_MPAN_MESSAGE =
        'Each site must have a different MPAN.';

    public const SITES_CSV_REQUIRED_MESSAGE =
        'Please upload a sites CSV with one MPAN per site for a Multiple Site lead.';

    /** AU Savers "Fuel Type" options (multi-select). */
    public const FUEL_TYPES = ['Electricity', 'Gas', 'Water'];

    /** AU Savers meter fields - each has its own checkbox on the form. */
    public const METERS = ['mpan', 'mprn', 'spid'];

    public const METER_LABELS = ['mpan' => 'MPAN', 'mprn' => 'MPRN', 'spid' => 'SPID'];

    public const METER_REQUIRED_MESSAGE =
        'Please tick at least one of MPAN, MPRN or SPID and enter its value.';

    /**
     * Fields required to Publish (or submit an AU Savers lead to
     * pricing) - every field except Mobile No. and Date of Birth.
     * Drafts may still be saved incomplete. Mirrored by the forms'
     * client-side check (leads/partials/form-rules).
     */
    public const REQUIRED_ON_PUBLISH = [
        'common' => [
            'company_type',
            'company_business_name',
            'business_start_date',
            'business_type',
            'business_registered_address',
            'supply_address',
            'customer_name',
            'contact_person',
            'phone_no',
            'email',
            'notes',
        ],
        // NFS / AF4U (funds_usage_details only when Loan Purpose is Other)
        'funding' => [
            'gross_sales',
            'funds_required',
            'funds_term_months',
            'home_owner',
            'vat_registered',
            'loan_purpose',
        ],
        // AU Savers (plus one of MPAN / MPRN / SPID for a Single Site)
        'au_savers' => [
            'number_of_sites',
            'fuel_types',
        ],
    ];

    /** Field names as shown on the form - used in "... is required." */
    public const FIELD_LABELS = [
        'company_type' => 'Company Type',
        'company_business_name' => 'Company / Business Name',
        'business_start_date' => 'Business Start Date',
        'business_type' => 'Business Type',
        'business_registered_address' => 'Business Registered Address',
        'supply_address' => 'Supply Address',
        'customer_name' => 'Customer Name',
        'contact_person' => 'Contact Person',
        'phone_no' => 'Phone No.',
        'email' => 'Email Address',
        'notes' => 'Notes',
        'gross_sales' => 'Gross Sales',
        'funds_required' => 'Funds Required',
        'funds_term_months' => 'Term of Funds Required',
        'home_owner' => 'Home Owner',
        'vat_registered' => 'VAT Registered',
        'loan_purpose' => 'Loan Purpose',
        'funds_usage_details' => 'Additional Details About Funds Usage',
        'number_of_sites' => 'Number of Sites',
        'fuel_types' => 'Fuel Type',
        'mpan' => 'MPAN',
        'mprn' => 'MPRN',
        'spid' => 'SPID',
        'stage_reminder_date' => 'Reminder date',
        'stage_reminder_time' => 'Reminder time',
        'stage_reminder_note' => 'Reminder note',
    ];

    /**
     * @param Lead|null $lead The lead being updated, so the
     *   published-can't-revert-to-draft guard can be applied. Null
     *   for a brand new lead (manual create or CSV import).
     * @param bool $requireSitesCountIfMultiple store()/CSV import
     *   require sites_count when number_of_sites is "Multiple Site";
     *   update() does not (an already-published batch's site count
     *   is fixed).
     */
    public static function rules(?Lead $lead = null, bool $requireSitesCountIfMultiple = true, bool $allowTakenMpan = false): array
    {
        return [

            // Product is the ONLY required field
            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'status' => array_values(array_filter([
                'required',
                'in:draft,published',
                $lead ? self::statusCannotRevertFromPublished($lead) : null,
            ])),

            'company_type' => [
                'nullable',
                'string',
                'max:255',
                'in:Limited,Sole Trader,Partnership,Limited Liability Partnership',
            ],

            'company_business_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'company_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'business_start_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'business_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'business_registered_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'business_trading_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'same_as_registered_address' => [
                'nullable',
                'boolean',
            ],

            'customer_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:255',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'phone_no' => [
                'nullable',
                'regex:/^[0-9]{10}$/',
            ],

            'mobile_no' => [
                'nullable',
                'regex:/^[0-9]{10}$/',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            // NFS / AF4U fields - optional but validated if entered
            'gross_sales' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'funds_required' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'funds_term_months' => [
                'nullable',
                'in:12,24,36,48,60,72',
            ],

            'home_owner' => [
                'nullable',
                'in:Yes,No',
            ],

            'vat_registered' => [
                'nullable',
                'in:Yes,No',
            ],

            'loan_purpose' => [
                'nullable',
                'string',
                Rule::in([
                    'Fund vehicle, equipment or machinery',
                    'Expansion / growth',
                    'Refinancing a loan',
                    'Tax payment',
                    'Working capital',
                    'Other',
                ]),
            ],

            'funds_usage_details' => [
                'nullable',
                'string',
                'max:2000',
            ],

            // AU Savers fields - optional but validated if entered
            'supply_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'number_of_sites' => [
                'nullable',
                'in:Single Site,Multiple Site',
            ],

            'fuel_types' => [
                'nullable',
                'array',
            ],

            'fuel_types.*' => [
                'string',
                Rule::in(self::FUEL_TYPES),
            ],

            // Only meaningful (and required) when number_of_sites is
            // "Multiple Site" - the count of individual site leads to
            // create, each getting its own "{base}-{n}" Lead ID.
            'sites_count' => array_values(array_filter([
                'nullable',
                $requireSitesCountIfMultiple ? 'required_if:number_of_sites,Multiple Site' : null,
                'integer',
                'min:1',
                'max:500',
            ])),

            // Exactly 13 digits - digits:13 already implies numeric-only.
            // Must not already be used by another lead - see
            // MpanRegistry for the scope, and for why an edit that
            // leaves the MPAN unchanged is never blocked - unless the
            // user confirmed the duplicate ($allowTakenMpan).
            'mpan' => [
                'nullable',
                'digits:13',
                MpanRegistry::rule($lead, $allowTakenMpan),
            ],

            // 6-10 digits - digits_between:x,y already implies numeric-only.
            'mprn' => [
                'nullable',
                'digits_between:6,10',
            ],

            // 8-10 digits - digits_between:x,y already implies numeric-only.
            'spid' => [
                'nullable',
                'digits_between:8,10',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            // Only supplied by the CSV import ("Lead Date" column) -
            // kept separate from created_at.
            'lead_date' => [
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],

        ];
    }

    public static function messages(): array
    {
        return [

            'product_id.required' =>
                'Please select a product.',

            'product_id.exists' =>
                'Please select a valid product.',

            'company_type.in' =>
                'Please select a valid company type.',

            'business_start_date.before_or_equal' =>
                'Business start date cannot be in the future.',

            'date_of_birth.before_or_equal' =>
                'Date of birth cannot be in the future.',

            'email.email' =>
                'Please enter a valid email address.',

            'phone_no.regex' =>
                'Phone number must contain exactly 10 digits.',

            'mobile_no.regex' =>
                'Mobile number must contain exactly 10 digits.',

            'funds_term_months.in' =>
                'Please select a valid funding term.',

            'home_owner.in' =>
                'Please select Yes or No for Home Owner.',

            'vat_registered.in' =>
                'Please select Yes or No for VAT Registered.',

            'number_of_sites.in' =>
                'Please select a valid number of sites.',

            'sites_count.required_if' =>
                'Please enter the number of sites (1-500).',

            'sites_count.integer' =>
                'Number of sites must be a whole number.',

            'sites_count.min' =>
                'Number of sites must be at least 1.',

            'sites_count.max' =>
                'Number of sites cannot be more than 500.',

            'mpan.digits' =>
                'Please enter a valid MPAN. It must contain exactly 13 digits.',

            'mprn.digits_between' =>
                'Please enter a valid MPRN. It must contain between 6 and 10 digits.',

            'fuel_types.required' =>
                'Please select at least one fuel type.',

            'fuel_types.*.in' =>
                'Please select a valid fuel type (Electricity, Gas or Water).',

            'funds_usage_details.required_if' =>
                'Please give more details about how the funds will be used.',

            '*.required' =>
                ':attribute is required.',

            'spid.digits_between' =>
                'Please enter a valid SPID. It must contain between 8 and 10 digits.',

            'lead_date.date_format' =>
                'Lead Date must be a valid date in YYYY-MM-DD format.',

            'lead_date.before_or_equal' =>
                'Lead Date cannot be in the future.',
        ];
    }

    /**
     * messages() reworded for the Lead CSV import, where a value is
     * typed into a cell rather than picked from a list - "must be
     * one of ..." instead of "Please select ...".
     */
    public static function importMessages(): array
    {
        return array_merge(self::messages(), [

            'status.in' =>
                'Status must be draft or published.',

            'company_type.in' =>
                'Company Type must be Limited, Sole Trader, Partnership or Limited Liability Partnership.',

            'business_start_date.date' =>
                'Business Start Date must be a valid date in YYYY-MM-DD format.',

            'date_of_birth.date' =>
                'Date of Birth must be a valid date in YYYY-MM-DD format.',

            'funds_term_months.in' =>
                'Funding term must be 12, 24, 36, 48, 60 or 72.',

            'home_owner.in' =>
                'Home Owner must be Yes or No.',

            'vat_registered.in' =>
                'VAT Registered must be Yes or No.',

            'loan_purpose.in' =>
                'Loan Purpose must be one of the values listed under Expected Columns.',

            'number_of_sites.in' =>
                'Number of Sites must be Single Site or Multiple Site.',

            'sites_count.required_if' =>
                'sites_count is required for a Multiple Site row (1-500).',

            'mpan.digits' =>
                'MPAN must contain exactly 13 digits.',

            'mprn.digits_between' =>
                'MPRN must contain 6 to 10 digits.',

            'fuel_types.*.in' =>
                'Fuel Type must be Electricity, Gas or Water (separate several with a comma).',

            'spid.digits_between' =>
                'SPID must contain 8 to 10 digits.',
        ]);
    }

    /**
     * A field's rules minus "nullable" - compared strictly, since rule
     * lists also hold closures / rule objects (e.g. the MPAN check).
     */
    private static function withoutNullable(array $rules): array
    {
        return array_values(array_filter($rules, fn ($rule) => $rule !== 'nullable'));
    }

    /** Most documents one Add / Edit form can attach at once. */
    public const MAX_FORM_DOCUMENTS = 10;

    /**
     * The form's optional Documents section - rows of a document type
     * and a file, stored as Notes & Documents entries (LeadActivity),
     * exactly like an upload on the Lead Details page.
     */
    public static function documentRules(): array
    {
        return [
            'documents' => ['nullable', 'array', 'max:' . self::MAX_FORM_DOCUMENTS],
            'documents.*.type' => ['nullable', 'required_with:documents.*.file', Rule::in(array_keys(\App\Models\LeadActivity::DOCUMENT_TYPES))],
            'documents.*.file' => ['nullable', 'required_with:documents.*.type', 'file', 'max:10240'],
        ];
    }

    public static function documentMessages(): array
    {
        return [
            'documents.max' => 'You can attach up to ' . self::MAX_FORM_DOCUMENTS . ' documents at a time.',
            'documents.*.type.required_with' => 'Please select a document type for each file.',
            'documents.*.type.in' => 'Please select a valid document type.',
            'documents.*.file.required_with' => 'Please choose a file for each document type, or remove the row.',
            'documents.*.file.file' => 'The document could not be uploaded - please choose it again.',
            'documents.*.file.max' => 'Each document may not be larger than 10MB.',
        ];
    }

    /**
     * AU Savers: saving at a draft stage (Call Back / Awaiting
     * Additional Information) also sets a reminder - its date and time
     * are required, the note optional. Same rules as a reminder added
     * on the Lead Details page.
     */
    public static function stageReminderRules(array $input): array
    {
        if (!Lead::isDraftStage((string) ($input['lead_stage'] ?? ''))) {
            return [];
        }

        return [
            'stage_reminder_date' => ['required', 'date', 'after_or_equal:today'],
            'stage_reminder_time' => ['required', 'date_format:H:i'],
            'stage_reminder_note' => ['required', 'string', 'max:2000'],
        ];
    }

    public static function stageReminderMessages(): array
    {
        return [
            'stage_reminder_date.required' => 'Please select a reminder date for this stage.',
            'stage_reminder_date.date' => 'Please enter a valid reminder date.',
            'stage_reminder_date.after_or_equal' => 'Reminder date cannot be in the past.',
            'stage_reminder_time.required' => 'Please select a reminder time for this stage.',
            'stage_reminder_time.date_format' => 'Please enter a valid reminder time.',
            'stage_reminder_note.required' => 'Please enter the reminder notes for this stage.',
        ];
    }

    /** Labels for :attribute in messages. */
    public static function attributes(): array
    {
        return self::FIELD_LABELS;
    }

    /**
     * The required-on-publish fields for this submission's product
     * (and, for AU Savers, its Number of Sites).
     *
     * @return array<int,string>
     */
    public static function requiredFieldsFor(array $input): array
    {
        $productId = (int) ($input['product_id'] ?? 0);

        return array_merge(
            self::REQUIRED_ON_PUBLISH['common'],
            in_array($productId, [1, 2], true) ? self::REQUIRED_ON_PUBLISH['funding'] : [],
            $productId === Product::AU_SAVERS_ID ? self::REQUIRED_ON_PUBLISH['au_savers'] : []
        );
    }

    /**
     * $rules with the publish requirements added - every field except
     * Mobile No. and Date of Birth must be filled. Only applied when the
     * lead is being published / submitted to pricing.
     */
    public static function withPublishRequirements(array $rules, array $input): array
    {
        foreach (self::requiredFieldsFor($input) as $field) {
            $rules[$field] = array_merge(['required'], self::withoutNullable($rules[$field] ?? []));
        }

        $productId = (int) ($input['product_id'] ?? 0);

        if (in_array($productId, [1, 2], true)) {
            $rules['funds_usage_details'] = array_merge(
                ['required_if:loan_purpose,Other'],
                $rules['funds_usage_details'] ?? []
            );
        }

        if ($productId === Product::AU_SAVERS_ID) {
            $rules['fuel_types'][] = 'min:1';

            if (self::hasOwnMeters($input)) {
                $rules['meters'] = [new AtLeastOneMeter()];
            }
        }

        return $rules;
    }

    /**
     * An AU Savers lead whose MPAN / MPRN / SPID are its own: a Single
     * Site lead, or one site of an expanded Multiple Site batch (the
     * edit form marks that with meter_toggles=site). An unexpanded
     * Multiple Site lead's meters come per site from its sites CSV.
     */
    public static function hasOwnMeters(array $input): bool
    {
        return (int) ($input['product_id'] ?? 0) === Product::AU_SAVERS_ID
            && (($input['number_of_sites'] ?? null) !== 'Multiple Site' || ($input['meter_toggles'] ?? null) === 'site');
    }

    /**
     * True when this submission comes from the Add/Edit form's meter
     * checkboxes - see meterEnabled().
     */
    public static function usesMeterToggles(array $input): bool
    {
        return self::hasOwnMeters($input) && array_key_exists('meter_toggles', $input);
    }

    /**
     * Whether a meter (mpan / mprn / spid) is in use: its checkbox is
     * ticked on the form, or - with no checkboxes (CSV import, API) -
     * always.
     */
    public static function meterEnabled(array $input, string $meter): bool
    {
        if (!self::usesMeterToggles($input)) {
            return true;
        }

        return filter_var($input[$meter . '_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * A ticked meter must have a value - on every save, draft or not.
     */
    public static function withMeterToggleRules(array $rules, array $input): array
    {
        if (!self::usesMeterToggles($input)) {
            return $rules;
        }

        foreach (self::METERS as $meter) {
            if (self::meterEnabled($input, $meter)) {
                $rules[$meter] = array_merge(['required'], self::withoutNullable($rules[$meter] ?? []));
            }
        }

        return $rules;
    }

    /** "Please enter the MPAN, or untick it." for each meter. */
    public static function meterToggleMessages(): array
    {
        $messages = [];

        foreach (self::METER_LABELS as $meter => $label) {
            $messages["{$meter}.required"] = "Please enter the {$label}, or untick {$label}.";
        }

        return $messages;
    }

    /**
     * An unticked meter is cleared - its input is disabled on the form
     * (so not submitted), and on Edit that must remove a saved value.
     */
    public static function applyMeterToggles(array $validated, array $input): array
    {
        if (self::usesMeterToggles($input)) {
            foreach (self::METERS as $meter) {
                if (!self::meterEnabled($input, $meter)) {
                    $validated[$meter] = null;
                }
            }
        }

        return Arr::except($validated, ['meters']);
    }

    /**
     * Fuel Type from a CSV cell - "Electricity, Gas" (comma, semicolon
     * or pipe separated, any case) - as the list the rules expect.
     * Unknown values are kept so validation can report them.
     */
    public static function parseFuelTypes(mixed $value): ?array
    {
        if (is_array($value) || $value === null || $value === '') {
            return $value === '' ? null : $value;
        }

        $known = array_combine(array_map('strtolower', self::FUEL_TYPES), self::FUEL_TYPES);

        $types = collect(preg_split('/[,;|]/', (string) $value))
            ->map(fn ($type) => trim($type))
            ->filter()
            ->map(fn ($type) => $known[strtolower($type)] ?? $type)
            ->unique()
            ->values()
            ->all();

        return $types ?: null;
    }

    /**
     * Publishing is one-way: once a lead is published, nobody can
     * move it back to draft. Moved from LeadController so the CSV
     * importer (which never updates an existing lead) doesn't need
     * to know about it, while store()/update() still get it via the
     * $lead parameter.
     */
    public static function statusCannotRevertFromPublished(Lead $lead): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($lead) {
            // Open (published) and Assigned leads alike.
            if ($lead->isPublishedOrBeyond() && $value === 'draft') {
                $fail('A published lead cannot be changed back to draft.');
            }
        };
    }
}
