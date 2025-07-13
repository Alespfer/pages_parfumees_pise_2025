<?php
/*
 * Fichier : admin_login.php
 * Rôle : Contrôleur et Vue de la page de connexion à l'espace d'administration.
 * Ce fichier gère l'authentification des administrateurs et affiche le formulaire de connexion.
 */

session_start();

// Inclusion des fichiers de configuration et de fonctions
require('parametrage/param.php');
require('fonction/fonctions.php');

// --- GARDE-FOU DE SÉCURITÉ ---
// Si un administrateur est déjà identifié dans la session, il ne doit pas pouvoir
// accéder à nouveau à la page de connexion. Il est donc redirigé vers le tableau de bord.

if (isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}

$errorMessage = '';


// --- TRAITEMENT DU FORMULAIRE DE CONNEXION ---
// On vérifie si le formulaire a été soumis en testant l'existence des champs attendus.
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = purifier_trim($_POST['email']);
    $password = purifier_trim($_POST['password']);

    // 2. Tentative de connexion via la fonction dédiée.

    $adminData = loginAdmin($email, $password);

    // 3. Routage en fonction du résultat de la connexion.

    if ($adminData) {
        $_SESSION['admin'] = array(
            'id_admin' => $adminData['id_admin'],
            'email' => $adminData['email'],
            'role' => $adminData['role']
        );
        header('Location: admin.php');
        exit();
    } else {
        $errorMessage = 'Les identifiants fournis sont incorrects.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background-color: #f8f9fa; }
        .login-card { width: 100%; max-width: 400px; padding: 1.5rem; }
    </style>
</head>
<body>
<div class="card login-card shadow-sm">
    <div class="card-body">
        <h3 class="card-title text-center mb-4">L'Atelier des Mots & Lumières</h3>
        <h5 class="card-subtitle mb-4 text-muted text-center">Espace Administration</h5>

        <form method="POST" action="admin_login.php">
            <div class="mb-3">
                <label for="email" class="form-label">Adresse Email</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <?php
            if ($errorMessage != '') {
            ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php
            }
            ?>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Connexion</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>