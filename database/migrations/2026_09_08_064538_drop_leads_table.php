<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_documents', function (Blueprint $table) {
            $table->dropForeign('lead_documents_lead_id_foreign');
        });

        Schema::table('lead_notes', function (Blueprint $table) {
            $table->dropForeign('lead_notes_lead_id_foreign');
        });

        Schema::table('lead_reminders', function (Blueprint $table) {
            $table->dropForeign('lead_reminders_lead_id_foreign');
        });

        Schema::table('lead_user', function (Blueprint $table) {
            $table->dropForeign('lead_user_lead_id_foreign');
        });

        Schema::dropIfExists('leads');
    }

    public function down(): void
    {
        // Add recreation logic here if you need rollback support.
    }
};
