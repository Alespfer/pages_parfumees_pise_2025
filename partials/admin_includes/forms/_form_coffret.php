<?php
/*
 * Affiche les champs de formulaire spécifiques à un coffret.
 */
?>
<fieldset class="border p-3 mb-3">
    <legend class="w-auto px-2 fs-5">Détails du Coffret</legend>
    <div class="mb-3">
        <label class="form-label">Nom du Coffret<span class="required-star">*</span></label>
        <input type="text" class="form-control" name="nom"
            value="<?php echo htmlspecialchars(isset($produit['nom_produit']) ? $produit['nom_produit'] : ''); ?>"
            required>
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description"
            rows="3"><?php echo htmlspecialchars(isset($produit['description_coffret']) ? $produit['description_coffret'] : ''); ?></textarea>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Livre Inclus<span class="required-star">*</span></label>
            <select name="id_produit_livre" class="form-select" required>
                <option value="">-- Choisir un livre --</option>
                <?php foreach ($produits_livres as $livre) { ?>
                    <option value="<?php echo $livre['id_produit']; ?>" <?php echo (isset($produit['id_produit_livre']) && $produit['id_produit_livre'] == $livre['id_produit']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($livre['titre']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Bougie Incluse<span class="required-star">*</span></label>
            <select name="id_produit_bougie" class="form-select" required>
                <option value="">-- Choisir une bougie --</option>
                <?php foreach ($produits_bougies as $bougie) { ?>
                    <option value="<?php echo $bougie['id_produit']; ?>" <?php echo (isset($produit['id_produit_bougie']) && $produit['id_produit_bougie'] == $bougie['id_produit']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($bougie['nom']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Catégorie du Coffret</label>
        <select name="id_categorie_coffret" class="form-select">
            <option value="">-- Aucune --</option>
            <?php foreach ($categories_coffret as $cat) { ?>
                <option value="<?php echo $cat['id_categorie_coffret']; ?>" <?php echo (isset($produit['id_categorie_coffret']) && $produit['id_categorie_coffret'] == $cat['id_categorie_coffret']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['libelle']); ?>
                </option>
            <?php } ?>
        </select>
    </div>
</fieldset>
<fieldset class="border p-3 mb-3">
    <legend class="w-auto px-2 fs-5">Ambiance </legend>
    <div class="row">
        <?php
        // La variable $tags est fournie par le script principal admin.php
        foreach ($tags as $tag) {
            $est_coche = (isset($produit['tags']) && in_array($tag['id_tag'], $produit['tags']));
            ?>
            <div class="col-md-4 col-sm-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="tags[]" value="<?php echo $tag['id_tag']; ?>"
                        id="tag-<?php echo $tag['id_tag']; ?>" <?php if ($est_coche) {
                               echo 'checked';
                           } ?>>
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