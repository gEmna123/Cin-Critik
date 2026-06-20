<?php

session_start();
require_once 'postgre.php'; // connexion $conn

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['idrole']) || $_SESSION['idrole'] != 1) {
    header('Location: login.php');
    exit();
}

// Ajouter un auteur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_auteur'])) {
    $nom = pg_escape_string($conn, $_POST['nom']);
    $prenom = pg_escape_string($conn, $_POST['prenom']);
    try {
        $query = "INSERT INTO Auteur (nomAuteur, prenomAuteur) VALUES ('$nom', '$prenom')";
        pg_query($conn, $query);
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Auteur ajouté avec succès.'
        ];
    } catch (Exception $e) {
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => 'Erreur lors de l\'ajout de l\'auteur : ' . $e->getMessage()
        ];
    }
    header('Location: admin_auteurs.php');
    exit();
}

// Modifier un auteur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_auteur'])) {
    $idAuteur = (int)$_POST['idAuteur'];
    $nom = pg_escape_string($conn, $_POST['nom']);
    $prenom = pg_escape_string($conn, $_POST['prenom']);
    $query = "UPDATE Auteur SET nomAuteur = '$nom', prenomAuteur = '$prenom' WHERE idAuteur = $idAuteur";
    pg_query($conn, $query);
    $_SESSION['message'] = [
        'type' => 'success',
        'text' => 'Auteur modifié avec succès.'
    ];
    header('Location: admin_auteurs.php');
    exit();
}

// Supprimer un auteur
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $query = "DELETE FROM Auteur WHERE idAuteur = $id";
    pg_query($conn, $query);
    $_SESSION['message'] = [
        'type' => 'success',
        'text' => 'Auteur supprimé avec succès.'
    ];
    header('Location: admin_auteurs.php');
    exit();
}

// Récupérer la liste des auteurs
$auteurs_query = "SELECT * FROM Auteur ORDER BY idAuteur";
$auteurs_result = pg_query($conn, $auteurs_query);
$auteurs = pg_fetch_all($auteurs_result);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration des auteurs</title>
    <link rel="stylesheet" href="/public_css/admin.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <h1>Gestion des auteurs</h1>

        <?php
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            $cssClass = $message['type'] === 'success' ? 'alert-success' : 'alert-error';
            echo '<div class="' . $cssClass . '">' . htmlspecialchars($message['text']) . '</div>';
            unset($_SESSION['message']);
        }
        ?>

        <h2>Ajouter un auteur</h2>
        <form method="POST" action="admin_auteurs.php">
            <label>Nom: <input type="text" name="nom" required></label><br>
            <label>Prénom: <input type="text" name="prenom" required></label><br>
            <input type="submit" name="ajouter_auteur" value="Ajouter l'auteur">
        </form>

        <h2>Liste des auteurs</h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Modifier</th>
                <th>Supprimer</th>
            </tr>
            <?php if ($auteurs): ?>
                <?php foreach ($auteurs as $auteur): ?>
                    <tr>
                        <form method="POST" action="admin_auteurs.php">
                            <td><?= $auteur['idauteur'] ?></td>
                            <td>
                                <input type="text" name="nom" value="<?= htmlspecialchars($auteur['nomauteur']) ?>" required>
                            </td>
                            <td>
                                <input type="text" name="prenom" value="<?= htmlspecialchars($auteur['prenomauteur']) ?>" required>
                            </td>
                            <td>
                                <input type="hidden" name="idAuteur" value="<?= $auteur['idauteur'] ?>">
                                <button type="submit" name="modifier_auteur">Modifier</button>
                            </td>
                        </form>
                        <td>
                            <a href="?supprimer=<?= $auteur['idauteur'] ?>" onclick="return confirm('Supprimer cet auteur ?')">🗑️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Aucun auteur trouvé.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
