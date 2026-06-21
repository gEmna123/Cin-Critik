<?php
session_start();
require_once("postgre.php");

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['critique_id'])) {
    $user_id = $_SESSION['user_id'];
    $critique_id = intval($_POST['critique_id']);
    
    // Vérifier que la critique appartient à l'utilisateur
    $check_query = "SELECT idutilisateur FROM critique WHERE idcritique = $1";
    $check_result = pg_query_params($conn, $check_query, [$critique_id]);
    $critique = pg_fetch_assoc($check_result);
    
    if ($critique && $critique['idutilisateur'] == $user_id) {
        // Supprimer la critique
        $delete_query = "DELETE FROM critique WHERE idcritique = $1";
        $result = pg_query_params($conn, $delete_query, [$critique_id]);
        
        if ($result) {
            header('Location: account.php');
            exit;
        } else {
            $error = 'Erreur lors de la suppression de la critique : ' . pg_last_error($conn);
        }
    } else {
        $error = 'Vous n\'êtes pas autorisé à supprimer cette critique.';
    }
} else {
    header('Location: account.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression de critique</title>
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
