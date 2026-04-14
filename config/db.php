<?php

// Paramètres de connexion à la base de données
$host = '127.0.0.1';
$port = '3307';
$dbname = 'vite_gourmand';
$username = 'root';
$password ='';
$charset ='utf8mb4';

// Chaîne de connexion (DSN = Data Source Name)
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

//Option PDO pour sécurité et gestion d'erreurs
$options = [
    PDO::ATTR_ERRMODE             => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES  => false,
];

// Tentative de connexion
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Erreur de connextion à la base de données : " . $e->getMessage());
}