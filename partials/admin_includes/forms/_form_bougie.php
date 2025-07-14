<?php
/*
 * Fichier partiel : _form_bougie.php
 * Rôle : Affiche les champs de formulaire spécifiques à une bougie.
 */
?>

<fieldset class="border p-3 mb-3">
    <legend class="w-auto px-2 fs-5">Détails de la Bougie</legend>
    
    <div class="mb-3">
        <label class="form-label">Nom<span class="required-star">*</span></label>
        <input type="text" class="form-control" name="nom" value="<?php echo htmlspecialchars($produit['nom_produit'] ?? ''); ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($produit['description_bougie'] ?? ''); ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Parfum</label>
            <input type="text" class="form-control" name="parfum" value="<?php echo htmlspecialchars($produit['parfum'] ?? ''); ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Durée (h)</label>
            <input type="number" class="form-control" name="duree_combustion" value="<?php echo htmlspecialchars($produit['duree_combustion'] ?? ''); ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Poids (g)</label>
            <input type="number" class="form-control" name="poids" value="<?php echo htmlspecialchars($produit['poids'] ?? ''); ?>">
        </div>
    </div>
</fieldset>

<fieldset class="border p-3 mb-3">
    <legend class="w-auto px-2 fs-5">Ambiance</legend>
    <div class="row">
        <?php
        // Dépendance : la variable $tags est injectée par le script appelant (admin.php)
        foreach ($tags as $tag) {
        ?>
            <div class="col-md-4 col-sm-6">
                <div class="form-check">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="tags[]" 
                           value="<?php echo $tag['id_tag']; ?>" 
                           id="tag-<?php echo $tag['id_tag']; ?>"
                           <?php echo (isset($produit['tags']) && in_array($tag['id_tag'], $produit['tags'])) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="tag-<?php echo $tag['id_tag']; ?>">
                        <?php echo htmlspecialchars($tag['nom_tag']); ?>
                    </label>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</fieldset>