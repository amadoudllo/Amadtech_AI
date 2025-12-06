<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');
$sql = "CREATE TABLE IF NOT EXISTS cache (
  `key` VARCHAR(255) COLLATE utf8mb4_unicode_ci PRIMARY KEY,
  `value` LONGTEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` INT NOT NULL
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB";
$pdo->exec($sql);
echo 'Table cache créée avec succès';
?>
