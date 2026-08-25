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
            // Drop foreign key first
            $table->dropForeign(['assigned_user_id']); // <--- important
            // Now drop the column
            $table->dropColumn('assigned_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Restore the column
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            // Restore foreign key (optional, if needed)
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
