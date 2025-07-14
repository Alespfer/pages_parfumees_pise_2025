<?php
// Page : notre_histoire.php
// Page de présentation du concept du site

$activePage = 'histoire'; 

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notre Histoire - Les Pages Parfumées</title>
    <link rel="stylesheet" href="styles/new_style.css">
</head>

<body>

    <?php require("partials/header.php"); ?>

    <main>

        <!-- ===== Bannière principale ===== -->
        <section class="hero-section hero-section--narrow">
            <div class="container text-center">
                <h1 class="hero-title">Une histoire écrite à la lumière d'une flamme</h1>
                <p class="hero-subtitle">
                    Découvrez comment est née Les Pages Parfumées – un projet entre mots, parfums et émotions.
                </p>
            </div>
        </section>

        <!-- ===== Section Notre Histoire ===== -->
        <section id="notre-histoire" class="section">
            <div class="hero-subtitle-histoire">
                <h2>Notre Histoire</h2>
                <p>
                    Les Pages Parfumées est née d’un amour partagé pour les livres d’occasion et les bougies
                    artisanales.<br>
                    Nous voulions créer une bulle de calme et de beauté où chaque objet raconte une histoire. Ici,
                    chaque livre est choisi avec soin. Chaque bougie est coulée à la main, pour vous offrir un moment à
                    part.
                </p>
            </div>
        </section>

        <!-- ===== Notre Démarche (3 images + titres) ===== -->
        <section class="process-section section">
            <div class="container text-center">
                <h2>Notre Démarche</h2>
                <div class="process-grid">
                    <div class="process-item">
                        <img src="ressources/decor/demarche1.png" alt="Bougie artisanale">
                        <p>Fabrication de bougies artisanales</p>
                    </div>
                    <div class="process-item">
                        <img src="ressources/decor/amour.jpg" alt="Amour du livre">
                        <p>L’amour du livre</p>
                    </div>
                    <div class="process-item">
                        <img src="ressources/decor/cadeau_bougie.png" alt="Packaging soigné">
                        <p>Emballage soigné <br>& responsable</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== Qui sommes-nous ? ===== -->
        <section class="team-section section">
            <div class="container">
                <h2 class="text-center">Qui sommes-nous&nbsp;?</h2>
                <p class="text-center">
                    Les Pages Parfumées, c’est avant tout une belle aventure humaine portée par deux passionnés&nbsp;:
                </p>

                <div class="team-wrapper">

                    <!-- Colonne image -->
                    <figure class="team-photo">
                        <img src="ressources/decor/qui_somme.png"
                            alt="Les fondateurs – Phuong Nguyen et Alberto Esperon">
                    </figure>

                    <!-- Colonne textes empilés -->
                    <div class="team-info">

                        <article class="team-member">
                            <h3>Phuong NGUYEN</h3>
                            <p class="role">Co-fondatrice – <em>Créative &amp; curieuse de nature</em></p>
                             <p>Amoureuse des livres anciens et de l’art de vivre, Phuong imagine des expériences sensorielles uniques en associant récits littéraires et parfums d’ambiance. Elle sélectionne chaque ouvrage avec soin et pense chaque bougie comme une invitation au voyage.</p>
                        </article>

                        <article class="team-member">
                            <h3>Alberto ESPERON</h3>
                            <p class="role">Co-fondateur – <em>Entrepreneur engagé &amp; stratège</em></p>
                            <p>Toujours animé par l’envie d’entreprendre avec sens, Alberto pilote le développement du projet. Il assure la logistique, la stratégie et s’investit pleinement pour faire découvrir ces trésors culturels et olfactifs au plus grand nombre.</p>
                        </article>

                    </div>
                </div>
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

    <?php require('partials/footer.php'); ?>
</body>

</html>