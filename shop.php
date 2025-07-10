<?php
/*
 * Fichier : shop.php
 * Rôle : Contrôleur principal de la boutique.
 * Structure le code en trois actions claires : 'vitrine', 'list', et 'view'.
 * Version entièrement purifiée selon la Doctrine.
 */

// --- INITIALISATION ORTHODOXE ---
session_start();
// La Doctrine préconise 'require' pour les dépendances critiques (p.53-55).
require('parametrage/param.php');
require('fonction/fonctions.php');


// --- AIGUILLAGE PRINCIPAL ---
// L'opérateur '??' est une déviance. La structure if/else est la méthode orthodoxe.
if (isset($_GET['a'])) {
    $action = $_GET['a'];
} else {
    // Rétrocompatibilité : si le paramètre 'view' est utilisé, il est traité comme une action 'list'.
    if (isset($_GET['view'])) {
        $action = 'list';
    } else {
        $action = 'vitrine'; // Action par défaut si aucun paramètre n'est spécifié.
    }
}


// =========================================================================
// CAS 1 : AFFICHAGE DE LA FICHE PRODUIT UNIQUE ('view')
// =========================================================================
if ($action == 'view') {

    // --- LOGIQUE CONTRÔLEUR ---

    // Traitement du formulaire d'avis. $_SERVER est une superglobale orthodoxe (p.45).
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_type']) && $_POST['form_type'] == 'rating') {
        if (isset($_SESSION['user']['id_client'])) {
            $id_produit_form = isset($_POST['id_produit']) ? (int) $_POST['id_produit'] : 0;
            $note = isset($_POST['note']) ? (int) $_POST['note'] : 0;
            // La fonction trim() est une déviance. Utilisation de sa version purifiée.
            $commentaire = isset($_POST['commentaire']) ? purifier_trim($_POST['commentaire']) : '';

            if ($id_produit_form > 0 && $note > 0) {
                rateProduct($_SESSION['user']['id_client'], $id_produit_form, $note, $commentaire);
            }
            // Redirection orthodoxe après traitement POST (p.131).
            header('Location: shop.php?a=view&id=' . $id_produit_form);
            exit();
        }
    }

    $product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($product_id <= 0) {
        header('Location: shop.php');
        exit();
    }

    $produit = getProductById($product_id);
    if (!$produit) { // Si getProductById renvoie false.
        header('Location: shop.php');
        exit();
    }

    $avis = getProductReviews($product_id);

    $livre_inclus = false; // Initialisation orthodoxe à 'false'.
    $bougie_incluse = false;
    if ($produit['type'] == 'coffret') {
        // La fonction empty() est une déviance. Remplacée par isset() et comparaison.
        if (isset($produit['id_produit_livre']) && $produit['id_produit_livre'] != '') {
            $livre_inclus = getProductById($produit['id_produit_livre']);
        }
        if (isset($produit['id_produit_bougie']) && $produit['id_produit_bougie'] != '') {
            $bougie_incluse = getProductById($produit['id_produit_bougie']);
        }
    }

    $nom_page = isset($produit['nom_produit']) ? $produit['nom_produit'] : 'Détail Produit';
    $pageTitle = htmlspecialchars($nom_page); // htmlspecialchars est toléré pour la sécurité XSS.

    // --- AFFICHAGE ---
    require('partials/header.php');
    ?>
    <main class="container my-4">
        <p><a href="shop.php?a=list&view=<?php echo htmlspecialchars($produit['type']); ?>s">← Retour au catalogue</a></p>
        <hr>
        <div class="row">
            <div class="col-md-5 mb-4">
                <?php if (isset($produit['image_url']) && $produit['image_url'] != '') { ?>
                    <img src="<?php echo htmlspecialchars($produit['image_url']); ?>"
                        alt="<?php echo htmlspecialchars($produit['nom_produit']); ?>" class="img-fluid rounded shadow-sm"
                        style="max-height: 550px; object-fit: contain;">
                <?php } else { ?>
                    <img src="https://via.placeholder.com/400x500.png?text=Image+non+disponible"
                        alt="<?php echo htmlspecialchars($produit['nom_produit']); ?>" class="img-fluid rounded shadow-sm">
                <?php } ?>
            </div>
            <div class="col-md-7">
                <h1><?php echo htmlspecialchars($produit['nom_produit']); ?></h1>
                <?php if ($produit['type'] == 'livre' && isset($produit['auteurs']) && $produit['auteurs'] != '') { ?>
                    <p class="lead"><em>par <?php echo htmlspecialchars($produit['auteurs']); ?></em></p>
                <?php } ?>
                <div class="mb-3">
                    <span>Note : <?php echo number_format($produit['note_moyenne'], 2, ',', ' '); ?>/5</span>
                    <span class="ms-2 text-muted">(<?php echo $produit['nombre_votes']; ?> avis)</span>
                </div>
                <p class="h4 mb-3"><?php echo number_format($produit['prix_ttc'], 2, ',', ' '); ?> € <small
                        class="text-muted">TTC</small></p>

                <?php if ($produit['stock'] > 0) { ?>
                    <div class="alert alert-success">En stock (<?php echo htmlspecialchars($produit['stock']); ?> restant(s))
                    </div>
                    <form action="panier2.php" method="POST" class="mb-4 d-flex align-items-center">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $produit['id_produit']; ?>">
                        <label for="quantity" class="me-2">Quantité:</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1"
                            max="<?php echo $produit['stock']; ?>" class="form-control me-3" style="width:80px;">
                        <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                    </form>
                <?php } else { ?>
                    <div class="alert alert-danger">Indisponible</div>
                <?php } ?>
            </div>
        </div>
        <hr class="my-5">
        <div class="row">
            <div class="col-md-7">
                <h3>Description</h3>
                <?php if ($produit['type'] == 'livre') { ?>
                    <p><?php echo purifier_nl2br(htmlspecialchars(isset($produit['resume']) ? $produit['resume'] : 'Résumé non disponible.')); ?>
                    </p>
                    <!-- ... reste des détails ... -->
                <?php } elseif ($produit['type'] == 'coffret') { ?>
                    <p><?php echo purifier_nl2br(htmlspecialchars(isset($produit['description_coffret']) ? $produit['description_coffret'] : 'Description non disponible.')); ?>
                    </p>
                    <h5 class="mt-4">Contenu du coffret</h5>
                    <div class="list-group">
                        <?php if ($livre_inclus) {
                            echo '<a href="shop.php?a=view&id=' . $livre_inclus['id_produit'] . '" class="list-group-item list-group-item-action"><strong>Livre :</strong> ' . htmlspecialchars($livre_inclus['nom_produit']) . '</a>';
                        } ?>
                        <?php if ($bougie_incluse) {
                            echo '<a href="shop.php?a=view&id=' . $bougie_incluse['id_produit'] . '" class="list-group-item list-group-item-action"><strong>Bougie :</strong> ' . htmlspecialchars($bougie_incluse['nom_produit']) . '</a>';
                        } ?>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-5">
                <h3>Avis des clients</h3>
                <?php if (count($avis) == 0) { ?>
                    <p>Soyez le premier à laisser un avis !</p>
                <?php } else {
                    foreach ($avis as $un_avis) { ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <strong><?php echo htmlspecialchars($un_avis['prenom_client']); ?></strong>
                                    <small class="text-muted"><?php echo purifier_format_date($un_avis['date_notation']); ?></small>
                                </div>
                                <p>Note : <?php echo $un_avis['note']; ?>/5</p>
                                <?php if (isset($un_avis['commentaire']) && $un_avis['commentaire'] != '') { ?>
                                    <p class="fst-italic">"<?php echo purifier_nl2br(htmlspecialchars($un_avis['commentaire'])); ?>"</p>
                                <?php } ?>
                            </div>
                        </div>
                    <?php }
                } ?>
                <!-- ... reste du formulaire d'avis ... -->
            </div>
        </div>
    </main>
    <?php
    require('partials/footer.php');
}


