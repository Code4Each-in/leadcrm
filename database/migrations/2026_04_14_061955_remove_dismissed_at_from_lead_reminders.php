<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('lead_reminders', 'dismissed_at')) {
            Schema::table('lead_reminders', function (Blueprint $table) {
                $table->dropColumn('dismissed_at');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('lead_reminders', 'dismissed_at')) {
            Schema::table('lead_reminders', function (Blueprint $table) {
                $table->timestamp('dismissed_at')->nullable();
            });
        }
    }
};
