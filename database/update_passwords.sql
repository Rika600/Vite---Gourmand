-- Mise à jour des mot de passe (bcrypt)

UPDATE utilisateur
SET password = '$2y$10$qFj20nb51VyC66IBrpwmmOETAJjsTKS8bFCfiGRhiTaD3OA3uVPvu'
WHERE email = 'jose@vite-gourmand.fr';

UPDATE utilisateur
SET password = '$2y$10$lMS4GM4ae1QYPGFS1XZzsOKAYAJZ8bq6d6JExNwDVOOCcs6SEP04i'
WHERE email = 'julie@vite-gourmand.fr';

UPDATE utilisateur
SET password = '$2y$10$MN7F0OTH9JSeVsYlbXBBGuA7kPxVqkA4PTjh5l4T3G2nBCl1RnP1q'
WHERE email = 'marie.dupont@email.com';

UPDATE utilisateur
SET password = '$2y$10$5vYWE4IkENGXzODI5MXj6uaFVTrBEJsQ6Ivn/534dbfwcTqAx/HH2'
WHERE email = 'alya.bernard@email.com';