<?php
/*
 * Fichier : mon_compte.php
 * Rôle : Contrôleur et Vue de l'espace personnel du client.
 * Ce fichier est un "mini-site" à lui tout seul. Il gère toutes les facettes
 * du compte d'un utilisateur connecté :
 *  - Affichage des informations, adresses, commandes, retours.
 *  - Modification des informations personnelles et du mot de passe.
 *  - Ajout, modification, suppression des adresses.
 *  - Consultation des détails d'une commande ou d'un retour.
 *  - Lancement d'une demande de retour pour une commande livrée.
 */


// Démarrage de la session pour accéder aux informations de l'utilisateur connecté.

session_start();
require('parametrage/param.php');
require('fonction/fonctions.php');


// --- GARDE-FOU D'ACCÈS ---

// Si la variable de session 'user' n'existe pas, cela signifie que personne n'est connecté. L'accès à cette page est donc interdit, et on redirige vers la page de connexion.
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id_client'])) {
    header('Location: auth.php?action=login');
    exit();
}


// --- INITIALISATION DU CONTRÔLEUR ---

$id_client = $_SESSION['user']['id_client'];
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// --- GESTION DES MESSAGES FLASH (pour le retour utilisateur) ---

if (isset($_SESSION['flash_success'])) {
    $message_flash_succes = $_SESSION['flash_success'];
} else {
    $message_flash_succes = null;
}

if (isset($_SESSION['flash_error'])) {
    $message_flash_erreur = $_SESSION['flash_error'];
} else {
    $message_flash_erreur = null;
}

unset($_SESSION['flash_success']);
unset($_SESSION['flash_error']);

