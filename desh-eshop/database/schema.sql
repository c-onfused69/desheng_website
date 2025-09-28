-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2025 at 06:12 PM
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
-- Database: `desh_eshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','editor') DEFAULT 'editor',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `name`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@deshengineering.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 1, '2025-09-24 11:01:27', '2025-09-24 11:01:27');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `parent_id`, `image`, `is_active`, `sort_order`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 'Web Development', 'web-development', 'Web development tools and resources', NULL, NULL, 1, 0, NULL, NULL, '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(2, 'Mobile Apps', 'mobile-apps', 'Mobile application templates and tools', NULL, NULL, 1, 0, NULL, NULL, '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(3, 'Graphics & Design', 'graphics-design', 'Design templates and graphics', NULL, NULL, 1, 0, NULL, NULL, '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(4, 'Digital Marketing', 'digital-marketing', 'Marketing tools and templates', NULL, NULL, 1, 0, NULL, NULL, '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(5, 'Business Tools', 'business-tools', 'Business and productivity tools', NULL, NULL, 1, 0, NULL, NULL, '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(6, 'Air Conditioners', 'air-conditioners', 'Complete range of air conditioning units from leading brands', NULL, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(7, 'Carrier AC', 'carrier-ac', 'High-quality Carrier AC products', 6, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(8, 'Chigo AC', 'chigo-ac', 'High-quality Chigo AC products', 6, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(9, 'General AC', 'general-ac', 'High-quality General AC products', 6, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(10, 'Gree AC', 'gree-ac', 'High-quality Gree AC products', 6, NULL, 1, 4, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(11, 'Green Aire', 'green-aire', 'High-quality Green Aire products', 6, NULL, 1, 5, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(12, 'Midea AC', 'midea-ac', 'High-quality Midea AC products', 6, NULL, 1, 6, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(13, 'York AC', 'york-ac', 'High-quality York AC products', 6, NULL, 1, 7, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(14, 'LG AC', 'lg-ac', 'High-quality LG AC products', 6, NULL, 1, 8, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(15, 'Mitsubishi AC', 'mitsubishi-ac', 'High-quality Mitsubishi AC products', 6, NULL, 1, 9, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(16, 'Panasonic AC', 'panasonic-ac', 'High-quality Panasonic AC products', 6, NULL, 1, 10, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(17, 'Daikin AC', 'daikin-ac', 'High-quality Daikin AC products', 6, NULL, 1, 11, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(18, 'Accessories', 'accessories', 'Essential AC accessories and components', NULL, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(19, 'Filter Drier', 'filter-drier', 'High-quality Filter Drier products', 18, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(20, 'Massive', 'massive', 'High-quality Massive products', 18, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(21, 'Water Treatment Kits', 'water-treatment-kits', 'High-quality Water Treatment Kits products', 18, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(22, 'York Accessories', 'york-accessories', 'High-quality York Accessories products', 18, NULL, 1, 4, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(23, 'Remote Controllers', 'remote-controllers', 'High-quality Remote Controllers products', 18, NULL, 1, 5, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(24, 'Thermostats', 'thermostats', 'High-quality Thermostats products', 18, NULL, 1, 6, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(25, 'Mounting Kits & Brackets', 'mounting-kits-brackets', 'High-quality Mounting Kits & Brackets products', 18, NULL, 1, 7, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(26, 'Cables & Wiring', 'cables-wiring', 'High-quality electrical cables and wiring solutions', NULL, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(27, 'ABS Cable', 'abs-cable', 'High-quality ABS Cable products', 26, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(28, 'ABS Cable 40/76 3 Core', 'abs-cable-4076-3-core', 'High-quality ABS Cable 40/76 3 Core products', 26, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(29, 'ABS Cable 70/76 3 Core', 'abs-cable-7076-3-core', 'High-quality ABS Cable 70/76 3 Core products', 26, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(30, 'BBS Cable', 'bbs-cable', 'High-quality BBS Cable products', 26, NULL, 1, 4, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(31, 'BBS Cable 23/76 3 Core', 'bbs-cable-2376-3-core', 'High-quality BBS Cable 23/76 3 Core products', 26, NULL, 1, 5, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(32, 'BBS Cable 40/76 3 Core', 'bbs-cable-4076-3-core', 'High-quality BBS Cable 40/76 3 Core products', 26, NULL, 1, 6, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(33, 'BBS Cable 70/76 3 Core', 'bbs-cable-7076-3-core', 'High-quality BBS Cable 70/76 3 Core products', 26, NULL, 1, 7, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(34, 'Bizli Cable', 'bizli-cable', 'High-quality Bizli Cable products', 26, NULL, 1, 8, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(35, 'BRB Cable', 'brb-cable', 'High-quality BRB Cable products', 26, NULL, 1, 9, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(36, 'Partex Cable', 'partex-cable', 'High-quality Partex Cable products', 26, NULL, 1, 10, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(37, 'Partex Cable 23/76 3 Core', 'partex-cable-2376-3-core', 'High-quality Partex Cable 23/76 3 Core products', 26, NULL, 1, 11, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(38, 'Partex Cable 40/76 3 Core', 'partex-cable-4076-3-core', 'High-quality Partex Cable 40/76 3 Core products', 26, NULL, 1, 12, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(39, 'Partex Cable 70/76 3 Core', 'partex-cable-7076-3-core', 'High-quality Partex Cable 70/76 3 Core products', 26, NULL, 1, 13, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(40, 'SQ Cable', 'sq-cable', 'High-quality SQ Cable products', 26, NULL, 1, 14, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(41, 'Compressors', 'compressors', 'Premium compressors from trusted manufacturers', NULL, NULL, 1, 4, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(42, 'Bitzer', 'bitzer', 'High-quality Bitzer products', 41, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(43, 'Bristol', 'bristol', 'High-quality Bristol products', 41, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(44, 'Chigo', 'chigo', 'High-quality Chigo products', 41, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(45, 'Copeland', 'copeland', 'High-quality Copeland products', 41, NULL, 1, 4, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(46, 'Daikin', 'daikin', 'High-quality Daikin products', 41, NULL, 1, 5, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(47, 'Danfoss', 'danfoss', 'High-quality Danfoss products', 41, NULL, 1, 6, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(48, 'Donper', 'donper', 'High-quality Donper products', 41, NULL, 1, 7, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(49, 'GMCC', 'gmcc', 'High-quality GMCC products', 41, NULL, 1, 8, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(50, 'Gree', 'gree', 'High-quality Gree products', 41, NULL, 1, 9, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(51, 'Hitachi', 'hitachi', 'High-quality Hitachi products', 41, NULL, 1, 10, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(52, 'Invotech', 'invotech', 'High-quality Invotech products', 41, NULL, 1, 11, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(53, 'Kulthorn', 'kulthorn', 'High-quality Kulthorn products', 41, NULL, 1, 12, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(54, 'LG', 'lg', 'High-quality LG products', 41, NULL, 1, 13, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(55, 'Mitsubishi', 'mitsubishi', 'High-quality Mitsubishi products', 41, NULL, 1, 14, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(56, 'Panasonic', 'panasonic', 'High-quality Panasonic products', 41, NULL, 1, 15, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(57, 'Secop', 'secop', 'High-quality Secop products', 41, NULL, 1, 16, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(58, 'Tecumseh', 'tecumseh', 'High-quality Tecumseh products', 41, NULL, 1, 17, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(59, 'Walton', 'walton', 'High-quality Walton products', 41, NULL, 1, 18, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(60, 'Compressor Oils', 'compressor-oils', 'Specialized lubricants for optimal compressor performance', NULL, NULL, 1, 5, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(61, 'Bitzer Compressor Oil', 'bitzer-compressor-oil', 'High-quality Bitzer Compressor Oil products', 60, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(62, 'B100', 'b100', 'High-quality B100 products', 60, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(63, 'B 5.2', 'b-52', 'High-quality B 5.2 products', 60, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(64, 'B320SH', 'b320sh', 'High-quality B320SH products', 60, NULL, 1, 4, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(65, 'BSE 170', 'bse-170', 'High-quality BSE 170 products', 60, NULL, 1, 5, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(66, 'BSE 32', 'bse-32', 'High-quality BSE 32 products', 60, NULL, 1, 6, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(67, 'Danfoss Compressor Oil', 'danfoss-compressor-oil', 'High-quality Danfoss Compressor Oil products', 60, NULL, 1, 7, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(68, '160 SZ POE', '160-sz-poe', 'High-quality 160 SZ POE products', 60, NULL, 1, 8, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(69, '160P Mineral', '160p-mineral', 'High-quality 160P Mineral products', 60, NULL, 1, 9, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(70, '175 PZ POE', '175-pz-poe', 'High-quality 175 PZ POE products', 60, NULL, 1, 10, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(71, '320 SZ POE', '320-sz-poe', 'High-quality 320 SZ POE products', 60, NULL, 1, 11, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(72, 'BOGE Compressor Oil OZ 120', 'boge-compressor-oil-oz-120', 'High-quality BOGE Compressor Oil OZ 120 products', 60, NULL, 1, 12, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(73, 'Emkarate Compressor Oil RL 68H', 'emkarate-compressor-oil-rl-68h', 'High-quality Emkarate Compressor Oil RL 68H products', 60, NULL, 1, 13, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(74, 'Suniso Compressor Oil 4GS', 'suniso-compressor-oil-4gs', 'High-quality Suniso Compressor Oil 4GS products', 60, NULL, 1, 14, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(75, 'Copper & Tubing', 'copper-tubing', 'High-grade copper pipes and tubing systems', NULL, NULL, 1, 6, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(76, 'Copper Straight Pipe', 'copper-straight-pipe', 'High-quality Copper Straight Pipe products', 75, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(77, 'Copper Coils', 'copper-coils', 'High-quality Copper Coils products', 75, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(78, 'Copper Fittings', 'copper-fittings', 'High-quality Copper Fittings products', 75, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(79, 'Capillary Tubes', 'capillary-tubes', 'High-quality Capillary Tubes products', 75, NULL, 1, 4, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(80, 'Insulation Materials', 'insulation-materials', 'Thermal insulation solutions for HVAC systems', NULL, NULL, 1, 7, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(81, 'Insulation Pipe', 'insulation-pipe', 'High-quality Insulation Pipe products', 80, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(82, 'Rubber Insulation Sheet', 'rubber-insulation-sheet', 'High-quality Rubber Insulation Sheet products', 80, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(83, 'Aluminum Foil Insulation', 'aluminum-foil-insulation', 'High-quality Aluminum Foil Insulation products', 80, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(84, 'Duct Insulation Materials', 'duct-insulation-materials', 'High-quality Duct Insulation Materials products', 80, NULL, 1, 4, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(85, 'Refrigerants & Chemicals', 'refrigerants-chemicals', 'Refrigerants and maintenance chemicals for AC systems', NULL, NULL, 1, 8, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(86, 'R22 Refrigerant', 'r22-refrigerant', 'High-quality R22 Refrigerant products', 85, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(87, 'R32 Refrigerant', 'r32-refrigerant', 'High-quality R32 Refrigerant products', 85, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(88, 'R134a Refrigerant', 'r134a-refrigerant', 'High-quality R134a Refrigerant products', 85, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(89, 'R404a Refrigerant', 'r404a-refrigerant', 'High-quality R404a Refrigerant products', 85, NULL, 1, 4, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(90, 'R407c Refrigerant', 'r407c-refrigerant', 'High-quality R407c Refrigerant products', 85, NULL, 1, 5, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(91, 'R410a Refrigerant', 'r410a-refrigerant', 'High-quality R410a Refrigerant products', 85, NULL, 1, 6, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(92, 'R507 Refrigerant', 'r507-refrigerant', 'High-quality R507 Refrigerant products', 85, NULL, 1, 7, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(93, 'Cleaning Chemicals (Coil Cleaners, Descalers)', 'cleaning-chemicals-coil-cleaners-descalers', 'High-quality Cleaning Chemicals (Coil Cleaners, Descalers) products', 85, NULL, 1, 8, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(94, 'Spare Parts', 'spare-parts', 'Genuine spare parts for all AC systems', NULL, NULL, 1, 9, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(95, 'VRF Spare Parts', 'vrf-spare-parts', 'High-quality VRF Spare Parts products', 94, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(96, 'Chiller Spare Parts', 'chiller-spare-parts', 'High-quality Chiller Spare Parts products', 94, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(97, 'Split AC Spare Parts', 'split-ac-spare-parts', 'High-quality Split AC Spare Parts products', 94, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(98, 'Window AC Spare Parts', 'window-ac-spare-parts', 'High-quality Window AC Spare Parts products', 94, NULL, 1, 4, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(99, 'PCB Boards & Controllers', 'pcb-boards-controllers', 'High-quality PCB Boards & Controllers products', 94, NULL, 1, 5, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(100, 'Sensors & Switches', 'sensors-switches', 'High-quality Sensors & Switches products', 94, NULL, 1, 6, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(101, 'Motors & Fans', 'motors-fans', 'High-quality Motors & Fans products', 94, NULL, 1, 7, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(102, 'Air Treatment & Ventilation', 'air-treatment-ventilation', 'Air quality and ventilation solutions', NULL, NULL, 1, 10, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(103, 'Dehumidifiers', 'dehumidifiers', 'High-quality Dehumidifiers products', 102, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(104, 'Humidifiers', 'humidifiers', 'High-quality Humidifiers products', 102, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(105, 'Air Purifiers', 'air-purifiers', 'High-quality Air Purifiers products', 102, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(106, 'Ventilation Fans', 'ventilation-fans', 'High-quality Ventilation Fans products', 102, NULL, 1, 4, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(107, 'Fresh Air Units', 'fresh-air-units', 'High-quality Fresh Air Units products', 102, NULL, 1, 5, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(108, 'Chillers', 'chillers', 'Industrial and commercial chiller systems', NULL, NULL, 1, 11, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(109, 'Water-Cooled Chillers', 'water-cooled-chillers', 'High-quality Water-Cooled Chillers products', 108, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(110, 'Air-Cooled Chillers', 'air-cooled-chillers', 'High-quality Air-Cooled Chillers products', 108, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(111, 'Chiller Accessories & Parts', 'chiller-accessories-parts', 'High-quality Chiller Accessories & Parts products', 108, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(112, 'VRF/VRV Systems', 'vrfvrv-systems', 'Variable Refrigerant Flow systems and components', NULL, NULL, 1, 12, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(113, 'Indoor Units', 'indoor-units', 'High-quality Indoor Units products', 112, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(114, 'Outdoor Units', 'outdoor-units', 'High-quality Outdoor Units products', 112, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(115, 'Controllers & Modules', 'controllers-modules', 'High-quality Controllers & Modules products', 112, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(116, 'Tools & Equipment', 'tools-equipment', 'Professional HVAC tools and equipment', NULL, NULL, 1, 13, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(117, 'Vacuum Pumps', 'vacuum-pumps', 'High-quality Vacuum Pumps products', 116, NULL, 1, 1, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(118, 'Manifold Gauges', 'manifold-gauges', 'High-quality Manifold Gauges products', 116, NULL, 1, 2, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(119, 'Leak Detectors', 'leak-detectors', 'High-quality Leak Detectors products', 116, NULL, 1, 3, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(120, 'Flaring & Swaging Tools', 'flaring-swaging-tools', 'High-quality Flaring & Swaging Tools products', 116, NULL, 1, 4, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(121, 'Brazing & Welding Kits', 'brazing-welding-kits', 'High-quality Brazing & Welding Kits products', 116, NULL, 1, 5, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42'),
(122, 'Pipe Benders & Cutters', 'pipe-benders-cutters', 'High-quality Pipe Benders & Cutters products', 116, NULL, 1, 6, NULL, NULL, '2025-09-25 07:59:42', '2025-09-25 07:59:42');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('fixed','percentage') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `minimum_amount` decimal(10,2) DEFAULT 0.00,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usage`
--

