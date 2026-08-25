<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_reminders', function (Blueprint $table) {
            $table->id();

            // Who created reminder
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // Agency under which reminder belongs
            $table->foreignId('agency_id')
                ->constrained()
                ->onDelete('cascade');

            // Related lead
            $table->foreignId('lead_id')
                ->constrained()
                ->onDelete('cascade');

            // Reminder date & time
            $table->date('date');
            $table->time('time');

            // Optional notes
            $table->text('notes')->nullable();

            // Track if reminder already shown
            $table->boolean('is_triggered')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_reminders');
    }
};
