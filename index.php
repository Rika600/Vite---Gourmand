<?php
// 1. Charger les classes nécessaires
require_once 'src/Database.php';
require_once 'src/Models/Avis.php';

// 2. Récupérer la connexion PDO via la classe Database (Singleton)
$pdo = Database::getConnection();

// 3. Créer un objet Avis et récupérer les avis validés
$avisModel = new Avis($pdo);
$avis = $avisModel->getAvisValides(3);

// 4. Titre de la page
$pageTitle = 'Accueil - Vite & Gourmand';

// 5. Inclure le header
require_once 'includes/header.php';
?>

<!-- Section Hero -->
<div class="like">
    <div class="like-content text-center text-white">
        <h1>Vite & Gourmand</h1>
        <p>Traiteur depuis 25 ans</p>
        <div class="like-button">
            <a href="/vite-gourmand/menus.php" class="btn btn-dark hero-button">Découvrir nos créations</a>
        </div>
    </div>
</div>

<!-- Section Présentation -->
<div class="container my-5 section-depuis">
    <h2 class="text-center mb-4">DEPUIS 25 ANS</h2>
    <hr class="section-line">
    <p class="text-center">
        La qualité et le savoir-faire sont au cœur de notre métier.
        Vite & Gourmand met son expertise au service de vos événements.
        Grâce à une exigence constante et un savoir-faire maîtrisé, nous proposons
        des prestations à la hauteur des attentes les plus élevées.
    </p>
    <div class="row align-items-center mt-4">
        <div class="col-md-6">
            <p>
                Nous recrutons les meilleurs professionnels pour vous accompagner.
                Composée de passionnés et d'experts, notre équipe met son savoir-faire
                et son expérience au service de vos évènements. Avec rigueur et exigence,
                chaque détail est pensé pour vous offrir une expérience à la hauteur de vos envies.
            </p>
        </div>
        <div class="col-md-6 text-center">
            <img class="img-fluid rounded"
                 src="/vite-gourmand/images/images/image équipe.jpg"
                 alt="Équipe Vite & Gourmand"
                 style="max-height: 350px; object-fit: cover;">
        </div>
    </div>
</div>

<!-- Section Avis Clients -->
<div class="container my-5 section-avis">
    <h2 class="text-center mb-4">AVIS CLIENTS</h2>

    <?php if (empty($avis)): ?>
        <p class="text-center text-muted">Aucun avis validé pour le moment.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($avis as $a): ?>
                <div class="col-md-4 mb-3">
                    <div class="card p-3 text-center">
                        <p class="text-warning">
                            <?= str_repeat('★', $a['note']) . str_repeat('☆', 5 - $a['note']) ?>
                        </p>
                        <p><?= htmlspecialchars($a['commentaire']) ?></p>
                        <p class="fw-bold">
                            - <?= htmlspecialchars($a['prenom']) ?> <?= htmlspecialchars(substr($a['nom'], 0, 1)) ?>.
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// 6. Inclure le footer
require_once 'includes/footer.php';
?>