<?php
/*
 * Fichier : panier2.php
 * Rôle : Gère la gestion et l'affichage du panier.
 * Version purifiée respectant la doctrine du cours.
 */

session_start();
require('parametrage/param.php');
require('fonction/fonctions.php');

// --- TRAITEMENT DES ACTIONS (LOGIQUE CONTRÔLEUR) ---
// Le cours n'enseigne pas l'opérateur de coalescence nulle '??'.
// On utilise une cascade de conditions 'if/elseif/else' avec 'isset' pour déterminer l'action.
$action = null;
if (isset($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
}

// On ne traite une action que si elle a été définie.
if ($action != null) {
    if ($action == 'update' && isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        // La fonction is_array est une construction de base qui peut être considérée comme implicite.
        foreach ($_POST['quantity'] as $id => $qty) {
            handleCartAction('update', (int) $id, (int) $qty);
        }
    } else {
        $id_produit = 0;
        if (isset($_POST['product_id'])) {
            $id_produit = (int) $_POST['product_id'];
        } elseif (isset($_GET['product_id'])) {
            $id_produit = (int) $_GET['product_id'];
        }

        $quantite = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
        handleCartAction($action, $id_produit, $quantite);
    }

    // Après chaque action, on redirige pour éviter les doubles soumissions (pattern Post-Redirect-Get).
    header('Location: panier2.php');
    exit();
}


// --- PRÉPARATION DES DONNÉES POUR L'AFFICHAGE ---
$pageTitle = "Votre Panier";
$panier_session = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$recapitulatif_panier = getCartSummary($panier_session);

$articles_panier = $recapitulatif_panier['items'];
$total_ht = $recapitulatif_panier['total_ht'];
$total_ttc = $recapitulatif_panier['total_ttc'];

// On vérifie si l'utilisateur est connecté pour l'affichage conditionnel.
$utilisateur_connecte = isset($_SESSION['user']);


require('partials/header.php');
?>
<!-- =========================================================================
     AFFICHAGE HTML (PARTIE "VUE")
========================================================================= -->

<main class="container my-4">
    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>

    <?php
    // La fonction 'empty()' n'est pas enseignée. 'count()' est la méthode sanctionnée (p.40).
    if (count($articles_panier) == 0) {
        ?>
        <div class="alert alert-info">
            Votre panier est vide. <a href="shop.php">Commencez vos achats !</a>
        </div>
    <?php } else { ?>
        <form action="panier2.php" method="POST">
            <input type="hidden" name="action" value="update">

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
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
                                    <a href="panier2.php?action=remove&product_id=<?php echo $item['id']; ?>"
                                        class="btn btn-sm btn-danger" title="Supprimer l'article">Supprimer</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-primary">Mettre à jour les quantités</button>
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
                            <a href="checkout.php" class="btn btn-success w-100">Valider et Payer</a>
                        <?php } else { ?>
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