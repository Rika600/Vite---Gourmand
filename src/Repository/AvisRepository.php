<?php 

 require_once __DIR__ . '/../Entity/Avis.php';

class AvisRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findValides(int $limit = 3): array
    {
        $sql = "
            SELECT a.avis_id, a.note, a.commentaire, a.date_creation,
                   a.statut_validation, a.date_validation,
                   a.commande_id, a.utilisateur_id,
                   u.prenom, u.nom
            FROM avis a
            JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id 
            WHERE a.statut_validation ='valide'
            ORDER BY a.date_validation DESC
            LIMIT :limit
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchALL(PDO::FETCH_CLASS, Avis::class);
    }

    public function findEnAttente(): array
    {
        $sql = "
            SELECT a.avis_id, a.note, a.commentaire, a.date_creation,
                   a.statut_validation, a.commande_id, a.utilisateur_id,
                   u.prenom, u.nom, m.titre AS menu_titre
            FROM avis a
            JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
            JOIN commande c ON a.commande_id = c.commande_id
            JOIN menu m ON c.menu_id = m.menu_id
            WHERE a.statut_validation = 'en_attente'
            ORDER BY a.date_creation DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Avis::class);
    }

    public function create (int $commandeId, int $utilisateurId, int $note, string $commentaire): void
    {
         $sql = "INSERT INTO avis (commande_id, utilisateur_id, note, commentaire)
                VALUES (:commande_id, :utilisateur_id, :note, :commentaire)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':commande_id' => $commandeId,
            ':utilisateur_id' => $utilisateurId,
            ':note' => $note,
            ':commentaire' => $commentaire
        ]);
    }

    public function existsPourCommande(int $commandeId): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM avis WHERE commande_id = :id");
        $stmt->execute([':id => $commandeId']);
        return $stmt->fetchColumn() > 0;
    }

    public function valider (int $avisId): void
    {
        $sql = "UPDATE avis SET statut_validation = 'valide', date_validation = NOW() WHERE avis_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $avisId]);
    }

    public function refuser(int $avisId): void
    {
        $sql = "UPDATE avis SET statut_validation = 'refuse', date_validation = NOW() WHERE avis_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id'=> $avisId]);
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
            $sql .= " AND (u.nom LIKE :nom OR u.prenom LIKE :nom)";
            $params[':nom'] = '%' . $nomClient . '%';
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

    public function findCommandeIdsParUtilisateur(int $utilisateurId): array
    {
        $stmt = $this->pdo->prepare("SELECT commande_id FROM avis WHERE utilisateur_id = :id");
        $stmt->execute([':id' => $utilisateurId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}