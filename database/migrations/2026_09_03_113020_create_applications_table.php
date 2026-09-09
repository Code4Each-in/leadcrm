<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            // Product
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            // Common fields
            $table->string('company_type')->nullable();
            $table->string('company_business_name')->nullable();
            $table->date('business_start_date')->nullable();
            $table->string('business_type')->nullable();

            $table->text('business_registered_address')->nullable();
            $table->text('business_trading_address')->nullable();

            $table->boolean('same_as_registered_address')->default(false);

            $table->string('customer_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->date('date_of_birth')->nullable();

            $table->string('phone_no')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();

            // NFS / AF4U specific fields
            $table->decimal('gross_sales', 15, 2)->nullable();
            $table->decimal('funds_required', 15, 2)->nullable();
            $table->integer('funds_term_months')->nullable();
            $table->string('home_owner')->nullable();
            $table->string('vat_registered')->nullable();

            // AU Savers specific fields
            $table->text('supply_address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('number_of_sites')->nullable();
            $table->string('mpan')->nullable();
            $table->string('mprn')->nullable();
            $table->string('spid')->nullable();

            // Draft / notes
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
