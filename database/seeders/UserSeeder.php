<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'role_id' => config('roles.super_admin'),
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
