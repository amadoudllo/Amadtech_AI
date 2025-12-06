<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');

// Vérifier si la colonne role existe, sinon l'ajouter
try {
    $sql = "ALTER TABLE users ADD COLUMN `role` VARCHAR(20) DEFAULT 'user' AFTER `country_code`;";
    $pdo->exec($sql);
    echo 'Colonne role ajoutée avec succès';
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo 'La colonne role existe déjà';
    } else {
        echo 'Erreur: ' . $e->getMessage();
    }
}
?>
