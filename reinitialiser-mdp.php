<?php
session_start();
$pageTitle = 'Réinitialiser le mot de passe - Vite & Gourmand';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/src/Database.php';

$pdo = Database::getConnection();
$message_succes = '';
$message_erreur = '';
$token_valide = false;

$token = $_GET['token'] ?? '';

if ($token !== '') {
    // Vérifier si le token existe et n'est pas expiré
    $sql = "SELECT utilisateur_id FROM utilisateur WHERE token_reset = :token AND token_expiration > NOW()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':token' => $token]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur) {
        $token_valide = true;
    } else {
        $message_erreur = 'Ce lien est invalide ou a expiré.';
    }
} else {
    $message_erreur = 'Aucun token fourni.';
}

// Traitement du nouveau mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $mdp = $_POST['password'] ?? '';
    $mdp_confirm = $_POST['password_confirm'] ?? '';

    if ($mdp === '' || $mdp_confirm === '') {
        $message_erreur = 'Veuillez remplir tous les champs.';
        $token_valide = true;
    } else if ($mdp !== $mdp_confirm) {
        $message_erreur = 'Les mots de passe ne correspondent pas.';
        $token_valide = true;
    } else if (strlen($mdp) < 8) {
        $message_erreur = 'Le mot de passe doit contenir au moins 8 caractères.';
        $token_valide = true;
    } else {
        // Vérifier le token encore une fois
        $sql = "SELECT utilisateur_id FROM utilisateur WHERE token_reset = :token AND token_expiration > NOW()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur) {
            // Mettre à jour le mot de passe
            $hash = password_hash($mdp, PASSWORD_BCRYPT);
            $sql = "UPDATE utilisateur SET password = :mdp, token_reset = NULL, token_expiration = NULL WHERE utilisateur_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':mdp' => $hash,
                ':id' => $utilisateur['utilisateur_id']
            ]);
            $message_succes = 'Votre mot de passe a été réinitialisé. Vous pouvez vous connecter.';
            $token_valide = false;
        } else {
            $message_erreur = 'Ce lien est invalide ou a expiré.';
        }
    }
}
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Réinitialiser le mot de passe</h1>

    <?php if ($message_succes !== '') : ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($message_succes) ?>
            <br><a href="compte.php">Se connecter</a>
        </div>
    <?php endif; ?>

    <?php if ($message_erreur !== '') : ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($message_erreur) ?></div>
    <?php endif; ?>

    <?php if ($token_valide) : ?>
        <form method="post" action="reinitialiser-mdp.php" class="formulaire-contact">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <fieldset>
                <label for="password">Nouveau mot de passe :
                    <input id="password" name="password" type="password" required minlength="8">
                </label>
                <label for="password_confirm">Confirmer le mot de passe :
                    <input id="password_confirm" name="password_confirm" type="password" required minlength="8">
                </label>
            </fieldset>
            <input type="submit" value="Réinitialiser" class="btn btn-dark">
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>