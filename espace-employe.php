<?php
session_start();
$pageTitle = 'Espace Employé - Vite & Gourmand';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/src/Database.php';

// Vérifier que c'est un employé ou admin (role_id 1 ou 2)
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
    echo '<div class="container my-5"><div class="alert alert-warning text-center">Accès réservé aux employés.</div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pdo = Database::getConnection();

$message_succes = '';
$message_erreur = '';

// ========== CHANGER STATUT COMMANDE ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_statut'])) {
    $commandeId = (int)$_POST['commande_id'];
    $nouveauStatut = $_POST['nouveau_statut'];
    $motif = trim($_POST['motif'] ?? '');
    $modeContact = $_POST['mode_contact'] ?? '';

    // Si annulation, motif obligatoire
    if ($nouveauStatut === 'annulee' && ($motif === '' || $modeContact === '')) {
        $message_erreur = 'Motif et mode de contact obligatoires pour une annulation.';
    } else {
        $sql = "UPDATE commande SET statut = :statut";
        $params = [':statut' => $nouveauStatut, ':id' => $commandeId];

        if ($nouveauStatut === 'annulee') {
            $sql .= ", motif_annulation = :motif, mode_contact_annulation = :mode";
            $params[':motif'] = $motif;
            $params[':mode'] = $modeContact;
        }

        $sql .= " WHERE commande_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Ajouter au suivi
        $stmt = $pdo->prepare("INSERT INTO suivi_commande (commande_id, statut) VALUES (:id, :statut)");
        $stmt->execute([':id' => $commandeId, ':statut' => $nouveauStatut]);

        $message_succes = 'Statut mis à jour.';
    }
}

// ========== VALIDER/REFUSER AVIS ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gerer_avis'])) {
    $avisId = (int)$_POST['avis_id'];
    $decision = $_POST['decision'];

    $stmt = $pdo->prepare("UPDATE avis SET statut_validation = :statut, date_validation = NOW() WHERE avis_id = :id");
    $stmt->execute([':statut' => $decision, ':id' => $avisId]);
    $message_succes = 'Avis ' . ($decision === 'valide' ? 'validé' : 'refusé') . '.';
}

// ========== SUPPRIMER UN MENU ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_menu'])) {
    $menuId = (int)$_POST['menu_id'];
    $stmt = $pdo->prepare("UPDATE menu SET actif = 0 WHERE menu_id = :id");
    $stmt->execute([':id' => $menuId]);
    $message_succes = 'Menu désactivé.';
}

// Récupérer les commandes (avec filtre)
$filtreStatut = $_GET['statut'] ?? '';
$filtreClient = trim($_GET['client'] ?? '');

$sqlCommandes = "
    SELECT c.*, m.titre AS menu_titre, u.nom, u.prenom, u.email 
    FROM commande c 
    JOIN menu m ON c.menu_id = m.menu_id 
    JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id 
    WHERE 1=1
";
$paramsCommandes = [];

if ($filtreStatut !== '') {
    $sqlCommandes .= " AND c.statut = :statut";
    $paramsCommandes[':statut'] = $filtreStatut;
}
if ($filtreClient !== '') {
    $sqlCommandes .= " AND (u.nom LIKE :client OR u.prenom LIKE :client OR u.email LIKE :client)";
    $paramsCommandes[':client'] = '%' . $filtreClient . '%';
}

$sqlCommandes .= " ORDER BY c.date_commande DESC";
$stmt = $pdo->prepare($sqlCommandes);
$stmt->execute($paramsCommandes);
$commandes = $stmt->fetchAll();

// Récupérer les menus actifs
$menus = $pdo->query("SELECT * FROM menu WHERE actif = 1 ORDER BY menu_id")->fetchAll();

