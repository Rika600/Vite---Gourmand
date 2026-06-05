<?php

require_once __DIR__ . '/../Repository/CommandeRepository.php';

class CommandeService
{
    private CommandeRepository $commandeRepository;

    public function __construct(PDO $pdo)
    {
        $this->commandeRepository = new CommandeRepository($pdo);
    }

    public function calculerPrix(float $prixMin, int $nbPersonnesMin, int $nbPersonnes,
                                 string $villeLivraison, float $distanceKm): array
    {
        $prixParPersonne = $prixMin / $nbPersonnesMin;
        $prixMenuTotal = $prixParPersonne * $nbPersonnes;

        $prixLivraison = 0;
        if (strtolower($villeLivraison) !== 'bordeaux') {
            $prixLivraison = 5 + (0.59 * $distanceKm);
        }

        $reduction = 0;
        if ($nbPersonnes >= $nbPersonnesMin + 5) {
            $reduction = $prixMenuTotal * 0.10;
        }

        $total = $prixMenuTotal - $reduction + $prixLivraison;

        return [
            'prix_par_personne' => $prixParPersonne,
            'prix_menu_total' => $prixMenuTotal,
            'prix_livraison' => $prixLivraison,
            'reduction' => $reduction,
            'total' => $total
        ];
    }

    public function genererNumero(): string
    {
        return 'CMD-' . date('Ymd') . '-' . rand(1000, 9999);
    }

    public function creerCommande(int $utilisateurId, int $menuId, array $infos, array $prix): int
    {
        $data = [
            ':numero' => $this->genererNumero(),
            ':utilisateur_id' => $utilisateurId,
            ':menu_id' => $menuId,
            ':date_livraison' => $infos['date_livraison'],
            ':heure_livraison' => $infos['heure_livraison'],
            ':adresse_livraison' => $infos['adresse_livraison'],
            ':ville_livraison' => $infos['ville_livraison'],
            ':distance_km' => $infos['distance_km'],
            ':nombre_personnes' => $infos['nombre_personnes'],
            ':prix_unitaire' => $prix['prix_par_personne'],
            ':prix_menu_total' => $prix['prix_menu_total'],
            ':prix_livraison' => $prix['prix_livraison'],
            ':reduction' => $prix['reduction'],
            ':prix_total' => $prix['total'],
            ':pret_materiel' => $infos['pret_materiel'] ?? 0
        ];
        return $this->commandeRepository->create($data);
    }

    public function getCommandesUtilisateur(int $utilisateurId): array
    {
        return $this->commandeRepository->findByUtilisateur($utilisateurId);
    }

    public function annulerCommande(int $commandeId, int $utilisateurId): bool
    {
        return $this->commandeRepository->annuler($commandeId, $utilisateurId);
    }

    public function modifierCommande(int $commandeId, int $utilisateurId, array $data): bool
    {
        return $this->commandeRepository->modifier($commandeId, $utilisateurId, $data);
    }

    public function changerStatut(int $commandeId, string $statut, ?string $motif = null, ?string $modeContact = null): void
    {
        $this->commandeRepository->changerStatut($commandeId, $statut, $motif, $modeContact);
        $this->commandeRepository->ajouterSuivi($commandeId, $statut);
    }

    public function getSuivi(int $commandeId): array
    {
        return $this->commandeRepository->findSuivi($commandeId);
    }

    public function getCommandesFiltrees(?string $statut = null, ?string $nomClient = null): array
    {
        return $this->commandeRepository->findAllWithFilters($statut, $nomClient);
    }

    public function getInfosClientCommande(int $commandeId): ?array
    {
        return $this->commandeRepository->findClientInfoByCommande($commandeId);
    }
}