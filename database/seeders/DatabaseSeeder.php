<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class,
            RolesSeeder::class,
            CompanySeeder::class,
            BranchSeeder::class,
            UsersSeeder::class,
            CountriesSeeder::class,
            PropertyTypesSeeder::class,
            AmenitiesSeeder::class,
            BedTypesSeeder::class,
            PaymentMethodsSeeder::class,
            SettingsSeeder::class,
            BookingDemoSeeder::class,
            PropertiesDemoSeeder::class,
            RatesAvailabilityDemoSeeder::class,
        ]);
    }
}
