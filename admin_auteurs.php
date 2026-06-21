<?php
session_start();
require_once __DIR__ . '/database.php';

if (!isset($_SESSION['idrole']) || $_SESSION['idrole'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_auteur'])) {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');

    try {
        $query = "INSERT INTO Auteur (nomAuteur, prenomAuteur) VALUES (:nom, :prenom)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':nom' => $nom, ':prenom' => $prenom]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Auteur ajouté avec succès.'];
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => "Erreur lors de l'ajout de l'auteur : " . $e->getMessage()];
    }
    header('Location: admin_auteurs.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_auteur'])) {
    $idAuteur = (int)($_POST['idAuteur'] ?? 0);
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');

    try {
        $query = "UPDATE Auteur SET nomAuteur = :nom, prenomAuteur = :prenom WHERE idAuteur = :idAuteur";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':idAuteur' => $idAuteur]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Auteur modifié avec succès.'];
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => "Erreur lors de la modification de l'auteur : " . $e->getMessage()];
    }
    header('Location: admin_auteurs.php');
    exit();
}

if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    try {
        $stmt = $pdo->prepare("DELETE FROM Auteur WHERE idAuteur = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Auteur supprimé avec succès.'];
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => "Erreur lors de la suppression de l'auteur : " . $e->getMessage()];
    }
    header('Location: admin_auteurs.php');
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM Auteur ORDER BY idAuteur");
    $auteurs = $stmt->fetchAll() ?: [];
} catch (PDOException $e) {
    $auteurs = [];
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Erreur lors de la récupération des auteurs : ' . $e->getMessage()];
}
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
                            <td><input type="text" name="nom" value="<?= htmlspecialchars($auteur['nomauteur']) ?>" required></td>
                            <td><input type="text" name="prenom" value="<?= htmlspecialchars($auteur['prenomauteur']) ?>" required></td>
                            <td>
                                <input type="hidden" name="idAuteur" value="<?= $auteur['idauteur'] ?>">
                                <button type="submit" name="modifier_auteur">Modifier</button>
                            </td>
                        </form>
                        <td><a href="?supprimer=<?= $auteur['idauteur'] ?>" onclick="return confirm('Supprimer cet auteur ?')">🗑️</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Aucun auteur trouvé.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
