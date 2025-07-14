<?php
/** 
 * Fichier partiel : _sidebar_coffrets.php
 * Affiche les options de filtre spécifiques aux coffrets.
 */
?>

<div class="filter-sidebar">
    <h4>Filtrer par</h4>

    <div class="filter-group">
        <h5>Ambiance</h5>
        <?php foreach ($view_data['ambiances'] as $ambiance) { ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="ambiances[]" value="<?php $ambiance['id_tag'] ?>"
                    id="ambiance_<?php $ambiance['id_tag'] ?>" <?php in_array($ambiance['id_tag'], $filters['ambiances']) ? 'checked' : '' ?>>
                <label class="form-check-label"
                    for="ambiance_<?php $ambiance['id_tag'] ?>"><?php htmlspecialchars($ambiance['nom_tag']) ?></label>
            </div>
        <?php } ?>
    </div>
</div>