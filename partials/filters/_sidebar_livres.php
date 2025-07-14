<?php
/*
 * Fichier partiel : _sidebar_livres.php
 * Affiche les options de filtre spécifiques aux livres.
 */
?>
<!-- Filtre par prix -->
<div class="mb-4">
    <h5>Prix</h5>
    <div class="d-flex align-items-center">
        <input type="number" name="prix_min" class="form-control" placeholder="Min" value="<?php if (isset($filtres['prix_min'])) echo htmlspecialchars($filtres['prix_min']); ?>">
        <span class="mx-2">-</span>
        <input type="number" name="prix_max" class="form-control" placeholder="Max" value="<?php if (isset($filtres['prix_max'])) echo htmlspecialchars($filtres['prix_max']); ?>">
    </div>
</div>

<!-- Filtre par genre -->
<div class="mb-4">
    <h5>Genre</h5>
    <?php
    if (isset($view_data['genres']) && count($view_data['genres']) > 0) {
        foreach ($view_data['genres'] as $genre) {
            $est_coche = false;
            if (isset($filtres['genres'])) { 
                if (in_array($genre['id_genre'], $filtres['genres'])) {
                    $est_coche = true;
                }
            }
    ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="genres[]" value="<?php echo $genre['id_genre']; ?>" id="genre_<?php echo $genre['id_genre']; ?>" <?php if ($est_coche) echo 'checked'; ?>>
                <label class="form-check-label" for="genre_<?php echo $genre['id_genre']; ?>">
                    <?php echo htmlspecialchars($genre['nom_genre']); ?>
                </label>
            </div>
    <?php
        }
    }
    ?>
</div>

<!-- Filtre par état -->
<div>
    <h5>État</h5>
    <?php
    if (isset($view_data['etats']) && count($view_data['etats']) > 0) {
        foreach ($view_data['etats'] as $etat) {
            $est_coche = false;
            if (isset($filtres['etats'])) {
                if (in_array($etat['etat'], $filtres['etats'])) {
                    $est_coche = true;
                }
            }
    ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="etats[]" value="<?php echo htmlspecialchars($etat['etat']); ?>" id="etat_<?php echo htmlspecialchars($etat['etat']); ?>" <?php if ($est_coche) echo 'checked'; ?>>
                <label class="form-check-label" for="etat_<?php echo htmlspecialchars($etat['etat']); ?>">
                    <?php echo htmlspecialchars($etat['etat']); ?>
                </label>
            </div>
    <?php
        }
    }
    ?>
</div>