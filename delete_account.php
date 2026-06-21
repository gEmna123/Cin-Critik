<?php
session_start();
require_once("postgre.php");

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // Supprimer les critiques de l'utilisateur
    $delete_critiques = "DELETE FROM critique WHERE idutilisateur = $1";
    pg_query_params($conn, $delete_critiques, [$user_id]);
    
    // Supprimer l'utilisateur
    $delete_user = "DELETE FROM utilisateur WHERE idutilisateur = $1";
    $result = pg_query_params($conn, $delete_user, [$user_id]);
    
    if ($result) {
        // Détruire la session et rediriger
        session_destroy();
        header('Location: index.php?message=Compte supprimé avec succès');
        exit;
    } else {
        $error = 'Erreur lors de la suppression du compte : ' . pg_last_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression de compte</title>
    <link rel="stylesheet" href="public_css/account.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container">
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
