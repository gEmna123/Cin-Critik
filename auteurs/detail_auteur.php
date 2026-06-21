<?php
session_start();
require_once __DIR__ . '/../database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    $stmt = $pdo->prepare("SELECT nomauteur, prenomauteur, datenaissanceauteur FROM auteur WHERE idauteur = :id");
    $stmt->execute([':id' => $id]);
    $author = $stmt->fetch();
} catch (PDOException $e) {
    die('Erreur lors de la récupération de l\'auteur : ' . htmlspecialchars($e->getMessage()));
}

if (!$author) {
    die("Auteur introuvable.");
}

try {
    $stmt = $pdo->prepare("SELECT idoeuvre, titreoeuvre, descriptionoeuvre FROM oeuvre WHERE idauteur = :id");
    $stmt->execute([':id' => $id]);
    $works = $stmt->fetchAll() ?: [];
} catch (PDOException $e) {
    $works = [];
}
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
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="detail-container">
        <div class="detail-header" style="background-image: url('images/<?php echo $id; ?>.jpg');">
            <h1><?php echo htmlspecialchars($author['prenomauteur'] . ' ' . $author['nomauteur']); ?></h1>
        </div>

        <div class="detail-content">
            <p><strong>Date de naissance :</strong> <?php echo htmlspecialchars($author['datenaissanceauteur']); ?></p>
        </div>

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

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
