<?php
// Démarrage de la session
session_start();

// Inclusion des fichiers de configuration et de fonctions
// La syntaxe "require()" est enseignée (p.55), "require_once" ne l'est pas.
// Les chemins relatifs simples sont la norme du cours, "__DIR__" n'est pas enseigné.
require('parametrage/param.php');
require('fonction/fonctions.php');

// Si l'administrateur est déjà connecté, redirection.
if (isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}

$errorMessage = '';

// La méthode enseignée pour détecter une soumission est de vérifier si les données
// du formulaire existent avec "isset()" sur les variables $_POST.
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // L'appel de fonction est une syntaxe de base enseignée.
    $adminData = loginAdmin($email, $password);

    if ($adminData) {
        // L'affectation d'un tableau à une variable de session est valide.
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
    <!-- Le cours ne concerne pas le HTML ou CSS, ces éléments sont conservés pour la fonctionnalité visuelle. -->
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
            // La syntaxe "if(): ... endif;" n'est pas enseignée. Seule la syntaxe avec accolades "{}" l'est.
            // La conversion implicite d'une chaîne en booléen n'est pas enseignée. Une comparaison explicite est requise.
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