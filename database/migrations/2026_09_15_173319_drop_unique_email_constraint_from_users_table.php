<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The original users_email_unique index enforces uniqueness
     * across *every* row, including soft-deleted ones - so deleting
     * a user and creating a new one with the same email hit a DB
     * duplicate-key error, since the old row is still physically
     * present with deleted_at set. Uniqueness among active users is
     * enforced instead at the application layer (see
     * UserController@store/@update, which scope the `unique` rule to
     * deleted_at IS NULL). A plain index is kept so email lookups
     * stay fast.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_email_index');
            $table->unique('email');
        });
    }
};
