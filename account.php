<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/database.php';

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT idutilisateur, nomutilisateur, prenomutilisateur, emailutilisateur, mdputilisateur FROM utilisateur WHERE idutilisateur = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    die('Erreur lors de la récupération du profil : ' . htmlspecialchars($e->getMessage()));
}

if (!$user) {
    die("Utilisateur non trouvé.");
}

if (isset($_POST['password'])) {
    $password = $_POST['password'];
    if (password_verify($password, $user['mdputilisateur']) || $password === $user['mdputilisateur']) {
        echo "Mot de passe vérifié !";
    } else {
        echo "Mot de passe incorrect.";
    }
}

try {
    $stmt = $pdo->prepare("SELECT idcritique, contenucritique, datecritique FROM critique WHERE idutilisateur = :id ORDER BY datecritique DESC");
    $stmt->execute([':id' => $user_id]);
    $critiques = $stmt->fetchAll() ?: [];
} catch (PDOException $e) {
    $critiques = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte</title>
    <link rel="stylesheet" href="public_css/account.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="account-container">
        <h1>Bienvenue, <?= htmlspecialchars($user['nomutilisateur'] . ' ' . $user['prenomutilisateur']) ?> !</h1>

        <div class="account-section">
            <h2>Informations personnelles</h2>
            <p><strong>Email :</strong> <?= htmlspecialchars($user['emailutilisateur']) ?></p>
        </div>

        <div class="account-section">
            <h2>Mes critiques</h2>
            <?php if ($critiques): ?>
                <ul class="critique-list">
                    <?php foreach ($critiques as $critique): ?>
                        <li class="critique-item">
                            <div class="critique-info">
                                <span class="critique-id">#<?= $critique['idcritique'] ?> - <?= htmlspecialchars($critique['contenucritique']) ?></span>
                                <span class="critique-date"><?= htmlspecialchars($critique['datecritique']) ?></span>
                            </div>
                            <form action="delete_critique.php" method="post" class="inline-form">
                                <input type="hidden" name="critique_id" value="<?= $critique['idcritique'] ?>">
                                <button type="submit" class="btn-danger-small">Supprimer</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Vous n'avez pas encore rédigé de critique.</p>
            <?php endif; ?>
        </div>

        <div class="account-section danger-zone">
            <h2>Zone dangereuse</h2>
            <form action="delete_account.php" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
                <button type="submit" class="btn btn-danger">Supprimer mon compte</button>
            </form>
        </div>
    </div>
</body>
</html>
