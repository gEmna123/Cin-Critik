
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Connexion à la base de données PostgreSQL
require_once("postgre.php");

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Récupération des données utilisateur
$user_id = $_SESSION['user_id'];
$query = "SELECT idutilisateur, nomutilisateur, prenomutilisateur, emailutilisateur, mdputilisateur FROM utilisateur WHERE idutilisateur = $1";
$result = pg_query_params($conn, $query, array($user_id));
$user = pg_fetch_assoc($result);

// Vérifier si l'utilisateur existe dans la base de données
if (!$user) {
    die("Utilisateur non trouvé.");
}

// Si l'utilisateur est trouvé, vérifier le mot de passe (en cas de connexion)
if (isset($_POST['password'])) {
    $password = $_POST['password'];

    // Comparer le mot de passe avec celui stocké dans la base de données
    if (password_verify($password, $user['mdputilisateur'])) {
        // Mot de passe correct
        echo "Mot de passe vérifié !";
    } else {
        // Mot de passe incorrect
        echo "Mot de passe incorrect.";
    }
}

// Récupération des critiques de l'utilisateur
$query_critiques = "SELECT idcritique, contenucritique, datecritique FROM critique WHERE idutilisateur = $1 ORDER BY datecritique DESC";
$result_critiques = pg_query_params($conn, $query_critiques, array($user_id));
$critiques = pg_fetch_all($result_critiques);


if(!$critiques) {
	$critiques = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte</title>
    <link rel="stylesheet" href="../public_css/account.css">
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
                                <span class="critique-date"><?= $critique['datecritique'] ?></span>
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
