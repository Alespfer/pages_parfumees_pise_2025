<?php
// _form_livre.php : Champs pour un formulaire de livre.


$etats_possibles = array('Neuf', 'Très bon état', 'Bon état', 'État correct');
?>
<fieldset class="border p-3 mb-3">
    <legend class="w-auto px-2 fs-5">Détails du Livre</legend>
    <div class="mb-3">
        <label class="form-label">Titre<span class="required-star">*</span></label>
        <input type="text" class="form-control" name="titre" value="<?php echo htmlspecialchars(isset($produit['nom_produit']) ? $produit['nom_produit'] : ''); ?>" required>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">ISBN</label>
            <input type="text" class="form-control" name="isbn" value="<?php echo htmlspecialchars(isset($produit['isbn']) ? $produit['isbn'] : ''); ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">État</label>
            <select name="etat" class="form-select">
                <?php
                foreach ($etats_possibles as $e) {
                    $selected = (isset($produit['etat']) && $produit['etat'] == $e) ? 'selected' : '';
                    echo '<option value="' . $e . '" ' . $selected . '>' . $e . '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Résumé</label>
        <textarea class="form-control" name="resume" rows="4"><?php echo htmlspecialchars(isset($produit['resume']) ? $produit['resume'] : ''); ?></textarea>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Année de Publication</label>
            <input type="number" class="form-control" name="annee_publication" value="<?php echo htmlspecialchars(isset($produit['annee_publication']) ? $produit['annee_publication'] : ''); ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Nombre de Pages</label>
            <input type="number" class="form-control" name="nb_pages" value="<?php echo htmlspecialchars(isset($produit['nb_pages']) ? $produit['nb_pages'] : ''); ?>">
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Éditeur</label>
        <select name="id_editeur" class="form-select">
            <option value="">-- Aucun --</option>
            <?php foreach ($editeurs as $editeur) { ?>
                <option value="<?php echo $editeur['id_editeur']; ?>" <?php echo (isset($produit['id_editeur']) && $produit['id_editeur'] == $editeur['id_editeur']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($editeur['nom']); ?>
                </option>
            <?php } ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Auteurs</label>
        <select name="auteurs[]" class="form-select" multiple size="5">
            <?php foreach ($auteurs as $auteur) { ?>
                <option value="<?php echo $auteur['id_auteur']; ?>" <?php echo (isset($produit['auteurs_ids']) && in_array($auteur['id_auteur'], $produit['auteurs_ids'])) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($auteur['nom_complet']); ?>
                </option>
            <?php } ?>
        </select>
        <small class="form-text text-muted">Maintenez CTRL (ou CMD sur Mac) pour sélectionner plusieurs auteurs.</small>
    </div>
</fieldset>

<fieldset class="border p-3 mb-3">
    <legend class="w-auto px-2 fs-5">Genres Littéraires</legend>
    <div class="row">
        <?php
        foreach ($genres as $genre) {
            $est_coche = (isset($produit['genres_ids']) && in_array($genre['id_genre'], $produit['genres_ids']));
        ?>
            <div class="col-md-4 col-sm-6">
                <div class="form-check">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="genres[]" 
                           value="<?php echo $genre['id_genre']; ?>" 
                           id="genre-<?php echo $genre['id_genre']; ?>"
                           <?php if ($est_coche) { echo 'checked'; } ?>>
                    <label class="form-check-label" for="genre-<?php echo $genre['id_genre']; ?>">
                        <?php echo htmlspecialchars($genre['nom']); ?>
                    </label>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</fieldset>

<fieldset class="border p-3 mb-3">
    <legend class="w-auto px-2 fs-5">Ambiance </legend>
    <div class="row">
        <?php
        foreach ($tags as $tag) {
            $est_coche = (isset($produit['tags']) && in_array($tag['id_tag'], $produit['tags']));
        ?>
            <div class="col-md-4 col-sm-6">
                <div class="form-check">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="tags[]" 
                           value="<?php echo $tag['id_tag']; ?>" 
                           id="tag-<?php echo $tag['id_tag']; ?>"
                           <?php if ($est_coche) { echo 'checked'; } ?>>
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