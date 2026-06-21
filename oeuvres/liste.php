<?php
session_start();
require_once __DIR__ . '/../database.php';

try {
    $stmt = $pdo->query("SELECT idoeuvre, titreoeuvre, descriptionoeuvre FROM Oeuvre");
    $works = $stmt->fetchAll() ?: [];
} catch (PDOException $e) {
    $works = [];
    error_log('Erreur PDO oeuvres/liste: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Œuvres</title>
    <link rel="stylesheet" href="css/liste_oeuvre.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <h1>Liste des Œuvres</h1>
    <div class="mosaic-container">
        <?php if ($works): ?>
            <?php foreach ($works as $work): ?>
                <div class="mosaic-item">
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

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
