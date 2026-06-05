<?php
$pageTitle = 'Détail du menu - Vite & Gourmand';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Services/MenuService.php';

// 1. Récupérer l'id depuis l'URL
$menuId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Connexion BDD + instancier le modèle
$pdo = Database::getConnection();
$menuService = new MenuService($pdo);

// 3. Récupérer CE menu
$menuComplet = $menuService->getMenuComplet($menuId);

// 4. Si le menu n'existe pas → message d'erreur
if (!$menuComplet) {
    echo '<div class="container my-5"><p>Menu introuvable.</p></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// 5. Extraire les données du menu complet
$menu = $menuComplet['menu'];
$platsParType = $menuService->separerPlatsParType($menuComplet['plats']);
$allergenesTexte = $menuService->formaterAllergenes($menuComplet['allergenes']);
$theme = $menuComplet['themes'][0]->getLibelle() ?? 'Menu';
?>

<div class="container my-5">
    <h1 class="text-center mb-5"><?= htmlspecialchars($theme) ?></h1>

    <div class="menu-card">
        <div class="menu-top">
            <!-- Image à GAUCHE -->
            <div class="menu-left">
                <div class="menu-image-wrapper">
                    <img src="<?= BASE_URL ?><?= htmlspecialchars($menu->getImage()) ?>" 
                         alt="<?= htmlspecialchars($menu->getTitre()) ?>"
                         class="menu-image">
                </div>
            </div>

            <!-- Infos à DROITE -->
            <div class="menu-infos">
                <h2 class="menu-titre"><?= htmlspecialchars($menu->getTitre()) ?></h2>
                <hr class="plat-line mb-5">

                <p><?= htmlspecialchars($menu->getDescription()) ?></p>

                <h4 class="plat-type">Entrée</h4>
                <p class="plat-nom"><?= htmlspecialchars($platsParType['entree']) ?></p>
                <hr class="plat-line">

                <h4 class="plat-type">Plat</h4>
                <p class="plat-nom"><?= htmlspecialchars( $platsParType['plat']) ?></p>
                <hr class="plat-line">

                <h4 class="plat-type">Dessert</h4>
                <p class="plat-nom"><?= htmlspecialchars($platsParType['dessert']) ?></p>

                <p class="allergenes">
                    <strong>Allergènes :</strong><br>
                    <?= htmlspecialchars($allergenesTexte) ?>
                </p>

                <p class="prix">
                    <?= number_format($menu->getPrix() / $menu->getNbPersonnesMin(), 2, ',', ' ') ?> € par personne,<br>
                    <?= $menu->getNbPersonnesMin() ?> personnes minimum.
                </p>
            </div>
        </div>

        <!-- Conditions bien en évidence -->
        <div class="menu-bas">
            <p class="mb-1"><strong>Conditions :</strong> <?= htmlspecialchars($menu->getConditions()) ?></p>
            <p class="mb-0"><strong>Stock restant :</strong> <?=  $menu->getStock() ?> commandes disponibles</p>
        </div>

        <!-- Bouton Commander -->
        <div class="text-center my-4">
            <a href="<?= BASE_URL ?>pages/livraison.php?menu_id=<?=  $menu->getId() ?>" class="btn btn-dark px-5 py-2">
                Commander ce menu
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>