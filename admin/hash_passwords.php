<?php
include 'postgre.php';

// Récupérer tous les utilisateurs avec des mots de passe en clair
$query = "SELECT idutilisateur, mdputilisateur FROM utilisateur";
$result = pg_query($conn, $query);

if (!$result) {
    die("Erreur lors de la récupération des utilisateurs.");
}

while ($user = pg_fetch_assoc($result)) {
    $id = $user['idutilisateur'];
    $plain_password = $user['mdputilisateur'];

    // Hacher le mot de passe
    $hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

    // Mettre à jour le mot de passe dans la base de données
    $update_query = "UPDATE utilisateur SET mdputilisateur = $1 WHERE idutilisateur = $2";
    $update_result = pg_query_params($conn, $update_query, [$hashed_password, $id]);

    if ($update_result) {
        echo "Mot de passe de l'utilisateur ID $id haché avec succès.\n";
    } else {
        echo "Erreur lors du hachage du mot de passe pour l'utilisateur ID $id.\n";
    }
}
?>
