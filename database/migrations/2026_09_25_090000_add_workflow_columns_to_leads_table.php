<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MIS -> AE -> Account Manager workflow.
     *
     * leads.assigned_to stays what it always was - the user who
     * currently holds the lead (the "current owner"), now an AE *or*
     * an Account Manager. These columns remember everyone else who
     * has been involved, so the lead never "forgets" its AE while
     * it sits with an Account Manager (and vice versa).
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // The MIS / Admin user who first assigned the lead.
            $table->foreignId('assigned_by')->nullable()->after('assigned_to')
                ->constrained('users')->nullOnDelete();

            // The AE on the lead (kept while the lead is with the AM).
            $table->foreignId('account_executive_id')->nullable()->after('assigned_by')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('ae_assigned_at')->nullable()->after('account_executive_id');

            // The Account Manager the lead was last forwarded to.
            $table->foreignId('account_manager_id')->nullable()->after('ae_assigned_at')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('am_assigned_at')->nullable()->after('account_manager_id');

            $table->timestamp('process_started_at')->nullable()->after('am_assigned_at');
            $table->foreignId('process_started_by')->nullable()->after('process_started_at')
                ->constrained('users')->nullOnDelete();

            $table->timestamp('closed_at')->nullable()->after('process_started_by');
            $table->foreignId('closed_by')->nullable()->after('closed_at')
                ->constrained('users')->nullOnDelete();
        });

        // Leads assigned before this workflow existed: their current
        // owner is, by definition, the AE.
        DB::table('leads')
            ->whereNotNull('assigned_to')
            ->where('status', 'assigned')
            ->update([
                'account_executive_id' => DB::raw('assigned_to'),
                'ae_assigned_at' => DB::raw('updated_at'),
            ]);
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            foreach (['closed_by', 'process_started_by', 'account_manager_id', 'account_executive_id', 'assigned_by'] as $column) {
                $table->dropConstrainedForeignId($column);
            }

            $table->dropColumn(['closed_at', 'process_started_at', 'am_assigned_at', 'ae_assigned_at']);
        });
    }
};
