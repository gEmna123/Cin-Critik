<?php
session_start();
require_once __DIR__ . '/database.php';

$is_logged_in = isset($_SESSION['user_id']);
$id_user = $is_logged_in ? $_SESSION['user_id'] : null;
$message = '';

try {
    $stmt = $pdo->query("SELECT e.idevenement, e.nomevenement, e.dateevenement, e.descriptionevenement, t.libelletypeevenement FROM evenement e LEFT JOIN typeevenement t ON e.idtypeevenement = t.idtypeevenement ORDER BY e.dateevenement DESC");
    $events = $stmt->fetchAll() ?: [];
} catch (PDOException $e) {
    die('Erreur lors de la récupération des événements : ' . htmlspecialchars($e->getMessage()));
}

$participants = [];
foreach ($events as $event) {
    try {
        $stmt = $pdo->prepare("SELECT u.nomutilisateur, u.prenomutilisateur FROM participation p JOIN utilisateur u ON p.idutilisateur = u.idutilisateur WHERE p.idevenement = :idevenement");
        $stmt->execute([':idevenement' => $event['idevenement']]);
        $participants[$event['idevenement']] = $stmt->fetchAll() ?: [];
    } catch (PDOException $e) {
        $participants[$event['idevenement']] = [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_logged_in) {
    $id_event = (int)($_POST['id_event'] ?? 0);

    if (isset($_POST['register_event'])) {
        try {
            $stmt = $pdo->prepare("SELECT 1 FROM participation WHERE idevenement = :idevenement AND idutilisateur = :idutilisateur");
            $stmt->execute([':idevenement' => $id_event, ':idutilisateur' => $id_user]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                $message = "Vous êtes déjà inscrit à cet événement.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO participation (idevenement, idutilisateur) VALUES (:idevenement, :idutilisateur)");
                $stmt->execute([':idevenement' => $id_event, ':idutilisateur' => $id_user]);
                $message = "Inscription réussie !";
            }
        } catch (PDOException $e) {
            $message = "Erreur d'inscription.";
        }
    } elseif (isset($_POST['unregister_event'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM participation WHERE idevenement = :idevenement AND idutilisateur = :idutilisateur");
            $stmt->execute([':idevenement' => $id_event, ':idutilisateur' => $id_user]);
            $message = "Désinscription réussie.";
        } catch (PDOException $e) {
            $message = "Erreur de désinscription.";
        }
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
        .event-item form button[name="register_event"] {
            background-color: #28a745;
            color: white;
        }
        .event-item form button[name="register_event"]:hover {
            background-color: #218838;
        }
        .event-item form button[name="unregister_event"] {
            background-color: #dc3545;
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
                        try {
                            $checkStmt = $pdo->prepare("SELECT 1 FROM participation WHERE idevenement = :idevenement AND idutilisateur = :idutilisateur");
                            $checkStmt->execute([':idevenement' => $event['idevenement'], ':idutilisateur' => $id_user]);
                            $registered = $checkStmt->fetchColumn();
                        } catch (PDOException $e) {
                            $registered = false;
                        }
                        if ($registered): ?>
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
