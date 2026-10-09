<?php

namespace App\Support;

use App\Models\Lead;

/**
 * The one place that decides whether an MPAN is already in use - by
 * manual Add Lead / Edit, the Multiple Site sites CSV, and the Lead
 * CSV import alike, so the flows can't drift apart.
 *
 * An MPAN is "in use" when any non-deleted lead, whatever its status
 * (Draft, Open, Lost, Closed, ...), has it either as its own mpan or
 * inside the pending_sites of a Multiple Site draft not yet expanded
 * into its batch.
 *
 * There's no unique index behind this (existing multisite batches
 * created before per-site MPANs share one MPAN across their sites), so
 * LeadCreationService re-checks inside its transaction, after taking
 * the Lead ID counter lock, to stop two concurrent submissions from
 * both claiming the same MPAN.
 *
 * On Add Lead an MPAN in use is allowed once the user has confirmed it
 * (confirm_duplicate_mpan) - the lead is then created with
 * leads.mpan_duplicate set, and links to the leads holding the same
 * MPAN (holders()).
 */
class MpanRegistry
{
    /**
     * @param array<int,string|null> $mpans
     * @param int|null $exceptLeadId the lead being edited / expanded,
     *   whose own MPAN(s) don't count as a clash with itself
     * @return array<int,string> the given MPANs that are already in use
     */
    public static function taken(array $mpans, ?int $exceptLeadId = null): array
    {
        // MPAN keys are numeric strings, which PHP turns into ints.
        return array_map('strval', array_keys(self::holders($mpans, $exceptLeadId)));
    }

    /**
     * The leads holding each of the given MPANs - as their own mpan, or
     * in the pending_sites of an unexpanded Multiple Site draft.
     *
     * @param array<int,string|null> $mpans
     * @return array<string, \Illuminate\Support\Collection<int, Lead>> mpan => leads (only MPANs in
     *   use; PHP turns the numeric MPAN keys into ints - cast back with strval)
     */
    public static function holders(array $mpans, ?int $exceptLeadId = null): array
    {
        $mpans = array_values(array_unique(array_filter(
            array_map(fn ($mpan) => trim((string) $mpan), $mpans),
            fn ($mpan) => $mpan !== ''
        )));

        if (!$mpans) {
            return [];
        }

        $holders = [];

        Lead::query()
            ->whereIn('mpan', $mpans)
            ->when($exceptLeadId, fn ($q) => $q->whereKeyNot($exceptLeadId))
            ->orderBy('id')
            ->get()
            ->each(function (Lead $lead) use (&$holders) {
                $holders[(string) $lead->mpan][$lead->id] = $lead;
            });

        // Only the (few) unexpanded Multiple Site drafts carry
        // pending_sites, so reading them back is cheap - and avoids
        // JSON-path SQL that differs between MySQL and SQLite.
        Lead::query()
            ->whereNotNull('pending_sites')
            ->when($exceptLeadId, fn ($q) => $q->whereKeyNot($exceptLeadId))
            ->orderBy('id')
            ->get()
            ->each(function (Lead $lead) use ($mpans, &$holders) {
                foreach ($lead->pending_sites ?? [] as $site) {
                    $mpan = (string) ($site['mpan'] ?? '');

                    if (in_array($mpan, $mpans, true)) {
                        $holders[$mpan][$lead->id] = $lead;
                    }
                }
            });

        return array_map(fn (array $leads) => collect(array_values($leads)), $holders);
    }

    public static function isTaken(?string $mpan, ?int $exceptLeadId = null): bool
    {
        return self::taken([$mpan], $exceptLeadId) !== [];
    }

    /**
     * Validation rule for a single MPAN field. When editing, $lead is
     * excluded from the check, and the check only runs if the MPAN is
     * actually being changed - so a lead from an older multisite batch
     * (which shares its MPAN with its siblings) can still be edited.
     * $allowTaken: the user confirmed a duplicate MPAN (Add Lead).
     */
    public static function rule(?Lead $lead = null, bool $allowTaken = false): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($lead, $allowTaken) {
            $value = trim((string) $value);

            if ($value === '' || $allowTaken) {
                return;
            }

            if ($lead && (string) $lead->mpan === $value) {
                return;
            }

            if (self::isTaken($value, $lead?->id)) {
                $fail(LeadValidationRules::MPAN_TAKEN_MESSAGE);
            }
        };
    }
}
