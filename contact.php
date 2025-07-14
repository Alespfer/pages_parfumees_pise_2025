<?php

/*
 * Fichier : contact.php
 * Rôle : Contrôleur et Vue de la page de contact.
 * Ce script gère :
 *  - L'affichage du formulaire de contact.
 *  - Le pré-remplissage des champs si l'utilisateur est connecté.
 *  - La validation et le traitement des données envoyées.
 *  - Une protection simple contre les soumissions multiples et les bots.
 */


session_start();
require_once 'parametrage/param.php';
require_once 'fonction/fonctions.php';

$activePage = 'contact'; 


require_once 'partials/header.php';

/***************************************************************************
 * SECTION 1 : INITIALISATION ET PRÉPARATION DES DONNÉES                                             
 ***************************************************************************/

// On détermine la vue à afficher : 'form' pour le formulaire (par défaut), 'faq' pour la FAQ.
$view = isset($_GET['view']) ? $_GET['view'] : 'form';
// On initialise toutes les variables dont on aura besoin pour éviter les erreurs "undefined".
$erreurs = [];
$nom = '';
$email = '';
$sujet = '';
$message = '';
$est_connecte = false;


// On définit le titre de la page en fonction de la vue
if ($view === 'faq') {
    $pageTitle = "Foire Aux Questions";
    // On prépare les données pour la FAQ. Idéalement, ça viendrait d'une BDD.
    $faq_sections = [
        'Produits' => [
            'Quels types de livres vendez-vous ?' => 'Nous vendons des livres d’occasion soigneusement sélectionnés : romans, essais, poésie, livres jeunesse… Tous sont en bon état, avec parfois une touche vintage charmante.',
            'Que contiennent vos coffrets ?' => 'Nos coffrets combinent un livre, une bougie parfumée assortie, et parfois une surprise littéraire. Chaque coffret est préparé avec soin, prêt à offrir ou à s’offrir.'
        ],
        'Livraison' => [
            'Quels sont les délais de livraison ?' => 'Nous expédions sous 2 à 4 jours ouvrés. Les délais de livraison varient ensuite selon votre lieu de résidence, entre 2 à 5 jours pour la France métropolitaine.',
            'Livrez-vous à l’international ?' => 'Oui, nous livrons dans plusieurs pays d’Europe. Les frais et délais varient selon la destination. Vous verrez les options disponibles lors du passage en caisse.'
        ],
        'Retours et remboursement' => [
            'Puis-je retourner un produit ?' => 'Oui. Vous disposez de 14 jours après réception pour nous retourner un produit non utilisé et dans son emballage d’origine.',
            'Combien de temps faut-il pour être remboursé ?' => 'Une fois le retour reçu et vérifié, le remboursement est effectué sous 5 à 7 jours ouvrés sur votre mode de paiement initial.'
        ]
    ];
} else {
    $pageTitle = "Nous Contacter";
    // On vérifie si un utilisateur est connecté pour pré-remplir les champs.
    if (isset($_SESSION['user']) && isset($_SESSION['user']['id_client'])) {
        $est_connecte = true;
        $nom = $_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom'];
        $email = $_SESSION['user']['email'];
    }
}


/***************************************************************************
 * SECTION 2 : TRAITEMENT FORMULAIRE                                       *
 ***************************************************************************/
if (isset($_POST['submit_contact']) && !isset($_SESSION['contact_envoye'])) {

    // Protection anti-bot. S'il est rempli, on considère que ce n'est pas un utilisateur humain et on stoppe l'exécution.

    if (isset($_POST['website']) && $_POST['website'] !== '') {
        exit(); // Fin du script.
    }

    // Si l'utilisateur n'est pas connecté, on récupère les données des champs nom/email.

    if (!$est_connecte) {
        if (isset($_POST['name'])) {
            $nom = trim($_POST['name']);
        }
        if (isset($_POST['email'])) {
            $email = trim($_POST['email']);
        }
    }


    // On récupère les autres champs du formulaire.

    if (isset($_POST['subject'])) {
        $sujet = trim($_POST['subject']);
    }
    if (isset($_POST['message'])) {
        $message = trim($_POST['message']);
    }

    // --- Phase de validation des données ---

    if ($nom === '') {
        $erreurs[] = 'Le champ « Nom » est obligatoire.';
    }
    if (!validate_email($email)) {
        $erreurs[] = 'L\'adresse email fournie n\'est pas valide.';
    }
    if ($sujet === '') {
        $erreurs[] = 'Le champ « Sujet » est obligatoire.';
    }
    if ($message === '') {
        $erreurs[] = 'Le champ « Message » est obligatoire.';
    }


    // Si le tableau d'erreurs est vide, tout est bon.

    if (count($erreurs) === 0) {
        $id_client = $est_connecte ? $_SESSION['user']['id_client'] : null;
        $enregistrement = saveContactMessage($nom, $email, $sujet, $message, $id_client);
        if ($enregistrement) {
            $_SESSION['contact_envoye'] = true;
            header('Location: contact.php');
            exit();
        } else {
            $erreurs[] = "Une erreur technique est survenue. Veuillez réessayer.";
        }
    }
}

