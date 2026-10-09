<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Date of Birth with an unknown day (only the Month and Year were
     * picked on the lead form). date_of_birth is a DATE column, which
     * can't hold a "00" day (MySQL strict mode rejects it, and it would
     * read back as the last day of the previous month), so the 1st of
     * the month is stored and this flag marks the day as unknown - see
     * App\Support\DateOfBirthParts and Lead::dateOfBirthLabel().
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('dob_day_unknown')->default(false)->after('date_of_birth');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('dob_day_unknown');
        });
    }
};
