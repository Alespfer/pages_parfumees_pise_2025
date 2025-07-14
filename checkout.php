<?php
/*
 * Fichier : checkout.php
 * Rôle : Contrôleur et Vue de la page de finalisation de la commande.
 * Ce script est la dernière étape avant le paiement. Il a pour mission de :
 *  1. Vérifier que l'utilisateur est connecté et que son panier n'est pas vide.
 *  2. Afficher un récapitulatif de la commande.
 *  3. Fournir un formulaire pour choisir l'adresse et saisir les informations de paiement.
 *  4. Traiter ce formulaire de manière sécurisée (validation des données, prévention de double soumission).
 *  5. Appeler la fonction de création de commande et de paiement.
 *  6. Afficher une page de succès ou d'erreur.
 */


session_start();
require('parametrage/param.php');
require('fonction/fonctions.php');

// --- GARDE-FOUS ET VÉRIFICATIONS PRÉLIMINAIRES ---
// 1. L'utilisateur doit être connecté. Sinon, redirection vers la page de connexion.

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id_client'])) {
    $_SESSION['flash_message'] = "Vous devez être connecté pour finaliser votre commande.";
    header('Location: auth.php?action=login&redirect=checkout.php');
    exit();
}


// 2. Le panier ne doit pas être vide, sauf si on arrive sur la page de succès.

$panier_session = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$est_page_succes = (isset($_GET['success']) && $_GET['success'] == 1);

if (count($panier_session) == 0 && !$est_page_succes) {
    header('Location: panier.php');
    exit();
}

// --- INITIALISATION DES VARIABLES POUR LA VUE ---

$pageTitle = "Finalisation de la Commande";
$id_client = $_SESSION['user']['id_client'];
$message_flash_erreur = isset($_SESSION['flash_message']) ? $_SESSION['flash_message'] : null;
unset($_SESSION['flash_message']);

// --- TRAITEMENT DU FORMULAIRE DE PAIEMENT ---
if (isset($_POST['form_token'])) {

    // --- PROTOCOLE D'IDEMPOTENCE (contre le double-clic) ---
    $token_form = $_POST['form_token'];
    $token_session = isset($_SESSION['form_token']) ? $_SESSION['form_token'] : '';

    if ($token_form == '' || $token_form != $token_session) {
        $_SESSION['flash_message'] = "Erreur de soumission. La commande a peut-être déjà été traitée.";
        header('Location: mon_compte.php');
        exit();
    }

    // --- VALIDATION DES DONNÉES DU FORMULAIRE ---
    $id_adresse = isset($_POST['id_adresse']) ? (int) $_POST['id_adresse'] : 0;
    $nom_carte = trim(isset($_POST['card_name']) ? $_POST['card_name'] : '');
    $numero_carte = trim(isset($_POST['card_number']) ? $_POST['card_number'] : '');
    $expiration_carte = trim(isset($_POST['card_expiry']) ? $_POST['card_expiry'] : '');
    $cvc_carte = trim(isset($_POST['card_cvc']) ? $_POST['card_cvc'] : '');

    $erreurs_formulaire = array();
    if ($id_adresse == 0 || !getAddressById($id_adresse, $id_client)) {
        $erreurs_formulaire[] = "Veuillez sélectionner une adresse de livraison valide.";
    }
    // VALIDATION DES CHAMPS DE PAIEMENT
    if ($nom_carte == '') {
        $erreurs_formulaire[] = "Le nom sur la carte est obligatoire.";
    }
    if (!validateCardNumber($numero_carte)) {
        $erreurs_formulaire[] = "Le numéro de carte de crédit est invalide.";
    }
    if (!validateExpiryDate($expiration_carte)) {
        $erreurs_formulaire[] = "La date d'expiration est invalide ou dépassée.";
    }
    if (!validateCVC($cvc_carte)) {
        $erreurs_formulaire[] = "Le code CVC est invalide.";
    }


    if (count($erreurs_formulaire) == 0) {
        // La validation a réussi, on procède à la création de la commande.
        $recapitulatif_panier_post = getCartSummary($panier_session);
        $resultat = createOrderAndPayment($id_client, $id_adresse, $panier_session, $recapitulatif_panier_post);

        if (is_int($resultat)) {
            unset($_SESSION['form_token']);
            unset($_SESSION['cart']);
            header('Location: checkout.php?success=1&id=' . $resultat);
            exit();
        } else {
            // Erreur de stock ou autre, la commande n'a pas été créée.
            $_SESSION['flash_message'] = $resultat;
            header('Location: checkout.php');
            exit();
        }
    } else {
        // La validation a échoué, on retourne à la page avec les erreurs.
        $_SESSION['flash_message'] = implode('<br>', $erreurs_formulaire);
        header('Location: checkout.php');
        exit();
    }
}

