<?php
session_start();
require_once __DIR__ . '/../database.php';

try {
    $stmt = $pdo->query("SELECT idauteur, nomauteur, prenomauteur, datenaissanceauteur FROM auteur");
    $authors = $stmt->fetchAll() ?: [];
} catch (PDOException $e) {
    die('Erreur lors de la récupération des auteurs : ' . htmlspecialchars($e->getMessage()));
}
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
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <h1>Liste des Auteurs</h1>
    <div class="mosaic-container">
        <?php if ($authors): ?>
            <?php foreach ($authors as $author): ?>
                <div class="mosaic-item">
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

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
