<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The stage an AU Savers draft was saved at from the Save dialog -
     * Call Back or Awaiting Additional Information (see
     * Lead::DRAFT_STAGES). The lead itself stays status "draft"; this
     * only changes what it is shown as. Null for every other lead, and
     * cleared once the lead is published (LeadObserver::saving()).
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('draft_stage', 50)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('draft_stage');
        });
    }
};
