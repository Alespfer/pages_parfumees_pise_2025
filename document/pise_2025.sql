-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 14-07-2025 a las 11:12:47
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ecommerce`
--

-- --------------------------------------------------------

DROP DATABASE IF EXISTS ecommerce;
CREATE DATABASE ecommerce
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;
USE ecommerce;

--
-- Estructura de tabla para la tabla `administrateur`
--

DROP TABLE IF EXISTS `administrateur`;
CREATE TABLE IF NOT EXISTS `administrateur` (
  `id_admin` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administrateur`
--

INSERT INTO `administrateur` (`id_admin`, `email`, `password_hash`, `role`) VALUES
(1, 'admin@pages-parfumees.local', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'superadmin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adresse`
--

DROP TABLE IF EXISTS `adresse`;
CREATE TABLE IF NOT EXISTS `adresse` (
  `id_adresse` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `rue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_postal` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ville` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pays` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `est_defaut` tinyint(1) DEFAULT '0',
  `id_client` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_adresse`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `adresse`
--

INSERT INTO `adresse` (`id_adresse`, `rue`, `code_postal`, `ville`, `pays`, `est_defaut`, `id_client`) VALUES
(1, '18 Rue de la Verrerie', '75004', 'Paris', 'France', 1, 1),
(2, '25 Quai Richelieu', '33000', 'Bordeaux', 'France', 0, 1),
(3, '5 Place Bellecour', '69002', 'Lyon', 'France', 1, 2),
(4, '14 Rue Saint-Ferréol', '13001', 'Marseille', 'France', 1, 3),
(5, '36 Rue du Maréchal Joffre', '06000', 'Nice', 'France', 0, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id_log` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_action` datetime DEFAULT CURRENT_TIMESTAMP,
  `type_action` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_concernee` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enregistrement_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `champ_modifie` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ancienne_valeur` text COLLATE utf8mb4_unicode_ci,
  `nouvelle_valeur` text COLLATE utf8mb4_unicode_ci,
  `id_admin` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_log`),
  KEY `id_admin` (`id_admin`),
  KEY `idx_audit_lookup` (`table_concernee`,`enregistrement_id`),
  KEY `idx_audit_date` (`date_action`)
) ENGINE=InnoDB AUTO_INCREMENT=491 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `audit_logs`
--

