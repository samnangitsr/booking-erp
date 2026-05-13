<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['description' => 'Full system access']
        );
        $superAdmin->syncPermissions(Permission::pluck('id')->all());

        // Admin: everything except the multi-tenant `organization` module
        // (managing companies / branches is owner-only).
        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['description' => 'Company administrator']
        );
        $admin->syncPermissions(
            Permission::whereNotIn('module', ['organization'])->pluck('id')->all()
        );

        // Manager: operational modules + reports + own profile.
        $manager = Role::firstOrCreate(
            ['name' => 'manager', 'guard_name' => 'web'],
            ['description' => 'Branch manager']
        );
        $manager->syncPermissions(
            Permission::whereIn('module', [
                'dashboard', 'bookings', 'customers', 'properties', 'rates',
                'finance', 'marketing', 'services', 'reports', 'profile',
            ])->pluck('id')->all()
        );

        // Staff: front-desk only — view/create/edit bookings + customers,
        // check guests in / out, take payments, manage own profile.
        $staff = Role::firstOrCreate(
            ['name' => 'staff', 'guard_name' => 'web'],
            ['description' => 'Front desk / staff member']
        );
        $staff->syncPermissions(
            Permission::whereIn('name', [
                'dashboard.view',
                'bookings.view', 'bookings.create', 'bookings.edit',
                'bookings.check_in', 'bookings.check_out',
                'customers.view', 'customers.create', 'customers.edit',
                'finance.view', 'finance.create',
                'profile.view', 'profile.edit',
            ])->pluck('id')->all()
        );
    }
}
