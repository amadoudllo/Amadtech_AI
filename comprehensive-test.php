<?php
/**
 * AMADTECH_AI - COMPREHENSIVE SYSTEM TEST
 * This script validates all critical system components
 */

$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');
$errors = [];
$warnings = [];
$success = [];

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║       AMADTECH_AI - COMPREHENSIVE SYSTEM TEST              ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// 1. DATABASE CONNECTION
echo "1. DATABASE CONNECTION\n";
try {
    $pdo->query("SELECT 1");
    $success[] = "Connected to amadtech_ai database";
    echo "   ✓ Connected to amadtech_ai\n";
} catch (Exception $e) {
    $errors[] = "Database connection failed: " . $e->getMessage();
    echo "   ✗ Connection failed\n";
    exit(1);
}

// 2. REQUIRED TABLES
echo "\n2. REQUIRED TABLES\n";
$requiredTables = ['users', 'email_verifications', 'admin_settings', 'activity_logs', 'request_stats'];
foreach ($requiredTables as $table) {
    $result = $pdo->query("SHOW TABLES LIKE '$table'");
    if ($result->rowCount() > 0) {
        $success[] = "Table '$table' exists";
        echo "   ✓ $table\n";
    } else {
        $errors[] = "Table '$table' is missing";
        echo "   ✗ $table (MISSING)\n";
    }
}

// 3. USERS TABLE STRUCTURE
echo "\n3. USERS TABLE COLUMNS\n";
$requiredColumns = ['id', 'email', 'password', 'role', 'is_active', 'is_blocked', 'email_verified_at'];
foreach ($requiredColumns as $column) {
    $result = $pdo->query("SHOW COLUMNS FROM users LIKE '$column'");
    if ($result->rowCount() > 0) {
        echo "   ✓ $column\n";
    } else {
        $warnings[] = "Column 'users.$column' missing";
        echo "   ⚠ $column (MISSING)\n";
    }
}

// 4. ADMIN USER CHECK
echo "\n4. ADMIN USER VALIDATION\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role='admin'");
$adminCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
if ($adminCount > 0) {
    $stmt = $pdo->query("SELECT email, name, email_verified_at FROM users WHERE role='admin' LIMIT 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $verified = $admin['email_verified_at'] ? '✓ Yes' : '✗ No';
    $success[] = "Admin user exists: {$admin['email']}";
    echo "   ✓ Admin user: {$admin['email']}\n";
    echo "   • Name: {$admin['name']}\n";
    echo "   • Email verified: $verified\n";
} else {
    $errors[] = "No admin user found in database";
    echo "   ✗ No admin user found\n";
}

// 5. USER STATISTICS
echo "\n5. USER STATISTICS\n";
$stmt = $pdo->query("SELECT 
    COUNT(*) as total,
    COUNT(CASE WHEN email_verified_at IS NOT NULL THEN 1 END) as verified,
    COUNT(CASE WHEN is_active = 1 THEN 1 END) as active,
    COUNT(CASE WHEN is_blocked = 1 THEN 1 END) as blocked
FROM users");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
echo "   • Total users: {$stats['total']}\n";
echo "   • Verified: {$stats['verified']}\n";
echo "   • Active: {$stats['active']}\n";
echo "   • Blocked: {$stats['blocked']}\n";

// 6. ADMIN SETTINGS TABLE
echo "\n6. ADMIN SETTINGS\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_settings");
$settingsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
$success[] = "Admin settings table accessible";
echo "   ✓ Admin settings table operational\n";
echo "   • Total settings: $settingsCount\n";

// 7. ACTIVITY LOGS
echo "\n7. ACTIVITY LOGS\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM activity_logs");
$logsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
echo "   • Total activity logs: $logsCount\n";

// 8. REQUEST STATS
echo "\n8. REQUEST STATISTICS\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM request_stats");
$requestCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
echo "   • Total requests tracked: $requestCount\n";

