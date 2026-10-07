<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes behind the dashboard's counts (see App\Services\Dashboard):
 *
 * - leads.status: nearly every dashboard count and the status
 *   breakdown filter or group on it.
 * - leads(created_by, created_at): "leads I created" and the
 *   created-per-day figures.
 * - lead_reminders(created_by, reminder_date): "My Reminders".
 * - lead_assignments(performed_by, created_at): MIS "assignments I made".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->index('status', 'leads_status_index');
            $table->index(['created_by', 'created_at'], 'leads_created_by_created_at_index');
        });

        Schema::table('lead_reminders', function (Blueprint $table) {
            $table->index(['created_by', 'reminder_date'], 'lead_reminders_created_by_date_index');
        });

        Schema::table('lead_assignments', function (Blueprint $table) {
            $table->index(['performed_by', 'created_at'], 'lead_assignments_performed_by_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_status_index');
            $table->dropIndex('leads_created_by_created_at_index');
        });

        Schema::table('lead_reminders', function (Blueprint $table) {
            $table->dropIndex('lead_reminders_created_by_date_index');
        });

        Schema::table('lead_assignments', function (Blueprint $table) {
            $table->dropIndex('lead_assignments_performed_by_created_at_index');
        });
    }
};
