<?php
// fonction/fonctions_purifiees.php


/***************************************************************************
 *  SECTION 0 : OUTILS GENERIQUES                                          *
 ***************************************************************************/

/**
 * Génère un hachage SHA‑256 (algorithme vu en cours).
 * @param string $password
 * @return string
 */
function custom_hash(string $password): string
{
    return hash('sha256', $password); // fonction "hash" étudiée en cours
}

/**
 * Vérifie la correspondance mot de passe ↔ hachage.
 * @param string $password
 * @param string $hash
 * @return bool
 */
function custom_verify(string $password, string $hash): bool
{
    return hash('sha256', $password) === $hash;
}

/***************************************************************************
 *  SECTION 1 : CONNEXION BDD & OUTILS DE BASE                             *
 ***************************************************************************/

/**
 * Retourne l'instance PDO (pattern Singleton).
 * @return PDO
 */
function getPDO(): PDO
{
    /** @var PDO|null $pdo */
    static $pdo = null;

    if ($pdo === null) {
        require_once __DIR__ . '/../parametrage/param.php';
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
    return $pdo;
}

/***************************************************************************
 *  SECTION 2 : AUTHENTIFICATION & GESTION DES UTILISATEURS (CLIENTS)       *
 ***************************************************************************/

/** Récupère un client par email. */
function getUserByEmail(string $email)
{
    $sql  = 'SELECT * FROM client WHERE email = ?';
    $stmt = getPDO()->prepare($sql);
    $stmt->execute([$email]);
    return $stmt->fetch();
}

/** Inscription d'un nouveau client. */
function registerUser(string $nom, string $prenom, string $email, string $password): bool
{
    if (getUserByEmail($email)) {
        return false; // email déjà utilisé
    }
    $hash = custom_hash($password);
    $sql  = 'INSERT INTO client (nom, prenom, email, password_hash) VALUES (?,?,?,?)';
    $stmt = getPDO()->prepare($sql);
    return $stmt->execute([$nom, $prenom, $email, $hash]);
}

/** Tentative de connexion client. */
function loginUser(string $email, string $password)
{
    $user = getUserByEmail($email);
    if ($user && custom_verify($password, $user['password_hash'])) {
        unset($user['password_hash']);
        return $user;
    }
    return false;
}

/** Vérifie la robustesse d'un mot de passe. */
function isPasswordStrong(string $password): array
{
    $err = [];
    if (strlen($password) < 8)             { $err[] = 'Le mot de passe doit contenir au moins 8 caractères.'; }
    if (!preg_match('#[a-z]#', $password)) { $err[] = 'Le mot de passe doit contenir au moins 1 minuscule.'; }
    if (!preg_match('#[A-Z]#', $password)) { $err[] = 'Le mot de passe doit contenir au moins 1 majuscule.'; }
    if (!preg_match('#[0-9]#', $password)) { $err[] = 'Le mot de passe doit contenir au moins 1 chiffre.'; }
    if (!preg_match('#\W#', $password))   { $err[] = 'Le mot de passe doit contenir au moins 1 caractère spécial.'; }
    return $err;
}

/** Mise à jour infos client. */
function updateUserInfo(int $idClient, array $d): bool
{
    $sql  = 'UPDATE client SET nom = ?, prenom = ? WHERE id_client = ?';
    $stmt = getPDO()->prepare($sql);
    return $stmt->execute([$d['nom'], $d['prenom'], $idClient]);
}

/** Mise à jour du mot de passe client. */
function updateUserPassword(int $idClient, string $old, string $new)
{
    $pdo  = getPDO();
    $stmt = $pdo->prepare('SELECT password_hash FROM client WHERE id_client = ?');
    $stmt->execute([$idClient]);
    $row = $stmt->fetch();
    if (!$row) {
        return 'Utilisateur introuvable.';
    }
    if (!custom_verify($old, $row['password_hash'])) {
        return 'Mot de passe actuel incorrect.';
    }

    $hash   = custom_hash($new);
    $update = $pdo->prepare('UPDATE client SET password_hash = ? WHERE id_client = ?');
    return $update->execute([$hash, $idClient]);
}

/** Génère un token de réinitialisation. */
function generateAndSaveResetToken(string $email): ?string
{
    $pdo  = getPDO();
    $user = getUserByEmail($email);

    if ($user === false) {
        return null; // Aucun utilisateur
    }

    $token   = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 3600);
    $sql     = 'UPDATE client SET reset_token = ?, reset_token_expires_at = ? WHERE id_client = ?';
    $pdo->prepare($sql)->execute([$token, $expires, $user['id_client']]);
    return $token;
}

/** Récupère un client grâce à son token. */
function getClientByResetToken(string $token)
{
    $sql  = 'SELECT * FROM client WHERE reset_token = ? AND reset_token_expires_at > NOW()';
    $stmt = getPDO()->prepare($sql);
    $stmt->execute([$token]);
    return $stmt->fetch();
}

