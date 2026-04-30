<?php
session_start();
$pageTitle = 'Espace Admin - Vite & Gourmand';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/mailer.php';

// Vérifier que c'est un admin (role_id = 1)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    echo '<div class="container my-5"><div class="alert alert-warning text-center">Accès réservé à l\'administrateur.</div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pdo = Database::getConnection();

$message_succes = '';
$message_erreur = '';

// ========== CRÉER UN EMPLOYÉ ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creer_employe'])) {
    $email = trim($_POST['email_employe'] ?? '');
    $mdp = $_POST['mdp_employe'] ?? '';

    if ($email === '' || $mdp === '') {
        $message_erreur = 'Email et mot de passe obligatoires.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_erreur = 'Email invalide.';
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->fetch()) {
            $message_erreur = 'Cette adresse email est déjà utilisée.';
        } else {
            $hash = password_hash($mdp, PASSWORD_BCRYPT);
            $sql = "INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, ville, role_id) 
                    VALUES (:email, :password, '', '', '', '', '', 2)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':password' => $hash
            ]);

            // Envoyer mail au nouvel employé
            envoyerMail($email, 'Votre compte employé - Vite & Gourmand',
            '<h2>Bienvenue dans l\'équipe !</h2>
            <p>Un compte employé a été créé pour vous sur Vite & Gourmand.</p>
            <p>Votre indentifiant : <strong>' . htmlspecialchars($email) . '</strong></p>)
            <p>Votre mot de passe : <strong>' . htmlspecialchars($mdp) . '</strong></p>)
            <p>Nous vous conseillons de changer votre mot de passe après votre première connexion.</p>
            <p>L\'équipe Vite & Gourmand</p>'
            );
            $message_succes = 'Compte employé créé pour ' . htmlspecialchars($email) . '.';
        }
    }
}

// ========== DÉSACTIVER UN EMPLOYÉ ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['desactiver_employe'])) {
    $employeId = (int)$_POST['employe_id'];
    $stmt = $pdo->prepare("UPDATE utilisateur SET actif = 0 WHERE utilisateur_id = :id AND role_id = 2");
    $stmt->execute([':id' => $employeId]);
    $message_succes = 'Compte employé désactivé.';
}

// ========== RÉACTIVER UN EMPLOYÉ ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reactiver_employe'])) {
    $employeId = (int)$_POST['employe_id'];
    $stmt = $pdo->prepare("UPDATE utilisateur SET actif = 1 WHERE utilisateur_id = :id AND role_id = 2");
    $stmt->execute([':id' => $employeId]);
    $message_succes = 'Compte employé réactivé.';
}

// Récupérer les employés
$employes = $pdo->query("SELECT * FROM utilisateur WHERE role_id = 2 ORDER BY email")->fetchAll();

// Récupérer les stats commandes depuis MongoDB
$client = new MongoDB\Client("mongodb+srv://karima740_db_user:OKmvBeGmG2WP4bpO@cluster0.hsa6bee.mongodb.net/?appName=Cluster0");
$db = $client->vite_gourmand;
$collection =$db->stats_commandes;
$stats = $collection->find([], ['sort' => ['menu_id' => 1]]);
$stats = iterator_to_array($stats);

// Préparer les données pour Chart.js
$labels = [];
$nbCommandes = [];
$ca = [];
foreach ($stats as $s) {
    $labels[] = $s['titre'];
    $nbCommandes[] = (int)$s['nb_commandes'];
    $ca[] = (float)$s['chiffre_affaires'];
}
?>

<div class="container my-5">
    <h1 class="text-center mb-5">Espace Administrateur</h1>

    <?php if ($message_succes !== '') : ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($message_succes) ?></div>
    <?php endif; ?>

    <?php if ($message_erreur !== '') : ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($message_erreur) ?></div>
    <?php endif; ?>

    <!-- ========== CRÉER UN EMPLOYÉ ========== -->
    <h2 class="mb-3">Créer un compte employé</h2>

    <form method="post" action="espace-admin.php" class="formulaire-contact mb-5">
        <fieldset>
            <label for="email_employe">Email (username) :
                <input id="email_employe" name="email_employe" type="email" required>
            </label>

            <label for="mdp_employe">Mot de passe :
                <input id="mdp_employe" name="mdp_employe" type="password" required>
            </label>
        </fieldset>

        <input type="hidden" name="creer_employe" value="1">
        <input type="submit" value="Créer le compte" class="btn btn-dark">
    </form>

    <!-- ========== LISTE DES EMPLOYÉS ========== -->
    <h2 class="mb-3">Employés</h2>

    <?php if (empty($employes)) : ?>
        <p>Aucun employé.</p>
    <?php else : ?>
        <?php foreach ($employes as $emp) : ?>
            <div class="card mb-2 p-3">
                <p>
                    <strong><?= htmlspecialchars($emp['email']) ?></strong>
                    — <?= $emp['actif'] ? 'Actif' : 'Désactivé' ?>
                </p>
                <form method="post" action="espace-admin.php" style="display: inline;">
                    <input type="hidden" name="employe_id" value="<?= $emp['utilisateur_id'] ?>">
                    <?php if ($emp['actif']) : ?>
                        <input type="hidden" name="desactiver_employe" value="1">
                        <button type="submit" class="btn btn-outline-dark btn-sm">Désactiver</button>
                    <?php else : ?>
                        <input type="hidden" name="reactiver_employe" value="1">
                        <button type="submit" class="btn btn-dark btn-sm">Réactiver</button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ========== GRAPHIQUE COMMANDES PAR MENU ========== -->
    <h2 class="mt-5 mb-3">Commandes par menu</h2>

    <canvas id="graphique-commandes" height="100"></canvas>

    <!-- ========== CHIFFRE D'AFFAIRES ========== -->
    <h2 class="mt-5 mb-3">Chiffre d'affaires par menu</h2>

    <?php if (empty($stats)) : ?>
        <p>Aucune donnée.</p>
    <?php else : ?>
        <?php foreach ($stats as $s) : ?>
            <div class="card mb-2 p-3">
                <p>
                    <strong><?= htmlspecialchars($s['titre']) ?></strong>
                    — <?= $s['nb_commandes'] ?> commande(s)
                    — CA : <?= number_format($s['chiffre_affaires'] ?? 0, 2, ',', ' ') ?> €
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('graphique-commandes').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Nombre de commandes',
                data: <?= json_encode($nbCommandes) ?>,
                backgroundColor: ['#F5B800', '#432911', '#2C1A17', '#FFFDFA']
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>