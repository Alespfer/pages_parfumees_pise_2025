<!-- Language Navigation -->
<div align="right">
  <a href="./README.md">English</a> | <b><a href="./README_fr.md">Français</a></b> | <a href="./README_es.md">Español</a>
</div>

# Les Pages Parfumées - Un Site E-commerce en PHP

[![License: MIT](https://img.shields.io/badge/Licence-MIT-blue.svg)](https://opensource.org/licenses/MIT)
![Language](https://img.shields.io/badge/Langage-PHP-8892BF)
![Database](https://img.shields.io/badge/Base_de_données-MySQL-4479A1)
![Tech](https://img.shields.io/badge/Technologie-Docker-2496ED)

"Les Pages Parfumées" est un site e-commerce entièrement fonctionnel, développé de A à Z dans le cadre d'un projet universitaire. Il simule une boutique en ligne fictive vendant des livres d'occasion, des bougies artisanales et des coffrets cadeaux. L'application est développée en **PHP natif**, suivant une structure modulaire, et propose deux options d'installation : un environnement de serveur local traditionnel (WampServer, MAMP) ou une configuration conteneurisée avec **Docker**.

![Capture d'écran de la page d'accueil](img/homepage.png)

## Table des matières

- [À propos du projet](#à-propos-du-projet)
- [Fonctionnalités principales](#fonctionnalités-principales)
- [Stack technique](#stack-technique)
- [Démarrage rapide](#démarrage-rapide)
- [Structure du projet](#structure-du-projet)
- [Licence](#licence)
- [Contact](#contact)

## À propos du projet

Ce projet a été conçu pour démontrer la maîtrise du développement web backend avec PHP, sans dépendre d'un framework comme Laravel ou Symfony. L'objectif était de construire une plateforme e-commerce complète, sécurisée et fonctionnelle, incluant une interface client et un panneau d'administration.

## Fonctionnalités principales

### 🛍️ Côté Client
*   **Catalogue Produits :** Navigation par catégories avec filtres avancés (genre, prix, etc.) et tri.
*   **Fiches Produits Détaillées :** Affichage des détails, images, descriptions et avis clients.
*   **Avis Clients :** Les utilisateurs connectés peuvent noter et commenter les produits.
*   **Panier d'Achat :** Ajout, mise à jour et suppression d'articles.
*   **Processus de Paiement Sécurisé :** Tunnel de commande simulé mais complet.
*   **Authentification Complète :** Inscription, connexion, déconnexion et réinitialisation de mot de passe.
*   **Espace Client :** Gestion des informations, adresses, historique de commandes et retours.

### ⚙️ Panneau d'Administration
*   **Connexion Administrateur Sécurisée.**
*   **Gestion des Produits (CRUD) :** Création, Lecture, Mise à jour et Suppression pour tous les produits.
*   **Journal d'Audit :** Une table `audit_logs` trace les modifications importantes effectuées dans le back-office.

## Stack technique

*   **Backend :** **PHP 8.2+** (approche procédurale et fonctionnelle)
*   **Base de données :** **MySQL**
*   **Frontend :** HTML5, CSS3, JavaScript natif
*   **Environnements de développement :** WampServer / MAMP / XAMPP, ou **Docker**.

## Démarrage rapide

Vous pouvez lancer ce projet via un serveur local classique ou avec Docker.

### Option 1 : Utiliser un serveur local (WampServer, MAMP, XAMPP)

Méthode recommandée si vous êtes familier avec les environnements de développement PHP locaux.

1.  **Prérequis :**
    *   Un environnement comme [WampServer](https://www.wampserver.com/), MAMP ou XAMPP installé.
    *   Accès à phpMyAdmin ou un autre client MySQL.

2.  **Cloner le dépôt :**
    ```bash
    git clone https://github.com/Alespfer/alespfer-pages_parfumees_pise_2025.git
    ```

3.  **Placer les fichiers :**
    Déplacez le dossier cloné dans le répertoire `www` de votre installation WampServer (ou `htdocs` pour XAMPP/MAMP).

4.  **Configuration de la base de données :**
    *   Lancez votre serveur local et ouvrez **phpMyAdmin**.
    *   Créez une nouvelle base de données nommée `ecommerce`.
    *   Sélectionnez cette base de données `ecommerce`.
    *   Allez dans l'onglet "Importer".
    *   Cliquez sur "Choisir un fichier" et sélectionnez le fichier `docs/database.sql` du projet.
    *   Cliquez sur "Exécuter" pour lancer le script. Toutes les tables et les données d'exemple seront créées.

5.  **Configuration :**
    *   Ouvrez le fichier `parametrage/param.php`.
    *   Vérifiez que les identifiants de la base de données correspondent à votre configuration (par défaut, `DB_USER` = 'root', `DB_PASSWORD` = '' sur WampServer).

6.  **Accéder à l'application :**
    Ouvrez votre navigateur et allez à l'adresse `http://localhost/alespfer-pages_parfumees_pise_2025/`.

### Option 2 : Utiliser Docker

Cette méthode utilise le `Dockerfile` pour créer un environnement PHP. **Note :** Cette configuration ne lance que le serveur PHP ; vous devez avoir un serveur MySQL fonctionnant sur votre machine locale.

1.  **Prérequis :**
    *   [Docker](https://www.docker.com/get-started) installé et fonctionnel.
    *   Un serveur MySQL fonctionnant sur votre machine (hors conteneur).

2.  **Cloner et configurer la BDD :**
    *   Clonez le dépôt comme dans l'Option 1.
    *   Suivez l'**Étape 4 (Configuration de la base de données)** de l'Option 1 pour créer et peupler votre base de données `ecommerce` sur votre serveur MySQL local.

3.  **Configuration pour Docker :**
    *   Ouvrez le fichier `parametrage/param.php`.
    *   Vous devez changer `DB_HOST` de `'localhost'` à `'host.docker.internal'`. Ce nom DNS spécial permet au conteneur Docker de se connecter aux services de votre machine hôte.
    ```php
    // Dans parametrage/param.php
    define('DB_HOST', 'host.docker.internal'); // Pour la configuration Docker
    ```

4.  **Construire et lancer le conteneur :**
    Dans un terminal, à la racine du projet, exécutez :
    ```bash
    # Construire l'image Docker
    docker build -t pages-parfumees .

    # Lancer le conteneur
    docker run -p 8000:10000 pages-parfumees
    ```

5.  **Accéder à l'application :**
    Ouvrez votre navigateur et allez à l'adresse `http://localhost:8000`.

## Structure du projet

*   `*.php`: Fichiers Contrôleur/Vue.
*   `/parametrage/`: Fichier de configuration globale.
*   `/fonction/`: Logique métier et fonctions de BDD.
*   `/partials/`: Composants réutilisables (header, footer).
*   `/styles/`: Feuilles de style CSS.
*   `/docs/`: Contient le dump SQL `database.sql`.
*   `Dockerfile`: Définit l'environnement de l'application.

## Licence

Distribué sous la Licence MIT. Voir le fichier `LICENSE`.

## Contact

Alberto Esperon - [LinkedIn](https://www.linkedin.com/in/alberto-espfer) - [Profil GitHub](https://github.com/Alespfer)