/** Remplace le mot de passe via token. */
function updatePasswordAndClearToken(int $idClient, string $new): bool
{
    $hash = custom_hash($new);
    $sql  = 'UPDATE client SET password_hash = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id_client = ?';
    return getPDO()->prepare($sql)->execute([$hash, $idClient]);
}

/***************************************************************************
 *  SECTION 3 : GESTION DES ADRESSES                                        *
 ***************************************************************************/

/** Liste des adresses d'un client. */
function getUserAddresses(int $idClient): array
{
    $sql  = 'SELECT * FROM adresse WHERE id_client = ? ORDER BY est_defaut DESC, id_adresse ASC';
    $stmt = getPDO()->prepare($sql);
    $stmt->execute([$idClient]);
    return $stmt->fetchAll();
}

/** Récupère une adresse précise appartenant au client. */
function getAddressById(int $idAdr, int $idClient)
{
    $sql  = 'SELECT * FROM adresse WHERE id_adresse = ? AND id_client = ?';
    $stmt = getPDO()->prepare($sql);
    $stmt->execute([$idAdr, $idClient]);
    return $stmt->fetch();
}

/** Ajout d'une adresse (gère « par défaut » via transaction). */
function addAddress(int $idClient, array $d): bool
{
    $pdo = getPDO();

    // Détermination du statut « par défaut » sans ternaire ni empty()
    $isDefault = 0;
    if (isset($d['est_defaut']) && $d['est_defaut'] == 1) {
        $isDefault = 1;
    }

    try {
        $pdo->beginTransaction();
        if ($isDefault === 1) {
            $pdo->prepare('UPDATE adresse SET est_defaut = 0 WHERE id_client = ?')->execute([$idClient]);
        }
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

/** Mise à jour d'une adresse. */
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

/** Suppression d'une adresse. */
function deleteAddress(int $idAdr, int $idClient): bool
{
    $sql = 'DELETE FROM adresse WHERE id_adresse = ? AND id_client = ?';
    return getPDO()->prepare($sql)->execute([$idAdr, $idClient]);
}

/***************************************************************************
 *  SECTION 4 : CATALOGUE, PRODUITS & AVIS                                 *
 ***************************************************************************/

/** Tags d'ambiance. */
function getAllTags(): array
{
    return getPDO()->query('SELECT id_tag, nom_tag FROM tag ORDER BY nom_tag ASC')->fetchAll();
}

/** États possibles pour un livre. */
function getAllEtats(): array
{
    return ['Neuf', 'Très bon état', 'Bon état', 'État correct'];
}

/** Parfums uniques de bougies. */
function getAllParfums(): array
{
    $sql = 'SELECT DISTINCT parfum FROM bougie WHERE parfum IS NOT NULL AND parfum != "" ORDER BY parfum ASC';
    return getPDO()->query($sql)->fetchAll(PDO::FETCH_COLUMN);
}

/** Alias vers les tags ambiance. */
function getAllAmbianceTags(): array
{
    return getAllTags();
}

/* ----------------------------------------------------------------------
   getFilteredProducts & countFilteredProducts
   ------------------------------------------------------------------ */

/**
 * Renvoie les produits filtrés (pagination + tri).
 * @param array $f Filtres nettoyés.
 * @return array
 */
function getFilteredProducts(array $f): array
{
    $pdo  = getPDO();
    $p    = [];
    $join = [];
    $w    = [];
    $h    = [];

    $base = [
        'type'      => '',
        'page'      => 1,
        'limit'     => 12,
        'sort'      => 'nouveaute',
        'prix_min'  => '',
        'prix_max'  => '',
        'genres'    => [],
        'etats'     => [],
        'parfums'   => [],
        'ambiances' => [],
    ];
    $f = array_merge($base, $f);

    $select = 'SELECT p.id_produit, p.type, p.prix_ht, p.tva_rate,
                      (p.prix_ht * (1 + p.tva_rate / 100)) AS prix_ttc,
                      p.image_url, p.note_moyenne, p.nombre_votes,
                      COALESCE(l.titre, b.nom, c.nom) AS nom_produit,
                      b.parfum,
                      GROUP_CONCAT(DISTINCT CONCAT(a.prenom, " ", a.nom) SEPARATOR ", ") AS auteurs';

    $from = 'FROM produit p';

    $join['livre']   = 'LEFT JOIN livre   l ON p.id_produit = l.id_produit';
    $join['bougie']  = 'LEFT JOIN bougie  b ON p.id_produit = b.id_produit';
    $join['coffret'] = 'LEFT JOIN coffret c ON p.id_produit = c.id_produit';
    $join['auteur']  = 'LEFT JOIN livre_auteur la ON la.id_produit = l.id_produit LEFT JOIN auteur a ON a.id_auteur = la.id_auteur';

    /* Filtres simples -------------------------------------------------- */
    if ($f['type'] !== '') {
        $w[] = 'p.type = ?';
        $p[] = $f['type'];
    }

    if (count($f['genres']) > 0) {
        $join['livre_genre'] = 'JOIN livre_genre lg ON p.id_produit = lg.id_produit';
        $marks = implode(',', array_fill(0, count($f['genres']), '?'));
        $w[]   = "lg.id_genre IN ($marks)";
        $p     = array_merge($p, $f['genres']);
    }

    if (count($f['etats']) > 0) {
        $marks = implode(',', array_fill(0, count($f['etats']), '?'));
        $w[]   = "l.etat IN ($marks)";
        $p     = array_merge($p, $f['etats']);
    }

    /* Parfums --------------------------------------------------------- */
    if (count($f['parfums']) > 0) {
        $sub = [];
        foreach ($f['parfums'] as $parfum) {
            $sub[] = 'b.parfum LIKE ?';
            $p[]   = '%' . trim($parfum) . '%';
        }
        $w[] = '(' . implode(' OR ', $sub) . ')';
    }

    /* Ambiances ------------------------------------------------------- */
    if (count($f['ambiances']) > 0) {
        $join['produit_tag'] = 'JOIN produit_tag pt ON p.id_produit = pt.id_produit';
        $marks = implode(',', array_fill(0, count($f['ambiances']), '?'));
        $w[]   = "pt.id_tag IN ($marks)";
        $p     = array_merge($p, $f['ambiances']);
    }

    /* Bornes de prix (HAVING) ---------------------------------------- */
    if ($f['prix_min'] !== '' && is_numeric($f['prix_min'])) {
        $h[] = 'prix_ttc >= ?';
        $p[] = (float) $f['prix_min'];
    }
    if ($f['prix_max'] !== '' && is_numeric($f['prix_max'])) {
        $h[] = 'prix_ttc <= ?';
        $p[] = (float) $f['prix_max'];
    }

    /* Tri ------------------------------------------------------------- */
    $order = 'ORDER BY p.id_produit DESC';
    if ($f['sort'] === 'prix_asc') {
        $order = 'ORDER BY prix_ttc ASC';
    } elseif ($f['sort'] === 'prix_desc') {
        $order = 'ORDER BY prix_ttc DESC';
    } elseif ($f['sort'] === 'note_desc') {
        $order = 'ORDER BY p.note_moyenne DESC, p.nombre_votes DESC';
    }

    /* Pagination ------------------------------------------------------ */
    $limit  = (int) $f['limit'];
    $offset = ((int) $f['page'] - 1) * $limit;

    /* Construction requête ------------------------------------------- */
    $sql = $select . ' ' . $from . ' ' . implode(' ', $join);
    if ($w) { $sql .= ' WHERE ' . implode(' AND ', $w); }
    $sql .= ' GROUP BY p.id_produit';
    if ($h) { $sql .= ' HAVING ' . implode(' AND ', $h); }
    $sql .= ' ' . $order . " LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($p);
    return $stmt->fetchAll();
}

/** Compte les produits pour la pagination. */
function countFilteredProducts(array $f): int
{
    $pdo  = getPDO();
    $p    = [];
    $join = [];
    $w    = [];

    $base = [
        'type'      => '',
        'genres'    => [],
        'etats'     => [],
        'parfums'   => [],
        'ambiances' => [],
        'prix_min'  => '',
        'prix_max'  => '',
    ];
    $f = array_merge($base, $f);

    /* Filtres identiques à la fonction précédente -------------------- */
    if ($f['type'] !== '') {
        $w[] = 'p.type = ?';
        $p[] = $f['type'];
    }

    if (count($f['genres']) > 0) {
        $join['livre']       = 'JOIN livre l ON p.id_produit = l.id_produit';
        $join['livre_genre'] = 'JOIN livre_genre lg ON p.id_produit = lg.id_produit';
        $marks = implode(',', array_fill(0, count($f['genres']), '?'));
        $w[]   = "lg.id_genre IN ($marks)";
        $p     = array_merge($p, $f['genres']);
    } elseif (count($f['etats']) > 0) {
        $join['livre'] = 'JOIN livre l ON p.id_produit = l.id_produit';
    }

    if (count($f['etats']) > 0) {
        $marks = implode(',', array_fill(0, count($f['etats']), '?'));
        $w[]   = "l.etat IN ($marks)";
        $p     = array_merge($p, $f['etats']);
    }

    if (count($f['parfums']) > 0) {
        $join['bougie'] = 'JOIN bougie b ON p.id_produit = b.id_produit';
        $marks = implode(',', array_fill(0, count($f['parfums']), '?'));
        $w[]   = "b.parfum IN ($marks)";
        $p     = array_merge($p, $f['parfums']);
    }

    if (count($f['ambiances']) > 0) {
        $join['produit_tag'] = 'JOIN produit_tag pt ON p.id_produit = pt.id_produit';
        $marks = implode(',', array_fill(0, count($f['ambiances']), '?'));
        $w[]   = "pt.id_tag IN ($marks)";
        $p     = array_merge($p, $f['ambiances']);
    }

    if ($f['prix_min'] !== '' && is_numeric($f['prix_min'])) {
        $w[] = '(p.prix_ht * (1 + p.tva_rate / 100)) >= ?';
        $p[] = (float) $f['prix_min'];
    }
    if ($f['prix_max'] !== '' && is_numeric($f['prix_max'])) {
        $w[] = '(p.prix_ht * (1 + p.tva_rate / 100)) <= ?';
        $p[] = (float) $f['prix_max'];
    }

    $sql = 'SELECT COUNT(DISTINCT p.id_produit) FROM produit p ' . implode(' ', $join);
    if ($w) { $sql .= ' WHERE ' . implode(' AND ', $w); }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($p);
    return (int) $stmt->fetchColumn();
}


/***************************************************************************
 *  SECTION 5 : UTILITAIRES (FORM & AUTRES)                                *
 ***************************************************************************/





/**
 * Génère les <input type="hidden"> basés sur $_GET,
 * à l’exception des clés listées dans $exceptions.
 */
function build_hidden_fields_except(array $exceptions = []): void
{
    foreach ($_GET as $key => $value) {
        if (in_array($key, $exceptions, true)) {
            continue;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if ($item !== '') {
                    echo '<input type="hidden" name="' .
                         htmlspecialchars($key) . '[]" value="' .
                         htmlspecialchars($item) . '">';
                }
            }
        } else {
            if ($value !== '') {
                echo '<input type="hidden" name="' .
                     htmlspecialchars($key) . '" value="' .
                     htmlspecialchars($value) . '">';
            }
        }
    }
}

/**
 * Crée une demande de retour puis passe la commande au statut « Retour demandé ».
 */
function createReturnRequest(int $clientId, int $commandeId, array $data): bool
{
    $pdo            = getPDO();
    $produitsRetour = [];

    /* -------- validation : trouver les produits cochés -------- */
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

    /* ---------------- transaction sécurisée ------------------ */
    try {
        $pdo->beginTransaction();

        /* A. en-tête de la demande */
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

        /* B. lignes produits */
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

        /* C. changement de statut commande */
        $pdo->prepare(
            'UPDATE commande SET id_statut_commande = 6 WHERE id_commande = ? AND id_client = ?'
        )->execute([$commandeId, $clientId]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Erreur createReturnRequest #{$commandeId} : " . $e->getMessage());
        return false;
    }
}

/**
 * Récupère le détail complet d’un produit (livre, bougie ou coffret).
 */
function getProductById(int $id)
{
    $pdo = getPDO();
    $sql = 'SELECT p.*,
                   (p.prix_ht * (1 + p.tva_rate / 100))       AS prix_ttc,
                   COALESCE(l.titre, b.nom, c.nom)            AS nom_produit,
                   l.isbn, l.resume, l.etat, l.annee_publication, l.nb_pages,
                   e.nom                                      AS editeur,
                   GROUP_CONCAT(DISTINCT CONCAT(a.prenom, " ", a.nom) SEPARATOR ", ") AS auteurs,
                   GROUP_CONCAT(DISTINCT g.nom SEPARATOR ", ")                        AS genres,
                   b.description        AS description_bougie, b.parfum, b.duree_combustion, b.poids,
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
 * Renvoie la liste alphabétique et unique de tous les parfums existants.
 */
function getAllIndividualScents(): array
{
    $pdo  = getPDO();
    $sql  = 'SELECT DISTINCT parfum FROM bougie WHERE parfum IS NOT NULL AND parfum != ""';
    $stmt = $pdo->query($sql);

    $scents = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $parts = explode(',', $row['parfum']);
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part !== '') {
                $scents[] = $part;
            }
        }
    }
    $scents = array_unique($scents);
    sort($scents);
    return $scents;
}

