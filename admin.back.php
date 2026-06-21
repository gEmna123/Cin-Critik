<?php
session_start();
require_once __DIR__ . '/database.php';

if (!isset($_SESSION['idrole']) || $_SESSION['idrole'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mdp = password_hash($_POST['mdp'] ?? '', PASSWORD_DEFAULT);
    $role = (int)($_POST['role'] ?? 0);

    try {
        $stmt = $pdo->prepare("INSERT INTO Utilisateur (nomUtilisateur, prenomUtilisateur, emailUtilisateur, mdpUtilisateur, idRole) VALUES (:nom, :prenom, :email, :mdp, :role)");
        $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email, ':mdp' => $mdp, ':role' => $role]);
    } catch (PDOException $e) {
        echo htmlspecialchars('Erreur lors de l'ajout de l'utilisateur : ' . $e->getMessage());
    }
}

if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    try {
        $stmt = $pdo->prepare("DELETE FROM Utilisateur WHERE idUtilisateur = :id");
        $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        echo htmlspecialchars('Erreur lors de la suppression de l'utilisateur : ' . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_role'])) {
    $idUser = (int)($_POST['idUtilisateur'] ?? 0);
    $nouveauRole = (int)($_POST['nouveauRole'] ?? 0);
    try {
        $stmt = $pdo->prepare("UPDATE Utilisateur SET idRole = :role WHERE idUtilisateur = :id");
        $stmt->execute([':role' => $nouveauRole, ':id' => $idUser]);
    } catch (PDOException $e) {
        echo htmlspecialchars('Erreur lors de la modification du rôle : ' . $e->getMessage());
    }
}

