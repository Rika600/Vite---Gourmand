
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Database.php';

// 1. Connexion à MySQL
$pdo = Database::getConnection();

//2 Récupérer les stats depuis MySQL
$sql = "
    SELECT m.menu_id, m.titre,
            COUNT(c.commande_id) AS nb_commandes,
            COALESCE(SUM(c.prix_total), 0) AS chiffre_affaires
    FROM menu m
    LEFT JOIN commande c ON m.menu_id = c.menu_id AND c.statut != 'annulee'
    GROUP BY m.menu_id, m.titre
    ORDER BY m.menu_id
";
$stmt=$pdo->prepare($sql);
$stmt->execute();
$stats = $stmt->fetchAll();

//3. Connexion à MongoDB Atlas
$client = new MongoDB\Client("mongodb+srv://karima740_db_user:OKmvBeGmG2WP4bpO@cluster0.hsa6bee.mongodb.net/?appName=Cluster0");
$db = $client->vite_gourmand;
$collection = $db->stats_commandes;

//4. Vider l'ancienne collection
$collection->deleteMany([]);

//5. Insérer les nouvelles stats
foreach ($stats as $s) {
    $collection->insertOne([
        'menu_id'           => (int)$s['menu_id'],
        'titre'             => $s['titre'],
        'nb_commandes'      => (int)$s['nb_commandes'],
        'chiffre_affaires'  => (float)$s['chiffre_affaires'],
        'date_sync'         => new MongoDB\BSON\UTCDateTime()             
    ]);
}

echo "Synchronisation terminée ! " . count($stats) . " menus envoyés vers MongoDB.\n";