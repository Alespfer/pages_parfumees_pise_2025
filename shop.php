<?php
/*
 * Fichier : shop.php
 * Rôle : Contrôleur et Vue principal de la boutique.
 * Ce fichier est un contrôleur frontal qui gère trois affichages distincts :
 *  1. La 'vitrine' : la page d'accueil de la boutique.
 *  2. La 'list' : la vue catalogue, avec ses filtres et sa pagination.
 *  3. La 'view' : la fiche détaillée d'un produit unique.
 * L'action à afficher est déterminée par les paramètres dans l'URL.
 */


// --- INITIALISATION  ---
session_start();
require('parametrage/param.php');
require('fonction/fonctions.php');


$activePage = 'boutique'; 


// --- AIGUILLAGE PRINCIPAL ---
// On détermine l'action à effectuer en fonction des paramètres GET 
if (isset($_GET['a'])) {
    $action = $_GET['a'];
} else {
    if (isset($_GET['view'])) {
        $action = 'list';
    } else {
        $action = 'vitrine';
    }
}

// --- INITIALISATION DES VARIABLES DE VUE ---
$pageTitle = SITE_NAME;
$produit = array();
$avis = array();
$livre_inclus = false;
$bougie_incluse = false;
$produits = array();
$totalProduits = 0;
$totalPages = 0;
$filters = array();
$view_data = array();
$livres_showcase = array();
$bougies_showcase = array();
$coffrets_showcase = array();
$priceBounds = getMinMaxPrices();

// --- JETON D'IDEMPOTENCE (Protection contre l'ajout multiple au panier) ---
$user_identifier = isset($_SESSION['user']['id_client']) ? $_SESSION['user']['id_client'] : 'visiteur_anonyme';
$_SESSION['add_to_cart_token'] = custom_hash(time() . $user_identifier);


// =========================================================================
// SECTION DE TRAITEMENT DE LA LOGIQUE (LE CONTRÔLEUR)
// =========================================================================


// --- LOGIQUE POUR LA FICHE PRODUIT ('view') ---

if ($action == 'view') {
    // Traitement du formulaire d'ajout d'avis.
    if (isset($_POST['form_type']) && $_POST['form_type'] == 'rating') {
        if (isset($_SESSION['user']['id_client'])) {
            $id_produit_form = isset($_POST['id_produit']) ? (int) $_POST['id_produit'] : 0;
            $note = isset($_POST['note']) ? (int) $_POST['note'] : 0;
            $commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : '';
            if ($id_produit_form > 0 && $note > 0) {
                rateProduct($_SESSION['user']['id_client'], $id_produit_form, $note, $commentaire);
            }
            header('Location: shop.php?a=view&id=' . $id_produit_form);
            exit();
        }
    }


    // Préparation des données pour l'affichage de la fiche produit.

    $product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($product_id <= 0) {
        header('Location: shop.php');
        exit();
    }
    $produit = getProductById($product_id);
    if (!$produit) {
        header('Location: shop.php');
        exit();
    }
    $avis = getProductReviews($product_id);
    if ($produit['type'] == 'coffret') {
        if (isset($produit['id_produit_livre']) && $produit['id_produit_livre'] != '') {
            $livre_inclus = getProductById($produit['id_produit_livre']);
        }
        if (isset($produit['id_produit_bougie']) && $produit['id_produit_bougie'] != '') {
            $bougie_incluse = getProductById($produit['id_produit_bougie']);
        }
    }
    $pageTitle = isset($produit['nom_produit']) ? htmlspecialchars($produit['nom_produit']) : 'Détail Produit';

} elseif ($action == 'list') {
    // --- LOGIQUE POUR LE CATALOGUE (vue filtrée) ---
    $view_type = isset($_GET['view']) ? $_GET['view'] : 'livres';
    $allowed_views = array('livres', 'bougies', 'coffrets');
    if (!in_array($view_type, $allowed_views)) {
        header('Location: shop.php');
        exit();
    }


    // On prépare le tableau des filtres en récupérant les données de l'URL.

    $type_filter = rtrim($view_type, 's');
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

    // On exécute les requêtes pour récupérer les produits et la pagination.

    $produits = getFilteredProducts($filters);
    $totalProduits = countFilteredProducts($filters);
    $totalPages = $filters['limit'] > 0 ? ceil($totalProduits / $filters['limit']) : 1;

    // --- Préparation des données pour le slider de prix ---
    $bounds = $priceBounds;
    $startMin = ($filters['prix_min'] !== '') ? (int) $filters['prix_min'] : $bounds['min'];
    $startMax = ($filters['prix_max'] !== '') ? (int) $filters['prix_max'] : $bounds['max'];


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

} else {
    // --- LOGIQUE POUR LA VITRINE (action par défaut) ---
    $action = 'vitrine';
    $pageTitle = "Tous Nos Produits";
    $livres_showcase = getFilteredProducts(array('type' => 'livre', 'limit' => 4, 'sort' => 'nouveaute'));
    $bougies_showcase = getFilteredProducts(array('type' => 'bougie', 'limit' => 4, 'sort' => 'nouveaute'));
    $coffrets_showcase = getFilteredProducts(array('type' => 'coffret', 'limit' => 4, 'sort' => 'nouveaute'));
}



