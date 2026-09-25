<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pricing records for a Lead (AU Savers only). A lead can have many
     * pricing records over time - a new one is created rather than
     * overwriting the previous one, so history is always retained. The
     * most recent row (by created_at) is treated as the current
     * pricing - see Lead::currentPricing().
     *
     * Rate fields (sc_pence_per_day, unit_rate_pence,
     * night_unit_rate_pence, evening_unit_rate_pence, uplift_pence)
     * are stored exactly as entered, in pence - converted to pounds
     * only at calculation time (see App\Support\LeadPricingCalculator).
     */
    public function up(): void
    {
        Schema::create('lead_pricings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')
                ->constrained('leads')
                ->cascadeOnDelete();

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->restrictOnDelete();

            // 'single' or 'multi' (day/evening/night) rate contract.
            $table->string('rate_type');

            $table->unsignedSmallInteger('contract_term_months');

            // Estimated Annual Consumption. Always populated - either
            // entered directly (single-rate) or the sum of the three
            // consumption columns below (multi-rate).
            $table->decimal('total_eac_kwh', 12, 2);

            $table->decimal('day_consumption_kwh', 12, 2)->nullable();
            $table->decimal('evening_consumption_kwh', 12, 2)->nullable();
            $table->decimal('night_consumption_kwh', 12, 2)->nullable();

            $table->decimal('sc_pence_per_day', 10, 4);

            // Single-rate contracts use this as the one unit rate;
            // multi-rate contracts use it as the Day rate.
            $table->decimal('unit_rate_pence', 10, 4);
            $table->decimal('night_unit_rate_pence', 10, 4)->nullable();
            $table->decimal('evening_unit_rate_pence', 10, 4)->nullable();

            $table->decimal('uplift_pence', 10, 4);

            // Snapshot of the calculated Annual Spend (in pounds) at
            // the time this record was saved, so historical records
            // don't drift if the calculation logic changes later.
            $table->decimal('annual_spend', 12, 2);

            // Mirrors leads.status - plain string, draft/published,
            // one-way transition enforced in application code.
            $table->string('status')->default('draft');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['lead_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_pricings');
    }
};
