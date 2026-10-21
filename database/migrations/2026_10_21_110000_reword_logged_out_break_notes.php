<?php

use App\Services\AttendanceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The note on the automatic logged-out break now reads in plain words
 * (AttendanceService::LOGGED_OUT_BREAK_NOTES) - breaks already recorded
 * with the old wording get the new one too.
 */
return new class extends Migration
{
    private const OLD_NOTES = 'Was logged out (logout or session timeout) until the next login';

    public function up(): void
    {
        DB::table('attendance_breaks')
            ->where('notes', self::OLD_NOTES)
            ->update(['notes' => AttendanceService::LOGGED_OUT_BREAK_NOTES]);
    }

    public function down(): void
    {
        DB::table('attendance_breaks')
            ->where('notes', AttendanceService::LOGGED_OUT_BREAK_NOTES)
            ->update(['notes' => self::OLD_NOTES]);
    }
};
