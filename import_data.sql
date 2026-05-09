-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 09, 2026 at 01:28 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

--
-- Database: `sympet`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorie`
--
DROP TABLE IF EXISTS `reset_password_request`;
DROP TABLE IF EXISTS `avis`;
DROP TABLE IF EXISTS `ligne_commande`;
DROP TABLE IF EXISTS `commande`;
DROP TABLE IF EXISTS `produit`;
DROP TABLE IF EXISTS `categorie`;
DROP TABLE IF EXISTS `user`;

CREATE TABLE `categorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `categorie` (`id`, `nom`) VALUES
(1, 'Chien'),
(2, 'Chat'),
(3, 'Oiseau'),
(4, 'Poisson'),
(5, 'Rongeur');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`id`, `email`, `roles`, `password`) VALUES
(1, 'admin@sympet.com', '["ROLE_ADMIN"]', '$2y$13$C7OsI0MvFbaRKSK7YDN6A.LP1M0Ld.Dp3BUZ908T0.dCKW2AYJvi6'),
(2, 'marie.lacombe@baudry.fr', '["ROLE_USER"]', '$2y$13$LcPcCc32sUJzfKLGVkfV4.9obPj9SGX0SeuHqHfuRp7FhIQ6aOuQy'),
(3, 'begue.benoit@tele2.fr', '["ROLE_USER"]', '$2y$13$pKxj5YStAqvfXiwLC2CK7.zy1xy3aI7OwavnWuXflR1bCjwR4Om7u'),
(4, 'dmuller@orange.fr', '["ROLE_USER"]', '$2y$13$4y6tUqO80Rg09ByD24QLFOkvtx3pXhRuCOzpygst4DFLlG7vg2BTW'),
(5, 'deschamps.jacques@orange.fr', '["ROLE_USER"]', '$2y$13$wBLD3TSepUTQDBwYZKmbsOEB.41C4584nMU2ukf0mNcSNbQKT/6BW'),
(6, 'valentine31@laposte.net', '["ROLE_USER"]', '$2y$13$bOSGBCPzE4TbNmPCHiMBT.09AuthQ2NKUOGur.7spKZyp5lIF7HWC'),
(7, 'nmary@hotmail.fr', '["ROLE_USER"]', '$2y$13$HhabmOTbXnhd0aVVVSt0V.Js454TjyuhlD66QB3ksBHKySk5qsngq'),
(8, 'baudry.frederic@free.fr', '["ROLE_USER"]', '$2y$13$vAoXmkwbgbebJxnfHUGPiOsKGfxzw8dXXkfkB9JARoiKy//69Uzna'),
(9, 'suzanne.marty@yahoo.fr', '["ROLE_USER"]', '$2y$13$i2ITFJpgW/PBk3xtObSaiea0v7C.Qw42QTrAFRV9IgDE214fi81aS'),
(10, 'hguillou@wanadoo.fr', '["ROLE_USER"]', '$2y$13$1d/O6xRc9s9MN7Qbk8J4SuJ1wzkH6M9eKNXwM4PSMCZ5eydwEhPj.'),
(11, 'sophie66@parent.com', '["ROLE_USER"]', '$2y$13$4.6DejyDm8SVMGMUTvMvI..dJaMHjHSHz0FBuePhyJezvkOsgf8M6');

-- --------------------------------------------------------

--
-- Table structure for table `produit`
--

CREATE TABLE `produit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `stock` int NOT NULL,
  `image` varchar(255) NOT NULL,
  `categorie_id` int NOT NULL,
  `description` longtext,
  `prix` double NOT NULL,
  `animal_type` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `produit_type` varchar(255) DEFAULT NULL,
  `image_url` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_29A5EC27BCF5E72D` (`categorie_id`),
  CONSTRAINT `FK_29A5EC27BCF5E72D` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `produit` (`id`, `nom`, `stock`, `image`, `categorie_id`, `description`, `prix`, `animal_type`, `created_at`, `produit_type`, `image_url`) VALUES
