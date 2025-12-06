<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');

$sql = "CREATE TABLE IF NOT EXISTS request_stats (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED,
  `endpoint` VARCHAR(255),
  `method` VARCHAR(10),
  `status_code` INT,
  `response_time` INT,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`user_id`) REFERENCES users(`id`) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB";

try {
    $pdo->exec($sql);
    echo 'Table request_stats creee avec succes';
} catch (Exception $e) {
    echo 'Erreur: ' . $e->getMessage();
}
?>