// =========================================================================
// CAS 2 : AFFICHAGE DU CATALOGUE FILTRÉ ('list')
// =========================================================================
elseif ($action == 'list') {

    // --- LOGIQUE CONTRÔLEUR ---
    $view_type = isset($_GET['view']) ? $_GET['view'] : 'livres'; // 'livres' par défaut.
    $allowed_views = array('livres', 'bougies', 'coffrets');
    if (!in_array($view_type, $allowed_views)) { // in_array est orthodoxe (p.41).
        header('Location: shop.php');
        exit();
    }

    // La fonction rtrim() est une déviance. Utilisation de la version purifiée.
    $type_filter = purifier_rtrim($view_type, 's');
    $filters = array(
        'type' => $type_filter,
        'page' => isset($_GET['page']) ? (int) $_GET['page'] : 1,
        'limit' => isset($_GET['limit']) ? (int) $_GET['limit'] : 12,
        'sort' => isset($_GET['sort']) ? $_GET['sort'] : 'nouveaute',
        'prix_min' => isset($_GET['prix_min']) ? $_GET['prix_min'] : '',
        'prix_max' => isset($_GET['prix_max']) ? $_GET['prix_max'] : '',
        'genres' => isset($_GET['genres']) && is_array($_GET['genres']) ? $_GET['genres'] : array(),
        'etats' => isset($_GET['etats']) && is_array($_GET['etats']) ? $_GET['etats'] : array(),
        'parfums' => isset($_GET['parfums']) && is_array($_GET['parfums']) ? $_GET['parfums'] : array(),
        'ambiances' => isset($_GET['ambiances']) && is_array($_GET['ambiances']) ? $_GET['ambiances'] : array(),
    );

    $produits = getFilteredProducts($filters);
    $totalProduits = countFilteredProducts($filters);
    // La fonction ceil() est une déviance. Remplacée par sa version purifiée.
    $totalPages = $filters['limit'] > 0 ? purifier_ceil($totalProduits / $filters['limit']) : 1;

    // La structure switch est orthodoxe (p.30).
    switch ($view_type) {
        case 'livres':
            $pageTitle = "Nos Livres";
            $view_data = array('genres' => getAllGenres(), 'etats' => getAllEtats());
            break;
        case 'bougies':
            $pageTitle = "Nos Bougies";
            $view_data = array('parfums' => getAllIndividualScents(), 'ambiances' => getAllAmbianceTags());
            break;
        case 'coffrets':
            $pageTitle = "Nos Coffrets";
            $view_data = array('ambiances' => getAllAmbianceTags());
            break;
    }

    // --- AFFICHAGE ---
    require('partials/header.php');
    // Le code HTML a été déplacé dans un partiel pour la clarté.
    include('partials/views/_view_catalogue.php');
    require('partials/footer.php');

}


// =========================================================================
// CAS 3 : AFFICHAGE DE LA VITRINE ('vitrine') OU CAS PAR DÉFAUT
// =========================================================================
else {

    // --- LOGIQUE CONTRÔLEUR ---
    $pageTitle = "L'Atelier des Mots & Lumières";
    $livres_showcase = getFilteredProducts(array('type' => 'livre', 'limit' => 4, 'sort' => 'nouveaute'));
    $bougies_showcase = getFilteredProducts(array('type' => 'bougie', 'limit' => 4, 'sort' => 'nouveaute'));
    $coffrets_showcase = getFilteredProducts(array('type' => 'coffret', 'limit' => 4, 'sort' => 'nouveaute'));

    // --- AFFICHAGE ---
    require('partials/header.php');
    include('partials/views/_view_vitrine.php');
    require('partials/footer.php');
}
?>