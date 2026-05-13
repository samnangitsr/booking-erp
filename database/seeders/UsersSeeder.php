<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $branch = Branch::first();

        $superAdmin = User::firstOrCreate(
            ['email' => 'super@bookingerp.demo'],
            [
                'company_id' => null,
                'branch_id' => null,
                'name' => 'Super Admin',
                'phone' => '+855 12 000 000',
                'password' => Hash::make('password'),
                'user_type' => 'super_admin',
                'status' => 'active',
            ]
        );
        $superAdmin->syncRoles(['super_admin']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@bookingerp.demo'],
            [
                'company_id' => $company?->id,
                'branch_id' => $branch?->id,
                'name' => 'Admin',
                'phone' => '+855 12 111 111',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'status' => 'active',
            ]
        );
        $admin->syncRoles(['admin']);

        $manager = User::firstOrCreate(
            ['email' => 'manager@bookingerp.demo'],
            [
                'company_id' => $company?->id,
                'branch_id' => $branch?->id,
                'name' => 'Branch Manager',
                'phone' => '+855 12 222 222',
                'password' => Hash::make('password'),
                'user_type' => 'staff',
                'status' => 'active',
            ]
        );
        $manager->syncRoles(['manager']);

        $staff = User::firstOrCreate(
            ['email' => 'staff@bookingerp.demo'],
            [
                'company_id' => $company?->id,
                'branch_id' => $branch?->id,
                'name' => 'Front Desk Staff',
                'phone' => '+855 12 333 333',
                'password' => Hash::make('password'),
                'user_type' => 'staff',
                'status' => 'active',
            ]
        );
        $staff->syncRoles(['staff']);
    }
}
