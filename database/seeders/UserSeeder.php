<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get Super Admin role ID safely
        $roleId = DB::table('roles')->where('name', 'Super Admin')->value('id');

        User::create([
            'role_id' => 1,
            'status' => 1,
            'name' => 'Super admin',
            'email' => 'superadmin@gmail.com',
            'date_of_birth' => null,
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'address' => null,
            'profile' => null,
            'city' => null,
            'state' => null,
            'zip' => null,
        ]);
    }
}
