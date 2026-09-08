<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop child tables first because they reference `leads`
        Schema::dropIfExists('lead_documents');
        Schema::dropIfExists('lead_notes');
        Schema::dropIfExists('lead_reminders');
        Schema::dropIfExists('lead_user');

        // Then drop the parent table
        Schema::dropIfExists('leads');
    }

    public function down(): void
    {
        // Recreate tables here if you need rollback support.
    }
};
