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
