<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');

            $table->dateTime('time_in')->nullable();
            $table->dateTime('time_out')->nullable();

            $table->dateTime('break1_start')->nullable();
            $table->dateTime('break1_end')->nullable();
            $table->dateTime('break2_start')->nullable();
            $table->dateTime('break2_end')->nullable();

            $table->timestamps();

            // One attendance row per user per day — prevents duplicate entries.
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
