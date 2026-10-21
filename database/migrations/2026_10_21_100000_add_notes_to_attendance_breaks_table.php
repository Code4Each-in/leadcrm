<?php

use App\Services\AttendanceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_breaks', function (Blueprint $table) {
            $table->string('notes')->nullable()->after('reason');
        });

        // Breaks recorded literally as "Other" were the automatic gap between a
        // Time Out and the next login (a manual "Other" stores the typed reason).
        DB::table('attendance_breaks')
            ->where('reason', AttendanceService::LOGGED_OUT_BREAK_REASON)
            ->whereNull('notes')
            ->update(['notes' => AttendanceService::LOGGED_OUT_BREAK_NOTES]);
    }

    public function down(): void
    {
        Schema::table('attendance_breaks', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
