<?php
/**
 * Route coverage audit: ensures every admin CRUD controller has the full set
 * of resource routes registered:
 *
 *   - admin.{routeName}.index
 *   - admin.{routeName}.create
 *   - admin.{routeName}.store
 *   - admin.{routeName}.show
 *   - admin.{routeName}.edit
 *   - admin.{routeName}.update
 *   - admin.{routeName}.destroy
 *
 * The migration template defines 71 tables. Subtracting the 3 pivot tables
 * managed by spatie-style permission pivots leaves 68 resource tables, but
 * Permission and User/Role controllers are special-cased (no public CRUD
 * routes for permissions). Anything else is expected to expose the full 7
 * routes so the admin UI is uniformly navigable.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

// Controllers that intentionally do NOT expose the full resource API.
$skipControllers = [
    'BaseCrudController',
    'DashboardController',
    'PermissionController',
    'BranchSwitcherController',
    'ProfileController',
];

$actions = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
$problems = [];
$checked = 0;

$files = glob(__DIR__ . '/../app/Http/Controllers/Admin/*.php');
foreach ($files as $file) {
    $base = pathinfo($file, PATHINFO_FILENAME);
    if (in_array($base, $skipControllers, true)) {
        continue;
    }

    $class = 'App\\Http\\Controllers\\Admin\\' . $base;
    if (!class_exists($class)) {
        $problems[] = "[CONTROLLER_NOT_LOADED] {$class}";
        continue;
    }

    $reflection = new ReflectionClass($class);
    if ($reflection->isAbstract()) {
        continue;
    }

    // Read the protected $routeName property.
    $defaults = $reflection->getDefaultProperties();
    $routeName = $defaults['routeName'] ?? null;
    if (!$routeName) {
        $problems[] = "[NO_ROUTE_NAME] {$class}: missing protected \$routeName";
        continue;
    }

    foreach ($actions as $action) {
        $routeKey = "admin.{$routeName}.{$action}";
        if (!Route::has($routeKey)) {
            $problems[] = "[ROUTE_MISSING] {$class}: route '{$routeKey}' is not registered";
        }
    }
    $checked++;
}

if (empty($problems)) {
    echo "OK - all {$checked} CRUD controllers have full resource routes\n";
    exit(0);
}
echo "Found " . count($problems) . " issue(s):\n";
foreach ($problems as $p) echo "  - $p\n";
exit(1);
