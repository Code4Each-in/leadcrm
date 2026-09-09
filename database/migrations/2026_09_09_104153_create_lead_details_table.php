<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_details', function (Blueprint $table) {

            $table->id();

            // Companies House unique company number
            $table->string('company_number')->unique();

            // Company name returned by Companies House
            $table->string('company_name')->nullable();

            // Selected company type from our form
            $table->string('company_type')->nullable();

            // Complete Companies House company response
            $table->json('company_api_response')->nullable();

            // Complete Companies House officers response
            $table->json('officers_api_response')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_details');
    }
};
