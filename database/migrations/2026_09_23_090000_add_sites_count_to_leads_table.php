<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Remembers the intended site count for a "Multiple Site" lead saved
 * as a draft, before it has been expanded into its batch of
 * "{base}-1" .. "{base}-N" leads (base_lead_id is still null at that
 * point - see LeadController::expandMultisiteBatch()). Also kept on
 * each expanded site lead afterwards, purely for display.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedSmallInteger('sites_count')
                ->nullable()
                ->after('site_sequence');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('sites_count');
        });
    }
};
