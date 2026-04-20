<?php
session_start();
$pageTitle = 'Mon Espace - Vite & Gourmand';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/src/Database.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    echo '<div class="container my-5"><div class="alert alert-warning text-center">Vous devez <a href="/vite-gourmand/compte.php">vous connecter</a> pour accéder à votre espace.</div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pdo = Database::getConnection();
$userId = $_SESSION['utilisateur_id'];

$message_succes = '';
$message_erreur = '';

// ========== MODIFIER SES INFOS ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_infos'])) {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');

    if ($nom === '' || $prenom === '' || $telephone === '' || $adresse === '' || $ville === '') {
        $message_erreur = 'Tous les champs sont obligatoires.';
    } else {
        $sql = "UPDATE utilisateur SET nom = :nom, prenom = :prenom, telephone = :telephone, adresse_postale = :adresse, ville = :ville WHERE utilisateur_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':telephone' => $telephone,
            ':adresse' => $adresse,
            ':ville' => $ville,
            ':id' => $userId
        ]);
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        $message_succes = 'Informations mises à jour.';
    }
}

// ========== ANNULER UNE COMMANDE ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['annuler_commande'])) {
    $commandeId = (int)$_POST['commande_id'];
    // Vérifier que la commande appartient à l'utilisateur et est en_attente
    $stmt = $pdo->prepare("SELECT * FROM commande WHERE commande_id = :id AND utilisateur_id = :user_id AND statut = 'en_attente'");
    $stmt->execute([':id' => $commandeId, ':user_id' => $userId]);
    $commande = $stmt->fetch();

    if ($commande) {
        $stmt = $pdo->prepare("UPDATE commande SET statut = 'annulee' WHERE commande_id = :id");
        $stmt->execute([':id' => $commandeId]);
        $message_succes = 'Commande annulée.';
    } else {
        $message_erreur = 'Impossible d\'annuler cette commande.';
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
        // Vérifier que la commande est terminée et pas encore d'avis
        $stmt = $pdo->prepare("SELECT * FROM commande WHERE commande_id = :id AND utilisateur_id = :user_id AND statut = 'terminee'");
        $stmt->execute([':id' => $commandeId, ':user_id' => $userId]);
        $commande = $stmt->fetch();

        if ($commande) {
            $stmt = $pdo->prepare("INSERT INTO avis (commande_id, utilisateur_id, note, commentaire) VALUES (:cmd_id, :user_id, :note, :commentaire)");
            $stmt->execute([
                ':cmd_id' => $commandeId,
                ':user_id' => $userId,
                ':note' => $note,
                ':commentaire' => $commentaire
            ]);
            $message_succes = 'Merci pour votre avis !';
        } else {
            $message_erreur = 'Impossible de donner un avis sur cette commande.';
        }
    }
}

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE utilisateur_id = :id");
$stmt->execute([':id' => $userId]);
$utilisateur = $stmt->fetch();

// Récupérer les commandes de l'utilisateur
$stmt = $pdo->prepare("
    SELECT c.*, m.titre AS menu_titre 
    FROM commande c 
    JOIN menu m ON c.menu_id = m.menu_id 
    WHERE c.utilisateur_id = :id 
    ORDER BY c.date_commande DESC
");
$stmt->execute([':id' => $userId]);
$commandes = $stmt->fetchAll();

// Récupérer les avis déjà donnés
$stmt = $pdo->prepare("SELECT commande_id FROM avis WHERE utilisateur_id = :id");
$stmt->execute([':id' => $userId]);
$avisExistants = $stmt->fetchAll(PDO::FETCH_COLUMN);
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
                <input id="nom" name="nom" type="text" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required>
            </label>

            <label for="prenom">Prénom :
                <input id="prenom" name="prenom" type="text" value="<?= htmlspecialchars($utilisateur['prenom']) ?>" required>
            </label>

            <label for="telephone">Téléphone :
                <input id="telephone" name="telephone" type="tel" value="<?= htmlspecialchars($utilisateur['telephone']) ?>" required>
            </label>

            <label for="adresse">Adresse :
                <input id="adresse" name="adresse" type="text" value="<?= htmlspecialchars($utilisateur['adresse_postale']) ?>" required>
            </label>

            <label for="ville">Ville :
                <input id="ville" name="ville" type="text" value="<?= htmlspecialchars($utilisateur['ville']) ?>" required>
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
                <p><strong>Commande :</strong> <?= htmlspecialchars($cmd['numero_commande']) ?></p>
                <p><strong>Menu :</strong> <?= htmlspecialchars($cmd['menu_titre']) ?></p>
                <p><strong>Date :</strong> <?= htmlspecialchars($cmd['date_livraison']) ?></p>
                <p><strong>Personnes :</strong> <?= $cmd['nombre_personnes'] ?></p>
                <p><strong>Total :</strong> <?= number_format($cmd['prix_total'], 2, ',', ' ') ?> €</p>
                <p><strong>Statut :</strong> <?= htmlspecialchars($cmd['statut']) ?></p>

                <!-- Annuler si en_attente -->
                <?php if ($cmd['statut'] === 'en_attente') : ?>
                    <form method="post" action="espace-utilisateur.php" style="display: inline;">
                        <input type="hidden" name="commande_id" value="<?= $cmd['commande_id'] ?>">
                        <input type="hidden" name="annuler_commande" value="1">
                        <button type="submit" class="btn btn-outline-dark btn-sm">Annuler</button>
                    </form>
                <?php endif; ?>

                <!-- Donner un avis si terminée et pas encore d'avis -->
                <?php if ($cmd['statut'] === 'terminee' && !in_array($cmd['commande_id'], $avisExistants)) : ?>
                    <form method="post" action="espace-utilisateur.php" class="mt-3">
                        <input type="hidden" name="commande_id" value="<?= $cmd['commande_id'] ?>">
                        <input type="hidden" name="donner_avis" value="1">

                        <label for="note-<?= $cmd['commande_id'] ?>">Note (1-5) :
                            <select name="note" id="note-<?= $cmd['commande_id'] ?>">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </label>

                        <label for="commentaire-<?= $cmd['commande_id'] ?>">Commentaire :
                            <textarea name="commentaire" id="commentaire-<?= $cmd['commande_id'] ?>" rows="3" required></textarea>
                        </label>

                        <button type="submit" class="btn btn-dark btn-sm">Envoyer mon avis</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>