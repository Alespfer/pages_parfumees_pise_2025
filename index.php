<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Pages Parfumées - Accueil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bona+Nova:ital,wght@0,400;0,700;1,400&family=Cormorant+Garamond:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-title: 'Cormorant Garamond', serif;
            --font-body: 'Bona Nova', serif;
            --color-bg-light: #EFE5CE;
            --color-bg-main: #FFFFFF;
            --color-bg-accent: #E5DDCE;
            --color-text: #4B360D;
            --color-button: #A38968;
            --color-button-text: #FFFFFF;
        }

        body {
            margin: 0;
            font-family: var(--font-body);
            background-color: var(--color-bg-main);
            color: #333;
            overflow-x: hidden;
        }

        h1, h2, h3, h4 {
            font-family: var(--font-title);
            font-weight: 600;
            color: #000;
        }
        
        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 0 40px;
        }

        .btn {
            display: inline-block;
            background-color: var(--color-button);
            color: var(--color-button-text);
            padding: 15px 40px;
            font-size: 24px;
            font-family: var(--font-body);
            border: 1px solid var(--color-text);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #8e7352;
        }

        /* Top Banner */
        .top-banner {
            background-color: var(--color-bg-accent);
            text-align: center;
            padding: 10px 0;
            font-family: var(--font-title);
            font-style: italic;
            font-weight: 600;
            font-size: 20px;
        }

        /* Header */
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 40px;
        }

        .main-header .logo img {
            width: 120px; /* Ajustez la taille du logo */
        }
        
        .main-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 50px;
        }

        .main-nav a {
            font-family: var(--font-title);
            font-size: 30px;
            font-weight: 600;
            padding-bottom: 5px;
        }

        .main-nav a.active {
            border-bottom: 2px solid var(--color-text);
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-icons img {
            width: 32px;
            height: 32px;
        }

        /* Hero Section */
        .hero {
            position: relative;
            text-align: left;
            color: #000;
        }

        .hero img.hero-background {
            width: 100%;
            height: 660px;
            object-fit: cover;
            display: block;
        }
        
        .hero .hero-content {
            position: absolute;
            top: 50%;
            left: 89px;
            transform: translateY(-50%);
            max-width: 700px;
        }

        .hero h1 {
            font-size: 60px;
            margin: 0;
        }

        .hero p {
            font-size: 35px;
            margin: 20px 0 40px;
        }
        
        /* Story Section */
        .story-section {
            display: flex;
            align-items: center;
            gap: 50px;
            padding: 100px 80px;
            justify-content: center;
        }

        .story-content {
            max-width: 650px;
        }

        .story-content h2 {
            font-size: 50px;
            margin-top: 0;
        }

        .story-content p {
            font-size: 35px;
            line-height: 1.6;
            margin-bottom: 40px;
        }
        
        .story-image img {
            width: 417px;
            height: 626px;
            object-fit: cover;
        }

        /* Category Grid */
        .category-grid {
            display: flex;
            justify-content: center;
            gap: 40px;
            padding: 50px 80px;
            text-align: center;
        }

        .category-card img {
            width: 330px;
            height: 450px;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .category-card a {
            font-family: var(--font-title);
            font-weight: 700;
            font-size: 35px;
        }

        /* Nouveauté Section */
        .section-title {
            text-align: center;
            font-size: 50px;
            margin: 80px 0 50px;
        }

        /* Testimonials Section */
        .testimonials {
            padding: 100px 40px;
            background-image: url('path/to/testimonials-background.jpg'); /* Mettez une image de fond si besoin */
            background-size: cover;
            background-position: center;
        }

        .testimonial-grid {
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        .testimonial-card {
            background-color: var(--color-bg-light);
            padding: 80px 30px 30px;
            text-align: center;
            max-width: 400px;
            position: relative;
        }
        
        .testimonial-card .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            position: absolute;
            top: -75px;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid white;
        }

        .testimonial-card h3 {
            font-size: 28px;
            font-family: var(--font-body);
            font-weight: 700;
        }
        
        .testimonial-card blockquote {
            border: none;
            margin: 0;
            padding: 0;
        }

        .testimonial-card p {
            font-size: 24px;
            line-height: 1.5;
            font-style: italic;
        }
        
        .testimonial-card cite {
            display: block;
            text-align: right;
            font-size: 24px;
            margin-top: 20px;
            font-style: normal;
        }

        /* Info Bar */
        .info-bar {
            padding: 80px 40px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        .info-item {
            max-width: 300px;
        }
        .info-item img {
            width: 100px; /* Ajustez la taille de l'icône */
            margin-bottom: 20px;
        }

        /* Footer */
        .main-footer {
            background-color: var(--color-bg-accent);
            padding: 60px 40px;
            color: #000;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 50px;
            max-width: 1200px;
            margin: 0 auto;
            font-size: 23px;
        }
        
        .footer-column h4 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        .footer-column ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-column li {
            margin-bottom: 10px;
        }

        .footer-column a:hover {
            text-decoration: underline;
        }

        .newsletter-form {
            display: flex;
            margin-top: 15px;
        }

        .newsletter-form input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #000;
            background: white;
            font-size: 20px;
        }
        .newsletter-form button {
            padding: 10px 20px;
            border: 1px solid #000;
            background: #333;
            color: white;
            cursor: pointer;
            font-size: 20px;
        }

        @media (max-width: 768px) {
            /* Mettez ici les styles pour rendre la page responsive sur mobile.
               Par exemple, passer les grilles en une seule colonne.
               C'est ce que le code de Figma ne peut PAS faire. */
            .main-header { flex-direction: column; gap: 20px; }
            .main-nav ul { gap: 20px; font-size: 24px; }
            .hero .hero-content { position: static; transform: none; padding: 40px; }
            .story-section, .testimonial-grid, .footer-grid { flex-direction: column; }
            .category-grid { flex-direction: column; align-items: center; }
        }

    </style>
</head>
<body>

    <div class="top-banner">
        <p>Profitez de la livraison gratuite dès 40 € d’achat avec le code FREESHIP</p>
    </div>

    <header class="main-header container">
        <div class="logo">
            <!-- REMPLACEZ PAR VOTRE VRAI LOGO -->
            <a href="/"><img src="path/to/logo-les-pages-parfumees.png" alt="Logo Les Pages Parfumées"></a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="#" class="active">Accueil</a></li>
                <li><a href="#">Boutique</a></li>
                <li><a href="#">Notre Histoire</a></li>
                <li><a href="#">Nouveauté</a></li>
            </ul>
        </nav>
        <div class="header-icons">
            <!-- REMPLACEZ PAR VOS VRAIES ICÔNES (SVG DE PRÉFÉRENCE) -->
            <a href="#"><img src="path/to/search-icon.svg" alt="Rechercher"></a>
            <a href="#"><img src="path/to/user-icon.svg" alt="Compte"></a>
            <a href="#"><img src="path/to/cart-icon.svg" alt="Panier"></a>
        </div>
    </header>

    <main>
        <section class="hero">
            <!-- REMPLACEZ PAR VOTRE VRAIE IMAGE DE BANNIÈRE -->
            <img src="path/to/hero-background.jpg" alt="Bougie allumée à côté d'un livre ouvert" class="hero-background">
            <div class="hero-content">
                <h1>Entre Pages Et Parfums</h1>
                <p>Offrez-vous une atmosphère de lecture douce et inspirante</p>
                <a href="#" class="btn">Commencer la découverte</a>
            </div>
        </section>

        <section class="story-section">
            <div class="story-content">
                <h2>Notre Histoire</h2>
                <p>Les Pages Parfumées est née d’un amour pour les livres et la lumière douce des bougies. Nous rassemblons des livres anciens pleins de charme, associés à des bougies artisanales pour offrir des instants de lecture apaisants.</p>
                <a href="#" class="btn">En savoir plus</a>
            </div>
            <div class="story-image">
                <!-- REMPLACEZ PAR VOTRE IMAGE -->
                <img src="path/to/story-image.jpg" alt="Ambiance de lecture avec bougies et livres">
            </div>
        </section>

        <section class="category-preview container">
            <div class="category-grid">
                <div class="category-card">
                    <img src="path/to/category-books.jpg" alt="Pile de livres anciens">
                    <a href="#">View category</a>
                </div>
                <div class="category-card">
                    <img src="path/to/category-candles.jpg" alt="Collection de bougies parfumées">
                    <a href="#">View category</a>
                </div>
                <div class="category-card">
                    <img src="path/to/category-sets.jpg" alt="Coffret cadeau livre et bougie">
                    <a href="#">View category</a>
                </div>
            </div>
        </section>

        <h2 class="section-title">Nouveauté</h2>
        
        <section class="product-grid container">
            <div class="category-grid">
                 <!-- PHP: Ici, vous feriez une boucle sur vos produits
                 Exemple: foreach ($products as $product) { ... } -->
                <div class="category-card">
                    <img src="path/to/new-product-1.jpg" alt="Nouveau produit 1">
                </div>
                <div class="category-card">
                    <img src="path/to/new-product-2.jpg" alt="Nouveau produit 2">
                </div>
                <div class="category-card">
                    <img src="path/to/new-product-3.jpg" alt="Nouveau produit 3">
                </div>
            </div>
        </section>

        <section class="testimonials">
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <img class="avatar" src="path/to/avatar-camille.jpg" alt="Avatar de Camille">
                    <h3>Cadeau parfait !</h3>
                    <blockquote>
                        <p>“J’ai offert un coffret livre + bougie à ma sœur : elle a adoré l’odeur et a passé sa soirée à lire. L’emballage était magnifique.”</p>
                        <cite>— Camille D.</cite>
                    </blockquote>
                </div>
                <div class="testimonial-card">
                     <img class="avatar" src="path/to/avatar-julie.jpg" alt="Avatar de Julie">
                    <h3>Une vraie bulle de douceur</h3>
                    <blockquote>
                        <p>“La bougie sent divinement bon et le livre choisi était exactement dans mes goûts. On sent l’attention dans chaque détail.”</p>
                        <cite>— Julie M.</cite>
                    </blockquote>
                </div>
                <div class="testimonial-card">
                     <img class="avatar" src="path/to/avatar-thomas.jpg" alt="Avatar de Thomas">
                    <h3>Un moment pour soi</h3>
                    <blockquote>
                        <p>“Un concept que j’adore. Recevoir un colis aussi soigné donne envie de ralentir, de lire, de respirer. Bravo !”</p>
                        <cite>— Thomas B.</cite>
                    </blockquote>
                </div>
            </div>
        </section>

    </main>

    <footer class="main-footer">
        <div class="footer-grid">
            <div class="footer-column">
                <h4>Les Pages Parfumées</h4>
                <a href="mailto:customerservice@lespagesparfumees.com" style="text-decoration: underline;">customerservice@lespagesparfumees.com</a>
            </div>
            <div class="footer-column">
                <ul>
                    <li><a href="#">Frequently Asked Questions (FAQs)</a></li>
                    <li><a href="#">Retour et Remboursement</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Mon Compte</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Newsletter</h4>
                <p>Recevez une offre sur votre premier achat et des promotions exclusives en vous abonnant.</p>
                <form action="#" method="post" class="newsletter-form">
                    <input type="email" name="email" placeholder="Email" required>
                    <button type="submit">Send</button>
                </form>
            </div>
        </div>
    </footer>

</body>
</html>