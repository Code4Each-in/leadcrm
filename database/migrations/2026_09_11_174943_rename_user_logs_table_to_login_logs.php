<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('user_logs', 'login_logs');
    }

    public function down(): void
    {
        Schema::rename('login_logs', 'user_logs');
    }
};
