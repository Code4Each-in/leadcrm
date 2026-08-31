<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;

class AgencySeeder extends Seeder
{
    public function run()
    {
        Agency::updateOrCreate(
            [
                'agency_name' => 'AGILE ONE',
            ],
            [
                'primary_contact_name' => 'AGILE ONE',
                'primary_email' => 'admin@agileone.com',
                'phone' => '0000000000',
                'address' => 'AGILE ONE',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'zip' => '110001',
            ]
        );
    }
}
