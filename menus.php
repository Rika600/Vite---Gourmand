<?php
$pageTitle = 'Nos Menus - Vite & Gourmand';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Models/Menu.php';

$pdo = Database::getConnection();
$menuModel = new Menu($pdo);
$menus = $menuModel->getAll();
?>

<div class="container my-5">
    <h1 class="text-center mb-5">Menus</h1>

    <div class="menus-grid">

    <?php foreach ($menus as $menu) : ?>
        <?php
        $themes = $menuModel->getThemes($menu['menu_id']);
        $theme = $themes[0]['libelle'] ?? 'Menu';
        ?>

        <div class="menu-wrapper">
            <h2 class="text-center theme-titre"><?= htmlspecialchars($theme) ?></h2>
            <hr class="theme-line">

            <div class="menu-card">
                <div class="menu-top">
                    <div class="menu-left">
                        <div class="menu-image-wrapper">
                            <img src="<?= htmlspecialchars($menu['image_principale']) ?>" 
                                 alt="<?= htmlspecialchars($menu['titre']) ?>"
                                 class="menu-image">
                            <a href="/vite-gourmand/detail-menus.php?id=<?= $menu['menu_id'] ?>" class="menu-overlay">
                                <span class="overlay-button">Voir le détail</span>
                            </a>
                        </div>
                    </div>

                    <div class="menu-infos">
                        <h3 class="menu-titre"><?= htmlspecialchars($menu['titre']) ?></h3>
                        <hr class="plat-line mb-5">

                        <p class="plat-nom"><?= htmlspecialchars($menu['description']) ?></p>

                        <p class="prix">
                            <?= number_format($menu['prix_min'] / $menu['nombre_personnes_min'], 2, ',', ' ') ?> € par personne,<br>
                            <?= $menu['nombre_personnes_min'] ?> personnes minimum.
                        </p>

                        <a href="/vite-gourmand/detail-menus.php?id=<?= $menu['menu_id'] ?>" class="btn btn-dark">
                            Voir le détail
                        </a>
                    </div>
                </div>
            </div>
        </div>

    <?php endforeach; ?>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>