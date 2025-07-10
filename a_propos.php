<?php
// L'Agent se conforme à la syntaxe démontrée.
require('partials/header.php');

$pageTitle = "À Propos de Nous";
?>

<main class="container my-4">
    <div class="card p-4 p-md-5 shadow-sm">
        <div class="card-body">
            <!-- L'instruction "echo" est enseignée et conforme. -->
            <h1 class="text-center mb-4"><?php echo $pageTitle; ?></h1>
            <p class="lead text-center mb-5">Là où les Mots rencontrent la Lumière.</p>
            
            <hr>

            <h2 class="mt-5">Le Concept : L'Harmonie des Sens</h2>
            <p>Notre idée est simple : créer des expériences de lecture immersives. Comment ? En associant deux mondes qui se complètent à merveille :</p>
            <ul>
                <li><strong>Les Mots :</strong> Une sélection de livres d'occasion, choisis avec soin pour leur capacité à transporter, émouvoir et faire réfléchir. Chaque livre a une âme, une histoire qui ne demande qu'à être redécouverte.</li>
                <li><strong>Les Lumières :</strong> Des bougies artisanales, conçues pour évoquer une atmosphère, un sentiment ou un lieu. Leurs parfums sont pensés pour accompagner et enrichir vos moments de détente et de lecture.</li>
            </ul>
            <p>La clé de notre concept réside dans notre système de <strong>tags d'ambiance</strong>. Vous cherchez un "Frisson mystérieux" ? Un moment "Cosy au coin du feu" ? Sélectionnez une ambiance et nous vous proposerons le livre et la bougie qui formeront le duo parfait pour votre évasion.</p>
            
            <h2 class="mt-5">Qui Sommes-Nous ?</h2>
            <p>Derrière ce projet se cache <strong>[Mettez Votre Nom Ici]</strong>, un(e) passionné(e) de lecture et d'atmosphères feutrées. "L'Atelier des Mots & Lumières" est avant tout un projet universitaire, réalisé avec cœur et rigueur, dans le but de démontrer qu'il est possible de lier technologie et poésie. Chaque ligne de code, chaque choix de produit est guidé par l'envie de vous offrir une expérience unique et mémorable.</p>
            
            <h2 class="mt-5">Nos Engagements</h2>
            <ul>
                <li><strong>La Magie de la Seconde Main :</strong> Nous donnons une nouvelle vie aux livres. En choisissant un livre d'occasion, vous faites un geste pour la planète tout en vous offrant un objet qui a déjà une histoire.</li>
                <li><strong>L'Artisanat au Cœur :</strong> Nos bougies sont conçues de manière artisanale, garantissant une qualité et une attention aux détails que vous ne trouverez nulle part ailleurs.</li>
                <li><strong>Une Expérience Sur-Mesure :</strong> Notre mission est de vous aider à trouver non pas un produit, mais l'association parfaite qui illuminera vos moments de quiétude.</li>
            </ul>

            <p class="text-center mt-5"><em>Merci de votre visite, et nous vous souhaitons de merveilleuses heures de lecture et de détente.</em></p>
        </div>
    </div>
</main>

<?php
require('partials/footer.php');
?>