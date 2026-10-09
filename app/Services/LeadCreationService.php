<?php

namespace App\Services;

use App\Models\Lead;
use App\Support\BusinessTypeMapper;
use App\Support\LeadValidationRules;
use App\Support\MpanRegistry;
use App\Support\MultisiteSitesCsv;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates a Lead (or, for a Multiple Site lead being published
 * directly, a full batch of site leads) from an already-validated
 * attribute array. Extracted from LeadController::store() so the CSV
 * importer can create leads the exact same way a manually-submitted
 * form does, rather than re-implementing lead_id reservation and
 * multisite batch creation separately.
 *
 * A Multiple Site lead carries per-site data ($sites - see
 * MultisiteSitesCsv): site n gets entry n's MPAN and Supply Address
 * (and MPRN / SPID where given), so every site has its own
 * MPAN and address. That data is required whether the lead is
 * published or saved as a draft.
 */
class LeadCreationService
{
    public function __construct(private LeadWorkflowService $workflow)
    {
    }

    /**
     * @param array $validated Must already be validated against
     *   LeadValidationRules::rules(), and include 'created_by'.
     * @param array|null $sites Per-site data for a Multiple Site lead,
     *   already validated by MultisiteSitesCsv::validate().
     * @param bool $allowDuplicateMpans The user confirmed MPANs already
     *   used by other leads: the lead(s) holding one are created with
     *   mpan_duplicate set instead of being refused.
     * @return Lead The first lead created - for a Multiple Site batch,
     *   its site #1.
     */
    public function create(array $validated, ?array $sites = null, bool $allowDuplicateMpans = false): Lead
    {
        $validated['business_type'] = BusinessTypeMapper::map($validated['business_type'] ?? null);

        $isMultisite = ($validated['number_of_sites'] ?? null) === 'Multiple Site';

        // A Multiple Site lead's MPANs live per site - the single MPAN
        // field doesn't apply to it.
        if ($isMultisite) {
            $validated['mpan'] = null;
        }

        $sitesCount = (int) ($validated['sites_count'] ?? 0);

        if ($isMultisite && (!is_array($sites) || count($sites) !== $sitesCount)) {
            throw ValidationException::withMessages([
                'sites_csv' => LeadValidationRules::SITES_CSV_REQUIRED_MESSAGE,
            ]);
        }

        if ($isMultisite && $validated['status'] === 'published') {

            // Publishing immediately - create the whole batch now.
            $leads = DB::transaction(function () use ($validated, $sitesCount, $sites, $allowDuplicateMpans) {

                $baseId = LeadIdGenerator::reserveNextBaseId();

                $taken = self::checkMpans(MultisiteSitesCsv::mpans($sites), $allowDuplicateMpans);

                $leads = [];

                for ($sequence = 1; $sequence <= $sitesCount; $sequence++) {

                    $site = $sites[$sequence - 1];

                    $leads[] = Lead::create(array_merge(
                        self::withSiteData($validated, $site),
                        [
                            'lead_id' => "{$baseId}-{$sequence}",
                            'base_lead_id' => $baseId,
                            'site_sequence' => $sequence,
                            'pending_sites' => null,
                            'mpan_duplicate' => in_array((string) ($site['mpan'] ?? ''), $taken, true),
                        ]
                    ));
                }

                return $leads;
            });

            $this->afterCreate($leads);

            return $leads[0];
        }

        // Either a regular single-site lead, or a "Multiple Site"
        // lead saved as a draft - the latter is saved as a single
        // placeholder (sites_count and its per-site data remembered)
        // and only expanded into its full batch once it's actually
        // published, via expand().
        if ($isMultisite) {
            $validated['pending_sites'] = $sites;
        }

        $lead = DB::transaction(function () use ($validated, $isMultisite, $allowDuplicateMpans) {

            $validated['lead_id'] = LeadIdGenerator::reserveNextBaseId();

            // A Multiple Site draft is flagged when any of its sites'
            // MPANs is a duplicate; each site lead gets its own flag when
            // the draft is expanded (see expand()).
            $validated['mpan_duplicate'] = self::checkMpans($isMultisite
                ? MultisiteSitesCsv::mpans($validated['pending_sites'] ?? null)
                : [$validated['mpan'] ?? null], $allowDuplicateMpans) !== [];

            return Lead::create($validated);
        });

        $this->afterCreate([$lead]);

        return $lead;
    }