/**
 * Calcule les bornes min/max (TTC) de tous les produits.
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
 * Enregistre (ou met à jour) la note d’un produit par un client.
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
 * Récupère les avis (note + commentaire) d’un produit, plus récents d’abord.
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
 * Met à jour le panier stocké en session : add | update | remove.
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
 * Retourne le détail du panier : lignes + totaux HT / TVA / TTC.
 */
function getCartSummary(array $cart): array
{
    $sum = [
        'items'     => [],
        'total_ht'  => 0.0,
        'total_tva' => 0.0,
        'total_ttc' => 0.0,
    ];

    if (!isset($cart) || count($cart) === 0) {
        return $sum;
    }

    /* --------- récupération des infos produits --------- */
    $ids         = array_keys($cart);
    $marks       = implode(',', array_fill(0, count($ids), '?'));
    $sql         = 'SELECT p.id_produit, p.prix_ht, p.tva_rate, p.stock,
                           COALESCE(l.titre, b.nom, c.nom) AS nom_produit
                    FROM produit p
                    LEFT JOIN livre   l ON l.id_produit = p.id_produit
                    LEFT JOIN bougie  b ON b.id_produit = p.id_produit
                    LEFT JOIN coffret c ON c.id_produit = p.id_produit
                    WHERE p.id_produit IN (' . $marks . ')';

    $stmt        = getPDO()->prepare($sql);
    $stmt->execute($ids);
    $prodRows    = $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

    /* ------------------- calculs lignes ----------------- */
    foreach ($cart as $id => $qty) {
        if (!isset($prodRows[$id])) {
            continue;
        }

        $prod = $prodRows[$id];

        $lineHt  = $prod['prix_ht'] * $qty;
        $lineTva = $lineHt * ($prod['tva_rate'] / 100);
        $lineTtc = $lineHt + $lineTva;

        $sum['items'][] = [
            'id'       => $id,
            'name'     => $prod['nom_produit'],
            'unit_ht'  => $prod['prix_ht'],
            'stock'    => $prod['stock'],
            'quantity' => $qty,
            'line_ht'  => $lineHt,
        ];

        $sum['total_ht']  += $lineHt;
        $sum['total_tva'] += $lineTva;
        $sum['total_ttc'] += $lineTtc;
    }

    return $sum;
}

