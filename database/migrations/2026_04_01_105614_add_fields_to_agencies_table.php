<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {

            if (!Schema::hasColumn('agencies', 'primary_contact_name')) {
                $table->string('primary_contact_name')->after('agency_name');
            }

            if (!Schema::hasColumn('agencies', 'primary_email')) {
                $table->string('primary_email')->nullable()->unique();
            }

            if (!Schema::hasColumn('agencies', 'logo')) {
                $table->string('logo')->nullable();
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            //
        });
    }
};
