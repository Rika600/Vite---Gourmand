<?php
session_start();
$pageTitle = 'Mot de passe oublié - Vite & Gourmand';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/mailer.php';

$pdo = Database::getConnection();
$message_succes = '';
$message_erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $message_erreur = 'Veuillez entrer votre adresse email.';
    } else {
        // Vérifier si l'email existe
        $sql = "SELECT utilisateur_id, prenom FROM utilisateur WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur) {
            // Générer un token unique
            $token = bin2hex(random_bytes(32));
            $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Stocker le token dans la base
            $sql = "UPDATE utilisateur SET token_reset = :token, token_expiration = :expiration WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':token' => $token,
                ':expiration' => $expiration,
                ':email' => $email
            ]);

            // Envoyer le mail
            $lien = 'http://localhost:8080/vite-gourmand/reinitialiser-mdp.php?token=' . $token;
            envoyerMail($email, 'Réinitialisation de votre mot de passe',
                '<h2>Réinitialisation de mot de passe</h2>
                <p>Bonjour ' . htmlspecialchars($utilisateur['prenom']) . ',</p>
                <p>Vous avez demandé à réinitialiser votre mot de passe.</p>
                <p>Cliquez sur le lien ci-dessous (valable 1 heure) :</p>
                <p><a href="' . $lien . '">Réinitialiser mon mot de passe</a></p>
                <p>Si vous n\'êtes pas à l\'origine de cette demande, ignorez ce mail.</p>
                <p>L\'équipe Vite & Gourmand</p>'
            );
        }

        // Message identique que l'email existe ou non (sécurité)
        $message_succes = 'Si cette adresse existe dans notre base, un email de réinitialisation a été envoyé.';
    }
}
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Mot de passe oublié</h1>

    <?php if ($message_succes !== '') : ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($message_succes) ?></div>
    <?php endif; ?>

    <?php if ($message_erreur !== '') : ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($message_erreur) ?></div>
    <?php endif; ?>

    <form method="post" action="mot-de-passe-oublie.php" class="formulaire-contact">
        <fieldset>
            <label for="email">Votre adresse email :
                <input id="email" name="email" type="email" required>
            </label>
        </fieldset>
        <input type="submit" value="Envoyer le lien" class="btn btn-dark">
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>