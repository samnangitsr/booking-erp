<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertyTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Hotel','Resort','Boutique','Guesthouse','Hostel','Villa','Apartment'];
        foreach ($types as $t) {
            PropertyType::firstOrCreate(
                ['slug' => Str::slug($t)],
                ['name' => $t, 'status' => 'active']
            );
        }
    }
}
