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
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
