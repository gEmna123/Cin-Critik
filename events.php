<?php
session_start();
include 'postgre.php';

// Vérification de la connexion de l'utilisateur
$is_logged_in = isset($_SESSION['user_id']);
$id_user = $is_logged_in ? $_SESSION['user_id'] : null;

// Récupération des événements
$query_events = "SELECT e.idevenement, e.nomevenement, e.dateevenement, e.descriptionevenement, t.libelletypeevenement 
                 FROM evenement e
                 LEFT JOIN typeevenement t ON e.idtypeevenement = t.idtypeevenement
                 ORDER BY e.dateevenement DESC";

$result_events = pg_query($conn, $query_events);

if (!$result_events) {
    die("Erreur lors de la récupération des événements.");
}

$events = pg_fetch_all($result_events);

// Récupération des participants pour chaque événement
$participants = [];
if ($events) {
    foreach ($events as $event) {
        $query_participants = "SELECT u.nomutilisateur, u.prenomutilisateur 
                               FROM participation p
                               JOIN utilisateur u ON p.idutilisateur = u.idutilisateur
                               WHERE p.idevenement = $1";
        $result_participants = pg_query_params($conn, $query_participants, [$event['idevenement']]);
        $participants[$event['idevenement']] = pg_fetch_all($result_participants);
    }
}

// Gestion de l'inscription et de la désinscription
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_logged_in) {
    $id_event = $_POST['id_event'];

    if (isset($_POST['register_event'])) {
        $query_check = "SELECT * FROM participation WHERE idevenement = $1 AND idutilisateur = $2";
        $result_check = pg_query_params($conn, $query_check, [$id_event, $id_user]);

        if (pg_num_rows($result_check) > 0) {
            $message = "Vous êtes déjà inscrit à cet événement.";
        } else {
            $query_register = "INSERT INTO participation (idevenement, idutilisateur) VALUES ($1, $2)";
            $result_register = pg_query_params($conn, $query_register, [$id_event, $id_user]);

            $message = $result_register ? "Inscription réussie !" : "Erreur d'inscription.";
        }
    } elseif (isset($_POST['unregister_event'])) {
        $query_unregister = "DELETE FROM participation WHERE idevenement = $1 AND idutilisateur = $2";
        $result_unregister = pg_query_params($conn, $query_unregister, [$id_event, $id_user]);

        $message = $result_unregister ? "Désinscription réussie." : "Erreur de désinscription.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - Critikart</title>
    <link rel="stylesheet" href="public_css/events.css">
    <style>
        .event-item form button {
    padding: 10px 20px;
    font-size: 1rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 10px;
}

/* Bouton S'inscrire */
.event-item form button[name="register_event"] {
    background-color: #28a745; /* vert */
    color: white;
}

.event-item form button[name="register_event"]:hover {
    background-color: #218838;
}

/* Bouton Se désinscrire */
.event-item form button[name="unregister_event"] {
    background-color: #dc3545; /* rouge */
    color: white;
}

.event-item form button[name="unregister_event"]:hover {
    background-color: #c82333;
}
</style>
</head>

<body>
<?php include 'includes/navbar.php'; ?>

<div class="container">
    <h1>Événements en cours</h1>
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($events): ?>
        <?php foreach ($events as $event): ?>
            <div id="event-<?php echo htmlspecialchars($event['idevenement']); ?>" class="event-item">
                <h2><?php echo htmlspecialchars($event['nomevenement']); ?></h2>
                <p><strong>Date :</strong> <?php echo htmlspecialchars($event['dateevenement']); ?></p>
                <p><strong>Type :</strong> <?php echo htmlspecialchars($event['libelletypeevenement']); ?></p>
                <p><strong>Description :</strong> <?php echo htmlspecialchars($event['descriptionevenement']); ?></p>
                <h3>Participants :</h3>
                <?php if (!empty($participants[$event['idevenement']])): ?>
                    <ul>
                        <?php foreach ($participants[$event['idevenement']] as $participant): ?>
                            <li><?php echo htmlspecialchars($participant['prenomutilisateur'] . ' ' . $participant['nomutilisateur']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun participant pour cet événement.</p>
                <?php endif; ?>

                <?php if ($is_logged_in): ?>
                    <form method="POST">
                        <input type="hidden" name="id_event" value="<?php echo htmlspecialchars($event['idevenement']); ?>">
                        <?php
                        $query_check_user = "SELECT 1 FROM participation WHERE idevenement = $1 AND idutilisateur = $2";
                        $result_check_user = pg_query_params($conn, $query_check_user, [$event['idevenement'], $id_user]);

                        if (pg_num_rows($result_check_user) > 0): ?>
                            <button type="submit" name="unregister_event">Se désinscrire</button>
                        <?php else: ?>
                            <button type="submit" name="register_event">S'inscrire à cet événement</button>
                        <?php endif; ?>
                    </form>
                <?php else: ?>
                    <p>Connectez-vous pour vous inscrire ou vous désinscrire de cet événement.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun événement en cours.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
