
<?php

session_start();
// Affichage du message flash (si présent)
if (isset($_SESSION['message'])) {
    $msg = $_SESSION['message'];
    // Exemple : ici on considère que message est un tableau ['text' => ..., 'type' => ...]
    // sinon adapte en fonction de ta structure
    $type = isset($msg['type']) && $msg['type'] === 'success' ? 'alert-success' : 'alert-error';
    echo '<div class="' . $type . '">' . htmlspecialchars($msg['text']) . '</div>';

    unset($_SESSION['message']); // Supprime le message après affichage
}

// filepath: /home/etu/test2/site-associatif/public/index.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'postgre.php'; // Inclusion du fichier de connexion à la base de données


// Fonction utilitaire pour récupérer toutes les lignes ou un tableau vide en cas d'erreur
function fetch_all_or_empty($conn, $sql)
{
    $result = pg_query($conn, $sql);
    if ($result === false) {
        error_log('PostgreSQL query failed: ' . pg_last_error($conn));
        return [];
    }

    $rows = pg_fetch_all($result);
    return $rows ?: [];
}

// Récupération des œuvres depuis la base de données
$query_works = "SELECT idoeuvre, titreoeuvre, descriptionoeuvre FROM oeuvre ORDER BY idoeuvre DESC";
$works = fetch_all_or_empty($conn, $query_works);

// Récupération des événements
$query_events = "SELECT idevenement, nomevenement, dateevenement FROM evenement";
$events = fetch_all_or_empty($conn, $query_events);

// Récupération des critiques
$query_reviews = "SELECT idoeuvre, contenucritique, notecritique FROM critique";
$reviews = fetch_all_or_empty($conn, $query_reviews);


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

    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>
    <!-- Section des œuvres -->
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

    <!-- Section des événements -->
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

    <!-- Section des critiques -->
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

    <!-- Footer -->
        <?php include 'includes/footer.php'; ?>

</body>
</html>
