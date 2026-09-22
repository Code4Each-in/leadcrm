<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * lead_id is the business-facing identifier (e.g. "1000", "1500-3").
     * It is intentionally separate from the internal auto-increment
     * "id" column, which stays a bigint and keeps being the primary
     * key / foreign key target for lead_documents, lead_notes,
     * lead_reminders, lead_activities, lead_logs and lead_user - none
     * of those need to change.
     *
     * Existing leads keep lead_id = NULL (no backfill); only leads
     * created from this point on are assigned one.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('lead_id', 20)
                ->nullable()
                ->unique()
                ->after('id');

            // Shared grouping key for a multisite batch, e.g. "1500"
            // for children "1500-1" .. "1500-20". Null for
            // non-multisite leads.
            $table->string('base_lead_id', 20)
                ->nullable()
                ->after('lead_id');

            // Position of this lead within its multisite batch
            // (1..site_count). Null for non-multisite leads.
            $table->unsignedSmallInteger('site_sequence')
                ->nullable()
                ->after('base_lead_id');

            $table->index('base_lead_id');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['base_lead_id']);
            $table->dropColumn(['lead_id', 'base_lead_id', 'site_sequence']);
        });
    }
};
