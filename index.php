<?php
/*
 * Fichier : index.php
 * Rôle : Contrôleur et Vue de la page d'accueil (Vitrine).
 */

// --- INITIALISATION ---
session_start();
require('parametrage/param.php');
require('fonction/fonctions.php');

// --- PRÉPARATION DES DONNÉES POUR L'AFFICHAGE ---
$pageTitle = "Accueil";
$activePage = 'accueil'; 


// On récupère une sélection de produits pour chaque catégorie à mettre en avant.
$livres_showcase = getFilteredProducts(['type' => 'livre', 'limit' => 3, 'sort' => 'nouveaute']);
$bougies_showcase = getFilteredProducts(['type' => 'bougie', 'limit' => 3, 'sort' => 'nouveaute']);
$coffrets_showcase = getFilteredProducts(['type' => 'coffret', 'limit' => 3, 'sort' => 'nouveaute']);

// Pour la section "Nouveauté", on peut prendre un mix
$nouveautes = getFilteredProducts(['limit' => 4, 'sort' => 'nouveaute']);

// Inclusion du header
require('partials/header.php');
?>

<!-- =========================================================================
     AFFICHAGE HTML DE LA PAGE D'ACCUEIL
========================================================================= -->
<main>
    <!-- Section Héros -->
    <section class="hero-section">
        <!-- COLONNE DE GAUCHE : L'IMAGE -->
        <div class="hero-image-container">
            <img src="ressources/decor/image_accueil.png" 
            alt="Ambiance de lecture avec un livre ouvert et une bougie allumée">
        </div>

        <!-- COLONNE DE DROITE : LE TEXTE -->
        <div class="hero-content">
            <h1>Entre Pages et Parfums</h1>
            <p>Offrez-vous une atmosphère de lecture douce et inspirante</p>
            <a href="shop.php" class="btn btn-primary">Commencer la découverte</a>
        </div>
    </section>


    <!-- Section Notre Histoire -->
    <section class="story-section container">
        <div class="story-content">
            <h2>Notre Histoire</h2>
            <p>Les Pages Parfumées est née d’un amour pour les livres et la lumière douce des bougies. Nous rassemblons
                des livres anciens pleins de charme, associés à des bougies artisanales pour offrir des instants de
                lecture apaisants.</p>
            <a href="notre_histoire.php" class="btn btn-secondary">En savoir plus</a>
        </div>
        <div class="story-image">
            <img src="ressources/decor/notre_histoire.png" alt="Livre et bougie sur un lit">
        </div>
    </section>

    <!-- Section Catégories -->
    <section class="category-preview-section">
        <div class="container">
            <div class="category-grid">
                <div class="category-card">
                    <div class="produits-img">
                        <img src="ressources/decor/livre_accueil.png" alt="Livre d'occasion">
                    </div>
                    <div class="produits-text">
                        <p>Laissez-vous emporter par des lectures choisies avec soin. Des livres d’occasion pour un
                            moment suspendu.</p>
                    </div>
                    <a href="shop.php?view=livres">Voir les livres</a>
                </div>
                <div class="category-card">
                    <div class="produits-img">
                        <img src="ressources/decor/cadeau_bougie.png" alt="Bougie">
                    </div>
                    <div class="produits-text">
                        <p> Des bougies artisanales, naturelles, conçues pour accompagner vos instants de lecture.</p>
                    </div>
                    <a href="shop.php?view=bougies">Voir les bougies</a>
                </div>
                <div class="category-card">
                    <div class="produits-img">
                        <img src="ressources/decor/coffret_accueil .png" alt="Coffret">
                    </div>
                    <div class="produits-text">
                        <p> Offrez une parenthèse de douceur : un coffret prêt à ravir les amoureux de calme et de
                            poésie.</p>
                    </div>
                    <a href="shop.php?view=coffrets">Voir les coffrets</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Nouveautés -->
    <section class="section-padding container">
        <h2 class="section-title">Nouveautés</h2>

        <div class="product-grid">

            <?php
            // CHANGEMENT 1 : Syntaxe "if" avec accolades et "count()"
            if (count($nouveautes) > 0) {

                // CHANGEMENT 2 : Syntaxe "foreach" avec accolades
                foreach ($nouveautes as $produit) {
                    ?>

                    <div class="product-card">
                        <a href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>" class="product-card__image-link">
                            <img class="product-card__image" src="<?php
                            // CHANGEMENT 3 : Syntaxe "isset" classique au lieu de "??"
                            echo htmlspecialchars(
                                isset($produit['image_url']) && $produit['image_url'] != ''
                                ? $produit['image_url']
                                : 'ressources/images/placeholder.jpg'
                            );
                            ?>" alt="<?php echo htmlspecialchars($produit['nom_produit']); ?>">
                        </a>

                        <div class="product-card__content">
                            <h3 class="product-card__title">
                                <a
                                    href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>"><?php echo htmlspecialchars($produit['nom_produit']); ?></a>
                            </h3>

                            <p class="product-card__price"><?php echo number_format($produit['prix_ttc'], 2, ',', ' '); ?> €</p>

                            <div class="product-card__actions">
                                <a href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>"
                                    class="btn btn--secondary">Voir le produit</a>
                            </div>
                        </div>
                    </div>

                    <?php
                } // Fin du foreach
            
            } else { // CHANGEMENT 4 : Syntaxe "else" avec accolades
                ?>
                <p style="text-align: center; grid-column: 1 / -1;">Aucune nouveauté à afficher pour le moment.</p>
                <?php
            } // Fin du if
            ?>

        </div> <!-- Fin de .product-grid -->
    </section>
    </div> <!-- Fin de .product-grid -->
    </section>

    <!-- Section Témoignages -->
    <!-- AVIS + ÉTOILES -->
    <section class="testimonials-section section-padding">
        <div class="container">
            <h2 class="section-title">Ce que vous en dites</h2>

            <div class="testimonials-grid">

                <div class="testimonial-card">

                    <div class="testimonial-card__rating">
                        <img src="ressources/decor/etoiles_avis.png" alt="Avis 5 étoiles">
                    </div>

                    <h3 class="testimonial-card__title">Cadeau parfait !</h3>
                    <blockquote class="testimonial-card__quote">
                        <p>“J’ai offert un coffret livre + bougie à ma sœur : elle a adoré l’odeur et a passé sa soirée
                            à lire. L’emballage était magnifique.”</p>
                    </blockquote>
                    <cite class="testimonial-card__author">— Camille D.</cite>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-card__rating">
                        <img src="ressources/decor/etoiles_avis.png" alt="Avis 5 étoiles">
                    </div>

                    <h3 class="testimonial-card__title">Une vraie bulle de douceur</h3>
                    <blockquote class="testimonial-card__quote">
                        <p>“La bougie sent divinement bon et le livre choisi était exactement dans mes goûts. On sent
                            l’attention dans chaque détail.”</p>
                    </blockquote>
                    <cite class="testimonial-card__author">— Julie M.</cite>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-card__rating">
                        <img src="ressources/decor/etoiles_avis.png" alt="Avis 5 étoiles">
                    </div>

                    <h3 class="testimonial-card__title">Un moment pour soi</h3>
                    <blockquote class="testimonial-card__quote">
                        <p>“Un concept que j’adore. Recevoir un colis aussi soigné donne envie de ralentir, de lire, de
                            respirer. Bravo !”</p>
                    </blockquote>
                    <cite class="testimonial-card__author">— Thomas B.</cite>
                </div>

            </div> <!-- Fin de .testimonials-grid -->
        </div>
    </section>

    <!-- =================================================================== -->
    <!--                      BANDE 3 AVANTAGES                               -->
    <!-- =================================================================== -->
    <section class="footer-benefits">
        <div class="benefit">
            <img src="ressources/decor/icon_livraison.png" alt="Livraison gratuite">
            <h5>Livraison gratuite</h5>
            <p>Livraison gratuite selon le montant et votre localisation.</p>
        </div>

        <div class="benefit">
            <img src="ressources/decor/icon_paiement.png" alt="Paiement sécurisé">
            <h5>Paiement sécurisé</h5>
            <p>Notre système de paiement est rapide et facile à utiliser.</p>
        </div>

        <div class="benefit">
            <img src="ressources/decor/icon_contact.png" alt="Contactez-nous">
            <h5>Contactez-nous</h5>
            <p>Vous avez des questions ? Contactez-nous.</p>
        </div>
    </section>

</main>

<?php
// Inclusion du footer
require('partials/footer.php');
?>