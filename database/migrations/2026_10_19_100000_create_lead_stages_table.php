<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Lead Staging stages, managed from Stage Management (until now they
 * were constants on App\Models\Lead). `key` is the value stored in
 * leads.status / lead_assignments.*_status - it never changes, so a
 * renamed stage keeps every lead and history row that uses it.
 *
 * Seeded with the stages as they were: same keys, names, groups and
 * order, each assigned to the role that picked its group, for AU
 * Savers. Quotes Sent to the Customer was listed under both Pricing
 * (MIS) and Closing (Account Manager) - it is one stage, in Pricing,
 * for both roles. The two stages already retired are seeded as deleted
 * so leads still at them keep their name and colour.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_stages', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();
            $table->string('name');
            $table->string('group', 32);
            $table->json('roles');
            $table->json('products');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        $ae = (int) config('roles.ae', 4);
        $mis = (int) config('roles.mis', 3);
        $manager = (int) config('roles.manager', 6);
        $auSavers = 3; // Product::AU_SAVERS_ID

        $stages = [
            ['lead_submitted_to_pricing', 'Lead Submitted to Pricing', 'Lead', [$ae]],

            ['pricing_request_received', 'Pricing Request Received', 'Pricing', [$mis]],
            ['pricing_in_progress', 'Pricing in Progress', 'Pricing', [$mis]],
            ['sent_back', 'Sent Back to AE', 'Pricing', [$mis]],
            ['no_quotes_available', 'No Quotes Available', 'Pricing', [$mis]],
            ['supplier_not_available', 'Supplier Not Available', 'Pricing', [$mis]],
            ['meter_profile_issue', 'Issue with Meter Profile', 'Pricing', [$mis]],
            ['meter_info_incorrect', 'Meter Information - Incorrect/Incomplete', 'Pricing', [$mis]],
            ['quotes_sent_to_customer', 'Quotes Sent to the Customer', 'Pricing', [$mis, $manager]],

            ['tender_submitted', 'Tender - Submitted', 'Tender Pricing', [$mis]],
            ['tender_awaiting_quotes', 'Tender - Awaiting Quotes', 'Tender Pricing', [$mis]],
            ['tender_quotes_received', 'Tender - Quotes Received', 'Tender Pricing', [$mis]],

            ['work_in_progress', 'Work In Progress', 'Closing', [$manager]],
            ['refresh_quotes_requested', 'Refresh Quotes Requested', 'Closing', [$manager]],
            ['tender_refresh_requested', 'Tender - Refresh Quotes Requested', 'Closing', [$manager]],
            ['lost_to_competition', 'Lost to Competition', 'Closing', [$manager]],
            ['pricing_declined_by_customer', 'Pricing Declined by Customer', 'Closing', [$manager]],
            ['declined_by_supplier', 'Declined by the Supplier', 'Closing', [$manager]],
            ['out_of_supplier_criteria', "Out of Supplier's Criteria", 'Closing', [$manager]],

            ['loa_issued', 'LOA Issued', 'LOA', [$manager]],
            ['loa_signed', 'LOA Signed', 'LOA', [$manager]],
            ['loa_submitted', 'LOA Submitted', 'LOA', [$manager]],
            ['loa_rejected', 'LOA Rejected', 'LOA', [$manager]],

            ['contracts_issued', 'Contracts Issued', 'Contract', [$manager]],
            ['contracts_signed', 'Contracts Signed', 'Contract', [$manager]],
            ['contracts_submitted', 'Contracts Submitted', 'Contract', [$manager]],
            ['contract_rejected', 'Contract Rejected', 'Contract', [$manager]],
            ['contract_registration_pending', 'Contract - Registration Pending', 'Contract', [$manager]],
            ['contract_live', 'Contract Live', 'Contract', [$manager]],

            // Retired before Stage Management existed.
            ['tender_quotes_sent', 'Tender - Quotes Sent to the Customer', 'Tender Pricing', [$mis], true],
            ['meter_profile_supplier_na', 'Meter Profile/Supplier Not Available', 'Pricing', [$mis], true],
        ];

        $now = now();

        DB::table('lead_stages')->insert(array_map(fn (array $stage, int $i) => [
            'key' => $stage[0],
            'name' => $stage[1],
            'group' => $stage[2],
            'roles' => json_encode($stage[3]),
            'products' => json_encode([$auSavers]),
            'sort_order' => ($i + 1) * 10,
            'created_at' => $now,
            'updated_at' => $now,
            'deleted_at' => ($stage[4] ?? false) ? $now : null,
        ], $stages, array_keys($stages)));
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_stages');
    }
};
