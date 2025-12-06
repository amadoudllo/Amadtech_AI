<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');
$pdo->exec('DROP TABLE IF EXISTS request_stats');
$sql = 'CREATE TABLE request_stats (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED, endpoint VARCHAR(255), method VARCHAR(10), status_code INT, response_time INT, response_time_ms INT DEFAULT 0, ip_address VARCHAR(45), user_agent TEXT, prompt_tokens INT DEFAULT 0, completion_tokens INT DEFAULT 0, model VARCHAR(255), success TINYINT(1) DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, INDEX idx_created_at (created_at), INDEX idx_user_id (user_id), INDEX idx_success (success)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
$pdo->exec($sql);
echo 'OK';
?>
