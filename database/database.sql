-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : lun. 13 avr. 2026 à 13:00
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `vite_gourmand`
--

-- --------------------------------------------------------

--
-- Structure de la table `allergene`
--

CREATE TABLE `allergene` (
  `allergene_id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `allergene`
--

INSERT INTO `allergene` (`allergene_id`, `libelle`) VALUES
(5, 'Arachides'),
(9, 'Céleri'),
(2, 'Crustacés'),
(8, 'Fruits à coque'),
(1, 'Gluten'),
(11, 'Graines de sésame'),
(7, 'Lait'),
(13, 'Lupin'),
(14, 'Mollusques'),
(10, 'Moutarde'),
(3, 'Œufs'),
(4, 'Poisson'),
(6, 'Soja'),
(12, 'Sulfites');

-- --------------------------------------------------------

--
-- Structure de la table `horaire`
--

CREATE TABLE `horaire` (
  `horaire_id` int(11) NOT NULL,
  `jour` varchar(20) NOT NULL,
  `heure_ouverture` time DEFAULT NULL,
  `heure_fermeture` time DEFAULT NULL,
  `ferme` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `horaire`
--

INSERT INTO `horaire` (`horaire_id`, `jour`, `heure_ouverture`, `heure_fermeture`, `ferme`) VALUES
(1, 'Lundi', NULL, NULL, 1),
(2, 'Mardi', '10:00:00', '20:00:00', 0),
(3, 'Mercredi', '10:00:00', '20:00:00', 0),
(4, 'Jeudi', '10:00:00', '20:00:00', 0),
(5, 'Vendredi', '10:00:00', '20:00:00', 0),
(6, 'Samedi', '10:00:00', '20:00:00', 0),
(7, 'Dimanche', '10:00:00', '20:00:00', 0);

-- --------------------------------------------------------

--
-- Structure de la table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `image_principale` varchar(255) DEFAULT NULL,
  `nombre_personnes_min` int(11) NOT NULL,
  `prix_min` decimal(10,2) NOT NULL,
  `conditions_menu` text DEFAULT NULL,
  `stock_disponible` int(11) NOT NULL DEFAULT 0,
  `actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menu_plat`
--

CREATE TABLE `menu_plat` (
  `menu_id` int(11) NOT NULL,
  `plat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menu_regime`
--

CREATE TABLE `menu_regime` (
  `menu_id` int(11) NOT NULL,
  `regime_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menu_theme`
--

CREATE TABLE `menu_theme` (
  `menu_id` int(11) NOT NULL,
  `theme_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `plat`
--

CREATE TABLE `plat` (
  `plat_id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `type` enum('entree','plat','dessert') NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `plat_allergene`
--

CREATE TABLE `plat_allergene` (
  `plat_id` int(11) NOT NULL,
  `allergene_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `regime`
--

CREATE TABLE `regime` (
  `regime_id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `regime`
--

INSERT INTO `regime` (`regime_id`, `libelle`) VALUES
(1, 'classique'),
(3, 'vegan'),
(2, 'végétarien');

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`role_id`, `libelle`) VALUES
(1, 'administrateur'),
(2, 'employe'),
(3, 'utilisateur');

-- --------------------------------------------------------

--
-- Structure de la table `theme`
--

CREATE TABLE `theme` (
  `theme_id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `theme`
--

INSERT INTO `theme` (`theme_id`, `libelle`) VALUES
(1, 'classique'),
(3, 'Mariage'),
(4, 'Noël'),
(2, 'Pâques');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `utilisateur_id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `adresse_postale` varchar(255) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `pays` varchar(100) NOT NULL DEFAULT 'France',
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `allergene`
--
ALTER TABLE `allergene`
  ADD PRIMARY KEY (`allergene_id`),
  ADD UNIQUE KEY `libelle` (`libelle`);

--
-- Index pour la table `horaire`
--
ALTER TABLE `horaire`
  ADD PRIMARY KEY (`horaire_id`),
  ADD UNIQUE KEY `jour` (`jour`);

--
-- Index pour la table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Index pour la table `menu_plat`
--
ALTER TABLE `menu_plat`
  ADD PRIMARY KEY (`menu_id`,`plat_id`),
  ADD KEY `plat_id` (`plat_id`);

--
-- Index pour la table `menu_regime`
--
ALTER TABLE `menu_regime`
  ADD PRIMARY KEY (`menu_id`,`regime_id`),
  ADD KEY `regime_id` (`regime_id`);

--
-- Index pour la table `menu_theme`
--
ALTER TABLE `menu_theme`
  ADD PRIMARY KEY (`menu_id`,`theme_id`),
  ADD KEY `theme_id` (`theme_id`);

--
-- Index pour la table `plat`
--
ALTER TABLE `plat`
  ADD PRIMARY KEY (`plat_id`);

--
-- Index pour la table `plat_allergene`
--
ALTER TABLE `plat_allergene`
  ADD PRIMARY KEY (`plat_id`,`allergene_id`),
  ADD KEY `allergene_id` (`allergene_id`);

--
-- Index pour la table `regime`
--
ALTER TABLE `regime`
  ADD PRIMARY KEY (`regime_id`),
  ADD UNIQUE KEY `libelle` (`libelle`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `libelle` (`libelle`);

--
-- Index pour la table `theme`
--
ALTER TABLE `theme`
  ADD PRIMARY KEY (`theme_id`),
  ADD UNIQUE KEY `libelle` (`libelle`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`utilisateur_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `allergene`
--
ALTER TABLE `allergene`
  MODIFY `allergene_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `horaire`
--
ALTER TABLE `horaire`
  MODIFY `horaire_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `plat`
--
ALTER TABLE `plat`
  MODIFY `plat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `regime`
--
ALTER TABLE `regime`
  MODIFY `regime_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `theme`
--
ALTER TABLE `theme`
  MODIFY `theme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `utilisateur_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `menu_plat`
--
ALTER TABLE `menu_plat`
  ADD CONSTRAINT `menu_plat_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_plat_ibfk_2` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`plat_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `menu_regime`
--
ALTER TABLE `menu_regime`
  ADD CONSTRAINT `menu_regime_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_regime_ibfk_2` FOREIGN KEY (`regime_id`) REFERENCES `regime` (`regime_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `menu_theme`
--
ALTER TABLE `menu_theme`
  ADD CONSTRAINT `menu_theme_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_theme_ibfk_2` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`theme_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `plat_allergene`
--
ALTER TABLE `plat_allergene`
  ADD CONSTRAINT `plat_allergene_ibfk_1` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`plat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `plat_allergene_ibfk_2` FOREIGN KEY (`allergene_id`) REFERENCES `allergene` (`allergene_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- GROUPE 4 : Tables métier

-- Table des commandes
CREATE TABLE commande (
  commande_id INT AUTO_INCREMENT PRIMARY KEY,
  numero_commande VARCHAR(50) NOT NULL UNIQUE,
  utilisateur_id INT NOT NULL,
  menu_id INT NOT NULL,

-- Détail prestation
date_commande DATETIME NOT NULL DEFAULT current_timestamp,
date_livraison DATE NOT NULL,
heure_livraison TIME NOT NULL,
adresse_livraison VARCHAR(255) NOT NULL,
ville_livraison VARCHAR(100) NOT NULL,
distance_km DECIMAL(6,2) NOT NULL DEFAULT 0,

-- Calculs
nombre_personnes INT NOT NULL,
prix_menu_unitaire DECIMAL(10,2) NOT NULL,
prix_menu_total DECIMAL(10,2) NOT NULL,
prix_livraison DECIMAL(10,2) NOT NULL DEFAULT 0,
reduction DECIMAL(10,2) NOT NULL DEFAULT 0,
prix_total DECIMAL(10,2) NOT NULL,

-- Satut et matériel
statut ENUM(
  'en_attente',
  'accepte',
  'en_preparation',
  'en_cours_livraison',
  'livre',
  'en_attente_retour_materiel',
  'terminee',
  'annulee'
) NOT NULL DEFAULT 'en_attente',
pret_materiel BOOLEAN NOT NULL DEFAULT FALSE,
materiel_restitue BOOLEAN NOT NULL DEFAULT FALSE,

-- Motif d'annulation (si applicable)
motif_annulation TEXT,
mode_contact_annulation ENUM('telephone', 'email'),

FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (utilisateur_id),
FOREIGN KEY (menu_id) REFERENCES menu(menu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--Table suivant de changements de statut (historique)
CREATE TABLE suivi_commande (
  suivi_id INT AUTO_INCREMENT PRIMARY KEY,
  commande_id INT NOT NULL,
  statut VARCHAR(50) NOT NULL,
  date_changement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  commentaire TEXT,
  FOREIGN KEY (commande_id) REFERENCES commande(commande_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des avis clients
CREATE TABLE avis (
  avis_id INT AUTO_INCREMENT PRIMARY KEY,
  commande_id INT NOT NULL UNIQUE,
  utilisateur_id INT NOT NULL,
  note TINYINT NOT NULL CHECK (note BETWEEN 1 AND 5),
  commentaire TEXT NOT NULL,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  statut_validation ENUM('en_attente', 'valide', 'refuse') NOT NULL DEFAULT 'en_attente',
  date_validation DATETIME,
  FOREIGN KEY (commande_id) REFERENCES commande(commande_id),
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- JEU DE DONNÉES TEST 

/* USE vite_gourmand;

-- 1. COMPTE ADMIN (José)
-- role_id = 1 (administrateur)

INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, ville, pays, role_id)
VALUES (
  'jose@vite-gourmand.fr',
  '$2y$10$KvF1p7eH0sZnVhR4lJ5QXeJxXhO2kQ6Pj8N5mT3wB1cA7dG9fH4qS',
  'Martinez',
  'José',
  '0556123456',
  '15 Rue des Gourmets',
  'Bordeaux',
  'France',
  1
);

-- 2. UTILISATEURS TEST (pour tester les parcours)
-- role_id = 3 (utilisateur) pour les clients, role_id = 2 pour l'employée Julie

-- Employée Julie (role_id = 2)

INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, ville, role_id)
VALUES (
    'julie@vite-gourmand.fr',
    '$2y$10$KvF1p7eH0sZnVhR4lJ5QXeJxXhO2kQ6Pj8N5mT3wB1cA7dG9fH4qS',
    'Durand',
    'Julie',
    '0556654321',
    '15 Rue des Gourmets',
    'Bordeaux',
    2
);

-- Utilisateurs clients (role_id = 3)

INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, ville, role_id)
VALUES (
    'marie.dupont@email.com',
    '$2y$10$KvF1p7eH0sZnVhR4lJ5QXeJxXhO2kQ6Pj8N5mT3wB1cA7dG9fH4qS',
    'Dupont',
    'Marie',
    '0612345678',
    '42 Avenue de la République',
    'Bordeaux',
    3
),
(
'alya.bernard@email.com',
    '$2y$10$KvF1p7eH0sZnVhR4lJ5QXeJxXhO2kQ6Pj8N5mT3wB1cA7dG9fH4qS',
    'Bernard',
    'Alya',
    '0698765432',
    '8 Boulevard du Maréchal Leclerc',
    'Mérignac',
    3
); */


-- 3. PLATS (12 plats : 4 entrées + 4 plats + 4 desserts)

--Entrées
INSERT INTO plat (nom, type, description) VALUES
('Carpaccio de saumon, citron vert et aneth', 'entree', 'Saumon frais en fines tranches, citron vert et aneth frais'),
('Salade de melon, mozzarella et menthe fraîche', 'entree', 'Melon doux, mozzarella di bufala et menthe du jardin'),
('Foie gras maison, chutney de figues et pain toasté', 'entree', 'Foie gras préparé maison et chutney aux figues confites'),
('Assortiment d''amuse-bouches et velouté de potimarron', 'entree', 'Sélection d''amuse-bouches festifs et velouté de saison');

-- Plats principaux
INSERT INTO plat(nom, type, description) VALUES
('Suprême de volaille sauce forestière, gratin', 'plat', 'Volaille fermière et sa sauce aux champignons, gratin dauphinois'),
('Filet de poisson grillé, légumes croquants', 'plat', 'Poisson frais du jour et légumes de saison al dente'),
('Magret de canard rôti, sauce miel et épices, pommes fondantes', 'plat', 'Magret rosé, sauce au miel et épices douces, pommes de terre fondantes'),
('Suprême de volaille rôti, sauce aux morilles, légumes de saison', 'plat', 'Volaille rôtie aux morilles et accompagnement de saison');

-- Desserts
INSERT INTO plat (nom, type, description) VALUES
('Assortiment de cupcakes colorés et macarons', 'dessert', 'Sélection de mignardises sucrées colorées'),
('Douceur de verrines gourmandes, crème légère et biscuits', 'dessert', 'Verrines sucrées aux textures variées'),
('Pièce montée et assortiment de mignardises', 'dessert', 'Pièce montée traditionnelle et mignardises de mariage'),
('Bûche de Noël revisitée, chocolat et fruits rouges', 'dessert', 'Bûche moderne chocolat noir et fruits rouges');

-- 4. MENUS (4 menus complets)

INSERT INTO menu (titre, description, image_principale, nombre_personnes_min, prix_min, conditions_menu, stock_disponible)
VALUES
  (
    'Menu Classique Gourmand',
    'Un menu raffiné aux saveurs intemporelles, parfait pour toute occasion.',
    '/images/menus/classique.jpg',
    10,
    450.00,
    'Commande à effectuer 2 semaines avant la prestation.',
    5
),
(
    'Menu Pâques Douceur',
    'Des saveurs fraîches et printanières pour célébrer Pâques en beauté.',
    '/images/menus/paques.jpg',
    10,
    420.00,
    'Commande à effectuer 1 semaine avant la prestation.',
    7
),
(
    'Menu Mariage Élégance',
    'Une expérience gastronomique d''exception pour sublimer votre mariage.',
    '/images/menus/mariage.jpg',
    20,
    1300.00,
    'Commande à effectuer 3 semaines avant la prestation.',
    3
),
(
    'Menu Noël Festif',
    'Un menu de fête pour émerveiller vos convives lors des célébrations de Noël.',
    '/images/menus/noel.jpg',
    8,
    400.00,
    'Commande à effectuer 2 semaines avant la prestation.',
    6
);

-- NB : prix_min = prix_par_personne × nombre_personnes_min

-- 5. ASSOCIATIONS menu_theme

INSERT INTO menu_theme(menu_id, theme_id) VALUES
(1, 1),  -- Menu Classique Gourmand → Classique
(2, 2),  -- Menu Pâques Douceur → Pâques
(3, 3),  -- Menu Mariage Élégance → Mariage
(4, 4);  -- Menu Noël Festif → Noël

-- 6. ASSOCIATIONS menu_regime

INSERT INTO menu_regime (menu_id, regime_id) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1);


-- 7. ASSOCIATIONS menu_plat

INSERT INTO menu_plat(menu_id, plat_id) VALUES
-- Menu 1 : Classique Gourmand
(1, 1),   -- Carpaccio saumon
(1, 5),   -- Volaille forestière
(1, 9),   -- Cupcakes/macarons

-- Menu 2 : Pâques Douceur
(2, 2),   -- Melon mozza
(2, 6),   -- Poisson grillé
(2, 10),  -- Verrines

-- Menu 3 : Mariage Élégance
(3, 3),   -- Foie gras
(3, 7),   -- Magret canard
(3, 11),  -- Pièce montée

-- Menu 4 : Noël Festif
(4, 4),   -- Amuse-bouches
(4, 8),   -- Volaille morilles
(4, 12);  -- Bûche Noël


-- 8. ASSOCIATIONS plat_allergene

-- Menu 1 : Classique Gourmand → Poisson, Lait, Gluten, Œufs, Fruits à coque
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
(1, 4),  -- Carpaccio saumon → Poisson
(5, 1),  -- Volaille forestière → Gluten
(5, 7),  -- Volaille forestière → Lait (crème)
(9, 1),  -- Cupcakes → Gluten
(9, 3),  -- Cupcakes → Œufs
(9, 7),  -- Cupcakes → Lait
(9, 8);  -- Cupcakes/macarons → Fruits à coque

-- Menu 2 : Pâques Douceur → Poisson, Lait, Gluten
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
(2, 7),   -- Melon mozza → Lait
(6, 4),   -- Poisson grillé → Poisson
(10, 1),  -- Verrines → Gluten
(10, 7);  -- Verrines → Lait

-- Menu 3 : Mariage Élégance → Gluten, Lait, Œufs
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
(3, 1),   -- Foie gras (pain toasté) → Gluten
(7, 1),   -- Magret canard → Gluten
(11, 1),  -- Pièce montée → Gluten
(11, 3),  -- Pièce montée → Œufs
(11, 7);  -- Pièce montée → Lait

-- Menu 4 : Noël Festif → Lait, Gluten, Œufs
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
(4, 7),   -- Velouté potimarron → Lait
(8, 7),   -- Volaille morilles → Lait (crème)
(12, 1),  -- Bûche → Gluten
(12, 3),  -- Bûche → Œufs
(12, 7);  -- Bûche → Lait
