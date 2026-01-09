-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 06, 2026 at 03:07 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u924465161_masterfile`
--

-- --------------------------------------------------------

--
-- Table structure for table `allowances`
--

CREATE TABLE `allowances` (
  `id` int(11) NOT NULL,
  `description` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `allowances`
--

INSERT INTO `allowances` (`id`, `description`) VALUES
(1, 'Food'),
(2, 'Travel'),
(5, 'Bonus');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('seclot-enterprise-cache-admin@gmail.com|180.193.212.130', 'i:1;', 1766131835),
('seclot-enterprise-cache-admin@gmail.com|180.193.212.130:timer', 'i:1766131835;', 1766131835),
('seclot-enterprise-cache-admin@gmil.com|49.148.155.86', 'i:1;', 1766313988),
('seclot-enterprise-cache-admin@gmil.com|49.148.155.86:timer', 'i:1766313988;', 1766313988),
('seclot-enterprise-cache-adminn@gmail.com|49.145.245.191', 'i:1;', 1766296185),
('seclot-enterprise-cache-adminn@gmail.com|49.145.245.191:timer', 'i:1766296185;', 1766296185),
('seclot-enterprise-cache-janedoe@gmail.com|49.148.155.86', 'i:1;', 1766313855),
('seclot-enterprise-cache-janedoe@gmail.com|49.148.155.86:timer', 'i:1766313855;', 1766313855),
('seclot-enterprise-cache-jessel@gmail.com|49.145.245.191', 'i:1;', 1766308778),
('seclot-enterprise-cache-jessel@gmail.com|49.145.245.191:timer', 'i:1766308778;', 1766308778),
('seclot-enterprise-cache-timekeeper@gmail.com|49.148.155.86', 'i:1;', 1766285550),
('seclot-enterprise-cache-timekeeper@gmail.com|49.148.155.86:timer', 'i:1766285550;', 1766285550);

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
-- Table structure for table `deductions`
--

CREATE TABLE `deductions` (
  `id` int(11) NOT NULL,
  `description` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deductions`
--

