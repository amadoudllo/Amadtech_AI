<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');

// Ajouter les colonnes is_active et is_blocked s'ils n'existent pas
try {
    $sql = "ALTER TABLE users 
            ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 AFTER `role`,
            ADD COLUMN `is_blocked` TINYINT(1) DEFAULT 0 AFTER `is_active`;";
    $pdo->exec($sql);
    echo 'Colonnes is_active et is_blocked ajoutées avec succès';
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo 'Les colonnes existent déjà';
    } else {
        echo 'Erreur: ' . $e->getMessage();
    }
}
?>
