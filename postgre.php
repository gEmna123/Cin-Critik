<?php
$host = "localhost";
$port = "5432";
$dbname = "projetdb";
$user = "projet_user";
$password = "ProjetBDWeb2025";

// Connexion à la base de données
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Connexion au serveur et/ou à la base de données impossible");
}
?>
