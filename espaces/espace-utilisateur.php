<?php
session_start();
$pageTitle = 'Mon Espace - Vite & Gourmand';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Services/UtilisateurService.php';
require_once __DIR__ . '/../src/Services/CommandeService.php';
require_once __DIR__ . '/../src/Services/AvisService.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    echo '<div class="container my-5"><div class="alert alert-warning text-center">Vous devez <a href="' . BASE_URL . 'pages/compte.php">vous connecter</a> pour accéder à votre espace.</div></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$pdo = Database::getConnection();
$utilisateurService = new UtilisateurService($pdo);
$commandeService = new CommandeService($pdo);
$avisService = new AvisService($pdo);
$userId = $_SESSION['utilisateur_id'];

$message_succes = '';
$message_erreur = '';

// ========== MODIFIER SES INFOS ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_infos'])) {
    $data = [
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'adresse' => trim($_POST['adresse'] ?? ''),
        'ville' => trim($_POST['ville'] ?? '')
    ];

    if ($data['nom'] === '' || $data['prenom'] === '' || $data['telephone'] === '' || $data['adresse'] === '' || $data['ville'] === '') {
        $message_erreur = 'Tous les champs sont obligatoires.';
    } else {
        $utilisateurService->mettreAJourProfil($userId, $data);
        $_SESSION['nom'] = $data['nom'];
        $_SESSION['prenom'] = $data['prenom'];
        $message_succes = 'Informations mises à jour.';
    }
}

// ========== ANNULER UNE COMMANDE ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['annuler_commande'])) {
    $commandeId = (int)$_POST['commande_id'];
    if ($commandeService->annulerCommande($commandeId, $userId)) {
        $message_succes = 'Commande annulée.';
    } else {
        $message_erreur = 'Impossible d\'annuler cette commande.';
    }
}

// ========== MODIFIER UNE COMMANDE ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_commande'])) {
    $commandeId = (int)$_POST['commande_id'];
    $data = [
        ':date' => $_POST['date_livraison'] ?? '',
        ':heure' => $_POST['heure_livraison'] ?? '',
        ':adresse' => trim($_POST['adresse_livraison'] ?? ''),
        ':ville' => trim($_POST['ville_livraison'] ?? ''),
        ':nb' => (int)$_POST['nombre_personnes']
    ];

    if ($commandeService->modifierCommande($commandeId, $userId, $data)) {
        $message_succes = 'Commande modifiée avec succès.';
    } else {
        $message_erreur = 'Impossible de modifier cette commande.';
    }
}

// ========== DONNER UN AVIS ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donner_avis'])) {
    $commandeId = (int)$_POST['commande_id'];
    $note = (int)$_POST['note'];
    $commentaire = trim($_POST['commentaire'] ?? '');

    if ($note < 1 || $note > 5 || $commentaire === '') {
        $message_erreur = 'Note entre 1 et 5 et commentaire obligatoire.';
    } else {
        if ($avisService->deposerAvis($commandeId, $userId, $note, $commentaire)) {
            $message_succes = 'Merci pour votre avis !';
        } else {
            $message_erreur = 'Impossible de donner un avis sur cette commande.';
        }
    }
}

// Récupérer les données
$utilisateur = $utilisateurService->getUtilisateur($userId);
$commandes = $commandeService->getCommandesUtilisateur($userId);
$avisExistants = $avisService->getCommandeIdsAvecAvis($userId);
?>

