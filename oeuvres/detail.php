<?php
require_once __DIR__ . '/../database.php';
session_start();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    $stmt = $pdo->prepare("SELECT titreoeuvre, descriptionoeuvre, datecreationoeuvre, idauteur FROM oeuvre WHERE idoeuvre = :id");
    $stmt->execute([':id' => $id]);
    $work = $stmt->fetch();
} catch (PDOException $e) {
    die('Erreur lors de la récupération de l\'œuvre : ' . htmlspecialchars($e->getMessage()));
}

if (!$work) {
    die("Œuvre introuvable.");
}

try {
    $stmt = $pdo->prepare("SELECT nomauteur, prenomauteur FROM auteur WHERE idauteur = :id");
    $stmt->execute([':id' => $work['idauteur']]);
    $author = $stmt->fetch();
} catch (PDOException $e) {
    $author = null;
}

try {
    $stmt = $pdo->prepare("SELECT c.contenucritique, c.datecritique, c.notecritique, u.nomutilisateur, u.prenomutilisateur FROM critique c JOIN utilisateur u ON c.idutilisateur = u.idutilisateur WHERE c.idoeuvre = :id");
    $stmt->execute([':id' => $id]);
    $reviews = $stmt->fetchAll() ?: [];
} catch (PDOException $e) {
    $reviews = [];
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    if (isset($_SESSION['user_id'])) {
        $contenu = trim($_POST['contenu'] ?? '');
        $note = intval($_POST['note'] ?? 0);
        $id_utilisateur = $_SESSION['user_id'];

        if ($note >= 1 && $note <= 5) {
            try {
                $stmt = $pdo->prepare("INSERT INTO critique (contenucritique, notecritique, datecritique, idoeuvre, idutilisateur) VALUES (:contenu, :note, CURRENT_DATE, :idoeuvre, :idutilisateur)");
                $stmt->execute([':contenu' => $contenu, ':note' => $note, ':idoeuvre' => $id, ':idutilisateur' => $id_utilisateur]);
                $message = 'Votre critique a été publiée avec succès.';
                header("Location: detail.php?id=$id");
                exit;
            } catch (PDOException $e) {
                $message = 'Erreur lors de la publication de la critique.';
            }
        } else {
            $message = 'La note doit être comprise entre 1 et 5.';
        }
    } else {
        $message = 'Vous devez être connecté pour publier une critique.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($work['titreoeuvre']); ?></title>
    <link rel="stylesheet" href="css/detail_oeuvre.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="detail-container">
        <div class="detail-header" style="background-image: url('../oeuvres/images/<?php echo $id; ?>.jpg');">
            <h1><?php echo htmlspecialchars($work['titreoeuvre']); ?></h1>
        </div>

        <div class="detail-content">
            <p><strong>Auteur :</strong> <?php echo htmlspecialchars(($author['prenomauteur'] ?? '') . ' ' . ($author['nomauteur'] ?? '')); ?></p>
            <p><strong>Date de création :</strong> <?php echo htmlspecialchars($work['datecreationoeuvre']); ?></p>
            <p><strong>Description :</strong> <?php echo htmlspecialchars($work['descriptionoeuvre']); ?></p>
        </div>

        <div id="reviews" class="detail-reviews">
            <h2>Critiques des utilisateurs</h2>
            <?php if ($reviews): ?>
                <ul>
                    <?php foreach ($reviews as $review): ?>
                        <li>
                            <p><strong><?php echo htmlspecialchars($review['prenomutilisateur'] . ' ' . $review['nomutilisateur']); ?></strong> - 
                            Note : <?php echo htmlspecialchars($review['notecritique'] ?? 'N/A') . '/5'; ?></p>
                            <p><?php echo htmlspecialchars($review['contenucritique']); ?></p>
                            <p><em>Posté le <?php echo htmlspecialchars($review['datecritique']); ?></em></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucune critique disponible pour cette œuvre.</p>
            <?php endif; ?>
        </div>

        <div class="add-review">
            <h2>Ajouter une critique</h2>
            <?php if ($message): ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST">
                    <label for="contenu">Votre critique :</label>
                    <textarea name="contenu" id="contenu" required></textarea>
                    <label for="note">Note (entre 1 et 5) :</label>
                    <input type="number" name="note" id="note" min="1" max="5" required>
                    <button type="submit" name="add_review">Publier ma critique</button>
                </form>
            <?php else: ?>
                <p>Vous devez être <a href="../login.php" class="btn-login">connecté</a> pour publier une critique.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
