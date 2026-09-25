<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marks a Notes & Documents entry as one written by the lead
     * workflow (an Account Manager's note when sending a lead back or
     * closing it - see LeadWorkflowService). Such entries are
     * permanent: they can't be edited or deleted like a normal note.
     */
    public function up(): void
    {
        Schema::table('lead_activities', function (Blueprint $table) {
            // "sent_back" | "closed" - null for an ordinary note/document.
            $table->string('workflow_action', 32)->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('lead_activities', function (Blueprint $table) {
            $table->dropColumn('workflow_action');
        });
    }
};
