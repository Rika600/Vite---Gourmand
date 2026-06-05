<?php
$pageTitle = 'Mon Compte - Vite & Gourmand';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/mailer.php';
require_once __DIR__ . '/../src/Services/UtilisateurService.php';

$pdo = Database::getConnection();
$utilisateurService = new UtilisateurService($pdo);

$message_succes = '';
$message_erreur = '';

// ========== INSCRIPTION ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inscription'])) {
  $data = [
        'email' => trim($_POST['email_inscription'] ?? ''),
        'password' => $_POST['mdp'] ?? '',
        'password_confirm' => $_POST['mdp_confirm'] ?? '',
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'adresse' => trim($_POST['adresse'] ?? ''),
        'ville' => trim($_POST['ville'] ?? '')
    ];

    $result = $utilisateurService->inscrire($data);

    if ($result['success']) {
        envoyerMail($data['email'], 'Bienvenue chez Vite & Gourmand !',
            '<h2>Bienvenue ' . htmlspecialchars($data['prenom']) . ' !</h2>
            <p>Votre compte a été créé avec succès.</p>
            <p>Vous pouvez maintenant commander nos menus traiteur.</p>
            <p>A bientôt,<br>L\'équipe Vite & Gourmand</p>'
        );
        $message_succes = 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.';
    } else {
        $message_erreur = $result['erreur'];
    }
}


// ========== CONNEXION ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connexion'])) {
    $email_co = trim($_POST['email_connexion'] ?? '');
    $mdp_co = $_POST['mdp_connexion'] ?? '';

    if ($email_co === '' || $mdp_co === '') {
        $message_erreur = 'Veuillez remplir tous les champs.';
    } else {
        $result = $utilisateurService->connecter($email_co, $mdp_co);

        if ($result['success']) {
            $utilisateur = $result['utilisateur'];
            $_SESSION['utilisateur_id'] = $utilisateur->getId();
            $_SESSION['nom'] = $utilisateur->getNom();
            $_SESSION['prenom'] = $utilisateur->getPrenom();
            $_SESSION['email'] = $utilisateur->getEmail();
            $_SESSION['role_id'] = $utilisateur->getRoleId();
            $message_succes = 'Bienvenue ' . htmlspecialchars($utilisateur->getPrenom()) . ' !';
        } else {
            $message_erreur = $result['erreur'];
        }
    }
}
?>

<div class="container my-5">
    <h1 class="text-center mb-5">Mon Compte</h1>

    <?php if ($message_succes !== '') : ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($message_succes) ?></div>
    <?php endif; ?>

    <?php if ($message_erreur !== '') : ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($message_erreur) ?></div>
    <?php endif; ?>

    <div class="row">

       
        <!-- ========== CONNEXION (droite) ========== -->
        <div class="col-md-6">
            <h2 class="text-center mb-4">Connexion</h2>

            <form method="post" action="compte.php" class="formulaire-contact">
                <fieldset>
                    <label for="email_connexion">Email :
                        <input id="email_connexion" name="email_connexion" type="email" required>
                    </label>

                    <label for="mdp_connexion">Mot de passe :
                        <input id="mdp_connexion" name="mdp_connexion" type="password" required>
                    </label>
                </fieldset>

                <input type="hidden" name="connexion" value="1">
                <input type="submit" value="Se connecter" class="btn btn-dark">
                <p class="text-center mt-3">
                <a href="<?= BASE_URL ?>pages/mot-de-passe-oublie.php">Mot de passe oublié ?</a>
                </p>
            </form>
        </div>

 <!-- ========== INSCRIPTION (gauche) ========== -->
        <div class="col-md-6">
            <h2 class="text-center mb-4">Inscription</h2>

            <form method="post" action="compte.php" class="formulaire-contact">
                <fieldset>
                    <label for="nom">Nom :
                        <input id="nom" name="nom" type="text" required>
                    </label>

                    <label for="prenom">Prénom :
                        <input id="prenom" name="prenom" type="text" required>
                    </label>

                    <label for="email_inscription">Email :
                        <input id="email_inscription" name="email_inscription" type="email" required>
                    </label>

                    <label for="telephone">Téléphone :
                        <input id="telephone" name="telephone" type="tel" required>
                    </label>

                    <label for="adresse">Adresse postale :
                        <input id="adresse" name="adresse" type="text" required>
                    </label>

                    <label for="ville">Ville :
                        <input id="ville" name="ville" type="text" required>
                    </label>

                    <label for="code_postal">Code postal :
                        <input id="code_postal" name="code_postal" type="text" required>
                    </label>

                    <label for="mdp">Mot de passe (10 caractères min) :
                        <input id="mdp" name="mdp" type="password" required>
                    </label>

                    <label for="mdp_confirm">Confirmer le mot de passe :
                        <input id="mdp_confirm" name="mdp_confirm" type="password" required>
                    </label>
                </fieldset>

                <input type="hidden" name="inscription" value="1">
                <input type="submit" value="S'inscrire" class="btn btn-dark">
            </form>
        </div>


    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>