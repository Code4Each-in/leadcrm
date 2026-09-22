<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Backs App\Services\LeadIdGenerator. A single named counter row is
 * read with a row lock inside a transaction and incremented, so
 * concurrent lead creation can't hand out the same base Lead ID
 * (unlike a MAX(lead_id) + 1 read, which two simultaneous requests
 * could both see before either has inserted).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_id_counters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedBigInteger('next_value')->default(1000);
            $table->timestamps();
        });

        DB::table('lead_id_counters')->insert([
            'name' => 'lead_base_id',
            'next_value' => 1000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_id_counters');
    }
};
