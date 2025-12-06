<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');

// Modifier la table pour rendre user_id nullable et ajouter des colonnes optionnelles
$sql = "ALTER TABLE email_verifications 
        MODIFY `user_id` BIGINT UNSIGNED NULL,
        ADD COLUMN `password_hash` VARCHAR(255) NULL,
        ADD COLUMN `name` VARCHAR(255) NULL,
        ADD COLUMN `phone` VARCHAR(20) NULL,
        ADD COLUMN `country_code` VARCHAR(5) NULL;";

try {
    $pdo->exec($sql);
    echo 'Table email_verifications modifiée avec succès';
} catch (Exception $e) {
    echo 'Erreur: ' . $e->getMessage();
}
?>
