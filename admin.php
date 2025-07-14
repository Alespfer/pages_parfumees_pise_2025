<?php
/*
 * Fichier : admin.php
 * Rôle : Cœur de l'espace d'administration (Contrôleur Principal).
 * Ce script est le point d'entrée unique de l'administration. Il a pour mission de :
 *  1. Gérer la sécurité et l'authentification.
 *  2. Analyser la requête de l'utilisateur (URL) pour déterminer l'action à faire.
 *  3. Traiter les données envoyées par les formulaires (POST).
 *  4. Préparer les données nécessaires pour l'affichage.
 *  5. Inclure le fichier de vue HTML correspondant à la page demandée.
 */

session_start();


require('parametrage/param.php');
require('fonction/fonctions.php');



// --- GESTION DE LA DÉCONNEXION ---
// Si l'URL contient "?action=logout", on gère la déconnexion en priorité.

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    $_SESSION = array();
    session_destroy();

    // Redirection via header() et arrêt du script via exit()
    header('Location: admin_login.php');
    exit();
}


// --- GARDIEN D'ACCÈS À L'ADMINISTRATION ---
// Vérifie si la variable de session 'admin' existe. Sinon, l'accès est refusé.
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit();
}


// --- INITIALISATION CENTRALE ---
$pdo = getPDO();


// --- ROUTEUR PRINCIPAL (Analyse des paramètres de l'URL) ---
// On détermine quelle page afficher et quelle action effectuer en se basant sur les paramètres dans l'URL (méthode GET).

$page = isset($_GET['p']) ? $_GET['p'] : 'dashboard'; // Page par défaut: 'dashboard'
$action = isset($_GET['action']) ? $_GET['action'] : null;
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0; 
$type_produit_demande = isset($_GET['type']) ? $_GET['type'] : null;


// --- GESTION DES MESSAGES FLASH ---
// Permet d'afficher un message après une redirection (ex: "Action réussie").
$message = isset($_SESSION['flash_message']) ? $_SESSION['flash_message'] : null;
$message_type = isset($_SESSION['flash_type']) ? $_SESSION['flash_type'] : '';
unset($_SESSION['flash_message']);
unset($_SESSION['flash_type']);


// --- TRAITEMENT DES ACTIONS (POST & GET) ---

// -- Traitement des actions de suppression --
if ($action == 'delete' && $id > 0) {
    $resultat_operation = false; 
    $page_redirection = $page;

    switch ($page) {
        case 'produits':
            $resultat_operation = deleteProduct($id);
            break;
        case 'utilisateurs':
            $resultat_operation = deleteClient($id);
            break;
        case 'messages':
            $resultat_operation = deleteContactMessage($id);
            break;
        case 'auteurs':
            $resultat_operation = deleteAuteur($id);
            break;
        case 'tags':
            $resultat_operation = deleteTag($id);
            break;
        case 'editeurs':
            $resultat_operation = deleteEditeur($id);
            break;
    }

    if ($resultat_operation === true) {
        $_SESSION['flash_message'] = 'Opération effectuée avec succès.';
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = $resultat_operation;
        $_SESSION['flash_type'] = 'danger';
    }
    
    header("Location: admin.php?p=" . $page_redirection);
    exit();
}


