<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');

echo "=== DATABASE VERIFICATION ===\n\n";

// Check tables
$tables = ['users', 'email_verifications', 'admin_settings', 'activity_logs', 'request_stats'];
foreach ($tables as $table) {
    $result = $pdo->query("SHOW TABLES LIKE '$table'");
    $exists = $result->rowCount() > 0 ? '✓' : '✗';
    echo "[$exists] Table: $table\n";
}

echo "\n=== ADMIN USER ===\n";
$stmt = $pdo->query("SELECT id, name, email, role, email_verified_at FROM users WHERE role='admin' LIMIT 1");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if ($admin) {
    echo "✓ Admin user found:\n";
    echo "  Email: {$admin['email']}\n";
    echo "  Name: {$admin['name']}\n";
    echo "  Email Verified: " . ($admin['email_verified_at'] ? 'Yes' : 'No') . "\n";
} else {
    echo "✗ No admin user found\n";
}

echo "\n=== ADMIN SETTINGS ===\n";
$result = $pdo->query("SELECT COUNT(*) as count FROM admin_settings");
$count = $result->fetch(PDO::FETCH_ASSOC)['count'];
echo "Total settings: $count\n";

echo "\n=== RECENT USERS ===\n";
$stmt = $pdo->query("SELECT id, name, email, role, is_active FROM users LIMIT 5");
while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "- {$user['name']} ({$user['email']}) - Role: {$user['role']}\n";
}

echo "\n=== RECENT ACTIVITY LOGS ===\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM activity_logs");
$count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
echo "Total activity logs: $count\n";

echo "\n✓ Database verification complete!\n";
?>
