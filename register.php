<?php
require_once __DIR__ . '/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rgpd = isset($_POST['rgpd']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Vous devez rentrer une adresse mail valide.';
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/', $password)) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères, une majuscule, un chiffre et un caractère spécial.';
    } elseif (!$rgpd) {
        $error = 'Vous devez accepter les conditions générales.';
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO utilisateur (nomutilisateur, prenomutilisateur, emailutilisateur, mdputilisateur, idrole, dateinscription) VALUES (:nom, :prenom, :email, :mdp, 2, CURRENT_DATE)";

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':mdp' => $hashed_password,
            ]);
            $success = 'Compte créé avec succès. Vous pouvez maintenant vous connecter.';
        } catch (PDOException $e) {
            $error = 'Erreur lors de la création du compte.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public_css/register.css">
    <title>Créer un compte</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <h1>Créer un compte</h1>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" id="prenom" required>
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" required>
            <label for="email">Adresse e-mail :</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Mot de passe :</label>
            <div class="password-container">
                <input type="password" name="password" id="password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility()">👀</span>
            </div>
            <label class="checkbox-label">
                <input type="checkbox" name="rgpd" required>
                J'accepte les <a href="conditions.php" target="_blank">conditions générales</a>
            </label>

            <button type="submit">Créer un compte</button>
        </form>
        <script>
            function togglePasswordVisibility() {
                const passwordInput = document.getElementById('password');
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
            }
        </script>
    </div>
</body>
</html>
