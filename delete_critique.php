<?php
session_start();
require_once __DIR__ . '/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['critique_id'])) {
    $user_id = $_SESSION['user_id'];
    $critique_id = intval($_POST['critique_id']);

    try {
        $stmt = $pdo->prepare("SELECT idutilisateur FROM critique WHERE idcritique = :id");
        $stmt->execute([':id' => $critique_id]);
        $critique = $stmt->fetch();

        if ($critique && $critique['idutilisateur'] == $user_id) {
            $deleteStmt = $pdo->prepare("DELETE FROM critique WHERE idcritique = :id");
            $deleteStmt->execute([':id' => $critique_id]);
            header('Location: account.php');
            exit;
        }
        $error = 'Vous n'êtes pas autorisé à supprimer cette critique.';
    } catch (PDOException $e) {
        $error = 'Erreur lors de la suppression de la critique.';
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
