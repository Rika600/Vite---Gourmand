<?php
$pageTitle = 'Détail du menu - Vite & Gourmand';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Models/detail-menu.php';

// 1. Récupérer l'id depuis l'URL
$menuId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Connexion BDD + instancier le modèle
$pdo = Database::getConnection();
$menuModel = new Menu($pdo);

// 3. Récupérer CE menu
$menu = $menuModel->getById($menuId);

// 4. Si le menu n'existe pas → message d'erreur
if (!$menu) {
    echo '<div class="container my-5"><p>Menu introuvable.</p></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// 5. Récupérer les données liées
$plats = $menuModel->getPlats($menu['menu_id']);
$themes = $menuModel->getThemes($menu['menu_id']);
$allergenes = $menuModel->getAllergenes($menu['menu_id']);

// 6. Séparer les plats par type
$entree = '';
$plat = '';
$dessert = '';
foreach ($plats as $p) {
    if ($p['type'] === 'entree') $entree = $p['nom'];
    if ($p['type'] === 'plat') $plat = $p['nom'];
    if ($p['type'] === 'dessert') $dessert = $p['nom'];
}

// 7. Allergènes en texte
$listeAllergenes = array_map(fn($a) => $a['libelle'], $allergenes);
$allergenesTexte = implode(', ', $listeAllergenes);

// 8. Thème
$theme = $themes[0]['libelle'] ?? 'Menu';
?>

<div class="container my-5">
    <h1 class="text-center mb-5"><?= htmlspecialchars($theme) ?></h1>

    <div class="menu-card">
        <div class="menu-top">
            <!-- Image à GAUCHE -->
            <div class="menu-left">
                <div class="menu-image-wrapper">
                    <img src="<?= htmlspecialchars($menu['image_principale']) ?>" 
                         alt="<?= htmlspecialchars($menu['titre']) ?>"
                         class="menu-image">
                </div>
            </div>

            <!-- Infos à DROITE -->
            <div class="menu-infos">
                <h2 class="menu-titre"><?= htmlspecialchars($menu['titre']) ?></h2>
                <hr class="plat-line mb-5">

                <p><?= htmlspecialchars($menu['description']) ?></p>

                <h4 class="plat-type">Entrée</h4>
                <p class="plat-nom"><?= htmlspecialchars($entree) ?></p>
                <hr class="plat-line">

                <h4 class="plat-type">Plat</h4>
                <p class="plat-nom"><?= htmlspecialchars($plat) ?></p>
                <hr class="plat-line">

                <h4 class="plat-type">Dessert</h4>
                <p class="plat-nom"><?= htmlspecialchars($dessert) ?></p>

                <p class="allergenes">
                    <strong>Allergènes :</strong><br>
                    <?= htmlspecialchars($allergenesTexte) ?>
                </p>

                <p class="prix">
                    <?= number_format($menu['prix_min'] / $menu['nombre_personnes_min'], 2, ',', ' ') ?> € par personne,<br>
                    <?= $menu['nombre_personnes_min'] ?> personnes minimum.
                </p>
            </div>
        </div>

        <!-- Conditions bien en évidence (exigence ECF) -->
        <div class="menu-bas">
            <p class="mb-1"><strong>Conditions :</strong> <?= htmlspecialchars($menu['conditions_menu']) ?></p>
            <p class="mb-0"><strong>Stock restant :</strong> <?= $menu['stock_disponible'] ?> commandes disponibles</p>
        </div>

        <!-- Bouton Commander (exigence ECF) -->
        <div class="text-center my-4">
            <a href="/vite-gourmand/livraison.php?menu_id=<?= $menu['menu_id'] ?>" class="btn btn-dark px-5 py-2">
                Commander ce menu
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>