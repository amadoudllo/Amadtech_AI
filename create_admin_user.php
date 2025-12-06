<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');

// Créer un utilisateur admin
$email = 'admin@amadtech.com';
$password = password_hash('Admin@2025', PASSWORD_BCRYPT);
$name = 'Administrateur';

$sql = "INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at) 
        VALUES ('$name', '$email', '$password', 'admin', NOW(), NOW(), NOW())
        ON DUPLICATE KEY UPDATE role='admin', email_verified_at=NOW()";

try {
    $pdo->exec($sql);
    echo 'Utilisateur admin créé/mis à jour avec succès';
    echo "\n\nIdentifiants admin:\n";
    echo "Email: $email\n";
    echo "Mot de passe: Admin@2025\n";
} catch (Exception $e) {
    echo 'Erreur: ' . $e->getMessage();
}
?>
