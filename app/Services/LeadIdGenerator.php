<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Generates sequential base Lead IDs (1000, 1001, 1002, ...) safely
 * under concurrent lead creation.
 *
 * Deliberately not "MAX(lead_id) + 1": two requests reading the same
 * MAX() before either has inserted would both compute the same next
 * ID. Instead a single named counter row is locked (SELECT ... FOR
 * UPDATE) inside a transaction, so a second concurrent request
 * blocks until the first has committed its increment.
 */
class LeadIdGenerator
{
    private const COUNTER_NAME = 'lead_base_id';

    /**
     * Reserve and return the next base Lead ID as a string, e.g. "1000".
     *
     * For a multisite batch of N sites, call this once and build the
     * child IDs as "{base}-1" .. "{base}-N" - the batch only consumes
     * a single base ID.
     */
    public static function reserveNextBaseId(): string
    {
        return DB::transaction(function () {
            $counter = DB::table('lead_id_counters')
                ->where('name', self::COUNTER_NAME)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                throw new \RuntimeException(
                    'Lead ID counter "' . self::COUNTER_NAME . '" is not configured. Run migrations.'
                );
            }

            DB::table('lead_id_counters')
                ->where('name', self::COUNTER_NAME)
                ->update([
                    'next_value' => $counter->next_value + 1,
                    'updated_at' => now(),
                ]);

            return (string) $counter->next_value;
        });
    }
}
