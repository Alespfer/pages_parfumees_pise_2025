<?php
// fonction/fonctions.php


/***************************************************************************
 *  SECTION 0 : OUTILS GENERIQUES                                          *
 ***************************************************************************/

/**
 * Génère un hachage SHA‑256 du mot de passe.
 *
 * @param string $password Le mot de passe brut à hacher.
 * @return string Le hachage hexadécimal en sortie.
 */
function custom_hash(string $password): string
{
    return hash('sha256', $password); // fonction "hash" étudiée en cours
}

/**
 * Vérifie la correspondance entre un mot de passe brut et un hachage SHA‑256.
 *
 * @param string $password Le mot de passe brut à tester.
 * @param string $hash     Le hachage attendu.
 * @return bool Résultat de la comparaison.
 */
function custom_verify(string $password, string $hash): bool
{
    return hash('sha256', $password) === $hash;
}

/***************************************************************************
 *  SECTION 1 : CONNEXION BDD                                              *
 ***************************************************************************/

/**
 * Retourne l’instance PDO unique (pattern Singleton).
 *
 * @return PDO Connexion à la base de données.
 */
function getPDO()
{
    /** @var PDO|null $pdo */
    static $pdo = null;

    if ($pdo === null) {

        // Inclusion du fichier de configuration contenant les constantes DB_*
        require_once('parametrage/param.php');
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ));
        } catch (PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
    return $pdo;
}

/***************************************************************************
 *  SECTION 2 : AUTHENTIFICATION & GESTION DES UTILISATEURS (CLIENTS)       *
 ***************************************************************************/

/**
 * Récupère un client par son adresse email.
 */
function getUserByEmail(string $email)
{
    $sql = 'SELECT * FROM client WHERE email = ?';
    $stmt = getPDO()->prepare($sql);
    $stmt->execute([$email]);
    return $stmt->fetch();
}

/**
 * Inscription d’un nouveau client.
 * Vérifie que l’email n’est pas déjà utilisé, puis enregistre les données avec hash du mot de passe.
 *
 * @param string $nom      Nom du client.
 * @param string $prenom   Prénom du client.
 * @param string $email    Adresse email unique.
 * @param string $password Mot de passe en clair (sera hashé).
 * @return bool True si l’inscription réussit, false sinon (ex: email déjà pris).
 */function registerUser(string $nom, string $prenom, string $email, string $password): bool
{
    if (getUserByEmail($email)) {
        return false; // email déjà utilisé
    }
    $hash = custom_hash($password);
    $sql = 'INSERT INTO client (nom, prenom, email, password_hash) VALUES (?,?,?,?)';
    $stmt = getPDO()->prepare($sql);
    return $stmt->execute([$nom, $prenom, $email, $hash]);
}

/**
 * Tentative de connexion d’un client.
 * Vérifie les identifiants, puis retourne les données (sans le mot de passe) si OK.
 *
 * @param string $email   
 * @param string $password 
 * @return array|false 
 */
function loginUser(string $email, string $password)
{
    $user = getUserByEmail($email);
    if ($user && custom_verify($password, $user['password_hash'])) {
        unset($user['password_hash']); // Sécurité : on supprime le hash avant de retourner les données.
        return $user;
    }
    return false;
}

/**
 * Vérifie que le mot de passe respecte les critères de robustesse recommandés par des regex. 
 *
 * @param string $password Le mot de passe à tester.
 * @return array Liste des erreurs rencontrées (chaînes explicites), ou tableau vide si conforme.
 */function isPasswordStrong(string $password): array
{
    $err = [];
    if (strlen($password) < 8) {
        $err[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }
    if (!preg_match('#[a-z]#', $password)) {
        $err[] = 'Le mot de passe doit contenir au moins 1 minuscule.';
    }
    if (!preg_match('#[A-Z]#', $password)) {
        $err[] = 'Le mot de passe doit contenir au moins 1 majuscule.';
    }
    if (!preg_match('#[0-9]#', $password)) {
        $err[] = 'Le mot de passe doit contenir au moins 1 chiffre.';
    }
    if (!preg_match('#\W#', $password)) {
        $err[] = 'Le mot de passe doit contenir au moins 1 caractère spécial.';
    }
    return $err;
}


/** 
 * Met à jour le nom et le prénom d’un client.
 * Utilise une requête préparée pour la sécurité.
 */

function updateUserInfo(int $idClient, array $d): bool
{
    $sql = 'UPDATE client SET nom = ?, prenom = ? WHERE id_client = ?';
    $stmt = getPDO()->prepare($sql);
    return $stmt->execute([$d['nom'], $d['prenom'], $idClient]);
}

/**
 * Met à jour le mot de passe d’un client après vérification de l’ancien.
 * Retourne un booléen ou un message d’erreur selon le cas.
 */
function updateUserPassword(int $idClient, string $old, string $new)
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT password_hash FROM client WHERE id_client = ?');
    $stmt->execute([$idClient]);
    $row = $stmt->fetch();
    if (!$row) {
        return 'Utilisateur introuvable.';
    }
    if (!custom_verify($old, $row['password_hash'])) {
        return 'Mot de passe actuel incorrect.';
    }

    $hash = custom_hash($new);
    $update = $pdo->prepare('UPDATE client SET password_hash = ? WHERE id_client = ?');
    return $update->execute([$hash, $idClient]);
}

/**
 * Génère un token de réinitialisation et le stocke avec une date d’expiration.
 * Retourne le token, ou null si l’email est inconnu.
 */
function generateAndSaveResetToken($email)
{
    $pdo = getPDO();
    $user = getUserByEmail($email);

    if ($user === false) {
        return null;
    }

    // Token basé sur email + timestamp + grain de sel statique
    $token = custom_hash($email . time() . 'un_sel_aleatoire_conforme');
    $expires = date('Y-m-d H:i:s', time() + 3600); 

    $sql = 'UPDATE client SET reset_token = ?, reset_token_expires_at = ? WHERE id_client = ?';
    $pdo->prepare($sql)->execute(array($token, $expires, $user['id_client']));
    return $token;
}

/* Récupère un client grâce à son token. */
function getClientByResetToken(string $token)
{
    $sql = 'SELECT * FROM client WHERE reset_token = ? AND reset_token_expires_at > NOW()';
    $stmt = getPDO()->prepare($sql);
    $stmt->execute([$token]);
    return $stmt->fetch();
}

/*Met à jour le mot de passe via un token valide et efface le token.*/
function updatePasswordAndClearToken(int $idClient, string $new): bool
{
    $hash = custom_hash($new);
    $sql = 'UPDATE client SET password_hash = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id_client = ?';
    return getPDO()->prepare($sql)->execute([$hash, $idClient]);
}

/***************************************************************************
 *  SECTION 3 : GESTION DES ADRESSES                                        *
 ***************************************************************************/

/**
 * Récupère la liste complète des adresses d'un client donné.
 */
function getUserAddresses(int $idClient): array
{
    $sql = 'SELECT * FROM adresse WHERE id_client = ? ORDER BY est_defaut DESC, id_adresse ASC';
    $stmt = getPDO()->prepare($sql);
    $stmt->execute([$idClient]);
    return $stmt->fetchAll();
}

/**
 * Récupère une adresse spécifique appartenant à un client donné.
 */
function getAddressById(int $idAdr, int $idClient)
{
    $sql = 'SELECT * FROM adresse WHERE id_adresse = ? AND id_client = ?';
    $stmt = getPDO()->prepare($sql);
    $stmt->execute([$idAdr, $idClient]);
    return $stmt->fetch();
}

/**
 * Ajoute une nouvelle adresse pour un client.
 * Gère également le statut "par défaut" via une transaction.
 *
 * @param int   $idClient L'identifiant du client.
 * @param array $d        Données de l'adresse (rue, code_postal, ville, pays, est_defaut).
 * @return bool TRUE si ajout réussi, FALSE sinon.
 */
