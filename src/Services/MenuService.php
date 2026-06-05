<?php

require_once __DIR__ . '/../Repository/MenuRepository.php';

class MenuService
{
    private MenuRepository $menuRepository;

    public function __construct(PDO $pdo)
    {
        $this->menuRepository = new MenuRepository($pdo);
    }

     public function getMenusActifs(): array
    {
        return $this->menuRepository->findAll();
    }

    public function getMenuComplet(int $menuId): ?array
    {
        $menu = $this->menuRepository->findById($menuId);
        if (!$menu) {
            return null;
        }

        return [
            'menu' => $menu,
            'plats' => $this->menuRepository->findPlats($menuId),
            'themes' => $this->menuRepository->findThemes($menuId),
            'allergenes' => $this->menuRepository->findAllergenes($menuId)
        ];
    }

    public function getFiltresData(): array
    {
        return [
            'themes' => $this->menuRepository->findAllThemes(),
            'regimes' => $this->menuRepository->findAllRegimes()
        ];
    }

    public function formaterAllergenes(array $allergenes): string
    {
        return implode(', ', array_column($allergenes, 'libelle'));
    }

    public function separerPlatsParType(array $plats): array
    {
        $result = ['entree' => null, 'plat' => null, 'dessert' => null];
        foreach ($plats as $p) {
            $type = $p['type'] ?? '';
            if (isset($result[$type])) {
                $result[$type] = $p['nom'];
            }
        }
        return $result;
    }

    public function desactiverMenu(int $menuId): void
    {
        $this->menuRepository->desactiver($menuId);
    }
}
