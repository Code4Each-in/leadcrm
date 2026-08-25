<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('city')->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('New');
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->unsignedBigInteger('agency_id'); // Added agency id
            $table->text('notes')->nullable();
            $table->string('documents')->nullable();
            $table->timestamps();

            // Optional foreign keys
            $table->foreign('assigned_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('agency_id')->references('id')->on('agencies')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
