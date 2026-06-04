<?php 

class Menu 
{

    private int $menu_id;
    private string $titre;
    private string $description;
    private ?string $image_principale;
    private int $nombre_personnes_min;
    private float $prix_min;
    private ?string $conditions_menu;
    private int $stock_disponible;
    private bool $actif; 

    public function getId(): int 
    {
        return $this->menu_id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImage(): ?string
    {
        return $this->image_principale;
    }

    public function getNbPersonnesMin(): int 
    {
        return $this->nombre_personnes_min;
    }

    public function getPrix(): float
    {
        return $this->prix_min;
    }

    public function getConditions(): ?string 
    {
        return $this->conditions_menu;
    }

    public function getStock(): int 
    {
        return $this->stock_disponible;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }
    
}