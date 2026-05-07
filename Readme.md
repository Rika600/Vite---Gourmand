# Vite & Gourmand
Ce site est un site web de traiteur événementiel, permettant aux clients de commander des menus pour leurs événements (mariages, fêtes, Noël, Pâques). Développé en PHP, MySQL et MongoDB.


# Environnement de travail
- XAMPP (Apache + MySQL 8.0)
- PHP 8.2
- Composer
- Node.js + npm
- Git
- Extension PHP MongoDB
- Un compte MongoDB Atlas

# Installation 
1. Cloner le projet :
git clone https://github.com/Rika600/Vite---Gourmand.git

2. Placer le dossier dans `C:\xampp\htdocs\vite-gourmand`

3. Installer les dépendances PHP :
cd C:\xampp\htdocs\vite-gourmand
composer install

4. Installer les dépendances Node.js :
npm install

# Base de données
1. Lancer XAMPP (Apache + MySQL)
2. Ouvrir phpMyAdmin : `http://localhost:8080/phpmyadmin`
3. Créer une base de données `vite_gourmand`
4. Importer le fichier `database.sql` (onglet Importer)

# Configuration
Créer un fichier `config.php` à la racine du projet avec :

```php
<?php
// Configuration MongoDB
define('MONGODB_URI', 'votre_lien_mongodb_atlas');

// Configuration PHPMailer
define('MAIL_USERNAME', 'votre_email@gmail.com');
define('MAIL_PASSWORD', 'votre_mot_de_passe_application');
```



##  Lancer le projet
### Avec XAMPP
1. Démarrer Apache et MySQL dans XAMPP
2. Ouvrir le navigateur : `http://localhost:8080/vite-gourmand/`

### Avec Docker
1. Installer Docker Desktop
2. Dans config.php, activer la configuration Docker (commenter XAMPP, décommenter Docker)
3. Lancer : `docker-compose up --build`
4. Ouvrir le navigateur : `http://localhost:8080/`

# Déploiement