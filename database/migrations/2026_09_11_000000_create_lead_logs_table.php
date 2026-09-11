<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Generic, append-only audit trail for everything that happens to a
     * Lead (the Lead itself, its notes/documents (lead_activities), and
     * its reminders (lead_reminders)). Deliberately named "lead_logs" -
     * not "lead_activities", which already exists and means something
     * else (the notes/documents feed shown on the Lead Show page).
     */
    public function up(): void
    {
        Schema::create('lead_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')
                ->constrained('leads')
                ->cascadeOnDelete();

            // Nullable + a denormalized name snapshot: the user who
            // performed the action may later be soft-deleted or renamed,
            // but the log's wording ("Sarah Williams updated...") must
            // stay accurate regardless.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('user_name')->nullable();

            // Machine key, e.g. "lead_created", "note_updated",
            // "reminder_deleted".
            $table->string('action');

            // Coarse category for icon/filter grouping on the UI:
            // "lead" | "note" | "document" | "reminder".
            $table->string('module');

            // Pre-rendered human sentence for display.
            $table->text('description');

            // What the action was actually about, when it isn't the
            // Lead itself (e.g. a LeadActivity or LeadReminder row).
            // Kept as loose type+id (not a real FK) since the subject
            // can be any model, including ones added in the future,
            // and the subject row may be hard-deleted by the time the
            // log is read.
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            // Old/new values for update-style actions.
            $table->json('changes')->nullable();

            // Immutable log - no updated_at.
            $table->timestamp('created_at')->nullable();

            $table->index(['lead_id', 'created_at']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_logs');
    }
};