try {
    $utilisateurs = $pdo->query("SELECT u.*, r.nomRole FROM Utilisateur u JOIN Role r ON u.idRole = r.idRole ORDER BY u.idUtilisateur")->fetchAll() ?: [];
    $roles = $pdo->query("SELECT * FROM Role")->fetchAll() ?: [];
    $oeuvres = $pdo->query("SELECT * FROM Oeuvre ORDER BY idOeuvre")->fetchAll() ?: [];
    $critiques = $pdo->query("SELECT * FROM Critique ORDER BY idCritique")->fetchAll() ?: [];
    $evenements = $pdo->query("SELECT * FROM Evenement ORDER BY dateEvenement")->fetchAll() ?: [];
} catch (PDOException $e) {
    $utilisateurs = $roles = $oeuvres = $critiques = $evenements = [];
    echo htmlspecialchars('Erreur de récupération : ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_critique'])) {
    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $idUtilisateur = $_SESSION['user_id'] ?? null;

    if ($idUtilisateur !== null) {
        try {
            $stmt = $pdo->prepare("INSERT INTO Critique (titre, contenu, idUtilisateur) VALUES (:titre, :contenu, :idUtilisateur)");
            $stmt->execute([':titre' => $titre, ':contenu' => $contenu, ':idUtilisateur' => $idUtilisateur]);
        } catch (PDOException $e) {
            echo htmlspecialchars('Erreur lors de l'ajout de la critique : ' . $e->getMessage());
        }
    }
}

if (isset($_GET['supprimer_critique'])) {
    $idCritique = (int)$_GET['supprimer_critique'];
    try {
        $stmt = $pdo->prepare("DELETE FROM Critique WHERE idCritique = :id");
        $stmt->execute([':id' => $idCritique]);
    } catch (PDOException $e) {
        echo htmlspecialchars('Erreur lors de la suppression de la critique : ' . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_evenement'])) {
    $nomEvenement = trim($_POST['nom_evenement'] ?? '');
    $dateEvenement = trim($_POST['date_evenement'] ?? '');
    $lieuEvenement = trim($_POST['lieu_evenement'] ?? '');

    try {
        $stmt = $pdo->prepare("INSERT INTO Evenement (nomEvenement, dateEvenement, lieuEvenement) VALUES (:nom, :date, :lieu)");
        $stmt->execute([':nom' => $nomEvenement, ':date' => $dateEvenement, ':lieu' => $lieuEvenement]);
    } catch (PDOException $e) {
        echo htmlspecialchars('Erreur lors de l'ajout de l'événement : ' . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin </title>
    <link rel="stylesheet" href="/public_css/admin.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <h1>Espace Administration</h1>
        <p>Bienvenue dans l’espace d'administration. Ici, vous pouvez gérer le contenu du site.</p>

        <ul>
            <li><a href="admin.php">Gérer les utilisateurs, critiques, événements</a></li>
            <li><a href="admin_oeuvres.php">Gérer les œuvres</a></li>
            <li><a href="admin_auteurs.php">Gérer les auteurs</a></li>
        </ul>
    </div>

    <h2>Ajouter un utilisateur</h2>
    <form method="POST">
        <label>Nom: <input type="text" name="nom" required></label><br>
        <label>Prénom: <input type="text" name="prenom" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Mot de passe: <input type="password" name="mdp" required></label><br>
        <label>Rôle:
            <select name="role">
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['idrole'] ?>"><?= htmlspecialchars($role['nomrole']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <input type="submit" name="ajouter" value="Ajouter l'utilisateur">
    </form>

    <h2>Liste des utilisateurs</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom complet</th>
            <th>Email</th>
            <th>Date d'inscription</th>
            <th>Rôle</th>
            <th>Changer rôle</th>
            <th>Supprimer</th>
        </tr>
        <?php if ($utilisateurs): ?>
            <?php foreach ($utilisateurs as $user): ?>
                <tr>
                    <td><?= $user['idutilisateur'] ?></td>
                    <td><?= htmlspecialchars($user['prenomutilisateur']) . ' ' . htmlspecialchars($user['nomutilisateur']) ?></td>
                    <td><?= htmlspecialchars($user['emailutilisateur']) ?></td>
                    <td><?= htmlspecialchars($user['dateinscription']) ?></td>
                    <td><?= htmlspecialchars($user['nomrole']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="idUtilisateur" value="<?= $user['idutilisateur'] ?>">
                            <select name="nouveauRole">
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['idrole'] ?>" <?= $role['idrole'] == $user['idrole'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($role['nomrole']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="changer_role">Modifier</button>
                        </form>
                    </td>
                    <td><a href="?supprimer=<?= $user['idutilisateur'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">🗑️</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">Aucun utilisateur trouvé.</td></tr>
        <?php endif; ?>
    </table>

    <h2>Ajouter une critique</h2>
    <form method="POST">
        <label>Titre: <input type="text" name="titre" required></label><br>
        <label>Contenu: <textarea name="contenu" required></textarea></label><br>
        <input type="submit" name="ajouter_critique" value="Ajouter la critique">
    </form>

    <h2>Liste des critiques</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Contenu</th>
            <th>Utilisateur</th>
            <th>Supprimer</th>
        </tr>
        <?php if ($critiques): ?>
            <?php foreach ($critiques as $critique): ?>
                <tr>
                    <td><?= $critique['idcritique'] ?></td>
                    <td><?= htmlspecialchars($critique['titre']) ?></td>
                    <td><?= htmlspecialchars($critique['contenu']) ?></td>
                    <td><?= htmlspecialchars($critique['idutilisateur']) ?></td>
                    <td><a href="?supprimer_critique=<?= $critique['idcritique'] ?>" onclick="return confirm('Supprimer cette critique ?')">🗑️</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">Aucune critique trouvée.</td></tr>
        <?php endif; ?>
    </table>

    <h2>Ajouter un événement</h2>
    <form method="POST">
        <label>Nom de l'événement: <input type="text" name="nom_evenement" required></label><br>
        <label>Date: <input type="date" name="date_evenement" required></label><br>
        <label>Lieu: <input type="text" name="description_evenement" required></label><br>
        <label>Type: <input type="text" name="type_evenement"></label><br>
        <input type="submit" name="ajouter_evenement" value="Ajouter l'événement">
    </form>

    <h2>Liste des événements</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Date</th>
            <th>Lieu</th>
            <th>Supprimer</th>
        </tr>
        <?php if ($evenements): ?>
            <?php foreach ($evenements as $evenement): ?>
                <tr>
                    <td><?= $evenement['idevenement'] ?></td>
                    <td><?= htmlspecialchars($evenement['nomevenement']) ?></td>
                    <td><?= htmlspecialchars($evenement['dateevenement']) ?></td>
                    <td><?= htmlspecialchars($evenement['descriptionevenement']) ?></td>
                    <td><a href="?supprimer_evenement=<?= $evenement['idevenement'] ?>" onclick="return confirm('Supprimer cet événement ?')">🗑️</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">Aucun événement trouvé.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