// 9. FILE SYSTEM CHECK
echo "\n9. REQUIRED DIRECTORIES\n";
$directories = [
    'storage' => 'storage',
    'bootstrap/cache' => 'bootstrap/cache',
    'logs' => 'storage/logs',
    'sessions' => 'storage/framework/sessions'
];
foreach ($directories as $name => $path) {
    $fullPath = 'C:\\xampp\\htdocs\\Amadtech_AI\\' . $path;
    if (is_dir($fullPath) && is_writable($fullPath)) {
        $success[] = "Directory '$name' is writable";
        echo "   ✓ $name (writable)\n";
    } else {
        $warnings[] = "Directory '$name' may not be writable";
        echo "   ⚠ $name (check permissions)\n";
    }
}

// 10. VIEW FILES CHECK
echo "\n10. VIEW FILES\n";
$views = [
    'admin/dashboard.blade.php' => 'resources/views/admin/dashboard.blade.php',
    'admin/settings/index.blade.php' => 'resources/views/admin/settings/index.blade.php',
    'admin/users/index.blade.php' => 'resources/views/admin/users/index.blade.php',
    'auth/admin-login.blade.php' => 'resources/views/auth/admin-login.blade.php',
    'auth/login.blade.php' => 'resources/views/auth/login.blade.php',
    'auth/register.blade.php' => 'resources/views/auth/register.blade.php',
    'auth/verify-email.blade.php' => 'resources/views/auth/verify-email.blade.php',
];
foreach ($views as $name => $path) {
    $fullPath = 'C:\\xampp\\htdocs\\Amadtech_AI\\' . $path;
    if (file_exists($fullPath)) {
        $success[] = "View file '$name' exists";
        echo "   ✓ $name\n";
    } else {
        $errors[] = "View file '$name' missing";
        echo "   ✗ $name (MISSING)\n";
    }
}

// 11. CONTROLLER FILES CHECK
echo "\n11. CONTROLLER FILES\n";
$controllers = [
    'Admin/AdminDashboardController.php' => 'app/Http/Controllers/Admin/AdminDashboardController.php',
    'Auth/AdminLoginController.php' => 'app/Http/Controllers/Auth/AdminLoginController.php',
    'Auth/RegisterController.php' => 'app/Http/Controllers/Auth/RegisterController.php',
    'Auth/VerifyEmailController.php' => 'app/Http/Controllers/Auth/VerifyEmailController.php',
];
foreach ($controllers as $name => $path) {
    $fullPath = 'C:\\xampp\\htdocs\\Amadtech_AI\\' . $path;
    if (file_exists($fullPath)) {
        $success[] = "Controller '$name' exists";
        echo "   ✓ $name\n";
    } else {
        $errors[] = "Controller '$name' missing";
        echo "   ✗ $name (MISSING)\n";
    }
}

// 12. MODEL FILES CHECK
echo "\n12. MODEL FILES\n";
$models = [
    'User.php' => 'app/Models/User.php',
    'AdminSetting.php' => 'app/Models/AdminSetting.php',
    'ActivityLog.php' => 'app/Models/ActivityLog.php',
];
foreach ($models as $name => $path) {
    $fullPath = 'C:\\xampp\\htdocs\\Amadtech_AI\\' . $path;
    if (file_exists($fullPath)) {
        echo "   ✓ $name\n";
    } else {
        $warnings[] = "Model '$name' missing";
        echo "   ⚠ $name (MISSING)\n";
    }
}

// SUMMARY
echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                      TEST SUMMARY                          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "✓ Successes: " . count($success) . "\n";
if (count($errors) > 0) {
    echo "✗ Errors: " . count($errors) . "\n";
    foreach ($errors as $error) {
        echo "   - $error\n";
    }
}
if (count($warnings) > 0) {
    echo "⚠ Warnings: " . count($warnings) . "\n";
    foreach ($warnings as $warning) {
        echo "   - $warning\n";
    }
}

echo "\n";
if (count($errors) == 0) {
    echo "✓ SYSTEM STATUS: OPERATIONAL\n";
    echo "\nREADY FOR TESTING:\n";
    echo "  1. User Registration: http://127.0.0.1:8000/register\n";
    echo "  2. Admin Login: http://127.0.0.1:8000/admin/login\n";
    echo "  3. Chat: http://127.0.0.1:8000/chat\n";
} else {
    echo "✗ SYSTEM STATUS: ERRORS DETECTED\n";
    echo "Please fix the above errors before proceeding.\n";
}

echo "\n";
?>
