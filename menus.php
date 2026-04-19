<?php
$pageTitle = 'Nos Menus - Vite & Gourmand';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Models/Menu.php';

$pdo = Database::getConnection();
$menuModel = new Menu($pdo);
$menus = $menuModel->getAll();

// Récupérer les thèmes et régimes pour les filtres
$themes = $pdo->query("SELECT * FROM theme ORDER BY libelle")->fetchAll();
$regimes = $pdo->query("SELECT * FROM regime ORDER BY libelle")->fetchAll();
?>


    <!-- ========== FILTRES========== -->
    <div class="container my-5">

    <!-- Icône filtre + Titre -->
    <div class="d-flex align-items-center mb-5">
     <button id="btn-toggle-filtres" class="btn p-0 me-3" aria-label="Ouvrir les filtres">
    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16">
        <path d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
    </svg>
</button>  
        <h1 class="text-center flex-grow-1 m-0">Menus</h1>
    </div>

    <!-- Panneau filtres déroulant (caché par défaut) -->
    <div id="filtres-panel" class="filtres-container mb-4" style="display: none;">

      <div class="row g-3 flex-column" style="max-width: 250px;">
            <div class="col-md-2">
                <label for="filtre-theme" class="form-label">Thème</label>
                <select id="filtre-theme" class="form-select">
                    <option value="">Tous</option>
                    <?php foreach ($themes as $t) : ?>
                        <option value="<?= $t['theme_id'] ?>"><?= htmlspecialchars($t['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="filtre-regime" class="form-label">Régime</label>
                <select id="filtre-regime" class="form-select">
                    <option value="">Tous</option>
                    <?php foreach ($regimes as $r) : ?>
                        <option value="<?= $r['regime_id'] ?>"><?= htmlspecialchars($r['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="filtre-prix-max" class="form-label">Prix max (€/pers)</label>
                <select id="filtre-prix-max" class="form-select">
                    <option value="">Tous</option>
                    <option value="45">45 €</option>
                    <option value="50">50 €</option>
                    <option value="65">65 €</option>
                    <option value="100">100 €</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="filtre-prix-min" class="form-label">Prix min (€/pers)</label>
                <select id="filtre-prix-min" class="form-select">
                    <option value="">Tous</option>
                    <option value="42">42 €</option>
                    <option value="45">45 €</option>
                    <option value="50">50 €</option>
                    <option value="65">65 €</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="filtre-personnes" class="form-label">Personnes min</label>
                <select id="filtre-personnes" class="form-select">
                    <option value="">Tous</option>
                    <option value="8">8</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
            </div>

        </div>

        <div class="mt-3">
            <button id="btn-filtrer" class="btn btn-dark px-4">Filtrer</button>
            <button id="btn-reset" class="btn btn-outline-dark px-4 ms-2">Réinitialiser</button>
        </div>
    </div>

    <!-- ========== GRILLE DES MENUS ========== -->
    <div class="menus-grid" id="menus-grid">

    <?php foreach ($menus as $menu) : ?>
        <?php
        $themesMenu = $menuModel->getThemes($menu['menu_id']);
        $theme = $themesMenu[0]['libelle'] ?? 'Menu';
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

<script src="/vite-gourmand/js/filtres.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>