<?php
/**
 * Page de contact d'un utilisateur : 
 * - Affiche un formulaire ou un message de remerciement.
 * - Pré‑remplit nom / email si l’utilisateur est connecté.
 */

// /contact.php

require_once 'parametrage/param.php';
require_once 'fonction/fonctions.php';
require_once 'partials/header.php';

/***************************************************************************
 * SECTION 1 : INITIALISATION                                              *
 ***************************************************************************/

$errors          = [];
$name            = '';
$email           = '';
$subject         = '';
$messageContent  = '';
$isConnected     = false;

// Pré‑remplissage si connecté
if (isset($_SESSION['user']) && isset($_SESSION['user']['id_client'])) {
    $isConnected = true;
    $name   = $_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom'];
    $email  = $_SESSION['user']['email'];
}

/***************************************************************************
 * SECTION 2 : TRAITEMENT FORMULAIRE                                       *
 ***************************************************************************/

// Soumission + pas déjà envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_SESSION['contact_sent'])) {
        // On ignore toute nouvelle soumission si un message est déjà envoyé
        header('Location: contact.php');
        exit();
    }

    /* Protection anti‑bot (honeypot) */
    if (isset($_POST['website']) && $_POST['website'] !== '') {
        exit(); // Arrêt silencieux
    }

    /* Récupération / nettoyage des champs */
    if (!$isConnected) {
        if (isset($_POST['name']))   { $name   = trim($_POST['name']); }
        if (isset($_POST['email']))  { $email  = trim($_POST['email']); }
    }
    if (isset($_POST['subject']))   { $subject        = trim($_POST['subject']); }
    if (isset($_POST['message']))   { $messageContent = trim($_POST['message']); }

    /* Validation – règles simples sans empty() ni filter_var() */
    if ($name === '') {
        $errors[] = 'Le champ « Nom » est obligatoire.';
    }

    if (!preg_match('#^[\w\.-]+@[\w\.-]+\.[A-Za-z]{2,}$#', $email)) {
        $errors[] = 'Adresse email non valide.';
    }

    if ($subject === '') {
        $errors[] = 'Le champ « Sujet » est obligatoire.';
    }

    if ($messageContent === '') {
        $errors[] = 'Le champ « Message » est obligatoire.';
    }

    /* Si tout est bon → sauvegarde */
    if (count($errors) === 0) {
        $clientId = null;
        if ($isConnected) {
            $clientId = $_SESSION['user']['id_client'];
        }

        $saved = saveContactMessage($name, $email, $subject, $messageContent, $clientId);

        if ($saved) {
            $_SESSION['contact_sent'] = true;
            header('Location: contact.php');
            exit();
        }

        // Erreur technique
        $errors[] = 'Une erreur technique est survenue. Veuillez réessayer.';
    }
}
?>

<main class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4">Nous Contacter</h1>

                    <?php if (isset($_SESSION['contact_sent'])) { ?>
                        <!-- Message de remerciement -->
                        <div class="alert alert-success text-center">
                            <h2>Merci pour votre message !</h2>
                            <p>Nous reviendrons vers vous dans les plus brefs délais.</p>
                            <a href="shop.php" class="btn btn-primary mt-3">Retour au catalogue</a>
                        </div>
                    <?php } else { ?>
                        <!-- Formulaire -->

                        <?php if (count($errors) > 0) { ?>
                            <div class="alert alert-danger">
                                <strong>Veuillez corriger les erreurs suivantes :</strong>
                                <ul>
                                    <?php foreach ($errors as $e) { ?>
                                        <li><?php echo htmlspecialchars($e); ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>

                        <form action="contact.php" method="POST">

                            <?php if ($isConnected) { ?>
                                <!-- Utilisateur connecté -->
                                <div class="alert alert-info">
                                    Bonjour <strong><?php echo htmlspecialchars($name); ?></strong><br>
                                    Ravis de vous retrouver ! Nous sommes à votre écoute pour toute question ou suggestion.

                                </div>
                                <input type="hidden" name="name"  value="<?php echo htmlspecialchars($name); ?>">
                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                            <?php } else { ?>
                                <!-- Visiteur -->
                                <div class="form-group mb-3">
                                    <label for="name">Votre nom</label>
                                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="email">Votre email</label>
                                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                            <?php } ?>

                            <div class="form-group mb-3">
                                <label for="subject">Sujet</label>
                                <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($subject); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="message">Votre message</label>
                                <textarea id="message" name="message" class="form-control" rows="5" required><?php echo htmlspecialchars($messageContent); ?></textarea>
                            </div>

                            <!-- Honeypot -->
                            <div style="display:none;" aria-hidden="true">
                                <label for="website">Ne pas remplir ce champ</label>
                                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Envoyer le message</button>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once 'partials/footer.php';
