<?php
require_once __DIR__ . '/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    try {
        $stmt = $pdo->prepare("SELECT idutilisateur FROM utilisateur WHERE emailutilisateur = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $updateStmt = $pdo->prepare("UPDATE utilisateur SET token_reinit = :token, token_expiration = NOW() + interval '1 hour' WHERE idutilisateur = :id");
            $updateStmt->execute([':token' => $token, ':id' => $user['idutilisateur']]);
        }
        $message = "Un e-mail de réinitialisation a été envoyé si cette adresse est enregistrée.";
    } catch (PDOException $e) {
        $message = "Un e-mail de réinitialisation a été envoyé si cette adresse est enregistrée.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="public_css/account.css">
    <link rel="stylesheet" href="public_css/connexion.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">    
        <h1>Réinitialisation du mot de passe</h1>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php else: ?>
            <form method="POST">
                <label for="email">Entrez votre adresse e-mail :</label>
                <input type="email" name="email" id="email" required>
                <button type="submit">Envoyer le lien</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