// --- TRAITEMENT DES FORMULAIRES (POST) ---
if (isset($_POST['form_type'])) {
    $type_formulaire = $_POST['form_type'];

    switch ($type_formulaire) {
        case 'user_info':
            // Traitement du formulaire de mise à jour des informations personnelles.
            $nom = purifier_trim(isset($_POST['nom']) ? $_POST['nom'] : '');
            $prenom = purifier_trim(isset($_POST['prenom']) ? $_POST['prenom'] : '');
            if ($nom != '' && $prenom != '') {
                if (updateUserInfo($id_client, array('nom' => $nom, 'prenom' => $prenom))) {
                    $_SESSION['user']['nom'] = $nom;
                    $_SESSION['user']['prenom'] = $prenom;
                    $_SESSION['flash_success'] = "Vos informations ont été mises à jour.";
                } else {
                    $_SESSION['flash_error'] = "Erreur technique lors de la mise à jour.";
                }
            } else {
                $_SESSION['flash_error'] = "Le nom et le prénom sont obligatoires.";
            }
            header('Location: mon_compte.php?action=edit_account');
            exit();

        case 'password':
            // Traitement du formulaire de changement de mot de passe.
            $ancien_mdp = isset($_POST['old_password']) ? $_POST['old_password'] : '';
            $nouveau_mdp = isset($_POST['new_password']) ? $_POST['new_password'] : '';
            $nouveau_mdp_confirm = isset($_POST['new_password_confirm']) ? $_POST['new_password_confirm'] : '';
            $erreurs_mdp = array();

            if ($ancien_mdp == '' || $nouveau_mdp == '' || $nouveau_mdp_confirm == '') {
                $erreurs_mdp[] = "Tous les champs pour le mot de passe sont requis.";
            } elseif ($nouveau_mdp != $nouveau_mdp_confirm) {
                $erreurs_mdp[] = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
            } else {
                $erreurs_force_mdp = isPasswordStrong($nouveau_mdp);
                if (count($erreurs_force_mdp) > 0) {
                    foreach ($erreurs_force_mdp as $e) {
                        $erreurs_mdp[] = $e;
                    }
                }
            }

            if (count($erreurs_mdp) == 0) {
                $resultat_maj = updateUserPassword($id_client, $ancien_mdp, $nouveau_mdp);
                if ($resultat_maj === true) {
                    $_SESSION['flash_success'] = "Votre mot de passe a été modifié avec succès.";
                    header('Location: mon_compte.php');
                    exit();
                } else {
                    $_SESSION['flash_error'] = $resultat_maj;
                }
            } else {
                $_SESSION['flash_error'] = purifier_implode('<br>', $erreurs_mdp);
            }
            header('Location: mon_compte.php?action=edit_account');
            exit();

        case 'address':
            // Traitement du formulaire d'ajout ou de modification d'adresse.
            $donnees = array(
                'rue' => purifier_trim(isset($_POST['rue']) ? $_POST['rue'] : ''),
                'code_postal' => purifier_trim(isset($_POST['code_postal']) ? $_POST['code_postal'] : ''),
                'ville' => purifier_trim(isset($_POST['ville']) ? $_POST['ville'] : ''),
                'pays' => purifier_trim(isset($_POST['pays']) ? $_POST['pays'] : ''),
                'est_defaut' => isset($_POST['est_defaut']) ? 1 : 0
            );
            $id_adresse = isset($_POST['id_adresse']) ? (int) $_POST['id_adresse'] : 0;

            if ($donnees['rue'] == '' || $donnees['code_postal'] == '' || $donnees['ville'] == '' || $donnees['pays'] == '') {
                $_SESSION['flash_error'] = "Tous les champs de l'adresse sont obligatoires.";
                $location = $id_adresse ? "mon_compte.php?action=edit&id=" . $id_adresse : "mon_compte.php?action=add";
                header('Location: ' . $location);
                exit();
            }

            if ($id_adresse > 0) {
                updateAddress($id_adresse, $id_client, $donnees);
                $_SESSION['flash_success'] = "Adresse modifiée avec succès.";
            } else {
                addAddress($id_client, $donnees);
                $_SESSION['flash_success'] = "Adresse ajoutée avec succès.";
            }
            header('Location: mon_compte.php');
            exit();

        case 'return_request':
            // Traitement du formulaire de demande de retour.
            $id_commande = isset($_POST['id_commande']) ? (int) $_POST['id_commande'] : 0;
            $commande = getOrderDetailsForUser($id_commande, $id_client);
            if ($commande) {
                $resultat = createReturnRequest($id_client, $id_commande, $_POST);
                if ($resultat === true) {
                    $_SESSION['flash_success'] = "Votre demande de retour a bien été enregistrée.";
                    header('Location: mon_compte.php?action=list');
                    exit();
                } else {
                    $_SESSION['flash_error'] = $resultat;
                }
            } else {
                $_SESSION['flash_error'] = "Opération non autorisée.";
            }
            header('Location: mon_compte.php?action=demande_retour&id=' . $id_commande);
            exit();
    }
}


// --- TRAITEMENT DES ACTIONS (logique GET) ---
// Principalement pour les actions de suppression qui utilisent un simple lien.

if ($action == 'delete_address' && $id > 0) {
    if (deleteAddress($id, $id_client)) {
        $_SESSION['flash_success'] = "Adresse supprimée avec succès.";
    } else {
        $_SESSION['flash_error'] = "Erreur lors de la suppression de l'adresse.";
    }
    header('Location: mon_compte.php');
    exit();
}


// --- PRÉPARATION DES DONNÉES POUR L'AFFICHAGE ---
switch ($action) {
    case 'edit_account':
        $pageTitle = "Modifier mes Informations";
        break;
    case 'add':
        $pageTitle = "Ajouter une Adresse";
        $adresse = array('id_adresse' => '', 'rue' => '', 'code_postal' => '', 'ville' => '', 'pays' => '', 'est_defaut' => 0);
        break;
    case 'edit':
        $pageTitle = "Modifier une Adresse";
        $adresse = getAddressById($id, $id_client);
        // Si l'adresse n'existe pas ou n'appartient pas au client, on le redirige.
        if (!$adresse) {
            header('Location: mon_compte.php');
            exit();
        }
        break;
    case 'details_commande':
        $pageTitle = "Détails de la Commande";
        $details_commande = getOrderDetailsForUser($id, $id_client);
        if (!$details_commande) {
            header('Location: mon_compte.php');
            exit();
        }
        $retour_deja_demande = checkIfReturnExistsForOrder($id);
        break;
    case 'details_retour':
        $pageTitle = "Détails de la Demande de Retour";
        $details_retour = getReturnRequestDetailsForUser($id, $id_client);
        if (!$details_retour) {
            $_SESSION['flash_error'] = "Demande de retour non trouvée.";
            header('Location: mon_compte.php');
            exit();
        }
        break;
    case 'demande_retour':
        $pageTitle = "Demande de Retour";
        $commande_a_retourner = getOrderDetailsForUser($id, $id_client);
        if (!$commande_a_retourner) {
            $_SESSION['flash_error'] = "Commande non trouvée.";
            header('Location: mon_compte.php');
            exit();
        }
        break;
    case 'list':
    default:
        $pageTitle = "Mon Compte";
        $adresses = getUserAddresses($id_client);
        $commandes_utilisateur = getUserOrders($id_client);
        $retours_utilisateur = getUserReturnRequests($id_client);
        break;
}

