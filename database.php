<?php
$host = 'localhost';
$port = '5432';
$dbname = 'projetdb';
$user = 'projet_user';
$password = 'ProjetBDWeb2025';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    die('Connexion à la base de données impossible : ' . $e->getMessage());
}
