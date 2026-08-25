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
        Schema::create('lead_upload_logs', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->integer('inserted_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->string('failed_file')->nullable();
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_upload_logs');
    }
};
