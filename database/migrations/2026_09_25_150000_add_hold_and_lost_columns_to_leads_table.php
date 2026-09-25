<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Who put a lead on Hold / marked it Lost and when - the same
     * pair closed_at / closed_by already holds for Closed. (The full
     * trail of every status change is in lead_assignments.)
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->timestamp('hold_at')->nullable()->after('closed_by');
            $table->foreignId('hold_by')->nullable()->after('hold_at')
                ->constrained('users')->nullOnDelete();

            $table->timestamp('lost_at')->nullable()->after('hold_by');
            $table->foreignId('lost_by')->nullable()->after('lost_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lost_by');
            $table->dropConstrainedForeignId('hold_by');
            $table->dropColumn(['lost_at', 'hold_at']);
        });
    }
};