(1, 'DC ADULT SENSITIVE AGNEAU RIZ 20 Kg', 97, 'https://assets.zanimo.tn/produits/4014355330527.jpg', 1, 'Croquettes premium pour chiens adultes sensibles, formule agneau et riz.', 390, 'Chien', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/4014355330527.jpg'),
(2, 'OWNAT CHIEN COMPLET 4 KG', 51, 'https://assets.zanimo.tn/produits/8429037016044.jpg', 1, 'Aliment complet et équilibré pour chiens adultes de toutes races.', 54, 'Chien', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8429037016044.jpg'),
(3, 'SIMBA DOG CROQUETTES CHICKEN 10 KG', 67, 'https://assets.zanimo.tn/produits/8009470009850.jpg', 1, 'Croquettes au poulet pour chien, sac économique 10kg.', 108.5, 'Chien', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8009470009850.jpg'),
(4, 'CARNILOVE CHIEN SAUMON 1,5KG', 93, 'https://assets.zanimo.tn/produits/8595602508914.jpg', 1, 'Croquettes sans céréales au saumon pour chiens actifs.', 50, 'Chien', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8595602508914.jpg'),
(5, 'PYLKRON CHIEN ADULT 4KG', 86, 'https://assets.zanimo.tn/produits/8429037006021.jpg', 1, 'Alimentation complète économique pour chiens adultes.', 46, 'Chien', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8429037006021.jpg'),
(6, 'BONES MUNCHY STICKS 12,5CM 25PCS', 94, 'https://assets.zanimo.tn/produits/8023222022225.jpg', 1, 'Bâtonnets à mâcher naturels pour l\'hygiène dentaire du chien.', 9.8, 'Chien', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8023222022225.jpg'),
(7, 'COLLIER NYLON ZANILOVE 25 MM ROUGE', 95, 'https://assets.zanimo.tn/produits/6191469600734.jpg', 1, 'Collier nylon solide et ajustable pour chiens de taille moyenne.', 15.7, 'Chien', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/6191469600734.jpg'),
(8, 'COLLIER NYLON ZANILOVE 19 MM NOIR', 64, 'https://assets.zanimo.tn/produits/6191469600673.jpg', 1, 'Collier nylon résistant pour chiens de taille moyenne.', 11.6, 'Chien', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/6191469600673.jpg'),
(9, 'OS EN COTON W/BALL PASTEL JAUNE 40CM', 65, 'https://assets.zanimo.tn/produits/8023222226968.jpg', 1, 'Jouet os en coton avec balle, idéal pour les séances de jeu.', 18.2, 'Chien', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/8023222226968.jpg'),
(10, 'CONTENEUR JERRY 23 LT SILVER', 88, 'https://assets.zanimo.tn/produits/0f255-8003507703001.jpg', 1, 'Conteneur hermétique pour stocker les croquettes au sec.', 44.55, 'Chien', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/0f255-8003507703001.jpg'),
(11, 'OWNAT CHAT DAILY CARE 4 KG', 69, 'https://assets.zanimo.tn/produits/8429037016174.jpg', 2, 'Croquettes équilibrées pour chats adultes, soutien immunitaire.', 66, 'Chat', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8429037016174.jpg'),
(12, 'OWNAT CHAT KITTEN 1,5 KG', 62, 'https://assets.zanimo.tn/produits/8429037016259.jpg', 2, 'Aliment spécifique pour chatons jusqu\'à 12 mois.', 41, 'Chat', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8429037016259.jpg'),
(13, 'OWNAT CHAT STERILIZED 4 KG', 41, 'https://assets.zanimo.tn/produits/8429037016280.jpg', 2, 'Formule adaptée pour chats stérilisés, contrôle du poids.', 75, 'Chat', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8429037016280.jpg'),
(14, 'SIMBA CAT CROQUETTES CHICKEN 2KG', 45, 'https://assets.zanimo.tn/produits/8009470016063.jpg', 2, 'Croquettes au poulet pour chats adultes, format familial.', 30.5, 'Chat', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8009470016063.jpg'),
(15, 'CARNILOVE CHAT SAUMON POILS LONG 2 KG', 28, 'https://assets.zanimo.tn/produits/de5d0-8595602512287.jpg', 2, 'Croquettes sans céréales pour chats à poils longs.', 71, 'Chat', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/de5d0-8595602512287.jpg'),
(16, 'DC KITTEN 400 gr', 21, 'https://assets.zanimo.tn/produits/4014355243049.jpg', 2, 'Aliment premium pour chatons de moins d\'un an.', 26, 'Chat', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/4014355243049.jpg'),
(17, 'COLLIER CHAT GLOSSY FUCHSIA', 89, 'https://assets.zanimo.tn/produits/8023222125209.jpg', 2, 'Collier élégant pour chats avec clip de sécurité.', 14, 'Chat', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/8023222125209.jpg'),
(18, 'COLLIER CHAT CATMANIA BLEU', 51, 'https://assets.zanimo.tn/produits/8023222204256.jpg', 2, 'Collier coloré avec élastique de sécurité pour chats.', 14, 'Chat', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/8023222204256.jpg'),
(19, 'BERLIN BLEU 38x38x59cm', 15, 'https://assets.zanimo.tn/produits/8023222128569.jpg', 2, 'Arbre à chat compact avec plateforme et griffe inclus.', 58, 'Chat', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/8023222128569.jpg'),
(20, 'HAMBURG NOIR 30*30*60', 60, 'https://assets.zanimo.tn/produits/8023222128552.jpg', 2, 'Arbre à chat design avec niches confortables.', 72, 'Chat', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/8023222128552.jpg'),
(21, 'CALCIMAX OISEAUX 500 GR', 56, 'https://assets.zanimo.tn/produits/6192461403484.jpg', 3, 'Complément alimentaire calcique pour oiseaux en cage.', 46.2, 'Oiseau', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/6192461403484.jpg'),
(22, 'Mélange Graines Canaris 1kg', 58, 'https://assets.zanimo.tn/produits/6192461403484.jpg', 3, 'Mélange de graines variées sélectionnées pour la vitalité des canaris.', 8.5, 'Oiseau', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/6192461403484.jpg'),
(23, 'Bâtonnets de Miel Perruches', 45, 'https://assets.zanimo.tn/produits/6192461403484.jpg', 3, 'Friandise naturelle au miel à suspendre dans la cage.', 4.5, 'Oiseau', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/6192461403484.jpg'),
(24, 'Bloc Calcaire Oiseaux', 29, 'https://assets.zanimo.tn/produits/6192461403484.jpg', 3, 'Apport essentiel en minéraux pour la solidité des os et du bec.', 3, 'Oiseau', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/6192461403484.jpg'),
(25, 'Sable de Sol Oiseaux 5kg', 67, 'https://assets.zanimo.tn/produits/6192461403484.jpg', 3, 'Litière minérale naturelle pour fond de cage, facilite l\'entretien.', 9, 'Oiseau', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/6192461403484.jpg'),
(26, 'Perchoir Bois Naturel', 45, 'https://assets.zanimo.tn/produits/6192461403484.jpg', 3, 'Perchoir en bois naturel pour l\'exercice et le confort de l\'oiseau.', 6.5, 'Oiseau', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/6192461403484.jpg'),
(27, 'Baignoire Extérieure Cage', 71, 'https://assets.zanimo.tn/produits/6192461403484.jpg', 3, 'Baignoire en plastique à fixer sur la porte de la cage.', 10, 'Oiseau', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/6192461403484.jpg'),
(28, 'Balançoire Colorée', 54, 'https://assets.zanimo.tn/produits/6192461403484.jpg', 3, 'Jouet de stimulation pour oiseaux, fixation universelle.', 7.5, 'Oiseau', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/6192461403484.jpg'),
(29, 'Abreuvoir Tube 100ml', 22, 'https://assets.zanimo.tn/produits/6192461403484.jpg', 3, 'Distributeur d\'eau hygiénique à fixer sur les barreaux de cage.', 5, 'Oiseau', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/6192461403484.jpg'),
(30, 'Miroir avec Clochette', 72, 'https://assets.zanimo.tn/produits/6192461403484.jpg', 3, 'Jouet miroir avec clochette pour occuper l\'oiseau.', 4, 'Oiseau', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/6192461403484.jpg'),
(31, 'EPUISETTE 10 CM', 32, 'https://assets.zanimo.tn/produits/8023222002937.jpg', 4, 'Petite épuisette fine pour capturer les poissons sans les blesser.', 5.6, 'Poisson', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/8023222002937.jpg'),
(32, 'EPUISETTE 25 CM', 35, 'https://assets.zanimo.tn/produits/8023222002975.jpg', 4, 'Épuisette à long manche pour aquariums de grande taille.', 11.2, 'Poisson', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/8023222002975.jpg'),
(33, 'DIFFUSEUR SPHERIQUE LARGE', 31, 'https://assets.zanimo.tn/produits/8023222019744.jpg', 4, 'Diffuseur d\'air pour une oxygénation optimale de l\'aquarium.', 2.8, 'Poisson', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/8023222019744.jpg'),
(34, 'DIFFUSEUR SPHERIQUE SMALL', 19, 'https://assets.zanimo.tn/produits/8023222019720.jpg', 4, 'Petit diffuseur d\'air silencieux pour nano-aquariums.', 2, 'Poisson', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/8023222019720.jpg'),
(35, 'Flocons Poissons Tropicaux 250ml', 20, 'https://assets.zanimo.tn/produits/8023222019744.jpg', 4, 'Aliment de base complet pour tous poissons tropicaux.', 8.5, 'Poisson', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8023222019744.jpg'),
(36, 'Comprimés de Fond Algues', 57, 'https://assets.zanimo.tn/produits/8023222019720.jpg', 4, 'Alimentation spéciale fond pour poissons nettoyeurs.', 10, 'Poisson', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8023222019720.jpg'),
(37, 'Granulés Poissons Rouges 200ml', 24, 'https://assets.zanimo.tn/produits/8023222019744.jpg', 4, 'Aliment complet pour poissons rouges et poissons d\'eau froide.', 6, 'Poisson', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8023222019744.jpg'),
(38, 'Vers de Vase Lyophilisés 20g', 10, 'https://assets.zanimo.tn/produits/8023222019720.jpg', 4, 'Friandise naturelle riche en protéines pour stimuler l\'appétit.', 12.5, 'Poisson', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/8023222019720.jpg'),
(39, 'Thermomètre Digital Aquarium', 39, 'https://assets.zanimo.tn/produits/8023222002937.jpg', 4, 'Mesure précise de la température en temps réel.', 15, 'Poisson', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/8023222002937.jpg'),
(40, 'Décorations Gravier Coloré 1kg', 68, 'https://assets.zanimo.tn/produits/8023222002975.jpg', 4, 'Gravier lavé et coloré pour personnaliser le fond d\'aquarium.', 8, 'Poisson', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/8023222002975.jpg'),
(41, 'CAGE LAPIN NERO 3 100*50*45 BLEU', 34, 'https://assets.zanimo.tn/produits/5411388520915.jpg', 5, 'Cage spacieuse et robuste pour lapins avec bac de fond.', 331, 'Rongeur', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/5411388520915.jpg'),
(42, 'CAGE LAPIN NERO 3 100*50*45 NOIR', 72, 'https://assets.zanimo.tn/produits/5411388520946.jpg', 5, 'Cage lapin avec grille solide et porte d\'accès pratique.', 331, 'Rongeur', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/5411388520946.jpg'),
(43, 'CAGE LAPIN AMBIENTE NOIR 80*50*43', 99, 'https://assets.zanimo.tn/produits/5411388522308.jpg', 5, 'Cage compacte pour lapin nain ou cochon d\'inde.', 169, 'Rongeur', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/5411388522308.jpg'),
(44, 'Mélange Granulés Hamster 500g', 70, 'https://assets.zanimo.tn/produits/5411388520915.jpg', 5, 'Nutrition complète pour hamsters, évite le tri alimentaire.', 9.5, 'Rongeur', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/5411388520915.jpg'),
(45, 'Foin Naturel Rongeurs 500g', 68, 'https://assets.zanimo.tn/produits/5411388520946.jpg', 5, 'Foin de qualité supérieure indispensable aux lapins et cobayes.', 12, 'Rongeur', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/5411388520946.jpg'),
(46, 'Bâtonnets Fruits Rongeurs', 80, 'https://assets.zanimo.tn/produits/5411388522308.jpg', 5, 'Friandise aux fruits pour l\'instinct naturel de ronger.', 5.5, 'Rongeur', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/5411388522308.jpg'),
(47, 'Aliment Complet Lapin 1kg', 51, 'https://assets.zanimo.tn/produits/5411388520939.jpg', 5, 'Mélange de pellets équilibré pour lapins nains adultes.', 14, 'Rongeur', '2026-05-09 13:52:10', 'Nourriture', 'https://assets.zanimo.tn/produits/5411388520939.jpg'),
(48, 'Roue Silencieuse Hamster 20cm', 15, 'https://assets.zanimo.tn/produits/5411388520939.jpg', 5, 'Roue d\'exercice silencieuse pour hamsters et gerbilles.', 18, 'Rongeur', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/5411388520939.jpg'),
(49, 'Biberon Rongeur 500ml', 13, 'https://assets.zanimo.tn/produits/5411388520915.jpg', 5, 'Biberon anti-goutte en plastique résistant.', 9, 'Rongeur', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/5411388520915.jpg'),
(50, 'Tunnel Osier Naturel Rongeur', 90, 'https://assets.zanimo.tn/produits/5411388520946.jpg', 5, 'Cachette saine que le rongeur peut grignoter librement.', 11, 'Rongeur', '2026-05-09 13:52:10', 'Accessoire', 'https://assets.zanimo.tn/produits/5411388520946.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `commande`
--

CREATE TABLE `commande` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_creation` date NOT NULL,
  `statut` varchar(255) NOT NULL,
  `utilisateur_id` int DEFAULT NULL,
  `total` double DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `IDX_6EEAA67DFB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_6EEAA67DFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ligne_commande`
--

CREATE TABLE `ligne_commande` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quantite` int NOT NULL,
  `prix_unitaire` double NOT NULL,
  `commande_id` int NOT NULL,
  `produit_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3170B74B82EA2E54` (`commande_id`),
  KEY `IDX_3170B74BF347EFB` (`produit_id`),
  CONSTRAINT `FK_3170B74B82EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`),
  CONSTRAINT `FK_3170B74BF347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;