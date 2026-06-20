<?php
// motdepasse-oublie.php
include 'postgre.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Vérifier si l'adresse existe
    $query = "SELECT idutilisateur FROM utilisateur WHERE emailutilisateur = $1";
    $result = pg_query_params($conn, $query, [$email]);

    if ($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);
        $token = bin2hex(random_bytes(32));

        // Enregistrer le token dans une table temporaire (à créer) ou directement dans la table utilisateur (temporairement)
        $query = "UPDATE utilisateur SET token_reinit = $1, token_expiration = NOW() + interval '1 hour' WHERE idutilisateur = $2";
        pg_query_params($conn, $query, [$token, $user['idutilisateur']]);

        // Envoyer l’e-mail de réinitialisation
        $resetLink = "http://tonsite.com/reinitialisation.php?token=$token";
        // mail($email, "Réinitialisation du mot de passe", "Clique sur ce lien pour réinitialiser ton mot de passe : $resetLink");

        $message = "Un e-mail de réinitialisation a été envoyé si cette adresse est enregistrée.";
    } else {
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
