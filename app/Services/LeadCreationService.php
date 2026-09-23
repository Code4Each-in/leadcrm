<?php

namespace App\Services;

use App\Models\Lead;
use App\Support\BusinessTypeMapper;
use Illuminate\Support\Facades\DB;

/**
 * Creates a Lead (or, for a Multiple Site lead being published
 * directly, a full batch of site leads) from an already-validated
 * attribute array. Extracted from LeadController::store() so the CSV
 * importer can create leads the exact same way a manually-submitted
 * form does, rather than re-implementing lead_id reservation and
 * multisite batch creation separately.
 */
class LeadCreationService
{
    /**
     * @param array $validated Must already be validated against
     *   LeadValidationRules::rules(), and include 'created_by'.
     * @return Lead The first lead created - for a Multiple Site batch,
     *   its site #1.
     */
    public function create(array $validated): Lead
    {
        $validated['business_type'] = BusinessTypeMapper::map($validated['business_type'] ?? null);

        $sitesCount = (int) ($validated['sites_count'] ?? 0);

        if (($validated['number_of_sites'] ?? null) === 'Multiple Site' && $validated['status'] === 'published') {

            // Publishing immediately - create the whole batch now.
            return DB::transaction(function () use ($validated, $sitesCount) {

                $baseId = LeadIdGenerator::reserveNextBaseId();

                $firstLead = null;

                for ($sequence = 1; $sequence <= $sitesCount; $sequence++) {

                    $siteLead = Lead::create(array_merge($validated, [
                        'lead_id' => "{$baseId}-{$sequence}",
                        'base_lead_id' => $baseId,
                        'site_sequence' => $sequence,
                    ]));

                    $firstLead ??= $siteLead;
                }

                return $firstLead;
            });
        }

        // Either a regular single-site lead, or a "Multiple Site"
        // lead saved as a draft - the latter is saved as a single
        // placeholder (sites_count remembered) and only expanded into
        // its full batch once it's actually published, via
        // LeadController::expandMultisiteBatch().
        return DB::transaction(function () use ($validated) {

            $validated['lead_id'] = LeadIdGenerator::reserveNextBaseId();

            return Lead::create($validated);
        });
    }
}
