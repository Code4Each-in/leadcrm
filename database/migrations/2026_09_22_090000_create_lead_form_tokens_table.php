<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backs LeadController's duplicate-submission guard. One token is
 * issued per visit to the create-lead form; consuming it is a single
 * atomic "UPDATE ... WHERE used_at IS NULL" - a second request
 * carrying the same token (double-click, browser retry, resubmitting
 * a stale back-button page) affects 0 rows and is treated as a
 * harmless no-op instead of creating a second batch of leads.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_form_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 36)->unique();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_form_tokens');
    }
};
