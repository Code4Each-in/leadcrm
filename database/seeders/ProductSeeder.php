<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'NFS',
        ]);

        Product::create([
            'name' => 'AF4U',
        ]);

        Product::create([
            'name' => 'AU Savers',
        ]);
    }
}
