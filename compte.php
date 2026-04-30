<?php
$pageTitle = 'Mon Compte - Vite & Gourmand';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/mailer.php';

$pdo = Database::getConnection();

$message_succes = '';
$message_erreur = '';

// ========== INSCRIPTION ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inscription'])) {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email_inscription'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $mdp = $_POST['mdp'] ?? '';
    $mdp_confirm = $_POST['mdp_confirm'] ?? '';

    // Vérifier que tous les champs sont remplis
    if ($nom === '' || $prenom === '' || $email === '' || $telephone === '' || $adresse === '' || $ville === '' || $mdp === '') {
        $message_erreur = 'Tous les champs sont obligatoires.';
    }
    // Vérifier le format email
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_erreur = 'Adresse email invalide.';
    }
    // Vérifier le mot de passe (10 chars min + majuscule + minuscule + chiffre + spécial)
    else if (strlen($mdp) < 10) {
        $message_erreur = 'Le mot de passe doit contenir au moins 10 caractères.';
    }
    else if (!preg_match('/[A-Z]/', $mdp)) {
        $message_erreur = 'Le mot de passe doit contenir au moins une majuscule.';
    }
    else if (!preg_match('/[a-z]/', $mdp)) {
        $message_erreur = 'Le mot de passe doit contenir au moins une minuscule.';
    }
    else if (!preg_match('/[0-9]/', $mdp)) {
        $message_erreur = 'Le mot de passe doit contenir au moins un chiffre.';
    }
    else if (!preg_match('/[^a-zA-Z0-9]/', $mdp)) {
        $message_erreur = 'Le mot de passe doit contenir au moins un caractère spécial.';
    }
    // Vérifier que les 2 mots de passe correspondent
    else if ($mdp !== $mdp_confirm) {
        $message_erreur = 'Les mots de passe ne correspondent pas.';
    }
    else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->fetch()) {
            $message_erreur = 'Cette adresse email est déjà utilisée.';
        } else {
            // Tout est bon → insérer l'utilisateur
            $hash = password_hash($mdp, PASSWORD_BCRYPT);
            $sql = "INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, ville, role_id) 
                    VALUES (:email, :password, :nom, :prenom, :telephone, :adresse, :ville, 3)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':password' => $hash,
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':telephone' => $telephone,
                ':adresse' => $adresse,
                ':ville' => $ville
            ]);
            envoyerMail($email, 'Bienvenue chez Vite & Gourmand !',
            '<h2>Bienvenue' . htmlspecialchars($prenom) . ' !</h2>
            <p>Votre compte a été créé avec succès.</p>
            <p>Vous puvez maintenant commander nos menus traiteur.</p>
            <p>A bientôt,<br>L\'équipe Vite & Gourmand</p>'
            );
            $message_succes = 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.';
        }
    }
}

// ========== CONNEXION ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connexion'])) {
    $email_co = trim($_POST['email_connexion'] ?? '');
    $mdp_co = $_POST['mdp_connexion'] ?? '';

    if ($email_co === '' || $mdp_co === '') {
        $message_erreur = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email AND actif = 1");
        $stmt->execute([':email' => $email_co]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur && password_verify($mdp_co, $utilisateur['password'])) {
            // Connexion réussie → démarrer la session
            session_start();
            $_SESSION['utilisateur_id'] = $utilisateur['utilisateur_id'];
            $_SESSION['nom'] = $utilisateur['nom'];
            $_SESSION['prenom'] = $utilisateur['prenom'];
            $_SESSION['email'] = $utilisateur['email'];
            $_SESSION['role_id'] = $utilisateur['role_id'];
            $message_succes = 'Bienvenue ' . htmlspecialchars($utilisateur['prenom']) . ' !';
        } else {
            $message_erreur = 'Email ou mot de passe incorrect.';
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
                <a href="/vite-gourmand/mot-de-passe-oublie.php">Mot de passe oublié ?</a>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>