function addAddress(int $idClient, array $d): bool
{
    $pdo = getPDO();

    // Vérification explicite de la demande d'adresse par défaut.
    $isDefault = 0;
    if (isset($d['est_defaut']) && $d['est_defaut'] == 1) {
        $isDefault = 1;
    }

    try {
        $pdo->beginTransaction();

        // Si adresse par défaut, on réinitialise les autres.
        if ($isDefault === 1) {
            $pdo->prepare('UPDATE adresse SET est_defaut = 0 WHERE id_client = ?')->execute([$idClient]);
        }

        // Insertion de la nouvelle adresse.

        $sql = 'INSERT INTO adresse (id_client, rue, code_postal, ville, pays, est_defaut) VALUES (?,?,?,?,?,?)';
        $pdo->prepare($sql)->execute([
            $idClient,
            $d['rue'],
            $d['code_postal'],
            $d['ville'],
            $d['pays'],
            $isDefault,
        ]);
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

/**
 * Met à jour une adresse existante pour un client.
 * Gère également le statut "par défaut" via une transaction.
 *
 * @param int   $idAdr    Identifiant de l'adresse.
 * @param int   $idClient Identifiant du client.
 * @param array $d        Données de mise à jour.
 * @return bool TRUE si succès, FALSE sinon.
 */
function updateAddress(int $idAdr, int $idClient, array $d): bool
{
    $pdo = getPDO();

    $isDefault = 0;
    if (isset($d['est_defaut']) && $d['est_defaut'] == 1) {
        $isDefault = 1;
    }

    try {
        $pdo->beginTransaction();
        if ($isDefault === 1) {
            $pdo->prepare('UPDATE adresse SET est_defaut = 0 WHERE id_client = ?')->execute([$idClient]);
        }
        $sql = 'UPDATE adresse SET rue = ?, code_postal = ?, ville = ?, pays = ?, est_defaut = ? WHERE id_adresse = ? AND id_client = ?';
        $pdo->prepare($sql)->execute([
            $d['rue'],
            $d['code_postal'],
            $d['ville'],
            $d['pays'],
            $isDefault,
            $idAdr,
            $idClient,
        ]);
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

/**
 * Supprime une adresse appartenant à un client donné.
 */
function deleteAddress(int $idAdr, int $idClient): bool
{
    $sql = 'DELETE FROM adresse WHERE id_adresse = ? AND id_client = ?';
    return getPDO()->prepare($sql)->execute([$idAdr, $idClient]);
}

/***************************************************************************
 *  SECTION 4 : CATALOGUE, PRODUITS & AVIS                                 *
 ***************************************************************************/

/**
 * Récupère tous les tags d'ambiance disponibles.
 * Utilisé notamment pour filtrer les bougies par ambiance.
 */
function getAllTags(): array
{
    return getPDO()->query('SELECT id_tag, nom_tag FROM tag ORDER BY nom_tag ASC')->fetchAll();
}

/**
 * Retourne la liste des états standards pour les livres.
 * Cette liste est utilisée dans les formulaires et pour le filtrage.
 */
function getAllEtats(): array
{
    return ['Neuf', 'Très bon état', 'Bon état', 'État correct'];
}

/**
 * Récupère la liste alphabétique des parfums uniques des bougies.
 * Évite les doublons grâce à DISTINCT, et ne conserve que les champs non vides.
 */
function getAllParfums(): array
{
    $sql = 'SELECT DISTINCT parfum FROM bougie WHERE parfum IS NOT NULL AND parfum != "" ORDER BY parfum ASC';
    return getPDO()->query($sql)->fetchAll(PDO::FETCH_COLUMN);
}

/** Fournit un alias pour getAllTags() utilisé spécifiquement pour les ambiances
* @return array Identique à getAllTags().
*/
function getAllAmbianceTags(): array
{
    return getAllTags();
}


/**
 * Récupère la liste des produits filtrés avec pagination et tri.
 * Utilise les filtres fournis pour construire dynamiquement la requête SQL.
 * 
 * @param array $f Filtres nettoyés (type, genres, prix, etc.).
 * @return array La liste des produits filtrés.
 */
function getFilteredProducts(array $f): array
{
    $pdo = getPDO();
    $p = [];
    $join = [];
    $w = [];
    $h = [];


    // Filtres par défaut
    $base = [
        'type' => '',
        'page' => 1,
        'limit' => 12,
        'sort' => 'nouveaute',
        'prix_min' => '',
        'prix_max' => '',
        'genres' => [],
        'etats' => [],
        'parfums' => [],
        'ambiances' => [],
    ];
    $f = array_merge($base, $f);

    /* ----------- Sélection de base avec regroupements nécessaires ----------- */
    $select = 'SELECT p.id_produit, p.type, p.prix_ht, p.tva_rate,
                      (p.prix_ht * (1 + p.tva_rate / 100)) AS prix_ttc,
                      p.image_url, p.note_moyenne, p.nombre_votes,
                      COALESCE(l.titre, b.nom, c.nom) AS nom_produit,
                      b.parfum,
                      GROUP_CONCAT(DISTINCT a.prenom, " ", a.nom SEPARATOR ", ") AS auteurs,
                      GROUP_CONCAT(DISTINCT t.nom_tag ORDER BY t.nom_tag SEPARATOR ", ") AS ambiances_tags'; // <-- LA COLONNE CLÉ

    $from = 'FROM produit p';

    $join['livre'] = 'LEFT JOIN livre   l ON p.id_produit = l.id_produit';
    $join['bougie'] = 'LEFT JOIN bougie  b ON p.id_produit = b.id_produit';
    $join['coffret'] = 'LEFT JOIN coffret c ON p.id_produit = c.id_produit';
    $join['auteur'] = 'LEFT JOIN livre_auteur la ON la.id_produit = l.id_produit LEFT JOIN auteur a ON a.id_auteur = la.id_auteur';
    $join['produit_tag'] = 'LEFT JOIN produit_tag pt ON p.id_produit = pt.id_produit';
    $join['tag'] = 'LEFT JOIN tag t ON pt.id_tag = t.id_tag';


    /* ------------------------- Conditions dynamiques ------------------------- */

    if ($f['type'] !== '') {
        $w[] = 'p.type = ?';
        $p[] = $f['type'];
    }

    if (count($f['genres']) > 0) {
        // Il faut un JOIN normal ici pour filtrer
        $join['livre_genre'] = 'JOIN livre_genre lg ON p.id_produit = lg.id_produit';
        $marks = implode(',', array_fill(0, count($f['genres']), '?'));
        $w[] = "lg.id_genre IN ($marks)";
        $p = array_merge($p, $f['genres']);
    }

    if (count($f['etats']) > 0) {
        $marks = implode(',', array_fill(0, count($f['etats']), '?'));
        $w[] = "l.etat IN ($marks)";
        $p = array_merge($p, $f['etats']);
    }

    if (count($f['ambiances']) > 0) {
        $marks = implode(',', array_fill(0, count($f['ambiances']), '?'));
        // On force un JOIN normal pour le filtre
        $join['produit_tag_filter'] = 'JOIN produit_tag pt_filter ON p.id_produit = pt_filter.id_produit';
        $w[] = "pt_filter.id_tag IN ($marks)";
        $p = array_merge($p, $f['ambiances']);
    }

    if ($f['prix_min'] !== '' && is_numeric($f['prix_min'])) {
        $h[] = 'prix_ttc >= ?';
        $p[] = (float) $f['prix_min'];
    }
    if ($f['prix_max'] !== '' && is_numeric($f['prix_max'])) {
        $h[] = 'prix_ttc <= ?';
        $p[] = (float) $f['prix_max'];
    }


    /* ---------------------------- Tri final ---------------------------- */

    $order = 'ORDER BY p.id_produit DESC';
    if ($f['sort'] === 'prix_asc') {
        $order = 'ORDER BY prix_ttc ASC';
    } elseif ($f['sort'] === 'prix_desc') {
        $order = 'ORDER BY prix_ttc DESC';
    } elseif ($f['sort'] === 'note_desc') {
        $order = 'ORDER BY p.note_moyenne DESC, p.nombre_votes DESC';
    }

    $limit = (int) $f['limit'];
    $offset = ((int) $f['page'] - 1) * $limit;

    /* ---------------------------- Assemblage ---------------------------- */
    $sql = $select . ' ' . $from . ' ' . implode(' ', $join);
    if ($w) {
        $sql .= ' WHERE ' . implode(' AND ', $w);
    }
    $sql .= ' GROUP BY p.id_produit'; // Groupement final
    if ($h) {
        $sql .= ' HAVING ' . implode(' AND ', $h);
    }
    $sql .= ' ' . $order . " LIMIT $limit OFFSET $offset";

    /* ---------------------------- Exécution ---------------------------- */

    $stmt = $pdo->prepare($sql);
    $stmt->execute($p);
    return $stmt->fetchAll();
}

/**
 * Compte le nombre de produits filtrés pour la pagination.
 * Les filtres peuvent inclure : type, genres, états, parfums, ambiances, prix min/max.
 *
 * @param array $f Les filtres de sélection.
 * @return int Le nombre de produits correspondant aux critères.
 */function countFilteredProducts(array $f): int
{
    $pdo = getPDO();
    $p = [];
    $join = [];
    $w = [];


    // Base de structure des filtres avec des valeurs par défaut
    $base = [
        'type' => '',
        'genres' => [],
        'etats' => [],
        'parfums' => [],
        'ambiances' => [],
        'prix_min' => '',
        'prix_max' => '',
    ];
    $f = array_merge($base, $f); // Fusion avec les valeurs reçues

    // Filtre par type de produit (ex: livre, bougie, coffret)
    if ($f['type'] !== '') {
        $w[] = 'p.type = ?';
        $p[] = $f['type'];
    }

    // Filtre par genre littéraire

    if (count($f['genres']) > 0) {
        $join['livre_genre'] = 'JOIN livre_genre lg ON p.id_produit = lg.id_produit';
        $marks = implode(',', array_fill(0, count($f['genres']), '?'));
        $w[] = "lg.id_genre IN ($marks)";
        $p = array_merge($p, $f['genres']);
    }

    // Filtre par état du livre (neuf, usé, etc.)

    if (count($f['etats']) > 0) {
        $join['livre'] = 'LEFT JOIN livre l ON p.id_produit = l.id_produit';
        $marks = implode(',', array_fill(0, count($f['etats']), '?'));
        $w[] = "l.etat IN ($marks)";
        $p = array_merge($p, $f['etats']);
    }

    // Filtre par parfums (bougies)
    if (count($f['parfums']) > 0) {
        $join['bougie'] = 'JOIN bougie b ON p.id_produit = b.id_produit';
        $parfum_conditions = [];
        foreach ($f['parfums'] as $parfum) {
            $parfum_conditions[] = 'FIND_IN_SET(?, REPLACE(b.parfum, ", ", ","))';
            $p[] = trim($parfum);
        }
        $w[] = '(' . implode(' OR ', $parfum_conditions) . ')';
    }

    // Filtre par ambiance (tags)

    if (count($f['ambiances']) > 0) {
        $join['produit_tag'] = 'JOIN produit_tag pt ON p.id_produit = pt.id_produit';
        $marks = implode(',', array_fill(0, count($f['ambiances']), '?'));
        $w[] = "pt.id_tag IN ($marks)";
        $p = array_merge($p, $f['ambiances']);
    }

    // Filtre sur le prix minimum TTC

    if ($f['prix_min'] !== '' && is_numeric($f['prix_min'])) {
        $w[] = '(p.prix_ht * (1 + p.tva_rate / 100)) >= ?';
        $p[] = (float) $f['prix_min'];
    }

    // Filtre sur le prix maximum TTC

    if ($f['prix_max'] !== '' && is_numeric($f['prix_max'])) {
        $w[] = '(p.prix_ht * (1 + p.tva_rate / 100)) <= ?';
        $p[] = (float) $f['prix_max'];
    }


    // Construction finale de la requête SQL

    $sql = 'SELECT COUNT(DISTINCT p.id_produit) FROM produit p ' . implode(' ', $join);
    if ($w) {
        $sql .= ' WHERE ' . implode(' AND ', $w);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($p);
    return (int) $stmt->fetchColumn();
}



/***************************************************************************
 *  SECTION 5 : UTILITAIRES (FORM & AUTRES)                                *
 ***************************************************************************/



/**
 * Génère dynamiquement des balises <input type="hidden"> à partir de $_GET,
 * en excluant explicitement les clés définies dans le tableau $exceptions.
 * Cette technique permet de préserver les paramètres GET lors des redirections POST.
 *
 * @param array $exceptions Liste des clés GET à ignorer (optionnel).
 * @return void
 */
function build_hidden_fields_except(array $exceptions = []): void
{

    // Parcours de toutes les paires clé => valeur de $_GET
    foreach ($_GET as $key => $value) {
        // Si la clé est dans les exceptions, on l'ignore.
        if (in_array($key, $exceptions, true)) {
            continue;
        }
        // Si la valeur est un tableau, on génère plusieurs balises.
        if (is_array($value)) {
            foreach ($value as $item) {
                if ($item !== '') {
                    echo '<input type="hidden" name="' .
                        htmlspecialchars($key) . '[]" value="' .
                        htmlspecialchars($item) . '">';
                }
            }
        } else {
            // Sinon, on génère une balise unique si la valeur n’est pas vide.
            if ($value !== '') {
                echo '<input type="hidden" name="' .
                    htmlspecialchars($key) . '" value="' .
                    htmlspecialchars($value) . '">';
            }
        }
    }
}

/**
 * Crée une demande de retour pour une commande client, puis passe la commande au statut « Retour demandé ».
 *
 * @param int   $clientId    L’ID du client effectuant la demande.
 * @param int   $commandeId  L’ID de la commande concernée.
 * @param array $data        Les données issues du formulaire de retour.
 * @return bool              TRUE si la demande a été créée avec succès, FALSE sinon.
 */
function createReturnRequest(int $clientId, int $commandeId, array $data): bool
{
    $pdo = getPDO();
    $produitsRetour = [];

    // Étape 1 : Extraction des produits cochés dans le formulaire.
    // On vérifie que la clé 'produits' existe et contient un tableau non vide.
    if (isset($data['produits']) && is_array($data['produits']) && count($data['produits']) > 0) {
        foreach ($data['produits'] as $idProduit => $det) {
            if (isset($det['selected']) && $det['selected'] === '1') {
                $produitsRetour[$idProduit] = $det;
            }
        }
    }

    if (count($produitsRetour) === 0) {
        return false;   // rien à retourner
    }

    // Étape 2 : Démarrage de la transaction sécurisée.
    try {
        $pdo->beginTransaction();

        // Étape 2.A : Insertion de l’en-tête de demande.
        $msg = '';
        if (isset($data['message_global'])) {
            $msg = trim($data['message_global']);
        }

        $stmtDemande = $pdo->prepare(
            'INSERT INTO demande_retour (id_commande, id_client, id_statut_demande, message_demande)
             VALUES (?,?,1,?)'
        );
        $stmtDemande->execute([$commandeId, $clientId, $msg]);
        $idDemande = (int) $pdo->lastInsertId();

        // Étape 2.B : Insertion des lignes produits associées à la demande.
        $stmtProd = $pdo->prepare(
            'INSERT INTO retour_produit (id_demande, id_produit, quantite, raison)
             VALUES (?,?,?,?)'
        );

        foreach ($produitsRetour as $idProduit => $det) {
            $qte = 0;
            if (isset($det['quantite'])) {
                $qte = (int) $det['quantite'];
            }
            if ($qte > 0) {
                $raison = '';
                if (isset($det['raison'])) {
                    $raison = trim($det['raison']);
                }
                $stmtProd->execute([$idDemande, $idProduit, $qte, $raison]);
            }
        }

        // Étape 2.C : Mise à jour du statut de la commande.
        $pdo->prepare(
            'UPDATE commande SET id_statut_commande = 6 WHERE id_commande = ? AND id_client = ?'
        )->execute([$commandeId, $clientId]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        // En cas d'erreur, on annule toutes les opérations et on journalise.
        $pdo->rollBack();
        error_log("Erreur createReturnRequest #{$commandeId} : " . $e->getMessage());
        return false;
    }
}

/**
 * Vérifie si une demande de retour existe déjà pour une commande donnée.
 *
 * @param int $idCommande L'ID de la commande à vérifier.
 * @return bool Vrai si une demande est présente, faux sinon.
 */
function checkIfReturnExistsForOrder($idCommande)
{
    $sql = 'SELECT COUNT(*) FROM demande_retour WHERE id_commande = ?';
    $stmt = getPDO()->prepare($sql);
    $stmt->execute(array($idCommande));
    // On convertit le résultat en entier, et on le compare à 0.
    return (int) $stmt->fetchColumn() > 0;
}

/**
 * Récupère tous les détails liés à un produit, quel que soit son type (livre, bougie, coffret).
 *
 * @param int $id L'ID du produit à interroger.
 * @return array|false Un tableau contenant les champs fusionnés, ou false si non trouvé.
 */
function getProductById(int $id)
{
    $pdo = getPDO();
    $sql = 'SELECT p.*,
                   (p.prix_ht * (1 + p.tva_rate / 100))       AS prix_ttc,
                   COALESCE(l.titre, b.nom, c.nom)            AS nom_produit,
                    
                    -- Détails spécifiques au livre
                   l.isbn, l.resume, l.etat, l.annee_publication, l.nb_pages,
                   e.nom  AS editeur,
                    -- Agrégation des auteurs du livre
                   GROUP_CONCAT(DISTINCT CONCAT(a.prenom, " ", a.nom) SEPARATOR ", ") AS auteurs,
                   GROUP_CONCAT(DISTINCT g.nom SEPARATOR ", ")                        AS genres,
                    -- Détails spécifiques à la bougie
                   b.description        AS description_bougie, b.parfum, b.duree_combustion, b.poids,
                    -- Détails spécifiques au coffret
                   c.description        AS description_coffret,
                   c.id_produit_livre,  c.id_produit_bougie,
                   cc.libelle           AS categorie_coffret

            FROM produit p
            LEFT JOIN livre            l  ON l.id_produit         = p.id_produit
            LEFT JOIN bougie           b  ON b.id_produit         = p.id_produit
            LEFT JOIN coffret          c  ON c.id_produit         = p.id_produit
            LEFT JOIN categorie_coffret cc ON cc.id_categorie_coffret = c.id_categorie_coffret
            LEFT JOIN editeur          e  ON e.id_editeur         = l.id_editeur
            LEFT JOIN livre_auteur     la ON la.id_produit        = l.id_produit
            LEFT JOIN auteur           a  ON a.id_auteur          = la.id_auteur
            LEFT JOIN livre_genre      lg ON lg.id_produit        = l.id_produit
            LEFT JOIN genre            g  ON g.id_genre           = lg.id_genre
            WHERE p.id_produit = ?
            GROUP BY p.id_produit, c.id_produit_livre, c.id_produit_bougie';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}



/**
 * Récupère tous les parfums uniques définis pour les bougies.
 * Chaque champ "parfum" pouvant contenir plusieurs parfums séparés par des virgules,
 * la fonction les éclate, les purifie, puis les trie alphabétiquement.
 *
 * @return array Liste triée de tous les parfums distincts.
 */

function getAllIndividualScents(): array
{
    $pdo = getPDO();
    $sql = 'SELECT DISTINCT parfum FROM bougie WHERE parfum IS NOT NULL AND parfum != ""';
    $stmt = $pdo->query($sql);

    $scents = [];

    // Chaque ligne peut contenir plusieurs parfums séparés par virgule
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $parts = explode(',', $row['parfum']);
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part !== '') {
                $scents[] = $part;
            }
        }
    }
    $scents = array_unique($scents); // Suppression des doublons
    sort($scents);
    return $scents;
}

/**
 * Calcule les bornes minimum et maximum (TTC) de tous les produits.
 * Retourne un tableau associatif avec les clés 'min' et 'max'.
 *
 * @return array Tableau contenant les bornes TTC : ['min' => int, 'max' => int]
 */
function getMinMaxPrices(): array
{
    $sql = 'SELECT MIN(prix_ht * (1 + tva_rate / 100)) AS min_price,
                   MAX(prix_ht * (1 + tva_rate / 100)) AS max_price
            FROM produit';
    $row = getPDO()->query($sql)->fetch(PDO::FETCH_ASSOC);

    $min = 0;
    $max = 100;
    if (isset($row['min_price'])) {
        $min = (int) floor($row['min_price']);
    }
    if (isset($row['max_price'])) {
        $max = (int) ceil($row['max_price']);
    }
    return ['min' => $min, 'max' => $max];
}

/**
 * Enregistre ou met à jour la note d’un produit par un client donné.
 * Utilise la syntaxe SQL ON DUPLICATE KEY UPDATE pour éviter les doublons.
 *
 * @param int         $idClient Identifiant du client.
 * @param int         $idProduit Identifiant du produit.
 * @param int         $note Note attribuée (entre 1 et 5).
 * @param string|null $commentaire Commentaire optionnel.
 * @return bool TRUE si la requête a réussi, FALSE sinon.
 */
function rateProduct(int $idClient, int $idProduit, int $note, ?string $commentaire): bool
{
    if ($note < 1 || $note > 5) {
        return false;
    }

    $sql = 'INSERT INTO notation_produit (id_produit, id_client, note, commentaire, date_notation)
            VALUES (?,?,?,?,NOW())
            ON DUPLICATE KEY UPDATE
                note          = VALUES(note),
                commentaire   = VALUES(commentaire),
                date_notation = NOW()';

    $stmt = getPDO()->prepare($sql);
    return $stmt->execute([$idProduit, $idClient, $note, $commentaire]);
}

/**
 * Récupère les avis (note + commentaire) associés à un produit donné.
 * Les résultats sont triés du plus récent au plus ancien.
 *
 * @param int $idProduit Identifiant du produit concerné.
 * @return array Liste des avis (note, commentaire, date, prénom du client).
 */
function getProductReviews(int $idProduit): array
{
    $sql = 'SELECT n.note, n.commentaire, n.date_notation,
                   c.prenom AS prenom_client
            FROM notation_produit n
            JOIN client c ON n.id_client = c.id_client
            WHERE n.id_produit = ?
            ORDER BY n.date_notation DESC';

    $stmt = getPDO()->prepare($sql);
    $stmt->execute([$idProduit]);
    return $stmt->fetchAll();
}


// =========================================================================
// SECTION 5 : PANIER, COMMANDE ET PAIEMENT
// =========================================================================

/**
 * Met à jour le panier stocké en session selon l'action spécifiée :
 * - 'add' : ajoute une quantité à un produit (ou l'initialise si absent).
 * - 'update' : remplace la quantité (ou supprime si quantité nulle).
 * - 'remove' : supprime le produit du panier.
 * @param string $action L'action à effectuer ('add', 'update', 'remove').
 * @param int    $productId L'identifiant du produit concerné.
 * @param int    $quantity La quantité associée à l'action.
 */
function handleCartAction(string $action, int $productId, int $quantity): void
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if ($productId <= 0) {
        return;
    }

    switch ($action) {
        case 'add':
            if ($quantity > 0) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId] += $quantity;
                } else {
                    $_SESSION['cart'][$productId] = $quantity;
                }
            }
            break;

        case 'update':
            if ($quantity > 0) {
                $_SESSION['cart'][$productId] = $quantity;
            } else {
                unset($_SESSION['cart'][$productId]);
            }
            break;

        case 'remove':
            unset($_SESSION['cart'][$productId]);
            break;
    }
}


