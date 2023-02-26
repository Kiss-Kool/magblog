<?php
// Configuration de la base de données
$dbhost = 'localhost';
$dbname = 'dbname';
$dbuser = 'dbuser';
$dbpass = 'password';

// Connexion à la base de données avec PDO
try {
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    // Définir l'attribut PDO::ATTR_ERRMODE à PDO::ERRMODE_EXCEPTION permet de déclencher des exceptions en cas d'erreur.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur lors de la connexion à la base de données, on affiche le message d'erreur
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    exit;
}
