<?php
// /parametrage/param.php

// -- Constantes pour la connexion à la base de données --
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// -- Constantes globales du site --
define('SITE_URL', 'http://localhost/projet2'); // MODIFIEZ CECI
define('SITE_NAME', 'L\'Atelier des Mots & Lumières');
define('PRODUITS_PAR_PAGE', 10);
define('PASSWORD_ALGO', PASSWORD_DEFAULT);

// -- Configuration PHP pour le développement --
// Mettre 'Off' en production
ini_set('display_errors', 'On');
error_reporting(E_ALL);
date_default_timezone_set('Europe/Paris');

// -- Gestionnaire de session centralisé --
// S'assure que la session est démarrée une seule et unique fois.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}