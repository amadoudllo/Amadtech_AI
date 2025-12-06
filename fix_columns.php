<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');

try {
    $sql = "ALTER TABLE users ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 AFTER `role`";
    $pdo->exec($sql);
} catch (Exception $e) {
}

try {
    $sql = "ALTER TABLE users ADD COLUMN `is_blocked` TINYINT(1) DEFAULT 0 AFTER `is_active`";
    $pdo->exec($sql);
} catch (Exception $e) {
}

echo 'Colonnes ajoutees';
?>
