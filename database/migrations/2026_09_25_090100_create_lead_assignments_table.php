<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Append-only assignment / movement history for a lead: every
     * assignment, start of process, hand-over to an Account Manager,
     * send-back and close. Names and role names are snapshotted so
     * the trail stays accurate if a user is later renamed, deleted
     * or moved to another role. (lead_logs stays the generic audit
     * feed; this is the structured "who -> whom, when" trail.)
     */
    public function up(): void
    {
        Schema::create('lead_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();

            // assigned | reassigned | process_started | moved_to_am | sent_back | closed
            $table->string('action', 32);

            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('performed_by_name')->nullable();
            $table->string('performed_by_role')->nullable();

            $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_user_name')->nullable();
            $table->string('from_role')->nullable();

            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('to_user_name')->nullable();
            $table->string('to_role')->nullable();

            $table->string('from_status', 32)->nullable();
            $table->string('to_status', 32)->nullable();

            // Optional reason, e.g. why a lead was sent back.
            $table->text('note')->nullable();

            $table->timestamp('created_at')->nullable();

            $table->index(['lead_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_assignments');
    }
};