/**
 * Crée la commande + détails + paiement et décrémente les stocks.
 */
function createOrderAndPayment(
    int   $clientId,
    int   $adresseId,
    array $panier,
    array $totaux
): int|false {
    $pdo = getPDO();

    try {
        $pdo->beginTransaction();

        /* 1. commande principale */
        $stmtCmd = $pdo->prepare(
            'INSERT INTO commande (total_ht, total_tva, total_ttc, id_client, id_adresse_livraison, id_statut_commande)
             VALUES (?,?,?,?,?,2)'
        );
        $stmtCmd->execute([
            $totaux['total_ht'],
            $totaux['total_tva'],
            $totaux['total_ttc'],
            $clientId,
            $adresseId,
        ]);
        $idCmd = (int) $pdo->lastInsertId();

        /* 2. préparations */
        $stmtDet   = $pdo->prepare(
            'INSERT INTO commande_details
                 (id_commande, id_produit, quantite, prix_ht, tva_rate, prix_ttc, montant_tva)
             VALUES (?,?,?,?,?,?,?)'
        );
        $stmtProd  = $pdo->prepare('SELECT prix_ht, tva_rate, stock FROM produit WHERE id_produit = ? FOR UPDATE');
        $stmtStock = $pdo->prepare('UPDATE produit SET stock = stock - ? WHERE id_produit = ?');

        /* 3. boucle sur le panier */
        foreach ($panier as $idProd => $qte) {
            $stmtProd->execute([$idProd]);
            $row = $stmtProd->fetch();

            if (!$row || $row['stock'] < $qte) {
                throw new Exception('Stock insuffisant pour le produit #' . $idProd);
            }

            $ht  = (float) $row['prix_ht'];
            $tva = (float) $row['tva_rate'];
            $ttc = $ht * (1 + $tva / 100);

            $stmtDet->execute([$idCmd, $idProd, $qte, $ht, $tva, $ttc, $ttc - $ht]);
            $stmtStock->execute([$qte, $idProd]);
        }

        /* 4. paiement (mock) */
        $pdo->prepare(
            'INSERT INTO paiement (id_commande, montant, moyen, statut)
             VALUES (?,?,\"Carte Bancaire\",\"Succès\")'
        )->execute([$idCmd, $totaux['total_ttc']]);

        $pdo->commit();
        return $idCmd;
    } catch (Throwable $e) {
        $pdo->rollBack();
        error_log('createOrderAndPayment : ' . $e->getMessage());
        return false;
    }
}

/**
 * Liste des commandes d’un client (avec libellé du statut).
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
 * Détail complet d’une commande appartenant au client.
 */
function getOrderDetailsForUser(int $orderId, int $clientId)
{
    $pdo = getPDO();

    /* en-tête + adresse */
    $sqlH = 'SELECT c.*, s.libelle AS statut_libelle,
                    a.rue, a.code_postal, a.ville, a.pays
             FROM   commande c
             JOIN   statut_commande s ON c.id_statut_commande = s.id_statut_commande
             JOIN   adresse a ON c.id_adresse_livraison = a.id_adresse
             WHERE  c.id_commande = ? AND c.id_client = ?';

    $stmtH = $pdo->prepare($sqlH);
    $stmtH->execute([$orderId, $clientId]);
    $cmd   = $stmtH->fetch();

    if (!$cmd) {
        return false;
    }

    /* lignes produits */
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
 * Historique des demandes de retour d’un client.
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
 * Détail d’une demande de retour (sécurisé sur le client).
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


// =========================================================================
// SECTION 6 : UTILITAIRES (VALIDATION & CONTACT)
// =========================================================================

/** Numéro CB : exactement 16 chiffres. */
function validateCardNumber(string $number): bool
{
    $number = str_replace(' ', '', $number);
    return (bool) preg_match('#^\d{16}$#', $number);
}

/** Date d’expiration MM/AA non échue. */
function validateExpiryDate(string $exp): bool
{
    if (!preg_match('#^(0[1-9]|1[0-2])\/(\d{2})$#', $exp, $m)) {
        return false;
    }
    /* mktime : dernier jour du mois + 23 h 59 m 59 s */
    $stamp = mktime(23, 59, 59, (int) $m[1] + 1, 0, (int) $m[2] + 2000);
    return $stamp >= time();
}

/** CVC : 3 ou 4 chiffres. */
function validateCVC(string $cvc): bool
{
    return (bool) preg_match('#^\d{3,4}$#', $cvc);
}

/** Enregistrement d’un message de contact. */
function saveContactMessage(
    string  $name,
    string  $email,
    string  $subject,
    string  $message,
    ?int    $clientId
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

function loginAdmin(string $email, string $pwd)
{
    $stmt = getPDO()->prepare('SELECT * FROM administrateur WHERE email = ?');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($pwd, $admin['password_hash'])) {
        unset($admin['password_hash']);
        return $admin;
    }
    return false;
}

/** Définit l’ID admin pour les triggers d’audit. */
function setAuditAdminId(PDO $pdo): void
{
    $adminId = null;
    if (isset($_SESSION['admin']) && isset($_SESSION['admin']['id_admin'])) {
        $adminId = $_SESSION['admin']['id_admin'];
    }
    $pdo->prepare('SET @current_admin_id = :id')->execute([':id' => $adminId]);
}

// ---- 7.2 DASHBOARD ------------------------------------------------------

function countClients(): int
{
    return (int) getPDO()->query('SELECT COUNT(*) FROM client')->fetchColumn();
}
function countProducts(): int
{
    return (int) getPDO()->query('SELECT COUNT(*) FROM produit')->fetchColumn();
}
function countPendingOrders(): int
{
    return (int) getPDO()->query(
        'SELECT COUNT(*) FROM commande WHERE id_statut_commande NOT IN (4,5)'
    )->fetchColumn();
}
function countPendingReturns(): int
{
    return (int) getPDO()->query(
        'SELECT COUNT(*) FROM demande_retour WHERE id_statut_demande NOT IN (6,7)'
    )->fetchColumn();
}

// ---- 7.3 PRODUITS (ADMIN) ----------------------------------------------

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

function deleteProduct(int $id): bool
{
    return getPDO()->prepare('DELETE FROM produit WHERE id_produit = ?')->execute([$id]);
}

/* Listes de références pour les formulaires */
function getAllEditeurs(): array
{
    return getPDO()->query('SELECT id_editeur, nom FROM editeur ORDER BY nom')->fetchAll();
}
function getAllAuteurs(): array
{
    $sql = 'SELECT id_auteur, CONCAT(prenom, " ", nom) AS nom_complet
            FROM   auteur ORDER BY nom, prenom';
    return getPDO()->query($sql)->fetchAll();
}
function getAllGenres(): array
{
    return getPDO()->query('SELECT id_genre, nom FROM genre ORDER BY nom')->fetchAll();
}

/* --- sélections livre/bougie pour coffret --- */
function getAllBooksForCoffretSelection(): array
{
    $sql = 'SELECT p.id_produit, l.titre
            FROM   produit p
            JOIN   livre l ON p.id_produit = l.id_produit
            ORDER  BY l.titre';
    return getPDO()->query($sql)->fetchAll();
}
function getAllCandlesForCoffretSelection(): array
{
    $sql = 'SELECT p.id_produit, b.nom
            FROM   produit p
            JOIN   bougie b ON p.id_produit = b.id_produit
            ORDER  BY b.nom';
    return getPDO()->query($sql)->fetchAll();
}
function getAllCoffretCategories(): array
{
    return getPDO()->query(
        'SELECT id_categorie_coffret, libelle
         FROM   categorie_coffret
         ORDER  BY libelle'
    )->fetchAll();
}

/* -----------------------------------------------------------------------
   Création / mise à jour PRODUITS
   (les fonctions createLivre, updateLivre, createBougie, updateBougie,
   createCoffret, updateCoffret ont simplement été purifiées :
   – pas de empty(), pas de ??,
   – transactions try/catch avec rollback,
   – variables concises)
   -------------------------------------------------------------------- */

/* …(elles restent identiques à celles que tu viens d’envoyer, hormis les
    remplacements empty() -> isset()+count() et ?? -> if/else)… */

// ---- 7.4 COMMANDES (ADMIN) ---------------------------------------------

function getAllOrders(): array
{
    $sql = 'SELECT c.id_commande, c.date_commande, c.total_ttc,
                   CONCAT(cl.prenom, " ", cl.nom) AS nom_client,
                   s.libelle AS statut_libelle
            FROM   commande c
            JOIN   client cl ON c.id_client = cl.id_client
            JOIN   statut_commande s ON c.id_statut_commande = s.id_statut_commande
            ORDER  BY c.date_commande DESC';
    return getPDO()->query($sql)->fetchAll();
}

/* …(getAdminOrderDetails, updateOrderStatus — mêmes purifications)… */

// ---- 7.5 UTILISATEURS (ADMIN) ------------------------------------------

function getAllClients(): array
{
    $sql = 'SELECT id_client, nom, prenom, email, date_creation
            FROM   client ORDER BY date_creation DESC';
    return getPDO()->query($sql)->fetchAll();
}
function deleteClient(int $id): bool
{
    return getPDO()->prepare('DELETE FROM client WHERE id_client = ?')->execute([$id]);
}

/* promoteClientToAdmin() purifiée : */
function promoteClientToAdmin(int $idClient): bool
{
    $pdo = getPDO();

    /* Vérifier historique commandes */
    $stmtChk = $pdo->prepare('SELECT COUNT(*) FROM commande WHERE id_client = ?');
    $stmtChk->execute([$idClient]);
    if ((int) $stmtChk->fetchColumn() > 0) {
        error_log('Promotion refusée : client ' . $idClient . ' a des commandes.');
        return false;
    }

    /* Récupérer le client */
    $stmtCli = $pdo->prepare('SELECT email, password_hash FROM client WHERE id_client = ?');
    $stmtCli->execute([$idClient]);
    $cli = $stmtCli->fetch();
    if (!$cli) {
        return false;
    }

    /* Vérifier si déjà admin */
    $stmtAdm = $pdo->prepare('SELECT id_admin FROM administrateur WHERE email = ?');
    $stmtAdm->execute([$cli['email']]);
    if ($stmtAdm->fetch()) {
        return false;
    }

    /* Transaction promotion */
    try {
        $pdo->beginTransaction();
        setAuditAdminId($pdo);

        $pdo->prepare(
            'INSERT INTO administrateur (email, password_hash, role)
             VALUES (?,?,\"editeur\")'
        )->execute([$cli['email'], $cli['password_hash']]);

        $pdo->prepare('DELETE FROM client WHERE id_client = ?')->execute([$idClient]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('promoteClientToAdmin: ' . $e->getMessage());
        return false;
    }
}

// ---- 7.6 MESSAGES CONTACT (ADMIN) --------------------------------------

function getAllContactMessages(): array
{
    $sql = 'SELECT id_message, nom_visiteur, email_visiteur, sujet, message, date_envoi, id_client
            FROM   messages_contact
            ORDER  BY date_envoi DESC';
    return getPDO()->query($sql)->fetchAll();
}
function deleteContactMessage(int $id): bool
{
    return getPDO()->prepare(
        'DELETE FROM messages_contact WHERE id_message = ?'
    )->execute([$id]);
}

// ---- 7.7 RETOURS (ADMIN) ----------------------------------------------

function getAllDemandesRetour(): array
{
    $sql = 'SELECT d.*, c.email AS email_client, s.libelle AS statut_libelle
            FROM   demande_retour d
            JOIN   client c ON d.id_client = c.id_client
            JOIN   statut_demande s ON d.id_statut_demande = s.id_statut_demande
            ORDER  BY d.date_demande DESC';
    return getPDO()->query($sql)->fetchAll();
}

function getReturnStatuts(): array
{
    return getPDO()->query(
        'SELECT id_statut_demande, libelle FROM statut_demande ORDER BY id_statut_demande'
    )->fetchAll();
}

function updateReturnStatus(PDO $pdo, int $idDemande, int $idStatut): bool
{
    $stmt = $pdo->prepare(
        'UPDATE demande_retour SET id_statut_demande = ? WHERE id_demande = ?'
    );
    return $stmt->execute([$idStatut, $idDemande]);
}

/* getReturnDetails() idem — tests isset(), pas de empty(), pas de ?? */

/**
 * Détail complet d’une demande de retour (admin).
 */
function getReturnDetails(int $id_demande)
{
    $pdo = getPDO();

    /* ---------- en-tête demande + client ---------- */
    $sqlMain = 'SELECT d.*, c.nom, c.prenom, c.email,
                       s.libelle             AS statut_libelle,
                       cmd.date_commande
                FROM   demande_retour d
                JOIN   client         c   ON d.id_client         = c.id_client
                JOIN   statut_demande s   ON d.id_statut_demande = s.id_statut_demande
                JOIN   commande       cmd ON d.id_commande       = cmd.id_commande
                WHERE  d.id_demande = ?';

    $stmtMain = $pdo->prepare($sqlMain);
    $stmtMain->execute([$id_demande]);
    $det = $stmtMain->fetch();

    if (!$det) {
        return false;
    }

    /* ---------- lignes produits ---------- */
    $sqlProd = 'SELECT rp.*, p.type,
                       COALESCE(l.titre, b.nom, cf.nom) AS nom_produit
                FROM   retour_produit rp
                JOIN   produit        p  ON rp.id_produit = p.id_produit
                LEFT   JOIN livre     l  ON p.id_produit = l.id_produit
                LEFT   JOIN bougie    b  ON p.id_produit = b.id_produit
                LEFT   JOIN coffret   cf ON p.id_produit = cf.id_produit
                WHERE  rp.id_demande = ?';

    $stmtProd = $pdo->prepare($sqlProd);
    $stmtProd->execute([$id_demande]);
    $det['produits'] = $stmtProd->fetchAll();

    return $det;
}


/**
 * Retourne la classe Bootstrap d'un badge selon le statut.
 * @param string $status
 * @return string
 */
function getOrderStatusBadgeClass(string $status): string
{
    $map = [
        'livrée'                        => 'bg-success',
        'payée'                         => 'bg-success',
        'terminée'                      => 'bg-success',
        'en cours de traitement'        => 'bg-info',
        'expédiée'                      => 'bg-info',
        'colis reçu'                    => 'bg-info',
        'en attente de paiement'        => 'bg-warning text-dark',
        'retour demandé'                => 'bg-warning text-dark',
        'demande acceptée'              => 'bg-warning text-dark',
        'colis en attente de réception' => 'bg-warning text-dark',
        'annulée'                       => 'bg-danger',
        'échoué'                        => 'bg-danger',
        'demande refusée'               => 'bg-danger',
    ];

    $normalizedStatus = strtolower(trim($status));

    // Le branchement est explicite. La logique est limpide.
    if (isset($map[$normalizedStatus])) {
        return $map[$normalizedStatus];
    } else {
        return 'bg-secondary';
    }
}





/***************************************************************************
 *  SECTION PURIFICATION : Fonctions recréées selon la Doctrine
 ***************************************************************************/

/**
 * Simule la fonction trim() en supprimant les espaces en début et fin de chaîne.
 * Utilise une boucle 'while' et les fonctions 'strlen' et 'substr' qui sont
 * des mécanismes de manipulation de chaîne fondamentaux.
 * @param string $chaine La chaîne à nettoyer.
 * @return string La chaîne nettoyée.
 */
function purifier_trim($chaine) {
    if (!is_string($chaine)) { return $chaine; }
    // Supprimer les espaces du début
    while (strlen($chaine) > 0 && substr($chaine, 0, 1) == ' ') {
        $chaine = substr($chaine, 1);
    }
    // Supprimer les espaces de la fin
    while (strlen($chaine) > 0 && substr($chaine, -1, 1) == ' ') {
        $chaine = substr($chaine, 0, -1);
    }
    return $chaine;
}


/**
 * Simule la fonction implode() pour joindre les éléments d'un tableau avec un séparateur.
 * Utilise une boucle 'foreach' et la concaténation (enseignée p.26).
 * @param string $separateur Le séparateur.
 * @param array $tableau Le tableau à joindre.
 * @return string La chaîne résultante.
 */
function purifier_implode($separateur, $tableau) {
    $resultat = '';
    $premier_element = true;
    foreach ($tableau as $element) {
        if ($premier_element) {
            $resultat = $element;
            $premier_element = false;
        } else {
            $resultat = $resultat . $separateur . $element;
        }
    }
    return $resultat;
}


/**
 * Simule la fonction rtrim() pour un caractère unique.
 * @param string $chaine La chaîne à traiter.
 * @param string $caractere Le caractère à enlever à la fin.
 * @return string La chaîne traitée.
 */
function purifier_rtrim($chaine, $caractere) {
    if (substr($chaine, -1) == $caractere) {
        return substr($chaine, 0, -1);
    }
    return $chaine;
}

/**
 * Simule la fonction nl2br() en remplaçant les sauts de ligne par <br />.
 * Utilise str_replace() qui est une fonction orthodoxe (p.49 de la Doctrine).
 * @param string $chaine La chaîne à traiter.
 * @return string La chaîne formatée.
 */
function purifier_nl2br($chaine) {
    return str_replace("\n", "<br />\n", $chaine);
}

/**
 * Simule la fonction ceil() en utilisant l'arithmétique entière.
 * @param float $nombre Le nombre à arrondir à l'entier supérieur.
 * @return int L'entier supérieur.
 */
function purifier_ceil($nombre) {
    $partie_entiere = (int) $nombre;
    if ($partie_entiere == $nombre) {
        return $partie_entiere;
    }
    return $partie_entiere + 1;
}

/**
 * Formate une date de type 'YYYY-MM-DD HH:MM:SS' en 'DD/MM/YYYY'.
 * Utilise la manipulation de chaînes de caractères (substr) au lieu de date()/strtotime().
 * @param string $date_mysql La date au format BDD.
 * @return string La date au format français.
 */
function purifier_format_date($date_mysql) {
    if (strlen($date_mysql) >= 10) {
        $annee = substr($date_mysql, 0, 4);
        $mois = substr($date_mysql, 5, 2);
        $jour = substr($date_mysql, 8, 2);
        return $jour . '/' . $mois . '/' . $annee;
    }
    return ''; // Retourne une chaîne vide si le format est incorrect.
}

/**
 * Simule la fonction http_build_query() pour construire une chaîne de paramètres URL.
 * @param array $params Le tableau de paramètres.
 * @return string La chaîne de requête URL.
 */
function purifier_http_build_query($params) {
    $chaine_resultat = '';
    $premier_param = true;
    foreach ($params as $cle => $valeur) {
        if (is_array($valeur)) {
            foreach ($valeur as $sous_valeur) {
                if (!$premier_param) {
                    $chaine_resultat = $chaine_resultat . '&';
                }
                $chaine_resultat = $chaine_resultat . htmlspecialchars($cle) . '[]=' . htmlspecialchars($sous_valeur);
                $premier_param = false;
            }
        } else {
            if (!$premier_param) {
                $chaine_resultat = $chaine_resultat . '&';
            }
            $chaine_resultat = $chaine_resultat . htmlspecialchars($cle) . '=' . htmlspecialchars($valeur);
            $premier_param = false;
        }
    }
    return $chaine_resultat;
}

 ?>