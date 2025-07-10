<?php
/*
 * Fichier : checkout.php
 * Rôle : Gère la finalisation de la commande (récapitulatif, paiement) et l'écran de succès.
 * Version purifiée respectant la doctrine du cours et maintenant toutes les fonctionnalités.
 */

session_start();
require('parametrage/param.php');
require('fonction/fonctions.php');


// --- GARDE-FOUS ET ROUTAGE ---

// 1. L'utilisateur doit être connecté.
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id_client'])) {
    // Mécanisme de message flash orthodoxe via $_SESSION
    $_SESSION['flash_message'] = "Vous devez être connecté pour finaliser votre commande.";
    header('Location: auth.php?action=login&redirect=checkout.php');
    exit();
}

// 2. Le panier ne doit pas être vide, sauf si on affiche la page de succès.
$panier_session = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$est_page_succes = (isset($_GET['success']) && $_GET['success'] == 1);

if (count($panier_session) == 0 && !$est_page_succes) {
    header('Location: panier2.php');
    exit();
}

// --- INITIALISATION DES VARIABLES ---
$pageTitle = "Finalisation de la Commande";
$id_client = $_SESSION['user']['id_client'];
// Lecture et nettoyage du message flash
$message_flash_erreur = isset($_SESSION['flash_message']) ? $_SESSION['flash_message'] : null;
unset($_SESSION['flash_message']);


// --- TRAITEMENT DU FORMULAIRE DE PAIEMENT ---
if (isset($_POST['id_adresse'])) {
    $id_adresse = isset($_POST['id_adresse']) ? (int) $_POST['id_adresse'] : 0;
    $nom_carte = purifier_trim(isset($_POST['card_name']) ? $_POST['card_name'] : '');
    $numero_carte = purifier_trim(isset($_POST['card_number']) ? $_POST['card_number'] : '');
    $expiration_carte = purifier_trim(isset($_POST['card_expiry']) ? $_POST['card_expiry'] : '');
    $cvc_carte = purifier_trim(isset($_POST['card_cvc']) ? $_POST['card_cvc'] : '');

    $erreurs_formulaire = array();
    if ($id_adresse == 0 || !getAddressById($id_adresse, $id_client)) {
        $erreurs_formulaire[] = "Veuillez sélectionner une adresse de livraison valide.";
    }
    if (!validateCardNumber($numero_carte) || !validateExpiryDate($expiration_carte) || !validateCVC($cvc_carte)) {
        $erreurs_formulaire[] = "Les informations de votre carte de paiement sont invalides.";
    }

    if (count($erreurs_formulaire) == 0) {
        $recapitulatif_panier = getCartSummary($panier_session);
        $id_commande = createOrderAndPayment($id_client, $id_adresse, $panier_session, $recapitulatif_panier);

        if ($id_commande) {
            unset($_SESSION['cart']);
            header('Location: checkout.php?success=1&id=' . $id_commande);
            exit();
        } else {
            $_SESSION['flash_message'] = "Une erreur technique est survenue lors de la création de votre commande. Veuillez réessayer.";
            header('Location: checkout.php');
            exit();
        }
    } else {
        // En cas d'erreur de formulaire, on stocke les erreurs et on recharge la page.
        // La fonction 'purifier_implode' est utilisée pour créer la chaîne d'erreur.
        $_SESSION['flash_message'] = purifier_implode('<br>', $erreurs_formulaire);
        header('Location: checkout.php');
        exit();
    }
}


// --- PRÉPARATION DES DONNÉES POUR L'AFFICHAGE ---
$adresses = getUserAddresses($id_client);
$recapitulatif_panier = getCartSummary($panier_session);


require('partials/header.php');
?>
<!-- =========================================================================
     AFFICHAGE HTML (PARTIE "VUE")
========================================================================= -->

<main class="container my-4">

    <?php if ($est_page_succes && isset($_GET['id'])) { ?>
        <div class="alert alert-success text-center p-5">
            <h1 class="alert-heading">Paiement réussi !</h1>
            <p class="lead">Votre commande n°<?php echo (int) $_GET['id']; ?> a été enregistrée avec succès.</p>
            <hr>
            <a href="shop.php" class="btn btn-primary me-2">Continuer mes achats</a>
            <a href="mon_compte.php?action=details_commande&id=<?php echo (int) $_GET['id']; ?>"
                class="btn btn-secondary">Voir les détails de ma commande</a>
        </div>

    <?php } else { ?>
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>

        <?php if ($message_flash_erreur != null) {
            // La chaîne contient déjà des <br>, on ne la purifie pas avec htmlspecialchars
            // car cela annulerait l'effet des sauts de ligne. La purification a déjà été
            // faite sur chaque message d'erreur individuellement avant de les joindre.
            ?>
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
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                <?php
                                // Le formatage de nombre n'est pas enseigné. On fait un calcul simple et on affiche.
                                $prix_unitaire_ttc = $item['unit_ht'] * (1 + 0.20);
                                $sous_total_ligne = $prix_unitaire_ttc * $item['quantity'];
                                ?>
                                <td class="text-end"><?php echo number_format($sous_total_ligne); ?> €</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <th colspan="2" class="text-end">Total à payer :</th>
                            <th class="text-end"><?php echo number_format($recapitulatif_panier['total_ttc']); ?> €</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="col-md-5">
                <h2>Validation et Paiement</h2>
                <div class="card p-3 shadow-sm">
                    <form method="POST" action="checkout.php">
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
                            <div class="row">
                                <div class="col-7"><input type="text" name="card_expiry" class="form-control"
                                        placeholder="Date d'expiration (MM/AA)" required></div>
                                <div class="col-5"><input type="text" name="card_cvc" class="form-control" placeholder="CVC"
                                        required></div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mt-3" <?php if (count($adresses) == 0)
                            echo 'disabled'; ?>>
                            Payer <?php echo number_format($recapitulatif_panier['total_ttc']); // https://www.php.net/manual/fr/function.number-format.php?> €
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>
</main>

<?php require('partials/footer.php'); ?>