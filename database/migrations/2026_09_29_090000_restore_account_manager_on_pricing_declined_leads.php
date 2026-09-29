<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * A declined pricing no longer takes the lead away from its
     * Account Manager (the "pricing_declined" lead status is gone -
     * the decline lives on the pricing record only). Leads already
     * moved into that status go back to their Account Manager; one
     * without an Account Manager on record goes back to Open so MIS
     * can assign it. Does nothing when no such leads exist.
     */
    public function up(): void
    {
        DB::table('leads')
            ->where('status', 'pricing_declined')
            ->whereNotNull('account_manager_id')
            ->update([
                'status' => 'with_account_manager',
                'assigned_to' => DB::raw('account_manager_id'),
            ]);

        DB::table('leads')
            ->where('status', 'pricing_declined')
            ->update([
                'status' => 'published',
                'assigned_to' => null,
            ]);
    }

    /**
     * One-way data fix - the old status no longer exists in the app.
     */
    public function down(): void
    {
    }
};
