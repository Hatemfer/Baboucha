-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 18 jan. 2025 à 12:29
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
-- Base de données : `ecom`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE `article` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `etat` enum('Neuf','Bon Etat','Usage') NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `DeletedAt` timestamp NULL DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id`, `title`, `description`, `image_path`, `etat`, `prix`, `createdAt`, `updatedAt`, `DeletedAt`, `userId`, `category_id`, `subcategory_id`) VALUES
(12, 'Chemise', 'description testttt', 'uploads/67888171c8b5b-images (1).jpeg', 'Bon Etat', 20.00, '2025-01-16 03:48:01', '2025-01-16 03:48:01', NULL, 4, 1, 6),
(13, 'capuche', 'description testttt 2', 'uploads/678882ba229ad-1702823657.jpeg', 'Neuf', 99.97, '2025-01-16 03:53:30', '2025-01-16 03:53:30', NULL, 4, 1, 5),
(14, 'chamise', 'testtt h,fgb j;h,gjhn', 'uploads/678888f20c554-téléchargement.jpeg', 'Usage', 18.00, '2025-01-16 04:20:02', '2025-01-16 04:20:02', NULL, 4, 1, 1),
(15, 'capuche 2', 'tkhe,fvbl', 'uploads/67888a2ba094e-510Yem077rL._AC_UY1000_.jpg', 'Bon Etat', 15.00, '2025-01-16 04:25:15', '2025-01-16 04:25:15', NULL, 4, 1, 4),
(16, 'hf,nb', 'j;hg,b', 'uploads/67888ab5bb6c4-images (1).jpeg', 'Neuf', 111.00, '2025-01-16 04:27:33', '2025-01-16 04:27:33', NULL, 4, 3, 13),
(17, 'vdvdvdv', 'dvvd', 'uploads/67888b4cd31ea-images (1).jpeg', 'Usage', 15.00, '2025-01-16 04:30:04', '2025-01-16 04:30:04', NULL, 4, 2, 8),
(18, 'kug;,', ';ug;kjh', 'uploads/67888be13ecbf-images (1).jpeg', 'Neuf', 15.00, '2025-01-16 04:32:33', '2025-01-16 04:32:33', NULL, 4, 1, 1),
(19, 'test', ';kj', 'uploads/67888c1c74acd-images (1).jpeg', 'Usage', 14.90, '2025-01-16 04:33:32', '2025-01-16 04:33:32', NULL, 4, 2, 11),
(20, 'dfgh', 'cvhjkl', 'uploads/67888cc52475d-images (1).jpeg', 'Neuf', 1.02, '2025-01-16 04:36:21', '2025-01-16 04:36:21', NULL, 4, 1, 6),
(21, 'test f', 'ugik', 'uploads/67888cdf40ae7-images (1).jpeg', 'Bon Etat', 14.96, '2025-01-16 04:36:47', '2025-01-16 04:36:47', NULL, 4, 2, 8);

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `name`) VALUES
(3, 'Enfant'),
(2, 'Femme'),
(1, 'Homme');

-- --------------------------------------------------------

--
-- Structure de la table `subcategorie`
--

CREATE TABLE `subcategorie` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `subcategorie`
--

INSERT INTO `subcategorie` (`id`, `name`, `category_id`) VALUES
(1, 'Jeans', 1),
(2, 'Chaussures', 1),
(3, 'Chemises', 1),
(4, 'Pantalons', 1),
(5, 'Vestes', 1),
(6, 'Costumes', 1),
(7, 'Accessoires', 1),
(8, 'Robes', 2),
(9, 'Jupes', 2),
(10, 'Blouses', 2),
(11, 'Accessoires', 2),
(12, 'Vêtements', 3),
(13, 'Chaussures', 3),
(14, 'Jouets', 3),
(15, 'Accessoires', 3);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(15) DEFAULT NULL,
  `role` enum('admin','member') DEFAULT 'member',
  `avatar` varchar(255) DEFAULT NULL,
  `notificationsActives` tinyint(1) DEFAULT 1,
  `dateInscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `DeletedAt` timestamp NULL DEFAULT NULL,
  `blocked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `prenom`, `nom`, `email`, `password`, `adresse`, `telephone`, `role`, `avatar`, `notificationsActives`, `dateInscription`, `DeletedAt`, `blocked`) VALUES
(3, 'sam', 'sam', 'sam@gmail.com', '$2y$10$I9M0p045xcaY0lvAFb0dQ.6Txvsy8hS3IgIJ8cx8G0fmycmUygOHG', 'La marsa', '20851689', 'member', NULL, 1, '2025-01-14 23:01:30', NULL, 0),
(4, 'Hatem', 'Ferjeni', 'hatem@gmail.com', '$2y$10$acY2lVfEW4IfNui3QK3fH.MwvwhHnCwYCn.Wf2QZPc/WFXxnWpmqq', '32 rue othman mousli La marsa', '51203835', 'member', NULL, 1, '2025-01-14 23:03:23', NULL, 0),
(12, 'in', 'Support', 'qa@gmail.com', '$2y$10$B0afwjxQAvnQ9Y6qJOWJ8uAdCcLhoIBc1qjnnXx3qMEwqbBoEcxBe', 'tunis', '24802937', 'admin', NULL, 1, '2025-01-18 11:10:08', NULL, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `subcategorie`
--
ALTER TABLE `subcategorie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `subcategorie`
--
ALTER TABLE `subcategorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `article_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categorie` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_ibfk_3` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategorie` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `subcategorie`
--
ALTER TABLE `subcategorie`
  ADD CONSTRAINT `subcategorie_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categorie` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
