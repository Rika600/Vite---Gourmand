<?php

class Regime
{
    private int $regime_id;
    private string $libelle;

    public function getId(): int
    {
        return $this->regime_id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }
}