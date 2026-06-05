<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Services/MenuService.php';

header('Content-Type: application/json');

$pdo = Database::getConnection();

$themeId = isset($_GET['theme']) ? (int)$_GET['theme'] : 0;
$regimeId = isset($_GET['regime']) ? (int)$_GET['regime'] : 0;
$prixMax = isset($_GET['prix_max']) && $_GET['prix_max'] !== '' ? (float)$_GET['prix_max'] : null;

$sql = "
    SELECT DISTINCT m.menu_id, m.titre, m.description, m.image_principale,
           m.nombre_personnes_min, m.prix_min, m.conditions_menu, m.stock_disponible
    FROM menu m
    LEFT JOIN menu_theme mt ON m.menu_id = mt.menu_id
    LEFT JOIN menu_regime mr ON m.menu_id = mr.menu_id
    WHERE m.actif = TRUE
";

$params = [];

if ($themeId > 0) {
    $sql .= " AND mt.theme_id = :theme_id";
    $params[':theme_id'] = $themeId;
}

if ($regimeId > 0) {
    $sql .= " AND mr.regime_id = :regime_id";
    $params[':regime_id'] = $regimeId;
}

if ($prixMax !== null) {
    $sql .= " AND (m.prix_min / m.nombre_personnes_min) <= :prix_max";
    $params[':prix_max'] = $prixMax;
}

if (isset($_GET['prix_min']) && $_GET['prix_min'] !== '') {
    $prixMin = (float)$_GET['prix_min'];
    $sql .= " AND (m.prix_min / m.nombre_personnes_min) >= :prix_min";
    $params[':prix_min'] = $prixMin;
}

if (isset($_GET['personnes']) && $_GET['personnes'] !== '') {
    $personnes = (int)$_GET['personnes'];
    $sql .= " AND m.nombre_personnes_min >= :personnes";
    $params[':personnes'] = $personnes;
}

$sql .= " ORDER BY m.menu_id ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$menus = $stmt->fetchAll();

$menuService = new MenuService($pdo);
$result = [];

foreach ($menus as $menu) {
    $themes = $menuService->getThemes($menu['menu_id']);
    $menu['theme'] = isset($themes[0]) ? $themes[0]->getLibelle() : 'Menu';
    $menu['prix_personne'] = number_format($menu['prix_min'] / $menu['nombre_personnes_min'], 2, ',', ' ');
    $result[] = $menu;
}

echo json_encode($result);