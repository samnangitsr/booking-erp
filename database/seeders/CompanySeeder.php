<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::firstOrCreate(
            ['company_code' => 'HQ001'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Booking ERP Demo Co.',
                'owner_name' => 'Demo Owner',
                'phone' => '+855 12 345 678',
                'email' => 'info@bookingerp.demo',
                'website' => 'https://bookingerp.demo',
                'address' => 'Phnom Penh, Cambodia',
                'tax_number' => '100100100',
                'status' => 'active',
            ]
        );
    }
}