INSERT INTO `audit_logs` (`id_log`, `date_action`, `type_action`, `table_concernee`, `enregistrement_id`, `champ_modifie`, `ancienne_valeur`, `nouvelle_valeur`, `id_admin`) VALUES
(1, '2025-07-14 13:07:47', 'CREATE', 'produit', '1', NULL, NULL, 'Produit de type livre', NULL),
(2, '2025-07-14 13:07:47', 'CREATE', 'produit', '2', NULL, NULL, 'Produit de type livre', NULL),
(3, '2025-07-14 13:07:47', 'CREATE', 'produit', '4', NULL, NULL, 'Produit de type livre', NULL),
(4, '2025-07-14 13:07:47', 'CREATE', 'produit', '5', NULL, NULL, 'Produit de type livre', NULL),
(5, '2025-07-14 13:07:47', 'CREATE', 'produit', '6', NULL, NULL, 'Produit de type livre', NULL),
(6, '2025-07-14 13:07:47', 'CREATE', 'produit', '7', NULL, NULL, 'Produit de type livre', NULL),
(7, '2025-07-14 13:07:47', 'CREATE', 'produit', '8', NULL, NULL, 'Produit de type livre', NULL),
(8, '2025-07-14 13:07:47', 'CREATE', 'produit', '9', NULL, NULL, 'Produit de type livre', NULL),
(9, '2025-07-14 13:07:47', 'CREATE', 'produit', '10', NULL, NULL, 'Produit de type livre', NULL),
(10, '2025-07-14 13:07:47', 'CREATE', 'produit', '11', NULL, NULL, 'Produit de type livre', NULL),
(11, '2025-07-14 13:07:47', 'CREATE', 'produit', '12', NULL, NULL, 'Produit de type livre', NULL),
(12, '2025-07-14 13:07:47', 'CREATE', 'produit', '13', NULL, NULL, 'Produit de type livre', NULL),
(13, '2025-07-14 13:07:47', 'CREATE', 'produit', '14', NULL, NULL, 'Produit de type livre', NULL),
(14, '2025-07-14 13:07:47', 'CREATE', 'produit', '15', NULL, NULL, 'Produit de type livre', NULL),
(15, '2025-07-14 13:07:47', 'CREATE', 'produit', '16', NULL, NULL, 'Produit de type livre', NULL),
(16, '2025-07-14 13:07:47', 'CREATE', 'produit', '17', NULL, NULL, 'Produit de type livre', NULL),
(17, '2025-07-14 13:07:47', 'CREATE', 'produit', '18', NULL, NULL, 'Produit de type livre', NULL),
(18, '2025-07-14 13:07:47', 'CREATE', 'produit', '19', NULL, NULL, 'Produit de type livre', NULL),
(19, '2025-07-14 13:07:47', 'CREATE', 'produit', '20', NULL, NULL, 'Produit de type livre', NULL),
(20, '2025-07-14 13:07:47', 'CREATE', 'produit', '21', NULL, NULL, 'Produit de type livre', NULL),
(21, '2025-07-14 13:07:47', 'CREATE', 'produit', '22', NULL, NULL, 'Produit de type livre', NULL),
(22, '2025-07-14 13:07:47', 'CREATE', 'produit', '23', NULL, NULL, 'Produit de type livre', NULL),
(23, '2025-07-14 13:07:47', 'CREATE', 'produit', '24', NULL, NULL, 'Produit de type livre', NULL),
(24, '2025-07-14 13:07:47', 'CREATE', 'produit', '25', NULL, NULL, 'Produit de type livre', NULL),
(25, '2025-07-14 13:07:47', 'CREATE', 'produit', '26', NULL, NULL, 'Produit de type livre', NULL),
(26, '2025-07-14 13:07:47', 'CREATE', 'produit', '27', NULL, NULL, 'Produit de type livre', NULL),
(27, '2025-07-14 13:07:47', 'CREATE', 'produit', '28', NULL, NULL, 'Produit de type livre', NULL),
(28, '2025-07-14 13:07:47', 'CREATE', 'produit', '29', NULL, NULL, 'Produit de type livre', NULL),
(29, '2025-07-14 13:07:47', 'CREATE', 'produit', '30', NULL, NULL, 'Produit de type livre', NULL),
(30, '2025-07-14 13:07:47', 'CREATE', 'produit', '31', NULL, NULL, 'Produit de type bougie', NULL),
(31, '2025-07-14 13:07:47', 'CREATE', 'produit', '32', NULL, NULL, 'Produit de type bougie', NULL),
(32, '2025-07-14 13:07:47', 'CREATE', 'produit', '33', NULL, NULL, 'Produit de type bougie', NULL),
(33, '2025-07-14 13:07:47', 'CREATE', 'produit', '34', NULL, NULL, 'Produit de type bougie', NULL),
(34, '2025-07-14 13:07:47', 'CREATE', 'produit', '35', NULL, NULL, 'Produit de type bougie', NULL),
(35, '2025-07-14 13:07:47', 'CREATE', 'produit', '36', NULL, NULL, 'Produit de type bougie', NULL),
(36, '2025-07-14 13:07:47', 'CREATE', 'produit', '37', NULL, NULL, 'Produit de type bougie', NULL),
(37, '2025-07-14 13:07:47', 'CREATE', 'produit', '38', NULL, NULL, 'Produit de type bougie', NULL),
(38, '2025-07-14 13:07:47', 'CREATE', 'produit', '39', NULL, NULL, 'Produit de type bougie', NULL),
(39, '2025-07-14 13:07:47', 'CREATE', 'produit', '40', NULL, NULL, 'Produit de type bougie', NULL),
(40, '2025-07-14 13:07:47', 'CREATE', 'produit', '41', NULL, NULL, 'Produit de type bougie', NULL),
(41, '2025-07-14 13:07:47', 'CREATE', 'produit', '42', NULL, NULL, 'Produit de type bougie', NULL),
(42, '2025-07-14 13:07:47', 'CREATE', 'produit', '43', NULL, NULL, 'Produit de type bougie', NULL),
(43, '2025-07-14 13:07:47', 'CREATE', 'produit', '44', NULL, NULL, 'Produit de type bougie', NULL),
(44, '2025-07-14 13:07:47', 'CREATE', 'produit', '45', NULL, NULL, 'Produit de type bougie', NULL),
(45, '2025-07-14 13:07:47', 'CREATE', 'produit', '46', NULL, NULL, 'Produit de type livre', NULL),
(46, '2025-07-14 13:07:47', 'CREATE', 'produit', '47', NULL, NULL, 'Produit de type livre', NULL),
(47, '2025-07-14 13:07:47', 'CREATE', 'produit', '48', NULL, NULL, 'Produit de type livre', NULL),
(48, '2025-07-14 13:07:47', 'CREATE', 'produit', '49', NULL, NULL, 'Produit de type livre', NULL),
(49, '2025-07-14 13:07:47', 'CREATE', 'produit', '50', NULL, NULL, 'Produit de type livre', NULL),
(50, '2025-07-14 13:07:47', 'CREATE', 'produit', '51', NULL, NULL, 'Produit de type livre', NULL),
(51, '2025-07-14 13:07:47', 'CREATE', 'produit', '53', NULL, NULL, 'Produit de type livre', NULL),
(52, '2025-07-14 13:07:47', 'CREATE', 'produit', '54', NULL, NULL, 'Produit de type livre', NULL),
(53, '2025-07-14 13:07:47', 'CREATE', 'produit', '55', NULL, NULL, 'Produit de type livre', NULL),
(54, '2025-07-14 13:07:47', 'CREATE', 'produit', '56', NULL, NULL, 'Produit de type livre', NULL),
(55, '2025-07-14 13:07:47', 'CREATE', 'produit', '57', NULL, NULL, 'Produit de type livre', NULL),
(56, '2025-07-14 13:07:47', 'CREATE', 'produit', '58', NULL, NULL, 'Produit de type livre', NULL),
(57, '2025-07-14 13:07:47', 'CREATE', 'produit', '59', NULL, NULL, 'Produit de type livre', NULL),
(58, '2025-07-14 13:07:47', 'CREATE', 'produit', '60', NULL, NULL, 'Produit de type livre', NULL),
(59, '2025-07-14 13:07:47', 'CREATE', 'produit', '61', NULL, NULL, 'Produit de type livre', NULL),
(60, '2025-07-14 13:07:47', 'CREATE', 'produit', '62', NULL, NULL, 'Produit de type livre', NULL),
(61, '2025-07-14 13:07:47', 'CREATE', 'produit', '63', NULL, NULL, 'Produit de type livre', NULL),
(62, '2025-07-14 13:07:47', 'CREATE', 'produit', '64', NULL, NULL, 'Produit de type livre', NULL),
(63, '2025-07-14 13:07:47', 'CREATE', 'produit', '65', NULL, NULL, 'Produit de type bougie', NULL),
(64, '2025-07-14 13:07:47', 'CREATE', 'produit', '66', NULL, NULL, 'Produit de type bougie', NULL),
(65, '2025-07-14 13:07:47', 'CREATE', 'produit', '67', NULL, NULL, 'Produit de type bougie', NULL),
(66, '2025-07-14 13:07:47', 'CREATE', 'produit', '68', NULL, NULL, 'Produit de type bougie', NULL),
(67, '2025-07-14 13:07:47', 'CREATE', 'produit', '69', NULL, NULL, 'Produit de type bougie', NULL),
(68, '2025-07-14 13:07:47', 'CREATE', 'produit', '70', NULL, NULL, 'Produit de type bougie', NULL),
(69, '2025-07-14 13:07:47', 'CREATE', 'produit', '71', NULL, NULL, 'Produit de type livre', NULL),
(70, '2025-07-14 13:07:47', 'CREATE', 'produit', '72', NULL, NULL, 'Produit de type livre', NULL),
(71, '2025-07-14 13:07:47', 'CREATE', 'produit', '73', NULL, NULL, 'Produit de type coffret', NULL),
(72, '2025-07-14 13:07:47', 'CREATE', 'produit', '74', NULL, NULL, 'Produit de type coffret', NULL),
(73, '2025-07-14 13:07:47', 'CREATE', 'produit', '75', NULL, NULL, 'Produit de type coffret', NULL),
(74, '2025-07-14 13:07:47', 'CREATE', 'produit', '76', NULL, NULL, 'Produit de type coffret', NULL),
(75, '2025-07-14 13:07:47', 'CREATE', 'produit', '77', NULL, NULL, 'Produit de type coffret', NULL),
(76, '2025-07-14 13:07:47', 'CREATE', 'produit', '78', NULL, NULL, 'Produit de type coffret', NULL),
(77, '2025-07-14 13:07:47', 'CREATE', 'produit', '79', NULL, NULL, 'Produit de type livre', NULL),
(78, '2025-07-14 13:07:47', 'CREATE', 'produit', '80', NULL, NULL, 'Produit de type livre', NULL),
(79, '2025-07-14 13:07:47', 'CREATE', 'produit', '81', NULL, NULL, 'Produit de type coffret', NULL),
(80, '2025-07-14 13:07:47', 'CREATE', 'produit', '82', NULL, NULL, 'Produit de type coffret', NULL),
(81, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '1-1', NULL, NULL, 'Livre #1 lié à auteur #1', NULL),
(82, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '2-1', NULL, NULL, 'Livre #2 lié à auteur #1', NULL),
(83, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '22-2', NULL, NULL, 'Livre #22 lié à auteur #2', NULL),
(84, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '27-2', NULL, NULL, 'Livre #27 lié à auteur #2', NULL),
(85, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '4-3', NULL, NULL, 'Livre #4 lié à auteur #3', NULL),
(86, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '5-3', NULL, NULL, 'Livre #5 lié à auteur #3', NULL),
(87, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '6-4', NULL, NULL, 'Livre #6 lié à auteur #4', NULL),
(88, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '7-5', NULL, NULL, 'Livre #7 lié à auteur #5', NULL),
(89, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '18-5', NULL, NULL, 'Livre #18 lié à auteur #5', NULL),
(90, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '8-6', NULL, NULL, 'Livre #8 lié à auteur #6', NULL),
(91, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '9-7', NULL, NULL, 'Livre #9 lié à auteur #7', NULL),
(92, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '20-7', NULL, NULL, 'Livre #20 lié à auteur #7', NULL),
(93, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '21-7', NULL, NULL, 'Livre #21 lié à auteur #7', NULL),
(94, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '26-7', NULL, NULL, 'Livre #26 lié à auteur #7', NULL),
(95, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '10-8', NULL, NULL, 'Livre #10 lié à auteur #8', NULL),
(96, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '11-9', NULL, NULL, 'Livre #11 lié à auteur #9', NULL),
(97, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '12-9', NULL, NULL, 'Livre #12 lié à auteur #9', NULL),
(98, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '13-10', NULL, NULL, 'Livre #13 lié à auteur #10', NULL),
(99, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '19-10', NULL, NULL, 'Livre #19 lié à auteur #10', NULL),
(100, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '16-11', NULL, NULL, 'Livre #16 lié à auteur #11', NULL),
(101, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '17-12', NULL, NULL, 'Livre #17 lié à auteur #12', NULL),
(102, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '23-13', NULL, NULL, 'Livre #23 lié à auteur #13', NULL),
(103, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '15-14', NULL, NULL, 'Livre #15 lié à auteur #14', NULL),
(104, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '28-15', NULL, NULL, 'Livre #28 lié à auteur #15', NULL),
(105, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '29-16', NULL, NULL, 'Livre #29 lié à auteur #16', NULL),
(106, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '30-17', NULL, NULL, 'Livre #30 lié à auteur #17', NULL),
(107, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '24-18', NULL, NULL, 'Livre #24 lié à auteur #18', NULL),
(108, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '25-19', NULL, NULL, 'Livre #25 lié à auteur #19', NULL),
(109, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '14-20', NULL, NULL, 'Livre #14 lié à auteur #20', NULL),
(110, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '46-21', NULL, NULL, 'Livre #46 lié à auteur #21', NULL),
(111, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '47-22', NULL, NULL, 'Livre #47 lié à auteur #22', NULL),
(112, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '48-23', NULL, NULL, 'Livre #48 lié à auteur #23', NULL),
(113, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '49-24', NULL, NULL, 'Livre #49 lié à auteur #24', NULL),
(114, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '50-25', NULL, NULL, 'Livre #50 lié à auteur #25', NULL),
(115, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '51-26', NULL, NULL, 'Livre #51 lié à auteur #26', NULL),
(116, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '53-28', NULL, NULL, 'Livre #53 lié à auteur #28', NULL),
(117, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '54-29', NULL, NULL, 'Livre #54 lié à auteur #29', NULL),
(118, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '55-30', NULL, NULL, 'Livre #55 lié à auteur #30', NULL),
(119, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '56-31', NULL, NULL, 'Livre #56 lié à auteur #31', NULL),
(120, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '57-32', NULL, NULL, 'Livre #57 lié à auteur #32', NULL),
(121, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '58-33', NULL, NULL, 'Livre #58 lié à auteur #33', NULL),
(122, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '59-34', NULL, NULL, 'Livre #59 lié à auteur #34', NULL),
(123, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '60-35', NULL, NULL, 'Livre #60 lié à auteur #35', NULL),
(124, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '61-36', NULL, NULL, 'Livre #61 lié à auteur #36', NULL),
(125, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '62-37', NULL, NULL, 'Livre #62 lié à auteur #37', NULL),
(126, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '63-38', NULL, NULL, 'Livre #63 lié à auteur #38', NULL),
(127, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '64-39', NULL, NULL, 'Livre #64 lié à auteur #39', NULL),
(128, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '71-40', NULL, NULL, 'Livre #71 lié à auteur #40', NULL),
(129, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '72-41', NULL, NULL, 'Livre #72 lié à auteur #41', NULL),
(130, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '79-42', NULL, NULL, 'Livre #79 lié à auteur #42', NULL),
(131, '2025-07-14 13:07:47', 'LINK', 'livre_auteur', '80-43', NULL, NULL, 'Livre #80 lié à auteur #43', NULL),
(132, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '1-1', NULL, NULL, 'Livre #1 lié à genre #1', NULL),
(133, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '1-6', NULL, NULL, 'Livre #1 lié à genre #6', NULL),
(134, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '8-1', NULL, NULL, 'Livre #8 lié à genre #1', NULL),
(135, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '15-1', NULL, NULL, 'Livre #15 lié à genre #1', NULL),
(136, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '15-6', NULL, NULL, 'Livre #15 lié à genre #6', NULL),
(137, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '17-1', NULL, NULL, 'Livre #17 lié à genre #1', NULL),
(138, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '17-9', NULL, NULL, 'Livre #17 lié à genre #9', NULL),
(139, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '22-1', NULL, NULL, 'Livre #22 lié à genre #1', NULL),
(140, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '23-1', NULL, NULL, 'Livre #23 lié à genre #1', NULL),
(141, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '23-9', NULL, NULL, 'Livre #23 lié à genre #9', NULL),
(142, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '24-1', NULL, NULL, 'Livre #24 lié à genre #1', NULL),
(143, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '24-9', NULL, NULL, 'Livre #24 lié à genre #9', NULL),
(144, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '25-1', NULL, NULL, 'Livre #25 lié à genre #1', NULL),
(145, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '25-9', NULL, NULL, 'Livre #25 lié à genre #9', NULL),
(146, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '27-1', NULL, NULL, 'Livre #27 lié à genre #1', NULL),
(147, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '28-1', NULL, NULL, 'Livre #28 lié à genre #1', NULL),
(148, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '28-6', NULL, NULL, 'Livre #28 lié à genre #6', NULL),
(149, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '29-1', NULL, NULL, 'Livre #29 lié à genre #1', NULL),
(150, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '4-2', NULL, NULL, 'Livre #4 lié à genre #2', NULL),
(151, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '5-2', NULL, NULL, 'Livre #5 lié à genre #2', NULL),
(152, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '8-2', NULL, NULL, 'Livre #8 lié à genre #2', NULL),
(153, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '16-2', NULL, NULL, 'Livre #16 lié à genre #2', NULL),
(154, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '16-6', NULL, NULL, 'Livre #16 lié à genre #6', NULL),
(155, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '30-2', NULL, NULL, 'Livre #30 lié à genre #2', NULL),
(156, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '6-3', NULL, NULL, 'Livre #6 lié à genre #3', NULL),
(157, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '21-3', NULL, NULL, 'Livre #21 lié à genre #3', NULL),
(158, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '79-3', NULL, NULL, 'Livre #79 lié à genre #3', NULL),
(159, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '2-4', NULL, NULL, 'Livre #2 lié à genre #4', NULL),
(160, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '2-6', NULL, NULL, 'Livre #2 lié à genre #6', NULL),
(161, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '7-4', NULL, NULL, 'Livre #7 lié à genre #4', NULL),
(162, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '7-5', NULL, NULL, 'Livre #7 lié à genre #5', NULL),
(163, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '10-4', NULL, NULL, 'Livre #10 lié à genre #4', NULL),
(164, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '10-8', NULL, NULL, 'Livre #10 lié à genre #8', NULL),
(165, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '11-4', NULL, NULL, 'Livre #11 lié à genre #4', NULL),
(166, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '12-4', NULL, NULL, 'Livre #12 lié à genre #4', NULL),
(167, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '12-9', NULL, NULL, 'Livre #12 lié à genre #9', NULL),
(168, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '18-4', NULL, NULL, 'Livre #18 lié à genre #4', NULL),
(169, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '18-5', NULL, NULL, 'Livre #18 lié à genre #5', NULL),
(170, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '80-4', NULL, NULL, 'Livre #80 lié à genre #4', NULL),
(171, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '80-10', NULL, NULL, 'Livre #80 lié à genre #10', NULL),
(172, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '46-5', NULL, NULL, 'Livre #46 lié à genre #5', NULL),
(173, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '47-5', NULL, NULL, 'Livre #47 lié à genre #5', NULL),
(174, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '48-5', NULL, NULL, 'Livre #48 lié à genre #5', NULL),
(175, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '53-5', NULL, NULL, 'Livre #53 lié à genre #5', NULL),
(176, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '54-5', NULL, NULL, 'Livre #54 lié à genre #5', NULL),
(177, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '55-5', NULL, NULL, 'Livre #55 lié à genre #5', NULL),
(178, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '56-5', NULL, NULL, 'Livre #56 lié à genre #5', NULL),
(179, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '57-5', NULL, NULL, 'Livre #57 lié à genre #5', NULL),
(180, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '58-5', NULL, NULL, 'Livre #58 lié à genre #5', NULL),
(181, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '13-6', NULL, NULL, 'Livre #13 lié à genre #6', NULL),
(182, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '13-9', NULL, NULL, 'Livre #13 lié à genre #9', NULL),
(183, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '19-6', NULL, NULL, 'Livre #19 lié à genre #6', NULL),
(184, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '19-9', NULL, NULL, 'Livre #19 lié à genre #9', NULL),
(185, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '59-6', NULL, NULL, 'Livre #59 lié à genre #6', NULL),
(186, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '61-6', NULL, NULL, 'Livre #61 lié à genre #6', NULL),
(187, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '72-6', NULL, NULL, 'Livre #72 lié à genre #6', NULL),
(188, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '72-11', NULL, NULL, 'Livre #72 lié à genre #11', NULL),
(189, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '9-7', NULL, NULL, 'Livre #9 lié à genre #7', NULL),
(190, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '14-7', NULL, NULL, 'Livre #14 lié à genre #7', NULL),
(191, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '14-10', NULL, NULL, 'Livre #14 lié à genre #10', NULL),
(192, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '20-7', NULL, NULL, 'Livre #20 lié à genre #7', NULL),
(193, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '21-7', NULL, NULL, 'Livre #21 lié à genre #7', NULL),
(194, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '26-7', NULL, NULL, 'Livre #26 lié à genre #7', NULL),
(195, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '49-8', NULL, NULL, 'Livre #49 lié à genre #8', NULL),
(196, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '50-8', NULL, NULL, 'Livre #50 lié à genre #8', NULL),
(197, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '51-8', NULL, NULL, 'Livre #51 lié à genre #8', NULL),
(198, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '60-9', NULL, NULL, 'Livre #60 lié à genre #9', NULL),
(199, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '62-10', NULL, NULL, 'Livre #62 lié à genre #10', NULL),
(200, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '63-10', NULL, NULL, 'Livre #63 lié à genre #10', NULL),
(201, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '64-10', NULL, NULL, 'Livre #64 lié à genre #10', NULL),
(202, '2025-07-14 13:07:47', 'LINK', 'livre_genre', '71-10', NULL, NULL, 'Livre #71 lié à genre #10', NULL),
(203, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '1-1', NULL, NULL, 'Produit #1 lié à tag #1', NULL),
(204, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '1-12', NULL, NULL, 'Produit #1 lié à tag #12', NULL),
(205, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '2-1', NULL, NULL, 'Produit #2 lié à tag #1', NULL),
(206, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '2-9', NULL, NULL, 'Produit #2 lié à tag #9', NULL),
(207, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '2-17', NULL, NULL, 'Produit #2 lié à tag #17', NULL),
(208, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '4-3', NULL, NULL, 'Produit #4 lié à tag #3', NULL),
(209, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '4-12', NULL, NULL, 'Produit #4 lié à tag #12', NULL),
(210, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '4-17', NULL, NULL, 'Produit #4 lié à tag #17', NULL),
(211, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '5-3', NULL, NULL, 'Produit #5 lié à tag #3', NULL),
(212, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '5-2', NULL, NULL, 'Produit #5 lié à tag #2', NULL),
(213, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '5-17', NULL, NULL, 'Produit #5 lié à tag #17', NULL),
(214, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '6-2', NULL, NULL, 'Produit #6 lié à tag #2', NULL),
(215, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '6-9', NULL, NULL, 'Produit #6 lié à tag #9', NULL),
(216, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '6-23', NULL, NULL, 'Produit #6 lié à tag #23', NULL),
(217, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '7-10', NULL, NULL, 'Produit #7 lié à tag #10', NULL),
(218, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '7-5', NULL, NULL, 'Produit #7 lié à tag #5', NULL),
(219, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '7-19', NULL, NULL, 'Produit #7 lié à tag #19', NULL),
(220, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '7-23', NULL, NULL, 'Produit #7 lié à tag #23', NULL),
(221, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '7-20', NULL, NULL, 'Produit #7 lié à tag #20', NULL),
(222, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '8-6', NULL, NULL, 'Produit #8 lié à tag #6', NULL),
(223, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '8-10', NULL, NULL, 'Produit #8 lié à tag #10', NULL),
(224, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '8-16', NULL, NULL, 'Produit #8 lié à tag #16', NULL),
(225, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '9-11', NULL, NULL, 'Produit #9 lié à tag #11', NULL),
(226, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '9-5', NULL, NULL, 'Produit #9 lié à tag #5', NULL),
(227, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '9-19', NULL, NULL, 'Produit #9 lié à tag #19', NULL),
(228, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '9-23', NULL, NULL, 'Produit #9 lié à tag #23', NULL),
(229, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '10-7', NULL, NULL, 'Produit #10 lié à tag #7', NULL),
(230, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '10-13', NULL, NULL, 'Produit #10 lié à tag #13', NULL),
(231, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '10-20', NULL, NULL, 'Produit #10 lié à tag #20', NULL),
(232, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '10-27', NULL, NULL, 'Produit #10 lié à tag #27', NULL),
(233, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '11-7', NULL, NULL, 'Produit #11 lié à tag #7', NULL),
(234, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '11-9', NULL, NULL, 'Produit #11 lié à tag #9', NULL),
(235, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '11-19', NULL, NULL, 'Produit #11 lié à tag #19', NULL),
(236, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '11-20', NULL, NULL, 'Produit #11 lié à tag #20', NULL),
(237, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '12-7', NULL, NULL, 'Produit #12 lié à tag #7', NULL),
(238, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '12-9', NULL, NULL, 'Produit #12 lié à tag #9', NULL),
(239, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '12-19', NULL, NULL, 'Produit #12 lié à tag #19', NULL),
(240, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '12-20', NULL, NULL, 'Produit #12 lié à tag #20', NULL),
(241, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '13-1', NULL, NULL, 'Produit #13 lié à tag #1', NULL),
(242, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '13-9', NULL, NULL, 'Produit #13 lié à tag #9', NULL),
(243, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '13-19', NULL, NULL, 'Produit #13 lié à tag #19', NULL),
(244, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '14-11', NULL, NULL, 'Produit #14 lié à tag #11', NULL),
(245, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '14-5', NULL, NULL, 'Produit #14 lié à tag #5', NULL),
(246, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '14-23', NULL, NULL, 'Produit #14 lié à tag #23', NULL),
(247, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '15-1', NULL, NULL, 'Produit #15 lié à tag #1', NULL),
(248, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '15-6', NULL, NULL, 'Produit #15 lié à tag #6', NULL),
(249, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '15-16', NULL, NULL, 'Produit #15 lié à tag #16', NULL),
(250, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '16-2', NULL, NULL, 'Produit #16 lié à tag #2', NULL),
(251, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '16-1', NULL, NULL, 'Produit #16 lié à tag #1', NULL),
(252, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '16-17', NULL, NULL, 'Produit #16 lié à tag #17', NULL),
(253, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '16-27', NULL, NULL, 'Produit #16 lié à tag #27', NULL),
(254, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '17-6', NULL, NULL, 'Produit #17 lié à tag #6', NULL),
(255, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '17-10', NULL, NULL, 'Produit #17 lié à tag #10', NULL),
(256, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '17-25', NULL, NULL, 'Produit #17 lié à tag #25', NULL),
(257, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '18-10', NULL, NULL, 'Produit #18 lié à tag #10', NULL),
(258, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '18-5', NULL, NULL, 'Produit #18 lié à tag #5', NULL),
(259, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '18-19', NULL, NULL, 'Produit #18 lié à tag #19', NULL),
(260, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '18-23', NULL, NULL, 'Produit #18 lié à tag #23', NULL),
(261, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '19-1', NULL, NULL, 'Produit #19 lié à tag #1', NULL),
(262, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '19-9', NULL, NULL, 'Produit #19 lié à tag #9', NULL),
(263, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '19-17', NULL, NULL, 'Produit #19 lié à tag #17', NULL),
(264, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '20-11', NULL, NULL, 'Produit #20 lié à tag #11', NULL),
(265, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '20-5', NULL, NULL, 'Produit #20 lié à tag #5', NULL),
(266, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '20-19', NULL, NULL, 'Produit #20 lié à tag #19', NULL),
(267, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '20-23', NULL, NULL, 'Produit #20 lié à tag #23', NULL),
(268, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '21-11', NULL, NULL, 'Produit #21 lié à tag #11', NULL),
(269, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '21-2', NULL, NULL, 'Produit #21 lié à tag #2', NULL),
(270, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '21-19', NULL, NULL, 'Produit #21 lié à tag #19', NULL),
(271, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '21-23', NULL, NULL, 'Produit #21 lié à tag #23', NULL),
(272, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '22-6', NULL, NULL, 'Produit #22 lié à tag #6', NULL),
(273, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '22-10', NULL, NULL, 'Produit #22 lié à tag #10', NULL),
(274, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '22-16', NULL, NULL, 'Produit #22 lié à tag #16', NULL),
(275, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '23-6', NULL, NULL, 'Produit #23 lié à tag #6', NULL),
(276, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '23-10', NULL, NULL, 'Produit #23 lié à tag #10', NULL),
(277, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '23-25', NULL, NULL, 'Produit #23 lié à tag #25', NULL),
(278, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '24-6', NULL, NULL, 'Produit #24 lié à tag #6', NULL),
(279, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '24-2', NULL, NULL, 'Produit #24 lié à tag #2', NULL),
(280, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '24-25', NULL, NULL, 'Produit #24 lié à tag #25', NULL),
(281, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '25-6', NULL, NULL, 'Produit #25 lié à tag #6', NULL),
(282, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '25-10', NULL, NULL, 'Produit #25 lié à tag #10', NULL),
(283, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '25-25', NULL, NULL, 'Produit #25 lié à tag #25', NULL),
(284, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '26-11', NULL, NULL, 'Produit #26 lié à tag #11', NULL),
(285, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '26-5', NULL, NULL, 'Produit #26 lié à tag #5', NULL),
(286, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '26-23', NULL, NULL, 'Produit #26 lié à tag #23', NULL),
(287, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '27-6', NULL, NULL, 'Produit #27 lié à tag #6', NULL),
(288, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '27-10', NULL, NULL, 'Produit #27 lié à tag #10', NULL),
(289, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '27-23', NULL, NULL, 'Produit #27 lié à tag #23', NULL),
(290, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '28-6', NULL, NULL, 'Produit #28 lié à tag #6', NULL),
(291, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '29-6', NULL, NULL, 'Produit #29 lié à tag #6', NULL),
(292, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '29-1', NULL, NULL, 'Produit #29 lié à tag #1', NULL),
(293, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '29-16', NULL, NULL, 'Produit #29 lié à tag #16', NULL),
(294, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '30-2', NULL, NULL, 'Produit #30 lié à tag #2', NULL),
(295, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '30-1', NULL, NULL, 'Produit #30 lié à tag #1', NULL),
(296, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '30-17', NULL, NULL, 'Produit #30 lié à tag #17', NULL),
(297, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '30-20', NULL, NULL, 'Produit #30 lié à tag #20', NULL),
(298, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '46-10', NULL, NULL, 'Produit #46 lié à tag #10', NULL),
(299, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '46-16', NULL, NULL, 'Produit #46 lié à tag #16', NULL),
(300, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '46-12', NULL, NULL, 'Produit #46 lié à tag #12', NULL),
(301, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '47-17', NULL, NULL, 'Produit #47 lié à tag #17', NULL),
(302, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '47-10', NULL, NULL, 'Produit #47 lié à tag #10', NULL),
(303, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '47-12', NULL, NULL, 'Produit #47 lié à tag #12', NULL),
(304, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '48-18', NULL, NULL, 'Produit #48 lié à tag #18', NULL),
(305, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '48-10', NULL, NULL, 'Produit #48 lié à tag #10', NULL),
(306, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '49-7', NULL, NULL, 'Produit #49 lié à tag #7', NULL),
(307, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '49-13', NULL, NULL, 'Produit #49 lié à tag #13', NULL),
(308, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '49-19', NULL, NULL, 'Produit #49 lié à tag #19', NULL),
(309, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '50-13', NULL, NULL, 'Produit #50 lié à tag #13', NULL),
(310, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '50-20', NULL, NULL, 'Produit #50 lié à tag #20', NULL),
(311, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '50-7', NULL, NULL, 'Produit #50 lié à tag #7', NULL),
(312, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '51-13', NULL, NULL, 'Produit #51 lié à tag #13', NULL),
(313, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '51-20', NULL, NULL, 'Produit #51 lié à tag #20', NULL),
(314, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '51-10', NULL, NULL, 'Produit #51 lié à tag #10', NULL),
(315, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '53-10', NULL, NULL, 'Produit #53 lié à tag #10', NULL),
(316, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '53-16', NULL, NULL, 'Produit #53 lié à tag #16', NULL),
(317, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '53-21', NULL, NULL, 'Produit #53 lié à tag #21', NULL),
(318, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '54-10', NULL, NULL, 'Produit #54 lié à tag #10', NULL),
(319, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '54-17', NULL, NULL, 'Produit #54 lié à tag #17', NULL),
(320, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '55-10', NULL, NULL, 'Produit #55 lié à tag #10', NULL),
(321, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '55-16', NULL, NULL, 'Produit #55 lié à tag #16', NULL),
(322, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '55-21', NULL, NULL, 'Produit #55 lié à tag #21', NULL),
(323, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '56-10', NULL, NULL, 'Produit #56 lié à tag #10', NULL),
(324, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '56-18', NULL, NULL, 'Produit #56 lié à tag #18', NULL),
(325, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '56-22', NULL, NULL, 'Produit #56 lié à tag #22', NULL),
(326, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '57-18', NULL, NULL, 'Produit #57 lié à tag #18', NULL),
(327, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '57-10', NULL, NULL, 'Produit #57 lié à tag #10', NULL),
(328, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '57-22', NULL, NULL, 'Produit #57 lié à tag #22', NULL),
(329, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '58-10', NULL, NULL, 'Produit #58 lié à tag #10', NULL),
(330, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '58-23', NULL, NULL, 'Produit #58 lié à tag #23', NULL),
(331, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '59-1', NULL, NULL, 'Produit #59 lié à tag #1', NULL),
(332, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '59-11', NULL, NULL, 'Produit #59 lié à tag #11', NULL),
(333, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '59-24', NULL, NULL, 'Produit #59 lié à tag #24', NULL),
(334, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '60-1', NULL, NULL, 'Produit #60 lié à tag #1', NULL),
(335, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '60-11', NULL, NULL, 'Produit #60 lié à tag #11', NULL),
(336, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '60-6', NULL, NULL, 'Produit #60 lié à tag #6', NULL),
(337, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '60-25', NULL, NULL, 'Produit #60 lié à tag #25', NULL),
(338, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '61-17', NULL, NULL, 'Produit #61 lié à tag #17', NULL),
(339, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '61-20', NULL, NULL, 'Produit #61 lié à tag #20', NULL),
(340, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '61-10', NULL, NULL, 'Produit #61 lié à tag #10', NULL),
(341, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '62-1', NULL, NULL, 'Produit #62 lié à tag #1', NULL),
(342, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '62-3', NULL, NULL, 'Produit #62 lié à tag #3', NULL),
(343, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '62-8', NULL, NULL, 'Produit #62 lié à tag #8', NULL),
(344, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '62-12', NULL, NULL, 'Produit #62 lié à tag #12', NULL),
(345, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '63-1', NULL, NULL, 'Produit #63 lié à tag #1', NULL),
(346, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '63-3', NULL, NULL, 'Produit #63 lié à tag #3', NULL),
(347, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '63-12', NULL, NULL, 'Produit #63 lié à tag #12', NULL),
(348, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '63-7', NULL, NULL, 'Produit #63 lié à tag #7', NULL),
(349, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '64-3', NULL, NULL, 'Produit #64 lié à tag #3', NULL),
(350, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '64-9', NULL, NULL, 'Produit #64 lié à tag #9', NULL),
(351, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '64-10', NULL, NULL, 'Produit #64 lié à tag #10', NULL),
(352, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '71-3', NULL, NULL, 'Produit #71 lié à tag #3', NULL),
(353, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '71-5', NULL, NULL, 'Produit #71 lié à tag #5', NULL),
(354, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '71-12', NULL, NULL, 'Produit #71 lié à tag #12', NULL),
(355, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '72-8', NULL, NULL, 'Produit #72 lié à tag #8', NULL),
(356, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '72-10', NULL, NULL, 'Produit #72 lié à tag #10', NULL),
(357, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '72-12', NULL, NULL, 'Produit #72 lié à tag #12', NULL),
(358, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '31-7', NULL, NULL, 'Produit #31 lié à tag #7', NULL),
(359, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '31-4', NULL, NULL, 'Produit #31 lié à tag #4', NULL),
(360, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '31-10', NULL, NULL, 'Produit #31 lié à tag #10', NULL),
(361, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '31-16', NULL, NULL, 'Produit #31 lié à tag #16', NULL),
(362, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '32-1', NULL, NULL, 'Produit #32 lié à tag #1', NULL),
(363, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '32-8', NULL, NULL, 'Produit #32 lié à tag #8', NULL),
(364, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '32-12', NULL, NULL, 'Produit #32 lié à tag #12', NULL),
(365, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '32-27', NULL, NULL, 'Produit #32 lié à tag #27', NULL),
(366, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '33-2', NULL, NULL, 'Produit #33 lié à tag #2', NULL),
(367, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '33-5', NULL, NULL, 'Produit #33 lié à tag #5', NULL),
(368, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '33-10', NULL, NULL, 'Produit #33 lié à tag #10', NULL),
(369, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '33-20', NULL, NULL, 'Produit #33 lié à tag #20', NULL),
(370, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '34-2', NULL, NULL, 'Produit #34 lié à tag #2', NULL),
(371, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '34-4', NULL, NULL, 'Produit #34 lié à tag #4', NULL),
(372, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '34-11', NULL, NULL, 'Produit #34 lié à tag #11', NULL),
(373, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '34-26', NULL, NULL, 'Produit #34 lié à tag #26', NULL),
(374, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '35-7', NULL, NULL, 'Produit #35 lié à tag #7', NULL),
(375, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '35-13', NULL, NULL, 'Produit #35 lié à tag #13', NULL),
(376, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '35-8', NULL, NULL, 'Produit #35 lié à tag #8', NULL),
(377, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '35-27', NULL, NULL, 'Produit #35 lié à tag #27', NULL),
(378, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '36-1', NULL, NULL, 'Produit #36 lié à tag #1', NULL),
(379, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '36-5', NULL, NULL, 'Produit #36 lié à tag #5', NULL),
(380, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '36-2', NULL, NULL, 'Produit #36 lié à tag #2', NULL),
(381, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '36-28', NULL, NULL, 'Produit #36 lié à tag #28', NULL),
(382, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '37-1', NULL, NULL, 'Produit #37 lié à tag #1', NULL),
(383, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '37-12', NULL, NULL, 'Produit #37 lié à tag #12', NULL),
(384, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '37-15', NULL, NULL, 'Produit #37 lié à tag #15', NULL),
(385, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '37-26', NULL, NULL, 'Produit #37 lié à tag #26', NULL),
(386, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '38-4', NULL, NULL, 'Produit #38 lié à tag #4', NULL),
(387, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '38-9', NULL, NULL, 'Produit #38 lié à tag #9', NULL),
(388, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '38-11', NULL, NULL, 'Produit #38 lié à tag #11', NULL),
(389, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '38-27', NULL, NULL, 'Produit #38 lié à tag #27', NULL),
(390, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '39-4', NULL, NULL, 'Produit #39 lié à tag #4', NULL),
(391, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '39-8', NULL, NULL, 'Produit #39 lié à tag #8', NULL),
(392, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '39-15', NULL, NULL, 'Produit #39 lié à tag #15', NULL),
(393, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '39-16', NULL, NULL, 'Produit #39 lié à tag #16', NULL),
(394, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '39-21', NULL, NULL, 'Produit #39 lié à tag #21', NULL),
(395, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '40-6', NULL, NULL, 'Produit #40 lié à tag #6', NULL),
(396, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '40-12', NULL, NULL, 'Produit #40 lié à tag #12', NULL),
(397, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '40-15', NULL, NULL, 'Produit #40 lié à tag #15', NULL),
(398, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '40-26', NULL, NULL, 'Produit #40 lié à tag #26', NULL),
(399, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '41-12', NULL, NULL, 'Produit #41 lié à tag #12', NULL),
(400, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '41-8', NULL, NULL, 'Produit #41 lié à tag #8', NULL),
(401, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '41-15', NULL, NULL, 'Produit #41 lié à tag #15', NULL),
(402, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '41-27', NULL, NULL, 'Produit #41 lié à tag #27', NULL),
(403, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '41-20', NULL, NULL, 'Produit #41 lié à tag #20', NULL),
(404, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '42-9', NULL, NULL, 'Produit #42 lié à tag #9', NULL),
(405, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '42-1', NULL, NULL, 'Produit #42 lié à tag #1', NULL),
(406, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '42-2', NULL, NULL, 'Produit #42 lié à tag #2', NULL),
(407, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '43-11', NULL, NULL, 'Produit #43 lié à tag #11', NULL),
(408, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '43-8', NULL, NULL, 'Produit #43 lié à tag #8', NULL),
(409, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '43-12', NULL, NULL, 'Produit #43 lié à tag #12', NULL),
(410, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '43-26', NULL, NULL, 'Produit #43 lié à tag #26', NULL),
(411, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '44-4', NULL, NULL, 'Produit #44 lié à tag #4', NULL),
(412, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '44-14', NULL, NULL, 'Produit #44 lié à tag #14', NULL),
(413, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '44-2', NULL, NULL, 'Produit #44 lié à tag #2', NULL),
(414, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '44-28', NULL, NULL, 'Produit #44 lié à tag #28', NULL),
(415, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '45-10', NULL, NULL, 'Produit #45 lié à tag #10', NULL),
(416, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '45-12', NULL, NULL, 'Produit #45 lié à tag #12', NULL),
(417, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '45-6', NULL, NULL, 'Produit #45 lié à tag #6', NULL),
(418, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '45-16', NULL, NULL, 'Produit #45 lié à tag #16', NULL),
(419, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '65-15', NULL, NULL, 'Produit #65 lié à tag #15', NULL),
(420, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '65-10', NULL, NULL, 'Produit #65 lié à tag #10', NULL),
(421, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '65-26', NULL, NULL, 'Produit #65 lié à tag #26', NULL),
(422, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '65-21', NULL, NULL, 'Produit #65 lié à tag #21', NULL),
(423, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '66-4', NULL, NULL, 'Produit #66 lié à tag #4', NULL),
(424, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '66-14', NULL, NULL, 'Produit #66 lié à tag #14', NULL),
(425, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '66-27', NULL, NULL, 'Produit #66 lié à tag #27', NULL),
(426, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '67-4', NULL, NULL, 'Produit #67 lié à tag #4', NULL),
(427, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '67-14', NULL, NULL, 'Produit #67 lié à tag #14', NULL),
(428, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '67-28', NULL, NULL, 'Produit #67 lié à tag #28', NULL),
(429, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '68-12', NULL, NULL, 'Produit #68 lié à tag #12', NULL),
(430, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '68-15', NULL, NULL, 'Produit #68 lié à tag #15', NULL),
(431, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '68-1', NULL, NULL, 'Produit #68 lié à tag #1', NULL),
(432, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '68-8', NULL, NULL, 'Produit #68 lié à tag #8', NULL),
(433, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '69-15', NULL, NULL, 'Produit #69 lié à tag #15', NULL),
(434, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '69-4', NULL, NULL, 'Produit #69 lié à tag #4', NULL),
(435, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '69-10', NULL, NULL, 'Produit #69 lié à tag #10', NULL),
(436, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '69-21', NULL, NULL, 'Produit #69 lié à tag #21', NULL),
(437, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '70-15', NULL, NULL, 'Produit #70 lié à tag #15', NULL),
(438, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '70-26', NULL, NULL, 'Produit #70 lié à tag #26', NULL),
(439, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '70-12', NULL, NULL, 'Produit #70 lié à tag #12', NULL),
(440, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '73-21', NULL, NULL, 'Produit #73 lié à tag #21', NULL),
(441, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '73-10', NULL, NULL, 'Produit #73 lié à tag #10', NULL),
(442, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '73-26', NULL, NULL, 'Produit #73 lié à tag #26', NULL),
(443, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '73-16', NULL, NULL, 'Produit #73 lié à tag #16', NULL),
(444, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '74-27', NULL, NULL, 'Produit #74 lié à tag #27', NULL),
(445, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '74-13', NULL, NULL, 'Produit #74 lié à tag #13', NULL),
(446, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '74-17', NULL, NULL, 'Produit #74 lié à tag #17', NULL),
(447, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '74-14', NULL, NULL, 'Produit #74 lié à tag #14', NULL),
(448, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '75-3', NULL, NULL, 'Produit #75 lié à tag #3', NULL),
(449, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '75-10', NULL, NULL, 'Produit #75 lié à tag #10', NULL),
(450, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '75-4', NULL, NULL, 'Produit #75 lié à tag #4', NULL),
(451, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '75-28', NULL, NULL, 'Produit #75 lié à tag #28', NULL),
(452, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '76-12', NULL, NULL, 'Produit #76 lié à tag #12', NULL),
(453, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '76-8', NULL, NULL, 'Produit #76 lié à tag #8', NULL),
(454, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '76-1', NULL, NULL, 'Produit #76 lié à tag #1', NULL),
(455, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '76-15', NULL, NULL, 'Produit #76 lié à tag #15', NULL),
(456, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '77-10', NULL, NULL, 'Produit #77 lié à tag #10', NULL),
(457, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '77-21', NULL, NULL, 'Produit #77 lié à tag #21', NULL),
(458, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '77-16', NULL, NULL, 'Produit #77 lié à tag #16', NULL),
(459, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '77-27', NULL, NULL, 'Produit #77 lié à tag #27', NULL),
(460, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '78-18', NULL, NULL, 'Produit #78 lié à tag #18', NULL),
(461, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '78-22', NULL, NULL, 'Produit #78 lié à tag #22', NULL),
(462, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '78-26', NULL, NULL, 'Produit #78 lié à tag #26', NULL),
(463, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '78-15', NULL, NULL, 'Produit #78 lié à tag #15', NULL),
(464, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '79-2', NULL, NULL, 'Produit #79 lié à tag #2', NULL),
(465, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '79-5', NULL, NULL, 'Produit #79 lié à tag #5', NULL),
(466, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '79-11', NULL, NULL, 'Produit #79 lié à tag #11', NULL),
(467, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '79-23', NULL, NULL, 'Produit #79 lié à tag #23', NULL),
(468, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '80-7', NULL, NULL, 'Produit #80 lié à tag #7', NULL),
(469, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '80-10', NULL, NULL, 'Produit #80 lié à tag #10', NULL),
(470, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '80-20', NULL, NULL, 'Produit #80 lié à tag #20', NULL),
(471, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '81-2', NULL, NULL, 'Produit #81 lié à tag #2', NULL),
(472, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '81-5', NULL, NULL, 'Produit #81 lié à tag #5', NULL),
(473, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '81-11', NULL, NULL, 'Produit #81 lié à tag #11', NULL),
(474, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '81-23', NULL, NULL, 'Produit #81 lié à tag #23', NULL),
(475, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '82-7', NULL, NULL, 'Produit #82 lié à tag #7', NULL),
(476, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '82-10', NULL, NULL, 'Produit #82 lié à tag #10', NULL);
INSERT INTO `audit_logs` (`id_log`, `date_action`, `type_action`, `table_concernee`, `enregistrement_id`, `champ_modifie`, `ancienne_valeur`, `nouvelle_valeur`, `id_admin`) VALUES
(477, '2025-07-14 13:07:47', 'LINK', 'produit_tag', '82-20', NULL, NULL, 'Produit #82 lié à tag #20', NULL),
(478, '2025-07-14 13:07:47', 'CREATE', 'client', '1', NULL, NULL, 'Nouveau client : alice@example.com', NULL),
(479, '2025-07-14 13:07:47', 'CREATE', 'client', '2', NULL, NULL, 'Nouveau client : bob@example.com', NULL),
(480, '2025-07-14 13:07:47', 'CREATE', 'client', '3', NULL, NULL, 'Nouveau client : chloe@example.com', NULL),
(481, '2025-07-14 13:07:47', 'UPDATE', 'produit', '1', 'stock', '15', '14', NULL),
(482, '2025-07-14 13:07:47', 'UPDATE', 'produit', '37', 'stock', '25', '24', NULL),
(483, '2025-07-14 13:07:47', 'UPDATE', 'produit', '6', 'stock', '8', '7', NULL),
(484, '2025-07-14 13:07:47', 'UPDATE', 'produit', '34', 'stock', '50', '49', NULL),
(485, '2025-07-14 13:07:47', 'UPDATE', 'produit', '10', 'stock', '6', '5', NULL),
(486, '2025-07-14 13:07:47', 'UPDATE', 'produit', '35', 'stock', '45', '44', NULL),
(487, '2025-07-14 13:07:47', 'UPDATE', 'produit', '15', 'stock', '20', '19', NULL),
(488, '2025-07-14 13:07:47', 'UPDATE', 'produit', '36', 'stock', '30', '29', NULL),
(489, '2025-06-14 15:00:00', 'UPDATE', 'produit', '1', 'stock', '15', '14', 1),
(490, '2025-07-14 13:09:29', 'CREATE', 'client', '4', NULL, NULL, 'Nouveau client : johndoe@test.com', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auteur`
--

DROP TABLE IF EXISTS `auteur`;
CREATE TABLE IF NOT EXISTS `auteur` (
  `id_auteur` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_auteur`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auteur`
--

INSERT INTO `auteur` (`id_auteur`, `nom`, `prenom`) VALUES
(1, 'Verne', 'Jules'),
(2, 'Asimov', 'Isaac'),
(3, 'Rowling', 'J.K.'),
(4, 'Christie', 'Agatha'),
(5, 'Camus', 'Albert'),
(6, 'Le Guin', 'Ursula K.'),
(7, 'King', 'Stephen'),
(8, 'Austen', 'Jane'),
(9, 'Hugo', 'Victor'),
(10, 'Dumas', 'Alexandre'),
(11, 'Tolkien', 'J.R.R.'),
(12, 'Orwell', 'George'),
(13, 'Huxley', 'Aldous'),
(14, 'Herbert', 'Frank'),
(15, 'Gibson', 'William'),
(16, 'Simmons', 'Dan'),
(17, 'Rothfuss', 'Patrick'),
(18, 'Howey', 'Hugh'),
(19, 'Damasio', 'Alain'),
(20, 'Lovecraft', 'H.P.'),
(21, 'Tolle', 'Eckhart'),
(22, 'Giordano', 'Raphaëlle'),
(23, 'Elrod', 'Hal'),
(24, 'Flaubert', 'Gustave'),
(25, 'Sparks', 'Nicholas'),
(26, 'Moyes', 'JoJo'),
(27, 'Gabaldon', 'Diana'),
(28, 'Lenoir', 'Frédéric'),
(29, 'Ankaoua', 'Maud'),
(30, 'Bourbeau', 'Lise'),
(31, 'Kiyosaki', 'Robert'),
(32, 'Sinek', 'Simon'),
(33, 'Cialdini', 'Robert'),
(34, 'Snicket', 'Lemony'),
(35, 'Collins', 'Suzanne'),
(36, 'Palacio', 'R.J.'),
(37, 'Hobb', 'Robin'),
(38, 'Brooks', 'Terry'),
(39, 'Clarke', 'Susanna'),
(40, 'Beagle', 'Peter S.'),
(41, 'Krakauer', 'Jon'),
(42, 'Flynn', 'Gillian'),
(43, 'de Saint-Exupéry', 'Antoine');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bougie`
--

DROP TABLE IF EXISTS `bougie`;
CREATE TABLE IF NOT EXISTS `bougie` (
  `id_produit` int UNSIGNED NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `parfum` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duree_combustion` smallint DEFAULT NULL,
  `poids` int DEFAULT NULL,
  PRIMARY KEY (`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bougie`
--

INSERT INTO `bougie` (`id_produit`, `nom`, `description`, `parfum`, `duree_combustion`, `poids`) VALUES
(31, 'Bibliothèque Ancienne', 'L\'odeur envoûtante du vieux papier, du cuir patiné et du bois ciré. Transformez votre salon en un havre de paix littéraire, comme si vous feuilletiez un incunable au coin du feu.', 'Papier, Cuir, Bois de Santal', 50, 200),
(32, 'Forêt Enchantée', 'Perdez-vous dans une épopée fantastique : pin frais, mousse humide et terre après la pluie. Fermez les yeux, vous cheminez sous une canopée millénaire où murmure la magie.', 'Pin, Mousse, Terre Humide', 55, 220),
(33, 'Pluie sur la Ville', 'L\'odeur minérale et fraîche de la pluie frappant l\'asphalte chaud. Une ambiance mélancolique et introspective, parfaite pour polar nocturne et jazz feutré.', 'Pétricor, Asphalte Mouillé', 45, 180),
(34, 'Pause Café', 'Arômes riches de café fraîchement torréfié, relevés d\'une pointe de noisette. Idéal pour veiller jusqu\'à la révélation finale d\'un thriller palpitant.', 'Café, Noisette Grillée', 40, 180),
(35, 'Jardin Anglais', 'Roses anciennes et thé noir délicat. Invitez-vous à un after-tea dans un boudoir victorien, coussin brodé et roman d\'Austen à la main.', 'Rose, Thé Noir, Poudre', 50, 200),
(36, 'Ambre des Sables', 'Ambre chaud, encens, épices lointaines : un souffle mystique qui vous transporte sur les dunes d\'Arrakis ou dans un temple oublié.', 'Ambre, Encens, Épices', 60, 220),
(37, 'Brise Marine', 'Respirez l\'air salin du large ; algues et embruns fouettent le pont de votre navire imaginaire. Compagnon idéal des explorations sous-marines et récits de corsaires.', 'Sel Marin, Notes Aquatiques', 50, 200),
(38, 'Feu de Cheminée', 'Crépitement du bois, braises rougeoyantes : la lecture d\'hiver prend une dimension cocooning, plaid sur les genoux et chocolat chaud.', 'Bois Fumé, Cèdre', 45, 190),
(39, 'Sérénité Provençale', 'Lavande pure adoucie de romarin. Créez une bulle de calme pour méditer, respirer et relire Camus à l\'ombre d\'un olivier.', 'Lavande, Romarin', 55, 210),
(40, 'Néons & Circuits', 'Senteur électrique et métallique, adoucie d\'un zeste d\'agrumes. Allumez-la pour hacker le cyberespace depuis votre fauteuil.', 'Agrumes, Notes Métalliques', 45, 180),
(41, 'Orangerie en Fleurs', 'Promenade ensoleillée dans un jardin d\'agrumes en fleur. Les notes vives de bergamote et fleur d\'oranger enveloppent les lectures optimistes.', 'Fleur d\'oranger, Bergamote', 50, 200),
(42, 'Cuir & Parchemin', 'Cuir vieilli, papier jauni, soupçon d\'encens : ouvrez un grimoire ancien ou une carte au trésor, l\'aventure commence.', 'Cuir, Papier, Encens léger', 60, 220),
(43, 'Tempête en Montagne', 'Air vif des sommets, pierre mouillée, sapin. Parfait pour récits de survie et thrillers glacés à plus de 3 000 m.', 'Air frais, Pierre, Sapin', 50, 190),
(44, 'Cidre & Épices', 'Cidre chaud, pomme caramélisée, cannelle et clou de girofle. Une atmosphère automnale réconfortante, plaid et conte merveilleux obligatoires.', 'Pomme, Cannelle, Clou de girofle', 40, 180),
(45, 'Nuit Étoilée', 'Brise nocturne, musc blanc et fleurs célestes : allumez-la, tirez les rideaux et contemplez la voie lactée depuis votre canapé.', 'Ozone, Musc blanc, Notes florales nocturnes', 55, 210),
(65, 'Zen & Focus', 'Voile herbacé de thé vert et maté pour favoriser concentration et méditation lors d\'une lecture studieuse.', 'Thé vert, Maté', 40, 190),
(66, 'Étreinte Vanillée', 'Gousse de vanille et sucre brun fondent en un cocon doux et rassurant. La bougie idéale pour les soirées feel-good.', 'Vanille, Sucre brun', 45, 200),
(67, 'Sortilège de Cannelle', 'Tourbillon épicé de cannelle et girofle qui évoque marchés d\'hiver et contes au coin du feu.', 'Cannelle, Girofle', 40, 190),
(68, 'Vent du Large', 'Brise saline, algues et sel marin rappelant les falaises atlantiques ; compagnon parfait des récits d\'aventures maritimes.', 'Brise marine, Sel, Algues', 35, 185),
(69, 'Lavande du Soir', 'Lavande provençale apaisée d\'une touche de miel. Installe une atmosphère relaxante avant le coucher.', 'Lavande, Miel', 40, 200),
(70, 'Éclat Citrus', 'Cocktail vivifiant de yuzu et citron jaune ; un soleil liquide qui dynamise vos après-midi lecture.', 'Citron, Yuzu', 35, 180);

--
-- Disparadores `bougie`
--
DROP TRIGGER IF EXISTS `trg_audit_bougie_update`;
DELIMITER $$
CREATE TRIGGER `trg_audit_bougie_update` AFTER UPDATE ON `bougie` FOR EACH ROW BEGIN
    IF IFNULL(OLD.nom,'') <> IFNULL(NEW.nom,'') THEN
        INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,champ_modifie,ancienne_valeur,nouvelle_valeur,id_admin)
        VALUES ('UPDATE','bougie',OLD.id_produit,'nom',OLD.nom,NEW.nom,@current_admin_id);
    END IF;
    IF IFNULL(OLD.parfum,'') <> IFNULL(NEW.parfum,'') THEN
        INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,champ_modifie,ancienne_valeur,nouvelle_valeur,id_admin)
        VALUES ('UPDATE','bougie',OLD.id_produit,'parfum',OLD.parfum,NEW.parfum,@current_admin_id);
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_unique_subtype_bougie`;
DELIMITER $$
CREATE TRIGGER `trg_unique_subtype_bougie` BEFORE INSERT ON `bougie` FOR EACH ROW BEGIN
    IF EXISTS (SELECT 1 FROM livre   WHERE id_produit = NEW.id_produit)
       OR EXISTS (SELECT 1 FROM coffret WHERE id_produit = NEW.id_produit) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produit déjà défini comme livre ou coffret';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorie_coffret`
--

DROP TABLE IF EXISTS `categorie_coffret`;
CREATE TABLE IF NOT EXISTS `categorie_coffret` (
  `id_categorie_coffret` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_categorie_coffret`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorie_coffret`
--

INSERT INTO `categorie_coffret` (`id_categorie_coffret`, `libelle`) VALUES
(3, 'Mini'),
(2, 'Premium'),
(1, 'Standard');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id_client` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_client`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `client`
--

INSERT INTO `client` (`id_client`, `nom`, `prenom`, `email`, `password_hash`, `date_creation`, `reset_token`, `reset_token_expires_at`) VALUES
(1, 'Durand', 'Alice', 'alice@example.com', '$2y$10$NotARealHashForSecurityReasons123', '2025-05-10 11:30:00', NULL, NULL),
(2, 'Martin', 'Bob', 'bob@example.com', '$2y$10$NotARealHashForSecurityReasons123', '2025-05-15 18:00:00', NULL, NULL),
(3, 'Dupont', 'Chloé', 'chloe@example.com', '$2y$10$NotARealHashForSecurityReasons123', '2025-06-01 09:00:00', NULL, NULL),
(4, 'Doe', 'John', 'johndoe@test.com', 'f6ad0569e8369452833c455b460ae1e54414cc90142745a8ecf2d5281de0ea70', '2025-07-14 13:09:29', NULL, NULL);

--
-- Disparadores `client`
--
DROP TRIGGER IF EXISTS `trg_audit_client_delete`;
DELIMITER $$
CREATE TRIGGER `trg_audit_client_delete` AFTER DELETE ON `client` FOR EACH ROW INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,ancienne_valeur,id_admin)
    VALUES ('DELETE','client',OLD.id_client,CONCAT('Client ',OLD.email,' supprimé'),@current_admin_id)
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_audit_client_insert`;
DELIMITER $$
CREATE TRIGGER `trg_audit_client_insert` AFTER INSERT ON `client` FOR EACH ROW INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,nouvelle_valeur,id_admin)
    VALUES ('CREATE','client',NEW.id_client,CONCAT('Nouveau client : ',NEW.email),@current_admin_id)
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_audit_client_update`;
DELIMITER $$
CREATE TRIGGER `trg_audit_client_update` AFTER UPDATE ON `client` FOR EACH ROW BEGIN
    IF OLD.nom <> NEW.nom THEN
        INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,champ_modifie,ancienne_valeur,nouvelle_valeur,id_admin)
        VALUES ('UPDATE','client',OLD.id_client,'nom',OLD.nom,NEW.nom,@current_admin_id);
    END IF;
    IF OLD.prenom <> NEW.prenom THEN
        INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,champ_modifie,ancienne_valeur,nouvelle_valeur,id_admin)
        VALUES ('UPDATE','client',OLD.id_client,'prenom',OLD.prenom,NEW.prenom,@current_admin_id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coffret`
--

DROP TABLE IF EXISTS `coffret`;
CREATE TABLE IF NOT EXISTS `coffret` (
  `id_produit` int UNSIGNED NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `id_produit_livre` int UNSIGNED NOT NULL,
  `id_produit_bougie` int UNSIGNED NOT NULL,
  `id_categorie_coffret` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_produit`),
  UNIQUE KEY `uq_coffret_pair` (`id_produit_livre`,`id_produit_bougie`),
  KEY `id_produit_bougie` (`id_produit_bougie`),
  KEY `id_categorie_coffret` (`id_categorie_coffret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `coffret`
--

INSERT INTO `coffret` (`id_produit`, `nom`, `description`, `id_produit_livre`, `id_produit_bougie`, `id_categorie_coffret`) VALUES
(73, 'Lecture Zen', 'Un coffret apaisant pour ralentir et se recentrer sur l\'essentiel. Idéal pour une pause méditative et une lecture inspirante.', 46, 65, 1),
(74, 'Cocoon Lecture', 'Un moment cocooning tout en douceur : laissez-vous envelopper par la chaleur d\'une bougie parfumée et le réconfort d\'une histoire captivante.', 10, 66, 1),
(75, 'Lecture Magique', 'Plongez dans un univers féerique avec la dernière licorne. Un coffret pour s\'évader et croire en la magie de la lecture.', 71, 67, 2),
(76, 'Évasion Littéraire', 'Un voyage littéraire au cœur de la nature sauvage et des grands espaces. L\'aventure vous attend à chaque page.', 72, 68, 1),
(77, 'Inspiration Douce', 'Un coffret bien-être entre réflexion et sérénité : le duo parfait pour nourrir l\'esprit et apaiser l\'âme.', 53, 69, 1),
(78, 'Business & Boost', 'Un duo dynamique pour stimuler ambition et énergie. Le coffret idéal pour les esprits entrepreneurs et ceux qui visent le sommet.', 56, 70, 1),
(81, 'Mystère & Tension', 'Un duo captivant pour les amateurs de thrillers psychologiques et d\'atmosphères troubles.', 79, 45, 1),
(82, 'Rêverie Étoilée', 'Une invitation à la poésie et à l\'émerveillement pour retrouver son âme d\'enfant.', 80, 35, 1);

--
-- Disparadores `coffret`
--
DROP TRIGGER IF EXISTS `trg_coffret_check_bougie`;
DELIMITER $$
CREATE TRIGGER `trg_coffret_check_bougie` BEFORE INSERT ON `coffret` FOR EACH ROW BEGIN
    DECLARE v_type VARCHAR(10);
    SELECT type INTO v_type FROM produit WHERE id_produit = NEW.id_produit_bougie;
    IF v_type <> 'bougie' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Le composant “bougie” n\'est pas de type bougie.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_coffret_check_livre`;
DELIMITER $$
CREATE TRIGGER `trg_coffret_check_livre` BEFORE INSERT ON `coffret` FOR EACH ROW BEGIN
    DECLARE v_type VARCHAR(10);
    SELECT type INTO v_type FROM produit WHERE id_produit = NEW.id_produit_livre;
    IF v_type <> 'livre' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Le composant “livre” n\'est pas de type livre.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_unique_subtype_coffret`;
DELIMITER $$
CREATE TRIGGER `trg_unique_subtype_coffret` BEFORE INSERT ON `coffret` FOR EACH ROW BEGIN
    IF EXISTS (SELECT 1 FROM livre  WHERE id_produit = NEW.id_produit)
       OR EXISTS (SELECT 1 FROM bougie WHERE id_produit = NEW.id_produit) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produit déjà défini comme livre ou bougie';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id_commande` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_ht` decimal(10,2) NOT NULL,
  `total_tva` decimal(10,2) NOT NULL,
  `total_ttc` decimal(10,2) NOT NULL,
  `id_client` int UNSIGNED NOT NULL,
  `id_adresse_livraison` int UNSIGNED NOT NULL,
  `id_statut_commande` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_commande`),
  KEY `id_client` (`id_client`),
  KEY `id_adresse_livraison` (`id_adresse_livraison`),
  KEY `id_statut_commande` (`id_statut_commande`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `commande`
--

INSERT INTO `commande` (`id_commande`, `date_commande`, `total_ht`, `total_tva`, `total_ttc`, `id_client`, `id_adresse_livraison`, `id_statut_commande`) VALUES
(1, '2025-06-10 10:00:00', 19.40, 2.79, 22.19, 1, 1, 3),
(2, '2025-06-11 14:20:00', 20.30, 2.93, 23.23, 2, 3, 2),
(3, '2025-06-12 09:15:00', 19.90, 3.05, 22.95, 3, 4, 2),
(4, '2025-06-13 16:45:00', 23.50, 3.18, 26.68, 1, 2, 1);

--
-- Disparadores `commande`
--
DROP TRIGGER IF EXISTS `trg_audit_commande_update`;
DELIMITER $$
CREATE TRIGGER `trg_audit_commande_update` AFTER UPDATE ON `commande` FOR EACH ROW BEGIN
    IF OLD.id_statut_commande <> NEW.id_statut_commande THEN
        INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,champ_modifie,ancienne_valeur,nouvelle_valeur,id_admin)
        VALUES ('UPDATE','commande',OLD.id_commande,'id_statut_commande',
                OLD.id_statut_commande,NEW.id_statut_commande,@current_admin_id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `commande_details`
--

DROP TABLE IF EXISTS `commande_details`;
CREATE TABLE IF NOT EXISTS `commande_details` (
  `id_commande` int UNSIGNED NOT NULL,
  `id_produit` int UNSIGNED NOT NULL,
  `quantite` int UNSIGNED NOT NULL,
  `prix_ht` decimal(10,2) NOT NULL,
  `tva_rate` decimal(4,2) NOT NULL,
  `prix_ttc` decimal(10,2) NOT NULL,
  `montant_tva` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_commande`,`id_produit`),
  KEY `idx_cmd_det_prod` (`id_produit`)
) ;

