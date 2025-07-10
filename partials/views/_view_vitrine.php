<?php
/*
 * Fichier partiel : _view_vitrine.php
 * Rôle : Affiche la structure HTML de la page d'accueil de la boutique (vitrine).
 * Ce fichier est inclus par shop.php lorsque l'action est 'vitrine'.
 */
?>
<main class="container-fluid p-0">
    <!-- Bannière de bienvenue -->
    <section class="text-center p-4" style="background-color: #f8f9fa;">
        <div class="container">
            <h1 class="display-5">L'Atelier des Mots & Lumières</h1>
            <p class="lead text-muted">Explorez notre collection de livres, bougies et coffrets uniques.</p>
        </div>
    </section>

    <!-- Section des livres -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Nos Nouveaux Livres</h2>
            <div class="row">
                <?php foreach ($livres_vitrine as $produit) {
                    include('partials/views/_card_produit.php');
                } ?>
            </div>
            <div class="text-center"><a href="shop.php?a=catalogue&view=livres" class="btn btn-outline-secondary">Voir tous nos livres</a></div>
        </div>
    </section>

    <!-- Section des bougies -->
    <section class="py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h2 class="text-center mb-4">Nos Bougies Artisanales</h2>
            <div class="row">
                <?php foreach ($bougies_vitrine as $produit) {
                    include('partials/views/_card_produit.php');
                } ?>
            </div>
            <div class="text-center"><a href="shop.php?a=catalogue&view=bougies" class="btn btn-outline-secondary">Voir toutes nos bougies</a></div>
        </div>
    </section>

    <!-- Section des coffrets -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Nos Coffrets Uniques</h2>
            <div class="row">
                <?php foreach ($coffrets_vitrine as $produit) {
                    include('partials/views/_card_produit.php');
                } ?>
            </div>
            <div class="text-center"><a href="shop.php?a=catalogue&view=coffrets" class="btn btn-outline-secondary">Voir tous nos coffrets</a></div>
        </div>
    </section>
</main>