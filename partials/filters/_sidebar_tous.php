<?php
// /partials/views/_sidebar_tous.php 
?>
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

<div class="filter-group">
    <h5>Genre de lecture</h5>
    <?php foreach ($view_data['genres'] as $genre) { ?>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="genres[]" value="<?php $genre['id_genre'] ?>"
                id="genre_<?php $genre['id_genre'] ?>" <?php in_array($genre['id_genre'], $filters['genres']) ? 'checked' : '' ?>>
            <label class="form-check-label"
                for="genre_<?php $genre['id_genre'] ?>"><?php htmlspecialchars($genre['nom']) ?></label>
        </div>
    <?php } ?>
</div>