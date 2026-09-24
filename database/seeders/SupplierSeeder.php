<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            'Barbican Power',
            'BES Commercial Electricity',
            'BP Gas Marketing',
            'British Gas',
            'British Gas Lite',
            'Brook Green',
            'Conrad Energy',
            'Corona Energy',
            'Crown Gas & Power',
            'Dare Power',
            'Delta Gas & Power',
            'Drax',
            'Dyce Energy',
            'Ecotricity',
            'EDF',
            'Engie',
            'EON Next',
            'EPG Energy',
            'Equinicity Ltd',
            'Equinix Energy',
            'Fuse Energy',
            'Green Energy UK',
            'Good Energy',
            'Haven Power',
            'Hudson Energy',
            'Jelly Fish',
            'London Power',
            'Npower',
            'Octopus Energy',
            'Ovo Energy',
            'PE Solutions',
            'Rebel Energy',
            'Scottish and Southern',
            'Scottish Power',
            'SEFE Energy',
            'Shell Energy',
            'Smartest Energy',
            'SSE Energy Solutions',
            'TEM Energy',
            'Total Gas & Power',
            'TruEnergy',
            'United Gas and Power',
            'Utilita Energy',
            'Utility Warehouse',
            'Valda',
            'Voltx Power',
            'Yorkshire Gas & Power',
            'Yu Energy',
        ];

        foreach ($suppliers as $name) {
            Supplier::firstOrCreate(['name' => $name]);
        }
    }
}
