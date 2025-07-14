<?php
/*
 * Fichier : panier.php
 * Rôle : Contrôleur et Vue de la page du panier d'achats.
 * Ce script a une double responsabilité :
 *  1. Traiter les actions de modification du panier :
 *     - Ajout d'un produit (add) de manière sécurisée.
 *     - Mise à jour des quantités (update).
 *     - Suppression d'un article (remove).
 *  2. Afficher le contenu détaillé du panier et les totaux.
 */


session_start();
require('parametrage/param.php');
require('fonction/fonctions.php');

// --- TRAITEMENT DES ACTIONS (LOGIQUE CONTRÔLEUR) ---
// On détermine d'abord si une action est demandée, que ce soit via POST (formulaire) ou GET (lien).

$action = null;
if (isset($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
}

// On n'exécute le bloc de traitement que si une action a été clairement identifiée.
if ($action != null) {

    // --- PROTOCOLE D'IDEMPOTENCE POUR L'AJOUT (éviter double clic) ---
    if ($action == 'add') {
        $token_form = isset($_POST['add_to_cart_token']) ? $_POST['add_to_cart_token'] : '';
        $token_session = isset($_SESSION['add_to_cart_token']) ? $_SESSION['add_to_cart_token'] : '';
        // Si le jeton envoyé par le formulaire est vide ou ne correspond pas à celui en session, c'est que l'action est invalide (probablement déjà traitée).
        if ($token_form == '' || $token_form != $token_session) {
            // On redirige vers le panier sans effectuer d'action pour éviter l'erreur.
            header('Location: panier.php');
            exit();
        }
    }

    // Cas spécifique de la mise à jour de toutes les quantités en une seule fois.
    if ($action == 'update' && isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $id => $qty) {
            handleCartAction('update', (int) $id, (int) $qty);
        }
    } else {
        // Cas des autres actions (add, remove) qui concernent un seul produit.
        $id_produit = 0;
        if (isset($_POST['product_id'])) {
            $id_produit = (int) $_POST['product_id'];
        } elseif (isset($_GET['product_id'])) {
            $id_produit = (int) $_GET['product_id'];
        }

        $quantite = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
        handleCartAction($action, $id_produit, $quantite);
    }

    header('Location: panier.php');
    exit();
}


// --- PRÉPARATION DES DONNÉES POUR L'AFFICHAGE ---
$pageTitle = "Votre Panier";
$panier_session = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$recapitulatif_panier = getCartSummary($panier_session);


// On extrait les données du récapitulatif dans des variables plus simples pour la vue.

$articles_panier = $recapitulatif_panier['items'];
$total_ht = $recapitulatif_panier['total_ht'];
$total_ttc = $recapitulatif_panier['total_ttc'];

$utilisateur_connecte = isset($_SESSION['user']);


require('partials/header.php');
?>
<!-- =========================================================================
     AFFICHAGE HTML (PARTIE "VUE")
========================================================================= -->

<main class="container my-4">
    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>

    <?php
    // Si le panier est vide (count == 0), on affiche un message informatif.
    if (count($articles_panier) == 0) {
        ?>
        <div class="alert alert-info">
            Votre panier est vide. <a href="shop.php">Commencez vos achats !</a>
        </div>
    <?php } else { ?>
        <!-- Si le panier contient des articles, on affiche le tableau et les actions. -->
        <form action="panier.php" method="POST">
            <input type="hidden" name="action" value="update">

            <div class="table-responsive">
                <table class="table table-bordered align-middle panier-table">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th class="text-end">Prix unitaire HT</th>
                            <th style="width: 150px;" class="text-center">Quantité</th>
                            <th class="text-end">Total HT</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles_panier as $item) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td class="text-end"><?php echo number_format($item['unit_ht'], 2, ',', ' '); ?> €</td>
                                <td class="text-center">
                                    <input type="number" name="quantity[<?php echo $item['id']; ?>]"
                                        value="<?php echo $item['quantity']; ?>" min="0" max="<?php echo $item['stock']; ?>"
                                        class="form-control text-center">
                                </td>
                                <td class="text-end"><?php echo number_format($item['line_ht'], 2, ',', ' '); ?> €</td>
                                <td class="text-center">
                                    <a href="panier.php?action=remove&product_id=<?php echo $item['id']; ?>"
                                        class="btn btn-sm btn-danger" title="Supprimer l'article">Supprimer</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-primary"> 
                    Mettre à jour les quantités
                </button>
            </div>
        </form>

        <div class="row justify-content-end">
            <div class="col-md-5"> 
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">Total de la commande</h3>
                        <table class="table mb-3">
                            <tr>
                                <td>Total HT</td>
                                <td class="text-end"><?php echo number_format($total_ht, 2, ',', ' '); ?> €</td>
                            </tr>
                            <tr class="fw-bold">
                                <td>Total TTC</td>
                                <td class="text-end"><?php echo number_format($total_ttc, 2, ',', ' '); ?> €</td>
                            </tr>
                        </table>

                        <?php if ($utilisateur_connecte) { ?>
                            <!-- Si l'utilisateur est connecté, on affiche le bouton pour finaliser la commande. -->
                            <a href="checkout.php" class="btn btn-success w-100">Valider et Payer</a>
                        <?php } else { ?>
                            <!-- Sinon, on l'invite à se connecter ou à créer un compte. -->
                            <div class="alert alert-warning text-center">
                                <a href="auth.php?action=login&redirect=checkout.php">Connectez-vous</a> ou <a
                                    href="auth.php?action=register">créez un compte</a> pour finaliser votre commande.
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</main>

<?php require('partials/footer.php'); ?>