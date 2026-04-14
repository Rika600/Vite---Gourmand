<?php

class Avis {
    private PDO $pdo;

    // Constructeur : reçoit la connexion PDO
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;

    }
    
    /*
     * Récupère les N derniers avis validés avec les infos de l'utilisateur
   */

    public function getAvisValides(int $limit = 3): array {
        $sql = "
        SELECT a.note, a.commentaire, u.prenom, u.nom 
        FROM avis a
        JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
        WHERE a.statut_validation = 'valide'
        ORDER BY a.date_validation DESC
        LIMIT :limit

        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchALL();
    } 
   }