?>

<!-- =========================================================================
     SECTION 3 : AFFICHAGE HTML (LA VUE)
========================================================================= -->

<?php if ($view === 'faq') { ?>
    <!-- ================== VUE FAQ ================== -->
    <div class="text-center mb-5">
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
        <p class="lead">Un espace pour répondre à toutes vos questions.</p>
    </div>

    <div class="faq-container">
        <?php foreach ($faq_sections as $section_title => $questions) { ?>
            <h2 class="faq-section-title"><?php echo htmlspecialchars($section_title); ?></h2>
            <div class="accordion">
                <?php foreach ($questions as $question => $answer) { ?>
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <span><?php echo htmlspecialchars($question); ?></span>
                            <span class="accordion-icon">+</span>
                        </button>
                        <div class="accordion-content">
                            <p><?php echo htmlspecialchars($answer); ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php }  ?>
    </div>

<?php } else { ?>
    <!-- ================== VUE FORMULAIRE DE CONTACT ================== -->
    <div class="contact-container">
        <?php
        // On vérifie la variable $_SESSION['contact_envoye'] qui est définie lors d'un envoi réussi.
        if (isset($_SESSION['contact_envoye'])) { ?>
            <!-- Vue de succès : affichée après redirection. -->
            <div class="contact-success text-center">
                <h2>Merci pour votre message !</h2>
                <p>Nous reviendrons vers vous dans les plus brefs délais.</p>
                <a href="shop.php" class="btn btn-primary mt-3">Retour à la boutique</a>
            </div>
            <?php  ?>
        <?php } else { ?>
            <!-- Vue du formulaire : affichée par défaut ou en cas d'erreur. -->
            <h1 class="text-center"><?php echo htmlspecialchars($pageTitle); ?></h1>

            <?php if (count($erreurs) > 0) { ?>
                <div class="alert alert-danger">
                    <strong>Veuillez corriger les erreurs suivantes :</strong>
                    <ul>
                        <?php foreach ($erreurs as $e) { ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php }  ?>
                    </ul>
                </div>
            <?php }  ?>

            <?php if ($est_connecte) { ?>
                <div class="alert alert-info text-center">
                    Bonjour <strong><?php echo htmlspecialchars($_SESSION['user']['prenom']); ?></strong>, nous sommes à votre
                    écoute.
                </div>
            <?php } ?>

            <form action="contact.php" method="POST" class="contact-form">
                <?php if (!$est_connecte) { ?>
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Votre nom</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($nom); ?>"
                            required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Votre email</label>
                        <input type="email" id="email" name="email" class="form-control"
                            value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                <?php }  ?>

                <div class="form-group mb-3">
                    <label for="subject" class="form-label">Sujet</label>
                    <input type="text" id="subject" name="subject" class="form-control"
                        value="<?php echo htmlspecialchars($sujet); ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="message" class="form-label">Votre message</label>
                    <textarea id="message" name="message" class="form-control" rows="6"
                        required><?php echo htmlspecialchars($message); ?></textarea>
                </div>

                <div style="display:none;" aria-hidden="true">
                    <label for="website">Ne pas remplir</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <button type="submit" class="btn btn-primary w-100" name="submit_contact">Envoyer le message</button>
            </form>
        <?php }  ?>
    </div>
<?php } ?>
</main>

<?php
require_once 'partials/footer.php';
?>