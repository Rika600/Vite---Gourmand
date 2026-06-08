<?php
session_start();
$pageTitle = 'Livraison - Vite & Gourmand';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/mailer.php';
require_once __DIR__ . '/../src/Services/MenuService.php';
require_once __DIR__ . '/../src/Services/CommandeService.php';
require_once __DIR__ . '/../src/Services/UtilisateurService.php';

$pdo = Database::getConnection();
$menuService = new MenuService($pdo);
$commandeService = new CommandeService($pdo);
$utilisateurService = new UtilisateurService($pdo);
$menus = $menuService->getMenusActifs();

// Si l'utilisateur est connecté, récupérer ses infos
$utilisateur = null;
if (isset($_SESSION['utilisateur_id'])) {
    $utilisateur = $utilisateurService->getUtilisateur($_SESSION['utilisateur_id']);
}

// Menu pré-sélectionné si on vient de detail-menus.php
$menuId = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : 0;

$message_succes = '';
$message_erreur = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['utilisateur_id'])) {
        $message_erreur = 'Vous devez être connecté pour commander.';
    } else {
        $menu_choisi = (int)$_POST['menu_choisi'];
        $nb_personnes = (int)$_POST['nb_personnes'];
        $adresse_livraison = trim($_POST['adresse_livraison'] ?? '');
        $ville_livraison = trim($_POST['ville_livraison'] ?? '');
        $date_livraison = $_POST['date_livraison'] ?? '';
        $heure_livraison = $_POST['heure_livraison'] ?? '';
        $distance = (float)($_POST['distance_km'] ?? 0);

        $menu = $menuService->getMenuComplet($menu_choisi);

        if (!$menu) {
            $message_erreur = 'Menu invalide.';
        } else if ($nb_personnes < $menu['menu']->getNbPersonnesMin()) {
            $message_erreur = 'Minimum ' . $menu['menu']->getNbPersonnesMin() . ' personnes pour ce menu.';
        } else if ($adresse_livraison === '' || $ville_livraison === '' || $date_livraison === '' || $heure_livraison === '') {
            $message_erreur = 'Tous les champs sont obligatoires.';
        } else {
            // Calcul du prix via le Service
            $prix = $commandeService->calculerPrix(
                $menu['menu']->getPrix(),
                $menu['menu']->getNbPersonnesMin(),
                $nb_personnes,
                $ville_livraison,
                $distance
            );

            // Créer la commande via le Service
            $infos = [
                'date_livraison' => $date_livraison,
                'heure_livraison' => $heure_livraison,
                'adresse_livraison' => $adresse_livraison,
                'ville_livraison' => $ville_livraison,
                'distance_km' => $distance,
                'nombre_personnes' => $nb_personnes
            ];
             $result = $commandeService->creerCommande(
                 $_SESSION['utilisateur_id'], $menu_choisi, $infos, $prix
            );
            $numero = $result['numero'];

            // Mail de confirmation
            envoyerMail($_SESSION['email'], 'Confirmation de commande ' . $numero,
                '<h2>Commande confirmée !</h2>
                <p>Bonjour ' . htmlspecialchars($_SESSION['prenom']) . ',</p>
                <p>Votre commande <strong>' . $numero . '</strong> a bien été enregistrée.</p>
                <p>Menu : ' . htmlspecialchars($menu['menu']->getTitre()) . '</p>
                <p>Date de livraison : ' . htmlspecialchars($date_livraison) . ' à ' . htmlspecialchars($heure_livraison) . '</p>
                <p>Total : ' . number_format($prix['total'], 2, ',', '') . '€</p>
                <p>Merci pour votre confiance !<br>L\'équipe Vite & Gourmand</p>'
            );

            // Mail rappel retour matériel
            envoyerMail($_SESSION['email'], 'Rappel : retour du matériel prêté',
                '<h2>Information matériel</h2>
                <p>Bonjour ' . htmlspecialchars($_SESSION['prenom']) . ',</p>
                <p>Suite à votre commande <strong>' . $numero . '</strong>, du matériel vous sera prêté pour la prestation.</p>
                <p>Ce matériel doit être restitué sous <strong>10 jours ouvrés</strong> après la prestation.</p>
                <p>Passé ce délai, une facturation de <strong>600€</strong> sera appliquée conformément à nos CGV.</p>
                <p>L\'équipe Vite & Gourmand</p>'
            );
            
         $message_succes = 'Commande ' . $numero . ' enregistrée ! Total : ' . number_format($prix['total'], 2, ',', ' ') . ' €';  
        }
    }
}
?>