// -- Traitement des formulaires (POST) --
if (isset($_POST['form_action'])) {
    // On traite les actions en fonction de la valeur du champ caché 'form_action'
    $action_formulaire = $_POST['form_action'];

    switch ($action_formulaire) {
        case 'update_order_status':
            updateOrderStatus($pdo, (int) $_POST['id_commande'], (int) $_POST['id_statut_commande']);
            $_SESSION['flash_message'] = 'Statut de la commande mis à jour.';
            $_SESSION['flash_type'] = 'success';
            header('Location: admin.php?p=commandes');
            exit();

        case 'update_return_status':
            updateReturnStatus($pdo, (int) $_POST['id_demande'], (int) $_POST['id_statut_demande']);
            $_SESSION['flash_message'] = 'Statut du retour mis à jour.';
            $_SESSION['flash_type'] = 'success';
            header('Location: admin.php?p=retours');
            exit();

        case 'save_product':
            $donnees_produit = $_POST;
            $is_edit_mode = (isset($donnees_produit['id_produit']) && (int) $donnees_produit['id_produit'] > 0);
            $chemin_image_bdd = isset($donnees_produit['image_url_actuelle']) ? $donnees_produit['image_url_actuelle'] : null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $nom_fichier_final = basename($_FILES['image']['name']);
                $destination_serveur = 'ressources/livres/' . $nom_fichier_final;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination_serveur)) {
                    $chemin_image_bdd = $destination_serveur;
                } else {
                    $_SESSION['flash_message'] = 'Erreur lors du transfert du fichier image.';
                    $_SESSION['flash_type'] = 'danger';
                    header('Location: admin.php?p=produits');
                    exit();
                }
            }
            $donnees_produit['image_url'] = $chemin_image_bdd;
            $resultat = $is_edit_mode ? updateProduct($donnees_produit) : createProduct($donnees_produit);
            $_SESSION['flash_message'] = $resultat ? 'Produit sauvegardé.' : 'Erreur lors de la sauvegarde du produit.';
            $_SESSION['flash_type'] = $resultat ? 'success' : 'danger';
            header('Location: admin.php?p=produits');
            exit();

       case 'promote_user':
            $id_client_promotion = isset($_POST['id_client']) ? (int) $_POST['id_client'] : 0;
            if ($id_client_promotion > 0) {
                $resultat_promotion = promoteClientToAdmin($id_client_promotion);
                
                if ($resultat_promotion === true) {
                    $_SESSION['flash_message'] = 'Client promu administrateur.';
                    $_SESSION['flash_type'] = 'success';
                } else {
                    $_SESSION['flash_message'] = $resultat_promotion;
                    $_SESSION['flash_type'] = 'danger';
                }
            }
            header('Location: admin.php?p=utilisateurs');
            exit();

        case 'save_auteur':
            $is_edit_mode = (isset($_POST['id_auteur']) && (int) $_POST['id_auteur'] > 0);
            $resultat = $is_edit_mode ? updateAuteur($_POST) : createAuteur($_POST);
            $_SESSION['flash_message'] = $resultat ? 'Auteur sauvegardé.' : 'Erreur de sauvegarde.';
            $_SESSION['flash_type'] = $resultat ? 'success' : 'danger';
            header('Location: admin.php?p=auteurs');
            exit();

        case 'save_tag':
            $is_edit_mode = (isset($_POST['id_tag']) && (int) $_POST['id_tag'] > 0);
            $resultat = $is_edit_mode ? updateTag($_POST) : createTag($_POST);
            $_SESSION['flash_message'] = $resultat ? 'Tag sauvegardé.' : 'Erreur de sauvegarde.';
            $_SESSION['flash_type'] = $resultat ? 'success' : 'danger';
            header('Location: admin.php?p=tags');
            exit();

        case 'save_editeur':
            $is_edit_mode = (isset($_POST['id_editeur']) && (int) $_POST['id_editeur'] > 0);
            $resultat = $is_edit_mode ? updateEditeur($_POST) : createEditeur($_POST);
            $_SESSION['flash_message'] = $resultat ? 'Éditeur sauvegardé.' : 'Erreur de sauvegarde.';
            $_SESSION['flash_type'] = $resultat ? 'success' : 'danger';
            header('Location: admin.php?p=editeurs');
            exit();
    }
}


