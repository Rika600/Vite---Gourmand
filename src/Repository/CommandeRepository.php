<?php 

require_once __DIR__ . '/../Entity/Commande.php';

class CommandeRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo= $pdo;
    }

    public function create(array $data): int 
    {
        $sql= "INSERT INTO commande (numero_commande, utilisateur_id, menu_id, date_livraison, 
                heure_livraison, adresse_livraison, ville_livraison, distance_km, nombre_personnes,
                prix_menu_unitaire, prix_menu_total, prix_livraison, reduction, prix_total, pret_materiel)
                VALUES (:numero, :utilisateur_id, :menu_id, :date_livraison, :heure_livraison,
                :adresse_livraison, :ville_livraison, :distance_km, :nombre_personnes,
                :prix_unitaire, :prix_menu_total, :prix_livraison, :reduction, :prix_total, :pret_materiel)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function findByUtilisateur(int $utilisateurId): array
    {
        $sql = "SELECT c.*, m.titre 
                FROM commande c 
                JOIN menu m ON c.menu_id = m.menu_id 
                WHERE c.utilisateur_id = :id 
                ORDER BY c.date_commande DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $utilisateurId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Commande::class);
    }

    public function annuler(int $commandeId, int $utilisateurId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM commande WHERE commande_id = :id AND utilisateur_id = :user_id AND statut = 'en_attente'"
        );
        $stmt->execute([':id' => $commandeId, ':user_id' => $utilisateurId]);
        if ($stmt->fetch()) {
            $stmt = $this->pdo->prepare("UPDATE commande SET statut = 'annulee' WHERE commande_id = :id");
            $stmt->execute([':id' => $commandeId]);
            return true;
        }
        return false;
    }

    public function modifier(int $commandeId, int $utilisateurId, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM commande WHERE commande_id = :id AND utilisateur_id = :user_id AND statut = 'en_attente'"
        );
        $stmt->execute([':id' => $commandeId, ':user_id' => $utilisateurId]);
        if ($stmt->fetch()) {
            $sql = "UPDATE commande SET date_livraison = :date, heure_livraison = :heure,
                    adresse_livraison = :adresse, ville_livraison = :ville, nombre_personnes = :nb
                    WHERE commande_id = :id";
            $data[':id'] = $commandeId;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            return true;
        }
        return false;
    }

      public function changerStatut(int $commandeId, string $statut, ?string $motif = null, ?string $modeContact = null): void
    {
        $sql = "UPDATE commande SET statut = :statut";
        $params = [':statut' => $statut, ':id' => $commandeId];

        if ($statut === 'annulee') {
            $sql .= ", motif_annulation = :motif, mode_contact_annulation = :mode";
            $params[':motif'] = $motif;
            $params[':mode'] = $modeContact;
        }

        $sql .= " WHERE commande_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function ajouterSuivi(int $commandeId, string $statut): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO suivi_commande (commande_id, statut) VALUES (:id, :statut)");
        $stmt->execute([':id' => $commandeId, ':statut' => $statut]);
    }

    public function findSuivi(int $commandeId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT statut, date_changement FROM suivi_commande WHERE commande_id = :id ORDER BY date_changement ASC"
        );
        $stmt->execute([':id' => $commandeId]);
        return $stmt->fetchAll();
    }

    public function findAllWithFilters(?string $statut = null, ?string $nomClient = null): array
    {
        $sql = "SELECT c.*, m.titre, u.nom, u.prenom, u.email 
                FROM commande c 
                JOIN menu m ON c.menu_id = m.menu_id 
                JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id 
                WHERE 1=1";
        $params = [];

        if ($statut) {
            $sql .= " AND c.statut = :statut";
            $params[':statut'] = $statut;
        }
        if ($nomClient) {
             $sql .= " AND (u.nom LIKE :nom1 OR u.prenom LIKE :nom2)";
             $params[':nom1'] = '%' . $nomClient . '%';
             $params[':nom2'] = '%' . $nomClient . '%';
        }

        $sql .= " ORDER BY c.date_commande DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findClientInfoByCommande(int $commandeId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.email, u.prenom, c.numero_commande 
             FROM commande c 
             JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id 
             WHERE c.commande_id = :id"
        );
        $stmt->execute([':id' => $commandeId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}