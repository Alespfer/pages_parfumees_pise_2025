<?php 



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notre Histoire - Les Pages Parfumées</title>
    
    <!-- Lien vers votre feuille de style externe -->
    <link rel="stylesheet" href="styles/style.css"> 
</head>

<body>

    <!-- ==================== HEADER ==================== -->
    <!-- Le header de votre site (avec le logo, le menu principal, etc.) irait ici. -->
    <header>
        <?php require ("partials/header.php"); ?>
        <!-- Exemple : <nav> ... </nav> -->
    </header>
    <!-- ================== FIN HEADER ================== -->


    <!-- ==================== CONTENU PRINCIPAL ==================== -->
    <main>

        <!-- ========= Section Héros (Bannière Principale) ========= -->
        <section class="hero-section">
            <div class="container">
                <!-- Le fil d'Ariane est souvent placé ici ou juste au-dessus du titre principal -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/accueil">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="/boutique">Boutique</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Notre Histoire</li>
                        <li class="breadcrumb-item"><a href="/nouveautes">Nouveauté</a></li>
                    </ol>
                </nav>

                <h1 class="hero-title">Une histoire écrite à la lumière d'une flamme</h1>
                <p class="hero-subtitle">Découvrez comment est née Les Pages Parfumées – un projet entre mots, parfums et émotions.</p>
                <!-- Ce bouton n'apparaît pas sur votre maquette, vous pouvez le supprimer si inutile -->
                <a href="#notre-histoire" class="btn btn-primary">Notre Histoire</a>
            </div>
        </section>

        <!-- ========= Section Notre Histoire ========= -->
        <section id="notre-histoire" class="story-section">
            <div class="container">
                <h2>Notre Histoire</h2>
                <p>Les Pages Parfumées est née d’un amour partagé pour les livres d’occasion et les bougies artisanales. Nous voulions créer une bulle de calme et de beauté où chaque objet raconte une histoire. Ici, chaque livre est choisi avec soin. Chaque bougie est coulée à la main, pour vous offrir un moment à part.</p>
            </div>
        </section>

        <!-- ========= Section Notre Démarche ========= -->
        <section class="process-section">
            <div class="container">
                <h2>Notre Démarche</h2>
                <!-- Ici iraient les 3 images/blocs de votre maquette -->
                <div class="process-grid">
                    <div class="process-item"></div>
                    <div class="process-item"></div>
                    <div class="process-item"></div>
                </div>
            </div>
        </section>
        
        <!-- ========= Section Qui Sommes-Nous ? (L'équipe) ========= -->
        <section class="team-section">
            <div class="container">
                <h2>Qui sommes-nous?</h2>
                <p class="team-description">Les Pages Parfumées, c’est avant tout une belle aventure humaine portée par deux passionnés :</p>
                
                <div class="team-grid">
                    <div class="team-member">
                        <!-- L'image de la personne irait ici, par exemple : <img src="phuong.jpg" alt="Phuong NGUYEN"> -->
                        <div class="team-member-info">
                            <h3>Phuong NGUYEN</h3>
                            <p class="team-member-role">Co-fondatrice – Créative & curieuse de nature</p>
                            <p>Amoureuse des livres anciens et de l’art de vivre, Phuong imagine des expériences sensorielles uniques en associant récits littéraires et parfums d’ambiance. Elle sélectionne chaque ouvrage avec soin et pense chaque bougie comme une invitation au voyage.</p>
                        </div>
                    </div>

                    <div class="team-member">
                         <!-- L'image de la personne irait ici, par exemple : <img src="alberto.jpg" alt="Alberto ESPERON"> -->
                        <div class="team-member-info">
                            <h3>Alberto ESPERON</h3>
                            <p class="team-member-role">Co-fondateur – Entrepreneur engagé & stratège</p>
                            <p>Toujours animé par l’envie d’entreprendre avec sens, Alberto pilote le développement du projet. Il assure la logistique, la stratégie et s’investit pleinement pour faire découvrir ces trésors culturels et olfactifs au plus grand nombre.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========= Section Promesses (Engagements) ========= -->
        <section class="promises-section">
            <div class="container">
                <h2>Notre promesse</h2>
                <div class="promises-grid">
                    <div class="promise-item">
                        <div class="promise-icon"></div>
                        <h3>Livraison gratuite</h3>
                        <p>Livraison gratuite selon le montant et votre localisation.</p>
                    </div>
                    <div class="promise-item">
                        <div class="promise-icon"></div>
                        <h3>Paiement sécurisé</h3>
                        <p>Notre système de paiement est rapide et facile à utiliser.</p>
                    </div>
                    <div class="promise-item">
                        <div class="promise-icon"></div>
                        <h3>Contactez-nous</h3>
                        <p>Vous avez des questions ? Contactez-nous.</p>
                        <a href="/contact">Contact</a>
                    </div>
                </div>
            </div>
        </section>

    </main>
    <!-- ================== FIN CONTENU PRINCIPAL ================== -->


    <!-- ==================== FOOTER ==================== -->
    <!-- Le footer, qui contient les liens et infos de la section "pré-footer", irait ici. -->
    <footer>
        <section class="pre-footer-section">
            <div class="container">
                <div class="pre-footer-grid">
                    <!-- Colonne 1: Marque -->
                    <div class="footer-column">
                        <h4>Les Pages Parfumées</h4>
                        <p><a href="mailto:customerservice@lespagesparfumees.com">customerservice@lespagesparfumees.com</a></p>
                    </div>

                    <!-- Colonne 2: Liens Utiles -->
                    <div class="footer-column">
                        <h4>Service clients</h4>
                        <ul>
                            <li><a href="/faq">Frequently Asked Questions (FAQs)</a></li>
                            <li><a href="/livraison">Livraison</a></li>
                            <li><a href="/retours">Retour et Remboursement</a></li>
                            <li><a href="/contact">Contact</a></li>
                            <li><a href="/mon-compte">Mon Compte</a></li>
                        </ul>
                    </div>

                    <!-- Colonne 3: Newsletter -->
                    <div class="footer-column">
                        <h4>Newsletter</h4>
                        <p>Recevez une offre sur votre premier achat et des promotions exclusives en vous abonnant.</p>
                        <form action="#" method="post" class="newsletter-form">
                            <label for="email-newsletter" class="sr-only">Votre email</label>
                            <input type="email" id="email-newsletter" name="email" placeholder="Email" required>
                            <button type="submit">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </footer>
    <!-- ================== FIN FOOTER ================== -->

</body>
</html>