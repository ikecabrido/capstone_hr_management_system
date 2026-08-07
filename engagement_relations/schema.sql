-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 06, 2026 at 05:31 AM
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
-- Database: `hrms`
--

-- --------------------------------------------------------

--
-- Table structure for table `eer_announcements`
--

CREATE TABLE `eer_announcements` (
  `eer_announcements_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `target_audience` varchar(100) DEFAULT 'all',
  `type` enum('announcement','recognition','department_update') DEFAULT 'announcement',
  `department` varchar(100) DEFAULT NULL,
  `priority` varchar(50) DEFAULT 'normal',
  `category` varchar(100) DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_announcements`
--

INSERT INTO `eer_announcements` (`eer_announcements_id`, `title`, `content`, `created_by_user_id`, `created_at`, `target_audience`, `type`, `department`, `priority`, `category`) VALUES
(1, 'Welcome Announcement', 'Welcome to the new engagement portal!', 9, '2026-04-07 10:00:00', 'all', 'announcement', NULL, 'normal', 'general'),
(2, 'Recognition Spotlight', 'Congratulations to the monthly recognition winner!', 9, '2026-04-07 11:30:00', 'all', 'announcement', NULL, 'normal', 'general'),
(15, 'fgh', 'fgh', 9, '2026-04-07 13:10:44', 'all', 'department_update', 'all', 'normal', 'general'),
(16, 'qasdf', 'asd', 9, '2026-04-07 13:32:31', 'all', 'department_update', 'it', 'urgent', 'general'),
(18, 'eqwe', 'qwewq', 9, '2026-04-07 21:09:52', 'all', 'announcement', NULL, 'normal', 'general'),
(19, '12321', '12312', 9, '2026-04-07 21:15:34', 'all', 'announcement', NULL, 'normal', 'general'),
(20, 'asdsa', 'asdas', 9, '2026-04-07 21:18:36', 'all', 'announcement', NULL, 'normal', 'general'),
(21, 'vbbcv', 'cbc', 9, '2026-04-07 21:33:24', 'management', 'announcement', NULL, 'high', 'events'),
(22, 'fff', 'ff', 9, '2026-04-08 02:38:41', 'all', 'announcement', NULL, 'normal', 'achievements');

-- --------------------------------------------------------

--
-- Table structure for table `eer_award_history`
--

CREATE TABLE `eer_award_history` (
  `eer_award_history_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `award_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` text DEFAULT NULL,
  `nominated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `award_type` enum('employee_of_month','performance_award','achievement','special_recognition') DEFAULT 'special_recognition',
  `points` int(11) DEFAULT 0,
  `status` enum('nominated','shortlisted','winner','archived') DEFAULT 'nominated',
  `vote_count` int(11) DEFAULT 0,
  `performance_score` decimal(5,2) DEFAULT NULL,
  `month_year` varchar(7) DEFAULT NULL,
  `award_icon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_award_history`
--

INSERT INTO `eer_award_history` (`eer_award_history_id`, `employee_id`, `award_name`, `created_at`, `reason`, `nominated_by`, `updated_at`, `award_type`, `points`, `status`, `vote_count`, `performance_score`, `month_year`, `award_icon`) VALUES
(11, 2, 'Employee of the Month Nomination', '2026-08-02 10:55:17', 'Xz', 9, '2026-08-02 11:35:10', 'employee_of_month', 0, 'nominated', 1, NULL, '2026-08', NULL),
(12, 2, 'Employee of the Month Nomination', '2026-08-02 11:36:12', 'X', 9, '2026-08-02 11:36:12', 'employee_of_month', 0, 'nominated', 0, NULL, '2026-08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `eer_award_votes`
--

CREATE TABLE `eer_award_votes` (
  `eer_award_vote_id` int(11) NOT NULL,
  `award_history_id` int(11) NOT NULL,
  `voter_user_id` int(11) NOT NULL,
  `nominee_employee_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_award_votes`
--

INSERT INTO `eer_award_votes` (`eer_award_vote_id`, `award_history_id`, `voter_user_id`, `nominee_employee_id`, `created_at`) VALUES
(0, 11, 9, 2, '2026-08-02 19:35:10');

-- --------------------------------------------------------

--
-- Table structure for table `eer_badges`
--

CREATE TABLE `eer_badges` (
  `eer_badge_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `tier` enum('bronze','silver','gold','platinum') DEFAULT 'bronze',
  `points_value` int(11) DEFAULT 10,
  `category` varchar(100) DEFAULT 'achievement',
  `requirement_type` varchar(100) DEFAULT 'manual',
  `requirement_value` int(11) DEFAULT NULL,
  `status` enum('active','inactive','retired') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_badges`
--

INSERT INTO `eer_badges` (`eer_badge_id`, `name`, `description`, `icon`, `tier`, `points_value`, `category`, `requirement_type`, `requirement_value`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Star Performer', 'Awarded for outstanding performance', NULL, 'bronze', 10, 'achievement', 'manual', NULL, 'active', '2026-07-13 15:24:24', '2026-07-13 07:24:24'),
(2, 'Team Player', 'Recognizes collaboration and teamwork', NULL, 'bronze', 10, 'achievement', 'manual', NULL, 'active', '2026-07-13 15:24:24', '2026-07-13 07:24:24');

-- --------------------------------------------------------

--
-- Table structure for table `eer_comments`
--

CREATE TABLE `eer_comments` (
  `eer_comment_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `user_type` enum('employee','user') DEFAULT 'employee'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_comments`
--

INSERT INTO `eer_comments` (`eer_comment_id`, `post_id`, `employee_id`, `comment`, `created_at`, `user_id`, `user_type`) VALUES
(1, 1, 2, 'Looks great!', '2026-04-07 10:40:00', 9, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `eer_forums`
--

CREATE TABLE `eer_forums` (
  `eer_forum_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_forums`
--

INSERT INTO `eer_forums` (`eer_forum_id`, `title`, `description`, `category`, `created_by_user_id`, `created_at`) VALUES
(1, 'Employee Engagement Ideas', 'Share your ideas on how to improve employee engagement.', 'Engagement', 1, '2026-04-08 01:12:36'),
(2, 'Workplace Concerns', 'Discuss any workplace issues or concerns.', 'Grievance', 2, '2026-04-08 01:12:36'),
(3, 'HR Announcements Discussion', 'Forum for discussing HR-related announcements.', 'HR', 1, '2026-04-08 01:12:36'),
(4, 'Team Building Activities', 'Suggest and plan team building activities.', 'Events', 3, '2026-04-08 01:12:36'),
(5, 'Recognition and Rewards', 'Discuss employee achievements and recognition.', 'Recognition', 2, '2026-04-08 01:12:36'),
(6, 'asd', 'asd', 'general', 9, '2026-04-08 03:08:31');

-- --------------------------------------------------------

--
-- Table structure for table `eer_grievances`
--

CREATE TABLE `eer_grievances` (
  `eer_grievance_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','Resolved','Closed','Escalated') DEFAULT 'pending',
  `resolution_of_complaint` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `priority` enum('low','medium','high','urgent','critical') DEFAULT 'medium',
  `category` varchar(100) DEFAULT NULL,
  `anonymous` tinyint(1) DEFAULT 0,
  `attachment_path` varchar(255) DEFAULT NULL,
  `confidential` tinyint(1) DEFAULT 0,
  `action_taken` text DEFAULT NULL,
  `satisfaction_rating` int(1) DEFAULT NULL,
  `satisfaction_comment` text DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `escalation_level` varchar(50) DEFAULT NULL,
  `escalation_reason` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by_user_id` int(11) DEFAULT NULL,
  `payslip_id` int(11) DEFAULT NULL,
  `gross_pay` decimal(10,2) DEFAULT NULL,
  `total_deductions` decimal(10,2) DEFAULT NULL,
  `net_pay` decimal(10,2) DEFAULT NULL,
  `payslip_information` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_grievances`
--

INSERT INTO `eer_grievances` (`eer_grievance_id`, `employee_id`, `subject`, `description`, `status`, `resolution_of_complaint`, `created_at`, `priority`, `category`, `anonymous`, `attachment_path`, `confidential`, `action_taken`, `satisfaction_rating`, `satisfaction_comment`, `resolved_at`, `escalation_level`, `escalation_reason`, `updated_at`, `created_by_user_id`, `payslip_id`, `gross_pay`, `total_deductions`, `net_pay`, `payslip_information`) VALUES
(1, 1, 'Payroll discrepancy', 'There is a problem with the latest payroll calculation.', 'pending', 'ZXCZ', '2026-04-07 11:05:00', 'medium', 'Payroll Issues', 0, NULL, 0, 'CZXC', NULL, NULL, '2026-07-18 14:41:22', '4', '12', '2026-07-18 06:42:59', 9, NULL, NULL, NULL, NULL, NULL),
(4, 2, '1', 'have a bad manners\r\n', 'Escalated', 'zxc', '2026-07-13 11:38:23', 'medium', 'Workplace Harassment', 1, '', 0, 'xcz', NULL, NULL, '2026-07-18 14:40:49', '2', '123', '2026-07-18 07:23:59', 9, NULL, NULL, NULL, NULL, NULL),
(6, 4, 'System freezes frequently during development.', 'cszzxc', 'Resolved', 'ASDSA', '2026-07-18 10:45:32', 'medium', 'Payroll Issues', 0, NULL, 0, 'ASDSA', NULL, NULL, '2026-07-18 14:54:36', NULL, NULL, '2026-07-18 06:54:36', 9, 19, 1348.50, 620.00, 728.50, 'Payslip 19: gross=₱1348.50, deductions=₱620.00, net=₱728.50');

-- --------------------------------------------------------

--
-- Table structure for table `eer_grievance_attendance_links`
--

CREATE TABLE `eer_grievance_attendance_links` (
  `id` int(11) NOT NULL,
  `grievance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `attendance_id` int(11) DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `attendance_status` varchar(50) DEFAULT NULL,
  `late_minutes` int(11) DEFAULT 0,
  `early_out_minutes` int(11) DEFAULT 0,
  `linked_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eer_grievance_updates`
--

CREATE TABLE `eer_grievance_updates` (
  `id` int(11) NOT NULL,
  `grievance_id` int(11) DEFAULT NULL,
  `update_text` text DEFAULT NULL,
  `updated_by_user_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_grievance_updates`
--

INSERT INTO `eer_grievance_updates` (`id`, `grievance_id`, `update_text`, `updated_by_user_id`, `updated_at`) VALUES
(1, 1, 'Investigation started and payroll team notified.', 9, '2026-04-07 11:10:00'),
(2, 4, 'HR Remarks: CASC', 9, '2026-07-18 14:40:49'),
(3, 4, 'Final Resolution: ZCXZX', 9, '2026-07-18 14:40:49'),
(4, 1, 'HR Remarks: CZXC', 9, '2026-07-18 14:41:22'),
(5, 1, 'Final Resolution: ZXCZ', 9, '2026-07-18 14:41:22'),
(6, 6, 'HR Remarks: ASDSA', 9, '2026-07-18 14:54:36'),
(7, 6, 'Final Resolution: ASDSA', 9, '2026-07-18 14:54:36'),
(8, 4, 'HR Remarks: adssa', 9, '2026-07-18 15:10:46'),
(9, 4, 'Final Resolution: asdsa', 9, '2026-07-18 15:10:46'),
(10, 4, 'HR Remarks: xcz', 9, '2026-07-18 15:23:59'),
(11, 4, 'Final Resolution: zxc', 9, '2026-07-18 15:23:59');

-- --------------------------------------------------------

--
-- Table structure for table `eer_groups`
--

CREATE TABLE `eer_groups` (
  `eer_group_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_groups`
--

INSERT INTO `eer_groups` (`eer_group_id`, `name`, `created_by_user_id`, `created_at`) VALUES
(1, 'Engagement Team', 9, '2026-04-07 10:05:00'),
(5, '123321', 9, '2026-04-08 16:10:02');

-- --------------------------------------------------------

--
-- Table structure for table `eer_group_members`
--

CREATE TABLE `eer_group_members` (
  `eer_group_member_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_group_members`
--

INSERT INTO `eer_group_members` (`eer_group_member_id`, `group_id`, `employee_id`) VALUES
(1, 1, 1),
(17, 1, 6),
(18, 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `eer_messages`
--

CREATE TABLE `eer_messages` (
  `eer_message_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_messages`
--

INSERT INTO `eer_messages` (`eer_message_id`, `sender_id`, `receiver_id`, `message`, `timestamp`) VALUES
(1, 1, 2, 'Hi, how is the portal launch going?', '2026-04-07 10:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `eer_notifications`
--

CREATE TABLE `eer_notifications` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_notifications`
--

INSERT INTO `eer_notifications` (`id`, `employee_id`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 1, 'Your survey has been published.', 'survey', 1, '2026-04-07 10:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `eer_policies`
--

CREATE TABLE `eer_policies` (
  `eer_policy_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `target_audience` varchar(100) DEFAULT 'all',
  `category` varchar(100) DEFAULT 'general',
  `effective_date` date DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_policies`
--

INSERT INTO `eer_policies` (`eer_policy_id`, `title`, `content`, `created_by_user_id`, `created_at`, `target_audience`, `category`, `effective_date`, `attachment_path`) VALUES
(1, 'Code of Conduct', 'All employees must follow the company code of conduct.', 9, '2026-04-07 10:20:00', 'all', 'general', NULL, NULL),
(3, 'eqw', 'qwe', 0, '2026-04-08 03:13:53', 'all', 'general', NULL, NULL),
(5, 'sad', 'sda', 9, '2026-05-11 07:56:56', 'all', 'hr', '2026-05-12', 'uploads/policies/1778457416_ANGIE_BUSINESS.docx');

-- --------------------------------------------------------

--
-- Table structure for table `eer_projects`
--

CREATE TABLE `eer_projects` (
  `eer_project_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'planning',
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_projects`
--

INSERT INTO `eer_projects` (`eer_project_id`, `name`, `description`, `deadline`, `status`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(1, '213213', '12321', '2026-04-09', 'completed', 9, '2026-04-08 02:36:14', '2026-04-08 02:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `eer_reactions`
--

CREATE TABLE `eer_reactions` (
  `eer_reaction_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_type` enum('employee','user') NOT NULL DEFAULT 'employee',
  `type` enum('like','heart','wow') DEFAULT 'like',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_reactions`
--

INSERT INTO `eer_reactions` (`eer_reaction_id`, `post_id`, `employee_id`, `user_id`, `user_type`, `type`, `created_at`) VALUES
(1, 1, 1, NULL, 'employee', 'like', '2026-04-07 10:45:00'),
(42, 7, NULL, 9, 'user', 'like', '2026-04-08 02:13:33'),
(54, 9, NULL, 9, 'user', 'wow', '2026-05-05 08:07:17'),
(60, 42, 1, 9, 'employee', 'wow', '2026-07-13 11:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `eer_recognitions`
--

CREATE TABLE `eer_recognitions` (
  `eer_recognition_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `category` varchar(100) DEFAULT 'general',
  `source` enum('manual','performance','achievement') DEFAULT 'manual',
  `performance_report_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'approved',
  `leaderboard_position` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_recognitions`
--

INSERT INTO `eer_recognitions` (`eer_recognition_id`, `sender_id`, `receiver_id`, `message`, `points`, `created_at`, `category`, `source`, `performance_report_id`, `status`, `leaderboard_position`, `department`, `updated_at`) VALUES
(43, 9, 2, 'DAS', 10, '2026-08-02 18:42:49', 'general', 'manual', NULL, 'approved', NULL, NULL, '2026-08-02 10:42:49'),
(44, 9, 2, 'Vote recognition', 5, '2026-08-02 19:35:10', 'vote', 'manual', NULL, 'approved', NULL, NULL, '2026-08-02 11:35:10'),
(45, 9, 11, 'X', 10, '2026-08-02 19:35:47', 'general', 'manual', NULL, 'approved', NULL, NULL, '2026-08-02 11:35:47');

-- --------------------------------------------------------

--
-- Table structure for table `eer_replies`
--

CREATE TABLE `eer_replies` (
  `eer_reply_id` int(11) NOT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `parent_reply_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_type` enum('employee','user') DEFAULT 'employee',
  `content` text DEFAULT NULL,
  `mentioned_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_replies`
--

INSERT INTO `eer_replies` (`eer_reply_id`, `comment_id`, `post_id`, `parent_reply_id`, `employee_id`, `user_id`, `user_type`, `content`, `mentioned_user_id`, `created_at`) VALUES
(1, 1, 1, NULL, 1, NULL, 'employee', 'Thanks for the update!', NULL, '2026-04-07 10:50:00'),
(7, 1, 1, 1, NULL, 9, 'user', 'asdsa', NULL, '2026-04-08 02:30:44');

-- --------------------------------------------------------

--
-- Table structure for table `eer_rewards`
--

CREATE TABLE `eer_rewards` (
  `eer_reward_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `points_required` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT 'general',
  `icon` varchar(255) DEFAULT NULL,
  `tier` enum('bronze','silver','gold','platinum') DEFAULT 'bronze',
  `performance_requirement` decimal(5,2) DEFAULT NULL,
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_rewards`
--

INSERT INTO `eer_rewards` (`eer_reward_id`, `name`, `description`, `points_required`, `category`, `icon`, `tier`, `performance_requirement`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Gift Card', 'Redeem for a store gift card', 100, 'general', NULL, 'bronze', NULL, 'active', '2026-07-13 15:21:25', '2026-07-13 07:21:25');

-- --------------------------------------------------------

--
-- Table structure for table `eer_reward_redemptions`
--

CREATE TABLE `eer_reward_redemptions` (
  `eer_reward_redemption_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `reward_id` int(11) DEFAULT NULL,
  `points_used` int(11) DEFAULT NULL,
  `redeemed_at` datetime DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eer_social_posts`
--

CREATE TABLE `eer_social_posts` (
  `eer_social_post_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `author_type` enum('employee','user') NOT NULL,
  `item_type` enum('post','file') DEFAULT 'post',
  `content` text DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(10) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_social_posts`
--

INSERT INTO `eer_social_posts` (`eer_social_post_id`, `employee_id`, `user_id`, `author_type`, `item_type`, `content`, `file_name`, `file_path`, `file_size`, `file_type`, `description`, `created_at`) VALUES
(1, 1, NULL, 'employee', 'post', 'Excited to use the new engagement portal!', NULL, NULL, NULL, NULL, NULL, '2026-04-07 10:35:00'),
(41, NULL, 9, 'user', 'file', 'qwerty', 'ANGIE_BUSINESS.docx', 'uploads/social_files/1778932340_ANGIE_BUSINESS.docx', 18412, 'docx', 'asd', '2026-05-16 19:52:20'),
(42, NULL, 9, 'user', 'post', 'QWEQWE', NULL, NULL, NULL, NULL, NULL, '2026-05-22 20:39:33'),
(43, NULL, 9, 'user', 'file', 'QWEQWE', 'ANGIE_BUSINESS.docx', 'uploads/social_files/1779453586_ANGIE_BUSINESS.docx', 18412, 'docx', 'QWEQW', '2026-05-22 20:39:46');

-- --------------------------------------------------------

--
-- Table structure for table `eer_surveys`
--

CREATE TABLE `eer_surveys` (
  `eer_survey_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `survey_type` varchar(100) DEFAULT 'engagement',
  `created_by_employee_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `feedback_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_surveys`
--

INSERT INTO `eer_surveys` (`eer_survey_id`, `title`, `created_by_user_id`, `is_anonymous`, `survey_type`, `created_by_employee_id`, `description`, `created_at`, `feedback_id`) VALUES
(1, 'Employee Engagement Survey', 9, 1, 'satisfaction', 1, NULL, '2026-04-07 13:46:39', NULL),
(11, '1234', 9, 0, 'satisfaction', NULL, '456', '2026-08-04 16:45:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `eer_survey_answers`
--

CREATE TABLE `eer_survey_answers` (
  `eer_survey_answer_id` int(11) NOT NULL,
  `response_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_survey_answers`
--

INSERT INTO `eer_survey_answers` (`eer_survey_answer_id`, `response_id`, `question_id`, `answer`) VALUES
(1, 1, 1, 'Excellent');

-- --------------------------------------------------------

--
-- Table structure for table `eer_survey_feedback`
--

CREATE TABLE `eer_survey_feedback` (
  `eer_survey_feedback_id` int(11) NOT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_survey_feedback`
--

INSERT INTO `eer_survey_feedback` (`eer_survey_feedback_id`, `survey_id`, `employee_id`, `comment`, `rating`) VALUES
(1, 1, '1', 'Great survey experience', 4);

-- --------------------------------------------------------

--
-- Table structure for table `eer_survey_feedback_id`
--

CREATE TABLE `eer_survey_feedback_id` (
  `eer_survey_feedback_id_id` int(11) NOT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `evaluator_type` varchar(50) DEFAULT 'Self',
  `category` varchar(100) DEFAULT 'general',
  `is_anonymous` tinyint(1) DEFAULT 0,
  `evaluation_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_survey_feedback_id`
--

INSERT INTO `eer_survey_feedback_id` (`eer_survey_feedback_id_id`, `survey_id`, `employee_id`, `comment`, `rating`, `evaluator_type`, `category`, `is_anonymous`, `evaluation_date`) VALUES
(1, 1, '1', 'Great survey experience', 4, 'Self', 'general', 0, '2026-04-07 22:48:59'),
(2, NULL, '1', 'please be kindness next time', 3, 'HR', 'behavior', 0, '2026-04-07 19:35:35'),
(3, NULL, '1', 'qwewqe', 3, 'HR', 'performance', 0, '2026-04-08 08:08:03'),
(4, NULL, '1', 'sdf', 2, 'HR', 'performance', 0, '2026-05-11 02:40:16'),
(5, NULL, '2', 'asd', 2, 'HR', 'performance', 0, '2026-05-11 02:40:50'),
(6, NULL, '1', 'wefewf', 4, 'Suggestion', 'management', 1, '2026-07-05 11:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `eer_survey_questions`
--

CREATE TABLE `eer_survey_questions` (
  `eer_survey_question_id` int(11) NOT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `question_text` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_survey_questions`
--

INSERT INTO `eer_survey_questions` (`eer_survey_question_id`, `survey_id`, `question_text`, `type`) VALUES
(1, 1, 'How satisfied are you with the portal?', 'rating'),
(9, 11, '678', 'text');

-- --------------------------------------------------------

--
-- Table structure for table `eer_survey_responses`
--

CREATE TABLE `eer_survey_responses` (
  `eer_survey_response_id` int(11) NOT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`)),
  `target_employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_survey_responses`
--

INSERT INTO `eer_survey_responses` (`eer_survey_response_id`, `survey_id`, `employee_id`, `answers`, `target_employee_id`) VALUES
(1, 1, 1, '{\"1\":\"5\"}', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `eer_survey_targets`
--

CREATE TABLE `eer_survey_targets` (
  `eer_survey_target_id` int(11) NOT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `status` enum('pending','completed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_survey_targets`
--

INSERT INTO `eer_survey_targets` (`eer_survey_target_id`, `survey_id`, `employee_id`, `status`) VALUES
(1, 1, 1, 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `employee_no` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `employment_status` varchar(50) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `birthdate` date DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `teacher_qualification` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `employee_no`, `user_id`, `full_name`, `address`, `contact_number`, `email`, `department`, `position`, `date_hired`, `employment_status`, `created_at`, `updated_at`, `birthdate`, `sex`, `teacher_qualification`) VALUES
(1, 'T001', 4, 'John Doe', '123 Main St', '123-456-7890', 'john.doe@example.com', 'IT', 'Software Engineer', '2023-01-01', 'Active', '2026-03-21 15:51:30', '2026-04-05 03:36:10', NULL, NULL, NULL),
(2, 'T002', 15, 'Jane Smith', '456 Oak Ave', '098-765-4321', 'jane.smith@example.com', 'HR', 'HR Manager', '2023-02-15', 'Active', '2026-03-21 15:51:30', '2026-04-05 02:38:51', NULL, NULL, NULL),
(4, 'T004', 17, 'Sarah Williams', '321 Elm St', '555-789-0123', 'sarah.williams@example.com', 'Operations', 'Operations Manager', '2023-04-01', 'Active', '2026-03-26 19:25:55', '2026-04-05 02:38:51', NULL, NULL, NULL),
(5, 'T005', 18, 'David Brown', '654 Maple Dr', '555-456-7890', 'david.brown@example.com', 'IT', 'Junior Developer', '2023-05-15', 'Active', '2026-03-26 19:25:55', '2026-04-05 02:38:51', NULL, NULL, NULL),
(6, 'T006', 19, 'Emily Davis', '987 Cedar Ln', '555-321-6543', 'emily.davis@example.com', 'HR', 'HR Specialist', '2023-06-01', 'Active', '2026-03-26 19:25:55', '2026-04-05 02:38:51', NULL, NULL, NULL),
(7, 'T007', 20, 'Robert Wilson', '147 Oak St', '555-654-3210', 'robert.wilson@example.com', 'Finance', 'Financial Analyst', '2023-07-10', 'Active', '2026-03-26 19:25:55', '2026-04-05 02:38:51', NULL, NULL, NULL),
(8, 'T008', 21, 'Jessica Martinez', '258 Pine Ave', '555-987-6543', 'jessica.martinez@example.com', 'Operations', 'Staff Coordinator', '2023-08-20', 'Active', '2026-03-26 19:25:55', '2026-04-05 02:38:51', NULL, NULL, NULL),
(10, 'T010', 23, 'Test Employee', NULL, '09123456789', 'test@email.com', 'IT', 'Tester', NULL, 'Active', '2026-03-28 17:27:55', '2026-04-05 02:38:51', NULL, NULL, NULL),
(11, 'T011', 24, 'Admin User', NULL, '09123456789', 'admin@bcp.com', 'Administration', 'System Administrator', NULL, 'Active', '2026-03-28 18:10:37', '2026-04-05 02:38:51', NULL, NULL, NULL),
(12, 'T012', 25, 'Placer, Jan Russel C.', '', '09876543211', 'ronalddeguzman@gmail.com', 'Finance', 'hr', '2026-04-15', 'Resigned', '2026-04-04 14:55:08', '2026-04-05 03:11:13', '2026-04-08', 'Male', 'Profed'),
(13, 'T013', 26, 'Placer, Jan Russel C.', '', '09876543211', 'placerrussel5@gmail.com', 'Finance', 'hr', '2026-04-15', 'Active', '2026-04-04 14:55:39', '2026-04-05 02:38:51', '2026-04-07', 'Male', 'Profed'),
(14, 'T014', 27, 'Jhon Carlo Garcia', 'Brgy.Assumption', '09916117933', 'jhoncarlogarcia30@gmail.com', 'test', 'sawadika', '2026-04-06', 'Active', '2026-04-05 00:03:55', '2026-04-05 02:38:51', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_badges`
--

CREATE TABLE `employee_badges` (
  `employee_badge_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `badge_id` int(11) DEFAULT NULL,
  `awarded_at` datetime DEFAULT current_timestamp(),
  `awarded_by` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `performance_linked` tinyint(1) DEFAULT 0,
  `performance_score` decimal(5,2) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_badges`
--

INSERT INTO `employee_badges` (`employee_badge_id`, `employee_id`, `badge_id`, `awarded_at`, `awarded_by`, `reason`, `performance_linked`, `performance_score`, `updated_at`) VALUES
(13, 2, 2, '2026-08-02 18:56:17', 9, NULL, 0, 89.00, '2026-08-02 10:56:17');

-- --------------------------------------------------------

--
-- Table structure for table `grievance_payroll`
--

CREATE TABLE `grievance_payroll` (
  `grievance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `payroll_module` enum('Salary Calculation','Payslip Generation','Benefits Management','Tax & Deductions','Compliance & Statutory Reporting') NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `complaint_title` varchar(150) NOT NULL,
  `complaint_details` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Under Review','Resolved','Rejected','Closed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pm_360_feedback`
--

CREATE TABLE `pm_360_feedback` (
  `feedback_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `evaluator_type` enum('Manager','Peer','Subordinate','Self') NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `category` enum('Communication','Teamwork','Leadership','Performance') NOT NULL,
  `comments` text DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `evaluation_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pm_360_feedback`
--

INSERT INTO `pm_360_feedback` (`feedback_id`, `employee_id`, `evaluator_type`, `rating`, `category`, `comments`, `is_anonymous`, `evaluation_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'Manager', 5, 'Performance', 'Consistently exceeds expectations.', 0, '2026-07-01', '2026-07-27 12:58:24', '2026-07-27 12:58:24'),
(2, 1, 'Peer', 4, 'Teamwork', 'Very supportive and cooperative.', 1, '2026-07-02', '2026-07-27 12:58:24', '2026-07-27 12:58:24'),
(3, 1, 'Self', 5, 'Communication', 'Confident in communicating with the team.', 0, '2026-07-02', '2026-07-27 12:58:24', '2026-07-27 12:58:24'),
(4, 2, 'Manager', 3, 'Leadership', 'Needs improvement in decision making.', 0, '2026-07-03', '2026-07-27 12:58:24', '2026-07-27 12:58:24'),
(5, 2, 'Peer', 4, 'Communication', 'Communicates clearly during meetings.', 1, '2026-07-03', '2026-07-27 12:58:24', '2026-07-27 12:58:24'),
(6, 3, 'Manager', 5, 'Leadership', 'Excellent leadership skills.', 0, '2026-07-04', '2026-07-27 12:58:24', '2026-07-27 12:58:24'),
(7, 3, 'Subordinate', 5, 'Leadership', 'Provides guidance and support.', 1, '2026-07-04', '2026-07-27 12:58:24', '2026-07-27 12:58:24'),
(8, 4, 'Manager', 2, 'Performance', 'Performance below expectations.', 0, '2026-07-05', '2026-07-27 12:58:24', '2026-07-27 12:58:24'),
(9, 5, 'Peer', 4, 'Teamwork', 'Always willing to help teammates.', 1, '2026-07-05', '2026-07-27 12:58:24', '2026-07-27 12:58:24'),
(10, 5, 'Self', 4, 'Performance', 'Met most of my goals this period.', 0, '2026-07-06', '2026-07-27 12:58:24', '2026-07-27 12:58:24');

-- --------------------------------------------------------

--
-- Table structure for table `pm_appraisals`
--

CREATE TABLE `pm_appraisals` (
  `appraisal_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `review_period` enum('Quarterly','Annual','Mid-Year') NOT NULL,
  `goals_kpis` text NOT NULL,
  `performance_ratings` text NOT NULL,
  `manager_evaluation` text NOT NULL,
  `overall_score` decimal(5,2) NOT NULL,
  `comments` text DEFAULT NULL,
  `review_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pm_appraisals`
--

INSERT INTO `pm_appraisals` (`appraisal_id`, `employee_id`, `review_period`, `goals_kpis`, `performance_ratings`, `manager_evaluation`, `overall_score`, `comments`, `review_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'Quarterly', 'Complete assigned software development projects, improve system performance, and maintain code quality.', '{\"technical_skill\":5,\"productivity\":5,\"teamwork\":4,\"attendance\":5}', 'Employee exceeded expectations and delivered high-quality software solutions.', 96.50, 'Outstanding performance during the quarter.', '2026-03-31', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(2, 2, 'Quarterly', 'Improve recruitment process, employee relations, and HR documentation.', '{\"communication\":5,\"leadership\":5,\"accuracy\":4,\"attendance\":5}', 'Successfully managed HR responsibilities and supported organizational goals.', 92.50, 'Very strong HR management performance.', '2026-03-31', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(3, 3, 'Quarterly', 'Prepare accurate financial reports and maintain accounting records.', '{\"accuracy\":4,\"deadline_management\":4,\"analysis\":4,\"attendance\":4}', 'Employee completed financial tasks with good accuracy.', 88.00, 'Good performance with minor areas for improvement.', '2026-03-31', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(4, 4, 'Quarterly', 'Manage operations workflow and improve team productivity.', '{\"leadership\":5,\"planning\":5,\"execution\":5,\"attendance\":5}', 'Outstanding operational leadership and decision making.', 95.00, 'Top performing manager this quarter.', '2026-03-31', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(5, 5, 'Quarterly', 'Complete software development assignments and improve technical skills.', '{\"coding\":4,\"problem_solving\":4,\"teamwork\":4,\"attendance\":4}', 'Employee showed good progress in development tasks.', 85.00, 'Needs further improvement in advanced programming skills.', '2026-03-31', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(6, 6, 'Quarterly', 'Support HR operations and employee assistance programs.', '{\"communication\":4,\"support\":5,\"accuracy\":4,\"attendance\":5}', 'Consistently provided excellent HR support.', 90.00, 'Reliable and dependable employee.', '2026-03-31', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(7, 7, 'Quarterly', 'Analyze financial data and prepare management reports.', '{\"analysis\":4,\"accuracy\":4,\"reporting\":4,\"attendance\":4}', 'Completed financial analysis requirements effectively.', 86.00, 'Maintained good quality of work.', '2026-03-31', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(8, 8, 'Quarterly', 'Improve coordination activities and maintain operational support.', '{\"coordination\":3,\"communication\":4,\"attendance\":3,\"teamwork\":4}', 'Employee needs improvement in attendance consistency.', 78.50, 'Requires coaching and monitoring.', '2026-03-31', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(9, 9, 'Quarterly', 'Perform software testing and ensure application quality.', '{\"testing\":5,\"bug_detection\":5,\"documentation\":5,\"attendance\":5}', 'Excellent testing performance with high accuracy.', 98.00, 'Exceptional employee performance.', '2026-03-31', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(10, 10, 'Quarterly', 'Execute testing procedures and maintain quality standards.', '{\"testing\":4,\"documentation\":4,\"teamwork\":4,\"attendance\":4}', 'Employee achieved expected testing objectives.', 86.00, 'Very satisfactory performance.', '2026-03-31', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(11, 11, 'Annual', 'Maintain system administration, security, and IT infrastructure.', '{\"system_admin\":5,\"security\":5,\"problem_solving\":5,\"attendance\":5}', 'Excellent system management and technical leadership.', 99.00, 'Outstanding annual performance.', '2026-12-31', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(12, 12, 'Annual', 'Complete assigned finance tasks during employment period.', '{\"accuracy\":3,\"attendance\":3,\"teamwork\":4,\"productivity\":3}', 'Performance affected by transition period.', 72.50, 'Needs improvement before separation.', '2025-12-31', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(13, 13, 'Mid-Year', 'Support finance operations and complete assigned tasks.', '{\"accuracy\":4,\"productivity\":4,\"teamwork\":4,\"attendance\":4}', 'Employee maintained consistent work quality.', 90.00, 'Good performance during evaluation period.', '2026-06-30', '2026-07-14 02:25:14', '2026-07-14 02:25:14'),
(14, 14, 'Mid-Year', 'Improve assigned technical responsibilities and support operations.', '{\"technical_skill\":5,\"adaptability\":5,\"teamwork\":4,\"attendance\":5}', 'Employee exceeded expectations in assigned duties.', 94.00, 'Outstanding mid-year performance.', '2026-06-30', '2026-07-14 02:25:14', '2026-07-14 02:25:14');

-- --------------------------------------------------------

--
-- Table structure for table `pm_reports`
--

CREATE TABLE `pm_reports` (
  `report_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `evaluation_period` enum('monthly','quarterly','yearly') NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `kpi_score` decimal(5,2) NOT NULL,
  `attendance_score` decimal(5,2) NOT NULL,
  `attendance_impact_notes` text DEFAULT NULL,
  `overall_rating_percent` decimal(5,2) NOT NULL,
  `overall_rating_5` tinyint(1) NOT NULL,
  `final_rating_percent` decimal(5,2) NOT NULL,
  `final_grade` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pm_reports`
--

INSERT INTO `pm_reports` (`report_id`, `employee_id`, `evaluation_period`, `period_start`, `period_end`, `kpi_score`, `attendance_score`, `attendance_impact_notes`, `overall_rating_percent`, `overall_rating_5`, `final_rating_percent`, `final_grade`, `remarks`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'monthly', '2026-01-01', '2026-01-31', 88.00, 90.00, 'One approved leave.', 89.00, 4, 89.00, 'Very Satisfactory', 'Met all performance targets.', '9', '2026-07-14 00:51:50', '2026-07-14 01:17:48');

-- --------------------------------------------------------

--
-- Table structure for table `pr_deductions`
--

CREATE TABLE `pr_deductions` (
  `deduction_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` enum('fixed','percentage') DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `is_statutory` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_employee_adjustments`
--

CREATE TABLE `pr_employee_adjustments` (
  `adjustment_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `payroll_period_id` int(11) NOT NULL,
  `type` enum('deduction') DEFAULT 'deduction',
  `description` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL COMMENT 'Path to uploaded loan proof document',
  `deduction_subtype` enum('loans','other') DEFAULT 'other' COMMENT 'Subtype for deductions'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_employee_adjustments`
--

INSERT INTO `pr_employee_adjustments` (`adjustment_id`, `employee_id`, `payroll_period_id`, `type`, `description`, `amount`, `created_at`, `file_path`, `deduction_subtype`) VALUES
(0, 11, 10, 'deduction', 'nawalang mouse', 300.00, '2026-04-05 12:47:28', NULL, 'other');

-- --------------------------------------------------------

--
-- Table structure for table `pr_employee_benefits`
--

CREATE TABLE `pr_employee_benefits` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `has_sss` tinyint(1) DEFAULT 1 COMMENT 'SSS Contribution enrollment',
  `has_philhealth` tinyint(1) DEFAULT 1 COMMENT 'PhilHealth enrollment',
  `has_pagibig` tinyint(1) DEFAULT 1 COMMENT 'Pag-IBIG enrollment',
  `sss_amount_override` decimal(10,2) DEFAULT NULL COMMENT 'Manual override if needed',
  `philhealth_amount_override` decimal(10,2) DEFAULT NULL,
  `pagibig_amount_override` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_payslips`
--

CREATE TABLE `pr_payslips` (
  `payslip_id` int(11) NOT NULL,
  `payroll_run_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `gross_pay` decimal(10,2) DEFAULT NULL,
  `total_deductions` decimal(10,2) DEFAULT NULL,
  `net_pay` decimal(10,2) DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_exit_settlement` tinyint(1) DEFAULT 0 COMMENT 'Flag: 1 if this is an exit/final payslip',
  `settlement_id` int(11) DEFAULT NULL COMMENT 'Links to exit_employee_settlements.id',
  `resignation_id` int(11) DEFAULT NULL COMMENT 'Links to exit_resignations.id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_payslips`
--

INSERT INTO `pr_payslips` (`payslip_id`, `payroll_run_id`, `employee_id`, `gross_pay`, `total_deductions`, `net_pay`, `generated_at`, `is_exit_settlement`, `settlement_id`, `resignation_id`) VALUES
(8, 4, 1, 1303.39, 0.00, 1303.39, '2026-04-05 12:52:21', 0, NULL, NULL),
(9, 4, 2, 1087.89, 30.00, 1057.89, '2026-04-05 12:52:21', 0, NULL, NULL),
(10, 4, 4, 62.50, 0.00, 62.50, '2026-04-05 12:52:21', 0, NULL, NULL),
(11, 4, 5, 979.82, 0.00, 979.82, '2026-04-05 12:52:21', 0, NULL, NULL),
(12, 4, 6, 744.79, 0.00, 744.79, '2026-04-05 12:52:22', 0, NULL, NULL),
(13, 4, 8, 812.40, 10.00, 802.40, '2026-04-05 12:52:22', 0, NULL, NULL),
(14, 4, 9, 744.79, 0.00, 744.79, '2026-04-05 12:52:22', 0, NULL, NULL),
(15, 4, 10, 711.98, 50.00, 661.98, '2026-04-05 12:52:22', 0, NULL, NULL),
(16, 4, 11, 783.85, 300.00, 483.85, '2026-04-05 12:52:22', 0, NULL, NULL),
(17, 4, 13, 705.73, 60.00, 645.73, '2026-04-05 12:52:22', 0, NULL, NULL),
(18, 4, 14, 744.79, 0.00, 744.79, '2026-04-05 12:52:22', 0, NULL, NULL),
(19, 5, 1, 1348.50, 620.00, 728.50, '2026-04-05 20:13:17', 0, NULL, NULL),
(20, 5, 2, 1117.19, 200.00, 917.19, '2026-04-05 20:13:17', 0, NULL, NULL),
(21, 5, 6, 744.79, 200.00, 544.79, '2026-04-05 20:13:17', 0, NULL, NULL),
(22, 5, 7, 1078.52, 40.00, 1038.52, '2026-04-05 20:13:17', 0, NULL, NULL),
(23, 5, 9, 744.79, 0.00, 744.79, '2026-04-05 20:13:18', 0, NULL, NULL),
(24, 5, 10, 39.06, 0.00, 39.06, '2026-04-05 20:13:18', 0, NULL, NULL),
(25, 5, 11, 744.79, 0.00, 744.79, '2026-04-05 20:13:18', 0, NULL, NULL),
(26, 5, 13, 666.67, 0.00, 666.67, '2026-04-05 20:13:18', 0, NULL, NULL),
(27, 5, 14, 744.79, 0.00, 744.79, '2026-04-05 20:13:18', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pr_payslip_items`
--

CREATE TABLE `pr_payslip_items` (
  `payslip_item_id` int(11) NOT NULL,
  `payslip_id` int(11) DEFAULT NULL,
  `item_type` enum('earning','deduction') DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_payslip_items`
--

INSERT INTO `pr_payslip_items` (`payslip_item_id`, `payslip_id`, `item_type`, `description`, `amount`) VALUES
(29, 8, 'earning', 'Basic Salary - Software Engineer (₱35000 ÷ 2 ÷ 15 × 1 days)', 1166.67),
(30, 8, 'earning', 'Overtime (1 hrs × ₱109.38/hr × 1.25x)', 136.72),
(31, 9, 'earning', 'Basic Salary - HR Manager (₱30000 ÷ 2 ÷ 15 × 1 days)', 1000.00),
(32, 9, 'earning', 'Overtime (0.75 hrs × ₱93.75/hr × 1.25x)', 87.89),
(33, 9, 'deduction', 'Late (15 minutes × ₱2/min)', 30.00),
(34, 10, 'earning', 'Basic Salary - Operations Manager (₱32000 ÷ 2 ÷ 15 × 0 days)', 0.00),
(35, 10, 'earning', 'Overtime (0.5 hrs × ₱100.00/hr × 1.25x)', 62.50),
(36, 11, 'earning', 'Basic Salary - Junior Developer (₱25000 ÷ 2 ÷ 15 × 1 days)', 833.33),
(37, 11, 'earning', 'Overtime (1.5 hrs × ₱78.13/hr × 1.25x)', 146.48),
(38, 12, 'earning', 'Basic Salary - HR Specialist (₱20000 ÷ 2 ÷ 15 × 1 days)', 666.67),
(39, 12, 'earning', 'Overtime (1 hrs × ₱62.50/hr × 1.25x)', 78.13),
(40, 13, 'earning', 'Basic Salary - Staff Coordinator (₱22000 ÷ 2 ÷ 15 × 1 days)', 733.33),
(41, 13, 'earning', 'Overtime (0.92 hrs × ₱68.75/hr × 1.25x)', 79.06),
(42, 13, 'deduction', 'Late (5 minutes × ₱2/min)', 10.00),
(43, 14, 'earning', 'Basic Salary - Tester (₱20000 ÷ 2 ÷ 15 × 1 days)', 666.67),
(44, 14, 'earning', 'Overtime (1 hrs × ₱62.50/hr × 1.25x)', 78.13),
(45, 15, 'earning', 'Basic Salary - Tester (₱20000 ÷ 2 ÷ 15 × 1 days)', 666.67),
(46, 15, 'earning', 'Overtime (0.58 hrs × ₱62.50/hr × 1.25x)', 45.31),
(47, 15, 'deduction', 'Late (25 minutes × ₱2/min)', 50.00),
(48, 16, 'earning', 'Basic Salary - System Administrator (₱20000 ÷ 2 ÷ 15 × 1 days)', 666.67),
(49, 16, 'earning', 'Overtime (1.5 hrs × ₱62.50/hr × 1.25x)', 117.19),
(50, 16, 'deduction', 'nawalang mouse', 300.00),
(51, 17, 'earning', 'Basic Salary - hr (₱20000 ÷ 2 ÷ 15 × 1 days)', 666.67),
(52, 17, 'earning', 'Overtime (0.5 hrs × ₱62.50/hr × 1.25x)', 39.06),
(53, 17, 'deduction', 'Late (30 minutes × ₱2/min)', 60.00),
(54, 18, 'earning', 'Basic Salary - sawadika (₱20000 ÷ 2 ÷ 15 × 1 days)', 666.67),
(55, 18, 'earning', 'Overtime (1 hrs × ₱62.50/hr × 1.25x)', 78.13),
(56, 19, 'earning', 'Basic Salary - Software Engineer (₱35000 ÷ 2 ÷ 15 × 1 days)', 1166.67),
(57, 19, 'earning', 'Overtime (1.33 hrs × ₱109.38/hr × 1.25x)', 181.84),
(58, 19, 'deduction', 'SSS', 200.00),
(59, 19, 'deduction', 'PhilHealth', 200.00),
(60, 19, 'deduction', 'Pag-IBIG', 200.00),
(61, 19, 'deduction', 'Late (10 minutes × ₱2/min)', 20.00),
(62, 20, 'earning', 'Basic Salary - HR Manager (₱30000 ÷ 2 ÷ 15 × 1 days)', 1000.00),
(63, 20, 'earning', 'Overtime (1 hrs × ₱93.75/hr × 1.25x)', 117.19),
(64, 20, 'deduction', 'Pag-IBIG', 200.00),
(65, 21, 'earning', 'Basic Salary - HR Specialist (₱20000 ÷ 2 ÷ 15 × 1 days)', 666.67),
(66, 21, 'earning', 'Overtime (1 hrs × ₱62.50/hr × 1.25x)', 78.13),
(67, 21, 'deduction', 'Pag-IBIG', 200.00),
(68, 22, 'earning', 'Basic Salary - Financial Analyst (₱30000 ÷ 2 ÷ 15 × 1 days)', 1000.00),
(69, 22, 'earning', 'Overtime (0.67 hrs × ₱93.75/hr × 1.25x)', 78.52),
(70, 22, 'deduction', 'Late (20 minutes × ₱2/min)', 40.00),
(71, 23, 'earning', 'Basic Salary - Tester (₱20000 ÷ 2 ÷ 15 × 1 days)', 666.67),
(72, 23, 'earning', 'Overtime (1 hrs × ₱62.50/hr × 1.25x)', 78.13),
(73, 24, 'earning', 'Basic Salary - Tester (₱20000 ÷ 2 ÷ 15 × 0 days)', 0.00),
(74, 24, 'earning', 'Overtime (0.5 hrs × ₱62.50/hr × 1.25x)', 39.06),
(75, 25, 'earning', 'Basic Salary - System Administrator (₱20000 ÷ 2 ÷ 15 × 1 days)', 666.67),
(76, 25, 'earning', 'Overtime (1 hrs × ₱62.50/hr × 1.25x)', 78.13),
(77, 26, 'earning', 'Basic Salary - hr (₱20000 ÷ 2 ÷ 15 × 1 days)', 666.67),
(78, 27, 'earning', 'Basic Salary - sawadika (₱20000 ÷ 2 ÷ 15 × 1 days)', 666.67),
(79, 27, 'earning', 'Overtime (1 hrs × ₱62.50/hr × 1.25x)', 78.13);

-- --------------------------------------------------------

--
-- Table structure for table `pr_runs`
--

CREATE TABLE `pr_runs` (
  `run_id` int(11) NOT NULL,
  `payroll_period_id` int(11) DEFAULT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('draft','finalized') DEFAULT 'draft',
  `finalized_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_runs`
--

INSERT INTO `pr_runs` (`run_id`, `payroll_period_id`, `processed_at`, `status`, `finalized_by`) VALUES
(4, 10, '2026-04-05 12:52:22', 'finalized', 1),
(5, 11, '2026-04-05 20:13:19', 'finalized', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pr_tax_tables`
--

CREATE TABLE `pr_tax_tables` (
  `tax_id` int(11) NOT NULL,
  `min_income` decimal(10,2) DEFAULT NULL,
  `max_income` decimal(10,2) DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `fixed_tax` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_attendance`
--

CREATE TABLE `ta_attendance` (
  `attendance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `recorded_by` enum('MANUAL','QR','SYSTEM') NOT NULL DEFAULT 'MANUAL',
  `status` enum('PRESENT','ABSENT','LATE','EARLY_OUT','ON_LEAVE','PENDING_APPROVAL') DEFAULT 'PENDING_APPROVAL',
  `leave_request_id` int(11) DEFAULT NULL,
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
  `is_within_shift_hours` tinyint(1) DEFAULT 1,
  `late_minutes` int(11) DEFAULT 0 COMMENT 'Number of minutes employee was late (0 if on time)',
  `early_out_minutes` int(11) DEFAULT 0 COMMENT 'Number of minutes employee left early (0 if on time)',
  `shift_minutes` int(11) DEFAULT 0 COMMENT 'Expected shift duration in minutes',
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_attendance`
--

INSERT INTO `ta_attendance` (`attendance_id`, `employee_id`, `shift_id`, `attendance_date`, `time_in`, `time_out`, `recorded_by`, `status`, `leave_request_id`, `is_approved`, `approved_by`, `approval_remarks`, `approved_at`, `created_at`, `updated_at`, `total_hours_worked`, `regular_hours`, `overtime_hours`, `is_within_time_window`, `is_within_timeout_window`, `is_within_shift_hours`, `late_minutes`, `early_out_minutes`, `shift_minutes`, `employee_id_new`) VALUES
(1, 3, NULL, '2026-03-27', '0000-00-00 00:00:00', NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-03-27 07:17:19', '2026-04-05 12:25:17', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(2, 3, NULL, '2026-03-28', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MANUAL', 'EARLY_OUT', NULL, 0, NULL, NULL, NULL, '2026-03-27 16:46:35', '2026-04-05 12:25:23', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(3, 1, NULL, '2026-03-28', '2026-03-28 09:03:23', '2026-03-28 09:03:31', 'MANUAL', 'EARLY_OUT', NULL, 0, NULL, NULL, NULL, '2026-03-27 17:01:53', '2026-04-05 12:25:26', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(4, 2, NULL, '2026-03-28', '2026-03-28 09:04:55', '2026-03-28 09:16:05', 'MANUAL', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-03-27 17:04:55', '2026-04-05 12:25:31', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(5, 6, NULL, '2026-03-28', '2026-03-28 10:59:33', NULL, 'MANUAL', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-03-27 18:59:33', '2026-04-05 12:25:35', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(6, 7, NULL, '2026-03-28', '2026-03-28 11:56:06', NULL, 'MANUAL', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-03-27 19:56:06', '2026-03-27 19:56:06', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(7, 8, NULL, '2026-03-28', '2026-03-28 17:36:20', '2026-03-28 17:37:39', 'MANUAL', 'LATE', NULL, 0, NULL, NULL, NULL, '2026-03-28 01:36:20', '2026-04-05 12:25:40', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(8, 4, NULL, '2026-03-28', '2026-03-28 15:44:30', NULL, 'QR', 'PENDING_APPROVAL', NULL, 1, 3, '', '2026-04-06 01:57:03', '2026-03-28 06:44:30', '2026-04-05 09:57:03', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(9, 5, NULL, '2026-03-28', '2026-03-28 23:13:41', NULL, 'MANUAL', 'ON_LEAVE', NULL, 1, 3, '', '2026-04-06 00:59:43', '2026-03-28 07:13:41', '2026-04-05 12:25:44', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(10, 3, NULL, '2026-03-29', '2026-03-29 16:06:47', '2026-03-29 16:06:53', 'MANUAL', 'EARLY_OUT', NULL, 1, 3, '', '2026-04-02 19:29:21', '2026-03-29 00:06:47', '2026-04-05 12:25:50', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(11, 4, NULL, '2026-03-29', '2026-03-29 16:57:44', '2026-03-29 16:58:24', 'QR', 'PENDING_APPROVAL', NULL, 1, 3, '', '2026-04-02 19:29:18', '2026-03-29 00:57:44', '2026-04-02 03:29:18', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(12, 6, NULL, '2026-03-29', '2026-03-29 17:15:18', NULL, 'QR', 'PRESENT', NULL, 1, 3, 'awdaw', '2026-04-02 19:29:02', '2026-03-29 01:15:18', '2026-04-05 12:25:54', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(13, 5, NULL, '2026-03-30', '2026-03-30 09:49:20', NULL, 'QR', 'PENDING_APPROVAL', NULL, 1, 3, '', '2026-04-02 14:33:42', '2026-03-29 17:49:20', '2026-04-01 22:33:42', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(14, 1, 2, '2026-04-05', '2026-04-05 19:24:55', '2026-04-05 19:26:31', 'QR', 'PRESENT', NULL, 1, 3, '', '2026-04-05 19:28:24', '2026-04-05 03:24:55', '2026-04-05 03:28:24', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 1, NULL, '2026-04-06', '2026-04-06 02:56:46', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-04-05 10:56:46', '2026-04-05 10:56:46', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 8, NULL, '2026-04-06', '2026-04-06 03:37:20', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-04-05 11:37:20', '2026-04-05 11:37:20', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(300, 1, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(301, 2, 1, '2026-01-05', '2026-01-05 08:15:00', '2026-01-05 17:00:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 8.75, 8.00, 0.75, 1, 1, 1, 15, 0, 480, NULL),
(302, 3, 1, '2026-01-05', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 0.00, 0.00, 0.00, 1, 1, 1, 0, 0, 480, NULL),
(303, 4, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 16:30:00', 'QR', 'EARLY_OUT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 8.50, 8.00, 0.50, 1, 1, 1, 0, 30, 480, NULL),
(304, 5, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 17:30:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.50, 8.00, 1.50, 1, 1, 1, 0, 0, 480, NULL),
(305, 13, 2, '2026-01-05', '2026-01-05 09:30:00', '2026-01-05 16:30:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 7.00, 6.50, 0.50, 1, 1, 1, 30, 0, 360, NULL),
(306, 6, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(307, 7, 1, '2026-01-05', NULL, NULL, 'SYSTEM', 'ON_LEAVE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 0.00, 0.00, 0.00, 1, 1, 1, 0, 0, 480, NULL),
(308, 8, 1, '2026-01-05', '2026-01-05 08:05:00', '2026-01-05 17:00:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 8.92, 8.00, 0.92, 1, 1, 1, 5, 0, 480, NULL),
(309, 9, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(310, 10, 1, '2026-01-05', '2026-01-05 08:25:00', '2026-01-05 17:00:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 8.58, 8.00, 0.58, 1, 1, 1, 25, 0, 480, NULL),
(311, 11, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 17:30:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.50, 8.00, 1.50, 1, 1, 1, 0, 0, 480, NULL),
(312, 14, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(313, 1, 1, '2026-01-20', '2026-01-20 08:10:00', '2026-01-20 17:30:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.33, 8.00, 1.33, 1, 1, 1, 10, 0, 480, NULL),
(314, 2, 1, '2026-01-20', '2026-01-20 08:00:00', '2026-01-20 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(315, 3, 1, '2026-01-20', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 0.00, 0.00, 0.00, 1, 1, 1, 0, 0, 480, NULL),
(316, 13, 2, '2026-01-20', '2026-01-20 10:00:00', '2026-01-20 16:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 6.00, 6.00, 0.00, 1, 1, 1, 0, 0, 360, NULL),
(317, 6, 1, '2026-01-20', '2026-01-20 08:00:00', '2026-01-20 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(318, 7, 1, '2026-01-20', '2026-01-20 08:20:00', '2026-01-20 17:00:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 8.67, 8.00, 0.67, 1, 1, 1, 20, 0, 480, NULL),
(319, 8, 1, '2026-01-20', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 0.00, 0.00, 0.00, 1, 1, 1, 0, 0, 480, NULL),
(320, 9, 1, '2026-01-20', '2026-01-20 08:00:00', '2026-01-20 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(321, 10, 1, '2026-01-20', '2026-01-20 08:00:00', '2026-01-20 16:30:00', 'QR', 'EARLY_OUT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 8.50, 8.00, 0.50, 1, 1, 1, 0, 30, 480, NULL),
(322, 11, 1, '2026-01-20', '2026-01-20 08:00:00', '2026-01-20 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(323, 14, 1, '2026-01-20', '2026-01-20 08:00:00', '2026-01-20 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(324, 1, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(325, 2, 1, '2026-02-05', '2026-02-05 08:40:00', '2026-02-05 17:00:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 8.33, 8.00, 0.33, 1, 1, 1, 40, 0, 480, NULL),
(326, 3, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:30:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.50, 8.00, 1.50, 1, 1, 1, 0, 0, 480, NULL),
(327, 13, 2, '2026-02-05', NULL, NULL, 'SYSTEM', 'ON_LEAVE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 0.00, 0.00, 0.00, 1, 1, 1, 0, 0, 360, NULL),
(328, 6, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(329, 7, 1, '2026-02-05', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 0.00, 0.00, 0.00, 1, 1, 1, 0, 0, 480, NULL),
(330, 8, 1, '2026-02-05', '2026-02-05 08:10:00', '2026-02-05 17:00:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 8.83, 8.00, 0.83, 1, 1, 1, 10, 0, 480, NULL),
(331, 9, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(332, 10, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(333, 11, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:30:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.50, 8.00, 1.50, 1, 1, 1, 0, 0, 480, NULL),
(334, 14, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 12:11:11', '2026-04-05 12:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(0, 4, NULL, '2026-04-06', '2026-04-06 05:14:40', NULL, 'QR', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-04-05 13:14:40', '2026-04-05 13:14:40', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 5, NULL, '2026-04-06', '2026-04-06 05:16:31', NULL, 'QR', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-04-05 13:16:31', '2026-04-05 13:16:31', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 11, NULL, '2026-04-06', '2026-04-06 05:19:14', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-04-05 13:19:14', '2026-04-05 13:19:14', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 3, 7, '2026-04-06', '2026-04-06 12:38:54', NULL, 'QR', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-04-05 20:38:54', '2026-04-05 20:38:54', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 1, NULL, '2026-03-13', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 1, NULL, '2026-03-11', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 1, NULL, '2026-03-24', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 1, NULL, '2026-03-10', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 1, NULL, '2026-04-04', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 1, NULL, '2026-03-23', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 2, NULL, '2026-03-17', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 2, NULL, '2026-04-04', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 2, NULL, '2026-03-13', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 2, NULL, '2026-03-27', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 2, NULL, '2026-03-31', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 2, NULL, '2026-03-14', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 3, NULL, '2026-03-19', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 3, NULL, '2026-03-18', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 3, NULL, '2026-03-31', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 3, NULL, '2026-04-02', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 3, NULL, '2026-03-12', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 3, NULL, '2026-03-11', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 4, NULL, '2026-03-24', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 4, NULL, '2026-03-15', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 4, NULL, '2026-04-05', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 4, NULL, '2026-03-12', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 4, NULL, '2026-04-03', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 4, NULL, '2026-03-08', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 4, NULL, '2026-04-04', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 5, NULL, '2026-03-29', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 5, NULL, '2026-03-16', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 5, NULL, '2026-03-08', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 5, NULL, '2026-04-05', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 5, NULL, '2026-03-15', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 5, NULL, '2026-04-04', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 6, NULL, '2026-03-11', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 6, NULL, '2026-03-25', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 6, NULL, '2026-03-22', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 6, NULL, '2026-03-09', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 6, NULL, '2026-03-14', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 6, NULL, '2026-04-02', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 7, NULL, '2026-03-26', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 7, NULL, '2026-03-31', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 7, NULL, '2026-03-10', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 7, NULL, '2026-03-21', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 7, NULL, '2026-03-14', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 7, NULL, '2026-03-13', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 8, NULL, '2026-03-24', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 8, NULL, '2026-03-27', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 8, NULL, '2026-04-03', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 8, NULL, '2026-04-04', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 8, NULL, '2026-03-16', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 8, NULL, '2026-03-22', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 9, NULL, '2026-03-22', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 9, NULL, '2026-03-26', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 9, NULL, '2026-03-08', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 9, NULL, '2026-03-14', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 9, NULL, '2026-03-09', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 9, NULL, '2026-03-12', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 9, NULL, '2026-03-24', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 10, NULL, '2026-03-30', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 10, NULL, '2026-03-10', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 10, NULL, '2026-03-24', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 10, NULL, '2026-03-12', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 10, NULL, '2026-03-17', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 10, NULL, '2026-03-22', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(0, 10, NULL, '2026-03-27', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 07:32:47', '2026-04-07 07:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('recruitment','payroll','time','compliance','workforce','employee','learning','performance','engagement_relations','exit','clinic','employee_portal') NOT NULL,
  `theme` enum('light','dark') DEFAULT 'light',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `account_status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `theme`, `created_at`, `profile_pic`, `employee_id`, `account_status`) VALUES
(1, 'hr_payroll', '$2y$10$YSkTSwrSdqSBsF2e.pfyq.mNCCIF7ijV4h/s1pAc8Q7KlQHzbQTmq', 'Russell Ike', 'payroll', 'light', '2026-03-06 05:13:06', NULL, NULL, 'Active'),
(2, 'hr_recruitment', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Administrator', 'recruitment', 'light', '2026-03-06 10:46:33', NULL, NULL, 'Active'),
(3, 'hr_time', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Jose Mari', 'time', 'light', '2026-03-06 10:47:07', NULL, NULL, 'Active'),
(4, 'hr_employee', '$2y$10$DDo9507ZVqsctCAKjpbtOO34yDPyekruKRuwbTk/ulWcF7q7E3qtS', 'someone', 'employee', 'light', '2026-03-06 10:47:55', NULL, '1', 'Active'),
(5, 'hr_compliance', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'comply', 'compliance', 'light', '2026-03-06 10:48:19', NULL, NULL, 'Active'),
(6, 'hr_workforce', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'force', 'workforce', 'light', '2026-03-06 10:48:43', NULL, NULL, 'Active'),
(7, 'hr_learning', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'learn', 'learning', 'light', '2026-03-06 10:49:22', NULL, NULL, 'Active'),
(8, 'hr_performance', '$2y$10$/aFKLVK.xloqiY31X4T.dOPKY2AnnkrpaME4f2z.l4LhQurY1/Zzy', 'Perform', 'performance', 'light', '2026-03-06 10:49:46', 'user_8.jpg', NULL, 'Active'),
(9, 'hr_engagement', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'engage', 'engagement_relations', 'light', '2026-03-06 10:50:37', NULL, '1', 'Active'),
(10, 'hr_exit', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'exit', 'exit', 'light', '2026-03-06 10:51:04', NULL, NULL, 'Active'),
(11, 'hr_clinic', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'clinic', 'clinic', 'light', '2026-03-11 08:20:55', NULL, NULL, 'Active'),
(12, 'learning_admin', '\\.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Learning Administrator', 'learning', 'light', '2026-03-21 20:35:30', NULL, NULL, 'Active'),
(13, 'employee', '$2y$10$COg/dGZyfIfKjbHbFeh8LOLRCR797mtfau7xdSH8H4eOsp51eyFFe', 'Adolf Hitler', 'employee_portal', 'light', '2026-03-23 18:06:12', NULL, NULL, 'Active'),
(14, 'john_doe', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'John Doe', 'employee', 'light', '2026-04-05 02:36:06', NULL, NULL, 'Active'),
(15, 'jane_smith', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Jane Smith', 'employee', 'light', '2026-04-05 02:38:06', NULL, NULL, 'Active'),
(16, 'mike_johnson', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Mike Johnson', 'employee', 'light', '2026-04-05 02:38:06', NULL, NULL, 'Active'),
(17, 'sarah_williams', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Sarah Williams', 'employee', 'light', '2026-04-05 02:38:06', NULL, NULL, 'Active'),
(18, 'david_brown', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'David Brown', 'employee', 'light', '2026-04-05 02:38:06', NULL, NULL, 'Active'),
(19, 'emily_davis', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Emily Davis', 'employee', 'light', '2026-04-05 02:38:18', NULL, NULL, 'Active'),
(20, 'robert_wilson', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Robert Wilson', 'employee', 'light', '2026-04-05 02:38:18', NULL, NULL, 'Active'),
(21, 'jessica_martinez', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Jessica Martinez', 'employee', 'light', '2026-04-05 02:38:18', NULL, NULL, 'Active'),
(22, 'test_employee_9', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Test Employee', 'employee', 'light', '2026-04-05 02:38:18', NULL, NULL, 'Active'),
(23, 'test_employee_10', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Test Employee', 'employee', 'light', '2026-04-05 02:38:30', NULL, NULL, 'Active'),
(24, 'admin_user', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Admin User', 'employee', 'light', '2026-04-05 02:38:30', NULL, NULL, 'Active'),
(25, 'placer_jan_russel_c_12', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Placer, Jan Russel C.', 'employee', 'light', '2026-04-05 02:38:30', NULL, NULL, 'Active'),
(26, 'placer_jan_russel_c_13', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Placer, Jan Russel C.', 'employee', 'light', '2026-04-05 02:38:30', NULL, NULL, 'Active'),
(27, 'jhon_carlo_garcia', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Jhon Carlo Garcia', 'employee', 'light', '2026-04-05 02:38:30', NULL, NULL, 'Active'),
(28, 'carlo', '$2y$10$t4hkBFA0UERwt9hZZiY62uo1ypCdNhkvuXGGi/8yQ8/97SNnWHUBO', 'Jhon Carlo Garcia', 'employee', 'light', '2026-04-05 01:34:44', NULL, '35', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `eer_announcements`
--
ALTER TABLE `eer_announcements`
  ADD PRIMARY KEY (`eer_announcements_id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`);

--
-- Indexes for table `eer_award_history`
--
ALTER TABLE `eer_award_history`
  ADD PRIMARY KEY (`eer_award_history_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `eer_award_votes`
--
ALTER TABLE `eer_award_votes`
  ADD UNIQUE KEY `uniq_award_vote` (`award_history_id`,`voter_user_id`),
  ADD KEY `award_history_id` (`award_history_id`),
  ADD KEY `voter_user_id` (`voter_user_id`);

--
-- Indexes for table `eer_badges`
--
ALTER TABLE `eer_badges`
  ADD PRIMARY KEY (`eer_badge_id`);

--
-- Indexes for table `eer_comments`
--
ALTER TABLE `eer_comments`
  ADD PRIMARY KEY (`eer_comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `eer_forums`
--
ALTER TABLE `eer_forums`
  ADD PRIMARY KEY (`eer_forum_id`);

--
-- Indexes for table `eer_grievances`
--
ALTER TABLE `eer_grievances`
  ADD PRIMARY KEY (`eer_grievance_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`);

--
-- Indexes for table `eer_grievance_attendance_links`
--
ALTER TABLE `eer_grievance_attendance_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grievance_id` (`grievance_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `attendance_id` (`attendance_id`);

--
-- Indexes for table `eer_grievance_updates`
--
ALTER TABLE `eer_grievance_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_update_grievance` (`grievance_id`),
  ADD KEY `fk_update_user` (`updated_by_user_id`);

--
-- Indexes for table `eer_groups`
--
ALTER TABLE `eer_groups`
  ADD PRIMARY KEY (`eer_group_id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`);

--
-- Indexes for table `eer_group_members`
--
ALTER TABLE `eer_group_members`
  ADD PRIMARY KEY (`eer_group_member_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `eer_messages`
--
ALTER TABLE `eer_messages`
  ADD PRIMARY KEY (`eer_message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `eer_notifications`
--
ALTER TABLE `eer_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notification_employee` (`employee_id`);

--
-- Indexes for table `eer_policies`
--
ALTER TABLE `eer_policies`
  ADD PRIMARY KEY (`eer_policy_id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`);

--
-- Indexes for table `eer_projects`
--
ALTER TABLE `eer_projects`
  ADD PRIMARY KEY (`eer_project_id`);

--
-- Indexes for table `eer_reactions`
--
ALTER TABLE `eer_reactions`
  ADD PRIMARY KEY (`eer_reaction_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `eer_recognitions`
--
ALTER TABLE `eer_recognitions`
  ADD PRIMARY KEY (`eer_recognition_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `eer_replies`
--
ALTER TABLE `eer_replies`
  ADD PRIMARY KEY (`eer_reply_id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `eer_rewards`
--
ALTER TABLE `eer_rewards`
  ADD PRIMARY KEY (`eer_reward_id`);

--
-- Indexes for table `eer_reward_redemptions`
--
ALTER TABLE `eer_reward_redemptions`
  ADD PRIMARY KEY (`eer_reward_redemption_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `reward_id` (`reward_id`);

--
-- Indexes for table `eer_social_posts`
--
ALTER TABLE `eer_social_posts`
  ADD PRIMARY KEY (`eer_social_post_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `eer_surveys`
--
ALTER TABLE `eer_surveys`
  ADD PRIMARY KEY (`eer_survey_id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`),
  ADD KEY `created_by_employee_id` (`created_by_employee_id`),
  ADD KEY `fk_eer_surveys_pm360` (`feedback_id`);

--
-- Indexes for table `eer_survey_answers`
--
ALTER TABLE `eer_survey_answers`
  ADD PRIMARY KEY (`eer_survey_answer_id`),
  ADD KEY `response_id` (`response_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `eer_survey_feedback`
--
ALTER TABLE `eer_survey_feedback`
  ADD PRIMARY KEY (`eer_survey_feedback_id`);

--
-- Indexes for table `eer_survey_feedback_id`
--
ALTER TABLE `eer_survey_feedback_id`
  ADD PRIMARY KEY (`eer_survey_feedback_id_id`);

--
-- Indexes for table `eer_survey_questions`
--
ALTER TABLE `eer_survey_questions`
  ADD PRIMARY KEY (`eer_survey_question_id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- Indexes for table `eer_survey_responses`
--
ALTER TABLE `eer_survey_responses`
  ADD PRIMARY KEY (`eer_survey_response_id`),
  ADD KEY `survey_id` (`survey_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `fk_response_target_employee` (`target_employee_id`);

--
-- Indexes for table `eer_survey_targets`
--
ALTER TABLE `eer_survey_targets`
  ADD PRIMARY KEY (`eer_survey_target_id`),
  ADD KEY `survey_id` (`survey_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `employee_badges`
--
ALTER TABLE `employee_badges`
  ADD PRIMARY KEY (`employee_badge_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `badge_id` (`badge_id`);

--
-- Indexes for table `grievance_payroll`
--
ALTER TABLE `grievance_payroll`
  ADD PRIMARY KEY (`grievance_id`),
  ADD KEY `fk_payroll_employee` (`employee_id`);

--
-- Indexes for table `pm_360_feedback`
--
ALTER TABLE `pm_360_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_appraisals`
--
ALTER TABLE `pm_appraisals`
  ADD PRIMARY KEY (`appraisal_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_reports`
--
ALTER TABLE `pm_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pr_deductions`
--
ALTER TABLE `pr_deductions`
  ADD PRIMARY KEY (`deduction_id`);

--
-- Indexes for table `pr_employee_adjustments`
--
ALTER TABLE `pr_employee_adjustments`
  ADD PRIMARY KEY (`adjustment_id`),
  ADD KEY `fk_emp_adj_employee` (`employee_id`);

--
-- Indexes for table `pr_employee_benefits`
--
ALTER TABLE `pr_employee_benefits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `idx_employee_id` (`employee_id`);

--
-- Indexes for table `pr_payslips`
--
ALTER TABLE `pr_payslips`
  ADD PRIMARY KEY (`payslip_id`),
  ADD KEY `fk_payslips_employee` (`employee_id`),
  ADD KEY `idx_is_exit_settlement` (`is_exit_settlement`),
  ADD KEY `fk_settlement_id` (`settlement_id`),
  ADD KEY `fk_resignation_id` (`resignation_id`);

--
-- Indexes for table `pr_payslip_items`
--
ALTER TABLE `pr_payslip_items`
  ADD PRIMARY KEY (`payslip_item_id`);

--
-- Indexes for table `pr_runs`
--
ALTER TABLE `pr_runs`
  ADD PRIMARY KEY (`run_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eer_announcements`
--
ALTER TABLE `eer_announcements`
  MODIFY `eer_announcements_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `eer_award_history`
--
ALTER TABLE `eer_award_history`
  MODIFY `eer_award_history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `eer_badges`
--
ALTER TABLE `eer_badges`
  MODIFY `eer_badge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `eer_comments`
--
ALTER TABLE `eer_comments`
  MODIFY `eer_comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `eer_forums`
--
ALTER TABLE `eer_forums`
  MODIFY `eer_forum_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `eer_grievances`
--
ALTER TABLE `eer_grievances`
  MODIFY `eer_grievance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `eer_grievance_attendance_links`
--
ALTER TABLE `eer_grievance_attendance_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eer_grievance_updates`
--
ALTER TABLE `eer_grievance_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `eer_groups`
--
ALTER TABLE `eer_groups`
  MODIFY `eer_group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `eer_group_members`
--
ALTER TABLE `eer_group_members`
  MODIFY `eer_group_member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `eer_messages`
--
ALTER TABLE `eer_messages`
  MODIFY `eer_message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `eer_notifications`
--
ALTER TABLE `eer_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `eer_policies`
--
ALTER TABLE `eer_policies`
  MODIFY `eer_policy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `eer_projects`
--
ALTER TABLE `eer_projects`
  MODIFY `eer_project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `eer_reactions`
--
ALTER TABLE `eer_reactions`
  MODIFY `eer_reaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `eer_recognitions`
--
ALTER TABLE `eer_recognitions`
  MODIFY `eer_recognition_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `eer_replies`
--
ALTER TABLE `eer_replies`
  MODIFY `eer_reply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `eer_rewards`
--
ALTER TABLE `eer_rewards`
  MODIFY `eer_reward_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `eer_reward_redemptions`
--
ALTER TABLE `eer_reward_redemptions`
  MODIFY `eer_reward_redemption_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `eer_social_posts`
--
ALTER TABLE `eer_social_posts`
  MODIFY `eer_social_post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `eer_surveys`
--
ALTER TABLE `eer_surveys`
  MODIFY `eer_survey_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `eer_survey_answers`
--
ALTER TABLE `eer_survey_answers`
  MODIFY `eer_survey_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `eer_survey_feedback`
--
ALTER TABLE `eer_survey_feedback`
  MODIFY `eer_survey_feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `eer_survey_feedback_id`
--
ALTER TABLE `eer_survey_feedback_id`
  MODIFY `eer_survey_feedback_id_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `eer_survey_questions`
--
ALTER TABLE `eer_survey_questions`
  MODIFY `eer_survey_question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `eer_survey_responses`
--
ALTER TABLE `eer_survey_responses`
  MODIFY `eer_survey_response_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `eer_survey_targets`
--
ALTER TABLE `eer_survey_targets`
  MODIFY `eer_survey_target_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `employee_badges`
--
ALTER TABLE `employee_badges`
  MODIFY `employee_badge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pm_360_feedback`
--
ALTER TABLE `pm_360_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pm_appraisals`
--
ALTER TABLE `pm_appraisals`
  MODIFY `appraisal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pm_reports`
--
ALTER TABLE `pm_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `pr_employee_benefits`
--
ALTER TABLE `pr_employee_benefits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pr_payslips`
--
ALTER TABLE `pr_payslips`
  MODIFY `payslip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `pr_payslip_items`
--
ALTER TABLE `pr_payslip_items`
  MODIFY `payslip_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `pr_runs`
--
ALTER TABLE `pr_runs`
  MODIFY `run_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `eer_comments`
--
ALTER TABLE `eer_comments`
  ADD CONSTRAINT `fk_comment_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `fk_comment_post` FOREIGN KEY (`post_id`) REFERENCES `eer_social_posts` (`eer_social_post_id`);

--
-- Constraints for table `eer_grievances`
--
ALTER TABLE `eer_grievances`
  ADD CONSTRAINT `fk_grievance_creator` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_grievance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `fk_grievances_created_by` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grievances_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `eer_grievance_attendance_links`
--
ALTER TABLE `eer_grievance_attendance_links`
  ADD CONSTRAINT `fk_link_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_link_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `eer_grievances` (`eer_grievance_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_links_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_links_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `eer_grievances` (`eer_grievance_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `eer_grievance_updates`
--
ALTER TABLE `eer_grievance_updates`
  ADD CONSTRAINT `fk_update_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `eer_grievances` (`eer_grievance_id`),
  ADD CONSTRAINT `fk_update_user` FOREIGN KEY (`updated_by_user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `eer_messages`
--
ALTER TABLE `eer_messages`
  ADD CONSTRAINT `fk_msg_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `eer_notifications`
--
ALTER TABLE `eer_notifications`
  ADD CONSTRAINT `fk_notification_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `eer_recognitions`
--
ALTER TABLE `eer_recognitions`
  ADD CONSTRAINT `fk_recognition_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `eer_social_posts`
--
ALTER TABLE `eer_social_posts`
  ADD CONSTRAINT `fk_post_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `fk_post_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `eer_surveys`
--
ALTER TABLE `eer_surveys`
  ADD CONSTRAINT `fk_eer_surveys_pm360` FOREIGN KEY (`feedback_id`) REFERENCES `pm_360_feedback` (`feedback_id`) ON DELETE CASCADE;

--
-- Constraints for table `eer_survey_responses`
--
ALTER TABLE `eer_survey_responses`
  ADD CONSTRAINT `fk_response_evaluator` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_response_target_employee` FOREIGN KEY (`target_employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `grievance_payroll`
--
ALTER TABLE `grievance_payroll`
  ADD CONSTRAINT `fk_payroll_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payroll_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `eer_grievances` (`eer_grievance_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pm_reports`
--
ALTER TABLE `pm_reports`
  ADD CONSTRAINT `pm_reports_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `pr_employee_adjustments`
--
ALTER TABLE `pr_employee_adjustments`
  ADD CONSTRAINT `fk_emp_adj_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `pr_employee_benefits`
--
ALTER TABLE `pr_employee_benefits`
  ADD CONSTRAINT `pr_employee_benefits_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
