<?php
/**
 * Fichier : /partials/header.php
 * Rôle : En-tête unifié du site.
 */

// Les dépendances sont chargées une seule fois.
require_once __DIR__ . '/../parametrage/param.php';
require_once __DIR__ . '/../fonction/fonctions.php';

// --- Préparation des variables pour le HTML ---

// On vérifie que la variable $activePage existe pour éviter les erreurs.
if (!isset($activePage)) {
    $activePage = ''; 
}

// On définit une seule fois le lien du compte pour éviter la répétition.
if (isset($_SESSION['user'])) {
    $userLink = SITE_URL . '/mon_compte.php';
} else {
    $userLink = SITE_URL . '/auth.php?action=login';
}

// Calcul du nombre total d'articles dans le panier :
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $quantity) {
        $cart_count += $quantity;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo (isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '') . SITE_NAME; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/styles/new_style.css">
</head>

<body>

    <div class="top-banner">
        <p>Profitez de la livraison gratuite dès 40 € d’achat avec le code FREESHIP</p>
    </div>

    <header class="main-header container">
        <div class="logo">
            <a href="<?php echo SITE_URL; ?>/index.php">
                <img src="<?php echo SITE_URL; ?>/ressources/logo/logo.png" alt="<?php echo SITE_NAME; ?>">
            </a>
        </div>

        <nav class="main-nav">
            <ul>
                <li><a href="<?php echo SITE_URL; ?>/index.php"
                        class="<?php if ($activePage == 'accueil') {
                            echo 'active';
                        } ?>">Accueil</a></li>
                <li><a href="<?php echo SITE_URL; ?>/shop.php"
                        class="<?php if ($activePage == 'boutique') {
                            echo 'active';
                        } ?>">Boutique</a></li>
                <li><a href="<?php echo SITE_URL; ?>/notre_histoire.php"
                        class="<?php if ($activePage == 'histoire') {
                            echo 'active';
                        } ?>">Notre Histoire</a></li>
                <li><a href="<?php echo SITE_URL; ?>/contact.php"
                        class="<?php if ($activePage == 'contact') {
                            echo 'active';
                        } ?>">Contact</a></li>
            </ul>
        </nav>

        <div class="header-icons">
            <a href="<?php echo $userLink; ?>" aria-label="Mon compte">
                <img src="<?php echo SITE_URL; ?>/ressources/decor/icon_compte.png" alt="Icône de compte">
            </a>
            <a href="<?php echo SITE_URL; ?>/panier.php" class="cart-icon-wrapper" aria-label="Panier">
                <img src="<?php echo SITE_URL; ?>/ressources/decor/icon_panier.png" alt="Icône du panier">
                <?php
                if ($cart_count > 0) {
                    echo '<span class="cart-badge">' . $cart_count . '</span>';
                }
                ?>
            </a>
        </div>
    </header>

    <main>