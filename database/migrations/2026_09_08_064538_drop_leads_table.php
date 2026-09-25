<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dropping by the conventional column-array form (rather than
        // the literal constraint name string) is required for this
        // migration to also run on SQLite (used by the test suite -
        // see phpunit.xml), which doesn't support dropping foreign
        // keys by name. Resolves to the same constraint on MySQL.
        Schema::table('lead_documents', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
        });

        Schema::table('lead_notes', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
        });

        Schema::table('lead_reminders', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
        });

        Schema::table('lead_user', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
        });

        Schema::dropIfExists('leads');
    }

    public function down(): void
    {
        // Add recreation logic here if you need rollback support.
    }
};
