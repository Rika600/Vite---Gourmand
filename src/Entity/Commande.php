<?php

class Commande
{
    private int $commande_id;
    private string $numero_commande;
    private int $utilisateur_id;
    private int $menu_id;
    private string $date_commande;
    private string $date_livraison;
    private string $heure_livraison;
    private string $adresse_livraison;
    private string $ville_livraison;
    private float $distance_km;
    private int $nombre_personnes;
    private float $prix_menu_unitaire;
    private float $prix_menu_total;
    private float $prix_livraison;
    private float $reduction;
    private float $prix_total;
    private string $statut;
    private bool $pret_materiel;
    private bool $materiel_restitue; 
    private ?string $motif_annulation;
    private ?string $mode_contact_annulation;
    private ?string $titre = null; 

    public function getId(): int
    {
        return $this->commande_id;
    }

    public function getNumero(): string
    {
        return $this->numero_commande;
    }

    public function getUtilisateurId(): int
    {
        return $this->utilisateur_id;
    }

    public function getMenuId(): int
    {
        return $this->menu_id;
    }

    public function getDateCommande(): string
    {
        return $this->date_commande;
    }

    public function getDateLivraison(): string
    {
        return $this->date_livraison;
    }

    public function getHeureLivraison(): string
    {
        return $this->heure_livraison;
    }

    public function getAdresseLivraison(): string
    {
        return $this->adresse_livraison;
    }

    public function getVilleLivraison(): string
    {
        return $this->ville_livraison;
    }

    public function getDistanceKm(): float
    {
        return $this->distance_km;
    }

    public function getNbPersonnes(): int
    {
        return $this->nombre_personnes;
    }

    public function getPrixMenuUnitaire(): float
    {
        return $this->prix_menu_unitaire;
    }

    public function getPrixMenuTotal(): float
    {
        return $this->prix_menu_total;
    }

    public function getPrixLivraison(): float
    {
        return $this->prix_livraison;
    }

    public function getReduction(): float
    {
        return $this->reduction;
    }

    public function getPrixTotal(): float
    {
        return $this->prix_total;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function hasPretMateriel(): bool
    {
        return $this->pret_materiel;
    }

    public function isMaterielRestitue(): bool
    {
        return $this->materiel_restitue;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motif_annulation;
    }

    public function getModeContactAnnulation(): ?string
    {
        return $this->mode_contact_annulation;
    }

    public function getTitreMenu(): ?string
    {
        return $this->titre;
    }

}