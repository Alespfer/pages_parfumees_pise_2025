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

require_once 'parametrage/param.php';
require_once 'fonction/fonctions.php';

require_once 'partials/header.php';

/***************************************************************************
 * SECTION 1 : INITIALISATION ET PRÉPARATION DES DONNÉES                                             *
 ***************************************************************************/
// On initialise toutes les variables dont on aura besoin pour éviter les erreurs "undefined".
$erreurs = [];
$nom = '';
$email = '';
$sujet = '';
$message = '';
$est_connecte = false;

// On vérifie si un utilisateur est connecté en inspectant la session.

if (isset($_SESSION['user']) && isset($_SESSION['user']['id_client'])) {
    $est_connecte = true;
    $nom = $_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom'];
    $email = $_SESSION['user']['email'];
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
            $nom = purifier_trim($_POST['name']);
        }
        if (isset($_POST['email'])) {
            $email = purifier_trim($_POST['email']);
        }
    }


    // On récupère les autres champs du formulaire.

    if (isset($_POST['subject'])) {
        $sujet = purifier_trim($_POST['subject']);
    }
    if (isset($_POST['message'])) {
        $message = purifier_trim($_POST['message']);
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

<div class="contact-container">
    <?php if (isset($_SESSION['contact_envoye'])) { ?>
        <!-- Vue de succès : affichée si le message a bien été envoyé. -->
        <div class="contact-success">
            <h2>Merci pour votre message !</h2>
            <p>Nous reviendrons vers vous dans les plus brefs délais.</p>
            <a href="shop.php" class="btn btn-primary">Retour à la boutique</a>
        </div>
    <?php } else { ?>
        <!-- Vue du formulaire : affichée par défaut ou en cas d'erreur. -->

        <h1>Nous Contacter</h1>

        <?php if (count($erreurs) > 0) { ?>
            <div class="contact-errors">
                <strong>Veuillez corriger les erreurs suivantes :</strong>
                <ul>
                    <?php foreach ($erreurs as $e) { ?>
                        <li><?php echo htmlspecialchars($e); ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <?php if ($est_connecte) { ?>
            <!-- Message de bienvenue si l'utilisateur est connecté. -->
            <div class="contact-greeting">
                Bonjour <strong><?php echo htmlspecialchars($nom); ?></strong>, nous sommes à votre écoute.
            </div>
        <?php } ?>

        <form action="contact.php" method="POST" class="contact-form">
            <?php if (!$est_connecte) { ?>
                <!-- Si l'utilisateur n'est pas connecté, on affiche les champs nom et email. -->
                <!-- S'il est connecté, on passe son nom et email en champs cachés. -->
                <div class="form-group">
                    <label for="name">Votre nom</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($nom); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Votre email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
            <?php } else { ?>
                <input type="hidden" name="name" value="<?php echo htmlspecialchars($nom); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <?php } ?>


            <div class="form-group">
                <label for="subject">Sujet</label>
                <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($sujet); ?>" required>
            </div>
            <div class="form-group">
                <label for="message">Votre message</label>
                <textarea id="message" name="message" rows="6" required><?php echo htmlspecialchars($message); ?></textarea>
            </div>
            <!-- Champ "Honeypot" anti-bot. Il est caché aux humains. -->
            <div style="display:none;" aria-hidden="true">
                <label for="website">Ne pas remplir</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <button type="submit" class="btn btn-primary">Envoyer le message</button>
        </form>
    <?php } ?>
</div>

<?php
require_once 'partials/footer.php';
