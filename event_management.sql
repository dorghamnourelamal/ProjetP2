-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 19 juin 2026 à 22:57
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
-- Base de données : `event_management`
--

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date_event` date NOT NULL,
  `heure` time NOT NULL,
  `heure_fin` time DEFAULT NULL,
  `places_disponibles` int(11) NOT NULL,
  `prix` decimal(8,2) NOT NULL DEFAULT 0.00,
  `salle_id` bigint(20) UNSIGNED NOT NULL,
  `statut` enum('actif','annulé') NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `titre`, `description`, `date_event`, `heure`, `heure_fin`, `places_disponibles`, `prix`, `salle_id`, `statut`, `created_at`, `updated_at`) VALUES
(2, 'Gala — Hommage à Jacques Brel', 'Soirée hommage à Jacques Brel avec les plus grandes voix de la chanson française dans la mythique salle de l\'Olympia.', '2026-07-28', '20:30:00', '23:00:00', 1996, 65.00, 2, 'actif', '2026-06-12 13:51:34', '2026-06-19 18:47:07'),
(3, 'VivaTech 2026', 'Le plus grand salon européen dédié à la technologie et à l\'innovation. Startups, grands groupes et investisseurs réunis pour 2 jours de conférences.', '2026-09-17', '09:00:00', '19:00:00', 3700, 120.00, 3, 'actif', '2026-06-12 13:51:34', '2026-06-19 18:51:36'),
(4, 'Festival du Film Fantastique', 'Projections exclusives des meilleurs films de science-fiction et fantastique de l\'année, suivies de rencontres avec les réalisateurs.', '2026-08-14', '19:00:00', '23:59:00', 2650, 18.00, 4, 'actif', '2026-06-12 13:51:34', '2026-06-19 18:48:29'),
(5, 'Nuits Sonores Lyon', 'Festival de musiques électroniques et cultures émergentes. DJs et live acts européens pour une nuit inoubliable à Lyon.', '2026-09-05', '22:00:00', '23:00:00', 1500, 28.00, 5, 'actif', '2026-06-12 13:51:34', '2026-06-19 18:53:14'),
(7, 'Forum Africain de l\'Innovation', 'Conférence internationale réunissant entrepreneurs, investisseurs et décideurs africains autour du numérique et des nouvelles technologies.', '2026-08-03', '09:00:00', '18:00:00', 3000, 80.00, 7, 'actif', '2026-06-12 13:51:34', '2026-06-19 18:47:33'),
(8, 'Festival de Carthage', 'L\'un des plus grands festivals de la Méditerranée dans le cadre antique de Carthage : musique, danse, théâtre et spectacles sous les étoiles.', '2026-07-25', '21:00:00', '23:03:00', 7931, 50.00, 8, 'actif', '2026-06-12 13:51:34', '2026-06-19 18:46:36'),
(9, 'Soirée Malouf & Musique Andalouse', 'Concert de malouf tunisien et musique andalouse, un voyage musical entre les deux rives de la Méditerranée.', '2026-08-30', '20:30:00', '23:00:00', 1190, 25.00, 9, 'actif', '2026-06-12 13:51:34', '2026-06-19 18:54:25'),
(10, 'Salon Business', 'Rencontres B2B et tables rondes entre entrepreneurs tunisiens et européens. Networking, pitch de startups et ateliers pratiques.', '2026-09-20', '09:00:00', '17:00:00', 2500, 60.00, 10, 'actif', '2026-06-12 13:51:34', '2026-06-19 18:53:34');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_06_125637_create_salles_table', 1),
(5, '2026_06_06_125638_create_events_table', 1),
(6, '2026_06_06_125639_create_reservations_table', 1),
(7, '2026_06_06_125642_create_tickets_table', 1),
(8, '2026_06_07_000001_add_role_to_users_table', 1),
(9, '2026_06_07_000002_add_fields_to_tickets_table', 1),
(10, '2026_06_07_125250_create_personal_access_tokens_table', 1),
(11, '2026_06_11_125730_add_duree_minutes_to_events_table', 1),
(12, '2026_06_11_135625_replace_duree_minutes_by_heure_fin_in_events_table', 1),
(13, '2026_06_12_000001_add_statut_to_events_table', 2);

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'api-token', 'dc91f0eb35db289b990bf90ea32371fda6ca33c004f72142b63da70fa2ade369', '[\"*\"]', '2026-06-19 17:52:20', NULL, '2026-06-12 13:53:08', '2026-06-19 17:52:20'),
(2, 'App\\Models\\User', 3, 'api-token', '4ffee21d41aada008306ec22468721388aeeeb7c26b1d72a776ededc753c2206', '[\"*\"]', NULL, NULL, '2026-06-12 13:54:48', '2026-06-12 13:54:48'),
(3, 'App\\Models\\User', 3, 'api-token', '16620d5d6793b3b2b9189f364cc758e9bb0d75a814789504902b603c682bd924', '[\"*\"]', '2026-06-12 14:07:24', NULL, '2026-06-12 13:55:00', '2026-06-12 14:07:24'),
(4, 'App\\Models\\User', 4, 'api-token', 'abf81a439ecefe8f277ba706a984d0eb67e387b569ae99292c19795718cc76b8', '[\"*\"]', NULL, NULL, '2026-06-19 17:27:49', '2026-06-19 17:27:49'),
(5, 'App\\Models\\User', 4, 'api-token', 'b9a83a77483d0491f701bb9df33b7600a86bf819d443af12312da60e163c86e3', '[\"*\"]', '2026-06-19 17:51:03', NULL, '2026-06-19 17:28:00', '2026-06-19 17:51:03'),
(6, 'App\\Models\\User', 1, 'api-token', 'bda3b58e892b1bee4a32eb89752272024f2dc8ad350f41c14953918ae25fa00d', '[\"*\"]', '2026-06-19 18:05:37', NULL, '2026-06-19 17:58:37', '2026-06-19 18:05:37'),
(8, 'App\\Models\\User', 5, 'api-token', 'e4e9d2ba4d5a6cc3a94d34d28dc97e8df2deda4ebac5a0b8eff7314b2aa8bdd0', '[\"*\"]', NULL, NULL, '2026-06-19 18:01:39', '2026-06-19 18:01:39'),
(10, 'App\\Models\\User', 1, 'api-token', 'fa6b2ba8b7198c64199e077741826159988ec79a851ee6d93d3030c2b7b53c79', '[\"*\"]', '2026-06-19 18:54:31', NULL, '2026-06-19 18:06:47', '2026-06-19 18:54:31'),
(11, 'App\\Models\\User', 3, 'api-token', '8eab65428f01fcd10cef5655f6ccb1f4c82b77f37f0bae7865943a904e76ac85', '[\"*\"]', '2026-06-19 18:54:30', NULL, '2026-06-19 18:39:11', '2026-06-19 18:54:30');

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom_client` varchar(255) NOT NULL,
  `email_client` varchar(255) NOT NULL,
  `nombre_places` int(11) NOT NULL DEFAULT 1,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `nom_client`, `email_client`, `nombre_places`, `event_id`, `created_at`, `updated_at`) VALUES
