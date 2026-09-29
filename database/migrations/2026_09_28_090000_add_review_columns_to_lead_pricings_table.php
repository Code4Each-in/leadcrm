<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MIS -> Account Manager pricing review. lead_pricings.status
     * (still a plain string) now runs draft -> published -> approved
     * | declined: "published" means MIS has finalised it and it is
     * awaiting the Account Manager's decision. These columns record
     * who made that decision and when (the decline reason itself
     * lives in Notes & Documents and lead_assignments, like every
     * other workflow note).
     */
    public function up(): void
    {
        Schema::table('lead_pricings', function (Blueprint $table) {
            $table->foreignId('reviewed_by')->nullable()->after('created_by')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('lead_pricings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn('reviewed_at');
        });
    }
};
