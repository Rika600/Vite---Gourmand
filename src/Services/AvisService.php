<?php

require_once __DIR__ . '/../Repository/AvisRepository.php';

class AvisService
{
    private AvisRepository $avisRepository;

    public function __construct(PDO $pdo)
    {
        $this->avisRepository = new AvisRepository($pdo);
    }

    public function getAvisAccueil(int $limit = 3): array
    {
        return $this->avisRepository->findValides($limit);
    }

    public function getAvisEnAttente(): array
    {
        return $this->avisRepository->findEnAttente();
    }

    public function deposerAvis(int $commandeId, int $utilisateurId, int $note, string $commentaire): bool
    {
        if ($this->avisRepository->existsPourCommande($commandeId)) {
            return false;
        }
        $this->avisRepository->create($commandeId, $utilisateurId, $note, $commentaire);
        return true;
    }

    public function validerAvis(int $avisId): void
    {
        $this->avisRepository->valider($avisId);
    }

    public function refuserAvis(int $avisId): void
    {
        $this->avisRepository->refuser($avisId);
    }

    public function genererEtoiles(int $note): string
    {
        return str_repeat('★', $note) . str_repeat('☆', 5 - $note);
    }

    public function getCommandeIdsAvecAvis(int $utilisateurId): array
    {
        return $this->avisRepository->findCommandeIdsParUtilisateur($utilisateurId);
    }
}