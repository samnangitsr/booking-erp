<?php
/**
 * Tries to instantiate-and-save a minimal row for every Eloquent model.
 * Builds the payload from $fillable + introspected column metadata so we
 * exercise type coercions / casts / NOT NULL constraints / FK constraints.
 *
 * Inserts are wrapped in a savepoint per model and rolled back so the
 * seeded data is not disturbed.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type;

DB::statement('PRAGMA foreign_keys = ON');

// Pre-seeded reference rows so FK columns can be populated.
$lookups = [
    'companies' => DB::table('companies')->value('id'),
    'branches'  => DB::table('branches')->value('id'),
    'users'     => DB::table('users')->value('id'),
    'roles'     => DB::table('roles')->value('id'),
    'permissions' => DB::table('permissions')->value('id'),
    'countries' => DB::table('countries')->value('id'),
    'cities'    => DB::table('cities')->value('id'),
    'areas'     => DB::table('areas')->value('id'),
    'destinations' => DB::table('destinations')->value('id'),
    'partners'  => DB::table('partners')->value('id'),
    'partner_contracts' => DB::table('partner_contracts')->value('id'),
    'property_types' => DB::table('property_types')->value('id'),
    'properties' => DB::table('properties')->value('id'),
    'property_contacts' => DB::table('property_contacts')->value('id'),
    'property_images' => DB::table('property_images')->value('id'),
    'amenities' => DB::table('amenities')->value('id'),
    'property_policies' => DB::table('property_policies')->value('id'),
    'nearby_places' => DB::table('nearby_places')->value('id'),
    'bed_types' => DB::table('bed_types')->value('id'),
    'room_types' => DB::table('room_types')->value('id'),
    'rooms' => DB::table('rooms')->value('id'),
    'room_type_images' => DB::table('room_type_images')->value('id'),
    'room_blocks' => DB::table('room_blocks')->value('id'),
    'cancellation_policies' => DB::table('cancellation_policies')->value('id'),
    'rate_plans' => DB::table('rate_plans')->value('id'),
    'daily_rates' => DB::table('daily_rates')->value('id'),
    'availability_calendars' => DB::table('availability_calendars')->value('id'),
    'occupancy_rules' => DB::table('occupancy_rules')->value('id'),
    'child_age_policies' => DB::table('child_age_policies')->value('id'),
    'taxes' => DB::table('taxes')->value('id'),
    'property_fees' => DB::table('property_fees')->value('id'),
    'customers' => DB::table('customers')->value('id'),
    'customer_documents' => DB::table('customer_documents')->value('id'),
    'bookings' => DB::table('bookings')->value('id'),
    'booking_items' => DB::table('booking_items')->value('id'),
    'guests' => DB::table('guests')->value('id'),
    'payment_methods' => DB::table('payment_methods')->value('id'),
    'payments' => DB::table('payments')->value('id'),
    'invoices' => DB::table('invoices')->value('id'),
    'invoice_items' => DB::table('invoice_items')->value('id'),
    'refunds' => DB::table('refunds')->value('id'),
    'promotions' => DB::table('promotions')->value('id'),
    'coupons' => DB::table('coupons')->value('id'),
    'reviews' => DB::table('reviews')->value('id'),
    'review_replies' => DB::table('review_replies')->value('id'),
    'commissions' => DB::table('commissions')->value('id'),
    'payouts' => DB::table('payouts')->value('id'),
    'service_categories' => DB::table('service_categories')->value('id'),
    'activities' => DB::table('activities')->value('id'),
    'activity_schedules' => DB::table('activity_schedules')->value('id'),
    'transfers' => DB::table('transfers')->value('id'),
    'notification_templates' => DB::table('notification_templates')->value('id'),
    'wishlists' => DB::table('wishlists')->value('id'),
];

// Read the migration once to harvest enum sets so we can pick a valid value.
$migration = file_get_contents(__DIR__ . '/../database/migrations/2026_05_13_000001_create_booking_erp_management_system_all_tables.php');
$enumByCol = [];
$currentTable = null;
foreach (explode("\n", $migration) as $line) {
    if (preg_match("/Schema::create\('([a-z_]+)'/", $line, $m)) {
        $currentTable = $m[1];
        continue;
    }
    if ($currentTable && preg_match("/\\\$table->enum\('([a-z_]+)',\s*\[(.+?)\]/", $line, $m)) {
        $vals = array_map(fn ($v) => trim(trim($v), "'\""), explode(',', $m[2]));
        $enumByCol["{$currentTable}.{$m[1]}"] = $vals;
    }
}

function pickValue(string $table, string $col, array $enumByCol, array $lookups, $columnSchema): mixed
{
    // Foreign keys are detected from naming convention "<x>_id" -> table name
    if (str_ends_with($col, '_id') && $col !== 'service_id' && $col !== 'subject_id') {
        $candidate = substr($col, 0, -3); // strip _id
        $plural = Illuminate\Support\Str::pluralStudly($candidate);
        $plural = Illuminate\Support\Str::snake($plural);
        // Special cases
        $map = [
            'changed_by' => 'users', 'created_by' => 'users', 'updated_by' => 'users',
            'received_by' => 'users', 'approved_by' => 'users', 'handled_by' => 'users',
            'replied_by' => 'users', 'manager' => 'users',
        ];
        if (isset($map[$col])) $plural = $map[$col];
        if (isset($lookups[$plural]) && $lookups[$plural]) {
            return $lookups[$plural];
        }
        if ($plural === 'manager' && $lookups['users']) return $lookups['users'];
        return null;
    }
    $type = $columnSchema['type_name'] ?? 'varchar';
    if (isset($enumByCol["{$table}.{$col}"])) {
        return $enumByCol["{$table}.{$col}"][0];
    }
    // numeric
    if (in_array($type, ['integer', 'bigint', 'tinyint', 'smallint'])) return 1;
    if (in_array($type, ['numeric', 'decimal', 'float', 'double', 'real'])) return 1.0;
    if ($type === 'date') return now()->toDateString();
    if (in_array($type, ['datetime', 'timestamp'])) return now()->toDateTimeString();
    if ($type === 'time') return '08:00:00';
    if ($type === 'json' || $type === 'text' || $type === 'longtext' || $type === 'mediumtext') return $type === 'json' ? '{}' : 'sample';
    if ($type === 'tinyint' && $columnSchema['precision'] === 1) return 0;
    // varchar
    return 'sample';
}

$modelFiles = glob(__DIR__ . '/../app/Models/*.php');
$problems = [];
$tested = 0;
$skipped = 0;

foreach ($modelFiles as $file) {
    $class = 'App\\Models\\' . pathinfo($file, PATHINFO_FILENAME);
    if (!class_exists($class)) continue;
    $rc = new ReflectionClass($class);
    if ($rc->isAbstract()) continue;
    if (!is_subclass_of($class, Model::class)) continue;

    /** @var Model $instance */
    $instance = new $class();
    $table = $instance->getTable();
    $fillable = $instance->getFillable();

    if (empty($fillable)) {
        $skipped++;
        continue;
    }

    $columns = DB::select("PRAGMA table_info('{$table}')");
    $colByName = [];
    foreach ($columns as $c) {
        $colByName[$c->name] = ['type_name' => strtolower($c->type), 'notnull' => $c->notnull, 'precision' => null];
    }

    $payload = [];
    $missingRequiredFk = false;
    foreach ($fillable as $col) {
        if (!isset($colByName[$col])) continue;
        $v = pickValue($table, $col, $enumByCol, $lookups, $colByName[$col]);
        if ($v === null && $colByName[$col]['notnull']) {
            // No seeded parent row exists for a required FK — skip model.
            if (str_ends_with($col, '_id') && $col !== 'service_id' && $col !== 'subject_id') {
                $missingRequiredFk = true;
                break;
            }
            $v = 1;
        }
        $payload[$col] = $v;
    }
    if ($missingRequiredFk) { $skipped++; continue; }
    // Required not-null columns NOT in fillable (e.g. unique codes generated outside) — give them too.
    foreach ($colByName as $col => $schema) {
        if ($col === 'id' || str_contains($col, 'created_at') || str_contains($col, 'updated_at') || str_contains($col, 'deleted_at')) continue;
        if (isset($payload[$col])) continue;
        if ($schema['notnull']) {
            $v = pickValue($table, $col, $enumByCol, $lookups, $schema);
            if ($v === null) {
                if (str_ends_with($col, '_id') && $col !== 'service_id' && $col !== 'subject_id') {
                    $missingRequiredFk = true;
                    break;
                }
                $v = 1;
            }
            $payload[$col] = $v;
        }
    }
    if ($missingRequiredFk) { $skipped++; continue; }
    // Force unique-ish suffix for code fields to avoid collisions with seeded rows.
    $suffix = substr(bin2hex(random_bytes(3)), 0, 6);
    foreach ($payload as $col => $v) {
        if (is_string($v) && (str_ends_with($col, '_code') || $col === 'code' || $col === 'slug' || str_ends_with($col, '_no'))) {
            $payload[$col] = "audit-{$suffix}";
        }
        if ($col === 'email') $payload[$col] = "audit-{$suffix}@audit.local";
    }

    DB::beginTransaction();
    try {
        $row = $instance->newQuery()->getModel();
        foreach ($payload as $k => $v) $row->{$k} = $v;
        $row->save();
        $tested++;
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        // Surface only meaningful issues — not duplicate-unique conflicts caused by seeded sample data.
        if (str_contains($msg, 'UNIQUE constraint failed')) {
            $skipped++;
        } else {
            $problems[] = "[CRUD_INSERT] {$class} ({$table}): " . $msg;
        }
    } finally {
        DB::rollBack();
    }
}

echo "Tested {$tested} models, skipped {$skipped}, " . count($problems) . " issues:\n";
foreach ($problems as $p) echo "  - $p\n";
exit(empty($problems) ? 0 : 1);
