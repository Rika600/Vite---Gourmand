<?php

require_once __DIR__ . '/../Repository/UtilisateurRepository.php';

class UtilisateurService
{
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(PDO $pdo)
    {
        $this->utilisateurRepository = new UtilisateurRepository($pdo);
    }

    public function inscrire(array $data): array
    {
        if (empty($data['email']) || empty($data['password']) || empty($data['nom']) || empty($data['prenom'])) {
            return ['success' => false, 'erreur' => 'Tous les champs sont obligatoires.'];
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'erreur' => 'Email invalide.'];
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{10,}$/', $data['password'])) {
            return ['success' => false, 'erreur' => 'Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.'];
        }

        if ($data['password'] !== $data['password_confirm']) {
            return ['success' => false, 'erreur' => 'Les mots de passe ne correspondent pas.'];
        }

        if ($this->utilisateurRepository->emailExists($data['email'])) {
            return ['success' => false, 'erreur' => 'Cet email est déjà utilisé.'];
        }

        $id = $this->utilisateurRepository->create(
            $data['email'], $data['password'], $data['nom'], $data['prenom'],
            $data['telephone'], $data['adresse'], $data['ville']
        );

        return ['success' => true, 'utilisateur_id' => $id];
    }

    public function connecter(string $email, string $password): array
    {
        $utilisateur = $this->utilisateurRepository->findByEmail($email);

        if (!$utilisateur) {
            return ['success' => false, 'erreur' => 'Email ou mot de passe incorrect.'];
        }

        if (!password_verify($password, $utilisateur->getPassword())) {
            return ['success' => false, 'erreur' => 'Email ou mot de passe incorrect.'];
        }

        return ['success' => true, 'utilisateur' => $utilisateur];
    }

    public function mettreAJourProfil(int $id, array $data): void
    {
        $this->utilisateurRepository->update(
            $id, $data['nom'], $data['prenom'],
            $data['telephone'], $data['adresse'], $data['ville']
        );
    }

    public function getUtilisateur(int $id): ?Utilisateur
    {
        return $this->utilisateurRepository->findById($id);
    }

    public function getEmployes(): array
    {
        return $this->utilisateurRepository->findEmployes();
    }

    public function creerEmploye(string $email, string $password): array
    {
        if ($this->utilisateurRepository->emailExists($email)) {
            return ['success' => false, 'erreur' => 'Cet email est déjà utilisé.'];
        }

        $id = $this->utilisateurRepository->create($email, $password, '', '', '', '', '', 2);
        return ['success' => true, 'utilisateur_id' => $id];
    }

    public function desactiverEmploye(int $id): void
    {
        $this->utilisateurRepository->desactiver($id);
    }

    public function reactiverEmploye(int $id): void
    {
        $this->utilisateurRepository->reactiver($id);
    }

    public function demanderResetPassword(string $email): string
    {
        $token = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->utilisateurRepository->storeResetToken($email, $token, $expiration);
        return $token;
    }

    public function verifierToken(string $token): ?Utilisateur
    {
        return $this->utilisateurRepository->findByToken($token);
    }

    public function resetPassword(string $token, string $newPassword): array
    {
        $utilisateur = $this->utilisateurRepository->findByToken($token);

        if (!$utilisateur) {
            return ['success' => false, 'erreur' => 'Token invalide ou expiré.'];
        }

        if (strlen($newPassword) < 8) {
            return ['success' => false, 'erreur' => 'Le mot de passe doit contenir au moins 8 caractères.'];
        }

        $this->utilisateurRepository->updatePassword($utilisateur->getId(), $newPassword);
        return ['success' => true];
    }
}