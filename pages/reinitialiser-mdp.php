<?php
session_start();
$pageTitle = 'Réinitialiser le mot de passe - Vite & Gourmand';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Services/UtilisateurService.php';

$pdo = Database::getConnection();
$utilisateurService = new UtilisateurService($pdo);
$message_succes = '';
$message_erreur = '';
$token_valide = false;

$token = $_GET['token'] ?? '';

if ($token !== '') {
    $utilisateur = $utilisateurService->verifierToken($token);
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
    } else {
        $result = $utilisateurService->resetPassword($token, $mdp);

        if ($result['success']) {
            $message_succes = 'Votre mot de passe a été réinitialisé. Vous pouvez vous connecter.';
            $token_valide = false;
        } else {
            $message_erreur = $result['erreur'];
            $token_valide = true;
        }
    }
}
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Réinitialiser le mot de passe</h1>

    <?php if ($message_succes !== '') : ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($message_succes) ?>
            <br><a href="<?= BASE_URL ?>pages/compte.php">Se connecter</a>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>