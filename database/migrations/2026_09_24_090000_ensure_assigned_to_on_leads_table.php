<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * leads.assigned_to (the Account Executive a lead is assigned to)
     * was originally added by 2026_04_14_073538, but the leads table
     * was later dropped and recreated (2026_09_08_122300) without it.
     * Some databases still have the old column, others don't, so this
     * only adds it where it's missing.
     */
    public function up(): void
    {
        if (Schema::hasColumn('leads', 'assigned_to')) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('assigned_to')
                ->nullable()
                ->after('created_by')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Intentionally left in place - the column may pre-date this
        // migration on some databases.
    }
};
