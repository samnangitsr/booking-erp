<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

/**
 * Seeds permissions using the same consolidated module names that the
 * admin controllers and the left sidebar reference (`bookings`, `properties`,
 * `rates`, `marketing`, `services`, `locations`, `finance`, `organization`,
 * `customers`, `partners`, `users`, `roles`, `reports`, `settings`,
 * `profile`, `dashboard`). The migration creates 71 tables but routes /
 * controllers group them into ~15 functional areas, so permissions are
 * issued at that granularity to keep role assignment manageable.
 */
class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'dashboard' => ['view'],
            'organization' => ['view', 'create', 'edit', 'delete'],
            'users' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'partners' => ['view', 'create', 'edit', 'delete'],
            'properties' => ['view', 'create', 'edit', 'delete'],
            'rates' => ['view', 'create', 'edit', 'delete'],
            'customers' => ['view', 'create', 'edit', 'delete'],
            'bookings' => ['view', 'create', 'edit', 'delete', 'cancel', 'check_in', 'check_out'],
            'finance' => ['view', 'create', 'edit', 'delete'],
            'marketing' => ['view', 'create', 'edit', 'delete'],
            'services' => ['view', 'create', 'edit', 'delete'],
            'locations' => ['view', 'create', 'edit', 'delete'],
            'reports' => ['view', 'create', 'edit', 'delete'],
            'settings' => ['view', 'create', 'edit', 'delete'],
            'profile' => ['view', 'edit'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ['name' => $module.'.'.$action, 'guard_name' => 'web'],
                    [
                        'module' => $module,
                        'description' => ucfirst($action).' '.str_replace('_', ' ', $module),
                    ]
                );
            }
        }
    }
}
