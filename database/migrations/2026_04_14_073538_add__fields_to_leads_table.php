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
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to')->nullable(); // current owner

            $table->unsignedBigInteger('assigned_qa_id')->nullable();
            $table->unsignedBigInteger('assigned_manager_id')->nullable();

            $table->unsignedBigInteger('previous_ae_id')->nullable();

            $table->string('stage')->default('ae');
            // ae, qa, manager, completed, lost
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            //
        });
    }
};
