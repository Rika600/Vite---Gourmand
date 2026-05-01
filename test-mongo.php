<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

$client = new MongoDB\Client(MONGODB_URI);
$db = $client->vite_gourmand;
$col = $db->stats_commandes;
$results = $col->find();

foreach ($results as $doc) {
    echo $doc['titre'] . PHP_EOL;
}

echo 'Connexion OK !';