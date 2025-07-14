<?php
// /parametrage/param.php

// -- Constantes pour la connexion à la base de données --
define('DB_HOST', 'localhost');
define('DB_NAME', 'pise_2025');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// -- Constantes globales du site --
define('SITE_URL', 'http://localhost/projet3'); 
define('SITE_NAME', 'Les Pages Parfumées');
define('PRODUITS_PAR_PAGE', 10);
define('PASSWORD_ALGO', PASSWORD_DEFAULT);



date_default_timezone_set('Europe/Paris');

// -- Gestionnaire de session centralisé --
// S'assure que la session est démarrée une seule et unique fois.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}