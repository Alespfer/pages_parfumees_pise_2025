<?php
/*
 * Fichier : auth.php
 * Rôle : Contrôleur central pour l'authentification des utilisateurs (clients).
 * Ce fichier est le point d'entrée unique pour tout ce qui concerne le compte utilisateur :
 *  - Connexion (login)
 *  - Inscription (register)
 *  - Déconnexion (logout)
 *  - Procédure de mot de passe oublié (forgot/reset)
 * Il utilise une structure de type "switch" pour aiguiller l'utilisateur vers la bonne action.
 */

session_start();

// Inclusion des dépendances
require('parametrage/param.php');
require('fonction/fonctions.php');

// --- INITIALISATION DES VARIABLES ---
// On prépare les variables qui seront utilisées dans les différentes vues.
$action = isset($_GET['action']) ? $_GET['action'] : 'login';
$pageTitle = '';
$message_succes = '';
$erreurs = array();
$pdo = getPDO();


// --- ROUTEUR PRINCIPAL ---
switch ($action) {
    case 'logout':
        // Action de déconnexion : on détruit la session.
        $_SESSION = array();
        session_destroy();
        // On redirige vers la page de connexion avec un message de succès.
        header('Location: auth.php?action=login&status=logout');
        exit();

    case 'register':
        // Action d'inscription.
        $pageTitle = "Créer un compte";
        // Si l'utilisateur est déjà connecté, on le redirige vers son compte.

        if (isset($_SESSION['user'])) {
            header('Location: mon_compte.php');
            exit();
        }

        // On initialise les variables pour pré-remplir le formulaire en cas d'erreur.

        $nom = '';
        $prenom = '';
        $email = '';

        // On vérifie si le formulaire a été soumis.
        if (isset($_POST['email'])) {
            $nom = trim(isset($_POST['nom']) ? $_POST['nom'] : '');
            $prenom = trim(isset($_POST['prenom']) ? $_POST['prenom'] : '');
            $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
            $mot_de_passe = isset($_POST['password']) ? $_POST['password'] : '';
            $mot_de_passe_confirmation = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

            // Validation des champs (non-vides).

            if ($nom == '') {
                $erreurs[] = "Le nom est requis.";
            }
            if ($prenom == '') {
                $erreurs[] = "Le prénom est requis.";
            }
            if ($email == '') {
                $erreurs[] = "L'adresse e-mail est requise.";
            }

            // Validation de la correspondance et de la force du mot de passe.

            if ($mot_de_passe != $mot_de_passe_confirmation) {
                $erreurs[] = "Les mots de passe ne correspondent pas.";
            } else {
                $erreurs_mdp = isPasswordStrong($mot_de_passe);
                if (count($erreurs_mdp) > 0) {
                    foreach ($erreurs_mdp as $e) {
                        $erreurs[] = $e;
                    }
                }
            }

            if (count($erreurs) == 0) {
                if (registerUser($nom, $prenom, $email, $mot_de_passe)) {
                    header('Location: auth.php?action=login&status=registered');
                    exit();
                } else {
                    $erreurs[] = "Un compte existe déjà avec cette adresse e-mail.";
                }
            }
        }
        break;

    case 'forgot':
    // Action pour la demande de réinitialisation de mot de passe.
        $pageTitle = "Mot de passe oublié";
        $jeton_genere = null; // Variable pour afficher le lien de test dans la vue.
        /*
        On affiche un message générique pour ne pas révéler si un e-mail existe ou non dans la BDD. $message_succes = "Si un compte est associé à cette adresse, des instructions ont été envoyées.";
        En réalité, il faudrait envoyer un vrai e-mail ici. Pour le test, on se contente de générer le jeton et de l'afficher.
         */
        if (isset($_POST['email'])) {
            $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
            if ($email != '') {
                $message_succes = "Si un compte est associé à cette adresse, des instructions ont été envoyées.";

                $jeton_temporaire = generateAndSaveResetToken($email);
                if ($jeton_temporaire != null) {
                    $jeton_genere = $jeton_temporaire;
                }
            } else {
                $erreurs[] = "Veuillez entrer une adresse e-mail.";
            }
        }
        break;

    case 'reset':
        // Action pour la réinitialisation effective du mot de passe.
        $pageTitle = "Nouveau mot de passe";
        $jeton = isset($_GET['token']) ? $_GET['token'] : '';
        $utilisateur = getClientByResetToken($jeton);
        $jeton_valide = $utilisateur ? true : false;

        if (!$jeton_valide && !isset($_POST['password'])) {
            $erreurs[] = "Ce lien de réinitialisation est invalide ou a expiré.";
        }

        // Si le jeton est valide ET que le formulaire de nouveau mot de passe est soumis.

        if ($jeton_valide && isset($_POST['password'])) {
            $pwd1 = isset($_POST['password']) ? $_POST['password'] : '';
            $pwd2 = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

            // Validation de la correspondance et de la force du nouveau mot de passe.

            if ($pwd1 != $pwd2) {
                $erreurs[] = "Les mots de passe ne correspondent pas.";
            } else {
                $erreurs_mdp = isPasswordStrong($pwd1);
                if (count($erreurs_mdp) > 0) {
                    foreach ($erreurs_mdp as $e) {
                        $erreurs[] = $e;
                    }
                }
            }

            if (count($erreurs) == 0) {
                if (updatePasswordAndClearToken($utilisateur['id_client'], $pwd1)) {
                    $message_succes = "Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.";
                    $jeton_valide = false;
                } else {
                    $erreurs[] = "Une erreur technique est survenue lors de la mise à jour.";
                }
            }
        }
        break;

    case 'login':
    default:
        $pageTitle = "Connexion";
        if (isset($_SESSION['user'])) {
            header('Location: mon_compte.php');
            exit();
        }

        // Affichage des messages de succès en fonction du paramètre 'status' dans l'URL.

        $status = isset($_GET['status']) ? $_GET['status'] : '';
        if ($status == 'registered') {
            $message_succes = "Compte créé avec succès !";
        }
        if ($status == 'logout') {
            $message_succes = "Vous avez été déconnecté avec succès.";
        }

        if (isset($_POST['email'])) {
            $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
            $mot_de_passe = isset($_POST['password']) ? $_POST['password'] : '';
            $utilisateur = loginUser($email, $mot_de_passe);

            if ($utilisateur) {
                $_SESSION['user'] = $utilisateur;

                // --- SÉCURISATION DE LA REDIRECTION ---
                // On récupère l'URL de redirection si elle existe, sinon on va sur 'mon_compte.php'.

                $url_redirection = isset($_GET['redirect']) ? $_GET['redirect'] : 'mon_compte.php';

                $est_externe = false;
                if (isset($url_redirection[5])) {
                    if ($url_redirection[0] == 'h' && $url_redirection[1] == 't' && $url_redirection[2] == 't' && $url_redirection[3] == 'p') {
                        $est_externe = true;
                    }
                }
                if (isset($url_redirection[1])) {
                    if ($url_redirection[0] == '/' && $url_redirection[1] == '/') {
                        $est_externe = true;
                    }
                }

                if ($est_externe) {
                    $url_redirection = 'mon_compte.php';
                }

                header('Location: ' . $url_redirection);
                exit();
            } else {
                $erreurs[] = "L'adresse e-mail ou le mot de passe est incorrect.";
            }
        }
        break;
}

