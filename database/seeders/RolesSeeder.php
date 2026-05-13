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

        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['description' => 'Company administrator']
        );
        $admin->syncPermissions(
            Permission::whereNotIn('module', ['companies'])
                ->pluck('id')->all()
        );

        $manager = Role::firstOrCreate(
            ['name' => 'manager', 'guard_name' => 'web'],
            ['description' => 'Branch manager']
        );
        $manager->syncPermissions(
            Permission::whereIn('module', [
                'dashboard','bookings','booking_items','guests','customers','customer_documents',
                'properties','room_types','rooms','daily_rates','availability_calendars',
                'payments','invoices','refunds','reviews','reports','profile',
            ])->pluck('id')->all()
        );

        $staff = Role::firstOrCreate(
            ['name' => 'staff', 'guard_name' => 'web'],
            ['description' => 'Front desk / staff member']
        );
        $staff->syncPermissions(
            Permission::whereIn('name', [
                'dashboard.view',
                'bookings.view', 'bookings.create', 'bookings.edit', 'bookings.check_in', 'bookings.check_out',
                'guests.view', 'guests.create', 'guests.edit',
                'customers.view', 'customers.create', 'customers.edit',
                'payments.view', 'payments.create',
                'profile.view', 'profile.edit',
            ])->pluck('id')->all()
        );
    }
}
