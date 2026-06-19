<?php
// filepath: /home/etu/test99999/auteurs/liste_auteur.php

session_start();
include '../postgre.php'; // Connexion à la base de données

// Récupération des auteurs
$query_authors = "SELECT idauteur, nomauteur, prenomauteur, datenaissanceauteur FROM auteur";
$result = pg_query($conn, $query_authors);  // Utilisation de pg_query() pour exécuter la requête

// Vérification de la réussite de la requête
if (!$result) {
    die("Erreur lors de la récupération des auteurs : " . pg_last_error($conn));
}

// Récupération des résultats sous forme de tableau associatif
$authors = pg_fetch_all($result);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Auteurs</title>
    <link rel="stylesheet" href="css/liste_auteur.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <h1>Liste des Auteurs</h1>
    <div class="mosaic-container">
        <?php if ($authors): ?>
            <?php foreach ($authors as $author): ?>
                <div class="mosaic-item">
                    <!-- Image de l'auteur -->
                    <img src="images/<?php echo $author['idauteur']; ?>.jpg" 
                         alt="Image de <?php echo htmlspecialchars($author['prenomauteur'] . ' ' . $author['nomauteur']); ?>" 
                         onerror="this.src='../assets/images/default_author.jpg';">
                    <h3><?php echo htmlspecialchars($author['prenomauteur'] . ' ' . $author['nomauteur']); ?></h3>
                    <p><strong>Date de naissance :</strong> <?php echo htmlspecialchars($author['datenaissanceauteur']); ?></p>
                    <a href="./detail_auteur.php?id=<?php echo $author['idauteur']; ?>" class="btn">Voir les détails</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun auteur disponible.</p>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
