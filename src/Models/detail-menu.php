<?php

class Menu {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère tous les menus actifs
     */
    public function getAll(): array {
        $sql = "
            SELECT menu_id, titre, description, image_principale,
                   nombre_personnes_min, prix_min, conditions_menu, stock_disponible
            FROM menu
            WHERE actif = TRUE
            ORDER BY menu_id ASC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
 * Récupère UN seul menu par son ID
 */
public function getById(int $menuId): ?array {
    $sql = "
        SELECT menu_id, titre, description, image_principale,
               nombre_personnes_min, prix_min, conditions_menu, stock_disponible
        FROM menu
        WHERE menu_id = :id AND actif = TRUE
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $menuId]);
    $result = $stmt->fetch();
    return $result ?: null;
}

    /**
     * Récupère les plats associés à un menu donné
     */
    public function getPlats(int $menuId): array {
        $sql = "
            SELECT p.plat_id, p.nom, p.type, p.description
            FROM plat p
            JOIN menu_plat mp ON p.plat_id = mp.plat_id
            WHERE mp.menu_id = :menu_id
            ORDER BY FIELD(p.type, 'entree', 'plat', 'dessert')
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':menu_id' => $menuId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les thèmes associés à un menu donné
     */
    public function getThemes(int $menuId): array {
        $sql = "
            SELECT t.libelle
            FROM theme t
            JOIN menu_theme mt ON t.theme_id = mt.theme_id
            WHERE mt.menu_id = :menu_id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':menu_id' => $menuId]);
        return $stmt->fetchAll();
    }
    /**
     * Récupère TOUS les allergènes uniques d'un menu donné
     */
    public function getAllergenes(int $menuId): array {
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
        return $stmt->fetchAll();
    }

}