<div class="container my-5">

    <!-- Info réduction -->
    <div class="alert alert-info">
        <strong>Réduction :</strong> Une réduction de 10% est appliquée pour toute commande ayant 5 personnes de plus que le nombre de personnes minimum indiqué dans le menu.
    </div>

    <h1 class="text-center mb-5">Livraison</h1>

    <?php if ($message_succes !== '') : ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($message_succes) ?></div>
    <?php endif; ?>

    <?php if ($message_erreur !== '') : ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($message_erreur) ?></div>
    <?php endif; ?>

    <?php if (!isset($_SESSION['utilisateur_id'])) : ?>
        <div class="alert alert-warning text-center">
            Vous devez <a href="<?= BASE_URL ?>compte.php" style="color: #432911; text-decoration: underline;" >vous connecter</a> pour passer commande.
        </div>
    <?php else : ?>

    <form method="post" action="livraison.php" class="formulaire-contact formulaire-livraison">
        <div class="row">

            <!-- COLONNE GAUCHE : Formulaire -->
           <div class="col-md-6">
                <h2 class="text-center mb-4">Formulaire</h2>

                <label for="nom">Nom :
                    <input id="nom" name="nom" type="text" value="<?= htmlspecialchars($utilisateur->getNom() ?? '') ?>">
                </label>

                <label for="prenom">Prénom :
                    <input id="prenom" name="prenom" type="text" value="<?= htmlspecialchars($utilisateur->getPrenom() ?? '') ?>">
                </label>

                <label for="email">Mail :
                    <input id="email" name="email" type="email" value="<?= htmlspecialchars($utilisateur->getEmail() ?? '') ?>">
                </label>

                <label for="telephone">Téléphone :
                    <input id="telephone" name="telephone" type="tel" value="<?= htmlspecialchars($utilisateur->getTelephone() ?? '') ?>">
                </label>

                <h3 class="mt-4 mb-3">Adresse de facturation</h3>

                <label for="adresse_facturation">Adresse :
                    <input id="adresse_facturation" name="adresse_facturation" type="text" value="<?= htmlspecialchars($utilisateur->getAdresse() ?? '') ?>">
                </label>

                <label for="ville_facturation">Ville :
                    <input id="ville_facturation" name="ville_facturation" type="text" value="<?= htmlspecialchars($utilisateur->getVille()?? '') ?>">
                </label>

                <label for="code_postal_facturation">Code postal :
                    <input id="code_postal_facturation" name="code_postal_facturation" type="text">
                </label>

                <label for="date_prestation">Date de la Prestation :
                    <input id="date_prestation" name="date_prestation" type="date" required>
                </label>
            </div>

            <!-- COLONNE DROITE : Prestation -->
          <div class="col-md-6">
                <h2 class="text-center mb-4">Prestation</h2>

                <label for="adresse_livraison">Adresse de livraison :
                    <input id="adresse_livraison" name="adresse_livraison" type="text" required>
                </label>

                <label for="ville_livraison">Ville :
                    <input id="ville_livraison" name="ville_livraison" type="text" required>
                </label>

                <label for="code_postal_livraison">Code postal :
                    <input id="code_postal_livraison" name="code_postal_livraison" type="text" required>
                </label>

                <!-- Date et Heure côte à côte -->
                <div class="row g-5">
                    <div class="col-6">
                        <label for="date_livraison">Date de livraison :
                            <input id="date_livraison" name="date_livraison" type="text" placeholder="jj/mm/aaaa" required>
                        </label>
                    </div>
                    <div class="col-6">
                        <label for="heure_livraison">Heure de livraison :
                            <input id="heure_livraison" name="heure_livraison" type="text" placeholder="hh:mm" required>
                        </label>
                    </div>
                </div>

                <!-- Menu et Personnes côte à côte -->
                <div class="row g-5">
                    <div class="col-6">
                       <label for="menu_choisi">Menu choisi :
                    <select id="menu_choisi" name="menu_choisi" required>
                        <option value="">Choisir</option>
                        <?php foreach ($menus as $m) : ?>
                            <option value="<?= $m->getId ()?>" <?= $m->getId() == $menuId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m->getTitre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label for="nb_personnes">Nombre de personnes :
                    <input id="nb_personnes" name="nb_personnes" type="text" required>
                </label>
                    </div>
                </div>

                <!-- Récap prix -->
                <label>Prix menu :
                    <input id="prix_menu" type="text" value="" readonly>
                </label>

                <label>Prix livraison :
                    <input id="prix_livraison" type="text" value="" readonly>
                </label>

                <label>Réduction :
                    <input id="reduction" type="text" value="" readonly>
                </label>

                <label><strong>Total TTC :</strong>
                    <input id="total_ttc" type="text" value="" readonly>
                </label>
            </div>

        </div>

        <!-- Bouton Valider -->
        <div class="text-center mt-4">
            <input type="submit" value="Valider" class="btn btn-dark">
        </div>
    </form>

    <?php endif; ?>

    <!-- Info majoration transport -->
    <p class="mt-5" style="font-size: 12px;">
        <strong>Majoration transport :</strong> Facturation de 5 euros majorée de 59 centimes par kilomètre parcouru si la livraison n'est pas dans la ville de Bordeaux. Livraison gratuite dans Bordeaux.
    </p>
    <p class="mt-2" style="font-size: 12px;">
    <strong>Matériel :</strong> En cas de prêt de matériel, celui-ci doit être restitué sous 10 jours ouvrés. Passé ce délai, une facturation de 600 € sera appliquée (voir <a href="<?= BASE_URL ?>cgv.php" style="color: #432911; text-decoration: underline;">CGV</a> ).
</p>

</div>

     <script>var BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="<?= BASE_URL ?>js/livraison.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>