--
-- Volcado de datos para la tabla `commande_details`
--

INSERT INTO `commande_details` (`id_commande`, `id_produit`, `quantite`, `prix_ht`, `tva_rate`, `prix_ttc`, `montant_tva`) VALUES
(1, 1, 1, 7.50, 5.50, 7.91, 0.41),
(1, 37, 1, 11.90, 20.00, 14.28, 2.38),
(2, 6, 1, 7.80, 5.50, 8.23, 0.43),
(2, 34, 1, 12.50, 20.00, 15.00, 2.50),
(3, 10, 1, 6.40, 5.50, 6.75, 0.35),
(3, 35, 1, 13.50, 20.00, 16.20, 2.70),
(4, 15, 1, 10.50, 5.50, 11.08, 0.58),
(4, 36, 1, 13.00, 20.00, 15.60, 2.60);

--
-- Disparadores `commande_details`
--
DROP TRIGGER IF EXISTS `trg_cmd_det_type`;
DELIMITER $$
CREATE TRIGGER `trg_cmd_det_type` BEFORE INSERT ON `commande_details` FOR EACH ROW BEGIN
    DECLARE v_type VARCHAR(10);
    SELECT type INTO v_type FROM produit WHERE id_produit = NEW.id_produit;

    IF v_type IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produit inexistant (commande_details)';
    END IF;

    IF v_type NOT IN ('livre','bougie','coffret') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Type de produit invalide pour une commande';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `demande_retour`
