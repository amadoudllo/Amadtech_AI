<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Get list of migration files
$migrationsPath = database_path('migrations');
$files = scandir($migrationsPath);

$migrations = [];
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..' && str_ends_with($file, '.php')) {
        $migrationName = str_replace('.php', '', $file);
        $migrations[] = $migrationName;
    }
}

// Get already migrated migrations
$alreadyMigrated = DB::table('migrations')->pluck('migration')->toArray();

// Insert missing migrations
foreach ($migrations as $migration) {
    if (!in_array($migration, $alreadyMigrated)) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => 1,
        ]);
        echo "✅ Marked as migrated: $migration\n";
    } else {
        echo "⏭️  Already migrated: $migration\n";
    }
}

echo "\n✅ Migration table synchronized!\n";
?>
