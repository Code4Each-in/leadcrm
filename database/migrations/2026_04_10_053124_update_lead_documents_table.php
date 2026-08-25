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
        Schema::table('lead_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('uploaded_by')->after('lead_id');
            $table->string('file_name')->after('file');
            $table->string('file_type')->after('file_name');
            $table->integer('file_size')->after('file_type');
        });
    }

    public function down(): void
    {
        Schema::table('lead_documents', function (Blueprint $table) {
            $table->dropColumn(['uploaded_by', 'file_name', 'file_type', 'file_size']);
        });
    }
};
