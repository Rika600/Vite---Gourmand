<?php

class Theme
{
    private int $theme_id;
    private string $libelle;

    public function getId(): int
    {
        return $this->theme_id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }
}