--

DROP TABLE IF EXISTS `demande_retour`;
CREATE TABLE IF NOT EXISTS `demande_retour` (
  `id_demande` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_demande` text COLLATE utf8mb4_unicode_ci,
  `date_demande` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_commande` int UNSIGNED NOT NULL,
  `id_client` int UNSIGNED NOT NULL,
  `id_statut_demande` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_demande`),
  KEY `id_commande` (`id_commande`),
  KEY `id_client` (`id_client`),
  KEY `id_statut_demande` (`id_statut_demande`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `demande_retour`
--

INSERT INTO `demande_retour` (`id_demande`, `message_demande`, `date_demande`, `id_commande`, `id_client`, `id_statut_demande`) VALUES
(1, 'La bougie est arrivée cassée dans le colis.', '2025-06-12 11:00:00', 1, 1, 1),
(2, 'Bonjour, je souhaiterais retourner cet article qui ne correspond pas à mes attentes.', '2025-06-15 18:30:00', 3, 3, 2);

--
-- Disparadores `demande_retour`
--
DROP TRIGGER IF EXISTS `trg_audit_demande_retour_update`;
DELIMITER $$
CREATE TRIGGER `trg_audit_demande_retour_update` AFTER UPDATE ON `demande_retour` FOR EACH ROW BEGIN
    IF OLD.id_statut_demande <> NEW.id_statut_demande THEN
        INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,champ_modifie,ancienne_valeur,nouvelle_valeur,id_admin)
        VALUES ('UPDATE','demande_retour',OLD.id_demande,'id_statut_demande',
                OLD.id_statut_demande,NEW.id_statut_demande,@current_admin_id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `editeur`
--

DROP TABLE IF EXISTS `editeur`;
CREATE TABLE IF NOT EXISTS `editeur` (
  `id_editeur` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_editeur`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `editeur`
--

INSERT INTO `editeur` (`id_editeur`, `nom`) VALUES
(1, 'Gallimard'),
(2, 'Pocket'),
(3, 'J\'ai lu'),
(4, 'Le Livre de Poche'),
(5, 'Folio'),
(6, 'Guy Trédaniel'),
(7, 'First'),
(8, 'Milady'),
(9, 'Plon'),
(10, 'Un Monde Différent'),
(11, 'Nathan'),
(12, 'Pocket Jeunesse'),
(13, 'Pygmalion'),
(14, 'Bragelonne'),
(15, 'Robert Laffont'),
(16, 'Presses de la Cité');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `genre`
--

DROP TABLE IF EXISTS `genre`;
CREATE TABLE IF NOT EXISTS `genre` (
  `id_genre` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_genre`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `genre`
--

INSERT INTO `genre` (`id_genre`, `nom`) VALUES
(1, 'Science-Fiction'),
(2, 'Fantasy'),
(3, 'Policier'),
(4, 'Classique'),
(5, 'Essai'),
(6, 'Aventure'),
(7, 'Horreur'),
(8, 'Romance'),
(9, 'Dystopie'),
(10, 'Fantastique'),
(11, 'Voyage');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `livre`
--

DROP TABLE IF EXISTS `livre`;
CREATE TABLE IF NOT EXISTS `livre` (
  `id_produit` int UNSIGNED NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isbn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resume` text COLLATE utf8mb4_unicode_ci,
  `etat` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `annee_publication` smallint DEFAULT NULL,
  `nb_pages` int DEFAULT NULL,
  `id_editeur` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_produit`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `id_editeur` (`id_editeur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `livre`
--

INSERT INTO `livre` (`id_produit`, `titre`, `isbn`, `resume`, `etat`, `annee_publication`, `nb_pages`, `id_editeur`) VALUES
(1, 'Vingt mille lieues sous les mers', '9782253006329', 'Le professeur Pierre Aronnax, son fidèle domestique Conseil et le harponneur Ned Land se lancent à la poursuite d\'un monstre marin gigantesque. Ils sont capturés par le capitaine Nemo à bord du Nautilus, submersible révolutionnaire, et entament un voyage extraordinaire au cœur des océans du globe.', 'Bon état', 1870, 520, 4),
(2, 'Le Tour du monde en 80 jours', '9782253006206', 'À Londres, le flegmatique gentleman Phileas Fogg parie qu\'il accomplira le tour du monde en quatre-vingts jours. Accompagné de son nouveau valet français Passepartout, il affronte retards, tempêtes, bandits et éléphants pour remporter son pari audacieux.', 'Très bon état', 1873, 410, 4),
(4, 'Harry Potter à l\'école des sorciers', '9782070643028', 'Le jour de ses onze ans, Harry Potter apprend qu\'il est sorcier et rejoint l\'école de sorcellerie Poudlard. Entre cours de potions, matchs de Quidditch et mystères de couloirs, il découvre le secret de la pierre philosophale et l\'ombre de Voldemort.', 'Bon état', 1997, 350, 1),
(5, 'Harry Potter et la Chambre des secrets', '9782070643042', 'Pour sa deuxième année à Poudlard, Harry entend une voix menaçante dans les murs. La légendaire Chambre des Secrets est rouverte : des élèves sont pétrifiés, un monstre rôde. Harry devra affronter un passé vieux de mille ans.', 'Bon état', 1998, 360, 1),
(6, 'Le Crime de l’Orient-Express', '9782070360536', 'Coincé par la neige dans l\'Orient-Express, Hercule Poirot enquête sur le meurtre d\'un riche Américain. Chaque passager cache un secret, chaque alibi vacille : le célèbre détective démêle un huis clos d\'une précision diabolique.', 'Très bon état', 1934, 280, 2),
(7, 'L’Étranger', '9782070360024', 'Meursault, employé indolent à Alger, tue un homme « parce que le soleil tapait ». Son procès devient un réquisitoire contre son indifférence et la société qui exige un sens à tout acte humain.', 'État correct', 1942, 220, 5),
(8, 'La Main gauche de la nuit', '9782253061281', 'Genly Aï, émissaire terrien, arrive sur Géthen où les habitants sont androgynes. Dans une atmosphère glaciale et politique, il doit convaincre la planète d\'entrer dans l\'Ekumen tout en révisant sa notion de genre.', 'Bon état', 1969, 380, 2),
(9, 'Carrie', '9782266238845', 'Carrie White, adolescente timide et martyrisée par ses camarades et sa mère fanatique, développe des pouvoirs de télékinésie. Humiliée lors du bal de promo, elle déchaîne une vengeance apocalyptique.', 'Bon état', 1974, 320, 4),
(10, 'Orgueil et Préjugés', '9782253098010', 'Elizabeth Bennet, esprit vif et indépendant, affronte l\'orgueil apparent de Mr Darcy. Entre malentendus, fierté de classe et confidences volées, l\'amour triomphe des préjugés dans l\'Angleterre georgienne.', 'Très bon état', 1813, 480, 2),
(11, 'Les Misérables', '9782253004226', 'Après dix-neuf ans de bagne, Jean Valjean tente la rédemption sous un nouveau nom. Traqué par l\'impitoyable inspecteur Javert, il croise la misère de Paris, la révolte étudiante et l\'espoir d\'une fillette nommée Cosette.', 'Bon état', 1862, 1450, 5),
(12, 'Notre-Dame de Paris', '9782253006336', 'Au pied de la cathédrale, Quasimodo le sonneur bossu aime la belle bohémienne Esméralda, convoitée par le ténébreux archidiacre Frollo. Amour, fatalité et pierre gothique se mêlent dans un Moyen Âge halluciné.', 'Bon état', 1831, 600, 5),
(13, 'Le Comte de Monte-Cristo', '9782253003786', 'Trahi le jour de ses fiançailles, Edmond Dantès est emprisonné à vie au château d\'If. Il s\'évade, découvre un fabuleux trésor et, sous l\'identité du comte de Monte-Cristo, orchestre une vengeance savamment planifiée.', 'Bon état', 1844, 1243, 2),
(14, 'L’Appel de Cthulhu', '9782253070030', 'Un professeur découvre, dans les notes d\'un parent décédé, la trace d\'un culte tentaculaire voué à l\'être cosmique Cthulhu. Entre rêves fiévreux et statuettes impies, la réalité vacille.', 'Bon état', 1928, 160, 3),
(15, 'Dune', '9782266282992', 'Arrakis : planète désertique, seule source de l\'Épice qui prolonge la vie et ouvre la prescience. Le jeune Paul Atréides doit survivre à la trahison, unir les Fremen du désert et accomplir une destinée messianique.', 'Très bon état', 1965, 900, 2),
(16, 'Le Hobbit', '9782266238548', 'Bilbo Bessac, paisible hobbit, est recruté par treize Nains et le magicien Gandalf pour reprendre le trésor gardé par le dragon Smaug. Il trouve un anneau magique et la mesure de son propre courage.', 'Très bon état', 1937, 320, 4),
(17, '1984', '9782070368228', 'Dans l\'Océania de Big Brother, Winston Smith falsifie les archives mais rêve de vérité et d\'amour. Il découvre que la liberté individuelle est le crime ultime.', 'Bon état', 1949, 360, 1),
(18, 'La Peste', '9782070360023', 'Oran se ferme sur elle-même tandis qu\'une épidémie meurtrière s\'étend. Le docteur Rieux, témoin lucide, combat la maladie et l\'absurde condition humaine.', 'Bon état', 1947, 380, 5),
(19, 'Les Trois Mousquetaires', '9782253004565', 'Le fougueux d\'Artagnan, fraîchement arrivé à Paris, s\'allie aux mousquetaires Athos, Porthos et Aramis. Épées, intrigues de cour et amitié héroïque illuminent le règne de Louis XIII.', 'Bon état', 1844, 900, 2),
(20, 'Ça (It)', '9782266228136', 'À Derry, une entité maléfique qui adopte l\'apparence d\'un clown tue les enfants. Sept amis la combattent adolescents puis, vingt-sept ans plus tard, reviennent affronter l\'horreur adulte.', 'Bon état', 1986, 1130, 4),
(21, 'Misery', '9782266258171', 'Après un accident de voiture, l\'écrivain Paul Sheldon est enfermé par Annie Wilkes, sa « plus grande fan ». Elle exige qu\'il réécrive son dernier roman ; le huis clos tourne au cauchemar.', 'Bon état', 1987, 600, 4),
(22, 'Chroniques martiennes', '9782253109598', 'Cycle de nouvelles retraçant la colonisation poétique de Mars par les Terriens et la lente extinction de la civilisation martienne. Un chant mélancolique sur la conquête et la nostalgie.', 'Bon état', 1950, 300, 3),
(23, 'Le Meilleur des mondes', '9782253002875', 'Dans la société conditionnée d\'Alpha à Epsilon, le bonheur est chimique. L\'arrivée de John, un « Sauvage » élevé hors système, fait vaciller l\'apparente perfection.', 'Bon état', 1932, 310, 2),
(24, 'Silo', '9782264065313', 'L\'humanité survit dans un silo souterrain de 144 étages. Le shérif Holston découvre que sortir est un tabou soigneusement entretenu — la vérité pourrait faire s\'écrouler la société toute entière.', 'Très bon état', 2012, 560, 4),
(25, 'La Zone du Dehors', '9782846261991', 'Dans la cité-état de Cerclon, le bonheur est obligatoire. Un groupe d\'insoumis rêve d\'une vie libre dans la « Zone du Dehors » et fomente une révolte artistique et politique.', 'Bon état', 1999, 420, 5),
(26, 'The Shining', '9782266228129', 'Jack Torrance, écrivain en panne d\'inspiration, devient gardien hivernal de l\'hôtel Overlook, isolé dans les montagnes. L\'hôtel hante les couloirs… et son esprit.', 'Bon état', 1977, 670, 4),
(27, 'Les Robots', '9782253083799', 'Susan Calvin enquête sur des anomalies robotiques illustrant les Trois Lois. Les récits interrogent la frontière entre obéissance mécanique et conscience.', 'Bon état', 1950, 400, 3),
(28, 'Neuromancien', '9782070378227', 'Case, hacker déchu, se voit offrir de nouvelles synapses pour une ultime mission dans le cyberespace. Il découvre un complot impliquant IA, yakuza et astronefs orbitales.', 'Bon état', 1984, 320, 3),
(29, 'Hyperion', '9782253063289', 'Sept pèlerins convergent vers les Tombeaux du Temps sur la planète Hyperion pour affronter le Gritche. Chacun raconte son histoire, tissant une fresque vertigineuse.', 'Bon état', 1989, 740, 2),
(30, 'Le Nom du vent', '9782811214122', 'Kvothe, aubergiste discret, narre sa jeunesse : orphelin barde, élève surdoué à l\'Université, magicien, assassin… Portrait d\'une légende en devenir.', 'Très bon état', 2007, 720, 4),
(46, 'Le pouvoir du moment présent', '9782895180749', 'Eckhart Tolle montre comment l\'identification à notre mental nous prive de l\'ici et maintenant. En observant sans juger pensées et émotions, le lecteur accède à une paix durable.', 'Bon état', 1997, 256, 6),
(47, 'Ta deuxième vie commence…', '9782266277271', 'Camille, 38 ans, rencontre Claude, « routinologue ». Par des micro-défis joyeux, elle réenchante son quotidien et retrouve enthousiasme et créativité.', 'Bon état', 2015, 224, 2),
(48, 'Miracle Morning', '9782754087798', 'Hal Elrod propose une routine matinale en six pratiques — Silence, Affirmations, Visualisation, Exercice, Lecture, Écriture — pour transformer chaque journée et dépasser ses objectifs.', 'Bon état', 2012, 240, 7),
(49, 'Madame Bovary', '9782070413119', 'Emma, épouse d\'un médecin de province, étouffe dans la médiocrité. Ses rêves romantiques, ses amants et ses dettes la conduisent au désespoir et au scandale.', 'Bon état', 1856, 576, 4),
(50, 'The Notebook', '9782266275819', 'En Caroline du Nord, Noah Calhoun lit chaque jour à Allie, atteinte d\'Alzheimer, l\'histoire de leur amour né en 1940. Entre guerre, classes sociales et destin, leurs souvenirs renaissent.', 'Bon état', 1996, 288, 2),
(51, 'Me Before You', '9782811216508', 'Louisa Clark, jeune femme excentrique, devient aide de vie de Will Traynor, tétraplégique cynique. Une relation profonde s\'ébauche, ébranlant leurs certitudes sur la dignité et le choix.', 'Bon état', 2012, 480, 8),
(53, 'Petit traité de vie intérieure', '9782259212458', 'Frédéric Lenoir mêle philosophie, spiritualités orientales et anecdotes pour inviter le lecteur à cultiver joie, liberté intérieure et émerveillement malgré l\'incertitude du monde.', 'Bon état', 2010, 240, 9),
(54, 'Respire !', '9782290238511', 'Malo, golden-boy condamné, part en Thaïlande relever trente défis spirituels. Au programme : méditation, rituels chamaniques et leçon d\'amour inconditionnel.', 'Bon état', 2020, 352, 3),
(55, 'Les cinq blessures…', '9782266229485', 'Lise Bourbeau identifie rejet, abandon, humiliation, trahison, injustice comme les blessures qui modèlent nos masques. Prendre conscience de leur origine permet de s\'en libérer.', 'Bon état', 2000, 256, 2),
(56, 'Père riche, père pauvre', '9782892259685', 'Robert Kiyosaki oppose les conseils de son « père pauvre » salarié à ceux de son « père riche » investisseur. Il explique la différence entre actif et passif et encourage l\'entrepreneuriat.', 'Bon état', 1997, 320, 10),
(57, 'Start With Why', '9782378890257', 'Simon Sinek démontre que les organisations inspirantes commencent par la question « Pourquoi ». Le sens alimente la loyauté bien davantage que le produit ou la méthode.', 'Bon état', 2009, 256, 7),
(58, 'Influence et Manipulation', '9782266274201', 'Robert Cialdini présente six principes universels de persuasion — réciprocité, cohérence, preuve sociale, sympathie, autorité, rareté — illustrés d\'expériences et d\'exemples marketing.', 'Bon état', 1984, 384, 7),
(59, 'Les Orphelins Baudelaire – Tome 1', '9782092552219', 'Après l\'incendie qui tue leurs parents, Violet, Klaus et Sunny sont confiés au sinistre comte Olaf. Le vil tuteur tente de s\'emparer de leur fortune ; les enfants font preuve d\'ingéniosité.', 'Bon état', 1999, 192, 11),
(60, 'Hunger Games', '9782266257525', 'Dans la dictature de Panem, chaque district doit livrer deux adolescents aux Hunger Games, télé-réalité mortelle. Katniss se porte volontaire pour sauver sa sœur et devient l\'étincelle de la rébellion.', 'Bon état', 2008, 416, 12),
(61, 'Wonder', '9782266279763', 'August Pullman, né avec une malformation faciale, fait son entrée au collège. À travers plusieurs points de vue, le roman célèbre la bonté et l\'empathie.', 'Bon état', 2012, 416, 12),
(62, 'Liveship Traders', '9782290056979', 'La famille Vestrit possède Vivacia, vivenef consciente liée par le sang. À Terrilville, complots marchands, piraterie et anciens dragons bouleversent l\'océan d\'Azur.', 'Bon état', 1998, 448, 13),
(63, 'The Sword of Shannara', '9780345314253', 'Dans un monde post-apocalyptique devenu féerique, Shea Ohmsford, dernier héritier elfique, doit manier l\'Épée de Shannara pour vaincre le Seigneur des Ténèbres.', 'Bon état', 1977, 726, 14),
(64, 'Jonathan Strange & Mr Norrell', '9780747570453', 'Angleterre, 1806 : Mr Norrell ramène la magie oubliée. Son élève, Jonathan Strange, plus audacieux, libère des forces féeriques qui menacent le royaume et leur amitié.', 'Bon état', 2004, 1006, 15),
(71, 'The Last Unicorn', '9780451450524', 'Alors que les licornes ont disparu du monde, la dernière d\'entre elles part à leur recherche. Transformée en femme par un magicien, elle découvre l\'amour et sa propre mortalité.', 'Bon état', 1968, 294, 2),
(72, 'Into the Wild', '9780385486804', 'Récit véridique de Christopher McCandless qui, en 1992, abandonne possessions et identité pour vivre seul en Alaska. Une méditation sur la liberté, la nature et le prix des choix radicaux.', 'Très bon état', 1996, 256, 16),
(79, 'Gone Girl', '9782253194723', 'Le jour de leur cinquième anniversaire de mariage, Amy Dunne disparaît. Son mari, Nick, devient le principal suspect. Entre faux-semblants et manipulations médiatiques, la vérité se cache dans les pages du journal intime d\'Amy.', 'Bon état', 2012, 576, 4),
(80, 'Le Petit Prince', '9782070612758', 'Un aviateur échoué dans le désert rencontre un jeune prince venu d\'une autre planète. Une fable poétique et philosophique sur l\'amitié, l\'amour et le sens de la vie.', 'Très bon état', 1943, 96, 5);

--
-- Disparadores `livre`
--
DROP TRIGGER IF EXISTS `trg_audit_livre_update`;
DELIMITER $$
CREATE TRIGGER `trg_audit_livre_update` AFTER UPDATE ON `livre` FOR EACH ROW BEGIN
    IF IFNULL(OLD.titre,'') <> IFNULL(NEW.titre,'') THEN
        INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,champ_modifie,ancienne_valeur,nouvelle_valeur,id_admin)
        VALUES ('UPDATE','livre',OLD.id_produit,'titre',OLD.titre,NEW.titre,@current_admin_id);
    END IF;
    IF IFNULL(OLD.isbn,'') <> IFNULL(NEW.isbn,'') THEN
        INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,champ_modifie,ancienne_valeur,nouvelle_valeur,id_admin)
        VALUES ('UPDATE','livre',OLD.id_produit,'isbn',OLD.isbn,NEW.isbn,@current_admin_id);
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_unique_subtype_livre`;
DELIMITER $$
CREATE TRIGGER `trg_unique_subtype_livre` BEFORE INSERT ON `livre` FOR EACH ROW BEGIN
    IF EXISTS (SELECT 1 FROM bougie  WHERE id_produit = NEW.id_produit)
       OR EXISTS (SELECT 1 FROM coffret WHERE id_produit = NEW.id_produit) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produit déjà défini comme bougie ou coffret';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `livre_auteur`
--

DROP TABLE IF EXISTS `livre_auteur`;
CREATE TABLE IF NOT EXISTS `livre_auteur` (
  `id_produit` int UNSIGNED NOT NULL,
  `id_auteur` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_produit`,`id_auteur`),
  KEY `id_auteur` (`id_auteur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `livre_auteur`
--

INSERT INTO `livre_auteur` (`id_produit`, `id_auteur`) VALUES
(1, 1),
(2, 1),
(22, 2),
(27, 2),
(4, 3),
(5, 3),
(6, 4),
(7, 5),
(18, 5),
(8, 6),
(9, 7),
(20, 7),
(21, 7),
(26, 7),
(10, 8),
(11, 9),
(12, 9),
(13, 10),
(19, 10),
(16, 11),
(17, 12),
(23, 13),
(15, 14),
(28, 15),
(29, 16),
(30, 17),
(24, 18),
(25, 19),
(14, 20),
(46, 21),
(47, 22),
(48, 23),
(49, 24),
(50, 25),
(51, 26),
(53, 28),
(54, 29),
(55, 30),
(56, 31),
(57, 32),
(58, 33),
(59, 34),
(60, 35),
(61, 36),
(62, 37),
(63, 38),
(64, 39),
(71, 40),
(72, 41),
(79, 42),
(80, 43);

--
-- Disparadores `livre_auteur`
--
DROP TRIGGER IF EXISTS `trg_audit_livre_auteur_delete`;
DELIMITER $$
CREATE TRIGGER `trg_audit_livre_auteur_delete` AFTER DELETE ON `livre_auteur` FOR EACH ROW INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,ancienne_valeur,id_admin)
    VALUES ('UNLINK','livre_auteur',CONCAT(OLD.id_produit,'-',OLD.id_auteur),
            CONCAT('Livre #',OLD.id_produit,' délié de auteur #',OLD.id_auteur),@current_admin_id)
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_audit_livre_auteur_insert`;
DELIMITER $$
CREATE TRIGGER `trg_audit_livre_auteur_insert` AFTER INSERT ON `livre_auteur` FOR EACH ROW INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,nouvelle_valeur,id_admin)
    VALUES ('LINK','livre_auteur',CONCAT(NEW.id_produit,'-',NEW.id_auteur),
            CONCAT('Livre #',NEW.id_produit,' lié à auteur #',NEW.id_auteur),@current_admin_id)
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_livre_auteur_check`;
DELIMITER $$
CREATE TRIGGER `trg_livre_auteur_check` BEFORE INSERT ON `livre_auteur` FOR EACH ROW BEGIN
    IF (SELECT type FROM produit WHERE id_produit = NEW.id_produit) <> 'livre' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Seul un produit de type livre peut être lié à un auteur.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `livre_genre`
--

DROP TABLE IF EXISTS `livre_genre`;
CREATE TABLE IF NOT EXISTS `livre_genre` (
  `id_produit` int UNSIGNED NOT NULL,
  `id_genre` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_produit`,`id_genre`),
  KEY `idx_livregenre_gen` (`id_genre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `livre_genre`
--

INSERT INTO `livre_genre` (`id_produit`, `id_genre`) VALUES
(1, 1),
(8, 1),
(15, 1),
(17, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(27, 1),
(28, 1),
(29, 1),
(4, 2),
(5, 2),
(8, 2),
(16, 2),
(30, 2),
(6, 3),
(21, 3),
(79, 3),
(2, 4),
(7, 4),
(10, 4),
(11, 4),
(12, 4),
(18, 4),
(80, 4),
(7, 5),
(18, 5),
(46, 5),
(47, 5),
(48, 5),
(53, 5),
(54, 5),
(55, 5),
(56, 5),
(57, 5),
(58, 5),
(1, 6),
(2, 6),
(13, 6),
(15, 6),
(16, 6),
(19, 6),
(28, 6),
(59, 6),
(61, 6),
(72, 6),
(9, 7),
(14, 7),
(20, 7),
(21, 7),
(26, 7),
(10, 8),
(49, 8),
(50, 8),
(51, 8),
(12, 9),
(13, 9),
(17, 9),
(19, 9),
(23, 9),
(24, 9),
(25, 9),
(60, 9),
(14, 10),
(62, 10),
(63, 10),
(64, 10),
(71, 10),
(80, 10),
(72, 11);

--
-- Disparadores `livre_genre`
--
DROP TRIGGER IF EXISTS `trg_audit_livre_genre_delete`;
DELIMITER $$
CREATE TRIGGER `trg_audit_livre_genre_delete` AFTER DELETE ON `livre_genre` FOR EACH ROW INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,ancienne_valeur,id_admin)
    VALUES ('UNLINK','livre_genre',CONCAT(OLD.id_produit,'-',OLD.id_genre),
            CONCAT('Livre #',OLD.id_produit,' délié de genre #',OLD.id_genre),@current_admin_id)
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_audit_livre_genre_insert`;
DELIMITER $$
CREATE TRIGGER `trg_audit_livre_genre_insert` AFTER INSERT ON `livre_genre` FOR EACH ROW INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,nouvelle_valeur,id_admin)
    VALUES ('LINK','livre_genre',CONCAT(NEW.id_produit,'-',NEW.id_genre),
            CONCAT('Livre #',NEW.id_produit,' lié à genre #',NEW.id_genre),@current_admin_id)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messages_contact`
--

DROP TABLE IF EXISTS `messages_contact`;
CREATE TABLE IF NOT EXISTS `messages_contact` (
  `id_message` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_visiteur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_visiteur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sujet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_client` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_message`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notation_produit`
