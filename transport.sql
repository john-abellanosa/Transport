-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2025 at 05:57 PM
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
-- Database: `transport`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$12$oFSAEC2s01rcLnaZ4JlsFuPzZApziUqYma54dtqq0NYnI2dAEz2nq', '2025-09-13 13:43:17', '2025-09-13 13:43:17');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `municipality` varchar(255) NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `password` varchar(255) NOT NULL,
  `is_temporary_password` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `email`, `address`, `owner`, `contact`, `municipality`, `cost`, `status`, `password`, `is_temporary_password`, `created_at`, `updated_at`) VALUES
(58, 'Apo Cement Corporation', 'kylereganion187@gmail.com', 'Naga dapitt', 'Apo Cement Corporation', '9895665656', 'Lapu-lapu City', 500.00, 'active', '$2y$12$Xwp1bIDel.h8Umn9Pa0ZhO5XSvno4EdUPg8Ht2hXo/10ngjy05B6u', 0, '2025-09-18 19:09:53', '2025-09-20 06:16:34'),
(59, 'Akai Foods, Inc.', 'johnlloydabellanosa0@gmail.com', 'Cebu dapit', 'Akai Foods, Inc.', '9566565656', 'Mandaue City', 300.00, 'active', '$2y$12$s13AlI1CadPZgKXw8HBRZOwoTtGyXOjLPL8miWcx7N.n78uGpYuQu', 0, '2025-09-18 22:14:13', '2025-09-18 22:19:54'),
(60, 'Cebu Logistics Co.', 'kylereganion85@gmail.com', 'Barangay Cabadiangan', 'Cebu Logistics Co.', '9825122112', 'Alcantara', 200.00, 'active', '$2y$12$vuQF9SxUoNfOfS10bOEHAusNOoMIOAiyY3jxUXGFus7H.0Bt4AYnu', 0, '2025-09-19 21:00:28', '2025-09-19 21:05:26'),
(61, 'DoubleDragon Properties Corp.', 'kylereganionn@gmail.com', 'Danao dapit', 'Kyle Reganion', '9566232331', 'Cebu City', 400.00, 'active', '$2y$12$ZRMD2Co3mVvkkjYVLF0Ls.XNkGdP/hQTlAyMqgfRnb3.sq.7MVjWy', 1, '2025-09-20 09:22:39', '2025-09-20 09:22:39');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_temporary_password` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `company_id`, `name`, `email`, `number`, `address`, `password`, `is_temporary_password`, `created_at`, `updated_at`) VALUES
(53, 58, 'Kyle Reganion', 'kylereganionn@gmail.com', '9821213131', 'Opon Lapu-lapu City', '$2y$12$LJmyYfJ1dmKQ9aQ0PrcxQuuhaZKsgXWDO/PobfXeBgLC83yFoFevC', 0, '2025-09-21 15:50:12', '2025-09-21 15:50:50'),
(55, 59, 'John Lloyd Abellanosa', 'johnlloydabellanosa0@gmail.com', '9123456789', 'Barangay Atabay', '$2y$12$Sb/3Rz9Cwoz9S69hRaLU2u5Co722Pfu.dtzLUcwFtVcl9s8vdJbH.', 0, '2025-09-22 00:10:18', '2025-09-22 03:20:35'),
(56, 59, 'Che Diongson', 'che@gmail.com', '9123458978', 'Labogon', '$2y$12$B6asMiOZhvyuGrHKwBukC.1V/8c1t/6gZe.19WFCFrP898Sw2.yPy', 1, '2025-09-22 06:27:04', '2025-09-22 06:27:04');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
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
-- Table structure for table `histories`
--

CREATE TABLE `histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `client_number` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `schedule` date NOT NULL,
  `delivery_type` varchar(255) DEFAULT NULL,
  `vehicle_type` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `distance` decimal(8,2) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Completed',
  `driver` varchar(255) DEFAULT NULL,
  `arrival` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
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
-- Table structure for table `job_batches`
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
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(6, '2025_09_10_071624_create_histories_table', 3),
(9, '0001_01_01_000000_create_users_table', 4),
(10, '0001_01_01_000001_create_cache_table', 4),
(11, '0001_01_01_000002_create_jobs_table', 4),
(12, '2025_09_10_045706_create_trips_table', 5),
(13, '2025_09_10_070324_create_admins_table', 5),
(14, '2025_09_10_083520_create_companies_table', 5),
(15, '2025_09_10_083607_create_drivers_table', 5),
(16, '2025_09_13_102147_create_histories_table', 5),
(17, '2025_09_13_143141_create_municipality_costs_table', 6),
(18, '2025_09_13_144419_create_municipality_costs_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `municipality_costs`
--

CREATE TABLE `municipality_costs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `municipality` varchar(100) NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `municipality_costs`
--

INSERT INTO `municipality_costs` (`id`, `company_id`, `name`, `municipality`, `cost`, `created_at`, `updated_at`) VALUES
(46, NULL, 'Cebu Logistics Co.', 'Alcantara', 200.00, '2025-09-18 17:00:58', '2025-09-18 17:00:58'),
(49, NULL, 'Akai Foods, Inc.', 'Mandaue City', 300.00, '2025-09-18 22:15:36', '2025-09-18 22:15:36'),
(52, NULL, 'Apo Cement Corporation', 'Lapu-lapu City', 500.00, '2025-09-20 06:16:34', '2025-09-20 06:16:34'),
(55, NULL, 'DoubleDragon Properties Corp.', 'Cebu City', 400.00, '2025-09-20 09:22:39', '2025-09-20 09:22:39');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('8vqrTBnbLpFnI2xAvY1S8WbnBWFxPOq78XWeN7Id', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZVRtSVEyNGhLM0xwSFdrV0ZZV25qSHpuWXc0SjNDTWNTSzFWakt2YyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kcml2ZXIvbG9naW4iO319', 1759592421),
('FUD8KwU2ILGrONJ0YBjReyNUBmCy9X7k1lbLemFK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiZ1I1NjZZODJXQXAzYmZYS09SVjRQdnZENjdpc2VEUllOd0FZRFFNYSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kcml2ZXIvdHJpcHMiO31zOjk6ImRyaXZlcl9pZCI7aTo1NTtzOjExOiJkcml2ZXJfbmFtZSI7czoyMToiSm9obiBMbG95ZCBBYmVsbGFub3NhIjtzOjEyOiJkcml2ZXJfZW1haWwiO3M6MzA6ImpvaG5sbG95ZGFiZWxsYW5vc2EwQGdtYWlsLmNvbSI7fQ==', 1759844810),
('HeVGp6iDZWHtmSMdHBYykvkHznsaWfXEW9bjqO4Z', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoiUDJLbHpnOVFnVEJZaEdLUVpsd1BEQXBkanBsVjg4aGpaU0ZtcmFqVyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7fXM6OToiZHJpdmVyX2lkIjtpOjU1O3M6MTE6ImRyaXZlcl9uYW1lIjtzOjIxOiJKb2huIExsb3lkIEFiZWxsYW5vc2EiO3M6MTI6ImRyaXZlcl9lbWFpbCI7czozMDoiam9obmxsb3lkYWJlbGxhbm9zYTBAZ21haWwuY29tIjtzOjEwOiJjb21wYW55X2lkIjtpOjU5O3M6MTI6ImNvbXBhbnlfbmFtZSI7czoxNjoiQWthaSBGb29kcywgSW5jLiI7czoxMzoiY29tcGFueV9lbWFpbCI7czozMDoiam9obmxsb3lkYWJlbGxhbm9zYTBAZ21haWwuY29tIjt9', 1759585896),
('Ing1JObWYPCb8RJoZPWRY6IOLBIux9S0guo1ESfB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid252ZFQ5THM3VHVudU9GalN6RDE0UUFYZGpGZmNrQXdCNFlId0Y0bCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jb21wYW55L2xvZ2luIjt9fQ==', 1759592026),
('lMOLQnatSOrNy6uSHfCvxTLbxTiXTYg4n4aQt4xs', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRWhzaUxiU21PeUU3eHRlaEZrM0lMSjlTUnppYWZZTlVuUEdRUjlWbiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jb21wYW55L2xvZ2luIjt9czo4OiJhZG1pbl9pZCI7aToxO30=', 1759592528),
('odGM7drY7FSb8tJLdBd9073wHLtlEW9HqjMEACZI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoicm9XM2hKbUJuR3cwcnpwc2lQZklwSXFiRTBrV0VFVEMxTElqWHFHTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kcml2ZXIvdHJpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjk6ImRyaXZlcl9pZCI7aTo1NTtzOjExOiJkcml2ZXJfbmFtZSI7czoyMToiSm9obiBMbG95ZCBBYmVsbGFub3NhIjtzOjEyOiJkcml2ZXJfZW1haWwiO3M6MzA6ImpvaG5sbG95ZGFiZWxsYW5vc2EwQGdtYWlsLmNvbSI7fQ==', 1759591907),
('qtyFiHG4OpHkavrDBvHxB0DalEnQJQhyIFTmZEoQ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoic0wyWTJxTlczNnpHeDBjdFJBcXFEd0Q3OEtRcEVXeVp4Q0ptZUN5dSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jb21wYW55L2xvZ2luIjt9czo5OiJkcml2ZXJfaWQiO2k6NTU7czoxMToiZHJpdmVyX25hbWUiO3M6MjE6IkpvaG4gTGxveWQgQWJlbGxhbm9zYSI7czoxMjoiZHJpdmVyX2VtYWlsIjtzOjMwOiJqb2hubGxveWRhYmVsbGFub3NhMEBnbWFpbC5jb20iO30=', 1759585825),
('uzQcYORKvPRReKFnFQBLj3nw7PtiBRYhKqh0WnXr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YToxMDp7czo2OiJfdG9rZW4iO3M6NDA6Inl6UXpGY0xmcFpGeVVCZGFlclNzMGhJeHFLbURiTHR3WEZ5NHRkbjQiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YTowOnt9fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vZGFzaGJvYXJkIjt9czo4OiJhZG1pbl9pZCI7aToxO3M6MTA6ImNvbXBhbnlfaWQiO2k6NTk7czoxMjoiY29tcGFueV9uYW1lIjtzOjE2OiJBa2FpIEZvb2RzLCBJbmMuIjtzOjEzOiJjb21wYW55X2VtYWlsIjtzOjMwOiJqb2hubGxveWRhYmVsbGFub3NhMEBnbWFpbC5jb20iO3M6OToiZHJpdmVyX2lkIjtpOjU1O3M6MTE6ImRyaXZlcl9uYW1lIjtzOjIxOiJKb2huIExsb3lkIEFiZWxsYW5vc2EiO3M6MTI6ImRyaXZlcl9lbWFpbCI7czozMDoiam9obmxsb3lkYWJlbGxhbm9zYTBAZ21haWwuY29tIjt9', 1759074710);

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transactionId` varchar(255) NOT NULL,
  `deliveryType` varchar(255) NOT NULL,
  `vehicleType` varchar(255) NOT NULL,
  `clientName` varchar(255) NOT NULL,
  `clientNumber` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `municipality` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `driver` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `schedule` date NOT NULL,
  `arrival_date` date DEFAULT NULL,
  `status` enum('Pending','In-transit','Completed','Cancelled','Archived') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`id`, `transactionId`, `deliveryType`, `vehicleType`, `clientName`, `clientNumber`, `destination`, `municipality`, `company`, `cost`, `driver`, `remarks`, `schedule`, `arrival_date`, `status`, `created_at`, `updated_at`) VALUES
