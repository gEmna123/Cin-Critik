<?php
session_start();

// Vider toutes les variables de session
$_SESSION = [];

// Détruire la session
session_destroy();

// Recréer une session temporaire juste pour le message
session_start();
$_SESSION['message'] = [
    'type' => 'success',
    'text' => 'Vous avez été déconnecté avec succès.'
];

// Rediriger vers index.php
header('Location: index.php');
exit();