--

DROP TABLE IF EXISTS `notation_produit`;
CREATE TABLE IF NOT EXISTS `notation_produit` (
  `id_produit` int UNSIGNED NOT NULL,
  `id_client` int UNSIGNED NOT NULL,
  `note` tinyint UNSIGNED NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `date_notation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_produit`,`id_client`),
  KEY `id_client` (`id_client`)
) ;

--
-- Volcado de datos para la tabla `notation_produit`
--

INSERT INTO `notation_produit` (`id_produit`, `id_client`, `note`, `commentaire`, `date_notation`) VALUES
(1, 1, 5, 'Un classique indémodable !', '2025-07-14 13:07:47'),
(1, 2, 4, 'Très bon livre d\'aventure, je recommande.', '2025-07-14 13:07:47'),
(6, 2, 4, NULL, '2025-07-14 13:07:47'),
(10, 3, 5, 'Un de mes romans préférés, une histoire intemporelle.', '2025-07-14 13:07:47'),
(15, 1, 5, 'LE meilleur livre de SF, tout simplement.', '2025-07-14 13:07:47'),
(34, 2, 5, 'Cette bougie sent VRAIMENT le café, c\'est incroyable !', '2025-07-14 13:07:47'),
(35, 3, 3, 'Le parfum de rose est un peu trop poudré à mon goût.', '2025-07-14 13:07:47'),
(37, 1, 4, 'L\'odeur est très fraîche et agréable.', '2025-07-14 13:07:47');

