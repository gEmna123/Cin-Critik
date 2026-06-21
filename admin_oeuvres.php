<?php
session_start();
require_once __DIR__ . '/database.php';

if (!isset($_SESSION['idrole']) || $_SESSION['idrole'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_oeuvre'])) {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');

    try {
        $query = "INSERT INTO Oeuvre (titreOeuvre, descriptionOeuvre) VALUES (:titre, :description)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':titre' => $titre, ':description' => $description]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Œuvre ajoutée avec succès.'];
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Erreur lors de l'ajout de l'œuvre : ' . $e->getMessage()];
    }
    header('Location: admin_oeuvres.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_oeuvre'])) {
    $idOeuvre = (int)($_POST['idOeuvre'] ?? 0);
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');

    try {
        $query = "UPDATE Oeuvre SET titreOeuvre = :titre, descriptionOeuvre = :description WHERE idOeuvre = :idOeuvre";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':titre' => $titre, ':description' => $description, ':idOeuvre' => $idOeuvre]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Œuvre modifiée avec succès.'];
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Erreur lors de la modification de l'œuvre : ' . $e->getMessage()];
    }
    header('Location: admin_oeuvres.php');
    exit();
}

if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    try {
        $stmt = $pdo->prepare("DELETE FROM Oeuvre WHERE idOeuvre = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Œuvre supprimée avec succès.'];
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Erreur lors de la suppression de l'œuvre : ' . $e->getMessage()];
    }
    header('Location: admin_oeuvres.php');
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM Oeuvre ORDER BY idOeuvre");
    $oeuvres = $stmt->fetchAll() ?: [];
} catch (PDOException $e) {
    $oeuvres = [];
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Erreur lors de la récupération des œuvres : ' . $e->getMessage()];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration des œuvres</title>
    <link rel="stylesheet" href="/public_css/admin.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <h1>Gestion des œuvres culturelles</h1>

        <?php
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            $cssClass = $message['type'] === 'success' ? 'alert-success' : 'alert-error';
            echo '<div class="' . $cssClass . '">' . htmlspecialchars($message['text']) . '</div>';
            unset($_SESSION['message']);
        }
        ?>

        <h2>Ajouter une œuvre</h2>
        <form method="POST" action="admin_oeuvres.php">
            <label>Titre: <input type="text" name="titre" required></label><br>
            <label>Description: <textarea name="description" rows="4" required></textarea></label><br>
            <input type="submit" name="ajouter_oeuvre" value="Ajouter l'œuvre">
        </form>

        <h2>Liste des œuvres</h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Modifier</th>
                <th>Supprimer</th>
            </tr>
            <?php if ($oeuvres): ?>
                <?php foreach ($oeuvres as $oeuvre): ?>
                    <tr>
                        <form method="POST" action="admin_oeuvres.php">
                            <td><?= $oeuvre['idoeuvre'] ?></td>
                            <td><input type="text" name="titre" value="<?= htmlspecialchars($oeuvre['titreoeuvre']) ?>" required></td>
                            <td><textarea name="description" rows="3" required><?= htmlspecialchars($oeuvre['descriptionoeuvre']) ?></textarea></td>
                            <td>
                                <input type="hidden" name="idOeuvre" value="<?= $oeuvre['idoeuvre'] ?>">
                                <button type="submit" name="modifier_oeuvre">Modifier</button>
                            </td>
                        </form>
                        <td><a href="?supprimer=<?= $oeuvre['idoeuvre'] ?>" onclick="return confirm('Supprimer cette œuvre ?')">🗑️</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Aucune œuvre trouvée.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
