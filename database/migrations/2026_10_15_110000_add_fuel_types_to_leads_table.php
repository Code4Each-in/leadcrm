<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AU Savers "Fuel Type" - any of Electricity, Gas, Water (see
     * LeadValidationRules::FUEL_TYPES), stored as a JSON list.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->json('fuel_types')->nullable()->after('number_of_sites');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('fuel_types');
        });
    }
};
