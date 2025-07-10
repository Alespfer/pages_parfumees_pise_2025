<?php
/**
 * Fichier : /partials/footer.php
 * Rôle : Pied de page commun à toutes les pages du site.
 * Il ferme la structure HTML et charge les scripts JS.
 */
?>

    </main> <!-- Fermeture de la balise <main> ouverte dans chaque page de contenu -->

    <!-- =================================================================== -->
    <!--                            PIED DE PAGE                             -->
    <!-- =================================================================== -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>À propos</h5>
                    <p class="text-white-50">L'Atelier des Mots & Lumières, votre boutique de livres d'occasion et de bougies artisanales pour des ambiances uniques.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Liens utiles</h5>
                    <ul class="list-unstyled">
                        <!-- Note: Ces liens ne fonctionneront que si vous créez les pages correspondantes -->
                        <li><a href="contact.php" class="text-white-50">Contactez-nous</a></li>
                        <li><a href="#" class="text-white-50">Mentions Légales</a></li>
                        <li><a href="#" class="text-white-50">Conditions Générales de Vente</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Suivez-nous</h5>
                    <!-- Ici, vous pourriez ajouter des icônes de réseaux sociaux -->
                    <a href="#" class="text-white-50">Facebook</a> | 
                    <a href="#" class="text-white-50">Instagram</a>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-center">
                    <p class="text-white-50 mb-0">© <?php echo date('Y'); ?> - L'Atelier des Mots & Lumières. Projet universitaire réalisé par [Votre Nom].</p>
                </div>
            </div>
        </div>
    </footer>


    <!-- =================================================================== -->
    <!--                      SCRIPTS JAVASCRIPT                             -->
    <!-- =================================================================== -->
    <!-- Les scripts sont placés à la fin pour un chargement plus rapide de la page. -->
    <!-- Pour que les composants Bootstrap (comme le menu déroulant) fonctionnent, -->
    <!-- il faut inclure jQuery, puis Popper.js, puis le JavaScript de Bootstrap. -->
    <!-- (Version pour Bootstrap 4) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
   
</body>
</html>

</body>
</html>