(2, 'nour', 'nourslim508@gmail.com', 1, 8, '2026-06-12 13:59:40', '2026-06-12 13:59:40'),
(3, 'nour', 'nour.dorgham@utbm.fr', 68, 8, '2026-06-19 17:50:53', '2026-06-19 18:00:21'),
(4, 'Amal', 'dorghamnourelamal@gmail.com', 4, 2, '2026-06-19 18:02:08', '2026-06-19 18:02:08'),
(6, 'nour', 'nourslim508@gmail.com', 10, 9, '2026-06-19 18:54:07', '2026-06-19 18:54:25');

-- --------------------------------------------------------

--
-- Structure de la table `salles`
--

CREATE TABLE `salles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `capacite` int(11) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `salles`
--

INSERT INTO `salles` (`id`, `nom`, `capacite`, `adresse`, `created_at`, `updated_at`) VALUES
(1, 'Zénith de Paris', 6300, '211 Avenue Jean Jaurès, 75019 Paris, France', '2026-06-12 13:51:34', '2026-06-12 13:51:34'),
(2, 'L\'Olympia', 2000, '28 Boulevard des Capucines, 75009 Paris, France', '2026-06-12 13:51:34', '2026-06-12 13:51:34'),
(3, 'Palais des Congrès de Paris', 3700, '2 Place de la Porte Maillot, 75017 Paris, France', '2026-06-12 13:51:34', '2026-06-12 13:51:34'),
(4, 'Le Grand Rex', 2650, '1 Boulevard Poissonnière, 75002 Paris, France', '2026-06-12 13:51:34', '2026-06-12 13:51:34'),
(5, 'Le Transbordeur', 1500, '3 Boulevard Stalingrad, 69100 Villeurbanne, Lyon, France', '2026-06-12 13:51:34', '2026-06-12 13:51:34'),
(6, 'Théâtre de l\'Opéra de Tunis', 1500, 'Avenue Habib Bourguiba, 1000 Tunis, Tunisie', '2026-06-12 13:51:34', '2026-06-12 13:51:34'),
(7, 'Palais des Congrès de Tunis', 3000, 'Avenue de la Ligue des États Arabes, 1053 Tunis, Tunisie', '2026-06-12 13:51:34', '2026-06-12 13:51:34'),
(8, 'Théâtre Antique de Carthage', 8000, 'Site archéologique de Carthage, 2016 Carthage, Tunisie', '2026-06-12 13:51:34', '2026-06-12 13:51:34'),
(9, 'Salle de Fêtes El Mechtel', 1200, 'Avenue Ouled Haffouz, 1002 Tunis, Tunisie', '2026-06-12 13:51:34', '2026-06-12 13:51:34'),
(10, 'Palais des Congrès de Sousse', 2500, 'Boulevard du Maghreb, 4000 Sousse, Tunisie', '2026-06-12 13:51:34', '2026-06-12 13:51:34');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reservation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'standard',
  `prix` decimal(8,2) NOT NULL DEFAULT 0.00,
  `statut` varchar(255) NOT NULL DEFAULT 'valide'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tickets`
