<?php

require_once __DIR__ .'/../Entity/Menu.php';
require_once __DIR__ . '/../Entity/Theme.php';
require_once __DIR__ . '/../Entity/Regime.php';
require_once __DIR__ . '/../Entity/Plat.php';
require_once __DIR__ . '/../Entity/Allergene.php';

class MenuRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $sql = "
            SELECT menu_id, titre, description, image_principale,
                    nombre_personnes_min, prix_min, conditions_menu, stock_disponible, actif
            FROM menu
            WHERE actif = TRUE
            ORDER BY menu_id ASC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Menu::class);
    }

    public function findById(int $menuId): ?Menu
    {
        $sql= "
            SELECT menu_id, titre, description, image_principale,
                    nombre_personnes_min, prix_min, conditions_menu, stock_disponible, actif
            FROM menu
            WHERE menu_id = :id AND actif = TRUE
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $menuId]);
        $stmt->SetFetchMode(PDO::FETCH_CLASS, Menu::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findPlats(int $menuId): array
    {
        $sql = "
            SELECT p.plat_id, p.nom, p.type, p.description
            FROM plat p
            JOIN menu_plat mp ON p.plat_id = mp.plat_id
            WHERE mp.menu_id = :menu_id
            ORDER BY FIELD(p.type, 'entree', 'plat', 'dessert')
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':menu_id' => $menuId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Plat::class);
    }

    public function findThemes(int $menuId): array
    {
        $sql = "
            SELECT t.libelle
            FROM theme t
            JOIN menu_theme mt ON t.theme_id = mt.theme_id
            WHERE mt.menu_id = :menu_id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':menu_id' => $menuId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Theme::class);
    }

    public function findAllergenes(int $menuId): array
    {
        $sql = "
            SELECT DISTINCT a.libelle
            FROM allergene a
            JOIN plat_allergene pa ON a.allergene_id = pa.allergene_id
            JOIN menu_plat mp ON pa.plat_id = mp.plat_id
            WHERE mp.menu_id = :menu_id
            ORDER BY a.libelle
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':menu_id' => $menuId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Allergene::class);
    }

    public function findAllThemes(): array
    {
        $sql = "SELECT theme_id, libelle FROM theme ORDER BY libelle";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Theme::class);
    }

    public function findAllRegimes(): array
    {
        $sql = "SELECT regime_id, libelle FROM regime ORDER BY libelle";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Regime::class);
    }

    public function desactiver(int $menuId): void
    {
        $stmt = $this->pdo->prepare("UPDATE menu SET actif = 0 WHERE menu_id = :id");
        $stmt->execute([':id' => $menuId]);
    }
}