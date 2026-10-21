<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Stage Management shows and edits each stage's Order - one sequence
 * across all stages (1, 2, 3 ...) that sets the order of the Lead
 * Stages dropdown. The seeded values (10, 20, 30 ...) become 1, 2, 3
 * ..., keeping the same order. Deleted stages go after the active ones.
 */
return new class extends Migration
{
    public function up(): void
    {
        $ids = DB::table('lead_stages')
            ->orderByRaw('deleted_at IS NOT NULL')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id');

        foreach ($ids as $i => $id) {
            DB::table('lead_stages')->where('id', $id)->update(['sort_order' => $i + 1]);
        }
    }

    public function down(): void
    {
        // Only the numbering changed - the order is the same either way.
    }
};
