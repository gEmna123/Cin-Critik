<?php

?>
<nav class="navbar">
    <div class="navbar-container">

        <div class="navbar-logo">
            <img src="../logo.png" alt="Logo" >
            <?php
            $current_page = basename($_SERVER['PHP_SELF'], ".php");
            switch ($current_page) {
                case 'index':
                    echo "Accueil";
                    break;
                case 'liste':
                    echo "Œuvres";
                    break;
                case 'liste_auteur':
                    echo "Auteurs";
                    break;
                case 'events':
                    echo "Événements";
                    break;
                case 'login':
                    echo "Connexion";
                    break;
                default:
                    echo "Critikart";
                    break;
            }
            ?>
        </div>

        <ul class="navbar-links">
            <li><a href="../index.php">Accueil</a></li>
            <li><a href="../oeuvres/liste.php">Œuvres</a></li>
            <li><a href="../auteurs/liste_auteur.php">Auteurs</a></li>
            <li><a href="../events.php">Événements</a></li>

            <?php if (isset($_SESSION['username'])): ?>
                <li>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></li>
                <?php if (isset($_SESSION['idrole']) && $_SESSION['idrole'] == 1): ?>
                    <li><a href="../admin.php">Page Admin</a></li>
                <?php else: ?>
                    <li><a href="../account.php">Mon profil</a></li>
                <?php endif; ?>
                <li><a href="../logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="../login.php">Connexion</a></li>
            <?php endif; ?>

        </ul>

    </div>
</nav>