// Récupérer les avis en attente
$avis = $pdo->query("
    SELECT a.*, u.nom, u.prenom, m.titre AS menu_titre 
    FROM avis a 
    JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id 
    JOIN commande c ON a.commande_id = c.commande_id 
    JOIN menu m ON c.menu_id = m.menu_id 
    WHERE a.statut_validation = 'en_attente'
    ORDER BY a.date_creation DESC
")->fetchAll();
?>

<div class="container my-5">
    <h1 class="text-center mb-5">Espace Employé</h1>

    <?php if ($message_succes !== '') : ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($message_succes) ?></div>
    <?php endif; ?>

    <?php if ($message_erreur !== '') : ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($message_erreur) ?></div>
    <?php endif; ?>

    <!-- ========== GESTION COMMANDES ========== -->
    <h2 class="mb-3">Commandes</h2>

    <!-- Filtres -->
    <form method="get" action="espace-employe.php" class="row g-3 mb-4">
        <div class="col-md-3">
            <select name="statut" class="form-select">
                <option value="">Tous les statuts</option>
                <option value="en_attente" <?= $filtreStatut === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                <option value="accepte" <?= $filtreStatut === 'accepte' ? 'selected' : '' ?>>Accepté</option>
                <option value="en_preparation" <?= $filtreStatut === 'en_preparation' ? 'selected' : '' ?>>En préparation</option>
                <option value="en_cours_livraison" <?= $filtreStatut === 'en_cours_livraison' ? 'selected' : '' ?>>En livraison</option>
                <option value="livre" <?= $filtreStatut === 'livre' ? 'selected' : '' ?>>Livré</option>
                <option value="terminee" <?= $filtreStatut === 'terminee' ? 'selected' : '' ?>>Terminée</option>
                <option value="annulee" <?= $filtreStatut === 'annulee' ? 'selected' : '' ?>>Annulée</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="client" class="form-control" placeholder="Rechercher un client" value="<?= htmlspecialchars($filtreClient) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-dark">Filtrer</button>
        </div>
    </form>

    <?php if (empty($commandes)) : ?>
        <p>Aucune commande trouvée.</p>
    <?php else : ?>
        <?php foreach ($commandes as $cmd) : ?>
            <div class="card mb-3 p-3">
                <p><strong><?= htmlspecialchars($cmd['numero_commande']) ?></strong> — <?= htmlspecialchars($cmd['nom']) ?> <?= htmlspecialchars($cmd['prenom']) ?> (<?= htmlspecialchars($cmd['email']) ?>)</p>
                <p>Menu : <?= htmlspecialchars($cmd['menu_titre']) ?> | <?= $cmd['nombre_personnes'] ?> pers. | <?= number_format($cmd['prix_total'], 2, ',', ' ') ?> €</p>
                <p>Livraison : <?= htmlspecialchars($cmd['date_livraison']) ?> à <?= htmlspecialchars($cmd['heure_livraison']) ?> — <?= htmlspecialchars($cmd['adresse_livraison']) ?>, <?= htmlspecialchars($cmd['ville_livraison']) ?></p>
                <p>Statut actuel : <strong><?= htmlspecialchars($cmd['statut']) ?></strong></p>

                <?php if ($cmd['statut'] !== 'terminee' && $cmd['statut'] !== 'annulee') : ?>
                    <form method="post" action="espace-employe.php" class="row g-2 align-items-end">
                        <input type="hidden" name="commande_id" value="<?= $cmd['commande_id'] ?>">
                        <input type="hidden" name="changer_statut" value="1">

                        <div class="col-md-3">
                            <select name="nouveau_statut" class="form-select">
                                <option value="accepte">Accepté</option>
                                <option value="en_preparation">En préparation</option>
                                <option value="en_cours_livraison">En livraison</option>
                                <option value="livre">Livré</option>
                                <option value="en_attente_retour_materiel">Attente retour matériel</option>
                                <option value="terminee">Terminée</option>
                                <option value="annulee">Annulée</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <input type="text" name="motif" class="form-control" placeholder="Motif (si annulation)">
                        </div>

                        <div class="col-md-2">
                            <select name="mode_contact" class="form-select">
                                <option value="">Contact</option>
                                <option value="telephone">Téléphone</option>
                                <option value="email">Email</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark btn-sm">Mettre à jour</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ========== GESTION MENUS ========== -->
    <h2 class="mt-5 mb-3">Menus</h2>

    <?php foreach ($menus as $menu) : ?>
        <div class="card mb-2 p-3">
            <p><strong><?= htmlspecialchars($menu['titre']) ?></strong> — <?= number_format($menu['prix_min'] / $menu['nombre_personnes_min'], 2, ',', ' ') ?> €/pers — Stock : <?= $menu['stock_disponible'] ?></p>
            <form method="post" action="espace-employe.php" style="display: inline;">
                <input type="hidden" name="menu_id" value="<?= $menu['menu_id'] ?>">
                <input type="hidden" name="supprimer_menu" value="1">
                <button type="submit" class="btn btn-outline-dark btn-sm" onclick="return confirm('Désactiver ce menu ?')">Désactiver</button>
            </form>
        </div>
    <?php endforeach; ?>

    <!-- ========== GESTION AVIS ========== -->
    <h2 class="mt-5 mb-3">Avis en attente</h2>

    <?php if (empty($avis)) : ?>
        <p>Aucun avis en attente.</p>
    <?php else : ?>
        <?php foreach ($avis as $a) : ?>
            <div class="card mb-2 p-3">
                <p><strong><?= htmlspecialchars($a['nom']) ?> <?= htmlspecialchars($a['prenom']) ?></strong> — <?= htmlspecialchars($a['menu_titre']) ?> — Note : <?= $a['note'] ?>/5</p>
                <p><?= htmlspecialchars($a['commentaire']) ?></p>

                <form method="post" action="espace-employe.php" style="display: inline;">
                    <input type="hidden" name="avis_id" value="<?= $a['avis_id'] ?>">
                    <input type="hidden" name="gerer_avis" value="1">
                    <button type="submit" name="decision" value="valide" class="btn btn-dark btn-sm">Valider</button>
                    <button type="submit" name="decision" value="refuse" class="btn btn-outline-dark btn-sm">Refuser</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>