require('partials/header.php');
?>
<!-- =========================================================================
     AFFICHAGE HTML
========================================================================= -->
<main class="container my-4" style="max-width: 500px;">
    <div class="card shadow-sm">
        <div class="card-body p-4 p-md-5">

            <?php if ($message_succes != '') { ?>
                <div class="alert alert-success">
                    <?php
                    echo htmlspecialchars($message_succes);

                    if (isset($jeton_genere) && $jeton_genere != null) {
                        $lien = "auth.php?action=reset&token=" . $jeton_genere;
                        ?>
                        <br>
                        <small>
                            <strong>Lien de test :</strong>
                            <a href="<?php echo htmlspecialchars($lien); ?>">Réinitialiser mon mot de passe</a>
                        </small>
                        <?php
                    }
                    ?>
                </div>
            <?php } ?>
            <?php if (count($erreurs) > 0) { ?>
                <div class="alert alert-danger">
                    <?php
                    // On purifie chaque message d'erreur avant de les joindre.
                    $erreurs_purifiees = array();
                    foreach ($erreurs as $erreur) {
                        $erreurs_purifiees[] = htmlspecialchars($erreur);
                    }
                    echo implode('<br>', $erreurs_purifiees);
                    ?>
                </div>
            <?php } ?>

            <!-- Formulaire de connexion -->
            <?php if ($action == 'login' || ($action != 'register' && $action != 'forgot' && $action != 'reset')) { ?>
                <h1 class="card-title text-center mb-4">Connexion</h1>
                <form method="POST"
                    action="auth.php?action=login<?php if (isset($_GET['redirect'])) {
                        echo '&redirect=' . htmlspecialchars($_GET['redirect']);
                    } ?>">
                    <div class="mb-3"><label for="email" class="form-label">Adresse e-mail</label><input type="email"
                            id="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label for="password" class="form-label">Mot de passe</label><input type="password"
                            id="password" name="password" class="form-control" required></div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
                <div class="text-center mt-3"><a href="auth.php?action=forgot">Mot de passe oublié ?</a></div>
                <hr>
                <p class="text-center mb-0">Pas encore de compte ? <a href="auth.php?action=register">Inscrivez-vous</a></p>
          
            <!-- Formulaire d'inscription -->

            <?php } elseif ($action == 'register') { ?>
                <h1 class="card-title text-center mb-4">Créer un compte</h1>
                <form method="POST" action="auth.php?action=register">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="nom" class="form-label">Nom</label><input type="text"
                                id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($nom); ?>"
                                required></div>
                        <div class="col-md-6 mb-3"><label for="prenom" class="form-label">Prénom</label><input type="text"
                                id="prenom" name="prenom" class="form-control"
                                value="<?php echo htmlspecialchars($prenom); ?>" required></div>
                    </div>
                    <div class="mb-3"><label for="email" class="form-label">Adresse e-mail</label><input type="email"
                            id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>"
                            required></div>
                    <div class="mb-3"><label for="password" class="form-label">Mot de passe</label><input type="password"
                            id="password" name="password" class="form-control" required><small
                            class="form-text text-muted">8 car. min, 1 majuscule, 1 minuscule, 1 chiffre, 1 spécial.</small>
                    </div>
                    <div class="mb-3"><label for="password_confirm" class="form-label">Confirmer</label><input
                            type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">S'inscrire</button>
                </form>
                <hr>
                <p class="text-center mb-0">Déjà un compte ? <a href="auth.php?action=login">Connectez-vous</a></p>
            
            <!-- Formulaire de demande de réinitialisation -->

            <?php } elseif ($action == 'forgot') { ?>
                <h1 class="card-title text-center mb-4">Mot de passe oublié</h1>
                <?php if ($message_succes == '') { ?>
                    <p class="text-center text-muted">Entrez votre e-mail pour recevoir les instructions.</p>
                    <form method="POST" action="auth.php?action=forgot">
                        <div class="mb-3"><label for="email" class="form-label">Adresse e-mail</label><input type="email"
                                name="email" id="email" class="form-control" required></div>
                        <button type="submit" class="btn btn-primary w-100">Envoyer</button>
                    </form>
                <?php } ?>
                <hr>
                <p class="text-center mb-0"><a href="auth.php?action=login">Retour à la connexion</a></p>
            
            <!-- Formulaire de saisie du nouveau mot de passe -->

            <?php } elseif ($action == 'reset') { ?>
                <h1 class="card-title text-center mb-4">Nouveau mot de passe</h1>
                <?php if ($jeton_valide) { 
                    // On n'affiche le formulaire que si le jeton est valide. ?>
                    <form method="POST" action="auth.php?action=reset&token=<?php echo htmlspecialchars($jeton); ?>">
                        <div class="mb-3"><label for="password" class="form-label">Nouveau mot de passe</label><input
                                type="password" name="password" id="password" class="form-control" required></div>
                        <div class="mb-3"><label for="password_confirm" class="form-label">Confirmer</label><input
                                type="password" name="password_confirm" id="password_confirm" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Réinitialiser</button>
                    </form>
                <?php } else { ?>
                    <hr>
                    <p class="text-center mb-0"><a href="auth.php?action=login">Retour à la connexion</a></p>
                <?php } ?>
            <?php } ?>

        </div>
    </div>
</main>
<?php require('partials/footer.php'); ?>