require('partials/header.php');
?>
<!-- =========================================================================
     AFFICHAGE HTML 
========================================================================= -->
<main class="container my-4">
    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>

    <?php if ($message_flash_succes != null) { ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message_flash_succes); ?></div>
    <?php } ?>
    <?php if ($message_flash_erreur != null) { ?>
        <div class="alert alert-danger"><?php echo $message_flash_erreur; ?></div>
    <?php } ?>

    <?php switch ($action) {
        // Vue principale du compte avec tous les panneaux.

        case 'list':
        default: ?>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Mes informations personnelles</h3>
                    <a href="mon_compte.php?action=edit_account" class="btn btn-secondary btn-sm">Modifier</a>
                </div>
                <div class="card-body">
                    <p><strong>Nom :</strong> <?php echo htmlspecialchars($_SESSION['user']['nom']); ?></p>
                    <p><strong>Prénom :</strong> <?php echo htmlspecialchars($_SESSION['user']['prenom']); ?></p>
                    <p><strong>Email :</strong> <?php echo htmlspecialchars($_SESSION['user']['email']); ?></p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h3>Mon historique de commandes</h3>
                </div>
                <div class="card-body">
                    <?php if (count($commandes_utilisateur) == 0) { ?>
                        <p>Vous n'avez encore passé aucune commande.</p>
                    <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Date</th>
                                        <th>Total TTC</th>
                                        <th>Statut</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($commandes_utilisateur as $commande) { ?>
                                        <tr>
                                            <td>#<?php echo htmlspecialchars($commande['id_commande']); ?></td>
                                            <td><?php echo htmlspecialchars($commande['date_commande']); ?></td>
                                            <td><?php echo number_format($commande['total_ttc'], 2, ',', ' '); ?> €</td>
                                            <td><span
                                                    class="badge <?php echo getOrderStatusBadgeClass($commande['statut_libelle']); ?>"><?php echo htmlspecialchars($commande['statut_libelle']); ?></span>
                                            </td>
                                            <td><a href="mon_compte.php?action=details_commande&id=<?php echo $commande['id_commande']; ?>"
                                                    class="btn btn-sm btn-primary">Voir</a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Mes adresses de livraison</h3>
                    <a href="mon_compte.php?action=add" class="btn btn-primary">Ajouter une adresse</a>
                </div>
                <div class="card-body">
                    <?php if (count($adresses) == 0) { ?>
                        <p>Vous n'avez aucune adresse enregistrée.</p>
                    <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Adresse</th>
                                        <th class="text-center">Par défaut</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($adresses as $adr) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($adr['rue'] . ', ' . $adr['code_postal'] . ' ' . $adr['ville'] . ', ' . $adr['pays']); ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (isset($adr['est_defaut']) && $adr['est_defaut']) {
                                                    echo '<span class="badge bg-success">Oui</span>';
                                                } else {
                                                    echo 'Non';
                                                } ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="mon_compte.php?action=edit&id=<?php echo $adr['id_adresse']; ?>"
                                                    class="btn btn-sm btn-warning">Modifier</a>
                                                <a href="mon_compte.php?action=delete_address&id=<?php echo $adr['id_adresse']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Êtes-vous sûr ?');">Supprimer</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h3>Mes demandes de retour</h3>
                </div>
                <div class="card-body">
                    <?php if (count($retours_utilisateur) == 0) { ?>
                        <p>Vous n'avez aucune demande de retour.</p>
                    <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>N° Demande</th>
                                        <th>N° Commande</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($retours_utilisateur as $retour) { ?>
                                        <tr>
                                            <td>#<?php echo htmlspecialchars($retour['id_demande']); ?></td>
                                            <td>#<?php echo htmlspecialchars($retour['id_commande']); ?></td>
                                            <td><?php echo htmlspecialchars($retour['date_demande']); ?></td>
                                            <td><span
                                                    class="badge <?php echo getOrderStatusBadgeClass($retour['statut_libelle']); ?>"><?php echo htmlspecialchars($retour['statut_libelle']); ?></span>
                                            </td>
                                            <td><a href="mon_compte.php?action=details_retour&id=<?php echo $retour['id_demande']; ?>"
                                                    class="btn btn-sm btn-info">Voir les détails</a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <br/>
            <div class="d-flex justify-content-end mb-3">
                <form action="auth.php?action=logout" method="POST">
                    <button type="submit" class="btn btn-danger">Se déconnecter</button>
                </form>
            </div>

            <?php break;

        case 'edit_account': 
            // Vue de modification des infos personnelles et du mot de passe.?>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card p-4 h-100">
                        <h5 class="card-title">Mes informations personnelles</h5>
                        <form method="POST" action="mon_compte.php">
                            <input type="hidden" name="form_type" value="user_info">
                            <div class="mb-3"><label for="nom" class="form-label">Nom</label><input type="text"
                                    class="form-control" id="nom" name="nom"
                                    value="<?php echo htmlspecialchars($_SESSION['user']['nom']); ?>" required></div>
                            <div class="mb-3"><label for="prenom" class="form-label">Prénom</label><input type="text"
                                    class="form-control" id="prenom" name="prenom"
                                    value="<?php echo htmlspecialchars($_SESSION['user']['prenom']); ?>" required></div>
                            <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email"
                                    class="form-control" id="email"
                                    value="<?php echo htmlspecialchars($_SESSION['user']['email']); ?>" disabled></div>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card p-4 h-100">
                        <h5 class="card-title">Changer mon mot de passe</h5>
                        <p class="card-text text-muted small">8 car. min, majuscule, minuscule, chiffre, spécial.</p>
                        <form method="POST" action="mon_compte.php">
                            <input type="hidden" name="form_type" value="password">
                            <div class="mb-3"><label for="old_password" class="form-label">Mot de passe actuel</label><input
                                    type="password" class="form-control" id="old_password" name="old_password" required></div>
                            <div class="mb-3"><label for="new_password" class="form-label">Nouveau mot de passe</label><input
                                    type="password" class="form-control" id="new_password" name="new_password" required></div>
                            <div class="mb-3"><label for="new_password_confirm" class="form-label">Confirmer</label><input
                                    type="password" class="form-control" id="new_password_confirm" name="new_password_confirm"
                                    required></div>
                            <button type="submit" class="btn btn-warning">Changer le mot de passe</button>
                        </form>
                    </div>
                </div>
            </div>
            <a href="mon_compte.php" class="btn btn-secondary mt-3">Retour à mon compte</a>
            <?php break;

        case 'add':
        case 'edit': ?>
            <!-- Vue ajout/modification adresse -->
            <div class="card p-4 mt-3">
                <form method="POST" action="mon_compte.php">
                    <input type="hidden" name="form_type" value="address">
                    <input type="hidden" name="id_adresse" value="<?php echo htmlspecialchars($adresse['id_adresse']); ?>">
                    <div class="mb-3"><label for="rue" class="form-label">Rue et numéro</label><input type="text"
                            class="form-control" id="rue" name="rue" value="<?php echo htmlspecialchars($adresse['rue']); ?>"
                            required></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="code_postal" class="form-label">Code Postal</label><input
                                type="text" class="form-control" id="code_postal" name="code_postal"
                                value="<?php echo htmlspecialchars($adresse['code_postal']); ?>" required></div>
                        <div class="col-md-6 mb-3"><label for="ville" class="form-label">Ville</label><input type="text"
                                class="form-control" id="ville" name="ville"
                                value="<?php echo htmlspecialchars($adresse['ville']); ?>" required></div>
                    </div>
                    <div class="mb-3"><label for="pays" class="form-label">Pays</label><input type="text" class="form-control"
                            id="pays" name="pays" value="<?php echo htmlspecialchars($adresse['pays']); ?>" required></div>
                    <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="est_defaut"
                            id="est_defaut" value="1" <?php if ($adresse['est_defaut'])
                                echo 'checked'; ?>><label
                            class="form-check-label" for="est_defaut">Faire de cette adresse mon adresse par défaut</label>
                    </div>
                    <button type="submit" class="btn btn-success"><?php if ($action == 'edit') {
                        echo 'Enregistrer';
                    } else {
                        echo 'Ajouter l\'adresse';
                    } ?></button>
                    <a href="mon_compte.php" class="btn btn-secondary">Annuler</a>
                </form>
            </div>
            <?php break;

        case 'details_commande': ?>
            <!-- Vue détails d'une commande -->
            <div class="card mt-3">
                <div class="card-body">
                    <p><strong>Date :</strong> <?php echo htmlspecialchars($details_commande['date_commande']); ?></p>
                    <p><strong>Statut :</strong> <?php echo htmlspecialchars($details_commande['statut_libelle']); ?></p>
                    <p><strong>Total :</strong> <?php echo number_format($details_commande['total_ttc'], 2, ',', ' '); ?> €</p>
                    <h5 class="mt-4">Produits commandés</h5>
                    <ul class="list-group">
                        <?php foreach ($details_commande['produits'] as $produit) { ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div><?php echo htmlspecialchars($produit['quantite']); ?> x
                                    <?php echo htmlspecialchars(isset($produit['nom_produit']) ? $produit['nom_produit'] : '[Produit non disponible]'); ?>
                                </div>
                                <span><?php echo number_format($produit['prix_ttc'], 2, ',', ' '); ?> €</span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="card-footer text-end">
                    <?php
                    // La logique d'affichage du bouton de retour.
                    // Pour que le bouton soit actif, il faut DEUX conditions :
                    // 1. La commande doit être au statut "Livrée".
                    // 2. Il ne doit pas y avoir de demande de retour déjà en cours pour cette commande.                    
                    $condition_retour_possible = (isset($details_commande['statut_libelle']) && $details_commande['statut_libelle'] == 'Livrée');
                    if ($condition_retour_possible == true && $retour_deja_demande == false) {
                        ?>
                        <a href="mon_compte.php?action=demande_retour&id=<?php echo $details_commande['id_commande']; ?>"
                            class="btn btn-warning">Faire une demande de retour</a>
                        <?php
                    } else {
                        // Si l'une des conditions n'est pas remplie, le bouton est désactivé.
                        $message_inactif = 'Retour non disponible';
                        if ($retour_deja_demande == true) {
                            $message_inactif = 'Demande de retour déjà effectuée';
                        }
                        ?>
                        <button class="btn btn-secondary" disabled><?php echo $message_inactif; ?></button>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <a href="mon_compte.php" class="btn btn-secondary mt-3">Retour à l'historique</a>
            <?php break;

        case 'demande_retour': ?>
            <!-- Vue formulaire de retour -->
            <form method="POST" action="mon_compte.php">
                <input type="hidden" name="form_type" value="return_request">
                <input type="hidden" name="id_commande" value="<?php echo $commande_a_retourner['id_commande']; ?>">
                <div class="card">
                    <div class="card-header">
                        <h4>Retour pour la Commande #<?php echo $commande_a_retourner['id_commande']; ?></h4>
                        <p class="text-muted mb-0">Veuillez sélectionner les produits que vous souhaitez retourner.</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;"></th>
                                        <th>Produit</th>
                                        <th style="width: 20%;">Quantité à retourner</th>
                                        <th>Raison (optionnel)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($commande_a_retourner['produits'] as $produit) { ?>
                                        <tr>
                                            <td><input type="checkbox" class="form-check-input"
                                                    name="produits[<?php echo $produit['id_produit']; ?>][selected]" value="1"></td>
                                            <td><?php echo htmlspecialchars(isset($produit['nom_produit']) ? $produit['nom_produit'] : '[Produit non disponible]'); ?>
                                            </td>
                                            <td><input type="number"
                                                    name="produits[<?php echo $produit['id_produit']; ?>][quantite]"
                                                    class="form-control" value="<?php echo $produit['quantite']; ?>" min="1"
                                                    max="<?php echo $produit['quantite']; ?>"></td>
                                            <td><input type="text" name="produits[<?php echo $produit['id_produit']; ?>][raison]"
                                                    class="form-control" placeholder="Ex: Produit endommagé"></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="mb-3"><label for="message_global" class="form-label">Message complémentaire
                                (optionnel)</label><textarea name="message_global" id="message_global" class="form-control"
                                rows="3" placeholder="Vous pouvez ajouter ici des détails sur votre demande."></textarea></div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="mon_compte.php?action=details_commande&id=<?php echo $commande_a_retourner['id_commande']; ?>"
                            class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Envoyer la demande</button>
                    </div>
                </div>
            </form>
            <?php break;

        case 'details_retour': ?>
            <!-- Vue détails d'un retour -->
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Détails de la Demande #<?php echo $details_retour['id_demande']; ?></h4>
                </div>
                <div class="card-body">
                    <p><strong>Date de la demande :</strong> <?php echo htmlspecialchars($details_retour['date_demande']); ?>
                    </p>
                    <p><strong>Commande associée :</strong> <a
                            href="mon_compte.php?action=details_commande&id=<?php echo $details_retour['id_commande']; ?>">#<?php echo $details_retour['id_commande']; ?></a>
                    </p>
                    <p><strong>Statut de la demande :</strong>
                        <?php echo htmlspecialchars($details_retour['statut_libelle']); ?></p>

                    <?php
                    $message_statut = null;
                    $alert_class = 'alert-info';
                    switch ($details_retour['statut_libelle']) {
                        case 'Demande acceptée':
                            $message_statut = "<strong>Votre demande de retour a été approuvée.</strong><br>Veuillez suivre les instructions reçues par e-mail.";
                            $alert_class = 'alert-success';
                            break;
                        case 'Demande refusée':
                            $message_statut = "<strong>Votre demande de retour a été refusée.</strong><br>Contactez le service client pour plus d'informations.";
                            $alert_class = 'alert-danger';
                            break;
                    }

                    if ($message_statut != null) {
                        ?>
                        <div class="alert <?php echo $alert_class; ?> mt-4" role="alert"><?php echo $message_statut; ?></div>
                    <?php } ?>

                    <?php if (isset($details_retour['message_demande']) && $details_retour['message_demande'] != '') { ?>
                        <p class="mt-4"><strong>Votre message initial :</strong></p>
                        <blockquote class="blockquote bg-light p-3 rounded">
                            <p class="mb-0 fst-italic">"<?php echo htmlspecialchars($details_retour['message_demande']); ?>"</p>
                        </blockquote>
                    <?php } ?>

                    <h5 class="mt-4">Produits concernés par le retour</h5>
                    <ul class="list-group">
                        <?php foreach ($details_retour['produits'] as $produit) { ?>
                            <li class="list-group-item">
                                <strong><?php echo htmlspecialchars($produit['quantite']); ?> x
                                    <?php echo htmlspecialchars(isset($produit['nom_produit']) ? $produit['nom_produit'] : '[Produit non disponible]'); ?></strong>
                                <?php if (isset($produit['raison']) && $produit['raison'] != '') { ?>
                                    <br><small class="text-muted">Raison : <?php echo htmlspecialchars($produit['raison']); ?></small>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <a href="mon_compte.php" class="btn btn-secondary mt-3">Retour à mon compte</a>
            <?php break;

    } ?>
</main>

<?php require('partials/footer.php'); ?>