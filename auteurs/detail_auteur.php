<?php
// filepath: /home/etu/test99999/auteurs/detail_auteur.php

session_start();
include '../postgre.php';

$id = intval($_GET['id']);

// Récupération des détails de l'auteur
$query_author = "SELECT nomauteur, prenomauteur, datenaissanceauteur FROM auteur WHERE idauteur = $1";
$result_author = pg_query_params($conn, $query_author, [$id]);
$author = pg_fetch_assoc($result_author);

if (!$author) {
    die("Auteur introuvable.");
}

// Récupération des œuvres de l'auteur
$query_works = "SELECT idoeuvre, titreoeuvre, descriptionoeuvre FROM oeuvre WHERE idauteur = $1";
$result_works = pg_query_params($conn, $query_works, [$id]);
$works = pg_fetch_all($result_works);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($author['prenomauteur'] . ' ' . $author['nomauteur']); ?></title>
    <link rel="stylesheet" href="css/detail_auteur.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="detail-container">
        <!-- Image de l'auteur en arrière-plan -->
        <div class="detail-header" style="background-image: url('images/<?php echo $id; ?>.jpg');">
            <h1><?php echo htmlspecialchars($author['prenomauteur'] . ' ' . $author['nomauteur']); ?></h1>
        </div>

        <!-- Informations principales -->
        <div class="detail-content">
            <p><strong>Date de naissance :</strong> <?php echo htmlspecialchars($author['datenaissanceauteur']); ?></p>
        </div>

        <!-- Liste des œuvres de l'auteur -->
        <div class="detail-works">
            <h2>Œuvres de l'auteur</h2>
            <?php if ($works): ?>
                <ul>
                    <?php foreach ($works as $work): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($work['titreoeuvre']); ?></strong> - 
                            <?php echo htmlspecialchars($work['descriptionoeuvre']); ?>
                            <a href="../oeuvres/detail.php?id=<?php echo $work['idoeuvre']; ?>" class="btn">Voir les détails</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucune œuvre disponible pour cet auteur.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>