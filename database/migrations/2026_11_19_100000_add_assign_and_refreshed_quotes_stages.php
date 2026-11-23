<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Three new Pricing stages, placed right after Quotes Sent to the
 * Customer:
 * - Assign to AM (MIS) - set together with assigning the Account
 *   Manager (the lead page's Assign to AM modal).
 * - Refreshed Quotes Sent to AM (MIS)
 * - Refreshed Quotes Sent to Customer (Account Manager)
 *
 * They go in the group Quotes Sent to the Customer is in (Pricing,
 * whatever it has since been renamed to); the rest of that group's
 * stages move down to make room. A key that already exists (e.g. added
 * by hand in Stage Management) is left alone.
 */
return new class extends Migration
{
    private const KEYS = ['assign_to_am', 'refreshed_quotes_sent_to_am', 'refreshed_quotes_sent_to_customer'];

    public function up(): void
    {
        $anchor = DB::table('lead_stages')->where('key', 'quotes_sent_to_customer')->first();

        $groupId = $anchor->group_id
            ?? DB::table('lead_stage_groups')->where('name', 'Pricing')->whereNull('deleted_at')->value('id');

        if (!$groupId) {
            return;
        }

        $mis = (int) config('roles.mis', 3);
        $manager = (int) config('roles.manager', 6);

        $new = collect([
            ['assign_to_am', 'Assign to AM', [$mis]],
            ['refreshed_quotes_sent_to_am', 'Refreshed Quotes Sent to AM', [$mis]],
            ['refreshed_quotes_sent_to_customer', 'Refreshed Quotes Sent to Customer', [$manager]],
        ])->reject(fn ($stage) => DB::table('lead_stages')->where('key', $stage[0])->exists());

        if ($new->isEmpty()) {
            return;
        }

        // The group's stages in order, the new ones slotted in after
        // Quotes Sent to the Customer (or at the end), then renumbered.
        $ids = DB::table('lead_stages')
            ->where('group_id', $groupId)
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $now = now();
        $newIds = $new->map(fn ($stage) => DB::table('lead_stages')->insertGetId([
            'key' => $stage[0],
            'name' => $stage[1],
            'group_id' => $groupId,
            'roles' => json_encode($stage[2]),
            'sort_order' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]))->all();

        $anchorAt = $anchor ? array_search((int) $anchor->id, array_map('intval', $ids), true) : false;
        $at = $anchorAt === false ? count($ids) : $anchorAt + 1;

        array_splice($ids, $at, 0, $newIds);

        foreach ($ids as $i => $id) {
            DB::table('lead_stages')->where('id', $id)->update(['sort_order' => $i + 1]);
        }
    }

    public function down(): void
    {
        // Stages leads are still at are kept (soft-deleted) so they keep their name.
        foreach (self::KEYS as $key) {
            $inUse = DB::table('leads')->where('status', $key)->exists();

            $inUse
                ? DB::table('lead_stages')->where('key', $key)->update(['deleted_at' => now()])
                : DB::table('lead_stages')->where('key', $key)->delete();
        }
    }
};
