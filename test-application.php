<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

echo "=== AMADTECH AI - APPLICATION STATUS ===\n\n";

// 1. Check Database Connection
echo "1. DATABASE CONNECTION\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');
    echo "   ✓ Connected to: amadtech_ai\n";
} catch (Exception $e) {
    echo "   ✗ Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Check Tables
echo "\n2. DATABASE TABLES\n";
$tables = ['users', 'email_verifications', 'admin_settings', 'activity_logs', 'request_stats', 'cache', 'conversations', 'messages'];
foreach ($tables as $table) {
    $result = $pdo->query("SHOW TABLES LIKE '$table'");
    $exists = $result->rowCount() > 0 ? '✓' : '✗';
    echo "   [$exists] $table\n";
}

// 3. Check Admin User
echo "\n3. ADMIN USER\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role='admin'");
$count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
if ($count > 0) {
    $stmt = $pdo->query("SELECT email, name FROM users WHERE role='admin' LIMIT 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✓ Admin account exists: {$admin['email']}\n";
} else {
    echo "   ✗ No admin account found\n";
}

// 4. Check User Count
echo "\n4. USERS IN SYSTEM\n";
$stmt = $pdo->query("SELECT COUNT(*) as total, COUNT(CASE WHEN email_verified_at IS NOT NULL THEN 1 END) as verified FROM users");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
echo "   Total users: {$stats['total']}\n";
echo "   Verified: {$stats['verified']}\n";

// 5. Check Recent Activity
echo "\n5. ACTIVITY LOGS\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM activity_logs");
$count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
echo "   Total logs: $count\n";

// 6. Check Request Stats
echo "\n6. REQUEST STATISTICS\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM request_stats");
$count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
echo "   Total requests tracked: $count\n";

// 7. Check Environment
echo "\n7. ENVIRONMENT\n";
echo "   APP_ENV: " . env('APP_ENV') . "\n";
echo "   APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";
echo "   Session Driver: " . env('SESSION_DRIVER') . "\n";
echo "   Cache Driver: " . env('CACHE_STORE') . "\n";

// 8. Check Storage Permissions
echo "\n8. STORAGE PERMISSIONS\n";
$dirs = [
    'storage' => storage_path(),
    'bootstrap/cache' => bootstrap_path('cache'),
    'logs' => storage_path('logs')
];
foreach ($dirs as $name => $path) {
    $writable = is_writable($path) ? '✓' : '✗';
    echo "   [$writable] $name\n";
}

echo "\n=== STATUS COMPLETE ===\n";
?>