--
-- Disparadores `notation_produit`
--
DROP TRIGGER IF EXISTS `trg_rating_after_delete`;
DELIMITER $$
CREATE TRIGGER `trg_rating_after_delete` AFTER DELETE ON `notation_produit` FOR EACH ROW BEGIN
    DECLARE avg_note   DECIMAL(3,2);
    DECLARE vote_count INT;
    SELECT AVG(note), COUNT(*) INTO avg_note, vote_count
      FROM notation_produit WHERE id_produit = OLD.id_produit;

    UPDATE produit
       SET note_moyenne = IFNULL(avg_note,0.00),
           nombre_votes = vote_count
     WHERE id_produit   = OLD.id_produit;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_rating_after_insert`;
DELIMITER $$
CREATE TRIGGER `trg_rating_after_insert` AFTER INSERT ON `notation_produit` FOR EACH ROW BEGIN
    DECLARE avg_note   DECIMAL(3,2);
    DECLARE vote_count INT;
    SELECT AVG(note), COUNT(*) INTO avg_note, vote_count
      FROM notation_produit WHERE id_produit = NEW.id_produit;

    UPDATE produit
       SET note_moyenne = avg_note,
           nombre_votes = vote_count
     WHERE id_produit   = NEW.id_produit;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_rating_after_update`;
