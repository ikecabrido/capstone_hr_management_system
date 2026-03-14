-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 14, 2026 at 06:36 PM
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
-- Database: `hr_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `recorded_by` enum('MANUAL','QR','SYSTEM') NOT NULL DEFAULT 'MANUAL',
  `status` enum('PRESENT','ABSENT','LATE','EARLY_OUT','PENDING_APPROVAL') NOT NULL DEFAULT 'PENDING_APPROVAL',
  `is_approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `approval_remarks` varchar(255) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `total_hours_worked` decimal(5,2) DEFAULT NULL,
  `regular_hours` decimal(5,2) DEFAULT NULL,
  `overtime_hours` decimal(5,2) DEFAULT NULL,
  `is_within_time_window` tinyint(1) DEFAULT 1,
  `is_within_timeout_window` tinyint(1) DEFAULT 1,
  `is_within_shift_hours` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `employee_id`, `shift_id`, `attendance_date`, `time_in`, `time_out`, `recorded_by`, `status`, `is_approved`, `approved_by`, `approval_remarks`, `approved_at`, `created_at`, `updated_at`, `total_hours_worked`, `regular_hours`, `overtime_hours`, `is_within_time_window`, `is_within_timeout_window`, `is_within_shift_hours`) VALUES
(1, 2, NULL, '2026-01-28', '2026-01-28 02:44:46', NULL, 'QR', 'PENDING_APPROVAL', 1, 1, '', '2026-01-28 11:00:35', '2026-01-28 02:44:46', '2026-01-28 03:00:35', NULL, NULL, NULL, 1, 1, 1),
(2, 9, NULL, '2026-01-28', '2026-01-28 10:48:41', NULL, 'QR', 'PENDING_APPROVAL', 1, 1, '', '2026-01-28 11:00:11', '2026-01-28 02:48:41', '2026-01-28 03:00:11', NULL, NULL, NULL, 1, 1, 1),
(3, 6, NULL, '2026-01-28', '2026-01-28 15:33:13', '2026-01-28 15:33:44', 'QR', 'PENDING_APPROVAL', 1, 1, '', '2026-01-29 21:27:43', '2026-01-28 07:33:13', '2026-01-29 13:27:43', NULL, NULL, NULL, 1, 1, 1),
(4, 6, NULL, '2026-01-29', '2026-01-29 21:25:04', '2026-01-29 21:25:28', 'QR', 'PENDING_APPROVAL', 1, 1, '', '2026-01-29 21:27:30', '2026-01-29 13:25:04', '2026-01-29 13:27:30', NULL, NULL, NULL, 1, 1, 1),
(5, 4, NULL, '2026-01-29', '2026-01-29 21:29:32', NULL, 'QR', 'PENDING_APPROVAL', 1, 1, '', '2026-01-31 23:16:56', '2026-01-29 13:29:32', '2026-01-31 15:16:56', NULL, NULL, NULL, 1, 1, 1),
(6, 4, NULL, '2026-01-30', '2026-01-30 16:21:08', '2026-01-30 16:22:21', 'QR', 'PENDING_APPROVAL', 1, 1, '', '2026-01-31 23:16:47', '2026-01-30 08:21:08', '2026-01-31 15:16:47', NULL, NULL, NULL, 1, 1, 1),
(7, 2, NULL, '2026-01-31', '2026-01-31 23:18:23', '2026-01-31 23:18:28', 'MANUAL', 'PENDING_APPROVAL', 1, 1, '', '2026-01-31 23:28:21', '2026-01-31 15:18:23', '2026-01-31 15:28:21', NULL, NULL, NULL, 1, 1, 1),
(8, 5, NULL, '2026-01-31', '2026-01-31 23:29:29', '2026-01-31 23:29:35', 'MANUAL', 'PENDING_APPROVAL', 1, 1, '', '2026-01-31 23:29:51', '2026-01-31 15:29:29', '2026-01-31 15:29:51', NULL, NULL, NULL, 1, 1, 1),
(9, 1, NULL, '2026-02-04', '2026-02-04 22:22:12', '2026-02-04 23:47:57', 'MANUAL', 'PENDING_APPROVAL', 1, 3, '', '2026-03-13 23:51:31', '2026-02-04 14:22:12', '2026-03-13 15:51:31', 1.43, 1.43, 0.00, 1, 1, 1),
(10, 6, NULL, '2026-02-04', '2026-02-04 23:28:17', NULL, 'QR', 'PENDING_APPROVAL', 1, 3, '', '2026-03-13 23:51:29', '2026-02-04 15:28:17', '2026-03-13 15:51:29', NULL, NULL, NULL, 1, 1, 1),
(11, 1, NULL, '2026-02-05', '2026-02-05 08:49:57', '2026-02-05 15:04:42', 'MANUAL', 'PENDING_APPROVAL', 1, 1, '', '2026-02-05 10:08:35', '2026-02-05 00:49:57', '2026-02-05 07:04:42', NULL, NULL, NULL, 1, 1, 1),
(12, 2, NULL, '2026-02-05', '2026-02-05 12:32:34', NULL, 'MANUAL', 'PENDING_APPROVAL', 1, 3, '', '2026-03-13 23:51:27', '2026-02-05 04:32:34', '2026-03-13 15:51:27', NULL, NULL, NULL, 1, 1, 1),
(13, 6, NULL, '2026-02-05', '2026-02-05 16:46:51', '2026-02-05 17:48:18', 'MANUAL', 'PRESENT', 1, 3, '', '2026-03-13 23:51:10', '2026-02-05 08:46:51', '2026-03-13 15:51:10', NULL, NULL, NULL, 1, 1, 1),
(14, 6, NULL, '2026-02-12', '2026-02-12 17:10:41', NULL, 'MANUAL', 'PRESENT', 1, 3, '', '2026-03-13 23:51:08', '2026-02-12 09:10:41', '2026-03-13 15:51:08', NULL, NULL, NULL, 1, 1, 1),
(15, 2, NULL, '2026-03-06', '2026-03-06 16:32:01', NULL, 'MANUAL', 'PRESENT', 1, 3, '', '2026-03-13 23:51:06', '2026-03-06 08:32:01', '2026-03-13 15:51:06', NULL, NULL, NULL, 1, 1, 1),
(16, 1, NULL, '2026-03-07', '2026-03-07 22:07:51', '2026-03-07 22:08:28', 'MANUAL', 'PRESENT', 1, 3, '', '2026-03-13 23:43:42', '2026-03-07 14:07:51', '2026-03-13 15:43:42', NULL, NULL, NULL, 1, 1, 1),
(17, 1, NULL, '2026-03-14', '2026-03-14 22:48:20', '2026-03-14 22:48:58', 'MANUAL', 'PRESENT', 1, 3, '', '2026-03-14 22:48:43', '2026-03-14 14:48:20', '2026-03-14 14:48:58', NULL, NULL, NULL, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_tokens`
--

CREATE TABLE `attendance_tokens` (
  `token_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `generated_by` int(11) NOT NULL,
  `generated_for_date` date NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `used_by` int(11) DEFAULT NULL,
  `used_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_tokens`
--

INSERT INTO `attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(2, '25a955b89de0d0d7513bf8aa1f48f563343a19e30903db9e400bb8e3eb10f939', 1, '2026-01-28', '2026-01-28 02:45:21', 1, 2, '2026-01-28 02:44:46', NULL, '2026-01-28 02:44:21'),
(7, 'cfad93fdd34894e956810115fb226627c94f7f0073dc7941cb959fdc7b768602', 1, '2026-01-28', '2026-01-28 10:49:29', 1, 9, '2026-01-28 10:48:41', NULL, '2026-01-28 02:48:29'),
(33, '8c2717b38431ee82550c7526b6589ac705d642601d438596b562682f29b35c96', 1, '2026-01-28', '2026-01-28 15:34:07', 1, 6, '2026-01-28 15:33:13', NULL, '2026-01-28 07:33:07'),
(34, 'd88f6da5211128d4a1464fc4e9b8826bf9b88b28ea3c1573085643bda20d6c05', 1, '2026-01-28', '2026-01-28 15:34:37', 1, 6, '2026-01-28 15:33:44', NULL, '2026-01-28 07:33:37'),
(42, '92b6123ee0a626f6104f6d129f3de9069e89d5d29281d139ab21bce616c4fc3c', 1, '2026-01-29', '2026-01-29 21:25:44', 1, 6, '2026-01-29 21:25:04', NULL, '2026-01-29 13:24:44'),
(43, 'ad32220aa3f801f87569e5f3a0bfabbf3e674bcfe59a79c8b9f2efb6f45c331d', 1, '2026-01-29', '2026-01-29 21:26:15', 1, 6, '2026-01-29 21:25:28', NULL, '2026-01-29 13:25:15'),
(47, '228e86caf7b194771649605cdcb23f0fd98cf31d954ac11b2d27d16cce98e019', 1, '2026-01-29', '2026-01-29 21:30:07', 1, 4, '2026-01-29 21:29:32', NULL, '2026-01-29 13:29:07'),
(52, 'd0703b75c4b39bd218594c62328aaef7f43bffa70348afb0d37de77972f22fa4', 1, '2026-01-30', '2026-01-30 16:21:59', 1, 4, '2026-01-30 16:21:08', NULL, '2026-01-30 08:20:59'),
(54, 'cf0777708ba3be22de0ecb02f43c8592e043c89cf0ec00189a76cc634c6e7015', 1, '2026-01-30', '2026-01-30 16:22:59', 1, 4, '2026-01-30 16:22:21', NULL, '2026-01-30 08:21:59'),
(118, 'b766f934077a71ac36b434f5af2661991d361b0049e617aef36c03804c57e7cd', 1, '2026-02-04', '2026-02-04 23:29:09', 1, 6, '2026-02-04 23:28:17', NULL, '2026-02-04 15:28:09'),
(183, 'd9b00fab49887c62dd203d8be0fa06dc737b4cc2a2d679e1ffc0ddcfcd0880fc', 1, '2026-02-05', '2026-02-05 15:05:33', 1, 1, '2026-02-05 15:04:42', '172.20.10.6', '2026-02-05 07:04:33'),
(260, '141ccc3cf812d9d1a8ef9eac5c1068c83ae6d9eca493f99fa868a533b1c5a2ef', 1, '2026-02-05', '2026-02-05 16:47:46', 1, 6, '2026-02-05 16:46:51', '172.20.10.6', '2026-02-05 08:46:46'),
(269, '472053ae52f1d8ac365bed41e2c6dacec58a24336840bd6f16f9cae34d85e8ad', 1, '2026-02-05', '2026-02-05 17:48:53', 1, 6, '2026-02-05 17:48:18', '172.20.10.6', '2026-02-05 09:47:53'),
(292, 'eabb79f28ea97e80e7d9e3829c893ec0e61d2c22df9cc26932d30f2fbd65f028', 1, '2026-02-12', '2026-02-12 17:11:15', 1, 6, '2026-02-12 17:10:41', '172.20.10.6', '2026-02-12 09:10:15'),
(296, '0e5a08148ef12dfc67eeae9a3aac22cc2036081e2ec60bb094772a77a424bebb', 1, '2026-03-06', '2026-03-06 16:32:52', 1, 2, '2026-03-06 16:32:01', '172.20.10.6', '2026-03-06 08:31:52'),
(327, '0cfc1be751a1deeb7d49e09548eeda3956a55e1523fd88fd9b0fd9ae1cb5a5ed', 1, '2026-03-07', '2026-03-07 22:08:44', 1, 1, '2026-03-07 22:07:51', '192.168.68.163', '2026-03-07 14:07:44'),
(328, '800d8a7212c60ed138dff7460933184f8a004b1e7d72957ec0b51f0ba6745c67', 1, '2026-03-07', '2026-03-07 22:09:14', 1, 1, '2026-03-07 22:08:28', '192.168.68.163', '2026-03-07 14:08:14'),
(530, '9e4b50f47d2d10eef8c96227ee96964b1552605cda5a1f1b588d050034183638', 3, '2026-03-14', '2026-03-14 22:49:00', 1, 1, '2026-03-14 22:48:20', '192.168.68.151', '2026-03-14 14:48:00'),
(531, 'f29727296a8788dcf37f6456d0f89884a7c2a9d24e49603ed725c951e9bfd30e', 3, '2026-03-14', '2026-03-14 22:49:53', 1, 1, '2026-03-14 22:48:58', '192.168.68.151', '2026-03-14 14:48:53');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `audit_id` int(11) NOT NULL,
  `action_type` varchar(100) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `attendance_id` int(11) DEFAULT NULL,
  `action_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`action_details`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `status` enum('SUCCESS','FAILED') DEFAULT 'SUCCESS',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`audit_id`, `action_type`, `user_id`, `employee_id`, `attendance_id`, `action_details`, `ip_address`, `user_agent`, `status`, `error_message`, `created_at`) VALUES
