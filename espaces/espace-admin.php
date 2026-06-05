<?php
session_start();
$pageTitle = 'Espace Admin - Vite & Gourmand';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/mailer.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/Services/UtilisateurService.php';

// Vérifier que c'est un admin (role_id = 1)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    echo '<div class="container my-5"><div class="alert alert-warning text-center">Accès réservé à l\'administrateur.</div></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$pdo = Database::getConnection();
$utilisateurService = new UtilisateurService($pdo);

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
        $result = $utilisateurService->creerEmploye($email, $mdp);

        if ($result['success']) {
            envoyerMail($email, 'Votre compte employé - Vite & Gourmand',
                '<h2>Bienvenue dans l\'équipe !</h2>
                <p>Un compte employé a été créé pour vous sur Vite & Gourmand.</p>
                <p>Votre identifiant : <strong>' . htmlspecialchars($email) . '</strong></p>
                <p>Votre mot de passe : <strong>' . htmlspecialchars($mdp) . '</strong></p>
                <p>Nous vous conseillons de changer votre mot de passe après votre première connexion.</p>
                <p>L\'équipe Vite & Gourmand</p>'
            );
            $message_succes = 'Compte employé créé pour ' . htmlspecialchars($email) . '.';
        } else {
            $message_erreur = $result['erreur'];
        }
    }
}

// ========== DÉSACTIVER UN EMPLOYÉ ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['desactiver_employe'])) {
    $employeId = (int)$_POST['employe_id'];
    $utilisateurService->desactiverEmploye($employeId);
    $message_succes = 'Compte employé désactivé.';
}

// ========== RÉACTIVER UN EMPLOYÉ ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reactiver_employe'])) {
    $employeId = (int)$_POST['employe_id'];
    $utilisateurService->reactiverEmploye($employeId);
    $message_succes = 'Compte employé réactivé.';
}

// Récupérer les employés
$employes = $utilisateurService->getEmployes();

// Récupérer les stats commandes depuis MongoDB
$client = new MongoDB\Client(MONGODB_URI);
$db = $client->vite_gourmand;
$collection = $db->stats_commandes;
$stats = $collection->find([], ['sort' => ['menu_id' => 1]]);
$stats = iterator_to_array($stats);

// Appliquer les filtres CA
$filtre_menu = $_GET['filtre_menu'] ?? '';
$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';

if ($filtre_menu !== '' || $date_debut !== '' || $date_fin !== '') {
    if ($filtre_menu !== '') {
        $stats = array_filter($stats, function($s) use ($filtre_menu) {
            return $s['titre'] === $filtre_menu;
        });
    }

    if ($date_debut !== '' || $date_fin !== '') {
        $sqlFiltre = "SELECT m.titre, COUNT(c.commande_id) AS nb_commandes, COALESCE(SUM(c.prix_total), 0) AS chiffre_affaires
            FROM menu m
            LEFT JOIN commande c ON m.menu_id = c.menu_id AND c.statut != 'annulee'";
        $paramsFiltre = [];

        if ($date_debut !== '') {
            $sqlFiltre .= " AND c.date_commande >= :debut";
            $paramsFiltre[':debut'] = $date_debut;
        }
        if ($date_fin !== '') {
            $sqlFiltre .= " AND c.date_commande <= :fin";
            $paramsFiltre[':fin'] = $date_fin . ' 23:59:59';
        }

        $sqlFiltre .= " GROUP BY m.menu_id, m.titre ORDER BY m.menu_id";
        $stmt = $pdo->prepare($sqlFiltre);
        $stmt->execute($paramsFiltre);
        $stats = $stmt->fetchAll();

        if ($filtre_menu !== '') {
            $stats = array_filter($stats, function($s) use ($filtre_menu) {
                return $s['titre'] === $filtre_menu;
            });
        }
    }
}

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
                    <strong><?= htmlspecialchars($emp->getEmail()) ?></strong>
                    <?= $emp->isActif() ? 'Actif' : 'Désactivé' ?>
                </p>
                <form method="post" action="espace-admin.php" style="display: inline;">
                    <input type="hidden" name="employe_id" value="<?= $emp->getId() ?>">
                    <?php if ($emp->isActif()) : ?>
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

     <!-- ========== FILTRES CHIFFRE D'AFFAIRES ========== -->
    <h2 class="mt-5 mb-3">Chiffre d'affaires par menu</h2>

    <form method="get" action="espace-admin.php" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="filtre_menu">Menu :</label>
                <select name="filtre_menu" id="filtre_menu" class="form-select">
                    <option value="">Tous</option>
                    <?php foreach ($stats as $s) : ?>
                        <option value="<?= htmlspecialchars($s['titre']) ?>" <?= (isset($_GET['filtre_menu']) && $_GET['filtre_menu'] === $s['titre']) ? 'selected' : '' ?>><?= htmlspecialchars($s['titre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_debut">Date début :</label>
                <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?= htmlspecialchars($_GET['date_debut'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="date_fin">Date fin :</label>
                <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?= htmlspecialchars($_GET['date_fin'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark">Filtrer</button>
                <a href="espace-admin.php" class="btn btn-outline-dark">Réinitialiser</a>
            </div>
        </div>
    </form>

    <?php if (empty($stats)) : ?>
        <p>Aucune donnée.</p>
    <?php else : ?>
        <?php foreach ($stats as $s) : ?>
            <div class="card mb-2 p-3">
                <p>
                    <strong><?= htmlspecialchars($s['Titre']) ?></strong>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>