<?php
/**
 * Fichier : /partials/footer.php
 * Rôle : Pied de page commun à toutes les pages du site.
 */
?>
</main> <!-- Fermeture de la balise <main> ouverte dans chaque page -->

<footer class="main-footer">
    <div class="container">
        <div class="main-footer__grid">

            <div class="main-footer__column">
                <h4>Les Pages Parfumées</h4>
                <p>
                    Une sélection de livres, bougies et coffrets pour des moments de lecture uniques et apaisants.
                </p>
            </div>

            <div class="main-footer__column">
                <h4>Service Client</h4>
                <ul>
                    <li><a href="contact.php?view=faq">Questions fréquentes (FAQ)</a></li>
                    <li><a href="#">Livraison & Retours</a></li>
                    <li><a href="contact.php">Nous Contacter</a></li>
                    <li><a href="#">Mentions Légales</a></li>
                </ul>
            </div>

            <div class="main-footer__column">
                <h4>Mon Compte</h4>
                <ul>
                    <li><a href="<?php echo isset($_SESSION['user']) ? 'mon_compte.php' : 'auth.php'; ?>">Mon Compte</a>
                    </li>
                    <li><a href="<?php echo isset($_SESSION['user']) ? 'mon_compte.php' : 'auth.php'; ?>">Suivre ma
                            commande</a></li>
                    <li><a href="panier.php">Mon Panier</a></li>
                </ul>
            </div>

            <!-- LA CORRECTION EST ICI : LES RÉSEAUX SOCIAUX SONT MAINTENANT DANS LEUR PROPRE COLONNE. -->
            <div class="main-footer__column">
                <h4>Suivez-nous</h4>
                <div class="main-footer__socials">
                    <a href="#" title="Facebook"><img src="ressources/reseaux/facebook.png" alt="Facebook"></a>
                    <a href="#" title="Instagram"><img src="ressources/reseaux/insta.png" alt="Instagram"></a>
                    <a href="#" title="TikTok"><img src="ressources/reseaux/tiktok.png" alt="TikTok"></a>
                    <a href="#" title="Linkedin"><img src="ressources/reseaux/linkedin.png" alt="Linkedin"></a>
                    <a href="#" title="Youtube"><img src="ressources/reseaux/youtube.png" alt="Youtube"></a>
                </div>
            </div>

        </div> <!-- Fin de .main-footer__grid -->

        <div class="main-footer__copyright">
            <!-- LA CORRECTION EST ICI : Le texte est dans un paragraphe. C'est plus propre. -->
            <p>© <?php echo date('Y'); ?> Les Pages Parfumées. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<!-- ===================================================
     SCRIPTS JAVASCRIPT
     =================================================== -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const accordionItems = document.querySelectorAll('.accordion-item');

        accordionItems.forEach(item => {
            const header = item.querySelector('.accordion-header');
            const content = item.querySelector('.accordion-content');
            const icon = item.querySelector('.accordion-icon');

            header.addEventListener('click', () => {
                const isActive = item.classList.contains('active');

                // Ferme tous les autres items
                accordionItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                        otherItem.querySelector('.accordion-content').style.maxHeight = null;
                        otherItem.querySelector('.accordion-icon').textContent = '+';
                    }
                });

                // Ouvre ou ferme l'item cliqué
                if (!isActive) {
                    item.classList.add('active');
                    content.style.maxHeight = content.scrollHeight + 'px';
                    icon.textContent = '−';
                } else {
                    // Si on reclique sur l'item déjà ouvert, on le ferme
                    item.classList.remove('active');
                    content.style.maxHeight = null;
                    icon.textContent = '+';
                }
            });
        });
    });
</script>


</body>

</html>