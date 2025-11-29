<?php
echo "Vérification de la base de données...\n";

try {
    // Connexion MySQL
    $pdo = new PDO('mysql:host=127.0.0.1:3306', 'root', '');
    
    // Vérifier si la DB existe
    $result = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'amadtech_ai'");
    
    if ($result->rowCount() > 0) {
        echo "✅ Base de données 'amadtech_ai' existe!\n";
    } else {
        echo "❌ Base de données 'amadtech_ai' n'existe pas!\n";
        echo "Création...\n";
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS amadtech_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✅ Base de données créée!\n";
    }
    
} catch (PDOException $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    exit(1);
}