INSERT INTO `deductions` (`id`, `description`) VALUES
(1, 'SSS'),
(2, 'Cash Advance'),
(7, 'PhilHealth'),
(8, 'Pag-Ibig');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `abbr` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `abbr`) VALUES
(2, 'Accounting Department', 'Acct. Dept'),
(3, 'Human Resource Department', 'HRD'),
(4, 'Bid Department', 'BD'),
(5, 'Purchasing Department', 'Pur. Dept'),
(6, 'Engineering Department', 'Engr.'),
(7, 'Top Management', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `dtr`
--

CREATE TABLE `dtr` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `log_date` date DEFAULT NULL,
  `am_in` datetime DEFAULT NULL,
  `am_out` datetime DEFAULT NULL,
  `pm_in` datetime DEFAULT NULL,
  `pm_out` datetime DEFAULT NULL,
  `ot_in` datetime DEFAULT NULL,
  `ot_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dtr`
--

INSERT INTO `dtr` (`id`, `employee_id`, `log_date`, `am_in`, `am_out`, `pm_in`, `pm_out`, `ot_in`, `ot_out`) VALUES
(11, 1761536897, '2025-12-03', '2025-12-03 09:10:00', '2025-12-03 12:20:00', '2025-12-03 13:46:00', '2025-12-03 17:21:00', NULL, NULL),
(12, 1761536897, '2025-12-04', '2025-12-04 10:01:00', '2025-12-04 12:03:00', '2025-12-04 13:04:00', '2025-12-04 17:04:00', NULL, NULL),
(13, 1761536897, '2025-12-05', '2025-12-05 08:04:00', '2025-12-05 11:05:00', '2025-12-05 12:16:00', '2025-12-05 16:56:00', NULL, NULL),
(14, 1761536897, '2025-12-06', '2025-12-06 08:49:00', '2025-12-06 11:49:00', '2025-12-06 13:50:00', '2025-12-06 17:51:08', '2025-12-06 18:00:00', '2025-12-06 18:54:00'),
(15, 1761536897, '2025-12-08', '2025-12-08 07:49:00', '2025-12-08 12:00:00', '2025-12-08 13:00:00', '2025-12-08 16:51:16', '2025-12-08 17:20:00', '2025-12-08 18:53:00'),
(16, 1761536897, '2025-12-09', '2025-12-09 06:49:00', '2025-12-09 11:50:00', '2025-12-09 12:50:00', '2025-12-09 17:01:28', '2025-12-09 17:30:00', '2025-12-09 18:52:00'),
(22, 1761536897, '2025-12-20', '2025-12-20 14:19:00', NULL, NULL, NULL, NULL, NULL),
(23, 1766291621, '2025-12-21', NULL, NULL, '2025-12-21 12:52:00', '2025-12-21 17:17:00', NULL, NULL),
(24, 1766294697, '2025-12-21', NULL, NULL, '2025-12-21 13:42:00', '2025-12-21 17:17:00', NULL, NULL),
(25, 1761536897, '2025-12-21', NULL, NULL, NULL, NULL, '2025-12-21 18:45:00', NULL),
(26, 1766291621, '2025-12-22', NULL, NULL, '2025-12-22 13:36:00', NULL, NULL, NULL),
(27, 1766291621, '2026-01-06', NULL, NULL, NULL, NULL, '2026-01-06 19:23:00', '2026-01-06 21:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `suffix` varchar(50) DEFAULT NULL,
  `sex` varchar(25) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `mobile_no` varchar(25) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `photo_2x2` varchar(150) DEFAULT NULL,
  `photo_lg` varchar(150) DEFAULT NULL,
  `photo_lg2` varchar(150) DEFAULT NULL,
  `photo_lg3` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `lastname`, `firstname`, `middlename`, `suffix`, `sex`, `address`, `mobile_no`, `position_id`, `department_id`, `email`, `password`, `remember_token`, `photo_2x2`, `photo_lg`, `photo_lg2`, `photo_lg3`) VALUES
(1761536897, 'Doe', 'Jane', 'M.', '', 'Female', 'Bangkok Thailand', '09077896547', 15, 5, 'janedoe@gmail.com', '$2y$12$9bwwtkcdc5.SGokpMlxFxOY.dyaudGZIzK9zEWiNbBxVhf4aaNqKa', 'Lw8fV2aSmCUWuWiJ4lYhAMr82fcnrXCDTP3by5pIHEylv4DZHLhEqONBHcS4', '/images/uploads/2x2/1761536897.jpg', '/images/uploads/1761536897/photo.jpg', '/images/uploads/1761536897/photo3.jpg', '/images/uploads/1761536897/photo3.jpg'),
(1766291621, 'Dimasuhid', 'Jhap Jessel', 'N.', '', 'Female', 'Tangub City', '09051532389', 1, 6, 'jessel@gmail.com', '$2y$12$Z52ReDQNYKBCqzK6LdTlbeNC1fE/R5E0eQCtAoMIX4UU7Ad6AUx92', NULL, '/images/uploads/2x2/1766291621.jpg', '/images/uploads/1766291621/photo.jpg', '/images/uploads/1766291621/photo3.jpg', '/images/uploads/1766291621/photo3.jpg'),
(1766294697, 'Fernandez', 'Sarah Jean', 'Campilan', '', 'Female', 'Bintana, Tangub City', '09567549198', 6, 3, 'sarahjean@gmail.com', '$2y$12$yJblZeI4dvcv9nv.D3/tHOp0XR7Vf9iF0fNneu4dPj5yG79eoaUW2', NULL, '/images/uploads/2x2/1766294697.jpg', '/images/uploads/1766294697/photo.jpg', '/images/uploads/1766294697/photo3.jpg', '/images/uploads/1766294697/photo3.jpg'),
(1767706548, 'Duhaylungsod', 'Welmar', 'P.', '', 'Male', 'Capalaran, Tangub City', '09754660911', 18, 6, 'welmarduhaylungsod@gmail.com', '$2y$12$vzo1KQJbWi5r9Ug9ac.IHOIzLn0dRXsYf0sogdbBvqWUkkF7WotW2', NULL, '/images/uploads/2x2/1767706548.jpg', '/images/uploads/1767706548/photo.jpg', '/images/uploads/1767706548/photo3.jpg', '/images/uploads/1767706548/photo3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `employee_allowances`
--

CREATE TABLE `employee_allowances` (
  `id` int(11) NOT NULL,
  `payroll_item_id` int(11) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `amount` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_benefits`
--

CREATE TABLE `employee_benefits` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `amount` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_deductions`
--

CREATE TABLE `employee_deductions` (
  `id` int(11) NOT NULL,
  `payroll_item_id` int(11) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `amount` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_deductions`
--

INSERT INTO `employee_deductions` (`id`, `payroll_item_id`, `description`, `amount`) VALUES
(3, 10, 'Tardiness 7h, 12m', 809.21133333333),
(4, 10, 'Cash Advance', 3000);

-- --------------------------------------------------------

--
-- Table structure for table `employee_password_reset_tokens`
--

CREATE TABLE `employee_password_reset_tokens` (
  `email` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_shifts`
--

CREATE TABLE `employee_shifts` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `remarks` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_shifts`
--

INSERT INTO `employee_shifts` (`id`, `employee_id`, `shift_id`, `remarks`) VALUES
(1, 1761536897, 5, 'active'),
(2, 1766291621, 1, 'active'),
(3, 1766294697, 1, 'active'),
(4, 1767706548, 1, 'active');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

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
-- Table structure for table `payrolls`
--

CREATE TABLE `payrolls` (
  `id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `status` varchar(25) DEFAULT 'Current'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payrolls`
--

INSERT INTO `payrolls` (`id`, `department_id`, `date_from`, `date_to`, `status`) VALUES
(14, 2, '2025-12-01', '2025-12-16', 'Current'),
(15, 3, '2025-12-01', '2025-12-15', 'Current'),
(16, 6, '2025-12-01', '2025-12-15', 'Current');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_items`
--

CREATE TABLE `payroll_items` (
  `id` int(11) NOT NULL,
  `payroll_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `num_of_days` double DEFAULT NULL,
  `daily_rate` double DEFAULT NULL,
  `overtime` int(11) DEFAULT 0,
  `overtime_pay` double DEFAULT 0,
  `gross_pay` double DEFAULT NULL,
  `net_pay` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_items`
--

INSERT INTO `payroll_items` (`id`, `payroll_id`, `employee_id`, `num_of_days`, `daily_rate`, `overtime`, `overtime_pay`, `gross_pay`, `net_pay`) VALUES
(10, 14, 1761536897, 6, 900, 229, 428.23, 5828.23, 5828.23);

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `daily_rate` double DEFAULT NULL,
  `hourly_rate` double DEFAULT NULL,
  `minutely_rate` double DEFAULT NULL,
  `holiday_rate` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `description`, `daily_rate`, `hourly_rate`, `minutely_rate`, `holiday_rate`) VALUES
(1, 'Office Engineer', 900, 112.5, 1.87, 270),
(5, 'Purchasing Manager', 900, 112.5, 1.87, 270),
(6, 'Bidding Manager', 500, 62.5, 1.04, 150),
(7, 'Accounting', 500, 62.5, 1.04, 150),
(8, 'HR', 1000, 125, 2.08, 300),
(10, 'Labor', 475, 59.37, 0.98, 142.5),
(11, 'Skilled Worker', 500, 62.5, 1.04, 150),
(12, 'Foreman', 650, 81.25, 1.35, 195),
(13, 'Leadman', 650, 81.25, 1.35, 195),
(14, 'Time Keeper', 500, 62.5, 1.04, 150),
(15, 'Project Incharge', 675, 84.37, 1.4, 202.5),
(17, 'Chief Engineer', 1250, 156.25, 2.6, 375),
(18, 'Driver', 500, 62.5, 1.04, 150);

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
('2gxks1FhGjaR3iXXcqRy8h7OnAx5oj1VaiqLAbDl', 3, '112.208.75.75', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSTAzZ0hFQXo4aDc3dnVCRlZuNThxOXdWeXRiNGdub29xOTBVQlZ3diI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vc2VjbG90LnN0b3JlL2VtcGxveWVlcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1767707568),
('71Cbx4l02eaVREs55xC5IHuvfyVjtZs3QSkN3Z2T', 1767706548, '112.208.75.75', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUDQ4R09FUURJMDZBUWZhTVNidFFpTFlVNk1YR055NmZqY2ZhOEhkVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vc2VjbG90LnN0b3JlL2VtcGxveWVlL2hvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjU1OiJsb2dpbl9lbXBsb3llZV81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE3Njc3MDY1NDg7fQ==', 1767706668),
('a9FkgLkqtRpdby020tWYlHPvNbis7nNZFX8IhNzN', NULL, '112.208.75.75', 'Mozilla/5.0 (Linux; Android 14; RMX3830 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.34 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicVZLRHZSSDM3Vlg5QnVmY085OHJ1V3l1NDRuWDNmaGt6R1ZZNDlaeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc2VjbG90LnN0b3JlL2lyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1767707637),
('ETT8MOJ6h2gGtNhAB7Y03qGR6dfWuQtYeXt9Ujb4', NULL, '93.158.92.13', 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSXg1TEVoYmZUTjFlU0VndnFTSjRPaHM5eXhNbkt4cFl6TGFrcVBWeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vc2VjbG90LnN0b3JlL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1767710044),
('hb18DeU2UHYx6yGIwHxSKNtCTzsSY9G0flI9kKBH', NULL, '93.158.91.252', 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQnk5VFN6ZE1CU2lnY0ZWUkhhbjZud1FvQXNqWVRDb3BLalRpZjJUSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vc2VjbG90LnN0b3JlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1767710043),
('imzQ635EzVAQpLBtdtHi43hn1ktNt6M4qp9s1GhR', 1766294697, '112.208.75.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiV2lHbWlzbDQ2SXhmckNuYUJ6M1kzTzFIT1BDOFp2Y2hzS1pVQlBIcyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vc2VjbG90LnN0b3JlL2VtcGxveWVlL2hvbWUiO31zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjM5OiJodHRwczovL3NlY2xvdC5zdG9yZS9wYXlyb2xsL2FsbG93YW5jZXMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO3M6NTU6ImxvZ2luX2VtcGxveWVlXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTc2NjI5NDY5Nzt9', 1767710488),
('KHX7u6IrbLyGkCpGgyZGiqrfgvmtgOfYWOlRSiwd', NULL, '1.37.67.136', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZjRISjVxZVNyS1htWjd3T25KZjN4bFI4cnBPeVRKQ2ZYTllXd3hQWCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMDoiaHR0cHM6Ly9zZWNsb3Quc3RvcmUvZW1wbG95ZWVzIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vc2VjbG90LnN0b3JlL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1767707489),
('LvrrdiNDXhrPHv324KQ1EckHY4I5KORa9rxITutB', 1766291621, '112.208.75.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiV0h5TWZhbll5RGFVVm5Mb0tVaDBIN3g1aWlKNnAzZ0FhSDliV2l5VSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vc2VjbG90LnN0b3JlL2VtcGxveWVlL2hvbWUiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO3M6NTU6ImxvZ2luX2VtcGxveWVlXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTc2NjI5MTYyMTt9', 1767706947),
('nk73VX7ZEk615gBjhtS9tqUppxHaed4CPBCkpTZn', NULL, '1.37.67.136', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicjdWS1VXQWtkNURNNGJVaktLejJoOWNhaW1YS25nN0M5bVZtQUZIMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vc2VjbG90LnN0b3JlL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1767705064),
('Q6P9vcksz9UkRtzUcuCl5PZ79zX1RvXAvzfNN7us', NULL, '112.208.75.75', 'Mozilla/5.0 (Linux; Android 15; SM-X210 Build/AP3A.240905.015.A2; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.146 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN3FLZVBEcnBVbUVqcmthanpTcFAwT1FVdU9uSGU0andxSjlQYnp2OCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc2VjbG90LnN0b3JlL2lyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1767704688),
('uL5SC30Mc6EJaQNyYlRjTzHFMUVkDbF3V6oHCV3k', 3, '112.208.75.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSnRrS2RQd3EwZ0E2Vjdlb1RDZ09MbWhVbHplYlVIbVZjVzBpVkZkMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vc2VjbG90LnN0b3JlL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1767705067);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `am_in` varchar(25) DEFAULT NULL,
  `am_out` varchar(25) DEFAULT NULL,
  `pm_in` varchar(25) DEFAULT NULL,
  `pm_out` varchar(25) DEFAULT NULL,
  `in_out_interval` int(11) DEFAULT 0,
  `out_in_interval` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `name`, `am_in`, `am_out`, `pm_in`, `pm_out`, `in_out_interval`, `out_in_interval`) VALUES
(1, 'Regular Shift', '08:00', '12:00', '13:00', '17:00', 60, 10),
(5, 'Holiday shifts', '08:00', '12:00', '01:00', '05:00', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'hr',
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `role`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'Hr', 'hr', 'hr@gmail.com', NULL, '$2y$12$hfJ1CrNH5uFmNWFHTVD5PuKvqY5yVEjyzQppRrNVcR.7T3Fa2mVRe', NULL, '2025-12-14 07:44:56', '2025-12-18 06:04:58'),
(3, 'Admin', 'admin', 'admin@gmail.com', NULL, '$2y$12$Y44/ts3J3ZcQQXLaTcEHe.7SFXADyvATcikgPltc9ffJuRu64pqb.', '74B8GTmMiNSEVFE2Rzmaun8WUayZbsJTIyct9rg22B0GYpf6oxyFo3DRbrnr', '2025-12-18 06:05:09', '2025-12-19 10:51:52'),
(4, 'Accounting', 'accounting', 'accounting@gmail.com', NULL, '$2y$12$m.NDhiqb1L6qSoSd5lnHC.14iTOiQ.Evo/vakHIpbLzOHcZ2ZWwVq', NULL, '2025-12-18 06:35:12', '2025-12-18 06:36:08'),
(5, 'Timekeeper', 'timekeeper', 'timekeeper@gmail.com', NULL, '$2y$12$sITqwQgldpIo6M3hzFlKJOU.9nzxFiQa50tM6nh1Ch2l97X2.f2dq', NULL, '2025-12-19 13:49:13', '2025-12-19 14:17:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `allowances`
--
ALTER TABLE `allowances`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `deductions`
--
ALTER TABLE `deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dtr`
--
ALTER TABLE `dtr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_benefits`
--
ALTER TABLE `employee_benefits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_shifts`
--
ALTER TABLE `employee_shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll_items`
--
ALTER TABLE `payroll_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
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
-- AUTO_INCREMENT for table `allowances`
--
ALTER TABLE `allowances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `deductions`
--
ALTER TABLE `deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dtr`
--
ALTER TABLE `dtr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `employee_benefits`
--
ALTER TABLE `employee_benefits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee_shifts`
--
ALTER TABLE `employee_shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `payroll_items`
--
ALTER TABLE `payroll_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
