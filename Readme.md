# Vite & Gourmand
Site web de traiteur événementiel, permettant aux clients de commander des menus pour leurs événements (mariages, fêtes, Noël, Pâques). Développé en PHP, MySQL et MongoDB.

Lien du site déployé : https://vite-gourmand-production-4cbf.up.railway.app

# Environnement de travail
- XAMPP (Apache + MySQL 8.0)
- PHP 8.2
- Composer
- Node.js + npm
- Git
- Extension PHP MongoDB
- Un compte MongoDB Atlas
- Docker

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
4. Importer le fichier `database/database.sql` (onglet Importer)

# Configuration
Créer un fichier `config.php` à la racine du projet avec :

```php
<?php
define('BASE_URL', '/vite-gourmand/');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3307');
define('DB_NAME', 'vite_gourmand');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

define('MONGODB_URI', 'votre_lien_mongodb_atlas');
define('MAIL_USERNAME', 'votre_email@gmail.com');
define('MAIL_PASSWORD', 'votre_mot_de_passe_application');
```

Ce fichier est dans le `.gitignore` et ne doit jamais être commité (contient des mots de passe).

## Lancer le projet

### Avec XAMPP
1. Démarrer Apache et MySQL dans XAMPP
2. Ouvrir le navigateur : `http://localhost:8080/vite-gourmand/`

### Avec Docker
1. Installer Docker Desktop
2. Dans config.php, activer la configuration Docker (commenter XAMPP, décommenter Docker)
3. Lancer : `docker-compose up --build`
4. Ouvrir le navigateur : `http://localhost:8080/`

# Déploiement

Le site est déployé sur **Railway** (https://railway.com).

### Services utilisés
- **Railway** : hébergement de l'application PHP + Apache (via Dockerfile)
- **Railway MySQL** : base de données relationnelle
- **MongoDB Atlas** : base de données non relationnelle (statistiques admin)

### Étapes du déploiement
1. Créer un compte Railway et connecter le dépôt GitHub
2. Ajouter un service MySQL dans le projet Railway
3. Configurer le builder sur **Dockerfile** (dans Settings > Build)
4. Ajouter les variables d'environnement dans le service web :
   - `MONGODB_URI` : lien de connexion MongoDB Atlas
   - `MAIL_USERNAME` : adresse email pour PHPMailer
   - `MAIL_PASSWORD` : mot de passe d'application Gmail
5. Les variables MySQL (`MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`) sont ajoutées automatiquement via les références partagées du service MySQL
6. Importer `database/database.sql` dans la base Railway via le client MySQL
7. Lancer `sync-mongodb.php` pour synchroniser les statistiques vers MongoDB Atlas
8. Générer un domaine public dans Settings > Networking

### Fichiers spécifiques au déploiement
- `Dockerfile` : image PHP 8.2 + Apache + extension MongoDB
- `start.sh` : script de démarrage qui génère `config.php` à partir des variables d'environnement et configure le port Apache
- `docker-compose.yml` : configuration pour le développement local avec Docker

# Identifiants de test

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Administrateur | jose@vite-gourmand.fr | Admin2026! |
| Employé | julie@vite-gourmand.fr | Employé2026! |
| Utilisateur | marie.dupont@email.com | Utilisateur2026! |
| Utilisateur | alya.bernard@email.com | Utilisateur2026! |

# Technologies

- **Front-end** : HTML5, CSS3/SCSS, Bootstrap 5, JavaScript (AJAX/fetch)
- **Back-end** : PHP 8.2 (POO), PDO, PHPMailer
- **Base de données** : MySQL (relationnelle), MongoDB Atlas (non relationnelle)
- **Outils** : Git/GitHub, Figma, Docker, Composer, npm
- **Déploiement** : Railway