(85, 'PUMVEQGDXRQR', 'Dry', '4-Wheeler', 'John', '9123456789', '733 M.L. Quezon St.', 'Mandaue City', 'Akai Foods, Inc.', 300.00, 'John Lloyd Abellanosa', 'Wala lokata sa client', '2025-09-30', NULL, 'Cancelled', '2025-09-22 00:23:31', '2025-09-22 08:33:56'),
(86, '4V6ZRB', 'Chilled', '6-Wheeler', 'John', '9333925123', '733 M.L. Quezon St.', 'Mandaue City', 'Akai Foods, Inc.', 300.00, 'John Lloyd Abellanosa', NULL, '2025-10-01', '2025-09-22', 'Completed', '2025-09-22 00:33:34', '2025-09-22 08:25:31'),
(87, 'N0YYW8', 'Dry', '4-Wheeler', 'Phen', '9123456789', 'Leyte', 'Mandaue City', 'Akai Foods, Inc.', 300.00, 'John Lloyd Abellanosa', 'Guba ang Truck', '2025-10-04', NULL, 'Cancelled', '2025-09-24 05:58:36', '2025-10-07 05:47:33'),
(88, '41RJVY', 'Dry', '4-Wheeler', 'Phen', '9123456789', 'Leyte', 'Mandaue City', 'Akai Foods, Inc.', 300.00, 'John Lloyd Abellanosa', NULL, '2025-09-28', '2025-09-30', 'Completed', '2025-09-28 05:49:06', '2025-09-30 05:54:14'),
(89, 'XF6ES9', 'Dry', '6-Wheeler', 'Phen', '9123456789', 'Leyte', 'Mandaue City', 'Akai Foods, Inc.', 300.00, 'John Lloyd Abellanosa', NULL, '2025-09-28', NULL, 'In-transit', '2025-09-28 07:12:53', '2025-09-28 07:14:45'),
(90, 'SA2SU9', 'Dry', '4-Wheeler', 'Phen', '9123456789', 'Leyte', 'Mandaue City', 'Akai Foods, Inc.', 300.00, NULL, NULL, '2025-09-28', NULL, 'Pending', '2025-09-28 07:38:58', '2025-09-28 07:38:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_username_unique` (`username`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `companies_email_unique` (`email`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `drivers_email_unique` (`email`),
  ADD KEY `drivers_company_id_foreign` (`company_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `histories`
--
ALTER TABLE `histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `histories_trip_id_foreign` (`trip_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `municipality_costs`
--
ALTER TABLE `municipality_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_municipalities_cost_company` (`company_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `histories`
--
ALTER TABLE `histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `municipality_costs`
--
ALTER TABLE `municipality_costs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `histories`
--
ALTER TABLE `histories`
  ADD CONSTRAINT `histories_trip_id_foreign` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `municipality_costs`
--
ALTER TABLE `municipality_costs`
  ADD CONSTRAINT `fk_municipalities_cost_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
