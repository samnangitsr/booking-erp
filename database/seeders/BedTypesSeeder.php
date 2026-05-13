<?php

namespace Database\Seeders;

use App\Models\BedType;
use Illuminate\Database\Seeder;

class BedTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Single', 'Double', 'Queen', 'King', 'Twin', 'Sofa Bed'];
        foreach ($types as $t) {
            BedType::firstOrCreate(['name' => $t], ['status' => 'active']);
        }
    }
}
