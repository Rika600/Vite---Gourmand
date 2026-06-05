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
        $noms = [];
        foreach ($allergenes as $a) {
            $noms[] = $a->getLibelle();
        }
        return implode(', ', $noms);
    }

    public function separerPlatsParType(array $plats): array
    {
        $result = ['entree' => null, 'plat' => null, 'dessert' => null];
        foreach ($plats as $p) {
            $type = $p->getType() ?? '';
            if (isset($result[$type])) {
                $result[$type] = $p->getNom();
            }
        }
        return $result;
    }

    public function desactiverMenu(int $menuId): void
    {
        $this->menuRepository->desactiver($menuId);
    }

    public function getThemes(int $menuId) : array
    {
        return  $this->menuRepository->findThemes($menuId);
    }
}