(1, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'FAILED', 'Password mismatch', '2026-01-28 02:36:19'),
(2, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'FAILED', 'Password mismatch', '2026-01-28 02:36:29'),
(3, 'LOGIN_FAILED', 4, NULL, NULL, '{\"reason\":\"Wrong password\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'FAILED', 'Password mismatch', '2026-01-28 02:37:22'),
(4, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'FAILED', 'Password mismatch', '2026-01-28 02:39:43'),
(5, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-28 02:43:04'),
(6, 'LOGIN_SUCCESS', 5, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-28 02:44:26'),
(7, 'TIME_IN_SUCCESS', 5, 2, 1, '{\"method\":\"QR\",\"status\":\"PRESENT\"}', '172.20.10.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-28 02:44:46'),
(8, 'QR_SCAN_SUCCESS', 5, 2, NULL, '{\"token_id\":2,\"action\":\"TIME_IN\"}', '172.20.10.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-28 02:44:46'),
(9, 'LOGOUT', 5, NULL, NULL, '[]', '172.20.10.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-28 02:48:15'),
(10, 'LOGIN_SUCCESS', 12, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-28 02:48:29'),
(11, 'TIME_IN_SUCCESS', 12, 9, 2, '{\"method\":\"QR\",\"status\":\"LATE\"}', '172.20.10.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-28 02:48:41'),
(12, 'QR_SCAN_SUCCESS', 12, 9, NULL, '{\"token_id\":7,\"action\":\"TIME_IN\"}', '172.20.10.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-28 02:48:41'),
(13, 'ATTENDANCE_APPROVED', 1, NULL, 2, '{\"remarks\":\"\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-28 03:00:11'),
(14, 'ATTENDANCE_APPROVED', 1, NULL, 1, '{\"remarks\":\"\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-28 03:00:35'),
(15, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-28 03:02:44'),
(16, 'LOGIN_SUCCESS', 12, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-28 03:02:54'),
(17, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-28 07:32:02'),
(18, 'LOGIN_SUCCESS', 9, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-01-28 07:32:41'),
(19, 'TIME_IN_SUCCESS', 9, 6, 3, '{\"method\":\"QR\",\"status\":\"LATE\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-01-28 07:33:13'),
(20, 'QR_SCAN_SUCCESS', 9, 6, NULL, '{\"token_id\":33,\"action\":\"TIME_IN\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-01-28 07:33:13'),
(21, 'QR_SCAN_FAILED', 9, 6, NULL, '{\"reason\":\"Invalid or expired token\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'FAILED', 'Token validation failed', '2026-01-28 07:33:35'),
(22, 'TIME_OUT_SUCCESS', 9, 6, 3, '{\"duration\":\"0h 0m\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-01-28 07:33:45'),
(23, 'QR_SCAN_SUCCESS', 9, 6, NULL, '{\"token_id\":34,\"action\":\"TIME_OUT\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-01-28 07:33:45'),
(24, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-28 07:46:52'),
(25, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-28 07:49:29'),
(26, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '192.168.68.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-29 13:21:19'),
(27, 'LOGIN_SUCCESS', 8, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '192.168.68.124', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-29 13:23:00'),
(28, 'LOGIN_SUCCESS', 9, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '192.168.68.164', 'Mozilla/5.0 (Linux; U; Android 13; en-ph; CPH2237 Build/TP1A.220905.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.72 Mobile Safari/537.36 HeyTapBrowser/45.13.6.1', 'SUCCESS', NULL, '2026-01-29 13:24:49'),
(29, 'TIME_IN_SUCCESS', 9, 6, 4, '{\"method\":\"QR\",\"status\":\"LATE\"}', '192.168.68.164', 'Mozilla/5.0 (Linux; U; Android 13; en-ph; CPH2237 Build/TP1A.220905.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.72 Mobile Safari/537.36 HeyTapBrowser/45.13.6.1', 'SUCCESS', NULL, '2026-01-29 13:25:04'),
(30, 'QR_SCAN_SUCCESS', 9, 6, NULL, '{\"token_id\":42,\"action\":\"TIME_IN\"}', '192.168.68.164', 'Mozilla/5.0 (Linux; U; Android 13; en-ph; CPH2237 Build/TP1A.220905.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.72 Mobile Safari/537.36 HeyTapBrowser/45.13.6.1', 'SUCCESS', NULL, '2026-01-29 13:25:04'),
(31, 'TIME_OUT_SUCCESS', 9, 6, 4, '{\"duration\":\"0h 0m\"}', '192.168.68.164', 'Mozilla/5.0 (Linux; U; Android 13; en-ph; CPH2237 Build/TP1A.220905.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.72 Mobile Safari/537.36 HeyTapBrowser/45.13.6.1', 'SUCCESS', NULL, '2026-01-29 13:25:28'),
(32, 'QR_SCAN_SUCCESS', 9, 6, NULL, '{\"token_id\":43,\"action\":\"TIME_OUT\"}', '192.168.68.164', 'Mozilla/5.0 (Linux; U; Android 13; en-ph; CPH2237 Build/TP1A.220905.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.72 Mobile Safari/537.36 HeyTapBrowser/45.13.6.1', 'SUCCESS', NULL, '2026-01-29 13:25:28'),
(33, 'ATTENDANCE_APPROVED', 1, NULL, 4, '{\"remarks\":\"\"}', '192.168.68.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-29 13:27:30'),
(34, 'ATTENDANCE_APPROVED', 1, NULL, 3, '{\"remarks\":\"\"}', '192.168.68.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-29 13:27:43'),
(35, 'LOGIN_SUCCESS', 7, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '192.168.68.168', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-01-29 13:29:22'),
(36, 'TIME_IN_SUCCESS', 7, 4, 5, '{\"method\":\"QR\",\"status\":\"LATE\"}', '192.168.68.168', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-01-29 13:29:32'),
(37, 'QR_SCAN_SUCCESS', 7, 4, NULL, '{\"token_id\":47,\"action\":\"TIME_IN\"}', '192.168.68.168', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-01-29 13:29:32'),
(38, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-30 08:19:26'),
(39, 'LOGIN_SUCCESS', 7, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.5', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-30 08:20:57'),
(40, 'TIME_IN_SUCCESS', 7, 4, 6, '{\"method\":\"QR\",\"status\":\"LATE\"}', '172.20.10.5', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-30 08:21:08'),
(41, 'QR_SCAN_SUCCESS', 7, 4, NULL, '{\"token_id\":52,\"action\":\"TIME_IN\"}', '172.20.10.5', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-30 08:21:08'),
(42, 'QR_SCAN_FAILED', 7, 4, NULL, '{\"reason\":\"Invalid or expired token\"}', '172.20.10.5', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'FAILED', 'Token validation failed', '2026-01-30 08:21:28'),
(43, 'LOGIN_SUCCESS', 9, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-30 08:21:42'),
(44, 'TIME_OUT_SUCCESS', 7, 4, 6, '{\"duration\":\"0h 1m\"}', '172.20.10.5', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-30 08:22:21'),
(45, 'QR_SCAN_SUCCESS', 7, 4, NULL, '{\"token_id\":54,\"action\":\"TIME_OUT\"}', '172.20.10.5', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-01-30 08:22:21'),
(46, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:16:37'),
(47, 'ATTENDANCE_APPROVED', 1, NULL, 6, '{\"remarks\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:16:47'),
(48, 'ATTENDANCE_APPROVED', 1, NULL, 5, '{\"remarks\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:16:56'),
(49, 'LOGOUT', 1, NULL, NULL, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:18:14'),
(50, 'LOGIN_SUCCESS', 5, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:18:18'),
(51, 'TIME_IN_SUCCESS', 5, 2, 7, '{\"method\":\"MANUAL\",\"status\":\"LATE\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:18:23'),
(52, 'TIME_OUT_SUCCESS', 5, 2, 7, '{\"duration\":\"0h 0m\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:18:28'),
(53, 'LOGOUT', 5, NULL, NULL, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:18:59'),
(54, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:28:11'),
(55, 'ATTENDANCE_APPROVED', 1, NULL, 7, '{\"remarks\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:28:21'),
(56, 'LOGOUT', 1, NULL, NULL, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:29:11'),
(57, 'LOGIN_SUCCESS', 5, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:29:14'),
(58, 'LOGOUT', 5, NULL, NULL, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:29:21'),
(59, 'LOGIN_SUCCESS', 8, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:29:26'),
(60, 'TIME_IN_SUCCESS', 8, 5, 8, '{\"method\":\"MANUAL\",\"status\":\"LATE\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:29:29'),
(61, 'TIME_OUT_SUCCESS', 8, 5, 8, '{\"duration\":\"0h 0m\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:29:35'),
(62, 'LOGOUT', 8, NULL, NULL, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:29:42'),
(63, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:29:45'),
(64, 'ATTENDANCE_APPROVED', 1, NULL, 8, '{\"remarks\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:29:51'),
(65, 'LOGOUT', 1, NULL, NULL, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-01-31 15:29:53'),
(66, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-01 11:18:30'),
(67, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 06:13:54'),
(68, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 07:12:37'),
(69, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 07:12:48'),
(70, 'LOGOUT', 4, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 07:18:02'),
(71, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 07:18:05'),
(72, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 13:55:10'),
(73, 'LOGOUT', 4, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 13:55:18'),
(74, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 13:55:21'),
(75, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 14:01:08'),
(76, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 14:01:10'),
(77, 'TIME_IN_SUCCESS', 4, 1, 9, '{\"method\":\"MANUAL\",\"status\":\"LATE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 14:22:12'),
(78, 'LOGOUT', 4, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 15:26:15'),
(79, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 15:26:18'),
(80, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 15:27:30'),
(81, 'LOGIN_SUCCESS', 9, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-02-04 15:27:58'),
(82, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 15:28:07'),
(83, 'TIME_IN_SUCCESS', 9, 6, 10, '{\"method\":\"QR\",\"status\":\"LATE\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-02-04 15:28:17'),
(84, 'QR_SCAN_SUCCESS', 9, 6, NULL, '{\"token_id\":118,\"action\":\"TIME_IN\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-02-04 15:28:17'),
(85, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 15:29:06'),
(86, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 15:29:08'),
(87, 'TIME_OUT_SUCCESS', 4, 1, 9, '{\"duration\":\"1h 25m\",\"hours\":{\"total_hours\":1.43,\"regular_hours\":1.43,\"overtime_hours\":0}}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-04 15:47:57'),
(88, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 00:15:29'),
(89, 'TIME_IN_SUCCESS', 4, 1, 11, '{\"method\":\"MANUAL\",\"status\":\"ON_TIME\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 00:49:57'),
(90, 'TIME_IN_FAILED', 4, 1, NULL, '{\"reason\":\"Already timed in\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'FAILED', 'Employee already has time in record for today', '2026-02-05 00:54:24'),
(91, 'TIME_IN_FAILED', 4, 1, NULL, '{\"reason\":\"Already timed in\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'FAILED', 'Employee already has time in record for today', '2026-02-05 00:57:28'),
(92, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.108.2 Chrome/142.0.7444.235 Electron/39.2.7 Safari/537.36', 'SUCCESS', NULL, '2026-02-05 02:01:55'),
(93, 'LOGOUT', 4, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 02:07:31'),
(94, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 02:07:34'),
(95, 'ATTENDANCE_APPROVED', 1, NULL, 11, '{\"remarks\":\"\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 02:08:35'),
(96, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:34:42'),
(97, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:37:30'),
(98, 'LOGOUT', 4, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:52:28'),
(99, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:52:31'),
(100, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:52:52'),
(101, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:53:48'),
(102, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:54:02'),
(103, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:54:08'),
(104, 'LOGOUT', 4, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:57:33'),
(105, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:57:36'),
(106, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:57:57'),
(107, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:57:59'),
(108, 'LOGOUT', 4, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:58:27'),
(109, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 03:58:29'),
(110, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 04:15:37'),
(111, 'LOGIN_SUCCESS', 5, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 04:22:20'),
(112, 'TIME_IN_SUCCESS', 5, 2, 12, '{\"method\":\"MANUAL\",\"status\":\"LATE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 04:32:34'),
(113, 'TIME_IN_FAILED', 5, 2, NULL, '{\"reason\":\"Already timed in\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'FAILED', 'Employee already has time in record for today', '2026-02-05 04:34:07'),
(114, 'LOGOUT', 5, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 04:39:57'),
(115, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 04:40:09'),
(116, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 04:48:15'),
(117, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 04:53:14'),
(118, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 04:54:29'),
(119, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 05:00:49'),
(120, 'LOGOUT', 4, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 05:09:05'),
(121, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 05:09:28'),
(122, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 05:09:37'),
(123, 'LOGIN_SUCCESS', 9, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 05:09:46'),
(124, 'LOGOUT', 9, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 05:31:03'),
(125, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 05:31:31'),
(126, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 05:33:03'),
(127, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-02-05 06:26:24'),
(128, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 06:39:35'),
(129, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 06:45:22'),
(130, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-02-05 07:31:10'),
(131, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 07:37:29'),
(132, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 07:37:31'),
(133, 'LOGOUT', 4, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 07:38:16'),
(134, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 07:38:18'),
(135, 'LOGIN_SUCCESS', 5, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-02-05 07:44:51'),
(136, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-02-05 07:45:29'),
(137, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-02-05 07:59:54'),
(138, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 08:05:03'),
(139, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 08:05:06'),
(140, 'LOGOUT', 4, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 08:06:18'),
(141, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 08:06:21'),
(142, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-02-05 08:06:36'),
(143, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 08:44:12'),
(144, 'LOGIN_SUCCESS', 9, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-02-05 08:46:35'),
(145, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 08:47:58'),
(146, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 08:48:02'),
(147, 'LOGOUT', 4, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 08:50:47'),
(148, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 08:51:09'),
(149, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 08:55:30'),
(150, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 08:55:34'),
(151, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 08:57:13'),
(152, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-05 09:47:21'),
(153, 'LOGIN_SUCCESS', 9, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_12 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-02-05 09:48:41'),
(154, 'LOGIN_SUCCESS', 7, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '192.168.1.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-06 14:49:14'),
(155, 'LOGOUT', 7, NULL, NULL, '[]', '192.168.1.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-06 14:49:58'),
(156, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '192.168.1.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-06 14:50:08'),
(157, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '192.168.1.25', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-02-06 14:51:23'),
(158, 'LOGOUT', 1, NULL, NULL, '[]', '192.168.1.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-06 15:01:52'),
(159, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '192.168.1.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-06 15:02:03'),
(160, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '192.168.1.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-06 16:58:40'),
(161, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '192.168.1.15', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-08 09:43:35'),
(162, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.24.93.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-09 11:34:51'),
(163, 'LOGOUT', 1, NULL, NULL, '[]', '172.24.93.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-09 11:41:52'),
(164, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.24.93.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-09 11:42:00'),
(165, 'LOGOUT', 4, NULL, NULL, '[]', '172.24.93.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-09 11:42:32'),
(166, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.24.93.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-09 11:42:40'),
(167, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'SUCCESS', NULL, '2026-02-12 09:10:05'),
(168, 'LOGIN_SUCCESS', 9, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_14 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-02-12 09:10:31'),
(169, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-06 08:31:08'),
(170, 'LOGIN_SUCCESS', 5, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.3', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', 'SUCCESS', NULL, '2026-03-06 08:31:48'),
(171, 'LOGOUT', 1, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-06 08:32:58'),
(172, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-06 08:33:00'),
(173, 'LOGOUT', 4, NULL, NULL, '[]', '172.20.10.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-06 08:33:38'),
(174, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '192.168.68.163', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'Password mismatch', '2026-03-07 09:46:17'),
(175, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '192.168.68.163', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-07 09:46:47'),
(176, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '192.168.68.149', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_14 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-03-07 09:48:04'),
(177, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '192.168.68.149', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_14 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-03-07 09:48:46'),
(178, 'LOGOUT', 1, NULL, NULL, '[]', '192.168.68.163', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-07 12:16:53'),
(179, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '192.168.68.163', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-07 12:17:03'),
(180, 'LOGOUT', 4, NULL, NULL, '[]', '192.168.68.163', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-07 13:43:23'),
(181, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '192.168.68.163', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-07 13:43:33'),
(182, 'LOGOUT', 4, NULL, NULL, '[]', '192.168.68.163', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-07 13:57:28'),
(183, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '192.168.68.163', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-07 13:57:36'),
(184, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '192.168.68.149', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_14 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-03-07 14:07:16'),
(185, 'LOGOUT', 1, NULL, NULL, '[]', '192.168.68.163', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-08 07:28:04'),
(186, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '192.168.68.163', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-08 07:28:17'),
(187, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-09 06:51:34'),
(188, 'LOGOUT', 1, NULL, NULL, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-09 06:56:42'),
(189, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'Password mismatch', '2026-03-09 06:56:46'),
(190, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'Password mismatch', '2026-03-09 06:56:51'),
(191, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-09 06:56:54'),
(192, 'LOGOUT', 1, NULL, NULL, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-09 06:56:59'),
(193, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'Password mismatch', '2026-03-09 06:57:03'),
(194, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'Password mismatch', '2026-03-09 06:57:08'),
(195, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'Password mismatch', '2026-03-09 06:57:11'),
(196, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'Password mismatch', '2026-03-09 06:57:15'),
(197, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'Password mismatch', '2026-03-09 06:57:18'),
(198, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'Password mismatch', '2026-03-09 07:08:15'),
(199, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-11 10:10:22'),
(200, 'LOGOUT', 1, NULL, NULL, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-11 10:15:20'),
(201, 'LOGOUT', 3, NULL, NULL, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-11 11:21:45'),
(202, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-11 11:24:54'),
(203, 'LOGOUT', 1, NULL, NULL, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-11 13:31:57');
INSERT INTO `audit_logs` (`audit_id`, `action_type`, `user_id`, `employee_id`, `attendance_id`, `action_details`, `ip_address`, `user_agent`, `status`, `error_message`, `created_at`) VALUES
(204, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-11 13:32:44'),
(205, 'LOGIN_FAILED', NULL, NULL, NULL, '{\"reason\":\"Invalid username\"}', '10.141.66.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'User not found', '2026-03-11 14:41:37'),
(207, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-13 13:53:30'),
(208, 'ATTENDANCE_APPROVED', 3, NULL, 16, '{\"remarks\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-13 15:43:42'),
(209, 'ATTENDANCE_APPROVED', 3, NULL, 15, '{\"remarks\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-13 15:51:06'),
(210, 'ATTENDANCE_APPROVED', 3, NULL, 14, '{\"remarks\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-13 15:51:08'),
(211, 'ATTENDANCE_APPROVED', 3, NULL, 13, '{\"remarks\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-13 15:51:10'),
(212, 'ATTENDANCE_APPROVED', 3, NULL, 12, '{\"remarks\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-13 15:51:27'),
(213, 'ATTENDANCE_APPROVED', 3, NULL, 10, '{\"remarks\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-13 15:51:29'),
(214, 'ATTENDANCE_APPROVED', 3, NULL, 9, '{\"remarks\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-13 15:51:31'),
(215, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '192.168.68.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'Password mismatch', '2026-03-13 15:53:56'),
(216, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '192.168.68.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'Password mismatch', '2026-03-13 15:54:02'),
(217, 'LOGIN_FAILED', 1, NULL, NULL, '{\"reason\":\"Wrong password\"}', '192.168.68.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'FAILED', 'Password mismatch', '2026-03-13 15:54:28'),
(218, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '192.168.68.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-13 15:57:11'),
(220, 'LOGOUT', 3, NULL, NULL, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-13 17:48:49'),
(221, 'LOGIN_SUCCESS', 1, NULL, NULL, '{\"role\":\"HR_ADMIN\"}', '192.168.68.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-14 14:46:29'),
(222, 'LOGIN_SUCCESS', 4, NULL, NULL, '{\"role\":\"EMPLOYEE\"}', '192.168.68.156', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_14 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6.1 Mobile/15E148 Safari/604.1', 'SUCCESS', NULL, '2026-03-14 14:48:07'),
(223, 'ATTENDANCE_APPROVED', 3, NULL, 17, '{\"remarks\":\"\"}', '192.168.68.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'SUCCESS', NULL, '2026-03-14 14:48:43');

-- --------------------------------------------------------

--
-- Table structure for table `department_heads`
--

CREATE TABLE `department_heads` (
  `dept_head_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `supervises_from` date DEFAULT NULL,
  `supervises_until` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `employee_number` varchar(20) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `salary_grade` varchar(20) DEFAULT NULL,
  `working_hours_per_day` int(11) DEFAULT 8,
  `status` enum('ACTIVE','INACTIVE','ON_LEAVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `user_id`, `first_name`, `last_name`, `email`, `phone`, `department`, `position`, `employee_number`, `hire_date`, `salary_grade`, `working_hours_per_day`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 'Natalie Nicole', 'Nomura', NULL, NULL, 'Psychology', 'Psychologist', 'EMP001', '2023-01-15', NULL, 8, 'ACTIVE', '2026-01-28 02:35:05', '2026-02-09 11:41:38'),
(2, 5, 'Maria', 'Santos', NULL, NULL, 'HR', 'HR Officer', 'EMP002', '2023-02-20', NULL, 8, 'ACTIVE', '2026-01-28 02:35:05', '2026-01-28 02:35:05'),
(3, 6, 'Carlos', 'Garcia', NULL, NULL, 'Academic', 'Teacher - Math', 'EMP003', '2023-03-10', NULL, 8, 'ACTIVE', '2026-01-28 02:35:05', '2026-01-28 02:35:05'),
(4, 7, 'Ana', 'Lopez', NULL, NULL, 'Academic', 'Teacher - English', 'EMP004', '2023-03-10', NULL, 8, 'ACTIVE', '2026-01-28 02:35:05', '2026-01-28 02:35:05'),
(5, 8, 'Roberto', 'Martinez', NULL, NULL, 'IT', 'System Administrator', 'EMP005', '2023-04-05', NULL, 8, 'ACTIVE', '2026-01-28 02:35:05', '2026-01-28 02:35:05'),
(6, 9, 'Sofia', 'Rodriguez', NULL, NULL, 'Finance', 'Accountant', 'EMP006', '2023-04-12', NULL, 8, 'ACTIVE', '2026-01-28 02:35:05', '2026-01-28 02:35:05'),
(7, 10, 'Miguel', 'Fernandez', NULL, NULL, 'Academic', 'Teacher - Science', 'EMP007', '2023-05-01', NULL, 8, 'ACTIVE', '2026-01-28 02:35:05', '2026-01-28 02:35:05'),
(8, 11, 'Isabella', 'Morales', NULL, NULL, 'Admin', 'Administrative Assistant', 'EMP008', '2023-05-15', NULL, 8, 'ACTIVE', '2026-01-28 02:35:05', '2026-01-28 02:35:05'),
(9, 12, 'Diego', 'Reyes', NULL, NULL, 'IT', 'IT Support', 'EMP009', '2023-06-01', NULL, 8, 'ACTIVE', '2026-01-28 02:35:05', '2026-01-28 02:35:05');

-- --------------------------------------------------------

--
-- Table structure for table `employee_shifts`
--

CREATE TABLE `employee_shifts` (
  `employee_shift_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `holiday_id` int(11) NOT NULL,
  `holiday_date` date NOT NULL,
  `holiday_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_working_day` tinyint(1) DEFAULT 0,
  `year` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_balances`
--

CREATE TABLE `leave_balances` (
  `balance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `total_days` int(11) NOT NULL DEFAULT 0,
  `used_days` int(11) NOT NULL DEFAULT 0,
  `remaining_days` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `leave_request_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `total_days` int(11) NOT NULL,
  `status` enum('PENDING','APPROVED_BY_HEAD','APPROVED_BY_HR','REJECTED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  `submitted_at` datetime DEFAULT current_timestamp(),
  `department_head_id` int(11) DEFAULT NULL,
  `department_head_approval_date` datetime DEFAULT NULL,
  `department_head_remarks` varchar(500) DEFAULT NULL,
  `hr_admin_id` int(11) DEFAULT NULL,
  `hr_admin_approval_date` datetime DEFAULT NULL,
  `hr_admin_remarks` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`leave_request_id`, `employee_id`, `leave_type_id`, `start_date`, `end_date`, `reason`, `total_days`, `status`, `submitted_at`, `department_head_id`, `department_head_approval_date`, `department_head_remarks`, `hr_admin_id`, `hr_admin_approval_date`, `hr_admin_remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '2026-02-09', '2026-02-12', '', 4, 'APPROVED_BY_HR', '2026-02-05 11:58:25', NULL, NULL, NULL, 1, '2026-03-11 21:32:48', '', '2026-02-05 03:58:25', '2026-03-11 13:32:48');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `leave_type_id` int(11) NOT NULL,
  `leave_type_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `days_per_year` int(11) NOT NULL DEFAULT 10,
  `is_deductible` tinyint(1) DEFAULT 1,
  `requires_approval` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`leave_type_id`, `leave_type_name`, `description`, `days_per_year`, `is_deductible`, `requires_approval`, `created_at`) VALUES
(1, 'Sick Leave', 'For medical reasons and illness', 10, 1, 1, '2026-02-04 06:22:44'),
(2, 'Vacation Leave', 'Annual paid time off', 10, 1, 1, '2026-02-04 06:22:44'),
(3, 'Emergency Leave', 'For unforeseen circumstances', 5, 1, 1, '2026-02-04 06:22:44');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `notification_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `related_type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `send_via_email` tinyint(1) DEFAULT 1,
  `send_via_sms` tinyint(1) DEFAULT 1,
  `email_sent` tinyint(1) DEFAULT 0,
  `sms_sent` tinyint(1) DEFAULT 0,
  `sms_status` enum('QUEUED','SENT','FAILED','SIMULATED') DEFAULT 'QUEUED',
  `email_status` enum('QUEUED','SENT','FAILED') DEFAULT 'QUEUED',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sent_at` datetime DEFAULT NULL,
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `shift_id` int(11) NOT NULL,
  `shift_name` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_duration` int(11) DEFAULT 60 COMMENT 'Break duration in minutes',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`shift_id`, `shift_name`, `start_time`, `end_time`, `break_duration`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Morning Shift', '08:00:00', '17:00:00', 60, 'Standard morning shift from 8 AM to 5 PM', 1, '2026-03-08 07:27:07', '2026-03-08 07:27:07'),
(2, 'Afternoon Shift', '14:00:00', '23:00:00', 60, 'Afternoon shift from 2 PM to 11 PM', 1, '2026-03-08 07:27:07', '2026-03-08 07:27:07'),
(3, 'Night Shift', '23:00:00', '08:00:00', 60, 'Night shift from 11 PM to 8 AM', 1, '2026-03-08 07:27:07', '2026-03-08 07:27:07'),
(4, 'Flexible Morning', '08:00:00', '16:00:00', 60, 'Flexible morning shift 8 AM to 4 PM', 1, '2026-03-08 07:27:07', '2026-03-08 07:27:07'),
(5, 'Flexible Evening', '16:00:00', '00:00:00', 60, 'Flexible evening shift 4 PM to 12 AM', 1, '2026-03-08 07:27:07', '2026-03-08 07:27:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('recruitment','payroll','time','compliance','workforce','employee','learning','performance','engagement_relations','exit') NOT NULL,
  `theme` enum('light','dark') DEFAULT 'light',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `theme`, `created_at`, `profile_pic`) VALUES
(1, 'hr_payroll', '$2y$10$YSkTSwrSdqSBsF2e.pfyq.mNCCIF7ijV4h/s1pAc8Q7KlQHzbQTmq', 'Russell Ike', 'payroll', 'light', '2026-03-06 21:13:06', NULL),
(2, 'hr_recruitment', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Administrator', 'recruitment', 'light', '2026-03-07 02:46:33', NULL),
(3, 'hr_time', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Admin', 'time', 'light', '2026-03-07 02:47:07', NULL),
(4, 'hr_employee', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'someone', 'employee', 'light', '2026-03-07 02:47:55', NULL),
(5, 'hr_compliance', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'comply', 'compliance', 'light', '2026-03-07 02:48:19', NULL),
(6, 'hr_workforce', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'force', 'workforce', 'light', '2026-03-07 02:48:43', NULL),
(7, 'hr_learning', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'learn', 'learning', 'light', '2026-03-07 02:49:22', NULL),
(8, 'hr_performance', '$2y$10$/aFKLVK.xloqiY31X4T.dOPKY2AnnkrpaME4f2z.l4LhQurY1/Zzy', 'Perform', 'performance', 'light', '2026-03-07 02:49:46', 'user_8.jpg'),
(9, 'hr_engagement', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'engage', 'engagement_relations', 'light', '2026-03-07 02:50:37', NULL),
(10, 'hr_exit', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'exit', 'exit', 'light', '2026-03-07 02:51:04', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `unique_attendance_per_day` (`employee_id`,`attendance_date`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_attendance_date` (`attendance_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_is_approved` (`is_approved`),
  ADD KEY `idx_shift_id` (`shift_id`),
  ADD KEY `idx_attendance_date_shift` (`attendance_date`,`shift_id`);

--
-- Indexes for table `attendance_tokens`
--
ALTER TABLE `attendance_tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `used_by` (`used_by`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_used` (`used`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `attendance_id` (`attendance_id`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `department_heads`
--
ALTER TABLE `department_heads`
  ADD PRIMARY KEY (`dept_head_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `employee_number` (`employee_number`),
  ADD KEY `idx_employee_number` (`employee_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_department` (`department`);

--
-- Indexes for table `employee_shifts`
--
ALTER TABLE `employee_shifts`
  ADD PRIMARY KEY (`employee_shift_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_shift_id` (`shift_id`),
  ADD KEY `idx_effective_from` (`effective_from`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`holiday_id`),
  ADD UNIQUE KEY `holiday_date` (`holiday_date`),
  ADD KEY `idx_holiday_date` (`holiday_date`),
  ADD KEY `idx_year` (`year`);

--
-- Indexes for table `leave_balances`
--
ALTER TABLE `leave_balances`
  ADD PRIMARY KEY (`balance_id`),
  ADD UNIQUE KEY `unique_balance` (`employee_id`,`leave_type_id`,`year`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `fk_lb_type` (`leave_type_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`leave_request_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_lr_type` (`leave_type_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`leave_type_id`),
  ADD UNIQUE KEY `leave_type_name` (`leave_type_name`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`shift_id`),
  ADD UNIQUE KEY `unique_shift_name` (`shift_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `attendance_tokens`
--
ALTER TABLE `attendance_tokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=539;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;

--
-- AUTO_INCREMENT for table `department_heads`
--
ALTER TABLE `department_heads`
  MODIFY `dept_head_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `employee_shifts`
--
ALTER TABLE `employee_shifts`
  MODIFY `employee_shift_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `holiday_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
