<?php
$pageTitle = 'Galerie - Vite & Gourmand';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Services/MenuService.php';

$pdo = Database::getConnection();
$menuService = new MenuService($pdo);
$menus = $menuService->getMenusActifs();
?>

<div class="container my-5">
    <h1 class="text-center mb-5">Galerie</h1>

    <div class="row g-5">
        <?php foreach ($menus as $menu) : ?>
            <?php
            $themes = $menuService->getThemes($menu->getId());
            $theme = $themes[0]->getLibelle() ?? 'Menu';
            ?>
            <div class="col-md-6 mb-5">
                <div class="text-center mt-4">
                    <a href="<?= BASE_URL ?>pages/detail-menus.php?id=<?= $menu->getId() ?>">
                      <img src="<?= BASE_URL ?><?= htmlspecialchars($menu->getImage()) ?>" 
                             alt="<?= htmlspecialchars($menu->getTitre()) ?>"
                             class="img-fluid"
                             style="width: 100%; height: 300px; object-fit: cover;">
                    </a>
                    <h3 class="mt-3 mb-2"><?= htmlspecialchars($theme) ?></h3>
                    <p><?= htmlspecialchars($menu->getTitre()) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>