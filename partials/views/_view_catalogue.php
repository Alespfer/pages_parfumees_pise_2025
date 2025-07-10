<?php
/*
 * Fichier partiel : _view_catalogue.php
 * Rôle : Affiche la structure de la page catalogue avec ses filtres et la liste des produits.
 * Ce fichier est inclus par shop.php lorsque l'action est 'catalogue'.
 */
?>
<div class="container my-5">
    <div class="text-center p-4 mb-5" style="background-color: #f8f9fa; border-radius: .25rem;">
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
        <p class="lead">Filtrez et découvrez notre sélection.</p>
    </div>

    <!-- Navigation pour changer de type de catalogue -->
    <ul class="nav nav-pills justify-content-center mb-4">
        <li class="nav-item"><a class="nav-link <?php if ($type_vue == 'livres') echo 'active'; ?>" href="shop.php?a=catalogue&view=livres">Livres</a></li>
        <li class="nav-item"><a class="nav-link <?php if ($type_vue == 'bougies') echo 'active'; ?>" href="shop.php?a=catalogue&view=bougies">Bougies</a></li>
        <li class="nav-item"><a class="nav-link <?php if ($type_vue == 'coffrets') echo 'active'; ?>" href="shop.php?a=catalogue&view=coffrets">Coffrets</a></li>
    </ul>

    <!-- Structure principale de la page en deux colonnes -->
    <div class="row">

        <!-- Colonne 1 : Le formulaire de filtres -->
        <div class="col-lg-3">
            <form method="GET" action="shop.php">
                <input type="hidden" name="a" value="catalogue">
                <input type="hidden" name="view" value="<?php echo htmlspecialchars($type_vue); ?>">
                <div class="p-3 bg-light rounded">
                    <?php
                    // Inclusion dynamique du formulaire de filtre approprié.
                    if ($type_vue == 'livres') {
                        include('partials/filters/_sidebar_livres.php');
                    } elseif ($type_vue == 'bougies') {
                        include('partials/filters/_sidebar_bougies.php');
                    } elseif ($type_vue == 'coffrets') {
                        include('partials/filters/_sidebar_coffrets.php');
                    }
                    ?>
                    <button type="submit" class="btn btn-primary w-100 mt-3">Appliquer les filtres</button>
                    <a href="shop.php?a=catalogue&view=<?php echo htmlspecialchars($type_vue); ?>" class="btn btn-link w-100 mt-2 text-decoration-none">Réinitialiser</a>
                </div>
            </form>
        </div>

        <!-- Colonne 2 : La grille de produits et la pagination -->
        <div class="col-lg-9">
            <!-- Barre de tri et d'affichage -->
            <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                <span class="text-muted"><strong><?php echo $totalProduits; ?></strong> produit(s)</span>
                
                <form method="GET" action="shop.php" class="d-flex align-items-center">
                    <?php
                    // Boucle orthodoxe pour préserver les paramètres GET lors du changement de tri/limite.
                    // Cette technique évite de perdre les filtres appliqués par l'utilisateur.
                    foreach ($_GET as $cle => $valeur) {
                        // On exclut les paramètres qui sont gérés par les <select> pour ne pas les dupliquer.
                        if ($cle != 'sort' && $cle != 'limit') {
                            echo '<input type="hidden" name="' . htmlspecialchars($cle) . '" value="' . htmlspecialchars($valeur) . '">';
                        }
                    }
                    ?>
                    <label for="limit" class="form-label me-2 mb-0 text-nowrap">Afficher:</label>
                    <select name="limit" id="limit" class="form-select form-select-sm me-3" onchange="this.form.submit()">
                        <option value="12" <?php if ($filtres['limit'] == 12) echo 'selected'; ?>>12</option>
                        <option value="24" <?php if ($filtres['limit'] == 24) echo 'selected'; ?>>24</option>
                        <option value="36" <?php if ($filtres['limit'] == 36) echo 'selected'; ?>>36</option>
                    </select>

                    <label for="sort" class="form-label me-2 mb-0 text-nowrap">Trier par:</label>
                    <select name="sort" id="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="nouveaute" <?php if ($filtres['sort'] == 'nouveaute') echo 'selected'; ?>>Nouveautés</option>
                        <option value="prix_asc" <?php if ($filtres['sort'] == 'prix_asc') echo 'selected'; ?>>Prix croissant</option>
                        <option value="prix_desc" <?php if ($filtres['sort'] == 'prix_desc') echo 'selected'; ?>>Prix décroissant</option>
                    </select>
                </form>
            </div>

            <!-- Grille des produits -->
            <?php include('partials/views/_grid_produits.php'); ?>

            <!-- Pagination -->
            <?php if ($totalPages > 1) { ?>
                <nav class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                            <li class="page-item <?php if ($i == $filtres['page']) echo 'active'; ?>">
                                <?php
                                // Construction de l'URL pour chaque page de la pagination.
                                $parametres_page = $_GET;
                                $parametres_page['page'] = $i;
                                // La fonction http_build_query n'est pas orthodoxe.
                                // Nous reconstruisons la chaîne de paramètres manuellement.
                                $chaine_params = 'a=catalogue&';
                                foreach ($parametres_page as $cle => $val) {
                                    if ($cle != 'a') { // Le paramètre 'a' est déjà ajouté.
                                         $chaine_params = $chaine_params . htmlspecialchars($cle) . '=' . htmlspecialchars($val) . '&';
                                    }
                                }
                                ?>
                                <a class="page-link" href="?<?php echo $chaine_params; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>
            <?php } ?>
        </div>
    </div>
</div>