    /**
     * Turns a "Multiple Site" lead that was saved as a draft (a
     * single placeholder row, base_lead_id still null) into its full
     * batch of site leads, now that it's being published. The
     * placeholder itself becomes site #1 - not deleted and
     * recreated - so any notes, documents, reminders or audit log
     * entries already attached to it survive. Sites 2..N are new
     * rows cloned from it, each with its own entry from pending_sites.
     *
     * Callers must have checked Lead::hasValidPendingSites() before
     * changing the lead's status; the MPANs are re-checked here under
     * the Lead ID counter lock, in case another lead took one since
     * the draft was saved.
     */
    public function expand(Lead $lead): Lead
    {
        return DB::transaction(function () use ($lead) {

            if (!$lead->hasValidPendingSites()) {
                throw ValidationException::withMessages([
                    'sites_csv' => LeadValidationRules::SITES_CSV_REQUIRED_MESSAGE,
                ]);
            }

            LeadIdGenerator::lock();

            $sites = $lead->pending_sites;

            // A draft created with a confirmed duplicate MPAN keeps that
            // confirmation; each site lead is flagged on its own MPAN.
            $taken = self::checkMpans(MultisiteSitesCsv::mpans($sites), (bool) $lead->mpan_duplicate, $lead->id);
            $isTaken = fn (array $site) => in_array((string) ($site['mpan'] ?? ''), $taken, true);

            $baseId = $lead->lead_id;
            $sitesCount = (int) $lead->sites_count;

            // The placeholder's own values - what every site falls
            // back to where its CSV row left a cell blank. Not the
            // Supply Address: each site has only its own (see
            // withSiteData()).
            $shared = $lead->only(['mprn', 'spid']);

            $lead->update(array_merge(
                self::withSiteData($shared, $sites[0]),
                [
                    'lead_id' => "{$baseId}-1",
                    'base_lead_id' => $baseId,
                    'site_sequence' => 1,
                    'pending_sites' => null,
                    'mpan_duplicate' => $isTaken($sites[0]),
                ]
            ));

            $attributes = Arr::except(
                Arr::only($lead->getAttributes(), $lead->getFillable()),
                ['lead_id', 'base_lead_id', 'site_sequence', 'pending_sites']
            );

            for ($sequence = 2; $sequence <= $sitesCount; $sequence++) {

                Lead::create(array_merge(
                    self::withSiteData(array_merge($attributes, $shared), $sites[$sequence - 1]),
                    [
                        'lead_id' => "{$baseId}-{$sequence}",
                        'base_lead_id' => $baseId,
                        'site_sequence' => $sequence,
                        'mpan_duplicate' => $isTaken($sites[$sequence - 1]),
                    ]
                ));
            }

            return $lead->fresh();
        });
    }

    /**
     * $attributes with one site's data applied: its MPAN and Supply
     * Address always - a blank Supply Address stays blank rather than
     * taking the form's own one, so every site keeps only its own
     * address - and its MPRN / SPID where the CSV gave one
     * (a blank cell keeps the lead's own value).
     */
    private static function withSiteData(array $attributes, array $site): array
    {
        $attributes['mpan'] = $site['mpan'] ?? null;
        $attributes['supply_address'] = $site['supply_address'] ?? null;

        foreach (['mprn', 'spid'] as $key) {
            if (filled($site[$key] ?? null)) {
                $attributes[$key] = $site[$key];
            }
        }

        return $attributes;
    }

    /**
     * Final MPAN check, run inside the creating transaction after the
     * Lead ID counter lock is held - so of two concurrent submissions
     * using the same MPAN, the second sees the first's and fails.
     * With $allowDuplicates (the user confirmed) nothing is refused;
     * returns the MPANs already in use, for the mpan_duplicate flag.
     *
     * @return array<int,string>
     */
    private static function checkMpans(array $mpans, bool $allowDuplicates = false, ?int $exceptLeadId = null): array
    {
        $taken = MpanRegistry::taken($mpans, $exceptLeadId);

        if ($taken && !$allowDuplicates) {
            throw ValidationException::withMessages([
                'mpan' => count($taken) === 1
                    ? "MPAN {$taken[0]} is already used by another lead."
                    : 'These MPANs are already used by other leads: ' . implode(', ', $taken) . '.',
            ]);
        }

        return $taken;
    }

    /**
     * Imported leads may name an Account Manager (see
     * LeadCsvController) - log it, and assign straight away where the
     * lead is already assignable (published, and a product with no
     * pricing step). A manually created lead never has one, so this
     * is a no-op for it.
     *
     * @param array<int,Lead> $leads
     */
    private function afterCreate(array $leads): void
    {
        foreach ($leads as $lead) {

            if (!$lead->intended_account_manager_id) {
                continue;
            }

            if ($am = $lead->intendedAccountManager) {
                LeadLogger::intendedAccountManagerSet($lead, $am);
            }

            $this->workflow->assignIntendedAccountManager($lead, Auth::user());
        }
    }
}
