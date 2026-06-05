<?php

class Plat
{
    private int $plat_id;
    private string $nom;
    private string $type;
    private ?string $description;

    public function getId(): int
    {
        return $this->plat_id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}