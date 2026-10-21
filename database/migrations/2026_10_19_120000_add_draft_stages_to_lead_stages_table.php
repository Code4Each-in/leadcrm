<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The draft stages - Call Back and Awaiting Additional Information,
 * picked in the Save dialog of an AU Savers lead before it's submitted
 * - move into Stage Management too. A draft stage keeps the lead a
 * draft (leads.draft_stage holds its key, not leads.status) and needs
 * a follow-up reminder. Assigned to every role that isn't Admin /
 * Super Admin (who always see every stage), as before: whoever saves
 * an AU Savers lead could pick them. Numbered 1 and 2, ahead of Lead
 * Submitted to Pricing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_stages', function (Blueprint $table) {
            $table->boolean('is_draft')->default(false)->after('group');
        });

        $roles = array_values(array_unique(array_map('intval', [
            config('roles.ae', 4),
            config('roles.mis', 3),
            config('roles.qa', 5),
            config('roles.manager', 6),
        ])));

        $now = now();

        foreach ([
            ['call_back', 'Call Back'],
            ['awaiting_additional_information', 'Awaiting Additional Information'],
        ] as [$key, $name]) {
            if (DB::table('lead_stages')->where('key', $key)->exists()) {
                continue;
            }

            DB::table('lead_stages')->insert([
                'key' => $key,
                'name' => $name,
                'group' => 'Lead',
                'is_draft' => true,
                'roles' => json_encode($roles),
                'products' => json_encode([3]), // Product::AU_SAVERS_ID
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Numbered first (1, 2), every other stage after them in the
        // same order - one sequence across all stages.
        $ids = DB::table('lead_stages')
            ->orderByRaw('deleted_at IS NOT NULL')
            ->orderByRaw("CASE `key` WHEN 'call_back' THEN 0 WHEN 'awaiting_additional_information' THEN 1 ELSE 2 END")
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id');

        foreach ($ids as $i => $id) {
            DB::table('lead_stages')->where('id', $id)->update(['sort_order' => $i + 1]);
        }
    }

    public function down(): void
    {
        DB::table('lead_stages')->where('is_draft', true)->delete();

        Schema::table('lead_stages', function (Blueprint $table) {
            $table->dropColumn('is_draft');
        });
    }
};
