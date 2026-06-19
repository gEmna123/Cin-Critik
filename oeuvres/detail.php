<?php
// filepath: /home/etu/test99999/oeuvres/detail.php

include '../postgre.php';

session_start();

$id = intval($_GET['id']);

// Récupération des détails de l'œuvre
$query_work = "SELECT titreoeuvre, descriptionoeuvre, datecreationoeuvre, idauteur FROM oeuvre WHERE idoeuvre = $1";
$result_work = pg_query_params($conn, $query_work, [$id]);
$work = pg_fetch_assoc($result_work);

if (!$work) {
    die("Œuvre introuvable.");
}

// Récupération des informations de l'auteur
$query_author = "SELECT nomauteur, prenomauteur FROM auteur WHERE idauteur = $1";
$result_author = pg_query_params($conn, $query_author, [$work['idauteur']]);
$author = pg_fetch_assoc($result_author);

// Récupération des critiques de l'œuvre
$query_reviews = "SELECT c.contenucritique, c.datecritique, c.notecritique, u.nomutilisateur, u.prenomutilisateur 
                  FROM critique c
                  JOIN utilisateur u ON c.idutilisateur = u.idutilisateur
                  WHERE c.idoeuvre = $1";
$result_reviews = pg_query_params($conn, $query_reviews, [$id]);
$reviews = pg_fetch_all($result_reviews);

// Gestion de l'ajout d'une critique
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    if (isset($_SESSION['user_id'])) {
    $contenu = $_POST['contenu'];
    $note = intval($_POST['note']);
    $id_utilisateur = $_SESSION['user_id'];

        if ($note >= 1 && $note <= 5) {
            $query_add_review = "INSERT INTO critique (contenucritique, notecritique, datecritique, idoeuvre, idutilisateur) 
                                 VALUES ($1, $2, CURRENT_DATE, $3, $4)";
            pg_query_params($conn, $query_add_review, [$contenu, $note, $id, $id_utilisateur]);
            $message = 'Votre critique a été publiée avec succès.';
            header("Location: detail.php?id=$id");
            exit;
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
    <?php include '../includes/navbar.php'; ?>

    <div class="detail-container">
        <!-- Image de l'œuvre en arrière-plan -->
        <div class="detail-header" style="background-image: url('../oeuvres/images/<?php echo $id; ?>.jpg');">
            <h1><?php echo htmlspecialchars($work['titreoeuvre']); ?></h1>
        </div>

        <!-- Informations principales -->
        <div class="detail-content">
            <p><strong>Auteur :</strong> <?php echo htmlspecialchars($author['prenomauteur'] . ' ' . $author['nomauteur']); ?></p>
            <p><strong>Date de création :</strong> <?php echo htmlspecialchars($work['datecreationoeuvre']); ?></p>
            <p><strong>Description :</strong> <?php echo htmlspecialchars($work['descriptionoeuvre']); ?></p>
        </div>

        <!-- Section des critiques -->
        <div id="reviews" class="detail-reviews">
            <h2>Critiques des utilisateurs</h2>
            <?php if ($reviews): ?>
                <ul>
                    <?php foreach ($reviews as $review): ?>
                        <li>
                            <p><strong><?php echo htmlspecialchars($review['prenomutilisateur'] . ' ' . $review['nomutilisateur']); ?></strong> - 
                            Note : <?php echo isset($review['notecritique']) ? htmlspecialchars($review['notecritique']) . '/5' : 'Note non disponible'; ?></p>

                            <p><?php echo htmlspecialchars($review['contenucritique']); ?></p>
                            <p><em>Posté le <?php echo htmlspecialchars($review['datecritique']); ?></em></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucune critique disponible pour cette œuvre.</p>
            <?php endif; ?>
        </div>

        <!-- Formulaire pour ajouter une critique -->
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

    <?php include '../includes/footer.php'; ?>
</body>
</html>
