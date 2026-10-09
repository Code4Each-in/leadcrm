<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Set on a lead created with an MPAN another lead already had, after
     * the user confirmed it on Add Lead - shown as a "D" flag on the
     * listing and the lead's page, which links to the leads holding the
     * same MPAN (see Lead::duplicateMpanMatches()).
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('mpan_duplicate')->default(false)->after('mpan');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('mpan_duplicate');
        });
    }
};
