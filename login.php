<?php
require_once __DIR__ . '/database.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $query = "SELECT idutilisateur, mdputilisateur, idrole, nomutilisateur, prenomutilisateur FROM utilisateur WHERE emailutilisateur = :email";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            if ($password === $user['mdputilisateur']) {
                $_SESSION['user_id'] = $user['idutilisateur'];
                $_SESSION['idrole'] = $user['idrole'];
                $_SESSION['username'] = $user['prenomutilisateur'] . ' ' . $user['nomutilisateur'];

                if ($user['idrole'] == 1) {
                    header('Location: admin.php');
                } else {
                    header('Location: account.php');
                }
                exit;
            }
            $error = 'Mot de passe incorrect.';
        } else {
            $error = 'Adresse e-mail introuvable.';
        }
    } catch (PDOException $e) {
        $error = 'Erreur de connexion.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public_css/connexion.css">
    <title>Connexion</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <h1>Connexion</h1>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="email">Adresse e-mail :</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Mot de passe :</label>
            <div class="password-container">
                <input type="password" name="password" id="password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility()">👀</span>
            </div>
            <p class="mdp-oublie"><a href="motdepasse-oublie.php">Mot de passe oublié ?</a></p>
            <button type="submit">Se connecter</button>
        </form>
        <p>Pas encore de compte ? <a href="register.php">Créer un compte</a></p>
    </div>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        }
    </script>
</body>
</html>