/**
 * Calcule le détail complet du panier : chaque ligne produit + totaux HT/TVA/TTC.
 * Cette fonction est essentielle pour la page récapitulative avant paiement.
 *
 * @param array $cart Tableau associatif des produits [id_produit => quantité].
 * @return array Résultat structuré avec lignes et totaux.
 */
function getCartSummary(array $cart): array
{
    // Initialisation des résultats avec structure vide.
    $sum = [
        'items' => [],
        'total_ht' => 0.0,
        'total_tva' => 0.0,
        'total_ttc' => 0.0,
    ];

    // Panier vide ou non défini.

    if (!isset($cart) || count($cart) === 0) {
        return $sum;
    }

    /* --------- Récupération des produits depuis la BDD --------- */
    $ids = array_keys($cart); // Extraction des IDs de produits.
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $sql = 'SELECT p.id_produit, p.prix_ht, p.tva_rate, p.stock,
                           COALESCE(l.titre, b.nom, c.nom) AS nom_produit
                    FROM produit p
                    LEFT JOIN livre   l ON l.id_produit = p.id_produit
                    LEFT JOIN bougie  b ON b.id_produit = p.id_produit
                    LEFT JOIN coffret c ON c.id_produit = p.id_produit
                    WHERE p.id_produit IN (' . $marks . ')';

    $stmt = getPDO()->prepare($sql);
    $stmt->execute($ids);

    // Récupération des résultats indexés par id_produit.
    $prodRows = $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

    /* --------- Calculs des lignes et des totaux --------- */
    foreach ($cart as $id => $qty) {
        // Vérification que le produit existe dans la base.
        if (!isset($prodRows[$id])) {
            continue;
        }

        $prod = $prodRows[$id];

        // Calculs unitaires pour chaque ligne.
        $lineHt = $prod['prix_ht'] * $qty;
        $lineTva = $lineHt * ($prod['tva_rate'] / 100);
        $lineTtc = $lineHt + $lineTva;

        // Constitution de la ligne détaillée.
        $sum['items'][] = [
            'id' => $id,
            'name' => $prod['nom_produit'],
            'unit_ht' => $prod['prix_ht'],
            'stock' => $prod['stock'],
            'quantity' => $qty,
            'line_ht' => $lineHt,
            'line_ttc' => $lineTtc,

        ];

        // Accumulation dans les totaux.
        $sum['total_ht'] += $lineHt;
        $sum['total_tva'] += $lineTva;
        $sum['total_ttc'] += $lineTtc;
    }

    return $sum;
}


