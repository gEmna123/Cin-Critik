<?php
session_start();
require_once 'postgre.php'; // Contient la connexion $conn via pg_connect

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['idrole']) || $_SESSION['idrole'] != 1) {
    header('Location: login.php');
    exit();
}

// Ajouter un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = pg_escape_string($conn, $_POST['nom']);
    $prenom = pg_escape_string($conn, $_POST['prenom']);
    $email = pg_escape_string($conn, $_POST['email']);
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
    $role = (int)$_POST['role'];

    $query = "INSERT INTO Utilisateur (nomUtilisateur, prenomUtilisateur, emailUtilisateur, mdpUtilisateur, idRole)
              VALUES ('$nom', '$prenom', '$email', '$mdp', $role)";
    pg_query($conn, $query);
}

// Supprimer un utilisateur
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $query = "DELETE FROM Utilisateur WHERE idUtilisateur = $id";
    pg_query($conn, $query);
}

// Modifier le rôle d'un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_role'])) {
    $idUser = (int)$_POST['idUtilisateur'];
    $nouveauRole = (int)$_POST['nouveauRole'];
    $query = "UPDATE Utilisateur SET idRole = $nouveauRole WHERE idUtilisateur = $idUser";
    pg_query($conn, $query);
}

// Récupérer les utilisateurs et les rôles
$utilisateurs_query = "SELECT u.*, r.nomRole FROM Utilisateur u JOIN Role r ON u.idRole = r.idRole ORDER BY u.idUtilisateur";
$utilisateurs_result = pg_query($conn, $utilisateurs_query);
$utilisateurs = pg_fetch_all($utilisateurs_result);

$roles_query = "SELECT * FROM Role";
$roles_result = pg_query($conn, $roles_query);
$roles = pg_fetch_all($roles_result);

// Ajouter une critique
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_critique'])) {
    $titre = pg_escape_string($conn, $_POST['titre']);
    $contenu = pg_escape_string($conn, $_POST['contenu']);
    $idUtilisateur = $_SESSION['idutilisateur'];

    $query = "INSERT INTO Critique (titre, contenu, idUtilisateur) VALUES ('$titre', '$contenu', $idUtilisateur)";
    pg_query($conn, $query);
}

// Supprimer une critique
if (isset($_GET['supprimer_critique'])) {
    $idCritique = (int)$_GET['supprimer_critique'];
    $query = "DELETE FROM Critique WHERE idCritique = $idCritique";
    pg_query($conn, $query);
}

// Ajouter un événement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_evenement'])) {
    $nomEvenement = pg_escape_string($conn, $_POST['nom_evenement']);
    $dateEvenement = pg_escape_string($conn, $_POST['date_evenement']);
    $lieuEvenement = pg_escape_string($conn, $_POST['lieu_evenement']);

    $query = "INSERT INTO Evenement (nomEvenement, dateEvenement, lieuEvenement) VALUES ('$nomEvenement', '$dateEvenement', '$lieuEvenement')";
    pg_query($conn, $query);
}

// Supprimer un événement
if (isset($_GET['supprimer_evenement'])) {
    $idEvenement = (int)$_GET['supprimer_evenement'];
    $query = "DELETE FROM Evenement WHERE idEvenement = $idEvenement";
    pg_query($conn, $query);
}

// Récupérer les critiques
$critiques_query = "SELECT * FROM Critique ORDER BY idCritique";
$critiques_result = pg_query($conn, $critiques_query);
$critiques = pg_fetch_all($critiques_result);

// Récupérer les événements
$evenements_query = "SELECT * FROM Evenement ORDER BY dateEvenement";
$evenements_result = pg_query($conn, $evenements_query);
$evenements = pg_fetch_all($evenements_result);

// Récupérer les œuvres culturelles
$oeuvres_query = "SELECT * FROM Oeuvre ORDER BY idOeuvre";
$oeuvres_result = pg_query($conn, $oeuvres_query);
$oeuvres = pg_fetch_all($oeuvres_result);
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
        	<!-- ajoute d'autres liens si nécessaire -->
    	</ul>
    </div>

    <h2>Administration des Utilisateurs</h2>


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
                    <td><?= $user['dateinscription'] ?></td>
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
                    <td><?= $critique['idutilisateur'] ?></td>
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
        <label>Lieu: <input type="text" name="lieu_evenement" required></label><br>
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
                    <td><?= $evenement['dateevenement'] ?></td>
                    <td><?= htmlspecialchars($evenement['lieuevenement']) ?></td>
                    <td><a href="?supprimer_evenement=<?= $evenement['idevenement'] ?>" onclick="return confirm('Supprimer cet événement ?')">🗑️</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">Aucun événement trouvé.</td></tr>
        <?php endif; ?>
    </table>

    <h2>Liste des œuvres culturelles</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Description</th>
        </tr>
        <?php if ($oeuvres): ?>
            <?php foreach ($oeuvres as $oeuvre): ?>
                <tr>
                    <td><?= $oeuvre['idoeuvre'] ?></td>
                    <td><?= htmlspecialchars($oeuvre['nomoeuvre']) ?></td>
                    <td><?= htmlspecialchars($oeuvre['descriptionoeuvre']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">Aucune œuvre culturelle trouvée.</td></tr>
        <?php endif; ?>
    </table>

</body>
</html>

