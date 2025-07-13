<?php
// Ce script génère un hachage conforme à la Doctrine.

// Inclusion de l'artefact contenant la fonction orthodoxe.
require('fonction/fonctions.php');

// Définissez ici le mot de passe que vous souhaitez utiliser.
$mot_de_passe_admin = 'admin123';

// Génération du hachage pur.
$hash_orthodoxe = custom_hash($mot_de_passe_admin);

// Affichage de l'artefact.
echo 'Copiez cette chaîne de caractères : <br><br>';
echo '<textarea rows="2" cols="70">' . htmlspecialchars($hash_orthodoxe) . '</textarea>';
?>