<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('id');
            $table->tinyInteger('status')->default(1)->after('role_id'); // 1=active, 0=inactive
            $table->text('address')->nullable();
            $table->string('profile')->nullable();
            $table->softDeletes(); // deleted_at
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();

            // Foreign key
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'role_id',
                'status',
                'address',
                'profile',
                'deleted_at',
                'city',
                'state',
                'zip'
            ]);
        });
    }
}
