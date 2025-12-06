<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');
$sql = 'CREATE TABLE activity_logs (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED, action VARCHAR(255), description TEXT, ip_address VARCHAR(45), user_agent TEXT, model VARCHAR(255), created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, INDEX idx_user_id (user_id), INDEX idx_created_at (created_at)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
$pdo->exec($sql);
echo 'OK';
?>
