<?php
// filepath: /home/etu/test99999/oeuvres/liste.php

session_start();
include '../postgre.php';

// Récupération des œuvres
$query_works = "SELECT idoeuvre, titreoeuvre, descriptionoeuvre FROM Oeuvre";
$result_works = pg_query($conn, $query_works);
$works = pg_fetch_all($result_works);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Œuvres</title>
    <link rel="stylesheet" href="css/liste_oeuvre.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <h1>Liste des Œuvres</h1>
    <div class="mosaic-container">
        <?php if ($works): ?>
            <?php foreach ($works as $work): ?>
                <div class="mosaic-item">
                    <!-- Vérification et affichage de l'image -->
                    <img src="images/<?php echo $work['idoeuvre']; ?>.jpg" 
                         alt="Image de <?php echo htmlspecialchars($work['titreoeuvre']); ?>" 
                         onerror="this.src='../assets/images/default.jpg';">
                    <h3><?php echo htmlspecialchars($work['titreoeuvre']); ?></h3>
                    <p><?php echo htmlspecialchars($work['descriptionoeuvre']); ?></p>
                    <a href="./detail.php?id=<?php echo $work['idoeuvre']; ?>" class="btn">Voir les détails</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune œuvre disponible.</p>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>