/**
 * Crée une commande et gère la décrémentation du stock, y compris pour les coffrets.
 *
 * @param int   $clientId    Identifiant du client.
 * @param int   $adresseId   Identifiant de l'adresse de livraison.
 * @param array $panier      Tableau associatif des produits [id_produit => quantité].
 * @param array $totaux      Détail des totaux : total_ht, total_tva, total_ttc.
 * @return int|string        ID de la commande ou message d'erreur explicite.
 */
function createOrderAndPayment(
    int $clientId,
    int $adresseId,
    array $panier,
    array $totaux
): int|string {
    $pdo = getPDO();

    try {
        $pdo->beginTransaction();

        // Insertion de l'en-tête de commande.
        $stmtCmd = $pdo->prepare(
            'INSERT INTO commande (total_ht, total_tva, total_ttc, id_client, id_adresse_livraison, id_statut_commande)
             VALUES (?, ?, ?, ?, ?, 2)' // Statut 2 = "En préparation"
        );
        $stmtCmd->execute([$totaux['total_ht'], $totaux['total_tva'], $totaux['total_ttc'], $clientId, $adresseId]);
        $idCmd = (int) $pdo->lastInsertId();

        // Préparation des requêtes.
        $stmtDet = $pdo->prepare(
            'INSERT INTO commande_details (id_commande, id_produit, quantite, prix_ht, tva_rate, prix_ttc, montant_tva)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        // On récupère aussi le type pour gérer les coffrets.
        $stmtProd = $pdo->prepare('SELECT prix_ht, tva_rate, stock, type FROM produit WHERE id_produit = ? FOR UPDATE');
        $stmtStock = $pdo->prepare('UPDATE produit SET stock = stock - ? WHERE id_produit = ?');

        // Traitement de chaque ligne du panier.
        foreach ($panier as $idProd => $qte) {
            $stmtProd->execute([$idProd]);
            $produit_principal = $stmtProd->fetch();

            if (!$produit_principal || $produit_principal['stock'] < $qte) {
                throw new Exception('Stock insuffisant pour le produit #' . $idProd);
            }

            // Insertion de la ligne de commande pour le produit principal.
            $ht = (float) $produit_principal['prix_ht'];
            $tva = (float) $produit_principal['tva_rate'];
            $ttc = $ht * (1 + $tva / 100);
            $stmtDet->execute([$idCmd, $idProd, $qte, $ht, $tva, $ttc, $ttc - $ht]);
            
            // --- DÉCRÉMENTATION DU STOCK (LOGIQUE CENTRALISÉE) ---
            $stmtStock->execute([$qte, $idProd]);

            // --- GESTION SPÉCIFIQUE DES COFFRETS ---
            // Si le produit est un coffret, on doit aussi décrémenter le stock de ses composants.
            if ($produit_principal['type'] == 'coffret') {
                // On récupère les IDs des produits contenus dans le coffret.
                $stmtCoffret = $pdo->prepare('SELECT id_produit_livre, id_produit_bougie FROM coffret WHERE id_produit = ?');
                $stmtCoffret->execute([$idProd]);
                $composants = $stmtCoffret->fetch();

                if ($composants) {
                    // Si un livre est inclus, on décrémente son stock.
                    if (isset($composants['id_produit_livre'])) {
                        $stmtStock->execute([$qte, $composants['id_produit_livre']]);
                    }
                    // Si une bougie est incluse, on décrémente son stock.
                    if (isset($composants['id_produit_bougie'])) {
                        $stmtStock->execute([$qte, $composants['id_produit_bougie']]);
                    }
                }
            }
        }

        // Simulation du paiement.
        $pdo->prepare('INSERT INTO paiement (id_commande, montant, moyen, statut) VALUES (?, ?, "Carte Bancaire", "Succès")')
            ->execute([$idCmd, $totaux['total_ttc']]);
        
        $pdo->commit();
        return $idCmd;

    } catch (Exception $e) {
        $pdo->rollBack();
        return $e->getMessage();
    }
}


/**
 * Récupère la liste des commandes passées par un client donné.
 * Retourne l’ID, la date, le total et le libellé du statut.
 * @param int $clientId L’ID du client.
 * @return array Liste des commandes du client.
 */
function getUserOrders(int $clientId): array
{
    $sql = 'SELECT c.id_commande, c.date_commande, c.total_ttc,
                   s.libelle AS statut_libelle
            FROM   commande c
            JOIN   statut_commande s ON c.id_statut_commande = s.id_statut_commande
            WHERE  c.id_client = ?
            ORDER  BY c.date_commande DESC';

    $stmt = getPDO()->prepare($sql);
    $stmt->execute([$clientId]);
    return $stmt->fetchAll();
}


/**
 * Récupère les détails d’une commande d’un client spécifique.
 * Inclut l'en-tête de commande, les informations de livraison et la liste des produits.
 * @param int $orderId   L’ID de la commande.
 * @param int $clientId  L’ID du client. 
 * @return array|false Détails complets de la commande ou FALSE si elle n’existe pas ou n’appartient pas au client.
 */

function getOrderDetailsForUser(int $orderId, int $clientId)
{
    $pdo = getPDO();

    // Récupération des informations générales de la commande (entête + adresse)
    $sqlH = 'SELECT c.*, s.libelle AS statut_libelle,
                    a.rue, a.code_postal, a.ville, a.pays
             FROM   commande c
             JOIN   statut_commande s ON c.id_statut_commande = s.id_statut_commande
             JOIN   adresse a ON c.id_adresse_livraison = a.id_adresse
             WHERE  c.id_commande = ? AND c.id_client = ?';

    $stmtH = $pdo->prepare($sqlH);
    $stmtH->execute([$orderId, $clientId]);
    $cmd = $stmtH->fetch();

    if (!$cmd) {
        return false;
    }

    // Récupération des lignes de produits associées à la commande
    $sqlL = 'SELECT cd.*, COALESCE(l.titre, b.nom) AS nom_produit
             FROM   commande_details cd
             JOIN   produit p ON cd.id_produit = p.id_produit
             LEFT   JOIN livre  l ON p.id_produit = l.id_produit
             LEFT   JOIN bougie b ON p.id_produit = b.id_produit
             WHERE  cd.id_commande = ?';

    $stmtL = $pdo->prepare($sqlL);
    $stmtL->execute([$orderId]);
    $cmd['produits'] = $stmtL->fetchAll();

    return $cmd;
}

/**
 * Récupère toutes les demandes de retour effectuées par un client.
 * Trie par date de demande décroissante.
 */
function getUserReturnRequests(int $clientId): array
{
    $sql = 'SELECT dr.*, sd.libelle AS statut_libelle
            FROM   demande_retour dr
            JOIN   statut_demande sd ON dr.id_statut_demande = sd.id_statut_demande
            WHERE  dr.id_client = ?
            ORDER  BY dr.date_demande DESC';

    $stmt = getPDO()->prepare($sql);
    $stmt->execute([$clientId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère les détails d’une demande de retour, pour un client donné.
 * Sécurise l'accès avec une double condition sur l'identité du client.
 *
 * @param int $returnId  L’ID de la demande de retour.
 * @param int $clientId  L’ID du client connecté.
 * @return array|null Le détail de la demande, ou NULL si elle n'existe pas ou n'appartient pas au client.
 */
function getReturnRequestDetailsForUser(int $returnId, int $clientId): ?array
{
    $pdo = getPDO();

    $sqlR = 'SELECT dr.*, sd.libelle AS statut_libelle
             FROM   demande_retour dr
             JOIN   statut_demande sd ON dr.id_statut_demande = sd.id_statut_demande
             WHERE  dr.id_demande = ? AND dr.id_client = ?';

    $stmtR = $pdo->prepare($sqlR);
    $stmtR->execute([$returnId, $clientId]);
    $ret = $stmtR->fetch(PDO::FETCH_ASSOC);

    if (!$ret) {
        return null;
    }

    // Récupération des produits associés à la demande
    $sqlP = 'SELECT rp.*, COALESCE(l.titre, b.nom, c.nom) AS nom_produit
             FROM   retour_produit rp
             JOIN   produit p ON rp.id_produit = p.id_produit
             LEFT   JOIN livre   l ON p.id_produit = l.id_produit
             LEFT   JOIN bougie  b ON p.id_produit = b.id_produit
             LEFT   JOIN coffret c ON p.id_produit = c.id_produit
             WHERE  rp.id_demande = ?';

    $stmtP = $pdo->prepare($sqlP);
    $stmtP->execute([$returnId]);
    $ret['produits'] = $stmtP->fetchAll(PDO::FETCH_ASSOC);

    return $ret;
}

/**
 * Retourne une classe CSS Bootstrap en fonction d’un libellé de statut.
 * Permet de colorer dynamiquement les badges dans l'interface.
 *
 * @param string $status Le libellé du statut.
 * @return string La classe CSS associée.
 */
function getOrderStatusBadgeClass(string $status): string
{
    // On met en minuscule pour éviter les problèmes de casse (ex: "Livrée" vs "livrée")
    switch (strtolower(trim($status))) {
        case 'livrée':
        case 'payée':
        case 'terminée':
            return 'bg-success';

        case 'en cours de traitement':
        case 'expédiée':
        case 'colis reçu':
            return 'bg-info';

        case 'en attente de paiement':
        case 'retour demandé':
        case 'demande acceptée':
        case 'colis en attente de réception':
            return 'bg-warning text-dark'; // text-dark est nécessaire pour la lisibilité sur fond jaune

        case 'annulée':
        case 'échoué':
        case 'demande refusée':
            return 'bg-danger';

        default:
            return 'bg-secondary'; // Statut par défaut si inconnu
    }
}


// =========================================================================
// SECTION 6 : UTILITAIRES (VALIDATION & CONTACT)
// =========================================================================


/** 
 * Vérifie que le numéro de carte contient exactement 16 chiffres.
 * Les espaces sont ignorés pour permettre les formats saisis courants.
 */
function validateCardNumber(string $number): bool
{
    $number = str_replace(' ', '', $number);
    return (bool) preg_match('#^\d{16}$#', $number);
}


/** 
 * Vérifie que la date d’expiration (format MM/AA) est valide et non échue.
 * Utilise mktime pour construire un timestamp au dernier jour du mois à 23h59.
 */
function validateExpiryDate(string $exp): bool
{
    if (!preg_match('#^(0[1-9]|1[0-2])\/(\d{2})$#', $exp, $m)) {
        return false;
    }

    // Extraction explicite des composants
    $mois = (int) $m[1];           
    $annee = (int) $m[2] + 2000;   

    // Passage au mois suivant pour viser le dernier jour du mois courant
    $mois_suivant = $mois + 1;

    // Construction d’un timestamp à 23h59:59 du dernier jour du mois courant
    $stamp = mktime(23, 59, 59, $mois_suivant, 0, $annee);

    // Comparaison avec le timestamp actuel
    return $stamp >= time();
}

/**
 * Vérifie que le code de sécurité (CVC) contient 3 ou 4 chiffres.
 */
function validateCVC(string $cvc): bool
{
    return (bool) preg_match('#^\d{3,4}$#', $cvc);
}

/**
 * Enregistre un message de contact dans la base.
 * Accepte aussi bien des visiteurs que des clients connectés (id_client facultatif).
 */
function saveContactMessage(
    string $name,
    string $email,
    string $subject,
    string $message,
    ?int $clientId
): bool {
    $sql = 'INSERT INTO messages_contact
            (nom_visiteur, email_visiteur, sujet, message, id_client)
            VALUES (?,?,?,?,?)';
    return getPDO()->prepare($sql)->execute([
        $name,
        $email,
        $subject,
        $message,
        $clientId,
    ]);
}

// =========================================================================
// SECTION 7 : ESPACE ADMIN                                                *
// =========================================================================

// ---- 7.1 AUTHENTIFICATION ADMIN ----------------------------------------

/**
 * Vérifie les identifiants d’un administrateur à partir de son email.
 * Utilise une fonction maison (custom_verify) pour valider le mot de passe.
 * @param mixed $email
 * @param mixed $pwd
 */
function loginAdmin($email, $pwd)
{
    $stmt = getPDO()->prepare('SELECT * FROM administrateur WHERE email = ?');
    $stmt->execute(array($email));
    $admin = $stmt->fetch();

    if ($admin && custom_verify($pwd, $admin['password_hash'])) {
        unset($admin['password_hash']);
        return $admin;
    }
    return false;
}


/** Définit l’ID admin pour les triggers d’audit. */

/**
 * Déclare dynamiquement l’ID de l’admin connecté pour l’audit via les triggers SQL.
 * Cette variable est utilisée côté serveur (MySQL) pour historiser les actions.
 * @param PDO $pdo
 * @return void
 */
function setAuditAdminId(PDO $pdo): void
{
    $adminId = null;
    if (isset($_SESSION['admin']) && isset($_SESSION['admin']['id_admin'])) {
        $adminId = $_SESSION['admin']['id_admin'];
    }
    $pdo->prepare('SET @current_admin_id = :id')->execute([':id' => $adminId]);
}

// ---- 7.2 DASHBOARD ------------------------------------------------------

/** Retourne le nombre total de clients enregistrés dans la base.*/
function countClients(): int
{
    return (int) getPDO()->query('SELECT COUNT(*) FROM client')->fetchColumn();
}

/** Retourne le nombre total de produits, tous types confondus.*/
function countProducts(): int
{
    return (int) getPDO()->query('SELECT COUNT(*) FROM produit')->fetchColumn();
}

/**
 *  Compte les commandes en attente (statuts différents de 4 : livrée, et 5 : annulée).
 * @return int
 */
function countPendingOrders(): int
{
    return (int) getPDO()->query(
        'SELECT COUNT(*) FROM commande WHERE id_statut_commande NOT IN (4,5)'
    )->fetchColumn();
}

/** Compte les demandes de retour en attente (statuts différents de 6 : remboursement effectué, et 7 : cloturée). */
function countPendingReturns(): int
{
    return (int) getPDO()->query(
        'SELECT COUNT(*) FROM demande_retour WHERE id_statut_demande NOT IN (6,7)'
    )->fetchColumn();
}

// ---- 7.3 PRODUITS (ADMIN) ----------------------------------------------



/**
 *  Récupère tous les produits pour affichage dans l’interface admin.
 * Le nom affiché dépend du type : titre pour les livres, nom pour bougies et coffrets.
 */
function getAllProductsForAdmin(): array
{
    $sql = 'SELECT p.id_produit, p.type, p.stock, p.prix_ht,
                   COALESCE(l.titre, b.nom, c.nom) AS nom_produit
            FROM   produit p
            LEFT   JOIN livre   l ON l.id_produit = p.id_produit
            LEFT   JOIN bougie  b ON b.id_produit = p.id_produit
            LEFT   JOIN coffret c ON c.id_produit = p.id_produit
            ORDER  BY p.id_produit DESC';
    return getPDO()->query($sql)->fetchAll();
}

/**
 * Supprime un produit, sauf s'il est lié à une commande.
 * Retourne TRUE en cas de succès, ou un message d'erreur (string) en cas d'échec.
 */
function deleteProduct($id)
{
    $pdo = getPDO();
    setAuditAdminId($pdo); // traçabilité admin pour l’audit
    try {

        // Vérifie que le produit n’est pas utilisé dans des commandes.
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM commande_details WHERE id_produit = ?');
        $stmt->execute(array($id));
        if ($stmt->fetchColumn() > 0) {
            return "Suppression impossible : ce produit est inclus dans au moins une commande.";
        }

        // Suppression sécurisée par transaction (toutes les dépendances sont supprimées d'abord).
        $pdo->beginTransaction();
        $pdo->prepare('DELETE FROM livre_auteur WHERE id_produit = ?')->execute(array($id));
        $pdo->prepare('DELETE FROM livre_genre WHERE id_produit = ?')->execute(array($id));
        $pdo->prepare('DELETE FROM produit_tag WHERE id_produit = ?')->execute(array($id));
        $pdo->prepare('DELETE FROM notation_produit WHERE id_produit = ?')->execute(array($id));
        $pdo->prepare('DELETE FROM produit WHERE id_produit = ?')->execute(array($id));
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return "Une erreur technique est survenue lors de la suppression du produit.";
    }
}

// --- Listes de références pour les formulaires ---

/* Récupère tous les éditeurs pour alimenter une liste déroulante. */
function getAllEditeurs(): array
{
    return getPDO()->query('SELECT id_editeur, nom FROM editeur ORDER BY nom')->fetchAll();
}

/* Renvoie tous les auteurs, nom complet concaténé, pour affichage dans les formulaires. */
function getAllAuteurs(): array
{
    $sql = 'SELECT id_auteur, CONCAT(prenom, " ", nom) AS nom_complet
            FROM   auteur ORDER BY nom, prenom';
    return getPDO()->query($sql)->fetchAll();
}

/* Liste tous les genres disponibles, triés alphabétiquement. */
function getAllGenres(): array
{
    return getPDO()->query('SELECT id_genre, nom FROM genre ORDER BY nom')->fetchAll();
}

/* Liste des livres disponibles pour constituer un coffret. */
function getAllBooksForCoffretSelection(): array
{
    $sql = 'SELECT p.id_produit, l.titre
            FROM   produit p
            JOIN   livre l ON p.id_produit = l.id_produit
            ORDER  BY l.titre';
    return getPDO()->query($sql)->fetchAll();
}

/* Liste des bougies disponibles pour constituer un coffret. */
function getAllCandlesForCoffretSelection(): array
{
    $sql = 'SELECT p.id_produit, b.nom
            FROM   produit p
            JOIN   bougie b ON p.id_produit = b.id_produit
            ORDER  BY b.nom';
    return getPDO()->query($sql)->fetchAll();
}

/* Renvoie toutes les catégories de coffrets (libellé + ID). */
function getAllCoffretCategories(): array
{
    return getPDO()->query(
        'SELECT id_categorie_coffret, libelle
         FROM   categorie_coffret
         ORDER  BY libelle'
    )->fetchAll();
}


// ---- 7.4 COMMANDES (ADMIN) ---------------------------------------------

/**
 * Récupère toutes les commandes pour l'affichage dans l'interface d'administration.
 * @return array
 */
function getAllOrders()
{
    /* Joint les informations client et statut à chaque commande.
    Classement par date décroissante. */
    $sql = 'SELECT c.id_commande, c.date_commande, c.total_ttc,
                   CONCAT(cl.prenom, " ", cl.nom) AS nom_client,
                   c.id_statut_commande, s.libelle AS statut_libelle
            FROM   commande c
            JOIN   client cl ON c.id_client = cl.id_client
            JOIN   statut_commande s ON c.id_statut_commande = s.id_statut_commande
            ORDER  BY c.date_commande DESC';
    return getPDO()->query($sql)->fetchAll(); // tableau des commandes
}

/**
 * Récupère tous les statuts de commande possibles.
 * Cette fonction est indispensable pour les listes déroulantes de mise à jour.
 * @return array La liste des statuts.
 */
function getAllStatuts()
{
    $sql = 'SELECT id_statut_commande, libelle FROM statut_commande ORDER BY id_statut_commande';
    return getPDO()->query($sql)->fetchAll();
}

/**
 * Met à jour le statut d'une commande spécifique.
 */
function updateOrderStatus($pdo, $idCommande, $idStatut)
{
    $stmt = $pdo->prepare('UPDATE commande SET id_statut_commande = ? WHERE id_commande = ?');
    return $stmt->execute(array($idStatut, $idCommande));
}

/**
 * Récupère les détails complets d'une commande (client, adresse, produits, etc.) pour l'admin.
 * @param int $id L'ID de la commande.
 * @return array|false Les détails de la commande ou false si non trouvée.
 */
function getAdminOrderDetails($id)
{
    $pdo = getPDO();
    setAuditAdminId($pdo);

    $commande = array();

    /* ========== INFOS GÉNÉRALES + CLIENT + ADRESSE DE LIVRAISON ========== */

    $sqlCommande = 'SELECT c.*, s.libelle AS statut_libelle, cl.nom, cl.prenom, cl.email,
                           a.rue, a.code_postal, a.ville, a.pays
                    FROM commande c
                    JOIN statut_commande s ON c.id_statut_commande = s.id_statut_commande
                    JOIN client cl ON c.id_client = cl.id_client
                    JOIN adresse a ON c.id_adresse_livraison = a.id_adresse
                    WHERE c.id_commande = ?';
    $stmt = $pdo->prepare($sqlCommande);
    $stmt->execute(array($id));
    $commande = $stmt->fetch();

    if (!$commande) {
        return false;
    }

    /* ========== DÉTAILS PRODUITS ========== */
    $sqlDetails = 'SELECT cd.*, p.type, COALESCE(l.titre, b.nom, cf.nom) AS nom_produit
                   FROM commande_details cd
                   JOIN produit p ON cd.id_produit = p.id_produit
                   LEFT JOIN livre l ON p.id_produit = l.id_produit
                   LEFT JOIN bougie b ON p.id_produit = b.id_produit
                   LEFT JOIN coffret cf ON p.id_produit = cf.id_produit
                   WHERE cd.id_commande = ?';
    $stmtDetails = $pdo->prepare($sqlDetails);
    $stmtDetails->execute(array($id));
    $commande['produits'] = $stmtDetails->fetchAll();

    return $commande;
}

// ---- 7.5 UTILISATEURS (ADMIN) ------------------------------------------

/**
 * Renvoie la liste des clients avec les informations principales (tri du plus récent au plus ancien).
 * @return array
 */
function getAllClients()
{
    $sql = 'SELECT id_client, nom, prenom, email, date_creation
            FROM client ORDER BY date_creation DESC';
    return getPDO()->query($sql)->fetchAll();
}

/**
 * Supprime un client, sauf s'il est lié à une commande.
 * Retourne TRUE en cas de succès, ou un message d'erreur (string) en cas d'échec.
 */
function deleteClient($id)
{
    $pdo = getPDO();
    setAuditAdminId($pdo);
    try {

        // Vérification de l'existence d'un historique de commandes.
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM commande WHERE id_client = ?');
        $stmt->execute(array($id));
        if ($stmt->fetchColumn() > 0) {
            return "Suppression impossible : ce client a un historique de commandes.";
        }
        // Suppression autorisée car client non lié à des commandes.
        $pdo->prepare('DELETE FROM client WHERE id_client = ?')->execute(array($id));
        return true;
    } catch (PDOException $e) {
        return "Une erreur technique est survenue lors de la suppression du client.";
    }
}

/**
 * Promeut un client au rang d'administrateur.
 * Retourne TRUE en cas de succès, ou un message d'erreur (string) en cas d'échec.
 */
function promoteClientToAdmin($idClient)
{
    $pdo = getPDO();
    setAuditAdminId($pdo);

    /* Vérifie que le client n’a aucune commande dans la base de données */
    $stmtChk = $pdo->prepare('SELECT COUNT(*) FROM commande WHERE id_client = ?');
    $stmtChk->execute(array($idClient));
    if ((int) $stmtChk->fetchColumn() > 0) {
        return 'Promotion refusée : le client a un historique de commandes.';
    }

    /* Récupère l’email + hash du client (indispensable pour créer le compte admin). */
    $stmtCli = $pdo->prepare('SELECT email, password_hash FROM client WHERE id_client = ?');
    $stmtCli->execute(array($idClient));
    $cli = $stmtCli->fetch();
    if (!$cli) {
        return 'Client introuvable.';
    }

    /* Vérifie qu’aucun admin ne possède déjà cet email  */
    $stmtAdm = $pdo->prepare('SELECT id_admin FROM administrateur WHERE email = ?');
    $stmtAdm->execute(array($cli['email']));
    if ($stmtAdm->fetch()) {
        return 'Un administrateur avec cet email existe déjà.';
    }

    /* Transaction : insertion dans `administrateur` puis suppression du client. */
    try {
        $pdo->beginTransaction();
        $pdo->prepare('INSERT INTO administrateur (email, password_hash, role) VALUES (?,?,?)')
            ->execute(array($cli['email'], $cli['password_hash'], 'editeur'));
        $pdo->prepare('DELETE FROM client WHERE id_client = ?')->execute(array($idClient));
        $pdo->commit();
        return true; // succès
    } catch (Exception $e) {
        $pdo->rollBack();
        return "Une erreur technique est survenue lors de la promotion.";
    }
}

// ---- 7.6 MESSAGES CONTACT (ADMIN) --------------------------------------

/**
 * Récupère tous les messages de contact triés du plus récent au plus ancien.
 * @return array
 */
function getAllContactMessages()
{
    $sql = 'SELECT * FROM messages_contact ORDER BY date_envoi DESC';
    return getPDO()->query($sql)->fetchAll();
}


/**
 * Supprime un message de contact.
 * Retourne TRUE en cas de succès, ou un message d'erreur (string) en cas d'échec.
 */
function deleteContactMessage($id)
{
    try {
        $pdo = getPDO();
        setAuditAdminId($pdo); // trace l’admin pour l’audit

        $stmt = $pdo->prepare('DELETE FROM messages_contact WHERE id_message = ?');
        $stmt->execute(array($id));
        return $stmt->rowCount() > 0 ? true : "Le message à supprimer n'a pas été trouvé.";
    } catch (PDOException $e) {
        return "Une erreur technique est survenue lors de la suppression du message.";
    }
}


// ---- 7.7 RETOURS (ADMIN) ----------------------------------------------

/**
 * Liste complète des demandes de retour (vue admin)
 * @return array
 */
function getAllDemandesRetour()
{
    /* Sélectionne l’en-tête de chaque demande + email client + libellé statut,
       tri décroissant pour voir les plus récentes en premier. */
    $sql = 'SELECT d.*, c.email AS email_client, s.libelle AS statut_libelle
            FROM   demande_retour d
            JOIN   client c ON d.id_client = c.id_client
            JOIN   statut_demande s ON d.id_statut_demande = s.id_statut_demande
            ORDER  BY d.date_demande DESC';
    return getPDO()->query($sql)->fetchAll();
}


/**
 * Récupère tous les statuts possibles pour une demande de retour.
 * @return array
 */
function getReturnStatuts()
{
    return getPDO()->query('SELECT id_statut_demande, libelle FROM statut_demande ORDER BY id_statut_demande')->fetchAll();
}


/**
 * Met à jour le statut d'une demande de retour.
 */
function updateReturnStatus($pdo, $idDemande, $idStatut)
{
    $stmt = $pdo->prepare('UPDATE demande_retour SET id_statut_demande = ? WHERE id_demande = ?');
    return $stmt->execute(array($idStatut, $idDemande));
}


/**
 * Récupère le détail complet d’une demande de retour (vue admin).
 *
 * Étape 1  : on récupère l’en-tête de la demande ainsi que le client,
 *              le statut libellé et la date de la commande associée.
 * Étape 2  : si la demande existe, on ajoute la liste détaillée
 *           
 * @param int $id_demande ID de la demande recherchée
 * @return array|false Tableau associatif / False si introuvable
 */
function getReturnDetails($id_demande)
{
    $pdo = getPDO();

    /* =================== ÉTAPE 1 — EN-TÊTE =================== */
    $sqlMain = 'SELECT d.*, c.nom, c.prenom, c.email,
                       s.libelle AS statut_libelle, cmd.date_commande
                FROM demande_retour d
                JOIN client c ON d.id_client = c.id_client
                JOIN statut_demande s ON d.id_statut_demande = s.id_statut_demande
                JOIN commande cmd ON d.id_commande = cmd.id_commande
                WHERE d.id_demande = ?';
    $stmtMain = $pdo->prepare($sqlMain);
    $stmtMain->execute(array($id_demande));
    $det = $stmtMain->fetch(); // false si pas trouvé

    if (!$det) {
        return false; // Demande inexistante
    }

    /* =================== ÉTAPE 2 — PRODUITS ================== */

    $sqlProd = 'SELECT rp.*, p.type, COALESCE(l.titre, b.nom, cf.nom) AS nom_produit
                FROM retour_produit rp
                JOIN produit p ON rp.id_produit = p.id_produit
                LEFT JOIN livre l ON p.id_produit = l.id_produit
                LEFT JOIN bougie b ON p.id_produit = b.id_produit
                LEFT JOIN coffret cf ON p.id_produit = cf.id_produit
                WHERE rp.id_demande = ?';
    $stmtProd = $pdo->prepare($sqlProd);
    $stmtProd->execute(array($id_demande));
    $det['produits'] = $stmtProd->fetchAll();

    return $det;
}


// =========================================================================
// SECTION 8 : Tables de référence (Auteurs, Tags, Editeurs)
// =========================================================================


/**
 * Récupère tous les auteurs, triés alphabétiquement par nom puis prénom.
 */
function getAllAuteursAdmin()
{
    return getPDO()->query('SELECT id_auteur, prenom, nom, CONCAT(prenom, " ", nom) AS nom_complet FROM auteur ORDER BY nom, prenom')->fetchAll();
}


/**
 * Récupère toutes les informations d’un auteur à partir de sa clé primaire.
 */
function getAuteurById($id)
{
    $stmt = getPDO()->prepare('SELECT * FROM auteur WHERE id_auteur = ?');
    $stmt->execute(array($id));
    return $stmt->fetch();
}

/**
 * Ajoute un nouvel auteur dans la base.
 */
function createAuteur($data)
{
    $nom = isset($data['nom']) ? trim($data['nom']) : '';
    $prenom = isset($data['prenom']) ? trim($data['prenom']) : '';
    if ($nom == '' || $prenom == '')
        return false;
    $sql = 'INSERT INTO auteur (nom, prenom) VALUES (?, ?)';
    return getPDO()->prepare($sql)->execute(array($nom, $prenom));
}


/**
 * Modifie le nom et le prénom d’un auteur existant.
 */
function updateAuteur($data)
{
    $id_auteur = isset($data['id_auteur']) ? (int) $data['id_auteur'] : 0;
    $nom = isset($data['nom']) ? trim($data['nom']) : '';
    $prenom = isset($data['prenom']) ? trim($data['prenom']) : '';
    if ($id_auteur == 0 || $nom == '' || $prenom == '')
        return false;
    $sql = 'UPDATE auteur SET nom = ?, prenom = ? WHERE id_auteur = ?';
    return getPDO()->prepare($sql)->execute(array($nom, $prenom, $id_auteur));
}
/**
 * Supprime un auteur **uniquement s’il n’est lié à aucun livre**.
 * Objectif : préserver l’intégrité référentielle (table `livre_auteur`).
 */
function deleteAuteur($id)
{
    $pdo = getPDO();
    setAuditAdminId($pdo); // inscrit l’ID admin pour les triggers d’audit
    try {

        /* A. Vérifier si l’auteur est encore utilisé */
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM livre_auteur WHERE id_auteur = ?');
        $stmt->execute(array($id));
        if ($stmt->fetchColumn() > 0) {
            return "Suppression impossible : cet auteur est lié à un ou plusieurs livres.";
        }
        /* B. Suppression définitive */
        $pdo->prepare('DELETE FROM auteur WHERE id_auteur = ?')->execute(array($id));
        return true;
    } catch (PDOException $e) {
        return "Une erreur technique est survenue lors de la suppression de l'auteur.";
    }
}

// --- FONCTIONS POUR LES TAGS ---


/**
 * Renvoie l’ensemble des tags existants, triés par ordre alpha.
 */
function getAllTagsAdmin()
{
    return getPDO()->query('SELECT * FROM tag ORDER BY nom_tag')->fetchAll();
}

/**
 * Retourne un tag précis à partir de son identifiant.
 */
function getTagById($id)
{
    $stmt = getPDO()->prepare('SELECT * FROM tag WHERE id_tag = ?');
    $stmt->execute(array($id));
    return $stmt->fetch();
}

/**
 * Ajoute un nouveau tag dans la table `tag`.
 */
function createTag($data)
{
    $nom_tag = isset($data['nom_tag']) ? trim($data['nom_tag']) : '';
    if ($nom_tag == '')
        return false;
    return getPDO()->prepare('INSERT INTO tag (nom_tag) VALUES (?)')->execute(array($nom_tag));
}

/**
 * Met à jour le libellé  d’un tag existant.
 */
function updateTag($data)
{

    // 1) Validation et nettoyage des entrées 
    $id_tag = isset($data['id_tag']) ? (int) $data['id_tag'] : 0;
    $nom_tag = isset($data['nom_tag']) ? trim($data['nom_tag']) : '';

    if ($id_tag == 0 || $nom_tag == '')
        return false;

    //2) Requête UPDATE paramétrée
    $sql = 'UPDATE tag SET nom_tag = ? WHERE id_tag = ?';
    return getPDO()->prepare($sql)->execute(array($nom_tag, $id_tag));
}


/**
 * Supprime un tag uniquement s’il n’est rattaché à aucun produit.
 * Cela évite de laisser des références orphelines dans `produit_tag`.
 */
function deleteTag($id)
{
    $pdo = getPDO(); // Connexion BDD
    setAuditAdminId($pdo); // Renseigne l’ID admin (triggers d’audit)
    try {

        // A. Vérification d’intégrité référentielle
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM produit_tag WHERE id_tag = ?');
        $stmt->execute(array($id));
        if ($stmt->fetchColumn() > 0) {
            return "Suppression impossible : ce tag est utilisé par un ou plusieurs produits.";
        }

        // B. Suppression effective
        $pdo->prepare('DELETE FROM tag WHERE id_tag = ?')->execute(array($id));
        return true;
    } catch (PDOException $e) {

        //Gestion d'erreur SQL
        return "Une erreur technique est survenue lors de la suppression du tag.";
    }
}

// --- FONCTIONS POUR LES ÉDITEURS ---

/**
 * Récupère tous les éditeurs existants triés alphabétiquement par nom
 */
function getAllEditeursAdmin()
{
    return getPDO()->query('SELECT * FROM editeur ORDER BY nom')->fetchAll();
}


/**
 * Récupère un éditeur précis grâce à son identifiant primaire.
 */
function getEditeurById($id)
{
    $stmt = getPDO()->prepare('SELECT * FROM editeur WHERE id_editeur = ?');
    $stmt->execute(array($id));
    return $stmt->fetch();
}


/**
 * Ajoute un nouvel éditeur dans la base.
 */
function createEditeur($data)
{
    $nom = isset($data['nom']) ? trim($data['nom']) : '';
    if ($nom == '')
        return false;
    return getPDO()->prepare('INSERT INTO editeur (nom) VALUES (?)')->execute(array($nom));
}


/**
 * Modifie le nom d’un éditeur existant.
 */
function updateEditeur($data)
{
    $id_editeur = isset($data['id_editeur']) ? (int) $data['id_editeur'] : 0;
    $nom = isset($data['nom']) ? trim($data['nom']) : '';
    if ($id_editeur == 0 || $nom == '')
        return false;
    $sql = 'UPDATE editeur SET nom = ? WHERE id_editeur = ?';
    return getPDO()->prepare($sql)->execute(array($nom, $id_editeur));
}

/**
 * Supprime un éditeur, **sauf** s’il est référencé dans la table `livre`.
 * Cela évite de violer le principe d'intégrité référentielle (FK id_editeur).
 */
function deleteEditeur($id)
{
    $pdo = getPDO();
    setAuditAdminId($pdo);
    try {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM livre WHERE id_editeur = ?');
        $stmt->execute(array($id));
        if ($stmt->fetchColumn() > 0) {
            return "Suppression impossible : cet éditeur est lié à un ou plusieurs livres.";
        }

        $pdo->prepare('DELETE FROM editeur WHERE id_editeur = ?')->execute(array($id));
        return true;
    } catch (PDOException $e) {
        return "Une erreur technique est survenue lors de la suppression de l'éditeur.";
    }
}


// =========================================================================
// SECTION 9 : FONCTIONS DE CRÉATION/MISE À JOUR PRODUIT
// =========================================================================


/**
 * Crée un nouveau produit (livre, bougie ou coffret) et enregistre
 * toutes les relations associées : genres, auteurs, tags, etc.
 *
 * Le code est structuré en 4 grandes phases :
 *   1) Insertion du produit « racine » dans la table `produit`
 *   2) Insertion dans la table spécifique (livre / bougie / coffret)
 *   3) Insertion des relations N:N (genres, auteurs, tags…)
 *   4) Commit ou Rollback selon que tout s’est bien passé ou non
 * @param array $data  Toutes les valeurs issues du formulaire admin.
 *    @return bool        TRUE si la création réussit, FALSE sinon.
 */
function createProduct($data)
{
    /* ------------------------------------------------------------------
       1) Préparation : connexion + ID d’admin pour l’audit admin
       ------------------------------------------------------------------ */
    $pdo = getPDO();
    setAuditAdminId($pdo);

    try {

        /* ================================================================
         * 2) DÉBUT TRANSACTION
         * ================================================================ */
        $pdo->beginTransaction();

        /* --------------------------------------------------------------
          2.a) Insertion générique dans la table `produit`
          -------------------------------------------------------------- */
        $sqlProduit = 'INSERT INTO produit (type, prix_ht, tva_rate, stock, image_url) VALUES (?, ?, ?, ?, ?)';

        $stmtProduit = $pdo->prepare($sqlProduit);
        $stmtProduit->execute(array(
            $data['type'],
            $data['prix_ht'],
            $data['tva_rate'],
            $data['stock'],
            isset($data['image_url']) ? $data['image_url'] : null
        ));
        $id_produit = $pdo->lastInsertId();

        /* --------------------------------------------------------------
          2.b) Insertion dans la table spécifique, selon le type
          -------------------------------------------------------------- */
        switch ($data['type']) {
            /* ===== LIVRE ============================================= */
            case 'livre':
                // Nettoyage des foreign keys potentielles
                $id_editeur_nettoye = (isset($data['id_editeur']) && $data['id_editeur'] != '') ? (int) $data['id_editeur'] : null;
                $annee_nettoye = (isset($data['annee_publication']) && $data['annee_publication'] != '') ? (int) $data['annee_publication'] : null;
                $nb_pages_nettoye = (isset($data['nb_pages']) && $data['nb_pages'] != '') ? (int) $data['nb_pages'] : null;

                // Table `livre`

                $pdo->prepare('INSERT INTO livre (id_produit, titre, isbn, resume, etat, annee_publication, nb_pages, id_editeur) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')
                    ->execute(array($id_produit, $data['titre'], $data['isbn'], $data['resume'], $data['etat'], $annee_nettoye, $nb_pages_nettoye, $id_editeur_nettoye));

                // Relations N:N : genres
                if (isset($data['genres']) && is_array($data['genres'])) {
                    foreach ($data['genres'] as $id) {
                        $pdo->prepare('INSERT INTO livre_genre (id_produit, id_genre) VALUES (?,?)')->execute(array($id_produit, $id));
                    }
                }

                // Relations N:N : auteurs
                if (isset($data['auteurs']) && is_array($data['auteurs'])) {
                    foreach ($data['auteurs'] as $id) {
                        $pdo->prepare('INSERT INTO livre_auteur (id_produit, id_auteur) VALUES (?,?)')->execute(array($id_produit, $id));
                    }
                }
                break;

            /* ===== BOUGIE =========================================== */
            case 'bougie':
                $pdo->prepare('INSERT INTO bougie (id_produit, nom, description, parfum, duree_combustion, poids) VALUES (?, ?, ?, ?, ?, ?)')
                    ->execute(array($id_produit, $data['nom'], $data['description'], $data['parfum'], $data['duree_combustion'], $data['poids']));
                break;
            /* ===== COFFRET ========================================== */
            case 'coffret':
                $id_livre_nettoye = (isset($data['id_produit_livre']) && $data['id_produit_livre'] != '') ? (int) $data['id_produit_livre'] : null;
                $id_bougie_nettoye = (isset($data['id_produit_bougie']) && $data['id_produit_bougie'] != '') ? (int) $data['id_produit_bougie'] : null;
                $id_categorie_nettoye = (isset($data['id_categorie_coffret']) && $data['id_categorie_coffret'] != '') ? (int) $data['id_categorie_coffret'] : null;

                $pdo->prepare('INSERT INTO coffret (id_produit, nom, description, id_produit_livre, id_produit_bougie, id_categorie_coffret) VALUES (?, ?, ?, ?, ?, ?)')
                    ->execute(array($id_produit, $data['nom'], $data['description'], $id_livre_nettoye, $id_bougie_nettoye, $id_categorie_nettoye));
                break;
        }

        /* --------------------------------------------------------------
           2.c) Tags d’ambiance (relation N:N générique)
           -------------------------------------------------------------- */

        if (isset($data['tags']) && is_array($data['tags'])) {
            foreach ($data['tags'] as $id_tag) {
                $pdo->prepare('INSERT INTO produit_tag (id_produit, id_tag) VALUES (?,?)')->execute(array($id_produit, $id_tag));
            }
        }

        /* --------------------------------------------------------------
          3) Fin : on valide la transaction
          -------------------------------------------------------------- */

        $pdo->commit();
        return true;
    } catch (Exception $e) {

        /* --------------------------------------------------------------
           Si un seul problème → on annule les insertions
           -------------------------------------------------------------- */
        $pdo->rollBack();
        return false;
    }
}

/**
 * Met à jour un produit existant et ses relations annexes.
 */
function updateProduct($data)
{

    /* ------------------------------------------------------------------
       1) Préambule : connexion BDD + contexte admin pour l’audit
       ------------------------------------------------------------------ */
    $pdo = getPDO();
    setAuditAdminId($pdo);  // Stocke l’ID admin dans une variable SQL

    /* Sécurité : on vérifie qu’un id_produit valide est fourni. */

    $id_produit = isset($data['id_produit']) ? (int) $data['id_produit'] : 0;
    if ($id_produit === 0) {
        return false;
    }

    try {
        /* ================================================================
         * 2) DÉBUT TRANSACTION
         *    Si une étape échoue, on revert la base pour qu’elle reste cohérente.
         * ================================================================ */
        $pdo->beginTransaction();

        /* ----------------------------------------------------------------
          2.a) Table « produit »  (données communes à tous les types)
          ---------------------------------------------------------------- */

        $pdo->prepare('UPDATE produit SET prix_ht = ?, tva_rate = ?, stock = ?, image_url = ? WHERE id_produit = ?')
            ->execute(array($data['prix_ht'], $data['tva_rate'], $data['stock'], isset($data['image_url']) ? $data['image_url'] : null, $id_produit));

        /* ----------------------------------------------------------------
       2.b) Tables spécifiques selon le type
       ---------------------------------------------------------------- */
        switch ($data['type']) {
            /* ===== LIVRE ================================================= */
            case 'livre':
                // On nettoie d’abord les clés étrangères qui peuvent être vides.
                $id_editeur_nettoye = (isset($data['id_editeur']) && $data['id_editeur'] != '') ? (int) $data['id_editeur'] : null;
                $annee_nettoye = (isset($data['annee_publication']) && $data['annee_publication'] != '') ? (int) $data['annee_publication'] : null;
                $nb_pages_nettoye = (isset($data['nb_pages']) && $data['nb_pages'] != '') ? (int) $data['nb_pages'] : null;

                // Mise à jour de la table « livre »
                $pdo->prepare('UPDATE livre SET titre = ?, isbn = ?, resume = ?, etat = ?, annee_publication = ?, nb_pages = ?, id_editeur = ? WHERE id_produit = ?')
                    ->execute(array($data['titre'], $data['isbn'], $data['resume'], $data['etat'], $annee_nettoye, $nb_pages_nettoye, $id_editeur_nettoye, $id_produit));

                /* --- Relations N:N (genres & auteurs) ------------------- */
                // On supprime les anciens liens puis on ajoute la sélection courante
                $pdo->prepare('DELETE FROM livre_genre WHERE id_produit = ?')->execute(array($id_produit));
                if (isset($data['genres']) && is_array($data['genres'])) {
                    foreach ($data['genres'] as $id) {
                        $pdo->prepare('INSERT INTO livre_genre (id_produit, id_genre) VALUES (?,?)')->execute(array($id_produit, $id));
                    }
                }

                $pdo->prepare('DELETE FROM livre_auteur WHERE id_produit = ?')->execute(array($id_produit));
                if (isset($data['auteurs']) && is_array($data['auteurs'])) {
                    foreach ($data['auteurs'] as $id) {
                        $pdo->prepare('INSERT INTO livre_auteur (id_produit, id_auteur) VALUES (?,?)')->execute(array($id_produit, $id));
                    }
                }
                break;
            /* ===== BOUGIE =============================================== */
            case 'bougie':
                $pdo->prepare('UPDATE bougie SET nom = ?, description = ?, parfum = ?, duree_combustion = ?, poids = ? WHERE id_produit = ?')
                    ->execute(array($data['nom'], $data['description'], $data['parfum'], $data['duree_combustion'], $data['poids'], $id_produit));
                break;
            /* ===== COFFRET ============================================== */
            case 'coffret':
                $id_livre_nettoye = (isset($data['id_produit_livre']) && $data['id_produit_livre'] != '') ? (int) $data['id_produit_livre'] : null;
                $id_bougie_nettoye = (isset($data['id_produit_bougie']) && $data['id_produit_bougie'] != '') ? (int) $data['id_produit_bougie'] : null;
                $id_categorie_nettoye = (isset($data['id_categorie_coffret']) && $data['id_categorie_coffret'] != '') ? (int) $data['id_categorie_coffret'] : null;

                $pdo->prepare('UPDATE coffret SET nom = ?, description = ?, id_produit_livre = ?, id_produit_bougie = ?, id_categorie_coffret = ? WHERE id_produit = ?')
                    ->execute(array($data['nom'], $data['description'], $id_livre_nettoye, $id_bougie_nettoye, $id_categorie_nettoye, $id_produit));
                break;
        }

        /* ----------------------------------------------------------------
          2.c) Tags d’ambiance (relation N:N générique)
          ---------------------------------------------------------------- */

        $pdo->prepare('DELETE FROM produit_tag WHERE id_produit = ?')->execute(array($id_produit));
        if (isset($data['tags']) && is_array($data['tags'])) {
            foreach ($data['tags'] as $id_tag) {
                $pdo->prepare('INSERT INTO produit_tag (id_produit, id_tag) VALUES (?,?)')->execute(array($id_produit, $id_tag));
            }
        }

        /* ----------------------------------------------------------------
          3) Fin de transaction --> COMMIT
          ---------------------------------------------------------------- */

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

















// --- Fonctions utilitaires pour la base de données (fonctions.php) ---

/**
 * Fonction générique pour récupérer une liste d'IDs depuis une table de liaison (many-to-many).
 *
 * @param PDO    $pdo         L'objet de connexion.
 * @param string $tableName   Le nom de la table de liaison (ex: 'produit_tag').
 * @param string $idColumn    Le nom de la colonne d'ID à récupérer (ex: 'id_tag').
 * @param int    $relatedId   L'ID de l'entité principale (ex: l'id du produit).
 *
 * @return array La liste des IDs trouvés.
 */
function getRelatedIds(PDO $pdo, string $tableName, string $idColumn, int $relatedId): array
{
 
    $sql = "SELECT " . $pdo->quote($idColumn) . " FROM " . $pdo->quote($tableName) . " WHERE id_produit = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$relatedId]);
    
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


/**
 * Récupère les IDs des tags associés à un produit.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $idProduit L'ID du produit.
 * @return array La liste des IDs de tags.
 */
function getProductTagIds(PDO $pdo, int $idProduit): array 
{
    return getRelatedIds($pdo, 'produit_tag', 'id_tag', $idProduit);
}

/**
 * Récupère les IDs des auteurs associés à un livre.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $idProduit L'ID du produit (livre).
 * @return array La liste des IDs d'auteurs.
 */
function getProductAuthorIds(PDO $pdo, int $idProduit): array 
{
    return getRelatedIds($pdo, 'livre_auteur', 'id_auteur', $idProduit);
}

/**
 * Valide le format d'une adresse email en utilisant une expression régulière.
 * Elle impose une structure 'local-part@domain-part.tld'.
 *
 * @param string $email L'adresse email à valider.
 * @return bool TRUE si le format de l'email est valide, FALSE sinon.
 */
function validate_email(string $email): bool
{
    $pattern = '#^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$#';

    // preg_match retourne 1 si le pattern est trouvé, 0 sinon, et false en cas d'erreur.
    return preg_match($pattern, $email) === 1;
}

/**
 * Transforme une date SQL 'YYYY-MM-DD HH:MM:SS' en format français 'DD/MM/YYYY'.
 * @param string $date_mysql La date au format BDD.
 * @return string La date au format français.
 */
function format_date($date_mysql)
{
    if (strlen($date_mysql) >= 10) {
        $annee = substr($date_mysql, 0, 4);
        $mois = substr($date_mysql, 5, 2);
        $jour = substr($date_mysql, 8, 2);
        return $jour . '/' . $mois . '/' . $annee;
    }
    return ''; // Retourne une chaîne vide si le format est incorrect.
}
?>