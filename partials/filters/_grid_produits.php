<?php
// /partials/views/_grid_produits.php
?>
<style>
    /* CSS pour la grille de produits et le système d'étoiles. */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
        justify-content: start; 
    }
    .product-card {
        border: 1px solid #dee2e6;
        border-radius: .25rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        transition: box-shadow .2s ease-in-out;
    }
    .product-card:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    }
    .product-card-img-top {
        width: 100%;
        height: 300px;
        object-fit: cover;
    }
    .product-card-body {
        padding: 1rem;
        flex-grow: 1; 
        display: flex;
        flex-direction: column;
    }
    .product-title {
        font-size: 1.1rem;
        font-weight: bold;
    }
    .product-meta {
        font-size: 0.9rem;
        color: #6c757d;
        min-height: 20px;
    }
    .product-price {
        font-size: 1.2rem;
        font-weight: bold;
        margin: 0.5rem 0;
    }
    .product-card-body .btn {
        margin-top: auto;
    }
    .rating-stars {
        display: inline-block;
        position: relative;
        font-size: 1.1rem;
        color: #d3d3d3;
    }
    .stars-foreground {
        position: absolute;
        top: 0;
        left: 0;
        white-space: nowrap;
        overflow: hidden;
        color: #ffc107;
    }

    /* ======================================================== */
    /* == NOUVEAU CODE CSS POUR CORRIGER LA TAILLE DE L'IMAGE == */
    /* ======================================================== */
    .product-detail-image-container img {
        width: 100%; /* Prend toute la largeur de sa colonne */
        max-height: 550px; /* Limite la hauteur maximale (ajustez si besoin) */
        height: auto; /* Permet au navigateur de calculer la hauteur proportionnellement */
        object-fit: contain; /* S'assure que l'image est entièrement visible et non déformée */}
</style>

<div class="product-grid">
    <?php if (empty($produits)): ?>
        <p class="text-center col-12">Aucun produit ne correspond à votre sélection.</p>
    <?php else: ?>
        <?php foreach($produits as $produit): ?>
            <div class="product-card">
                <a href="shop.php?a=view&id=<?= $produit['id_produit'] ?>">
                    
                    <?php
                    // =========================================================
                    // == BLOC MODIFIÉ : AFFICHE L'IMAGE OU UN PLACEHOLDER =====
                    // =========================================================
                    
                    // On vérifie si l'URL de l'image existe et n'est pas vide
                    if (!empty($produit['image_url'])) {
                        // Si oui, on l'utilise
                        $image_source = htmlspecialchars($produit['image_url']);
                    } else {
                        // Sinon, on utilise une image par défaut
                        $image_source = 'https://via.placeholder.com/300x400.png/eee/999?text=Image+non+disponible';
                    }
                    ?>
                    
                    <img class="product-card-img-top" src="<?= $image_source ?>" alt="<?= htmlspecialchars($produit['nom_produit'] ?? 'Produit') ?>">
                
                </a>
                <div class="product-card-body">
                    <h5 class="product-title"><?= htmlspecialchars($produit['nom_produit'] ?? 'Titre inconnu') ?></h5>
                    
                    <div class="my-2">
                        <?php if ($produit['nombre_votes'] > 0): ?>
                            <div class="rating-stars" title="Note : <?= number_format($produit['note_moyenne'], 2) ?>/5">
                                <div class="stars-foreground" style="width: <?= ($produit['note_moyenne'] / 5) * 100 ?>%;">★★★★★</div>
                                <div class="stars-background">★★★★★</div>
                            </div>
                            <small class="text-muted ms-1">(<?= $produit['nombre_votes'] ?> avis)</small>
                        <?php else: ?>
                            <small class="text-muted">Aucun avis</small>
                        <?php endif; ?>
                    </div>

                    <p class="product-meta">
                        <?php 
                            if ($produit['type'] === 'livre') {
                                echo htmlspecialchars($produit['auteurs'] ?? 'Auteur inconnu');
                            } else {
                                echo ucfirst(htmlspecialchars($produit['type']));
                            }
                        ?>
                    </p>
                    <p class="product-price"><?= number_format($produit['prix_ttc'], 2, ',', ' ') ?> €</p>
                    <form action="panier2.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $produit['id_produit'] ?>">
                        <button type="submit" class="btn btn-outline-dark btn-sm">Ajouter au panier</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>