// --- PRÉPARATION DES DONNÉES POUR L'AFFICHAGE ---
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
            $requete_tags = $pdo->prepare("SELECT id_tag FROM produit_tag WHERE id_produit = ?");
            $requete_tags->execute(array($id));
            $produit['tags'] = array();
            while ($ligne_tag = $requete_tags->fetch()) {
                $produit['tags'][] = $ligne_tag['id_tag'];
            }
            if ($produit['type'] == 'livre') {
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

    case 'auteurs':
        $pageTitle = "Gestion des Auteurs";
        $auteurs = getAllAuteursAdmin();
        break;

    case 'auteur_form':
        $is_edit_mode = $id > 0;
        $pageTitle = $is_edit_mode ? "Modifier un Auteur" : "Ajouter un Auteur";
        $auteur = $is_edit_mode ? getAuteurById($id) : array();
        break;

    case 'tags':
        $pageTitle = "Gestion des Tags";
        $tags = getAllTagsAdmin();
        break;

    case 'tag_form':
        $is_edit_mode = $id > 0;
        $pageTitle = $is_edit_mode ? "Modifier un Tag" : "Ajouter un Tag";
        $tag = $is_edit_mode ? getTagById($id) : array();
        break;

    case 'editeurs':
        $pageTitle = "Gestion des Éditeurs";
        $editeurs = getAllEditeursAdmin();
        break;

    case 'editeur_form':
        $is_edit_mode = $id > 0;
        $pageTitle = $is_edit_mode ? "Modifier un Éditeur" : "Ajouter un Éditeur";
        $editeur = $is_edit_mode ? getEditeurById($id) : array();
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
    <title><?php echo htmlspecialchars($pageTitle); ?> - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        .required-star {
            color: #dc3545;
            font-weight: bold;
            margin-left: .25rem;
        }
    </style>
</head>

 <!-- Barre de navigation principale -->
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin.php?p=dashboard">Admin - Les Pages Parfumées</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'dashboard' ? 'active' : ''); ?>"
                            href="admin.php?p=dashboard">Tableau de bord</a></li>
                    <li class="nav-item"><a
                            class="nav-link <?php echo (in_array($page, array('produits', 'produit_form')) ? 'active' : ''); ?>"
                            href="admin.php?p=produits">Produits</a></li>
                    <li class="nav-item"><a
                            class="nav-link <?php echo (in_array($page, array('commandes', 'commande_details')) ? 'active' : ''); ?>"
                            href="admin.php?p=commandes">Commandes</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'utilisateurs' ? 'active' : ''); ?>"
                            href="admin.php?p=utilisateurs">Utilisateurs</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'messages' ? 'active' : ''); ?>"
                            href="admin.php?p=messages">Messages</a></li>
                    <li class="nav-item"><a
                            class="nav-link <?php echo (in_array($page, array('retours', 'retour_details')) ? 'active' : ''); ?>"
                            href="admin.php?p=retours">Retours</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php
                        $data_pages = array('auteurs', 'auteur_form', 'tags', 'tag_form', 'editeurs', 'editeur_form');
                        if (in_array($page, $data_pages)) {
                            echo 'active';
                        }
                        ?>" href="#" id="dataDropdown" role="button" data-bs-toggle="dropdown">Données Annexes</a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="admin.php?p=auteurs">Auteurs</a></li>
                            <li><a class="dropdown-item" href="admin.php?p=editeurs">Éditeurs</a></li>
                            <li><a class="dropdown-item" href="admin.php?p=tags">Tags</a></li>
                        </ul>
                    </li>
                </ul>
                <span class="navbar-text me-3">Rôle: <?php echo htmlspecialchars($_SESSION['admin']['role']); ?></span>
                <a href="admin.php?action=logout" class="btn btn-outline-light">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">

        <?php
        // Affichage du message "flash", qui sert à donner un retour à l'utilisateur après une redirection.

        if ($message != null) {
            ?>
            <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show"
                role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
        }
        ?>

        <?php

         // Aiguillage principal de la vue. En fonction de la valeur de la variable $page, on affiche un bloc de code HTML différent. C'est ce qui permet d'afficher des pages
        switch ($page) {
            case 'dashboard': {
                ?>
                    <h1 class="mb-4">Tableau de Bord</h1>
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card text-white bg-primary h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Clients</h5>
                                    <p class="card-text fs-2"><?php echo $nombreClients; ?></p>
                                </div>
                                <a href="admin.php?p=utilisateurs" class="card-footer text-white">Voir...</a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card text-white bg-success h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Produits</h5>
                                    <p class="card-text fs-2"><?php echo $nombreProduits; ?></p>
                                </div>
                                <a href="admin.php?p=produits" class="card-footer text-white">Voir...</a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card text-white bg-warning h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Commandes à traiter</h5>
                                    <p class="card-text fs-2"><?php echo $nombreCommandesEnAttente; ?></p>
                                </div>
                                <a href="admin.php?p=commandes" class="card-footer text-white">Voir...</a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card text-white bg-danger h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Retours à traiter</h5>
                                    <p class="card-text fs-2"><?php echo $nombreRetoursEnAttente; ?></p>
                                </div>
                                <a href="admin.php?p=retours" class="card-footer text-white">Voir...</a>
                            </div>
                        </div>
                    </div>
                    <?php
                    break;
            }
            case 'produits': {
                ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1>Gestion des Produits</h1>
                        <div class="btn-group">
                            <a href="admin.php?p=produit_form&type=livre" class="btn btn-primary"><i class="bi bi-plus-circle"></i>
                                Ajouter un Livre</a>
                            <a href="admin.php?p=produit_form&type=bougie" class="btn btn-info"><i class="bi bi-plus-circle"></i>
                                Ajouter une Bougie</a>
                            <a href="admin.php?p=produit_form&type=coffret" class="btn btn-secondary"><i
                                    class="bi bi-plus-circle"></i> Ajouter un Coffret</a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Nom</th>
                                        <th>Stock</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($produits as $p) {
                                        ?>
                                        <tr>
                                            <td><?php echo $p['id_produit']; ?></td>
                                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($p['type']); ?></span></td>
                                            <td><?php echo htmlspecialchars($p['nom_produit']); ?></td>
                                            <td><?php echo $p['stock']; ?></td>
                                            <td class="text-end">
                                                <a href="admin.php?p=produit_form&id=<?php echo $p['id_produit']; ?>"
                                                    class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i></a>
                                                <a href="admin.php?p=produits&action=delete&id=<?php echo $p['id_produit']; ?>"
                                                    class="btn btn-sm btn-danger" onclick="return confirm('Sûr ?');"><i
                                                        class="bi bi-trash-fill"></i></a>
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
                    break;
            }
            case 'produit_form': {
                ?>
                    <a href="admin.php?p=produits" class="btn btn-secondary mb-3">← Retour à la liste</a>
                    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
                    <form method="POST" action="admin.php" enctype="multipart/form-data">
                        <input type="hidden" name="form_action" value="save_product">
                        <input type="hidden" name="id_produit" value="<?php echo $id; ?>">
                        <input type="hidden" name="type"
                            value="<?php echo $mode_edition ? $produit['type'] : $type_produit_demande; ?>">
                        <fieldset class="border p-3 mb-3">
                            <legend class="w-auto px-2 fs-5">Informations Générales</legend>
                            <div class="row">
                                <div class="col-md-4 mb-3"><label class="form-label">Prix HT<span
                                            class="required-star">*</span></label><input type="text" class="form-control"
                                        name="prix_ht"
                                        value="<?php echo htmlspecialchars(isset($produit['prix_ht']) ? $produit['prix_ht'] : ''); ?>"
                                        required></div>
                                <div class="col-md-4 mb-3"><label class="form-label">Taux TVA<span
                                            class="required-star">*</span></label><input type="text" class="form-control"
                                        name="tva_rate"
                                        value="<?php echo htmlspecialchars(isset($produit['tva_rate']) ? $produit['tva_rate'] : $tva_defaut); ?>"
                                        required></div>
                                <div class="col-md-4 mb-3"><label class="form-label">Stock<span
                                            class="required-star" required>*</span></label><input type="text" class="form-control"
                                        name="stock"
                                        value="<?php echo htmlspecialchars(isset($produit['stock']) ? $produit['stock'] : '0'); ?>"
                                        required></div>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Fichier Image</label>
                                <input class="form-control" type="file" id="image" name="image">
                            <?php if ($mode_edition && isset($produit['image_url']) && $produit['image_url'] != '') { ?>
                                    <small class="form-text text-muted">Image actuelle :</small>
                                    <img src="<?php echo htmlspecialchars($produit['image_url']); ?>" alt="Aperçu"
                                        class="img-thumbnail mt-2" style="max-height: 100px;">
                                    <input type="hidden" name="image_url_actuelle"
                                        value="<?php echo htmlspecialchars($produit['image_url']); ?>">
                            <?php } ?>
                            </div>
                        </fieldset>
                        <?php
                        $type_a_afficher = $mode_edition ? $produit['type'] : $type_produit_demande;
                        if ($type_a_afficher == 'livre') {
                            include 'partials/admin_includes/forms/_form_livre.php';
                        } elseif ($type_a_afficher == 'bougie') {
                            include 'partials/admin_includes/forms/_form_bougie.php';
                        } elseif ($type_a_afficher == 'coffret') {
                            include 'partials/admin_includes/forms/_form_coffret.php';
                        }
                        ?>
                        <button type="submit" class="btn btn-primary btn-lg">Sauvegarder le Produit</button>
                    </form>
                    <?php
                    break;
            }
            case 'commandes': {
                ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1>Gestion des Commandes</h1>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Date</th>
                                        <th>Client</th>
                                        <th>Total</th>
                                        <th>Statut</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (count($commandes) == 0) { ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Aucune commande.</td>
                                        </tr>
                                <?php } else {
                                    foreach ($commandes as $c) { ?>
                                            <tr>
                                                <td>#<?php echo $c['id_commande']; ?></td>
                                                <td><?php echo htmlspecialchars($c['date_commande']); ?></td>
                                                <td><?php echo htmlspecialchars($c['nom_client']); ?></td>
                                                <td><?php echo number_format($c['total_ttc'], 2, ',', ' '); ?> €</td>
                                                <td>
                                                    <form method="POST" action="admin.php" class="d-flex">
                                                        <input type="hidden" name="form_action" value="update_order_status">
                                                        <input type="hidden" name="id_commande" value="<?php echo $c['id_commande']; ?>">
                                                        <select name="id_statut_commande" class="form-select form-select-sm me-2">
                                                        <?php foreach ($statuts_commandes as $s) { ?>
                                                                <option value="<?php echo $s['id_statut_commande']; ?>" <?php echo ($s['id_statut_commande'] == $c['id_statut_commande'] ? 'selected' : ''); ?>>
                                                                <?php echo htmlspecialchars($s['libelle']); ?></option>
                                                        <?php } ?>
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-primary">OK</button>
                                                    </form>
                                                </td>
                                                <td class="text-end"><a
                                                        href="admin.php?p=commande_details&id=<?php echo $c['id_commande']; ?>"
                                                        class="btn btn-sm btn-info"><i class="bi bi-eye-fill"></i></a></td>
                                            </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                    break;
            }
            case 'commande_details': {
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
                                    <p><strong>Total TTC :</strong>
                                    <?php echo number_format($commande['total_ttc'], 2, ',', ' '); ?> €</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">Client & Livraison</div>
                                <div class="card-body">
                                    <p><?php echo htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']); ?>
                                        (<?php echo htmlspecialchars($commande['email']); ?>)</p>
                                    <hr>
                                    <p><strong>Adresse de livraison
                                            :</strong><br><?php echo htmlspecialchars($commande['rue']); ?><br><?php echo htmlspecialchars($commande['code_postal'] . ' ' . $commande['ville']); ?><br><?php echo htmlspecialchars($commande['pays']); ?>
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
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité</th>
                                        <th class="text-end">Prix TTC Unitaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($commande['produits'] as $p) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($p['nom_produit']); ?> <span
                                                    class="badge bg-secondary"><?php echo $p['type']; ?></span></td>
                                            <td><?php echo $p['quantite']; ?></td>
                                            <td class="text-end"><?php echo number_format($p['prix_ttc'], 2, ',', ' '); ?> €</td>
                                        </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                    break;
            }
            case 'utilisateurs': {
                ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1>Gestion des Utilisateurs</h1>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Inscrit le</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (count($utilisateurs) == 0) { ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Aucun utilisateur.</td>
                                        </tr>
                                <?php } else {
                                    foreach ($utilisateurs as $u) { ?>
                                            <tr>
                                                <td><?php echo $u['id_client']; ?></td>
                                                <td><?php echo htmlspecialchars($u['prenom'] . ' ' . $u['nom']); ?></td>
                                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                                <td><?php echo htmlspecialchars($u['date_creation']); ?></td>
                                                <td class="text-end">
                                                    <form method="POST" action="admin.php" class="d-inline">
                                                        <input type="hidden" name="form_action" value="promote_user">
                                                        <input type="hidden" name="id_client" value="<?php echo $u['id_client']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            onclick="return confirm('Promouvoir ce client en administrateur ? Le compte client sera définitivement supprimé.');">Promouvoir</button>
                                                    </form>
                                                    <a href="admin.php?p=utilisateurs&action=delete&id=<?php echo $u['id_client']; ?>"
                                                        class="btn btn-sm btn-danger ms-1"
                                                        onclick="return confirm('Sûr de vouloir supprimer ce client ?');"><i
                                                            class="bi bi-trash-fill"></i></a>
                                                </td>
                                            </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                    break;
            }
            case 'messages': {
                ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1>Messages de Contact</h1>
                    </div>
                <?php if (count($messages_contact) == 0) { ?>
                        <div class="alert alert-info">Aucun message de contact.</div>
                <?php } else {
                    foreach ($messages_contact as $msg) { ?>
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between">
                                    <span><strong>Sujet :</strong> <?php echo htmlspecialchars($msg['sujet']); ?></span>
                                    <small>Reçu le: <?php echo htmlspecialchars($msg['date_envoi']); ?></small>
                                </div>
                                <div class="card-body">
                                    <blockquote class="blockquote mb-0">
                                        <p>
                                        <pre
                                            style="font-family: inherit; font-size: inherit; margin: 0; white-space: pre-wrap;"><?php echo htmlspecialchars($msg['message']); ?></pre>
                                        </p>
                                        <footer class="blockquote-footer"><?php echo htmlspecialchars($msg['nom_visiteur']); ?> (<a
                                                href="mailto:<?php echo htmlspecialchars($msg['email_visiteur']); ?>"><?php echo htmlspecialchars($msg['email_visiteur']); ?></a>)
                                        </footer>
                                    </blockquote>
                                </div>
                                <div class="card-footer text-end">
                                    <a href="mailto:<?php echo htmlspecialchars($msg['email_visiteur']); ?>"
                                        class="btn btn-sm btn-primary">Répondre</a>
                                    <a href="admin.php?p=messages&action=delete&id=<?php echo $msg['id_message']; ?>"
                                        class="btn btn-sm btn-danger ms-1" onclick="return confirm('Sûr ?');">Supprimer</a>
                                </div>
                            </div>
                    <?php }
                }
                break;
            }
            case 'retours': {
                ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1>Gestion des Demandes de Retour</h1>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Commande</th>
                                        <th>Client</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (count($demandes_retour) == 0) { ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Aucune demande de retour.</td>
                                        </tr>
                                <?php } else {
                                    foreach ($demandes_retour as $r) { ?>
                                            <tr>
                                                <td>#<?php echo $r['id_demande']; ?></td>
                                                <td><a
                                                        href="admin.php?p=commande_details&id=<?php echo $r['id_commande']; ?>">#<?php echo $r['id_commande']; ?></a>
                                                </td>
                                                <td><?php echo htmlspecialchars($r['email_client']); ?></td>
                                                <td><?php echo htmlspecialchars($r['date_demande']); ?></td>
                                                <td>
                                                    <form method="POST" action="admin.php" class="d-flex">
                                                        <input type="hidden" name="form_action" value="update_return_status">
                                                        <input type="hidden" name="id_demande" value="<?php echo $r['id_demande']; ?>">
                                                        <select name="id_statut_demande" class="form-select form-select-sm me-2">
                                                        <?php foreach ($statuts_retour as $s) { ?>
                                                                <option value="<?php echo $s['id_statut_demande']; ?>" <?php echo ($s['id_statut_demande'] == $r['id_statut_demande'] ? 'selected' : ''); ?>>
                                                                <?php echo htmlspecialchars($s['libelle']); ?></option>
                                                        <?php } ?>
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-primary">OK</button>
                                                    </form>
                                                </td>
                                                <td class="text-end"><a href="admin.php?p=retour_details&id=<?php echo $r['id_demande']; ?>"
                                                        class="btn btn-sm btn-info"><i class="bi bi-eye-fill"></i></a></td>
                                            </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                    break;
            }
            case 'retour_details': {
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
                                    <pre
                                        style="font-family: inherit; font-size: inherit; margin: 0; white-space: pre-wrap;"><?php echo htmlspecialchars(isset($retour['message_demande']) ? $retour['message_demande'] : 'Aucun message.'); ?></pre>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">Client et Commande Associée</div>
                                <div class="card-body">
                                    <p><?php echo htmlspecialchars($retour['prenom'] . ' ' . $retour['nom']); ?>
                                        (<?php echo htmlspecialchars($retour['email']); ?>)</p>
                                    <p><strong>Commande d'origine :</strong> <a
                                            href="admin.php?p=commande_details&id=<?php echo $retour['id_commande']; ?>">#<?php echo $retour['id_commande']; ?></a>
                                        du <?php echo htmlspecialchars($retour['date_commande']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">Produits retournés</div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité</th>
                                        <th>Raison</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (count($retour['produits']) == 0) { ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Aucun produit listé dans cette demande.</td>
                                        </tr>
                                <?php } else {
                                    foreach ($retour['produits'] as $p) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($p['nom_produit']); ?></td>
                                                <td><?php echo $p['quantite']; ?></td>
                                                <td><?php echo htmlspecialchars(isset($p['raison']) ? $p['raison'] : 'Aucune raison spécifiée.'); ?>
                                                </td>
                                            </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                    break;
            }
            case 'auteurs': {
                ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1>Gestion des Auteurs</h1><a href="admin.php?p=auteur_form" class="btn btn-primary">Ajouter un Auteur</a>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom Complet</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($auteurs as $auteur) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($auteur['nom_complet']); ?></td>
                                            <td class="text-end">
                                                <a href="admin.php?p=auteur_form&id=<?php echo $auteur['id_auteur']; ?>"
                                                    class="btn btn-sm btn-warning">Modifier</a>
                                                <a href="admin.php?p=auteurs&action=delete&id=<?php echo $auteur['id_auteur']; ?>"
                                                    class="btn btn-sm btn-danger" onclick="return confirm('Sûr ?');">Supprimer</a>
                                            </td>
                                        </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                    break;
            }
            case 'auteur_form': {
                ?>
                    <a href="admin.php?p=auteurs" class="btn btn-secondary mb-3">← Retour à la liste</a>
                    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="admin.php">
                                <input type="hidden" name="form_action" value="save_auteur">
                                <input type="hidden" name="id_auteur"
                                    value="<?php echo isset($auteur['id_auteur']) ? $auteur['id_auteur'] : ''; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Prénom<span class="required-star">*</span></label>
                                    <input type="text" name="prenom" class="form-control"
                                        value="<?php echo htmlspecialchars(isset($auteur['prenom']) ? $auteur['prenom'] : ''); ?>"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nom<span class="required-star">*</span></label>
                                    <input type="text" name="nom" class="form-control"
                                        value="<?php echo htmlspecialchars(isset($auteur['nom']) ? $auteur['nom'] : ''); ?>"
                                        required>
                                </div>
                                <button type="submit" class="btn btn-primary">Sauvegarder</button>
                            </form>
                        </div>
                    </div>
                    <?php
                    break;
            }
            case 'tags': {
                ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1>Gestion des Tags</h1><a href="admin.php?p=tag_form" class="btn btn-primary">Ajouter un Tag</a>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom du Tag</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($tags as $tag) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($tag['nom_tag']); ?></td>
                                            <td class="text-end">
                                                <a href="admin.php?p=tag_form&id=<?php echo $tag['id_tag']; ?>"
                                                    class="btn btn-sm btn-warning">Modifier</a>
                                                <a href="admin.php?p=tags&action=delete&id=<?php echo $tag['id_tag']; ?>"
                                                    class="btn btn-sm btn-danger" onclick="return confirm('Sûr ?');">Supprimer</a>
                                            </td>
                                        </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                    break;
            }
            case 'tag_form': {
                ?>
                    <a href="admin.php?p=tags" class="btn btn-secondary mb-3">← Retour à la liste</a>
                    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="admin.php">
                                <input type="hidden" name="form_action" value="save_tag">
                                <input type="hidden" name="id_tag"
                                    value="<?php echo isset($tag['id_tag']) ? $tag['id_tag'] : ''; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Nom du Tag<span class="required-star">*</span></label>
                                    <input type="text" name="nom_tag" class="form-control"
                                        value="<?php echo htmlspecialchars(isset($tag['nom_tag']) ? $tag['nom_tag'] : ''); ?>"
                                        required>
                                </div>
                                <button type="submit" class="btn btn-primary">Sauvegarder</button>
                            </form>
                        </div>
                    </div>
                    <?php
                    break;
            }
            case 'editeurs': {
                ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1>Gestion des Éditeurs</h1><a href="admin.php?p=editeur_form" class="btn btn-primary">Ajouter un
                            Éditeur</a>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom de l'Éditeur</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($editeurs as $editeur) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($editeur['nom']); ?></td>
                                            <td class="text-end">
                                                <a href="admin.php?p=editeur_form&id=<?php echo $editeur['id_editeur']; ?>"
                                                    class="btn btn-sm btn-warning">Modifier</a>
                                                <a href="admin.php?p=editeurs&action=delete&id=<?php echo $editeur['id_editeur']; ?>"
                                                    class="btn btn-sm btn-danger" onclick="return confirm('Sûr ?');">Supprimer</a>
                                            </td>
                                        </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                    break;
            }
            case 'editeur_form': {
                ?>
                    <a href="admin.php?p=editeurs" class="btn btn-secondary mb-3">← Retour à la liste</a>
                    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="admin.php">
                                <input type="hidden" name="form_action" value="save_editeur">
                                <input type="hidden" name="id_editeur"
                                    value="<?php echo isset($editeur['id_editeur']) ? $editeur['id_editeur'] : ''; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Nom de l'Éditeur<span class="required-star">*</span></label>
                                    <input type="text" name="nom" class="form-control"
                                        value="<?php echo htmlspecialchars(isset($editeur['nom']) ? $editeur['nom'] : ''); ?>"
                                        required>
                                </div>
                                <button type="submit" class="btn btn-primary">Sauvegarder</button>
                            </form>
                        </div>
                    </div>
                    <?php
                    break;
            }
            default: {
                // Si la valeur de $page ne correspond à aucun 'case' connu, on affiche une page d'erreur.
                ?>
                    <div class="alert alert-danger">
                        <h4>Erreur</h4>
                        <p>La page que vous demandez n'existe pas.</p>
                        <a href="admin.php?p=dashboard" class="btn btn-primary">Retour au tableau de bord</a>
                    </div>
                    <?php
                    break;
            }
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>