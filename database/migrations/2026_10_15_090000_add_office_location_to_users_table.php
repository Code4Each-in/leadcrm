<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Free-text office location entered on the Users Add/Edit forms,
     * which no longer collect DOB / city / state / ZIP / address /
     * profile photo (those columns stay - the Profile page still uses
     * them).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('office_location')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('office_location');
        });
    }
};
