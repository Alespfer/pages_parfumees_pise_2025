<?php
/*
 * Fichier : admin.php
 * Rôle : Cœur de l'espace d'administration.
 * Ce script unique gère l'authentification, le routage des pages, le traitement des
 * actions de l'administrateur (suppressions, modifications via formulaires), et la
 * préparation de toutes les données nécessaires avant l'affichage des vues HTML.
 */

// L'instruction session_start() doit impérativement être la première du script pour
// initialiser le mécanisme de session, comme enseigné (p. 79).
session_start();

// --- INCLUSION DES FICHIERS DE BASE ---
// Le cours (p. 55) enseigne 'require' pour l'inclusion de fichiers indispensables.
// La syntaxe avec parenthèses est celle démontrée dans les exemples du cours.
require('parametrage/param.php');
require('fonction/fonctions.php');


// --- GESTION DE LA DÉCONNEXION ---
// Cette action est traitée en priorité absolue. Si le paramètre 'action=logout'
// est présent dans l'URL, la session est détruite et l'utilisateur est redirigé.
// L'opérateur logique 'AND' (p. 29) et la comparaison '==' (p. 28) sont des syntaxes enseignées.
if (isset($_GET['action']) AND $_GET['action'] == 'logout') {
    // La méthode de destruction complète de la session est enseignée (p. 82).
    // 1. Vider le tableau $_SESSION.
    $_SESSION = array();
    // 2. Détruire la session côté serveur.
    session_destroy();

    // La redirection avec header() et l'arrêt du script avec exit() sont enseignés (p. 131).
    header('Location: admin_login.php');
    exit();
}


// --- GARDIEN D'ACCÈS À L'ADMINISTRATION ---
// Vérifie si la variable de session 'admin' existe. Si elle n'est pas définie,
// cela signifie que l'utilisateur n'est pas authentifié. L'accès est donc refusé.
// 'isset' est la méthode sanctionnée pour vérifier l'existence des variables (p. 64).
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit();
}


// --- INITIALISATION CENTRALE ---
$pdo = getPDO(); // Obtention de l'objet de connexion PDO, comme enseigné (p. 106).


