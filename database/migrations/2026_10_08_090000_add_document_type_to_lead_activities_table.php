<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Document Type chosen in the Notes & Documents composer (see
     * LeadActivity::DOCUMENT_TYPES). "other" is a typed note; every
     * other type is an uploaded document. Null for entries added
     * before document types existed and for workflow notes.
     */
    public function up(): void
    {
        Schema::table('lead_activities', function (Blueprint $table) {
            $table->string('document_type', 32)->nullable()->after('workflow_action');
        });
    }

    public function down(): void
    {
        Schema::table('lead_activities', function (Blueprint $table) {
            $table->dropColumn('document_type');
        });
    }
};
