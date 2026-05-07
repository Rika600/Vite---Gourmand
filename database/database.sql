CREATE DATABASE IF NOT EXISTS vite_gourmand CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE vite_gourmand;

-- ==========================================================
-- GROUPE 1 : Tables de référence
-- ==========================================================

CREATE TABLE role (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE theme (
    theme_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE regime (
    regime_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE allergene (
    allergene_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- GROUPE 2 : Tables principales
-- ==========================================================

CREATE TABLE utilisateur (
    utilisateur_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    adresse_postale VARCHAR(255) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    pays VARCHAR(100) NOT NULL DEFAULT 'France',
    actif BOOLEAN NOT NULL DEFAULT TRUE,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    role_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES role(role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE horaire (
    horaire_id INT AUTO_INCREMENT PRIMARY KEY,
    jour VARCHAR(20) NOT NULL UNIQUE,
    heure_ouverture TIME,
    heure_fermeture TIME,
    ferme BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE plat (
    plat_id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    type ENUM('entree', 'plat', 'dessert') NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE menu (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    image_principale VARCHAR(255),
    nombre_personnes_min INT NOT NULL,
    prix_min DECIMAL(10,2) NOT NULL,
    conditions_menu TEXT,
    stock_disponible INT NOT NULL DEFAULT 0,
    actif BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- GROUPE 3 : Tables de liaison (many-to-many)
-- ==========================================================

CREATE TABLE menu_theme (
    menu_id INT NOT NULL,
    theme_id INT NOT NULL,
    PRIMARY KEY (menu_id, theme_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE CASCADE,
    FOREIGN KEY (theme_id) REFERENCES theme(theme_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE menu_regime (
    menu_id INT NOT NULL,
    regime_id INT NOT NULL,
    PRIMARY KEY (menu_id, regime_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE CASCADE,
    FOREIGN KEY (regime_id) REFERENCES regime(regime_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE menu_plat (
    menu_id INT NOT NULL,
    plat_id INT NOT NULL,
    PRIMARY KEY (menu_id, plat_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE CASCADE,
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE plat_allergene (
    plat_id INT NOT NULL,
    allergene_id INT NOT NULL,
    PRIMARY KEY (plat_id, allergene_id),
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id) ON DELETE CASCADE,
    FOREIGN KEY (allergene_id) REFERENCES allergene(allergene_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- GROUPE 4 : Tables métier
-- ==========================================================

CREATE TABLE commande (
    commande_id INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(50) NOT NULL UNIQUE,
    utilisateur_id INT NOT NULL,
    menu_id INT NOT NULL,

    -- Détail prestation
    date_commande DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
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

    -- Statut et matériel
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

    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE suivi_commande (
    suivi_id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    statut VARCHAR(50) NOT NULL,
    date_changement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    commentaire TEXT,
    FOREIGN KEY (commande_id) REFERENCES commande(commande_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- ==========================================================
-- DONNÉES DE RÉFÉRENCE
-- ==========================================================

INSERT INTO role (libelle) VALUES 
    ('administrateur'),
    ('employe'),
    ('utilisateur');

INSERT INTO theme (libelle) VALUES 
    ('Classique'),
    ('Pâques'),
    ('Mariage'),
    ('Noël');

INSERT INTO regime (libelle) VALUES 
    ('classique'),
    ('végétarien'),
    ('vegan');

INSERT INTO allergene (libelle) VALUES 
    ('Gluten'), ('Crustacés'), ('Œufs'), ('Poisson'),
    ('Arachides'), ('Soja'), ('Lait'), ('Fruits à coque'),
    ('Céleri'), ('Moutarde'), ('Graines de sésame'),
    ('Sulfites'), ('Lupin'), ('Mollusques');

INSERT INTO horaire (jour, heure_ouverture, heure_fermeture, ferme) VALUES 
    ('Lundi', NULL, NULL, TRUE),
    ('Mardi', '10:00:00', '20:00:00', FALSE),
    ('Mercredi', '10:00:00', '20:00:00', FALSE),
    ('Jeudi', '10:00:00', '20:00:00', FALSE),
    ('Vendredi', '10:00:00', '20:00:00', FALSE),
    ('Samedi', '10:00:00', '20:00:00', FALSE),
    ('Dimanche', '10:00:00', '20:00:00', FALSE);

-- ==========================================================
-- UTILISATEURS TEST (hashés avec bcrypt via password_hash)
-- Admin : Admin2026! | Employée : Employé2026!
-- Clientes : Utilisateur2026!
-- ==========================================================

-- Admin : José Martinez (role_id = 1)
INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, ville, pays, role_id)
VALUES (
    'jose@vite-gourmand.fr',
    '$2y$10$ItD3oukai3c9gz4hEFsRDu4w8PiGbfjAFiP5NER3t6K4qE2stBhrm',
    'Martinez',
    'José',
    '0556123456',
    '15 Rue des Gourmets',
    'Bordeaux',
    'France',
    1
);

-- Employée : Julie Durand (role_id = 2)
INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, ville, role_id)
VALUES (
    'julie@vite-gourmand.fr',
    '$2y$10$neUB/UVElQmcHmztOwwhMuNicapu62PZkzEtrp9O101ajk1voFQ2i',
    'Durand',
    'Julie',
    '0556654321',
    '15 Rue des Gourmets',
    'Bordeaux',
    2
);

-- Cliente : Marie Dupont (role_id = 3)
INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, ville, role_id)
VALUES (
    'marie.dupont@email.com',
    '$2y$10$pnV5WmX/cknYM09V5GXe5.PWs2xDS4Uao68E5BEkeG7iXVrgm8dZu',
    'Dupont',
    'Marie',
    '0612345678',
    '42 Avenue de la République',
    'Bordeaux',
    3
);

-- Cliente : Alya Bernard (role_id = 3)
INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, ville, role_id)
VALUES (
    'alya.bernard@email.com',
    '$2y$10$5XrQ0CDHkR3l5lWMnuHl..bSSkV3FQrbjf/QRvoh6xZXQPEO/u1q6',
    'Bernard',
    'Alya',
    '0698765432',
    '8 Boulevard du Maréchal Leclerc',
    'Mérignac',
    3
);

-- ==========================================================
-- PLATS (12 plats : 4 entrées + 4 plats + 4 desserts)
-- ==========================================================

-- Entrées
INSERT INTO plat (nom, type, description) VALUES
('Carpaccio de saumon, citron vert et aneth', 'entree', 'Saumon frais en fines tranches, citron vert et aneth frais'),
('Salade de melon, mozzarella et menthe fraîche', 'entree', 'Melon doux, mozzarella di bufala et menthe du jardin'),
('Foie gras maison, chutney de figues et pain toasté', 'entree', 'Foie gras préparé maison et chutney aux figues confites'),
('Assortiment d''amuse-bouches et velouté de potimarron', 'entree', 'Sélection d''amuse-bouches festifs et velouté de saison');

-- Plats principaux
INSERT INTO plat (nom, type, description) VALUES
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

-- ==========================================================
-- MENUS (4 menus complets)
-- ==========================================================

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

-- ==========================================================
-- ASSOCIATIONS menu_theme
-- ==========================================================

INSERT INTO menu_theme (menu_id, theme_id) VALUES
(1, 1),  -- Menu Classique Gourmand → Classique
(2, 2),  -- Menu Pâques Douceur → Pâques
(3, 3),  -- Menu Mariage Élégance → Mariage
(4, 4);  -- Menu Noël Festif → Noël

-- ==========================================================
-- ASSOCIATIONS menu_regime
-- ==========================================================

INSERT INTO menu_regime (menu_id, regime_id) VALUES
(1, 1),  -- Classique → classique
(2, 1),  -- Pâques → classique
(3, 1),  -- Mariage → classique
(4, 1);  -- Noël → classique

-- ==========================================================
-- ASSOCIATIONS menu_plat
-- ==========================================================

INSERT INTO menu_plat (menu_id, plat_id) VALUES
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

-- ==========================================================
-- ASSOCIATIONS plat_allergene
-- ==========================================================

-- Menu 1 : Classique Gourmand
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
(1, 4),  -- Carpaccio saumon → Poisson
(5, 1),  -- Volaille forestière → Gluten
(5, 7),  -- Volaille forestière → Lait
(9, 1),  -- Cupcakes → Gluten
(9, 3),  -- Cupcakes → Œufs
(9, 7),  -- Cupcakes → Lait
(9, 8);  -- Cupcakes/macarons → Fruits à coque

-- Menu 2 : Pâques Douceur
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
(2, 7),   -- Melon mozza → Lait
(6, 4),   -- Poisson grillé → Poisson
(10, 1),  -- Verrines → Gluten
(10, 7);  -- Verrines → Lait

-- Menu 3 : Mariage Élégance
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
(3, 1),   -- Foie gras (pain toasté) → Gluten
(7, 1),   -- Magret canard → Gluten
(11, 1),  -- Pièce montée → Gluten
(11, 3),  -- Pièce montée → Œufs
(11, 7);  -- Pièce montée → Lait

-- Menu 4 : Noël Festif
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
(4, 7),   -- Velouté potimarron → Lait
(8, 7),   -- Volaille morilles → Lait
(12, 1),  -- Bûche → Gluten
(12, 3),  -- Bûche → Œufs
(12, 7);  -- Bûche → Lait

-- ==========================================================
-- COLONNE token_reset (mot de passe oublié)
-- ==========================================================

ALTER TABLE utilisateur
ADD COLUMN token_reset VARCHAR(255) DEFAULT NULL,
ADD COLUMN token_expiration DATETIME DEFAULT NULL;

-- ==========================================================
-- COMMANDES TEST (4 commandes variées)
-- Prix calculés selon la logique du code :
--   prix_par_personne = prix_min / nombre_personnes_min
--   prix_menu_total = prix_par_personne × nombre_personnes
--   réduction -10% si nb_personnes >= min + 5
--   livraison gratuite à Bordeaux, sinon 5 + 0.59×km
-- ==========================================================

-- Commande 1 : Marie, Menu Classique, 10 pers, Bordeaux → pas de réduction, livraison gratuite
-- prix_par_personne = 450/10 = 45€, total menu = 45×10 = 450€, total = 450€
INSERT INTO commande (numero_commande, utilisateur_id, menu_id, date_commande, date_livraison, heure_livraison, adresse_livraison, ville_livraison, distance_km, nombre_personnes, prix_menu_unitaire, prix_menu_total, prix_livraison, reduction, prix_total, statut)
VALUES ('CMD-20260301-1001', 3, 1, '2026-03-01 14:00:00', '2026-03-15', '12:00:00', '42 Avenue de la République', 'Bordeaux', 0, 10, 45.00, 450.00, 0.00, 0.00, 450.00, 'terminee');

-- Commande 2 : Marie, Menu Pâques, 16 pers, Bordeaux → réduction -10% (16 >= 10+5)
-- prix_par_personne = 420/10 = 42€, total menu = 42×16 = 672€, réduction = 67.20€, total = 604.80€
INSERT INTO commande (numero_commande, utilisateur_id, menu_id, date_commande, date_livraison, heure_livraison, adresse_livraison, ville_livraison, distance_km, nombre_personnes, prix_menu_unitaire, prix_menu_total, prix_livraison, reduction, prix_total, statut)
VALUES ('CMD-20260310-2002', 3, 2, '2026-03-10 10:30:00', '2026-04-05', '13:00:00', '42 Avenue de la République', 'Bordeaux', 0, 16, 42.00, 672.00, 0.00, 67.20, 604.80, 'terminee');

-- Commande 3 : Alya, Menu Noël, 12 pers, Mérignac (15km) → pas de réduction, livraison payante
-- prix_par_personne = 400/8 = 50€, total menu = 50×12 = 600€, livraison = 5+(0.59×15) = 13.85€, total = 613.85€
INSERT INTO commande (numero_commande, utilisateur_id, menu_id, date_commande, date_livraison, heure_livraison, adresse_livraison, ville_livraison, distance_km, nombre_personnes, prix_menu_unitaire, prix_menu_total, prix_livraison, reduction, prix_total, statut)
VALUES ('CMD-20260415-3003', 4, 4, '2026-04-15 09:00:00', '2026-04-30', '19:00:00', '8 Boulevard du Maréchal Leclerc', 'Mérignac', 15.00, 12, 50.00, 600.00, 13.85, 0.00, 613.85, 'terminee');

-- Commande 4 : Alya, Menu Mariage, 25 pers, Mérignac (15km) → réduction -10% (25 >= 20+5)
-- prix_par_personne = 1300/20 = 65€, total menu = 65×25 = 1625€, réduction = 162.50€, livraison = 13.85€, total = 1476.35€
INSERT INTO commande (numero_commande, utilisateur_id, menu_id, date_commande, date_livraison, heure_livraison, adresse_livraison, ville_livraison, distance_km, nombre_personnes, prix_menu_unitaire, prix_menu_total, prix_livraison, reduction, prix_total, statut)
VALUES ('CMD-20260420-4004', 4, 3, '2026-04-20 11:00:00', '2026-05-10', '12:30:00', '8 Boulevard du Maréchal Leclerc', 'Mérignac', 15.00, 25, 65.00, 1625.00, 13.85, 162.50, 1476.35, 'en_attente');

-- ==========================================================
-- SUIVI COMMANDES (historique des changements de statut)
-- ==========================================================

-- Suivi commande 1 (terminée)
INSERT INTO suivi_commande (commande_id, statut, date_changement, commentaire) VALUES
(1, 'en_attente', '2026-03-01 14:00:00', 'Commande reçue'),
(1, 'accepte', '2026-03-02 09:00:00', 'Commande validée par Julie'),
(1, 'en_preparation', '2026-03-14 08:00:00', 'Préparation en cours'),
(1, 'en_cours_livraison', '2026-03-15 11:00:00', 'Livraison en route'),
(1, 'livre', '2026-03-15 12:15:00', 'Livré à l''adresse'),
(1, 'en_attente_retour_materiel', '2026-03-16 10:00:00', 'En attente du retour matériel'),
(1, 'terminee', '2026-03-18 14:00:00', 'Matériel récupéré, commande terminée');

-- Suivi commande 2 (terminée)
INSERT INTO suivi_commande (commande_id, statut, date_changement, commentaire) VALUES
(2, 'en_attente', '2026-03-10 10:30:00', 'Commande reçue'),
(2, 'accepte', '2026-03-11 09:00:00', 'Commande validée'),
(2, 'en_preparation', '2026-04-04 08:00:00', 'Préparation en cours'),
(2, 'en_cours_livraison', '2026-04-05 12:00:00', 'Livraison en route'),
(2, 'livre', '2026-04-05 13:20:00', 'Livré'),
(2, 'terminee', '2026-04-08 10:00:00', 'Commande terminée');

-- Suivi commande 3 (terminée)
INSERT INTO suivi_commande (commande_id, statut, date_changement, commentaire) VALUES
(3, 'en_attente', '2026-04-15 09:00:00', 'Commande reçue'),
(3, 'accepte', '2026-04-16 10:00:00', 'Commande validée'),
(3, 'en_preparation', '2026-04-29 07:00:00', 'Préparation en cours'),
(3, 'en_cours_livraison', '2026-04-30 18:00:00', 'Livraison en route'),
(3, 'livre', '2026-04-30 19:10:00', 'Livré'),
(3, 'terminee', '2026-05-03 11:00:00', 'Commande terminée');

-- Suivi commande 4 (en attente)
INSERT INTO suivi_commande (commande_id, statut, date_changement, commentaire) VALUES
(4, 'en_attente', '2026-04-20 11:00:00', 'Commande reçue');

-- ==========================================================
-- AVIS TEST (3 avis validés + 1 en attente)
-- ==========================================================

-- Avis 1 : Marie sur commande 1 (validé) → affiché sur la page d'accueil
INSERT INTO avis (commande_id, utilisateur_id, note, commentaire, statut_validation, date_validation)
VALUES (1, 3, 5, 'Service impeccable ! Le menu Classique Gourmand était délicieux, tous nos invités ont adoré. Livraison ponctuelle et équipe très professionnelle.', 'valide', '2026-03-20 10:00:00');

-- Avis 2 : Marie sur commande 2 (validé) → affiché sur la page d'accueil
INSERT INTO avis (commande_id, utilisateur_id, note, commentaire, statut_validation, date_validation)
VALUES (2, 3, 4, 'Très bon menu de Pâques, les plats étaient frais et savoureux. Petit bémol sur le délai de livraison mais rien de grave. Je recommande !', 'valide', '2026-04-10 14:00:00');

-- Avis 3 : Alya sur commande 3 (validé) → affiché sur la page d'accueil
INSERT INTO avis (commande_id, utilisateur_id, note, commentaire, statut_validation, date_validation)
VALUES (3, 4, 5, 'Le menu Noël Festif a fait sensation lors de notre réveillon ! La bûche revisitée était un vrai régal. Merci à toute l''équipe Vite & Gourmand.', 'valide', '2026-05-05 09:00:00');