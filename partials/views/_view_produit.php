<?php
/*
 * Fichier partiel : _view_produit.php
 * Rôle : Affiche la structure HTML de la page de détail d'un produit.
 * Ce fichier est inclus par shop.php lorsque l'action est 'view'.
 */
?>
<main class="container my-4">
    <p><a href="shop.php?a=vitrine">← Retour à la boutique</a></p>
    <hr>
    <div class="row">
        <!-- Colonne de l'image du produit -->
        <div class="col-md-5 mb-4">
            <?php if (isset($produit['image_url']) && $produit['image_url'] != '') { ?>
                <img src="<?php echo htmlspecialchars($produit['image_url']); ?>" alt="<?php echo htmlspecialchars($produit['nom_produit']); ?>" class="img-fluid rounded shadow-sm" style="width: 100%; max-height: 550px; object-fit: contain;">
            <?php } else { ?>
                <img src="https://via.placeholder.com/400x500.png?text=Image+non+disponible" alt="<?php echo htmlspecialchars($produit['nom_produit']); ?>" class="img-fluid rounded shadow-sm">
            <?php } ?>
        </div>

        <!-- Colonne des informations principales -->
        <div class="col-md-7">
            <h1><?php echo htmlspecialchars($produit['nom_produit']); ?></h1>

            <?php if ($produit['type'] == 'livre' && isset($produit['auteurs']) && $produit['auteurs'] != '') { ?>
                <p class="lead"><em>par <?php echo htmlspecialchars($produit['auteurs']); ?></em></p>
            <?php } ?>

            <div class="mb-3">
                <span>Note : <?php echo number_format($produit['note_moyenne'], 2, ',', ' '); ?>/5 (<?php echo $produit['nombre_votes']; ?> avis)</span>
            </div>

            <p class="h4 mb-3"><?php echo number_format($produit['prix_ttc'], 2, ',', ' '); ?> € <small class="text-muted">TTC</small></p>

            <?php if ($produit['stock'] > 0) { ?>
                <div class="alert alert-success">En stock (<?php echo htmlspecialchars($produit['stock']); ?> restant(s))</div>
                <form action="panier.php" method="POST" class="mb-4 d-flex align-items-center">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                    <label for="quantity" class="me-2">Quantité:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $produit['stock']; ?>" class="form-control me-3" style="width:80px;">
                    <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                </form>
            <?php } else { ?>
                <div class="alert alert-danger">Indisponible</div>
            <?php } ?>
        </div>
    </div>

    <hr class="my-5">

    <div class="row">
        <!-- Colonne de la description détaillée -->
        <div class="col-md-7">
            <h3>Description</h3>
            <?php if ($produit['type'] == 'livre') { ?>
                <p><?php echo htmlspecialchars(isset($produit['resume']) ? $produit['resume'] : 'Résumé non disponible.'); ?></p>
                <h5>Détails</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Éditeur :</strong> <?php echo htmlspecialchars(isset($produit['editeur']) ? $produit['editeur'] : 'N/A'); ?></li>
                    <li class="list-group-item"><strong>Année :</strong> <?php echo htmlspecialchars(isset($produit['annee_publication']) ? $produit['annee_publication'] : 'N/A'); ?></li>
                    <li class="list-group-item"><strong>ISBN :</strong> <?php echo htmlspecialchars(isset($produit['isbn']) ? $produit['isbn'] : 'N/A'); ?></li>
                    <li class="list-group-item"><strong>Pages :</strong> <?php echo htmlspecialchars(isset($produit['nb_pages']) ? $produit['nb_pages'] : 'N/A'); ?></li>
                    <li class="list-group-item"><strong>État :</strong> <?php echo htmlspecialchars(isset($produit['etat']) ? $produit['etat'] : 'N/A'); ?></li>
                    <li class="list-group-item"><strong>Genre(s) :</strong> <?php echo htmlspecialchars(isset($produit['genres']) ? $produit['genres'] : 'N/A'); ?></li>
                </ul>
            <?php } elseif ($produit['type'] == 'bougie') { ?>
                <p><?php echo htmlspecialchars(isset($produit['description_bougie']) ? $produit['description_bougie'] : 'Description non disponible.'); ?></p>
                <h5>Détails</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Parfum :</strong> <?php echo htmlspecialchars(isset($produit['parfum']) ? $produit['parfum'] : 'N/A'); ?></li>
                    <li class="list-group-item"><strong>Durée :</strong> <?php echo htmlspecialchars(isset($produit['duree_combustion']) ? $produit['duree_combustion'] : 'N/A'); ?> heures</li>
                    <li class="list-group-item"><strong>Poids :</strong> <?php echo htmlspecialchars(isset($produit['poids']) ? $produit['poids'] : 'N/A'); ?> g</li>
                </ul>
            <?php } elseif ($produit['type'] == 'coffret') { ?>
                <p><?php echo htmlspecialchars(isset($produit['description_coffret']) ? $produit['description_coffret'] : 'Description non disponible.'); ?></p>
                <h5 class="mt-4">Contenu du coffret</h5>
                <div class="list-group">
                    <?php if ($livre_inclus) { ?><a href="shop.php?a=view&id=<?php echo $livre_inclus['id_produit']; ?>" class="list-group-item list-group-item-action"><strong>Livre :</strong> <?php echo htmlspecialchars($livre_inclus['nom_produit']); ?></a><?php } ?>
                    <?php if ($bougie_incluse) { ?><a href="shop.php?a=view&id=<?php echo $bougie_incluse['id_produit']; ?>" class="list-group-item list-group-item-action"><strong>Bougie :</strong> <?php echo htmlspecialchars($bougie_incluse['nom_produit']); ?> (Parfum: <?php echo htmlspecialchars(isset($bougie_incluse['parfum']) ? $bougie_incluse['parfum'] : 'N/A'); ?>)</a><?php } ?>
                </div>
            <?php } ?>
        </div>

        <!-- Colonne des avis et du formulaire d'avis -->
        <div class="col-md-5">
            <h3>Avis des clients</h3>
            <?php if (count($avis) == 0) { ?>
                <p>Soyez le premier à laisser un avis !</p>
            <?php } else {
                foreach ($avis as $un_avis) { ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <strong><?php echo htmlspecialchars($un_avis['prenom_client']); ?></strong>
                                <small class="text-muted"><?php echo htmlspecialchars($un_avis['date_notation']); ?></small>
                            </div>
                            <p>Note : <?php echo $un_avis['note']; ?>/5</p>
                            <?php if (isset($un_avis['commentaire']) && $un_avis['commentaire'] != '') { ?>
                                <p class="fst-italic">"<?php echo htmlspecialchars($un_avis['commentaire']); ?>"</p>
                            <?php } ?>
                        </div>
                    </div>
                <?php }
            } ?>
            
            <div class="mt-4">
                <?php if (isset($_SESSION['user'])) { ?>
                    <div class="card bg-light p-3">
                        <h5>Laisser un avis :</h5>
                        <form action="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>" method="POST">
                            <input type="hidden" name="form_type" value="rating">
                            <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                            <label class="form-label" for="note">Votre note</label>
                            <select name="note" id="note" class="form-select mb-2">
                                <option value="" disabled selected>-- Choisir une note --</option>
                                <?php for ($i = 5; $i >= 1; $i--) { ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> étoile<?php if ($i > 1) echo 's'; ?></option>
                                <?php } ?>
                            </select>
                            <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                            <textarea name="commentaire" id="commentaire" class="form-control mb-2" rows="3"></textarea>
                            <button type="submit" class="btn btn-dark w-100">Envoyer</button>
                        </form>
                    </div>
                <?php } else { ?>
                    <div class="alert alert-secondary">
                        <a href="auth.php?action=login&redirect=shop.php?a=view&id=<?php echo $produit['id_produit']; ?>">Connectez-vous</a> pour laisser un avis.
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</main>