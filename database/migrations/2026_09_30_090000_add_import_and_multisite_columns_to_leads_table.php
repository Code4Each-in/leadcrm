<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - lead_date: the optional "Lead Date" supplied on a CSV import.
 *   Deliberately separate from created_at, which stays the moment the
 *   row was actually created (listing sort, logs).
 *
 * - intended_account_manager_id: the Account Manager named in an
 *   imported row's "User Name" column, validated at import time. Kept
 *   apart from account_manager_id (which means "is / was this lead's
 *   Account Manager" and drives visibility) because an AU Savers lead
 *   can't actually be assigned until its pricing is published - see
 *   LeadWorkflowService::assignIntendedAccountManager(). Cleared once
 *   the lead is really assigned.
 *
 * - pending_sites: per-site Supply Address / MPAN / MPRN / SPID for a
 *   "Multiple Site" lead saved as a draft, held on the placeholder row
 *   until it's published and expanded into its batch of site leads
 *   (see LeadController::expandMultisiteBatch()). Cleared on expansion.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->date('lead_date')->nullable()->after('lead_id');

            $table->foreignId('intended_account_manager_id')
                ->nullable()
                ->after('account_manager_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->json('pending_sites')->nullable()->after('sites_count');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('intended_account_manager_id');
            $table->dropColumn(['lead_date', 'pending_sites']);
        });
    }
};
