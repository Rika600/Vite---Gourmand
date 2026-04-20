<?php
$pageTitle = 'Galerie - Vite & Gourmand';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Models/Menu.php';

$pdo = Database::getConnection();
$menuModel = new Menu($pdo);
$menus = $menuModel->getAll();
?>

<div class="container my-5">
    <h1 class="text-center mb-5">Galerie</h1>

    <div class="row g-5">
        <?php foreach ($menus as $menu) : ?>
            <?php
            $themes = $menuModel->getThemes($menu['menu_id']);
            $theme = $themes[0]['libelle'] ?? 'Menu';
            ?>
            <div class="col-md-6 mb-5">
                <div class="text-center mt-4">
                    <a href="/vite-gourmand/detail-menus.php?id=<?= $menu['menu_id'] ?>">
                        <img src="<?= htmlspecialchars($menu['image_principale']) ?>" 
                             alt="<?= htmlspecialchars($menu['titre']) ?>"
                             class="img-fluid"
                             style="width: 100%; height: 300px; object-fit: cover;">
                    </a>
                    <h3 class="mt-3 mb-2"><?= htmlspecialchars($theme) ?></h3>
                    <p><?= htmlspecialchars($menu['titre']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>