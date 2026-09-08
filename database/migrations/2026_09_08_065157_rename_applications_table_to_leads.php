<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('applications', 'leads');
    }

    public function down(): void
    {
        Schema::rename('leads', 'applications');
    }
};