DELIMITER $$
CREATE TRIGGER `trg_rating_after_update` AFTER UPDATE ON `notation_produit` FOR EACH ROW BEGIN
    DECLARE avg_note   DECIMAL(3,2);
    DECLARE vote_count INT;
    SELECT AVG(note), COUNT(*) INTO avg_note, vote_count
      FROM notation_produit WHERE id_produit = NEW.id_produit;

    UPDATE produit
       SET note_moyenne = avg_note,
           nombre_votes = vote_count
     WHERE id_produit   = NEW.id_produit;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paiement`
--

DROP TABLE IF EXISTS `paiement`;
CREATE TABLE IF NOT EXISTS `paiement` (
  `id_paiement` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `montant` decimal(10,2) NOT NULL,
  `moyen` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_paiement` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_commande` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_paiement`),
  KEY `id_commande` (`id_commande`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `paiement`
--

INSERT INTO `paiement` (`id_paiement`, `montant`, `moyen`, `date_paiement`, `statut`, `id_commande`) VALUES
(1, 22.19, 'CB', '2025-07-14 13:07:47', 'capturé', 1),
(2, 23.23, 'PayPal', '2025-07-14 13:07:47', 'capturé', 2),
(3, 22.95, 'CB', '2025-07-14 13:07:47', 'capturé', 3),
(4, 26.68, 'CB', '2025-07-14 13:07:47', 'autorisé', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produit`
--

DROP TABLE IF EXISTS `produit`;
CREATE TABLE IF NOT EXISTS `produit` (
  `id_produit` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` enum('livre','bougie','coffret') COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix_ht` decimal(10,2) NOT NULL,
  `tva_rate` decimal(4,2) NOT NULL,
  `image_url` varchar(400) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int UNSIGNED DEFAULT '0',
  `note_moyenne` decimal(3,2) DEFAULT '0.00',
  `nombre_votes` int UNSIGNED DEFAULT '0',
  PRIMARY KEY (`id_produit`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `produit`
--

INSERT INTO `produit` (`id_produit`, `type`, `prix_ht`, `tva_rate`, `image_url`, `stock`, `note_moyenne`, `nombre_votes`) VALUES
(1, 'livre', 7.50, 5.50, 'ressources/livres/vingt_mille.jpeg', 14, 4.50, 2),
(2, 'livre', 6.90, 5.50, 'ressources/livres/le_tour_du_monde.jpg', 12, 0.00, 0),
(4, 'livre', 9.90, 5.50, 'ressources/livres/harry_potter_1.jpg', 25, 0.00, 0),
(5, 'livre', 9.90, 5.50, 'ressources/livres/harry_potter_2.jpg', 30, 0.00, 0),
(6, 'livre', 7.80, 5.50, 'ressources/livres/le_crime_de_l_orient.jpg', 7, 4.00, 1),
(7, 'livre', 5.50, 5.50, 'ressources/livres/l_etranger.jpg', 14, 0.00, 0),
(8, 'livre', 8.90, 5.50, 'ressources/livres/la_main_gauche.jpeg', 22, 0.00, 0),
(9, 'livre', 8.50, 5.50, 'ressources/livres/carrie.jpg', 10, 0.00, 0),
(10, 'livre', 6.40, 5.50, 'ressources/livres/orgueil.jpg', 5, 5.00, 1),
(11, 'livre', 12.00, 5.50, 'ressources/livres/les_miserables.jpg', 33, 0.00, 0),
(12, 'livre', 8.00, 5.50, 'ressources/livres/notre_dame.jpg', 4, 0.00, 0),
(13, 'livre', 11.50, 5.50, 'ressources/livres/le_comte_de_monte_cristo.jpg', 11, 0.00, 0),
(14, 'livre', 7.20, 5.50, 'ressources/livres/l_appel.jpg', 18, 0.00, 0),
(15, 'livre', 10.50, 5.50, 'ressources/livres/dune.jpg', 19, 5.00, 1),
(16, 'livre', 8.90, 5.50, 'ressources/livres/le_hobbit.jpg', 27, 0.00, 0),
(17, 'livre', 7.00, 5.50, 'ressources/livres/1984.jpg', 19, 0.00, 0),
(18, 'livre', 6.80, 5.50, 'ressources/livres/la_peste.jpg', 7, 0.00, 0),
(19, 'livre', 9.50, 5.50, 'ressources/livres/les_trois_mousquetaires.jpg', 26, 0.00, 0),
(20, 'livre', 9.20, 5.50, 'ressources/livres/it.jpg', 21, 0.00, 0),
(21, 'livre', 8.80, 5.50, 'ressources/livres/misery.jpg', 13, 0.00, 0),
(22, 'livre', 7.90, 5.50, 'ressources/livres/chroniques.jpeg', 9, 0.00, 0),
(23, 'livre', 7.10, 5.50, 'ressources/livres/meilleur_monde.jpg', 16, 0.00, 0),
(24, 'livre', 9.80, 5.50, 'ressources/livres/silo.jpg', 8, 0.00, 0),
(25, 'livre', 8.60, 5.50, 'ressources/livres/la_zone_dehors.jpeg', 17, 0.00, 0),
(26, 'livre', 9.00, 5.50, 'ressources/livres/the_shining.jpg', 28, 0.00, 0),
(27, 'livre', 8.10, 5.50, 'ressources/livres/les_robots.jpg', 12, 0.00, 0),
(28, 'livre', 9.30, 5.50, 'ressources/livres/neuromacien.jpg', 15, 0.00, 0),
(29, 'livre', 10.20, 5.50, 'ressources/livres/hyperion.jpg', 5, 0.00, 0),
(30, 'livre', 11.00, 5.50, 'ressources/livres/le_nom_du_vent.jpg', 23, 0.00, 0),
(31, 'bougie', 12.50, 20.00, 'ressources/bougies/Bibli.png', 40, 0.00, 0),
(32, 'bougie', 13.00, 20.00, 'ressources/bougies/FORET.png', 35, 0.00, 0),
(33, 'bougie', 11.90, 20.00, 'ressources/bougies/pluie.png', 28, 0.00, 0),
(34, 'bougie', 12.50, 20.00, 'ressources/bougies/pause_cafe.png', 49, 5.00, 1),
(35, 'bougie', 13.50, 20.00, 'ressources/bougies/JARDIN.png', 44, 3.00, 1),
(36, 'bougie', 13.00, 20.00, 'ressources/bougies/AMBRE.png', 29, 0.00, 0),
(37, 'bougie', 11.90, 20.00, 'ressources/bougies/BRISE.png', 24, 4.00, 1),
(38, 'bougie', 12.80, 20.00, 'ressources/bougies/FEU.png', 42, 0.00, 0),
(39, 'bougie', 11.50, 20.00, 'ressources/bougies/serenite.png', 38, 0.00, 0),
(40, 'bougie', 13.20, 20.00, 'ressources/bougies/NEONS.png', 20, 0.00, 0),
(41, 'bougie', 12.80, 20.00, 'ressources/bougies/ORANGERIE.png', 30, 0.00, 0),
(42, 'bougie', 13.50, 20.00, 'ressources/bougies/CUIR.png', 25, 0.00, 0),
(43, 'bougie', 12.20, 20.00, 'ressources/bougies/TEMPETE.png', 32, 0.00, 0),
(44, 'bougie', 11.80, 20.00, 'ressources/bougies/CIDRE.png', 40, 0.00, 0),
(45, 'bougie', 13.00, 20.00, 'ressources/bougies/NUIT.png', 28, 0.00, 0),
(46, 'livre', 5.90, 5.50, 'ressources/livres/Le-pouvoir-du-moment-present.jpg', 12, 0.00, 0),
(47, 'livre', 4.40, 5.50, 'ressources/livres/Ta-deuxieme-vie.jpg', 12, 0.00, 0),
(48, 'livre', 5.90, 5.50, 'ressources/livres/miracle_morning.jpg', 12, 0.00, 0),
(49, 'livre', 8.40, 5.50, 'ressources/livres/madame_bovary.jpg', 12, 0.00, 0),
(50, 'livre', 5.40, 5.50, 'ressources/livres/the_notebook.jpg', 12, 0.00, 0),
(51, 'livre', 5.90, 5.50, 'ressources/livres/Me_before_you.jpg', 12, 0.00, 0),
(53, 'livre', 5.40, 5.50, 'ressources/livres/petit_traite_de_vie.jpg', 12, 0.00, 0),
(54, 'livre', 5.90, 5.50, 'ressources/livres/respire!.jpg', 12, 0.00, 0),
(55, 'livre', 5.90, 5.50, 'ressources/livres/les_5_blessures.jpg', 12, 0.00, 0),
(56, 'livre', 5.90, 5.50, 'ressources/livres/pere_riche_pere_pauvre.jpg', 12, 0.00, 0),
(57, 'livre', 5.90, 5.50, 'ressources/livres/start_with_why.jpg', 12, 0.00, 0),
(58, 'livre', 6.80, 5.50, 'ressources/livres/influence_et_manipulation.jpg', 12, 0.00, 0),
(59, 'livre', 4.40, 5.50, 'ressources/livres/les_orphelins.jpg', 12, 0.00, 0),
(60, 'livre', 5.90, 5.50, 'ressources/livres/hunger_games.jpg', 12, 0.00, 0),
(61, 'livre', 5.40, 5.50, 'ressources/livres/wonder.jpg', 12, 0.00, 0),
(62, 'livre', 8.10, 5.50, 'ressources/livres/liveship.jpg', 12, 0.00, 0),
(63, 'livre', 5.90, 5.50, 'ressources/livres/the_sword_of_shannara.jpg', 12, 0.00, 0),
(64, 'livre', 6.80, 5.50, 'ressources/livres/jonathan_strange.jpg', 12, 0.00, 0),
(65, 'bougie', 12.20, 20.00, 'ressources/bougies/ZEN.png', 35, 0.00, 0),
(66, 'bougie', 12.60, 20.00, 'ressources/bougies/ETREINTE.png', 40, 0.00, 0),
(67, 'bougie', 12.30, 20.00, 'ressources/bougies/SORTILEGE.png', 38, 0.00, 0),
(68, 'bougie', 11.90, 20.00, 'ressources/bougies/VENTE.png', 42, 0.00, 0),
(69, 'bougie', 12.10, 20.00, 'ressources/bougies/LAVANDE.png', 36, 0.00, 0),
(70, 'bougie', 12.50, 20.00, 'ressources/bougies/ECLAT.png', 34, 0.00, 0),
(71, 'livre', 6.40, 5.50, 'ressources/livres/the_last_unicorn.jpg', 12, 0.00, 0),
(72, 'livre', 7.10, 5.50, 'ressources/livres/into_the_wild.jpg', 12, 0.00, 0),
(73, 'coffret', 17.92, 5.50, 'ressources/coffrets/coffret_pouvoir.png', 15, 0.00, 0),
(74, 'coffret', 17.54, 5.50, 'ressources/coffrets/coffret_orgueil.png', 15, 0.00, 0),
(75, 'coffret', 20.76, 5.50, 'ressources/coffrets/coffret_unicorn.png', 15, 0.00, 0),
(76, 'coffret', 17.92, 5.50, 'ressources/coffrets/coffret_into.png', 15, 0.00, 0),
(77, 'coffret', 16.59, 5.50, 'ressources/coffrets/coffret_petit_traite.png', 15, 0.00, 0),
(78, 'coffret', 17.54, 5.50, 'ressources/coffrets/coffret_pere.png', 15, 0.00, 0),
(79, 'livre', 9.80, 5.50, 'ressources/livres/gone_girl.webp', 20, 0.00, 0),
(80, 'livre', 7.20, 5.50, 'ressources/livres/le_petit_prince.jpg', 30, 0.00, 0),
(81, 'coffret', 17.92, 5.50, 'ressources/coffrets/coffret_gone_girl.png', 15, 0.00, 0),
(82, 'coffret', 17.54, 5.50, 'ressources/coffrets/coffret_petit_prince.png', 15, 0.00, 0);

--
-- Disparadores `produit`
--
DROP TRIGGER IF EXISTS `trg_audit_produit_delete`;
DELIMITER $$
CREATE TRIGGER `trg_audit_produit_delete` AFTER DELETE ON `produit` FOR EACH ROW INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,ancienne_valeur,id_admin)
    VALUES ('DELETE','produit',OLD.id_produit,CONCAT('Produit de type ',OLD.type,' supprimé'),@current_admin_id)
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_audit_produit_insert`;
DELIMITER $$
CREATE TRIGGER `trg_audit_produit_insert` AFTER INSERT ON `produit` FOR EACH ROW INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,nouvelle_valeur,id_admin)
    VALUES ('CREATE','produit',NEW.id_produit,CONCAT('Produit de type ',NEW.type),@current_admin_id)
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_audit_produit_update`;
DELIMITER $$
CREATE TRIGGER `trg_audit_produit_update` AFTER UPDATE ON `produit` FOR EACH ROW BEGIN
    IF OLD.prix_ht <> NEW.prix_ht THEN
        INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,champ_modifie,ancienne_valeur,nouvelle_valeur,id_admin)
        VALUES ('UPDATE','produit',OLD.id_produit,'prix_ht',OLD.prix_ht,NEW.prix_ht,@current_admin_id);
    END IF;
    IF OLD.tva_rate <> NEW.tva_rate THEN
        INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,champ_modifie,ancienne_valeur,nouvelle_valeur,id_admin)
        VALUES ('UPDATE','produit',OLD.id_produit,'tva_rate',OLD.tva_rate,NEW.tva_rate,@current_admin_id);
    END IF;
    IF OLD.stock <> NEW.stock THEN
        INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,champ_modifie,ancienne_valeur,nouvelle_valeur,id_admin)
        VALUES ('UPDATE','produit',OLD.id_produit,'stock',OLD.stock,NEW.stock,@current_admin_id);
    END IF;
    IF IFNULL(OLD.image_url,'') <> IFNULL(NEW.image_url,'') THEN
        INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,champ_modifie,ancienne_valeur,nouvelle_valeur,id_admin)
        VALUES ('UPDATE','produit',OLD.id_produit,'image_url',OLD.image_url,NEW.image_url,@current_admin_id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produit_tag`
--

DROP TABLE IF EXISTS `produit_tag`;
CREATE TABLE IF NOT EXISTS `produit_tag` (
  `id_produit` int UNSIGNED NOT NULL,
  `id_tag` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_produit`,`id_tag`),
  KEY `idx_prodtag_tag` (`id_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `produit_tag`
--

INSERT INTO `produit_tag` (`id_produit`, `id_tag`) VALUES
(1, 1),
(2, 1),
(13, 1),
(15, 1),
(16, 1),
(19, 1),
(29, 1),
(30, 1),
(32, 1),
(36, 1),
(37, 1),
(42, 1),
(59, 1),
(60, 1),
(62, 1),
(63, 1),
(68, 1),
(76, 1),
(5, 2),
(6, 2),
(16, 2),
(21, 2),
(24, 2),
(30, 2),
(33, 2),
(34, 2),
(36, 2),
(42, 2),
(44, 2),
(79, 2),
(81, 2),
(4, 3),
(5, 3),
(62, 3),
(63, 3),
(64, 3),
(71, 3),
(75, 3),
(31, 4),
(34, 4),
(38, 4),
(39, 4),
(44, 4),
(66, 4),
(67, 4),
(69, 4),
(75, 4),
(7, 5),
(9, 5),
(14, 5),
(18, 5),
(20, 5),
(26, 5),
(33, 5),
(36, 5),
(71, 5),
(79, 5),
(81, 5),
(8, 6),
(15, 6),
(17, 6),
(22, 6),
(23, 6),
(24, 6),
(25, 6),
(27, 6),
(28, 6),
(29, 6),
(40, 6),
(45, 6),
(60, 6),
(10, 7),
(11, 7),
(12, 7),
(31, 7),
(35, 7),
(49, 7),
(50, 7),
(63, 7),
(80, 7),
(82, 7),
(32, 8),
(35, 8),
(39, 8),
(41, 8),
(43, 8),
(62, 8),
(68, 8),
(72, 8),
(76, 8),
(2, 9),
(6, 9),
(11, 9),
(12, 9),
(13, 9),
(19, 9),
(38, 9),
(42, 9),
(64, 9),
(7, 10),
(8, 10),
(17, 10),
(18, 10),
(22, 10),
(23, 10),
(25, 10),
(27, 10),
(31, 10),
(33, 10),
(45, 10),
(46, 10),
(47, 10),
(48, 10),
(51, 10),
(53, 10),
(54, 10),
(55, 10),
(56, 10),
(57, 10),
(58, 10),
(61, 10),
(64, 10),
(65, 10),
(69, 10),
(72, 10),
(73, 10),
(75, 10),
(77, 10),
(80, 10),
(82, 10),
(9, 11),
(14, 11),
(20, 11),
(21, 11),
(26, 11),
(34, 11),
(38, 11),
(43, 11),
(59, 11),
(60, 11),
(79, 11),
(81, 11),
(1, 12),
(4, 12),
(32, 12),
(37, 12),
(40, 12),
(41, 12),
(43, 12),
(45, 12),
(46, 12),
(47, 12),
(62, 12),
(63, 12),
(68, 12),
(70, 12),
(71, 12),
(72, 12),
(76, 12),
(10, 13),
(35, 13),
(49, 13),
(50, 13),
(51, 13),
(74, 13),
(44, 14),
(66, 14),
(67, 14),
(74, 14),
(37, 15),
(39, 15),
(40, 15),
(41, 15),
(65, 15),
(68, 15),
(69, 15),
(70, 15),
(76, 15),
(78, 15),
(8, 16),
(15, 16),
(22, 16),
(29, 16),
(31, 16),
(39, 16),
(45, 16),
(46, 16),
(53, 16),
(55, 16),
(73, 16),
(77, 16),
(2, 17),
(4, 17),
(5, 17),
(16, 17),
(19, 17),
(30, 17),
(47, 17),
(54, 17),
(61, 17),
(74, 17),
(48, 18),
(56, 18),
(57, 18),
(78, 18),
(7, 19),
(9, 19),
(11, 19),
(12, 19),
(13, 19),
(18, 19),
(20, 19),
(21, 19),
(49, 19),
(7, 20),
(10, 20),
(11, 20),
(12, 20),
(30, 20),
(33, 20),
(41, 20),
(50, 20),
(51, 20),
(61, 20),
(80, 20),
(82, 20),
(39, 21),
(53, 21),
(55, 21),
(65, 21),
(69, 21),
(73, 21),
(77, 21),
(56, 22),
(57, 22),
(78, 22),
(6, 23),
(7, 23),
(9, 23),
(14, 23),
(18, 23),
(20, 23),
(21, 23),
(26, 23),
(27, 23),
(58, 23),
(79, 23),
(81, 23),
(59, 24),
(17, 25),
(23, 25),
(24, 25),
(25, 25),
(60, 25),
(34, 26),
(37, 26),
(40, 26),
(43, 26),
(65, 26),
(70, 26),
(73, 26),
(78, 26),
(10, 27),
(16, 27),
(32, 27),
(35, 27),
(38, 27),
(41, 27),
(66, 27),
(74, 27),
(77, 27),
(36, 28),
(44, 28),
(67, 28),
(75, 28);

--
-- Disparadores `produit_tag`
--
DROP TRIGGER IF EXISTS `trg_audit_produit_tag_delete`;
DELIMITER $$
CREATE TRIGGER `trg_audit_produit_tag_delete` AFTER DELETE ON `produit_tag` FOR EACH ROW INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,ancienne_valeur,id_admin)
    VALUES ('UNLINK','produit_tag',CONCAT(OLD.id_produit,'-',OLD.id_tag),
            CONCAT('Produit #',OLD.id_produit,' délié de tag #',OLD.id_tag),@current_admin_id)
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_audit_produit_tag_insert`;
DELIMITER $$
CREATE TRIGGER `trg_audit_produit_tag_insert` AFTER INSERT ON `produit_tag` FOR EACH ROW INSERT INTO audit_logs(type_action,table_concernee,enregistrement_id,nouvelle_valeur,id_admin)
    VALUES ('LINK','produit_tag',CONCAT(NEW.id_produit,'-',NEW.id_tag),
            CONCAT('Produit #',NEW.id_produit,' lié à tag #',NEW.id_tag),@current_admin_id)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retour_produit`
--

DROP TABLE IF EXISTS `retour_produit`;
CREATE TABLE IF NOT EXISTS `retour_produit` (
  `id_demande` int UNSIGNED NOT NULL,
  `id_produit` int UNSIGNED NOT NULL,
  `quantite` int UNSIGNED NOT NULL,
  `raison` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_demande`,`id_produit`),
  KEY `id_produit` (`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `retour_produit`
--

INSERT INTO `retour_produit` (`id_demande`, `id_produit`, `quantite`, `raison`) VALUES
(1, 37, 1, 'Produit endommagé pendant le transport'),
(2, 10, 1, 'Erreur sur l\'édition'),
(2, 35, 1, 'Ne correspond pas à mes attentes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `statut_commande`
--

DROP TABLE IF EXISTS `statut_commande`;
CREATE TABLE IF NOT EXISTS `statut_commande` (
  `id_statut_commande` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_statut_commande`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `statut_commande`
--

INSERT INTO `statut_commande` (`id_statut_commande`, `libelle`) VALUES
(5, 'Annulée'),
(1, 'En attente de paiement'),
(2, 'En préparation'),
(3, 'Expédiée'),
(4, 'Livrée'),
(6, 'Retour demandé');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `statut_demande`
--

DROP TABLE IF EXISTS `statut_demande`;
CREATE TABLE IF NOT EXISTS `statut_demande` (
  `id_statut_demande` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_statut_demande`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `statut_demande`
--

INSERT INTO `statut_demande` (`id_statut_demande`, `libelle`) VALUES
(7, 'Clôturée'),
(4, 'Colis en attente de réception'),
(5, 'Colis reçu'),
(2, 'Demande acceptée'),
(1, 'Demande initiée'),
(3, 'Demande refusée'),
(6, 'Remboursement effectué');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tag`
--

DROP TABLE IF EXISTS `tag`;
CREATE TABLE IF NOT EXISTS `tag` (
  `id_tag` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_tag` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_tag`),
  UNIQUE KEY `nom_tag` (`nom_tag`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tag`
--

INSERT INTO `tag` (`id_tag`, `nom_tag`) VALUES
(1, 'Aventure'),
(21, 'Bien-être'),
(22, 'Business'),
(7, 'Classique'),
(4, 'Cosy'),
(27, 'Douceur'),
(19, 'Dramatique'),
(25, 'Dystopie'),
(20, 'Émotion'),
(26, 'Énergisant'),
(28, 'Épicé'),
(12, 'Évasion'),
(17, 'Feel-good'),
(15, 'Frais & Végétal'),
(11, 'Frisson'),
(6, 'Futuriste'),
(14, 'Gourmand'),
(9, 'Historique'),
(24, 'Humour'),
(3, 'Magie'),
(18, 'Motivation'),
(2, 'Mystère'),
(8, 'Nature'),
(23, 'Psychologie'),
(10, 'Réflexion'),
(13, 'Romantique'),
(5, 'Sombre'),
(16, 'Spiritualité');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `adresse`
--
ALTER TABLE `adresse`
  ADD CONSTRAINT `adresse_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON DELETE CASCADE;

--
-- Filtros para la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `administrateur` (`id_admin`) ON DELETE SET NULL;

--
-- Filtros para la tabla `bougie`
--
ALTER TABLE `bougie`
  ADD CONSTRAINT `bougie_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE;

--
-- Filtros para la tabla `coffret`
--
ALTER TABLE `coffret`
  ADD CONSTRAINT `coffret_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE,
  ADD CONSTRAINT `coffret_ibfk_2` FOREIGN KEY (`id_produit_livre`) REFERENCES `produit` (`id_produit`),
  ADD CONSTRAINT `coffret_ibfk_3` FOREIGN KEY (`id_produit_bougie`) REFERENCES `produit` (`id_produit`),
  ADD CONSTRAINT `coffret_ibfk_4` FOREIGN KEY (`id_categorie_coffret`) REFERENCES `categorie_coffret` (`id_categorie_coffret`) ON DELETE SET NULL;

--
-- Filtros para la tabla `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`),
  ADD CONSTRAINT `commande_ibfk_2` FOREIGN KEY (`id_adresse_livraison`) REFERENCES `adresse` (`id_adresse`),
  ADD CONSTRAINT `commande_ibfk_3` FOREIGN KEY (`id_statut_commande`) REFERENCES `statut_commande` (`id_statut_commande`);

--
-- Filtros para la tabla `commande_details`
--
ALTER TABLE `commande_details`
  ADD CONSTRAINT `commande_details_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE CASCADE,
  ADD CONSTRAINT `commande_details_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`);

--
-- Filtros para la tabla `demande_retour`
--
ALTER TABLE `demande_retour`
  ADD CONSTRAINT `demande_retour_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`),
  ADD CONSTRAINT `demande_retour_ibfk_2` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON DELETE CASCADE,
  ADD CONSTRAINT `demande_retour_ibfk_3` FOREIGN KEY (`id_statut_demande`) REFERENCES `statut_demande` (`id_statut_demande`);

--
-- Filtros para la tabla `livre`
--
ALTER TABLE `livre`
  ADD CONSTRAINT `livre_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE,
  ADD CONSTRAINT `livre_ibfk_2` FOREIGN KEY (`id_editeur`) REFERENCES `editeur` (`id_editeur`);

--
-- Filtros para la tabla `livre_auteur`
--
ALTER TABLE `livre_auteur`
  ADD CONSTRAINT `livre_auteur_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `livre` (`id_produit`) ON DELETE CASCADE,
  ADD CONSTRAINT `livre_auteur_ibfk_2` FOREIGN KEY (`id_auteur`) REFERENCES `auteur` (`id_auteur`) ON DELETE CASCADE;

--
-- Filtros para la tabla `livre_genre`
--
ALTER TABLE `livre_genre`
  ADD CONSTRAINT `livre_genre_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `livre` (`id_produit`) ON DELETE CASCADE,
  ADD CONSTRAINT `livre_genre_ibfk_2` FOREIGN KEY (`id_genre`) REFERENCES `genre` (`id_genre`) ON DELETE CASCADE;

--
-- Filtros para la tabla `messages_contact`
--
ALTER TABLE `messages_contact`
  ADD CONSTRAINT `messages_contact_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON DELETE SET NULL;

--
-- Filtros para la tabla `notation_produit`
--
ALTER TABLE `notation_produit`
  ADD CONSTRAINT `notation_produit_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE,
  ADD CONSTRAINT `notation_produit_ibfk_2` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON DELETE CASCADE;

--
-- Filtros para la tabla `paiement`
--
ALTER TABLE `paiement`
  ADD CONSTRAINT `paiement_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE CASCADE;

--
-- Filtros para la tabla `produit_tag`
--
ALTER TABLE `produit_tag`
  ADD CONSTRAINT `produit_tag_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE,
  ADD CONSTRAINT `produit_tag_ibfk_2` FOREIGN KEY (`id_tag`) REFERENCES `tag` (`id_tag`) ON DELETE CASCADE;

--
-- Filtros para la tabla `retour_produit`
--
ALTER TABLE `retour_produit`
  ADD CONSTRAINT `retour_produit_ibfk_1` FOREIGN KEY (`id_demande`) REFERENCES `demande_retour` (`id_demande`) ON DELETE CASCADE,
  ADD CONSTRAINT `retour_produit_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
