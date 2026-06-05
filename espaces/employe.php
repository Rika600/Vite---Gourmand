<?php
session_start();
$pageTitle = 'Espace Employé - Vite & Gourmand';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/mailer.php';
require_once __DIR__ . '/../src/Services/CommandeService.php';
require_once __DIR__ . '/../src/Services/MenuService.php';
require_once __DIR__ . '/../src/Services/AvisService.php';

// Vérifier que c'est un employé ou admin (role_id 1 ou 2)
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
    echo '<div class="container my-5"><div class="alert alert-warning text-center">Accès réservé aux employés.</div></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$pdo = Database::getConnection();
$commandeService = new CommandeService($pdo);
$menuService = new MenuService($pdo);
$avisService = new AvisService($pdo);

$message_succes = '';
$message_erreur = '';

// ========== CHANGER STATUT COMMANDE ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_statut'])) {
    $commandeId = (int)$_POST['commande_id'];
    $nouveauStatut = $_POST['nouveau_statut'];
    $motif = trim($_POST['motif'] ?? '');
    $modeContact = $_POST['mode_contact'] ?? '';

    if ($nouveauStatut === 'annulee' && ($motif === '' || $modeContact === '')) {
        $message_erreur = 'Motif et mode de contact obligatoires pour une annulation.';
    } else {
        $commandeService->changerStatut($commandeId, $nouveauStatut, $motif, $modeContact);

        // Si commande terminée, envoyer mail pour donner un avis
        if ($nouveauStatut === 'terminee') {
            $infos = $commandeService->getInfosClientCommande($commandeId);
            if ($infos) {
                envoyerMail($infos['email'], 'Donnez votre avis - Vite & Gourmand',
                    '<h2>Votre commande est terminée !</h2>
                    <p>Bonjour ' . htmlspecialchars($infos['prenom']) . ',</p>
                    <p>Votre commande <strong>' . htmlspecialchars($infos['numero_commande']) . '</strong> est terminée.</p>
                    <p>Nous espérons que vous avez apprécié notre prestation !</p>
                    <p>N\'hésitez pas à nous laisser un avis depuis votre espace client.</p>
                    <p>L\'équipe Vite & Gourmand</p>'
                );
            }
        }

        $message_succes = 'Statut mis à jour.';
    }
}

// ========== VALIDER/REFUSER AVIS ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gerer_avis'])) {
    $avisId = (int)$_POST['avis_id'];
    $decision = $_POST['decision'];

    if ($decision === 'valide') {
        $avisService->validerAvis($avisId);
    } else {
        $avisService->refuserAvis($avisId);
    }
    $message_succes = 'Avis ' . ($decision === 'valide' ? 'validé' : 'refusé') . '.';
}

// ========== DÉSACTIVER UN MENU ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_menu'])) {
    $menuId = (int)$_POST['menu_id'];
    $menuService->desactiverMenu($menuId);
    $message_succes = 'Menu désactivé.';
}

// Récupérer les données
$filtreStatut = $_GET['statut'] ?? '';
$filtreClient = trim($_GET['client'] ?? '');
$commandes = $commandeService->getCommandesFiltrees($filtreStatut ?: null, $filtreClient ?: null);
$menus = $menuService->getMenusActifs();
$avis = $avisService->getAvisEnAttente();
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
    <form method="get" action="employe.php" class="row g-3 mb-4">
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
                <p>Menu : <?= htmlspecialchars($cmd['titre']) ?> | <?= $cmd['nombre_personnes'] ?> pers. | <?= number_format($cmd['prix_total'], 2, ',', ' ') ?> €</p>
                <p>Livraison : <?= htmlspecialchars($cmd['date_livraison']) ?> à <?= htmlspecialchars($cmd['heure_livraison']) ?> — <?= htmlspecialchars($cmd['adresse_livraison']) ?>, <?= htmlspecialchars($cmd['ville_livraison']) ?></p>
                <p>Statut actuel : <strong><?= htmlspecialchars($cmd['statut']) ?></strong></p>

                <?php if ($cmd['statut'] !== 'terminee' && $cmd['statut'] !== 'annulee') : ?>
                    <form method="post" action="employe.php" class="row g-2 align-items-end">
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
            <p><strong><?= htmlspecialchars($menu->getTitre()) ?></strong> — <?= number_format( $menu->getPrix() /  $menu->getNbPersonnesMin(), 2, ',', ' ') ?> €/pers — Stock : <?= $menu->getStock() ?></p>
            <form method="post" action="employe.php" style="display: inline;">
                <input type="hidden" name="menu_id" value="<?=  $menu->getId() ?>">
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
                <p><strong><?= htmlspecialchars($a->getNom()) ?> <?= htmlspecialchars($a->getPrenom()) ?></strong> — <?= htmlspecialchars($a->getMenuTitre()) ?> — Note : <?= $a->getNote()?>/5</p>
                <p><?= htmlspecialchars($a->getCommentaire()) ?></p>

                <form method="post" action="employe.php" style="display: inline;">
                    <input type="hidden" name="avis_id" value="<?= $a->getId() ?>">
                    <input type="hidden" name="gerer_avis" value="1">
                    <button type="submit" name="decision" value="valide" class="btn btn-dark btn-sm">Valider</button>
                    <button type="submit" name="decision" value="refuse" class="btn btn-outline-dark btn-sm">Refuser</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>