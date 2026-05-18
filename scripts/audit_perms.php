<?php
/**
 * Audit: every admin controller's $permissionModule has matching seeded
 * permission rows (module.create / module.update / module.delete / module.view).
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$seededPermissions = DB::table('permissions')->pluck('name')->all();
$missing = [];

$controllerFiles = glob(__DIR__ . '/../app/Http/Controllers/Admin/*Controller.php');
foreach ($controllerFiles as $file) {
    $class = 'App\\Http\\Controllers\\Admin\\' . pathinfo($file, PATHINFO_FILENAME);
    if (!class_exists($class)) continue;
    $rc = new ReflectionClass($class);
    if ($rc->isAbstract()) continue;
    if (!$rc->isSubclassOf(App\Http\Controllers\Admin\BaseCrudController::class)) continue;
    if ($rc->getName() === App\Http\Controllers\Admin\BaseCrudController::class) continue;
    $instance = $rc->newInstanceWithoutConstructor();
    $permProp = $rc->getProperty('permissionModule');
    $permProp->setAccessible(true);
    $module = $permProp->getValue($instance);
    if (!$module) continue;
    // BaseCrudController calls authorizeAbility() with these four names.
    foreach (['view', 'create', 'edit', 'delete'] as $ability) {
        $name = "{$module}.{$ability}";
        if (!in_array($name, $seededPermissions)) {
            $missing[] = "{$class} expects '{$name}' but it is not seeded";
        }
    }
}

if (empty($missing)) {
    echo "OK - all controller permissions seeded\n";
    exit(0);
}
echo "Missing seeded permissions:\n";
foreach ($missing as $m) echo "  - $m\n";
exit(1);
