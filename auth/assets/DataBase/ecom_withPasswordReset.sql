-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2025 at 12:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecom`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
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
-- Dumping data for table `article`
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
-- Table structure for table `categorie`
--

CREATE TABLE `categorie` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorie`
--

INSERT INTO `categorie` (`id`, `name`) VALUES
(3, 'Enfant'),
(2, 'Femme'),
(1, 'Homme');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `code`, `created_at`, `expiry`, `used`) VALUES
(1, 'houssembouallagui1@gmail.com', '292740', '2025-01-18 22:21:07', '2025-01-19 00:21:07', 0),
(2, 'houssembouallagui1@gmail.com', '731531', '2025-01-18 22:25:14', '2025-01-19 00:25:14', 0),
(3, 'houssembouallagui1@gmail.com', '891731', '2025-01-18 22:26:32', '2025-01-19 00:26:32', 0),
(4, 'houssembouallagui1@gmail.com', '623031', '2025-01-18 22:27:02', '2025-01-19 00:27:02', 0),
(5, 'houssembouallagui1@gmail.com', '436254', '2025-01-18 22:33:02', '2025-01-19 00:33:02', 0),
(6, 'houssembouallagui1@gmail.com', '520912', '2025-01-18 22:47:30', '2025-01-19 00:47:30', 0),
(7, 'houssembouallagui1@gmail.com', '065145', '2025-01-18 22:47:40', '2025-01-19 00:47:40', 0),
(8, 'houssembouallagui1@gmail.com', '028297', '2025-01-18 22:47:50', '2025-01-19 00:47:50', 0),
(9, 'houssembouallagui1@gmail.com', '401646', '2025-01-18 22:48:00', '2025-01-19 00:48:00', 0),
(10, 'houssembouallagui1@gmail.com', '427380', '2025-01-18 22:48:11', '2025-01-19 00:48:11', 0),
(11, 'houssembouallagui1@gmail.com', '086628', '2025-01-18 22:48:21', '2025-01-19 00:48:21', 0),
(12, 'houssembouallagui1@gmail.com', '576485', '2025-01-18 22:48:31', '2025-01-19 00:48:31', 0),
(13, 'houssembouallagui1@gmail.com', '560098', '2025-01-18 22:49:06', '2025-01-19 00:49:06', 0),
(14, 'houssembouallagui1@gmail.com', '792786', '2025-01-18 22:49:16', '2025-01-19 00:49:16', 0),
(15, 'houssembouallagui1@gmail.com', '528428', '2025-01-18 22:49:26', '2025-01-19 00:49:26', 0),
(16, 'houssembouallagui1@gmail.com', '925872', '2025-01-18 22:49:51', '2025-01-19 00:49:51', 0),
(17, 'houssembouallagui1@gmail.com', '109384', '2025-01-18 22:50:06', '2025-01-19 00:50:06', 0),
(18, 'houssembouallagui1@gmail.com', '795393', '2025-01-18 22:50:56', '2025-01-19 00:50:56', 0),
(19, 'houssembouallagui1@gmail.com', '954060', '2025-01-18 23:10:12', '2025-01-19 01:10:12', 0),
(20, 'houssembouallagui1@gmail.com', '747300', '2025-01-19 10:50:29', '2025-01-19 12:50:29', 0),
(21, 'houssembouallagui1@gmail.com', '322600', '2025-01-19 10:50:50', '2025-01-19 12:50:50', 0),
(22, 'houssembouallagui1@gmail.com', '895681', '2025-01-19 10:50:57', '2025-01-19 12:50:57', 0),
(23, 'houssembouallagui1@gmail.com', '159271', '2025-01-19 10:51:58', '2025-01-19 12:51:58', 0),
(24, 'houssembouallagui1@gmail.com', '613609', '2025-01-19 10:53:43', '2025-01-19 12:53:43', 0),
(25, 'houssembouallagui1@gmail.com', '212037', '2025-01-19 10:54:02', '2025-01-19 12:54:02', 0),
(26, 'houssembouallagui1@gmail.com', '247329', '2025-01-19 10:54:43', '2025-01-19 12:54:43', 0),
(27, 'houssembouallagui1@gmail.com', '908292', '2025-01-19 10:55:04', '2025-01-19 12:55:04', 0),
(28, 'houssembouallagui1@gmail.com', '958895', '2025-01-19 10:57:54', '2025-01-19 12:57:54', 0),
(29, 'houssembouallagui1@gmail.com', '652447', '2025-01-19 11:00:39', '2025-01-19 13:00:39', 0),
(30, 'houssembouallagui1@gmail.com', '632187', '2025-01-19 11:06:37', '2025-01-19 13:06:37', 0),
(31, 'houssembouallagui1@gmail.com', '835521', '2025-01-19 11:06:51', '2025-01-19 13:06:51', 1),
(32, 'houssembouallagui1@gmail.com', '899776', '2025-01-19 11:16:43', '2025-01-19 13:16:43', 1),
(33, 'houssembouallagui1@gmail.com', '749567', '2025-01-19 11:23:03', '2025-01-19 13:23:03', 0),
(34, 'houssembouallagui1@gmail.com', '297605', '2025-01-19 11:23:59', '2025-01-19 13:23:59', 1);

-- --------------------------------------------------------

--
-- Table structure for table `subcategorie`
--

CREATE TABLE `subcategorie` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subcategorie`
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
-- Table structure for table `user`
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
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `prenom`, `nom`, `email`, `password`, `adresse`, `telephone`, `role`, `avatar`, `notificationsActives`, `dateInscription`, `DeletedAt`, `blocked`) VALUES
(3, 'sam', 'sam', 'sam@sam.com', '$2y$10$BdbDriEZK6s6EAPI2xkilukylPZnYnc3npKGRizvgqNivhByN07PW', 'La marsa', '20851689', 'member', NULL, 1, '2025-01-14 23:01:30', NULL, 0),
(4, 'Hatem', 'Ferjeni', 'hatem@gmail.com', '$2y$10$acY2lVfEW4IfNui3QK3fH.MwvwhHnCwYCn.Wf2QZPc/WFXxnWpmqq', '32 rue othman mousli La marsa', '51203835', 'member', NULL, 1, '2025-01-14 23:03:23', NULL, 0),
(12, 'in', 'Support', 'qa@gmail.com', '$2y$10$B0afwjxQAvnQ9Y6qJOWJ8uAdCcLhoIBc1qjnnXx3qMEwqbBoEcxBe', 'tunis', '24802937', 'admin', NULL, 1, '2025-01-18 11:10:08', NULL, 0),
(15, 'testt', 'test', 'test@gmail.com', '$2y$10$jydzo2gMgiixF1qwPCoqOO44VvJsTagpms5rCs15kREQXN6jX.6B6', 'Tunis, Tunisie', '55555555', 'member', NULL, 1, '2025-01-18 19:23:50', NULL, 0),
(16, 'Bouallagui', 'Houssem', 'houssembouallagui1@gmail.com', '$2y$10$sDor5sC/9PKvDyQuA0seP.FUoEWlaaFy18yFWNggGKcg.NhmEzHbC', 'Kasserine, sbeitla', '29223440', 'member', 'uploads/Screenshot 2025-01-18 224858.png', 1, '2025-01-18 22:20:32', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Indexes for table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subcategorie`
--
ALTER TABLE `subcategorie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `article`
--
ALTER TABLE `article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `subcategorie`
--
ALTER TABLE `subcategorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `article_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categorie` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_ibfk_3` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategorie` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subcategorie`
--
ALTER TABLE `subcategorie`
  ADD CONSTRAINT `subcategorie_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categorie` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
