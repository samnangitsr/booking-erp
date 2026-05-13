<?php
/**
 * Schema audit: compares every Eloquent model in app/Models against its
 * actual database table columns, reporting:
 *   - $fillable entries that reference non-existent columns
 *   - DB columns missing from $fillable (informational, system columns excluded)
 *   - Cast columns that don't exist on the table
 *   - Model -> table resolution mismatches
 *   - Tables that have no corresponding model (excluding pivots/system)
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$systemCols = ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'email_verified_at'];

// Tables in DB (excluding framework tables)
$frameworkTables = [
    'migrations', 'password_reset_tokens', 'sessions', 'cache', 'cache_locks',
    'jobs', 'job_batches', 'failed_jobs', 'sqlite_sequence',
];
$dbTables = collect(DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name"))
    ->pluck('name')
    ->reject(fn ($t) => in_array($t, $frameworkTables))
    ->values()
    ->all();

// Models
$modelFiles = glob(__DIR__ . '/../app/Models/*.php');
$modelsByTable = [];
$problems = [];

foreach ($modelFiles as $file) {
    $class = 'App\\Models\\' . pathinfo($file, PATHINFO_FILENAME);
    if (!class_exists($class)) {
        continue;
    }
    $reflection = new ReflectionClass($class);
    if ($reflection->isAbstract()) {
        continue;
    }
    if (!is_subclass_of($class, Illuminate\Database\Eloquent\Model::class)) {
        continue;
    }

    /** @var Illuminate\Database\Eloquent\Model $instance */
    $instance = new $class();
    $table = $instance->getTable();
    $modelsByTable[$table] = $class;

    if (!Schema::hasTable($table)) {
        $problems[] = "[MODEL_BAD_TABLE] {$class} -> table '{$table}' does not exist";
        continue;
    }

    $columns = Schema::getColumnListing($table);
    $fillable = $instance->getFillable();
    $casts = $instance->getCasts();

    foreach ($fillable as $col) {
        if (!in_array($col, $columns)) {
            $problems[] = "[FILLABLE_MISSING_COL] {$class} ({$table}): fillable '{$col}' has no matching column. Columns: " . implode(',', $columns);
        }
    }
    foreach (array_keys($casts) as $col) {
        if ($col === 'id') continue; // default
        if (!in_array($col, $columns) && !in_array($col, ['email_verified_at', 'password'])) {
            $problems[] = "[CAST_MISSING_COL] {$class} ({$table}): cast '{$col}' has no matching column";
        }
    }

    $missingFromFillable = array_diff(
        $columns,
        array_merge($fillable, $systemCols)
    );
    if (count($missingFromFillable) > 0) {
        $problems[] = "[COL_NOT_IN_FILLABLE] {$class} ({$table}): columns not in \$fillable: " . implode(',', $missingFromFillable);
    }
}

// Pure pivot tables managed via morphToMany / belongsToMany; no standalone
// Eloquent model is expected.
$pivotTables = ['model_has_roles', 'model_has_permissions', 'role_has_permissions'];
$tablesWithoutModel = array_diff($dbTables, array_keys($modelsByTable), $pivotTables);
foreach ($tablesWithoutModel as $t) {
    $problems[] = "[NO_MODEL] table '{$t}' has no Eloquent model";
}

if (empty($problems)) {
    echo "OK - no issues\n";
    exit(0);
}
echo "Found " . count($problems) . " issue(s):\n";
foreach ($problems as $p) echo "  - $p\n";
exit(1);
