<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');

try {
    $sql = "ALTER TABLE request_stats ADD COLUMN `response_time_ms` INT DEFAULT 0";
    $pdo->exec($sql);
    echo 'Colonne response_time_ms ajoutee';
} catch (Exception $e) {
    echo 'Erreur: ' . $e->getMessage();
}
?>
