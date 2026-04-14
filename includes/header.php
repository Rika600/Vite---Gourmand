<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Vite & Gourmand' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/vite-gourmand/scss/main.css">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="/vite-gourmand/">Vite & Gourmand</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-5">
                    <li class="nav-item"><a class="nav-link text-white" href="/vite-gourmand/">ACCUEIL</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/vite-gourmand/menus.php">MENU</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/vite-gourmand/galerie.php">GALERIE</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/vite-gourmand/livraison.php">LIVRAISON</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/vite-gourmand/compte.php">COMPTE</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/vite-gourmand/contact.php">CONTACT</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main id="main-page" style="min-height: 80vh;">
