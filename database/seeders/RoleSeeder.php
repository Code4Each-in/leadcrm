<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'id' => 1,
                'name' => 'Super Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
              [
                'id' => 3,
                'name' => 'MIS User',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Account Executive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'QA User',
                'created_at' => now(),
                'updated_at' => now(),
            ],
              [
                'id' => 6,
                'name' => 'Account Manager',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
