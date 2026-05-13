<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (! $company) return;

        $branches = [
            ['code' => 'PNH', 'name' => 'Phnom Penh HQ',  'phone' => '+855 23 100 100'],
            ['code' => 'SR',  'name' => 'Siem Reap',      'phone' => '+855 63 100 100'],
            ['code' => 'SHV', 'name' => 'Sihanoukville',  'phone' => '+855 34 100 100'],
        ];

        foreach ($branches as $b) {
            Branch::firstOrCreate(
                ['branch_code' => $b['code']],
                [
                    'company_id' => $company->id,
                    'name' => $b['name'],
                    'phone' => $b['phone'],
                    'email' => strtolower($b['code']).'@bookingerp.demo',
                    'address' => $b['name'].', Cambodia',
                    'status' => 'active',
                ]
            );
        }
    }
}
