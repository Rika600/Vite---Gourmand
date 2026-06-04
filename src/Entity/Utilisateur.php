<? php 

class Utilisateur
{
    private int $utlisateur_id;
    private string $email;
    private string $password;
    private string $nom;
    private string $prenom;
    private string $telephone;
    private string $adresse_postale;
    private string $ville;
    private string $pays;
    private bool $actif;
    private string $date_creation;
    private int $role_id;
    private ?string $token_reset;
    private ?string $token_expiration;

    public function getId(): int
    {
        return $this->utlisateur_id;
    }

    
    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getAdresse(): string
    {
        return $this->adresse_postale;
    }

    public function getVille(): string
    {
        return $this->ville;
    }

    public function getPays(): string
    {
        return $this->pays;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function getDateCreation(): string
    {
        return $this->date_creation;
    }

    public function getRoleId(): int
    {
        return $this->role_id;
    }

    public function getTokenReset(): ?string
    {
        return $this->token_reset;
    }

    public function getTokenExpiration(): ?string
    {
        return $this->token_expiration;
    }

    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}