<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Services/MenuService.php';
require_once __DIR__ . '/../src/Services/CommandeService.php';

header('Content-Type: application/json');

$pdo = Database::getConnection();
$menuService = new MenuService($pdo);
$commandeService = new CommandeService($pdo);

$menuId = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : 0;
$nbPersonnes = isset($_GET['nb_personnes']) ? (int)$_GET['nb_personnes'] : 0;
$ville = isset($_GET['ville']) ? trim($_GET['ville']) : '';

if ($menuId === 0 || $nbPersonnes === 0) {
    echo json_encode(['erreur' => 'Données manquantes']);
    exit;
}

$menuComplet = $menuService->getMenuComplet($menuId);

if (!$menuComplet) {
    echo json_encode(['erreur' => 'Menu introuvable']);
    exit;
}

$menu = $menuComplet['menu'];

if ($nbPersonnes < $menu->getNbPersonnesMin()) {
    echo json_encode(['erreur' => 'Minimum ' . $menu->getNbPersonnesMin() . ' personnes']);
    exit;
}

$prix = $commandeService->calculerPrix(
    $menu->getPrix(),
    $menu->getNbPersonnesMin(),
    $nbPersonnes,
    $ville,
    0
);

echo json_encode([
    'prix_menu' => number_format($prix['prix_menu_total'], 2, ',', ' '),
    'prix_livraison' => number_format($prix['prix_livraison'], 2, ',', ' '),
    'reduction' => number_format($prix['reduction'], 2, ',', ' '),
    'total' => number_format($prix['total'], 2, ',', ' ')
]);