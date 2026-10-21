<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * When the user was last seen working on this attendance day. Time
     * Out after a session expiry is taken from here, not from whenever
     * the expiry happened to be noticed.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dateTime('last_activity_at')->nullable()->after('time_out');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('last_activity_at');
        });
    }
};
