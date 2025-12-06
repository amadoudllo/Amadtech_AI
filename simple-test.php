<?php
// Simple direct test without Ctrl+C issues
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');

// Check admin settings table
$result = $pdo->query("DESCRIBE admin_settings");
$columns = $result->fetchAll();

echo "ADMIN_SETTINGS TABLE STRUCTURE:\n";
foreach ($columns as $col) {
    echo "  - {$col['Field']} ({$col['Type']})\n";
}

// Check if settings exist
$result = $pdo->query("SELECT * FROM admin_settings LIMIT 1");
$setting = $result->fetch();

if ($setting) {
    echo "\nSETTING EXAMPLE:\n";
    echo "  Key: " . $setting['key'] . "\n";
    echo "  Value: " . substr($setting['value'], 0, 50) . "...\n";
} else {
    echo "\nNo settings found yet.\n";
}

// Check admin user
$result = $pdo->query("SELECT email, name, role FROM users WHERE role='admin' LIMIT 1");
$admin = $result->fetch();

if ($admin) {
    echo "\nADMIN USER FOUND:\n";
    echo "  Email: {$admin['email']}\n";
    echo "  Name: {$admin['name']}\n";
} else {
    echo "\nNo admin user found.\n";
}

echo "\n✓ Test complete.\n";
?>
