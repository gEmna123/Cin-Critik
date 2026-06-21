<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/database.php';

function fetch_all_or_empty(PDO $pdo, string $sql): array
{
    try {
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll() ?: [];
    } catch (PDOException $e) {
        error_log('PDO query failed: ' . $e->getMessage());
        return [];
    }
}

$query_works = "SELECT idoeuvre, titreoeuvre, descriptionoeuvre FROM oeuvre ORDER BY idoeuvre DESC";
$works = fetch_all_or_empty($pdo, $query_works);

$query_events = "SELECT idevenement, nomevenement, dateevenement FROM evenement";
$events = fetch_all_or_empty($pdo, $query_events);

$query_reviews = "SELECT idoeuvre, contenucritique, notecritique FROM critique";
$reviews = fetch_all_or_empty($pdo, $query_reviews);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public_css/style.css">
    <title>Accueil - Critikart</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <section>
        <h2>Liste des Œuvres</h2>
        <div class="mosaic-container">
            <?php if (!empty($works)): ?>
                <?php foreach ($works as $work): ?>
                    <div class="mosaic-item">
                        <img src="oeuvres/images/<?php echo $work['idoeuvre']; ?>.jpg" 
                             alt="Image de <?php echo htmlspecialchars($work['titreoeuvre']); ?>" 
                             onerror="this.onerror=null; this.src='oeuvres/images/default.jpg';">
                        <h3><?php echo htmlspecialchars($work['titreoeuvre']); ?></h3>
                        <p><?php echo htmlspecialchars($work['descriptionoeuvre']); ?></p>
                        <a href="oeuvres/detail.php?id=<?php echo $work['idoeuvre']; ?>" class="btn">Voir plus</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune œuvre disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </section>
    <section>
        <h2>Événements à venir</h2>
        <div class="mosaic-container">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="mosaic-item">
                        <h3><?php echo htmlspecialchars($event['nomevenement']); ?></h3>
                        <p>Date : <?php echo htmlspecialchars($event['dateevenement']); ?></p>
                        <a href="events.php#event-<?php echo htmlspecialchars($event['idevenement']); ?>" class="btn">Voir plus</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun événement à venir.</p>
            <?php endif; ?>
        </div>
    </section>
    <section>
        <h2>Critiques récentes</h2>
        <div class="mosaic-container">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="mosaic-item">
                        <h3>Critique</h3>
                        <p><strong><?php echo htmlspecialchars($review['contenucritique']); ?></strong></p>
                        <p>Note : <?php echo htmlspecialchars($review['notecritique']); ?>/5</p>
                        <a href="oeuvres/detail.php?id=<?php echo $review['idoeuvre']; ?>#reviews" class="btn">Voir plus</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune critique disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>
