<?php

require_once __DIR__ . '/../Entity/Utilisateur.php';

class UtilisateurRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?Utilisateur
    {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = :email AND actif = 1");
        $stmt->execute([':email' => $email]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Utilisateur::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() !== false;
    }

    public function findById(int $id): ?Utilisateur
    {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE utilisateur_id = :id");
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Utilisateur::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(string $email, string $password, string $nom, string $prenom,
                          string $telephone, string $adresse, string $ville, int $roleId = 3): int
    {
        $sql = "INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, ville, role_id)
                VALUES (:email, :password, :nom, :prenom, :telephone, :adresse, :ville, :role_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_BCRYPT),
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':telephone' => $telephone,
            ':adresse' => $adresse,
            ':ville' => $ville,
            ':role_id' => $roleId
        ]);
        return (int) $this->pdo->lastInsertId();
    }  

     public function update(int $id, string $nom, string $prenom, string $telephone,
                          string $adresse, string $ville): void
    {
        $sql = "UPDATE utilisateur SET nom = :nom, prenom = :prenom, telephone = :telephone,
                adresse_postale = :adresse, ville = :ville WHERE utilisateur_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':telephone' => $telephone,
            ':adresse' => $adresse,
            ':ville' => $ville,
            ':id' => $id
        ]);
    }

    public function findEmployes(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM utilisateur WHERE role_id = 2 ORDER BY email");
        return $stmt->fetchAll(PDO::FETCH_CLASS, Utilisateur::class);
    }

    public function desactiver(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE utilisateur SET actif = 0 WHERE utilisateur_id = :id AND role_id = 2");
        $stmt->execute([':id' => $id]);
    }

    public function reactiver(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE utilisateur SET actif = 1 WHERE utilisateur_id = :id AND role_id = 2");
        $stmt->execute([':id' => $id]);
    }

    public function storeResetToken(string $email, string $token, string $expiration): void
    {
        $sql = "UPDATE utilisateur SET token_reset = :token, token_expiration = :expiration WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token, ':expiration' => $expiration, ':email' => $email]);
    }

    public function findByToken(string $token): ?Utilisateur
    {
        $sql = "SELECT * FROM utilisateur WHERE token_reset = :token AND token_expiration > NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Utilisateur::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function updatePassword(int $id, string $newPassword): void
    {
        $sql = "UPDATE utilisateur SET password = :password, token_reset = NULL, token_expiration = NULL 
                WHERE utilisateur_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':password' => password_hash($newPassword, PASSWORD_BCRYPT),
            ':id' => $id
        ]);
    }
}