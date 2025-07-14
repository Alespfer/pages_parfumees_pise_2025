<?php

/**
 * /partials/filters/_grid_produits.php
 * Rôle : Affiche une grille de produits.
 * Fichier partial à etre appelé depuis le principal
 */

?>

<div class="product-grid">

    <?php if (!isset($produits) || count($produits) === 0) { 
    ?>
        <p class="text-center col-12">Aucun produit ne correspond à votre sélection.</p>
    <?php } else { ?>
        <?php foreach($produits as $produit) { 
            
            // --- Préparation des variables pour un affichage propre ---
            
            if (isset($produit['image_url']) && $produit['image_url'] !== '') {
                $image_source = htmlspecialchars($produit['image_url']);
            } else {
                $image_source = SITE_URL . '/ressources/decor/landscape-placeholder.svg';
            }
            // Méta-données : afficher l'auteur pour un livre, sinon le type de produit.
            $meta_text = '';
            if ($produit['type'] === 'livre') {
                $meta_text = htmlspecialchars(isset($produit['auteurs']) ? $produit['auteurs'] : 'Auteur inconnu');
            } else {
                // on utilise ucfirst pour mettre la première lettre en majuscule
                $meta_text = ucfirst(htmlspecialchars($produit['type']));
            }

            $rating_percentage = 0;
            if (isset($produit['nombre_votes']) && $produit['nombre_votes'] > 0) {
                $rating_percentage = ($produit['note_moyenne'] / 5) * 100;
            }
        ?>
            <div class="product-card">
                <a href="shop.php?a=view&id=<?php echo $produit['id_produit']; ?>">
                    <img class="product-card-img-top" src="<?php echo $image_source; ?>" alt="<?php echo htmlspecialchars(isset($produit['nom_produit']) ? $produit['nom_produit'] : 'Produit'); ?>">
                </a>
                <div class="product-card-body">
                    <h5 class="product-title"><?php echo htmlspecialchars(isset($produit['nom_produit']) ? $produit['nom_produit'] : 'Titre inconnu'); ?></h5>
                    
                    <div class="my-2">
                        <?php if (isset($produit['nombre_votes']) && $produit['nombre_votes'] > 0) { ?>
                            <div class="rating-stars" title="Note : <?php echo number_format($produit['note_moyenne'], 2); ?>/5">
                                <div class="stars-foreground" style="width: <?php echo $rating_percentage; ?>%;">★★★★★</div>
                                <div class="stars-background">★★★★★</div>
                            </div>
                            <small class="text-muted ms-1">(<?php echo $produit['nombre_votes']; ?> avis)</small>
                        <?php } else { ?>
                            <small class="text-muted">Aucun avis</small>
                        <?php } ?>
                    </div>

                    <p class="product-meta">
                        <?php echo $meta_text; ?>
                    </p>
                    <p class="product-price"><?php echo number_format($produit['prix_ttc'], 2, ',', ' '); ?> €</p>
                    <form action="panier2.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $produit['id_produit']; ?>">
                        <button type="submit" class="btn btn-outline-dark btn-sm">Ajouter au panier</button>
                    </form>
                </div>
            </div>
        <?php }  ?>
    <?php }  ?>
</div>