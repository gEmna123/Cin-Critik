<?php
require_once __DIR__ . '/../database.php';

try {
    $stmt = $pdo->query("SELECT idutilisateur, mdputilisateur FROM utilisateur");
    $users = $stmt->fetchAll() ?: [];
} catch (PDOException $e) {
    die('Erreur lors de la récupération des utilisateurs : ' . htmlspecialchars($e->getMessage()));
}

foreach ($users as $user) {
    $id = $user['idutilisateur'];
    $plain_password = $user['mdputilisateur'];
    $hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

    try {
        $update_stmt = $pdo->prepare("UPDATE utilisateur SET mdputilisateur = :hashed WHERE idutilisateur = :id");
        $update_stmt->execute([':hashed' => $hashed_password, ':id' => $id]);
        echo "Mot de passe de l'utilisateur ID $id haché avec succès.
";
    } catch (PDOException $e) {
        echo "Erreur lors du hachage du mot de passe pour l'utilisateur ID $id : " . htmlspecialchars($e->getMessage()) . "
";
    }
}
?>
