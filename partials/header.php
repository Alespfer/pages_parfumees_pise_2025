<?php
// Fichier : partials/header.php
// Rôle : En-tête unifié du site, adapté au design.
// Gère l'affichage de la bannière, du logo, de la navigation principale
// et des icônes dynamiques (compte, panier).

// On s'assure que les dépendances sont chargées une seule fois.
require_once __DIR__ . '/../parametrage/param.php';
require_once __DIR__ . '/../fonction/fonctions.php';

// Variable pour identifier la page active et styler le lien de menu
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo (isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '') . SITE_NAME; ?></title>

    <!-- NOTE : Le lien vers Bootstrap a été supprimé. Seul notre style.css est nécessaire. -->
    <link rel="stylesheet" href="styles/style.css"> 
</head>
<body>

    <!-- Bannière supérieure promotionnelle -->
    <div class="top-banner">
        <p>Profitez de la livraison gratuite dès 40 € d’achat avec le code FREESHIP</p>
    </div>

    <!-- En-tête principal avec logo, navigation et icônes -->
    <header class="main-header container">
        <div class="logo">
            <a href="index.php">
                <!-- IMPORTANT : Remplacez par le chemin de votre vrai logo -->
                <img src="assets/images/logo.png" alt="<?php echo SITE_NAME; ?>">
            </a>
        </div>

        <nav class="main-nav">
            <ul>
                <!-- La classe 'active' est ajoutée dynamiquement en PHP -->
                <li><a href="index.php" class="<?php if ($currentPage == 'index.php') echo 'active'; ?>">Accueil</a></li>
                <li><a href="shop.php" class="<?php if ($currentPage == 'shop.php') echo 'active'; ?>">Boutique</a></li>
                <li><a href="notre_histoire.php" class="<?php if ($currentPage == 'notre_histoire.php') echo 'active'; ?>">Notre Histoire</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>

        <div class="header-icons">

            <!-- Icône Compte : lien dynamique selon la session -->
            <?php $userLink = isset($_SESSION['user']) ? 'mon_compte.php' : 'auth.php?action=login'; ?>
            <a href="<?php echo $userLink; ?>" aria-label="Mon compte">
                <img src="assets/icons/user.svg" alt="Icône de compte">
            </a>

            <!-- Icône Panier avec compteur -->
            <a href="panier.php" class="cart-icon-wrapper" aria-label="Panier">
                <img src="assets/icons/cart.svg" alt="Icône du panier">
                <?php 
                    $cart_count = 0;
                    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                        // On calcule la quantité totale d'articles, pas juste le nombre de lignes
                        foreach ($_SESSION['cart'] as $quantity) {
                            $cart_count += $quantity;
                        }
                    }
                    if ($cart_count > 0) {
                        echo '<span class="cart-badge">' . $cart_count . '</span>';
                    }
                ?>
            </a>
        </div>
    </header>

    <!-- La balise <main> est ouverte ici. Elle sera fermée par le footer.php -->
    <main>