// =========================================================================
// SECTION D'AFFICHAGE (LA VUE)
// =========================================================================
require('partials/header.php');
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css">

<script defer src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>



<?php
// On affiche la bonne section HTML en fonction de l'action déterminée par le contrôleur.
if ($action == 'view') {
    // Vue de la Fiche Produit.
    ?>
    <main class="container my-4">
        <p><a href="shop.php?a=list&view=<?php echo htmlspecialchars($produit['type']); ?>s">← Retour au catalogue</a></p>
        <hr>
        <div class="row">
            <div class="col-md-5 mb-4 product-detail-image-container">
                <?php if (isset($produit['image_url']) && $produit['image_url'] != '') { ?>
                    <img src="<?php echo htmlspecialchars(isset($produit['image_url']) ? $produit['image_url'] : ''); ?>"
                        alt="<?php echo htmlspecialchars(isset($produit['nom_produit']) ? $produit['nom_produit'] : ''); ?>"
                        class="img-fluid rounded shadow-sm">
                <?php } else { ?>
                    <img src="https://via.placeholder.com/400x500.png?text=Image+non+disponible"
                        alt="<?php echo htmlspecialchars(isset($produit['nom_produit']) ? $produit['nom_produit'] : ''); ?>"
                        class="img-fluid rounded shadow-sm">
                <?php } ?>
            </div>

            <div class="col-md-7">
                <h1><?php echo htmlspecialchars($produit['nom_produit']); ?></h1>
                <?php if ($produit['type'] == 'livre' && isset($produit['auteurs']) && $produit['auteurs'] != '') { ?>
                    <p class="lead"><em>par <?php echo htmlspecialchars($produit['auteurs']); ?></em></p>
                <?php } ?>
                <div class="mb-3 d-flex align-items-center">
                    <div class="rating-stars rating-stars-large"
                        title="Note : <?php echo number_format($produit['note_moyenne'], 2); ?>/5">
                        <div class="stars-foreground" style="width: <?php echo ($produit['note_moyenne'] / 5) * 100; ?>%;">
                            ★★★★★</div>
                        <div class="stars-background">★★★★★</div>
                    </div>
                    <span class="ms-2 text-muted">(<?php echo $produit['nombre_votes']; ?> avis)</span>
                </div>
                <p class="h4 mb-3"><?php echo number_format($produit['prix_ttc'], 2, ',', ' '); ?> € <small
                        class="text-muted">TTC</small></p>
                <?php if ($produit['stock'] > 0) { ?>
                    <div class="alert alert-success">En stock (<?php echo htmlspecialchars($produit['stock']); ?> restant(s))
                    </div>
                    <form action="panier.php" method="POST" class="mb-4 d-flex align-items-center"> <input type="hidden"
                            name="add_to_cart_token" value="<?php echo $_SESSION['add_to_cart_token']; ?>">

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
                <?php
                if ($produit['type'] == 'livre') {
                    $description = isset($produit['resume']) ? $produit['resume'] : 'Résumé non disponible.';
                    echo '<p>' . nl2br(htmlspecialchars($description)) . '</p>';
                    echo '<h5>Détails</h5><ul class="list-group list-group-flush">';
                    echo '<li class="list-group-item"><strong>Éditeur :</strong> ' . htmlspecialchars(isset($produit['editeur']) ? $produit['editeur'] : 'N/A') . '</li>';
                    echo '<li class="list-group-item"><strong>Année :</strong> ' . htmlspecialchars(isset($produit['annee_publication']) ? $produit['annee_publication'] : 'N/A') . '</li>';
                    echo '<li class="list-group-item"><strong>ISBN :</strong> ' . htmlspecialchars(isset($produit['isbn']) ? $produit['isbn'] : 'N/A') . '</li>';
                    echo '<li class="list-group-item"><strong>Pages :</strong> ' . htmlspecialchars(isset($produit['nb_pages']) ? $produit['nb_pages'] : 'N/A') . '</li>';
                    echo '<li class="list-group-item"><strong>État :</strong> ' . htmlspecialchars(isset($produit['etat']) ? $produit['etat'] : 'N/A') . '</li>';
                    echo '<li class="list-group-item"><strong>Genre(s) :</strong> ' . htmlspecialchars(isset($produit['genres']) ? $produit['genres'] : 'N/A') . '</li>';
                    echo '</ul>';
                } elseif ($produit['type'] == 'bougie') {
                    $description = isset($produit['description_bougie']) ? $produit['description_bougie'] : 'Description non disponible.';
                    echo '<p>' . nl2br(htmlspecialchars($description)) . '</p>';
                    echo '<h5>Détails</h5><ul class="list-group list-group-flush">';
                    echo '<li class="list-group-item"><strong>Parfum :</strong> ' . htmlspecialchars(isset($produit['parfum']) ? $produit['parfum'] : 'N/A') . '</li>';
                    echo '<li class="list-group-item"><strong>Durée :</strong> ' . htmlspecialchars(isset($produit['duree_combustion']) ? $produit['duree_combustion'] : 'N/A') . ' heures</li>';
                    echo '<li class="list-group-item"><strong>Poids :</strong> ' . htmlspecialchars(isset($produit['poids']) ? $produit['poids'] : 'N/A') . ' g</li>';
                    echo '</ul>';
                } elseif ($produit['type'] == 'coffret') {
                    $description = isset($produit['description_coffret']) ? $produit['description_coffret'] : 'Description non disponible.';
                    echo '<p>' . nl2br(htmlspecialchars($description)) . '</p>';
                    echo '<h5 class="mt-4">Contenu du coffret</h5><div class="list-group">';
                    if ($livre_inclus) {
                        echo '<a href="shop.php?a=view&id=' . $livre_inclus['id_produit'] . '" class="list-group-item list-group-item-action"><strong>Livre :</strong> ' . htmlspecialchars($livre_inclus['nom_produit']) . '</a>';
                    }
                    if ($bougie_incluse) {
                        echo '<a href="shop.php?a=view&id=' . $bougie_incluse['id_produit'] . '" class="list-group-item list-group-item-action"><strong>Bougie :</strong> ' . htmlspecialchars($bougie_incluse['nom_produit']) . '</a>';
                    }
                    echo '</div>';
                }
                ?>
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
                                    <small class="text-muted"><?php echo format_date($un_avis['date_notation']); ?></small>
                                </div>
                                <div class="rating-stars my-2">
                                    <div class="stars-foreground" style="width: <?php echo ($un_avis['note'] / 5) * 100; ?>%;">★★★★★
                                    </div>
                                    <div class="stars-background">★★★★★</div>
                                </div>
                                <?php if (isset($un_avis['commentaire']) && $un_avis['commentaire'] != '') { ?>
                                    <p class="fst-italic">"<?php echo nl2br(htmlspecialchars($un_avis['commentaire'])); ?>"</p>
                                <?php } ?>
                            </div>
                        </div>
                    <?php }
                } ?>
                <div class="mt-4">
                    <?php if (isset($_SESSION['user'])) { ?>
                        <div class="card bg-light p-3">
                            <h5>Laisser un avis :</h5>
                            <form action="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>" method="POST">
                                <input type="hidden" name="form_type" value="rating">
                                <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                                <div class="mb-3">
                                    <label class="form-label" for="note">Votre note</label>
                                    <select name="note" id="note" class="form-select" required>
                                        <option value="" disabled selected>-- Choisir une note --</option>
                                        <?php for ($i = 5; $i >= 1; $i--) { ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?>
                                                étoile<?php if ($i > 1) {
                                                    echo 's';
                                                } ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                                    <textarea name="commentaire" id="commentaire" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-dark w-100">Envoyer</button>
                            </form>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-secondary">
                            <a
                                href="auth.php?action=login&redirect=shop.php?a=view&id=<?php echo $produit['id_produit']; ?>">Connectez-vous</a>
                            pour laisser un avis.
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>
    <?php
} elseif ($action == 'list') {
    // Vue du Catalogue.

    ?>
    <main class="container my-5">
        <div class="text-center p-4 mb-5" style="background-color: #E5DDCE; border-radius: .25rem;">
            <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
            <p class="lead">Filtrez et découvrez notre sélection.</p>
        </div>
        <ul class="nav nav-pills justify-content-center mb-4">
            <li class="nav-item"><a class="nav-link <?php if ($view_type == 'livres') {
                echo 'active';
            } ?>" href="shop.php?a=list&view=livres">Livres</a></li>
            <li class="nav-item"><a class="nav-link <?php if ($view_type == 'bougies') {
                echo 'active';
            } ?>" href="shop.php?a=list&view=bougies">Bougies</a></li>
            <li class="nav-item"><a class="nav-link <?php if ($view_type == 'coffrets') {
                echo 'active';
            } ?>" href="shop.php?a=list&view=coffrets">Coffrets</a></li>
        </ul>
        <div class="row">
            <div class="col-lg-3">
                <form method="GET" action="shop.php">
                    <input type="hidden" name="a" value="list">
                    <input type="hidden" name="view" value="<?php echo htmlspecialchars($view_type); ?>">
                    <div class="p-3 bg-light rounded">
                        <h4>Filtrer par</h4>
                        <hr>
                        <?php
                        switch ($view_type) {
                            case 'livres':
                                ?>
                                <div class="mb-4">
                                    <h5>Genre</h5>
                                    <?php if (isset($view_data['genres']) && count($view_data['genres']) > 0) {
                                        foreach ($view_data['genres'] as $genre) {
                                            $est_coche = in_array($genre['id_genre'], $filters['genres']);
                                            echo '<div class="form-check"><input class="form-check-input" type="checkbox" name="genres[]" value="' . $genre['id_genre'] . '" id="genre_' . $genre['id_genre'] . '" ' . ($est_coche ? 'checked' : '') . '><label class="form-check-label" for="genre_' . $genre['id_genre'] . '">' . htmlspecialchars($genre['nom']) . '</label></div>';
                                        }
                                    } ?>
                                </div>
                                <div class="mb-4">
                                    <h5>État</h5>
                                    <?php if (isset($view_data['etats']) && count($view_data['etats']) > 0) {
                                        foreach ($view_data['etats'] as $etat) {
                                            $est_coche = in_array($etat, $filters['etats']);
                                            echo '<div class="form-check"><input class="form-check-input" type="checkbox" name="etats[]" value="' . htmlspecialchars($etat) . '" id="etat_' . htmlspecialchars(str_replace(' ', '_', $etat)) . '" ' . ($est_coche ? 'checked' : '') . '><label class="form-check-label" for="etat_' . htmlspecialchars(str_replace(' ', '_', $etat)) . '">' . htmlspecialchars($etat) . '</label></div>';
                                        }
                                    } ?>
                                </div>
                                <?php
                                break;
                            case 'bougies':
                            case 'coffrets':
                                ?>
                                <div class="mb-4">
                                    <h5>Ambiance</h5>
                                    <?php if (isset($view_data['ambiances']) && count($view_data['ambiances']) > 0) {
                                        foreach ($view_data['ambiances'] as $ambiance) {
                                            $est_coche = in_array($ambiance['id_tag'], $filters['ambiances']);
                                            echo '<div class="form-check"><input class="form-check-input" type="checkbox" name="ambiances[]" value="' . $ambiance['id_tag'] . '" id="ambiance_' . $ambiance['id_tag'] . '" ' . ($est_coche ? 'checked' : '') . '><label class="form-check-label" for="ambiance_' . $ambiance['id_tag'] . '">' . htmlspecialchars($ambiance['nom_tag']) . '</label></div>';
                                        }
                                    } ?>
                                </div>
                                <?php
                                break;
                        }
                        ?>
                        <!-- Filtre du prix -->
                        <div class="mb-4 price-filter">
                            <h5 class="price-filter__label">Prix</h5>

                            <div id="price-slider"></div>

                            <div class="price-filter__values">
                                <span id="price-min">€ <?= $startMin ?></span>
                                <span class="mx-1">-</span>
                                <span id="price-max">€ <?= $startMax ?></span>
                            </div>

                            <input type="hidden" id="input-price-min" name="prix_min" value="<?= $startMin ?>">
                            <input type="hidden" id="input-price-max" name="prix_max" value="<?= $startMax ?>">

                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-3">Appliquer</button>
                        <a href="shop.php?a=list&view=<?= htmlspecialchars($view_type) ?>" id="reset-price"
                            class="btn w-100 mt-2"
                            style="border: 1px solid #A38968; border-radius: 15px; font-family: 'Cormorant Garamond', serif; font-weight: 600; color: black; background-color: white; padding: 10px;">
                            Réinitialiser les filtres
                        </a>
                    </div>
                </form>
            </div>
            <div class="col-lg-9">

                <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                    <span class="text-muted"><strong><?php echo $totalProduits; ?></strong> produit(s)</span>
                    <form method="GET" action="shop.php" class="d-flex align-items-center">
                        <?php build_hidden_fields_except(array('sort', 'limit')); ?>
                        <label for="limit" class="form-label me-2 mb-0 text-nowrap">Afficher:</label>
                        <select name="limit" id="limit" class="form-select form-select-sm me-3"
                            onchange="this.form.submit()">
                            <option value="12" <?php if ($filters['limit'] == 12)
                                echo 'selected'; ?>>12</option>
                            <option value="24" <?php if ($filters['limit'] == 24)
                                echo 'selected'; ?>>24</option>
                            <option value="36" <?php if ($filters['limit'] == 36)
                                echo 'selected'; ?>>36</option>
                        </select>
                        <label for="sort" class="form-label me-2 mb-0 text-nowrap">Trier par:</label>
                        <select name="sort" id="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="nouveaute" <?php if ($filters['sort'] == 'nouveaute')
                                echo 'selected'; ?>>
                                Nouveautés</option>
                            <option value="prix_asc" <?php if ($filters['sort'] == 'prix_asc')
                                echo 'selected'; ?>>Prix
                                croissant</option>
                            <option value="prix_desc" <?php if ($filters['sort'] == 'prix_desc')
                                echo 'selected'; ?>>Prix
                                décroissant</option>
                            <option value="note_desc" <?php if ($filters['sort'] == 'note_desc')
                                echo 'selected'; ?>>Mieux
                                notés</option>
                        </select>
                    </form>
                </div>

                <div class="row">
                    <?php if (count($produits) > 0) {
                        foreach ($produits as $produit) { ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 text-center">
                                    <a href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>"
                                        class="text-decoration-none text-dark d-block p-3">
                                        <?php if (isset($produit['image_url']) && $produit['image_url'] != '') { ?>
                                            <img src="<?php echo htmlspecialchars($produit['image_url']); ?>"
                                                alt="<?php echo htmlspecialchars($produit['nom_produit']); ?>" class="card-img-top"
                                                style="height: 250px; object-fit: contain;">
                                        <?php } else { ?>
                                            <img src="https://via.placeholder.com/200x280.png?text=Image+absente"
                                                alt="<?php echo htmlspecialchars($produit['nom_produit']); ?>" class="card-img-top"
                                                style="height: 250px; object-fit: contain;">
                                        <?php } ?>
                                    </a>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title" style="min-height: 3em;"><a
                                                href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>"
                                                class="text-decoration-none text-dark"><?php echo htmlspecialchars($produit['nom_produit']); ?></a>
                                        </h5>

                                        <!-- BLOC DES ÉTIQUETTES  -->
                                        <div class="product-tags-container">
                                            <?php
                                            if ($produit['type'] == 'bougie') {
                                                // GROUPE 1 : LES PARFUMS
                                                $parfums_propres = array();
                                                if (isset($produit['parfum']) && $produit['parfum'] != '') {
                                                    $parfums_array = explode(',', $produit['parfum']);
                                                    foreach ($parfums_array as $parfum) {
                                                        $parfum_nettoye = trim($parfum);
                                                        if ($parfum_nettoye != '') {
                                                            $parfums_propres[] = htmlspecialchars($parfum_nettoye);
                                                        }
                                                    }
                                                }
                                                if (count($parfums_propres) > 0) {
                                                    echo '<div class="product-tag-line">' . implode(' / ', $parfums_propres) . '</div>';
                                                }

                                                // GROUPE 2 : LES AMBIANCES
                                                $ambiances_propres = array();
                                                if (isset($produit['ambiances_tags']) && $produit['ambiances_tags'] != '') {
                                                    $ambiances_array = explode(',', $produit['ambiances_tags']);
                                                    foreach ($ambiances_array as $ambiance) {
                                                        $ambiance_nettoyee = trim($ambiance);
                                                        if ($ambiance_nettoyee != '') {
                                                            $ambiances_propres[] = htmlspecialchars($ambiance_nettoyee);
                                                        }
                                                    }
                                                }
                                                if (count($ambiances_propres) > 0) {
                                                    echo '<div class="product-tag-line">' . implode(' / ', $ambiances_propres) . '</div>';
                                                }
                                            }
                                            ?>
                                        </div>

                                        <!-- BLOC DES AVIS -->
                                        <div class="my-2">
                                            <?php if (isset($produit['nombre_votes']) && $produit['nombre_votes'] > 0) { ?>
                                                <div class="rating-stars"
                                                    title="Note : <?php echo number_format($produit['note_moyenne'], 2); ?>/5">
                                                    <div class="stars-foreground"
                                                        style="width: <?php echo ($produit['note_moyenne'] / 5) * 100; ?>%;">★★★★★</div>
                                                    <div class="stars-background">★★★★★</div>
                                                </div>
                                                <small class="text-muted ms-1">(<?php echo $produit['nombre_votes']; ?>)</small>
                                            <?php } else { ?>
                                                <small class="text-muted" style="display:inline-block; height: 1.2rem;">Aucun
                                                    avis</small>
                                            <?php } ?>
                                        </div>

                                        <!-- BLOC PRIX ET BOUTON  -->
                                        <div class="mt-auto">
                                            <p class="card-text h5"><?php echo number_format($produit['prix_ttc'], 2, ',', ' '); ?>
                                                €</p>
                                            <form action="panier.php" method="POST" class="mt-2">
                                                <input type="hidden" name="add_to_cart_token"
                                                    value="<?php echo $_SESSION['add_to_cart_token']; ?>">
                                                <input type="hidden" name="action" value="add">
                                                <input type="hidden" name="product_id"
                                                    value="<?php echo $produit['id_produit']; ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-sm btn-primary">Ajouter au panier</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else {
                        echo '<div class="col-12"><div class="alert alert-info">Aucun produit ne correspond à votre sélection.</div></div>';
                    } ?>
                </div>

                <?php if ($totalPages > 1) { ?>
                    <nav class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                <li class="page-item <?php if ($i == $filters['page']) {
                                    echo 'active';
                                } ?>">
                                    <a class="page-link" href="?<?php $params = $_GET;
                                    $params['page'] = $i;
                                    echo http_build_query($params); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </nav>
                <?php } ?>

            </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /* --- Bornes & valeurs de départ (injectées par PHP) --- */
            const bounds = { min: <?= $priceBounds['min'] ?>, max: <?= $priceBounds['max'] ?> };
            const start = [<?= $startMin ?>, <?= $startMax ?>];

            /* --- Instances DOM --- */
            const slider = document.getElementById('price-slider');
            const spanMin = document.getElementById('price-min');
            const spanMax = document.getElementById('price-max');
            const inputMin = document.getElementById('input-price-min');
            const inputMax = document.getElementById('input-price-max');

            /* --- Creation du slider --- */
            noUiSlider.create(slider, {
                start: start,
                connect: true,
                step: 1,
                range: bounds,
                tooltips: false
            });

            /* --- Sync slider → affichage + champs cachés --- */
            slider.noUiSlider.on('update', values => {
                const vMin = Math.round(values[0]);
                const vMax = Math.round(values[1]);
                spanMin.textContent = '€ ' + vMin;
                spanMax.textContent = '€ ' + vMax;
                inputMin.value = vMin;
                inputMax.value = vMax;
            });

            /* --- Bouton « Réinitialiser » --- */
            document.getElementById('reset-price').addEventListener('click', () => {
                slider.noUiSlider.set([bounds.min, bounds.max]);
            });

        });
    </script>
    <?php
} else {
    // Vue de la Vitrine
    ?>
    <main class="container-fluid p-0">
        <div class="shop-header">
            <h1 class="shop-header__title">Tous Nos Produits</h1>
            <p class="shop-header__subtitle">Explorez notre collection de livres, bougies et coffrets pour des moments de
                lecture uniques et apaisants.</p>
        </div>
        <br/>

        <!-- Section Livres -->
        <section class="shop-section container">
            <h2 class="shop-section__title">Nos Nouveaux Livres</h2>
            <div class="product-grid-vitrine">
                <?php if (count($livres_showcase) > 0) {
                    foreach ($livres_showcase as $produit) { ?>
                        <div class="product-card">
                            <a href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>" class="product-card__image-link">
                                <img class="product-card__image"
                                    src="<?php echo htmlspecialchars(isset($produit['image_url']) && $produit['image_url'] != '' ? $produit['image_url'] : 'ressources/images/placeholder.jpg'); ?>"
                                    alt="<?php echo htmlspecialchars($produit['nom_produit']); ?>">
                            </a>
                            <div class="product-card__content">
                                <h3 class="product-card__title">
                                    <a
                                        href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>"><?php echo htmlspecialchars($produit['nom_produit']); ?></a>
                                </h3>
                                <p class="product-card__price"><?php echo number_format($produit['prix_ttc'], 2, ',', ' '); ?> €</p>
                                <div class="product-card__actions">
                                    <a href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>"
                                        class="btn btn--secondary">Voir le détail</a>
                                </div>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
            <br />
            <div class="shop-section__cta">
                <a href="shop.php?a=list&view=livres" class="btn btn--primary cta-large"> Voir tous nos livres</a>
            </div>
        </section>
        <br/>

        <!-- Section Bougies -->
        <section class="shop-section container">
            <h2 class="shop-section__title">Nos Bougies Artisanales</h2>
            <div class="product-grid-vitrine">
                <?php if (count($bougies_showcase) > 0) {
                    foreach ($bougies_showcase as $produit) { ?>
                        <div class="product-card">
                            <a href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>" class="product-card__image-link">
                                <img class="product-card__image"
                                    src="<?php echo htmlspecialchars(isset($produit['image_url']) && $produit['image_url'] != '' ? $produit['image_url'] : 'ressources/images/placeholder.jpg'); ?>"
                                    alt="<?php echo htmlspecialchars($produit['nom_produit']); ?>">
                            </a>
                            <div class="product-card__content">
                                <h3 class="product-card__title">
                                    <a
                                        href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>"><?php echo htmlspecialchars($produit['nom_produit']); ?></a>
                                </h3>
                                <p class="product-card__price"><?php echo number_format($produit['prix_ttc'], 2, ',', ' '); ?> €</p>
                                <div class="product-card__actions">
                                    <a href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>"
                                        class="btn btn--secondary">Voir le détail</a>
                                </div>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
            <br />
            <div class="shop-section__cta">
                <a href="shop.php?a=list&view=bougies" class="btn btn--primary cta-large"> Voir toutes nos bougies</a>
            </div>
        </section>
        <br/>

        <!-- Section Coffrets -->
        <section class="shop-section container">
            <h2 class="shop-section__title">Nos Coffrets Uniques</h2>
            <div class="product-grid-vitrine">
                <?php if (count($coffrets_showcase) > 0) {
                    foreach ($coffrets_showcase as $produit) { ?>
                        <div class="product-card">
                            <a href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>" class="product-card__image-link">
                                <img class="product-card__image"
                                    src="<?php echo htmlspecialchars(isset($produit['image_url']) && $produit['image_url'] != '' ? $produit['image_url'] : 'ressources/images/placeholder.jpg'); ?>"
                                    alt="<?php echo htmlspecialchars($produit['nom_produit']); ?>">
                            </a>
                            <div class="product-card__content">
                                <h3 class="product-card__title">
                                    <a
                                        href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>"><?php echo htmlspecialchars($produit['nom_produit']); ?></a>
                                </h3>
                                <p class="product-card__price"><?php echo number_format($produit['prix_ttc'], 2, ',', ' '); ?> €</p>
                                <div class="product-card__actions">
                                    <a href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>"
                                        class="btn btn--secondary">Voir le détail</a>
                                </div>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
            <br />
            <div class="shop-section__cta">
                <a href="shop.php?a=list&view=coffrets" class="btn btn--primary cta-large">Voir tous nos coffrets</a>
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

        <?php
}
require('partials/footer.php');
?>