<?php
// Ce fichier doit être inclus au début de chaque page du front-office.
// Il gère l'inclusion des dépendances et l'affichage de la barre de navigation.

// On s'assure que les paramètres et fonctions sont chargés.
require_once __DIR__ . '/../parametrage/param.php';
require_once __DIR__ . '/../fonction/fonctions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo (isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '') . SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- <link rel="stylesheet" href="css/style.css"> -->
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?php echo SITE_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="shop.php">Catalogue</a></li>
                    <li class="nav-item"><a class="nav-link" href="a_propos.php">À Propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item"><a class="nav-link" href="mon_compte.php">Mon Compte</a></li>
                        <li class="nav-item"><a class="nav-link" href="auth.php?action=logout">Déconnexion</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="auth.php?action=login">Connexion</a></li>
                        <li class="nav-item"><a class="nav-link" href="auth.php?action=register">Inscription</a></li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="panier2.php">
                            Panier <i class="bi bi-cart-fill"></i>
                            <?php 
                                $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                                if ($cart_count > 0) echo '<span class="badge bg-danger">'.$cart_count.'</span>';
                            ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main>
    <!-- La balise <main> est ouverte ici. Elle sera fermée par le footer.php -->
    