<div class="container my-5">
    <h1 class="text-center mb-5">Mon Espace</h1>

    <?php if ($message_succes !== '') : ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($message_succes) ?></div>
    <?php endif; ?>

    <?php if ($message_erreur !== '') : ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($message_erreur) ?></div>
    <?php endif; ?>

    <!-- ========== MES INFORMATIONS ========== -->
    <h2 class="mb-3">Mes informations</h2>

    <form method="post" action="espace-utilisateur.php" class="formulaire-contact mb-5">
        <fieldset>
            <label for="nom">Nom :
                <input id="nom" name="nom" type="text" value="<?= htmlspecialchars($utilisateur->getNom()) ?>" required>
            </label>

            <label for="prenom">Prénom :
                <input id="prenom" name="prenom" type="text" value="<?= htmlspecialchars($utilisateur->getPrenom()) ?>" required>
            </label>

            <label for="telephone">Téléphone :
                <input id="telephone" name="telephone" type="tel" value="<?= htmlspecialchars($utilisateur->getTelephone()) ?>" required>
            </label>

            <label for="adresse">Adresse :
                <input id="adresse" name="adresse" type="text" value="<?= htmlspecialchars($utilisateur->getAdresse()) ?>" required>
            </label>

            <label for="ville">Ville :
                <input id="ville" name="ville" type="text" value="<?= htmlspecialchars($utilisateur->getVille()) ?>" required>
            </label>
        </fieldset>

        <input type="hidden" name="modifier_infos" value="1">
        <input type="submit" value="Modifier" class="btn btn-dark">
    </form>

    <!-- ========== MES COMMANDES ========== -->
    <h2 class="mb-3">Mes commandes</h2>

    <?php if (empty($commandes)) : ?>
        <p>Aucune commande pour le moment.</p>
    <?php else : ?>
        <?php foreach ($commandes as $cmd) : ?>
            <div class="card mb-3 p-3">
                <p><strong>Commande :</strong> <?= htmlspecialchars($cmd->getNumero()) ?></p>
                <p><strong>Menu :</strong> <?= htmlspecialchars($cmd->getTitreMenu()) ?></p>
                <p><strong>Date :</strong> <?= htmlspecialchars($cmd->getDateLivraison()) ?></p>
                <p><strong>Personnes :</strong> <?= $cmd->getNbPersonnes() ?></p>
                <p><strong>Total :</strong> <?= number_format($cmd->getPrixTotal(), 2, ',', ' ') ?> €</p>
                <p><strong>Statut :</strong> <?= htmlspecialchars($cmd->getStatut()) ?></p>

                <!-- Annuler et Modifier si en_attente -->
                 <?php if ($cmd->getStatut() === 'en_attente') : ?>
                    <form method="post" action="espace-utilisateur.php" style="display: inline;">
                        <input type="hidden" name="commande_id" value="<?= $cmd->getId() ?>">
                        <input type="hidden" name="annuler_commande" value="1">
                        <button type="submit" class="btn btn-outline-dark btn-sm">Annuler</button>
                    </form>

                    <button class="btn btn-dark btn-sm" onclick="document.getElementById('modif-<?= $cmd->getId() ?>').style.display='block'">Modifier</button>

                    <div id="modif-<?= $cmd->getId() ?>" style="display:none;" class="mt-3">
                        <form method="post" action="espace-utilisateur.php" class="formulaire-contact">
                            <input type="hidden" name="commande_id" value="<?= $cmd->getId() ?>">
                            <input type="hidden" name="modifier_commande" value="1">

                            <label for="date-<?= $cmd->getId() ?>">Date de livraison :
                                <input id="date-<?= $cmd->getId() ?>" name="date_livraison" type="date" value="<?= htmlspecialchars($cmd->getDateLivraison()) ?>" required>
                            </label>

                            <label for="heure-<?= $cmd->getId() ?>">Heure :
                                <input id="heure-<?= $cmd->getId() ?>" name="heure_livraison" type="time" value="<?= htmlspecialchars( $cmd->getHeureLivraison()) ?>" required>
                            </label>

                            <label for="adresse-<?= $cmd->getId() ?>">Adresse de livraison :
                                <input id="adresse-<?= $cmd->getId() ?>" name="adresse_livraison" type="text" value="<?= htmlspecialchars($cmd->getAdresseLivraison()) ?>" required>
                            </label>

                            <label for="nb-<?= $cmd->getId() ?>">Nombre de personnes :
                                <input id="nb-<?= $cmd->getId() ?>" name="nombre_personnes" type="number" min="1" value="<?= $cmd->getNbPersonnes() ?>" required>
                            </label>

                            <button type="submit" class="btn btn-dark btn-sm">Enregistrer</button>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Suivi de commande -->
                <?php
                $suivis = $commandeService->getSuivi($cmd->getId());
                ?>
                <?php if (!empty($suivis)) : ?>
                    <div class="mt-3">
                        <p><strong>Suivi :</strong></p>
                        <?php foreach ($suivis as $suivi) : ?>
                            <p>— <?= htmlspecialchars($suivi['statut']) ?> le <?= date('d/m/Y à H:i', strtotime($suivi['date_changement'])) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Donner un avis si terminée et pas encore d'avis -->
                <?php if ($cmd->getStatut() === 'terminee' && !in_array($cmd->getId(), $avisExistants)) : ?>
                    <form method="post" action="espace-utilisateur.php" class="mt-3">
                        <input type="hidden" name="commande_id" value="<?= $cmd->getId() ?>">
                        <input type="hidden" name="donner_avis" value="1">

                        <label for="note-<?= $cmd->getId() ?>">Note (1-5) :
                            <select name="note" id="note-<?= $cmd->getId() ?>">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </label>

                        <label for="commentaire-<?= $cmd->getId() ?>">Commentaire :
                            <textarea name="commentaire" id="commentaire-<?= $cmd->getId() ?>" rows="3" required></textarea>
                        </label>

                        <button type="submit" class="btn btn-dark btn-sm">Envoyer mon avis</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>