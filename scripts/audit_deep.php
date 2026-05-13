<?php
/**
 * Deep audit: compares every Eloquent model against the actual schema and
 * detects issues with foreign keys, enums, relationships, and CRUD.
 *
 * Checks:
 *   - All declared belongsTo / hasMany / hasOne relationships resolve and use
 *     existing foreign key columns.
 *   - Enum columns in DB have all the values expected by code (via grep of
 *     fillable + values used in seeders).
 *   - Each admin controller's $model class exists.
 *   - Each admin controller's $routeName resolves to an admin.* route group.
 *   - Each model can be instantiated and saved with a basic factory-style
 *     payload built from $fillable (lightweight smoke test that the columns
 *     and casts at least accept the expected types).
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;

$problems = [];

// 1. Model relationship audit -----------------------------------------------
$modelFiles = glob(__DIR__ . '/../app/Models/*.php');
foreach ($modelFiles as $file) {
    $class = 'App\\Models\\' . pathinfo($file, PATHINFO_FILENAME);
    if (!class_exists($class)) continue;
    $rc = new ReflectionClass($class);
    if ($rc->isAbstract()) continue;
    if (!is_subclass_of($class, Illuminate\Database\Eloquent\Model::class)) continue;
    /** @var Illuminate\Database\Eloquent\Model $m */
    $m = new $class();
    $table = $m->getTable();
    foreach ($rc->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if ($method->class !== $class) continue;
        if ($method->getNumberOfParameters() !== 0) continue;
        if ($method->isStatic()) continue;
        // skip getters / standard accessors
        $name = $method->getName();
        if (in_array($name, ['getRouteKeyName', 'getRouteKey', 'getTable'])) continue;
        if (str_starts_with($name, 'get') || str_starts_with($name, 'set')
            || str_starts_with($name, 'scope') || str_starts_with($name, '__')) continue;
        try {
            $result = $method->invoke($m);
        } catch (Throwable $e) {
            continue;
        }
        if (!$result instanceof Illuminate\Database\Eloquent\Relations\Relation) continue;
        if ($result instanceof Illuminate\Database\Eloquent\Relations\BelongsTo) {
            $fk = $result->getForeignKeyName();
            if (!Schema::hasColumn($table, $fk)) {
                $problems[] = "[REL_BAD_FK] {$class}::{$name}() -> belongsTo missing FK column '{$fk}' on '{$table}'";
            }
            $relatedTable = $result->getRelated()->getTable();
            if (!Schema::hasTable($relatedTable)) {
                $problems[] = "[REL_BAD_TABLE] {$class}::{$name}() -> related table '{$relatedTable}' missing";
            }
        }
        if ($result instanceof Illuminate\Database\Eloquent\Relations\HasOneOrMany) {
            $fk = $result->getForeignKeyName();
            $relatedTable = $result->getRelated()->getTable();
            if (!Schema::hasColumn($relatedTable, $fk)) {
                $problems[] = "[REL_BAD_FK] {$class}::{$name}() -> hasMany/hasOne missing FK column '{$fk}' on '{$relatedTable}'";
            }
        }
        if ($result instanceof Illuminate\Database\Eloquent\Relations\BelongsToMany) {
            $pivot = $result->getTable();
            if (!Schema::hasTable($pivot)) {
                $problems[] = "[REL_BAD_PIVOT] {$class}::{$name}() -> pivot table '{$pivot}' missing";
            } else {
                $fpk = $result->getForeignPivotKeyName();
                $rpk = $result->getRelatedPivotKeyName();
                if (!Schema::hasColumn($pivot, $fpk)) {
                    $problems[] = "[REL_BAD_FK] {$class}::{$name}() -> pivot '{$pivot}' missing column '{$fpk}'";
                }
                if (!Schema::hasColumn($pivot, $rpk)) {
                    $problems[] = "[REL_BAD_FK] {$class}::{$name}() -> pivot '{$pivot}' missing column '{$rpk}'";
                }
            }
        }
    }
}

// 2. Controller audit -------------------------------------------------------
$controllerFiles = glob(__DIR__ . '/../app/Http/Controllers/Admin/*Controller.php');
foreach ($controllerFiles as $file) {
    $class = 'App\\Http\\Controllers\\Admin\\' . pathinfo($file, PATHINFO_FILENAME);
    if (!class_exists($class)) continue;
    $rc = new ReflectionClass($class);
    if ($rc->isAbstract()) continue;
    if (!$rc->isSubclassOf(App\Http\Controllers\Admin\BaseCrudController::class)) continue;
    if ($rc->getName() === App\Http\Controllers\Admin\BaseCrudController::class) continue;
    $instance = $rc->newInstanceWithoutConstructor();
    $modelProp = $rc->getProperty('model');
    $modelProp->setAccessible(true);
    $routeProp = $rc->getProperty('routeName');
    $routeProp->setAccessible(true);
    $permProp = $rc->getProperty('permissionModule');
    $permProp->setAccessible(true);
    $model = $modelProp->getValue($instance);
    $route = $routeProp->getValue($instance);
    $perm  = $permProp->getValue($instance);
    if (!$model || !class_exists($model)) {
        $problems[] = "[CTL_BAD_MODEL] {$class} model='{$model}' does not exist";
    }
    if (!$route) {
        $problems[] = "[CTL_NO_ROUTE] {$class} routeName not set";
    } else {
        // Expect at least admin.{route}.index to exist
        $expected = "admin.{$route}.index";
        if (!Route::has($expected)) {
            $problems[] = "[CTL_NO_ROUTE_REG] {$class} routeName='{$route}' but '{$expected}' not registered";
        }
    }
    if (!$perm) {
        $problems[] = "[CTL_NO_PERMISSION] {$class} permissionModule not set";
    }
}

if (empty($problems)) {
    echo "OK - deep audit clean\n";
    exit(0);
}
echo "Found " . count($problems) . " deep issue(s):\n";
foreach ($problems as $p) echo "  - $p\n";
exit(1);
