<?php
session_start();
require_once __DIR__ . '/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM critique WHERE idutilisateur = :id");
        $stmt->execute([':id' => $user_id]);
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE idutilisateur = :id");
        $stmt->execute([':id' => $user_id]);
        $pdo->commit();

        session_destroy();
        header('Location: index.php?message=Compte supprimé avec succès');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = 'Erreur lors de la suppression du compte.';
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
