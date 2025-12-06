<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');

// Modifier user_id pour le rendre nullable
$sql = "ALTER TABLE email_verifications MODIFY `user_id` BIGINT UNSIGNED NULL;";

try {
    $pdo->exec($sql);
    echo 'Colonne user_id rendue nullable avec succès';
} catch (Exception $e) {
    echo 'Erreur: ' . $e->getMessage();
}
?>