--

INSERT INTO `tickets` (`id`, `created_at`, `updated_at`, `reservation_id`, `code`, `type`, `prix`, `statut`) VALUES
(6, '2026-06-12 13:59:40', '2026-06-12 14:00:48', 2, 'OESGYULSDI', 'standard', 50.00, 'annulé'),
(7, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'FVZRY88CXV', 'standard', 50.00, 'valide'),
(8, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'KZUQ1ELISE', 'standard', 50.00, 'valide'),
(9, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'DTDKGY1JOR', 'standard', 50.00, 'valide'),
(10, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'VEIHCC2NTJ', 'standard', 50.00, 'valide'),
(11, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'T1THRDBWOB', 'standard', 50.00, 'valide'),
(12, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'WVP9QEQQHQ', 'standard', 50.00, 'valide'),
(13, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'CF0UJERQMA', 'standard', 50.00, 'valide'),
(14, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'XP0E3DEZQK', 'standard', 50.00, 'valide'),
(15, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'B8CD3KC0FR', 'standard', 50.00, 'valide'),
(16, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'GYWHSLWFIM', 'standard', 50.00, 'valide'),
(17, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'ZOZDHUWZFX', 'standard', 50.00, 'valide'),
(18, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'UX40TQHOBU', 'standard', 50.00, 'valide'),
(19, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'DNTQIM8FT4', 'standard', 50.00, 'valide'),
(20, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'L8NQRAJIMY', 'standard', 50.00, 'valide'),
(21, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'TVZNVJAW0V', 'standard', 50.00, 'valide'),
(22, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'WVKXDZWYOJ', 'standard', 50.00, 'valide'),
(23, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, '6FPAZD9KOM', 'standard', 50.00, 'valide'),
(24, '2026-06-19 17:50:53', '2026-06-19 18:00:21', 3, 'MGBI3IY2A5', 'standard', 50.00, 'valide'),
(25, '2026-06-19 18:00:21', '2026-06-19 18:00:21', 3, 'YYSY12SQTY', 'standard', 50.00, 'valide'),
(26, '2026-06-19 18:00:21', '2026-06-19 18:00:21', 3, 'MIQWR5KW2S', 'standard', 50.00, 'valide'),
(27, '2026-06-19 18:00:21', '2026-06-19 18:00:21', 3, 'LP67NJ8EPY', 'standard', 50.00, 'valide'),
(28, '2026-06-19 18:00:21', '2026-06-19 18:00:21', 3, 'D0DIPCU6NC', 'standard', 50.00, 'valide'),
(29, '2026-06-19 18:00:21', '2026-06-19 18:00:21', 3, 'OJSHHXSB5E', 'standard', 50.00, 'valide'),
(30, '2026-06-19 18:00:21', '2026-06-19 18:00:21', 3, 'ONDGBIG2IY', 'standard', 50.00, 'valide'),
(31, '2026-06-19 18:00:21', '2026-06-19 18:00:21', 3, 'KNC5CNGRC6', 'standard', 50.00, 'valide'),
(32, '2026-06-19 18:00:21', '2026-06-19 18:00:21', 3, 'QN9Q67AMWR', 'standard', 50.00, 'valide'),
(33, '2026-06-19 18:00:21', '2026-06-19 18:00:21', 3, 'IPCQHLADBD', 'standard', 50.00, 'valide'),
(34, '2026-06-19 18:00:21', '2026-06-19 18:00:21', 3, 'XCMTNML1MC', 'standard', 50.00, 'valide'),
(35, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'HJB9EKFNHI', 'standard', 50.00, 'valide'),
(36, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'VUTDQHMLSV', 'standard', 50.00, 'valide'),
(37, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'FPSMIJZTM8', 'standard', 50.00, 'valide'),
(38, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'GZWDOPBWHZ', 'standard', 50.00, 'valide'),
(39, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'ADFSW8XPUB', 'standard', 50.00, 'valide'),
(40, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'TFQYOHCZT8', 'standard', 50.00, 'valide'),
(41, '2026-06-19 18:00:22', '2026-06-19 18:08:34', 3, '1C4CMDD2U1', 'standard', 50.00, 'utilisé'),
(42, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'TQKAT2OEHN', 'standard', 50.00, 'valide'),
(43, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'CAMRDUGH08', 'standard', 50.00, 'valide'),
(44, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'ROPA3ARK9Z', 'standard', 50.00, 'valide'),
(45, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'NAL8YMW8YZ', 'standard', 50.00, 'valide'),
(46, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'NQKFFVU5SV', 'standard', 50.00, 'valide'),
(47, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'EPNLAYX3FA', 'standard', 50.00, 'valide'),
(48, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'M8QJCLLK6R', 'standard', 50.00, 'valide'),
(49, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'TMATDCVKSU', 'standard', 50.00, 'valide'),
(50, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'IN64479RRW', 'standard', 50.00, 'valide'),
(51, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, '70EW9UNIWT', 'standard', 50.00, 'valide'),
(52, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'FGLV2JORPV', 'standard', 50.00, 'valide'),
(53, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'LFG4RKIAW7', 'standard', 50.00, 'valide'),
(54, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'PTXQ2JR58G', 'standard', 50.00, 'valide'),
(55, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'DLJYR1DEPD', 'standard', 50.00, 'valide'),
(56, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'GJ31EEFLE5', 'standard', 50.00, 'valide'),
(57, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'UIGMUR2AJE', 'standard', 50.00, 'valide'),
(58, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'SFECOQR299', 'standard', 50.00, 'valide'),
(59, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'U6FT0RRHYW', 'standard', 50.00, 'valide'),
(60, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'QZEAJQ3LAV', 'standard', 50.00, 'valide'),
(61, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'H6CLP8I3ZO', 'standard', 50.00, 'valide'),
(62, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'YSI4HUNJGK', 'standard', 50.00, 'valide'),
(63, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, '5BXECFPDIO', 'standard', 50.00, 'valide'),
(64, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'KLRP4YGMA9', 'standard', 50.00, 'valide'),
(65, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'SLJRV2WSUE', 'standard', 50.00, 'valide'),
(66, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'D4PVANFW3Y', 'standard', 50.00, 'valide'),
(67, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'KKGNNXOLXI', 'standard', 50.00, 'valide'),
(68, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, '4T61JDBQOF', 'standard', 50.00, 'valide'),
(69, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'IXINIDYWHA', 'standard', 50.00, 'valide'),
(70, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'LTEOJIFEEZ', 'standard', 50.00, 'valide'),
(71, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'YMPZJNMRE2', 'standard', 50.00, 'valide'),
(72, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'C2YJZGKERP', 'standard', 50.00, 'valide'),
(73, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, '1CTADGRR5G', 'standard', 50.00, 'valide'),
(74, '2026-06-19 18:00:22', '2026-06-19 18:00:22', 3, 'TNXPDCCJGP', 'standard', 50.00, 'valide'),
(75, '2026-06-19 18:02:08', '2026-06-19 18:02:08', 4, 'DJ8AQFM7VY', 'standard', 65.00, 'valide'),
(76, '2026-06-19 18:02:08', '2026-06-19 18:02:08', 4, 'MTVKID5RE2', 'standard', 65.00, 'valide'),
(77, '2026-06-19 18:02:08', '2026-06-19 18:02:08', 4, 'XH8OXESMC1', 'standard', 65.00, 'valide'),
(78, '2026-06-19 18:02:08', '2026-06-19 18:02:08', 4, 'Y6HOQWTDHL', 'standard', 65.00, 'valide'),
(82, '2026-06-19 18:54:07', '2026-06-19 18:54:25', 6, 'OLNZFEBXT5', 'standard', 25.00, 'valide'),
(83, '2026-06-19 18:54:07', '2026-06-19 18:54:25', 6, '2WO0M07WOU', 'standard', 25.00, 'valide'),
(84, '2026-06-19 18:54:07', '2026-06-19 18:54:25', 6, 'AZOOSHE1Z1', 'standard', 25.00, 'valide'),
(85, '2026-06-19 18:54:07', '2026-06-19 18:54:25', 6, 'J9ETDQ1CYW', 'standard', 25.00, 'valide'),
(86, '2026-06-19 18:54:25', '2026-06-19 18:54:25', 6, 'M7CTBB7RDG', 'standard', 25.00, 'valide'),
(87, '2026-06-19 18:54:25', '2026-06-19 18:54:25', 6, 'H9SCZPFM48', 'standard', 25.00, 'valide'),
(88, '2026-06-19 18:54:25', '2026-06-19 18:54:25', 6, 'TWYITAQLSI', 'standard', 25.00, 'valide'),
(89, '2026-06-19 18:54:25', '2026-06-19 18:54:25', 6, '3QM1DLCBC8', 'standard', 25.00, 'valide'),
(90, '2026-06-19 18:54:25', '2026-06-19 18:54:25', 6, 'N5Y0Z9DLRS', 'standard', 25.00, 'valide'),
(91, '2026-06-19 18:54:25', '2026-06-19 18:54:25', 6, '9DVKWV6G6T', 'standard', 25.00, 'valide');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrateur', 'admin@evenements.test', 'admin', NULL, '$2y$12$.6UsLeuBCbf4nffTn/1p7esKaasxSNWGC2wWz37Vx5II2tQkX1wIC', NULL, '2026-06-12 13:51:25', '2026-06-12 13:51:25'),
(2, 'Utilisateur Test', 'user@evenements.test', 'user', NULL, '$2y$12$Ne1GaVpMD.0W2wGusU3Teu73/x5FRSzTm2Mm9/dtCYgIRgISBdrGO', NULL, '2026-06-12 13:51:25', '2026-06-12 13:51:25'),
(3, 'nour', 'nourslim508@gmail.com', 'user', NULL, '$2y$12$e0cukwbaHuAdHqOAUS75nuCgRCkduG.VmrWCVJfnJcbK0t5P4ayma', NULL, '2026-06-12 13:54:48', '2026-06-19 18:38:42'),
(4, 'nour', 'nour.dorgham@utbm.fr', 'user', NULL, '$2y$12$AwgxY294YXa9p6.Nx0CluemhUFActVrfksBi.aDrfNqASghYd4nGC', NULL, '2026-06-19 17:27:49', '2026-06-19 17:27:49'),
(5, 'Amal', 'dorghamnourelamal@gmail.com', 'user', NULL, '$2y$12$xW8CAYn0V2/R35yhB2LQSeZwgh8ndLpn9apziUSyGyuo14FI.Aid6', NULL, '2026-06-19 18:01:39', '2026-06-19 18:01:39');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `events_salle_id_foreign` (`salle_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservations_event_id_foreign` (`event_id`);

--
-- Index pour la table `salles`
--
ALTER TABLE `salles`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tickets_code_unique` (`code`),
  ADD KEY `tickets_reservation_id_foreign` (`reservation_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `salles`
--
ALTER TABLE `salles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_salle_id_foreign` FOREIGN KEY (`salle_id`) REFERENCES `salles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
