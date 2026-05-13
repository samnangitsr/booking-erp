<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitiesSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Wi-Fi', 'bi-wifi', 'property'],
            ['Parking', 'bi-p-square', 'property'],
            ['Pool', 'bi-water', 'property'],
            ['Gym', 'bi-bicycle', 'property'],
            ['Restaurant', 'bi-cup-hot', 'property'],
            ['Air Conditioning', 'bi-snow', 'room'],
            ['TV', 'bi-tv', 'room'],
            ['Minibar', 'bi-cup-straw', 'room'],
            ['Hairdryer', 'bi-wind', 'room'],
            ['Safe', 'bi-shield-lock', 'room'],
        ];
        foreach ($items as [$name, $icon, $type]) {
            Amenity::firstOrCreate(
                ['name' => $name, 'amenity_type' => $type],
                ['icon' => $icon, 'status' => 'active']
            );
        }
    }
}
