

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php?p=dashboard">Admin - L'Atelier</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link <?php echo ($page == 'dashboard' ? 'active' : ''); ?>" href="admin.php?p=dashboard">Tableau de bord</a></li>
                <li class="nav-item"><a class="nav-link <?php echo (in_array($page, ['produits', 'produit_form']) ? 'active' : ''); ?>" href="admin.php?p=produits">Produits</a></li>
                <li class="nav-item"><a class="nav-link <?php echo (in_array($page, ['commandes', 'commande_details']) ? 'active' : ''); ?>" href="admin.php?p=commandes">Commandes</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($page == 'utilisateurs' ? 'active' : ''); ?>" href="admin.php?p=utilisateurs">Utilisateurs</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($page == 'messages' ? 'active' : ''); ?>" href="admin.php?p=messages">Messages</a></li>
                <li class="nav-item"><a class="nav-link <?php echo (in_array($page, ['retours', 'retour_details']) ? 'active' : ''); ?>" href="admin.php?p=retours">Retours</a></li>
                
                <!-- PURIFICATION FINALE : Ajout de la logique 'active' au lien du dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php
                        // La classe 'active' est ajoutée si la page actuelle est l'une des pages de gestion des données annexes.
                        $data_pages = array('auteurs', 'auteur_form', 'tags', 'tag_form', 'editeurs', 'editeur_form');
                        if (in_array($page, $data_pages)) {
                            echo 'active';
                        }
                    ?>" href="#" id="dataDropdown" role="button" data-bs-toggle="dropdown">
                        Données Annexes
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="admin.php?p=auteurs">Auteurs</a></li>
                        <li><a class="dropdown-item" href="admin.php?p=editeurs">Éditeurs</a></li>
                        <li><a class="dropdown-item" href="admin.php?p=tags">Tags</a></li>
                    </ul>
                </li>
            </ul>

            <span class="navbar-text me-3">Rôle: <?php echo htmlspecialchars($_SESSION['admin']['role']); ?></span>
            <a href="admin.php?action=logout" class="btn btn-outline-light">Déconnexion</a>
        </div>
    </div>
</nav>