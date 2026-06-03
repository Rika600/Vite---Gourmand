<?php
$pageTitle = 'Contact - Vite & Gourmand';
require_once __DIR__ . '/includes/header.php';

// Traitement du formulaire quand il est soumis
$message_succes = '';
$message_erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les valeurs du formulaire
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Vérifier que tous les champs sont remplis
    if ($nom === '' || $email === '' || $sujet === '' || $description === '') {
        $message_erreur = 'Tous les champs sont obligatoires.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_erreur = 'Adresse email invalide.';
    } else {
        // Tout est bon
        $message_succes = 'Votre message a bien été envoyé. Nous vous répondrons rapidement.';
    }
}
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Contact</h1>
    <p class="text-center mb-5">Une question ? Contactez-nous via ce formulaire.</p>

    <?php if ($message_succes !== '') : ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($message_succes) ?></div>
    <?php endif; ?>

    <?php if ($message_erreur !== '') : ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($message_erreur) ?></div>
    <?php endif; ?>

    <form method="post" action="contact.php" class="formulaire-contact">
        <fieldset>
            <label for="nom">Nom et prénom :
                <input id="nom" name="nom" type="text" required
                       value="<?= htmlspecialchars($nom ?? '') ?>">
            </label>

            <label for="email">Votre email :
                <input id="email" name="email" type="email" required
                       value="<?= htmlspecialchars($email ?? '') ?>">
            </label>

            <label for="sujet">Sujet :
                <input id="sujet" name="sujet" type="text" required
                       value="<?= htmlspecialchars($sujet ?? '') ?>">
            </label>

            <label for="description">Votre message :
                <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($description ?? '') ?></textarea>
            </label>
        </fieldset>

        <input type="submit" value="Envoyer" class="btn btn-dark">
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>