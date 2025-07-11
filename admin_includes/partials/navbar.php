<!-- Fichier : admin_includes/partials/navbar.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php?p=dashboard">Admin - L'Atelier</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link <?= ($p === 'dashboard') ? 'active' : '' ?>" href="admin.php?p=dashboard">Tableau de bord</a></li>
                <li class="nav-item"><a class="nav-link <?= ($p === 'produits') ? 'active' : '' ?>" href="admin.php?p=produits">Produits</a></li>
                <li class="nav-item"><a class="nav-link <?= ($p === 'commandes') ? 'active' : '' ?>" href="admin.php?p=commandes">Commandes</a></li>
                <li class="nav-item"><a class="nav-link <?= ($p === 'utilisateurs') ? 'active' : '' ?>" href="admin.php?p=utilisateurs">Utilisateurs</a></li>
                <li class="nav-item"><a class="nav-link <?= ($p === 'messages') ? 'active' : '' ?>" href="admin.php?p=messages">Messages</a></li>
            </ul>
            <span class="navbar-text me-3">Rôle : <?= htmlspecialchars($_SESSION['admin']['role']) ?></span>
            <a href="admin_logout.php" class="btn btn-outline-light">Déconnexion</a>
        </div>
    </div>
</nav>