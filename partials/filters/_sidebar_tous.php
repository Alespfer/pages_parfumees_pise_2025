<?php
// /partials/views/_sidebar_tous.php (Version nettoyée)
?>
<div class="filter-group">
    <h5>Ambiance</h5>
    <?php foreach ($view_data['ambiances'] as $ambiance): ?>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="ambiances[]" value="<?= $ambiance['id_tag'] ?>" id="ambiance_<?= $ambiance['id_tag'] ?>" <?= in_array($ambiance['id_tag'], $filters['ambiances']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="ambiance_<?= $ambiance['id_tag'] ?>"><?= htmlspecialchars($ambiance['nom_tag']) ?></label>
        </div>
    <?php endforeach; ?>
</div>

<div class="filter-group">
    <h5>Genre de lecture</h5>
    <?php foreach ($view_data['genres'] as $genre): ?>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="genres[]" value="<?= $genre['id_genre'] ?>" id="genre_<?= $genre['id_genre'] ?>" <?= in_array($genre['id_genre'], $filters['genres']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="genre_<?= $genre['id_genre'] ?>"><?= htmlspecialchars($genre['nom']) ?></label>
        </div>
    <?php endforeach; ?>
</div>