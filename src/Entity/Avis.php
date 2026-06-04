<?php

class Avis
{
    private int $avis_id;
    private int $commande_id;
    private int $utilisateur_id;
    private int $note;
    private string $commentaire;
    private string $date_creation;
    private string $statut_validation;
    private ? string $date_validation;
    private ? string $prenom = null;
    private ? string $nom = null;

    public function getId(): int 
    {
        return $this->avis_id;
    }

    public function getCommandeId(): int
    {
        return $this->commande_id;
    }

    public function getUtilisateurId(): int
    {
        return $this->utilisateur_id;
    }

    public function getNote(): int
    {
        return $this->note;
    }

    public function getCommentaire(): string
    {
        return $this->commentaire;
    }

    public function getDateCreation(): string
    {
        return $this->date_creation;
    }

    public function getStatutValidation(): string
    {
        return $this->statut_validation;
    }

    public function getDateValidation(): ?string
    {
        return $this->date_validation;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getInitialeNom(): string
    {
        return $this->nom ? mb_substr($this->nom, 0, 1) . '.' : '';
    }

}