CREATE TABLE `coupon_usage` (
  `id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `downloads`
--

CREATE TABLE `downloads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `download_token` varchar(255) NOT NULL,
  `download_count` int(11) DEFAULT 0,
  `max_downloads` int(11) DEFAULT 5,
  `expires_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_downloaded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `answer` text NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'How do I download my purchased products?', 'After successful payment, you can download your products from the Orders page in your account dashboard. You will also receive a download link via email.', 'Downloads', 1, 0, '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(2, 'What payment methods do you accept?', 'We accept all major credit cards, PayPal, and Razorpay for secure payments.', 'Payment', 1, 0, '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(3, 'Can I get a refund?', 'Yes, we offer a 30-day money-back guarantee on all digital products. Please contact our support team for refund requests.', 'Refunds', 1, 0, '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(4, 'How many times can I download a product?', 'Most products allow up to 5 downloads within 30 days of purchase. Check individual product pages for specific download limits.', 'Downloads', 1, 0, '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(5, 'Do you offer technical support?', 'Yes, we provide technical support for all our products. You can contact us through the support ticket system or email.', 'Support', 1, 0, '2025-09-24 11:01:27', '2025-09-24 11:01:27');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `status` enum('pending','processing','completed','cancelled','refunded') DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_gateway` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'USD',
  `billing_name` varchar(255) DEFAULT NULL,
  `billing_email` varchar(255) DEFAULT NULL,
  `billing_phone` varchar(20) DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_state` varchar(100) DEFAULT NULL,
  `billing_country` varchar(100) DEFAULT NULL,
  `billing_postal_code` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_title` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `digital_file_path` varchar(255) DEFAULT NULL,
  `demo_url` varchar(255) DEFAULT NULL,
  `download_limit` int(11) DEFAULT 5,
  `download_expiry_days` int(11) DEFAULT 30,
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `views_count` int(11) DEFAULT 0,
  `sales_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Desh Engineering', 'text', 'Website name', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(2, 'site_logo', '/assets/images/logo.png', 'text', 'Website logo path', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(3, 'currency', 'USD', 'text', 'Default currency', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(4, 'tax_rate', '0', 'number', 'Tax rate percentage', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(5, 'payment_gateway', 'stripe', 'text', 'Default payment gateway', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(6, 'stripe_publishable_key', '', 'text', 'Stripe publishable key', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(7, 'stripe_secret_key', '', 'text', 'Stripe secret key', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(8, 'razorpay_key_id', '', 'text', 'Razorpay key ID', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(9, 'razorpay_key_secret', '', 'text', 'Razorpay key secret', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(10, 'paypal_client_id', '', 'text', 'PayPal client ID', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(11, 'paypal_client_secret', '', 'text', 'PayPal client secret', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(12, 'smtp_host', '', 'text', 'SMTP host', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(13, 'smtp_port', '587', 'text', 'SMTP port', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(14, 'smtp_username', '', 'text', 'SMTP username', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(15, 'smtp_password', '', 'text', 'SMTP password', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(16, 'from_email', 'noreply@deshengineering.com', 'text', 'From email address', '2025-09-24 11:01:27', '2025-09-24 11:01:27'),
(17, 'from_name', 'Desh Engineering', 'text', 'From name', '2025-09-24 11:01:27', '2025-09-24 11:01:27');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('open','in_progress','closed') DEFAULT 'open',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `admin_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified` tinyint(1) DEFAULT 0,
  `email_verification_token` varchar(255) DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_categories_parent` (`parent_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `downloads`
--
ALTER TABLE `downloads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `download_token` (`download_token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product_order` (`user_id`,`product_id`,`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `downloads`
--
ALTER TABLE `downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD CONSTRAINT `coupon_usage_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usage_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usage_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `downloads`
--
ALTER TABLE `downloads`
  ADD CONSTRAINT `downloads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `downloads_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `downloads_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