// --- PRÉPARATION DES DONNÉES POUR L'AFFICHAGE ---
$_SESSION['form_token'] = custom_hash(time() . $id_client);
$adresses = getUserAddresses($id_client);
$recapitulatif_panier = getCartSummary($panier_session);
$is_stock_ok = true;
$erreurs_stock = array();


// On vérifie une dernière fois le stock pour l'affichage et pour désactiver le bouton de paiement si besoin.
foreach ($recapitulatif_panier['items'] as $item) {
    if ($item['quantity'] > $item['stock']) {
        $is_stock_ok = false;
        $erreurs_stock[] = "Stock insuffisant pour le produit : \"" . htmlspecialchars($item['name']) . "\". Quantité demandée : " . $item['quantity'] . ", stock restant : " . $item['stock'] . ".";
    }
}

if (!$is_stock_ok) {
    $message_flash_erreur = implode('<br>', $erreurs_stock);
}

require('partials/header.php');
?>
<!-- =========================================================================
     AFFICHAGE HTML (PARTIE "VUE")
========================================================================= -->

<main class="container my-4">

    <?php if ($est_page_succes && isset($_GET['id'])) { ?>
        <!-- Vue de succès : affichée après une commande réussie. -->
        <div class="alert alert-success text-center p-5">
            <h1 class="alert-heading">Paiement réussi !</h1>
            <p class="lead">Votre commande n°<?php echo (int) $_GET['id']; ?> a été enregistrée avec succès.</p>
            <hr>
            <a href="shop.php" class="btn btn-primary me-2">Continuer mes achats</a>
            <a href="mon_compte.php?action=details_commande&id=<?php echo (int) $_GET['id']; ?>"
                class="btn btn-secondary">Voir les détails de ma commande</a>
        </div>

    <?php } else { ?>
        <!-- Vue principale : affichée avant la soumission de la commande. -->
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>

        <?php if ($message_flash_erreur != null) { ?>
            <div class="alert alert-danger"><?php echo $message_flash_erreur; ?></div>
        <?php } ?>

        <div class="row">
            <div class="col-md-7">
                <h2>Récapitulatif de votre panier</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-end">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recapitulatif_panier['items'] as $item) { ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($item['name']); ?>
                                    <?php if ($item['quantity'] > $item['stock']) { ?>
                                        <br><small class="text-danger">Stock insuffisant (Restant:
                                            <?php echo $item['stock']; ?>)</small>
                                    <?php } ?>
                                </td>
                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                <td class="text-end"><?php echo number_format($item['line_ttc'], 2, ',', ' '); ?> €</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <th colspan="2" class="text-end">Total à payer :</th>
                            <th class="text-end">
                                <?php echo number_format($recapitulatif_panier['total_ttc'], 2, ',', ' '); ?> €
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="col-md-5">
                <h2>Validation et Paiement</h2>
                <div class="card p-3 shadow-sm">
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>">

                        <div class="mb-3">
                            <label for="id_adresse" class="form-label"><strong>1. Adresse de livraison</strong></label>
                            <?php if (count($adresses) == 0) { ?>
                                <div class="alert alert-warning">Vous devez <a href="mon_compte.php?action=add">ajouter une
                                        adresse</a> avant de continuer.</div>
                            <?php } else { ?>
                                <select name="id_adresse" id="id_adresse" class="form-select" required>
                                    <?php foreach ($adresses as $adresse) { ?>
                                        <option value="<?php echo $adresse['id_adresse']; ?>" <?php if (isset($adresse['est_defaut']) && $adresse['est_defaut'])
                                               echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($adresse['rue'] . ', ' . $adresse['code_postal'] . ' ' . $adresse['ville']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label"><strong>2. Informations de paiement</strong></label>
                            <input type="text" name="card_name" class="form-control mb-2" placeholder="Nom sur la carte"
                                required>
                            <input type="text" name="card_number" class="form-control mb-2"
                                placeholder="Numéro de carte (16 chiffres)" required>
                            <div class="row g-2">
                                <div class="col-7">
                                    <label for="card_expiry" class="form-label small mb-1">Date d'expiration</label>
                                    <input type="text" name="card_expiry" id="card_expiry" class="form-control"
                                        placeholder="MM/AA" required>
                                </div>
                                <div class="col-5">
                                    <label for="card_cvc" class="form-label small mb-1">CVC</label>
                                    <input type="text" name="card_cvc" id="card_cvc" class="form-control" placeholder="CVC"
                                        required>
                                </div>
                            </div>
                        </div>
                        <!-- Le bouton de paiement est désactivé si aucune adresse n'est disponible ou si le stock est insuffisant. -->
                        <button type="submit" class="btn btn-primary" <?php if (count($adresses) == 0 || !$is_stock_ok)
                            echo 'disabled'; ?>>
                            Payer <?php echo number_format($recapitulatif_panier['total_ttc'], 2, ',', ' '); ?> €
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>
</main>

<?php require('partials/footer.php'); ?>