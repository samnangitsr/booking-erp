<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Cambodia', 'iso_code' => 'KH', 'phone_code' => '+855', 'currency_code' => 'KHR', 'cities' => ['Phnom Penh','Siem Reap','Sihanoukville','Battambang','Kampot']],
            ['name' => 'Thailand', 'iso_code' => 'TH', 'phone_code' => '+66', 'currency_code' => 'THB', 'cities' => ['Bangkok','Chiang Mai','Phuket']],
            ['name' => 'Vietnam', 'iso_code' => 'VN', 'phone_code' => '+84', 'currency_code' => 'VND', 'cities' => ['Hanoi','Ho Chi Minh','Da Nang']],
            ['name' => 'Singapore', 'iso_code' => 'SG', 'phone_code' => '+65', 'currency_code' => 'SGD', 'cities' => ['Singapore']],
            ['name' => 'United States', 'iso_code' => 'US', 'phone_code' => '+1', 'currency_code' => 'USD', 'cities' => []],
        ];

        foreach ($countries as $c) {
            $country = Country::firstOrCreate(
                ['iso_code' => $c['iso_code']],
                ['name' => $c['name'], 'phone_code' => $c['phone_code'], 'currency_code' => $c['currency_code'], 'status' => 'active']
            );
            foreach ($c['cities'] as $city) {
                City::firstOrCreate(
                    ['country_id' => $country->id, 'slug' => Str::slug($city)],
                    ['name' => $city, 'status' => 'active']
                );
            }
        }
    }
}