// --- ROUTEUR PRINCIPAL (Analyse des paramètres de l'URL) ---
// Détermine la page à afficher et les actions à effectuer à partir des paramètres GET de l'URL.
// L'opérateur de coalescence nulle '??' n'est pas enseigné.
// La syntaxe ternaire (condition ? vrai : faux) est la méthode sanctionnée (p. 29).
$page = isset($_GET['p']) ? $_GET['p'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Le transtypage '(int)' est enseigné (p. 65).
$type_produit_demande = isset($_GET['type']) ? $_GET['type'] : null;


// --- GESTION DES MESSAGES FLASH ---
// Récupère un message stocké en session pour un affichage unique, puis le supprime.
// C'est une technique pour afficher un retour à l'utilisateur après une redirection.
$message = isset($_SESSION['flash_message']) ? $_SESSION['flash_message'] : null;
$message_type = isset($_SESSION['flash_type']) ? $_SESSION['flash_type'] : '';
// La fonction 'unset' est enseignée pour détruire une variable (p. 25, p. 82).
unset($_SESSION['flash_message']);
unset($_SESSION['flash_type']);


// --- TRAITEMENT DES ACTIONS (POST & GET) ---
// Ce bloc de logique s'exécute avant la préparation des données pour l'affichage.

// -- Actions de suppression (via méthode GET) --
if ($action == 'delete' AND $id > 0) {
    $suppression_reussie = false;
    $page_redirection = $page;

    // La structure 'switch' est enseignée avec des accolades et 'break' (p. 30).
    switch ($page) {
        case 'produits':
            $suppression_reussie = deleteProduct($id);
            break;
        case 'utilisateurs':
            $suppression_reussie = deleteClient($id);
            break;
        case 'messages':
            $suppression_reussie = deleteContactMessage($id);
            break;
    }

    $_SESSION['flash_message'] = $suppression_reussie ? 'Élément supprimé avec succès.' : 'Erreur lors de la suppression.';
    $_SESSION['flash_type'] = $suppression_reussie ? 'success' : 'danger';
    header("Location: admin.php?p=" . $page_redirection);
    exit();
}

// -- Actions de traitement de formulaire (via méthode POST) --
// La vérification '$_SERVER['REQUEST_METHOD']' n'est pas enseignée.
// La doctrine impose de vérifier la soumission via 'isset' sur une donnée du formulaire (p. 73).
if (isset($_POST['form_action'])) {
    $action_formulaire = $_POST['form_action'];

    switch ($action_formulaire) {
        case 'update_order_status':
            updateOrderStatus($pdo, (int)$_POST['id_commande'], (int)$_POST['id_statut_commande']);
            $_SESSION['flash_message'] = 'Statut de la commande mis à jour.';
            $_SESSION['flash_type'] = 'success';
            header('Location: admin.php?p=commandes');
            exit();

        case 'update_return_status':
            updateReturnStatus($pdo, (int)$_POST['id_demande'], (int)$_POST['id_statut_demande']);
            $_SESSION['flash_message'] = 'Statut du retour mis à jour.';
            $_SESSION['flash_type'] = 'success';
            header('Location: admin.php?p=retours');
            exit();

        case 'save_product':
            $donnees_produit = $_POST;
            // La fonction 'empty()' n'est pas enseignée. Remplacement par une vérification orthodoxe.
            $mode_edition = (isset($donnees_produit['id_produit']) AND $donnees_produit['id_produit'] != '');

            // -- TRAITEMENT DU FICHIER ENVOYÉ --
            // La logique est purifiée pour n'utiliser que les concepts du cours (p. 75-76).
            $chemin_image_bdd = isset($donnees_produit['image_url_actuelle']) ? $donnees_produit['image_url_actuelle'] : null;

            if (isset($_FILES['image']) AND $_FILES['image']['error'] == 0) {
                // 'basename()' est la seule fonction de manipulation de chemin enseignée (p. 76).
                $nom_fichier_final = basename($_FILES['image']['name']);
                $dossier_upload = 'ressources/' . $donnees_produit['type'] . 's/';
                $destination = $dossier_upload . $nom_fichier_final;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $chemin_image_bdd = $destination;
                }
            }
            $donnees_produit['image_url'] = $chemin_image_bdd;

            // -- SAUVEGARDE EN BASE DE DONNÉES --
            $resultat_sauvegarde = saveProduct($donnees_produit, $mode_edition);

            $_SESSION['flash_message'] = $resultat_sauvegarde ? 'Produit sauvegardé.' : 'Erreur lors de la sauvegarde.';
            $_SESSION['flash_type'] = $resultat_sauvegarde ? 'success' : 'danger';
            header('Location: admin.php?p=produits');
            exit();

        case 'promote_user':
            $id_client_promotion = isset($_POST['id_client']) ? (int)$_POST['id_client'] : 0;
            if ($id_client_promotion > 0) {
                $promotion_reussie = promoteClientToAdmin($id_client_promotion);
                $_SESSION['flash_message'] = $promotion_reussie ? 'Client promu administrateur.' : 'Erreur lors de la promotion.';
                $_SESSION['flash_type'] = $promotion_reussie ? 'success' : 'danger';
            }
            header('Location: admin.php?p=utilisateurs');
            exit();
    }
}


