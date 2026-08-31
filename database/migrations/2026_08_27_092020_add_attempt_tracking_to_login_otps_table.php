<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('login_otps', function (Blueprint $table) {

            $table->unsignedTinyInteger('verification_attempts')
                ->default(0)
                ->after('otp');

            $table->unsignedTinyInteger('resend_count')
                ->default(0)
                ->after('verification_attempts');

            $table->timestamp('locked_until')
                ->nullable()
                ->after('expires_at');

        });
    }

    public function down(): void
    {
        Schema::table('login_otps', function (Blueprint $table) {

            $table->dropColumn([
                'verification_attempts',
                'resend_count',
                'locked_until',
            ]);

        });
    }
};