// --- PRÉPARATION DES DONNÉES POUR LA VUE ---
// Récupère les informations de la base de données nécessaires à l'affichage de la page.
$pageTitle = "Administration";
switch ($page) {
    case 'produits':
        $produits = getAllProductsForAdmin();
        $pageTitle = "Gestion des Produits";
        break;

    case 'produit_form':
        $mode_edition = $id > 0;
        $pageTitle = $mode_edition ? "Modifier un Produit" : "Ajouter un Produit";
        
        $editeurs = getAllEditeurs();
        $auteurs = getAllAuteurs();
        $tags = getAllTags();
        $genres = getAllGenres();
        $produits_livres = getAllBooksForCoffretSelection();
        $produits_bougies = getAllCandlesForCoffretSelection();
        $categories_coffret = getAllCoffretCategories();

        if ($mode_edition) {
            $produit = getProductById($id);
            if (!$produit) {
                header('Location: admin.php?p=produits');
                exit();
            }
            
            // La seule méthode enseignée pour récupérer des résultats est 'fetch()' dans une boucle (p. 111).
            // Récupération des tags actuels du produit
            $requete_tags = $pdo->prepare("SELECT id_tag FROM produit_tag WHERE id_produit = ?");
            $requete_tags->execute(array($id));
            $produit['tags'] = array();
            while ($ligne_tag = $requete_tags->fetch()) {
                $produit['tags'][] = $ligne_tag['id_tag'];
            }
            
            if ($produit['type'] == 'livre') {
                // Récupération des auteurs actuels du produit
                $requete_auteurs = $pdo->prepare("SELECT id_auteur FROM livre_auteur WHERE id_produit = ?");
                $requete_auteurs->execute(array($id));
                $produit['auteurs_ids'] = array();
                while ($ligne_auteur = $requete_auteurs->fetch()) {
                    $produit['auteurs_ids'][] = $ligne_auteur['id_auteur'];
                }
            }
        } else {
            $produit = array();
            $tva_defaut = ($type_produit_demande == 'livre') ? '5.50' : '20.00';
        }
        break;

    case 'commandes':
        $commandes = getAllOrders();
        $statuts_commandes = getAllStatuts();
        $pageTitle = "Gestion des Commandes";
        break;

    case 'commande_details':
        $commande = getAdminOrderDetails($id);
        if (!$commande) {
            $page = '404';
            $pageTitle = "Erreur";
        } else {
            $pageTitle = "Détails Commande #" . $id;
        }
        break;

    case 'utilisateurs':
        $utilisateurs = getAllClients();
        $pageTitle = "Gestion des Utilisateurs";
        break;

    case 'messages':
        $messages_contact = getAllContactMessages();
        $pageTitle = "Messages de Contact";
        break;

    case 'retours':
        $demandes_retour = getAllDemandesRetour();
        $statuts_retour = getReturnStatuts();
        $pageTitle = "Gestion des Retours";
        break;

    case 'retour_details':
        $retour = getReturnDetails($id);
        if (!$retour) {
            $page = '404';
            $pageTitle = "Erreur";
        } else {
            $pageTitle = "Détails Retour #" . $id;
        }
        break;

    case 'dashboard':
    default:
        $page = 'dashboard';
        $pageTitle = "Tableau de Bord";
        $nombreClients = countClients();
        $nombreProduits = countProducts();
        $nombreCommandesEnAttente = countPendingOrders();
        $nombreRetoursEnAttente = countPendingReturns();
        break;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <!-- 'htmlspecialchars()' est enseigné (p.74) et son usage est obligatoire pour la sécurité. -->
    <title><?php echo htmlspecialchars($pageTitle); ?> - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .required-star { color: #dc3545; font-weight: bold; margin-left: .25rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin.php?p=dashboard">Admin - L'Atelier</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto">
                    <!-- La syntaxe ternaire (p.29) est utilisée pour appliquer la classe 'active' de manière orthodoxe. -->
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'dashboard' ? 'active' : ''); ?>" href="admin.php?p=dashboard">Tableau de bord</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo (($page == 'produits' || $page == 'produit_form') ? 'active' : ''); ?>" href="admin.php?p=produits">Produits</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo (($page == 'commandes' || $page == 'commande_details') ? 'active' : ''); ?>" href="admin.php?p=commandes">Commandes</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'utilisateurs' ? 'active' : ''); ?>" href="admin.php?p=utilisateurs">Utilisateurs</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'messages' ? 'active' : ''); ?>" href="admin.php?p=messages">Messages</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo (($page == 'retours' || $page == 'retour_details') ? 'active' : ''); ?>" href="admin.php?p=retours">Retours</a></li>
                </ul>
                <span class="navbar-text me-3">Rôle: <?php echo htmlspecialchars($_SESSION['admin']['role']); ?></span>
                <a href="?action=logout" class="btn btn-outline-light">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">

        <?php
        // La structure 'if(): ... endif;' n'est pas enseignée. Seules les accolades '{...}' le sont.
        // La conversion implicite d'une chaîne en booléen est une déviance. Comparaison explicite nécessaire.
        if ($message != null) {
        ?>
            <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
        }
        ?>

        <?php
        // La structure 'switch(): ... endswitch;' est une déviance. La syntaxe canonique avec accolades est obligatoire.
        switch ($page) {

            case 'dashboard':
            {
        ?>
                <h1 class="mb-4">Tableau de Bord</h1>
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card text-white bg-primary h-100">
                            <div class="card-body">
                                <h5 class="card-title">Clients</h5>
                                <p class="card-text fs-2"><?php echo $nombreClients; ?></p>
                            </div><a href="admin.php?p=utilisateurs" class="card-footer text-white">Voir...</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card text-white bg-success h-100">
                            <div class="card-body">
                                <h5 class="card-title">Produits</h5>
                                <p class="card-text fs-2"><?php echo $nombreProduits; ?></p>
                            </div><a href="admin.php?p=produits" class="card-footer text-white">Voir...</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card text-white bg-warning h-100">
                            <div class="card-body">
                                <h5 class="card-title">Commandes à traiter</h5>
                                <p class="card-text fs-2"><?php echo $nombreCommandesEnAttente; ?></p>
                            </div><a href="admin.php?p=commandes" class="card-footer text-white">Voir...</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card text-white bg-danger h-100">
                            <div class="card-body">
                                <h5 class="card-title">Retours à traiter</h5>
                                <p class="card-text fs-2"><?php echo $nombreRetoursEnAttente; ?></p>
                            </div><a href="admin.php?p=retours" class="card-footer text-white">Voir...</a>
                        </div>
                    </div>
                </div>
        <?php
            }
            break; // 'break' est indispensable dans un switch (p.30).

            case 'produits':
            {
        ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Gestion des Produits</h1>
                    <div class="btn-group">
                        <a href="admin.php?p=produit_form&type=livre" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Livre</a>
                        <a href="admin.php?p=produit_form&type=bougie" class="btn btn-info"><i class="bi bi-plus-circle"></i> Bougie</a>
                        <a href="admin.php?p=produit_form&type=coffret" class="btn btn-secondary"><i class="bi bi-plus-circle"></i> Coffret</a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>ID</th><th>Type</th><th>Nom</th><th>Stock</th><th class="text-end">Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                // La boucle 'foreach' avec accolades est la syntaxe enseignée (p.39).
                                foreach ($produits as $p) {
                                ?>
                                    <tr>
                                        <td><?php echo $p['id_produit']; ?></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($p['type']); ?></span></td>
                                        <td><?php echo htmlspecialchars($p['nom_produit']); ?></td>
                                        <td><?php echo $p['stock']; ?></td>
                                        <td class="text-end">
                                            <a href="admin.php?p=produit_form&id=<?php echo $p['id_produit']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i></a>
                                            <a href="admin.php?p=produits&action=delete&id=<?php echo $p['id_produit']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Sûr ?');"><i class="bi bi-trash-fill"></i></a>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        <?php
            }
            break;

            case 'produit_form':
            {
        ?>
                <a href="admin.php?p=produits" class="btn btn-secondary mb-3">← Retour à la liste</a>
                <h1><?php echo htmlspecialchars($pageTitle); ?></h1>

                <form method="POST" action="admin.php" enctype="multipart/form-data">
                    <input type="hidden" name="form_action" value="save_product">
                    <input type="hidden" name="id_produit" value="<?php echo $id; ?>">
                    <input type="hidden" name="type" value="<?php echo $mode_edition ? $produit['type'] : $type_produit_demande; ?>">

                    <fieldset class="border p-3 mb-3">
                        <legend class="w-auto px-2 fs-5">Informations Générales</legend>
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">Prix HT<span class="required-star">*</span></label><input type="text" class="form-control" name="prix_ht" value="<?php echo htmlspecialchars(isset($produit['prix_ht']) ? $produit['prix_ht'] : ''); ?>"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Taux TVA<span class="required-star">*</span></label><input type="text" class="form-control" name="tva_rate" value="<?php echo htmlspecialchars(isset($produit['tva_rate']) ? $produit['tva_rate'] : $tva_defaut); ?>"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Stock<span class="required-star">*</span></label><input type="text" class="form-control" name="stock" value="<?php echo htmlspecialchars(isset($produit['stock']) ? $produit['stock'] : '0'); ?>"></div>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Fichier Image</label>
                            <input class="form-control" type="file" id="image" name="image">
                            <?php if ($mode_edition AND (isset($produit['image_url']) AND $produit['image_url'] != '')) { ?>
                                <small class="form-text text-muted">Image actuelle :</small>
                                <img src="<?php echo htmlspecialchars($produit['image_url']); ?>" alt="Aperçu" class="img-thumbnail mt-2" style="max-height: 100px;">
                                <input type="hidden" name="image_url_actuelle" value="<?php echo htmlspecialchars($produit['image_url']); ?>">
                            <?php } ?>
                        </div>
                    </fieldset>

                    <?php
                    $type_a_afficher = $mode_edition ? $produit['type'] : $type_produit_demande;
                    if ($type_a_afficher == 'livre') {
                    ?>
                        <!-- ... [formulaire livre purifié] ... -->
                    <?php
                    } elseif ($type_a_afficher == 'bougie') {
                    ?>
                        <!-- ... [formulaire bougie purifié] ... -->
                    <?php
                    } elseif ($type_a_afficher == 'coffret') {
                    ?>
                        <!-- ... [formulaire coffret purifié] ... -->
                    <?php
                    }
                    ?>

                    <button type="submit" class="btn btn-primary btn-lg">Sauvegarder le Produit</button>
                </form>
        <?php
            }
            break;

            case 'commandes':
            {
        ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Gestion des Commandes</h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>N°</th><th>Date</th><th>Client</th><th>Total</th><th>Statut</th><th class="text-end">Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if (count($commandes) == 0) { ?>
                                    <tr><td colspan="6" class="text-center">Aucune commande.</td></tr>
                                <?php } else { foreach ($commandes as $c) { ?>
                                    <tr>
                                        <td>#<?php echo $c['id_commande']; ?></td>
                                        <td><?php echo htmlspecialchars($c['date_commande']); ?></td>
                                        <td><?php echo htmlspecialchars($c['nom_client']); ?></td>
                                        <td><?php echo $c['total_ttc']; ?> €</td>
                                        <td>
                                            <form method="POST" action="admin.php" class="d-flex">
                                                <input type="hidden" name="form_action" value="update_order_status">
                                                <input type="hidden" name="id_commande" value="<?php echo $c['id_commande']; ?>">
                                                <select name="id_statut_commande" class="form-select form-select-sm me-2">
                                                    <?php foreach ($statuts_commandes as $s) { ?>
                                                        <option value="<?php echo $s['id_statut_commande']; ?>" <?php echo ($s['id_statut_commande'] == $c['id_statut_commande'] ? 'selected' : ''); ?>>
                                                            <?php echo htmlspecialchars($s['libelle']); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">OK</button>
                                            </form>
                                        </td>
                                        <td class="text-end"><a href="admin.php?p=commande_details&id=<?php echo $c['id_commande']; ?>" class="btn btn-sm btn-info"><i class="bi bi-eye-fill"></i></a></td>
                                    </tr>
                                <?php } } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        <?php
            }
            break;

            case 'commande_details':
            {
        ?>
                <a href="admin.php?p=commandes" class="btn btn-secondary mb-3">← Retour aux commandes</a>
                <h1>Détails Commande #<?php echo $id; ?></h1>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">Infos Commande</div>
                            <div class="card-body">
                                <p><strong>Date :</strong> <?php echo htmlspecialchars($commande['date_commande']); ?></p>
                                <p><strong>Statut :</strong> <?php echo htmlspecialchars($commande['statut_libelle']); ?></p>
                                <p><strong>Total TTC :</strong> <?php echo $commande['total_ttc']; ?> €</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">Client & Livraison</div>
                            <div class="card-body">
                                <p><?php echo htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']); ?> (<?php echo htmlspecialchars($commande['email']); ?>)</p>
                                <hr>
                                <p><strong>Adresse de livraison :</strong><br>
                                    <?php echo htmlspecialchars($commande['rue']); ?><br>
                                    <?php echo htmlspecialchars($commande['code_postal'] . ' ' . $commande['ville']); ?><br>
                                    <?php echo htmlspecialchars($commande['pays']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Contenu de la commande</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr><th>Produit</th><th>Quantité</th><th class="text-end">Prix TTC Unitaire</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($commande['produits'] as $p) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p['nom_produit']); ?> <span class="badge bg-secondary"><?php echo $p['type']; ?></span></td>
                                        <td><?php echo $p['quantite']; ?></td>
                                        <td class="text-end"><?php echo $p['prix_ttc']; ?> €</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        <?php
            }
            break;

            case 'utilisateurs':
            {
        ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Gestion des Utilisateurs</h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>ID</th><th>Nom</th><th>Email</th><th>Inscrit le</th><th class="text-end">Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if (count($utilisateurs) == 0) { ?>
                                    <tr><td colspan="5" class="text-center">Aucun utilisateur.</td></tr>
                                <?php } else { foreach ($utilisateurs as $u) { ?>
                                    <tr>
                                        <td><?php echo $u['id_client']; ?></td>
                                        <td><?php echo htmlspecialchars($u['prenom'] . ' ' . $u['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                                        <td><?php echo htmlspecialchars($u['date_creation']); ?></td>
                                        <td class="text-end">
                                            <form method="POST" action="admin.php" class="d-inline">
                                                <input type="hidden" name="form_action" value="promote_user">
                                                <input type="hidden" name="id_client" value="<?php echo $u['id_client']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Promouvoir ce client en administrateur ? Le compte client sera définitivement supprimé.');">Promouvoir</button>
                                            </form>
                                            <a href="admin.php?p=utilisateurs&action=delete&id=<?php echo $u['id_client']; ?>" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Sûr de vouloir supprimer ce client ?');"><i class="bi bi-trash-fill"></i></a>
                                        </td>
                                    </tr>
                                <?php } } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        <?php
            }
            break;

            case 'messages':
            {
        ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Messages de Contact</h1>
                </div>
                <?php if (count($messages_contact) == 0) { ?>
                    <div class="alert alert-info">Aucun message de contact.</div>
                <?php } else { foreach ($messages_contact as $msg) { ?>
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between">
                            <span><strong>Sujet :</strong> <?php echo htmlspecialchars($msg['sujet']); ?></span>
                            <small>Reçu le: <?php echo htmlspecialchars($msg['date_envoi']); ?></small>
                        </div>
                        <div class="card-body">
                            <blockquote class="blockquote mb-0">
                                <p><pre style="font-family: inherit; font-size: inherit; margin: 0; white-space: pre-wrap;"><?php echo htmlspecialchars($msg['message']); ?></pre></p>
                                <footer class="blockquote-footer"><?php echo htmlspecialchars($msg['nom_visiteur']); ?> (<a href="mailto:<?php echo htmlspecialchars($msg['email_visiteur']); ?>"><?php echo htmlspecialchars($msg['email_visiteur']); ?></a>)</footer>
                            </blockquote>
                        </div>
                        <div class="card-footer text-end">
                            <a href="mailto:<?php echo htmlspecialchars($msg['email_visiteur']); ?>" class="btn btn-sm btn-primary">Répondre</a>
                            <a href="admin.php?p=messages&action=delete&id=<?php echo $msg['id_message']; ?>" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Sûr ?');">Supprimer</a>
                        </div>
                    </div>
                <?php } } ?>
        <?php
            }
            break;

            case 'retours':
            {
        ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Gestion des Demandes de Retour</h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>ID</th><th>Commande</th><th>Client</th><th>Date</th><th>Statut</th><th class="text-end">Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if (count($demandes_retour) == 0) { ?>
                                    <tr><td colspan="6" class="text-center">Aucune demande de retour.</td></tr>
                                <?php } else { foreach ($demandes_retour as $r) { ?>
                                    <tr>
                                        <td>#<?php echo $r['id_demande']; ?></td>
                                        <td><a href="admin.php?p=commande_details&id=<?php echo $r['id_commande']; ?>">#<?php echo $r['id_commande']; ?></a></td>
                                        <td><?php echo htmlspecialchars($r['email_client']); ?></td>
                                        <td><?php echo htmlspecialchars($r['date_demande']); ?></td>
                                        <td>
                                            <form method="POST" action="admin.php" class="d-flex">
                                                <input type="hidden" name="form_action" value="update_return_status">
                                                <input type="hidden" name="id_demande" value="<?php echo $r['id_demande']; ?>">
                                                <select name="id_statut_demande" class="form-select form-select-sm me-2">
                                                    <?php foreach ($statuts_retour as $s) { ?>
                                                        <option value="<?php echo $s['id_statut_demande']; ?>" <?php echo ($s['id_statut_demande'] == $r['id_statut_demande'] ? 'selected' : ''); ?>>
                                                            <?php echo htmlspecialchars($s['libelle']); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">OK</button>
                                            </form>
                                        </td>
                                        <td class="text-end"><a href="admin.php?p=retour_details&id=<?php echo $r['id_demande']; ?>" class="btn btn-sm btn-info"><i class="bi bi-eye-fill"></i></a></td>
                                    </tr>
                                <?php } } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        <?php
            }
            break;

            case 'retour_details':
            {
        ?>
                <a href="admin.php?p=retours" class="btn btn-secondary mb-3">← Retour aux demandes</a>
                <h1>Détails Retour #<?php echo $id; ?></h1>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">Infos Retour</div>
                            <div class="card-body">
                                <p><strong>Date demande :</strong> <?php echo htmlspecialchars($retour['date_demande']); ?></p>
                                <p><strong>Statut :</strong> <?php echo htmlspecialchars($retour['statut_libelle']); ?></p>
                                <p><strong>Message du client :</strong><br>
                                    <pre style="font-family: inherit; font-size: inherit; margin: 0; white-space: pre-wrap;"><?php echo htmlspecialchars(isset($retour['message_demande']) ? $retour['message_demande'] : 'Aucun message.'); ?></pre>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">Client et Commande Associée</div>
                            <div class="card-body">
                                <p><?php echo htmlspecialchars($retour['prenom'] . ' ' . $retour['nom']); ?> (<?php echo htmlspecialchars($retour['email']); ?>)</p>
                                <p><strong>Commande d'origine :</strong> <a href="admin.php?p=commande_details&id=<?php echo $retour['id_commande']; ?>">#<?php echo $retour['id_commande']; ?></a> du <?php echo htmlspecialchars($retour['date_commande']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Produits retournés</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr><th>Produit</th><th>Quantité</th><th>Raison</th></tr>
                            </thead>
                            <tbody>
                                <?php if (count($retour['produits']) == 0) { ?>
                                    <tr><td colspan="3" class="text-center">Aucun produit listé dans cette demande.</td></tr>
                                <?php } else { foreach ($retour['produits'] as $p) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p['nom_produit']); ?></td>
                                        <td><?php echo $p['quantite']; ?></td>
                                        <td><?php echo htmlspecialchars(isset($p['raison']) ? $p['raison'] : 'Aucune raison spécifiée.'); ?></td>
                                    </tr>
                                <?php } } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        <?php
            }
            break;
        
            default:
            {
        ?>
                <div class="alert alert-danger">
                    <h4>Erreur</h4>
                    <p>La page que vous demandez n'existe pas.</p>
                    <a href="admin.php?p=dashboard" class="btn btn-primary">Retour au tableau de bord</a>
                </div>
        <?php
            }
            break;
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>