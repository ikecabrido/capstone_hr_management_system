-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 08, 2026 at 02:16 PM
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
-- Database: `hr-management`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_medical_record` (IN `p_record_id` VARCHAR(20), IN `p_patient_id` VARCHAR(50), IN `p_chief_complaint` TEXT, IN `p_diagnosis` TEXT, IN `p_treatment` TEXT, IN `p_consultation_type` VARCHAR(20), IN `p_attending_physician` VARCHAR(150), IN `p_created_by` VARCHAR(100))   BEGIN
    -- Insert medical record
    INSERT INTO cm_medical_records (
        record_id, patient_id, chief_complaint, diagnosis, treatment,
        consultation_type, attending_physician, created_by
    ) VALUES (
        p_record_id, p_patient_id, p_chief_complaint, p_diagnosis, p_treatment,
        p_consultation_type, p_attending_physician, p_created_by
    );
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `audit_id` int(11) NOT NULL,
  `action` enum('CREATE','UPDATE','DELETE','VIEW') NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `biometric_devices`
--

CREATE TABLE `biometric_devices` (
  `id` int(11) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `device_type` varchar(50) NOT NULL DEFAULT 'fingerprint',
  `manufacturer` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `ip_address` varchar(100) DEFAULT NULL,
  `port` int(11) DEFAULT 4370,
  `mac_address` varchar(100) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'active',
  `last_sync` datetime DEFAULT NULL,
  `capacity` int(11) DEFAULT 500,
  `enrolled_count` int(11) DEFAULT 0,
  `firmware_version` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `biometric_enrollments`
--

CREATE TABLE `biometric_enrollments` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `biometric_id` varchar(100) DEFAULT NULL,
  `enrollment_type` varchar(50) DEFAULT 'fingerprint',
  `finger_position` varchar(50) DEFAULT NULL,
  `quality_score` decimal(5,2) DEFAULT 0.00,
  `enrollment_status` varchar(30) DEFAULT 'pending',
  `enrollment_date` datetime DEFAULT NULL,
  `verification_count` int(11) DEFAULT 0,
  `failed_attempts` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `biometric_logs`
--

CREATE TABLE `biometric_logs` (
  `id` int(11) NOT NULL,
  `device_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `biometric_id` varchar(100) DEFAULT NULL,
  `log_datetime` datetime DEFAULT NULL,
  `punch_type` varchar(20) DEFAULT 'in',
  `quality_score` decimal(5,2) DEFAULT 0.00,
  `raw_data` text DEFAULT NULL,
  `is_matched` tinyint(1) DEFAULT 0,
  `match_confidence` decimal(5,2) DEFAULT 0.00,
  `status` varchar(30) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cm_clinic_reports`
--

CREATE TABLE `cm_clinic_reports` (
  `report_id` varchar(20) NOT NULL,
  `report_type` enum('Daily','Weekly','Monthly','Custom','Annual') DEFAULT NULL,
  `report_date` date NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`report_data`)),
  `generated_by` varchar(100) DEFAULT NULL,
  `status` enum('Generated','Processing','Error') DEFAULT 'Generated',
  `file_path` varchar(500) DEFAULT NULL,
  `file_format` enum('PDF','Excel','HTML','JSON') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cm_clinic_reports`
--

INSERT INTO `cm_clinic_reports` (`report_id`, `report_type`, `report_date`, `start_date`, `end_date`, `report_data`, `generated_by`, `status`, `file_path`, `file_format`, `created_at`, `updated_at`) VALUES
('REPDAI20260406034323', 'Daily', '2026-04-06', '2026-04-06', '2026-04-06', '{\"date\":\"2026-04-06\",\"summary\":{\"total_visits\":1,\"emergency_cases\":1},\"details\":[],\"trends\":[]}', 'clinic', 'Generated', NULL, 'HTML', '2026-04-06 01:43:21', '2026-04-06 01:43:21'),
('REPDAI20260406034421', 'Daily', '2026-04-06', '2026-04-06', '2026-04-06', '{\"date\":\"2026-04-06\",\"summary\":{\"total_visits\":1,\"emergency_cases\":1},\"details\":[],\"trends\":[]}', 'clinic', 'Generated', NULL, 'Excel', '2026-04-06 01:44:19', '2026-04-06 01:44:19'),
('REPDAI20260406034458', 'Daily', '2026-04-06', '2026-04-06', '2026-04-06', '{\"date\":\"2026-04-06\",\"summary\":{\"total_visits\":1,\"emergency_cases\":1},\"details\":[],\"trends\":[]}', 'clinic', 'Generated', NULL, 'PDF', '2026-04-06 01:44:56', '2026-04-06 01:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `cm_departments`
--

CREATE TABLE `cm_departments` (
  `department_id` varchar(10) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `department_head` int(11) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cm_departments`
--

INSERT INTO `cm_departments` (`department_id`, `department_name`, `department_head`, `location`, `contact_phone`, `status`, `created_at`) VALUES
('ACAD', 'Academic Affairs', NULL, 'Main Building', NULL, 'Active', '2026-04-05 17:32:09'),
('ADMIN', 'Administration', NULL, 'Admin Building', NULL, 'Active', '2026-04-05 17:32:09'),
('FINANCE', 'Finance', NULL, 'Admin Building', NULL, 'Active', '2026-04-05 17:32:09'),
('HR', 'Human Resources', NULL, 'Admin Building', NULL, 'Active', '2026-04-05 17:32:09'),
('IT', 'Information Technology', NULL, 'Tech Building', NULL, 'Active', '2026-04-05 17:32:09'),
('LIB', 'Library', NULL, 'Library Building', NULL, 'Active', '2026-04-05 17:32:09'),
('MAINT', 'Maintenance', NULL, 'Utility Building', NULL, 'Active', '2026-04-05 17:32:09'),
('MED', 'Medical Services', NULL, 'Clinic Building', NULL, 'Active', '2026-04-05 17:32:09'),
('SEC', 'Security', NULL, 'Gate House', NULL, 'Active', '2026-04-05 17:32:09');

-- --------------------------------------------------------

--
-- Table structure for table `cm_document_attachments`
--

CREATE TABLE `cm_document_attachments` (
  `attachment_id` varchar(20) NOT NULL,
  `record_id` varchar(20) DEFAULT NULL,
  `patient_id` varchar(50) DEFAULT NULL,
  `document_type` enum('Lab Result','X-Ray','Prescription','Medical Certificate','Other') DEFAULT NULL,
  `document_name` varchar(200) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploaded_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cm_emergency_cases`
--

CREATE TABLE `cm_emergency_cases` (
  `case_id` varchar(20) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `incident_date` datetime NOT NULL,
  `incident_type` enum('Accident','Medical Emergency','Injury','Other') DEFAULT NULL,
  `severity_level` enum('Low','Medium','High','Critical','Minor') DEFAULT NULL,
  `chief_complaint` text NOT NULL,
  `initial_assessment` text DEFAULT NULL,
  `treatment_provided` text DEFAULT NULL,
  `attending_staff` varchar(255) DEFAULT NULL,
  `case_status` enum('Active','Resolved','Transferred','Closed','Open') DEFAULT 'Active',
  `ambulance_called` tinyint(1) DEFAULT 0,
  `ambulance_arrival_time` datetime DEFAULT NULL,
  `parents_notified` tinyint(1) DEFAULT 0,
  `parent_notification_time` datetime DEFAULT NULL,
  `witness_names` text DEFAULT NULL,
  `transfer_hospital` varchar(200) DEFAULT NULL,
  `follow_up_required` tinyint(1) DEFAULT 0,
  `follow_up_date` date DEFAULT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cm_emergency_cases`
--

INSERT INTO `cm_emergency_cases` (`case_id`, `patient_id`, `incident_date`, `incident_type`, `severity_level`, `chief_complaint`, `initial_assessment`, `treatment_provided`, `attending_staff`, `case_status`, `ambulance_called`, `ambulance_arrival_time`, `parents_notified`, `parent_notification_time`, `witness_names`, `transfer_hospital`, `follow_up_required`, `follow_up_date`, `contact_person`, `contact_phone`, `notes`, `created_at`, `updated_at`, `created_by`) VALUES
('EM20261760', 'PAT1', '2026-04-06 08:19:00', 'Injury', 'Critical', 'ftufuyfiytty8p9yt8i', 'drftijop[;rftguyik', 'wsredftijokpl[', 'sw4tsrftijo', 'Transferred', 1, '2026-04-06 10:20:00', 1, '2026-04-06 00:22:00', '', NULL, 0, '0000-00-00', NULL, NULL, NULL, '2026-04-06 02:20:47', '2026-04-06 02:20:47', 'clinic'),
('EM20265135', 'PAT1', '2026-04-06 12:05:00', 'Medical Emergency', 'Medium', 'zczscsz', 'cszcsz', 'cszcz', 'dada', 'Open', 0, NULL, 0, NULL, 'cszczs', NULL, 1, '2026-04-07', NULL, NULL, NULL, '2026-04-05 22:06:35', '2026-04-05 22:06:35', 'clinic');

-- --------------------------------------------------------

--
-- Table structure for table `cm_medical_records`
--

CREATE TABLE `cm_medical_records` (
  `record_id` varchar(20) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `visit_date` datetime NOT NULL,
  `chief_complaint` text NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `consultation_type` enum('Walk-in','Appointment','Emergency','Follow-up') DEFAULT NULL,
  `status` enum('Completed','Pending','Follow-up') DEFAULT 'Pending',
  `attending_physician` varchar(150) DEFAULT NULL,
  `vital_signs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vital_signs`)),
  `medications_prescribed` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cm_medical_records`
--

INSERT INTO `cm_medical_records` (`record_id`, `patient_id`, `visit_date`, `chief_complaint`, `diagnosis`, `treatment`, `consultation_type`, `status`, `attending_physician`, `vital_signs`, `medications_prescribed`, `notes`, `follow_up_date`, `created_at`, `updated_at`, `created_by`) VALUES
('MR20267557', 'PAT1', '2026-04-06 00:09:27', 'fdaw', 'dawdadwa', 'dwadaw', 'Walk-in', 'Pending', 'dwad', '{\"bp_systolic\":\"110\",\"bp_diastolic\":\"50\",\"heart_rate\":\"65\",\"temperature\":\"39.00\",\"weight\":\"65.00\",\"height\":\"165.00\"}', 'dwada', 'dwadaw', '2026-04-06', '2026-04-05 22:09:27', '2026-04-05 22:09:27', 'clinic');

-- --------------------------------------------------------

--
-- Table structure for table `cm_medicine_inventory`
--

CREATE TABLE `cm_medicine_inventory` (
  `medicine_id` varchar(20) NOT NULL,
  `medicine_name` varchar(200) NOT NULL,
  `generic_name` varchar(200) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `dosage_form` enum('Tablet','Capsule','Liquid','Injection','Ointment','Other') DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `current_stock` int(11) DEFAULT 0,
  `reorder_level` int(11) DEFAULT 10,
  `unit_cost` decimal(8,2) DEFAULT NULL,
  `selling_price` decimal(8,2) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `supplier` varchar(200) DEFAULT NULL,
  `manufacturer` varchar(200) DEFAULT NULL,
  `storage_requirements` text DEFAULT NULL,
  `status` enum('Available','Low Stock','Out of Stock','Expired') DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cm_medicine_inventory`
--

INSERT INTO `cm_medicine_inventory` (`medicine_id`, `medicine_name`, `generic_name`, `category`, `dosage_form`, `strength`, `current_stock`, `reorder_level`, `unit_cost`, `selling_price`, `expiry_date`, `supplier`, `manufacturer`, `storage_requirements`, `status`, `created_at`, `updated_at`, `created_by`) VALUES
('1', 'Paracetamol', 'Acetaminophen', 'Analgesic', 'Tablet', '500mg', 500, 50, 2.50, NULL, '2025-12-31', 'MEDSUP001', NULL, NULL, '', '2026-04-05 17:32:09', '2026-04-05 22:09:33', NULL),
('2', 'Ibuprofen', 'Ibuprofen', 'Analgesic', 'Tablet', '400mg', 300, 30, 3.75, NULL, '2025-11-30', 'MEDSUP001', NULL, NULL, '', '2026-04-05 17:32:09', '2026-04-05 22:09:33', NULL),
('3', 'Amoxicillin', 'Amoxicillin', 'Antibiotic', 'Capsule', '500mg', 10, 25, 8.50, NULL, '2026-10-31', 'MEDSUP002', NULL, NULL, 'Low Stock', '2026-04-05 17:32:09', '2026-04-06 01:26:22', NULL),
('4', 'Omeprazole', 'Omeprazole', 'Antacid', 'Capsule', '20mg', 150, 20, 6.25, NULL, '2026-01-31', 'MEDSUP002', NULL, NULL, '', '2026-04-05 17:32:09', '2026-04-05 22:09:33', NULL),
('5', 'Loratadine', 'Loratadine', 'Antihistamine', 'Tablet', '10mg', 400, 40, 4.00, NULL, '2025-09-30', 'MEDSUP001', NULL, NULL, '', '2026-04-05 17:32:09', '2026-04-05 22:09:33', NULL),
('MED20263495', 'biogesic', 'tablet', 'Analgesic', NULL, NULL, 110, 10, NULL, NULL, '2026-08-21', NULL, NULL, NULL, 'Available', '2026-04-06 01:47:36', '2026-04-06 01:47:36', 'clinic');

-- --------------------------------------------------------

--
-- Table structure for table `cm_medicine_usage_logs`
--

CREATE TABLE `cm_medicine_usage_logs` (
  `log_id` varchar(20) NOT NULL,
  `medicine_id` varchar(20) NOT NULL,
  `record_id` varchar(20) DEFAULT NULL,
  `usage_date` datetime NOT NULL,
  `quantity_used` int(11) NOT NULL,
  `remaining_stock` int(11) NOT NULL,
  `purpose` varchar(200) DEFAULT NULL,
  `used_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cm_patients`
--

CREATE TABLE `cm_patients` (
  `patient_id` varchar(50) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `blood_type` varchar(10) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `current_medications` text DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `patient_type` enum('Student','Staff','Faculty','Visitor') DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cm_patients`
--

INSERT INTO `cm_patients` (`patient_id`, `employee_id`, `first_name`, `last_name`, `middle_name`, `email`, `phone`, `address`, `birth_date`, `gender`, `blood_type`, `allergies`, `medical_conditions`, `current_medications`, `emergency_contact_name`, `emergency_contact_phone`, `patient_type`, `status`, `created_at`, `updated_at`) VALUES
('PAT1', 1, 'John', 'Doe', '', 'john.doe@example.com', '123-456-7890', '', NULL, NULL, 'O+', 'dwdadwad', 'dwadadwad', 'dwada', 'dwada', '09942033282', 'Staff', 'Active', '2026-04-05 22:05:20', '2026-04-05 22:05:20'),
('PAT42', 42, 'Philip', 'Arevalo', '', 'boros.dominator@gmail.com', '09916117933', '', NULL, NULL, 'AB+', 'N/A', 'N/A', 'N/A', '12312312312', 'WEFA123', 'Staff', 'Active', '2026-04-06 09:43:22', '2026-04-06 09:43:22');

-- --------------------------------------------------------

--
-- Table structure for table `cm_suppliers`
--

CREATE TABLE `cm_suppliers` (
  `supplier_id` varchar(20) NOT NULL,
  `supplier_name` varchar(200) NOT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cm_suppliers`
--

INSERT INTO `cm_suppliers` (`supplier_id`, `supplier_name`, `contact_person`, `phone`, `email`, `address`, `payment_terms`, `status`, `created_at`) VALUES
('MEDSUP001', 'MediCare Pharmaceuticals', 'John Smith', '123-456-7890', 'john@medicare.com', NULL, NULL, 'Active', '2026-04-05 17:32:09'),
('MEDSUP002', 'HealthPlus Supplies', 'Maria Santos', '098-765-4321', 'maria@healthplus.com', NULL, NULL, 'Active', '2026-04-05 17:32:09');

-- --------------------------------------------------------

--
-- Table structure for table `cm_vital_signs`
--

CREATE TABLE `cm_vital_signs` (
  `vital_sign_id` varchar(20) NOT NULL,
  `record_id` varchar(20) NOT NULL,
  `blood_pressure_systolic` int(11) DEFAULT NULL,
  `blood_pressure_diastolic` int(11) DEFAULT NULL,
  `heart_rate` int(11) DEFAULT NULL,
  `respiratory_rate` int(11) DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `oxygen_saturation` decimal(3,1) DEFAULT NULL,
  `blood_sugar` decimal(5,1) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `recorded_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `target_audience` varchar(100) DEFAULT 'all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_announcements`
--

INSERT INTO `eer_announcements` (`eer_announcements_id`, `title`, `content`, `created_by_user_id`, `created_at`, `target_audience`) VALUES
(1, 'Company Town Hall', 'Join tomorrow', 1, '2026-03-29 04:27:57', 'all'),
(2, 'Clean-up Day', 'This Friday', 2, '2026-03-29 04:27:57', 'all'),
(4, 'Company Town Hall', 'Join tomorrow', 9, '2026-03-29 12:36:01', 'all'),
(10, '890', 'jkl', 9, '2026-04-04 04:15:32', 'all'),
(11, 'lop', 'jkl', 9, '2026-04-04 04:15:41', 'all'),
(0, 'qwerty', 'ytrewq', 9, '2026-04-06 14:31:48', 'all');

-- --------------------------------------------------------

--
-- Table structure for table `eer_award_history`
--

CREATE TABLE `eer_award_history` (
  `eer_award_history_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `award_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_award_history`
--

INSERT INTO `eer_award_history` (`eer_award_history_id`, `employee_id`, `award_name`) VALUES
(1, 1, 'Employee of the Month'),
(2, 2, 'Best Team Player');

-- --------------------------------------------------------

--
-- Table structure for table `eer_badges`
--

CREATE TABLE `eer_badges` (
  `eer_badge_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_badges`
--

INSERT INTO `eer_badges` (`eer_badge_id`, `name`, `description`) VALUES
(1, 'Top Performer', 'Outstanding performance'),
(2, 'Team Player', 'Great collaboration');

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
(1, 1, 1, 'Nice!', '2026-04-05 19:20:03', NULL, 'employee'),
(2, 2, 1, 'Count me', '2026-04-05 19:20:03', NULL, 'employee');

-- --------------------------------------------------------

--
-- Table structure for table `eer_grievances`
--

CREATE TABLE `eer_grievances` (
  `eer_grievance_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `status` enum('Submitted','Under Review','Resolved','Closed') DEFAULT 'Submitted',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_grievances`
--

INSERT INTO `eer_grievances` (`eer_grievance_id`, `employee_id`, `subject`, `description`, `assigned_to`, `status`, `created_at`) VALUES
(1, 3, 'Payroll Issue', 'Missing bonus', 7, 'Under Review', '2026-04-05 19:20:03'),
(2, 2, 'Laptop Issue', 'System freezing', 2, 'Resolved', '2026-04-05 19:20:03');

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
(1, 1, 'Escalated', 4, '2026-04-05 19:20:03');

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
(1, 'HR Team', 1, '2026-03-30 22:08:31'),
(2, 'IT Team', 2, '2026-03-30 22:08:31'),
(3, 'eer', NULL, '2026-04-02 23:51:07'),
(4, 'pwesto', NULL, '2026-04-04 05:22:06');

-- --------------------------------------------------------

--
-- Table structure for table `eer_group_members`
--

CREATE TABLE `eer_group_members` (
  `eer_group_member_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 1, 2, 'Send report', '2026-04-05 19:20:03'),
(2, 2, 1, 'Done', '2026-04-05 19:20:03'),
(0, 9, 36, 'olol', '2026-04-06 10:18:42');

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
(1, 1, 'New survey available', 'survey', 0, '2026-04-05 19:20:03');

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
(25, 8, 9, 9, 'employee', 'heart', '2026-04-02 22:41:09'),
(26, 9, NULL, 9, 'user', 'like', '2026-04-04 05:22:19'),
(27, 1, NULL, NULL, 'user', 'like', '2026-04-06 04:32:34');

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
  `category` varchar(100) DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_recognitions`
--

INSERT INTO `eer_recognitions` (`eer_recognition_id`, `sender_id`, `receiver_id`, `message`, `points`, `created_at`, `category`) VALUES
(1, 1, 2, 'Great job', 20, '2026-03-29 04:27:57', 'general'),
(2, 2, 3, 'Well done', 15, '2026-03-29 04:27:57', 'general'),
(11, 9, 2, 'great', 10, '2026-03-29 13:34:43', 'general'),
(13, 9, 1, 'eq3ewq', 10, '2026-04-04 04:26:42', 'general');

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
(1, 1, 1, NULL, 2, NULL, 'employee', 'Thanks for the update!', NULL, '2026-04-05 19:25:00'),
(2, 2, 2, NULL, NULL, 9, 'user', 'Looking forward to it @Jane Smith', 15, '2026-04-05 19:26:00'),
(0, 2, 2, NULL, NULL, 9, 'user', 'thank you', NULL, '2026-04-06 12:32:07');

-- --------------------------------------------------------

--
-- Table structure for table `eer_rewards`
--

CREATE TABLE `eer_rewards` (
  `eer_reward_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `points_required` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_rewards`
--

INSERT INTO `eer_rewards` (`eer_reward_id`, `name`, `description`, `points_required`) VALUES
(1, 'Day Off', NULL, 100),
(2, 'Gift Card', NULL, 50);

-- --------------------------------------------------------

--
-- Table structure for table `eer_reward_redemptions`
--

CREATE TABLE `eer_reward_redemptions` (
  `eer_reward_redemption_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `reward_id` int(11) DEFAULT NULL,
  `points_used` int(11) DEFAULT NULL,
  `redeemed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_reward_redemptions`
--

INSERT INTO `eer_reward_redemptions` (`eer_reward_redemption_id`, `employee_id`, `reward_id`, `points_used`, `redeemed_at`) VALUES
(1, 1, 1, 100, '2026-03-29 04:27:57'),
(2, 2, 2, 50, '2026-03-29 04:27:57');

-- --------------------------------------------------------

--
-- Table structure for table `eer_shared_files`
--

CREATE TABLE `eer_shared_files` (
  `eer_shared_file_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(10) DEFAULT NULL,
  `uploaded_by_user_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_shared_files`
--

INSERT INTO `eer_shared_files` (`eer_shared_file_id`, `file_name`, `file_path`, `file_size`, `file_type`, `uploaded_by_user_id`, `description`, `created_at`) VALUES
(1, '7eec7035-5cdf-47f8-9280-aedaaddb3561.jpg', 'uploads/social_files/1775242830_7eec7035-5cdf-47f8-9280-aedaaddb3561.jpg', 167058, 'jpg', 9, 'eqweqw', '2026-04-04 03:00:30'),
(2, '77dfbc5e-921e-4a4a-9b93-522236e5f6c0.jpg', 'uploads/social_files/1775251370_77dfbc5e-921e-4a4a-9b93-522236e5f6c0.jpg', 162429, 'jpg', 9, 'hehe', '2026-04-04 05:22:50');

-- --------------------------------------------------------

--
-- Table structure for table `eer_social_posts`
--

CREATE TABLE `eer_social_posts` (
  `eer_social_post_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `author_type` enum('employee','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_social_posts`
--

INSERT INTO `eer_social_posts` (`eer_social_post_id`, `employee_id`, `content`, `created_at`, `author_type`) VALUES
(1, 2, 'New project!', '2026-04-05 19:20:03', 'employee'),
(2, 3, 'Soccer game', '2026-04-05 19:20:03', 'employee'),
(3, 1, 'Company update', '2026-04-05 19:20:03', 'employee');

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
  `created_by_employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_surveys`
--

INSERT INTO `eer_surveys` (`eer_survey_id`, `title`, `created_by_user_id`, `is_anonymous`, `survey_type`, `created_by_employee_id`) VALUES
(1, 'Satisfaction', 1, 0, 'engagement', NULL),
(2, 'Remote Work', 2, 0, 'engagement', NULL),
(3, 'remote work', 9, 0, 'engagement', NULL),
(4, 'rumors', 9, 0, 'engagement', NULL),
(5, 'qwerty', 9, 0, 'engagement', NULL),
(6, 'int', 9, 0, 'engagement', NULL);

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
(1, 1, 1, '5'),
(2, 1, 2, 'Good environment'),
(3, 2, 1, '4'),
(4, 2, 2, 'More benefits');

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
(1, 3, 'How satisfied are you with your job?', 'text'),
(2, 4, 'qweqwe', 'text'),
(3, 5, 'ask me', 'text'),
(4, 6, 'rty', 'text');

-- --------------------------------------------------------

--
-- Table structure for table `eer_survey_responses`
--

CREATE TABLE `eer_survey_responses` (
  `eer_survey_response_id` int(11) NOT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_survey_responses`
--

INSERT INTO `eer_survey_responses` (`eer_survey_response_id`, `survey_id`, `employee_id`, `answers`) VALUES
(1, 1, 1, '{\"q1\":5,\"q2\":\"Good environment\"}'),
(2, 1, 2, '{\"q1\":4,\"q2\":\"More benefits\"}'),
(3, 2, 3, '{\"q1\":\"Yes\",\"q2\":4}');

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
(1, 1, 1, 'completed'),
(2, 1, 2, 'completed'),
(3, 2, 3, 'pending');

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
  `sex` enum('Male','Female','Others','') DEFAULT NULL,
  `teacher_qualification` varchar(255) DEFAULT NULL,
  `gender` enum('Male','Female','Others','') DEFAULT NULL,
  `marital_status` enum('Single','Married','Widowed','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `employee_no`, `user_id`, `full_name`, `address`, `contact_number`, `email`, `department`, `position`, `date_hired`, `employment_status`, `created_at`, `updated_at`, `birthdate`, `sex`, `teacher_qualification`, `gender`, `marital_status`) VALUES
(1, 'T001', 4, 'John Doe', 'Not Provided', '123-456-7890', 'john.doe@example.com', 'IT', 'Software Engineer', '2023-01-01', 'Active', '2026-03-22 07:51:30', '2026-04-07 14:53:37', '1990-05-15', 'Male', NULL, 'Male', 'Single'),
(2, 'T002', 15, 'Jane Smith', 'Not Provided', '098-765-4321', 'jane.smith@example.com', 'HR', 'HR Manager', '2023-02-15', 'Active', '2026-03-22 07:51:30', '2026-04-07 14:53:37', '1988-08-22', 'Female', NULL, 'Male', 'Single'),
(3, 'T003', 16, 'Mike Johnson', 'Not Provided', '555-123-4567', 'mike.johnson@example.com', 'Finance', 'Accountant', '2023-03-10', 'Active', '2026-03-22 07:51:30', '2026-04-07 14:53:37', '1992-03-10', 'Male', NULL, 'Male', 'Married'),
(4, 'T004', 17, 'Sarah Williams', 'Not Provided', '555-789-0123', 'sarah.williams@example.com', 'Operations', 'Operations Manager', '2023-04-01', 'Active', '2026-03-27 11:25:55', '2026-04-07 14:53:37', '1991-07-18', 'Female', NULL, 'Female', 'Married'),
(5, 'T005', 18, 'David Brown', 'Not Provided', '555-456-7890', 'david.brown@example.com', 'IT', 'Junior Developer', '2023-05-15', 'Active', '2026-03-27 11:25:55', '2026-04-07 14:53:37', '1995-11-30', 'Male', NULL, 'Male', 'Married'),
(6, 'T006', 19, 'Emily Davis', 'Not Provided', '555-321-6543', 'emily.davis@example.com', 'HR', 'HR Specialist', '2023-06-01', 'Active', '2026-03-27 11:25:55', '2026-04-07 14:53:37', '1993-02-14', 'Female', NULL, 'Female', 'Married'),
(7, 'T007', 20, 'Robert Wilson', 'Not Provided', '555-654-3210', 'robert.wilson@example.com', 'Finance', 'Financial Analyst', '2023-07-10', 'Active', '2026-03-27 11:25:55', '2026-04-07 14:53:37', '1989-09-20', 'Male', NULL, 'Male', 'Single'),
(8, 'T008', 21, 'Jessica Martinez', 'Not Provided', '555-987-6543', 'jessica.martinez@example.com', 'Operations', 'Staff Coordinator', '2023-08-20', 'Active', '2026-03-27 11:25:55', '2026-04-07 14:53:37', '1994-06-08', 'Female', NULL, 'Female', 'Married'),
(9, 'T009', 22, 'Raniel Quebada', 'sampol', '09123456789', 'test@email.com', 'IT', 'Tester', NULL, 'Active', '2026-03-29 09:27:35', '2026-04-07 14:53:37', '1987-01-25', 'Others', NULL, 'Others', 'Widowed'),
(10, 'T010', 23, 'Irene Ronato', 'Not Provided', '09123456789', 'test@email.com', 'IT', 'Tester', NULL, 'Active', '2026-03-29 09:27:55', '2026-04-07 14:53:37', '1986-04-12', 'Others', NULL, 'Others', 'Widowed'),
(11, 'T011', 24, 'Jayson Paigma', 'Not Provided', '09123456789', 'admin@bcp.com', 'Administration', 'System Administrator', NULL, 'Active', '2026-03-29 10:10:37', '2026-04-07 14:53:37', '1989-10-05', 'Others', NULL, 'Others', 'Widowed'),
(12, 'T012', 25, 'Ronald, De Guzman D.', 'Not Provided', '09876543211', 'ronalddeguzman@gmail.com', 'Finance', 'hr', '2026-04-15', 'Active', '2026-04-05 06:55:08', '2026-04-07 14:53:37', '1990-12-20', 'Male', 'Profed', 'Male', 'Single'),
(13, 'T013', 26, 'Placer, Jan Russel C.', 'Not Provided', '09876543211', 'placerrussel5@gmail.com', 'Finance', 'hr', '2026-04-15', 'Active', '2026-04-05 06:55:39', '2026-04-07 14:53:37', '1991-08-15', 'Male', 'Profed', 'Male', 'Single'),
(14, 'T014', 27, 'Jhon Carlo Garcia', 'Brgy.Assumption', '09916117933', 'jhoncarlogarcia30@gmail.com', 'test', 'sawadika', '2026-04-06', 'Active', '2026-04-05 16:03:55', '2026-04-07 14:53:37', '1996-06-30', 'Male', NULL, 'Male', 'Single'),
(36, 'T015', NULL, 'De Guzman, Ronald J.', 'Not Provided', '09876543211', 'ronalddeguzman@gmail.com', 'Finance', 'hr', '2026-04-06', 'Active', '2026-04-05 21:30:57', '2026-04-07 14:53:37', '1980-02-12', 'Male', NULL, 'Male', 'Single'),
(37, 'T016', NULL, 'malana Jeoffrey', 'Not Provided', '09876543211', 'ronalddeguzman@gmail.com', 'Finance', 'hr', '2026-04-06', 'Active', '2026-04-06 01:37:35', '2026-04-07 14:53:37', '1983-06-06', 'Male', NULL, 'Male', 'Single'),
(38, '', NULL, 'michael kanan', 'Not Provided', '09876543211', 'ronalddeguzman@gmail.com', 'Finance', 'hr', '2026-04-06', 'Active', '2026-04-06 01:43:53', '2026-04-07 14:53:37', '1985-05-06', 'Male', NULL, 'Male', 'Single'),
(39, '', NULL, 'Danmark Baldonido', 'Area f', '09853926174', 'danmark1201@gmail.com', 'test', 'TEST', '2026-04-06', 'Active', '2026-04-06 02:07:57', '2026-04-07 14:53:37', '1992-09-10', 'Male', NULL, 'Male', 'Single'),
(40, '', NULL, 'David Jaurigue', 'dsadsad', '09916117933', 'aviidjaurigue07@gmail.com', 'directress', 'directress', '2026-04-06', 'Active', '2026-04-06 03:52:51', '2026-04-07 14:53:37', '1990-11-08', 'Male', NULL, 'Male', 'Married'),
(41, '', NULL, 'Mark Christian Pangilinan', 'Brgy.Assumption', '09556683080', 'boros.jaurigue@gmail.com', 'IT', 'IT prog', '2026-04-06', 'Active', '2026-04-06 03:52:52', '2026-04-07 14:53:37', '1998-03-25', 'Male', NULL, 'Male', 'Single'),
(42, 'T042', NULL, 'Philip Arevalo', 'STARMALL', '09916117933', 'boros.dominator@gmail.com', 'it', 'Prof', '2026-04-06', 'Active', '2026-04-06 03:52:53', '2026-04-07 14:53:37', '1997-07-14', 'Male', NULL, 'Male', 'Single'),
(46, '', NULL, 'Macjane Desipolo', 'Phase 5, Minuyan Proper', '111-222-3333', 'test.user1@example.com', 'IT', '', '2024-01-01', 'Active', '2026-08-02 14:19:36', '2026-08-02 14:19:36', NULL, NULL, NULL, NULL, NULL),
(47, '', NULL, 'Juan Dela Cruz', '101 Test Blvd', '111-222-3334', 'test.user2@example.com', 'IT', '', '2024-01-02', 'Active', '2026-08-02 14:19:36', '2026-08-02 14:19:36', NULL, NULL, NULL, NULL, NULL),
(48, '', NULL, 'Rusty Talento', 'Towerville', '111-222-3335', 'test.user3@example.com', 'IT', '', '2024-01-03', 'Active', '2026-08-02 14:19:36', '2026-08-02 14:19:36', NULL, NULL, NULL, NULL, NULL),
(49, '', NULL, 'Jay-R Alemana', 'Providence', '111-222-3336', 'test.user4@example.com', 'IT', '', '2024-01-04', 'Active', '2026-08-02 14:19:36', '2026-08-02 14:19:36', NULL, NULL, NULL, NULL, NULL),
(50, '', NULL, 'Jayr Dela Cruz', 'Towerville', '111-222-3337', 'test.user5@example.com', 'IT', '', '2024-01-05', 'Active', '2026-08-02 14:19:36', '2026-08-02 14:19:36', NULL, NULL, NULL, NULL, NULL),
(51, '', NULL, 'Warren Bantanos', 'Towerville', '111-222-3338', 'test.user6@example.com', 'IT', '', '2024-01-06', 'Active', '2026-08-02 14:19:36', '2026-08-02 14:19:36', NULL, NULL, NULL, NULL, NULL);

--
-- Triggers `employees`
--
DELIMITER $$
CREATE TRIGGER `after_employee_update` AFTER UPDATE ON `employees` FOR EACH ROW BEGIN
    -- Log department change
    IF OLD.department != NEW.department AND NEW.department IS NOT NULL THEN
        INSERT INTO employee_change_history 
        (employee_id, change_type, old_value, new_value, effective_date, updated_by)
        VALUES 
        (NEW.employee_id, 'Department Transfer', OLD.department, NEW.department, CURDATE(), 'System Trigger');
    END IF;
    
    -- Log position change
    IF OLD.position != NEW.position AND NEW.position IS NOT NULL THEN
        INSERT INTO employee_change_history 
        (employee_id, change_type, old_value, new_value, effective_date, updated_by)
        VALUES 
        (NEW.employee_id, 'Position Change', OLD.position, NEW.position, CURDATE(), 'System Trigger');
    END IF;
    
    -- Log status change
    IF OLD.employment_status != NEW.employment_status AND NEW.employment_status IS NOT NULL THEN
        INSERT INTO employee_change_history 
        (employee_id, change_type, old_value, new_value, effective_date, updated_by)
        VALUES 
        (NEW.employee_id, 'Status Change', OLD.employment_status, NEW.employment_status, CURDATE(), 'System Trigger');
    END IF;
    
    -- Log name change
    IF OLD.full_name != NEW.full_name AND NEW.full_name IS NOT NULL THEN
        INSERT INTO employee_change_history 
        (employee_id, change_type, old_value, new_value, effective_date, updated_by)
        VALUES 
        (NEW.employee_id, 'Name Change', OLD.full_name, NEW.full_name, CURDATE(), 'System Trigger');
    END IF;
    
    -- Log contact number change
    IF OLD.contact_number != NEW.contact_number AND NEW.contact_number IS NOT NULL THEN
        INSERT INTO employee_change_history 
        (employee_id, change_type, old_value, new_value, effective_date, updated_by)
        VALUES 
        (NEW.employee_id, 'Contact Update', OLD.contact_number, NEW.contact_number, CURDATE(), 'System Trigger');
    END IF;
    
    -- Log email change
    IF OLD.email != NEW.email AND NEW.email IS NOT NULL THEN
        INSERT INTO employee_change_history 
        (employee_id, change_type, old_value, new_value, effective_date, updated_by)
        VALUES 
        (NEW.employee_id, 'Email Update', OLD.email, NEW.email, CURDATE(), 'System Trigger');
    END IF;
    
    -- Log address change
    IF OLD.address != NEW.address AND NEW.address IS NOT NULL THEN
        INSERT INTO employee_change_history 
        (employee_id, change_type, old_value, new_value, effective_date, updated_by)
        VALUES 
        (NEW.employee_id, 'Address Update', OLD.address, NEW.address, CURDATE(), 'System Trigger');
    END IF;
    
    -- Log date hired change
    IF OLD.date_hired != NEW.date_hired AND NEW.date_hired IS NOT NULL THEN
        INSERT INTO employee_change_history 
        (employee_id, change_type, old_value, new_value, effective_date, updated_by)
        VALUES 
        (NEW.employee_id, 'Hire Date Change', OLD.date_hired, NEW.date_hired, CURDATE(), 'System Trigger');
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `employees_contributions`
--

CREATE TABLE `employees_contributions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `contribution_type` enum('sss','pagibig','philhealth') NOT NULL,
  `status` enum('submitted','pending') DEFAULT 'pending',
  `contribution_number` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees_contributions`
--

INSERT INTO `employees_contributions` (`id`, `employee_id`, `contribution_type`, `status`, `contribution_number`, `created_at`, `updated_at`) VALUES
(1, 6, 'sss', 'submitted', '94-9646469-9', '2026-04-06 10:06:41', '2026-04-06 10:06:41'),
(2, 6, 'philhealth', 'submitted', '4548-9549-4865', '2026-04-06 10:06:41', '2026-04-06 10:06:41'),
(3, 6, 'pagibig', 'submitted', '6526-9498-9748', '2026-04-06 10:06:41', '2026-04-06 10:06:41'),
(4, 39, 'sss', 'submitted', '95-6454868-4', '2026-04-06 10:18:39', '2026-04-06 10:18:39'),
(5, 39, 'philhealth', 'submitted', '6816-8684-1681', '2026-04-06 10:18:39', '2026-04-06 10:18:39'),
(6, 39, 'pagibig', 'submitted', '5641-6811-6186', '2026-04-06 10:18:39', '2026-04-06 10:18:39');

-- --------------------------------------------------------

--
-- Table structure for table `employee_accounts`
--

CREATE TABLE `employee_accounts` (
  `account_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `account_status` varchar(50) DEFAULT 'Active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_accounts`
--

INSERT INTO `employee_accounts` (`account_id`, `employee_id`, `username`, `email`, `password_hash`, `account_status`, `last_login`, `created_at`, `updated_at`) VALUES
(6, 1, 'john.doe', 'john.doe@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(7, 2, 'jane.smith', 'jane.smith@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(8, 3, 'mike.johnson', 'mike.johnson@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(9, 4, 'sarah.williams', 'sarah.williams@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(10, 5, 'david.brown', 'david.brown@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(11, 6, 'emily.davis', 'emily.davis@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(12, 7, 'robert.wilson', 'robert.wilson@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(13, 8, 'jessica.martinez', 'jessica.martinez@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50');

-- --------------------------------------------------------

--
-- Table structure for table `employee_badges`
--

CREATE TABLE `employee_badges` (
  `employee_badge_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `badge_id` int(11) DEFAULT NULL,
  `awarded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_badges`
--

INSERT INTO `employee_badges` (`employee_badge_id`, `employee_id`, `badge_id`, `awarded_at`) VALUES
(2, 1, 1, '2026-03-30 22:08:31'),
(3, 2, 2, '2026-03-30 22:08:31');

-- --------------------------------------------------------

--
-- Table structure for table `employee_certifications`
--

CREATE TABLE `employee_certifications` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `certification_name` varchar(255) NOT NULL,
  `issuing_organization` varchar(255) NOT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `certification_number` varchar(100) DEFAULT NULL,
  `status` enum('Active','Expired','Revoked') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_change_history`
--

CREATE TABLE `employee_change_history` (
  `history_id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `change_type` varchar(50) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `effective_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `updated_by` varchar(100) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_change_history`
--

INSERT INTO `employee_change_history` (`history_id`, `employee_id`, `change_type`, `old_value`, `new_value`, `effective_date`, `remarks`, `updated_by`, `ip_address`, `created_at`) VALUES
(1, '12', 'Status Change', 'Active', 'Resigned', '2026-04-06', NULL, 'System Trigger', NULL, '2026-04-05 19:11:13'),
(2, '12', 'Status Change', 'Resigned', 'Active', '2026-04-06', NULL, 'System Trigger', NULL, '2026-04-05 20:15:05'),
(3, '1', 'Status Change', 'Active', 'Resigned', '2026-04-06', NULL, 'System Trigger', NULL, '2026-04-06 04:15:38'),
(4, '1', 'Status Change', 'Resigned', 'Active', '2026-04-06', NULL, 'System Trigger', NULL, '2026-04-06 04:16:28'),
(5, '9', 'Name Change', 'Test Employee', 'Jan Russel Placer', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:47:13'),
(6, '9', 'Name Change', 'Jan Russel Placer', 'Raniel Quebada', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:47:29'),
(7, '10', 'Name Change', 'Test Employee', 'Irene Ronato', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:47:47'),
(8, '11', 'Name Change', 'Admin User', 'Jayson Paigma', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:48:00'),
(9, '12', 'Name Change', 'Placer, Jan Russel C.', 'Ronald, De Guzman D.', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:48:26'),
(10, '1', 'Address Update', '123 Main St', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37'),
(11, '2', 'Address Update', '456 Oak Ave', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37'),
(12, '3', 'Address Update', '789 Pine Rd', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37'),
(13, '4', 'Address Update', '321 Elm St', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37'),
(14, '5', 'Address Update', '654 Maple Dr', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37'),
(15, '6', 'Address Update', '987 Cedar Ln', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37'),
(16, '7', 'Address Update', '147 Oak St', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37'),
(17, '8', 'Address Update', '258 Pine Ave', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37'),
(18, '12', 'Address Update', '', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37'),
(19, '13', 'Address Update', '', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37'),
(20, '36', 'Address Update', '', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37'),
(21, '37', 'Address Update', '', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37'),
(22, '38', 'Address Update', '', 'Not Provided', '2026-04-07', NULL, 'System Trigger', NULL, '2026-04-07 14:53:37');

-- --------------------------------------------------------

--
-- Table structure for table `employee_contributions`
--

CREATE TABLE `employee_contributions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `contribution_type` enum('sss','pagibig','philhealth') NOT NULL,
  `status` enum('submitted','pending') DEFAULT 'pending',
  `contribution_number` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_dependents`
--

CREATE TABLE `employee_dependents` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `dependent_name` varchar(255) NOT NULL,
  `relationship` varchar(100) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_education`
--

CREATE TABLE `employee_education` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `institution` varchar(255) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `field_of_study` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_emergency_contacts`
--

CREATE TABLE `employee_emergency_contacts` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `contact_name` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `relationship` varchar(100) NOT NULL,
  `alternate_number` varchar(50) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_languages`
--

CREATE TABLE `employee_languages` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `language_name` varchar(100) NOT NULL,
  `proficiency_level` enum('Basic','Conversational','Fluent','Native') DEFAULT 'Basic',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_skills`
--

CREATE TABLE `employee_skills` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `skill_name` varchar(255) NOT NULL,
  `proficiency_level` enum('Beginner','Intermediate','Advanced','Expert') DEFAULT 'Beginner',
  `years_of_experience` int(11) DEFAULT 0,
  `last_used` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_work_experience`
--

CREATE TABLE `employee_work_experience` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exit_archive`
--

CREATE TABLE `exit_archive` (
  `id` int(11) NOT NULL,
  `archive_type` enum('resignation','settlement','interview','document','survey','transfer_plan','transfer_item','termination') NOT NULL,
  `original_id` int(11) NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `original_created_by` int(11) DEFAULT NULL,
  `archived_by` int(11) NOT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archive_reason` varchar(255) DEFAULT NULL,
  `archive_data` longtext DEFAULT NULL COMMENT 'JSON data of the original record',
  `restored` tinyint(1) DEFAULT 0,
  `restored_by` int(11) DEFAULT NULL,
  `restored_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exit_archive`
--

INSERT INTO `exit_archive` (`id`, `archive_type`, `original_id`, `employee_id`, `title`, `description`, `content`, `status`, `original_created_by`, `archived_by`, `archived_at`, `archive_reason`, `archive_data`, `restored`, `restored_by`, `restored_at`) VALUES
(1, 'interview', 5, '1', 'Exit Interview - Employee 1', 'Archived exit interview record', '{\"id\":5,\"employee_id\":\"1\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"10:00:00\",\"location\":\"Main Office\",\"notes\":\"Test interview inserted for dummy data.\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-08-02 13:38:45\",\"updated_at\":\"2026-08-02 16:18:56\",\"exit_case_type\":\"resignation\",\"exit_case_id\":57,\"completed_at\":\"2026-08-02 16:18:56\"}', 'completed', NULL, 10, '2026-08-02 08:42:59', 'Bulk archive', '{\"id\":5,\"employee_id\":\"1\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"10:00:00\",\"location\":\"Main Office\",\"notes\":\"Test interview inserted for dummy data.\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-08-02 13:38:45\",\"updated_at\":\"2026-08-02 16:18:56\",\"exit_case_type\":\"resignation\",\"exit_case_id\":57,\"completed_at\":\"2026-08-02 16:18:56\"}', 1, 3, '2026-08-02 09:15:01'),
(2, 'interview', 6, '1', 'Exit Interview - Employee 1', 'Archived exit interview record', '{\"id\":6,\"employee_id\":\"1\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"07:00:00\",\"location\":\"Virtual\",\"notes\":\"uiahsdhawodahjwd\",\"status\":\"cancelled\",\"feedback\":null,\"created_at\":\"2026-08-02 14:28:40\",\"updated_at\":\"2026-08-02 16:21:24\",\"exit_case_type\":\"termination\",\"exit_case_id\":1,\"completed_at\":null}', 'cancelled', NULL, 10, '2026-08-02 08:43:00', 'Bulk archive', '{\"id\":6,\"employee_id\":\"1\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"07:00:00\",\"location\":\"Virtual\",\"notes\":\"uiahsdhawodahjwd\",\"status\":\"cancelled\",\"feedback\":null,\"created_at\":\"2026-08-02 14:28:40\",\"updated_at\":\"2026-08-02 16:21:24\",\"exit_case_type\":\"termination\",\"exit_case_id\":1,\"completed_at\":null}', 1, 3, '2026-08-02 09:15:01'),
(3, 'interview', 7, '4', 'Exit Interview - Employee 4', 'Archived exit interview record', '{\"id\":7,\"employee_id\":\"4\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"07:00:00\",\"location\":\"AVR\",\"notes\":\"\",\"status\":\"scheduled\",\"feedback\":null,\"created_at\":\"2026-08-02 16:22:47\",\"updated_at\":\"2026-08-02 16:35:12\",\"exit_case_type\":\"resignation\",\"exit_case_id\":60,\"completed_at\":null}', 'scheduled', NULL, 10, '2026-08-02 08:43:01', 'Bulk archive', '{\"id\":7,\"employee_id\":\"4\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"07:00:00\",\"location\":\"AVR\",\"notes\":\"\",\"status\":\"scheduled\",\"feedback\":null,\"created_at\":\"2026-08-02 16:22:47\",\"updated_at\":\"2026-08-02 16:35:12\",\"exit_case_type\":\"resignation\",\"exit_case_id\":60,\"completed_at\":null}', 1, 3, '2026-08-02 10:05:28'),
(4, 'interview', 4, '39', 'Exit Interview - Employee 39', 'Archived exit interview record', '{\"id\":4,\"employee_id\":\"39\",\"interviewer_id\":9,\"scheduled_date\":\"2026-07-10\",\"scheduled_time\":\"01:00:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-07-28 12:40:20\",\"updated_at\":\"2026-08-02 15:13:04\",\"exit_case_type\":null,\"exit_case_id\":null,\"completed_at\":\"2026-08-02 15:13:04\"}', 'completed', NULL, 10, '2026-08-02 08:43:02', 'Bulk archive', '{\"id\":4,\"employee_id\":\"39\",\"interviewer_id\":9,\"scheduled_date\":\"2026-07-10\",\"scheduled_time\":\"01:00:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-07-28 12:40:20\",\"updated_at\":\"2026-08-02 15:13:04\",\"exit_case_type\":null,\"exit_case_id\":null,\"completed_at\":\"2026-08-02 15:13:04\"}', 1, 3, '2026-08-02 10:05:17'),
(5, 'interview', 3, '39', 'Exit Interview - Employee 39', 'Archived exit interview record', '{\"id\":3,\"employee_id\":\"39\",\"interviewer_id\":2,\"scheduled_date\":\"2026-04-02\",\"scheduled_time\":\"02:30:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-04-08 15:41:34\",\"updated_at\":\"2026-08-02 15:17:26\",\"exit_case_type\":null,\"exit_case_id\":null,\"completed_at\":\"2026-08-02 15:17:26\"}', 'completed', NULL, 10, '2026-08-02 08:43:03', 'Bulk archive', '{\"id\":3,\"employee_id\":\"39\",\"interviewer_id\":2,\"scheduled_date\":\"2026-04-02\",\"scheduled_time\":\"02:30:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-04-08 15:41:34\",\"updated_at\":\"2026-08-02 15:17:26\",\"exit_case_type\":null,\"exit_case_id\":null,\"completed_at\":\"2026-08-02 15:17:26\"}', 1, 3, '2026-08-02 09:15:01'),
(6, 'interview', 4, '39', 'Exit Interview - Employee 39', 'Archived exit interview record', '{\"id\":4,\"employee_id\":\"39\",\"interviewer_id\":9,\"scheduled_date\":\"2026-07-10\",\"scheduled_time\":\"01:00:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-07-28 12:40:20\",\"updated_at\":\"2026-08-02 17:19:03\",\"exit_case_type\":\"resignation\",\"exit_case_id\":0,\"completed_at\":\"2026-08-02 17:19:03\"}', 'completed', NULL, 3, '2026-08-02 09:40:11', 'completed', '{\"id\":4,\"employee_id\":\"39\",\"interviewer_id\":9,\"scheduled_date\":\"2026-07-10\",\"scheduled_time\":\"01:00:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-07-28 12:40:20\",\"updated_at\":\"2026-08-02 17:19:03\",\"exit_case_type\":\"resignation\",\"exit_case_id\":0,\"completed_at\":\"2026-08-02 17:19:03\"}', 0, NULL, NULL),
(7, 'interview', 3, '39', 'Exit Interview - Employee 39', 'Archived exit interview record', '{\"id\":3,\"employee_id\":\"39\",\"interviewer_id\":2,\"scheduled_date\":\"2026-04-02\",\"scheduled_time\":\"02:30:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-04-08 15:41:34\",\"updated_at\":\"2026-08-02 15:17:26\",\"exit_case_type\":\"resignation\",\"exit_case_id\":0,\"completed_at\":\"2026-08-02 15:17:26\"}', 'completed', NULL, 3, '2026-08-02 09:43:26', 'completed\n', '{\"id\":3,\"employee_id\":\"39\",\"interviewer_id\":2,\"scheduled_date\":\"2026-04-02\",\"scheduled_time\":\"02:30:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-04-08 15:41:34\",\"updated_at\":\"2026-08-02 15:17:26\",\"exit_case_type\":\"resignation\",\"exit_case_id\":0,\"completed_at\":\"2026-08-02 15:17:26\"}', 0, NULL, NULL),
(8, 'interview', 4, '39', 'Exit Interview - Employee 39', 'Archived exit interview record', '{\"id\":4,\"employee_id\":\"39\",\"interviewer_id\":9,\"scheduled_date\":\"2026-07-10\",\"scheduled_time\":\"01:00:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-07-28 12:40:20\",\"updated_at\":\"2026-08-02 15:13:04\",\"exit_case_type\":\"resignation\",\"exit_case_id\":0,\"completed_at\":\"2026-08-02 15:13:04\"}', 'completed', NULL, 3, '2026-08-02 09:52:26', 'completed\n', '{\"id\":4,\"employee_id\":\"39\",\"interviewer_id\":9,\"scheduled_date\":\"2026-07-10\",\"scheduled_time\":\"01:00:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-07-28 12:40:20\",\"updated_at\":\"2026-08-02 15:13:04\",\"exit_case_type\":\"resignation\",\"exit_case_id\":0,\"completed_at\":\"2026-08-02 15:13:04\"}', 0, NULL, NULL),
(9, 'interview', 4, '39', 'Exit Interview - Employee 39', 'Archived exit interview record', '{\"id\":4,\"employee_id\":\"39\",\"interviewer_id\":9,\"scheduled_date\":\"2026-07-10\",\"scheduled_time\":\"01:00:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-07-28 12:40:20\",\"updated_at\":\"2026-08-02 15:13:04\",\"exit_case_type\":\"resignation\",\"exit_case_id\":0,\"completed_at\":\"2026-08-02 15:13:04\"}', 'completed', NULL, 3, '2026-08-02 10:04:47', 'complete', '{\"id\":4,\"employee_id\":\"39\",\"interviewer_id\":9,\"scheduled_date\":\"2026-07-10\",\"scheduled_time\":\"01:00:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-07-28 12:40:20\",\"updated_at\":\"2026-08-02 15:13:04\",\"exit_case_type\":\"resignation\",\"exit_case_id\":0,\"completed_at\":\"2026-08-02 15:13:04\"}', 0, NULL, NULL),
(10, 'interview', 7, '4', 'Exit Interview - Employee 4', 'Archived exit interview record', '{\"id\":7,\"employee_id\":\"4\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"07:00:00\",\"location\":\"AVR\",\"notes\":\"\",\"status\":\"scheduled\",\"feedback\":null,\"created_at\":\"2026-08-02 16:22:47\",\"updated_at\":\"2026-08-02 16:35:12\",\"exit_case_type\":\"resignation\",\"exit_case_id\":60,\"completed_at\":null}', 'scheduled', NULL, 3, '2026-08-02 10:11:02', 'test\n', '{\"id\":7,\"employee_id\":\"4\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"07:00:00\",\"location\":\"AVR\",\"notes\":\"\",\"status\":\"scheduled\",\"feedback\":null,\"created_at\":\"2026-08-02 16:22:47\",\"updated_at\":\"2026-08-02 16:35:12\",\"exit_case_type\":\"resignation\",\"exit_case_id\":60,\"completed_at\":null}', 1, 3, '2026-08-02 10:11:23'),
(11, 'interview', 7, '4', 'Exit Interview - Employee 4', 'Archived exit interview record', '{\"id\":7,\"employee_id\":\"4\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"07:00:00\",\"location\":\"AVR\",\"notes\":\"\",\"status\":\"scheduled\",\"feedback\":null,\"created_at\":\"2026-08-02 16:22:47\",\"updated_at\":\"2026-08-02 16:35:12\",\"exit_case_type\":\"resignation\",\"exit_case_id\":60,\"completed_at\":null}', 'scheduled', NULL, 3, '2026-08-02 10:13:33', 'test', '{\"id\":7,\"employee_id\":\"4\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"07:00:00\",\"location\":\"AVR\",\"notes\":\"\",\"status\":\"scheduled\",\"feedback\":null,\"created_at\":\"2026-08-02 16:22:47\",\"updated_at\":\"2026-08-02 16:35:12\",\"exit_case_type\":\"resignation\",\"exit_case_id\":60,\"completed_at\":null}', 1, 3, '2026-08-02 10:16:38'),
(12, 'interview', 7, '4', 'Exit Interview - Employee 4', 'Archived exit interview record', '{\"id\":7,\"employee_id\":\"4\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"07:00:00\",\"location\":\"AVR\",\"notes\":\"\",\"status\":\"scheduled\",\"feedback\":null,\"created_at\":\"2026-08-02 16:22:47\",\"updated_at\":\"2026-08-02 16:35:12\",\"exit_case_type\":\"resignation\",\"exit_case_id\":60,\"completed_at\":null}', 'scheduled', NULL, 3, '2026-08-02 10:16:52', 'test', '{\"id\":7,\"employee_id\":\"4\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"07:00:00\",\"location\":\"AVR\",\"notes\":\"\",\"status\":\"scheduled\",\"feedback\":null,\"created_at\":\"2026-08-02 16:22:47\",\"updated_at\":\"2026-08-02 16:35:12\",\"exit_case_type\":\"resignation\",\"exit_case_id\":60,\"completed_at\":null}', 1, 3, '2026-08-02 10:50:23'),
(13, 'interview', 7, '4', 'Exit Interview - Employee 4', 'Archived exit interview record', '{\"id\":7,\"employee_id\":\"4\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"07:00:00\",\"location\":\"AVR\",\"notes\":\"\",\"status\":\"scheduled\",\"feedback\":null,\"created_at\":\"2026-08-02 16:22:47\",\"updated_at\":\"2026-08-02 16:35:12\",\"exit_case_type\":\"resignation\",\"exit_case_id\":60,\"completed_at\":null}', 'scheduled', NULL, 3, '2026-08-02 10:50:36', 'test', '{\"id\":7,\"employee_id\":\"4\",\"interviewer_id\":10,\"scheduled_date\":\"2026-08-03\",\"scheduled_time\":\"07:00:00\",\"location\":\"AVR\",\"notes\":\"\",\"status\":\"scheduled\",\"feedback\":null,\"created_at\":\"2026-08-02 16:22:47\",\"updated_at\":\"2026-08-02 16:35:12\",\"exit_case_type\":\"resignation\",\"exit_case_id\":60,\"completed_at\":null}', 0, NULL, NULL),
(14, 'transfer_plan', 11, '37', 'Knowledge Transfer Plan #11', 'Archived knowledge transfer plan for employee 37', '{\"id\":11,\"employee_id\":\"37\",\"successor_id\":\"21\",\"start_date\":\"2026-04-13\",\"end_date\":\"2026-04-28\",\"status\":\"active\",\"created_by\":10,\"created_at\":\"2026-04-01 19:50:49\",\"updated_at\":\"2026-08-02 22:05:51\"}', 'active', 10, 3, '2026-08-02 15:11:29', 'test', '{\"id\":11,\"employee_id\":\"37\",\"successor_id\":\"21\",\"start_date\":\"2026-04-13\",\"end_date\":\"2026-04-28\",\"status\":\"active\",\"created_by\":10,\"created_at\":\"2026-04-01 19:50:49\",\"updated_at\":\"2026-08-02 22:05:51\"}', 1, 3, '2026-08-02 15:22:16'),
(15, 'transfer_plan', 11, '37', 'Knowledge Transfer Plan #11', 'Archived knowledge transfer plan for employee 37', '{\"id\":11,\"employee_id\":\"37\",\"successor_id\":\"21\",\"start_date\":\"2026-04-13\",\"end_date\":\"2026-04-28\",\"status\":\"active\",\"created_by\":10,\"created_at\":\"2026-04-01 19:50:49\",\"updated_at\":\"2026-08-02 22:05:51\"}', 'active', 10, 3, '2026-08-02 15:22:04', 'test', '{\"id\":11,\"employee_id\":\"37\",\"successor_id\":\"21\",\"start_date\":\"2026-04-13\",\"end_date\":\"2026-04-28\",\"status\":\"active\",\"created_by\":10,\"created_at\":\"2026-04-01 19:50:49\",\"updated_at\":\"2026-08-02 22:05:51\"}', 0, NULL, NULL),
(16, 'transfer_plan', 12, '40', 'Knowledge Transfer Plan #12', 'Archived knowledge transfer plan for employee 40', '{\"id\":12,\"employee_id\":\"40\",\"successor_id\":\"37\",\"start_date\":\"2026-04-09\",\"end_date\":\"2026-04-23\",\"status\":\"active\",\"created_by\":10,\"created_at\":\"2026-04-08 15:42:24\",\"updated_at\":\"2026-08-02 22:05:51\"}', 'active', 10, 3, '2026-08-02 15:26:59', 'test\n', '{\"id\":12,\"employee_id\":\"40\",\"successor_id\":\"37\",\"start_date\":\"2026-04-09\",\"end_date\":\"2026-04-23\",\"status\":\"active\",\"created_by\":10,\"created_at\":\"2026-04-08 15:42:24\",\"updated_at\":\"2026-08-02 22:05:51\"}', 1, 3, '2026-08-02 15:27:15'),
(17, 'resignation', 57, '1', 'Resignation - Employee 1', 'Archived resignation record', '{\"id\":57,\"employee_id\":\"1\",\"resignation_type\":\"Voluntary(Resignation)\",\"reason\":\"Career advancement opportunity\",\"notice_date\":\"2026-08-01\",\"last_working_date\":\"2026-08-15\",\"comments\":\"Accepted an offer at another company.\",\"submitted_by\":1,\"preclearance_desk_person\":null,\"status\":\"approved\",\"approved_by\":10,\"approved_at\":\"2026-08-02 13:18:40\",\"created_at\":\"2026-08-01 12:30:18\",\"updated_at\":\"2026-08-02 13:18:40\",\"archived_from_status\":null,\"hr_approved_by\":10,\"hr_approved_at\":\"2026-08-01 18:34:23\",\"hr_approval_comments\":\"Accepted an offer at another company.\",\"legal_approved_by\":10,\"legal_approved_at\":\"2026-08-02 13:18:40\",\"legal_approval_comments\":\"Accepted an offer at another company.\",\"reviewed_by\":null,\"reviewed_at\":null,\"review_remarks\":null}', 'approved', 1, 3, '2026-08-02 17:16:42', 'test\n', '{\"id\":57,\"employee_id\":\"1\",\"resignation_type\":\"Voluntary(Resignation)\",\"reason\":\"Career advancement opportunity\",\"notice_date\":\"2026-08-01\",\"last_working_date\":\"2026-08-15\",\"comments\":\"Accepted an offer at another company.\",\"submitted_by\":1,\"preclearance_desk_person\":null,\"status\":\"approved\",\"approved_by\":10,\"approved_at\":\"2026-08-02 13:18:40\",\"created_at\":\"2026-08-01 12:30:18\",\"updated_at\":\"2026-08-02 13:18:40\",\"archived_from_status\":null,\"hr_approved_by\":10,\"hr_approved_at\":\"2026-08-01 18:34:23\",\"hr_approval_comments\":\"Accepted an offer at another company.\",\"legal_approved_by\":10,\"legal_approved_at\":\"2026-08-02 13:18:40\",\"legal_approval_comments\":\"Accepted an offer at another company.\",\"reviewed_by\":null,\"reviewed_at\":null,\"review_remarks\":null}', 1, 10, '2026-08-03 07:35:33'),
(18, 'termination', 6, '49', 'Termination - Employee 49', 'Archived termination record', '{\"id\":6,\"employee_id\":\"49\",\"termination_reason\":\"test\",\"effective_date\":\"2026-08-03\",\"comments\":\"\",\"submitted_by\":3,\"status\":\"pending_review\",\"reviewed_by\":null,\"reviewed_at\":null,\"review_remarks\":null,\"legal_approved_by\":null,\"legal_approved_at\":null,\"legal_approval_comments\":null,\"approved_by\":null,\"approved_at\":null,\"created_at\":\"2026-08-03 01:17:28\",\"updated_at\":\"2026-08-03 01:17:28\"}', 'pending_review', 3, 10, '2026-08-03 08:02:18', 'test', '{\"id\":6,\"employee_id\":\"49\",\"termination_reason\":\"test\",\"effective_date\":\"2026-08-03\",\"comments\":\"\",\"submitted_by\":3,\"status\":\"pending_review\",\"reviewed_by\":null,\"reviewed_at\":null,\"review_remarks\":null,\"legal_approved_by\":null,\"legal_approved_at\":null,\"legal_approval_comments\":null,\"approved_by\":null,\"approved_at\":null,\"created_at\":\"2026-08-03 01:17:28\",\"updated_at\":\"2026-08-03 01:17:28\"}', 1, 10, '2026-08-03 08:13:51'),
(19, 'termination', 1, '1', 'Termination - Employee 1', 'Archived termination record', '{\"id\":1,\"employee_id\":\"1\",\"termination_reason\":\"Violation of company policy\",\"effective_date\":\"2026-08-01\",\"comments\":\"Repeat attendance infractions and failure to comply with workplace rules.\",\"submitted_by\":1,\"status\":\"approved\",\"reviewed_by\":null,\"reviewed_at\":null,\"review_remarks\":null,\"legal_approved_by\":10,\"legal_approved_at\":\"2026-08-02 13:41:30\",\"legal_approval_comments\":\"Repeat attendance infractions and failure to comply with workplace rules.\",\"approved_by\":10,\"approved_at\":\"2026-08-02 13:41:30\",\"created_at\":\"2026-08-01 15:56:47\",\"updated_at\":\"2026-08-02 13:41:30\"}', 'approved', 1, 10, '2026-08-03 08:10:02', 'test', '{\"id\":1,\"employee_id\":\"1\",\"termination_reason\":\"Violation of company policy\",\"effective_date\":\"2026-08-01\",\"comments\":\"Repeat attendance infractions and failure to comply with workplace rules.\",\"submitted_by\":1,\"status\":\"approved\",\"reviewed_by\":null,\"reviewed_at\":null,\"review_remarks\":null,\"legal_approved_by\":10,\"legal_approved_at\":\"2026-08-02 13:41:30\",\"legal_approval_comments\":\"Repeat attendance infractions and failure to comply with workplace rules.\",\"approved_by\":10,\"approved_at\":\"2026-08-02 13:41:30\",\"created_at\":\"2026-08-01 15:56:47\",\"updated_at\":\"2026-08-02 13:41:30\"}', 1, 10, '2026-08-03 08:10:18'),
(20, 'settlement', 32, '4', 'Settlement - Employee 4', 'Archived settlement record', '{\"id\":32,\"employee_id\":\"4\",\"resignation_id\":56,\"basic_salary\":\"25000.00\",\"hra\":\"10000.00\",\"conveyance\":\"19200.00\",\"lta\":\"30000.00\",\"medical_allowance\":\"5000.00\",\"other_allowances\":\"3000.00\",\"provident_fund\":\"3000.00\",\"gratuity\":\"12000.00\",\"notice_pay\":\"0.00\",\"outstanding_loans\":\"0.00\",\"other_deductions\":\"0.00\",\"net_payable\":\"101200.00\",\"settlement_date\":\"2026-04-08\",\"status\":\"draft\",\"approved_by\":null,\"approved_at\":null,\"created_by\":10,\"created_at\":\"2026-04-08 15:36:43\",\"updated_at\":\"2026-04-08 15:36:43\"}', 'draft', 10, 10, '2026-08-06 11:14:54', 'test\n', '{\"id\":32,\"employee_id\":\"4\",\"resignation_id\":56,\"basic_salary\":\"25000.00\",\"hra\":\"10000.00\",\"conveyance\":\"19200.00\",\"lta\":\"30000.00\",\"medical_allowance\":\"5000.00\",\"other_allowances\":\"3000.00\",\"provident_fund\":\"3000.00\",\"gratuity\":\"12000.00\",\"notice_pay\":\"0.00\",\"outstanding_loans\":\"0.00\",\"other_deductions\":\"0.00\",\"net_payable\":\"101200.00\",\"settlement_date\":\"2026-04-08\",\"status\":\"draft\",\"approved_by\":null,\"approved_at\":null,\"created_by\":10,\"created_at\":\"2026-04-08 15:36:43\",\"updated_at\":\"2026-04-08 15:36:43\"}', 0, NULL, NULL),
(21, 'interview', 4, '39', 'Exit Interview - Employee 39', 'Archived exit interview record', '{\"id\":4,\"employee_id\":\"39\",\"interviewer_id\":9,\"scheduled_date\":\"2026-07-10\",\"scheduled_time\":\"01:00:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-07-28 12:40:20\",\"updated_at\":\"2026-08-02 15:13:04\",\"exit_case_type\":\"resignation\",\"exit_case_id\":0,\"completed_at\":\"2026-08-02 15:13:04\"}', 'completed', NULL, 10, '2026-08-07 09:12:31', 'Auto-archived after 3 days of completion', '{\"id\":4,\"employee_id\":\"39\",\"interviewer_id\":9,\"scheduled_date\":\"2026-07-10\",\"scheduled_time\":\"01:00:00\",\"location\":\"Virtual\",\"notes\":\"\",\"status\":\"completed\",\"feedback\":null,\"created_at\":\"2026-07-28 12:40:20\",\"updated_at\":\"2026-08-02 15:13:04\",\"exit_case_type\":\"resignation\",\"exit_case_id\":0,\"completed_at\":\"2026-08-02 15:13:04\"}', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exit_documents`
--

CREATE TABLE `exit_documents` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `document_type` enum('resignation_letter','clearance_form','handover_document','certificate','other') NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `status` enum('active','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exit_documents`
--

INSERT INTO `exit_documents` (`id`, `employee_id`, `document_type`, `title`, `file_path`, `uploaded_by`, `status`, `created_at`) VALUES
(0, '40', 'clearance_form', '54945', 'uploads/documents/1775401638_Calibration_Compliance_Report_2026-03-27 (1).pdf', 1, 'active', '2026-04-05 07:07:18'),
(1, 'EMP002', '', 'sdadasd', 'uploads/documents/1773752497_sir mark docu.docx', 10, 'active', '2026-03-17 05:01:37'),
(2, '1', 'clearance_form', 'dasdasd', 'uploads/documents/1773755616_sir mark docu.docx', 10, 'active', '2026-03-17 05:53:36');

-- --------------------------------------------------------

--
-- Table structure for table `exit_employee_settlements`
--

CREATE TABLE `exit_employee_settlements` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `resignation_id` int(11) DEFAULT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `hra` decimal(10,2) DEFAULT 0.00,
  `conveyance` decimal(10,2) DEFAULT 0.00,
  `lta` decimal(10,2) DEFAULT 0.00,
  `medical_allowance` decimal(10,2) DEFAULT 0.00,
  `other_allowances` decimal(10,2) DEFAULT 0.00,
  `provident_fund` decimal(10,2) DEFAULT 0.00,
  `gratuity` decimal(10,2) DEFAULT 0.00,
  `notice_pay` decimal(10,2) DEFAULT 0.00,
  `outstanding_loans` decimal(10,2) DEFAULT 0.00,
  `other_deductions` decimal(10,2) DEFAULT 0.00,
  `net_payable` decimal(10,2) NOT NULL,
  `settlement_date` date NOT NULL,
  `status` enum('draft','approved','paid') DEFAULT 'draft',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exit_employee_settlements`
--

INSERT INTO `exit_employee_settlements` (`id`, `employee_id`, `resignation_id`, `basic_salary`, `hra`, `conveyance`, `lta`, `medical_allowance`, `other_allowances`, `provident_fund`, `gratuity`, `notice_pay`, `outstanding_loans`, `other_deductions`, `net_payable`, `settlement_date`, `status`, `approved_by`, `approved_at`, `created_by`, `created_at`, `updated_at`) VALUES
(33, '1', 57, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2026-08-10', '', NULL, NULL, 10, '2026-08-07 09:11:10', '2026-08-07 09:11:10');

-- --------------------------------------------------------

--
-- Table structure for table `exit_interviews`
--

CREATE TABLE `exit_interviews` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `interviewer_id` int(11) DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_time` time NOT NULL,
  `location` varchar(255) DEFAULT 'Virtual',
  `notes` text DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `exit_case_type` enum('resignation','termination') DEFAULT NULL,
  `exit_case_id` int(11) DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exit_interviews`
--

INSERT INTO `exit_interviews` (`id`, `employee_id`, `interviewer_id`, `scheduled_date`, `scheduled_time`, `location`, `notes`, `status`, `feedback`, `created_at`, `updated_at`, `exit_case_type`, `exit_case_id`, `completed_at`) VALUES
(1, '2002', NULL, '2025-01-22', '10:00:00', 'Virtual', 'Contract expiration exit interview', 'scheduled', NULL, '2026-04-06 14:30:13', '2026-04-06 14:30:13', NULL, NULL, NULL),
(2, '2001', NULL, '2025-01-07', '10:00:00', 'Virtual', 'Contract expiration exit interview', 'scheduled', NULL, '2026-04-06 14:30:13', '2026-04-06 14:30:13', NULL, NULL, NULL),
(8, '47', 10, '2026-08-07', '17:20:00', 'Virtual', '', 'completed', NULL, '2026-08-07 09:14:18', '2026-08-07 09:15:36', 'termination', 7, '2026-08-07 09:15:36'),
(9, '51', 10, '2026-08-07', '17:30:00', 'Virtual', '', 'completed', NULL, '2026-08-07 09:25:52', '2026-08-07 09:26:52', 'termination', 8, '2026-08-07 09:26:52');

-- --------------------------------------------------------

--
-- Table structure for table `exit_interview_hr_assessments`
--

CREATE TABLE `exit_interview_hr_assessments` (
  `id` int(11) NOT NULL,
  `interview_id` int(11) NOT NULL,
  `summary` text DEFAULT NULL,
  `key_findings` text DEFAULT NULL,
  `hr_recommendations` text DEFAULT NULL,
  `follow_up_actions` text DEFAULT NULL,
  `rehire_eligibility` enum('yes','no','conditional') DEFAULT NULL,
  `knowledge_transfer_required` tinyint(1) DEFAULT 0,
  `clearance_recommendation` enum('clear','not_clear','pending') DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exit_interview_hr_assessments`
--

INSERT INTO `exit_interview_hr_assessments` (`id`, `interview_id`, `summary`, `key_findings`, `hr_recommendations`, `follow_up_actions`, `rehire_eligibility`, `knowledge_transfer_required`, `clearance_recommendation`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 0, 'Interview scheduled for dummy test data.', 'Employee indicates work-life balance concerns.', 'Recommend follow-up with manager and review workload.', 'Schedule knowledge transfer session for project handover.', 'conditional', 1, 'pending', 0, '2026-08-02 05:38:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exit_knowledge_transfer_items`
--

CREATE TABLE `exit_knowledge_transfer_items` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `item_type` enum('document','process','contact','system','other') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exit_knowledge_transfer_items`
--

INSERT INTO `exit_knowledge_transfer_items` (`id`, `plan_id`, `item_type`, `title`, `description`, `priority`, `status`, `completed_at`, `created_at`, `notes`) VALUES
(1, 1, 'process', 'asd', 'das', 'medium', 'pending', NULL, '2026-03-16 22:07:27', NULL),
(2, 2, 'process', 'ghjhgj', 'jhjjhj', 'medium', 'pending', NULL, '2026-03-17 00:48:34', NULL),
(3, 3, 'process', 'asd', 'iyggyi', 'medium', 'pending', NULL, '2026-03-17 00:55:48', NULL),
(4, 4, 'contact', 'ghjhgj', 'f j;dhk;jhgrl;hjfgb;jgvl;bj', 'low', 'pending', NULL, '2026-03-17 00:59:31', NULL),
(5, 5, 'system', 'sadda', 'das', 'medium', 'pending', NULL, '2026-03-17 01:09:40', NULL),
(6, 6, 'system', 'sadda', 'das', 'medium', 'pending', NULL, '2026-03-17 01:09:51', NULL),
(10, 10, 'system', 'ads', 'asdasdas', 'medium', 'pending', NULL, '2026-03-17 04:41:47', NULL),
(11, 0, 'process', 'k', '', 'medium', 'pending', NULL, '2026-04-08 07:42:24', NULL),
(12, 13, 'document', 'basta document', 'hakdog', 'high', 'pending', NULL, '2026-08-02 14:21:04', 'mwamwa');

-- --------------------------------------------------------

--
-- Table structure for table `exit_knowledge_transfer_plans`
--

CREATE TABLE `exit_knowledge_transfer_plans` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `successor_id` varchar(50) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exit_knowledge_transfer_plans`
--

INSERT INTO `exit_knowledge_transfer_plans` (`id`, `employee_id`, `successor_id`, `start_date`, `end_date`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'EMP002', 'EMP001', '2026-03-16', '2026-03-17', 'active', 10, '2026-03-16 22:07:26', '2026-03-16 22:07:26'),
(2, 'EMP002', 'EMP001', '2026-03-12', '2026-03-18', 'active', NULL, '2026-03-17 00:48:34', '2026-03-17 00:48:34'),
(3, 'EMP003', 'EMP001', '2026-03-19', '2026-03-19', 'active', NULL, '2026-03-17 00:55:48', '2026-03-17 00:55:48'),
(4, 'EMP002', 'EMP001', '2026-03-19', '2026-03-11', 'active', NULL, '2026-03-17 00:59:31', '2026-03-17 00:59:31'),
(5, 'EMP002', 'EMP003', '2026-03-18', '2026-03-26', 'active', NULL, '2026-03-17 01:09:39', '2026-03-17 01:09:39'),
(6, 'EMP002', 'EMP003', '2026-03-18', '2026-03-26', 'active', NULL, '2026-03-17 01:09:51', '2026-03-17 01:09:51'),
(10, 'EMP002', 'EMP003', '2026-03-10', '2026-03-18', 'active', 10, '2026-03-17 04:41:47', '2026-03-17 04:41:47'),
(11, '37', '21', '2026-04-13', '2026-04-28', 'active', 10, '2026-04-01 11:50:49', '2026-08-02 14:05:51'),
(12, '40', '37', '2026-04-09', '2026-04-23', 'active', 10, '2026-04-08 07:42:24', '2026-08-02 14:05:51'),
(13, '4', '46', '2026-08-03', '2027-05-15', 'active', 3, '2026-08-02 14:21:04', '2026-08-02 14:21:04');

-- --------------------------------------------------------

--
-- Table structure for table `exit_resignations`
--

CREATE TABLE `exit_resignations` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `resignation_type` enum('Voluntary(Resignation)','Involuntary(Terminated)') NOT NULL,
  `reason` text NOT NULL,
  `notice_date` date NOT NULL,
  `last_working_date` date NOT NULL,
  `comments` text DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `preclearance_desk_person` int(11) DEFAULT NULL,
  `status` enum('pending_review','pending_legal_review','approved','rejected','rejected_by_legal','withdrawn') NOT NULL DEFAULT 'pending_review',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived_from_status` enum('pending','approved','rejected','withdrawn') DEFAULT NULL,
  `hr_approved_by` int(11) DEFAULT NULL,
  `hr_approved_at` datetime DEFAULT NULL,
  `hr_approval_comments` text DEFAULT NULL,
  `legal_approved_by` int(11) DEFAULT NULL,
  `legal_approved_at` datetime DEFAULT NULL,
  `legal_approval_comments` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exit_resignations`
--

INSERT INTO `exit_resignations` (`id`, `employee_id`, `resignation_type`, `reason`, `notice_date`, `last_working_date`, `comments`, `submitted_by`, `preclearance_desk_person`, `status`, `approved_by`, `approved_at`, `created_at`, `updated_at`, `archived_from_status`, `hr_approved_by`, `hr_approved_at`, `hr_approval_comments`, `legal_approved_by`, `legal_approved_at`, `legal_approval_comments`, `reviewed_by`, `reviewed_at`, `review_remarks`) VALUES
(57, '1', 'Voluntary(Resignation)', 'Career advancement opportunity', '2026-08-01', '2026-08-15', 'Accepted an offer at another company.', 1, NULL, 'approved', 10, '2026-08-02 05:18:40', '2026-08-01 04:30:18', '2026-08-03 01:35:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, '2', 'Voluntary(Resignation)', 'Family relocation', '2026-08-03', '2026-08-20', 'Moving out of town for family reasons.', 1, NULL, '', NULL, NULL, '2026-08-01 04:30:18', '2026-08-01 11:40:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, '3', 'Voluntary(Resignation)', 'Company restructuring', '2026-07-15', '2026-07-31', 'Position eliminated due to organizational changes.', 1, NULL, 'approved', NULL, NULL, '2026-08-01 04:30:18', '2026-08-01 11:40:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, '4', 'Voluntary(Resignation)', 'Health reasons', '2026-07-10', '2026-07-25', 'Resigning to focus on recovery.', 1, NULL, 'approved', 10, '2026-08-02 05:40:51', '2026-08-01 04:30:18', '2026-08-02 05:40:51', NULL, NULL, NULL, NULL, 10, '2026-08-02 13:40:51', '', NULL, NULL, NULL),
(61, '5', 'Voluntary(Resignation)', 'Education pursuit', '2026-08-05', '2026-08-30', 'Returning to school for higher studies.', 1, NULL, '', NULL, NULL, '2026-08-01 04:30:18', '2026-08-01 11:40:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, '6', 'Voluntary(Resignation)', 'Remote work transition', '2026-07-20', '2026-08-05', 'Company policy changed to office-only.', 1, NULL, '', NULL, NULL, '2026-08-01 04:30:18', '2026-08-01 11:40:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, '7', 'Voluntary(Resignation)', 'Better salary offer', '2026-07-22', '2026-08-08', 'Accepted a new role with higher compensation.', 1, NULL, 'approved', NULL, NULL, '2026-08-01 04:30:18', '2026-08-01 11:40:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, '8', 'Voluntary(Resignation)', 'Work-life balance', '2026-08-07', '2026-08-22', 'Leaving to spend more time with family.', 1, NULL, '', NULL, NULL, '2026-08-01 04:30:18', '2026-08-01 11:40:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, '9', 'Voluntary(Resignation)', 'Career change', '2026-08-09', '2026-08-28', 'Pursuing a different career path.', 1, NULL, 'pending_legal_review', NULL, NULL, '2026-08-01 04:30:18', '2026-08-01 11:40:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, '10', 'Voluntary(Resignation)', 'Entrepreneurship', '2026-08-12', '2026-08-27', 'Starting a personal business venture.', 1, NULL, '', NULL, NULL, '2026-08-01 04:30:18', '2026-08-01 11:40:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exit_surveys`
--

CREATE TABLE `exit_surveys` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `target_audience` enum('all','voluntary','involuntary') DEFAULT 'all',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exit_surveys`
--

INSERT INTO `exit_surveys` (`id`, `title`, `description`, `target_audience`, `start_date`, `end_date`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'casedca', 'dcasdcas', 'voluntary', '2026-03-01', '2026-12-31', 'active', NULL, '2026-03-17 01:05:00', '2026-04-01 12:24:32'),
(2, 'casedca', 'dcasdcas', 'voluntary', '2026-03-01', '2026-12-31', 'active', NULL, '2026-03-17 01:05:23', '2026-04-01 12:24:32'),
(3, 'dsadsaadsda', 'adsdasadsadsasd', 'all', '2026-03-18', '2026-12-31', 'active', 10, '2026-03-17 06:01:22', '2026-04-01 12:24:32'),
(4, 'dasdasd', 'sadasdasd', 'all', '2026-03-25', '2026-12-31', 'active', 10, '2026-03-17 07:34:26', '2026-04-01 12:24:32');

-- --------------------------------------------------------

--
-- Table structure for table `exit_survey_answers`
--

CREATE TABLE `exit_survey_answers` (
  `id` int(11) NOT NULL,
  `response_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text DEFAULT NULL,
  `answer_value` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exit_survey_answers`
--

INSERT INTO `exit_survey_answers` (`id`, `response_id`, `question_id`, `answer_text`, `answer_value`, `created_at`) VALUES
(1, 5, 5, '[\"hi\",\"hello\"]', 'hi, hello', '2026-04-01 12:27:20'),
(2, 5, 6, '4', '4', '2026-04-01 12:27:20'),
(3, 5, 7, 'das', 'das', '2026-04-01 12:27:20'),
(4, 5, 8, 'test answer', 'test answer', '2026-04-01 12:27:20'),
(5, 6, 5, 'hello', 'hello', '2026-04-05 05:25:01'),
(6, 6, 6, '4', '4', '2026-04-05 05:25:02'),
(7, 6, 7, 'dasd', 'dasd', '2026-04-05 05:25:02'),
(8, 6, 8, 'dadadewqeqewqeqwdsadasdsa', 'dadadewqeqewqeqwdsadasdsa', '2026-04-05 05:25:02'),
(0, 0, 1, 'I appreciated the team, but I need a softer daily load.', 'I appreciated the team, but I need a softer daily load.', '2026-08-02 05:38:45');

-- --------------------------------------------------------

--
-- Table structure for table `exit_survey_questions`
--

CREATE TABLE `exit_survey_questions` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('text','textarea','radio','checkbox','select','rating') NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `required` tinyint(1) DEFAULT 0,
  `order_num` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exit_survey_questions`
--

INSERT INTO `exit_survey_questions` (`id`, `survey_id`, `question_text`, `question_type`, `options`, `required`, `order_num`, `created_at`) VALUES
(1, 1, 'casdcacd', 'textarea', NULL, 0, 1, '2026-03-17 01:05:01'),
(2, 2, 'casdcacd', 'textarea', NULL, 0, 1, '2026-03-17 01:05:23'),
(3, 2, '2323232', 'textarea', NULL, 0, 2, '2026-03-17 01:05:23'),
(4, 3, 'dsadasds', 'textarea', NULL, 0, 1, '2026-03-17 06:01:23'),
(5, 4, 'hello', 'checkbox', '\"hi\\r\\nhello\\r\\nwao\"', 0, 1, '2026-03-17 07:34:26'),
(6, 4, 'mwke', 'rating', NULL, 0, 2, '2026-03-17 07:34:27'),
(7, 4, 'asdw', 'radio', '\"das\\r\\ndasd\\r\\nweq\"', 0, 3, '2026-03-17 07:34:27'),
(8, 4, 'sad', 'textarea', NULL, 0, 4, '2026-03-17 07:34:27');

-- --------------------------------------------------------

--
-- Table structure for table `exit_survey_responses`
--

CREATE TABLE `exit_survey_responses` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `employee_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `responses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`responses`)),
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exit_survey_responses`
--

INSERT INTO `exit_survey_responses` (`id`, `survey_id`, `employee_id`, `responses`, `submitted_at`) VALUES
(2, 4, '1', NULL, '2026-04-01 12:26:24'),
(5, 4, '1', NULL, '2026-04-01 12:27:20'),
(6, 4, '10', NULL, '2026-04-05 05:25:01'),
(0, 1, '1', '{\"1\":\"I wanted better work-life balance.\",\"2\":\"4\",\"3\":\"Flexible Schedule\"}', '2026-08-02 05:38:45');

-- --------------------------------------------------------

--
-- Table structure for table `exit_terminations`
--

CREATE TABLE `exit_terminations` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `termination_reason` text NOT NULL,
  `effective_date` date NOT NULL,
  `comments` text DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `status` enum('pending_review','pending_legal_review','approved','rejected','rejected_by_legal','withdrawn') NOT NULL DEFAULT 'pending_review',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_remarks` text DEFAULT NULL,
  `legal_approved_by` int(11) DEFAULT NULL,
  `legal_approved_at` datetime DEFAULT NULL,
  `legal_approval_comments` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exit_terminations`
--

INSERT INTO `exit_terminations` (`id`, `employee_id`, `termination_reason`, `effective_date`, `comments`, `submitted_by`, `status`, `reviewed_by`, `reviewed_at`, `review_remarks`, `legal_approved_by`, `legal_approved_at`, `legal_approval_comments`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, '1', 'Violation of company policy', '2026-08-01', 'Repeat attendance infractions and failure to comply with workplace rules.', 1, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 10, '2026-08-02 13:41:30', '2026-08-01 07:56:47', '2026-08-03 02:10:18'),
(2, '2', 'Gross misconduct', '2026-08-05', 'Involved in inappropriate conduct during work hours.', 1, 'pending_legal_review', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-01 07:56:47', '2026-08-01 07:56:47'),
(3, '3', 'Fraudulent behavior', '2026-08-07', 'Unauthorized expense claims discovered during audit.', 1, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-01 07:56:47', '2026-08-01 07:56:47'),
(4, '4', 'Job abandonment', '2026-08-10', 'Employee failed to report to work for multiple consecutive days.', 1, 'rejected', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-01 07:56:47', '2026-08-01 07:56:47'),
(5, '5', 'Serious safety violation', '2026-08-12', 'Deliberately ignored mandatory safety procedures on site.', 1, 'pending_review', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-01 07:56:47', '2026-08-01 07:56:47'),
(6, '49', 'test', '2026-08-03', '', 3, 'pending_review', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-02 17:17:28', '2026-08-03 02:13:51'),
(7, '47', 'test', '2026-08-07', '', 10, 'approved', NULL, NULL, NULL, 10, '2026-08-07 17:13:44', '', 10, '2026-08-07 17:13:44', '2026-08-07 09:13:17', '2026-08-07 09:13:44'),
(8, '51', 'test', '2026-08-07', '', 10, 'approved', NULL, NULL, NULL, 10, '2026-08-07 17:25:19', '', 10, '2026-08-07 17:25:19', '2026-08-07 09:24:58', '2026-08-07 09:25:19');

-- --------------------------------------------------------

--
-- Table structure for table `lc_activity_alerts`
--

CREATE TABLE `lc_activity_alerts` (
  `id` int(11) NOT NULL,
  `alert_type` enum('incident','risk','compliance','general') NOT NULL DEFAULT 'general',
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `severity_level` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `created_by` int(11) DEFAULT NULL COMMENT 'Foreign key to employees table',
  `assigned_to` int(11) DEFAULT NULL COMMENT 'Foreign key to employees table',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  `related_entity_type` varchar(50) DEFAULT NULL COMMENT 'Polymorphic: incidents, risks, compliance_items',
  `related_entity_id` int(11) DEFAULT NULL COMMENT 'ID of related entity',
  `is_read` tinyint(1) DEFAULT 0 COMMENT 'Whether alert has been read',
  `is_notified` tinyint(1) DEFAULT 0 COMMENT 'Whether notification sent',
  `notes` text DEFAULT NULL COMMENT 'Additional notes or comments'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_activity_alerts`
--

INSERT INTO `lc_activity_alerts` (`id`, `alert_type`, `title`, `description`, `severity_level`, `status`, `created_by`, `assigned_to`, `created_at`, `updated_at`, `resolved_at`, `related_entity_type`, `related_entity_id`, `is_read`, `is_notified`, `notes`) VALUES
(1, 'incident', 'Workplace Safety Incident Reported', 'A workplace safety incident has been reported in the Operations department requiring immediate attention', 'high', 'open', 1, 2, '2026-03-24 14:23:36', '2026-03-24 14:23:36', NULL, 'incidents', 1, 0, 0, NULL),
(2, 'risk', 'Compliance Deadline Approaching', 'Maternity Leave Compliance deadline is approaching within 7 days', 'medium', 'in_progress', 1, 1, '2026-03-24 14:23:36', '2026-03-24 14:23:36', NULL, 'compliance_items', 1, 0, 0, NULL),
(3, 'compliance', 'Policy Update Required', 'Employee Manual requires annual review and update', 'low', 'open', 1, 1, '2026-03-24 14:23:36', '2026-03-24 14:23:36', NULL, 'compliance_items', 12, 0, 0, NULL),
(4, 'general', 'System Maintenance', 'Scheduled system maintenance for compliance tracking module', 'low', 'resolved', 1, NULL, '2026-03-24 14:23:36', '2026-03-24 14:23:36', NULL, NULL, NULL, 0, 0, NULL),
(5, 'incident', 'Harassment Investigation', 'Harassment complaint investigation in progress', 'critical', 'in_progress', 1, 2, '2026-03-24 14:23:36', '2026-03-24 14:23:36', NULL, 'incidents', 2, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lc_activity_log`
--

CREATE TABLE `lc_activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Foreign key to employees - who performed the action',
  `action` varchar(100) NOT NULL COMMENT 'Action type: created, updated, deleted, status_changed',
  `entity_type` varchar(50) NOT NULL COMMENT 'Table name: incidents, risks, compliance_items, etc.',
  `entity_id` int(11) NOT NULL COMMENT 'ID of affected record',
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON of previous values' CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON of new values' CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_activity_log`
--

INSERT INTO `lc_activity_log` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'created', 'incidents', 1, NULL, '{\"title\":\"Workplace Safety Incident Reported\",\"severity\":\"high\",\"status\":\"open\"}', NULL, NULL, '2026-03-24 14:23:36'),
(2, 1, 'status_changed', 'incidents', 1, '{\"status\":\"open\"}', '{\"status\":\"in_progress\"}', NULL, NULL, '2026-03-24 14:23:36'),
(3, 2, 'created', 'compliance_items', 1, NULL, '{\"name\":\"Maternity Leave Compliance\",\"status\":\"Compliant\"}', NULL, NULL, '2026-03-24 14:23:36'),
(4, 1, 'created', 'risks', 1, NULL, '{\"title\":\"Data Privacy Risk\",\"severity\":\"medium\",\"status\":\"identified\"}', NULL, NULL, '2026-03-24 14:23:36'),
(5, 1, 'created', 'employee_compliance_records', 1, NULL, '{\"employee_id\":1,\"compliance_item_id\":1,\"status\":\"compliant\"}', NULL, NULL, '2026-03-24 14:28:21'),
(6, 1, 'created', 'employee_risk_assessments', 1, NULL, '{\"employee_id\":1,\"risk_type\":\"Overtime Risk\",\"risk_level\":\"low\"}', NULL, NULL, '2026-03-24 14:28:21'),
(7, 2, 'created', 'employee_risk_assessments', 4, NULL, '{\"employee_id\":4,\"risk_type\":\"Training Compliance\",\"risk_level\":\"medium\"}', NULL, NULL, '2026-03-24 14:28:21'),
(8, 1, 'created', 'employee_risk_assessments', 5, NULL, '{\"employee_id\":5,\"risk_type\":\"Performance Risk\",\"risk_level\":\"high\"}', NULL, NULL, '2026-03-24 14:28:21'),
(9, 1, 'created', 'employee_risk_assessments', 6, NULL, '{\"employee_id\":6,\"risk_type\":\"Contract Expiry Risk\",\"risk_level\":\"high\"}', NULL, NULL, '2026-03-24 14:28:21'),
(10, 1, 'created', 'employee_compliance_records', 1, NULL, '{\"employee_id\":1,\"compliance_item_id\":1,\"status\":\"compliant\"}', NULL, NULL, '2026-03-25 16:24:48'),
(11, 1, 'created', 'employee_risk_assessments', 1, NULL, '{\"employee_id\":1,\"risk_type\":\"Overtime Risk\",\"risk_level\":\"low\"}', NULL, NULL, '2026-03-25 16:24:48'),
(12, 2, 'created', 'employee_risk_assessments', 4, NULL, '{\"employee_id\":4,\"risk_type\":\"Training Compliance\",\"risk_level\":\"medium\"}', NULL, NULL, '2026-03-25 16:24:48'),
(13, 1, 'created', 'employee_risk_assessments', 5, NULL, '{\"employee_id\":5,\"risk_type\":\"Performance Risk\",\"risk_level\":\"high\"}', NULL, NULL, '2026-03-25 16:24:48'),
(14, 1, 'created', 'employee_risk_assessments', 6, NULL, '{\"employee_id\":6,\"risk_type\":\"Contract Expiry Risk\",\"risk_level\":\"high\"}', NULL, NULL, '2026-03-25 16:24:48');

-- --------------------------------------------------------

--
-- Table structure for table `lc_alerts`
--

CREATE TABLE `lc_alerts` (
  `id` int(11) NOT NULL,
  `compliance_item_id` int(11) DEFAULT NULL,
  `alert_type` enum('upcoming_deadline','overdue','critical_non_compliance','status_change','risk_elevated','system') NOT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `message` text NOT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_resolved` tinyint(1) DEFAULT 0,
  `resolved_at` datetime DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_alerts`
--

INSERT INTO `lc_alerts` (`id`, `compliance_item_id`, `alert_type`, `priority`, `message`, `recipient_id`, `is_read`, `is_resolved`, `resolved_at`, `resolved_by`, `created_at`) VALUES
(1, 4, 'upcoming_deadline', 'low', 'Q1 Safety Training deadline approaching on March 31, 2026', 1, 0, 0, NULL, NULL, '2026-03-24 14:27:08'),
(2, 8, 'upcoming_deadline', 'medium', 'Payroll tax compliance deadline on March 25, 2026', 1, 0, 0, NULL, NULL, '2026-03-24 14:27:08'),
(3, 9, 'upcoming_deadline', 'medium', 'SSS contribution deadline approaching on March 31, 2026', 1, 0, 0, NULL, NULL, '2026-03-24 14:27:08'),
(4, 14, 'critical_non_compliance', 'critical', 'First Aid Kit Maintenance is marked as Non-Compliant - immediate action required', 1, 0, 0, NULL, NULL, '2026-03-24 14:27:08'),
(5, 4, 'upcoming_deadline', 'low', 'Q1 Safety Training deadline approaching on March 31, 2026', 1, 0, 0, NULL, NULL, '2026-03-24 14:29:27'),
(6, 8, 'upcoming_deadline', 'medium', 'Payroll tax compliance deadline on March 25, 2026', 1, 0, 0, NULL, NULL, '2026-03-24 14:29:27'),
(7, 9, 'upcoming_deadline', 'medium', 'SSS contribution deadline approaching on March 31, 2026', 1, 0, 0, NULL, NULL, '2026-03-24 14:29:27'),
(8, 14, 'critical_non_compliance', 'critical', 'First Aid Kit Maintenance is marked as Non-Compliant - immediate action required', 1, 0, 0, NULL, NULL, '2026-03-24 14:29:27');

-- --------------------------------------------------------

--
-- Table structure for table `lc_allowances`
--

CREATE TABLE `lc_allowances` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` enum('fixed','percentage') DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_allowances`
--

INSERT INTO `lc_allowances` (`id`, `name`, `type`, `amount`) VALUES
(1, 'Rice Allowance', 'fixed', 1500.00),
(2, 'Teaching Load Allowance', 'fixed', 4000.00),
(3, 'Transportation Allowance', 'fixed', 1200.00);

-- --------------------------------------------------------

--
-- Table structure for table `lc_attendance`
--

CREATE TABLE `lc_attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('present','absent','late','on_leave') DEFAULT 'present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_attendance`
--

INSERT INTO `lc_attendance` (`id`, `employee_id`, `date`, `time_in`, `time_out`, `status`) VALUES
(1, 1, '2026-02-01', '08:00:00', '17:00:00', 'present'),
(2, 1, '2026-02-02', '08:05:00', '17:00:00', 'late'),
(3, 1, '2026-02-03', NULL, NULL, 'absent'),
(4, 2, '2026-02-01', '08:00:00', '17:00:00', 'present'),
(5, 2, '2026-02-02', '08:00:00', '17:00:00', 'present'),
(6, 3, '2026-02-01', NULL, NULL, 'absent'),
(7, 3, '2026-02-02', '08:15:00', '17:00:00', 'late'),
(8, 4, '2026-02-01', '08:00:00', '17:00:00', 'present'),
(9, 5, '2026-02-01', '08:00:00', '17:00:00', 'present'),
(10, 5, '2026-02-02', NULL, NULL, 'absent'),
(11, 6, '2026-02-01', '08:00:00', '17:00:00', 'present'),
(12, 6, '2026-02-02', '08:10:00', '17:00:00', 'late');

-- --------------------------------------------------------

--
-- Table structure for table `lc_compliance_items`
--

CREATE TABLE `lc_compliance_items` (
  `id` int(11) NOT NULL,
  `compliance_id` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `category` enum('Labor Law','Company Policy','Health & Safety','Data Privacy','Payroll','Other') NOT NULL,
  `subcategory` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `responsible_person_id` int(11) DEFAULT NULL,
  `frequency` enum('Daily','Weekly','Monthly','Quarterly','Yearly','One-time') DEFAULT 'Monthly',
  `due_date` date DEFAULT NULL,
  `status` enum('Compliant','Pending','Non-Compliant','Overdue') DEFAULT 'Pending',
  `risk_level` enum('Low','Medium','High','Critical') DEFAULT 'Low',
  `last_checked` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_compliance_items`
--

INSERT INTO `lc_compliance_items` (`id`, `compliance_id`, `name`, `category`, `subcategory`, `description`, `department`, `responsible_person_id`, `frequency`, `due_date`, `status`, `risk_level`, `last_checked`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 'COMP-001', 'Maternity Leave Compliance', 'Labor Law', 'Leave Benefits', 'Ensure all pregnant employees receive 105 days paid maternity leave as per RA 11210', 'Human Resources', 1, 'Yearly', '2026-12-31', 'Compliant', 'High', '2026-03-24 22:27:08', 'All maternity leaves are properly documented', '2026-03-24 14:27:08', '2026-03-24 14:27:08'),
(2, 'COMP-002', 'Paternity Leave Processing', 'Labor Law', 'Leave Benefits', 'Process 7 days paternity leave for qualifying employees as per RA 8187', 'Human Resources', 1, 'Yearly', '2026-12-31', 'Compliant', 'Medium', '2026-03-24 22:27:08', 'Paternity leaves are being processed', '2026-03-24 14:27:08', '2026-03-24 14:27:08'),
(3, 'COMP-003', 'Solo Parent Leave Implementation', 'Labor Law', 'Leave Benefits', 'Implement 7 days solo parent leave per RA 8972 for qualifying employees', 'Human Resources', 1, 'Yearly', '2026-12-31', 'Pending', 'Medium', '2026-03-24 22:27:08', 'Awaiting policy update', '2026-03-24 14:27:08', '2026-03-24 14:27:08'),
(4, 'COMP-004', 'OSHA Safety Training', 'Health & Safety', 'Training', 'Conduct quarterly safety training for all employees per RA 11058', 'Operations', 1, 'Quarterly', '2026-03-31', 'Compliant', 'High', '2026-03-24 22:27:08', 'Q1 training completed', '2026-03-24 14:27:08', '2026-03-24 14:27:08'),
(5, 'COMP-005', 'Data Privacy Compliance', 'Data Privacy', 'Data Protection', 'Ensure compliance with Data Privacy Act RA 10173 - data mapping and privacy impact assessments', 'IT', 1, 'Yearly', '2026-06-30', 'Pending', 'High', '2026-03-24 22:27:08', 'Privacy impact assessment in progress', '2026-03-24 14:27:08', '2026-03-24 14:27:08'),
(6, 'COMP-006', 'Anti-Sexual Harassment Policy', 'Company Policy', 'Policy Compliance', 'Maintain zero-tolerance policy for sexual harassment per RA 7877', 'Human Resources', 2, 'Monthly', '2026-03-31', 'Compliant', 'Critical', '2026-03-24 22:27:08', 'Policy posted and acknowledged', '2026-03-24 14:27:08', '2026-03-24 14:28:21'),
(7, 'COMP-007', 'Safe Spaces Act Compliance', 'Labor Law', 'Workplace Safety', 'Ensure workplace safety from gender-based harassment per RA 11313', 'Human Resources', 2, 'Monthly', '2026-03-31', 'Compliant', 'High', '2026-03-24 22:27:08', 'Complaint mechanism in place', '2026-03-24 14:27:08', '2026-03-24 14:28:21'),
(8, 'COMP-008', 'Payroll Tax Compliance', 'Payroll', 'Tax', 'Ensure accurate withholding and remittance of taxes per BIR regulations', 'Finance', 2, 'Monthly', '2026-03-25', 'Compliant', 'High', '2026-03-24 22:27:08', 'Tax filings up to date', '2026-03-24 14:27:08', '2026-03-24 14:28:21'),
(9, 'COMP-009', 'SSS Contribution Remittance', 'Payroll', 'Benefits', 'Monthly SSS contribution remittance for all employees', 'Finance', 2, 'Monthly', '2026-03-31', 'Pending', 'Medium', '2026-03-24 22:27:08', 'Awaiting HR verification', '2026-03-24 14:27:08', '2026-03-24 14:28:21'),
(10, 'COMP-010', 'PhilHealth Contribution', 'Payroll', 'Benefits', 'Monthly PhilHealth contribution remittance', 'Finance', 2, 'Monthly', '2026-03-31', 'Pending', 'Medium', '2026-03-24 22:27:08', 'Awaiting HR verification', '2026-03-24 14:27:08', '2026-03-24 14:28:21'),
(11, 'COMP-011', 'Pag-IBIG Contribution', 'Payroll', 'Benefits', 'Monthly Pag-IBIG contribution remittance', 'Finance', 3, 'Monthly', '2026-03-31', 'Compliant', 'Low', '2026-03-24 22:27:08', 'Contributions remitted', '2026-03-24 14:27:08', '2026-03-24 14:28:21'),
(12, 'COMP-012', 'Employee Manual Update', 'Company Policy', 'Documentation', 'Review and update employee handbook annually', 'Human Resources', 3, 'Yearly', '2026-06-30', 'Pending', 'Low', '2026-03-24 22:27:08', 'Review scheduled for Q2', '2026-03-24 14:27:08', '2026-03-24 14:28:21'),
(13, 'COMP-013', 'Workplace Fire Safety Inspection', 'Health & Safety', 'Safety', 'Annual fire safety inspection and certification', 'Operations', 3, 'Yearly', '2026-09-30', 'Compliant', 'Critical', '2026-03-24 22:27:08', 'Inspection passed', '2026-03-24 14:27:08', '2026-03-24 14:28:21'),
(14, 'COMP-014', 'First Aid Kit Maintenance', 'Health & Safety', 'Safety', 'Monthly inspection and restocking of first aid kits', 'Operations', 3, 'Monthly', '2026-03-31', 'Non-Compliant', 'Medium', '2026-03-24 22:27:08', 'Several kits need restocking', '2026-03-24 14:27:08', '2026-03-24 14:28:21'),
(15, 'COMP-015', 'Employee Data Encryption', 'Data Privacy', 'Data Protection', 'Ensure all employee personal data is encrypted at rest', 'IT', 3, 'Quarterly', '2026-06-30', 'Pending', 'High', '2026-03-24 22:27:08', 'Encryption audit in progress', '2026-03-24 14:27:08', '2026-03-24 14:28:21'),
(16, 'COMP-001', 'Maternity Leave Compliance', 'Labor Law', 'Leave Benefits', 'Ensure all pregnant employees receive 105 days paid maternity leave as per RA 11210', 'Human Resources', 3, 'Yearly', '2026-12-31', 'Compliant', 'High', '2026-03-24 22:29:27', 'All maternity leaves are properly documented', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(17, 'COMP-002', 'Paternity Leave Processing', 'Labor Law', 'Leave Benefits', 'Process 7 days paternity leave for qualifying employees as per RA 8187', 'Human Resources', 3, 'Yearly', '2026-12-31', 'Compliant', 'Medium', '2026-03-24 22:29:27', 'Paternity leaves are being processed', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(18, 'COMP-003', 'Solo Parent Leave Implementation', 'Labor Law', 'Leave Benefits', 'Implement 7 days solo parent leave per RA 8972 for qualifying employees', 'Human Resources', 3, 'Yearly', '2026-12-31', 'Pending', 'Medium', '2026-03-24 22:29:27', 'Awaiting policy update', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(19, 'COMP-004', 'OSHA Safety Training', 'Health & Safety', 'Training', 'Conduct quarterly safety training for all employees per RA 11058', 'Operations', 3, 'Quarterly', '2026-03-31', 'Compliant', 'High', '2026-03-24 22:29:27', 'Q1 training completed', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(20, 'COMP-005', 'Data Privacy Compliance', 'Data Privacy', 'Data Protection', 'Ensure compliance with Data Privacy Act RA 10173 - data mapping and privacy impact assessments', 'IT', 3, 'Yearly', '2026-06-30', 'Pending', 'High', '2026-03-24 22:29:27', 'Privacy impact assessment in progress', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(21, 'COMP-006', 'Anti-Sexual Harassment Policy', 'Company Policy', 'Policy Compliance', 'Maintain zero-tolerance policy for sexual harassment per RA 7877', 'Human Resources', 3, 'Monthly', '2026-03-31', 'Compliant', 'Critical', '2026-03-24 22:29:27', 'Policy posted and acknowledged', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(22, 'COMP-007', 'Safe Spaces Act Compliance', 'Labor Law', 'Workplace Safety', 'Ensure workplace safety from gender-based harassment per RA 11313', 'Human Resources', 3, 'Monthly', '2026-03-31', 'Compliant', 'High', '2026-03-24 22:29:27', 'Complaint mechanism in place', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(23, 'COMP-008', 'Payroll Tax Compliance', 'Payroll', 'Tax', 'Ensure accurate withholding and remittance of taxes per BIR regulations', 'Finance', 3, 'Monthly', '2026-03-25', 'Compliant', 'High', '2026-03-24 22:29:27', 'Tax filings up to date', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(24, 'COMP-009', 'SSS Contribution Remittance', 'Payroll', 'Benefits', 'Monthly SSS contribution remittance for all employees', 'Finance', 3, 'Monthly', '2026-03-31', 'Pending', 'Medium', '2026-03-24 22:29:27', 'Awaiting HR verification', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(25, 'COMP-010', 'PhilHealth Contribution', 'Payroll', 'Benefits', 'Monthly PhilHealth contribution remittance', 'Finance', 3, 'Monthly', '2026-03-31', 'Pending', 'Medium', '2026-03-24 22:29:27', 'Awaiting HR verification', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(26, 'COMP-011', 'Pag-IBIG Contribution', 'Payroll', 'Benefits', 'Monthly Pag-IBIG contribution remittance', 'Finance', 3, 'Monthly', '2026-03-31', 'Compliant', 'Low', '2026-03-24 22:29:27', 'Contributions remitted', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(27, 'COMP-012', 'Employee Manual Update', 'Company Policy', 'Documentation', 'Review and update employee handbook annually', 'Human Resources', 3, 'Yearly', '2026-06-30', 'Pending', 'Low', '2026-03-24 22:29:27', 'Review scheduled for Q2', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(28, 'COMP-013', 'Workplace Fire Safety Inspection', 'Health & Safety', 'Safety', 'Annual fire safety inspection and certification', 'Operations', 3, 'Yearly', '2026-09-30', 'Compliant', 'Critical', '2026-03-24 22:29:27', 'Inspection passed', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(29, 'COMP-014', 'First Aid Kit Maintenance', 'Health & Safety', 'Safety', 'Monthly inspection and restocking of first aid kits', 'Operations', 3, 'Monthly', '2026-03-31', 'Non-Compliant', 'Medium', '2026-03-24 22:29:27', 'Several kits need restocking', '2026-03-24 14:29:27', '2026-03-25 16:24:48'),
(30, 'COMP-015', 'Employee Data Encryption', 'Data Privacy', 'Data Protection', 'Ensure all employee personal data is encrypted at rest', 'IT', 3, 'Quarterly', '2026-06-30', 'Pending', 'High', '2026-03-24 22:29:27', 'Encryption audit in progress', '2026-03-24 14:29:27', '2026-03-25 16:24:48');

-- --------------------------------------------------------

--
-- Table structure for table `lc_deductions`
--

CREATE TABLE `lc_deductions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` enum('fixed','percentage') DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `is_statutory` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_deductions`
--

INSERT INTO `lc_deductions` (`id`, `name`, `type`, `amount`, `is_statutory`) VALUES
(1, 'SSS', 'fixed', 1125.00, 1),
(2, 'PhilHealth', 'fixed', 450.00, 1),
(3, 'Pag-IBIG', 'fixed', 200.00, 1),
(4, 'Withholding Tax', 'fixed', 2500.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `lc_disciplinary_actions`
--

CREATE TABLE `lc_disciplinary_actions` (
  `id` int(11) NOT NULL,
  `action_reference` varchar(50) NOT NULL COMMENT 'Auto-generated: DISC-YYYY-NNNN',
  `incident_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL COMMENT 'Employee receiving disciplinary action',
  `action_type` enum('verbal_warning','written_warning','suspension','termination','final_warning','other') NOT NULL,
  `reason` text NOT NULL,
  `violation_description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `duration_days` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `issued_by` int(11) DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','issued','appealed','upheld','dismissed') DEFAULT 'issued',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_disciplinary_actions`
--

INSERT INTO `lc_disciplinary_actions` (`id`, `action_reference`, `incident_id`, `employee_id`, `action_type`, `reason`, `violation_description`, `start_date`, `end_date`, `duration_days`, `is_active`, `issued_by`, `document_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 'DISC-2026-0001', 5, 3, 'verbal_warning', 'Verbal altercation - first offense', NULL, '2026-03-12', NULL, NULL, 1, 2, NULL, 'issued', '2026-03-25 16:33:19', '2026-03-25 16:33:19'),
(2, 'DISC-2026-0002', 2, 5, 'written_warning', 'Repeated absenteeism without proper notification', NULL, '2026-03-20', NULL, NULL, 1, 1, NULL, 'issued', '2026-03-25 16:33:19', '2026-03-25 16:33:19');

-- --------------------------------------------------------

--
-- Table structure for table `lc_employees`
--

CREATE TABLE `lc_employees` (
  `id` int(11) NOT NULL,
  `employee_no` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `employment_type_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `date_hired` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `marital_status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_employees`
--

INSERT INTO `lc_employees` (`id`, `employee_no`, `first_name`, `last_name`, `category_id`, `position_id`, `employment_type_id`, `status`, `date_hired`, `gender`, `marital_status`) VALUES
(1, 'T001', 'Ana', 'Cruz', 1, 1, 1, 'active', '2021-06-01', 'Female', NULL),
(2, 'T002', 'Mark', 'Lee', 1, 2, 3, 'active', '2022-01-15', 'Male', NULL),
(3, 'S001', 'John', 'Rey', 2, 3, 1, 'active', '2020-09-10', 'Male', NULL),
(4, 'S002', 'Liza', 'Torres', 2, 4, 2, 'active', '2022-02-20', NULL, NULL),
(5, 'S003', 'Pedro', 'Santos', 2, 5, 1, 'active', '2019-11-05', 'Male', NULL),
(6, 'T003', 'Karen', 'Villanueva', 1, 1, 3, 'active', '2024-08-01', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lc_employee_adjustments`
--

CREATE TABLE `lc_employee_adjustments` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `payroll_period_id` int(11) NOT NULL,
  `type` enum('allowance','deduction') NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_employee_allowances`
--

CREATE TABLE `lc_employee_allowances` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `allowance_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_employee_allowances`
--

INSERT INTO `lc_employee_allowances` (`id`, `employee_id`, `allowance_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 2),
(4, 3, 3),
(5, 4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `lc_employee_categories`
--

CREATE TABLE `lc_employee_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_employee_categories`
--

INSERT INTO `lc_employee_categories` (`id`, `name`) VALUES
(1, 'Teaching'),
(2, 'Non-Teaching');

-- --------------------------------------------------------

--
-- Table structure for table `lc_employee_compliance_records`
--

CREATE TABLE `lc_employee_compliance_records` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `compliance_item_id` int(11) NOT NULL,
  `status` enum('compliant','non_compliant','pending','not_applicable') DEFAULT 'pending',
  `last_checked` datetime DEFAULT NULL,
  `checked_by` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_employee_compliance_records`
--

INSERT INTO `lc_employee_compliance_records` (`id`, `employee_id`, `compliance_item_id`, `status`, `last_checked`, `checked_by`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'compliant', '2026-03-24 22:28:21', 1, 'Employee has proper maternity leave documentation', '2026-03-24 14:28:21', '2026-03-24 14:28:21'),
(2, 1, 6, 'compliant', '2026-03-24 22:28:21', 1, 'Acknowledged anti-harassment policy', '2026-03-24 14:28:21', '2026-03-24 14:28:21'),
(3, 2, 1, 'compliant', '2026-03-24 22:28:21', 1, 'No pending maternity leave requests', '2026-03-24 14:28:21', '2026-03-24 14:28:21'),
(4, 2, 6, 'compliant', '2026-03-24 22:28:21', 1, 'Policy acknowledged', '2026-03-24 14:28:21', '2026-03-24 14:28:21'),
(5, 3, 4, 'non_compliant', '2026-03-24 22:28:21', 2, 'Missing Q1 safety training', '2026-03-24 14:28:21', '2026-03-24 14:28:21'),
(6, 3, 13, 'compliant', '2026-03-24 22:28:21', 2, 'Fire safety certificate valid', '2026-03-24 14:28:21', '2026-03-24 14:28:21'),
(7, 4, 7, 'compliant', '2026-03-24 22:28:21', 1, 'Safe spaces policy in place', '2026-03-24 14:28:21', '2026-03-24 14:28:21'),
(8, 4, 14, 'non_compliant', '2026-03-24 22:28:21', 1, 'First aid kit not restocked', '2026-03-24 14:28:21', '2026-03-24 14:28:21'),
(9, 5, 8, 'compliant', '2026-03-24 22:28:21', 1, 'Tax compliance up to date', '2026-03-24 14:28:21', '2026-03-24 14:28:21'),
(10, 5, 11, 'compliant', '2026-03-24 22:28:21', 1, 'Pag-IBIG contributions current', '2026-03-24 14:28:21', '2026-03-24 14:28:21'),
(11, 6, 2, 'compliant', '2026-03-24 22:28:21', 1, 'Paternity leave processed', '2026-03-24 14:28:21', '2026-03-24 14:28:21'),
(12, 6, 3, 'pending', '2026-03-24 22:28:21', 1, 'Solo parent status verification pending', '2026-03-24 14:28:21', '2026-03-24 14:28:21'),
(13, 1, 1, 'compliant', '2026-03-26 00:24:48', 1, 'Employee has proper maternity leave documentation', '2026-03-25 16:24:48', '2026-03-25 16:24:48'),
(14, 1, 6, 'compliant', '2026-03-26 00:24:48', 1, 'Acknowledged anti-harassment policy', '2026-03-25 16:24:48', '2026-03-25 16:24:48'),
(15, 2, 1, 'compliant', '2026-03-26 00:24:48', 1, 'No pending maternity leave requests', '2026-03-25 16:24:48', '2026-03-25 16:24:48'),
(16, 2, 6, 'compliant', '2026-03-26 00:24:48', 1, 'Policy acknowledged', '2026-03-25 16:24:48', '2026-03-25 16:24:48'),
(17, 3, 4, 'non_compliant', '2026-03-26 00:24:48', 2, 'Missing Q1 safety training', '2026-03-25 16:24:48', '2026-03-25 16:24:48'),
(18, 3, 13, 'compliant', '2026-03-26 00:24:48', 2, 'Fire safety certificate valid', '2026-03-25 16:24:48', '2026-03-25 16:24:48'),
(19, 4, 7, 'compliant', '2026-03-26 00:24:48', 1, 'Safe spaces policy in place', '2026-03-25 16:24:48', '2026-03-25 16:24:48'),
(20, 4, 14, 'non_compliant', '2026-03-26 00:24:48', 1, 'First aid kit not restocked', '2026-03-25 16:24:48', '2026-03-25 16:24:48'),
(21, 5, 8, 'compliant', '2026-03-26 00:24:48', 1, 'Tax compliance up to date', '2026-03-25 16:24:48', '2026-03-25 16:24:48'),
(22, 5, 11, 'compliant', '2026-03-26 00:24:48', 1, 'Pag-IBIG contributions current', '2026-03-25 16:24:48', '2026-03-25 16:24:48'),
(23, 6, 2, 'compliant', '2026-03-26 00:24:48', 1, 'Paternity leave processed', '2026-03-25 16:24:48', '2026-03-25 16:24:48'),
(24, 6, 3, 'pending', '2026-03-26 00:24:48', 1, 'Solo parent status verification pending', '2026-03-25 16:24:48', '2026-03-25 16:24:48');

-- --------------------------------------------------------

--
-- Table structure for table `lc_employee_contributions`
--

CREATE TABLE `lc_employee_contributions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `contribution_type` enum('sss','pagibig','philhealth') NOT NULL,
  `status` enum('submitted','pending') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `contribution_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lc_employee_contributions`
--

INSERT INTO `lc_employee_contributions` (`id`, `employee_id`, `contribution_type`, `status`, `notes`, `created_at`, `updated_at`, `contribution_number`) VALUES
(1, 1, 'sss', 'submitted', NULL, '2026-03-29 21:03:57', '2026-03-29 21:03:57', '26-5464544-8'),
(2, 1, 'pagibig', 'submitted', NULL, '2026-03-29 21:03:57', '2026-03-29 21:03:57', '2849-4926-6162'),
(3, 1, 'philhealth', 'submitted', NULL, '2026-03-29 21:03:57', '2026-03-29 21:03:57', '2162-6265-1651'),
(4, 6, 'sss', 'pending', NULL, '2026-03-29 21:09:39', '2026-03-29 21:09:39', ''),
(5, 6, 'pagibig', 'submitted', NULL, '2026-03-29 21:09:39', '2026-03-29 21:09:39', '6241-9265-9592'),
(6, 6, 'philhealth', 'pending', NULL, '2026-03-29 21:09:39', '2026-03-29 21:09:39', ''),
(7, 3, 'sss', 'submitted', NULL, '2026-03-29 21:09:52', '2026-03-29 21:09:52', '98-5959126-2'),
(8, 3, 'philhealth', 'pending', NULL, '2026-03-29 21:09:52', '2026-03-29 21:09:52', ''),
(9, 3, 'pagibig', 'pending', NULL, '2026-03-29 21:09:52', '2026-03-29 21:09:52', ''),
(10, 4, 'sss', 'pending', NULL, '2026-03-29 21:10:08', '2026-03-29 21:10:08', ''),
(11, 4, 'pagibig', 'pending', NULL, '2026-03-29 21:10:08', '2026-03-29 21:10:08', ''),
(12, 4, 'philhealth', 'submitted', NULL, '2026-03-29 21:10:08', '2026-03-29 21:10:08', '2965-9595-8929'),
(13, 2, 'sss', 'pending', NULL, '2026-03-29 21:10:21', '2026-03-29 21:10:21', ''),
(14, 2, 'pagibig', 'submitted', NULL, '2026-03-29 21:10:21', '2026-03-29 21:10:21', '9829-5989-5262'),
(15, 2, 'philhealth', 'pending', NULL, '2026-03-29 21:10:21', '2026-03-29 21:10:21', ''),
(16, 5, 'sss', 'submitted', NULL, '2026-03-29 21:10:35', '2026-04-05 15:25:42', '11-2312312-3'),
(17, 5, 'pagibig', 'submitted', NULL, '2026-03-29 21:10:35', '2026-04-05 15:25:42', '1213-1123-1231'),
(18, 5, 'philhealth', 'submitted', NULL, '2026-03-29 21:10:35', '2026-04-05 15:25:42', '2131-2112-3123');

-- --------------------------------------------------------

--
-- Table structure for table `lc_employee_deductions`
--

CREATE TABLE `lc_employee_deductions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `deduction_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_employee_deductions`
--

INSERT INTO `lc_employee_deductions` (`id`, `employee_id`, `deduction_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 2, 4),
(6, 3, 1),
(7, 3, 2),
(8, 3, 3),
(9, 3, 4),
(10, 4, 4),
(11, 5, 1),
(12, 5, 2),
(13, 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `lc_employee_risk_assessments`
--

CREATE TABLE `lc_employee_risk_assessments` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `risk_type` varchar(100) NOT NULL,
  `risk_level` enum('low','medium','high','critical') DEFAULT 'low',
  `description` text DEFAULT NULL,
  `mitigation_plan` text DEFAULT NULL,
  `status` enum('active','mitigated','resolved') DEFAULT 'active',
  `assessed_by` int(11) DEFAULT NULL,
  `assessment_date` date DEFAULT NULL,
  `next_review_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_employee_risk_assessments`
--

INSERT INTO `lc_employee_risk_assessments` (`id`, `employee_id`, `risk_type`, `risk_level`, `description`, `mitigation_plan`, `status`, `assessed_by`, `assessment_date`, `next_review_date`, `created_at`) VALUES
(1, 1, 'Overtime Risk', 'low', 'Regular overtime hours but within limits', 'Monitor monthly', 'active', 1, '2026-03-01', '2026-06-01', '2026-03-24 14:28:21'),
(2, 2, 'Contract Expiry Risk', 'medium', 'Part-time contract expiring soon', 'Process renewal', 'active', 1, '2026-03-10', '2026-04-10', '2026-03-24 14:28:21'),
(3, 3, 'Leave Balance Risk', 'low', 'High leave balance but within policy', 'Monitor usage', 'active', 1, '2026-03-05', '2026-06-05', '2026-03-24 14:28:21'),
(4, 4, 'Training Compliance', 'medium', 'Missing some mandatory trainings', 'Schedule training', 'active', 2, '2026-03-15', '2026-04-15', '2026-03-24 14:28:21'),
(5, 5, 'Performance Risk', 'high', 'Below average performance ratings', 'Performance improvement plan', 'active', 1, '2026-03-01', '2026-04-01', '2026-03-24 14:28:21'),
(6, 6, 'Contract Expiry Risk', 'high', 'Probationary period nearly completed', 'Process regularization', 'active', 1, '2026-03-20', '2026-04-01', '2026-03-24 14:28:21'),
(7, 1, 'Overtime Risk', 'low', 'Regular overtime hours but within limits', 'Monitor monthly', 'active', 1, '2026-03-01', '2026-06-01', '2026-03-25 16:24:48'),
(8, 2, 'Contract Expiry Risk', 'medium', 'Part-time contract expiring soon', 'Process renewal', 'active', 1, '2026-03-10', '2026-04-10', '2026-03-25 16:24:48'),
(9, 3, 'Leave Balance Risk', 'low', 'High leave balance but within policy', 'Monitor usage', 'active', 1, '2026-03-05', '2026-06-05', '2026-03-25 16:24:48'),
(10, 4, 'Training Compliance', 'medium', 'Missing some mandatory trainings', 'Schedule training', 'active', 2, '2026-03-15', '2026-04-15', '2026-03-25 16:24:48'),
(11, 5, 'Performance Risk', 'high', 'Below average performance ratings', 'Performance improvement plan', 'active', 1, '2026-03-01', '2026-04-01', '2026-03-25 16:24:48'),
(12, 6, 'Contract Expiry Risk', 'high', 'Probationary period nearly completed', 'Process regularization', 'active', 1, '2026-03-20', '2026-04-01', '2026-03-25 16:24:48');

-- --------------------------------------------------------

--
-- Table structure for table `lc_employee_salary`
--

CREATE TABLE `lc_employee_salary` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `salary_structure_id` int(11) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `effective_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_employee_salary`
--

INSERT INTO `lc_employee_salary` (`id`, `employee_id`, `salary_structure_id`, `rate`, `effective_date`) VALUES
(1, 1, 1, NULL, '2023-01-01'),
(2, 2, NULL, 500.00, '2023-01-01'),
(3, 3, 3, NULL, '2023-01-01'),
(4, 4, 4, NULL, '2023-01-01'),
(5, 5, 5, NULL, '2023-01-01'),
(6, 6, NULL, 550.00, '2024-08-01');

-- --------------------------------------------------------

--
-- Table structure for table `lc_employee_shifts`
--

CREATE TABLE `lc_employee_shifts` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_employment_history`
--

CREATE TABLE `lc_employment_history` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `reason_for_change` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_employment_types`
--

CREATE TABLE `lc_employment_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_employment_types`
--

INSERT INTO `lc_employment_types` (`id`, `name`) VALUES
(1, 'Regular'),
(2, 'Contractual'),
(3, 'Part-Time');

-- --------------------------------------------------------

--
-- Table structure for table `lc_holidays`
--

CREATE TABLE `lc_holidays` (
  `id` int(11) NOT NULL,
  `holiday_name` varchar(100) DEFAULT NULL,
  `holiday_date` date DEFAULT NULL,
  `type` enum('regular','special') DEFAULT 'regular'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_incidents`
--

CREATE TABLE `lc_incidents` (
  `id` int(11) NOT NULL,
  `incident_id` varchar(50) DEFAULT NULL COMMENT 'Auto-generated: INC-YYYY-NNNN',
  `reporter_id` int(11) DEFAULT NULL COMMENT 'Foreign key to employees - who reported',
  `reporter_employee_id` varchar(20) DEFAULT NULL COMMENT 'Reporter Employee ID',
  `reporter_name` varchar(100) DEFAULT NULL COMMENT 'Reporter Full Name',
  `reporter_department` varchar(100) DEFAULT NULL COMMENT 'Reporter Department',
  `reporter_position` varchar(100) DEFAULT NULL COMMENT 'Reporter Position',
  `reporter_contact` varchar(100) DEFAULT NULL COMMENT 'Reporter Contact Info',
  `reporter_role` enum('witness','victim','reporter') DEFAULT 'reporter' COMMENT 'Role in Incident',
  `reporter_type` enum('employee','hr','management','anonymous') DEFAULT 'employee',
  `respondent_id` int(11) DEFAULT NULL COMMENT 'Foreign key to employees - person being reported',
  `respondent_employee_id` varchar(20) DEFAULT NULL COMMENT 'Respondent Employee ID',
  `respondent_name` varchar(100) DEFAULT NULL COMMENT 'Respondent Full Name',
  `respondent_department` varchar(100) DEFAULT NULL COMMENT 'Respondent Department',
  `respondent_position` varchar(100) DEFAULT NULL COMMENT 'Respondent Position',
  `respondent_relationship` enum('co_worker','supervisor','subordinate','external') DEFAULT 'co_worker' COMMENT 'Relationship to Reporter',
  `incident_type` varchar(100) DEFAULT NULL COMMENT 'Misconduct, Harassment, Absenteeism, etc.',
  `type` enum('workplace_safety','harassment','policy_violation','complaint','other') DEFAULT 'other',
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `incident_date` date DEFAULT NULL,
  `incident_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('submitted','under_review','investigation','escalated','resolved','closed','open','in_progress','pending_approval','rejected','closed_no_violation') DEFAULT 'submitted',
  `assigned_to` int(11) DEFAULT NULL COMMENT 'HR officer assigned to handle',
  `created_by` int(11) DEFAULT NULL,
  `reported_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_incidents`
--

INSERT INTO `lc_incidents` (`id`, `incident_id`, `reporter_id`, `reporter_employee_id`, `reporter_name`, `reporter_department`, `reporter_position`, `reporter_contact`, `reporter_role`, `reporter_type`, `respondent_id`, `respondent_employee_id`, `respondent_name`, `respondent_department`, `respondent_position`, `respondent_relationship`, `incident_type`, `type`, `severity`, `incident_date`, `incident_time`, `location`, `title`, `description`, `status`, `assigned_to`, `created_by`, `reported_by`, `created_at`, `updated_at`, `resolved_at`) VALUES
(1, 'INC-2026-0001', 3, 'S001', 'John Rey', 'IT Department', 'Software Developer', NULL, 'reporter', 'employee', 5, 'S003', 'Pedro Santos', 'IT Department', 'IT Supervisor', 'supervisor', 'harassment', 'harassment', 'high', '2026-03-20', '09:30:00', 'Office Floor 2', 'Workplace Harassment', 'Experienced verbal harassment from supervisor regarding work assignments', 'submitted', NULL, 3, 3, '2026-03-20 10:15:00', '2026-03-20 10:15:00', NULL),
(2, 'INC-2026-0002', 4, 'S002', 'Liza Torres', 'Finance', 'Accountant', NULL, 'witness', 'employee', NULL, NULL, NULL, NULL, NULL, NULL, 'safety_violation', 'workplace_safety', 'medium', '2026-03-18', '14:00:00', 'Warehouse Area', 'Safety Protocol Violation', 'Observed unsafe equipment handling in the warehouse area without proper PPE', 'under_review', 2, 4, 4, '2026-03-18 15:30:00', '2026-03-19 09:00:00', NULL),
(3, 'INC-2026-0003', 3, 'S001', 'John Rey', 'IT Department', 'Software Developer', NULL, 'victim', 'employee', 5, 'S003', 'Pedro Santos', 'IT Department', 'IT Supervisor', 'supervisor', 'theft', 'other', 'critical', '2026-03-15', '08:00:00', 'IT Office', 'Property Theft', 'Missing company laptop from locked office cabinet', 'investigation', 1, 3, 3, '2026-03-15 08:45:00', '2026-03-16 11:20:00', NULL),
(4, 'INC-2026-0004', 6, 'T003', 'Karen Villanueva', 'HR Department', 'HR Assistant', NULL, 'reporter', 'employee', 2, 'T002', 'Mark Lee', 'Operations', 'Operations Manager', 'supervisor', 'policy_violation', 'policy_violation', 'low', '2026-03-22', '17:00:00', 'HR Office', 'Repeated Absenteeism', 'Employee has been consistently arriving late and taking unauthorized leaves', 'escalated', 1, 6, 6, '2026-03-22 17:30:00', '2026-04-06 05:35:51', NULL),
(5, 'INC-2026-0005', 4, 'S002', 'Liza Torres', 'Finance', 'Accountant', NULL, 'reporter', 'employee', 3, 'S001', 'John Rey', 'IT Department', 'Software Developer', 'co_worker', 'harassment', 'harassment', 'medium', '2026-03-10', '11:00:00', 'Office Room', 'Workplace Bullying', 'Experienced repeated intimidation and verbal abuse from colleague', 'resolved', 1, 4, 4, '2026-03-10 12:00:00', '2026-04-04 14:25:02', NULL),
(6, 'INC-2026-0006', NULL, NULL, 'Compliance', NULL, NULL, NULL, 'witness', 'employee', 3, 'S001', 'John Rey, Mark Lee', '2', '3', 'co_worker', 'harassment', 'harassment', 'high', '2026-04-05', '12:50:00', 'Parking Lot', 'Workplace fight', 'Fist fight at the parking lot', 'escalated', NULL, NULL, NULL, '2026-04-05 09:58:36', '2026-04-06 02:17:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lc_incident_comments`
--

CREATE TABLE `lc_incident_comments` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Who added the comment',
  `comment` text NOT NULL,
  `is_internal` tinyint(1) DEFAULT 0 COMMENT 'Internal notes not visible to reporter',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_incident_evidence`
--

CREATE TABLE `lc_incident_evidence` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_incident_evidence`
--

INSERT INTO `lc_incident_evidence` (`id`, `incident_id`, `file_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `description`, `uploaded_at`) VALUES
(1, 6, 'rice.jpg', 'uploads/incident_evidence/69d2324d016ce_1775383117.jpg', 'image/jpeg', 5891, 999, NULL, '2026-04-05 09:58:37');

-- --------------------------------------------------------

--
-- Table structure for table `lc_incident_notifications`
--

CREATE TABLE `lc_incident_notifications` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `recipient_id` int(11) DEFAULT NULL COMMENT 'Employee receiving notification',
  `recipient_email` varchar(255) DEFAULT NULL,
  `notification_type` enum('new_incident','status_change','disciplinary_action','incident_closed') NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `sent_by` int(11) DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_incident_people_involved`
--

CREATE TABLE `lc_incident_people_involved` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL COMMENT 'Foreign key to employees',
  `role` enum('complainant','respondent','witness','involved') DEFAULT 'involved',
  `statement` text DEFAULT NULL,
  `is_complainant` tinyint(1) DEFAULT 0,
  `is_respondent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_incident_status_history`
--

CREATE TABLE `lc_incident_status_history` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_incident_types`
--

CREATE TABLE `lc_incident_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `severity_default` enum('low','medium','high','critical') DEFAULT 'medium',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_incident_types`
--

INSERT INTO `lc_incident_types` (`id`, `type_name`, `category`, `severity_default`, `is_active`, `created_at`) VALUES
(1, 'Misconduct', 'Behavioral', 'medium', 1, '2026-03-25 18:39:05'),
(2, 'Harassment', 'Behavioral', 'high', 1, '2026-03-25 18:39:05'),
(3, 'Absenteeism', 'Attendance', 'low', 1, '2026-03-25 18:39:05'),
(4, 'Theft', 'Criminal', 'critical', 1, '2026-03-25 18:39:05'),
(5, 'Policy Violation', 'Compliance', 'medium', 1, '2026-03-25 18:39:05'),
(6, 'Safety Violation', 'Safety', 'high', 1, '2026-03-25 18:39:05'),
(7, 'Bullying', 'Behavioral', 'high', 1, '2026-03-25 18:39:05'),
(8, 'Fraud', 'Criminal', 'critical', 1, '2026-03-25 18:39:05'),
(9, 'Drug/Alcohol', 'Safety', 'critical', 1, '2026-03-25 18:39:05'),
(10, 'Other', 'Other', 'medium', 1, '2026-03-25 18:39:05');

-- --------------------------------------------------------

--
-- Table structure for table `lc_labor_law_compliance`
--

CREATE TABLE `lc_labor_law_compliance` (
  `id` int(11) NOT NULL,
  `compliance_id` varchar(50) DEFAULT NULL COMMENT 'Unique compliance code (e.g., SSS-001, DOLE-001)',
  `requirement_name` varchar(255) NOT NULL COMMENT 'Name of the compliance requirement',
  `category` enum('Government Contributions','Employee Benefits','Legal Documentation','Mandatory Reports','Workplace Safety','Tax Compliance') NOT NULL DEFAULT 'Government Contributions',
  `subcategory` varchar(100) DEFAULT NULL COMMENT 'Sub-category for more specific classification',
  `description` text DEFAULT NULL COMMENT 'Detailed description of the requirement',
  `legal_basis` varchar(255) DEFAULT NULL COMMENT 'Law or regulation reference (e.g., RA 8282, Labor Code)',
  `status` enum('Compliant','Pending','Overdue','Not Applicable') DEFAULT 'Pending',
  `due_date` date DEFAULT NULL COMMENT 'Deadline for compliance',
  `frequency` enum('Monthly','Quarterly','Semi-Annual','Yearly','One-time') DEFAULT 'Monthly',
  `assigned_to` int(11) DEFAULT NULL COMMENT 'Foreign key to employees table - responsible HR personnel',
  `attachments` text DEFAULT NULL COMMENT 'JSON array of document paths',
  `remarks` text DEFAULT NULL COMMENT 'Additional notes or comments',
  `last_checked` datetime DEFAULT NULL COMMENT 'Last verification date',
  `next_due_date` date DEFAULT NULL COMMENT 'Next deadline after current one',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Whether this compliance item is active',
  `created_by` int(11) DEFAULT NULL COMMENT 'User who created the record',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_labor_law_compliance`
--

INSERT INTO `lc_labor_law_compliance` (`id`, `compliance_id`, `requirement_name`, `category`, `subcategory`, `description`, `legal_basis`, `status`, `due_date`, `frequency`, `assigned_to`, `attachments`, `remarks`, `last_checked`, `next_due_date`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'SSS-001', 'SSS Registration & Contribution', 'Government Contributions', 'Social Security', 'Monthly SSS contribution remittance for all employees', 'Social Security Act of 2018 (RA 11199)', 'Compliant', '2026-03-31', 'Monthly', 1, NULL, NULL, '2026-03-01 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(2, 'SSS-002', 'SSS EC (Employee Compensation) Program', 'Government Contributions', 'Social Security', 'EC contributions for work-related injuries', 'SS Act of 2018', 'Compliant', '2026-03-31', 'Monthly', 1, NULL, NULL, '2026-03-01 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(3, 'PH-001', 'PhilHealth Registration & Contribution', 'Government Contributions', 'Health Insurance', 'Monthly PhilHealth contribution for all employees', 'National Health Insurance Act (RA 7875, as amended by RA 10606)', 'Pending', '2026-03-31', 'Monthly', 1, NULL, NULL, '2026-03-01 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(4, 'PH-002', 'PhilHealth Coverage for Dependents', 'Government Contributions', 'Health Insurance', 'Ensure all qualified dependents are registered', 'RA 7875', 'Compliant', '2026-12-31', 'Yearly', 1, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(5, 'PI-001', 'Pag-IBIG Registration & Contribution', 'Government Contributions', 'Housing', 'Monthly Pag-IBIG contribution remittance', 'Expanded Pag-IBIG Act of 2019 (RA 11491)', 'Compliant', '2026-03-31', 'Monthly', 1, NULL, NULL, '2026-03-01 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(6, 'MAT-001', 'Maternity Leave Compliance', 'Employee Benefits', 'Leave Benefits', '105 days paid maternity leave for qualifying employees', 'Expanded Maternity Leave Act of 2019 (RA 11210)', 'Compliant', '2026-12-31', 'Yearly', 1, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(7, 'PAT-001', 'Paternity Leave Processing', 'Employee Benefits', 'Leave Benefits', '7 days paternity leave for married employees', 'Paternity Leave Act of 2007 (RA 8187)', 'Compliant', '2026-12-31', 'Yearly', 1, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(8, 'SOLO-001', 'Solo Parent Leave', 'Employee Benefits', 'Leave Benefits', '7 days solo parent leave per RA 8972', 'Solo Parents Welfare Act (RA 8972)', 'Pending', '2026-12-31', 'Yearly', 1, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(9, 'VACL-001', 'Vacation Leave Credits', 'Employee Benefits', 'Leave Benefits', 'Ensure employees receive correct leave credits', 'Labor Code Article 100', 'Compliant', '2026-12-31', 'Yearly', 1, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(10, 'SICK-001', 'Sick Leave Credits', 'Employee Benefits', 'Leave Benefits', 'Proper sick leave tracking and utilization', 'Labor Code Article 100', 'Compliant', '2026-12-31', 'Yearly', 1, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(11, '13TH-001', '13th Month Pay', 'Employee Benefits', 'Yearly Bonus', 'Payment of 13th month pay not later than December 24', 'Presidential Decree 851', 'Pending', '2026-12-24', 'Yearly', 1, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(12, 'OVERTIME-001', 'Overtime Pay Compliance', 'Employee Benefits', 'Working Hours', 'Proper computation and payment of overtime work', 'Labor Code Articles 83-87', 'Compliant', '2026-12-31', 'Monthly', 1, NULL, NULL, '2026-03-01 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(13, 'CONTRACT-001', 'Employment Contract Management', 'Legal Documentation', 'Contracts', 'All employees must have valid employment contracts', 'Labor Code Article 279', 'Compliant', '2026-12-31', 'Yearly', 1, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(14, 'CONTRACT-002', 'Contract of Regular Employment', 'Legal Documentation', 'Contracts', 'Ensure regular employees have proper appointments', 'Labor Code Article 295', 'Compliant', '2026-12-31', 'Yearly', 1, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(15, 'CONTRACT-003', 'Part-time/Job Order Contracts', 'Legal Documentation', 'Contracts', 'Proper documentation for part-time and job order workers', 'DOLE Department Order 174', 'Compliant', '2026-12-31', 'Yearly', 1, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(16, 'ID-001', 'Employee ID and Records', 'Legal Documentation', 'Personal Files', 'Maintain employee ID and personal data sheets', 'Labor Code', 'Compliant', '2026-12-31', 'Yearly', 1, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(17, 'DOLE-001', 'Annual Report (AWR)', 'Mandatory Reports', 'DOLE', 'Annual Work Report to DOLE', 'Labor Code Article 128', 'Compliant', '2026-01-31', 'Yearly', 1, NULL, NULL, '2026-04-06 10:16:51', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-04-06 02:16:51'),
(18, 'DOLE-002', 'Quarterly Report on Special Concerns', 'Mandatory Reports', 'DOLE', 'Quarterly submission of special concerns to DOLE', 'DOLE Regulations', 'Compliant', '2026-03-31', 'Quarterly', 1, NULL, NULL, '2026-03-01 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(19, 'BIR-001', 'BIR Withholding Tax', 'Mandatory Reports', 'BIR', 'Monthly withholding tax remittance', 'Tax Reform Act of 1997 (RA 8424)', 'Compliant', '2026-03-25', 'Monthly', 2, NULL, NULL, '2026-03-01 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(20, 'BIR-002', 'Annual BIR Reports (Alphalist, 2316)', 'Mandatory Reports', 'BIR', 'Annual withholding tax certificates and alphalist', 'Tax Code', 'Overdue', '2026-01-31', 'Yearly', 2, NULL, NULL, '2026-04-06 10:39:44', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-04-06 02:39:44'),
(21, 'SSS-003', 'SSS Employment Report', 'Mandatory Reports', 'SSS', 'Monthly employment report to SSS', 'SS Act of 2018', 'Compliant', '2026-03-31', 'Monthly', 1, NULL, NULL, '2026-03-01 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(22, 'PH-003', 'PhilHealth Employer Data', 'Mandatory Reports', 'PhilHealth', 'Monthly employer data report', 'NTHC Act', 'Pending', '2026-03-31', 'Monthly', 1, NULL, NULL, '2026-03-01 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(23, 'OSH-001', 'OSHA Safety Training', 'Workplace Safety', 'Training', 'Quarterly safety training for all employees', 'Occupational Safety and Health Standards (OSHS) 2016', 'Compliant', '2026-03-31', 'Quarterly', 3, NULL, NULL, '2026-03-01 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(24, 'OSH-002', 'First Aid Kit Maintenance', 'Workplace Safety', 'Safety Equipment', 'Monthly inspection of first aid kits', 'OSHS 2016 Rule 1040', 'Pending', '2026-02-28', 'Monthly', 3, NULL, NULL, '2026-04-05 15:40:55', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-04-05 07:40:55'),
(25, 'OSH-003', 'Fire Safety Inspection', 'Workplace Safety', 'Safety Certificate', 'Annual fire safety inspection and certification', 'Fire Code of the Philippines (RA 9514)', 'Compliant', '2026-09-30', 'Yearly', 3, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(26, 'OSH-004', 'Workplace Health & Safety Committee', 'Workplace Safety', 'Committee', 'Maintain functional OSH committee', 'OSHS 2016 Rule 1032', 'Compliant', '2026-12-31', 'Yearly', 3, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(27, 'TAX-001', 'Withholding Tax on Compensation', 'Tax Compliance', 'BIR', 'Proper withholding of employee taxes', 'Tax Code Section 24', 'Compliant', '2026-03-25', 'Monthly', 2, NULL, NULL, '2026-03-01 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(28, 'TAX-002', 'Withholding Tax on Benefits', 'Tax Compliance', 'BIR', 'Tax withholding on employee benefits', 'Tax Code Section 24', 'Pending', '2026-12-31', 'Monthly', 2, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(29, 'TAX-003', 'BIR Registration Updates', 'Tax Compliance', 'BIR', 'Keep BIR registration updated', 'Tax Code', 'Compliant', '2026-12-31', 'Yearly', 2, NULL, NULL, '2026-01-15 00:00:00', NULL, 1, NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(30, 'szffghjk', 'sdfzsdxbc', 'Government Contributions', NULL, 'sggesdfzdg', 'zgvxbxcb', 'Compliant', '2026-04-22', 'Yearly', 4, NULL, 'sdsvafsa', '2026-04-05 15:01:20', NULL, 1, 999, '2026-04-05 01:41:00', '2026-04-05 07:01:20'),
(31, 'dgawddsg', 'eferfwefwsdf', 'Employee Benefits', NULL, 'zxc zxvczxvzxcs df', 'afcsdgvsggfsdf', 'Compliant', '2026-04-01', 'Yearly', 4, NULL, 'sdfsdf', NULL, NULL, 0, 999, '2026-04-05 01:49:41', '2026-04-05 01:57:47'),
(32, 'asfsdfgsdfg', 'sdfsdgdfgsdfg', 'Government Contributions', NULL, 'asfdasdfsdf', 'sgdsdfsdfs', 'Overdue', '2026-04-21', 'Monthly', 4, NULL, 'sdfsdfsdfsdf', NULL, NULL, 0, 999, '2026-04-05 07:00:56', '2026-04-05 07:01:07');

-- --------------------------------------------------------

--
-- Table structure for table `lc_labor_law_compliance_attachments`
--

CREATE TABLE `lc_labor_law_compliance_attachments` (
  `id` int(11) NOT NULL,
  `compliance_id` int(11) NOT NULL COMMENT 'Foreign key to labor_law_compliance',
  `file_name` varchar(255) NOT NULL COMMENT 'Original file name',
  `file_path` varchar(500) NOT NULL COMMENT 'Path to stored file',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'MIME type',
  `file_size` int(11) DEFAULT NULL COMMENT 'File size in bytes',
  `description` varchar(255) DEFAULT NULL COMMENT 'Description of the document',
  `uploaded_by` int(11) DEFAULT NULL COMMENT 'User who uploaded the file',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_labor_law_compliance_attachments`
--

INSERT INTO `lc_labor_law_compliance_attachments` (`id`, `compliance_id`, `file_name`, `file_path`, `file_type`, `file_size`, `description`, `uploaded_by`, `uploaded_at`) VALUES
(1, 17, 'activity_alerts.sql', 'uploads/compliance_documents/1774456710_activity_alerts.sql', 'application/octet-stream', 5947, NULL, 999, '2026-03-25 16:38:30'),
(2, 17, '1774456710_activity_alerts (1).sql', 'uploads/compliance_documents/1775372424_1774456710_activity_alerts (1).sql', 'application/octet-stream', 5947, NULL, 999, '2026-04-05 07:00:24');

-- --------------------------------------------------------

--
-- Table structure for table `lc_labor_law_compliance_history`
--

CREATE TABLE `lc_labor_law_compliance_history` (
  `id` int(11) NOT NULL,
  `compliance_id` int(11) NOT NULL COMMENT 'Foreign key to labor_law_compliance',
  `action` varchar(50) NOT NULL COMMENT 'Action type: created, updated, status_changed, document_uploaded',
  `field_changed` varchar(50) DEFAULT NULL COMMENT 'Field that was changed',
  `old_value` text DEFAULT NULL COMMENT 'Previous value',
  `new_value` text DEFAULT NULL COMMENT 'New value',
  `changed_by` int(11) DEFAULT NULL COMMENT 'User who made the change',
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_leaves`
--

CREATE TABLE `lc_leaves` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `leave_type` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('approved','pending','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_leaves`
--

INSERT INTO `lc_leaves` (`id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `status`) VALUES
(1, 1, 'Sick Leave', '2026-02-10', '2026-02-12', 'approved'),
(2, 2, 'Vacation Leave', '2026-02-15', '2026-02-18', 'pending'),
(3, 3, 'Sick Leave', '2026-02-05', '2026-02-06', 'approved'),
(4, 4, 'Vacation Leave', '2026-02-20', '2026-02-22', 'approved'),
(5, 5, 'Emergency Leave', '2026-02-03', '2026-02-03', 'rejected'),
(6, 6, 'Sick Leave', '2026-02-08', '2026-02-09', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `lc_leave_documents`
--

CREATE TABLE `lc_leave_documents` (
  `id` int(11) NOT NULL,
  `leave_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_leave_documents`
--

INSERT INTO `lc_leave_documents` (`id`, `leave_id`, `document_type`, `file_path`, `uploaded_at`) VALUES
(1, 3, 'Medical Certificate', 'uploads/leave_documents/sick_medical_cert_3.pdf', '2026-03-26 06:16:30'),
(2, 5, 'Death Certificate', 'uploads/leave_documents/bereavement_death_cert_5.pdf', '2026-03-26 06:16:30'),
(3, 7, 'Medical Certificate', 'uploads/leave_documents/sick_medical_cert_7.pdf', '2026-03-26 06:16:30');

-- --------------------------------------------------------

--
-- Table structure for table `lc_leave_requests`
--

CREATE TABLE `lc_leave_requests` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` decimal(5,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `checked_by` int(11) DEFAULT NULL,
  `checked_at` timestamp NULL DEFAULT NULL,
  `hr_comments` text DEFAULT NULL,
  `checklist_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_leave_requests`
--

INSERT INTO `lc_leave_requests` (`id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `total_days`, `reason`, `status`, `checked_by`, `checked_at`, `hr_comments`, `checklist_data`, `created_at`, `updated_at`) VALUES
(1, 1, 'Maternity Leave', '2026-04-01', '2026-07-15', 105.00, 'Pregnancy with expected delivery date July 2026 - Required for maternity coverage under RA 11210', 'pending', NULL, NULL, NULL, NULL, '2026-03-26 06:16:30', '2026-03-26 06:16:30'),
(2, 2, 'Paternity Leave', '2026-03-20', '2026-03-26', 7.00, 'Wife Maria Santos scheduled for normal delivery on March 22, 2026', 'pending', NULL, NULL, NULL, NULL, '2026-03-26 06:16:30', '2026-03-26 06:16:30'),
(3, 3, 'Sick Leave', '2026-03-10', '2026-03-11', 2.00, 'Medical certificate attached - Upper respiratory infection', 'approved', NULL, '2026-04-05 22:49:43', '', NULL, '2026-03-26 06:16:30', '2026-04-05 22:49:43'),
(4, 4, 'Vacation Leave', '2026-04-10', '2026-04-15', 6.00, 'Family vacation to Palawan - filed 2 weeks in advance', 'pending', NULL, NULL, NULL, NULL, '2026-03-26 06:16:30', '2026-03-26 06:16:30'),
(5, 5, 'Bereavement Leave', '2026-03-05', '2026-03-07', 3.00, 'Death of father - death certificate attached', 'pending', NULL, NULL, NULL, NULL, '2026-03-26 06:16:30', '2026-03-26 06:16:30'),
(6, 6, 'Emergency Leave', '2026-03-12', '2026-03-12', 1.00, 'Immediate family medical emergency - hospitalization', 'pending', NULL, NULL, NULL, NULL, '2026-03-26 06:16:30', '2026-03-26 06:16:30'),
(7, 1, 'Sick Leave', '2026-02-10', '2026-02-12', 3.00, 'Flu with fever', 'approved', NULL, NULL, NULL, '{\"requirements\":{\"medical_cert\":true,\"leave_credits\":true,\"reason_stated\":true},\"hrChecks\":{\"days_reasonable\":true,\"medical_proof\":true,\"not_abused\":true}}', '2026-03-26 06:16:30', '2026-03-26 06:16:30'),
(8, 2, 'Vacation Leave', '2026-02-15', '2026-02-18', 4.00, 'Personal matter', 'approved', NULL, NULL, NULL, '{\"requirements\":{\"leave_credits\":true,\"filed_advance\":true},\"hrChecks\":{\"no_schedule_conflict\":true,\"enough_balance\":true,\"coverage_arranged\":true}}', '2026-03-26 06:16:30', '2026-03-26 06:16:30'),
(9, 3, 'Emergency Leave', '2026-02-20', '2026-02-20', 1.00, 'Power outage at home', 'rejected', NULL, NULL, NULL, '{\"requirements\":{\"valid_reason\":true,\"supporting_explanation\":false},\"hrChecks\":{\"urgency_justified\":false,\"not_abused\":true,\"documents_if_applicable\":false}}', '2026-03-26 06:16:30', '2026-03-26 06:16:30'),
(10, 9, 'jaswhdawdjaw', '2026-04-10', '2026-04-13', 4.00, 'jaswhdawdjaw', 'pending', 1, '2026-04-05 22:15:39', 'Testing approval', NULL, '2026-04-05 22:15:39', '2026-04-05 22:15:39'),
(11, 9, 'Bereavement Leave', '2026-04-10', '2026-04-13', 4.00, 'jaswhdawdjaw', 'pending', 1, '2026-04-05 22:25:15', 'Testing approval', NULL, '2026-04-05 22:25:15', '2026-04-05 22:25:15');

-- --------------------------------------------------------

--
-- Table structure for table `lc_overtime`
--

CREATE TABLE `lc_overtime` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `hours` decimal(5,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_part_time_hours`
--

CREATE TABLE `lc_part_time_hours` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `payroll_period_id` int(11) DEFAULT NULL,
  `hours_worked` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_part_time_hours`
--

INSERT INTO `lc_part_time_hours` (`id`, `employee_id`, `payroll_period_id`, `hours_worked`) VALUES
(3, 2, 30, 45.00),
(4, 6, 30, 40.00);

-- --------------------------------------------------------

--
-- Table structure for table `lc_payroll_periods`
--

CREATE TABLE `lc_payroll_periods` (
  `id` int(11) NOT NULL,
  `period_name` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `status` enum('open','processing','closed') DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_payroll_periods`
--

INSERT INTO `lc_payroll_periods` (`id`, `period_name`, `start_date`, `end_date`, `pay_date`, `status`) VALUES
(30, 'Jan 1-15, 2026', '2026-01-01', '2026-01-15', '2026-03-20', 'closed'),
(31, 'Jan 16-31, 2026', '2026-01-16', '2026-01-31', '2026-02-05', 'open');

-- --------------------------------------------------------

--
-- Table structure for table `lc_payroll_runs`
--

CREATE TABLE `lc_payroll_runs` (
  `id` int(11) NOT NULL,
  `payroll_period_id` int(11) DEFAULT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('draft','finalized') DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_payroll_runs`
--

INSERT INTO `lc_payroll_runs` (`id`, `payroll_period_id`, `processed_at`, `status`) VALUES
(15, 30, '2026-03-05 05:55:34', 'finalized');

-- --------------------------------------------------------

--
-- Table structure for table `lc_payslips`
--

CREATE TABLE `lc_payslips` (
  `id` int(11) NOT NULL,
  `payroll_run_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `gross_pay` decimal(10,2) DEFAULT NULL,
  `total_deductions` decimal(10,2) DEFAULT NULL,
  `net_pay` decimal(10,2) DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_payslips`
--

INSERT INTO `lc_payslips` (`id`, `payroll_run_id`, `employee_id`, `gross_pay`, `total_deductions`, `net_pay`, `generated_at`) VALUES
(57, 15, 1, 15000.00, 1750.00, 13250.00, '2026-03-05 05:55:33'),
(58, 15, 2, 22500.00, 2125.00, 20375.00, '2026-03-05 05:55:33'),
(59, 15, 3, 11000.00, 1550.00, 9450.00, '2026-03-05 05:55:33'),
(60, 15, 4, 9000.00, 1360.00, 7640.00, '2026-03-05 05:55:33'),
(61, 15, 5, 7500.00, 1150.00, 6350.00, '2026-03-05 05:55:34'),
(62, 15, 6, 22000.00, 2100.00, 19900.00, '2026-03-05 05:55:34');

-- --------------------------------------------------------

--
-- Table structure for table `lc_payslip_items`
--

CREATE TABLE `lc_payslip_items` (
  `id` int(11) NOT NULL,
  `payslip_id` int(11) DEFAULT NULL,
  `item_type` enum('earning','deduction') DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_payslip_items`
--

INSERT INTO `lc_payslip_items` (`id`, `payslip_id`, `item_type`, `description`, `amount`) VALUES
(1, 57, 'earning', 'Basic Salary', 15000.00),
(2, 57, 'deduction', 'SSS', 900.00),
(3, 57, 'deduction', 'PhilHealth', 750.00),
(4, 57, 'deduction', 'Pag-IBIG', 100.00),
(5, 58, 'earning', 'Basic Salary', 22500.00),
(6, 58, 'deduction', 'SSS', 900.00),
(7, 58, 'deduction', 'PhilHealth', 1125.00),
(8, 58, 'deduction', 'Pag-IBIG', 100.00),
(9, 59, 'earning', 'Basic Salary', 11000.00),
(10, 59, 'deduction', 'SSS', 900.00),
(11, 59, 'deduction', 'PhilHealth', 550.00),
(12, 59, 'deduction', 'Pag-IBIG', 100.00),
(13, 60, 'earning', 'Basic Salary', 9000.00),
(14, 60, 'deduction', 'SSS', 810.00),
(15, 60, 'deduction', 'PhilHealth', 450.00),
(16, 60, 'deduction', 'Pag-IBIG', 100.00),
(17, 61, 'earning', 'Basic Salary', 7500.00),
(18, 61, 'deduction', 'SSS', 675.00),
(19, 61, 'deduction', 'PhilHealth', 375.00),
(20, 61, 'deduction', 'Pag-IBIG', 100.00),
(21, 62, 'earning', 'Basic Salary', 22000.00),
(22, 62, 'deduction', 'SSS', 900.00),
(23, 62, 'deduction', 'PhilHealth', 1100.00),
(24, 62, 'deduction', 'Pag-IBIG', 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `lc_policy_acknowledgments`
--

CREATE TABLE `lc_policy_acknowledgments` (
  `id` int(11) NOT NULL,
  `policy_id` int(11) NOT NULL COMMENT 'Foreign key to policy_documents',
  `employee_id` int(11) NOT NULL COMMENT 'Foreign key to employees',
  `status` enum('read','unread','acknowledged') DEFAULT 'unread',
  `acknowledged_at` datetime DEFAULT NULL COMMENT 'Timestamp when employee acknowledged',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP address when acknowledged',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_policy_acknowledgments`
--

INSERT INTO `lc_policy_acknowledgments` (`id`, `policy_id`, `employee_id`, `status`, `acknowledged_at`, `ip_address`, `created_at`) VALUES
(1, 1, 1, 'acknowledged', '2026-03-05 10:30:00', '192.168.1.100', '2026-03-01 01:00:00'),
(2, 2, 1, 'acknowledged', '2026-03-05 10:35:00', '192.168.1.100', '2026-02-20 01:00:00'),
(3, 3, 1, 'acknowledged', '2026-03-10 14:00:00', '192.168.1.100', '2026-03-01 01:00:00'),
(4, 4, 1, 'acknowledged', '2026-03-10 14:05:00', '192.168.1.100', '2026-02-01 01:00:00'),
(5, 1, 2, 'acknowledged', '2026-03-08 09:00:00', '192.168.1.101', '2026-03-01 01:00:00'),
(6, 2, 2, 'acknowledged', '2026-03-08 09:05:00', '192.168.1.101', '2026-02-20 01:00:00'),
(7, 5, 2, 'acknowledged', '2026-03-08 09:10:00', '192.168.1.101', '2026-02-01 01:00:00'),
(8, 1, 3, 'unread', NULL, NULL, '2026-03-01 01:00:00'),
(9, 2, 3, 'unread', NULL, NULL, '2026-02-20 01:00:00'),
(10, 3, 3, 'unread', NULL, NULL, '2026-03-01 01:00:00'),
(11, 1, 4, 'acknowledged', '2026-03-15 11:00:00', '192.168.1.103', '2026-03-01 01:00:00'),
(12, 4, 4, 'acknowledged', '2026-03-15 11:05:00', '192.168.1.103', '2026-02-01 01:00:00'),
(13, 6, 4, 'acknowledged', '2026-03-15 11:10:00', '192.168.1.103', '2026-02-10 01:00:00'),
(14, 1, 5, 'unread', NULL, NULL, '2026-03-01 01:00:00'),
(15, 2, 5, 'unread', NULL, NULL, '2026-02-20 01:00:00'),
(16, 6, 5, 'acknowledged', '2026-03-20 10:00:00', '192.168.1.105', '2026-02-10 01:00:00'),
(17, 1, 6, 'acknowledged', '2026-03-18 15:00:00', '192.168.1.106', '2026-03-01 01:00:00'),
(18, 5, 6, 'acknowledged', '2026-03-18 15:05:00', '192.168.1.106', '2026-02-01 01:00:00'),
(19, 8, 6, 'acknowledged', '2026-03-18 15:10:00', '192.168.1.106', '2026-02-15 01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `lc_policy_audit_log`
--

CREATE TABLE `lc_policy_audit_log` (
  `id` int(11) NOT NULL,
  `policy_id` int(11) NOT NULL COMMENT 'Foreign key to policy_documents',
  `action` varchar(50) NOT NULL COMMENT 'Action type: created, updated, deleted, version_added, acknowledged',
  `description` text DEFAULT NULL COMMENT 'Description of the action',
  `old_values` text DEFAULT NULL COMMENT 'JSON of previous values',
  `new_values` text DEFAULT NULL COMMENT 'JSON of new values',
  `user_id` int(11) DEFAULT NULL COMMENT 'User who performed the action',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP address of user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_policy_audit_log`
--

INSERT INTO `lc_policy_audit_log` (`id`, `policy_id`, `action`, `description`, `old_values`, `new_values`, `user_id`, `ip_address`, `created_at`) VALUES
(1, 1, 'created', 'Policy created', NULL, '{\"title\":\"Employee Handbook\",\"category\":\"HR Policies\"}', 1, '192.168.1.100', '2025-01-15 01:00:00'),
(2, 1, 'version_added', 'Version 2 added', '{\"version\":1}', '{\"version\":2,\"change_notes\":\"Added new leave benefits section\"}', 1, '192.168.1.100', '2025-08-20 01:00:00'),
(3, 1, 'version_added', 'Version 3 added', '{\"version\":2}', '{\"version\":3,\"change_notes\":\"Updated benefits and added remote work policy\"}', 1, '192.168.1.100', '2026-03-01 06:30:00'),
(4, 2, 'created', 'Policy created', NULL, '{\"title\":\"Code of Conduct\",\"category\":\"Code of Conduct\"}', 1, '192.168.1.100', '2025-06-01 01:00:00'),
(5, 2, 'version_added', 'Version 2 added', '{\"version\":1}', '{\"version\":2,\"change_notes\":\"Updated dress code and attendance policy\"}', 1, '192.168.1.100', '2026-02-15 02:00:00'),
(6, 3, 'created', 'Policy created', NULL, '{\"title\":\"Data Privacy Policy\",\"category\":\"Legal Policies\"}', 1, '192.168.1.100', '2025-09-01 01:00:00'),
(7, 4, 'created', 'Policy created', NULL, '{\"title\":\"Health and Safety Guidelines\",\"category\":\"Compliance Guidelines\"}', 1, '192.168.1.100', '2025-03-01 01:00:00'),
(8, 4, 'version_added', 'Version 2 added', '{\"version\":1}', '{\"version\":2,\"change_notes\":\"Added pandemic protocols\"}', 1, '192.168.1.100', '2026-01-20 03:00:00'),
(9, 1, 'acknowledged', 'Policy acknowledged by employee', NULL, '{\"employee_id\":1}', 1, '192.168.1.100', '2026-03-05 02:30:00'),
(10, 2, 'acknowledged', 'Policy acknowledged by employee', NULL, '{\"employee_id\":1}', 1, '192.168.1.100', '2026-03-05 02:35:00'),
(11, 5, 'created', 'Policy created', NULL, '{\"title\":\"Anti-Sexual Harassment Policy\",\"category\":\"Legal Policies\"}', 1, '192.168.1.100', '2025-01-01 01:00:00'),
(12, 6, 'created', 'Policy created', NULL, '{\"title\":\"Leave Policy\",\"category\":\"HR Policies\"}', 1, '192.168.1.100', '2024-12-01 01:00:00'),
(13, 6, 'version_added', 'Version 2 added', '{\"version\":1}', '{\"version\":2,\"change_notes\":\"Updated maternity leave to 105 days per RA 11210\"}', 1, '192.168.1.100', '2026-02-01 01:00:00'),
(14, 7, 'created', 'Policy created', NULL, '{\"title\":\"IT Security Policy\",\"category\":\"Compliance Guidelines\"}', 2, '192.168.1.102', '2025-08-15 01:00:00'),
(15, 8, 'created', 'Policy created', NULL, '{\"title\":\"Grievance Procedure\",\"category\":\"Legal Policies\"}', 1, '192.168.1.100', '2025-04-01 01:00:00'),
(16, 1, 'updated', 'Policy updated', '{\"id\":1,\"title\":\"Employee Handbook\",\"description\":\"Comprehensive guide to company policies, procedures, and employee benefits\",\"category\":\"HR Policies\",\"department_owner\":\"Human Resources\",\"file_path\":null,\"file_name\":\"employee_handbook_2026.pdf\",\"file_type\":null,\"version_number\":3,\"is_active\":1,\"requires_acknowledgment\":1,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-01-15 09:00:00\",\"updated_at\":\"2026-03-01 14:30:00\"}', '{\"action\":\"update\",\"id\":\"1\",\"title\":\"Employee Handbook\",\"category\":\"HR Policies\",\"department_owner\":\"Human Resources\",\"description\":\"Comprehensive guide to company policies, procedures, and employee benefits\",\"change_notes\":\"sss\",\"requires_acknowledgment\":\"1\"}', 999, '::1', '2026-03-25 16:37:54'),
(17, 1, 'updated', 'Policy updated', '{\"id\":1,\"title\":\"Employee Handbook\",\"description\":\"Comprehensive guide to company policies, procedures, and employee benefits\",\"category\":\"HR Policies\",\"department_owner\":\"Human Resources\",\"file_path\":null,\"file_name\":\"employee_handbook_2026.pdf\",\"file_type\":\"\",\"version_number\":4,\"is_active\":1,\"requires_acknowledgment\":1,\"created_by\":1,\"updated_by\":999,\"created_at\":\"2026-01-15 09:00:00\",\"updated_at\":\"2026-03-26 00:37:54\"}', '{\"action\":\"update\",\"id\":\"1\",\"title\":\"Employee Handbooks\",\"category\":\"HR Policies\",\"department_owner\":\"Human Resources\",\"description\":\"Comprehensive guide to company policies, procedures, and employee benefits\",\"change_notes\":\"\",\"requires_acknowledgment\":\"1\"}', 999, '::1', '2026-03-25 17:53:35'),
(18, 8, 'updated', 'Policy updated', '{\"id\":8,\"title\":\"Grievance Procedure\",\"description\":\"Procedures for filing and resolving workplace complaints\",\"category\":\"Legal Policies\",\"department_owner\":\"Human Resources\",\"file_path\":null,\"file_name\":\"grievance_procedure.pdf\",\"file_type\":null,\"version_number\":1,\"is_active\":1,\"requires_acknowledgment\":1,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2025-04-01 09:00:00\",\"updated_at\":\"2025-04-01 09:00:00\"}', '{\"action\":\"update\",\"id\":\"8\",\"title\":\"Grievance Procedure\",\"category\":\"Code of Conduct\",\"department_owner\":\"Human Resources\",\"description\":\"Procedures for filing and resolving workplace complaints\",\"change_notes\":\"\",\"requires_acknowledgment\":\"1\"}', 999, '::1', '2026-03-25 17:53:57'),
(19, 9, 'created', 'Policy created', NULL, '{\"action\":\"add\",\"title\":\"ascbjhmngbvcx\",\"category\":\"Legal Policies\",\"department_owner\":\"afsdfgkhjgfd\",\"description\":\"afsgdfhkjhgfds\",\"requires_acknowledgment\":\"1\"}', 999, '::1', '2026-04-05 07:01:53'),
(20, 9, 'updated', 'Policy updated', '{\"id\":9,\"title\":\"ascbjhmngbvcx\",\"description\":\"afsgdfhkjh\",\"category\":\"Legal Policies\",\"department_owner\":\"afsdfgkhj\",\"file_path\":\"uploads\\/policies\\/1775372513_resume_JOB0002_1774669407_cheska.pdf\",\"file_name\":\"resume_JOB0002_1774669407_cheska.pdf\",\"file_type\":\"\",\"version_number\":2,\"is_active\":1,\"requires_acknowledgment\":1,\"created_by\":999,\"updated_by\":999,\"created_at\":\"2026-04-05 15:01:53\",\"updated_at\":\"2026-04-05 15:03:54\"}', '{\"action\":\"update\",\"id\":\"9\",\"title\":\"ascbjhmngbvcx\",\"category\":\"Legal Policies\",\"department_owner\":\"afsdfgkhj12\",\"description\":\"afsgdfhkjh21\",\"change_notes\":\"\",\"requires_acknowledgment\":\"1\"}', 999, '::1', '2026-04-05 07:04:15'),
(21, 9, 'updated', 'Policy updated', '{\"id\":9,\"title\":\"ascbjhmngbvcx\",\"description\":\"afsgdfhkjh21\",\"category\":\"Legal Policies\",\"department_owner\":\"afsdfgkhj12\",\"file_path\":\"uploads\\/policies\\/1775372513_resume_JOB0002_1774669407_cheska.pdf\",\"file_name\":\"resume_JOB0002_1774669407_cheska.pdf\",\"file_type\":\"\",\"version_number\":3,\"is_active\":1,\"requires_acknowledgment\":1,\"created_by\":999,\"updated_by\":999,\"created_at\":\"2026-04-05 15:01:53\",\"updated_at\":\"2026-04-05 15:04:15\"}', '{\"action\":\"update\",\"id\":\"9\",\"title\":\"ascbjhmngbvcx\",\"category\":\"Legal Policies\",\"department_owner\":\"afsdfgkhkjhgfds\",\"description\":\"afsgdfhkjh21\",\"change_notes\":\"ilkjhgnbfdv\",\"requires_acknowledgment\":\"1\"}', 999, '::1', '2026-04-05 07:04:48'),
(22, 9, 'deleted', 'Policy deleted', NULL, '{\"is_active\":0}', 999, '::1', '2026-04-05 07:05:59');

-- --------------------------------------------------------

--
-- Table structure for table `lc_policy_documents`
--

CREATE TABLE `lc_policy_documents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'Policy title',
  `description` text DEFAULT NULL COMMENT 'Policy description',
  `category` enum('HR Policies','Code of Conduct','Legal Policies','Compliance Guidelines') NOT NULL DEFAULT 'HR Policies',
  `department_owner` varchar(100) DEFAULT NULL COMMENT 'Department responsible for this policy',
  `file_path` varchar(500) DEFAULT NULL COMMENT 'Path to uploaded file (PDF/DOC)',
  `file_name` varchar(255) DEFAULT NULL COMMENT 'Original file name',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'MIME type of uploaded file',
  `version_number` int(11) DEFAULT 1 COMMENT 'Current version number',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Whether policy is active',
  `requires_acknowledgment` tinyint(1) DEFAULT 1 COMMENT 'Whether employees must acknowledge',
  `created_by` int(11) DEFAULT NULL COMMENT 'User who created the policy',
  `updated_by` int(11) DEFAULT NULL COMMENT 'User who last updated the policy',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_policy_documents`
--

INSERT INTO `lc_policy_documents` (`id`, `title`, `description`, `category`, `department_owner`, `file_path`, `file_name`, `file_type`, `version_number`, `is_active`, `requires_acknowledgment`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Employee Handbooks', 'Comprehensive guide to company policies, procedures, and employee benefits', 'HR Policies', 'Human Resources', 'uploads/policies/v3_employee_handbook.pdf', 'employee_handbook_2026.pdf', '', 5, 1, 1, 1, 999, '2026-01-15 01:00:00', '2026-04-05 10:06:10'),
(2, 'Code of Conduct', 'Standards of professional behavior and ethical guidelines for all employees', 'Code of Conduct', 'Human Resources', 'uploads/policies/v2_code_of_conduct.pdf', 'code_of_conduct.pdf', NULL, 2, 1, 1, 1, 1, '2025-06-01 01:00:00', '2026-04-05 10:05:33'),
(3, 'Data Privacy Policy', 'Guidelines for handling and protecting personal data in compliance with RA 10173', 'Legal Policies', 'IT Department', 'uploads/policies/v1_data_privacy.pdf', 'data_privacy_policy.pdf', NULL, 1, 1, 1, 1, 1, '2025-09-01 01:00:00', '2026-04-05 10:05:33'),
(4, 'Health and Safety Guidelines', 'Workplace safety protocols and emergency procedures', 'Compliance Guidelines', 'Operations', 'uploads/policies/v2_health_safety.pdf', 'health_safety_guidelines.pdf', NULL, 2, 1, 1, 1, 1, '2025-03-01 01:00:00', '2026-04-05 10:05:33'),
(5, 'Anti-Sexual Harassment Policy', 'Zero-tolerance policy for sexual harassment in the workplace per RA 7877', 'Legal Policies', 'Human Resources', 'uploads/policies/v1_anti_harassment.pdf', 'anti_harassment_policy.pdf', NULL, 1, 1, 1, 1, 1, '2025-01-01 01:00:00', '2026-04-05 10:05:33'),
(6, 'Leave Policy', 'Guidelines for vacation, sick leave, and other leave benefits', 'HR Policies', 'Human Resources', 'uploads/policies/v2_leave_policy.pdf', 'leave_policy.pdf', NULL, 2, 1, 1, 1, 1, '2024-12-01 01:00:00', '2026-04-05 10:05:33'),
(7, 'IT Security Policy', 'Guidelines for computer and network security, password management, and data protection', 'Compliance Guidelines', 'IT Department', 'uploads/policies/v1_it_security.pdf', 'it_security_policy.pdf', NULL, 1, 1, 0, 2, 2, '2025-08-15 01:00:00', '2026-04-05 10:05:33'),
(8, 'Grievance Procedure', 'Procedures for filing and resolving workplace complaints', 'Code of Conduct', 'Human Resources', NULL, 'grievance_procedure.pdf', '', 2, 1, 1, 1, 999, '2025-04-01 01:00:00', '2026-03-25 17:53:57'),
(9, 'ascbjhmngbvcx', 'afsgdfhkjh21', 'Legal Policies', 'afsdfgkhkjhgfds', 'uploads/policies/1775372513_resume_JOB0002_1774669407_cheska.pdf', 'resume_JOB0002_1774669407_cheska.pdf', '', 4, 0, 1, 999, 999, '2026-04-05 07:01:53', '2026-04-05 07:05:59');

-- --------------------------------------------------------

--
-- Table structure for table `lc_policy_notifications`
--

CREATE TABLE `lc_policy_notifications` (
  `id` int(11) NOT NULL,
  `policy_id` int(11) NOT NULL COMMENT 'Foreign key to policy_documents',
  `notification_type` enum('new_policy','policy_updated','acknowledgment_reminder') NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'Notification title',
  `message` text NOT NULL COMMENT 'Notification message',
  `recipient_id` int(11) DEFAULT NULL COMMENT 'Specific recipient (null = all employees)',
  `is_read` tinyint(1) DEFAULT 0 COMMENT 'Whether notification has been read',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_policy_notifications`
--

INSERT INTO `lc_policy_notifications` (`id`, `policy_id`, `notification_type`, `title`, `message`, `recipient_id`, `is_read`, `created_at`) VALUES
(1, 1, 'new_policy', 'New Employee Handbook Available', 'A new Employee Handbook (Version 3) has been published. Please review and acknowledge.', NULL, 0, '2026-03-01 06:30:00'),
(2, 2, 'policy_updated', 'Code of Conduct Updated', 'The Code of Conduct has been updated to version 2 with changes to dress code and attendance policy.', NULL, 1, '2026-02-15 02:00:00'),
(3, 6, 'policy_updated', 'Leave Policy Updated', 'The Leave Policy has been updated. Maternity leave has been increased to 105 days per RA 11210.', NULL, 1, '2026-02-01 01:00:00'),
(4, 3, 'new_policy', 'New Data Privacy Policy', 'A new Data Privacy Policy has been published in compliance with RA 10173.', NULL, 1, '2025-09-01 01:00:00'),
(5, 1, 'acknowledgment_reminder', 'Acknowledgment Required', 'Please acknowledge that you have read the Employee Handbook.', 3, 0, '2026-03-20 01:00:00'),
(6, 2, 'acknowledgment_reminder', 'Acknowledgment Required', 'Please acknowledge that you have read the Code of Conduct.', 3, 0, '2026-03-20 01:00:00'),
(7, 3, 'acknowledgment_reminder', 'Acknowledgment Required', 'Please acknowledge that you have read the Data Privacy Policy.', 3, 0, '2026-03-20 01:00:00'),
(8, 1, 'acknowledgment_reminder', 'Acknowledgment Required', 'Please acknowledge that you have read the Employee Handbook.', 5, 0, '2026-03-20 01:00:00'),
(9, 2, 'acknowledgment_reminder', 'Acknowledgment Required', 'Please acknowledge that you have read the Code of Conduct.', 5, 0, '2026-03-20 01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `lc_policy_reminders`
--

CREATE TABLE `lc_policy_reminders` (
  `id` int(11) NOT NULL,
  `acknowledgment_id` int(11) NOT NULL,
  `sent_by` int(11) DEFAULT NULL COMMENT 'Foreign key to employees - who sent the reminder',
  `message` text DEFAULT NULL COMMENT 'Custom reminder message',
  `sent_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_policy_versions`
--

CREATE TABLE `lc_policy_versions` (
  `id` int(11) NOT NULL,
  `policy_id` int(11) NOT NULL COMMENT 'Foreign key to policy_documents',
  `version_number` int(11) NOT NULL COMMENT 'Version number (v1, v2, v3...)',
  `file_path` varchar(500) DEFAULT NULL COMMENT 'Path to versioned file',
  `file_name` varchar(255) DEFAULT NULL COMMENT 'Original file name for this version',
  `change_notes` text DEFAULT NULL COMMENT 'Description of changes in this version',
  `is_current` tinyint(1) DEFAULT 0 COMMENT 'Whether this is the current version',
  `created_by` int(11) DEFAULT NULL COMMENT 'User who created this version',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_policy_versions`
--

INSERT INTO `lc_policy_versions` (`id`, `policy_id`, `version_number`, `file_path`, `file_name`, `change_notes`, `is_current`, `created_by`, `created_at`) VALUES
(1, 1, 1, 'uploads/policies/v1_employee_handbook.pdf', 'employee_handbook_2025.pdf', 'Initial version', 0, 1, '2025-01-15 01:00:00'),
(2, 1, 2, 'uploads/policies/v2_employee_handbook.pdf', 'employee_handbook_2025v2.pdf', 'Added new leave benefits section', 0, 1, '2025-08-20 01:00:00'),
(3, 1, 3, 'uploads/policies/v3_employee_handbook.pdf', 'employee_handbook_2026.pdf', 'Updated benefits and added remote work policy', 0, 1, '2026-03-01 06:30:00'),
(4, 2, 1, 'uploads/policies/v1_code_of_conduct.pdf', 'code_of_conduct_2024.pdf', 'Initial version', 0, 1, '2025-06-01 01:00:00'),
(5, 2, 2, 'uploads/policies/v2_code_of_conduct.pdf', 'code_of_conduct.pdf', 'Updated dress code and attendance policy', 1, 1, '2026-02-15 02:00:00'),
(6, 3, 1, 'uploads/policies/v1_data_privacy.pdf', 'data_privacy_policy.pdf', 'Initial version', 1, 1, '2025-09-01 01:00:00'),
(7, 4, 1, 'uploads/policies/v1_health_safety.pdf', 'health_safety_v1.pdf', 'Initial version', 0, 1, '2025-03-01 01:00:00'),
(8, 4, 2, 'uploads/policies/v2_health_safety.pdf', 'health_safety_guidelines.pdf', 'Added pandemic protocols', 1, 1, '2026-01-20 03:00:00'),
(9, 5, 1, 'uploads/policies/v1_anti_harassment.pdf', 'anti_harassment_policy.pdf', 'Initial version', 1, 1, '2025-01-01 01:00:00'),
(10, 6, 1, 'uploads/policies/v1_leave_policy.pdf', 'leave_policy_2024.pdf', 'Initial version', 0, 1, '2024-12-01 01:00:00'),
(11, 6, 2, 'uploads/policies/v2_leave_policy.pdf', 'leave_policy.pdf', 'Updated maternity leave to 105 days per RA 11210', 1, 1, '2026-02-01 01:00:00'),
(12, 7, 1, 'uploads/policies/v1_it_security.pdf', 'it_security_policy.pdf', 'Initial version', 1, 2, '2025-08-15 01:00:00'),
(13, 8, 1, 'uploads/policies/v1_grievance.pdf', 'grievance_procedure.pdf', 'Initial version', 0, 1, '2025-04-01 01:00:00'),
(14, 1, 4, 'uploads/policies/v3_employee_handbook.pdf', 'employee_handbook_2026.pdf', 'sss', 0, 999, '2026-03-25 16:37:54'),
(15, 1, 5, 'uploads/policies/v3_employee_handbook.pdf', 'employee_handbook_2026.pdf', '', 1, 999, '2026-03-25 17:53:35'),
(16, 8, 2, NULL, 'grievance_procedure.pdf', '', 1, 999, '2026-03-25 17:53:57'),
(17, 9, 1, 'uploads/policies/1775372513_resume_JOB0002_1774669407_cheska.pdf', 'resume_JOB0002_1774669407_cheska.pdf', 'Initial version', 0, 999, '2026-04-05 07:01:53'),
(18, 9, 3, 'uploads/policies/1775372513_resume_JOB0002_1774669407_cheska.pdf', 'resume_JOB0002_1774669407_cheska.pdf', '', 0, 999, '2026-04-05 07:04:15'),
(19, 9, 4, 'uploads/policies/1775372513_resume_JOB0002_1774669407_cheska.pdf', 'resume_JOB0002_1774669407_cheska.pdf', 'ilkjhgnbfdv', 1, 999, '2026-04-05 07:04:48');

-- --------------------------------------------------------

--
-- Table structure for table `lc_positions`
--

CREATE TABLE `lc_positions` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_positions`
--

INSERT INTO `lc_positions` (`id`, `title`, `category_id`) VALUES
(1, 'Instructor', 1),
(2, 'Professor', 1),
(3, 'Registrar', 2),
(4, 'Cashier', 2),
(5, 'Janitor', 2);

-- --------------------------------------------------------

--
-- Table structure for table `lc_report_notes`
--

CREATE TABLE `lc_report_notes` (
  `id` int(11) NOT NULL,
  `payroll_period_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_salary_structures`
--

CREATE TABLE `lc_salary_structures` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_salary_structures`
--

INSERT INTO `lc_salary_structures` (`id`, `name`, `basic_salary`) VALUES
(1, 'Instructor Level 1', 30000.00),
(2, 'Professor Level 2', 40000.00),
(3, 'Registrar Level 1', 22000.00),
(4, 'Cashier Contractual', 18000.00),
(5, 'Janitor Level 1', 15000.00);

-- --------------------------------------------------------

--
-- Table structure for table `lc_shifts`
--

CREATE TABLE `lc_shifts` (
  `id` int(11) NOT NULL,
  `shift_name` varchar(50) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_statutory_contributions`
--

CREATE TABLE `lc_statutory_contributions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `employee_share` decimal(10,2) DEFAULT NULL,
  `employer_share` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_statutory_contributions`
--

INSERT INTO `lc_statutory_contributions` (`id`, `name`, `employee_share`, `employer_share`) VALUES
(1, 'SSS', 1125.00, 2000.00),
(2, 'PhilHealth', 450.00, 900.00),
(3, 'Pag-IBIG', 200.00, 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `lc_tax_tables`
--

CREATE TABLE `lc_tax_tables` (
  `id` int(11) NOT NULL,
  `min_income` decimal(10,2) DEFAULT NULL,
  `max_income` decimal(10,2) DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `fixed_tax` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_users`
--

CREATE TABLE `lc_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `theme` enum('light','dark') DEFAULT 'light',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lc_users`
--

INSERT INTO `lc_users` (`id`, `username`, `password`, `full_name`, `role`, `theme`, `created_at`) VALUES
(1, 'admin', '$2y$10$GF34eDR6uEqpxNIovwKmRu2A6u3ALXgmMkn8zBdoREYLb1Em0euAK', 'Administrator', 'admin', 'light', '2026-01-28 15:21:13');

-- --------------------------------------------------------

--
-- Table structure for table `ld_archive`
--

CREATE TABLE `ld_archive` (
  `id` int(11) NOT NULL,
  `archive_type` enum('course','program') NOT NULL,
  `original_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `original_created_by` int(11) DEFAULT NULL,
  `archived_by` int(11) NOT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archive_reason` varchar(255) DEFAULT NULL,
  `archive_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`archive_data`)),
  `restored` tinyint(1) DEFAULT 0,
  `restored_by` int(11) DEFAULT NULL,
  `restored_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ld_certification`
--

CREATE TABLE `ld_certification` (
  `ld_certification_id` int(11) NOT NULL,
  `employee_user_id` int(11) NOT NULL,
  `ld_courses_id` int(11) NOT NULL,
  `issued_by_user_id` int(11) NOT NULL,
  `issued_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('active','expired','revoked') DEFAULT 'active',
  `certificate_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ld_courses`
--

CREATE TABLE `ld_courses` (
  `ld_courses_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `instructor` varchar(255) DEFAULT NULL,
  `duration_hours` int(11) DEFAULT NULL,
  `ld_training_programs_id` int(11) DEFAULT NULL,
  `content_type` enum('online','in-person','hybrid') DEFAULT 'online',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ld_courses`
--

INSERT INTO `ld_courses` (`ld_courses_id`, `title`, `description`, `instructor`, `duration_hours`, `ld_training_programs_id`, `content_type`, `status`, `created_by_user_id`, `created_at`) VALUES
(1, 'Strategic Leadership', 'Learn strategic thinking and long-term planning skills', 'Dr. Sarah Johnson', 8, 4, 'in-person', 'active', 7, '2026-04-05 18:50:43'),
(2, 'Team Dynamics & Management', 'Understanding team behavior and effective management techniques', 'Dr. Sarah Johnson', 6, 4, 'online', 'active', 7, '2026-04-05 18:50:43'),
(3, 'SEO Fundamentals', 'Search engine optimization basics and advanced techniques', 'Mike Chen', 6, 5, 'online', 'active', 7, '2026-04-05 18:50:43'),
(4, 'Social Media Marketing', 'Building brand presence on social platforms', 'Mike Chen', 8, 5, 'online', 'active', 7, '2026-04-05 18:50:43'),
(5, 'Data Protection Basics', 'Understanding data privacy and protection laws', 'Lisa Rodriguez', 4, 6, 'online', 'active', 7, '2026-04-05 18:50:43'),
(6, 'Python for Data Analysis', 'Python programming fundamentals for data work', 'Dr. Emily Wang', 10, 8, 'online', 'active', 7, '2026-04-05 18:50:43'),
(7, 'Data Visualization', 'Creating effective data visualizations', 'Dr. Emily Wang', 6, 8, 'hybrid', 'active', 7, '2026-04-05 18:50:43'),
(8, 'Communication Skills', 'Effective verbal and written communication', 'Jennifer Martinez', 4, 9, 'online', 'active', 7, '2026-04-05 18:50:44'),
(9, 'Strategic Leadership', 'Learn strategic thinking and long-term planning skills', 'Dr. Sarah Johnson', 8, 4, 'in-person', 'active', 7, '2026-04-05 18:51:01'),
(10, 'Team Dynamics & Management', 'Understanding team behavior and effective management techniques', 'Dr. Sarah Johnson', 6, 4, 'online', 'active', 7, '2026-04-05 18:51:01'),
(11, 'SEO Fundamentals', 'Search engine optimization basics and advanced techniques', 'Mike Chen', 6, 5, 'online', 'active', 7, '2026-04-05 18:51:01'),
(12, 'Social Media Marketing', 'Building brand presence on social platforms', 'Mike Chen', 8, 5, 'online', 'active', 7, '2026-04-05 18:51:01'),
(13, 'Data Protection Basics', 'Understanding data privacy and protection laws', 'Lisa Rodriguez', 4, 6, 'online', 'active', 7, '2026-04-05 18:51:01'),
(14, 'Python for Data Analysis', 'Python programming fundamentals for data work', 'Dr. Emily Wang', 10, 8, 'online', 'active', 7, '2026-04-05 18:51:01'),
(15, 'Data Visualization', 'Creating effective data visualizations', 'Dr. Emily Wang', 6, 8, 'hybrid', 'active', 7, '2026-04-05 18:51:01'),
(16, 'Communication Skills', 'Effective verbal and written communication', 'Jennifer Martinez', 4, 9, 'online', 'active', 7, '2026-04-05 18:51:01');

-- --------------------------------------------------------

--
-- Table structure for table `ld_enrollments`
--

CREATE TABLE `ld_enrollments` (
  `ld_enrollment_id` int(11) NOT NULL,
  `employee_user_id` int(11) NOT NULL,
  `ld_courses_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('enrolled','in-progress','completed','cancelled') DEFAULT 'enrolled',
  `completion_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ld_training_programs`
--

CREATE TABLE `ld_training_programs` (
  `ld_training_programs_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `trainer` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `created_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ld_training_programs`
--

INSERT INTO `ld_training_programs` (`ld_training_programs_id`, `title`, `description`, `trainer`, `start_date`, `end_date`, `max_participants`, `status`, `created_by_user_id`, `created_at`) VALUES
(1, 'Advanced Leadership Development', 'Comprehensive leadership training for senior managers covering strategic thinking, team management, and organizational change.', 'Dr. Sarah Johnson', '2026-05-01', '2026-05-15', 25, 'active', 7, '2026-04-05 18:50:43'),
(2, 'Digital Marketing Mastery', 'Complete digital marketing course covering SEO, social media marketing, content strategy, and analytics.', 'Mike Chen', '2026-04-15', '2026-04-30', 30, 'active', 7, '2026-04-05 18:50:43'),
(3, 'Cybersecurity Fundamentals', 'Essential cybersecurity training for all employees covering data protection, phishing awareness, and security best practices.', 'Lisa Rodriguez', '2026-06-01', '2026-06-10', 50, 'active', 7, '2026-04-05 18:50:43'),
(4, 'Project Management Professional', 'PMP certification preparation course covering project lifecycle, risk management, and stakeholder communication.', 'David Thompson', '2026-07-01', '2026-07-20', 20, 'active', 7, '2026-04-05 18:50:43'),
(5, 'Data Analytics with Python', 'Hands-on data analytics training using Python, pandas, and visualization tools.', 'Dr. Emily Wang', '2026-05-20', '2026-06-05', 35, 'active', 7, '2026-04-05 18:50:43'),
(6, 'Advanced Leadership Development', 'Comprehensive leadership training for senior managers covering strategic thinking, team management, and organizational change.', 'Dr. Sarah Johnson', '2026-05-01', '2026-05-15', 25, 'active', 7, '2026-04-05 18:51:00'),
(7, 'Digital Marketing Mastery', 'Complete digital marketing course covering SEO, social media marketing, content strategy, and analytics.', 'Mike Chen', '2026-04-15', '2026-04-30', 30, 'active', 7, '2026-04-05 18:51:00'),
(8, 'Cybersecurity Fundamentals', 'Essential cybersecurity training for all employees covering data protection, phishing awareness, and security best practices.', 'Lisa Rodriguez', '2026-06-01', '2026-06-10', 50, 'active', 7, '2026-04-05 18:51:01'),
(9, 'Project Management Professional', 'PMP certification preparation course covering project lifecycle, risk management, and stakeholder communication.', 'David Thompson', '2026-07-01', '2026-07-20', 20, 'active', 7, '2026-04-05 18:51:01'),
(10, 'Data Analytics with Python', 'Hands-on data analytics training using Python, pandas, and visualization tools.', 'Dr. Emily Wang', '2026-05-20', '2026-06-05', 35, 'active', 7, '2026-04-05 18:51:01'),
(11, 'gfhgfhfg', 'fghfghfg', 'fghfghf', '2026-04-07', '2026-04-14', 44, 'active', 7, '2026-04-06 06:33:13');

-- --------------------------------------------------------

--
-- Table structure for table `ld_training_requests`
--

CREATE TABLE `ld_training_requests` (
  `ld_request_id` int(11) NOT NULL,
  `performance_request_id` int(11) DEFAULT NULL,
  `employee_user_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `goal_id` int(11) DEFAULT NULL,
  `kpi_name` varchar(255) DEFAULT NULL,
  `request_reason` text NOT NULL,
  `requested_program` varchar(255) DEFAULT NULL,
  `requested_course` varchar(255) DEFAULT NULL,
  `ld_training_program_id` int(11) DEFAULT NULL,
  `ld_course_id` int(11) DEFAULT NULL,
  `request_status` enum('New','Received','Approved','Rejected','Completed') NOT NULL DEFAULT 'New',
  `received_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ld_training_requests`
--

INSERT INTO `ld_training_requests` (`ld_request_id`, `performance_request_id`, `employee_user_id`, `employee_id`, `goal_id`, `kpi_name`, `request_reason`, `requested_program`, `requested_course`, `ld_training_program_id`, `ld_course_id`, `request_status`, `received_at`, `processed_at`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 7, NULL, NULL, 'Need leadership skills for upcoming promotion', 'Advanced Leadership Development', 'Strategic Leadership', NULL, NULL, 'New', '2026-03-31 16:00:00', NULL, '2026-04-05 18:51:01', '2026-04-05 18:51:01'),
(2, NULL, 2, 7, NULL, NULL, 'Required for marketing department role', 'Digital Marketing Mastery', 'SEO Fundamentals', NULL, NULL, 'Received', '2026-04-01 16:00:00', NULL, '2026-04-05 18:51:01', '2026-04-05 18:51:01'),
(3, NULL, 3, 7, NULL, NULL, 'Mandatory security training for all staff', 'Cybersecurity Fundamentals', 'Data Protection Basics', NULL, NULL, 'Approved', '2026-04-02 16:00:00', NULL, '2026-04-05 18:51:01', '2026-04-05 18:51:01'),
(4, 1, 25, 12, NULL, 'dasda', 'aaa', 'asdas', 'asdasdas', 1, 8, 'New', '2026-04-05 19:02:06', NULL, '2026-04-05 19:02:06', '2026-04-05 19:02:06'),
(5, 2, 25, 12, NULL, 'dasda', 'aaa', 'asdas', 'asdasdas', 1, 8, 'Received', '2026-04-05 19:02:07', '2026-04-06 03:03:05', '2026-04-05 19:02:07', '2026-04-05 19:03:05'),
(6, 3, 25, 12, NULL, 'dasda', 'aaa', 'asdas', 'asdasdas', 1, 8, 'New', '2026-04-05 19:04:25', NULL, '2026-04-05 19:04:25', '2026-04-05 19:04:25'),
(7, 4, 15, 2, NULL, 'dfdsf', 'dsdsfs', 'fdsfds', 'fdsf', 2, 12, 'New', '2026-04-05 19:10:39', NULL, '2026-04-05 19:10:39', '2026-04-05 19:10:39'),
(8, 5, 27, 14, NULL, 'hjhkh7777', 'lkjlkjlk', 'hkjhkjhkhkhhjhkhkk7777', 'kjhkjkjhkjhkhkkh8888', 2, 4, 'New', '2026-04-05 19:17:23', NULL, '2026-04-05 19:17:23', '2026-04-05 19:17:23'),
(9, 6, 26, 13, NULL, 'asdasdas', 'asdasdasdasdasdas', 'dasdasd', 'asdas', 7, 11, 'New', '2026-04-05 20:00:29', NULL, '2026-04-05 20:00:29', '2026-04-05 20:00:29'),
(10, 7, 4, 0, NULL, 'asdasd', 'asdasdasdaads', 'asdasd', 'dasda', 7, 12, 'Received', '2026-04-05 20:07:03', '2026-04-06 14:35:22', '2026-04-05 20:07:03', '2026-04-06 06:35:22'),
(11, 6, 26, 13, NULL, 'asdasdas', 'asdasdasdasdasdas', 'dasdasd', 'asdas', 7, 11, 'New', '2026-04-05 20:00:29', NULL, '2026-04-05 20:00:29', '2026-04-05 20:00:29');

-- --------------------------------------------------------

--
-- Table structure for table `leave_documents`
--

CREATE TABLE `leave_documents` (
  `id` int(11) NOT NULL,
  `leave_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_documents`
--

INSERT INTO `leave_documents` (`id`, `leave_id`, `document_type`, `file_path`, `uploaded_at`) VALUES
(1, 3, 'Medical Certificate', 'uploads/leave_documents/sick_medical_cert_3.pdf', '2026-04-05 06:40:06'),
(2, 5, 'Death Certificate', 'uploads/leave_documents/bereavement_death_cert_5.pdf', '2026-04-05 06:40:06'),
(3, 7, 'Medical Certificate', 'uploads/leave_documents/sick_medical_cert_7.pdf', '2026-04-05 06:40:06');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` decimal(5,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `checked_by` int(11) DEFAULT NULL,
  `checked_at` timestamp NULL DEFAULT NULL,
  `hr_comments` text DEFAULT NULL,
  `checklist_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `total_days`, `reason`, `status`, `checked_by`, `checked_at`, `hr_comments`, `checklist_data`, `created_at`, `updated_at`) VALUES
(1, 1, 'Maternity Leave', '2026-04-01', '2026-07-15', 105.00, 'Pregnancy with expected delivery date July 2026 - Required for maternity coverage under RA 11210', 'pending', NULL, NULL, NULL, NULL, '2026-04-05 06:40:06', '2026-04-05 06:40:06'),
(2, 2, 'Paternity Leave', '2026-03-20', '2026-03-26', 7.00, 'Wife Maria Santos scheduled for normal delivery on March 22, 2026', 'pending', NULL, NULL, NULL, NULL, '2026-04-05 06:40:06', '2026-04-05 06:40:06'),
(3, 3, 'Sick Leave', '2026-03-10', '2026-03-11', 2.00, 'Medical certificate attached - Upper respiratory infection', 'approved', NULL, '2026-04-05 22:38:24', '', NULL, '2026-04-05 06:40:06', '2026-04-05 22:38:24'),
(4, 4, 'Vacation Leave', '2026-04-10', '2026-04-15', 6.00, 'Family vacation to Palawan - filed 2 weeks in advance', 'pending', NULL, NULL, NULL, NULL, '2026-04-05 06:40:06', '2026-04-05 06:40:06'),
(5, 5, 'Bereavement Leave', '2026-03-05', '2026-03-07', 3.00, 'Death of father - death certificate attached', 'pending', NULL, NULL, NULL, NULL, '2026-04-05 06:40:06', '2026-04-05 06:40:06'),
(6, 6, 'Emergency Leave', '2026-03-12', '2026-03-12', 1.00, 'Immediate family medical emergency - hospitalization', 'pending', NULL, NULL, NULL, NULL, '2026-04-05 06:40:06', '2026-04-05 06:40:06'),
(7, 1, 'Sick Leave', '2026-02-10', '2026-02-12', 3.00, 'Flu with fever', 'approved', NULL, NULL, NULL, '{\"requirements\":{\"medical_cert\":true,\"leave_credits\":true,\"reason_stated\":true},\"hrChecks\":{\"days_reasonable\":true,\"medical_proof\":true,\"not_abused\":true}}', '2026-04-05 06:40:06', '2026-04-05 06:40:06'),
(8, 2, 'Vacation Leave', '2026-02-15', '2026-02-18', 4.00, 'Personal matter', 'approved', NULL, NULL, NULL, '{\"requirements\":{\"leave_credits\":true,\"filed_advance\":true},\"hrChecks\":{\"no_schedule_conflict\":true,\"enough_balance\":true,\"coverage_arranged\":true}}', '2026-04-05 06:40:06', '2026-04-05 06:40:06'),
(9, 3, 'Emergency Leave', '2026-02-20', '2026-02-20', 1.00, 'Power outage at home', 'rejected', NULL, NULL, NULL, '{\"requirements\":{\"valid_reason\":true,\"supporting_explanation\":false},\"hrChecks\":{\"urgency_justified\":false,\"not_abused\":true,\"documents_if_applicable\":false}}', '2026-04-05 06:40:06', '2026-04-05 06:40:06');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_clearances`
--

CREATE TABLE `payroll_clearances` (
  `id` int(11) NOT NULL,
  `settlement_id` int(11) NOT NULL,
  `requested_by` int(11) DEFAULT NULL,
  `requested_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `last_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Payroll clearance requests linked to exit settlements';

--
-- Dumping data for table `payroll_clearances`
--

INSERT INTO `payroll_clearances` (`id`, `settlement_id`, `requested_by`, `requested_at`, `status`, `approved_by`, `approved_at`, `comments`, `last_updated`) VALUES
(9, 0, 10, '2026-04-08 15:26:50', 'pending', NULL, NULL, 'Pre-clearance request created automatically during resignation submission.', '2026-04-08 15:26:50'),
(10, 0, 10, '2026-04-08 15:27:55', 'pending', NULL, NULL, 'Pre-clearance request created automatically during resignation submission.', '2026-04-08 15:27:55'),
(11, 0, 10, '2026-04-08 15:28:38', 'pending', NULL, NULL, 'Pre-clearance request created automatically during resignation submission.', '2026-04-08 15:28:38'),
(12, 31, 10, '2026-04-08 15:34:54', 'pending', NULL, NULL, 'Pre-clearance request created automatically during resignation submission.', '2026-04-08 15:34:54'),
(13, 33, 10, '2026-08-07 17:11:10', 'pending', NULL, NULL, 'HR requested payroll settlement calculation', '2026-08-07 17:11:10');

-- --------------------------------------------------------

--
-- Table structure for table `personal_information`
--

CREATE TABLE `personal_information` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `civil_status` enum('Single','Married','Divorced','Widowed') DEFAULT 'Single',
  `nationality` varchar(100) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `disability_info` text DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personal_information`
--

INSERT INTO `personal_information` (`id`, `employee_id`, `date_of_birth`, `gender`, `civil_status`, `nationality`, `blood_type`, `religion`, `disability_info`, `current_address`, `permanent_address`, `created_at`, `updated_at`) VALUES
(1, '1', '1990-01-01', 'Male', 'Single', 'Filipino', NULL, NULL, NULL, NULL, NULL, '2026-04-05 19:37:40', '2026-04-05 19:37:40'),
(2, '36', NULL, NULL, 'Single', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 21:30:57', '2026-04-05 21:30:57'),
(3, '37', NULL, NULL, 'Single', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-06 01:37:35', '2026-04-06 01:37:35'),
(4, '38', NULL, NULL, 'Single', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-06 01:43:53', '2026-04-06 01:43:53');

-- --------------------------------------------------------

--
-- Table structure for table `pm_360_competency_feedback`
--

CREATE TABLE `pm_360_competency_feedback` (
  `feedback_competency_id` int(11) NOT NULL,
  `feedback_id` int(11) NOT NULL,
  `competency_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `strengths` text DEFAULT NULL,
  `improvement_areas` text DEFAULT NULL,
  `examples` text DEFAULT NULL
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
(1, 6, 'Peer', 3, 'Communication', 'asdasdasda', 0, '2026-04-05', '2026-04-05 19:49:58', '2026-04-05 19:49:58'),
(2, 13, 'Subordinate', 3, 'Teamwork', 'kgjgj', 0, '2026-04-06', '2026-04-06 01:22:47', '2026-04-06 01:22:47'),
(3, 8, 'Peer', 2, 'Teamwork', 'dasdasdasdas', 1, '2026-04-06', '2026-04-06 01:28:40', '2026-04-06 01:28:40'),
(4, 4, 'Peer', 4, 'Leadership', 'asdasdasdasdasdasdasdasdas', 1, '2026-04-06', '2026-04-06 01:30:01', '2026-04-06 01:30:01'),
(5, 5, 'Subordinate', 4, 'Performance', 'asdasdasdasdasdas', 1, '2026-04-06', '2026-04-06 01:31:05', '2026-04-06 01:31:05'),
(6, 5, 'Manager', 3, 'Communication', 'ewrwr', 1, '2026-04-06', '2026-04-06 02:06:32', '2026-04-06 02:06:32'),
(7, 1, 'Manager', 1, 'Communication', 'Needs improvement', 1, '2026-02-05', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(8, 1, 'Peer', 2, 'Teamwork', 'Needs improvement', 1, '2026-03-28', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(9, 1, 'Peer', 1, 'Leadership', 'Needs improvement', 1, '2026-02-09', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(10, 2, 'Manager', 2, 'Communication', 'Needs improvement', 1, '2026-01-12', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(11, 2, 'Peer', 1, 'Teamwork', 'Needs improvement', 1, '2026-01-29', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(12, 2, 'Peer', 2, 'Leadership', 'Needs improvement', 1, '2026-03-07', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(13, 3, 'Manager', 2, 'Communication', 'Needs improvement', 1, '2026-01-25', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(14, 3, 'Peer', 2, 'Teamwork', 'Needs improvement', 1, '2026-03-01', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(15, 3, 'Peer', 1, 'Leadership', 'Needs improvement', 1, '2026-03-05', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(16, 7, 'Manager', 1, 'Communication', 'Needs improvement', 1, '2026-01-17', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(17, 7, 'Peer', 1, 'Teamwork', 'Needs improvement', 1, '2026-02-25', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(18, 7, 'Peer', 2, 'Leadership', 'Needs improvement', 1, '2026-03-01', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(19, 9, 'Manager', 1, 'Communication', 'Needs improvement', 1, '2026-02-10', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(20, 9, 'Peer', 2, 'Teamwork', 'Needs improvement', 1, '2026-01-20', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(21, 9, 'Peer', 1, 'Leadership', 'Needs improvement', 1, '2026-03-14', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(22, 10, 'Manager', 1, 'Communication', 'Needs improvement', 1, '2026-03-18', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(23, 10, 'Peer', 1, 'Teamwork', 'Needs improvement', 1, '2026-03-24', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(24, 10, 'Peer', 2, 'Leadership', 'Needs improvement', 1, '2026-03-28', '2026-04-07 15:32:47', '2026-04-07 15:32:47');

-- --------------------------------------------------------

--
-- Table structure for table `pm_anonymous_verification`
--

CREATE TABLE `pm_anonymous_verification` (
  `verification_id` int(11) NOT NULL,
  `feedback_id` int(11) NOT NULL,
  `verification_code` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verified_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pm_anonymous_verification`
--

INSERT INTO `pm_anonymous_verification` (`verification_id`, `feedback_id`, `verification_code`, `is_verified`, `verified_at`, `expires_at`) VALUES
(1, 3, '58c1938457c0828ae53792ea0cfeb520', 1, '2026-04-06 01:29:04', '2026-05-05 19:28:41'),
(2, 4, '55238b1c51e21cca4e123edeef70bd68', 1, '2026-04-06 01:30:14', '2026-05-05 19:30:02'),
(3, 5, '571511e6247ad98dc1343c55aced5885', 0, NULL, '2026-05-05 19:31:06'),
(4, 6, '5d7f56fb1767199278c444a400f13bbe', 0, NULL, '2026-05-05 20:06:33');

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
(1, 6, 'Mid-Year', 'asdasdasdas', '[{\"name\":\"Achievement of Objectives\",\"score\":\"4\"},{\"name\":\"Quality of Work\",\"score\":\"4\"},{\"name\":\"Productivity\",\"score\":\"4\"},{\"name\":\"Collaboration\",\"score\":\"4\"},{\"name\":\"Professional Development\",\"score\":\"4\"}]', 'asdasdas', 4.00, 'asdasdas', '2026-04-05', '2026-04-05 19:50:27', '2026-04-05 19:50:27'),
(2, 8, 'Quarterly', 'asdasdas', '[{\"name\":\"Achievement of Objectives\",\"score\":\"1\"},{\"name\":\"Quality of Work\",\"score\":\"1\"},{\"name\":\"Productivity\",\"score\":\"1\"},{\"name\":\"Collaboration\",\"score\":\"1\"},{\"name\":\"Professional Development\",\"score\":\"1\"}]', 'asdasdasdasdas', 1.00, 'dadasdsadasdasd', '2026-04-05', '2026-04-05 19:53:45', '2026-04-05 19:53:45'),
(3, 40, 'Quarterly', 'adasdasdasdasdasdas', '[{\"name\":\"Achievement of Objectives\",\"score\":\"3\"},{\"name\":\"Quality of Work\",\"score\":\"3\"},{\"name\":\"Productivity\",\"score\":\"3\"},{\"name\":\"Collaboration\",\"score\":\"3\"},{\"name\":\"Professional Development\",\"score\":\"3\"}]', 'adasdasdasda', 3.00, 'dsadasdasdasdas', '2026-04-06', '2026-04-06 06:12:06', '2026-04-06 06:12:06'),
(4, 1, 'Quarterly', '[]', '{}', 'Needs improvement in productivity and meeting deadlines', 2.70, 'Performance below expectations', '2026-03-05', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(5, 2, 'Quarterly', '[]', '{}', 'Needs improvement in productivity and meeting deadlines', 2.10, 'Performance below expectations', '2026-01-25', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(6, 3, 'Quarterly', '[]', '{}', 'Needs improvement in productivity and meeting deadlines', 2.10, 'Performance below expectations', '2026-02-15', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(7, 4, 'Quarterly', '[]', '{}', 'Needs improvement in productivity and meeting deadlines', 2.70, 'Performance below expectations', '2026-03-08', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(8, 5, 'Quarterly', '[]', '{}', 'Needs improvement in productivity and meeting deadlines', 2.80, 'Performance below expectations', '2026-02-12', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(9, 7, 'Quarterly', '[]', '{}', 'Needs improvement in productivity and meeting deadlines', 2.20, 'Performance below expectations', '2026-03-21', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(10, 9, 'Quarterly', '[]', '{}', 'Needs improvement in productivity and meeting deadlines', 2.40, 'Performance below expectations', '2026-02-19', '2026-04-07 15:32:47', '2026-04-07 15:32:47'),
(11, 10, 'Quarterly', '[]', '{}', 'Needs improvement in productivity and meeting deadlines', 2.60, 'Performance below expectations', '2026-03-03', '2026-04-07 15:32:47', '2026-04-07 15:32:47');

-- --------------------------------------------------------

--
-- Table structure for table `pm_calibration_participants`
--

CREATE TABLE `pm_calibration_participants` (
  `participant_id` int(11) NOT NULL,
  `calibration_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `role` enum('Participant','Observer','Facilitator') NOT NULL DEFAULT 'Participant'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pm_calibration_participants`
--

INSERT INTO `pm_calibration_participants` (`participant_id`, `calibration_id`, `employee_id`, `role`) VALUES
(1, 1, 36, 'Participant'),
(2, 1, 6, 'Participant');

-- --------------------------------------------------------

--
-- Table structure for table `pm_calibration_sessions`
--

CREATE TABLE `pm_calibration_sessions` (
  `calibration_id` int(11) NOT NULL,
  `session_name` varchar(255) NOT NULL,
  `session_date` date NOT NULL,
  `facilitator_id` int(11) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `status` enum('Planned','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Planned',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pm_calibration_sessions`
--

INSERT INTO `pm_calibration_sessions` (`calibration_id`, `session_name`, `session_date`, `facilitator_id`, `department`, `status`, `notes`, `created_at`) VALUES
(1, 'GDGHGH', '2026-04-06', 12, 'sale department', 'Planned', NULL, '2026-04-06 02:13:06');

-- --------------------------------------------------------

--
-- Table structure for table `pm_competencies`
--

CREATE TABLE `pm_competencies` (
  `competency_id` int(11) NOT NULL,
  `competency_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('Technical','Leadership','Interpersonal','Strategic','Operational') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pm_competency_frameworks`
--

CREATE TABLE `pm_competency_frameworks` (
  `competency_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `competency_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `criticality` enum('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
  `performance_impact` enum('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
  `weight` decimal(5,2) NOT NULL DEFAULT 1.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pm_competency_frameworks`
--

INSERT INTO `pm_competency_frameworks` (`competency_id`, `role_id`, `competency_name`, `description`, `criticality`, `performance_impact`, `weight`, `is_active`, `created_at`) VALUES
(1, 1, 'Talent Development', 'Creates development plans and supports career growth.', 'High', 'High', 1.50, 1, '2026-04-05 18:44:07'),
(2, 1, 'Employee Engagement', 'Drives a positive workplace culture.', 'High', 'High', 1.25, 1, '2026-04-05 18:44:07'),
(3, 1, 'HR Policies', 'Ensures policies are followed consistently.', 'Medium', 'Medium', 1.00, 1, '2026-04-05 18:44:07'),
(4, 1, 'Coaching', 'Coaches employees to achieve performance goals.', 'High', 'High', 1.40, 1, '2026-04-05 18:44:07'),
(5, 2, 'Presentation', 'Delivers compelling client presentations.', 'High', 'High', 1.40, 1, '2026-04-05 18:44:07'),
(6, 2, 'Negotiation', 'Closes deals with high-value terms.', 'High', 'High', 1.50, 1, '2026-04-05 18:44:07'),
(7, 2, 'CRM', 'Maintains accurate customer records and follow-up.', 'Medium', 'Medium', 1.10, 1, '2026-04-05 18:44:07'),
(8, 2, 'Product Knowledge', 'Understands solutions and can articulate value.', 'High', 'High', 1.35, 1, '2026-04-05 18:44:07'),
(9, 3, 'Troubleshooting', 'Diagnoses and resolves technical issues quickly.', 'High', 'High', 1.50, 1, '2026-04-05 18:44:07'),
(10, 3, 'Customer Service', 'Communicates clearly with end users.', 'Medium', 'Medium', 1.10, 1, '2026-04-05 18:44:07'),
(11, 3, 'Network Support', 'Maintains network availability and security.', 'High', 'High', 1.40, 1, '2026-04-05 18:44:07'),
(12, 3, 'Documentation', 'Keeps accurate technical records.', 'Low', 'Medium', 0.90, 1, '2026-04-05 18:44:07');

-- --------------------------------------------------------

--
-- Table structure for table `pm_competency_indicators`
--

CREATE TABLE `pm_competency_indicators` (
  `indicator_id` int(11) NOT NULL,
  `competency_id` int(11) NOT NULL,
  `indicator_text` text NOT NULL,
  `level` enum('Beginner','Intermediate','Advanced','Expert') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pm_feedback_action_plans`
--

CREATE TABLE `pm_feedback_action_plans` (
  `action_plan_id` int(11) NOT NULL,
  `feedback_id` int(11) NOT NULL,
  `action_description` text NOT NULL,
  `priority` enum('High','Medium','Low') NOT NULL DEFAULT 'Medium',
  `target_date` date NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `status` enum('Not Started','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Not Started',
  `progress_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pm_feedback_action_plans`
--

INSERT INTO `pm_feedback_action_plans` (`action_plan_id`, `feedback_id`, `action_description`, `priority`, `target_date`, `assigned_to`, `status`, `progress_notes`, `created_at`, `updated_at`) VALUES
(1, 3, 'fghfghfghfghf', 'Medium', '2026-05-06', 39, 'Not Started', NULL, '2026-04-06 02:11:31', '2026-04-06 02:11:31');

-- --------------------------------------------------------

--
-- Table structure for table `pm_feedback_analytics`
--

CREATE TABLE `pm_feedback_analytics` (
  `analytics_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `analysis_date` date NOT NULL,
  `overall_rating_trend` enum('Improving','Stable','Declining') NOT NULL,
  `strengths_count` int(11) DEFAULT 0,
  `improvement_areas_count` int(11) DEFAULT 0,
  `top_competencies` text DEFAULT NULL,
  `development_needs` text DEFAULT NULL,
  `consistency_score` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pm_goals`
--

CREATE TABLE `pm_goals` (
  `goal_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `goal_title` varchar(255) NOT NULL,
  `kpi_name` varchar(255) NOT NULL,
  `target_value` decimal(10,2) NOT NULL DEFAULT 100.00,
  `current_progress` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('On Track','Delayed','Completed') NOT NULL DEFAULT 'On Track',
  `priority` enum('Low','Medium','High') NOT NULL DEFAULT 'Medium',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pm_goals`
--

INSERT INTO `pm_goals` (`goal_id`, `employee_id`, `goal_title`, `kpi_name`, `target_value`, `current_progress`, `status`, `priority`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 5, 'errwrw', 'wwrwr', 100.00, 100.00, 'On Track', 'Medium', '2026-04-06', '2026-04-06', '2026-04-06 01:59:06', '2026-04-06 01:59:06');

-- --------------------------------------------------------

--
-- Table structure for table `pm_job_roles`
--

CREATE TABLE `pm_job_roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `job_description` text DEFAULT NULL,
  `required_skills` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pm_job_roles`
--

INSERT INTO `pm_job_roles` (`role_id`, `role_name`, `department`, `job_description`, `required_skills`, `is_active`, `created_at`) VALUES
(1, 'HR Manager', 'Human Resources', 'Leads HR operations, employee relations, and performance development.', 'Talent Development,Employee Engagement,HR Policies,Coaching,Conflict Resolution', 1, '2026-04-05 18:44:07'),
(2, 'Sales Executive', 'Sales', 'Manages customer relationships, closes deals, and drives revenue.', 'Presentation,Negotiation,CRM,Product Knowledge,Prospecting', 1, '2026-04-05 18:44:07'),
(3, 'IT Support Specialist', 'IT', 'Resolves technical issues and maintains system reliability.', 'Troubleshooting,Customer Service,Network Support,System Administration,Documentation', 1, '2026-04-05 18:44:07');

-- --------------------------------------------------------

--
-- Table structure for table `pm_kpi_training_requests`
--

CREATE TABLE `pm_kpi_training_requests` (
  `request_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `goal_id` int(11) DEFAULT NULL,
  `kpi_name` varchar(255) DEFAULT NULL,
  `request_reason` text NOT NULL,
  `requested_program` varchar(255) DEFAULT NULL,
  `requested_course` varchar(255) DEFAULT NULL,
  `ld_training_program_id` int(11) DEFAULT NULL,
  `ld_course_id` int(11) DEFAULT NULL,
  `status` enum('Pending','Sent','Approved','Rejected','Completed') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pm_kpi_training_requests`
--

INSERT INTO `pm_kpi_training_requests` (`request_id`, `employee_id`, `goal_id`, `kpi_name`, `request_reason`, `requested_program`, `requested_course`, `ld_training_program_id`, `ld_course_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 12, NULL, 'dasda', 'aaa', 'asdas', 'asdasdas', 1, 8, 'Pending', '2026-04-05 19:02:06', '2026-04-05 19:02:06'),
(2, 12, NULL, 'dasda', 'aaa', 'asdas', 'asdasdas', 1, 8, 'Pending', '2026-04-05 19:02:07', '2026-04-05 19:02:07'),
(3, 12, NULL, 'dasda', 'aaa', 'asdas', 'asdasdas', 1, 8, 'Pending', '2026-04-05 19:04:25', '2026-04-05 19:04:25'),
(4, 2, NULL, 'dfdsf', 'dsdsfs', 'fdsfds', 'fdsf', 2, 12, 'Pending', '2026-04-05 19:10:39', '2026-04-05 19:10:39'),
(5, 14, NULL, 'hjhkh7777', 'lkjlkjlk', 'hkjhkjhkhkhhjhkhkk7777', 'kjhkjkjhkjhkhkkh8888', 2, 4, 'Pending', '2026-04-05 19:17:23', '2026-04-05 19:17:23'),
(6, 13, NULL, 'asdasdas', 'asdasdasdasdasdas', 'dasdasd', 'asdas', 7, 11, 'Pending', '2026-04-05 20:00:29', '2026-04-05 20:00:29'),
(7, 1, NULL, 'asdasd', 'asdasdasdaads', 'asdasd', 'dasda', 7, 12, 'Pending', '2026-04-05 20:07:03', '2026-04-05 20:07:03');

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

-- --------------------------------------------------------

--
-- Table structure for table `pm_review_templates`
--

CREATE TABLE `pm_review_templates` (
  `template_id` int(11) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `review_period` enum('Quarterly','Annual','Mid-Year') NOT NULL,
  `rating_categories` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pm_review_templates`
--

INSERT INTO `pm_review_templates` (`template_id`, `template_name`, `description`, `review_period`, `rating_categories`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Standard Performance Review', 'Comprehensive performance evaluation template', 'Annual', '[\"Communication Skills\", \"Technical Proficiency\", \"Teamwork\", \"Leadership\", \"Problem Solving\", \"Initiative\", \"Attendance\", \"Goal Achievement\"]', 1, NULL, '2026-04-05 14:46:49', '2026-04-05 14:46:49'),
(2, 'Mid-Year Check-in', 'Mid-year performance check-in template', 'Mid-Year', '[\"Goal Progress\", \"Skill Development\", \"Team Contribution\", \"Communication\", \"Adaptability\"]', 1, NULL, '2026-04-05 14:46:49', '2026-04-05 14:46:49'),
(3, 'Quarterly Review', 'Quarterly performance assessment template', 'Quarterly', '[\"Achievement of Objectives\", \"Quality of Work\", \"Productivity\", \"Collaboration\", \"Professional Development\"]', 1, NULL, '2026-04-05 14:46:49', '2026-04-05 14:46:49');

-- --------------------------------------------------------

--
-- Table structure for table `pm_training_recommendations`
--

CREATE TABLE `pm_training_recommendations` (
  `recommendation_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `skill_gaps` text NOT NULL,
  `employee_competencies` text DEFAULT NULL,
  `gap_score` decimal(5,2) DEFAULT NULL,
  `training_program` varchar(255) NOT NULL,
  `training_type` enum('Online Course','Workshop','Seminar','Internal Training') NOT NULL,
  `priority_level` enum('High','Medium','Low') NOT NULL,
  `job_role_id` int(11) DEFAULT NULL,
  `suggested_completion_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('Proposed','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Proposed',
  `approval_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approval_comments` text DEFAULT NULL,
  `employee_acknowledged` tinyint(1) DEFAULT 0,
  `acknowledged_at` datetime DEFAULT NULL,
  `ld_training_program_id` int(11) DEFAULT NULL,
  `ld_course_id` int(11) DEFAULT NULL,
  `is_overdue` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pm_users`
--

CREATE TABLE `pm_users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','manager','employee') NOT NULL DEFAULT 'employee',
  `theme` enum('light','dark') DEFAULT 'light',
  `employee_id` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(0, 11, 10, 'deduction', 'nawalang mouse', 300.00, '2026-04-05 20:47:28', NULL, 'other');

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
-- Table structure for table `pr_employee_deductions`
--

CREATE TABLE `pr_employee_deductions` (
  `employee_deduction_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `deduction_id` int(11) DEFAULT NULL
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
(8, 4, 1, 1303.39, 0.00, 1303.39, '2026-04-05 20:52:21', 0, NULL, NULL),
(9, 4, 2, 1087.89, 30.00, 1057.89, '2026-04-05 20:52:21', 0, NULL, NULL),
(10, 4, 4, 62.50, 0.00, 62.50, '2026-04-05 20:52:21', 0, NULL, NULL),
(11, 4, 5, 979.82, 0.00, 979.82, '2026-04-05 20:52:21', 0, NULL, NULL),
(12, 4, 6, 744.79, 0.00, 744.79, '2026-04-05 20:52:22', 0, NULL, NULL),
(13, 4, 8, 812.40, 10.00, 802.40, '2026-04-05 20:52:22', 0, NULL, NULL),
(14, 4, 9, 744.79, 0.00, 744.79, '2026-04-05 20:52:22', 0, NULL, NULL),
(15, 4, 10, 711.98, 50.00, 661.98, '2026-04-05 20:52:22', 0, NULL, NULL),
(16, 4, 11, 783.85, 300.00, 483.85, '2026-04-05 20:52:22', 0, NULL, NULL),
(17, 4, 13, 705.73, 60.00, 645.73, '2026-04-05 20:52:22', 0, NULL, NULL),
(18, 4, 14, 744.79, 0.00, 744.79, '2026-04-05 20:52:22', 0, NULL, NULL),
(19, 5, 1, 1348.50, 620.00, 728.50, '2026-04-06 04:13:17', 0, NULL, NULL),
(20, 5, 2, 1117.19, 200.00, 917.19, '2026-04-06 04:13:17', 0, NULL, NULL),
(21, 5, 6, 744.79, 200.00, 544.79, '2026-04-06 04:13:17', 0, NULL, NULL),
(22, 5, 7, 1078.52, 40.00, 1038.52, '2026-04-06 04:13:17', 0, NULL, NULL),
(23, 5, 9, 744.79, 0.00, 744.79, '2026-04-06 04:13:18', 0, NULL, NULL),
(24, 5, 10, 39.06, 0.00, 39.06, '2026-04-06 04:13:18', 0, NULL, NULL),
(25, 5, 11, 744.79, 0.00, 744.79, '2026-04-06 04:13:18', 0, NULL, NULL),
(26, 5, 13, 666.67, 0.00, 666.67, '2026-04-06 04:13:18', 0, NULL, NULL),
(27, 5, 14, 744.79, 0.00, 744.79, '2026-04-06 04:13:18', 0, NULL, NULL);

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
-- Table structure for table `pr_periods`
--

CREATE TABLE `pr_periods` (
  `period_id` int(11) NOT NULL,
  `period_name` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `status` enum('open','processing','closed') DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_periods`
--

INSERT INTO `pr_periods` (`period_id`, `period_name`, `start_date`, `end_date`, `pay_date`, `status`) VALUES
(10, 'Jan 1-15, 2026', '2026-01-01', '2026-01-15', '2026-01-20', 'closed'),
(11, 'Jan 16-31, 2026', '2026-01-16', '2026-01-31', '2026-02-05', 'closed'),
(13, 'Apr 1-15, 2026', '2026-04-01', '2026-04-15', '2026-04-20', 'open'),
(14, 'Feb 1-15, 2026', '2026-02-01', '2026-02-15', '2026-02-20', 'open');

-- --------------------------------------------------------

--
-- Table structure for table `pr_position_deduction_rates`
--

CREATE TABLE `pr_position_deduction_rates` (
  `id` int(11) NOT NULL,
  `position_type` enum('Admin','Teacher','Other') NOT NULL,
  `absence_deduction_amount` decimal(10,2) NOT NULL COMMENT 'Per absence deduction',
  `late_per_minute_rate` decimal(5,2) DEFAULT 2.00 COMMENT 'Deduction per minute late',
  `late_per_hour_rate` decimal(5,2) DEFAULT 120.00 COMMENT 'Deduction per hour late',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_position_deduction_rates`
--

INSERT INTO `pr_position_deduction_rates` (`id`, `position_type`, `absence_deduction_amount`, `late_per_minute_rate`, `late_per_hour_rate`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 1020.00, 2.00, 120.00, 1, '2026-03-29 08:58:43', '2026-03-29 08:58:43'),
(2, 'Teacher', 1536.00, 2.00, 120.00, 1, '2026-03-29 08:58:43', '2026-03-29 08:58:43'),
(3, 'Other', 1000.00, 2.00, 120.00, 1, '2026-03-29 08:58:43', '2026-03-29 08:58:43');

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
(4, 10, '2026-04-05 20:52:22', 'finalized', 1),
(5, 11, '2026-04-06 04:13:19', 'finalized', 1);

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
-- Table structure for table `pr_teacher_loads`
--

CREATE TABLE `pr_teacher_loads` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `academic_year` varchar(10) NOT NULL COMMENT 'e.g., 2025-2026',
  `semester` enum('1st','2nd','Summer') NOT NULL,
  `total_units` decimal(5,2) NOT NULL COMMENT 'Total teaching units (per semester)',
  `created_by` varchar(100) DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL COMMENT 'College Coordinator approval',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Teacher course assignments and units - managed by College Coordinator';

-- --------------------------------------------------------

--
-- Table structure for table `pr_teacher_qualification_rates`
--

CREATE TABLE `pr_teacher_qualification_rates` (
  `id` int(11) NOT NULL,
  `qualification` enum('ProfEd','LPT','Masteral') NOT NULL,
  `pay_per_unit` decimal(10,2) NOT NULL COMMENT 'PHP per unit',
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_teacher_qualification_rates`
--

INSERT INTO `pr_teacher_qualification_rates` (`id`, `qualification`, `pay_per_unit`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ProfEd', 128.00, 'ProfEd/Normal Teacher - Default', 1, '2026-03-29 08:58:43', '2026-03-29 08:58:43'),
(2, 'LPT', 130.00, 'Licensed Professional Teacher', 1, '2026-03-29 08:58:43', '2026-03-29 08:58:43'),
(3, 'Masteral', 250.00, 'Teachers with Masteral Degree', 1, '2026-03-29 08:58:43', '2026-03-29 08:58:43');

-- --------------------------------------------------------

--
-- Table structure for table `rao_admins`
--

CREATE TABLE `rao_admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rao_admins`
--

INSERT INTO `rao_admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$x/mCwvbQhChJHA29F80iyuoz/t3PDrEDuOX156Ya.9A6UwAPmT1gq'),
(1, 'admin', '$2y$10$x/mCwvbQhChJHA29F80iyuoz/t3PDrEDuOX156Ya.9A6UwAPmT1gq');

-- --------------------------------------------------------

--
-- Table structure for table `rao_applications`
--

CREATE TABLE `rao_applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending',
  `hired` tinyint(1) DEFAULT 0,
  `is_archived` tinyint(1) DEFAULT 0,
  `archived_at` datetime DEFAULT NULL,
  `source` varchar(50) DEFAULT 'Online Posting',
  `ready_for_offer` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rao_applications`
--

INSERT INTO `rao_applications` (`id`, `job_id`, `first_name`, `last_name`, `email`, `phone`, `address`, `summary`, `cover_letter`, `resume`, `created_at`, `status`, `hired`, `is_archived`, `archived_at`, `source`, `ready_for_offer`) VALUES
(1, 1, 'Jhon Carlo', 'Garcia', 'jhoncarlogarcia30@gmail.com', '09916117933', 'Brgy.Assumption', '', 'TEST', 'uploads/resumes/1775404856_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-05 16:00:56', 'approved', 0, 0, NULL, 'Online Posting', 1),
(2, 2, 'Richard John', 'Capalad', 'jhoncarlogarcia30@gmail.com', '09916117933', 'Brgy.Assumption', '', 'TEST', 'uploads/resumes/1775407253_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-05 16:40:53', 'approved', 0, 0, NULL, 'Online Posting', 0),
(3, 1, 'Richard John', 'Capalad', 'capaladrichardjohn@gmail.com', '09556683080', 'Caloocan', '', 'DUMMY ', 'uploads/resumes/1775443121_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-06 01:38:40', 'pending', 0, 1, '2026-04-06 09:38:59', 'Online Posting', 0),
(4, 1, 'Danmark', 'Baldonido', 'danmark1201@gmail.com', '09853926174', 'Area f', '', 'DUMMY 1', 'uploads/resumes/1775443179_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-06 01:39:38', 'approved', 0, 0, NULL, 'Online Posting', 1),
(5, 1, 'Bryan', 'Palco', 'palcobryan1104@gmail.com', '09085795733', 'Tungko', '', 'DUMMY 2', 'uploads/resumes/1775443251_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-06 01:40:51', 'pending', 0, 0, NULL, 'Online Posting', 0),
(6, 1, 'Radiant', 'Fabellion', 'terraipanger@gmail.com', '09916117933', 'Kurba', '', 'DUMMY 3', 'uploads/resumes/1775443340_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-06 01:42:20', 'pending', 0, 0, NULL, 'Online Posting', 0),
(7, 1, 'Sophia', 'Capalad', 'jhoncarlogarcia40@gmail.com', '09556683080', 'Brgy.Assumption', '', '', 'uploads/resumes/1775441737_Property-Management-System-final.docx', '2026-04-06 02:15:36', 'approved', 0, 0, NULL, 'Online Posting', 1),
(8, 3, 'David', 'Jaurigue', 'aviidjaurigue07@gmail.com', '09916117933', 'dsadsad', '', 'TEST', 'uploads/resumes/1775445661_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-06 03:21:00', 'approved', 0, 0, NULL, 'Online Posting', 1),
(9, 1, 'Mark Christian', 'Pangilinan', 'boros.jaurigue@gmail.com', '09556683080', 'Brgy.Assumption', '', 'TEST 1', 'uploads/resumes/1775445726_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-06 03:22:05', 'approved', 0, 0, NULL, 'Online Posting', 1),
(10, 1, 'Philip', 'Arevalo', 'boros.dominator@gmail.com', '09916117933', 'STARMALL', '', 'TEST', 'uploads/resumes/1775445762_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-06 03:22:41', 'approved', 0, 0, NULL, 'Online Posting', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rao_education`
--

CREATE TABLE `rao_education` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `school` varchar(255) DEFAULT NULL,
  `field_of_study` varchar(255) DEFAULT NULL,
  `degree` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rao_experience`
--

CREATE TABLE `rao_experience` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rao_hired`
--

CREATE TABLE `rao_hired` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `base_job` varchar(255) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `hired_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rao_hired`
--

INSERT INTO `rao_hired` (`id`, `application_id`, `first_name`, `last_name`, `email`, `phone`, `address`, `position`, `department`, `base_job`, `salary`, `hired_at`) VALUES
(11, 1, 'Jhon Carlo', 'Garcia', 'jhoncarlogarcia30@gmail.com', '09916117933', 'Brgy.Assumption', 'sawadika', 'test', 'Information in Technology', 41.00, '2026-04-05 16:03:34'),
(12, 4, 'Danmark', 'Baldonido', 'danmark1201@gmail.com', '09853926174', 'Area f', 'TEST', 'test', 'Information in Technology', 123123.00, '2026-04-06 02:07:20'),
(13, 7, 'Sophia', 'Capalad', 'jhoncarlogarcia40@gmail.com', '09556683080', 'Brgy.Assumption', 'Taga hugas ng Plato', 'Canteen', 'Information in Technology', 8000.00, '2026-04-06 02:19:30'),
(14, 8, 'David', 'Jaurigue', 'aviidjaurigue07@gmail.com', '09916117933', 'dsadsad', 'directress', 'directress', 'School Directress', 70000.00, '2026-04-06 03:52:39'),
(15, 9, 'Mark Christian', 'Pangilinan', 'boros.jaurigue@gmail.com', '09556683080', 'Brgy.Assumption', 'IT prog', 'IT', 'Information in Technology', 123213.00, '2026-04-06 03:52:42'),
(16, 10, 'Philip', 'Arevalo', 'boros.dominator@gmail.com', '09916117933', 'STARMALL', 'Prof', 'it', 'Information in Technology', 60000.00, '2026-04-06 03:52:46');

-- --------------------------------------------------------

--
-- Table structure for table `rao_hired_applicants`
--

CREATE TABLE `rao_hired_applicants` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `job_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `hired_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rao_interviews`
--

CREATE TABLE `rao_interviews` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `interview_date` date NOT NULL,
  `interview_time` time NOT NULL,
  `interview_type` varchar(50) DEFAULT NULL,
  `stage_order` int(11) DEFAULT 1,
  `interview_mode` varchar(50) DEFAULT NULL,
  `meeting_link` varchar(255) DEFAULT NULL,
  `interviewer` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Scheduled',
  `result` enum('pending','passed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rating` int(1) DEFAULT 0,
  `feedback` text DEFAULT NULL,
  `feedback_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rao_interviews`
--

INSERT INTO `rao_interviews` (`id`, `application_id`, `interview_date`, `interview_time`, `interview_type`, `stage_order`, `interview_mode`, `meeting_link`, `interviewer`, `status`, `result`, `created_at`, `rating`, `feedback`, `feedback_date`) VALUES
(1, 1, '2026-04-07', '00:01:00', 'Initial Interview', 1, 'Online', 'ATETAT', 'setsetse', 'Scheduled', 'passed', '2026-04-05 16:01:17', 0, NULL, '2026-04-05 16:01:17'),
(2, 1, '2026-04-07', '00:01:00', 'Technical Interview', 2, 'Online', 'TEST', 'TESTST', 'Scheduled', 'passed', '2026-04-05 16:01:36', 0, NULL, '2026-04-05 16:01:36'),
(3, 1, '2026-04-08', '00:01:00', 'Final Interview', 3, 'Online', 'ATESF', 'EFSEF', 'Scheduled', 'passed', '2026-04-05 16:01:53', 5, '123214', '2026-04-05 16:02:07'),
(4, 2, '2026-04-06', '13:43:00', 'Initial Interview', 1, 'Online', 'asdas', 'asdas', 'Scheduled', 'passed', '2026-04-05 16:41:18', 0, NULL, '2026-04-05 16:41:18'),
(5, 2, '2026-04-05', '12:45:00', 'Technical Interview', 2, 'Online', 'sadsa', 'dasdasd', 'Scheduled', 'pending', '2026-04-05 16:45:22', 0, NULL, '2026-04-05 16:45:22'),
(6, 4, '2026-04-07', '10:05:00', 'Initial Interview', 1, 'Online', '123', '123123', 'Scheduled', 'passed', '2026-04-06 02:05:54', 0, NULL, '2026-04-06 02:05:54'),
(7, 4, '2026-04-07', '10:06:00', 'Technical Interview', 2, 'Online', '12312', '123124123', 'Scheduled', 'passed', '2026-04-06 02:06:12', 0, NULL, '2026-04-06 02:06:12'),
(8, 4, '2026-04-08', '10:06:00', 'Final Interview', 3, 'Online', '124123', '123124123', 'Scheduled', 'passed', '2026-04-06 02:06:30', 5, '123124213', '2026-04-06 02:06:50'),
(9, 7, '2026-04-06', '14:20:00', 'Initial Interview', 1, 'Online', 'HAHAHHAH', 'Mark Christian', 'Scheduled', 'passed', '2026-04-06 02:16:33', 0, NULL, '2026-04-06 02:16:33'),
(10, 7, '2026-04-07', '13:16:00', 'Technical Interview', 2, 'Online', 'HEHEHEHHE', 'sadwa', 'Scheduled', 'passed', '2026-04-06 02:17:03', 0, NULL, '2026-04-06 02:17:03'),
(11, 7, '2026-04-08', '22:21:00', 'Final Interview', 3, 'Online', 'HIHIHIHIHI', 'adwd', 'Scheduled', 'passed', '2026-04-06 02:17:32', 5, 'BUBU', '2026-04-06 02:18:04'),
(12, 10, '2026-04-06', '12:10:00', 'Initial Interview', 1, 'Face to Face', 'HRD OFFICE', 'JANNY MAE NARRAK', 'Scheduled', 'passed', '2026-04-06 03:36:25', 0, NULL, '2026-04-06 03:36:25'),
(13, 10, '2026-04-07', '11:41:00', 'Technical Interview', 2, 'Face to Face', 'TEST', 'TEST', 'Scheduled', 'passed', '2026-04-06 03:41:58', 0, NULL, '2026-04-06 03:41:58'),
(14, 10, '2026-04-08', '23:57:00', 'Final Interview', 3, 'Online', 'testdfse', 'sadwa', 'Scheduled', 'passed', '2026-04-06 03:42:22', 5, 'EQWEADAS', '2026-04-06 03:42:59'),
(15, 8, '2026-04-06', '23:48:00', 'Initial Interview', 1, 'Online', 'TEST', 'TEST', 'Scheduled', 'passed', '2026-04-06 03:48:54', 0, NULL, '2026-04-06 03:48:54'),
(16, 8, '2026-04-07', '11:49:00', 'Technical Interview', 2, 'Online', 'TEST', 'TEST', 'Scheduled', 'passed', '2026-04-06 03:49:12', 0, NULL, '2026-04-06 03:49:12'),
(17, 8, '2026-04-08', '11:49:00', 'Final Interview', 3, 'Online', 'TEST', 'TEST', 'Scheduled', 'passed', '2026-04-06 03:49:33', 5, 'sd', '2026-04-06 03:50:58'),
(18, 9, '2026-04-06', '23:49:00', 'Initial Interview', 1, 'Online', 'TEST', 'TEST', 'Scheduled', 'passed', '2026-04-06 03:50:01', 0, NULL, '2026-04-06 03:50:01'),
(19, 9, '2026-04-07', '11:50:00', 'Technical Interview', 2, 'Online', 'TEST', 'TEST', 'Scheduled', 'passed', '2026-04-06 03:50:19', 0, NULL, '2026-04-06 03:50:19'),
(20, 9, '2026-04-08', '11:50:00', 'Final Interview', 3, 'Online', 'TEST', 'TEST', 'Scheduled', 'passed', '2026-04-06 03:50:42', 5, 'asd', '2026-04-06 03:51:04');

-- --------------------------------------------------------

--
-- Table structure for table `rao_jobs`
--

CREATE TABLE `rao_jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `qualifications` varchar(255) DEFAULT NULL,
  `category` enum('Teaching','Non-Teaching') NOT NULL,
  `location` enum('Bestlink Main Campus','Bestlink MV Campus','Bestlink Bulacan Campus') NOT NULL,
  `max_applicants` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rao_jobs`
--

INSERT INTO `rao_jobs` (`id`, `title`, `description`, `qualifications`, `category`, `location`, `max_applicants`) VALUES
(1, 'Information in Technology', 'TEST', 'TEST', 'Teaching', 'Bestlink Bulacan Campus', 10),
(2, 'Information Technology', 'Test', 'Masteral', 'Teaching', 'Bestlink Bulacan Campus', 1),
(3, 'School Directress', 'school directress', 'Doctorate\r\nPHD\r\n3yrs Princi[al', 'Non-Teaching', 'Bestlink Bulacan Campus', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rao_offer`
--

CREATE TABLE `rao_offer` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `base_job` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `benefit_package` text DEFAULT NULL,
  `additional_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Sent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rao_offer`
--

INSERT INTO `rao_offer` (`id`, `application_id`, `base_job`, `position`, `department`, `salary`, `benefit_package`, `additional_note`, `created_at`, `status`) VALUES
(17, 1, 'Information in Technology', 'sawadika', 'test', 41.00, '12321', '123213', '2026-04-05 16:03:21', 'Accepted'),
(18, 4, 'Information in Technology', 'TEST', 'test', 123123.00, '123', '12341', '2026-04-06 02:07:10', 'Accepted'),
(19, 7, 'Information in Technology', 'Taga hugas ng Plato', 'Canteen', 8000.00, 'wala', 'hanap ka na lang ibang work kapag ayaw mo', '2026-04-06 02:19:08', 'Accepted'),
(20, 10, 'Information in Technology', 'Prof', 'it', 60000.00, 'WALA', 'WALA', '2026-04-06 03:48:10', 'Accepted'),
(21, 9, 'Information in Technology', 'IT prog', 'IT', 123213.00, 'wala', 'wala', '2026-04-06 03:51:39', 'Accepted'),
(22, 8, 'School Directress', 'directress', 'directress', 70000.00, 'wala', 'walaa', '2026-04-06 03:52:13', 'Accepted'),
(23, 8, 'School Directress', 'directress', 'directress', 70000.00, 'wala', 'walaa', '2026-04-06 03:52:19', 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `rao_offer_salary`
--

CREATE TABLE `rao_offer_salary` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `interview_date` date NOT NULL,
  `interview_time` time NOT NULL,
  `interviewer` varchar(100) DEFAULT NULL,
  `stage_order` int(11) DEFAULT 3,
  `rating` int(1) DEFAULT 0,
  `feedback` text DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `offer_status` enum('for_offer','offered','accepted','rejected') DEFAULT 'for_offer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rao_offer_salary`
--

INSERT INTO `rao_offer_salary` (`id`, `application_id`, `job_id`, `interview_date`, `interview_time`, `interviewer`, `stage_order`, `rating`, `feedback`, `position`, `salary`, `offer_status`, `created_at`) VALUES
(1, 1001, 1, '2026-01-10', '09:00:00', 'HR Manager', 3, 4, 'Strong technical skills, good communication', 'Software Engineer', 35000.00, 'offered', '2026-04-05 20:35:53'),
(2, 1002, 2, '2026-01-12', '10:30:00', 'Finance Head', 3, 5, 'Excellent accounting knowledge, highly recommended', 'Accountant', 30000.00, 'accepted', '2026-04-05 20:35:53');

-- --------------------------------------------------------

--
-- Table structure for table `rao_onboarding`
--

CREATE TABLE `rao_onboarding` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `base_job` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `mentor` varchar(255) DEFAULT NULL,
  `checklist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`checklist`)),
  `progress` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rao_onboarding`
--

INSERT INTO `rao_onboarding` (`id`, `application_id`, `full_name`, `base_job`, `position`, `department`, `location`, `start_date`, `mentor`, `checklist`, `progress`, `created_at`) VALUES
(1, 7, 'Sophia Capalad', 'Information in Technology', 'Taga hugas ng Plato', 'Canteen', '1213', '2026-04-13', 'sfawdas', '[{\"text\":\"SSS\",\"checked\":1},{\"text\":\"Pag-IBIG\",\"checked\":1},{\"text\":\"NBI Clearance\",\"checked\":1},{\"text\":\"PhilHealth\",\"checked\":1},{\"text\":\"Prepare Workstation\",\"checked\":1},{\"text\":\"Orientation\",\"checked\":1},{\"text\":\"Department Introduction\",\"checked\":1}]', 100, '2026-04-06 02:20:02'),
(2, 10, 'Philip Arevalo', 'Information in Technology', 'Prof', 'it', '3rd floor', '2026-04-06', 'test', '[{\"text\":\"SSS\",\"checked\":1},{\"text\":\"Pag-IBIG\",\"checked\":1},{\"text\":\"NBI Clearance\",\"checked\":1},{\"text\":\"PhilHealth\",\"checked\":1},{\"text\":\"Prepare Workstation\",\"checked\":1},{\"text\":\"Orientation\",\"checked\":1},{\"text\":\"Department Introduction\",\"checked\":1}]', 100, '2026-04-06 03:56:18'),
(3, 8, 'David Jaurigue', 'School Directress', 'directress', 'directress', 'TEST', '2026-04-07', '123213', '[{\"text\":\"SSS\",\"checked\":1},{\"text\":\"Pag-IBIG\",\"checked\":1},{\"text\":\"NBI Clearance\",\"checked\":1},{\"text\":\"PhilHealth\",\"checked\":1},{\"text\":\"Prepare Workstation\",\"checked\":1},{\"text\":\"Orientation\",\"checked\":1},{\"text\":\"Department Introduction\",\"checked\":1}]', 100, '2026-04-06 03:56:57'),
(4, 9, 'Mark Christian Pangilinan', 'Information in Technology', 'IT prog', 'IT', '1213', '2026-04-07', 'sfawdas', '[{\"text\":\"SSS\",\"checked\":1},{\"text\":\"Pag-IBIG\",\"checked\":1},{\"text\":\"NBI Clearance\",\"checked\":1},{\"text\":\"PhilHealth\",\"checked\":1},{\"text\":\"Prepare Workstation\",\"checked\":1},{\"text\":\"Orientation\",\"checked\":1},{\"text\":\"Department Introduction\",\"checked\":1}]', 100, '2026-04-06 03:57:28');

-- --------------------------------------------------------

--
-- Table structure for table `rao_parsed_resume`
--

CREATE TABLE `rao_parsed_resume` (
  `id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `job_title` varchar(150) DEFAULT NULL,
  `resume` text DEFAULT NULL,
  `approved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rao_parsed_resume`
--

INSERT INTO `rao_parsed_resume` (`id`, `application_id`, `first_name`, `last_name`, `email`, `phone`, `job_title`, `resume`, `approved_at`) VALUES
(1, 1, 'Jhon Carlo', 'Garcia', 'jhoncarlogarcia30@gmail.com', '09916117933', 'Information in Technology', 'uploads/resumes/1775404856_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-05 16:01:03'),
(2, 2, 'Richard John', 'Capalad', 'jhoncarlogarcia30@gmail.com', '09916117933', 'Information Technology', 'uploads/resumes/1775407253_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-05 16:41:01'),
(3, 4, 'Danmark', 'Baldonido', 'danmark1201@gmail.com', '09853926174', 'Information in Technology', 'uploads/resumes/1775443179_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-06 02:05:41'),
(4, 7, 'Sophia', 'Capalad', 'jhoncarlogarcia40@gmail.com', '09556683080', 'Information in Technology', 'uploads/resumes/1775441737_Property-Management-System-final.docx', '2026-04-06 02:15:49'),
(5, 10, 'Philip', 'Arevalo', 'boros.dominator@gmail.com', '09916117933', 'Information in Technology', 'uploads/resumes/1775445762_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-06 03:31:53'),
(6, 9, 'Mark Christian', 'Pangilinan', 'boros.jaurigue@gmail.com', '09556683080', 'Information in Technology', 'uploads/resumes/1775445726_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-06 03:48:26'),
(7, 8, 'David', 'Jaurigue', 'aviidjaurigue07@gmail.com', '09916117933', 'School Directress', 'uploads/resumes/1775445661_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-04-06 03:48:29');

-- --------------------------------------------------------

--
-- Table structure for table `ta_absence_late_policies`
--

CREATE TABLE `ta_absence_late_policies` (
  `policy_id` int(11) NOT NULL,
  `policy_name` varchar(100) NOT NULL,
  `max_late_per_month` int(11) DEFAULT 3,
  `max_absent_per_month` int(11) DEFAULT 2,
  `max_excused_absent_per_month` int(11) DEFAULT 2,
  `max_excused_late_per_month` int(11) DEFAULT 5,
  `warning_after_late_count` int(11) DEFAULT 5,
  `warning_after_absent_count` int(11) DEFAULT 3,
  `late_threshold_minutes` int(11) DEFAULT 15,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_absence_late_policies`
--

INSERT INTO `ta_absence_late_policies` (`policy_id`, `policy_name`, `max_late_per_month`, `max_absent_per_month`, `max_excused_absent_per_month`, `max_excused_late_per_month`, `warning_after_late_count`, `warning_after_absent_count`, `late_threshold_minutes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Default Company Policy', 3, 2, 2, 5, 5, 3, 15, 1, '2026-03-19 18:42:28', '2026-03-19 18:42:28');

-- --------------------------------------------------------

--
-- Table structure for table `ta_absence_late_records`
--

CREATE TABLE `ta_absence_late_records` (
  `record_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `absence_date` date NOT NULL,
  `type` enum('ABSENT','LATE') NOT NULL DEFAULT 'ABSENT',
  `late_minutes` int(11) DEFAULT NULL,
  `excuse_status` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
  `reason` text DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ta_absence_late_records`
--

INSERT INTO `ta_absence_late_records` (`record_id`, `employee_id`, `absence_date`, `type`, `late_minutes`, `excuse_status`, `reason`, `approval_notes`, `approved_by`, `approval_date`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-04-04', 'ABSENT', NULL, 'APPROVED', 'Sick leave', NULL, NULL, '2026-04-06 10:13:59', '2026-04-06 02:08:24', '2026-04-06 02:13:59'),
(2, 1, '2026-04-05', 'LATE', 25, 'PENDING', 'Traffic delay', NULL, NULL, NULL, '2026-04-06 02:08:24', '2026-04-06 02:08:24'),
(3, 1, '2026-04-06', 'ABSENT', NULL, 'APPROVED', 'Doctor appointment', '', NULL, '2026-04-06 10:39:53', '2026-04-06 02:08:24', '2026-04-06 02:39:53'),
(4, 2, '2026-04-04', 'ABSENT', NULL, 'PENDING', 'Sick leave', NULL, NULL, NULL, '2026-04-06 02:08:24', '2026-04-06 02:08:24'),
(5, 2, '2026-04-05', 'LATE', 25, 'PENDING', 'Traffic delay', NULL, NULL, NULL, '2026-04-06 02:08:24', '2026-04-06 02:08:24'),
(6, 2, '2026-04-06', 'ABSENT', NULL, 'APPROVED', 'Doctor appointment', '', NULL, '2026-04-06 10:39:59', '2026-04-06 02:08:24', '2026-04-06 02:39:59'),
(7, 3, '2026-04-04', 'ABSENT', NULL, 'PENDING', 'Sick leave', NULL, NULL, NULL, '2026-04-06 02:08:24', '2026-04-06 02:08:24'),
(8, 3, '2026-04-05', 'LATE', 25, 'PENDING', 'Traffic delay', NULL, NULL, NULL, '2026-04-06 02:08:24', '2026-04-06 02:08:24'),
(9, 3, '2026-04-06', 'ABSENT', NULL, 'PENDING', 'Doctor appointment', NULL, NULL, NULL, '2026-04-06 02:08:24', '2026-04-06 02:08:24'),
(10, 4, '2026-04-04', 'ABSENT', NULL, 'PENDING', 'Sick leave', NULL, NULL, NULL, '2026-04-06 02:08:24', '2026-04-06 02:08:24'),
(11, 4, '2026-04-05', 'LATE', 25, 'PENDING', 'Traffic delay', NULL, NULL, NULL, '2026-04-06 02:08:24', '2026-04-06 02:08:24'),
(12, 4, '2026-04-06', 'ABSENT', NULL, 'PENDING', 'Doctor appointment', NULL, NULL, NULL, '2026-04-06 02:08:24', '2026-04-06 02:08:24'),
(13, 5, '2026-04-04', 'ABSENT', NULL, 'PENDING', 'Sick leave', NULL, NULL, NULL, '2026-04-06 02:08:24', '2026-04-06 02:08:24'),
(14, 5, '2026-04-05', 'LATE', 25, 'PENDING', 'Traffic delay', NULL, NULL, NULL, '2026-04-06 02:08:24', '2026-04-06 02:08:24'),
(15, 5, '2026-04-06', 'ABSENT', NULL, 'PENDING', 'Doctor appointment', NULL, NULL, NULL, '2026-04-06 02:08:24', '2026-04-06 02:08:24'),
(16, 1, '2026-04-04', 'ABSENT', NULL, 'PENDING', 'Sick leave', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(17, 1, '2026-04-05', 'LATE', 25, 'PENDING', 'Traffic delay', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(18, 1, '2026-04-06', 'ABSENT', NULL, 'PENDING', 'Doctor appointment', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(19, 2, '2026-04-04', 'ABSENT', NULL, 'PENDING', 'Sick leave', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(20, 2, '2026-04-05', 'LATE', 25, 'PENDING', 'Traffic delay', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(21, 2, '2026-04-06', 'ABSENT', NULL, 'PENDING', 'Doctor appointment', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(22, 3, '2026-04-04', 'ABSENT', NULL, 'PENDING', 'Sick leave', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(23, 3, '2026-04-05', 'LATE', 25, 'PENDING', 'Traffic delay', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(24, 3, '2026-04-06', 'ABSENT', NULL, 'PENDING', 'Doctor appointment', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(25, 4, '2026-04-04', 'ABSENT', NULL, 'PENDING', 'Sick leave', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(26, 4, '2026-04-05', 'LATE', 25, 'PENDING', 'Traffic delay', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(27, 4, '2026-04-06', 'ABSENT', NULL, 'PENDING', 'Doctor appointment', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(28, 5, '2026-04-04', 'ABSENT', NULL, 'PENDING', 'Sick leave', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(29, 5, '2026-04-05', 'LATE', 25, 'PENDING', 'Traffic delay', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28'),
(30, 5, '2026-04-06', 'ABSENT', NULL, 'PENDING', 'Doctor appointment', NULL, NULL, NULL, '2026-04-06 02:08:28', '2026-04-06 02:08:28');

-- --------------------------------------------------------

--
-- Table structure for table `ta_absence_late_thresholds`
--

CREATE TABLE `ta_absence_late_thresholds` (
  `threshold_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month_year` varchar(7) NOT NULL,
  `absent_count` int(11) DEFAULT 0,
  `late_count` int(11) DEFAULT 0,
  `excused_absent_count` int(11) DEFAULT 0,
  `excused_late_count` int(11) DEFAULT 0,
  `warning_level` enum('NONE','LEVEL_1','LEVEL_2','LEVEL_3') DEFAULT 'NONE',
  `warning_date` datetime DEFAULT NULL,
  `last_action_taken` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_absence_late_thresholds`
--

INSERT INTO `ta_absence_late_thresholds` (`threshold_id`, `employee_id`, `month_year`, `absent_count`, `late_count`, `excused_absent_count`, `excused_late_count`, `warning_level`, `warning_date`, `last_action_taken`, `created_at`, `updated_at`, `employee_id_new`) VALUES
(0, 1, '2026-04', 2, 2, 2, 0, 'NONE', NULL, NULL, '2026-04-06 02:39:53', '2026-04-06 02:39:53', NULL),
(0, 2, '2026-04', 3, 2, 1, 0, 'NONE', NULL, NULL, '2026-04-06 02:39:59', '2026-04-06 02:39:59', NULL),
(0, 11, '2026-08', NULL, NULL, NULL, NULL, 'NONE', NULL, NULL, '2026-08-01 04:50:23', '2026-08-01 04:50:23', NULL),
(0, 14, '2026-08', NULL, NULL, NULL, NULL, 'NONE', NULL, NULL, '2026-08-01 04:50:28', '2026-08-01 04:50:28', NULL);

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
(1, 3, NULL, '2026-03-27', '0000-00-00 00:00:00', NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-03-27 15:17:19', '2026-04-05 20:25:17', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(2, 3, NULL, '2026-03-28', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MANUAL', 'EARLY_OUT', NULL, 0, NULL, NULL, NULL, '2026-03-28 00:46:35', '2026-04-05 20:25:23', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(3, 1, NULL, '2026-03-28', '2026-03-28 09:03:23', '2026-03-28 09:03:31', 'MANUAL', 'EARLY_OUT', NULL, 0, NULL, NULL, NULL, '2026-03-28 01:01:53', '2026-04-05 20:25:26', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(4, 2, NULL, '2026-03-28', '2026-03-28 09:04:55', '2026-03-28 09:16:05', 'MANUAL', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-03-28 01:04:55', '2026-04-05 20:25:31', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(5, 6, NULL, '2026-03-28', '2026-03-28 10:59:33', NULL, 'MANUAL', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-03-28 02:59:33', '2026-04-05 20:25:35', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(6, 7, NULL, '2026-03-28', '2026-03-28 11:56:06', NULL, 'MANUAL', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-03-28 03:56:06', '2026-03-28 03:56:06', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(7, 8, NULL, '2026-03-28', '2026-03-28 17:36:20', '2026-03-28 17:37:39', 'MANUAL', 'LATE', NULL, 0, NULL, NULL, NULL, '2026-03-28 09:36:20', '2026-04-05 20:25:40', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(8, 4, NULL, '2026-03-28', '2026-03-28 15:44:30', NULL, 'QR', 'PENDING_APPROVAL', NULL, 1, 3, '', '2026-04-06 01:57:03', '2026-03-28 14:44:30', '2026-04-05 17:57:03', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(9, 5, NULL, '2026-03-28', '2026-03-28 23:13:41', NULL, 'MANUAL', 'ON_LEAVE', NULL, 1, 3, '', '2026-04-06 00:59:43', '2026-03-28 15:13:41', '2026-04-05 20:25:44', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(10, 3, NULL, '2026-03-29', '2026-03-29 16:06:47', '2026-03-29 16:06:53', 'MANUAL', 'EARLY_OUT', NULL, 1, 3, '', '2026-04-02 19:29:21', '2026-03-29 08:06:47', '2026-04-05 20:25:50', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(11, 4, NULL, '2026-03-29', '2026-03-29 16:57:44', '2026-03-29 16:58:24', 'QR', 'PENDING_APPROVAL', NULL, 1, 3, '', '2026-04-02 19:29:18', '2026-03-29 08:57:44', '2026-04-02 11:29:18', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(12, 6, NULL, '2026-03-29', '2026-03-29 17:15:18', NULL, 'QR', 'PRESENT', NULL, 1, 3, 'awdaw', '2026-04-02 19:29:02', '2026-03-29 09:15:18', '2026-04-05 20:25:54', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(13, 5, NULL, '2026-03-30', '2026-03-30 09:49:20', NULL, 'QR', 'PENDING_APPROVAL', NULL, 1, 3, '', '2026-04-02 14:33:42', '2026-03-30 01:49:20', '2026-04-02 06:33:42', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(14, 1, 2, '2026-04-05', '2026-04-05 19:24:55', '2026-04-05 19:26:31', 'QR', 'PRESENT', NULL, 1, 3, '', '2026-04-05 19:28:24', '2026-04-05 11:24:55', '2026-04-05 11:28:24', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(15, 1, NULL, '2026-04-06', '2026-04-06 02:56:46', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-04-05 18:56:46', '2026-04-05 18:56:46', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(16, 8, NULL, '2026-04-06', '2026-04-06 03:37:20', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-04-05 19:37:20', '2026-04-05 19:37:20', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(300, 1, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(301, 2, 1, '2026-01-05', '2026-01-05 08:15:00', '2026-01-05 17:00:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 8.75, 8.00, 0.75, 1, 1, 1, 15, 0, 480, NULL),
(302, 3, 1, '2026-01-05', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 0.00, 0.00, 0.00, 1, 1, 1, 0, 0, 480, NULL),
(303, 4, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 16:30:00', 'QR', 'EARLY_OUT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 8.50, 8.00, 0.50, 1, 1, 1, 0, 30, 480, NULL),
(304, 5, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 17:30:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.50, 8.00, 1.50, 1, 1, 1, 0, 0, 480, NULL),
(305, 13, 2, '2026-01-05', '2026-01-05 09:30:00', '2026-01-05 16:30:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 7.00, 6.50, 0.50, 1, 1, 1, 30, 0, 360, NULL),
(306, 6, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(307, 7, 1, '2026-01-05', NULL, NULL, 'SYSTEM', 'ON_LEAVE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 0.00, 0.00, 0.00, 1, 1, 1, 0, 0, 480, NULL),
(308, 8, 1, '2026-01-05', '2026-01-05 08:05:00', '2026-01-05 17:00:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 8.92, 8.00, 0.92, 1, 1, 1, 5, 0, 480, NULL),
(309, 9, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(310, 10, 1, '2026-01-05', '2026-01-05 08:25:00', '2026-01-05 17:00:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 8.58, 8.00, 0.58, 1, 1, 1, 25, 0, 480, NULL),
(311, 11, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 17:30:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.50, 8.00, 1.50, 1, 1, 1, 0, 0, 480, NULL),
(312, 14, 1, '2026-01-05', '2026-01-05 08:00:00', '2026-01-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(313, 1, 1, '2026-01-20', '2026-01-20 08:10:00', '2026-01-20 17:30:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.33, 8.00, 1.33, 1, 1, 1, 10, 0, 480, NULL),
(314, 2, 1, '2026-01-20', '2026-01-20 08:00:00', '2026-01-20 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(315, 3, 1, '2026-01-20', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 0.00, 0.00, 0.00, 1, 1, 1, 0, 0, 480, NULL),
(316, 13, 2, '2026-01-20', '2026-01-20 10:00:00', '2026-01-20 16:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 6.00, 6.00, 0.00, 1, 1, 1, 0, 0, 360, NULL),
(317, 6, 1, '2026-01-20', '2026-01-20 08:00:00', '2026-01-20 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(318, 7, 1, '2026-01-20', '2026-01-20 08:20:00', '2026-01-20 17:00:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 8.67, 8.00, 0.67, 1, 1, 1, 20, 0, 480, NULL),
(319, 8, 1, '2026-01-20', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 0.00, 0.00, 0.00, 1, 1, 1, 0, 0, 480, NULL),
(320, 9, 1, '2026-01-20', '2026-01-20 08:00:00', '2026-01-20 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(321, 10, 1, '2026-01-20', '2026-01-20 08:00:00', '2026-01-20 16:30:00', 'QR', 'EARLY_OUT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 8.50, 8.00, 0.50, 1, 1, 1, 0, 30, 480, NULL),
(322, 11, 1, '2026-01-20', '2026-01-20 08:00:00', '2026-01-20 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(323, 14, 1, '2026-01-20', '2026-01-20 08:00:00', '2026-01-20 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(324, 1, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(325, 2, 1, '2026-02-05', '2026-02-05 08:40:00', '2026-02-05 17:00:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 8.33, 8.00, 0.33, 1, 1, 1, 40, 0, 480, NULL),
(326, 3, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:30:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.50, 8.00, 1.50, 1, 1, 1, 0, 0, 480, NULL),
(327, 13, 2, '2026-02-05', NULL, NULL, 'SYSTEM', 'ON_LEAVE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 0.00, 0.00, 0.00, 1, 1, 1, 0, 0, 360, NULL),
(328, 6, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(329, 7, 1, '2026-02-05', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 0.00, 0.00, 0.00, 1, 1, 1, 0, 0, 480, NULL),
(330, 8, 1, '2026-02-05', '2026-02-05 08:10:00', '2026-02-05 17:00:00', 'QR', 'LATE', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 8.83, 8.00, 0.83, 1, 1, 1, 10, 0, 480, NULL),
(331, 9, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(332, 10, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(333, 11, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:30:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.50, 8.00, 1.50, 1, 1, 1, 0, 0, 480, NULL),
(334, 14, 1, '2026-02-05', '2026-02-05 08:00:00', '2026-02-05 17:00:00', 'QR', 'PRESENT', NULL, 1, 11, NULL, '2026-04-06 04:11:11', '2026-04-05 20:11:11', '2026-04-05 20:11:11', 9.00, 8.00, 1.00, 1, 1, 1, 0, 0, 480, NULL),
(335, 4, NULL, '2026-04-06', '2026-04-06 05:14:40', NULL, 'QR', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-04-05 21:14:40', '2026-04-05 21:14:40', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(336, 5, NULL, '2026-04-06', '2026-04-06 05:16:31', NULL, 'QR', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-04-05 21:16:31', '2026-04-05 21:16:31', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(337, 11, NULL, '2026-04-06', '2026-04-06 05:19:14', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-04-05 21:19:14', '2026-04-05 21:19:14', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(338, 3, 7, '2026-04-06', '2026-04-06 12:38:54', NULL, 'QR', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-04-06 04:38:54', '2026-04-06 04:38:54', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(339, 1, NULL, '2026-03-13', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(340, 1, NULL, '2026-03-11', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(341, 1, NULL, '2026-03-24', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(342, 1, NULL, '2026-03-10', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(343, 1, NULL, '2026-04-04', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(344, 1, NULL, '2026-03-23', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(345, 2, NULL, '2026-03-17', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(346, 2, NULL, '2026-04-04', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(347, 2, NULL, '2026-03-13', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(348, 2, NULL, '2026-03-27', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(349, 2, NULL, '2026-03-31', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(350, 2, NULL, '2026-03-14', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(351, 3, NULL, '2026-03-19', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(352, 3, NULL, '2026-03-18', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(353, 3, NULL, '2026-03-31', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(354, 3, NULL, '2026-04-02', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(355, 3, NULL, '2026-03-12', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(356, 3, NULL, '2026-03-11', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(357, 4, NULL, '2026-03-24', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(358, 4, NULL, '2026-03-15', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(359, 4, NULL, '2026-04-05', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(360, 4, NULL, '2026-03-12', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(361, 4, NULL, '2026-04-03', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(362, 4, NULL, '2026-03-08', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(363, 4, NULL, '2026-04-04', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(364, 5, NULL, '2026-03-29', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(365, 5, NULL, '2026-03-16', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(366, 5, NULL, '2026-03-08', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(367, 5, NULL, '2026-04-05', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(368, 5, NULL, '2026-03-15', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(369, 5, NULL, '2026-04-04', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(370, 6, NULL, '2026-03-11', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(371, 6, NULL, '2026-03-25', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(372, 6, NULL, '2026-03-22', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(373, 6, NULL, '2026-03-09', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(374, 6, NULL, '2026-03-14', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(375, 6, NULL, '2026-04-02', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(376, 7, NULL, '2026-03-26', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(377, 7, NULL, '2026-03-31', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(378, 7, NULL, '2026-03-10', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(379, 7, NULL, '2026-03-21', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(380, 7, NULL, '2026-03-14', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(381, 7, NULL, '2026-03-13', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(382, 8, NULL, '2026-03-24', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(383, 8, NULL, '2026-03-27', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(384, 8, NULL, '2026-04-03', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(385, 8, NULL, '2026-04-04', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(386, 8, NULL, '2026-03-16', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(387, 8, NULL, '2026-03-22', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(388, 9, NULL, '2026-03-22', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(389, 9, NULL, '2026-03-26', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(390, 9, NULL, '2026-03-08', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(391, 9, NULL, '2026-03-14', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(392, 9, NULL, '2026-03-09', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(393, 9, NULL, '2026-03-12', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(394, 9, NULL, '2026-03-24', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(395, 10, NULL, '2026-03-30', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(396, 10, NULL, '2026-03-10', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(397, 10, NULL, '2026-03-24', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(398, 10, NULL, '2026-03-12', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(399, 10, NULL, '2026-03-17', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(400, 10, NULL, '2026-03-22', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(401, 10, NULL, '2026-03-27', NULL, NULL, 'MANUAL', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-04-07 15:32:47', '2026-04-07 15:32:47', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(402, 1, NULL, '2026-07-21', '2026-07-21 15:07:35', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-07-21 07:07:35', '2026-07-21 07:07:35', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(403, 1, NULL, '2026-07-31', '2026-07-31 16:50:07', NULL, 'QR', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-07-31 08:50:07', '2026-07-31 08:50:07', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(417, 42, NULL, '2026-07-31', '2026-07-31 18:46:44', NULL, 'QR', 'LATE', NULL, 0, NULL, NULL, NULL, '2026-07-31 10:46:44', '2026-07-31 10:46:44', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(418, 11, NULL, '2026-07-31', '2026-07-31 18:51:08', NULL, 'QR', 'LATE', NULL, 0, NULL, NULL, NULL, '2026-07-31 10:51:08', '2026-07-31 10:51:08', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(419, 11, NULL, '2026-08-01', '2026-08-01 10:33:46', NULL, 'QR', 'LATE', NULL, 0, NULL, NULL, NULL, '2026-08-01 02:33:46', '2026-08-01 02:33:46', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(420, 14, NULL, '2026-08-01', '2026-08-01 10:41:38', NULL, 'QR', 'LATE', NULL, 0, NULL, NULL, NULL, '2026-08-01 02:41:38', '2026-08-01 02:41:38', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(421, 39, NULL, '2026-08-01', '2026-08-01 11:41:52', NULL, 'QR', 'LATE', NULL, 0, NULL, NULL, NULL, '2026-08-01 03:41:52', '2026-08-01 03:41:52', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(422, 39, NULL, '2025-01-01', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-08-03 08:52:28', '2026-08-03 08:52:28', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(423, 5, NULL, '2025-01-01', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-08-03 08:52:28', '2026-08-03 08:52:28', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(424, 40, NULL, '2026-08-03', '2026-08-03 19:12:56', '2026-08-03 17:00:00', 'QR', 'LATE', NULL, 0, NULL, NULL, NULL, '2026-08-03 11:12:56', '2026-08-03 13:43:53', -2.22, -2.22, 0.00, 1, 1, 1, 792, 0, 0, NULL),
(425, 39, NULL, '2026-08-03', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-08-03 12:07:01', '2026-08-03 13:43:53', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(426, 11, NULL, '2026-08-03', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-08-03 12:07:01', '2026-08-03 13:43:54', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(427, 42, NULL, '2026-08-03', NULL, NULL, 'SYSTEM', 'ABSENT', NULL, 0, NULL, NULL, NULL, '2026-08-03 12:07:01', '2026-08-03 13:43:54', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ta_attendance_backup`
--

CREATE TABLE `ta_attendance_backup` (
  `attendance_id` int(11) NOT NULL DEFAULT 0,
  `employee_id` varchar(50) DEFAULT NULL,
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
  `is_within_shift_hours` tinyint(1) DEFAULT 1,
  `late_minutes` int(11) DEFAULT 0 COMMENT 'Number of minutes employee was late (0 if on time)',
  `early_out_minutes` int(11) DEFAULT 0 COMMENT 'Number of minutes employee left early (0 if on time)',
  `shift_minutes` int(11) DEFAULT 0 COMMENT 'Expected shift duration in minutes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_attendance_metrics`
--

CREATE TABLE `ta_attendance_metrics` (
  `metric_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month_year` varchar(7) NOT NULL,
  `attendance_rate` decimal(5,2) DEFAULT 0.00,
  `absence_rate` decimal(5,2) DEFAULT 0.00,
  `punctuality_score` decimal(5,2) DEFAULT 100.00,
  `overtime_frequency_rating` varchar(20) DEFAULT NULL,
  `overall_performance_score` decimal(5,2) DEFAULT 0.00,
  `total_present_days` int(11) DEFAULT 0,
  `total_absent_days` int(11) DEFAULT 0,
  `total_late_incidents` int(11) DEFAULT 0,
  `total_overtime_hours` decimal(8,2) DEFAULT 0.00,
  `status_summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`status_summary`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_attendance_tokens`
--

CREATE TABLE `ta_attendance_tokens` (
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
-- Dumping data for table `ta_attendance_tokens`
--

INSERT INTO `ta_attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(1, '043148d84415dd582b66a67aa2afcb3d3b168262a2a4df10a3c95a65342cd336', 3, '2026-03-18', '2026-03-18 00:08:54', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:07:54'),
(2, '7e2a4f39745c191d39b4d6e32984e646bf86f0d1af7578c0b0da05cf81c93e2a', 3, '2026-03-18', '2026-03-18 00:09:24', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:08:24'),
(3, 'a0b446e2246ddfce94c6948b2025ef93b43946341765db1959bdbe304d8d3d9c', 3, '2026-03-18', '2026-03-18 00:09:55', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:08:55'),
(4, '4f1ee473d39eb6dad7f8c6468f53694a6b89e6544b706113c8638dc40ab6e552', 3, '2026-03-18', '2026-03-18 00:10:26', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:09:26'),
(5, 'b387c4baf5bca16d9bb818c44e0e1ef712099ff2d8bd35ce396fe3adae6b181a', 3, '2026-03-18', '2026-03-18 00:11:26', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:10:26'),
(6, '46015fc415a8c0c3772f6e04bacecbc150e90b9657a5d7e9a456f2a545b8475d', 3, '2026-03-18', '2026-03-18 00:12:17', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:11:17'),
(7, 'c959c440222444b7e127a5596c93282c465f2b00f095aa7b1288cdeaf048925b', 3, '2026-03-18', '2026-03-18 00:12:48', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:11:48'),
(8, 'ae99b0b831594c8484df6bc320a475ddffceffc27d441b378529bcf18442acf7', 3, '2026-03-18', '2026-03-18 00:13:19', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:12:19'),
(9, '63af9e07a63cfc0c53ee37b156c3a33cb9b805dfee3f3c5104dcb49448054ac0', 3, '2026-03-18', '2026-03-18 00:14:18', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:13:18'),
(10, 'b20eb116fe63885f97fc8f4b4903c37bbaa3704d625061bf42321900f347a900', 3, '2026-03-18', '2026-03-18 00:14:57', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:13:57'),
(11, 'a03743d996ccc4090ea88141e75a185a548a45f1924ef56ac2202edefc9f4862', 3, '2026-03-18', '2026-03-18 00:15:28', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:14:28'),
(12, 'cd2e542a096725134eaf8f0e66f855202e5268493f781fd4d63516368a1f36d8', 3, '2026-03-18', '2026-03-18 00:16:04', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:15:04'),
(13, '90966563747b1c4dd9cd8ce201bf2a8d75e85fe98261be7e4e01f723cabce0b7', 3, '2026-03-18', '2026-03-18 00:17:03', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:16:03'),
(14, 'abd1e5752193ef8a4a1da6a0594473c5f930b5140381cf68fd8c18d4cd5c9ec4', 3, '2026-03-18', '2026-03-18 00:17:37', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:16:37'),
(15, '28c3a17452c451b1d57997b9551f45044a66b43d49897a99604a5a24bf265ea7', 3, '2026-03-18', '2026-03-18 00:18:09', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:17:09'),
(16, 'c8593078edd352ee35759965a7e9ef31891cb22c63e0c08aaf02b090ec999de0', 3, '2026-03-18', '2026-03-18 00:19:07', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:18:07'),
(17, '5a70d4d93e035aa4b5d3c52af4fb6d7ad70901f5f04d4e360ad00507d5a749ff', 3, '2026-03-18', '2026-03-18 00:19:56', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:18:56'),
(18, '817450609cf23c2f0a1cb6e3759821cc0f53ec7798f7e033cae97c717c512d1e', 3, '2026-03-18', '2026-03-18 00:20:27', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:19:27'),
(19, '3fbda365fc2b5724d45da317c914f3d4b3e7347aff9ac2029ccb91950ad90983', 3, '2026-03-18', '2026-03-18 00:21:22', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:20:22'),
(20, 'ee774c3df55f5dc2cdb340cfcc53423389fb803c1adf7b5179ed81528e3c1826', 3, '2026-03-18', '2026-03-18 00:22:17', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:21:17'),
(21, '4c55864b24a512c4ce8f355d8bddf12324e85a1813c65a4c05bb8211fb3103cc', 3, '2026-03-18', '2026-03-18 00:23:16', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:22:16'),
(22, 'b2ad857e32e14b8d9ed3bf313feb21f6df7bdee5b648881a97b3d48afdd57372', 3, '2026-03-18', '2026-03-18 00:23:52', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:22:52'),
(23, '45d06822fec3fb23106f9859d1a4e5f93c4f0ee23cfd26af3bfb109ab2aa48fe', 3, '2026-03-18', '2026-03-18 00:24:24', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:23:24'),
(24, 'ec9c6e1273bb0fbe3689424fdb8de216f767328d8086c57dadaa3a6ff9009d6b', 3, '2026-03-18', '2026-03-18 00:25:03', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:24:03'),
(25, '389701b71bddc93a6ab1b2a68e68cb4c22575f89fd40f822087558be7e0716cf', 3, '2026-03-18', '2026-03-18 00:25:45', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:24:45'),
(26, '079997bb11794ec6a9289a6142a81ad0dfb6f047290d39a7bbefb3a8ea5bb83c', 3, '2026-03-18', '2026-03-18 00:26:21', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:25:21'),
(27, '8058d8928de8fecfe2c6117392fc3d40f4082c9a1f85f0b7a80f627c2afe774c', 3, '2026-03-18', '2026-03-18 00:27:16', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:26:16'),
(28, '88bd721123be1b36c070ac5ebcfa4e1adcf659a4ed09ee2de6341c53ceceff73', 3, '2026-03-18', '2026-03-18 00:27:54', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:26:54'),
(29, '148232cb06ef4b88c1b46e40a754024de7473f6b7a8676085606a9572ab8dc3d', 3, '2026-03-18', '2026-03-18 00:28:50', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:27:50'),
(30, '7e14b4498b98660310e23b6c6b504c22dbd3d40ad566c800d61934794a42a21e', 3, '2026-03-18', '2026-03-18 00:29:21', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:28:21'),
(31, '3f73e0b5b9dcea4b6dfbd069a6118b4b92b8853c865d9924ef647f8faec28bdd', 3, '2026-03-18', '2026-03-18 00:30:02', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:29:02'),
(32, '606d45a38ce5edc63e0236e9d470833ed40554633695e8b1e983f492410ca7e7', 3, '2026-03-18', '2026-03-18 00:30:36', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:29:36'),
(33, '65acdd67c3ce519f5b502efd2b9e44c196c99a51c51ac9c3f0285dbb17aca783', 3, '2026-03-18', '2026-03-18 00:31:16', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:30:16'),
(34, '81d6601704e5faeca9fbcac7cf5349d96b0d78391265e681b5a6db5a396e1553', 3, '2026-03-18', '2026-03-18 00:32:13', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:31:13'),
(35, 'd52522d9328fc303a28cbfb6b1aa564a41a08dde6453496eae5db449e5270d15', 3, '2026-03-18', '2026-03-18 00:33:00', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:32:00'),
(36, '708bbc2e412acc78707d3c9c2a028a11ddfffe1e1f90984fa082d127ce8bf437', 3, '2026-03-18', '2026-03-18 00:33:59', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:32:59'),
(37, 'b0d00d1bbbf71f68fc9bb92605e734ea1491af6e070029ad823d079fb6f07d86', 3, '2026-03-18', '2026-03-18 00:34:49', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:33:49'),
(38, '32bf044c18ea88d4aa85acfca43062fc4b4e76b146698d02641875707757d31c', 3, '2026-03-18', '2026-03-18 00:35:20', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:34:20'),
(39, 'f0f36f196cdb3c0da5b4ab4e402f1ba81f6fea032167331b37fba9b15074054a', 3, '2026-03-18', '2026-03-18 00:36:11', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:35:11'),
(40, '19e3f15d410f6b10d8517b63c4bc561116f03dbe9fb3668c9240d89b0e223b25', 3, '2026-03-18', '2026-03-18 00:36:52', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:35:52'),
(41, 'eaaad30ce12189143d1c7052ea295aa6be46dfb9b08f4e18ac7053ac0d4e3c1c', 3, '2026-03-18', '2026-03-18 00:37:24', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:36:24'),
(42, '816b523aa6bb9d2ec016f99a22975187c5879eef9f6507b13ca8fbf1c8b20d14', 3, '2026-03-18', '2026-03-18 00:37:57', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:36:57'),
(43, '554f31ae2f3ac7d776c93793d1c2199cf738e3bcaf7d80e58323987b71a79d2a', 3, '2026-03-18', '2026-03-18 00:38:30', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:37:30'),
(44, '3ce3e458dd82430da3efa58525dad9c40fed0bd8967ecc92811f858a7446788b', 3, '2026-03-18', '2026-03-18 00:39:04', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:38:04'),
(45, '2220695d8e8f2d9bbc6ef6ec94da8482a2a08117723399928fd772e2453f7213', 3, '2026-03-18', '2026-03-18 00:40:04', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:39:04'),
(46, '849ca8046723a6d5dbf3da6aba1fba4b5442942388b438bc1bb7e7becbe80cdf', 3, '2026-03-18', '2026-03-18 00:40:35', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:39:35'),
(47, 'eab323bd3c93728af4a8f3c86d6359bb79ecf0889deeffffdd6fb1bb7837c758', 3, '2026-03-18', '2026-03-18 00:41:08', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:40:08'),
(48, 'a8d66b3f8132487f6b5c74a78c731f608ec9becab5302c436030e54a6ecc2e03', 3, '2026-03-18', '2026-03-18 00:42:08', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:41:08'),
(49, 'bab6b9ed881db0c98b00088cf8fe9d12301269040876ece48a14a79ba3b16aed', 3, '2026-03-18', '2026-03-18 00:42:51', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:41:51'),
(50, 'ea61c00946c37210004b5c8d7e0b74f43adc375b2e38f202f51a4ced33525c16', 3, '2026-03-18', '2026-03-18 00:43:23', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:42:23'),
(51, 'fca673092f98c1dc72ce6cb3e243521046fb872a9f7d008fdb9dc2309d007dcb', 3, '2026-03-18', '2026-03-18 00:44:11', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:43:11'),
(52, 'f1030c4458231f2bc6ca8b7619938a90689d67932d2bb1e04cc8b6a0ef2764f2', 3, '2026-03-18', '2026-03-18 00:45:10', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:44:10'),
(53, '0f75730acb44e136d4700fdef512b3dce74aee8646022e86d3452d68b342ca3c', 3, '2026-03-18', '2026-03-18 00:46:09', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:45:09'),
(54, 'e719a530d4d443d2924d4751d3dc9acb02d349095291928f136ef6793d3cb5aa', 3, '2026-03-18', '2026-03-18 00:46:40', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:45:40'),
(55, '2797f0fe045e032d524048bd78c027d6c8966af7a1cecd6094c1c7ec9763f9ac', 3, '2026-03-18', '2026-03-18 00:47:20', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:46:20'),
(56, '0be2d069dc08c00af54ced1d9c56b72e521ac3644f14a240c445f84779c62f3b', 3, '2026-03-18', '2026-03-18 00:48:11', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:47:11'),
(57, '0e875e312c530123d68d19ca891635dcf5c910a23b7d03e81ad43c797e952ba4', 3, '2026-03-18', '2026-03-18 00:48:42', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:47:42'),
(58, '8957f1b9a79454559a1fa54936454031a2d822cdd2c4b2dd18f04bc3d05d07ca', 3, '2026-03-18', '2026-03-18 00:49:41', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:48:41'),
(59, 'd7362d3f77aa71a48eea0c24f7e3ef2a3e2da649973b01a89341e430b99ceefa', 3, '2026-03-18', '2026-03-18 00:50:14', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:49:14'),
(60, '3ff7acd76973de4bc5b08e0a454c0d61c619a4a0b428b21a7b2951cde999b536', 3, '2026-03-18', '2026-03-18 00:50:45', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:49:45'),
(61, 'bae6bcb655d3666966cc0da543f21e738d00cc05310a5e7387fc21011ebab58c', 3, '2026-03-18', '2026-03-18 00:51:36', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:50:36'),
(62, 'f6c739b9e79aad8459fce81444bb9dba502d03fab08a0ffb4f9166ac27a131b2', 3, '2026-03-18', '2026-03-18 00:52:33', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:51:33'),
(63, 'adf9f969d38198ffead315d1b1342c6717707ff911237a5ee95b46c0a07b3797', 3, '2026-03-18', '2026-03-18 00:53:08', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:52:08'),
(64, 'a85b801200f88314fc82daf6596fb69320be51273968469dedfbf6e872a66368', 3, '2026-03-18', '2026-03-18 00:53:48', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:52:48'),
(65, '487ed6a5daf343889c8880ec3f32c8a790897b7993432f43d8ab30553a16ab5b', 3, '2026-03-18', '2026-03-18 00:54:47', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:53:47'),
(66, 'bc992edd765524c88922f985b70cd742ab9db1ce35a96969e8950dbc177cccbc', 3, '2026-03-18', '2026-03-18 00:55:47', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:54:47'),
(67, '36abd2c4cccc583c1e852b0abbc843302a82fff48b42d4008d271a51f9623c0c', 3, '2026-03-18', '2026-03-18 00:56:19', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:55:19'),
(68, '5b6837587ba1c0ca1a76d3101d05bfedda5ea56b152db8c0b88f07aee32f8c31', 3, '2026-03-18', '2026-03-18 00:57:18', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:56:18'),
(69, 'fa65f42d3d34384866f1c21c12d957096d5794c205147b2e2824df4806a201cd', 3, '2026-03-18', '2026-03-18 00:58:11', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:57:11'),
(70, 'a150e144de4404fdc10524255436c430359c7ae51e5ae4a4951b565b7d687b3c', 3, '2026-03-18', '2026-03-18 00:58:42', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:57:42'),
(71, 'e09394827617aa18827e39604f7cbd73e313deef1845aed77d3a45d675e6f002', 3, '2026-03-18', '2026-03-18 00:59:26', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:58:26'),
(72, '8199ef0966c64faa4695ce443bd5bd3a1d04a43846eabb94a82d2599738bd83d', 3, '2026-03-18', '2026-03-18 01:00:18', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:59:18'),
(73, 'cbe93704c2aeffeec28b827e1e78dc35786a6b03ca58780438934bf9e0f53589', 3, '2026-03-18', '2026-03-18 01:00:49', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:59:49'),
(74, '5297af9f4c68b7060b147c9e14390254eea02b0b1b45a28f89a8ea43ba4425e0', 3, '2026-03-18', '2026-03-18 01:01:20', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:00:20'),
(75, 'eebae7984f85fd3d62ea27ee42784a921d9584f9cbec5b20f7356a70e31c4912', 3, '2026-03-18', '2026-03-18 01:02:20', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:01:20'),
(76, 'e4963ea6f2b049684b02a0644e4691dbecdc3f8c8b1b42063d8578e7e183f7d1', 3, '2026-03-18', '2026-03-18 01:02:55', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:01:55'),
(77, '496dcba3ee591d65292e418ad60f490ac9b8f7a348f378198c14df49ebd292e6', 3, '2026-03-18', '2026-03-18 01:03:30', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:02:30'),
(78, '3872329738e7fb2bf8e62d717f17543274fc50168fcc75bea77908737b581051', 3, '2026-03-18', '2026-03-18 01:04:02', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:03:02'),
(79, '7ccdb0ca86fd28924f582fa5250a5b83334d757ddd86552a17d029ad2a08fdbc', 3, '2026-03-18', '2026-03-18 01:04:33', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:03:33'),
(80, 'f6c0e0d76efbe49ae28584ac93aba3125d1304ee1ebad55875b5c36036ad9d00', 3, '2026-03-18', '2026-03-18 01:05:06', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:04:06'),
(81, '27ece0d44190de227b11cc2dc9b1477693fea1786ea08934eff98c3671dc4e5d', 3, '2026-03-18', '2026-03-18 01:05:37', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:04:37'),
(82, 'b0fccbbf167745fccd4e7d6a0b6842894ff3e807c4707c83853c0d56f01668e4', 3, '2026-03-18', '2026-03-18 01:06:12', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:05:12'),
(83, 'eadd49baa36557b3eb7f78ee252d292b9fe2d873cfe126cb5ce674ffbca57621', 3, '2026-03-18', '2026-03-18 01:07:12', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:06:12'),
(84, 'b8ac1741ec0e4b52d74f6449aa9b4808f77d55032565dd78974fd2e91abb0e1c', 3, '2026-03-18', '2026-03-18 01:07:43', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:06:43'),
(85, 'cdc837f030d3a560b33fb10d1521fec2c5ea2d20e060821f67f41884990c214e', 3, '2026-03-18', '2026-03-18 01:08:16', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:07:16'),
(86, 'bc243312bf9c9f108f7f1c13d18a6007f8815ace4e7710ca12b0028d8c25066e', 3, '2026-03-18', '2026-03-18 01:08:47', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:07:47'),
(87, 'dddeb7ca8d7bd038d2cc4cbe9726e9d205f0a346258d79d8e5fff1ca804bedaa', 3, '2026-03-18', '2026-03-18 01:09:21', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:08:21'),
(88, '3d93f7f6eb5805200bfcd11f4ad371efbbfe370ad77dbf82a03777607d66a35e', 3, '2026-03-18', '2026-03-18 01:09:53', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:08:53'),
(89, 'aba942968a8146f8a6b10c987866088ae26064e55f6757c0c91efc9ae055b839', 3, '2026-03-18', '2026-03-18 01:10:24', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:09:24'),
(90, '52170170ddd941fb8ef8039bf2662074785f72266fefeb468d127c1ed7a80dc7', 3, '2026-03-18', '2026-03-18 01:10:55', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:09:55'),
(91, 'cfd2b6a63472471391af5bfb6f37f7457b4614a819afd0a45ee7efeef08f7338', 3, '2026-03-18', '2026-03-18 01:11:26', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:10:26'),
(92, 'b35c8eb3ebd644c316207198a1ff765d9dcf50a8ee9e233e08bc0e5aa6d2f65d', 3, '2026-03-18', '2026-03-18 01:11:57', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:10:57'),
(93, '5eef8d96dac1ce4d7a50c73fff63da36a509a5bccf9dc713e6fd252cf23bda40', 3, '2026-03-18', '2026-03-18 01:12:28', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:11:28'),
(94, '1c03546b2fc4a427a54b18405d28c87eeaf97f5de4f65f9c290d2b2de1c21cea', 3, '2026-03-18', '2026-03-18 01:13:00', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:12:00'),
(95, '70e836e15b1516b2054617ee43a6c783caae6ed1209fd327ebc818b4e9b8c5a2', 3, '2026-03-18', '2026-03-18 01:13:32', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:12:32'),
(96, '64f523be72d9ab0a595c573e96fe7a50bd2e4c223a4d49040a9828d3b8a5b430', 3, '2026-03-18', '2026-03-18 01:14:03', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:13:03'),
(97, 'd1d42f89fae652ba53f7e389bad2630820d303042e6600fb6c162b7a4f29d3e5', 3, '2026-03-18', '2026-03-18 01:14:12', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:13:12'),
(98, '2690565381a550ef952a073877ffe7e53db4b712d562ccadc2ab2aa4914daf25', 3, '2026-03-18', '2026-03-18 01:14:43', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:13:43'),
(99, '89dc4f46ab74cd399d64a741a1ea038bb4299a8b05eb35bdabaf8a351aab40e5', 3, '2026-03-18', '2026-03-18 01:15:14', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:14:14'),
(100, '657a11b148d2f7841e97c492a66893a35b0adb5c666dac7c346a855a7aad89aa', 3, '2026-03-18', '2026-03-18 01:15:45', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:14:45'),
(101, 'ed7665d201e5a5981e5136992b62f67ea00ca0a621f00e37a656d51af0eda9a5', 3, '2026-03-18', '2026-03-18 01:16:16', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:15:16'),
(102, '3df0762536c2166ebbdd75abb439514abcd27bfb8b1bd099c45bcd82febbc4ec', 3, '2026-03-18', '2026-03-18 01:16:47', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:15:47'),
(103, '5c1c12a169e10e9595aaae620ab11ef84d298a4316348406e6ef3bbec38fe831', 3, '2026-03-18', '2026-03-18 01:17:18', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:16:18'),
(104, '0c6308bd9cfaa7fcd93f89abbfe2ea5c04db0c11013b576abddaef244d0db4da', 3, '2026-03-18', '2026-03-18 01:17:49', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:16:49'),
(105, 'dfecd08b8b6617a13647c2ec1a519481dcf05ad35f3b5291551c331d6b156d48', 3, '2026-03-18', '2026-03-18 01:18:20', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:17:20'),
(106, '8379e5bd31d8485c53e3f55296ec9ef24c720d30cef90ee7eec3b7b49022a861', 3, '2026-03-18', '2026-03-18 01:18:51', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:17:51'),
(107, '33b2b987ac24efab1c2810a47be9e15cfeb645f33389a74c020c7268b4e2b92e', 3, '2026-03-18', '2026-03-18 01:19:23', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:18:23'),
(108, 'bc7c1d3a1d7345afeb404f721e6c5e68114cc47a0d1b484e7cd918f80030150e', 3, '2026-03-18', '2026-03-18 01:19:54', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:18:54'),
(109, '85f80f29a2447ae3519eec1cb15708b491f973b3e68d7c40d461bad67d6bb099', 3, '2026-03-18', '2026-03-18 01:20:25', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:19:25'),
(110, '1c2e2fa0f23ac881cf58fa67e92ee145edf64f121715848e74577f45e7a91ada', 3, '2026-03-18', '2026-03-18 01:20:56', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:19:56'),
(111, '1e1677f84d693040a6462e0d090c1a9cdec4ad623a06e1ffe2bdab9e27d8de6e', 3, '2026-03-18', '2026-03-18 01:21:27', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:20:27'),
(112, '038600c1973033937118525ce7e795518f5bf2cd6b89bf4a8578efa54281bec7', 3, '2026-03-18', '2026-03-18 01:21:58', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:20:58'),
(113, 'b3b4736c791245f3d133f2ca6ced00a41d40fa5043f5b140f874321c8f274b48', 3, '2026-03-18', '2026-03-18 01:22:29', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:21:29'),
(114, 'f588d754c4f74b642f0ecfa5f76b1bca863498fcaec114ec8ed582f32488549a', 3, '2026-03-18', '2026-03-18 01:23:00', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:22:00'),
(115, '91ed9b26d4758ddd1958ca59f5e506ce18eaf9b619f86cf11285b99fad0971e4', 3, '2026-03-18', '2026-03-18 01:23:32', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:22:32'),
(116, '811f92f2f0779cc8caf0c8b35ae9db1ea77dddd8b6183e71fddf6b8022ae89c1', 3, '2026-03-18', '2026-03-18 01:24:31', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:23:31'),
(117, '87218a896eef791df5817bdc2aff5f4a67978795faeefbe0b5f04d0d291973b0', 3, '2026-03-18', '2026-03-18 01:25:31', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:24:31'),
(118, '8af6eab8cb359d28453a911acf196bdffcc2f8199e46cdbcc8f24eeacdb60496', 3, '2026-03-18', '2026-03-18 01:26:02', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:25:02'),
(119, '1a6c3ae7209fca85e55049217c86f9f3fb3f1d92659e099e210e9a1a74209aa8', 3, '2026-03-18', '2026-03-18 01:26:33', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:25:33'),
(120, '667a129c9e92e3e7629c270cb51fab42ad666fe28af15b49fd46be859c3f81aa', 3, '2026-03-18', '2026-03-18 01:27:04', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:26:04'),
(121, '2f6f7308fc2f4229f246db4cf5fa0b894eef63c473638561544409aaa1737de4', 3, '2026-03-18', '2026-03-18 01:27:35', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:26:35'),
(122, '949c5446ff5f9ad7aa2e0d043ba095210cbe0a64d697e053e5f0ee7a23522f89', 3, '2026-03-18', '2026-03-18 01:28:06', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:27:06'),
(123, '4749e0ac198dda231d32f9bfdc3c43c9bb5170ab67f0d31871d35f97e04bd531', 3, '2026-03-18', '2026-03-18 01:28:39', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:27:39'),
(124, '926383f919fa5ff591f4c3feb77cf3f09edb98b09fc1d4dbd4e16cdcd6f980e8', 3, '2026-03-18', '2026-03-18 01:29:10', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:28:10'),
(125, '2156c3a3ff8a3ecb1b7fed15ce680c625ee4890c9582e03bb0db2a9e0fb4a50e', 3, '2026-03-18', '2026-03-18 01:29:46', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:28:46'),
(126, 'e88c8fdfb77758b275a15aef2973e5a11b91d5f658945d71a9cce706d9a18086', 3, '2026-03-18', '2026-03-18 01:30:19', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:29:19'),
(127, 'dde9be81afe6a6c6e83ff5c88baf7cc4090c77716e01373f2acbfa0c694d5932', 3, '2026-03-18', '2026-03-18 01:30:50', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:29:50'),
(128, '72905786c6c5dbc306729f1e2574675f7904c84018dc3a85becf54f88d335493', 3, '2026-03-18', '2026-03-18 01:31:21', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:30:21'),
(129, 'f3c9fdd33acde403a65cd8d05f13dcff391a3de5948b496dbe659ab293ba14c0', 3, '2026-03-18', '2026-03-18 01:31:52', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:30:52'),
(130, '895fa8452a55ab8712c99992b4d889727894ce1e1550a65628f3375b0d40d41c', 3, '2026-03-18', '2026-03-18 01:32:23', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:31:23'),
(131, 'a920f6e13f8516494c7821556cb9015a5b9f8fc1fa602f081e7550d7f71ca225', 3, '2026-03-18', '2026-03-18 01:34:23', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:33:23'),
(132, '7ff0cf5ae02f1ce10b24c143e6af6b7c123598db70f1e07dfa6e49575abaa4a9', 3, '2026-03-18', '2026-03-18 01:34:53', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:33:53'),
(133, '14e2630f3a0e152ae238d7498f7d9346785d3d3ee89985202a95da800f95c6b9', 3, '2026-03-18', '2026-03-18 01:35:24', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:34:24'),
(134, 'b8d072c4e133253341ca62222b860b3882f7b58e2739284f3f7b2b5f3576edf5', 3, '2026-03-18', '2026-03-18 01:35:54', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:34:54'),
(135, '112b485e2f8e6a1cb814b95fabd3da689d58c531b32ab96500c42d9604201647', 3, '2026-03-18', '2026-03-18 01:36:31', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:35:31'),
(136, 'ac90bd80e3f25ad4e7d6e5ff25068b64440ba6d9b21382639c067470c77fc492', 3, '2026-03-18', '2026-03-18 01:37:01', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:36:01'),
(137, '5ef123e03d684138f763139ab79947b78881ffbe5b7c1af920c243091920eddb', 3, '2026-03-18', '2026-03-18 01:37:31', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:36:31'),
(138, '205a07cbe4f93137a76c0f13afbbf0c12a71a391d2e49cd8d425690f62c9f53f', 3, '2026-03-18', '2026-03-18 01:38:01', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:37:01'),
(139, 'f54479d2b735171cf14fda0f1f8892dea7eeee7bbea3a44f279cd41f79e67c04', 3, '2026-03-18', '2026-03-18 01:38:31', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:37:31'),
(140, '4e9a01605e9e749ef04abc2575bfef6b97f8001ab9d0598ba29dbda41c132a22', 3, '2026-03-18', '2026-03-18 01:39:02', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:38:02'),
(141, '13a2ac126f095209a09375e2a934b70ed5bd4fa1b6e3e14f7ac3a5f6032c1b7f', 3, '2026-03-18', '2026-03-18 01:51:07', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:50:07'),
(142, '682468d854b9b2213361fbb68b1c9c47537ca79940de74f0241249760f4d85ad', 3, '2026-03-18', '2026-03-18 01:51:37', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:50:37'),
(143, '87a933540470114374f2356af786d95e93722e367047e276ba3c7c5e991f1991', 3, '2026-03-18', '2026-03-18 01:52:07', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:51:07'),
(144, 'a933abe3b37512fc1e1ad9495d3d048d05e1b2d04a4a5862e29f388bec60d098', 3, '2026-03-18', '2026-03-18 01:52:37', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:51:37'),
(145, 'e7cf029bb3d90d6d5fc97aad9d2d7c48ed16bdb6f04497da91483c69a935e194', 3, '2026-03-18', '2026-03-18 01:53:07', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:52:07'),
(146, '10af51399395ccb3cdbc58241606ce66b10d2cb826d5a80785a18d077d3f3c35', 3, '2026-03-18', '2026-03-18 01:53:37', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:52:37'),
(147, '2d70f01d3e6548710af985c6b32598764b35b346b9b575b7a779f90d49740628', 3, '2026-03-18', '2026-03-18 01:54:08', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:53:08'),
(148, '1074f4e4284e665b5d68232f8d4193bad93961aca53cc43e802349a44eb5ffc7', 3, '2026-03-18', '2026-03-18 01:54:39', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:53:39'),
(149, 'ab0473edef4abd3f98b87e49d8dd1014f556ada1b2c7e158c3a4bf31860a6643', 3, '2026-03-18', '2026-03-18 01:55:11', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:54:11'),
(150, 'b802fcaaf877fb71da92e733eb2ee7163dc8bf9602843872d910b0c57bb9a627', 3, '2026-03-18', '2026-03-18 01:55:42', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:54:42'),
(151, 'fada8e3be56282d6d8640abda2dab8cba88d4570ef417e0ea7542beef9bb17f3', 3, '2026-03-18', '2026-03-18 01:56:13', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:55:13'),
(152, 'e125a38e2cb3d743a6788c1c88cb7dec4d9fdeba674134ae72e396ce10e6a696', 3, '2026-03-18', '2026-03-18 01:56:44', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:55:44'),
(153, '8bfbdc36c41859a41b93b9ce7f90fd85b2079b7328d584b805be5869fa4bc551', 3, '2026-03-18', '2026-03-18 01:57:15', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:56:15'),
(154, '3d4026c6bac445e1f20e8509bd350ef0f2c6f6ab347838a9dc345a3d1fbb276c', 3, '2026-03-18', '2026-03-18 01:57:47', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:56:47'),
(155, '0fbd36e7f6775d424f5416a50dff6b3e21437c02f809583cae8350d3df2f7198', 3, '2026-03-18', '2026-03-18 01:58:18', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:57:18'),
(156, '6c3c6ca6339bd5556080fb63b2c281869de4b8d2bc99da21b6399c00f97092cf', 3, '2026-03-18', '2026-03-18 01:58:49', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:57:49'),
(157, '28dc0430dc7338b87e20c6c94658c8a936fceef8a3e89a2b6fc761b5e6f15d53', 3, '2026-03-18', '2026-03-18 01:59:20', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:58:20'),
(158, '073f96bc07d73ae211bae03239abb766da5e51e1356cb697e4ca09a28063113b', 3, '2026-03-18', '2026-03-18 01:59:51', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:58:51'),
(159, 'c0cd5a256af68e0ab3fe5e93f2d42180a001fb275bf34beeabf8fbf9668d3486', 3, '2026-03-18', '2026-03-18 02:00:22', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:59:22'),
(160, 'b5b2ce87a209fd9213b1f3bed59240b3d8f1be70f14a26b24843e38e9602701c', 3, '2026-03-18', '2026-03-18 02:00:53', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:59:53'),
(161, '852f17c5381dd9cee230301e1a5d06c24035a2f28d11facb233db4550270d95e', 3, '2026-03-18', '2026-03-18 02:01:24', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:00:24'),
(162, '614eb51410854ad466b1188568e6a67ffd2ec10722e9866838dca6a5be85e2d9', 3, '2026-03-18', '2026-03-18 02:01:55', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:00:55'),
(163, '52f81572c28e970e68d5eed53037100f55947c886699f3fc79ecf2684dfb2975', 3, '2026-03-18', '2026-03-18 02:02:26', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:01:26'),
(164, 'dd0fbd7639b05b3e241af279b56cfee2b1ba69d92842860e4620177047307758', 3, '2026-03-18', '2026-03-18 02:02:57', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:01:57'),
(165, 'ce6b19bdfdb9db4b07b4dfc2dbe7f85dadacec5d0312e425dd1ca9c27e43afe0', 3, '2026-03-18', '2026-03-18 02:03:28', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:02:28'),
(166, 'c3cd5bed7421de9be27d49b1f814b4b358bd39cc5eea74d134ac4be4f2050c88', 3, '2026-03-18', '2026-03-18 02:04:01', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:03:01'),
(167, '6c8bac5fe1cf29789289bbd403a46f25d321eda3ba58a42d14ba6637f9fe905e', 3, '2026-03-18', '2026-03-18 02:04:32', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:03:32'),
(168, '690c3679f97c696fba67f6119a5741b368e418a4d269a4d16526ff69806f4987', 3, '2026-03-18', '2026-03-18 02:05:03', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:04:03'),
(169, 'ce55dbd42bc57641943f29492efe3062d779ee496edf79d01dbc37d3321dbd71', 3, '2026-03-18', '2026-03-18 02:05:34', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:04:34'),
(170, '256cab813f1c86c1058aeb0105dd580ed806d62df8edd0c3a5784c0fe38e2fda', 3, '2026-03-18', '2026-03-18 02:06:05', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:05:05'),
(171, 'de9de89ad098059d400e10f6c5aa753390cf3a7c021f8fe92ab014096e8edf5c', 3, '2026-03-18', '2026-03-18 02:06:36', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:05:36'),
(172, 'f65a4daff0998e8013ec49b3668dc7715e93404d3dd3a8a1cc9914ee78d1f22b', 3, '2026-03-18', '2026-03-18 02:07:07', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:06:07'),
(173, '1ee53232dc767cbee51a0482244f1a82e7ac1785506ed043dd0f0482a281d27e', 3, '2026-03-18', '2026-03-18 02:07:38', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:06:38'),
(174, 'bb050367a47d75d5d44e678ab5b0a5f2c1b0974889802fc76f1d49c2ad9c6b39', 3, '2026-03-18', '2026-03-18 02:08:09', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:07:09'),
(175, 'a80502c81d43fb622775a0d68f5d73a3c922aa8030be99fbc355d765f3699e86', 3, '2026-03-18', '2026-03-18 02:08:40', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:07:40'),
(176, '51591be8b406739e29fa7253847d74a90d4c5633395630f84c5fa93b5f5dd961', 3, '2026-03-18', '2026-03-18 02:08:57', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:07:57'),
(177, '37b9cf726168bb97b8d06364496e634e41983c6e591e11f3a2ee948afd5e8126', 3, '2026-03-18', '2026-03-18 02:09:27', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:08:27'),
(178, '09641b6f031b1580b73b8c0d199fd816c3b611176f3589336c40453db7265424', 3, '2026-03-18', '2026-03-18 02:09:58', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:08:58'),
(179, 'b2b30ce6b558d56082e33c6079d25c7a7f006349e9fcfff36dfed81b8e9003f8', 3, '2026-03-18', '2026-03-18 02:10:29', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:09:29'),
(180, 'c0ed5266350eaef1e6f11889086791b53b2911e5a0f13a34360a0bb08f71d333', 3, '2026-03-18', '2026-03-18 02:11:00', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:10:00'),
(181, '35427f1a218a090ed5c801c8b1a0ebc3b3e2c9d6a1a47950cc3aec3638412526', 3, '2026-03-18', '2026-03-18 02:11:31', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:10:31'),
(182, '4cf44df1055ac1eac6d5d4e1380ec9ca94951bb08f92ea638f5f53ae3574e8e4', 3, '2026-03-18', '2026-03-18 02:12:03', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:11:03'),
(183, '4975ea74dbdb4d449383a7292a686f28c1a186701292ff81a390ad3d8d7476c2', 3, '2026-03-18', '2026-03-18 02:12:34', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:11:34'),
(184, '920cfc9d15e3de394d3fc8eaec533d1f15a5e8dd24547c2a530aab06b6513369', 3, '2026-03-18', '2026-03-18 02:13:05', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:12:05'),
(185, '45b2c1e08d103595cb2ed6a88fbdf58d5ef7b05c9d4d31006b60ed82e908837e', 3, '2026-03-18', '2026-03-18 02:13:36', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:12:36'),
(186, '58f6f8aff5e4fbf94643defa6086f7414cc2369785d89fb58b2c32158e0511a0', 3, '2026-03-18', '2026-03-18 02:14:07', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:13:07'),
(187, '9077fae58f4ad0d515eabb5b42ddbae70f09bc89ccc3e029d99b32b068935f11', 3, '2026-03-18', '2026-03-18 03:34:26', 0, NULL, NULL, '192.168.68.188', '2026-03-17 19:33:26'),
(188, '70b98603bce022580ca45e5d889a5d8d75b745b9d4f05a74aac50e9e88110132', 3, '2026-03-18', '2026-03-18 03:37:17', 0, NULL, NULL, '192.168.68.188', '2026-03-17 19:36:17'),
(189, '37f6c02f5af51bf8df9ee83198aff368fed87535ced39c0030ce300d63d29e8d', 3, '2026-03-18', '2026-03-18 03:37:30', 0, NULL, NULL, '192.168.68.188', '2026-03-17 19:36:30'),
(190, '919a94a5a39b7c553aeca17fce4d9f2b77e994b9d7503a88a92630d4a8a085e8', 3, '2026-03-18', '2026-03-18 03:54:54', 0, NULL, NULL, '192.168.68.188', '2026-03-17 19:53:54'),
(191, 'dfa362692880bb7c68b3dfee3f85e6b08c7733fd8c67debed62476524781dd46', 3, '2026-03-18', '2026-03-18 15:46:04', 0, NULL, NULL, '::1', '2026-03-18 07:45:04'),
(192, 'afd5ee5ce107eafb9b46afa8215caaab2f5970d57f253db6306ef3645b179a00', 3, '2026-03-18', '2026-03-18 15:46:34', 0, NULL, NULL, '::1', '2026-03-18 07:45:34'),
(193, '5c8b4a67cf45863a1b8ba23a9dc8fc7c34038ec97564c7af8e09ceb43c6c2a55', 3, '2026-03-18', '2026-03-18 15:47:04', 0, NULL, NULL, '::1', '2026-03-18 07:46:04'),
(194, '28c3e542c1a60539484cb738061232eec221b70566cc83944793ebb508743bee', 3, '2026-03-18', '2026-03-18 15:47:34', 0, NULL, NULL, '::1', '2026-03-18 07:46:34'),
(195, '0d2cf45691b1c5c5fe01aa7c509822114e9413a52a4200c0ad3c76a2c21581b4', 3, '2026-03-18', '2026-03-18 15:48:04', 0, NULL, NULL, '::1', '2026-03-18 07:47:04'),
(196, '6e64218395261c3c4915296c317fe14a14b96030ec6ba726ac30666dccc77de8', 3, '2026-03-18', '2026-03-18 15:48:34', 0, NULL, NULL, '::1', '2026-03-18 07:47:34'),
(197, 'ff4cb114e504a1348f8c1aa51d618a459750173c78ff1a53c7249af76410bc9e', 3, '2026-03-18', '2026-03-18 17:31:39', 0, NULL, NULL, '::1', '2026-03-18 09:30:39'),
(198, 'ad6bfe13ed8af6670798cd6c9e4987774c7fb0fe36eda280e71db8cb930bf880', 4, '2026-03-19', '2026-03-19 13:31:35', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:30:35'),
(199, '9a1b72e2a07c7d11d91a4aeaa1c908eed4443ef32135532da0e3284acb1f43b9', 4, '2026-03-19', '2026-03-19 13:32:55', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:31:55'),
(200, '674766f652531bceccabc1dc52ce381da20f24f652730162779a5c9361faaf96', 4, '2026-03-19', '2026-03-19 13:34:46', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:33:46'),
(201, 'b39a686ea13e79ccf1c69d7136997884aed78d3018b47d227748a27def620cfe', 4, '2026-03-19', '2026-03-19 13:40:29', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:39:29'),
(202, 'd1a013ea001a0f42d7daa5bb13aad3b6ea0903e54e8a632b9b4ebf4bc21e3c8d', 3, '2026-03-19', '2026-03-19 13:40:53', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:39:53'),
(203, 'bc546ce2cf8a4b67cf4023cb3d88d5d47e4123cd7487190d8c5c28560f734a20', 3, '2026-03-19', '2026-03-19 13:46:46', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:45:46'),
(204, '368da10a9a48c58e17eea11b4fcd23646a5555e04725c08156e2e2c5a66695f7', 3, '2026-03-19', '2026-03-19 13:50:16', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:49:16'),
(205, '6e9c96b545901bda2a5db1fe0057e1cb708b61d1afa48efcf0b844369d7c349f', 3, '2026-03-19', '2026-03-19 13:58:16', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:57:16'),
(206, 'ba80ae160f9175b433454e7cf562b629ce8ebe919548649939a1cb3b7c2bc3a2', 3, '2026-03-19', '2026-03-19 14:01:02', 0, NULL, NULL, '10.43.2.98', '2026-03-19 06:00:02'),
(207, 'c0501b056a9798f9add4dc72cd4318fe5105b2fa249433238c44a3f98a3110d0', 3, '2026-03-19', '2026-03-19 14:58:14', 0, NULL, NULL, '10.56.5.98', '2026-03-19 06:57:14'),
(208, 'cc9766daa571a96fc9930ed88abee4ffa031ac43a505391e5eac79b33b19989b', 3, '2026-03-19', '2026-03-19 14:58:28', 0, NULL, NULL, '10.56.5.98', '2026-03-19 06:57:28'),
(209, '5574019e018a826b43935be1072dc3fe3e37de9c998805a3758addbd4d73d3cb', 3, '2026-03-19', '2026-03-19 17:32:45', 0, NULL, NULL, '10.56.5.98', '2026-03-19 09:31:45'),
(210, '41a7f1613dbad5e3846c4bf9e0b177d6d7167d2b0b0625e8f49aebd3b3f54ab8', 3, '2026-03-19', '2026-03-19 17:33:01', 0, NULL, NULL, '10.56.5.98', '2026-03-19 09:32:01'),
(211, '6cce3b0b225320a5978e2bc75e362277d156f5e28c05c0246b36a6e4218fe906', 3, '2026-03-19', '2026-03-19 19:27:31', 0, NULL, NULL, '10.56.5.98', '2026-03-19 11:26:31'),
(212, '82108daf0a6a1bc5d0ae937011e8fe449566beda4b12dff11b1677f6da4060a1', 3, '2026-03-19', '2026-03-19 22:50:19', 0, NULL, NULL, '::1', '2026-03-19 14:49:19'),
(213, '6e997895322b9e471dc24c8b4d2d922be4358d5156e35c5da03d29164c100180', 3, '2026-03-20', '2026-03-20 02:46:47', 0, NULL, NULL, '10.56.5.98', '2026-03-19 18:45:47'),
(214, 'b6b9a85726bcb12fb7072bb05534845865cbb1a4566169a59ab7423f3ca09f31', 3, '2026-03-20', '2026-03-20 14:35:18', 0, NULL, NULL, '192.168.68.188', '2026-03-20 06:34:18'),
(215, 'c233b58d779a32c01a052e4a3426394edca3f069f6fd70b521643bde2eec8e4f', 3, '2026-03-20', '2026-03-20 20:49:44', 0, NULL, NULL, '10.56.5.98', '2026-03-20 12:48:44'),
(216, '8a8e3e6e33dcfe0321730662a7f298f06251b2aed26cbb3f44caa1daaf2b8756', 3, '2026-03-21', '2026-03-21 12:36:36', 0, NULL, NULL, '10.56.5.98', '2026-03-21 04:35:36'),
(217, '3a38193c83f06d9ac8d42de7274c0c36e776888112a12a6fb82a35b272f681ee', 3, '2026-03-21', '2026-03-21 12:40:29', 0, NULL, NULL, '10.56.5.98', '2026-03-21 04:39:29'),
(218, 'f3954aa87ade808b8c421f57394d61f18c20e8ac947cdf70f015b5e92a1a32a8', 3, '2026-03-21', '2026-03-21 12:40:41', 0, NULL, NULL, '10.56.5.98', '2026-03-21 04:39:41'),
(219, '0e685c4bc59a99b5232e1a89d4b948bdac44f89e5616e7ce0547ba5b10244cc7', 3, '2026-03-22', '2026-03-22 11:18:18', 0, NULL, NULL, '192.168.68.141', '2026-03-22 03:17:18'),
(220, 'b2596ae18fc7c953ca210ec44ed222f42842f8b18b3dd38211a10675ec7b77d7', 3, '2026-03-22', '2026-03-22 11:18:48', 0, NULL, NULL, '192.168.68.141', '2026-03-22 03:17:48'),
(221, 'bb8843668a888a9cbbbfb795bf7c36bce61206150809dfa94621a491666b82b4', 3, '2026-03-22', '2026-03-22 11:19:18', 0, NULL, NULL, '192.168.68.141', '2026-03-22 03:18:18'),
(222, '98d9deba9b336a965b7cfcb400cfb4173f9ee8bf9a14775206a36400d6c1e208', 3, '2026-03-22', '2026-03-22 11:19:48', 0, NULL, NULL, '192.168.68.141', '2026-03-22 03:18:48'),
(223, 'e57ee1cabbe3fa32b5615f2b4b53402f73a8945f2582b9b09c2fd9f495d1c1b9', 3, '2026-03-22', '2026-03-22 12:30:29', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:29:29'),
(224, '92ac358dafa12addf4aa871a349559c40a837dfd117425cb0ddcd15c422e5b9a', 3, '2026-03-22', '2026-03-22 12:30:59', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:29:59'),
(225, '8a246be0bcdc136d4ecf21bff80d3d1890645af44dd19a459c46f12b73c2479b', 3, '2026-03-22', '2026-03-22 12:31:29', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:30:29'),
(226, '21593df39a5fe50b67966beb96136982cfbb708fa0f85801eac9b7b69a3d51bc', 3, '2026-03-22', '2026-03-22 12:31:59', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:30:59'),
(227, '5b00c4c3ee8eee1059a6e24257780047f6f95b5aad3d1f0300f442f674eba61b', 3, '2026-03-22', '2026-03-22 12:32:29', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:31:29'),
(228, '21da5af6001c056f337466ad5564815b5e0c95a6b0591e1682f2a8a5f52a5a3f', 3, '2026-03-22', '2026-03-22 12:33:00', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:32:00'),
(229, 'f9915021289f1b47695825115e5c1095daf2ab64b11600b5a3408ceb83aff1f8', 3, '2026-03-22', '2026-03-22 12:33:50', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:32:50'),
(230, 'c4d35b3fd3dfbcd886597f130921a8c262e3fcddd077e3cff52778e9d3933e58', 3, '2026-03-22', '2026-03-22 12:34:47', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:33:47'),
(231, '5f639ee0f5e605af18950c665a2d35b52218b66d02c12d6368b8c3cc69dbc365', 3, '2026-03-22', '2026-03-22 12:35:34', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:34:34'),
(232, '1a13b473f49ed19551b3e1651d3d6d6d02fcd2ed2b3f32dee8c1a833a43fe18c', 3, '2026-03-22', '2026-03-22 12:36:05', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:35:05'),
(233, '9c8e2eb1b61a1ed4ed0b46a19acb9fa2d23fb8a717ff41e8653c01ae964cbec7', 3, '2026-03-22', '2026-03-22 12:36:35', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:35:35'),
(234, 'ee47d72e14b328ccbe439b70fca73a2f85b8de92d8eaa0c28f23137cedc4ce75', 3, '2026-03-22', '2026-03-22 13:01:12', 0, NULL, NULL, '192.168.68.141', '2026-03-22 05:00:12'),
(235, '297bed73de23faa8dcdf85c49a56e4d51d102f2d67da76a205194195d6a0e4a6', 3, '2026-03-22', '2026-03-22 13:01:45', 0, NULL, NULL, '192.168.68.141', '2026-03-22 05:00:45'),
(236, '73454468c9cfb1827e5b62cef5143e36961ef3ca52ef218853ccd6219abe62ce', 3, '2026-03-22', '2026-03-22 14:52:57', 0, NULL, NULL, '192.168.68.141', '2026-03-22 06:51:57'),
(237, '0ca499699c02b9c4d6b8865942408ac5f3cee02fe57cabd27bbc5d90b2f30ea0', 3, '2026-03-22', '2026-03-22 16:07:49', 0, NULL, NULL, '192.168.68.141', '2026-03-22 08:06:49'),
(238, '712e3822006dc40bd083072db8212fbe8bc9b53aa56a4aa448c5bb1e5659755d', 3, '2026-03-27', '2026-03-27 17:12:13', 0, NULL, NULL, '::1', '2026-03-27 09:11:13'),
(239, '902c92ffe562691b0398c06a0b1d102263d9c24e03701fb29b96164563aef93c', 3, '2026-03-27', '2026-03-27 17:12:44', 0, NULL, NULL, '::1', '2026-03-27 09:11:44'),
(240, '7cfc4b4954bb45c2e243449ec0745170ea4ae785229bdb82fe7a1e10ff726b2f', 3, '2026-03-27', '2026-03-27 17:13:15', 0, NULL, NULL, '::1', '2026-03-27 09:12:15'),
(241, 'cc7154cfb831cb019a86fb68663e6a52ce4770c492eda0cbef4658cbfe6fd7a1', 3, '2026-03-27', '2026-03-27 17:13:46', 0, NULL, NULL, '::1', '2026-03-27 09:12:46'),
(242, '7b91f61e146a2e205e74ba41f42b64c4b615249bf7fe76fc778b7405f71e4ee1', 3, '2026-03-27', '2026-03-27 17:14:26', 0, NULL, NULL, '::1', '2026-03-27 09:13:26'),
(243, 'db47cc3c95389fd01b7d8d6588698cc364fbd1a04d942486c589fff1a724e3e2', 3, '2026-03-27', '2026-03-27 17:15:01', 0, NULL, NULL, '::1', '2026-03-27 09:14:01'),
(244, '2f4118a038f60adb52cd8cbdc889c054825af94be48058c87f7f416473d376ef', 3, '2026-03-27', '2026-03-27 17:16:01', 0, NULL, NULL, '::1', '2026-03-27 09:15:01'),
(245, 'e44b53fd998721a1dbeb2bb1677489ab465dce2ed66f6c01d65d03300eca45ad', 3, '2026-03-27', '2026-03-27 17:16:32', 0, NULL, NULL, '::1', '2026-03-27 09:15:32'),
(246, 'f9d7f10ae15dcc4ef88d89ce6f397e0358e062e52ff3c91f34597249445e5d2f', 3, '2026-03-27', '2026-03-27 17:17:03', 0, NULL, NULL, '::1', '2026-03-27 09:16:03'),
(247, 'b7606c7626dc2a894f6a9f1ab68de0948a17c30b6b7ed68988a75dcec5ee19b4', 3, '2026-03-27', '2026-03-27 17:17:34', 0, NULL, NULL, '::1', '2026-03-27 09:16:34'),
(248, '372a403d250e71dfbf5d32a6ee7802c378a2c9b447b2e40fead48c32e3b9a76f', 3, '2026-03-27', '2026-03-27 17:18:05', 0, NULL, NULL, '::1', '2026-03-27 09:17:05'),
(249, 'd30e1c86dc82622030279591d483fa9677d2aefbe64f01dc0b2ccd2a1b39876c', 3, '2026-03-27', '2026-03-27 17:18:37', 0, NULL, NULL, '::1', '2026-03-27 09:17:37'),
(250, '437931e222707c5664a1a45c46614e2b5e8bd72638194e7fa8a481e3a229ae75', 3, '2026-03-27', '2026-03-27 17:19:10', 0, NULL, NULL, '::1', '2026-03-27 09:18:10'),
(251, 'ff33a40308f16cde3a5b00be65873e8b5236f5cfc4011f7bb5a666414ffd8805', 3, '2026-03-27', '2026-03-27 17:19:41', 0, NULL, NULL, '::1', '2026-03-27 09:18:41'),
(252, 'a083d2388e7aa9445e2a247619dd8c245fef86be82e5c9425c6f2fcd3d87ab7a', 3, '2026-03-27', '2026-03-27 17:20:12', 0, NULL, NULL, '::1', '2026-03-27 09:19:12'),
(253, '286c7dc5dff488e16a2b5c73479e5fe00fd6181b61e66cdec3183f7299071fc7', 3, '2026-03-27', '2026-03-27 17:21:01', 0, NULL, NULL, '::1', '2026-03-27 09:20:01'),
(254, '0b97fa93048d61ac23917daf6e815a3b8c54f769c067b7c5f92810a9d7b8c8bf', 3, '2026-03-27', '2026-03-27 17:21:33', 0, NULL, NULL, '::1', '2026-03-27 09:20:33'),
(255, 'd0884adb3ff568502dd62233a62c2a39afd2b415dd3afc903190957e1039dd93', 3, '2026-03-27', '2026-03-27 17:22:04', 0, NULL, NULL, '::1', '2026-03-27 09:21:04'),
(256, '200db7fbaa0ec4f1b7508c01e9b6d54f534c21fc2f35135fda26a4b7f1316082', 3, '2026-03-27', '2026-03-27 17:22:35', 0, NULL, NULL, '::1', '2026-03-27 09:21:35'),
(257, '25b6b3eb069285f8b98f0fe7f589adc363b2f76d326589a4de442019a23ef895', 3, '2026-03-27', '2026-03-27 17:23:06', 0, NULL, NULL, '::1', '2026-03-27 09:22:06'),
(258, '48055f3aeb6d0b7c91ea892b870fe61416101cfd51455f4a1a32f80d2ae68599', 3, '2026-03-27', '2026-03-27 17:23:37', 0, NULL, NULL, '::1', '2026-03-27 09:22:37'),
(259, '9aa40de259d2dedd1398fb44aa66c4e7beb9f9684a8e44ca42fa287c6bc5ee90', 3, '2026-03-27', '2026-03-27 17:24:09', 0, NULL, NULL, '::1', '2026-03-27 09:23:09'),
(260, 'd63cf4ade6c324ed79c08f16b6c3dc2887e9cc48f99fa9d2edf52f728b71d61f', 3, '2026-03-27', '2026-03-27 17:24:40', 0, NULL, NULL, '::1', '2026-03-27 09:23:40'),
(261, '770a3ac27b3fa32073d5e1650123c2fa52fe6188b0cf96456c75e1ef842d3814', 3, '2026-03-27', '2026-03-27 17:25:25', 0, NULL, NULL, '::1', '2026-03-27 09:24:25'),
(262, 'c8f31b96ce65ced1b67ed816ddff0bae189fc9f6a17290d646f10de601901f2e', 3, '2026-03-27', '2026-03-27 17:25:55', 0, NULL, NULL, '::1', '2026-03-27 09:24:55'),
(263, 'ba6b581a2947eed9081a73a486123fa746bbac1fb184f356e1d4c595538d3285', 3, '2026-03-27', '2026-03-27 17:26:26', 0, NULL, NULL, '::1', '2026-03-27 09:25:26'),
(264, '1ee9202edbcd5cd421fefd8fee4ae499d3bc82ff02d3bed66e8b459bc87c1b3d', 3, '2026-03-27', '2026-03-27 17:26:57', 0, NULL, NULL, '::1', '2026-03-27 09:25:57'),
(265, '76a9aaa7ba46e7e74096064ed0d870e902b95105238c03440bda95c560823c10', 3, '2026-03-27', '2026-03-27 17:27:28', 0, NULL, NULL, '::1', '2026-03-27 09:26:28'),
(266, 'b13c0d4677b400f6e472f018986c3a2ad26438a41887fd956d5eaa07b3814725', 3, '2026-03-27', '2026-03-27 17:27:59', 0, NULL, NULL, '::1', '2026-03-27 09:26:59'),
(267, 'd08df5ffccae07cb7094b3014d9698c80355dd8fcd8b1b118e3ae6049509502e', 3, '2026-03-27', '2026-03-27 17:28:30', 0, NULL, NULL, '::1', '2026-03-27 09:27:30'),
(268, '8964b237c6a1efcdc7c43445fde7000e4a68773ac6f53faa704401063bcf2c1c', 3, '2026-03-27', '2026-03-27 17:29:01', 0, NULL, NULL, '::1', '2026-03-27 09:28:01'),
(269, 'ffec74abba17a0f02baee6db5dfdede3053a66378b10a33f877e219db81915d2', 3, '2026-03-27', '2026-03-27 17:29:32', 0, NULL, NULL, '::1', '2026-03-27 09:28:32'),
(270, '9b75e5386e65b21abe49d000f36163ec1de9484d68b4561a56e628e3c157d3bf', 3, '2026-03-27', '2026-03-27 17:30:03', 0, NULL, NULL, '::1', '2026-03-27 09:29:03'),
(271, 'a9dd8541a06c42df215d5d51d00789eee180d15741e62f04d2287bc92b1647a7', 3, '2026-03-27', '2026-03-27 17:30:34', 0, NULL, NULL, '::1', '2026-03-27 09:29:34'),
(272, '1f5ad493a21e28ed219dd40b763ebf7506fb4df448adcf206e57ba037062ccca', 3, '2026-03-27', '2026-03-27 17:31:05', 0, NULL, NULL, '::1', '2026-03-27 09:30:05'),
(273, '81c4c7b54a6ce2745896480b9d4c9511f1a9f1746131c5ce6b1b1915877bca0c', 3, '2026-03-27', '2026-03-27 17:31:36', 0, NULL, NULL, '::1', '2026-03-27 09:30:36'),
(274, '0b1e30557dcbc359555b54fa171023ea73f5a3449c17b0ead7c5304f22c91b4f', 3, '2026-03-27', '2026-03-27 17:32:08', 0, NULL, NULL, '::1', '2026-03-27 09:31:08'),
(275, 'c7242d5f36fd09f6d07bf127b18a9b9aeb11a270ab6a11dafe03e1e2a19f1064', 3, '2026-03-27', '2026-03-27 17:32:39', 0, NULL, NULL, '::1', '2026-03-27 09:31:39'),
(276, '9beaa6f8dd2a79448f37fdf5d93549d8e6b40e75ec4149f7642cfafd99ffd45b', 3, '2026-03-27', '2026-03-27 17:33:11', 0, NULL, NULL, '::1', '2026-03-27 09:32:11'),
(277, '18fa50bb502b0d0b885040beb6948f9034900af8034bb93933610b96b55b28e8', 3, '2026-03-27', '2026-03-27 17:33:42', 0, NULL, NULL, '::1', '2026-03-27 09:32:42'),
(278, '05bb4cf63e3c2796d78175b7787128230ffccf4a06bd551119fbffd06d426894', 3, '2026-03-27', '2026-03-27 17:34:13', 0, NULL, NULL, '::1', '2026-03-27 09:33:13'),
(279, '653483fd66dba2912dee2635a523c32ad881ca50b88f6504e2ba0c8947208bbe', 3, '2026-03-27', '2026-03-27 17:34:44', 0, NULL, NULL, '::1', '2026-03-27 09:33:44'),
(280, '10c7516ba426f4b830ab016be86ab9ebcc898d656e4f013e197a5043aa4ce277', 3, '2026-03-27', '2026-03-27 17:35:15', 0, NULL, NULL, '::1', '2026-03-27 09:34:15'),
(281, 'b3a835543cbc88b9fce113a278ae616a486c110f6666aae8c6a090303b5cef32', 3, '2026-03-27', '2026-03-27 17:35:48', 0, NULL, NULL, '::1', '2026-03-27 09:34:48'),
(282, '76b89c468e13423e3dbcb538e1f5909ff25bb9396df9602e1d0b1d49c11f40d9', 3, '2026-03-27', '2026-03-27 17:36:19', 0, NULL, NULL, '::1', '2026-03-27 09:35:19'),
(283, '20b80c5ea534177b1fa0d66db4eb79b1b73cf2d8f9ef09acd9f4a490be716962', 3, '2026-03-27', '2026-03-27 17:36:52', 0, NULL, NULL, '::1', '2026-03-27 09:35:52'),
(284, '5343951345b41406e59494d0a70dba6af574842258434f879d90a4a30ce6f6a7', 3, '2026-03-27', '2026-03-27 17:37:25', 0, NULL, NULL, '::1', '2026-03-27 09:36:25'),
(285, '0918ee15cd40aa9a358575c57e94017dd91a4ecf21899082cdcb8b4791afdc60', 3, '2026-03-27', '2026-03-27 17:38:08', 0, NULL, NULL, '::1', '2026-03-27 09:37:08'),
(286, '3185e7cbd03a55293a6bfd0ed2cc7187bb531e37919e6e3b02500ef6b300c804', 3, '2026-03-27', '2026-03-27 17:39:07', 0, NULL, NULL, '::1', '2026-03-27 09:38:07'),
(287, 'f645afb14d07251106b92606af07107da95c6cc70c92a2e1d1a203db3930473d', 3, '2026-03-28', '2026-03-28 11:03:08', 0, NULL, NULL, '::1', '2026-03-28 03:02:08'),
(288, 'dc2c89a7bc20a548c42190e4f7071eb5bf06660a374c2de489e257a260f81bec', 3, '2026-03-28', '2026-03-28 11:03:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:02:48'),
(289, 'ce43cb70ce542193f6ff17bd698a29fc515c7ea8a974f529adaff38d160cfdc0', 3, '2026-03-28', '2026-03-28 11:04:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:03:18'),
(290, '07a5ece9c994a8c08973a5586cbc3e3d0b3b23f7b9294218663482487cc64196', 3, '2026-03-28', '2026-03-28 11:04:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:03:48'),
(291, 'd52b962e74aad9474129ffdef361ba477c4c1a0bc973ee500c90252d53293cb1', 3, '2026-03-28', '2026-03-28 11:05:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:04:18'),
(292, 'a50de43e2d9a814222663b45f8692b3bf78e166214991975db9416123144ffcf', 3, '2026-03-28', '2026-03-28 11:05:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:04:51'),
(293, 'b7446b0be612f0c76e01b9d742b32076601df08d583955de3dc371b495e96824', 3, '2026-03-28', '2026-03-28 11:06:22', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:05:22'),
(294, 'd85db0bcc8c9b67517bb78906e8f86a38a0707243b007445c7bed0b29de3266f', 3, '2026-03-28', '2026-03-28 11:06:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:05:52'),
(295, 'f139ec01ce654a0dc29c58484d2aba09c94750156cd35321cd0fed46352a6d73', 3, '2026-03-28', '2026-03-28 11:07:22', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:06:22'),
(296, '73af6cff79bf6ad1961d756d9195c0f96a8a82830fdc677339e33891556f80b7', 3, '2026-03-28', '2026-03-28 11:07:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:06:53'),
(297, 'ab9e75ecd24ff652cc88b6fea8dbfba5680b31568a0e6d6d495ea02f10a243c6', 3, '2026-03-28', '2026-03-28 11:08:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:07:24'),
(298, '013dbc81b647c9345194e3f7f8f6e2049c3eb33cec3ca8039e9e9ac852baaf35', 3, '2026-03-28', '2026-03-28 11:08:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:07:55'),
(299, 'b22276f34e44944b11985d9378c0d143560ebd5e6e71d9d8f49c48f9d913c2cd', 3, '2026-03-28', '2026-03-28 11:09:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:08:26');
INSERT INTO `ta_attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(300, '33ae4b7a319db6fb46f05b8664491bb6da8c83763265bfe44b53ed0be13c617d', 3, '2026-03-28', '2026-03-28 11:09:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:08:57'),
(301, '5a5897fbb7d0b696536ee53b9fc2186cc098d063f461251b85a4d7fd2e79ac5c', 3, '2026-03-28', '2026-03-28 11:10:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:09:28'),
(302, 'fc94b6a9dd6985d61699c7768b18a008dba6c68dfc8e32c2490f1a1cb83f10e3', 3, '2026-03-28', '2026-03-28 11:10:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:09:59'),
(303, '22560f2e3d465290af69e991e4740256e54e1cecab99beed99d5ec91dbde3937', 3, '2026-03-28', '2026-03-28 11:11:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:10:30'),
(304, '2d2d18dba74f01ece4c3c0b3479a95387f0db30d06e764db05d6f7139524198f', 3, '2026-03-28', '2026-03-28 11:12:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:11:01'),
(305, '504145aa9fdb08b32ae64acfa6ab01ab63e8e4908a6393b906ba5f3e06574d7f', 3, '2026-03-28', '2026-03-28 11:12:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:11:49'),
(306, 'e827c4af95c8c75c9154fbef59308d1ae2809f6b2675ffe5e4322cf6bdb8cfae', 3, '2026-03-28', '2026-03-28 11:13:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:12:48'),
(307, 'be09696382087df9710697f53b53a208eb4e723b74781c4c1d2f278c8880257c', 3, '2026-03-28', '2026-03-28 11:14:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:13:48'),
(308, 'c304f5db1ebdc0e18a2fe6d9701c06f84e2447ce7be830848a61524e2b9d0843', 3, '2026-03-28', '2026-03-28 11:15:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:14:29'),
(309, '6fc77eb07f578d1dafb00a791bc7d8bc65c55bc4092bb16168d944b97f695f52', 3, '2026-03-28', '2026-03-28 11:16:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:15:00'),
(310, 'ec97d76893a8a122cfab0f0e24db8d5e275807d025463c9b985c266ebf903d92', 3, '2026-03-28', '2026-03-28 11:16:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:15:42'),
(311, '211622a6890699aecae7049cd71b1602204f2b3877627facd2aa0d36380d5ab1', 3, '2026-03-28', '2026-03-28 11:17:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:16:24'),
(312, 'cc35ee1e59d2c9e2af57337f31b69aa8856033a5f38037e4d48e08d6083070e1', 3, '2026-03-28', '2026-03-28 11:18:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:17:11'),
(313, '76b2c602f43e865bb8ac3b31046d60d209fa9dfef943095a7605b0ffac518ac1', 3, '2026-03-28', '2026-03-28 11:18:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:17:44'),
(314, 'eb0413258723ab4ea052664acd055b69314fc3c9de1e30ec6d9ecf90eabc7295', 3, '2026-03-28', '2026-03-28 11:19:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:18:28'),
(315, '0b926152f0e98a6beef63a7ede13f4fd044c8b8a71668d91f3d43f35b143cedd', 3, '2026-03-28', '2026-03-28 11:20:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:19:28'),
(316, 'a142724465ad5bb601aa1d5e79c4f75e5700c39f9668ee2f230022071fc22cab', 3, '2026-03-28', '2026-03-28 11:21:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:20:00'),
(317, '1dac411625520546c92a86b3419f93dfaa82df1c45f3f740dcb3294174a65e30', 3, '2026-03-28', '2026-03-28 11:21:33', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:20:33'),
(318, '78cda73de2c9ad0cd2886235820ab24cd7db636cc6a1aad5fa58bf8490a1d831', 3, '2026-03-28', '2026-03-28 11:22:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:21:07'),
(319, 'bf77e71b474d23448d0327469f61963fd46237b6c68f1d342b9e39df9d5ae415', 3, '2026-03-28', '2026-03-28 11:22:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:21:40'),
(320, '13a273e8535a3fbe54e03dc6818cabb075eba8a12ebf2239d74379fac567f5f6', 3, '2026-03-28', '2026-03-28 11:23:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:22:14'),
(321, 'dc520b59a107b2629ba8d547b9bc825cc37b3ffb52652445c75391ed5f4e9366', 3, '2026-03-28', '2026-03-28 11:24:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:23:14'),
(322, '3b9e500137b20568a2fc06ffa23d8ec7605604a0620160f9a0d2062ceb975428', 3, '2026-03-28', '2026-03-28 11:24:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:23:46'),
(323, '705cf04adf09d2dbf57737ee505cf74ba00c8ac71af1c7b632497cf8073d941a', 3, '2026-03-28', '2026-03-28 11:25:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:24:17'),
(324, 'fa91eafd8d3b5355c81214937cd17870a9be18d86479423f7523459adf4479ea', 3, '2026-03-28', '2026-03-28 11:25:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:24:48'),
(325, 'd7b232b77b3efe9b2ea3a676fb496fd91b2509cc2214e77b9fa1db85651627b8', 3, '2026-03-28', '2026-03-28 11:26:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:25:19'),
(326, '6c5cb097e6f00c4c28078d71318b07002644ad90e9d9be592f6814803ba398ae', 3, '2026-03-28', '2026-03-28 11:26:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:25:50'),
(327, '8474a4153e1e4d97ed799006e5c9c7ef3abf11f700a57f80b5c9bb5227e48d99', 3, '2026-03-28', '2026-03-28 11:27:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:26:24'),
(328, '52183e57027bf5f7ee0c988361ff6791b58efd1a9fc46c09046683c843646df3', 3, '2026-03-28', '2026-03-28 11:27:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:26:56'),
(329, 'fde90034900b99fbf2122607a31455fc6a162ee14e847caccdca906561d69a5f', 3, '2026-03-28', '2026-03-28 11:28:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:27:54'),
(330, 'dbd9c20b962739be1ac076ebf0ce463a31422b001929147b57215022a6b7cf47', 3, '2026-03-28', '2026-03-28 11:29:33', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:28:33'),
(331, '74ba64a830e92463b38978432696b000213e66a8c2e4288915a898278e0c5311', 3, '2026-03-28', '2026-03-28 11:30:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:29:04'),
(332, '8f0ff47e98fe7a6f4bf267f8b5fb73f05c41298d4c33e8c0bb88e4e72f8fecd8', 3, '2026-03-28', '2026-03-28 11:30:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:29:35'),
(333, 'f7aa51627e9e0a1aedfb4be160821cba8a2165d57abbf815794fe7fa985377bb', 3, '2026-03-28', '2026-03-28 11:31:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:30:06'),
(334, '59607dc4b807ef6a9b5c112894302b7e2ada86cfb1f09a88deea9d9d258d8d3b', 3, '2026-03-28', '2026-03-28 11:32:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:31:06'),
(335, 'f5eadf979c46341d9bcbb424ca2e0e91b37aedf87d3e8dd09b2cd923a3360ed8', 3, '2026-03-28', '2026-03-28 11:33:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:32:06'),
(336, 'a90e46be3774189c5c18485b1a17aed3a8ab0e1d703ac9ab120b6eec8abb3c8f', 3, '2026-03-28', '2026-03-28 11:33:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:32:54'),
(337, 'd8d9f78f0dab49646cce216f38dd54c329acb42cf690ddb5c975f1520933e0f0', 3, '2026-03-28', '2026-03-28 11:34:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:33:54'),
(338, '55888512007e1dd1b45e774e09aa41b17699a0317491c359e39100fa48a4a5ea', 3, '2026-03-28', '2026-03-28 11:35:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:34:36'),
(339, '8818f7ce2800cd6f487a73039b86e6255de1b4fc5ff0d97f8477b80484c69f89', 3, '2026-03-28', '2026-03-28 11:36:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:35:36'),
(340, 'f88a6b53d3a2ac910df0eb4b1326179ba754294e1b97a14ec2e41a1d035ceb4e', 3, '2026-03-28', '2026-03-28 11:37:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:36:07'),
(341, 'ead90c53cd1c9f27a7f0bccbc57a9339dbdba982bf7342f5875b483576b2fbc4', 3, '2026-03-28', '2026-03-28 11:37:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:36:38'),
(342, '4b3e2493dcb126661a8bed0c00947d0938ba5eeaa5092e4d5494355950c6d9d6', 3, '2026-03-28', '2026-03-28 11:38:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:37:30'),
(343, 'f12f231c20355f0929ef3e5148d14b366a991d30190a83e4b69596e7e60be7cb', 3, '2026-03-28', '2026-03-28 11:39:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:38:01'),
(344, 'b52022497a7e880348338e9bed3c884b1b8f4201939bb3e5ccf732e4a42f7a73', 3, '2026-03-28', '2026-03-28 11:39:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:38:56'),
(345, 'abbe046b0214c7ab5a933aef885df4b056daac56888bf12c63f1bb73b5e993c4', 3, '2026-03-28', '2026-03-28 11:40:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:39:29'),
(346, '05b92f5781ec6088225b6db27ccbb5ed8c3a76967bd80506c746ce5c49613956', 3, '2026-03-28', '2026-03-28 11:41:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:40:29'),
(347, 'b7f6e9c6a954aa482b7af54d42458dc5224b58142741cf36e8cc8f4184766dda', 3, '2026-03-28', '2026-03-28 11:42:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:41:29'),
(348, '68a8b0bce07a2a71f38b783590810c121a8a535e9b8daf9948694bd883a83079', 3, '2026-03-28', '2026-03-28 11:43:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:42:00'),
(349, 'e8658a2663bb2f6a9178b2f496b8d412daa13a0fe84b6a296d57582e3c5f45e0', 3, '2026-03-28', '2026-03-28 11:43:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:42:36'),
(350, '2141edceb123f9cfe1a4ce76562970ff2077b2bf85661c4d22a9881a08778aa2', 3, '2026-03-28', '2026-03-28 11:44:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:43:23'),
(351, '6f342149551a8137473924040dc57168c6d3a7ededd87e5226092ccc0f2d8f64', 3, '2026-03-28', '2026-03-28 11:45:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:44:01'),
(352, 'be1dd0408fc4b3698cbe7b1ef4ad60eff184df323e20626b61e5b477511ea265', 3, '2026-03-28', '2026-03-28 11:45:32', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:44:32'),
(353, 'c886ceb2252e16480b08b6eee8fdb89640059e527edd74d3802608f34533d22c', 3, '2026-03-28', '2026-03-28 11:46:03', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:45:03'),
(354, 'bf0993f824600ed586395fa46bfdaff9f62a7e410dbc980584ac9452f01905f7', 3, '2026-03-28', '2026-03-28 11:47:03', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:46:03'),
(355, '16771ce947985dea8566a8bc6a2eabaed8d3a02071deccf78fcea9d5f7ab243e', 3, '2026-03-28', '2026-03-28 11:47:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:46:36'),
(356, '9b562918416704204148a8236a04b191b63803af31ea41626098bdabbd9298a5', 3, '2026-03-28', '2026-03-28 11:48:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:47:07'),
(357, '3a4644343df2b3fee370fa5c227b52e32715d19cf1d7926c9e4aea64200b33fe', 3, '2026-03-28', '2026-03-28 11:48:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:47:45'),
(358, '0b7acb3634adb6be65338d04d93ed491651f1e2773fb7be58a310c55a73f9905', 3, '2026-03-28', '2026-03-28 11:49:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:48:42'),
(359, '10609ea22404366d64f589dbdc638297a1d8116faedfd8afc3e4045ea04a0cac', 3, '2026-03-28', '2026-03-28 11:50:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:49:13'),
(360, 'e1911b20ff0911683deb72b968c4303619c1ef899aa0edcc0a2f5db4224233e3', 3, '2026-03-28', '2026-03-28 11:50:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:49:44'),
(361, '5be1b21a068c76298492f7d9d298ce1c4f82c1257adde2b3de1675ca40ac4b50', 3, '2026-03-28', '2026-03-28 11:51:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:50:44'),
(362, '17db58be1e9b0e6297e3ce5b5c88e15dd78fa6110f94021dee6a8668d0334317', 3, '2026-03-28', '2026-03-28 11:52:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:51:30'),
(363, 'dab8099dd1824df58cc7f7d6b5fa0b3c5eef0d477831791dd3f7bed704f8fe1a', 3, '2026-03-28', '2026-03-28 11:53:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:52:01'),
(364, '961ecc544a263ea6cb7e936f2340aa633ae6b8ac8ae0ff7b23e7f76ccada048d', 3, '2026-03-28', '2026-03-28 11:53:32', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:52:32'),
(365, '221bbf7a0f241c0a04c5b110fe44c961a0004c875f19aa93965c3a783d9e4251', 3, '2026-03-28', '2026-03-28 11:54:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:53:06'),
(366, '6eda8f43b096128b696dfaf1bdd4f6806bf024fd6cc24b41f3163d8a491d7190', 3, '2026-03-28', '2026-03-28 11:54:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:53:52'),
(367, '9ee14ffa151579fe38673bfba0d466d0324149062b0f83e9265b9cdc14128e15', 3, '2026-03-28', '2026-03-28 11:55:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:54:23'),
(368, 'c4d268ab2b790afbba433f90bda4e4c23eede113c95edbb46ef72697402a1a51', 3, '2026-03-28', '2026-03-28 11:55:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:54:36'),
(369, '984eee8060ef3836be059aca6255618bf8705e142f1f6bd968493e2cf3726b14', 3, '2026-03-28', '2026-03-28 11:56:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:55:06'),
(370, '9dd01c714b6f4350d5ff1645f649288718c85a2a344c72a98449e94b207745c2', 3, '2026-03-28', '2026-03-28 11:56:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:55:42'),
(371, '5286f2b093d26805df997dc62d67044f6ecb71e38cb3100902f40a9f8b5aba84', 3, '2026-03-28', '2026-03-28 11:57:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:56:13'),
(372, '49dcc853f3a077346b4aba97607e2720046734eb5166ceaba0245cc985ffb3e8', 3, '2026-03-28', '2026-03-28 11:57:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:56:48'),
(373, 'bdc3bc191f7821be89091a114077d5ce16cc9f77ff64585e153adee210e16117', 3, '2026-03-28', '2026-03-28 11:58:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:57:19'),
(374, 'b21116807a0a821a20d88fbd9b5ad233f6399e6be0aa565295ab64ed412ea15c', 3, '2026-03-28', '2026-03-28 12:16:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:15:21'),
(375, '8667bfba9079c8fabd7e0fecc502cb503331bbf710560659a119703957e518b4', 3, '2026-03-28', '2026-03-28 12:16:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:15:51'),
(376, 'c7317da10ef27eb230981504ee7d5269a73bca0ee77f04c4021f27ee80ad5836', 3, '2026-03-28', '2026-03-28 12:17:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:16:47'),
(377, '56d60e25cc42d49678fc2e03c31d0eedab3fe0b537af194efe5d5439c38d1d9a', 3, '2026-03-28', '2026-03-28 12:18:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:17:23'),
(378, '886de22b3d87afef333bc314ad5678a5f288fd80a97b3cc8dd1ffa20afb3bc89', 3, '2026-03-28', '2026-03-28 12:19:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:18:04'),
(379, 'e766dbcd96f4781ae28f4238adadbd7597d8f8845e840bfdb851f8a30a53922c', 3, '2026-03-28', '2026-03-28 12:19:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:18:34'),
(380, '4bab5861a75063fc6a4b44197e79cfd20ab7df1f46413d3642ae85f0a95a0cc1', 3, '2026-03-28', '2026-03-28 12:20:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:19:04'),
(381, '13084317f1d8cbbbf1f56b5bf1f60f0e2b869a226a12a529e22309477343df82', 3, '2026-03-28', '2026-03-28 12:20:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:19:57'),
(382, 'cfae07e6a94e3718cf1c498f7c61acf1ce9cde323a389ae107a8aa47d474dc58', 3, '2026-03-28', '2026-03-28 12:21:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:20:28'),
(383, '1b3d2236d1d5c22fbd95d2e43f27a48fd748373f4a38d448e780e5f53689546e', 3, '2026-03-28', '2026-03-28 12:21:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:20:59'),
(384, '7375fb344298886fb3e7ec9204d2fc715ccd95047fe4b1f1df2e06d5853a36bd', 3, '2026-03-28', '2026-03-28 12:22:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:21:30'),
(385, '7e05849c9f42822adbcdde0550885b8bc03a3a2c62af782a0f81f62d593ea0e2', 3, '2026-03-28', '2026-03-28 12:23:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:22:02'),
(386, '8ea529cfde916b7ccbf46ba8f3da1892144ed106d53d9ef9ba371bdf75b4cfe8', 3, '2026-03-28', '2026-03-28 12:23:03', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:22:03'),
(387, '67e51bf4e311806a74459d8760eb48657ef0262225b719607b4381e962847639', 3, '2026-03-28', '2026-03-28 12:23:33', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:22:33'),
(388, '3560a562af45bf88ba510aa2548b2de946a88428cb7496a56e10b44d62454574', 3, '2026-03-28', '2026-03-28 12:24:03', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:23:03'),
(389, 'ca70a2c83bfcbf992dcdd247ee8a858ecb340999a2d84155a7f469790f368d68', 3, '2026-03-28', '2026-03-28 12:24:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:23:34'),
(390, '9fa67e1babddea342f963960b313b3439de7edbdc88921e8a4dd9a80f5e8bd94', 3, '2026-03-28', '2026-03-28 12:25:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:24:05'),
(391, 'e0e599468b107f2571b6745c6352f59a65276b9c88895e5a5a5a7a3f1f8af33f', 3, '2026-03-28', '2026-03-28 12:25:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:24:36'),
(392, '112d7d4920f4f17df4fa791ca44394ec8e827d8f5f2230b2d5ed18eacedf59c6', 3, '2026-03-28', '2026-03-28 12:26:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:25:07'),
(393, 'fc96295886cc9d71c14e2d34f3bac327d7c0f62d1d74e9bce830f769b762cbc9', 3, '2026-03-28', '2026-03-28 12:26:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:25:38'),
(394, 'ec5b3644759db0449cc389c7304f5ef7104ddaa56435a2b749c89f57bb505db3', 3, '2026-03-28', '2026-03-28 12:27:09', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:26:09'),
(395, '059d20786bb8fe1ec1377c6c586b5ab0c0463f7b0aade9377b2c0829018d6991', 3, '2026-03-28', '2026-03-28 12:27:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:26:42'),
(396, '9bab5cf1129e155a99ccc8652174176d27527855a014afbd378908d8550b62fb', 3, '2026-03-28', '2026-03-28 12:28:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:27:13'),
(397, '748e1a835466856d9ea504d5f2464637913cc5f712a7fdf60680469f2c9c9653', 3, '2026-03-28', '2026-03-28 12:28:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:27:44'),
(398, 'f87930ffada867ef12a507404117537141373c19161bcd39dada2ee88e3b1802', 3, '2026-03-28', '2026-03-28 12:29:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:28:15'),
(399, 'd7fb375a21e06f8ffead41e442bc6ab8e0648e77ab64015b4b5bc23a49bf6c58', 3, '2026-03-28', '2026-03-28 12:29:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:28:46'),
(400, '7c5e9ea924424b7c3be97202a5a8c25aa5084ba2cc562c3e46e50b94a36aef90', 3, '2026-03-28', '2026-03-28 12:30:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:29:17'),
(401, '55402f12ea7229bad4f16486929a6d690b9b0d0ea670547c6d7010af820e55b0', 3, '2026-03-28', '2026-03-28 12:30:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:29:48'),
(402, '1df7f0f86d743c391fd36cc79b3b26381dc5b1ed88dd14618e09e89f57569570', 3, '2026-03-28', '2026-03-28 12:31:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:30:19'),
(403, '2d6f183ac8c57f3d538c2ca2065e1c685dd93b409ca4b6bb795ec8b2c1d877b5', 3, '2026-03-28', '2026-03-28 12:31:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:30:50'),
(404, '5fc6a63f11904b6b70e3510f1d7a7378224ad96fedda48772f13090065d4de45', 3, '2026-03-28', '2026-03-28 12:32:22', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:31:22'),
(405, '6ba46a6c8a8a17e23da7adc9173f6f99649373ea33c0079e7d7967d265332296', 3, '2026-03-28', '2026-03-28 12:32:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:31:53'),
(406, 'd5a5262492c07195e5fac0eaf7128b355aabf7c1e82cd6960183989f2aa2b306', 3, '2026-03-28', '2026-03-28 12:33:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:32:24'),
(407, 'f1d00f7ee79c0d9e8746a8ed26edd02d00cc966383943c9a6fd682ba7313bd34', 3, '2026-03-28', '2026-03-28 12:33:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:32:55'),
(408, 'bd03019b87aee0ff6d775a49dd756b9bbbf945b768f9951fff83f3693cab53c6', 3, '2026-03-28', '2026-03-28 12:34:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:33:27'),
(409, '1bec2180c32567928c7dc01c875ccf061a8be07bb717e57dcb5d02464d6d7f4f', 3, '2026-03-28', '2026-03-28 12:34:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:33:58'),
(410, '157063f0f008b573e97f40d5c137a5f2dac957229451b95252d4aefbb2bed210', 3, '2026-03-28', '2026-03-28 12:35:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:34:29'),
(411, '526617ea63dae0419a01ac8b868abf1fcf9c5710ad1c4609af5e917a62f82144', 3, '2026-03-28', '2026-03-28 12:36:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:35:00'),
(412, '9bab9ea1e00b60f8c0e8b935e24606b2c2ce5ea732a499e276f5165b4b844ca9', 3, '2026-03-28', '2026-03-28 12:36:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:35:31'),
(413, 'c2e82de0b1182ef794b1431f847f00a0c4bba4fa08486ffed68c8a09071e3fa7', 3, '2026-03-28', '2026-03-28 12:37:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:36:02'),
(414, 'b1c7c196457c673e278eabadde6ffc568a5b6403425294ab0a6a4b2ea5f117ca', 3, '2026-03-28', '2026-03-28 12:37:33', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:36:33'),
(415, '4fdebc072ad678c5d1a643003d9953fce3da220f2263d9937f70f9e364bba44b', 3, '2026-03-28', '2026-03-28 12:38:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:37:04'),
(416, 'b85141dd963de9bfd92073effc41d2ffbcc3725618880344d8435b0dcad1dc18', 3, '2026-03-28', '2026-03-28 12:38:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:37:34'),
(417, '1e7960169036fb85ac0d68312f27f509442d0bd2c7f471bd3ded68f725400839', 3, '2026-03-28', '2026-03-28 12:39:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:38:05'),
(418, '97549cf8643643e8fd2f9da328e56ffde3d34828d3f184e7a674eda0689373e6', 3, '2026-03-28', '2026-03-28 12:39:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:38:35'),
(419, '66b3ef558d516b969dc51dbfe47464a2d9206e1978604a0f52a144d06696a5e6', 3, '2026-03-28', '2026-03-28 12:40:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:39:07'),
(420, '7768807371a60d3444256838f5b2d333e2bdef0465c6d87d35235a42fbf22197', 3, '2026-03-28', '2026-03-28 12:40:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:39:38'),
(421, '813b3c486e4d500bd2dae2ab4ed8932e61592234f565dc7eef2fa84bd5fd9804', 3, '2026-03-28', '2026-03-28 12:41:09', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:40:09'),
(422, '263936a99323516e32b8d9174fbf2040960924a27323a0993e84b99a6a8d7e6c', 3, '2026-03-28', '2026-03-28 12:41:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:40:40'),
(423, 'dc7c8f667e7492c56ca2ec258e57bdf80fd6c4c0bb108c1d9972393e7757045d', 3, '2026-03-28', '2026-03-28 12:42:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:41:11'),
(424, '7a22bddbb5a1d10b199eb16c855732d12d3ec27b51f9b61712cd904bb71e3be2', 3, '2026-03-28', '2026-03-28 12:42:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:41:42'),
(425, '00d8e03bee8e78a4aaec0678008fd33a8bee51f6bfecd7cf9fb54f8fdceef508', 3, '2026-03-28', '2026-03-28 12:43:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:42:12'),
(426, '2bb1658f4a4b04154af21a54e86f2a5a7b886dcf43cc5d4d3a81aca5fc7b1b5d', 3, '2026-03-28', '2026-03-28 12:43:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:42:43'),
(427, 'c5a11ac4ff902310e96ea0cbb1306220e0e5e40a86a4a76ad9653526f23a5df7', 3, '2026-03-28', '2026-03-28 12:44:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:43:13'),
(428, '7eced952c38e10c1f0e84060f0b0d9818f031503e6a3787cb63fd464594d4549', 3, '2026-03-28', '2026-03-28 12:44:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:43:43'),
(429, 'c55f4b2212436b968f85065ef04eb448ad6b36fa9d1513e33252fdbdfa0e5208', 3, '2026-03-28', '2026-03-28 12:45:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:44:13'),
(430, 'a3102cb8e29fe54ae8dbd63441bebc8c0e48c9e68624c3461401aa3d51e2be0b', 3, '2026-03-28', '2026-03-28 12:45:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:44:43'),
(431, '8bf839d8c2f88aab018a8b92ea7a0bc5ee3bf3134949fe4cc7319cd96006ba1c', 3, '2026-03-28', '2026-03-28 12:46:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:45:13'),
(432, 'db8b73fd8a8e9ee5e0ae0aa9cbb6e9428502bf8ba31fab88222ead57b75e15b3', 3, '2026-03-28', '2026-03-28 12:46:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:45:43'),
(433, 'd98952e937f25ef4b8e9c2d3402a87af1839b6cc277446245baf183b408288cd', 3, '2026-03-28', '2026-03-28 12:47:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:46:13'),
(434, 'f14a79b5b877f82667fa54b9bad0474a8164e56d69fe851879914d9ce1b52301', 3, '2026-03-28', '2026-03-28 12:47:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:46:43'),
(435, '4f39bfcc39469b90af7bbdc350bc24d8738982c7a95cee06f3ad1d27a61c2beb', 3, '2026-03-28', '2026-03-28 12:48:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:47:13'),
(436, 'f10bf1ddfbd957383b6ac29da16aec8aa85aa4f4756b05b09fa94fc486eec37c', 3, '2026-03-28', '2026-03-28 12:48:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:47:44'),
(437, '244a029fdeee30f693859f2f017310ec3a7ab13b8c552861ad2e0502fa1d3959', 3, '2026-03-28', '2026-03-28 12:49:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:48:14'),
(438, '3ac90e9dbe1e6d4627bba33b845f8b82a467a975b6d3e6537da0a0e91d5b2ed3', 3, '2026-03-28', '2026-03-28 12:49:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:48:44'),
(439, '3ae1e4f9011dfbd2195d38d813842cb1948aad286cdb305c08b45fe604ef0ad2', 3, '2026-03-28', '2026-03-28 12:50:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:49:14'),
(440, 'cca882af8301534b64a9be57aa7b4525e6063d0fca064992b7e92fd2586cf88a', 3, '2026-03-28', '2026-03-28 12:50:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:49:44'),
(441, '8f8fb1863c8f1df6b00f1d273e687f2478c2caaa739932ad82b92997e5976dac', 3, '2026-03-28', '2026-03-28 12:51:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:50:14'),
(442, 'e770e30a80e64e78f03b10efc7c5f8af4607e0970f9d23125bf5e39d9bba7daf', 3, '2026-03-28', '2026-03-28 12:51:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:50:44'),
(443, '1605daf0db079a4b4be3acbbfb1cac89db70eca678112ed5df7ac30d4dff6743', 3, '2026-03-28', '2026-03-28 12:52:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:51:14'),
(444, 'dfa8aecafc35cfeb58bc0bb422b1f4216bd5dacaa1c76f4c1a313a8a73bc6a7d', 3, '2026-03-28', '2026-03-28 12:52:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:51:44'),
(445, '12b42fbbd70217848de38c93fcb40d893c206799015c5188dc9b4d2180812e52', 3, '2026-03-28', '2026-03-28 12:53:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:52:14'),
(446, '668f10c5cf040c9248886a76ca304828b9fb70cbdf99c59e3590804443073449', 3, '2026-03-28', '2026-03-28 12:53:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:52:44'),
(447, '1ac27c38d39cf3368ebb7e720b3d9b24e414cab4cffaa2f06c4ac3ea722eafe7', 3, '2026-03-28', '2026-03-28 12:54:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:53:14'),
(448, '2b5b4ccd697f7f0d9c96ffd6dc0ed41cd0c83a996c48cfe4c9954b161bc76c2c', 3, '2026-03-28', '2026-03-28 12:54:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:53:44'),
(449, 'efbf96e8bf0cd3bcd111d2f2089a1ee4d3ea228d72431e1fd638ccaf8fd5cb55', 3, '2026-03-28', '2026-03-28 12:55:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:54:15'),
(450, '1b368e70119a530d5abe2ee0440928b02ece0c4001abc981ba5f1c8f6990d133', 3, '2026-03-28', '2026-03-28 12:55:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:54:45'),
(451, 'f9adafed26ce0e4f41c91e16b54b2ef7f4ab479852c30043f16f61c39aa4de25', 3, '2026-03-28', '2026-03-28 12:56:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:55:15'),
(452, '3499e53491a986e131f789c65250a941669920cb9fc2617a3cab0be0b037638f', 3, '2026-03-28', '2026-03-28 12:56:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:55:45'),
(453, 'b94738d0a72d60b6d566c828863fe23ff6525aa52454f94c40e01a8a9b65dd7a', 3, '2026-03-28', '2026-03-28 12:57:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:56:15'),
(454, '8baad5d2fbd263da4d61299c1a2485003c0a791ffca0e465e2b175e3a686425d', 3, '2026-03-28', '2026-03-28 12:57:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:56:45'),
(455, '00bd9dc6d1a8aa45d66d4ef5f6d7e60a3d5d892d214eb0c80ef1aed211739ab7', 3, '2026-03-28', '2026-03-28 12:58:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:57:15'),
(456, '8dfb8260b2c10d2a77b59a26392a7756d0b9a5df54b81e25976725aaedff99fc', 3, '2026-03-28', '2026-03-28 12:58:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:57:45'),
(457, '80fb46118430c9e3942dfa6ef39935c59b134c39c8a72d57ebcf4c73687ed265', 3, '2026-03-28', '2026-03-28 12:59:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:58:15'),
(458, '5759533392b075380d0e48e36e495fc226bb7899c83cc344b3e103a5a2c8f47b', 3, '2026-03-28', '2026-03-28 12:59:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:58:45'),
(459, 'f053fcc7a250223f40eda24b815124d19d6d2836674f06d9f56c009053e7cb07', 3, '2026-03-28', '2026-03-28 13:00:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:59:15'),
(460, '9f073c6501a2ac94048b717c9ac10c726ee86c66f9833c4d3d983a528c78dd26', 3, '2026-03-28', '2026-03-28 13:00:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:59:45'),
(461, '6f521efbf36f8e0b015fd78e8e8a42dc1b29899e686dfebbb54c406f756088e7', 3, '2026-03-28', '2026-03-28 13:01:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:00:15'),
(462, '65630c23732953bda742bed0e45e006537878def119949b4fb254b74f31f25ca', 3, '2026-03-28', '2026-03-28 13:01:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:00:45'),
(463, '4856fdbdda9677ce1d4ca3c91e6dae136bebda8455da6fa5c8ef0257c60b2767', 3, '2026-03-28', '2026-03-28 13:02:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:01:15'),
(464, '30719598251a28d9bad46bed4cd6c7ffe3f713a053cff759104969c079cf5fdc', 3, '2026-03-28', '2026-03-28 13:02:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:01:46'),
(465, '363f3ac6e4ced21a904a53a5854f0737fcb5f669910e648c934454d3dd3c921b', 3, '2026-03-28', '2026-03-28 13:03:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:02:16'),
(466, '6b31a99bda0ce4e98fab548f30d480356afdb010bd83845372f2ed1cfde7c529', 3, '2026-03-28', '2026-03-28 13:03:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:02:46'),
(467, 'ab65cffebd86d760e11f5a39032b601df973b7c9c1a371716890cb6a4969afee', 3, '2026-03-28', '2026-03-28 13:04:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:03:16'),
(468, 'c33c2266d992d1066d3f90ee5055cae260a470e20f08e567c0bde0be4fb21de3', 3, '2026-03-28', '2026-03-28 13:04:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:03:46'),
(469, 'ae4be37f299f49b481ce3ba22637f134aa609a1ba12d7c6069d1fe5d36da0aed', 3, '2026-03-28', '2026-03-28 13:05:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:04:17'),
(470, '23f8669e2c5f81db39d60a086c482baa3a86d438351ddccdf38993994566faea', 3, '2026-03-28', '2026-03-28 13:05:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:04:47'),
(471, 'bc000628afa2b545b2286b470fa72a6086a6b8c93f0277fc047e5545e1885c3f', 3, '2026-03-28', '2026-03-28 13:06:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:05:17'),
(472, 'd7c493a08b00d7de2c2213d1f3ce986bc123687ddf0f6d5d77b5d6e987da174a', 3, '2026-03-28', '2026-03-28 13:06:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:05:47'),
(473, 'fa0d2e331b38810533bc59a2b05a17de9de4708dde1a167e6227f6013e791ac6', 3, '2026-03-28', '2026-03-28 13:07:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:06:17'),
(474, '81affc3dfe5f04bc0a2f06755e42c0919336fada05b9f109f2683e3b15e7c93a', 3, '2026-03-28', '2026-03-28 13:07:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:06:47'),
(475, '3d5bad96ea12952347e92f4fd9b7d917758d47afc461449b6679c0bcdf2b2ec8', 3, '2026-03-28', '2026-03-28 13:08:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:07:17'),
(476, 'b9c018528cb895a7b925ba771ce1f712585afed082eb8303520378555402b4ec', 3, '2026-03-28', '2026-03-28 13:08:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:07:47'),
(477, 'a8b1e32a1542357d72431a9cd4d2cf0a949f4a754a4e4ff9e9c56e68f9a0df7d', 3, '2026-03-28', '2026-03-28 13:09:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:08:17'),
(478, 'f81a5ca48bf6a48c3b5146877e93de5f75d504d9bd28901967e6c9a383720235', 3, '2026-03-28', '2026-03-28 13:09:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:08:47'),
(479, '479f8fd1c86d6ce37a4c14f7f073713be4d43ec2ecf639c1a492b967ff3ff357', 3, '2026-03-28', '2026-03-28 13:10:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:09:17'),
(480, '957a6bd89630cbb46b57ca2d20e73f1ced26ad2ab40d5538d2f9b7b1718d76f4', 3, '2026-03-28', '2026-03-28 13:10:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:09:47'),
(481, '673360e688576991f7a0c2d590b7177f2df0853a23d3e8bacf03b228594a055a', 3, '2026-03-28', '2026-03-28 13:11:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:10:17'),
(482, '988859343bba2e67e47680ced89adbf0829f79bf5ecc12a2c16cf52e933d9c5b', 3, '2026-03-28', '2026-03-28 13:11:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:10:48'),
(483, '84a1e5941ed8aefe4e32e3c6b9826e7b879c763ebbbbfc02d7a79d4d94221e5d', 3, '2026-03-28', '2026-03-28 13:12:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:11:18'),
(484, 'dde400e1b1d4b27bed5c4cc7d89910eb746078828686bdc5f21a48ae995b2c2a', 3, '2026-03-28', '2026-03-28 13:12:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:11:48'),
(485, '40ae32ea3abd72db35b64bf689749e65e1d9e0a42aec077c6382dcb3e954fedd', 3, '2026-03-28', '2026-03-28 13:13:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:12:18'),
(486, '04c814d9cf1c5979d5361aa59a02a17ce8f04a5ea3eac84b56525e4beed8a9f4', 3, '2026-03-28', '2026-03-28 13:13:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:12:48'),
(487, '5917e7f2a8cf63b632150ab5948de247ef9ab9ebcbc2698ff4570aa397e61ab3', 3, '2026-03-28', '2026-03-28 13:14:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:13:18'),
(488, '34d80b3f0435b342a1de10bd6fe61d3362da1fe9279ee0adb7a3e41cb4e303c1', 3, '2026-03-28', '2026-03-28 13:14:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:13:48'),
(489, 'd3209f5115a50350f993743466aef4eb040bec338699476dab88404401f5206c', 3, '2026-03-28', '2026-03-28 13:15:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:14:18'),
(490, 'a52a393f01554263087e9e1b8a2027be4fdcf36cc28aaa1aad25cced391515f8', 3, '2026-03-28', '2026-03-28 13:15:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:14:48'),
(491, '5d37904ade2914526ec9371b0cad4a997a3a060a94fdce4c3f40363da175b995', 3, '2026-03-28', '2026-03-28 13:16:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:15:18'),
(492, 'da188cf8ab6ef168c2ac8333d728ae47eee7f4517abea81129b3245b64335a48', 3, '2026-03-28', '2026-03-28 13:16:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:15:49'),
(493, '44ad706b10a8fb1d114f3944cb73d1c01118e9b2201cbb00099ffd86a582d2fc', 3, '2026-03-28', '2026-03-28 13:17:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:16:19'),
(494, '92611dc3b2109d884de78f5905aa7c790070b7785fb3ae342b69b1e8a314ba79', 3, '2026-03-28', '2026-03-28 13:17:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:16:49'),
(495, '0e0bd4bc841462af9868a7d52d7b4ea69171ea3f7513f062fbe8705647bf37d2', 3, '2026-03-28', '2026-03-28 13:18:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:17:19'),
(496, 'b6289e8ec2c37dfa116dcbc534f96507da961b89e97acf38cb9912b096d0c770', 3, '2026-03-28', '2026-03-28 13:18:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:17:49'),
(497, '8024e6a1ee4ff41502b525f4633d6308533382cd2ea59cf09db2e27953cea45c', 3, '2026-03-28', '2026-03-28 13:19:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:18:19'),
(498, 'e947c78c3a830ca9e5400dff83db1312739bf34843488558a0eae6c51ef49a8b', 3, '2026-03-28', '2026-03-28 13:19:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:18:49'),
(499, '15ec5c39fbde4ac7ad17e4fe76bd929608d2cffe7cf9b50fe3902e09c89fb89d', 3, '2026-03-28', '2026-03-28 13:20:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:19:19'),
(500, '0c5362acf826c19640a56c58f5786234a56ec0e518b3528cfd4f192ce2c03b68', 3, '2026-03-28', '2026-03-28 13:20:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:19:49'),
(501, '183d9dc58b6291f4c11157df7d493a4e2c9089b6b5041ef3802ee8b94a2c4898', 3, '2026-03-28', '2026-03-28 13:21:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:20:19'),
(502, '71c7f3fd077fc29e9aba8f77246dd9108128c1f978fb0385df1c3b1085385193', 3, '2026-03-28', '2026-03-28 13:21:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:20:49'),
(503, 'd8d0e13ea90ad3cba8df6c0dd9b973e572a44a983c3ad8e610cf56f64190a795', 3, '2026-03-28', '2026-03-28 13:22:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:21:19'),
(504, '169f684272ecc8bdfb42ee3c619309592f65226c3642a08629eeef0ea29f55d4', 3, '2026-03-28', '2026-03-28 13:22:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:21:49'),
(505, 'a63fd7ab4e0905e0094a58f540d6e4e3521b429c56553fb8775aae471b6633de', 3, '2026-03-28', '2026-03-28 13:23:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:22:19'),
(506, '4986abb152998b2ce7cb0964e7a415cf2173eab8e3fe2adec363e9ba20e82abd', 3, '2026-03-28', '2026-03-28 13:23:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:22:50'),
(507, '9d5651c7ac292eb11d93cfdf9c90f984809e614732d89fad36ef100b7edd0ce5', 3, '2026-03-28', '2026-03-28 13:24:20', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:23:20'),
(508, '9faad817b9b74925674a6847ac667e131e5bbddaa8fcfb625be45e12fbae95a4', 3, '2026-03-28', '2026-03-28 13:24:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:23:50'),
(509, '4dac6446812b5dc0c578e840da0b9da27caa3c1b990cacb179197e276b2884da', 3, '2026-03-28', '2026-03-28 13:25:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:24:21'),
(510, '9f7a1651a1831efe74f0a34b026342bd13d159b077bd626f91c66da3000527b3', 3, '2026-03-28', '2026-03-28 13:25:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:24:51'),
(511, '0e36711353fc80517e5e29474108da10efcf4edbf90daccaf7caa3a0c9a554ee', 3, '2026-03-28', '2026-03-28 13:26:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:25:23'),
(512, '95299254ffb04bf6bdcbe6ec7b21c21fa559643c232c7dc43bf25a52dfef5b5e', 3, '2026-03-28', '2026-03-28 13:27:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:26:06'),
(513, '61f0b0babdd29a30c55e6f7234aea5ba1f23413630626279a0c1ac7908fd6f72', 3, '2026-03-28', '2026-03-28 13:27:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:26:38'),
(514, '5813e7259059c8693a2d0152acb69c009cfa75865572081ab72c171181e01020', 3, '2026-03-28', '2026-03-28 13:28:09', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:27:09'),
(515, 'fc75d9d2932f8935ec51eb5552cfd507df7f19b3e8151dbcbcde15d8fb0c3cd8', 3, '2026-03-28', '2026-03-28 13:28:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:27:46'),
(516, '72aadbf9319bc01104a793f4d53ca31fb8b7ad99fc98659b3cbf81016b8fec3d', 3, '2026-03-28', '2026-03-28 13:29:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:28:45'),
(517, '6be46ade7391df91c924718bc757c31c5427a5736467aee483c0be2bf7fb11a4', 3, '2026-03-28', '2026-03-28 13:30:22', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:29:22'),
(518, '775c53dff0444e80d6ac5fe7407f3203913e3672f3986b83d04bf9a54c8264ea', 3, '2026-03-28', '2026-03-28 13:30:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:29:53'),
(519, 'a591dacb384fd11baa3475cd90e2fefa409cbc7a436f5b591b3a599e848bc9c6', 3, '2026-03-28', '2026-03-28 13:31:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:30:23'),
(520, '6174422f8a70205931ca2fde535c639ec0d7be4cce753de9b3307bd6a7ddd5b5', 3, '2026-03-28', '2026-03-28 13:31:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:30:53'),
(521, '6d0b7b97bb9fb67882c728eddf1f610f90324b620846041fb6022f9ae9848e7f', 3, '2026-03-28', '2026-03-28 13:32:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:31:23'),
(522, '32c59c253b8e0d17c4bab56abbc4dcf3e9432757164e91a360ae29e99cf74ecf', 3, '2026-03-28', '2026-03-28 13:32:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:31:53'),
(523, '8c583bf632d932cc9483de6c516df38f4d20a49a22f8293d15e7f0ed60d5fb92', 3, '2026-03-28', '2026-03-28 13:33:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:32:23'),
(524, 'f7031b3e6f36b8d0a15188ae222c8144e3a3ced39da4344eac435200b2f22fd7', 3, '2026-03-28', '2026-03-28 13:33:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:32:53'),
(525, '1f991ed2b7fe57a44568f8c300824c6cb8c8b8224e560f3fbbb9ff452192723a', 3, '2026-03-28', '2026-03-28 13:34:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:33:23'),
(526, 'cb9c6ee039422f2daf1d7931287173b4053464ef9db099a2099d58763304550a', 3, '2026-03-28', '2026-03-28 13:34:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:33:53'),
(527, 'fceea5c7800c6d70cbee2a77bb32d09684fc261246649aef077bff3943132dcc', 3, '2026-03-28', '2026-03-28 13:35:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:34:23'),
(528, '9c351afaefd12379a34d786ef49a0c10a065241a4502b5129052a341476a9641', 3, '2026-03-28', '2026-03-28 13:35:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:34:53'),
(529, '1b47a7d5a708c32b8012f8e04c20e692e5e791da0038a014192fdaeb02c63bd9', 3, '2026-03-28', '2026-03-28 13:36:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:35:23'),
(530, 'ae0fae14863a3d05ecacef7eba93f343ab1fa180da657f54197f449c39306873', 3, '2026-03-28', '2026-03-28 13:36:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:35:53'),
(531, '9bfeca5fef5fcb106548343a872f5aa8199eadead05448f323ba895a5460b431', 3, '2026-03-28', '2026-03-28 13:37:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:36:24'),
(532, '4e1cdd1cfae9e4700a73cc964b476cab8cf5f8d0e8d58cc5ab7b7298e8a1fff9', 3, '2026-03-28', '2026-03-28 13:37:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:36:54'),
(533, 'eea818ac3f203dbd4d95341960e4be102aeade978b2d599a07cb49ec8c7b229a', 3, '2026-03-28', '2026-03-28 13:38:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:37:24'),
(534, '44d3ee5f0f566c6fc54510b9789566a4d3d741757d26157e3284733fd945fef7', 3, '2026-03-28', '2026-03-28 13:38:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:37:54'),
(535, 'dafb8b22a19d0a9e24aa8d0dcaa8e991caf921271583cb6c1e7dfd97450caedb', 3, '2026-03-28', '2026-03-28 13:39:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:38:24'),
(536, '21302259fcabc03d09c1350622b75b2bc3fe9ab03cdcf9c157c494e788a8a0e3', 3, '2026-03-28', '2026-03-28 13:39:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:38:54'),
(537, '5774836a66ed25637d1d9cd3b946455e2acae19b2e55856cee8f92d2e1f14762', 3, '2026-03-28', '2026-03-28 13:40:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:39:24'),
(538, 'f58aadadff76640cf8e2dd4052464e368b9787af8b48333b662b066e16e1e14f', 3, '2026-03-28', '2026-03-28 13:40:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:39:54'),
(539, '3989a1df25bc3530b2427d969bd60f6e1ed5bd9d4f73764be492ab5f7671fe8c', 3, '2026-03-28', '2026-03-28 13:41:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:40:24'),
(540, '47cf121cf2e3d693a8c3c843755db5bb64227afb8fb091c8afb89c638baa6df0', 3, '2026-03-28', '2026-03-28 13:41:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:40:54'),
(541, '084851f6ec422d985f9e794ce1a14f432f37ef05d4f2c42af591f48be0b2360c', 3, '2026-03-28', '2026-03-28 13:42:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:41:24'),
(542, 'c63367b1667e7eaa3a0aab0292f8ef6569d10664441cf94a3dee76ef1b405d92', 3, '2026-03-28', '2026-03-28 13:42:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:41:54'),
(543, '58540281fa20851c4e4f800b06c98c6d46f03e122809605ced800ceba92f3665', 3, '2026-03-28', '2026-03-28 13:43:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:42:24'),
(544, '2dc520c9a434811378d9457b0504eb690d9b3965e848f5b80a2023dd54393506', 3, '2026-03-28', '2026-03-28 13:43:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:42:55'),
(545, 'dfb93f3da92917b9af6ae9cc079db9a58fef2f5426b05b96c0e42f083113e5d1', 3, '2026-03-28', '2026-03-28 13:44:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:43:25'),
(546, 'db6780c91cabacd59ddfd491f1c138e7bb6298223bea26977877aa260cc77f1a', 3, '2026-03-28', '2026-03-28 13:44:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:43:55'),
(547, 'bf62176e6cb9409ec165607b2e32604b17be1d28259bc53cb7e29f0d4edabcda', 3, '2026-03-28', '2026-03-28 13:45:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:44:25'),
(548, '435359cb4401b32fbf7f7d1e12a9344d647a9ac7d9b8b0f63684ee0c67cc455d', 3, '2026-03-28', '2026-03-28 13:45:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:44:55'),
(549, '10070fa856cf5bfc2daecd5fec762b0c8ca23090dd5e8a3bc74592547191e4bb', 3, '2026-03-28', '2026-03-28 13:46:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:45:25'),
(550, '2f705435752173f3c5d8753b3e9855fe31bae035fc1bf16df4994b48f1ade90e', 3, '2026-03-28', '2026-03-28 13:46:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:45:55'),
(551, '69d79b6f332143e506abaa3ddc52be257aaa5751bec7817fb033fea9447762aa', 3, '2026-03-28', '2026-03-28 13:47:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:46:25'),
(552, '81864e462725dc1d1e10c18360df19eac7a10fe3b20dd6d3dfa2347661ecf861', 3, '2026-03-28', '2026-03-28 13:47:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:46:55'),
(553, '621a93ed3ed3c9253868d163dd4399ae9616f99895ec4cfa875fc26949d6bcfc', 3, '2026-03-28', '2026-03-28 13:48:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:47:25'),
(554, '7a3bc057167821159e3026bde7e81cebd6e9e96bcae8f76113744574ca2bf29f', 3, '2026-03-28', '2026-03-28 13:48:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:47:55'),
(555, 'af2f3f6a7e4ca6ca947e31e6605ce7cc5628640d58b1d9be33d41d3ce8308123', 3, '2026-03-28', '2026-03-28 13:49:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:48:25'),
(556, 'dda16d80d0235f8e402d081ab0266d701d139ca52a29eb434cd602e96be1f1e7', 3, '2026-03-28', '2026-03-28 13:49:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:48:55'),
(557, '51bd14d63a3dfbb247dfb801d743fc8638a4d8b007f6d59f9a1ebab1a0de0115', 3, '2026-03-28', '2026-03-28 13:50:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:49:26'),
(558, '45f43111661efac3fbf201e84100a67c13cbfb9ca3cb1eb701efbd4dbe179acd', 3, '2026-03-28', '2026-03-28 13:50:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:49:56'),
(559, 'c30a97bdd9d9e0014b9993707ab3b70a0a2a98399ca6578df7feaebb408de308', 3, '2026-03-28', '2026-03-28 13:51:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:50:26'),
(560, '2d1c6767ae297a779a32abd4b65aabfca865a88649337e8b3a3b4c199afe55ed', 3, '2026-03-28', '2026-03-28 13:51:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:50:56'),
(561, '0fc2088754e6eda5f50c158df66848549bc4a84461d9edd415e53329d5b2d017', 3, '2026-03-28', '2026-03-28 13:52:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:51:26'),
(562, '2737b1cb1b6fe7031c066558790a3ca491b99942591428625857d4a78d5f24e1', 3, '2026-03-28', '2026-03-28 13:52:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:51:56'),
(563, '67872bce0bd07e0ca40ba7e55c5cdfa005bcc49a0da83e044361533a411aa10e', 3, '2026-03-28', '2026-03-28 13:53:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:52:26'),
(564, 'ed7842175f14dc853f82e1e0d98494ad052341d9bfc83e5aca950f7005b46f04', 3, '2026-03-28', '2026-03-28 13:53:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:52:56'),
(565, '4ec067c6d353ba9699c145a72756d31961d21e9ac2b53b6dcf2f238868fdfafd', 3, '2026-03-28', '2026-03-28 13:54:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:53:26'),
(566, 'caee1dad43d8856cf112cdf958ef8751859e481675cb57b88f2fdbc5821ca00d', 3, '2026-03-28', '2026-03-28 13:54:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:53:56'),
(567, 'edaee5076e82ab7131dbb8bbfc54f34a33fb43146ee7c43cfe68c59626fc27b2', 3, '2026-03-28', '2026-03-28 13:55:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:54:26'),
(568, '30912584e7af553efe25831de4b4e643499e87930f89317c50cff3f99cb0f2e0', 3, '2026-03-28', '2026-03-28 13:55:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:54:56'),
(569, 'b7c9f085dabcd17ff9f74500195bfed61a86064e4b8adcb2a31cc94805c3cd47', 3, '2026-03-28', '2026-03-28 13:56:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:55:26'),
(570, 'f8176b80ef8200a1360455f0ff80cf058b03a8f70ecf6bfc3968f91ce8beb52d', 3, '2026-03-28', '2026-03-28 13:56:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:55:57'),
(571, '955abc4c73c3eeb1769f00da092998dca6fcbf02027ab0b04a4a8547a3f02ca7', 3, '2026-03-28', '2026-03-28 13:57:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:56:27'),
(572, '09fdfde9686bc6b66e6241a93b56318bd5066b9a1da671ebb26580e11a8c2c35', 3, '2026-03-28', '2026-03-28 13:57:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:56:57'),
(573, 'c14f8ff702f3c8f3cf8a94d2adbe74004b1d208fa6aafa4e4e51a08c5315546b', 3, '2026-03-28', '2026-03-28 13:58:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:57:27'),
(574, '5a55bac7b84a9c74cc9b39998a8a3ef5231be8664e920cdeb4530053907dbe36', 3, '2026-03-28', '2026-03-28 13:58:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:57:57'),
(575, 'd1a99336e8eaae68cdba9fc0b3d18454f1f1973afb55c06074169a960e8fe129', 3, '2026-03-28', '2026-03-28 13:59:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:58:27'),
(576, 'a829bf959cb3f3b4b146f61290ad416a1b1acea0c766087325e38de6e9e2a9ad', 3, '2026-03-28', '2026-03-28 13:59:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:58:57'),
(577, 'db9755650b1297920effde0d734ba3158d963eba454528a007b58524cb4892ab', 3, '2026-03-28', '2026-03-28 14:00:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:59:27'),
(578, '9affc68690d7d525f4ebe94cfd6676f8e5c76f17062b7477f3837052504def46', 3, '2026-03-28', '2026-03-28 14:00:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:59:57'),
(579, '7786f35905dfe27803605ca32bec01642d5f1e852f5da2ee246e442980cd8bd9', 3, '2026-03-28', '2026-03-28 14:01:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:00:27'),
(580, '89fc31f6cf5966789ca07ff2eeea2e001fe690abda3a39f6f0575a2a47ce6d9d', 3, '2026-03-28', '2026-03-28 14:01:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:00:57'),
(581, '59335ae4addcc6bb6a64d7920cf52a5b01148ad4f235b1279b98a4ebb3a111dc', 3, '2026-03-28', '2026-03-28 14:02:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:01:27'),
(582, 'bf0c13cf49b4f8a58c48a012b3035d557857b53d63f82bd474beedae8c44ac0e', 3, '2026-03-28', '2026-03-28 14:02:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:01:57'),
(583, '596f78e3b1254a2435940278fd497afe1ee8e7e66862ce9bef80c07bac988964', 3, '2026-03-28', '2026-03-28 14:03:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:02:28'),
(584, '2acf7158405c08a90339695bbe98cb415a7461f452d5a9bd96454765700e8fda', 3, '2026-03-28', '2026-03-28 14:03:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:02:58'),
(585, 'cdcc646240e9e010fd5eab85650ba9789584e103b35b4ce8b99eb60a637dadba', 3, '2026-03-28', '2026-03-28 14:04:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:03:28'),
(586, 'd1611a64827df0275adc584694772921829db5ba5eb29487f1b822c37571e4e7', 3, '2026-03-28', '2026-03-28 14:04:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:03:58'),
(587, '8c4d85e565cff12b2ed861228f23ad58b880910cd5902119fc19877e65dd0733', 3, '2026-03-28', '2026-03-28 14:05:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:04:28'),
(588, '159c1fbbdcf39382c66627aced37765e2292b6e9d936ed177b12e4a1324ff126', 3, '2026-03-28', '2026-03-28 14:05:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:04:58'),
(589, 'd37666fbc8698e9e1578cd1a120f6e621fe45c95c7106f7eb71aa994486ed436', 3, '2026-03-28', '2026-03-28 14:06:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:05:28'),
(590, '1e173e47c68e4f7399e671839d1f364ad4be29e46eedb11c16deb1de628d0554', 3, '2026-03-28', '2026-03-28 14:06:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:05:58'),
(591, 'd6106c45d3990c26662019492945745df7c984aaed0767875db2d29515a785aa', 3, '2026-03-28', '2026-03-28 14:07:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:06:28'),
(592, 'bec24a7be85fb5cad12f2fb81acee557efa607a6742152a30807330f5d2dc64a', 3, '2026-03-28', '2026-03-28 14:07:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:06:58'),
(593, '1217e43e6fb4cf2d1a6511c29d9e3e9bc03ea4de15388da5bc64037cd76fcf71', 3, '2026-03-28', '2026-03-28 14:08:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:07:28'),
(594, '5e62aaa4cb20e50cade46afc2bcdc2e9b543f6d3e8c963dd08bf4975a8a13cf0', 3, '2026-03-28', '2026-03-28 14:08:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:07:58'),
(595, '4516dcf9be95fd8756cedb11d4ef5da12c127a0c7300140e19d1fafce3766063', 3, '2026-03-28', '2026-03-28 14:09:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:08:28');
INSERT INTO `ta_attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(596, 'df14533b19257d8b0899446077766fc1e47dd1af02b7a54b3d8b5a755e589584', 3, '2026-03-28', '2026-03-28 14:09:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:08:58'),
(597, '70ccaa9ff33acc4135b5b1dd17eb11eabcb3deb1d1933b1a2b6d3547e56fe53c', 3, '2026-03-28', '2026-03-28 14:10:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:09:28'),
(598, 'b9a4866023b5f3570ac1ee58b33f3b469d89c36979727ca4977d7045a68bb5e9', 3, '2026-03-28', '2026-03-28 14:10:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:09:59'),
(599, '63994069c1ad66e0b36040819f75d9973d154dec280090a535835fef4552ba3f', 3, '2026-03-28', '2026-03-28 14:11:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:10:29'),
(600, '7ed7ce3f3d660bc3f2b6cb084ad8603a7c8ef0160e8e539128e73b797ddd6979', 3, '2026-03-28', '2026-03-28 14:11:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:10:59'),
(601, 'a36c0e7f35bd9161c743f95c912d82f08349dd7f89e6a3486d5b9ca03e6d08bd', 3, '2026-03-28', '2026-03-28 14:12:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:11:29'),
(602, '98ebf5c161ac3126f7da3e8e91318b83b1909880d4d88d8e7a101ae2db07da68', 3, '2026-03-28', '2026-03-28 14:12:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:11:59'),
(603, '3fa62b16f66a896b6f2548319637f97b2b9b2112e814f4d39cfed1ab6e8de30f', 3, '2026-03-28', '2026-03-28 14:13:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:12:29'),
(604, '2cba03165b3864d743e6eb6ec91f9d297df3138a6d2b30e8c9cd9250af58ec4b', 3, '2026-03-28', '2026-03-28 14:13:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:12:59'),
(605, 'c2cb3228d6420bbe3f64bf6c9e98300b14d032d89ce987c987e9084ab1ad28cc', 3, '2026-03-28', '2026-03-28 14:14:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:13:29'),
(606, 'bb33bb568d8c71a5fe0a12b2e7c6ca30eee8a8acd3fcfb1dc3b52d699ec0ba6e', 3, '2026-03-28', '2026-03-28 14:14:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:13:59'),
(607, '546cb8135fac77ee449342ceb510879b4d5c5ce63aa12e5e5b412d6f82d58c23', 3, '2026-03-28', '2026-03-28 14:15:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:14:29'),
(608, '91070290af2f203d006a09d265237f9fdb7a13f2e6a1e0c91b7b3b350c20a379', 3, '2026-03-28', '2026-03-28 14:15:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:14:59'),
(609, 'a838d05d088f8aeb22fc6a2dc039e5f35c60157b49f9bf2fb18068abbc6f5b80', 3, '2026-03-28', '2026-03-28 14:16:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:15:29'),
(610, 'e0e215c538ddcb878dc22c357490bc8651dd0a061ea9525ec30c8339e18b4fbc', 3, '2026-03-28', '2026-03-28 14:16:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:15:59'),
(611, 'd24195c3c74be879b2f4c4022caec14110353ab2dba9e87a9e6e157d3b798f68', 3, '2026-03-28', '2026-03-28 14:17:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:16:29'),
(612, '5201bdcf42d1d5121abbd1e616a6e7028a47952825d6d7071b9a1927b1349eeb', 3, '2026-03-28', '2026-03-28 14:17:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:16:59'),
(613, '3ff638979082d6baf3d5c68c23277e904ffb7cf6641c8f868064c5861d8f3ddd', 3, '2026-03-28', '2026-03-28 14:18:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:17:29'),
(614, '4019c7effbf8fd14c8f69a35e70ecdf2c2e381f024a2c684488d1ba5d2361970', 3, '2026-03-28', '2026-03-28 14:19:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:18:00'),
(615, '1436a800cd8215b6e82c738be872d255889377f2dac55082be0d8160fa763513', 3, '2026-03-28', '2026-03-28 14:19:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:18:30'),
(616, '95016e6407509a0412716cd0841378ede81f2668498a3baf9b5c44c062e1c8d7', 3, '2026-03-28', '2026-03-28 14:20:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:19:00'),
(617, 'ec544844b4d93cf36a855fd5124c97717ce24962e42639187efcc9156b423d71', 3, '2026-03-28', '2026-03-28 14:20:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:19:30'),
(618, 'ddc913a6ca953fbf191654e6754186aff272eb39fa70fdbd291b07ea5430fe29', 3, '2026-03-28', '2026-03-28 14:21:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:20:00'),
(619, '51505385a7b9e49cca5593638d854557593f2f3af6219d4c6602d03e0adeffd6', 3, '2026-03-28', '2026-03-28 14:21:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:20:30'),
(620, '98db8e071317bc4ab9d572147753377d85927d1c409f3c754bb9b341c8384da3', 3, '2026-03-28', '2026-03-28 14:22:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:21:00'),
(621, 'bce0c198853b64872602ae4f671816f9713d39c50b2b48a1d6e81b6ef62d750a', 3, '2026-03-28', '2026-03-28 14:22:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:21:30'),
(622, '705217c1aac1563a95e5aab86bd93cfe646ff76112ac3088a930987030604505', 3, '2026-03-28', '2026-03-28 14:23:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:22:00'),
(623, '6c140db1a7264c05b01695dcf7807a7a5654c9bf26d57f71fb9d06e4353dff68', 3, '2026-03-28', '2026-03-28 14:23:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:22:30'),
(624, 'ca7757f4ffd0dfc5cf4bfe05e4ee898d5ad41b255f887e40de70e5c78cdbcb15', 3, '2026-03-28', '2026-03-28 14:24:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:23:00'),
(625, 'b5328c782d71b397cdace8171117b8c1dd9b19a1735ae2de8e746057de7e546c', 3, '2026-03-28', '2026-03-28 14:24:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:23:30'),
(626, '36412bef5ba4ecc1717e90cdfabf9bd937872614bc7e2b3814ba51bcb26b4e47', 3, '2026-03-28', '2026-03-28 14:25:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:24:00'),
(627, '5d3c433be0a8c1ceb70b4bede92c8e4ab6c08563bc7c3139813bfe257e40389c', 3, '2026-03-28', '2026-03-28 14:25:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:24:30'),
(628, '1304a837387cb09762302ef212a7fd18a5c43096bec0704b8bb1b9c51060ca07', 3, '2026-03-28', '2026-03-28 14:26:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:25:00'),
(629, '13e8f82c08c7ba942f2f1a60b168a42ae55aab9c40db2c3dbe3ddbb1bddee073', 3, '2026-03-28', '2026-03-28 14:26:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:25:30'),
(630, 'e1d716ab6c8cfe65d391868ebb1df2b00f6484fc8cbc4ce5a7651feb29f909af', 3, '2026-03-28', '2026-03-28 14:27:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:26:00'),
(631, '7be818030e347c76d214d7ebeaeb0507a43ce04c6cf68e7e007678fae944126b', 3, '2026-03-28', '2026-03-28 14:27:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:26:30'),
(632, 'c53ff0f48edca91a17a12c2bafde15c1965c5c9f8381efdcf75829a01ba2262d', 3, '2026-03-28', '2026-03-28 14:28:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:27:01'),
(633, '55032af8365b17b34c81c19355c5c2431478d9743f6f17dd78de0897134664c0', 3, '2026-03-28', '2026-03-28 14:28:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:27:31'),
(634, '078e70285d89e0cdb637e0c3d24ab66416891598c643c7bce30704a07bd09c18', 3, '2026-03-28', '2026-03-28 14:29:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:28:01'),
(635, '7f2a01f5497fac485cce1eebd65b0c8f4a76ab47ad8a6d33b6d3321e5e49f37e', 3, '2026-03-28', '2026-03-28 14:29:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:28:31'),
(636, '721f19728ebf8052758d1b2f53f8259bfc9b65ff0db4d5882f2b6ba9b7f0c666', 3, '2026-03-28', '2026-03-28 14:30:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:29:01'),
(637, '524466f94f843615b78e4907746598dbc562a0db4e2bad9db6f9cf6b106dfe4b', 3, '2026-03-28', '2026-03-28 14:30:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:29:31'),
(638, 'ce6f83ff1961eca3b159b95978027f4fd2f633f2b0f0f3ec4caf5eb699480d29', 3, '2026-03-28', '2026-03-28 14:31:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:30:01'),
(639, 'd9bb768654e984b83494fda29836247d2fa187512ae629197278acd6df61c744', 3, '2026-03-28', '2026-03-28 14:31:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:30:31'),
(640, '76331e47763ce3a36eb2919599a01061a5df4856c45b1996f78f2d0e4d988194', 3, '2026-03-28', '2026-03-28 14:32:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:31:01'),
(641, '87a95ad798cd4010c026160759d768218e85389cc7184b7eb073569e8f648fdb', 3, '2026-03-28', '2026-03-28 14:32:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:31:31'),
(642, '05c897ed6d15f48f955c5679f981df145c59f26d0f86220b2d595d7da6776279', 3, '2026-03-28', '2026-03-28 14:33:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:32:01'),
(643, 'c8231b033adf672bbcbba8a32ae706f39a501a22f60c75a1c32b14a6f4be4aef', 3, '2026-03-28', '2026-03-28 17:14:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:13:54'),
(644, 'd233d54b06ec29401b30305e5a3f03035c113ff796230e874e800a772f409923', 3, '2026-03-28', '2026-03-28 17:15:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:14:24'),
(645, 'd5b5e21c429f33fd5a5ce7fd7b57c7332186d59f38b98f2c075889302d25cd60', 3, '2026-03-28', '2026-03-28 17:15:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:14:55'),
(646, 'bcae46fe1df61012b2d98f9a90f1618a39b89673cb53311817b138135eb6503c', 3, '2026-03-28', '2026-03-28 17:16:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:15:14'),
(647, '1577105efba7b194326a30e3dc1f26dda22cf69a9c8689efe97fc9020f27a0fe', 3, '2026-03-28', '2026-03-28 17:16:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:15:45'),
(648, '709d431ce1a122524a2f2c22fd14144e18a7d50d8103cf83908da335fd148e8c', 3, '2026-03-28', '2026-03-28 17:17:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:16:15'),
(649, '06fd2dbebae3c0b096ec979cae2743b222486128773fe95fbb08a9b3a1af7bc2', 3, '2026-03-28', '2026-03-28 17:17:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:16:54'),
(650, '88f85e643e7ce5144698b1af55f3d721b10ba9300b55296c50e4e94e10729dcc', 3, '2026-03-28', '2026-03-28 17:18:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:17:27'),
(651, 'fd4bc859ea8d3ef09e16de6e82954121c7bd9248aef8aac279620f3c6e8b0a33', 3, '2026-03-28', '2026-03-28 17:18:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:17:59'),
(652, 'a7b9eb76204c8da66cd7667224fd3104cbbe852016607d94803059641cc15c67', 3, '2026-03-28', '2026-03-28 17:19:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:18:30'),
(653, 'b8b108f22b17ca323a4e241623a26b574977b57a03c99e488057db0fc0a88e9c', 3, '2026-03-28', '2026-03-28 17:20:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:19:21'),
(654, 'ee09d5a54e3b2e2ba4126a83207633555599ad72e83a137065958da5c831c879', 3, '2026-03-28', '2026-03-28 17:21:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:20:11'),
(655, '2088786fdbcfdd86d9cd63c64536631a6e68ed6beac4f6807fe7e3ad0a1ce641', 3, '2026-03-28', '2026-03-28 17:21:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:20:59'),
(656, '9dd91e00a219d5869560ca9f0dd80eaf7a53280883cfaa483f31af4be7ba8923', 3, '2026-03-28', '2026-03-28 17:22:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:21:38'),
(657, 'a73b68ddadf21b1dc8b42c080a8bc264f49600d3afaa64c4e1955bb2349c852b', 3, '2026-03-28', '2026-03-28 17:23:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:22:10'),
(658, '8643854eb2b97497c1faec543d37cf7bd9a840324a7695f28a0b152cf3ec463d', 3, '2026-03-28', '2026-03-28 17:23:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:22:40'),
(659, '52cfaf0d83a849083bd94a43c540fd0fccf4da1f9a6c96621158362decf15e82', 3, '2026-03-28', '2026-03-28 17:24:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:23:10'),
(660, 'db62930af414cd44727e56255fd516b250306d3777dd2559453d892657bb5b19', 3, '2026-03-28', '2026-03-28 17:24:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:23:43'),
(661, '71fc25172743386616cb54acaa403b04f42a5772f4766ad0618487d8846aa111', 3, '2026-03-28', '2026-03-28 17:25:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:24:34'),
(662, '48ceb02ac977bcba5585baa2d719e1be5588f3e8a5bf6c8c0ff8f80d88bae16c', 3, '2026-03-28', '2026-03-28 17:26:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:25:05'),
(663, '13ec63dfed16c9cd0ba818cf0584c1d4bdc0c23527e7a6a7ee674f3698ec7381', 3, '2026-03-28', '2026-03-28 17:26:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:25:35'),
(664, '33e111bbb44dc8ba8dcf13ac1d57eb4f36541bf359990f422eab41b33749ba4d', 3, '2026-03-28', '2026-03-28 17:27:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:26:05'),
(665, 'fb4ef73ad79409e24d058801dc539f933dd13eb06262332d6e3d0282b0530f6a', 3, '2026-03-28', '2026-03-28 17:27:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:26:35'),
(666, 'ee584b2d0a4fa58348ed2e9fb58728445e30eac8b93c9bfdc85d990e7a47d4de', 3, '2026-03-28', '2026-03-28 17:28:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:27:06'),
(667, 'db68c799baa367919c17df16f15d6611c8211ccd62fe0022519ecabcb4ba9e18', 3, '2026-03-28', '2026-03-28 17:28:39', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:27:39'),
(668, '3a706126cd4defa402099ce2366d22f2ebe010595d852a4bacfe2921365f52cf', 3, '2026-03-28', '2026-03-28 17:29:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:28:31'),
(669, '3c80d45111badd3da93ff726ab5f4b135c04b098e5e41b5f5a28a2744fa459c2', 3, '2026-03-28', '2026-03-28 17:30:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:29:02'),
(670, '09257ecb0735b52a174adbc5675aa69199e05f0161902f49995049f812540315', 3, '2026-03-28', '2026-03-28 17:30:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:29:58'),
(671, '28ed0e9fa2358d05532ca3a0e17a38d917e6843c9cc06193ceb9f0a2005fc053', 3, '2026-03-28', '2026-03-28 17:31:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:30:35'),
(672, '4dc28df029d90cfb3f630aeb6264ddadb0f43a6884cc27f4a76618f053ffb613', 3, '2026-03-28', '2026-03-28 17:32:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:31:06'),
(673, '10f2c83908c1fa989f82f7c85173139a765896585b06c9914928fe6c2f73f2f2', 3, '2026-03-28', '2026-03-28 17:32:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:31:40'),
(674, '8ff41acd8f02723eea8e8a0ac0432e72b21692eb344bb9de59b09f86799b9ed0', 3, '2026-03-28', '2026-03-28 17:33:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:32:11'),
(675, 'a337996a393bf2c75e84d0fd1e3afa9efb65b3db27c5863afb51f1e5e65d52de', 3, '2026-03-28', '2026-03-28 17:34:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:33:04'),
(676, '268c95a78c68e80ffdec8f7f39f5aa28d5cf8889e702196e8f64da803c1df63a', 3, '2026-03-28', '2026-03-28 17:34:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:33:34'),
(677, 'aa03d268155e412d5943b655a828c7b873da20ecf1adb7050f51c2d80dcc0024', 3, '2026-03-28', '2026-03-28 17:35:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:34:18'),
(678, '6e8acdd63b5c7b71aba83126827bac573930c7686cf5e5c0e8b301cb8cb32624', 3, '2026-03-28', '2026-03-28 17:35:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:34:51'),
(679, '67b25eaa7a5fbf1f6f13a0d9ed0c48eca699c8037f7b3fe784dfa092cf330e84', 3, '2026-03-28', '2026-03-28 17:36:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:35:42'),
(680, 'c60aeabc101583a77eaaba7a8ba27e6a8650e8272ee38c891ff39bb46077fd67', 3, '2026-03-28', '2026-03-28 17:37:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:36:14'),
(681, 'a911502ee4f1c78ba5bd73e3a8b38f6e06b0bd7922708987044367197dcb7db7', 3, '2026-03-28', '2026-03-28 17:37:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:36:45'),
(682, '42c1d881de6ccb1e8bc11ef174e43eaf073f0165c375e8e2167e314216d10504', 3, '2026-03-28', '2026-03-28 17:38:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:37:19'),
(683, 'a814298aa8948d2bceeb2faec1184576206169ea88c91d980869f8237d929f67', 3, '2026-03-28', '2026-03-28 17:38:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:37:50'),
(684, 'ec3beb6759f8d0fee0a3e878b03e9f06d994afd70d0bdce5f13298150eaf24bc', 3, '2026-03-28', '2026-03-28 17:40:26', 0, NULL, NULL, '::1', '2026-03-28 09:39:26'),
(685, '1c4d1fa1d48d31b16f542a242e1149d729f63722aa511e235782b74e431ddc38', 3, '2026-03-28', '2026-03-28 17:40:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:39:45'),
(686, 'e002e95c38b4d7940f1d9f58c0c83d001271574d17627eae1e1f6b27c07ef660', 3, '2026-03-28', '2026-03-28 17:41:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:40:15'),
(687, '9d1e0c46282dbabf616ad94db3de4fbc5895f4ae6fd78d6f7dde7a08a99a7d04', 3, '2026-03-28', '2026-03-28 17:41:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:40:54'),
(688, '3a69918cd8ae6be710b3edd5b4b278de9c70fdbf2511c0fd0f61a301c2b942f7', 3, '2026-03-28', '2026-03-28 17:42:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:41:25'),
(689, 'f2449939560711165c9fc252925c2b1412d4111c97f6e45ac700d494bc870e98', 3, '2026-03-28', '2026-03-28 17:42:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:41:56'),
(690, 'e01fbd6350e17556fe45777e936d4577098670f7bcb22a0e377b6875b1fa8cf4', 3, '2026-03-28', '2026-03-28 17:43:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:42:29'),
(691, 'e1ffb47135af8c507fcf8b012dd9f0723b2614663338892e7a4fc0f7842b21aa', 3, '2026-03-28', '2026-03-28 17:44:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:43:02'),
(692, '0a0f50541dd920c7207c8df1355bed6dc551fe3ae3a4db3b1cb2219d9339094d', 3, '2026-03-28', '2026-03-28 17:44:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:43:35'),
(693, '9cb9e3f1319b326011b419d80097bb7bbfee586b21b4a9349c273863a9e9bb6e', 3, '2026-03-28', '2026-03-28 17:45:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:44:07'),
(694, '25f6bc5f4fa46c3c722b9247e3a2fb40bf8ad483d89ccfc85b1cf1b8a2531336', 3, '2026-03-28', '2026-03-28 17:45:39', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:44:39'),
(695, 'c23be2e80a26b763d3dc05234000ff9dc4afd7990ba5b898915df2807a3281c6', 3, '2026-03-28', '2026-03-28 17:46:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:45:28'),
(696, '3236173f138309b351d1dd63a73adb7bf9d0e26f0ed87f22830a74345f6a1004', 3, '2026-03-28', '2026-03-28 17:46:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:45:58'),
(697, '7be837af9c88f98c5b1359424f1b1bf8b54e2b54a2c6c8c580f67cdd81e6cdd2', 3, '2026-03-28', '2026-03-28 17:47:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:46:29'),
(698, 'ced8c8469602d51a94cda3d390692458a9afcb1d89667caa9f7cbfe5ca3350cc', 3, '2026-03-28', '2026-03-28 17:48:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:47:00'),
(699, '5ae2e5e8f5b9515dd151a19ba08d2c2064c5e19f1abb35dda28af7a03110a68a', 3, '2026-03-28', '2026-03-28 17:48:32', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:47:32'),
(700, 'e5ed7ec50838961c361e30fc08964a8d208a86d13e719c20ad906ffac5e59151', 3, '2026-03-28', '2026-03-28 17:49:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:48:04'),
(701, 'adcdc0659a5d04177ea993b32fd7b338fec9272029a649d2fe716a3675d7e4ec', 3, '2026-03-28', '2026-03-28 17:49:37', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:48:37'),
(702, '029660e0c8144941846c9d54539cfb25ed4ee84564446a14cbab70c0aaad2494', 3, '2026-03-28', '2026-03-28 17:50:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:49:12'),
(703, '280d3cdb92b088c23ccee697a94d641138260c98d4eb9c805d3bd1120321890c', 3, '2026-03-28', '2026-03-28 17:50:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:49:46'),
(704, '96738a56b9493536405f1a82e18c3eb597e0b6a6444b08a2105c8dc5a20eedca', 3, '2026-03-28', '2026-03-28 17:51:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:50:19'),
(705, '926fd5e722258681d476d0117b6b62399fbe116ed690d264218c38013525797e', 3, '2026-03-28', '2026-03-28 17:51:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:50:50'),
(706, '9fee9eba48edae240d1de4cff2113bededd4a35155a1400c32dbd41250e6b4a4', 3, '2026-03-28', '2026-03-28 17:52:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:51:21'),
(707, '46a20aae580ee2b2aa31440754cfc37d818288db2973158177d7a3cbc0dfabef', 3, '2026-03-28', '2026-03-28 17:52:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:51:52'),
(708, 'eb16682699282fa0dc772ca006f8a0e2c545175a0962709f808616b6db45ceab', 3, '2026-03-28', '2026-03-28 17:53:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:52:43'),
(709, '32db6c6bf7c26c24db0758e6f6034cdd11306d8bf175092a9043e53ee40bfd29', 3, '2026-03-28', '2026-03-28 17:54:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:53:14'),
(710, 'dc803f99ca9eeddcd85cf3a641831caed59c492f549522608803a863cc022b7f', 3, '2026-03-28', '2026-03-28 17:54:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:53:52'),
(711, 'c0b5c1b1d51823431a09cffbfdf922227319774c134d8362e93873225c1e2cf6', 3, '2026-03-28', '2026-03-28 17:55:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:54:44'),
(712, '24fe7e5026140e1bab292562f5e927fad706efd723e4bd404d8a1b64b79e4cdd', 3, '2026-03-28', '2026-03-28 17:56:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:55:15'),
(713, '3febb19bf3b1983941c74e3b5ac1fac97fe6ef7899d9a556d10fbaec88d3422f', 3, '2026-03-28', '2026-03-28 17:57:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:56:12'),
(714, 'a845047efc1070c3f630c85f4d60a3667d5fa91dd42c4521678755038e4f88b0', 3, '2026-03-28', '2026-03-28 17:57:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:56:44'),
(715, '5db3cbc4798d006173caa99d01cbf8e15115be7b6c100034e52787372158b575', 3, '2026-03-28', '2026-03-28 17:58:32', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:57:32'),
(716, '712fb3a44797d902f04ecd9363ed83e23d443eacfbaea9fa062dc9e500070757', 3, '2026-03-28', '2026-03-28 17:59:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:58:04'),
(717, 'a78a1f448735f790a44b6fc5ed44ce9074c20530563df05d8ee7e59da1d8e8b8', 3, '2026-03-28', '2026-03-28 17:59:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:58:36'),
(718, 'b374932edd08688266523204c769475925f615491b06bb099c57d29e67377972', 3, '2026-03-28', '2026-03-28 18:00:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:59:07'),
(719, 'b3553404b26f8491952c8a17f1c165b84821ce07998f9f2167208e9e1cc955e7', 3, '2026-03-28', '2026-03-28 18:00:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:59:40'),
(720, '4f38e4f6679ca54dede9bfac930726278e9cf76ca8e79c318d484a73cfda40ce', 3, '2026-03-28', '2026-03-28 18:01:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:00:11'),
(721, '5556ef2485aafeb926a23c4b43d0429a7d1fee6396a03fc8911831e7511558c4', 3, '2026-03-28', '2026-03-28 18:01:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:00:44'),
(722, '0848a3433e76de07dc383ee33ba97325837ddd991b7dd9e835cb29f837792c3d', 3, '2026-03-28', '2026-03-28 18:02:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:01:30'),
(723, '32d3153347051608eb989d399d5c15e89ee64e164000ccfe2763f3d9bad2d332', 3, '2026-03-28', '2026-03-28 18:03:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:02:01'),
(724, 'bb660480ad3b0d69364e16719d8542070f86c85f5b17f152529b2fc45f5e0942', 3, '2026-03-28', '2026-03-28 18:03:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:02:34'),
(725, 'a869c3287fa2e908fcf08407f1594cc3f1e9e6754e2dbbe97779adf5971a286c', 3, '2026-03-28', '2026-03-28 18:04:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:03:05'),
(726, 'e67520056979e2776a4971ff05885c0affb4c61c4da16957c16de15728e0233f', 3, '2026-03-28', '2026-03-28 18:04:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:03:38'),
(727, '08dc6c3bec03cb2b3bd27cfc9b5ecde0fc3f7d3f6fcce9b92842cbe77be3228e', 3, '2026-03-28', '2026-03-28 18:05:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:04:19'),
(728, 'dc87bed54652384d6a5520b261c6eb732505bc248231d4fc64eee7f13b049642', 3, '2026-03-28', '2026-03-28 18:05:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:04:53'),
(729, '6a03f95a624be9cf7c563b602aed48d8d3bb2ffb0ee626fda9774a7f574279bd', 3, '2026-03-28', '2026-03-28 18:06:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:05:24'),
(730, 'e9bdb5b17ee48f73204d046e932f723103d6c27dc3b15b3c5f1e131a6d7a7f1e', 3, '2026-03-28', '2026-03-28 18:06:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:05:58'),
(731, '14506a64eb10dcd9a8acfbb39bff8b03853bfdf33af8c5d6cd88e7aa73cdd41e', 3, '2026-03-28', '2026-03-28 18:07:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:06:31'),
(732, 'ad2b90ff7bfc6d9165295807b77a1c9f0ce309192713e83257bce74e1d94456b', 3, '2026-03-28', '2026-03-28 18:08:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:07:01'),
(733, '249eaa79e53d044c5a977ac3008c30e3b58591cda24f81db9c159e7f6c9c08c7', 3, '2026-03-28', '2026-03-28 18:08:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:07:36'),
(734, 'bc8d9a10095f5e6a3347492e8e708b2c6d3c63dd20b68daacf505a52612479a7', 3, '2026-03-28', '2026-03-28 18:09:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:08:07'),
(735, '9950ef2c0fb58cac1c8f26dfb2cea8611a8d24736112e11eb4457c315ee1a6b1', 3, '2026-03-28', '2026-03-28 18:09:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:08:40'),
(736, '90ad0136442f1d3326ce83934452b6ad64f94c2a31ad1f66d9b833f92ad7786a', 3, '2026-03-28', '2026-03-28 18:10:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:09:11'),
(737, '785610c06237d27dfbc92bf22ca01300ab717d3d18a9eca3ded4a29aee845110', 3, '2026-03-28', '2026-03-28 18:10:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:09:44'),
(738, 'bc9d4644520a3bb78203caa259b02edd238881f1dda07e29d283d271dea1cebf', 3, '2026-03-28', '2026-03-28 18:11:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:10:43'),
(739, '039685c2b99e98f3246987222d654f495d9a97e4440f9ee7061c320fe2f52622', 3, '2026-03-28', '2026-03-28 18:12:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:11:14'),
(740, '7ada2e4cec085e05c12b40983d637fbc1187204e2330727ede6527fa5dfff218', 3, '2026-03-28', '2026-03-28 18:12:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:11:51'),
(741, 'e0c2e2c8936bd41930d4ec8f16aa90bf0a7eedc88d6e94e8f0712cc0f0a4561c', 3, '2026-03-28', '2026-03-28 18:13:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:12:23'),
(742, '981481a4aa86972d50a7c4fd08f846f3cf74ef309b51729f64942885c60357d6', 3, '2026-03-28', '2026-03-28 18:13:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:12:57'),
(743, '64b5fae111b103ca41f6edeae0297a37634689290ff73cfe366a54a74b01a99d', 3, '2026-03-28', '2026-03-28 18:14:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:13:31'),
(744, 'b120b60a51d8c684926ebed14029633f7f0bf1d76e18eff8478d529be8a13e0c', 3, '2026-03-28', '2026-03-28 18:15:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:14:02'),
(745, 'd0c71766d248c022fffd857e7841ee182e90b64fbf6da8c8c6b1b12cc417b9ce', 3, '2026-03-28', '2026-03-28 18:15:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:14:36'),
(746, 'a1d93fdef886b257f0abc78cf02e653413e8b4fbefd2be7593d88971519f8f37', 3, '2026-03-28', '2026-03-28 18:27:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:26:46'),
(747, 'a50f95d7fa28f308cd0609df7aebc6bf7e8ebfba2a456a4fc9e8b816cf8818d2', 3, '2026-03-28', '2026-03-28 18:28:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:27:16'),
(748, '3c691b578ee7945cf75b046d65149f8dd81866aef2d6ac06fd7c32548ff0280b', 3, '2026-03-28', '2026-03-28 18:28:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:27:49'),
(749, '9d9a268aefbc27f30618f440eb60d7e7df923090a18edc29c0bd2441e72f1777', 3, '2026-03-28', '2026-03-28 18:29:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:28:21'),
(750, '8b41f8bf2eddea8df057fd5ea34170112cda4871e3c67300e6f90d85546580af', 3, '2026-03-28', '2026-03-28 18:29:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:28:52'),
(751, '7191f7bfc45a2b468cae463c27cecda3e42dcd0b4009fc48f6240e4fd12b57b9', 3, '2026-03-28', '2026-03-28 18:30:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:29:24'),
(752, '02243013e6a60676de50c65f5e4f791a48f1f29a5840c4c8092e00981febef38', 3, '2026-03-28', '2026-03-28 18:30:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:29:56'),
(753, 'a24601225b0ed5025cccc683762fe04da39441fdbd81b7234b5eeebcf18afa66', 3, '2026-03-28', '2026-03-28 18:31:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:30:27'),
(754, '9dcd8df6c52d66d4a0ac61ca91e4fd08906dd7dfd2eb10215b0e284bbd43f448', 3, '2026-03-28', '2026-03-28 18:32:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:31:00'),
(755, 'fb7965fb19e8630293bc83e9e09c238df2952ed5ef7d92b6ea1eef5886001a78', 3, '2026-03-28', '2026-03-28 18:32:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:31:31'),
(756, '55d37d21920060bcaa72563ac4a95bc4dbffedc014daedbc192d285ff489f7fc', 3, '2026-03-28', '2026-03-28 18:33:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:32:04'),
(757, '9dc83981861952fe8d51590c229b62d9039e3ae00c812422cce913594ce42a7c', 3, '2026-03-28', '2026-03-28 18:33:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:32:35'),
(758, '836bb266fe32fdecfe5849930604b11c31f08f282214fee5b23e05448e21bb9e', 3, '2026-03-28', '2026-03-28 18:34:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:33:10'),
(759, 'a8e742492335659faa0d414c6aa281a839296cc01df24873a1768601c5170f80', 3, '2026-03-28', '2026-03-28 18:34:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:33:42'),
(760, '521dd9f3bf06495796c8776268ff6dd52c5998eaefdbafe1bc62d5fc95cd5f82', 3, '2026-03-28', '2026-03-28 18:35:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:34:16'),
(761, 'f4b414364cec4cba45e0a881f7d702ba3d066ded4785a5d24b853623650ff15d', 3, '2026-03-28', '2026-03-28 18:35:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:34:48'),
(762, 'a8a55574c54cb37061893ca22849ce103326239348cce1916b0c6f4eb8400a4d', 3, '2026-03-28', '2026-03-28 18:36:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:35:18'),
(763, '67fe5b0e9a03ae1b724e66df9e084dd56b2a879509d966a63b7749bbbed50378', 3, '2026-03-28', '2026-03-28 18:37:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:36:16'),
(764, '77807d307e60f08aedfaaee863a5a8d5baf131c7635350c4421bb45ef8c07cff', 3, '2026-03-28', '2026-03-28 18:37:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:36:48'),
(765, '82c8c9c391445baad49c9c9246809b02f12975ab62e227ac56d93de66cc8ca63', 3, '2026-03-28', '2026-03-28 18:38:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:37:30'),
(766, '80dcc95c7bf28a267d7aab0d6869a336a852585a2a25d7b647448dd30ed8ed4c', 3, '2026-03-28', '2026-03-28 18:39:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:38:18'),
(767, '24942c09a5c72c3998c9e8954df678187daa938fe57b7e376b43c20b50dc63ca', 3, '2026-03-28', '2026-03-28 18:40:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:39:13'),
(768, '6132e282b74ba31d9986c379684b8bbe751442b599d5add677d9abef0e2bb4ac', 3, '2026-03-28', '2026-03-28 18:41:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:40:12'),
(769, '5a613ed9ad95404a6639802c53071abcc60d9ac308b2e07bf9a5571f5ac04e07', 3, '2026-03-28', '2026-03-28 18:41:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:40:44'),
(770, '25f73bd85e542c7e6c720bd851f89b4ae7f0962339536837f022d71f46bc7759', 3, '2026-03-28', '2026-03-28 18:42:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:41:15'),
(771, 'a24ac0a3d6f55589911481c73e3635291037cf745e92a38eaa65ab3fa869cbe9', 3, '2026-03-28', '2026-03-28 18:42:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:41:47'),
(772, '1ed9db036c84c6b401b676b88876b67e6ed6c56779ebb17f66e6ee0aca70d87f', 3, '2026-03-28', '2026-03-28 18:43:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:42:19'),
(773, 'c6a67594053d2bee6cc628c3e7c1c29c40118f8bec455b6dc9a7529d5a5a48fd', 3, '2026-03-28', '2026-03-28 18:43:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:42:51'),
(774, 'dd5f3cf61aee15cf1291d1e815e7cab4c6805a2aa455b86b9a1b6297ac59a9d7', 3, '2026-03-28', '2026-03-28 18:44:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:43:42'),
(775, 'fc280caa1d8bfb3433ce45f9bb0b10d75d96c44953f5b62925e7a2f44867a827', 3, '2026-03-28', '2026-03-28 18:45:41', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:44:41'),
(776, '339b6ba172f7310328a591058fae5ddb812120da53a4c7d4a0026d0db130b0fc', 3, '2026-03-28', '2026-03-28 18:46:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:45:12'),
(777, '9699ed6d3df70bb017b18882d8049a0bfac4caea6b6d3ddd088b76bc3124ff88', 3, '2026-03-28', '2026-03-28 18:46:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:45:43'),
(778, '34979db04089aea473bd1462dc59ffda5052ca9d7ef23a81663849414a5ab8f5', 3, '2026-03-28', '2026-03-28 18:47:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:46:42'),
(779, '2e6771659e1133eb8521a88b2440905f9c1720f48491a8467e62270bfd34f6f3', 3, '2026-03-28', '2026-03-28 18:48:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:47:13'),
(780, 'f26706ed28525bb09be9d17a3ab69196a5b8faf16e20a02bbf46cd78b99412fe', 3, '2026-03-28', '2026-03-28 18:49:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:48:11'),
(781, 'd94399b3b6fb38bac4a19104ff4f0ff7c27893258e9933459125694a42b584e1', 3, '2026-03-28', '2026-03-28 18:49:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:48:43'),
(782, '6b2750e0ef9cef8c2678a51f64723e0e4bbbf602034edd1223c3b414d96127a9', 3, '2026-03-28', '2026-03-28 18:50:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:49:35'),
(783, 'b8e07d7c19fea68bf2b035ec5bacf8434894a0cd9ce07390d4db76db9f267509', 3, '2026-03-28', '2026-03-28 18:51:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:50:06'),
(784, '483e92d1e7312b30717bc7d9ad14fcd394ffd099556da05603a147f1acde70fc', 3, '2026-03-28', '2026-03-28 18:51:37', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:50:37'),
(785, '8b969ea2f0ca6747a4bfe72a296768e80281417dde398633cac2b72f8a655d10', 3, '2026-03-28', '2026-03-28 18:52:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:51:13'),
(786, 'bcbddc287062862305b9fcd20f3d3fc43b178ebcbd14ea488ad933e16f8e6f8f', 3, '2026-03-28', '2026-03-28 18:52:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:51:44'),
(787, 'da5fb2cef50f852a11e324524316dde54551fd3a6ba8504b8461dc313ab16b8c', 3, '2026-03-28', '2026-03-28 18:53:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:52:15'),
(788, 'ee2ae6866398d03be77d4cc64e0dc4976c7ec97cfd60631bf33291be2a1c5782', 3, '2026-03-28', '2026-03-28 18:54:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:53:05'),
(789, '4964246403bf27eab15af6f001f0bb13b08deefd908b51616fff5521c64fb6f3', 3, '2026-03-28', '2026-03-28 18:54:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:53:36'),
(790, 'b10735f3979fde8fd47fa83a74b4cf99d4382a96c11a36b885ab3e293a36fdc6', 3, '2026-03-28', '2026-03-28 18:55:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:54:07'),
(791, '4a2d4938d5b571ebfb71e69e324e1fd7bcaf58e4902d4b5d763bcbbd35c80d01', 3, '2026-03-28', '2026-03-28 18:55:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:54:38'),
(792, '6e1ea8f304a42742a60091d25e188ab061622bc3d3d993d6e8ebbf35a7670ef8', 3, '2026-03-28', '2026-03-28 18:56:09', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:55:09'),
(793, '32068f27eb6ce0786564472b347b4ffd407a2b5ac790771e55af544a1794d31d', 3, '2026-03-28', '2026-03-28 18:56:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:55:55'),
(794, '2eee94ebc12d7e4a3d04bff92e4cfadc390d5763f4e2048863be0a7b853b9095', 3, '2026-03-28', '2026-03-28 18:57:39', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:56:39'),
(795, '8c90f8496a4bb925e675465c43cda60c201336971386e4aa96768293e8c5ffb2', 3, '2026-03-28', '2026-03-28 18:58:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:57:10'),
(796, '2612144ba8ce51b035dafd2a1a9e8b74fc7e6f3a0dd1ee8da58c641d397a6990', 3, '2026-03-28', '2026-03-28 18:58:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:57:42'),
(797, 'f13c6c8a1ddec6dbc587f26454da1bf35e5f39d3eadd76f1267d9620df7f7027', 3, '2026-03-28', '2026-03-28 18:59:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:58:13'),
(798, '802ba3953b17a7bc8f87c469366e569cd367534d9218d6d788d8456ea958d22a', 3, '2026-03-28', '2026-03-28 18:59:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:58:47'),
(799, '588303fc50f64ec760d4b7a0834c85733dc2156ab2025433a38e464cddb6b358', 3, '2026-03-28', '2026-03-28 19:00:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:59:21'),
(800, '90a588cf2c678eef22b4aecd59931b99c58e74378cebd53f5102689172984746', 3, '2026-03-28', '2026-03-28 19:00:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:59:52'),
(801, '24e152949263b32c44751f5cf851b25e620280ad33b9ce90ad64393ee7958e85', 3, '2026-03-28', '2026-03-28 19:01:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:00:23'),
(802, '404feaf123c8c77859da89947a41b6115474b2ae0f6120f64a640f10c40d0bae', 3, '2026-03-28', '2026-03-28 19:01:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:00:55'),
(803, 'acaf9a0653313daf39d8714611791ad311e0c0f7cf4806ec92e352a92b1c5fc9', 3, '2026-03-28', '2026-03-28 19:02:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:01:28'),
(804, '3f13e45f6e6ec526b8fecf639ea763f8d44fd896d7f903d3c0c0bd6fcc2b2211', 3, '2026-03-28', '2026-03-28 19:02:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:01:59'),
(805, '936621b08fc09ee19d8351175f03976a6f4d2716eeeaa4a26bd93b56f4afbd55', 3, '2026-03-28', '2026-03-28 19:03:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:02:58'),
(806, '0e5ea344668b6ff363e9236e60b787eb7e6cde5b58819c0526f2d39a18251ade', 3, '2026-03-28', '2026-03-28 19:04:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:03:29'),
(807, '282a9457a8fb8a023c73394954160741dc26f71ae5e33dd4916ee6256022ac4d', 3, '2026-03-28', '2026-03-28 19:05:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:04:28'),
(808, '6a5fc47289877ad87d6b1f98bc8bbb84493c9866211622625180bd9a1e15a989', 3, '2026-03-28', '2026-03-28 19:06:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:05:27'),
(809, '67a85049eda7a298d43718c7671a67d981c6b73b0ced486003f769745a7da881', 3, '2026-03-28', '2026-03-28 19:06:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:05:58'),
(810, '1909e012b520f4bd34a9ce42d1539725b1868d12a62b0a2aa65803788da3c762', 3, '2026-03-28', '2026-03-28 19:07:39', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:06:39'),
(811, 'e421a10117cfee0f273e76ad5fe5993d83000d7a2c534e9e02593767a14de108', 3, '2026-03-28', '2026-03-28 19:08:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:07:27'),
(812, 'fcc10c679bd621830d3c41002a0cce461a19ac7f0e98439528c2ed1e89e647f1', 3, '2026-03-28', '2026-03-28 19:08:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:07:58'),
(813, '07a1ecf3cd78ccf72e5a6d60328906712ef392581a5707fe7c00ff4a8328b988', 3, '2026-03-28', '2026-03-28 19:09:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:08:29'),
(814, '278881c2b0025719c0da6e588a3ee57d832b1b93773aafbb434710baa1ba43d2', 3, '2026-03-28', '2026-03-28 19:10:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:09:00'),
(815, '9ca15a3976867624a79b5f95a63f2ae9fdefc5537df3cf857322303bff0832b8', 3, '2026-03-28', '2026-03-28 19:10:32', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:09:32'),
(816, 'bcba2b010b2c1a8ec0d97d8fd7a46d8506869346261b83e76db8767dd915f507', 3, '2026-03-28', '2026-03-28 19:11:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:10:18'),
(817, '4b3999d8b30d62aeae8473dcb9d6c23670c0ed5ee250a07ee73ac3c8fe7d451e', 3, '2026-03-28', '2026-03-28 19:11:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:10:49'),
(818, 'c8fbaa955371121014b7b21a0b97b8c3054a9c1d4704ab00a9ee74f1993d5bb8', 3, '2026-03-28', '2026-03-28 19:12:20', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:11:20'),
(819, 'd60a8acb82f9bb0ed4e52dd0aee38712a5944762bd3827b38bbceaf9e0076f14', 3, '2026-03-28', '2026-03-28 19:12:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:11:51'),
(820, '7b9b23531753b455c22d49a7b8a887d102fca7a28cec44fddc1d92dfd3d7f6c4', 3, '2026-03-28', '2026-03-28 19:13:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:12:34'),
(821, '274890dd21b0c89a9cda6fbf1ac4cd0105e864c031775236d2e2d51226fe9742', 3, '2026-03-28', '2026-03-28 19:14:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:13:05'),
(822, '93b25d5f45a02fcca9d1fd88db0423e076552c21c93d2eef92e29950321584fc', 3, '2026-03-28', '2026-03-28 19:14:37', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:13:37'),
(823, 'd8d3cf0c6dad9b81c49d2c86eae7d74aa02ca4a2ec26a6fa629ffa1eb66b0a1e', 3, '2026-03-28', '2026-03-28 19:15:08', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:14:08'),
(824, 'b55e6bbe8e5ea1025ac8cd8b7c7793afa985a32c5f94afd56d9d0529529ae1e0', 3, '2026-03-28', '2026-03-28 19:15:39', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:14:39'),
(825, 'c0c7e4c6b2ed64ec52f3703224225938f08d2991317333ca1af3215329259bd1', 3, '2026-03-28', '2026-03-28 19:16:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:15:17'),
(826, '2f2d071b413156128c3485754759f6f1179a47c29bd99ac65798fc5de3cdb765', 3, '2026-03-28', '2026-03-28 19:16:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:15:48'),
(827, 'ead35745a3435299c97c710151804653950f3736b08e3245a9852f7740a53830', 3, '2026-03-28', '2026-03-28 19:17:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:16:19'),
(828, '2655ebaf9e57174312ae60749bcd349b3c04af67fdd78f09211dd217a74d40b4', 3, '2026-03-28', '2026-03-28 19:17:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:16:30'),
(829, '19520f2c23c8d5a414824a131f5f1fa2f4f8f8f68f7c43effcda1294e219e707', 3, '2026-03-28', '2026-03-28 19:18:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:17:00'),
(830, '5e6ed38158073b1a7f25202a62312b05bb8de58f4659c02de097256cd87f99ba', 3, '2026-03-28', '2026-03-28 19:18:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:17:59'),
(831, '39ea4a084811fb4b6c6d70fb9465fd62ca6bf0197f233cafcb56d3aad149ca33', 3, '2026-03-28', '2026-03-28 19:19:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:18:47'),
(832, 'a88df89caa512e196f6fbaceaff3858de2cbbd04316baa70093c6dcc95c169df', 3, '2026-03-28', '2026-03-28 19:20:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:19:18'),
(833, 'b15edad6eb73f4bccf539510a51e617d552b2811d7af66bbbf72875da5dee46b', 3, '2026-03-28', '2026-03-28 19:21:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:20:00'),
(834, '4ae950d23ff252bb386880e1b49758676b24d8cc08efee7b5fdb5c76f6922d05', 3, '2026-03-28', '2026-03-28 19:21:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:20:54'),
(835, 'f110a7baa94face5fcf2c4114b4c9811ac9a1acb63a16d96f2774a93bf570e85', 3, '2026-03-28', '2026-03-28 19:22:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:21:45'),
(836, '6656f3f0f3574023d4539a47e6cccb389745b7f1b037e799d4f6039330800c80', 3, '2026-03-28', '2026-03-28 19:23:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:22:44'),
(837, 'f7ade076ac51a163569bcfee57fc4089e87a198ab07f1502ff355fb37f1b0800', 3, '2026-03-28', '2026-03-28 19:24:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:23:42'),
(838, '654b0ec8b47e7e3412a3f7758b6baccbfb252f536401ac97f0e14dde6aeeb28b', 3, '2026-03-28', '2026-03-28 19:25:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:24:13'),
(839, '8a09684d349e115d1e607a9c460d69d4fee8405717ab6b9f6f3feb1f832ee1ec', 3, '2026-03-28', '2026-03-28 19:26:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:25:12'),
(840, '35bb2d785d9d9e6577e5b91a16bb019586eb47ab7360394aa1335c225fd04136', 3, '2026-03-28', '2026-03-28 19:27:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:26:11'),
(841, 'cc33da365d52c94f9e044a20b418a05829fc071635422d175f34601020e9b212', 3, '2026-03-28', '2026-03-28 19:27:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:26:45'),
(842, '23f079484c60869b59dd20be26c4da32cc52b92f0639de7ff1aec64b31f5a2e9', 3, '2026-03-28', '2026-03-28 19:28:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:27:21'),
(843, 'a5b9f9b9be287503172e274ca9808dc815839d2faa6a3f97cade8eb6b5cada60', 3, '2026-03-28', '2026-03-28 19:28:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:27:52'),
(844, 'd5d770e65c246080e11226ef825848a1bebe39e002380df91a55b3ba5bd44429', 3, '2026-03-28', '2026-03-28 19:29:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:28:24'),
(845, 'f15e0af2c50506616d95934a496889580ff9a8cda4f810f0470f13ad7c9bed3d', 3, '2026-03-28', '2026-03-28 19:29:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:28:56'),
(846, '6b0a37a2ee5be062860c215c72bda3c1b68cd16fdb8fd22657c50ef6287fa282', 3, '2026-03-28', '2026-03-28 19:30:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:29:27'),
(847, 'ca839d8250af03908334c9ac2662500eadb287b7b0674ef9da6adf35469bc448', 3, '2026-03-28', '2026-03-28 19:31:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:30:02'),
(848, '8c3447409a20c8b8f35f519492df1ba1985bdddf97486fb9d784ba65155ffaf9', 3, '2026-03-28', '2026-03-28 19:31:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:30:34'),
(849, '8dcba2f932d2d148507ed00256cbbaf654556ea6e3b9b5f89ea353c9a47442c4', 3, '2026-03-28', '2026-03-28 19:32:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:31:16'),
(850, '8fbbf4f03c6747321e1d73278136fe90765a38a2717d07f385a6f4f3a6fc74ad', 3, '2026-03-28', '2026-03-28 19:32:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:31:51'),
(851, '410634f12dec6d3953319c4730e99485f2fafba10e4405f75e5a2acd24a491a7', 3, '2026-03-28', '2026-03-28 19:33:22', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:32:22'),
(852, 'd5f6f397d587b004ef2bd9d23269bf8fea1af6c37db77ce2c3fa63d41bb1e50a', 3, '2026-03-28', '2026-03-28 19:33:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:32:54'),
(853, '037b3ff919f6e6c1930af87589a6813d2125c17bb22e6f1975228bc1e6f337d4', 3, '2026-03-28', '2026-03-28 19:34:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:33:25'),
(854, '7b2662e5bbf072aa5d1f8d2a1e7ef51622ea9a100536d69a7d256d23b543fce0', 3, '2026-03-28', '2026-03-28 19:34:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:33:56'),
(855, '85ef773290acd02cbd68b79053f8e1d0632ecd18b29a308b62237c3f00e5e3ba', 3, '2026-03-28', '2026-03-28 19:35:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:34:27'),
(856, '51f1ef9c45b6d6826e638d9d1f69a369ba452f1b9c1a77eabb2702a757e5b0ed', 3, '2026-03-28', '2026-03-28 19:35:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:34:58'),
(857, '713b38dab8183be537ebe48ecd470b0cf725092492cd0909187daa326e98b3ff', 3, '2026-03-28', '2026-03-28 19:36:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:35:29'),
(858, '52f0acb56cf71deaaecd4a2f1023aae090c90e35687d7a5a2278921a949e2b79', 3, '2026-03-28', '2026-03-28 19:37:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:36:00'),
(859, '2d874bce87e4302ab1866aaa1d7986fb8cc6ab2c875c524d3cd9f919f1ed3061', 3, '2026-03-28', '2026-03-28 19:37:32', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:36:32'),
(860, '10300b49aa3d729982ad11d430ebd97cfed65e56876c0023a721d4d28403eee9', 3, '2026-03-28', '2026-03-28 19:38:03', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:37:03'),
(861, '17507c3f7fcc1bcc7080cf1c50c7f8c87309fdbeef8483e486303f710b58cc2a', 3, '2026-03-28', '2026-03-28 19:38:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:37:34'),
(862, '671e55c608a44a0e014c2360180fc8ef65dde6731ccc9463e42c33360e5a5e23', 3, '2026-03-28', '2026-03-28 19:39:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:38:05'),
(863, 'cdad2e4379ca62d337e30211c598dedcc86029f67b328a7e8a9ee9f7886bc8d9', 3, '2026-03-28', '2026-03-28 19:39:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:38:36'),
(864, '9c00f9d9a34394c54ea012281a93da92840ab03ef7506489d8ecdd2904cec211', 3, '2026-03-28', '2026-03-28 19:40:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:39:07'),
(865, '586cd037efd9b038419735835159af2e8156842393a4cd88e84f3a337a7f0f49', 3, '2026-03-28', '2026-03-28 19:40:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:39:38'),
(866, '1f5980bf1a2c7a6344e7faf8f964bb6ec852a3522866f3476e8cf5ff15e871d5', 3, '2026-03-28', '2026-03-28 19:41:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:40:10'),
(867, '1b488728a6abc576e2e8d7d086ad00e3302c39c7fa00f838656c31451d58760b', 3, '2026-03-28', '2026-03-28 19:41:41', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:40:41'),
(868, '169a60fd4858186006ad8ce87f2de6336028115bd1ab9603a2b2ae7459a5afd7', 3, '2026-03-28', '2026-03-28 19:42:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:41:12'),
(869, '4db9335748549a5f1334b48feb97188f8d1fe73b80069a44733880c988655857', 3, '2026-03-28', '2026-03-28 19:42:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:41:45'),
(870, 'b34335a5bb05dbdeb4483fdd99fbbf0f558c7264306f936e7ba442a2e0ea76e2', 3, '2026-03-28', '2026-03-28 19:43:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:42:16'),
(871, '1202b93ddf3597370fd2a5832573a049c8deb802f9d0b9053f8e3a99ebd18d86', 3, '2026-03-28', '2026-03-28 19:43:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:42:49'),
(872, '6b72d8cbdd75babc4d376acaf9787fbd09bbb3f0c910b06882b79ed87872906f', 3, '2026-03-28', '2026-03-28 19:44:20', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:43:20'),
(873, 'eb29f48070e8d97602bad2d7bad12e7a808939c340ed3bfbc5946f322f185b8f', 3, '2026-03-28', '2026-03-28 19:44:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:43:52'),
(874, '8e74d9b18607593b8c51dd475ac91aa4568a584e956c0897fb93c982e3d4268f', 3, '2026-03-28', '2026-03-28 19:45:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:44:23'),
(875, '6f1a61c9c138f735ac560f68d4decfa01e150250fbd0de908dd8b94ea8ebfa37', 3, '2026-03-28', '2026-03-28 19:45:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:44:54'),
(876, '572341035405d2bd8c61db5338f7869a8a832e961e592a12322b15d65b37af65', 3, '2026-03-28', '2026-03-28 19:46:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:45:24'),
(877, '9615b4001e965dba1283615464ce75041283b9ab067b5046a7419e57104cc171', 3, '2026-03-28', '2026-03-28 19:46:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:45:55'),
(878, '27e77a5a58c7089e863295acdcd2356886ea39beb4759141bb4fb7d9514f53d5', 3, '2026-03-28', '2026-03-28 19:47:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:46:28'),
(879, 'a140bbee3e0d27717ed36da6d2d91684523c52eb622e8718c79f0fa95ac3d84a', 3, '2026-03-28', '2026-03-28 19:48:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:47:00'),
(880, '8119c97fd9bf757935d986757a7b29d08e33472d7fcdec787fc77a271ba7b1d8', 3, '2026-03-28', '2026-03-28 19:48:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:47:31'),
(881, '4ff441bfde25eb698f6e4505bded07738786afbfdc75bf587bd3b0f80600b354', 3, '2026-03-28', '2026-03-28 19:49:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:48:02'),
(882, '3af67df3019dd0c937de723605233bdc3944642de8f14efea4271dda00494bbe', 3, '2026-03-28', '2026-03-28 19:49:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:48:34'),
(883, 'b56a4aadff6ce44302ce9d5609c800c73937af2127b58971c3055eaf015c2069', 3, '2026-03-28', '2026-03-28 19:49:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:48:40'),
(884, '26d1c3492d248ab3a1b92de5d5cdcebda873bf2dbb6dceb06ded46fb998075fd', 3, '2026-03-28', '2026-03-28 19:50:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:49:10'),
(885, '9ff2bb9465ce6028d7f7de67c096b3c660daf9764228aeb2e08319536037a623', 3, '2026-03-28', '2026-03-28 19:50:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:49:42'),
(886, '579f2a8519fe268fd80d3d39ce73baba6e0628a0002cad11636913dd3541a640', 3, '2026-03-28', '2026-03-28 19:51:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:50:14'),
(887, '74538688f4119a6ca7087fe8bcc77fa63ab933bbfbfa6e9ce4d4bc10d2ccbe8f', 3, '2026-03-28', '2026-03-28 19:51:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:50:45'),
(888, 'f5e9a6303f373d5b0272308e5a15077d482e5734645cdc12c8ae67221d81e0b1', 3, '2026-03-28', '2026-03-28 19:52:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:51:15'),
(889, '16afb802e6ee9138b0d2998ee37f508290ddd4f576752d97329a5c35fb185d82', 3, '2026-03-28', '2026-03-28 19:52:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:51:46'),
(890, 'eb4330dff10bf84fd002231cfb522a74fa9beb0d4a8d358f7209beee7462de87', 3, '2026-03-28', '2026-03-28 19:53:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:52:17'),
(891, '2ad2689447d5f1a586b28ccece7df0ef932ecd22837627f70eb1f0567caad7a2', 3, '2026-03-28', '2026-03-28 19:53:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:52:42');
INSERT INTO `ta_attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(892, '8b5bb4deae04084c943d0c24a5da3dede120d2760062bd21c456885f28d6875f', 3, '2026-03-28', '2026-03-28 19:54:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:53:13'),
(893, 'ecccecc9f74fe65aa41149387f20c9a1cf9dfcbdcbc7018762eee2fd92e05b05', 3, '2026-03-28', '2026-03-28 19:54:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:53:49'),
(894, 'aa953e210f39fce9b5e3a0e909237e5c9fa7c84a8698b0c23c429588a228abd4', 3, '2026-03-28', '2026-03-28 19:55:22', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:54:22'),
(895, '49dd5d1b1bf42d1f2c53d56bd33133765f43db3a363aa4b2fedfdf4b33f3f5b9', 3, '2026-03-28', '2026-03-28 19:55:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:54:53'),
(896, '26315fc25ce8fa004883fa1adb9ffd8750fdfefa03400977bcabae4731ffe8fb', 3, '2026-03-28', '2026-03-28 19:56:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:55:27'),
(897, '46ce258564eac4c13d6074e698bdb9c5b0a79182a06b5baa23d372c2ad576852', 3, '2026-03-28', '2026-03-28 19:57:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:56:00'),
(898, '3374f0909670e930316364c15d9a64fd9e82f9c489285dd62ce7f222a3899ade', 3, '2026-03-28', '2026-03-28 19:57:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:56:31'),
(899, '3f4195add379af8bf2d2424365f96564e489cabf691ba9c7e75480bb7c0deb57', 3, '2026-03-28', '2026-03-28 19:58:03', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:57:03'),
(900, '188a73ed9b225eab6df611065d89d18e73df8e55704ff6e44e0af1f84b4b3b93', 3, '2026-03-28', '2026-03-28 19:58:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:57:36'),
(901, 'b974159a7961fbe28cd5e81a66f7cb5fa1e37d816c172df37b8a821880dac949', 3, '2026-03-28', '2026-03-28 19:59:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:58:13'),
(902, 'e31335928eef2a66cdd493c171136f1f2162c86be5b599c11499b89aa0523854', 3, '2026-03-28', '2026-03-28 19:59:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:58:44'),
(903, 'b9feb3303435ae4ca450e1700a22409a37c940cad362bbc30b8a77df67e0fa47', 3, '2026-03-28', '2026-03-28 20:00:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:59:15'),
(904, '17d50358803fb3aae0952fc494ff090e1043ae9b0c066d556925aff818b5e534', 3, '2026-03-28', '2026-03-28 20:00:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:59:23'),
(905, '13f9d91bfb90de37bfc1f095080a2c5e05796cb57c5207a81635a65564e0ec64', 3, '2026-03-28', '2026-03-28 20:00:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:59:53'),
(906, '9aaa2590287ebfc0b77166936b31b085b16c73636c9df38113f799916de9ef1a', 3, '2026-03-28', '2026-03-28 20:01:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:00:24'),
(907, '58b7780f1910a855fea6cc2e769d58f4fcada1acf67b627b4c72be4764e12aec', 3, '2026-03-28', '2026-03-28 20:01:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:00:57'),
(908, '149728601edba7c737bf83195e89e044afcb7f795687a46282d629e2d42610ea', 3, '2026-03-28', '2026-03-28 20:02:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:01:28'),
(909, '6fff81c198e10b3f7b9b71719f394b475b68857c7c4d31178eb81e4c379e9a9b', 3, '2026-03-28', '2026-03-28 20:02:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:01:59'),
(910, '4b1bf3035c738ba618396ebeb4db892bbf7c505b51f919eb8e465cccc29f6721', 3, '2026-03-28', '2026-03-28 20:03:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:02:30'),
(911, '0847515a31952aa119d306ed3eb61fa0b452a2ec8b4163a6cb19e78ef8c46467', 3, '2026-03-28', '2026-03-28 20:04:08', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:03:08'),
(912, '2f4236c44175d964e65f2491f416b03b36b7e4d7bbb8821dbaa20d0fb1a26102', 3, '2026-03-28', '2026-03-28 20:04:39', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:03:39'),
(913, 'efe6ac6cedabfcc75d4415cc24746aa4d4b467e105b8980f6959db3867ae74cd', 3, '2026-03-28', '2026-03-28 20:05:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:04:12'),
(914, '4c33c4a3ea36994f7f0aa8020e9b2e44a17e2730eb787e6b543c1a668cabc834', 3, '2026-03-28', '2026-03-28 20:05:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:04:43'),
(915, '3661cf8878c1b845a5ea60ce1eb94855f35068b752ae54101e4d5aff6d6c2dc6', 3, '2026-03-28', '2026-03-28 20:06:20', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:05:20'),
(916, 'c624d93791fcf3501b04ae78124bda3f7e8d3aa44e3f19407e883d9c6854957e', 3, '2026-03-28', '2026-03-28 20:06:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:05:57'),
(917, 'aa87e3547e524822548162715c980ca8167fc044b973ea435fc438c9ebe44a05', 3, '2026-03-28', '2026-03-28 20:07:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:06:49'),
(918, '4660c304050e8bc0285b3fb5d7007fee5c8718bcec7c715b8a5f03c263e592c5', 3, '2026-03-28', '2026-03-28 20:08:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:07:23'),
(919, '550388147fe9a4e0435cc67d54503aa8cb5ffbc7c8cb7f74a0240104a28d5001', 3, '2026-03-28', '2026-03-28 20:08:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:07:55'),
(920, 'fed87d4ac8ab61bde338a1b286f54e539bf81e50f7ac788464f6d4382d4454a0', 3, '2026-03-28', '2026-03-28 20:09:33', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:08:33'),
(921, '26091a3b95d57125d3a6baee1f72fb90907ef9b7c20b91afbd6c3eeed6ed5c2c', 3, '2026-03-28', '2026-03-28 20:10:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:09:04'),
(922, 'c552219f5e4803bcf253df072da07ea55fdebf7df6decef33df068c8a60107bb', 3, '2026-03-28', '2026-03-28 20:10:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:09:42'),
(923, '7b4da9ecbc199da12780dc949c153d542aac030091b7de19f33acec4962f4ec2', 3, '2026-03-28', '2026-03-28 20:11:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:10:15'),
(924, 'ab414c7557ad57cbce59a02625a6080994195e1b7c5e1e8bb24db91351bc7b36', 3, '2026-03-28', '2026-03-28 20:11:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:10:50'),
(925, '3a414d13f39b9de7ef90dfba97f88ba4d91800356ca251556ad47cf8e076c6c0', 3, '2026-03-28', '2026-03-28 20:12:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:11:45'),
(926, '099200e200ae8a3554baf629160ddc22794c67212677834ea4c926963342be95', 3, '2026-03-28', '2026-03-28 20:13:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:12:17'),
(927, 'aedddcafba517b3c3dc0cf0556e31fb0a3ae98cf4f04f5d8bfe1b6c8ec971bca', 3, '2026-03-28', '2026-03-28 20:13:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:12:48'),
(928, 'cb7fc78c358a7a374eb34b6f538c28a63c0814cb75f7e95719c8041cb72211af', 3, '2026-03-28', '2026-03-28 20:14:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:13:19'),
(929, 'a21b1b0e517486fabfd129e60a85a6f2083cd2a3ad6beb0ac294ec345eb6441b', 3, '2026-03-28', '2026-03-28 20:14:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:13:50'),
(930, '6cf0d885700f08a9d0f8ecefbf9abe257970087124945541dd15ac081dd03a58', 3, '2026-03-28', '2026-03-28 20:15:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:14:21'),
(931, '6d14601bb69cffa8a0c5c81a4fe95ac420beb5a6ba324c509358d0eaf7b143ee', 3, '2026-03-28', '2026-03-28 22:43:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:42:40'),
(932, '740439890b7283ea68168673815cb9d81bcbdfa1a89b9a786fea9ddf4d8e1dc2', 3, '2026-03-28', '2026-03-28 22:44:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:43:10'),
(933, '8c2395e1cd9121c11aa365431254ca3c93cbdef5d1f391f621b5e6f9cd0be19e', 3, '2026-03-28', '2026-03-28 22:44:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:43:40'),
(934, '03c1ce612d19a0e44d2bd0affc37fac86495e9cab21151e98229a0d821585bce', 3, '2026-03-28', '2026-03-28 22:45:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:44:10'),
(935, 'c5d91500388bcf3b0c3e4dcd14c3e6256391b80bdae004d94b3320c3f7197c8b', 3, '2026-03-28', '2026-03-28 22:45:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:44:40'),
(936, '1c39b1922b3c43b0ce9d70880b975be3142cf4299b7a38c4fb10c0f73b7b3e1b', 3, '2026-03-28', '2026-03-28 22:45:47', 0, NULL, NULL, '::1', '2026-03-28 14:44:47'),
(937, '46a8696c7546143df3f1bafb308e356febca1bdc394415351994960a3067adb1', 3, '2026-03-28', '2026-03-28 22:47:29', 0, NULL, NULL, '::1', '2026-03-28 14:46:29'),
(938, '1e6e8948f991e291867caf179d51a98f241aed65139096522ef3728532b94629', 3, '2026-03-28', '2026-03-28 22:47:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:46:56'),
(939, 'fb8df7f6ffbfc657f6b771bc6fa9aa05b760c67da83e90056feffa76e80fca90', 3, '2026-03-28', '2026-03-28 22:48:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:47:26'),
(940, 'f097da2f416ecd5f3691d8d06d3a96fa0c061b804e212fa063e8b5e358b8e337', 3, '2026-03-28', '2026-03-28 22:49:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:48:07'),
(941, '28d474dea4f011df54c26d6784157970f61422540c16b87e7c6ea36802f3fb14', 3, '2026-03-28', '2026-03-28 22:49:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:48:38'),
(942, 'c47549ae6f1d9f3496919c1b1381e95ed7d59ef60584362fc7a0f1c6b5cdcc03', 3, '2026-03-28', '2026-03-28 22:50:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:49:10'),
(943, 'e3764c46e58661f42673f186aa69d2e0548c7bb56191fccb02f1a35e734b5b22', 3, '2026-03-28', '2026-03-28 22:50:41', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:49:41'),
(944, '5f2b8929be765edc6b8a2735dc390796da1a12cf3c0e167f1c9ddca9e3999c1c', 3, '2026-03-28', '2026-03-28 22:51:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:50:13'),
(945, '21a0fd33b3d436ccf0688d4eec40d62ad958708961d5a62f8a03889fa72f1bb2', 3, '2026-03-28', '2026-03-28 22:51:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:50:44'),
(946, '3b11ebb588a4bee482591fd9f386cf113daa2c05a3bb142c6c2502302f6e8f78', 3, '2026-03-28', '2026-03-28 22:52:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:51:15'),
(947, '84b5522d9068ab08cea669fb7325fe1395a59d5bc85a9a542363cc6d2d07360e', 3, '2026-03-28', '2026-03-28 22:52:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:51:56'),
(948, 'fc13ca274342f790b2f9bd423ef5866457edcf4c4e32d4019630bd0dbf6c1b86', 3, '2026-03-28', '2026-03-28 22:53:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:52:27'),
(949, '3f242baef432ab1007eab7f6a63523e4cf2de79af6b30fd3357bd0bd335dedbb', 3, '2026-03-28', '2026-03-28 22:54:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:53:00'),
(950, '9f7283639d6f5a8a27dacc3aa130f3c43da0746164fdd99bf1aaf2e8df110f69', 3, '2026-03-28', '2026-03-28 22:54:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:53:31'),
(951, '21bf736a6699c5080ec860365686340340f03e18a4c3cab24b0c1f4013486669', 3, '2026-03-28', '2026-03-28 22:55:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:54:02'),
(952, 'a00f3a5c2c6f0a72839e60286b00f6448c788d5dc68410ad7da7be6c3c71d8af', 3, '2026-03-28', '2026-03-28 22:55:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:54:34'),
(953, 'c054b4f03cf6bc0e36141309db6eb277896daba803d7c9e934f712958acb8132', 3, '2026-03-28', '2026-03-28 22:56:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:55:05'),
(954, 'af08e33d6c126728e371dfaae30a35381c987cdf557142acd21ac016ca6c6eac', 3, '2026-03-28', '2026-03-28 22:56:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:55:26'),
(955, 'd7b322e99279dde68fbce1059509a9ec5c85b13c36bd86bc4becd88168f05ab4', 3, '2026-03-28', '2026-03-28 22:56:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:55:57'),
(956, 'e5f2007a16c29d79cbe332983a59e397f115706ae107e256b6dd772796086fd2', 3, '2026-03-28', '2026-03-28 22:57:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:56:28'),
(957, 'e55ed2e5aaba23a8e3d20bab95b6d1a58b1414c805b601dddf2bec443e936887', 3, '2026-03-28', '2026-03-28 22:58:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:57:04'),
(958, 'aba7cf3a50a3613d7a3d524bc13efe8e296421a7c76b932bfa19b4d347f53765', 3, '2026-03-28', '2026-03-28 22:58:41', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:57:41'),
(959, '12480a5e576af6c40b7773180eacca570be5b0ee8fe34dfed07be2a5f145506a', 3, '2026-03-28', '2026-03-28 22:59:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:58:17'),
(960, 'e3ff305e334a81b6f27678fcb3449160f76bae4c4d80c2595c54ac8a0560a0a8', 3, '2026-03-28', '2026-03-28 22:59:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:58:47'),
(961, '9deefb7582d4111eeb0e1244b2367978613921e3e2ff841801cd0dbeee994d34', 3, '2026-03-28', '2026-03-28 23:00:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:59:18'),
(962, '57d2ac174011929466831410185e51ab4ea5ea007a6a28d2afc32e2cedfdb026', 3, '2026-03-28', '2026-03-28 23:00:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:59:49'),
(963, 'f975d57e9ac870337ea24970d63a1c41ba96bad2d700e9150a4e516638b49b10', 3, '2026-03-28', '2026-03-28 23:01:16', 0, NULL, NULL, '::1', '2026-03-28 15:00:16'),
(964, '062d2f2f0ab0dc5e99c16b2db7b8b7138e7003f9ea48485f7f3b6e449c1f116c', 3, '2026-03-28', '2026-03-28 23:13:11', 0, NULL, NULL, '::1', '2026-03-28 15:12:11'),
(965, '02229d8d2f9d6b2ce9d74ad2d83858c017952d3050b0f77f2c0bb6aa4df675e9', 3, '2026-03-28', '2026-03-28 23:13:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:12:40'),
(966, '11759556f2309e55466d89cabb182845539c1f5cae5077b874d2ca0bab352636', 3, '2026-03-28', '2026-03-28 23:14:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:13:10'),
(967, '825005020ecfdffabb6278d6d7fa2db5f0abc5aa5f65fb94b9ef4eb4687955ee', 3, '2026-03-28', '2026-03-28 23:14:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:13:40'),
(968, 'd9a7bc3839a5208ff6c7994e92f8bb3152564ae3d6576abe23ee2248d4ec7341', 3, '2026-03-28', '2026-03-28 23:15:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:14:11'),
(969, 'f01c693486c9755d9c4b489ada5f13a576fa25d8c3a1df2608162bd91d160ba6', 3, '2026-03-28', '2026-03-28 23:15:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:14:42'),
(970, '2722eaa5b1ca756631508e032054a94d5cd4c49eae9ca26ec8a87f1a98ffa978', 3, '2026-03-28', '2026-03-28 23:16:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:15:17'),
(971, 'f64dd99dba3896bd5a740ca12dfa2feedf3684f03033b52c87ad985f25481d97', 3, '2026-03-28', '2026-03-28 23:17:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:16:02'),
(972, '1965fbfb413c80cac199f3ac49d052449e3ffae131242523357398df8ba05644', 3, '2026-03-28', '2026-03-28 23:17:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:16:42'),
(973, '672a86e8d3b082c147da8ec26b4355b56fb709359f409c8eeeffbddb70d9b5cd', 3, '2026-03-28', '2026-03-28 23:18:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:17:13'),
(974, 'abf3c0d83f9ec98b03901fd3176465ba8717afaa9476138569ebbda8373e3fe7', 3, '2026-03-28', '2026-03-28 23:18:35', 0, NULL, NULL, '::1', '2026-03-28 15:17:35'),
(975, 'aa3cbf84089419c40246766d80720dc848ec125dd5dd3974103a22e84f84220b', 3, '2026-03-29', '2026-03-29 16:56:53', 0, NULL, NULL, '::1', '2026-03-29 08:55:53'),
(976, '79fe2d5fe67db29d7ca0b294926b48866d41fe2772817f867d7bd303914c4ae1', 3, '2026-03-29', '2026-03-29 16:57:24', 0, NULL, NULL, '::1', '2026-03-29 08:56:24'),
(977, '56564c04adc160fbb7e0d6b8f0fdb7b94ee6d6da73a16a3455dc7b068f7dfc09', 3, '2026-03-29', '2026-03-29 16:58:10', 0, NULL, NULL, '192.168.68.136', '2026-03-29 08:57:10'),
(978, '6ed96e708bea50cd55893c2db46c24cc1ffb8882ea6dbd04ad13a6e72d279d6b', 3, '2026-03-29', '2026-03-29 16:58:41', 0, NULL, NULL, '192.168.68.136', '2026-03-29 08:57:41'),
(979, 'db93de617759a1e3c2a701013cead778a9914fceb46c75f669c8232770640da1', 3, '2026-03-29', '2026-03-29 16:59:18', 0, NULL, NULL, '192.168.68.136', '2026-03-29 08:58:18'),
(980, 'f9b5cc8d60fbe3cb08bf586b96be40df125f00a16a1e4f234398ad1caaeda349', 3, '2026-03-29', '2026-03-29 16:59:48', 0, NULL, NULL, '192.168.68.136', '2026-03-29 08:58:48'),
(981, '73d99e125c68bf99d0bf335bca55535cd6ead3153ded1576b9d926ee6065fe9f', 3, '2026-03-29', '2026-03-29 17:14:02', 0, NULL, NULL, '10.231.241.98', '2026-03-29 09:13:02'),
(982, '9a05fdc304a0c4dc5eb87f27cae3cf8ba749a2bdc313c8539cc8b2f561ce6938', 32, '2026-03-29', '2026-03-29 17:14:58', 0, NULL, NULL, '192.168.68.136', '2026-03-29 09:13:58'),
(983, '6f1bb2989a4149200f460854b70b0df149fd239abb329f2761d704fed9c7953f', 32, '2026-03-29', '2026-03-29 17:15:29', 0, NULL, NULL, '192.168.68.136', '2026-03-29 09:14:29'),
(984, 'e9b843dd8adb950340017de5adfcf1a58dfe2e02283b26e03192a93ea9c832d5', 32, '2026-03-29', '2026-03-29 17:16:00', 0, NULL, NULL, '192.168.68.136', '2026-03-29 09:15:00'),
(985, 'a3b8ab0d3f844d0d7a10b14c0e2d893ba17fa7c0dea672a6291ca9696ff7b025', 32, '2026-03-29', '2026-03-29 17:16:34', 0, NULL, NULL, '192.168.68.136', '2026-03-29 09:15:34'),
(986, 'b232f8b28d9587d552f12bfec9584b96e2377fbf31bbe68d5975696f9ddbd77a', 3, '2026-03-30', '2026-03-30 09:49:23', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:48:23'),
(987, 'ca747329f251baecca39bd4dbd006cdd3514d0cfbebd8b4971984ee652eb1cb0', 3, '2026-03-30', '2026-03-30 09:49:53', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:48:53'),
(988, '9b783e0035dc500eca19b477a9ad3e043bbe1ed36b857b24956a6a872fd6c9b1', 3, '2026-03-30', '2026-03-30 09:50:23', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:49:23'),
(989, 'af6c7a7eb567349de21c9811744370000293fd6485608be666af85d3780a188e', 3, '2026-03-30', '2026-03-30 09:50:59', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:49:59'),
(990, '436385660b70de994424c84d05cdc76e9ca88b5e9a61d2d92a2dfbd6ceee79cf', 3, '2026-03-30', '2026-03-30 09:51:29', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:50:29'),
(991, '6c21eeda5eace42ac71b3d10ff800c05e9bdf0137f11bda64027018bcf8012f8', 3, '2026-03-30', '2026-03-30 09:51:59', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:50:59'),
(992, '87bd8d0b1acb0194ee99dc6217b0e735b2d6d303b38b69a56ce18b11bfecd9b6', 3, '2026-03-30', '2026-03-30 09:52:29', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:51:29'),
(993, '692aa2b63ca46e3625f3668f1905840b73b2cace5eac0ed140677cb83d0b8642', 3, '2026-03-30', '2026-03-30 09:53:29', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:52:29'),
(994, 'a3eff5bfc4e714f1bd38006d963824a157a9114ba7dcb0c8297b0b618dc99044', 3, '2026-03-30', '2026-03-30 09:54:04', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:53:04'),
(995, 'fb3725fb7a2b55d3124251299ae7f5a9464ff66404d1c4f5e91ae8fb302d44bd', 3, '2026-03-30', '2026-03-30 09:54:35', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:53:35'),
(996, '4e135279147f1cc5130da6d747ac0a9e7f2c1f83060fb729c1bfc85b489928c9', 3, '2026-03-30', '2026-03-30 09:55:09', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:54:09'),
(997, 'ee3c6afacf8528fbe803273618d96796f7da71618ae790ced2cf55d5be6ffa84', 3, '2026-03-30', '2026-03-30 09:55:42', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:54:42'),
(998, '256f90e868fb9c4ee5b931408e1ef2c48a8ea127e2b78d0aa218a410aa6a92f6', 3, '2026-03-30', '2026-03-30 09:56:13', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:55:13'),
(999, '468c5c0bb1358642b6b67677d4160a66a560876ce84b3b2ea97a3175cc98b995', 3, '2026-03-30', '2026-03-30 09:56:46', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:55:46'),
(1000, 'f8335dca872a0b8a2e383b528682ce8a5bccdcbd3db88dfc8bf93f4985fec30e', 3, '2026-03-30', '2026-03-30 09:57:16', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:56:16'),
(1001, '69d9eb60bf9602831c048075a11a0b0c90d1f7e5de64d6354e713fdac80f37f8', 3, '2026-03-30', '2026-03-30 09:58:16', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:57:16'),
(1002, '8bf62f623784ebe5dc90c1facd60a0761f3b7c221c5abcf3d5de3a7072db9543', 3, '2026-03-30', '2026-03-30 09:58:47', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:57:47'),
(1003, 'aa3a37871b7758e67b411d503614fba4dccd55f0b33c3822b35e84d36ce791c1', 3, '2026-04-02', '2026-04-02 00:50:03', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:49:03'),
(1004, '7f762405dd9a8bff808bb421828789817722438a18979bba683d8bca637e4a07', 3, '2026-04-02', '2026-04-02 00:50:33', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:49:33'),
(1005, 'be02d16e2154a8c3723e5b91578c02873db9cdad6575c0d2da9ff9be4d4bd533', 3, '2026-04-02', '2026-04-02 00:51:03', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:50:03'),
(1006, '03dd426ce86366db8e0611f1a0a5c553f4d7f37aa068a316dc80df20f252ce9c', 3, '2026-04-02', '2026-04-02 00:51:35', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:50:35'),
(1007, '6288bf425c8a755227b24e5e90df3e27618598ce7617c7f87d248e60bd74a58b', 3, '2026-04-02', '2026-04-02 00:52:06', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:51:06'),
(1008, 'dfe433ac0938c9d117df42a05335eb8efa43f127eda72c7da6c16c05fbecc7cf', 3, '2026-04-02', '2026-04-02 00:52:38', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:51:38'),
(1009, 'd1d4741b0449fae6f9e6c4bab11f2bb2912d360abb7db1204206d8d0441f82bf', 3, '2026-04-02', '2026-04-02 00:53:09', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:52:09'),
(1010, '8d4e14641aec5d5d0eda5e74aedee11bc78a9975b413746e7c1f3c02aa0a13c9', 3, '2026-04-02', '2026-04-02 00:53:40', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:52:40'),
(1011, '240e1ad6d15680b379e5ac2c9aefdb3fe7f4001d113cfccec17e5a89b481ce32', 3, '2026-04-02', '2026-04-02 00:54:11', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:53:11'),
(1012, '2abe8fbb199fb4aa6e456eb833f7b208c71e9d3242a4f24f925fec5e172640c6', 3, '2026-04-02', '2026-04-02 00:54:42', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:53:42'),
(1013, '4596b4a2568b4ab9aaf1a50800a704fae421aa99d3bd58a69651c5caefcaf94b', 3, '2026-04-02', '2026-04-02 00:55:13', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:54:13'),
(1014, '48773c1cfcbb0d80619a2c7cd60aaae89be54954e5fb63b55cb6fb13a431ec84', 3, '2026-04-02', '2026-04-02 00:55:44', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:54:44'),
(1015, '38d6242d61bb27da004ffbbd3637eab0998622b9031ee09440d200cd765c4239', 3, '2026-04-02', '2026-04-02 00:56:15', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:55:15'),
(1016, '59bc8778feddcd0e076101197461f7da54793fab6602e381f0ddcaf9288911a9', 3, '2026-04-02', '2026-04-02 00:57:09', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:56:09'),
(1017, 'c8716abe1d5530238f9637404e2ecf35f51f79bc88ca5dc841faa351968bde91', 3, '2026-04-02', '2026-04-02 00:57:40', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:56:40'),
(1018, '906fe3c4e7cbfb40551d33fdf514783becbfdb8c118795abf3ba623db11434b9', 3, '2026-04-02', '2026-04-02 00:58:11', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:57:11'),
(1019, '02cc5873f0cd9d3204cc9ec5503a9dee422ea26ff11546cbfa45b398eaa20cf0', 3, '2026-04-02', '2026-04-02 00:58:42', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:57:42'),
(1020, '52109472283cb4e018ced420b15be67b9c91bba5f1017a5e2b9b2b8402ae14ce', 3, '2026-04-02', '2026-04-02 00:59:19', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:58:19'),
(1021, 'e0e36db761f87a9a59dc00659a9470d16dd5ee2cc284b904c1116879bdaa26f6', 3, '2026-04-02', '2026-04-02 00:59:50', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:58:50'),
(1022, '7982d6774b993f486400fab8212c6c5aa73d45e6275b9344a4170c09d7d6a866', 3, '2026-04-02', '2026-04-02 01:00:21', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:59:21'),
(1023, '7089096e3cfe49419240b6389f7ec222f58ca57d1af7f2fe72adb93f354de2b3', 3, '2026-04-02', '2026-04-02 01:00:52', 0, NULL, NULL, '10.74.195.98', '2026-04-01 16:59:52'),
(1024, 'd39be673ebe381f14fc4fe2c6487d2aef7b73bd5d670a5195e07d5ecb5d31cc0', 3, '2026-04-02', '2026-04-02 01:01:23', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:00:23'),
(1025, '2545f7c4d6b1f7352c5a928366658211d377ff662d6ec5624980981b19c8e6de', 3, '2026-04-02', '2026-04-02 01:01:54', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:00:54'),
(1026, '9fdfe4d13ac982fae1b2e90b89a290d56205a54cb37885717cbf6c7e9946be75', 3, '2026-04-02', '2026-04-02 01:02:25', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:01:25'),
(1027, 'd9d61b8dbf3362c43a469ddccc2cf32200f980a6539356a7f28425945af21eb8', 3, '2026-04-02', '2026-04-02 01:02:56', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:01:56'),
(1028, 'c5737ab0e0425dd014de8b005e9fd85c9fca0822a7ec4c0d328fea7360d64c79', 3, '2026-04-02', '2026-04-02 01:03:27', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:02:27'),
(1029, '27e94f921121beafeee796fb01a6e727c87ddce08d792b2e0f6a9c7261cdc44b', 3, '2026-04-02', '2026-04-02 01:03:58', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:02:58'),
(1030, '993a286ed462c4e5c7fb6d3b21c02282f1d17d8a39a51629568507f532b5b3df', 3, '2026-04-02', '2026-04-02 01:04:29', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:03:29'),
(1031, '90418eb31882746cb64641aa240ccc29333103f63a618d3fb96fe42dcfe4246a', 3, '2026-04-02', '2026-04-02 01:05:00', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:04:00'),
(1032, '3aec90acb119af05eb8580a29465e202da92180bfded887772f27f10b27c31fc', 3, '2026-04-02', '2026-04-02 01:05:57', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:04:57'),
(1033, 'c28d77f5fba894410cb9060e691a63dadcc1ee5c7f074d23c0e8633fa5d15671', 3, '2026-04-02', '2026-04-02 01:06:57', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:05:57'),
(1034, '1cf7253ed95addec9886d978fc7ca64eb804422dd3127380388a77bb7ea70556', 3, '2026-04-02', '2026-04-02 01:07:28', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:06:28'),
(1035, '34df20504697251c93245d07ae3a089702176f36450dc45baae5df0c7eb5894a', 3, '2026-04-02', '2026-04-02 01:08:04', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:07:04'),
(1036, '0f266f0200b52f96b3aa930ba826fb041fdfbba87cd5927e14931d542e142f11', 3, '2026-04-02', '2026-04-02 01:08:35', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:07:35'),
(1037, '1436a8b96fd48846e78aed9b55057405ff05c175265ac0ba327b08ccbd396dbf', 3, '2026-04-02', '2026-04-02 01:09:07', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:08:07'),
(1038, 'd1a635e78dcbe64fc4932b797a970969953df5d4da1e94cde76735c21701fc12', 3, '2026-04-02', '2026-04-02 01:09:38', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:08:38'),
(1039, 'ff7b4fc1f6b2032392cbbf3aaa5fd9deb4e7346d24f4a85e49c1d043546d2a3c', 3, '2026-04-02', '2026-04-02 01:10:10', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:09:10'),
(1040, '5cd58911fe756a43f97ff59b65fe4235447a346c27790df13d8f2ca244e07532', 3, '2026-04-02', '2026-04-02 01:10:41', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:09:41'),
(1041, '73ab0b1ecf8bad0f18a1353af755e66e18349705996ffa484475aa792a25ebbc', 3, '2026-04-02', '2026-04-02 01:11:12', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:10:12'),
(1042, '8d499d8b302580eb63627645ec2e441918326a49be2fcf1f3545d3390fdaba55', 3, '2026-04-02', '2026-04-02 01:11:43', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:10:43'),
(1043, '121cb8e2322ea2a0f834ef66cc2e8bbd5680f722ef00b3da6c13e29b2b37195e', 3, '2026-04-02', '2026-04-02 01:12:14', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:11:14'),
(1044, '9f0c0dc529f220f76e89db26a2800194d5ceb87dadf585296491cfa5198fda9c', 3, '2026-04-02', '2026-04-02 01:12:46', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:11:46'),
(1045, 'd465fec6eb6eb0403fbbbe436406b92fbc57e6d28d94741faa0db065d99ae063', 3, '2026-04-02', '2026-04-02 01:13:17', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:12:17'),
(1046, '5bc0d343e47b8ff7559e99336ed5dcd104499bb3812d8fe12a316485ef3f8183', 3, '2026-04-02', '2026-04-02 01:13:47', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:12:47'),
(1047, '034537581e6544e38924fea3f1302fab546489632bdbb069988576c364612769', 3, '2026-04-02', '2026-04-02 01:14:18', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:13:18'),
(1048, '42805d1c2b75e1a8dd9167b68b34b72ea1928b47d92b45f39bb8eada21294e37', 3, '2026-04-02', '2026-04-02 01:14:50', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:13:50'),
(1049, '644bd95de2e24305dcac479a8c674943ca5a9383739b196be88a33d4a433277a', 3, '2026-04-02', '2026-04-02 01:15:20', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:14:20'),
(1050, '223caed51617cfb4df9fa4ad64c7d436d8c05db603c72e80602dd8cc7ee41c55', 3, '2026-04-02', '2026-04-02 01:15:51', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:14:51'),
(1051, 'c846702f438dfc3dc365215cca061e49f5aa56b499ef6f17be722112dfaa578e', 3, '2026-04-02', '2026-04-02 01:16:22', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:15:22'),
(1052, 'a049ca73c2c1ab7eeefd252dd993981fba435e338ce3a7d87d96bdebeb773e1e', 3, '2026-04-02', '2026-04-02 01:17:02', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:16:02'),
(1053, '2a7b58ead6cfddb96b698a7202b528b776c5a5fd8d78e9c9c2903e4269c0fb1d', 3, '2026-04-02', '2026-04-02 01:17:34', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:16:34'),
(1054, '230bc70e2b779fa357b6aa069e679626bf85fad972c3320176fcb18b02ce39d0', 3, '2026-04-02', '2026-04-02 01:17:38', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:16:38'),
(1055, 'a1516c6d986c2bf85631120ff698520273a840a0f4e78569be3dd743959d99b6', 3, '2026-04-02', '2026-04-02 01:18:08', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:17:08'),
(1056, '3b4dd0739f5c2c69c1b760fa7def1726e902488db3a7ffd6426f3aa75417a19d', 3, '2026-04-02', '2026-04-02 01:18:39', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:17:39'),
(1057, '3eea41071ce31a455af368abea90923e4a689cc20212d3b801a19b9885d89b57', 3, '2026-04-02', '2026-04-02 01:19:11', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:18:11'),
(1058, 'd737d1f37b5973b59bfbe6a4b3e7fce497e219e4dd85f00ea0a3c0a441e4fde7', 3, '2026-04-02', '2026-04-02 01:19:55', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:18:55'),
(1059, 'c95190f68c75562867286a7dcdd1cf891a69c0573e303e4b1e134fce8cd4d07d', 3, '2026-04-02', '2026-04-02 01:20:26', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:19:26'),
(1060, '58523d07ca7828ada85dfd0be7afe9a76b49c84c4526922155f03130ad4556a4', 3, '2026-04-02', '2026-04-02 01:20:58', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:19:58'),
(1061, 'af34fa377b2f7287b4186430257b1c9e74443d048e0b7c7298665fbf016782a1', 3, '2026-04-02', '2026-04-02 01:21:29', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:20:29'),
(1062, 'b26ff383196b4dfc0411e1c671ab0f2a3b1123b86f1e3b399be21fb66a18ee29', 3, '2026-04-02', '2026-04-02 01:22:02', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:21:02'),
(1063, 'de17a392e7fd9b7feb6b59a9a830881f835d13850fb6ce7eb9294f3ec551ebe6', 3, '2026-04-02', '2026-04-02 01:22:35', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:21:35'),
(1064, 'a2453415702a44e58db96facee022609002da32393256c1340c160be3c7c10b0', 3, '2026-04-02', '2026-04-02 01:23:19', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:22:19'),
(1065, '10ef4dbad56fe2cf59036ec1b7dcfef85347c4e23bba5642fd264dc11384c86d', 3, '2026-04-02', '2026-04-02 01:23:49', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:22:49'),
(1066, 'f63c6688db912a1047cb8dedbe0aab1084fb049d3b65638c96ec88ffd7e90abf', 3, '2026-04-02', '2026-04-02 01:24:20', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:23:20'),
(1067, 'da3a8a7b8e2f802d6a2e89b6d5857394de31ea8ec77ac113010a479610b19637', 3, '2026-04-02', '2026-04-02 01:24:50', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:23:50'),
(1068, '6eb40ff78ad73a7e3965ea1b90a0972aad8ec6fa8c80daab1df29bb34b8bcb91', 3, '2026-04-02', '2026-04-02 01:25:21', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:24:21'),
(1069, '102ebc0b2bf24df69906db3f4a05e3f608d5e20e579d8284df438fdd95bb3c3d', 3, '2026-04-02', '2026-04-02 01:25:52', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:24:52'),
(1070, '80a0228bfd22af47c4e7042ae9cbcd544e7f1124da5f40f6e39a85b30417a5ee', 3, '2026-04-02', '2026-04-02 01:26:22', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:25:22'),
(1071, 'a9a1e12fb5d1b022d62494c93a4b55d853d503cfa5e838aa17d133d9cde0e925', 3, '2026-04-02', '2026-04-02 01:26:53', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:25:53'),
(1072, '9db6df72ec771f877434b3e06a1b82b909a99ec8641ee99d456b083c5e7a9346', 3, '2026-04-02', '2026-04-02 01:27:23', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:26:23'),
(1073, '2484844dc66f9cc598bd4ba8b9f60c61c98d6234f50ba30354dbe7f2d3b09d54', 3, '2026-04-02', '2026-04-02 01:27:54', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:26:54'),
(1074, '71608d0dfd9c048e8adb739d8285bfbb1df72a0ac22812d1dc4781d87146b684', 3, '2026-04-02', '2026-04-02 01:28:26', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:27:26'),
(1075, 'b1f25e21de0498c3cccc4fed59a9621b71c4c041ee4b196891f8e79b52a3bf0e', 3, '2026-04-02', '2026-04-02 01:28:58', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:27:58'),
(1076, '656cb049e4f04d3ea6f5ffeeba74138b1d7ebf4f1661d76b63040ed86aa707b2', 3, '2026-04-02', '2026-04-02 01:29:29', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:28:29'),
(1077, '81605ffdfeabd7caeda25ed1f938f4f102e8254bc490a29307fe6bccdf662527', 3, '2026-04-02', '2026-04-02 01:30:00', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:29:00'),
(1078, '91dd66f4e1d36eb7f9860a05e69f62147ecb24c7a3549cd90e471fdfdd1c020a', 3, '2026-04-02', '2026-04-02 01:30:31', 0, NULL, NULL, '10.74.195.98', '2026-04-01 17:29:31'),
(1079, '85b62f61519e3ff3c9b0f5fb994af61b246cf732ef0388e6dc475614f91d6e89', 3, '2026-04-02', '2026-04-02 13:59:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 05:58:58'),
(1080, 'ee9b1d53a31a251b171d3e9dc11c973ca78835ad3f6f2bd5b9136be3f0125c0a', 3, '2026-04-02', '2026-04-02 14:00:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 05:59:29'),
(1081, 'd8489c5d0f6555b73307760377a76d71525fcf9eafac2d63396f606893284b29', 3, '2026-04-02', '2026-04-02 14:00:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 05:59:59'),
(1082, 'a1255bdeec9b8b5547f8b41667b63f16075e25b5992e491179bd68c0bd3df827', 3, '2026-04-02', '2026-04-02 14:01:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:00:30'),
(1083, '6f339b3c4141469a1e2727d2d4328f30364525e16be7a77e73b0623356a88577', 3, '2026-04-02', '2026-04-02 14:02:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:01:01'),
(1084, '5f31b70c37a6da79779cb55b0135b3fc2688db01dc4d4892f064838ccfae1220', 3, '2026-04-02', '2026-04-02 14:02:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:01:32'),
(1085, '14c26aeaf87bed07bd6d3862a775dfffd9e696986a374ec3b3f92b6472834820', 3, '2026-04-02', '2026-04-02 14:03:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:02:03'),
(1086, '7f57a1ce59c903a0dfd85adfe14863a9a29fa62babb608c92acfaad98d26e154', 3, '2026-04-02', '2026-04-02 14:03:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:02:34'),
(1087, '0645bef122193e98c08bcc7911a50290a37d261ed1927ba20c306759fedebda1', 3, '2026-04-02', '2026-04-02 14:04:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:03:05'),
(1088, 'c71aa1d8e873c15c3f219a8acd69f5a32ded35068e5e387e60837c5883003c77', 3, '2026-04-02', '2026-04-02 14:04:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:03:36'),
(1089, 'b3b3deb79ebfc303119e52d8bab195213c1003b3d911ccda04c8147b82eb5a32', 3, '2026-04-02', '2026-04-02 14:05:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:04:07'),
(1090, 'b88aa1a5ab7252995a361a831b1a4bfad9ece042df3350311ea2adf7b81c489d', 3, '2026-04-02', '2026-04-02 14:05:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:04:38'),
(1091, '6a026a72a93c600ae058037adc268a17466b225b335b73b507408249dcb65465', 3, '2026-04-02', '2026-04-02 14:06:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:05:09'),
(1092, '1ab6c36c1e703327541a409f4f3b8d343e88285d4a8da1c4da0a01752077f26e', 3, '2026-04-02', '2026-04-02 14:06:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:05:40'),
(1093, '869b13556543ea0ca0e92653756a3a84167a788a1c8afe5e72acb855c65d5f5d', 3, '2026-04-02', '2026-04-02 14:07:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:06:11'),
(1094, 'b9074dcba0bf592891976605bf269718e1919fac9f62e9fcd972cb7d5163ed05', 3, '2026-04-02', '2026-04-02 14:07:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:06:42'),
(1095, '19d0aa52bf42c4b65d2c183f370d6160ef4960d548a2252ad2ab72eeae20b8dd', 3, '2026-04-02', '2026-04-02 14:08:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:07:13'),
(1096, '4bdc2fb05197325109f7f4b3ca4aaa6c8694e8449658beabcd401d0aa9fd7852', 3, '2026-04-02', '2026-04-02 14:08:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:07:44'),
(1097, 'd1f5e63143c3c732bacc0bcca5a9825656ee62c64d97e9acbf7ef60b31fd7f57', 3, '2026-04-02', '2026-04-02 14:09:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:08:15'),
(1098, 'c5f27a8291fe047c93bdd1eb0da30d2192be9202cae097dcb0c5c0df0872c794', 3, '2026-04-02', '2026-04-02 14:09:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:08:46'),
(1099, 'e984a7e958aeccb4bcf0e604f69e8d807d35dd9323b2424153d2cdf6829eca73', 3, '2026-04-02', '2026-04-02 14:10:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:09:17'),
(1100, 'fb070d2ce28fadb4009b82d5a0d150a441c0307d6c400e2e699ba5119f73297f', 3, '2026-04-02', '2026-04-02 14:10:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:09:48'),
(1101, 'd3e5d2da4491ade9354c0a4f810a2d6d4c64104bf1c44e0e617a49d9339a42f3', 3, '2026-04-02', '2026-04-02 14:11:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:10:19'),
(1102, '7f339ac15207063b93e07571953c963c21e5c5ba6bdd367e7323ca49f4115200', 3, '2026-04-02', '2026-04-02 14:11:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:10:50'),
(1103, 'ec727a52fbf753e61f51b08a58389c0d9b37346e452fb9cee4b8c88c6b4d870d', 3, '2026-04-02', '2026-04-02 14:12:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:11:22'),
(1104, '7ba236d4ff76c9f70dc345703f33afc92af94aa32e1e8a57516562ddcc2f6df5', 3, '2026-04-02', '2026-04-02 14:12:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:11:53'),
(1105, 'd7d15ebabe1cd395868870c425ac78c5d4f2d47f5d2bbcaf1a4699211aa1e573', 3, '2026-04-02', '2026-04-02 14:13:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:12:24'),
(1106, 'b04886cb8bb6a85c6f6ab47a64f104bda71e65f461aa0f161cfde7d5b6834e2d', 3, '2026-04-02', '2026-04-02 14:13:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:12:55'),
(1107, 'dfb0451733247fea267ec15d26a7c3b63f6d0f3f4995c0efdf60d2ab3931ce75', 3, '2026-04-02', '2026-04-02 14:14:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:13:26'),
(1108, 'a8d470e55d699a7cec499d3243a1d7f7636aa9427d13c6cde66088820a7a7243', 3, '2026-04-02', '2026-04-02 14:14:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:13:57'),
(1109, 'ea8fae5ef78db67be7b0818a05376e5d992063230c2db1c3006dc26ef92ee364', 3, '2026-04-02', '2026-04-02 14:15:28', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:14:28'),
(1110, 'b71da96c851ad35acf423686be41cdfc7c3f92094c44ff10d5de54f8f720d03d', 3, '2026-04-02', '2026-04-02 14:15:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:14:59'),
(1111, 'dec15f3a5f3b914440da66d90745f4d526e67f05e47b84cd6bb9b3a0e876ffee', 3, '2026-04-02', '2026-04-02 14:16:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:15:30'),
(1112, 'db75aa2059dd2ff2f8cbcbc059e911435a86d56512bc330e3c8664cbadb1d3b9', 3, '2026-04-02', '2026-04-02 14:17:02', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:16:02'),
(1113, '603f908a95d37ec43482c7f2e94d42f1d344620a4676f3289ecd3519d77190e6', 3, '2026-04-02', '2026-04-02 14:17:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:16:33'),
(1114, '3aa66d3710e3e6e00ba0c60efd135ec27fb87afafb133402590fbda7bea484e2', 3, '2026-04-02', '2026-04-02 14:18:04', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:17:04'),
(1115, '7aa9720f2d4eeb42af8dfb5d1021ba4ac12f0a9af529682ec9237872d962ff9a', 3, '2026-04-02', '2026-04-02 14:18:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:17:35'),
(1116, 'ffed62cf82cba612dc61a3b9f8963f41fb1eb325c95da9ea21feb5c0226f9e05', 3, '2026-04-02', '2026-04-02 14:19:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:18:06'),
(1117, '692aea0ccdae945d3ae204e5c89ea6063494121610de063bdb73dead1fab3288', 3, '2026-04-02', '2026-04-02 14:19:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:18:37'),
(1118, 'b820dcfaf1ccbbbdea2012bfb6fe392182edd78b611aebc4ceeccafd8358f068', 3, '2026-04-02', '2026-04-02 14:20:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:19:08'),
(1119, 'd7ddba4906d0d394b5c3b26bff25fcf254d987a4712156973920e738bf2cf529', 3, '2026-04-02', '2026-04-02 14:20:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:19:40'),
(1120, '9603f2116a6a291829e25533b0ed63a798e34cdf9eeb05fe0db052bdeb7bcc3d', 3, '2026-04-02', '2026-04-02 14:21:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:20:11'),
(1121, '901589fe3afb382b4f3bdedd2ae0f616d22bd276951d9af3155d0d1d61b6d1ec', 3, '2026-04-02', '2026-04-02 14:21:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:20:43'),
(1122, 'c2fdd96f99430e09a4949affef3f6fde01fe5a58dca32e912689c41e3959e4b6', 3, '2026-04-02', '2026-04-02 14:22:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:21:14'),
(1123, '31b3f964334b413fe1cfe20c839997b3ddd340a2ac7ae60fa3d26232a9a4738d', 3, '2026-04-02', '2026-04-02 14:22:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:21:45'),
(1124, '8f3e5bec7a069225aa3cbac6a2d3fc383b45cf7594298a90650f7b0f271b339a', 3, '2026-04-02', '2026-04-02 14:23:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:22:16'),
(1125, 'ad3aeb20ffdb2f04d6fa84c6f3af7c7e84ee1ee484eeaae4573e7ca1b82c72f9', 3, '2026-04-02', '2026-04-02 14:23:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:22:47'),
(1126, 'b79a6cc7999143c7ec5037bc37f562d1fe3699eff6d9e5d5192c83bba770126b', 3, '2026-04-02', '2026-04-02 14:24:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:23:18'),
(1127, 'a5d423fb5ed1a8d7c542758ac386bc2e417fbbc0a4b7be4bc266b687416b9e78', 3, '2026-04-02', '2026-04-02 14:24:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:23:50'),
(1128, '7dd045e5dd0247d42fa04bb13828f70680b03ee64f8fde2b73ce90a5802170f4', 3, '2026-04-02', '2026-04-02 14:25:21', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:24:21'),
(1129, 'fe1a20f9823a0989a1ce6970af18511810309f104b8b7426400a6224fc942830', 3, '2026-04-02', '2026-04-02 14:25:52', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:24:52'),
(1130, 'f9930e0351a26626ee6e100f0dbbc7a9c123fbc3578324b82c6db450406e5d69', 3, '2026-04-02', '2026-04-02 14:26:23', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:25:23'),
(1131, '67ed10c2bdeb99d1f5fc999d8968b27a4d4cfeefc63c42fd9390ffd682a7a07a', 3, '2026-04-02', '2026-04-02 14:26:54', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:25:54'),
(1132, '40bd00018e9b960f0d0e3a2b1f8ba3589731bd8592e51394d9944bf36d426e2e', 3, '2026-04-02', '2026-04-02 14:27:25', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:26:25'),
(1133, 'f6770a00b0440d6fa1b5a03fb91f67ef41966630993c770a829704a79051746b', 3, '2026-04-02', '2026-04-02 14:27:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:26:56'),
(1134, '290623a7e3a481adcd4581f8e48f30721486a1f9a04da74cd0ffaacaa5529a82', 3, '2026-04-02', '2026-04-02 14:28:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:27:27'),
(1135, '39a1585812a9dd245047afa382cd6fda329de4365504ef8f952e648cda6274d2', 3, '2026-04-02', '2026-04-02 14:28:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:27:58'),
(1136, 'dcd2b8e3530f117c13870434428ef718c0ea9dac1fe591accbdb57ed3ca6f47f', 3, '2026-04-02', '2026-04-02 14:29:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:28:29'),
(1137, '2c663a5d76ffcf52a5b93fedc28dc01e2e9ba52fed5dcaa95b8121fe281134f0', 3, '2026-04-02', '2026-04-02 14:30:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:29:00'),
(1138, '74b0f27a0aae804f35c9e5f8b91c271874f6611bc15db6af18ddb1e9b135e98f', 3, '2026-04-02', '2026-04-02 14:30:31', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:29:31'),
(1139, '91d3d15e8889bfaf86d5fb83dcb157b63abd0a5b713e227f782a2b1c948e31d9', 3, '2026-04-02', '2026-04-02 14:31:02', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:30:02'),
(1140, 'dae749a6c740b5c6d34d136e3831c72023f57e301493dd52c69538d60b7a248a', 3, '2026-04-02', '2026-04-02 14:31:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:30:33'),
(1141, '19867645d7c2a7a9c0f3972ceadbf1ccc9f6cf3fa3f1cd0ae545e478a4066f3b', 3, '2026-04-02', '2026-04-02 14:32:04', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:31:04'),
(1142, 'a09891c77aaa9bb3df17832c623dfc72045e251939bb3727cbfc7afd66a40931', 3, '2026-04-02', '2026-04-02 14:32:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:31:35'),
(1143, '3120183e29578cb98e43270d50071299bf532c6934aa2a70d811e48ca8d99c4c', 3, '2026-04-02', '2026-04-02 14:33:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:32:07'),
(1144, '5a44a2857309be2a3639fe4192460d9d9d147477ada83258f8ddade6d45138a5', 3, '2026-04-02', '2026-04-02 14:33:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:32:38'),
(1145, '87676ace39df1239c965ae189fe09f51a26a250323520554ef4266aace411eef', 3, '2026-04-02', '2026-04-02 14:34:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:33:10'),
(1146, 'a9f044753bfa330f31880b005fd696fef71f293ade1bb59de73c3d246482ff67', 3, '2026-04-02', '2026-04-02 14:34:23', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:33:23'),
(1147, 'a3ab9d5e11b624954831b052a7e2f1d89f9bac502fa1b23dfcd48d0837926341', 3, '2026-04-02', '2026-04-02 14:34:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:33:41'),
(1148, '592bc2746f1688aed2c429e03ebf35ba536981a0d805318d8ee6fb42f3c7c07f', 3, '2026-04-02', '2026-04-02 14:35:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:34:12'),
(1149, 'e7fba3dda7ecc9e805279241c9c0fb8ee92aef65a7cc141ff0f17a5bd33723d9', 3, '2026-04-02', '2026-04-02 14:35:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:34:43'),
(1150, 'f7c51823e5e65db49c07924fed25a6f5a82dc1ceb8a1424bcbe124a9ce18dcc8', 3, '2026-04-02', '2026-04-02 14:36:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:35:15'),
(1151, 'b129e7f3b3922cfb83226d4d60b522dfed6f9392c70f1fdb5b240b997108f204', 3, '2026-04-02', '2026-04-02 14:36:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:35:50'),
(1152, 'b5f1bf831245150593f36620873f717487a38d6fde128e2f4fcf1e2be4bbb318', 3, '2026-04-02', '2026-04-02 14:37:21', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:36:21'),
(1153, '04fb0f28183441fe6d2618fbe01560de8251ce9796b4e30e545cc105efff4743', 3, '2026-04-02', '2026-04-02 14:37:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:36:53'),
(1154, '60fff1f4b2b48c3de80a89eac7356ff7d9fa1c225deb876aa4938a73f0941889', 3, '2026-04-02', '2026-04-02 14:38:25', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:37:25'),
(1155, 'cc4a41fda283f02b7088543ce9576de6c0638ea767a48af4a26b90b27c84c151', 3, '2026-04-02', '2026-04-02 14:38:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:37:56'),
(1156, 'd25aaee685de28f5c340522f657c95fe9e0ba59af8c46c228d734655b24e3c66', 3, '2026-04-02', '2026-04-02 14:39:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:38:27'),
(1157, 'ceff7fab5de9e7b04fcd24d0356dd047459908f081c88b816f19c5a66d165509', 3, '2026-04-02', '2026-04-02 14:39:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:38:58'),
(1158, 'b48b333f5dfdd363fe495361e3f23b10ebcf513f1193f1b84eedb7a17a5d11eb', 3, '2026-04-02', '2026-04-02 14:40:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:39:29'),
(1159, '4004f6a4d8365dc8e026e536209d96c539afdd94466d5d89ed7657f288ee852c', 3, '2026-04-02', '2026-04-02 14:41:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:40:00'),
(1160, '3099117c67dda406c42e9a3c80d8c8c4bb1d201ace309b20461e29ebfb578bfe', 3, '2026-04-02', '2026-04-02 14:41:31', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:40:31'),
(1161, '9cc3806fab87833f7c1083b309d3a7bf0b617a9cd0369c62654db39734edf6d8', 3, '2026-04-02', '2026-04-02 14:42:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:41:03'),
(1162, 'abe677fff6d66908a6cc338021f4fad1d9ba9fa54287931fda74347978cbb750', 3, '2026-04-02', '2026-04-02 14:42:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:41:34'),
(1163, '54f3478ee78d7221cb437894f36e74b642dc4b15d494817b0853446bc97048f8', 3, '2026-04-02', '2026-04-02 14:43:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:42:07'),
(1164, 'f15a9973b1dbb2897af4cfd11b761b38cb02cec9d1910c01cc0440f953fb879d', 3, '2026-04-02', '2026-04-02 14:43:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:42:38'),
(1165, 'e26cb8bdb027c8901c75afaf5adb38333ba290d6d019346464fa37d7106e0bf2', 3, '2026-04-02', '2026-04-02 14:44:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:43:11'),
(1166, '3294b3af162208d447ebc13d838f8398932a4d1ce2a4ec86177f818937c66b2b', 3, '2026-04-02', '2026-04-02 14:44:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:43:43'),
(1167, 'cd9a2100b4463b75d82ff9f794979d6e88bc9534cae152758d7a2a12de8598bf', 3, '2026-04-02', '2026-04-02 14:45:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:44:15'),
(1168, '37582c843abf102cfd285ab7f21700023ff037cd0c906edac2470090bf9c6c6b', 3, '2026-04-02', '2026-04-02 14:45:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:44:46'),
(1169, '5390e6dcf349bafc5b9c68ea5d7a3fe968969431576208646460f587edf82ea9', 3, '2026-04-02', '2026-04-02 14:46:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:45:17'),
(1170, 'fd6edec979cc3c880f469f453130d39cc32b8a1054d54be0c3909315dcea450e', 3, '2026-04-02', '2026-04-02 14:46:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:45:48'),
(1171, 'b340e5d68518f4e48f08951e602ffd62f9b62dbd45428dcf8be144a8afa10b68', 3, '2026-04-02', '2026-04-02 14:47:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:46:19'),
(1172, 'c90c942526f2d4455550e30d79fef76e5aff355111bdb3b1e72ed6cb9612adb8', 3, '2026-04-02', '2026-04-02 14:47:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:46:50'),
(1173, '82be8e357cd67e0a824db85d9faf3d45d73450987452c2a5ababa4d518ba7447', 3, '2026-04-02', '2026-04-02 14:48:21', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:47:21'),
(1174, '01d246f6f138b4274e601a0d21c3f89c8b8dd89854b6a0a70f991e904fc30241', 3, '2026-04-02', '2026-04-02 14:48:52', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:47:52'),
(1175, '9bb56ac50722f6d928b3d3448b7dc8ee897c9e78f11bb30e3192c2a42852b323', 3, '2026-04-02', '2026-04-02 14:49:25', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:48:25'),
(1176, '4ddb2757ba338e5d149da2f733d6f27b284ebb50aeb4360cd2649988df112338', 3, '2026-04-02', '2026-04-02 14:49:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:48:58'),
(1177, 'b2414cb60ea20bc8f05c8a617a430aac8d4a40dcb7f5a5a1592594c2f155bbf5', 3, '2026-04-02', '2026-04-02 14:50:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:49:29'),
(1178, 'afcae2888f25f748e6fecb635ee50f4587730475c60ebce161a0439202af8456', 3, '2026-04-02', '2026-04-02 14:51:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:50:01'),
(1179, 'c2883242faea761d5129be0f6cd52ab71daa2a8e89f32801db2f9d8b10dad482', 3, '2026-04-02', '2026-04-02 14:51:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:50:32'),
(1180, 'dec88931d34a060a4d2d5850487d0e52a577f254f18e7f3e8d0e5cf1cb8f5c52', 3, '2026-04-02', '2026-04-02 14:52:04', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:51:04'),
(1181, 'd10a2f872f76bddd13efce3a756ead16845e2f88c5d18df27196c70a8c422e09', 3, '2026-04-02', '2026-04-02 14:52:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:51:35'),
(1182, '4cbc18ee78adeef83831155767453dbf020077fae89d2590f7ae741cde4235af', 3, '2026-04-02', '2026-04-02 14:53:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:52:08'),
(1183, '7121c7922b080b0f9f5faded8debc73ed878f8203ba704b49792aa027d641e28', 3, '2026-04-02', '2026-04-02 14:53:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:52:39'),
(1184, '43e58847ea155e3b50c43716930ab0a9256a89a0ccf638b8ca2ef70189cfd371', 3, '2026-04-02', '2026-04-02 14:54:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:53:12'),
(1185, 'add61414a7607da3ab40f4ff9395694cff9a4413d6b829f17a6881b131395aee', 3, '2026-04-02', '2026-04-02 14:54:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:53:46'),
(1186, '79a56b792cc41d7be0e6ec846346ca3b942836727a2be6ce32948436dfd1d90c', 3, '2026-04-02', '2026-04-02 14:55:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:54:19'),
(1187, '585acf9635fa50b7ea854ad723e14f74a877284c9e69a6f0f44dcabc5d416465', 3, '2026-04-02', '2026-04-02 14:55:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:54:50');
INSERT INTO `ta_attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(1188, '07879a5918cc002a451c8d2240f684c37bce70fc7e20cc9152d59555df83b95e', 3, '2026-04-02', '2026-04-02 14:56:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:55:22'),
(1189, '972c52938ac97db3a4a74d89eb3fb478192a32337ba0f026ca4b114a72fcf7d1', 3, '2026-04-02', '2026-04-02 14:56:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:55:53'),
(1190, 'd061a7fb26643ac4a717b7a156447f5937db59969f669973606f051982c99035', 3, '2026-04-02', '2026-04-02 14:57:25', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:56:25'),
(1191, '67ba401c49b6c20771450b0926ee68f1e7ac44773080399789d904a5565a68ac', 3, '2026-04-02', '2026-04-02 14:57:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:56:56'),
(1192, 'ebcb4dbba2c4b7dbb4955e0f816eba5145ab92511accec166fbc0ac7d8a901d3', 3, '2026-04-02', '2026-04-02 14:58:28', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:57:28'),
(1193, 'f70f5e45054757b9b2ab102cee30382c0ef4ddf61247ce414785038cfc7ef6c9', 3, '2026-04-02', '2026-04-02 14:58:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:57:59'),
(1194, '88d8ac0e2304eabfa20c96023b2da8f49d0645129caa9781b89cc993a18b8b77', 3, '2026-04-02', '2026-04-02 14:59:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:58:32'),
(1195, 'eef2359c152d6c01ff5004a250ef2c7c41d7d08cbea0e0dfcad0cc88c6491806', 3, '2026-04-02', '2026-04-02 15:00:04', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:59:04'),
(1196, 'c44d190d67473b3d0f78478713cb5c392e014cfaf533dd7520c2b30cf35c3492', 3, '2026-04-02', '2026-04-02 15:00:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 06:59:36'),
(1197, 'a0231665c7f4c3670857a570d1300e6f285d4f47819fc40915e2e5b38cb213ab', 3, '2026-04-02', '2026-04-02 15:01:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:00:07'),
(1198, '9a2e6790fb39610b605a2e2c19d490fd0735a22267f16f931778b2340648bb9c', 3, '2026-04-02', '2026-04-02 15:01:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:00:39'),
(1199, '6f850fda05f2dfa7087b94f74d813959b7b888464eec36981ca0e6f32d1e19d7', 3, '2026-04-02', '2026-04-02 15:02:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:01:10'),
(1200, 'ace579ac9cb3fac144a3bd13dcc12a8491e5af16e1224f16a4591d9e79b6b12f', 3, '2026-04-02', '2026-04-02 15:02:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:01:41'),
(1201, 'ff2600682f9007885b932153945861892f47896595ec4d58e338a4a2986ca8d8', 3, '2026-04-02', '2026-04-02 15:03:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:02:13'),
(1202, '0b6d06d5125385be80d399f640021fcf9047bff1173b561a57f78e8de78839a1', 3, '2026-04-02', '2026-04-02 15:03:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:02:44'),
(1203, '9576973ec076522aba77d90932e29d3d7acb552d6ccb068f26139be3508630dd', 3, '2026-04-02', '2026-04-02 15:04:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:03:16'),
(1204, '6f93d690d27f1a68ecfc1a9be3e49b4b11d3896956bf1eab3afd71860d88853e', 3, '2026-04-02', '2026-04-02 15:04:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:03:47'),
(1205, '5bd9f8e09ebc278038b5b9e9bbd54fc95fc0abbdaf2402e20fbda70723892d33', 3, '2026-04-02', '2026-04-02 15:05:20', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:04:20'),
(1206, 'ab0eebb295ad0f7f3f735680b739c5bdcc2bda4c04ebc6ce52897e50583a0ecc', 3, '2026-04-02', '2026-04-02 15:05:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:04:51'),
(1207, '8b3dee276cee501952daf8f9bc414437ab531459cc7b621f3abac2431350cc3a', 3, '2026-04-02', '2026-04-02 15:06:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:05:22'),
(1208, 'f54e0937cca1de6aea8ada439b4ebb9aa600db9452ba97f35d8c85b5ae9db9a2', 3, '2026-04-02', '2026-04-02 15:06:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:05:53'),
(1209, '966d61f10d4842dff08378497997fca7d60a7a859a00b059d182d6a9a8e4da09', 3, '2026-04-02', '2026-04-02 15:07:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:06:24'),
(1210, '4a80b06cac7da2d255e29315198d04d0c820b08d1960f6df71682a876abe71be', 3, '2026-04-02', '2026-04-02 15:07:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:06:56'),
(1211, 'a1941e04efe50dff28fe6e204d9d4299fdafbfd3f47d5f8ea151dc172b38103a', 3, '2026-04-02', '2026-04-02 15:08:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:07:27'),
(1212, 'bc43064662666827d972061860da2cf274d987ab7e770bf9dab6c86ab08652eb', 3, '2026-04-02', '2026-04-02 15:08:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:07:58'),
(1213, 'dfc56b251c3a967a073ea65bc38f23fd1c7e695bcb518f9405e7f12e49e45b74', 3, '2026-04-02', '2026-04-02 15:09:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:08:29'),
(1214, 'bf1ec6b338c401e275599af58e8d27581574628a4af79c5f88d778e73bd835ef', 3, '2026-04-02', '2026-04-02 15:10:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:09:01'),
(1215, '35736f8d3557d5a1a7824d20b8bc85e72c39103309e6541e27d1b7ff9e2d778e', 3, '2026-04-02', '2026-04-02 15:10:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:09:32'),
(1216, '361844c2fc727221c5ee36e66a9e0dc375c4f0e588ea72c5cbe1b8393b459ea8', 3, '2026-04-02', '2026-04-02 15:11:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:10:03'),
(1217, 'f51ccaceda9c5c2098547e762130c2d6302f1e7dc034101e002976fdcae3d941', 3, '2026-04-02', '2026-04-02 15:11:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:10:34'),
(1218, '3f6f508fa10e023b87912f0f526c2203afc434a39262309d8536bd205b34c974', 3, '2026-04-02', '2026-04-02 15:12:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:11:05'),
(1219, '45658aac294ae1745c5dc2e594dd6ff8f8d9d3e83537bae075905a66523ec934', 3, '2026-04-02', '2026-04-02 15:12:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:11:36'),
(1220, 'acbd911ade630f7b7fcda384888349eefeac591d834ef71d6ccbfc0746ebfc18', 3, '2026-04-02', '2026-04-02 15:13:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:12:07'),
(1221, '2b0540bb3d7edd906a5932dada7c94cef5b2301e3ac0993e1888a36aae5c71e0', 3, '2026-04-02', '2026-04-02 15:13:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:12:38'),
(1222, 'b2251291ba6cfb3180f52c0c8cd0851b3205494c886dc631fdd314b37504eb7f', 3, '2026-04-02', '2026-04-02 15:14:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:13:09'),
(1223, '4e1c269211d83b0f4edf82bb7c5c78fe0e645d19bf901b6185bca34d6c417508', 3, '2026-04-02', '2026-04-02 15:14:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:13:40'),
(1224, 'bb2d5274264977836d758921f329fb43946056a9007811e1bfb0bf110fb730cb', 3, '2026-04-02', '2026-04-02 15:15:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:14:12'),
(1225, '0c61e949967f15c5c1f9e06d838c2899b16801d9b4f2811c309407f427c70d0e', 3, '2026-04-02', '2026-04-02 15:15:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:14:43'),
(1226, '856ae66bcd2c7435a4e0f88b8c6c24cfd184e82d3bb4fbf8c56675f86798cfc8', 3, '2026-04-02', '2026-04-02 15:16:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:15:14'),
(1227, '5dc97c7d3357a69bc599cd6b38c156b7001b490cb4028dc1cb69c0312a33c833', 3, '2026-04-02', '2026-04-02 15:16:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:15:45'),
(1228, 'ebb85e14d839cc6dffcdc5f676e59baf5eb3d194fb5948f19b712bf356a546c5', 3, '2026-04-02', '2026-04-02 15:17:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:16:18'),
(1229, '66d62ee03ed7fdd51ff3f7c6e51bd1af33c8e63999bba90400110ba7c2451128', 3, '2026-04-02', '2026-04-02 15:17:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:16:50'),
(1230, '2179664ed4379a81f9d0931168f76d405cb669fd480c5aa9e51909195a33a623', 3, '2026-04-02', '2026-04-02 15:18:21', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:17:21'),
(1231, '3beb3da4d1e54d7ab563fff78277c759db626b0b984a529efcabb5ee0b2226a0', 3, '2026-04-02', '2026-04-02 15:18:52', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:17:52'),
(1232, '30612658c0bc2a5d684de2f6c68f2144df7b62929044633a5a118ee707f1787b', 3, '2026-04-02', '2026-04-02 15:19:25', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:18:25'),
(1233, '9e8688b0fe178e232225651610e24bfa75f682ed7e4459401122fd02df8dc2b5', 3, '2026-04-02', '2026-04-02 15:19:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:18:57'),
(1234, 'eb809a7018b60bb2185c746d48dd7a4f800e373ce8677a69f0ec674453a419d6', 3, '2026-04-02', '2026-04-02 15:20:28', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:19:28'),
(1235, '652eef1e17ddfccb72bac6ee1a11e7726ba62ba43b94fc45a30a5515cd51d5e7', 3, '2026-04-02', '2026-04-02 15:20:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:19:59'),
(1236, '08c27b56d313f41a1b11c6aa0f386d886f8b330481ff4d2ab8346cc1f70201d5', 3, '2026-04-02', '2026-04-02 15:21:31', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:20:31'),
(1237, '34f60850c005276796646ede7725b49348bfa44d15dfe818ebfe85042bcbb409', 3, '2026-04-02', '2026-04-02 15:22:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:21:03'),
(1238, 'bd39adca7c5899864e639c71c720f78ef5446368a9e22b1fbec30a75c343d54e', 3, '2026-04-02', '2026-04-02 15:22:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:21:34'),
(1239, '383b5faf368b0523737c54b8d12ebcac5a138dcf02864211a6c200964b116119', 3, '2026-04-02', '2026-04-02 15:23:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:22:01'),
(1240, 'bbc29b60ef456bec0e712329f510948a42e884d606bd3ffac724ac7a56bc2d09', 3, '2026-04-02', '2026-04-02 15:23:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:22:06'),
(1241, '2212d5623b1b98fde37f71fa31ff7f4f104364ee1e3c7a4f616cc2837a7cc469', 3, '2026-04-02', '2026-04-02 15:23:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:22:37'),
(1242, '1b517dc58e1074675cdcc1efac4f744efe7563fad0b2d5630ebcccc497497881', 3, '2026-04-02', '2026-04-02 15:24:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:23:09'),
(1243, '2caa898b29492c1fb0dd7b031f1ae0009aeb1ca7e7590e1203355b1c2c88d168', 3, '2026-04-02', '2026-04-02 15:24:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:23:40'),
(1244, '658247d2dc06d1eeff355e417ef30a9a43d2b658a2693a3d9544128e48134068', 3, '2026-04-02', '2026-04-02 15:25:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:24:12'),
(1245, 'f99f9feb4718966708196baac8218cc1dce207e56d295db0900f94fe3708f8a8', 3, '2026-04-02', '2026-04-02 15:25:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:24:43'),
(1246, '2d390fab8fbd007d89b69fb9d286f2e3c0374afc88061c89468197954e554b37', 3, '2026-04-02', '2026-04-02 15:26:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:25:14'),
(1247, '716ad2343f3f4a6ba4abafc2c2cca01d353b152fb35ca78a3e5c0b027ba5a810', 3, '2026-04-02', '2026-04-02 15:26:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:25:45'),
(1248, '0452e1812db06b58fe66c40248f9c025004f46b6008c9246efa8a15695ef1bb5', 3, '2026-04-02', '2026-04-02 15:27:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:26:17'),
(1249, '4cda8d2162836c6746afa0bdfeb002a449b7c1cc0abd0893658685a73818e415', 3, '2026-04-02', '2026-04-02 15:27:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:26:48'),
(1250, 'ae2aebe039857724d18b9ca1d65aedce6d38161a25477ddc57459cd6c33ebeab', 3, '2026-04-02', '2026-04-02 15:28:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:27:19'),
(1251, 'f6590c76effbe43212a49994392753276e74ae2c61ca9cfe4e1bc0d4541a47e3', 3, '2026-04-02', '2026-04-02 15:28:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:27:50'),
(1252, '71960b9aee70d6c1fa9faee903bba4ec6544725ef205eb756073b0beb31a372f', 3, '2026-04-02', '2026-04-02 15:29:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:28:22'),
(1253, '0adeeee4c2c95eed757177224758c7e4f826a2d52b4cd109120f02da861e52a4', 3, '2026-04-02', '2026-04-02 15:29:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:28:53'),
(1254, 'cafce1c01def4198a731b7e7df52d280252adb72469d52b3d39ecca70e18622c', 3, '2026-04-02', '2026-04-02 15:30:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:29:26'),
(1255, 'a050d9331decd43e773ad93ab4fb04dab65dd60684d1fb8e024eed9803934260', 3, '2026-04-02', '2026-04-02 15:30:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:29:57'),
(1256, 'cb187e8313d6e5eede87fa9b084281f9f50b30bddaae77c99457e98264170338', 3, '2026-04-02', '2026-04-02 15:31:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:30:29'),
(1257, '9b7b067f8f3c70b74b238ef8b6d292c311adc15072986ada280a49d50ddf06f8', 3, '2026-04-02', '2026-04-02 15:32:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:31:00'),
(1258, '95679a78977a383ca02e79b570d9e89c2abd7eacada5765d1b13b1a78c5524e6', 3, '2026-04-02', '2026-04-02 15:32:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:31:32'),
(1259, '4cd7ce950ebb65b69230ef10f2065b4601a0f64295a3296a5d7d4959337187c8', 3, '2026-04-02', '2026-04-02 15:33:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:32:05'),
(1260, 'ac358d0dc29e7728541ba3585cd085ee5705efdd27245cab235feac797de3125', 3, '2026-04-02', '2026-04-02 15:33:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:32:36'),
(1261, '3dc7b9b6e02971177a67f73723bbd7b5b367a9f3765128ec7fc84d93b9b49eb2', 3, '2026-04-02', '2026-04-02 15:34:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:33:09'),
(1262, '6ac1e67adca79ff026284d1002012affa805ad51f95caa3b9746b3048027eb5a', 3, '2026-04-02', '2026-04-02 15:34:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:33:40'),
(1263, 'cccfd8111c8e2c50e004c01bec9533bc7c811617f3410da4f3969e8b3a677b18', 3, '2026-04-02', '2026-04-02 15:35:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:34:11'),
(1264, '3e56b6b5e593a2938db5c9fee7d593831186cdbc3ef3f3b48ef27a1c97c7d9a4', 3, '2026-04-02', '2026-04-02 15:35:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:34:44'),
(1265, 'fc951f55ab86993e5fd54df6d49ef96c8c6b9426fd3dcba76c5e463622bfa4c9', 3, '2026-04-02', '2026-04-02 15:36:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:35:17'),
(1266, '7c779b01b4d98330201294e0f8fdc792841dd6cd496900769a03fa367f12ebf4', 3, '2026-04-02', '2026-04-02 15:36:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:35:51'),
(1267, '4c773d8eb9ac5452b6829641db4c980ad20e12b2f57dbfbbdc8621338e62b571', 3, '2026-04-02', '2026-04-02 15:37:23', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:36:23'),
(1268, 'd69271e31a2edcea2acc14139e6aa66b27146e2273842168e77ef24f77d977d7', 3, '2026-04-02', '2026-04-02 15:37:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:36:55'),
(1269, '745f43f07cc350bcd757e577787f2327fc784225a9b08aaf34a4d5bc9e830ff3', 3, '2026-04-02', '2026-04-02 15:38:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:37:27'),
(1270, 'd94907397e86466a5c5ae7e5647a81d4263ccc995d30915af30e69303667b9f7', 3, '2026-04-02', '2026-04-02 15:38:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:37:59'),
(1271, 'eedacda0043be9b4e79483bed55cba449f0b06f0fa10e4bffb65203bb7b7c20c', 3, '2026-04-02', '2026-04-02 15:39:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:38:32'),
(1272, '080634a60c8cdbf020720592c94fa3fc14241af5cd3ccdafc4c9e2041f6d9245', 3, '2026-04-02', '2026-04-02 15:40:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:39:03'),
(1273, '875b23c87c2a2da3d972149a45ad337d423388eb90be0ec873817c546decb806', 3, '2026-04-02', '2026-04-02 15:40:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:39:38'),
(1274, '63a77da8cd3973ea222d59373c7fc4bc9d7b1a8945febf4b7826c4a8f4d3c369', 3, '2026-04-02', '2026-04-02 15:41:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:40:10'),
(1275, 'a3c3a3f85b99744b525114441c4691616c4a9bee860ca2bc6e0d423858a2c23e', 3, '2026-04-02', '2026-04-02 15:41:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:40:43'),
(1276, '39f7814e1405c4a4fd669a6d139931f8b95bed94b8089e8a64cc432a5782b8dd', 3, '2026-04-02', '2026-04-02 15:42:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:41:14'),
(1277, '7058d35e7231d94c71fbf9e427c2f61dd45a3bed513ccc0bcfb4068442caa737', 3, '2026-04-02', '2026-04-02 15:42:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:41:46'),
(1278, 'd9dbb3ce8926bc12d69535b24926e48982c6df731da6d821782a0aef3a3de1e4', 3, '2026-04-02', '2026-04-02 15:43:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:42:18'),
(1279, 'd2d5ae83b6f3f1969141db2d7f852a0ce7cc9be66e3a07b637dc621fcba79921', 3, '2026-04-02', '2026-04-02 15:43:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:42:49'),
(1280, '3252b513527d9585f21c36abb7e7eacd62827602e4e5d19331a32c88bee75f93', 3, '2026-04-02', '2026-04-02 15:44:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:43:22'),
(1281, 'e379df6f679e508ffb1ecf66bc19a5f7f3927a9e3132f4c0fc7f8cd3d5d0cc85', 3, '2026-04-02', '2026-04-02 15:44:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:43:56'),
(1282, 'daa5ab215f384c417e500484cb4501d78159f2ef2f073e913a130a27865a2836', 3, '2026-04-02', '2026-04-02 15:45:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:44:30'),
(1283, 'ffccd1a35e902994c732ed0eaaf2cc31aa33dec74d81f65e531adf646451018a', 3, '2026-04-02', '2026-04-02 15:46:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:45:01'),
(1284, 'a20b73fd875c224a67089542fc689b201f09574f2edba608aec3d8a9c52d3248', 3, '2026-04-02', '2026-04-02 15:46:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:45:35'),
(1285, '822b6c51a45a4557bd1df1860d632cda2ce389305ed7a276bd394ed30cde22b7', 3, '2026-04-02', '2026-04-02 15:47:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:46:07'),
(1286, 'f470a8034415538b4ebd20dc3824119406eba392ad7eea8d830cabc250f86d55', 3, '2026-04-02', '2026-04-02 15:47:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:46:38'),
(1287, '30a7a875f26a397804b7f93930ecfee4c249044632abeae8bf8573225be4be1f', 3, '2026-04-02', '2026-04-02 15:48:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:47:12'),
(1288, 'ba55e46efe463bde3de5457fba7f21ef60f5daa76635a8b1e942d9a3ba6b03f5', 3, '2026-04-02', '2026-04-02 15:48:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:47:46'),
(1289, '3106110d282d2d7d3785a94b8f537acd35711e1e16766bd0f17ba9918f34ffde', 3, '2026-04-02', '2026-04-02 15:49:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:48:19'),
(1290, '27548eb668e805ae26d59f726bf4c3eebed8b51cd35a015e50632a491303cd22', 3, '2026-04-02', '2026-04-02 15:49:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:48:50'),
(1291, 'a81825a2dbd930bfa8114d1b76aec43e3c417f22d986b3d5e214fdb3c0d71b89', 3, '2026-04-02', '2026-04-02 15:50:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:49:26'),
(1292, 'a6acf3c52f3863937fe0bea42d6a841ad68c6694390f4e6637ebd65bd6f0e010', 3, '2026-04-02', '2026-04-02 15:50:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:49:59'),
(1293, 'a9850f7b61e2ce85c1cf1197b8bc408de63f88578d9882a86b6a08e8dea14489', 3, '2026-04-02', '2026-04-02 15:51:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:50:30'),
(1294, '9789e86858d52b685d0e6b970f67df678c09d781c89cf8b9e3f402ba3cf34018', 3, '2026-04-02', '2026-04-02 15:52:02', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:51:02'),
(1295, 'c31f35ea02771d8b86e8e91bcf1bd0955df9029d14ace00cb71bd94c580b30f5', 3, '2026-04-02', '2026-04-02 15:52:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:51:33'),
(1296, '8aae07f7bbd38dd749a3339140636ad821920ce012679f3bcc876901153de242', 3, '2026-04-02', '2026-04-02 15:53:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:52:09'),
(1297, '08563cfa249fd5e7546112e53d45014f72e813ffb0ccc27733cd929bb7ce7f81', 3, '2026-04-02', '2026-04-02 15:53:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:52:43'),
(1298, 'ff1312b519306b2806390495018df869ebfc58d78fd6f5e63276042ea2f856eb', 3, '2026-04-02', '2026-04-02 15:54:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:53:14'),
(1299, 'eba3fc923f8af90abc72626d4f01b399ae8f8e26b313fa860b27e1485d641dd4', 3, '2026-04-02', '2026-04-02 15:54:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:53:45'),
(1300, '53c4189999217d022feaf378688f56f99b85a4fa04341e25cc4d586babde57be', 3, '2026-04-02', '2026-04-02 15:55:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:54:16'),
(1301, '7187ab6254544a6ffec97c3f867b70672a26d3c81321fc5697ae0199a6c02e00', 3, '2026-04-02', '2026-04-02 15:55:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:54:48'),
(1302, '8f55a1aa260fc04579719afe8cf6341f08f2812fe3da68d9acd46ce098b1540a', 3, '2026-04-02', '2026-04-02 15:56:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:55:19'),
(1303, '30719220428e1f21fd3b9b35933dd8a17eb5f171e94f8834cd31e63f56246de2', 3, '2026-04-02', '2026-04-02 15:56:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:55:50'),
(1304, 'e42cf59519c6270a1639afa291e3a8f387be012efcdccfb527abd8581127cf5c', 3, '2026-04-02', '2026-04-02 15:57:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:56:22'),
(1305, '96b072c91bf88fe00226bda1f77d7d4d58a64280a3c436f2dee77d5e726297a5', 3, '2026-04-02', '2026-04-02 15:57:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:56:53'),
(1306, 'bd5b42535233590881dde2118e5ccecabb8d009c509e4df855a4a0057004c7e6', 3, '2026-04-02', '2026-04-02 15:58:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:57:24'),
(1307, '9b73f94be887c9ab0cee23451bb5bc6e1594add8d035d029713b0ebc8c94eeb4', 3, '2026-04-02', '2026-04-02 15:58:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:57:56'),
(1308, '4c712175ff0fa45fb3f50c5dfd20af633748bc99f75d8ef8ccfb001363769282', 3, '2026-04-02', '2026-04-02 15:59:28', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:58:28'),
(1309, '445262d57243958208a426d4d9c27462ae34167f7c89cd6fd0a13c277d6a169e', 3, '2026-04-02', '2026-04-02 15:59:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:58:59'),
(1310, '9911a5bb8bdadbc8ebcaecf1ee3f4968783434b9a516ecd54abbdc038801dd57', 3, '2026-04-02', '2026-04-02 16:00:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 07:59:30'),
(1311, '174aba1ea4eee59b3b10fa6ff853c4c18695e6550341f004a8d56a38cc5be1ff', 3, '2026-04-02', '2026-04-02 16:01:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:00:01'),
(1312, '40f9e562c123f6349794cd1e252bf590adbe4edf65d67554be732e29de9fb483', 3, '2026-04-02', '2026-04-02 16:01:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:00:32'),
(1313, '7d6fae3aa4f767b187feefdcaed7a4d7911edcda9107a8e663377486a344bccd', 3, '2026-04-02', '2026-04-02 16:02:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:01:03'),
(1314, '40822e4f075fc85df0b1a3683fc18f0806d6cb76d67ec2e79fbd598e655319d3', 3, '2026-04-02', '2026-04-02 16:02:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:01:35'),
(1315, '73b9f994e70f564d65866d44a71e0e79cadac87aff0e30253946a4ed21a095bb', 3, '2026-04-02', '2026-04-02 16:03:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:02:06'),
(1316, 'bccb0096f037a970a9810c2974711143bac217c1fd85f4c1fbfae285e870958e', 3, '2026-04-02', '2026-04-02 16:03:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:02:37'),
(1317, '04d82febe3a5595f878d7218b7091b0934ad3d87dd61a83b532360b4477b430c', 3, '2026-04-02', '2026-04-02 16:04:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:03:09'),
(1318, 'e3d3cdcebba32f5ae68ef078f1de5771eab2f934c0621c7e3e37178fcb2b7f67', 3, '2026-04-02', '2026-04-02 16:04:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:03:40'),
(1319, 'e674da1204b168e6312bb68e6a4ce0734eabf267800d07b12a070c5461e2f58d', 3, '2026-04-02', '2026-04-02 16:05:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:04:11'),
(1320, 'dfd308b0496d0e7c56c06e33a50516a747eb2d06a4039205e35d62a010b1b3d9', 3, '2026-04-02', '2026-04-02 16:05:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:04:42'),
(1321, '15801a2030aea6ceae798c689522ddd895b61e85c318f68d97cc05b31e9d60fd', 3, '2026-04-02', '2026-04-02 16:06:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:05:13'),
(1322, 'f63501ed6802292316aaf5f420e13747ff65f105475e9ad64d3ea3b8bf96c290', 3, '2026-04-02', '2026-04-02 16:06:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:05:44'),
(1323, '39b842660dac5029452b63298f4890bfa4dcc223d178fe459ca2b659d9f3a97b', 3, '2026-04-02', '2026-04-02 16:07:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:06:15'),
(1324, 'af6b4338dab7e1837a83c911fa8dd9fa2dbc9e302929c8516252612ef43b499c', 3, '2026-04-02', '2026-04-02 16:07:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:06:46'),
(1325, '0525f4e21237c564ab25d1c4f14f2ed8554069c3df0face05b4d01d61aa7bb50', 3, '2026-04-02', '2026-04-02 16:08:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:07:17'),
(1326, 'e6c0a54ff61dc1f227225c8af782077776d3752d5931e2f1daff4ff74393768f', 3, '2026-04-02', '2026-04-02 16:08:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:07:48'),
(1327, 'dc1ad37bcb6638fa94b473b21eef45c22ac70dd08b7b8205f9ac24ea63a313bc', 3, '2026-04-02', '2026-04-02 16:09:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:08:19'),
(1328, '5d7fa40cef9d4d7c92916cdca31986feceb0a79534feb679bfeb7cc0cd773fc3', 3, '2026-04-02', '2026-04-02 16:09:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:08:50'),
(1329, '711c7dfaabd183f18ba9e2d88f39a891c3831703a31f838bea4010b17755c37e', 3, '2026-04-02', '2026-04-02 16:10:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:09:22'),
(1330, 'cc22f74101dd13db2ac03816d93cb937aeaf5c0860ec25968c889c8e32902cc2', 3, '2026-04-02', '2026-04-02 16:10:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:09:53'),
(1331, '2ef6bc93d3a72d173a57739656aa70ee47e84e1a885b6ed362716ddd15e0f18e', 3, '2026-04-02', '2026-04-02 16:11:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:10:24'),
(1332, 'd415f1355163cca3ac535b7e358e098d6565667a6950bf7ec2f9b35e41f8237f', 3, '2026-04-02', '2026-04-02 16:11:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:10:55'),
(1333, '929230af60b2dbd2b100b080500458eb453060029a14388a14b968468b164ff4', 3, '2026-04-02', '2026-04-02 16:12:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:11:26'),
(1334, 'ec4622ac1e32c17ada799b2793b05757869d26d16788e5074d3f6a79a780b29b', 3, '2026-04-02', '2026-04-02 16:12:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:11:57'),
(1335, 'a44d33691fb5935e8ce244a4dae379cc2ba646556cbac43af8afe85f58441c0c', 3, '2026-04-02', '2026-04-02 16:13:28', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:12:28'),
(1336, '1e15d1ecc6405e7306a975941ff0e0af8738c9fdf9c5ebe270db915b6b09f2ef', 3, '2026-04-02', '2026-04-02 16:13:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:12:59'),
(1337, '7cbe10136eb5b4692b5f2a16cdf50a9850b3549f42acf37e988c3ae6501b8944', 3, '2026-04-02', '2026-04-02 16:14:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:13:30'),
(1338, 'cfe43b3ec9f4c1284b0e70d27a4575e72f3477f352c49116fe9240e399cb18ca', 3, '2026-04-02', '2026-04-02 16:15:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:14:03'),
(1339, '102dc8b21dbca20b7244f5b794bf9ddc20167fcade8a1a438c50007471d36b04', 3, '2026-04-02', '2026-04-02 16:15:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:14:34'),
(1340, 'c67c5a3acf1cf51f796d822ea2f63af9fcb0176dac92962b8282efe80a8e1abe', 3, '2026-04-02', '2026-04-02 16:16:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:15:05'),
(1341, '579b8a051e50a2dab5417f09b5cf613ff9ebaa57dd4f240d1e9580a858b6bc72', 3, '2026-04-02', '2026-04-02 16:16:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:15:36'),
(1342, '0ffd5e03c1c57cf1f99894ea106ece847ce42708b3679e14a8971de2d476d871', 3, '2026-04-02', '2026-04-02 16:17:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:16:07'),
(1343, 'd86d80f268dd77b1e6ee588c96e8c139e548e800306fc94a0dab62927d71536c', 3, '2026-04-02', '2026-04-02 16:17:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:16:38'),
(1344, '65118896a2c56342dded32faf8ac9ebfe82bfbf8220d95aee94995e7df549964', 3, '2026-04-02', '2026-04-02 16:18:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:17:09'),
(1345, 'acc99a9f31b0fdf2c3bc50e795ecffe04bd124663f7f2d29c88cd2a2342c1a46', 3, '2026-04-02', '2026-04-02 16:18:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:17:40'),
(1346, '23b226d7e51604dafc199a9b4ccfd2cb49bf1cc41995256de70901141b728bb3', 3, '2026-04-02', '2026-04-02 16:19:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:18:11'),
(1347, 'c25853502a75b6df11d22de196f41711fced51f6cf72795a8ce9a9ccb5947f95', 3, '2026-04-02', '2026-04-02 16:19:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:18:42'),
(1348, '38d2ef47f5aea4ddb82be7b426284abb6f0bbdd26a569fadb2793529873ff495', 3, '2026-04-02', '2026-04-02 16:20:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:19:13'),
(1349, '8d5f40bdb1ca85266f45c4608e545d5a26a09f18c4349f3040ed8c51f607be70', 3, '2026-04-02', '2026-04-02 16:20:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:19:44'),
(1350, 'eafdc47af200feadcc19d5024d201c3c54d7b113ded21f734d8bbc805ff2f7ed', 3, '2026-04-02', '2026-04-02 16:21:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:20:15'),
(1351, 'd184d375708a239009386ce90695d1070ff6e41f47a1f8c0c72c57baaeef5fd5', 3, '2026-04-02', '2026-04-02 16:21:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:20:46'),
(1352, 'ebe544d593ac800ded28cd6dcefebb80ea1ac43df1e726ffee74365c8bf5748e', 3, '2026-04-02', '2026-04-02 16:22:31', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:21:31'),
(1353, 'de58efc5dc2d9b4b5af7f3cf8ace78e349c260b2241dba249825df2d85daf1ce', 3, '2026-04-02', '2026-04-02 16:23:02', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:22:02'),
(1354, 'eb65875c025a8b5f069dea1a7edfdf716e380e3ac1ac0c5eb871dbb54a62d609', 3, '2026-04-02', '2026-04-02 16:23:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:22:33'),
(1355, '6b0cf76f9b837ee81775c41af088706db3c627a25412505438bbd1a10cc6fbf8', 3, '2026-04-02', '2026-04-02 16:24:04', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:23:04'),
(1356, '2285591c6ccb742d6843b873ecc826d352a11c6b67b63247e5d82dc40a343652', 3, '2026-04-02', '2026-04-02 16:24:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:23:35'),
(1357, '0d271c1faaf93cc1398ef866a36cdf677c8c9aa132e5d0435e41e222daaecb71', 3, '2026-04-02', '2026-04-02 16:25:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:24:06'),
(1358, 'ba773d5861796e78cebe7e719a962e515927bcb8c25eb29c3e7d9855fa34a1c0', 3, '2026-04-02', '2026-04-02 16:25:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:24:38'),
(1359, '132d69dab8cbcfd1e1649ab1fca3ca7f61ce67e64f7f99312a971487dd01aade', 3, '2026-04-02', '2026-04-02 16:26:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:25:09'),
(1360, '331b9e2c832eaa107393142f225ed477ca3332e35580555135ab8ea212838fa9', 3, '2026-04-02', '2026-04-02 16:26:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:25:40'),
(1361, 'f0b56cfb9c81b6a73208f9e781000a172f3d0fe10deb6edf216bc4ef26c23863', 3, '2026-04-02', '2026-04-02 16:27:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:26:11'),
(1362, '95337f8c8be97ec622f781491426ac11c3e0ad3e8974414ec73ea8d727447de4', 3, '2026-04-02', '2026-04-02 16:27:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:26:43'),
(1363, 'ddbebae2a52a7f4153ac816afd8408df354d3dac077629486a0f670335d13883', 3, '2026-04-02', '2026-04-02 16:28:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:27:14'),
(1364, '23c1b7357f3e45af38b30f37119f1482a89677a18d0f9750cd6c181c27f56f7f', 3, '2026-04-02', '2026-04-02 16:28:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:27:45'),
(1365, 'beaef160fef41ed85658e4d2d820a8052c8e3520676ea4386aa34d3b69950fe5', 3, '2026-04-02', '2026-04-02 16:29:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:28:18'),
(1366, 'cc2b5f14b225193aa76afd7d86eee1d27c4503d0a95f8aaa47ba7fcb87944fa7', 3, '2026-04-02', '2026-04-02 16:29:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:28:50'),
(1367, '967fc9c2baa1167febcc0cee5a2f200135d65401214f5742731cc1c8ddca831a', 3, '2026-04-02', '2026-04-02 16:30:21', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:29:21'),
(1368, '7baea2920fa563eb04bb071f4438267a73778ce966ba6a8bfa2ca49a8f789712', 3, '2026-04-02', '2026-04-02 16:30:52', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:29:52'),
(1369, '70a97cee5a651b886b6f1373c6ab9216cf5221710957d61ad871292f56b4848c', 3, '2026-04-02', '2026-04-02 16:31:23', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:30:23'),
(1370, 'cda2bb54199a93c0682597bb32a6ba74c7ca99e93b5786e531c9ebd4a2129f6e', 3, '2026-04-02', '2026-04-02 16:31:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:30:56'),
(1371, '56b7be3d249b87ecd5320ac4a3f803dbd4973cc7a0372fa0226d9d3280434bfd', 3, '2026-04-02', '2026-04-02 16:32:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:31:29'),
(1372, '72069eb9e61d2063de8a2139bbf7f74cefb3261e9b7cb53d0b493ab35d7add22', 3, '2026-04-02', '2026-04-02 16:33:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:32:01'),
(1373, 'a17b4aa9bf9cefda2428aa7fe2815fd5de6e2ff191c3562d5205ca40f924cac2', 3, '2026-04-02', '2026-04-02 16:33:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:32:32'),
(1374, '1f5a99a1e28e1fdb4a86dd4a64f3fd849655f64a5d2954e32e257faeeb3b5f2c', 3, '2026-04-02', '2026-04-02 16:34:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:33:05'),
(1375, '9ac72564f8de39ec6877f059997248784ff792b67b00963b91e84564a035f1e0', 3, '2026-04-02', '2026-04-02 16:34:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:33:36'),
(1376, '19f3f66d1511acd0db3838249f413f399b4808b7299b9a0718ae427848118d09', 3, '2026-04-02', '2026-04-02 16:35:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:34:08'),
(1377, '67248efb42961eb5062448c338f7744e1777bcdbad66882f042472931d817325', 3, '2026-04-02', '2026-04-02 16:35:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:34:39'),
(1378, '555c73a52cb90dda53355f29bda9112f33d5622e8085ce7c3d01523b9f713ffb', 3, '2026-04-02', '2026-04-02 16:36:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:35:10'),
(1379, 'acb7e2b59a92e5a4c13556d9a0816a901780d282eb0d783f8e267eafe4e5c890', 3, '2026-04-02', '2026-04-02 16:36:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:35:41'),
(1380, '47db49b2b14d2de4873d16b3d5d19f0a2378261fea2ae3c294993c804c4cf0a8', 3, '2026-04-02', '2026-04-02 16:37:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:36:13'),
(1381, '9aad8dc47a78a290fe3e8b66bcd70864b7f6b2da0527ee3a87ba90d960be9476', 3, '2026-04-02', '2026-04-02 16:37:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:36:46'),
(1382, '7f5d4ebcc6386b1bdf25cb13435b2b04684d076456e4c64a1931fd468528059c', 3, '2026-04-02', '2026-04-02 16:38:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:37:17'),
(1383, '906f008d53c0c366734f582094e86567966454dc38bfceb50f575e96a2e07322', 3, '2026-04-02', '2026-04-02 16:38:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:37:50'),
(1384, 'a277b739ed671263387a4d9ef87f57464bca1143b630c9934fb6c7cc83336dbd', 3, '2026-04-02', '2026-04-02 16:39:21', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:38:21'),
(1385, 'dcd0d24969be0b90f10f1669e59667d0105b9f52b5da53c570cb2fa2776f893f', 3, '2026-04-02', '2026-04-02 16:39:52', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:38:52'),
(1386, '77d8babcf526f848f836defe61aa6c1c98a0d25ba9c203bede2c468d12a685a6', 3, '2026-04-02', '2026-04-02 16:40:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:39:26'),
(1387, 'a05f811afbc69d8136b38c7a664881f67accab1adae5c5873caba6f31ee7847f', 3, '2026-04-02', '2026-04-02 16:40:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:39:57'),
(1388, '9d3f00b777e1a0a7e9b418a6ccd9aa13f1334e8e6ace9b01c20ab726168efed9', 3, '2026-04-02', '2026-04-02 16:41:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:40:29'),
(1389, '066a2ecc61c8be7911bf61ed6e74f7e38b0095edc5730c8969a1122155257c31', 3, '2026-04-02', '2026-04-02 16:42:02', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:41:02'),
(1390, '2d6f76ea63c65b8a071a10b50f5aba4b4a55fe959833730e44b4506901c168c6', 3, '2026-04-02', '2026-04-02 16:42:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:41:35'),
(1391, 'a32120f3bc95185336ebea5d9304dc72f08adde6ff7c68ed007d5201a8a97ae1', 3, '2026-04-02', '2026-04-02 16:43:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:42:08'),
(1392, 'aeb8d0da6c8e20c96f8190cf857ac85bbb446ce83d6d4d9d16dd3ac353c6e193', 3, '2026-04-02', '2026-04-02 16:43:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:42:39'),
(1393, '0091cffa3a84ce664cfba0b05db20d9e8423f0a60e9d2866af811c6c99ee1be7', 3, '2026-04-02', '2026-04-02 16:44:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:43:11'),
(1394, '3fd6cba8ffd8608cfc6f2092271b0cc665c299971637cdd75e03cda8d67927f6', 3, '2026-04-02', '2026-04-02 16:44:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:43:42'),
(1395, '85ebd2653f1648508c76fa9b5c45985a25ce87be214edc7529e86ac54ab6f2bb', 3, '2026-04-02', '2026-04-02 16:45:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:44:13'),
(1396, '115269ef698b19801b11862d2f6ed84eb9d46585a362fc363a45e3fe3e83eec4', 3, '2026-04-02', '2026-04-02 16:45:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:44:44'),
(1397, '4ce9a77b3abe720affc1e2b993e2bb551856c032bf1a70ab8eabb163491aaff6', 3, '2026-04-02', '2026-04-02 16:46:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:45:15'),
(1398, '830fe9fcb28346e7b454091d371f26c68e54cedf025c69959fecc0120acede30', 3, '2026-04-02', '2026-04-02 16:46:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:45:46'),
(1399, '53557fdb8e5982e252a47bfd93b94bbdc4bc5716a7c388e62c945bc1436c2ff8', 3, '2026-04-02', '2026-04-02 16:47:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:46:17'),
(1400, 'e45bd91a3f3320473f1c216873ad1a996f55a89e63895f91c7bd958961701a96', 3, '2026-04-02', '2026-04-02 16:48:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:47:08'),
(1401, '3cf761db5c5c9d9b141da51c402f52b4689e1bb402cf9da18f0740726a7092cf', 3, '2026-04-02', '2026-04-02 16:48:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:47:39'),
(1402, 'b3d68a07b07cdab4444aa7f3b42ce086ff91d7feeb544e964686c9a32e6a0e6c', 3, '2026-04-02', '2026-04-02 16:49:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:48:11'),
(1403, '0174d6a390ce96bf2376270d5a07403f2a98245a2f79627bf4474a544b39b39f', 3, '2026-04-02', '2026-04-02 16:49:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:48:42'),
(1404, '602a52e5a9f0810b423c16dbc4f4da2016fa017df3514d68085e9b8bfa58e2e5', 3, '2026-04-02', '2026-04-02 16:50:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:49:14'),
(1405, '83d8eaa6a414470cd2fce66f50055c20117228782ba7a2bcf84dd8be6b06052b', 3, '2026-04-02', '2026-04-02 16:50:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:49:45'),
(1406, '2258553914d93c2c04ead297c2c29b3ee1559e59b1413cc992e87e9982262248', 3, '2026-04-02', '2026-04-02 16:51:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:50:16'),
(1407, '75a3d14883c71bf32be433e627f6f55829b874e5e5cf15f5c1427ca6894731d0', 3, '2026-04-02', '2026-04-02 16:51:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:50:47'),
(1408, '0117d68b01127d8db1ad3b70d239695bc1a711ff3a4a4dac62334cfc9e8d0343', 3, '2026-04-02', '2026-04-02 16:52:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:51:19'),
(1409, '66066e48ff3c8b72b1ee0745d4f11762031c2b8285f947f37b8bfbdf37c4ca34', 3, '2026-04-02', '2026-04-02 16:52:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:51:53'),
(1410, '346fbed2906566c9ad4f90d60147fc7abbd66864c52ff2d4ddddcf320929e52e', 3, '2026-04-02', '2026-04-02 16:53:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:52:24'),
(1411, '2f8bd9edddb13a1b24af068d8ff0ee4a8b71882f701a109d95a6907c67e41b00', 3, '2026-04-02', '2026-04-02 16:53:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:52:55'),
(1412, 'a62f6f48f3682e07a8dcf2d573630f1d6fc81bbba074a1f9aaff87ebdfc109fb', 3, '2026-04-02', '2026-04-02 16:54:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:53:27'),
(1413, 'cf73e877f1d710df9789adafaaeec8afc0d6ef1dc6adc14dca95e6f03ae90894', 3, '2026-04-02', '2026-04-02 16:55:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:54:01'),
(1414, '60ba9493d08acddbb692a4b04a77ccec46789337d0b353ffce75c49f54767934', 3, '2026-04-02', '2026-04-02 16:55:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:54:35'),
(1415, '994ce4d2c5528b02eea396c028d5234bc46ab01b5ce9a287b49b6eb2e9069404', 3, '2026-04-02', '2026-04-02 16:56:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:55:06'),
(1416, '432288c51e906cc50aff59edf8feb0c2c53d299d723afead856541f3b0357b49', 3, '2026-04-02', '2026-04-02 16:56:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:55:37'),
(1417, '93644ed0ccdb8c94012ce78621a969975b8695d76a7afd3dc72bb89ac3a91771', 3, '2026-04-02', '2026-04-02 16:57:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:56:09'),
(1418, 'e1f6079befb00d07190e78a26f708c18e3f832ba75fb52ece7f0ca45415f47ae', 3, '2026-04-02', '2026-04-02 16:57:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:56:53'),
(1419, 'c685e184bdcf00e3e9c6df86a1be26267eb3ab17d4d61d56d9a223d4500bc34a', 3, '2026-04-02', '2026-04-02 16:58:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:57:27'),
(1420, '1acb8df1d6f01419f4840ad02f8a0c3b1adb195973f879db23b7e717fece0dff', 3, '2026-04-02', '2026-04-02 16:58:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:57:59'),
(1421, '1cea1e33ec2aa7a00fe1d82905927953c8d684e9ce0c65cb56d00df9215dbb81', 3, '2026-04-02', '2026-04-02 16:59:31', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:58:31'),
(1422, '4230c80f74b75526c2774ff66509221f1071a1086e0fcd5182b66e52d19a6608', 3, '2026-04-02', '2026-04-02 17:00:02', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:59:02'),
(1423, '87ccf31a8d24923c35cddb4e9d182a0577cfa40d30221968783ac2be0802b7a9', 3, '2026-04-02', '2026-04-02 17:00:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 08:59:33'),
(1424, '632b4f6c6ec3926eefba37152659e95baab6734738578bdbc3e41f2aaa8e986b', 3, '2026-04-02', '2026-04-02 17:01:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:00:06'),
(1425, '9354660f5c7f974d103bbd5644953b2cfeb4ea4e46e893f1f36e9511e697a30b', 3, '2026-04-02', '2026-04-02 17:01:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:00:37'),
(1426, '48d026857e92f52ac4e5193924fe10cbaf911c7fc41a3d8a81223f0023dd5cd5', 3, '2026-04-02', '2026-04-02 17:02:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:01:09'),
(1427, '2c8b4bed921306325fe5440e284837ce6ad8ffb67c81d11ba89684925b4f9a47', 3, '2026-04-02', '2026-04-02 17:02:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:01:42'),
(1428, 'd97cceb952d5fc2178b6f0940588894e3d684d22461fa9798e9659f790bf9866', 3, '2026-04-02', '2026-04-02 17:03:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:02:13'),
(1429, '5f97aab9f7ce371ea280e1d051f1fdc44fb6283fe55c68c0d6a0ca1218a231d2', 3, '2026-04-02', '2026-04-02 17:03:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:02:44'),
(1430, '7e1a00a6c5215e63d327a6ac89a048523371b9b319f1f38f1f583a710147a3f3', 3, '2026-04-02', '2026-04-02 17:04:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:03:15'),
(1431, '164c033e137a9156e5a11300af59c9bd9dd684fb2ca142b17b17e3197700da7f', 3, '2026-04-02', '2026-04-02 17:04:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:03:47'),
(1432, 'b7dcae583ef331c8540e8bb8c54431e5e9b9850d04f923e88e93390d95800bf7', 3, '2026-04-02', '2026-04-02 17:05:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:04:18'),
(1433, '9d8ef406ec8c7f3f136197f535471556358a66a2a74eacf99bfe6a19586a4c53', 3, '2026-04-02', '2026-04-02 17:05:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:04:50'),
(1434, 'c2c553cdc74a1d82c9250337ac6279a8e87a8020c986db6a8d5f1ab4d43b897c', 3, '2026-04-02', '2026-04-02 17:06:21', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:05:21'),
(1435, '61c920d9614b6d143174b024c88be74615fa3dd535d200edd8b5a77e4e8e06c1', 3, '2026-04-02', '2026-04-02 17:06:52', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:05:52'),
(1436, '2530b694c59667ab2f5b6bb5900f6fa850355f7ca1620b88b3f859958dd8bfe3', 3, '2026-04-02', '2026-04-02 17:07:23', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:06:23'),
(1437, '2d009031f87d5d81879250c8f828bf4a432295afa02b9c46499e0ad3506fc8ff', 3, '2026-04-02', '2026-04-02 17:07:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:06:55'),
(1438, 'dbc5cd68dadf8881eee28bdbb9dab5eb522cc95e9dff3bba03c690a67ba793a9', 3, '2026-04-02', '2026-04-02 17:08:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:07:26'),
(1439, 'c1a3e018149cb68a01a06c3e01954aadbf3c87fe8844c02875d94441861ea705', 3, '2026-04-02', '2026-04-02 17:08:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:07:57'),
(1440, '9fcdef7fbdb536d6eae7300bc0a305890b55efefdf037abccf9685803df749d0', 3, '2026-04-02', '2026-04-02 17:09:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:08:30'),
(1441, '9717621cf813f1342c1e308ffcd1ed4ffb3bafcabeb5c2d7f74b35d3386f0eb7', 3, '2026-04-02', '2026-04-02 17:10:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:09:01'),
(1442, '626ba6bd937f5b9c7edfd416a19ad69d4a85b8d2bfdeaf2734b8da3aae43b542', 3, '2026-04-02', '2026-04-02 17:10:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:09:33'),
(1443, 'db0a204d78013a8216ed591686a3e2c43c8458314edfc1f4e1ca75a05b4e802a', 3, '2026-04-02', '2026-04-02 17:11:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:10:05'),
(1444, '123e111339de2f8d6a699193684ebf2e5c0adcbcb325b6c5867070aab88e3709', 3, '2026-04-02', '2026-04-02 17:11:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:10:37'),
(1445, 'af422532c00010fcc8cc8fd05f52423a71e0cb07cf544a709500286fd1859114', 3, '2026-04-02', '2026-04-02 17:12:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:11:09'),
(1446, '38755bbc89c26d1f3d3aa21893a2a377960ce551f1d70cfab296ad68c28d9d42', 3, '2026-04-02', '2026-04-02 17:12:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:11:42'),
(1447, '6f743d78cbe11e9be6af4ea9a8c4cd8b7de843472d2c8941eec63effa0a12c07', 3, '2026-04-02', '2026-04-02 17:13:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:12:13'),
(1448, '90c34c8b87ca680b2486ac8e1f95f6b32eb87bf6c2764214c9a57e3735e98a3b', 3, '2026-04-02', '2026-04-02 17:13:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:12:46'),
(1449, 'f41c3d13fb88937d7b4441b9f7c9b0a223ee24f12e17c3501c3963412e198166', 3, '2026-04-02', '2026-04-02 17:14:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:13:18'),
(1450, 'a225aa3a338b2683f93ce59e0b528977ee398fdd980894440e331fda9ba62927', 3, '2026-04-02', '2026-04-02 17:14:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:13:50'),
(1451, 'd6c40e7f20d0c32461ea9e735941e9b356e5529026e1b70497aee674cf2f8503', 3, '2026-04-02', '2026-04-02 17:15:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:14:24'),
(1452, 'a5562ceff0b9d4894645b939a638bdab1794ef471a1ebecfd6b393291d61faca', 3, '2026-04-02', '2026-04-02 17:15:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:14:56'),
(1453, '3074abc91a2b5b5039c6811d75060e8a4e3d86ebf04efc9aafa72d2f197c2007', 3, '2026-04-02', '2026-04-02 17:16:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:15:29'),
(1454, 'aac706f31bb5f348086c94880aea83850c22e8a0e1a7e66c2ffdf339b1d1e56b', 3, '2026-04-02', '2026-04-02 17:17:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:16:00'),
(1455, '5423da0f4fd621472afe1383094420751e7cbe5aa78a18816deef1b39c9ea5bf', 3, '2026-04-02', '2026-04-02 17:17:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:16:35'),
(1456, 'ac47028d7949628e77145bec47f86369be1343adc53187b489b79ac3ee154596', 3, '2026-04-02', '2026-04-02 17:18:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:17:07'),
(1457, 'dea3c83e06c48c88e8eeb7f31c0a3ec1be8f5ce4218a4f982b3eb367367cb59c', 3, '2026-04-02', '2026-04-02 17:18:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:17:38'),
(1458, 'bfe0238420fd3d49629741cdc8137605cb3abcd2b6eedc46b4c2545d9c993822', 3, '2026-04-02', '2026-04-02 17:19:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:18:10'),
(1459, '1d873ca418f43d07593e0c6fc40563f8ede6bc6855e498a0bc6d4074bb25344b', 3, '2026-04-02', '2026-04-02 17:19:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:18:42'),
(1460, '7d42fdf3deb2bdc43f0e65ee29e359ac4b57697589cf61cc7eba8a34f8eec719', 3, '2026-04-02', '2026-04-02 17:20:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:19:14'),
(1461, '2881da9c9449468ed4e6401d3ba91d6f2f029d1514cd7f593f0f94dd28a50d19', 3, '2026-04-02', '2026-04-02 17:20:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:19:45'),
(1462, '87538d01a621085baa1c8d8d61a35d968e872ea7bde9713ef1078446b2052882', 3, '2026-04-02', '2026-04-02 17:21:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:20:16'),
(1463, '2ab77b368c6fd687aa2e228dd2c53d1c28fc6137f98bb076192452c319f04bb5', 3, '2026-04-02', '2026-04-02 17:21:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:20:47'),
(1464, 'f8cb6e33e75c62ebd09bd0d63e7e286c85f884e1cdf2aff46edc28ac61339219', 3, '2026-04-02', '2026-04-02 17:22:20', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:21:20'),
(1465, '3806321566f96728bf7f042e02c46e7b5f9129dcfbeb288e3e1c3fbbfaae1fa1', 3, '2026-04-02', '2026-04-02 17:22:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:21:51'),
(1466, '20013ef6f6af4aaeaa929829062f5fd25bd6d4b5d2605828390fb4189c24dd3e', 3, '2026-04-02', '2026-04-02 17:23:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:22:24'),
(1467, '605018b94b1d857470c491956e231a6251b560750d8a927784b84e87ee51a6fd', 3, '2026-04-02', '2026-04-02 17:23:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:22:56'),
(1468, '886909dde6933080e2bb46ae49005f35b37b6fd5b7ad191ed4ffbd1a461a7e05', 3, '2026-04-02', '2026-04-02 17:24:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:23:30'),
(1469, '28c2cdb2bb265f3e23aaaef7bdc8233a5a55b3ae90a8d0a3d1db403723ca3a5f', 3, '2026-04-02', '2026-04-02 17:25:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:24:17'),
(1470, 'c11cb434cc3f98d1935aeb30415f240c0bb560b35ef4b9eef10b4f92615082af', 3, '2026-04-02', '2026-04-02 17:25:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:24:48'),
(1471, '5be049fb3d03a3452a02b2a1028243a4cf8e7b744f2d832c40353c6eadf19a24', 3, '2026-04-02', '2026-04-02 17:26:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:25:19'),
(1472, 'c2511988eeb5404a218b8a0e0433a5bcf37e64c8f7ad3a6788c9c48a750889ff', 3, '2026-04-02', '2026-04-02 17:26:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:25:53'),
(1473, 'b8a439b41fcd5f19ab803a59132ae202fbb4ef9c532c859b9c071edc8e7d4a12', 3, '2026-04-02', '2026-04-02 17:27:25', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:26:25'),
(1474, 'dda99b84b494d4d419c7780b0c98e1fc9172a876a249bca0d412776c51f89a5d', 3, '2026-04-02', '2026-04-02 17:27:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:26:56'),
(1475, '5d88c5cf354f71ada5c9fabcd6948563120605ca588ea8cb7de6cda8dc8175c7', 3, '2026-04-02', '2026-04-02 17:28:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:27:27'),
(1476, 'c03b37a540e73b0a0e32545311397153306a354844c5f2bc204d95210539d540', 3, '2026-04-02', '2026-04-02 17:28:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:27:58'),
(1477, 'df88ecf6ead2bfc751859d882b3488f9ff470a6aab5d579b0bd9c0951c5885ab', 3, '2026-04-02', '2026-04-02 17:29:31', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:28:31'),
(1478, 'e1a0a61df9d049273158f15b462e3489e54eb558f5c3c8eed81a4ad754ed24e1', 3, '2026-04-02', '2026-04-02 17:30:02', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:29:02'),
(1479, '4852690d2d0b144137146b05c5c8388ecb24ebaff10cf0251fc28abfeb4dcc3d', 3, '2026-04-02', '2026-04-02 17:30:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:29:33'),
(1480, '34c884e7273d00a7021ec8fb4f07ebb7576a4b92cf91c6579b0b9958b49ba384', 3, '2026-04-02', '2026-04-02 17:31:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:30:05'),
(1481, '1f1460116e4cdb398c7cc7f31b86fef343a06a517e06fc3ba415fad9cd83b47f', 3, '2026-04-02', '2026-04-02 17:31:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:30:36');
INSERT INTO `ta_attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(1482, 'ad059c3e59d48119bbe78221a881d08ab70cf698c863b773b8271a7a23435248', 3, '2026-04-02', '2026-04-02 17:32:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:31:07'),
(1483, '2a9605dd70364a401fee6e962f949b38d546b1b3b71e133d64bb844cd1aa346f', 3, '2026-04-02', '2026-04-02 17:32:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:31:38'),
(1484, 'd1821e536a3afddfedafe75032bec3ac9c34fcb80406c57d1b6bef8f4c4f62e6', 3, '2026-04-02', '2026-04-02 17:33:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:32:09'),
(1485, '0a794e5b305248cffd0cfd434d42469e9c6ccc87c0ec3ee3d305c737b5c395aa', 3, '2026-04-02', '2026-04-02 17:33:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:32:40'),
(1486, '66f10e955025cffa999d91152ed287fa98368dfd7af03847116eca7f33cddd45', 3, '2026-04-02', '2026-04-02 17:34:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:33:11'),
(1487, 'f344bbc84ef6bcea9308a3739321f088a2702ffb97dba9d016cd854596047ca1', 3, '2026-04-02', '2026-04-02 17:34:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:33:42'),
(1488, 'c025087242975aaaff6dab92ed4ceb1791b604c33c71612fa27ce7c86f0ad40a', 3, '2026-04-02', '2026-04-02 17:35:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:34:15'),
(1489, '2dce628d68f47840c3db11de59518baa65f14bd5ca826a4c278efa76fe9747ad', 3, '2026-04-02', '2026-04-02 17:35:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:34:47'),
(1490, 'dbcd7742d788c1bb37d871affc10f3381fa39869f6168f4711b54f4856c9cc76', 3, '2026-04-02', '2026-04-02 17:36:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:35:18'),
(1491, '596b836a29470324bdb8b489b5e971f8b2b485990652cf0602f1fc7b05286b16', 3, '2026-04-02', '2026-04-02 17:36:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:35:49'),
(1492, '4f006d5ed826800ccc279fd31de41d602ae1d09dbb2764e2621ca6acb8a00fa0', 3, '2026-04-02', '2026-04-02 17:37:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:36:33'),
(1493, '07503e6d84c89aa6d19e810874b41adf6393f942362bb34bc70c5787cc84ad32', 3, '2026-04-02', '2026-04-02 17:38:04', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:37:04'),
(1494, 'ddd6c6851f598b0d92f759664dfaf68d53bc101bd02529e1b58ba6a38e4a4fdf', 3, '2026-04-02', '2026-04-02 17:38:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:37:35'),
(1495, '9236bece74258533eec36e0325c85b1bdf7ec7df35a39790cac7079f686c5e22', 3, '2026-04-02', '2026-04-02 17:39:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:38:06'),
(1496, '9d3f8b0a6269da86b3bee4e53188b8aae3d750c1d783ca4e3fc6a4a1fbe8fbd8', 3, '2026-04-02', '2026-04-02 17:39:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:38:37'),
(1497, '70c15148a9f5c6ab1e74b2e1ae4a143bab3586ffba8543633194c191e278bd32', 3, '2026-04-02', '2026-04-02 17:40:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:39:08'),
(1498, 'ec1e78b1847cf4e33ac2a6c6a19450b0b98e23894fdefde019cc74e3ee29ce4d', 3, '2026-04-02', '2026-04-02 17:40:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:39:39'),
(1499, '8916ea1f6aeb9b6dd047474dd7b2db8cce45e05433f416b05dd5a48b597096d1', 3, '2026-04-02', '2026-04-02 17:41:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:40:10'),
(1500, '5ec3743fcbd072f10b6733de471dcca90881b4c2d51c6d5ef44dd46e6aeaf72c', 3, '2026-04-02', '2026-04-02 17:41:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:40:41'),
(1501, '51be3cb9e7aebc4a95f27049a9696fb612555a835904548a2795a42c7b764284', 3, '2026-04-02', '2026-04-02 17:42:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:41:13'),
(1502, '5a14bb443f02506432854c2195c305720c188502dbd9b71a1d2f3c456264e1f7', 3, '2026-04-02', '2026-04-02 17:42:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:41:44'),
(1503, '90a6c64b3502357251b3313a27d76b77a6ff670fa78d01931fdf9b3f2cb0d435', 3, '2026-04-02', '2026-04-02 17:43:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:42:15'),
(1504, 'c7ef2625edfdde65c9d3a3912a1384e0a827a39a2a39bcfa510afe9c7bd25f2a', 3, '2026-04-02', '2026-04-02 17:43:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:42:46'),
(1505, 'fe5860ee2e38e6a1d4633c7f05aad7f86f915190eb7bf08c8235d424ea5cdf82', 3, '2026-04-02', '2026-04-02 17:44:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:43:17'),
(1506, '08996535d621d02296bb110c7e6e9d46974765d96cbb8ee4109fad04ce725bf8', 3, '2026-04-02', '2026-04-02 17:44:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:43:49'),
(1507, '373ee0b6c43cc3453e1dc2176b64be300a72f5d3bd491387267080178d737dd2', 3, '2026-04-02', '2026-04-02 17:45:20', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:44:20'),
(1508, '6c100346506b40ef1da8a7859273b73e87f417dd0015a57733ae7f7c9c9eb57b', 3, '2026-04-02', '2026-04-02 17:45:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:44:51'),
(1509, 'f264ab76e325df1b4ec8e4723829c797066ef953e07160f19305e3cd6b78e51c', 3, '2026-04-02', '2026-04-02 17:46:23', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:45:23'),
(1510, 'eb42190aa1ec44c34470934194defeeea93b1a9b32bec4284ef79689d2204e59', 3, '2026-04-02', '2026-04-02 17:46:54', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:45:54'),
(1511, 'e12250409d47ae3e22540d4c63fea1ea51963cb05f82aeb5aa0a0f492ed3ca2c', 3, '2026-04-02', '2026-04-02 17:47:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:46:26'),
(1512, '7c547a83515e37f22663eeb19e3a299d6970e1a7da2711391adea88761d63089', 3, '2026-04-02', '2026-04-02 17:47:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:46:57'),
(1513, '9bd828a956da2930d9212da472ff0b88d0aa424ed7a4748153c10e8925667ff8', 3, '2026-04-02', '2026-04-02 17:48:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:47:29'),
(1514, 'e6c4384cc4a799cceb65871e8b6738414160bb9a0c7d04b82355a2e08b3524eb', 3, '2026-04-02', '2026-04-02 17:49:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:48:00'),
(1515, 'df243a329ff6ba5446c1129b595424a38f4b24fafb8dcd942fca6cd29b1fd2bd', 3, '2026-04-02', '2026-04-02 17:49:31', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:48:31'),
(1516, '4206d96fd76ab4b56117ec5cc108cc29696a8b50d852df8c161876dc066f7b98', 3, '2026-04-02', '2026-04-02 17:50:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:49:03'),
(1517, 'f548e7dcca65595528e4d892958a65d24184698df2303610f70b15d8ac6c013d', 3, '2026-04-02', '2026-04-02 17:50:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:49:34'),
(1518, '438188199cec0a722453c3ba482ae9a2e627f2f792826c292b5e0f594af5ce77', 3, '2026-04-02', '2026-04-02 17:51:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:50:06'),
(1519, '80ecb3a5c763ce304aac4d129065c51ae9e7f8f1094ad6a51c03a8f764f69bd0', 3, '2026-04-02', '2026-04-02 17:51:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:50:37'),
(1520, 'e6055881c909c2d3525e28a87cf4ce3ef1f8eb5c5a6e355bd82d8db70658edbe', 3, '2026-04-02', '2026-04-02 17:52:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:51:09'),
(1521, '016869319944153a575cabd710596989f6aad6153fec7925ec89a06706257f7c', 3, '2026-04-02', '2026-04-02 17:52:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:51:40'),
(1522, '38e4112126b4c986038b7095b8d89a643bf2cf1c66fe5d5cbc6c373af83d274f', 3, '2026-04-02', '2026-04-02 17:53:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:52:11'),
(1523, 'e6e2ce9e5623a7e4569ec3c46fa9747f804fd90d68b7ab07d13f34867d803e87', 3, '2026-04-02', '2026-04-02 17:53:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:52:43'),
(1524, '3b7e12d3aea47f611306a1b08b4e5afa058cdf59f9dc16839cf078cfc74da79c', 3, '2026-04-02', '2026-04-02 17:54:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:53:14'),
(1525, 'd9fc97ec0cdaee586a6ed79ac12399a1c4ca95ffc074a1653674c1e6cebea198', 3, '2026-04-02', '2026-04-02 17:54:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:53:45'),
(1526, 'd2b636e3661aa6e670cd30505875373331d471f886703530904781b5deda06ce', 3, '2026-04-02', '2026-04-02 17:55:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:54:16'),
(1527, 'c4b89aaeb856db789291181c1ceaf29157a53c386bae6b033f2f8f7f97c24409', 3, '2026-04-02', '2026-04-02 17:55:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:54:47'),
(1528, 'd6b60c434fa8232f513a47a98b353d417f2dc5e8aeeec4bf6e08cf54afc2b261', 3, '2026-04-02', '2026-04-02 17:56:21', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:55:21'),
(1529, 'c3bc49cd428c09f5f9bf207c2276da23bb583de1ab53587fa6ca3e0812c24d5b', 3, '2026-04-02', '2026-04-02 17:56:52', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:55:52'),
(1530, '8dc05af6efe7d01e7de89a896f574cfff8328da47e80e6dadcb5ecc7377e6529', 3, '2026-04-02', '2026-04-02 17:57:23', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:56:23'),
(1531, '84c192c6a75260304aa710ae230ec485a86dd7e2834c5e023fba494d6f958d9e', 3, '2026-04-02', '2026-04-02 17:57:54', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:56:54'),
(1532, 'da5ac10e0ddce3b1528a652e7d793fadab1662cd009848ddc86eef3f2d03adf7', 3, '2026-04-02', '2026-04-02 17:58:25', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:57:25'),
(1533, '562119ab7e555a215f4cfb49eece61e69219b0cd10c2b8ec11e0bed996efa9cd', 3, '2026-04-02', '2026-04-02 17:58:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:57:57'),
(1534, 'e44046092e6c20adb74885ce7f3cfb4b0fba84755b6e2044042f441030b128c2', 3, '2026-04-02', '2026-04-02 17:59:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:58:30'),
(1535, 'c9c8c53763b7aa5ff99e7009fd33ad688eb4fe38e697b5306c1e1f11285854f4', 3, '2026-04-02', '2026-04-02 18:00:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:59:01'),
(1536, '0aa7c7f1c6b79b9adb5b275bad83bcce43d6f4d1077d4cd62970565fcc5d3f7a', 3, '2026-04-02', '2026-04-02 18:00:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 09:59:32'),
(1537, '04d9b5d98f9c652c158c74efc6fd3e990ff37b5193bde5fd917ebd8ddb58d463', 3, '2026-04-02', '2026-04-02 18:01:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:00:03'),
(1538, 'c800678f0c6cb8248a82e2feab110e418ec672059c34694bceed497e8053ad0f', 3, '2026-04-02', '2026-04-02 18:01:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:00:34'),
(1539, '3bc27287991bf7a44c0037bc0f2b76e681f5b2c9ead27d8f8d0c48544581f590', 3, '2026-04-02', '2026-04-02 18:02:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:01:05'),
(1540, 'f329d032d219f90cb75bb51a64f80af6913ef0c942f55b48285910ecf6a9c50b', 3, '2026-04-02', '2026-04-02 18:02:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:01:36'),
(1541, 'eb087e6fd64f32d926c6a133546250d0ff4a37a8dc6fcb146d8276322e6399aa', 3, '2026-04-02', '2026-04-02 18:03:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:02:08'),
(1542, '12d4c5d38630b39b1f9d239910c97128684261c3811b3cec0e3ab6e020407f0f', 3, '2026-04-02', '2026-04-02 18:03:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:02:40'),
(1543, '09d5b4d425fd1ee1b5fe2384eb92be5185568e276730a899572c188e4610c0a0', 3, '2026-04-02', '2026-04-02 18:04:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:03:11'),
(1544, '8fec9fa573c9044b8f0e5cc09b6d8c28e04ee66c23fc7eb29655b6486c10103a', 3, '2026-04-02', '2026-04-02 18:04:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:03:42'),
(1545, 'bcfc53851261eea5537f37bd89085c99041c08cb969a7c3f38abaa6e3f4c8b1d', 3, '2026-04-02', '2026-04-02 18:05:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:04:14'),
(1546, '97dcfbe21c4e998122c1c1cc330736906da3c1451c3fb570e4819320255d52d7', 3, '2026-04-02', '2026-04-02 18:05:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:04:45'),
(1547, '10974d6687644a6a2b22b1e6c0aa154e65e36fed4fe1b406513f3511c18f6e71', 3, '2026-04-02', '2026-04-02 18:06:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:05:16'),
(1548, 'ddede603df864ade4195b68283cd33cc22e74bb151f49420de3553157646ddee', 3, '2026-04-02', '2026-04-02 18:06:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:05:47'),
(1549, '68ba0a5c433c250b722dc07ce7164a44cd22d54682dfaacb882169731665e458', 3, '2026-04-02', '2026-04-02 18:07:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:06:19'),
(1550, 'fbe84ee91e33832d1d3a98eee5ad0e6deeed8e3b4f2df46d6b2332585761393c', 3, '2026-04-02', '2026-04-02 18:07:52', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:06:52'),
(1551, 'b96402790190982719f21bd409adb724072b9b6db31a701d2df9d8d77d9247ce', 3, '2026-04-02', '2026-04-02 18:08:23', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:07:23'),
(1552, 'f32949b1aa15087d51ee5a850a826a9e817fb01714fc868f7bc73721ad85d722', 3, '2026-04-02', '2026-04-02 18:09:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:08:00'),
(1553, '9584d8b1e74658adb3821eb042aff3713c9b70082e3fa77ae654a41588c357bd', 3, '2026-04-02', '2026-04-02 18:09:31', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:08:31'),
(1554, '5c77061adef58c5cd5f32e6b4563243f0ebd1cd427297db778c53b9f5ccae753', 3, '2026-04-02', '2026-04-02 18:10:02', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:09:02'),
(1555, 'f40849e9a217facd2937c1e497789f0aafee91d166aabbd8601e36dc1a5d786b', 3, '2026-04-02', '2026-04-02 18:10:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:09:33'),
(1556, '591e54739d84ebf444854dc06cf740583eae79c15dff4ccee2f6fc1937f29d89', 3, '2026-04-02', '2026-04-02 18:11:04', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:10:04'),
(1557, '486f4d170221eefdc6df90a796c37cd46ca14e1b0a4c0621f3ecfcace05c023b', 3, '2026-04-02', '2026-04-02 18:11:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:10:37'),
(1558, '4fa8d9a11a77530b929f7a884a0dafe564b10289c83c2203c61d815363c90894', 3, '2026-04-02', '2026-04-02 18:12:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:11:10'),
(1559, 'b2c149b042e98e2e7dfeb47c17e836e11b97beed8b87413e50f54733ac49760d', 3, '2026-04-02', '2026-04-02 18:12:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:11:41'),
(1560, '2bb279350446cf825058317932b8e836005afbc9647b3b7852617181b84e21a5', 3, '2026-04-02', '2026-04-02 18:13:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:12:13'),
(1561, '18ba8259739ea7e1d0f0d69568ba3c4438d396a8707065a8dcb44ea6d29f032b', 3, '2026-04-02', '2026-04-02 18:13:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:12:45'),
(1562, 'fc2411ab60ebb471a9c3fe98171cd7e5c3e6f821ac338613ec1c7c80710aea60', 3, '2026-04-02', '2026-04-02 18:14:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:13:16'),
(1563, '1c0d67665873266b262c95a3de1b4cced32fe2ee1b5555472ed5e70f2137684d', 3, '2026-04-02', '2026-04-02 18:14:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:13:47'),
(1564, '337de8110bd179c450055c9d009e808e9b0b98ecb318a5580aa9e8713f3414e0', 3, '2026-04-02', '2026-04-02 18:15:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:14:18'),
(1565, 'fea1ab1f8b886d2c884b11d5915e07ef634b02b7935be75c8b1fe29906c29aca', 3, '2026-04-02', '2026-04-02 18:15:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:14:49'),
(1566, '731bea8784d04eeba677991b55466d2c0fa0419950561bf44a8e2d6d1a215a7e', 3, '2026-04-02', '2026-04-02 18:16:20', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:15:20'),
(1567, 'b7b36e7b6b89ea583d45542251b849571630778b29a9450677f7b187ba8ad9e0', 3, '2026-04-02', '2026-04-02 18:16:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:15:51'),
(1568, 'a289a8cf6e07c6edbc2e8558ef17b34f08c956992510e48ecd0f209d8a23ee26', 3, '2026-04-02', '2026-04-02 18:17:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:16:22'),
(1569, 'dd196087919d6dcafc99872b4d99b53d529b7091ad93c60681e4c283a9d5bf38', 3, '2026-04-02', '2026-04-02 18:17:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:16:53'),
(1570, 'c474d6ff1063ac2f37bf792384b4c50222900c9c01750d5dc92f4ce7184083a7', 3, '2026-04-02', '2026-04-02 18:18:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:17:24'),
(1571, '8fde3531ae2d88ece265bd2cd88479ae9b65867699319be8116ae2557fa7595b', 3, '2026-04-02', '2026-04-02 18:18:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:17:55'),
(1572, '4053ea23988703b0a763900f2d9289ef8d45d937bcf919b2a205edd9dc100adc', 3, '2026-04-02', '2026-04-02 18:19:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:18:26'),
(1573, '69a3377ec77bcd4df24d1d5df95e1d2cdf59acb10f6a3bd1e59907d802aaccf2', 3, '2026-04-02', '2026-04-02 18:19:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:18:57'),
(1574, '56b201edd4c1204509aefae57c6345b5c66af99d45a7d7280267e35209f5393b', 3, '2026-04-02', '2026-04-02 18:20:28', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:19:28'),
(1575, 'f75d3bda7e843fae9929d5c506d28bf9114983f9c2bfefe25d94983b498de938', 3, '2026-04-02', '2026-04-02 18:20:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:19:59'),
(1576, '4627033b42ed0707688c175bbc4adeb827589967b183483367c17f9eec70c2e5', 3, '2026-04-02', '2026-04-02 18:21:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:20:30'),
(1577, 'f02af1201a67688157581cdd889d239b63cdbcd565fa8d3ccd510fb81f2eaaa5', 3, '2026-04-02', '2026-04-02 18:22:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:21:01'),
(1578, 'dd158bb29e6c6e5eabc0d36d54a5af3457097312475bd98e8181ca396a10020e', 3, '2026-04-02', '2026-04-02 18:22:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:21:32'),
(1579, '776447c66018f6bd7e7d32703e7c2fb580494f6bdeeb9e9fbbd892365a19eee5', 3, '2026-04-02', '2026-04-02 18:23:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:22:03'),
(1580, '48687372d5e99a4c4e5bfa8933e7cffd66f87070b8eb7f766f7f7afce9b613b7', 3, '2026-04-02', '2026-04-02 18:23:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:22:34'),
(1581, '093c0ca7044fe0ee4065f735cf64ce6dd572dbad8428d77c27879785f8aa3c7e', 3, '2026-04-02', '2026-04-02 18:24:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:23:05'),
(1582, 'd83829f2648beb58b67a65b8fb70798a40071a682b814c315692f4dd442834e4', 3, '2026-04-02', '2026-04-02 18:24:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:23:36'),
(1583, 'f6f3ee5c3b9d34ea2f6bec157f2da857baf2e3ba731ef127162b563f04b766c3', 3, '2026-04-02', '2026-04-02 18:25:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:24:08'),
(1584, 'b037dd3d6242f44943860791a870c280dd70c1408224e18fe7905a819943d270', 3, '2026-04-02', '2026-04-02 18:25:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:24:39'),
(1585, 'a73c75bbcc87402f0d6021d9a0e1399ead5803ccc4603230c84edefe22e4c599', 3, '2026-04-02', '2026-04-02 18:26:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:25:10'),
(1586, '796b0ac9c38b338dc12b50876ff53b104e3f334014611696bf37ac44f3bd1409', 3, '2026-04-02', '2026-04-02 18:26:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:25:41'),
(1587, 'fb8fb1c7a55606eda2edc8db81ed874993e2a7af9e0906fc111bdf4440cbb67f', 3, '2026-04-02', '2026-04-02 18:27:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:26:12'),
(1588, '14883babd794b63a65950d9a0706b2cd126a2bab1ee8434b3a221c0839e3b187', 3, '2026-04-02', '2026-04-02 18:27:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:26:43'),
(1589, '358372abdba86e0d03717a77f49a65ae125779b1bf452032882a8a8510355185', 3, '2026-04-02', '2026-04-02 18:28:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:27:14'),
(1590, '74bba7949787e68b0a7c140246f0a106fa7731d762dd4130a468d6d0646f7cf3', 3, '2026-04-02', '2026-04-02 18:28:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:27:45'),
(1591, '50db8c185180ff5892bd75c952fea6682cb0560999b5be6043507c4e2358fab4', 3, '2026-04-02', '2026-04-02 18:29:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:28:17'),
(1592, '6a9c3a7654bd225bf330961b95cec9a4b840bac508f974881edee05f75aa50df', 3, '2026-04-02', '2026-04-02 18:29:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:28:48'),
(1593, '87a9ef87211b16209ef8e649c462ad8265b6e4925eda02ad5a1819bb3bd448ab', 3, '2026-04-02', '2026-04-02 18:30:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:29:19'),
(1594, '38b340bbc476f47a4408738f08acd80fdcb35695381f5d554cf838a2c9841e0b', 3, '2026-04-02', '2026-04-02 18:30:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:29:51'),
(1595, '7b7ab7e6998561dcf95e9ca0a608dc29fc8a88bc4e5b72fec42c6f7de06c991c', 3, '2026-04-02', '2026-04-02 18:31:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:30:22'),
(1596, 'c182ae9d2b38cc8bc464a9c408985f1701340f787940a3938bab47481f62e699', 3, '2026-04-02', '2026-04-02 18:31:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:30:53'),
(1597, 'ec27fd762fdc3626a914e84caa35be38b1fa17efe1209ad3fa5231a9f4f0999d', 3, '2026-04-02', '2026-04-02 18:32:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:31:24'),
(1598, '9486a9ff092909be23e2c845b5fb447ae3c43a10bed01031956b1465360fba9e', 3, '2026-04-02', '2026-04-02 18:32:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:31:55'),
(1599, '5a5d554f51e2796b97cf47535b21992021455f44e69ca81ea7c8244505dc3542', 3, '2026-04-02', '2026-04-02 18:33:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:32:27'),
(1600, 'ec3885baa54649e1a73bd33ad84c25b16fb036f02642828cd0449e2e1ea44127', 3, '2026-04-02', '2026-04-02 18:33:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:32:59'),
(1601, 'cb5e120ebb9514bf36acd2eae5fc5cab6ea8fa45a54c101a68ea4d69f387f80b', 3, '2026-04-02', '2026-04-02 18:34:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:33:30'),
(1602, '97cbd0627abe88046404d3ad7ccd302859dc6550d2c7fc48c5dd87dd2f41269a', 3, '2026-04-02', '2026-04-02 18:35:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:34:01'),
(1603, '0333abcaefcdad4ca68ece1475d14263b28f7d83e3de6ed9ecd68952822cbdf9', 3, '2026-04-02', '2026-04-02 18:35:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:34:32'),
(1604, 'c40de1c0d9da4c35b166e4966b1252e6dfc0d143534d6f6031e3149d8603ca3f', 3, '2026-04-02', '2026-04-02 18:36:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:35:03'),
(1605, '0c5f8381720c787734e3f42ff91882e6c6f993e21b396bfd805fedd08e998ef9', 3, '2026-04-02', '2026-04-02 18:36:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:35:34'),
(1606, '29f511c21476a40f1f7c829167cd830cd9c7c2794b1013a30c980ae369a118a4', 3, '2026-04-02', '2026-04-02 18:37:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:36:05'),
(1607, '31b83c755d15a06e73fc55bfac912694f409fa5252111d5f19f3bb1f90c70de6', 3, '2026-04-02', '2026-04-02 18:37:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:36:36'),
(1608, '9665fdf707ef74d5c905966cca7502f171755a511d85d49e91f4ea1e55365f38', 3, '2026-04-02', '2026-04-02 18:38:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:37:07'),
(1609, 'edfcdcf54a423ccc7800caa6c671e777adc46c6800b6b64d8e20dbbb8793907c', 3, '2026-04-02', '2026-04-02 18:38:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:37:38'),
(1610, '8b6161f133657fec6400261c88c2fafb71d7595805c0f7df4e6d6ce77f9def26', 3, '2026-04-02', '2026-04-02 18:39:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:38:10'),
(1611, 'acc596d2170c023d08c0aae1f467a66155c0a638f0588c5c8fdd005de793117e', 3, '2026-04-02', '2026-04-02 18:39:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:38:41'),
(1612, '431fbf069b6d12f15656dd55a71418188f395fecacb39a348c007dde55cdd1d0', 3, '2026-04-02', '2026-04-02 18:40:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:39:12'),
(1613, '882c969ec569b1175b7640332125cb54dd8b3d8e064f8e8ecc2f3aa765007286', 3, '2026-04-02', '2026-04-02 18:40:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:39:43'),
(1614, 'bece6fbbfdbbdbfcdab05ca3cf85b9fdb59e841c2e872f95edf7d2924215d6b5', 3, '2026-04-02', '2026-04-02 18:41:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:40:14'),
(1615, '3951fcb91778ab4e894617286a8f89f1ded050a0c4bad6d6b29a898c9e99efd2', 3, '2026-04-02', '2026-04-02 18:41:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:40:45'),
(1616, 'f70b0aa1452a82f576fbcb917929eee2254a756e7bc45ea34dd157e7f3d06b2b', 3, '2026-04-02', '2026-04-02 18:42:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:41:16'),
(1617, '193eac738079bdb7782321c5ee2c399e54f7312171dd68d907b5049963dedc07', 3, '2026-04-02', '2026-04-02 18:42:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:41:47'),
(1618, 'cb18e167e5691c09acc44524ae7c7c17a5ba36e4c02104c9e695cdb9647e2251', 3, '2026-04-02', '2026-04-02 18:43:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:42:18'),
(1619, '50f66fdfe70fbb2a8b973f6489a75fe4e39fe3b598e0571810f86cf1c294c7a4', 3, '2026-04-02', '2026-04-02 18:43:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:42:49'),
(1620, 'a12101f86d2ef03bf50ac45256604adfae6b16760b54178a1b407e2b3ed88a66', 3, '2026-04-02', '2026-04-02 18:44:21', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:43:21'),
(1621, 'e938a68b5d9440ebb771cb63964068ee462a0beb17c72efbb496bbfa237ce60d', 3, '2026-04-02', '2026-04-02 18:45:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:44:05'),
(1622, 'ef93e7e9f6730db22c3e0900aeb8a30fe297aa92cebde773ea7dd268fbc5e8a2', 3, '2026-04-02', '2026-04-02 18:45:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:44:36'),
(1623, 'd8a09fd8aed170a2d7f807ff388054d478b1967ba0d1e2867c31519286b2e148', 3, '2026-04-02', '2026-04-02 18:46:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:45:07'),
(1624, 'c5b445ba692ce5434dcd21d0482a4ad49734e477349b5c626db467b041ce52b7', 3, '2026-04-02', '2026-04-02 18:46:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:45:38'),
(1625, '78d13fc21bc1b16c5dc0ccc20685230af1f66fdef931bd74a5edfd5637012dfe', 3, '2026-04-02', '2026-04-02 18:47:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:46:10'),
(1626, 'd25ca78043c79b714de74d5379af7576238ca35fbff37d2d9ae85683c244b5f9', 3, '2026-04-02', '2026-04-02 18:47:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:46:42'),
(1627, 'a15dbf2b2dba96a5fedc1eb5bb094a301318aa0b764174836f3174f2706e77c3', 3, '2026-04-02', '2026-04-02 18:48:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:47:13'),
(1628, 'c2a54499d431efdcbc58b0ead5c5960f87ff91856823d21bef1b2cc2cdd5a023', 3, '2026-04-02', '2026-04-02 18:48:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:47:44'),
(1629, 'c3f65df815dd54a33e515430b6aaaa8e6ae7ebb417ff49ba9580a5f8f9b57603', 3, '2026-04-02', '2026-04-02 18:49:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:48:16'),
(1630, 'd139350a61b378e72e052a325be76ecdbbb124f2ca5a982f00090a5bf5d95237', 3, '2026-04-02', '2026-04-02 18:49:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:48:47'),
(1631, '086dd08e5ce732c034b70374f11773156a9320c4a1ac65ff417b127f2385f7d8', 3, '2026-04-02', '2026-04-02 18:50:20', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:49:20'),
(1632, '7dfd507483b445d3267cf4bd51449c5c2ee49c59cf7f6705412f67fc0ec4c5c1', 3, '2026-04-02', '2026-04-02 18:50:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:49:51'),
(1633, '7604bbd5cb88faea7c87ac750ba17a885784a71041fe63d36a5ef5bc322dcd15', 3, '2026-04-02', '2026-04-02 18:51:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:50:22'),
(1634, '3e3692eb41c0c146e1f10906df0385d0aca99781f4488c9a545e74f674efba00', 3, '2026-04-02', '2026-04-02 18:51:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:50:53'),
(1635, '647777444d77f860e44c3e19b555b68cbb8ef84963fa91aab060e8a809d94062', 3, '2026-04-02', '2026-04-02 18:52:25', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:51:25'),
(1636, '3e084704ec4c567e4d1f41a7af2a4b46acf93b61ad576eeed4ba524a13b5b95c', 3, '2026-04-02', '2026-04-02 18:52:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:51:56'),
(1637, '436dbd74c6b919bbf16388ed4257b87a26c312163b4eb4512a911674c668025a', 3, '2026-04-02', '2026-04-02 18:53:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:52:27'),
(1638, '5ec4b8438f0d92e08b7575840c57e044d8c962af95e3086a2f098a2748f5a796', 3, '2026-04-02', '2026-04-02 18:53:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:52:58'),
(1639, '81a1b623bd358c4c2fa024ca5d7bc02494024a6aa7dc53607fa491623a6db620', 3, '2026-04-02', '2026-04-02 18:54:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:53:29'),
(1640, '432769f72bad2d9577cd1f42ed5507152342faf410ca1f72509b49ffe53b43c8', 3, '2026-04-02', '2026-04-02 18:55:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:54:00'),
(1641, 'c428a3da9a9fa0a89c2a42218e47497bc65a03e9af29a49759245f17642e79e6', 3, '2026-04-02', '2026-04-02 18:55:31', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:54:31'),
(1642, '65e1cfdaf5f4ee1aa9a0f210d7dff0c0797734bcbaef84279789edb48a885af5', 3, '2026-04-02', '2026-04-02 18:56:02', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:55:02'),
(1643, '77053dc80b05d093b061c700dc5de966ef49d92d378240e641aaeabca5582328', 3, '2026-04-02', '2026-04-02 18:56:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:55:33'),
(1644, 'd4aa07c6fee76b8828da4d45d3b1b966d242356d2734013a8690488a033fb447', 3, '2026-04-02', '2026-04-02 18:57:04', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:56:04'),
(1645, '01721e149b1028a1acd0c6772f5689691f82aa88251dac5ba8ed5840a25ae0a8', 3, '2026-04-02', '2026-04-02 18:57:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:56:35'),
(1646, 'b42509b993dfebf807f7cc2ca11a32d88e38532171e9012b1b2817ad3379cfec', 3, '2026-04-02', '2026-04-02 18:58:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:57:06'),
(1647, '6b0c6157f02e8234ae57358bcd8c8c221a6b160a6508f0d50d3e43dabacaeade', 3, '2026-04-02', '2026-04-02 18:58:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:57:37'),
(1648, '8b110b12ed84ca0a2fa65962aeec9af575d11df8d31100980ed3f62f77512c75', 3, '2026-04-02', '2026-04-02 18:59:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:58:08'),
(1649, 'db8237598cc64686b502093384fd1879a61a61a9bb3a4543eda85dbb78eb2079', 3, '2026-04-02', '2026-04-02 18:59:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:58:42'),
(1650, 'c78afe4c201ca2150641f51f3f2ec13d949ee882fc212394fac14ca8ddb2dfe3', 3, '2026-04-02', '2026-04-02 19:00:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:59:13'),
(1651, 'cb2ae0327148bf9aadb64941bae147621e42156bb6699627577f7ca0f2c74921', 3, '2026-04-02', '2026-04-02 19:00:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 10:59:44'),
(1652, '627060641c537f46e9f51dbc3c0c304def8cdcab7db7070810afe3ff2a94a485', 3, '2026-04-02', '2026-04-02 19:01:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:00:16'),
(1653, '0b5376de312be702f2be25d8766faa000fdf52b1d3a1bebe7fb88288d58da5fa', 3, '2026-04-02', '2026-04-02 19:01:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:00:47'),
(1654, 'c1f04f7a89bf809e1068c04a40b8d24de55ed3c6eb7aa099cbac67cd882c88dc', 3, '2026-04-02', '2026-04-02 19:02:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:01:18'),
(1655, '0de71fb0123b26308de40c3a431777437b434e6d2e97653e8da4d487d1ebde00', 3, '2026-04-02', '2026-04-02 19:02:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:01:49'),
(1656, '0fc022b728ee6becea7604791ddd11a74f0fc9f1b82a1a8fd09d7675901d5307', 3, '2026-04-02', '2026-04-02 19:03:20', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:02:20'),
(1657, 'c0c29dbb546f4268c46749463e13df2c042c32ef215a6f65a3a1e9cda750ae8d', 3, '2026-04-02', '2026-04-02 19:03:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:02:51'),
(1658, '678e5a2eac08902359364ff570cc9e6eb007e8bb3f0250bf10aefe0f1d541693', 3, '2026-04-02', '2026-04-02 19:04:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:03:22'),
(1659, 'ceac59f9d337b7c64dcb1ab462955e69eb1c0cd2ee7a18f10914cec04cf407d3', 3, '2026-04-02', '2026-04-02 19:04:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:03:53'),
(1660, 'deffa91e2002002d5f4d640b77907c74d4e8a355cc18b64bc291a09e016a5b4c', 3, '2026-04-02', '2026-04-02 19:05:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:04:24'),
(1661, 'c8a6714990783c13499788cbe89e1d8db52c89ed062408b0b781e646ac1aceb6', 3, '2026-04-02', '2026-04-02 19:05:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:04:55'),
(1662, 'bb82f5606eedf5bd368f889c4b303412cba5b5efbe0fe684235d30d3e40dd119', 3, '2026-04-02', '2026-04-02 19:06:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:05:26'),
(1663, '6c06fc44c5cb5cf89086e4e3c6e2bfef087cb35838f3c67ea88b29cbfe274570', 3, '2026-04-02', '2026-04-02 19:06:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:05:57'),
(1664, 'c248c69ba2073cfb58c88f8c117a22b1a17e16a10465e7934ccfdb51b9b1b72c', 3, '2026-04-02', '2026-04-02 19:07:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:06:30'),
(1665, '08cd3d0ee4e093ee8c37d940dba4e1ea70e8dbb893dd01fa85be782188c8a94e', 3, '2026-04-02', '2026-04-02 19:08:04', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:07:04'),
(1666, 'df4916c76eb900c46eede5bea78640bb70db86f0075af058ebad9c27bc5ec317', 3, '2026-04-02', '2026-04-02 19:08:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:07:35'),
(1667, '9037c77d3a96aa1f5e62d9bdecfbe0168e8768cde1cc763ba1c77bb0904c55ad', 3, '2026-04-02', '2026-04-02 19:09:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:08:06'),
(1668, 'ff12a38cd99ef17c55cc5a893b57485da7ffef6ac81ae4c39b9bb66eb31fe36a', 3, '2026-04-02', '2026-04-02 19:09:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:08:38'),
(1669, 'ca56da63f65ab1f755ffce5227088e527a334d3358a0603d3266aa5280b7bb3b', 3, '2026-04-02', '2026-04-02 19:10:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:09:09'),
(1670, 'bd6a9a3ae309c82837a95711adbb1b88d794ef4a01fbbae83b5d2a702072cc58', 3, '2026-04-02', '2026-04-02 19:10:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:09:40'),
(1671, '2db0ad7749e52baa53f2afc5e84abb44673e3cd274a4a215e110113307995c61', 3, '2026-04-02', '2026-04-02 19:11:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:10:11'),
(1672, 'ba6d900eb86a0d72ba02e0559f6d1f6a8ae43d0745ad11584fac16ad0f3f0984', 3, '2026-04-02', '2026-04-02 19:11:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:10:43'),
(1673, '99f56127d740cf90b76945e9155a4dd4c4520f55b6397dd22a4f070b1630c385', 3, '2026-04-02', '2026-04-02 19:12:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:11:14'),
(1674, 'dc31efc242ce10fac09c9e417aaf09d52f33066c270acf98bcf31f9847860584', 3, '2026-04-02', '2026-04-02 19:12:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:11:45'),
(1675, '6890bb89dd14a3b67691a281b01aa0a4767a96382c5074498983f84f1b7c962b', 3, '2026-04-02', '2026-04-02 19:13:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:12:16'),
(1676, '0104b018e3906d4588c9d8a1d3d55d4a601bdf5640a80f224f74afd6865ca534', 3, '2026-04-02', '2026-04-02 19:13:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:12:47'),
(1677, 'a38ed6817467d460881881a02db928c00c803d3c45a978e13cf614297650df9b', 3, '2026-04-02', '2026-04-02 19:14:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:13:18'),
(1678, 'a66356b65fcc45f8cd6beb5f4df47d10a9b489110275565fe654fdf6f2713b48', 3, '2026-04-02', '2026-04-02 19:14:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:13:49'),
(1679, '641f6e89f0d46b3d26ac4f7d856aeebf17c7fe9ba1e91954c71f7c8d1c28dde2', 3, '2026-04-02', '2026-04-02 19:15:20', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:14:20'),
(1680, '044f495f6034975cb7abac22ccfcf58ff323679f874b7bf5bdcaa176f72f4faa', 3, '2026-04-02', '2026-04-02 19:15:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:14:51'),
(1681, '5e88f32d047e4260e7ff91a78a69533afbfa28dd5b68d96b94cfcdca2fa7a006', 3, '2026-04-02', '2026-04-02 19:16:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:15:22'),
(1682, '05cf2f3200353ef379e8fd743c5e804d93db0c6198694fcc0ada18a464fd33e6', 3, '2026-04-02', '2026-04-02 19:16:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:15:53'),
(1683, 'f55868410a5f4a2e9b976c868be7aa29fa3333ecf9104b42a4f5321919cfa851', 3, '2026-04-02', '2026-04-02 19:17:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:16:24'),
(1684, 'c14619952f319672250ed5165d0f71e4f4d9860af7dcb9624c7925a979bca250', 3, '2026-04-02', '2026-04-02 19:17:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:16:55'),
(1685, 'af8037f2b87b7c58791b3b91805e8b21e0d07b4e3423bdcf3eec62718a861e9c', 3, '2026-04-02', '2026-04-02 19:18:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:17:26'),
(1686, '4a3c8ec3ef9af67b592075bb3438f78fd199e1b2332a4e830b20df064697c293', 3, '2026-04-02', '2026-04-02 19:18:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:17:57'),
(1687, 'b5baaa92d5eed6cdbeb603119cd6014db230fb4a011a8b41ed67a9628e5d10ed', 3, '2026-04-02', '2026-04-02 19:19:28', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:18:28'),
(1688, 'f7a520be2c1e5e1154267a7d6a42ffac45f0b26f8bb81dbec332a9794f82033c', 3, '2026-04-02', '2026-04-02 19:19:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:18:59'),
(1689, 'f1a30f8db5534b88645356e375028c3b5200ea84699c9f035f416ecfc74fea11', 3, '2026-04-02', '2026-04-02 19:20:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:19:30'),
(1690, '14dba4edb95843cdfc53e30b69e3afafb9e9519d5f410d3fe11af624dae5b8b4', 3, '2026-04-02', '2026-04-02 19:21:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:20:01'),
(1691, '4c4e9cf8b5528ee80a567f4e902139cc2012089a5965a681a121db692827a558', 3, '2026-04-02', '2026-04-02 19:21:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:20:32'),
(1692, '4cabef7248484eda35ee4dc4858bce981193534b124a2b9a4543197d2918ea0e', 3, '2026-04-02', '2026-04-02 19:22:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:21:03'),
(1693, '31a7397ae75356bb1558a618fb8b541d6a989b02a28cf553d95b6cdf7e86490b', 3, '2026-04-02', '2026-04-02 19:22:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:21:34'),
(1694, 'ff5aa3f9c806d6650ab01c3e77dc145d8086f79865727992f667d0b667d1122e', 3, '2026-04-02', '2026-04-02 19:23:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:22:05'),
(1695, '23009e54ecfccb00ae1101607349a19bb47f40b95ab9e0e48e07d01f2ac2f670', 3, '2026-04-02', '2026-04-02 19:23:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:22:36'),
(1696, '3663240b6b06edb06b6a9a2d1c88a7814e91daff1d41404672c1984a693b06e6', 3, '2026-04-02', '2026-04-02 19:24:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:23:07'),
(1697, 'f2d126e4ac4062d9593109bd76957d5f3203e89094f27ecb924cd7e1ef7cc876', 3, '2026-04-02', '2026-04-02 19:24:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:23:38'),
(1698, 'f3441cef4f80782f697b5a78906f34bad653a507c66004a20bb42623d1c9af08', 3, '2026-04-02', '2026-04-02 19:25:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:24:09'),
(1699, 'a92919270c5d237c6377175681461bc56088921417ff53176e32bccdfe801049', 3, '2026-04-02', '2026-04-02 19:25:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:24:40'),
(1700, '2dbfe4d9df7e99e7ffc4d12915b21adceab7182e23b316311e3850ad4b77c4fe', 3, '2026-04-02', '2026-04-02 19:26:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:25:11'),
(1701, 'bf4b2f8bedbf81cede4614d8b310d1c9b1852a6cfaac665c57eb7101c1d33230', 3, '2026-04-02', '2026-04-02 19:26:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:25:42'),
(1702, '9b34fa2e5e4025e8e4e4858cdff24dcff3b55f42bbb412556b323ef0528bf2f7', 3, '2026-04-02', '2026-04-02 19:27:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:26:13'),
(1703, '5dd062d2530d6d746899f84657d96406bfdf8e140f8c74d2eaa187b0af5f3589', 3, '2026-04-02', '2026-04-02 19:27:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:26:44'),
(1704, '65eb27ba14e86229c49e420591f975f431cd02aa2a4dd6e682f54be9282a62ce', 3, '2026-04-02', '2026-04-02 19:28:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:27:15'),
(1705, '053b95bed618ba2c44b544ce06eb237e24ed12bb365212e61f655049d31ae558', 3, '2026-04-02', '2026-04-02 19:28:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:27:41'),
(1706, '398937cdb4bbcd1e5963c5a27df486abe028baabaf7ae0dd2f47b7d394e19e95', 3, '2026-04-02', '2026-04-02 19:28:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:27:46'),
(1707, '7e32bef54bf8d2deb03700bedbea325f8b4cfb9d44ec883ac7ead27180c6526d', 3, '2026-04-02', '2026-04-02 19:29:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:28:17'),
(1708, '1b3776d437fa5830d38d836a1e444a906cd7d71b47710d5f03f66eb36d0ebb33', 3, '2026-04-02', '2026-04-02 19:29:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:28:48'),
(1709, '92620b790e9d0dbecf7873429926c2abd2d0b6911b728fe7e01c2c160568ebfe', 3, '2026-04-02', '2026-04-02 19:30:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:29:19'),
(1710, 'f94f121824efa6322fcf9b4fe9d1c169d0397f04a4b3199697eeb55f65cdef5c', 3, '2026-04-02', '2026-04-02 19:30:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:29:50'),
(1711, '115e3206b95cb1c1006a084dc191c127e08bb20fd3f00c5ac6625682fcf661ce', 3, '2026-04-02', '2026-04-02 19:31:21', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:30:21'),
(1712, '70c7f83144d15f2677847d76e9db749e7bf9e167c3a80e45d655649258c5eb54', 3, '2026-04-02', '2026-04-02 19:31:52', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:30:52'),
(1713, 'efa4668ca920bcacb24f4d0df8a4336e4301fd3c029a48d2522f7b64b84cfe6d', 3, '2026-04-02', '2026-04-02 19:32:23', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:31:23'),
(1714, '2a6e345e6a15181abfa8aa1bf78522c8558f048b3003b070adda091303ca8a16', 3, '2026-04-02', '2026-04-02 19:32:54', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:31:54'),
(1715, 'd8e7218d7186759d513834ad198f2a459981507d33995065adcd0d5866e00353', 3, '2026-04-02', '2026-04-02 19:33:25', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:32:25'),
(1716, 'c6d5e669962875c021d63b91956a73b50fe9e8ede227394c6c57cc9c394f3aac', 3, '2026-04-02', '2026-04-02 19:33:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:32:56'),
(1717, '4d954b0516a6a83d571e3058457c20da51143b227946e845849f1c4c419d5ff6', 3, '2026-04-02', '2026-04-02 19:34:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:33:27'),
(1718, '91fd9dfcb1ecaeea70fdf2416dd207ecec504fb28655c14167e52a9317e47c04', 3, '2026-04-02', '2026-04-02 19:34:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:33:58'),
(1719, '894927106eb3917479bfadb1d1ef82ad82ef858d113d9a5b8184cf94d01782ea', 3, '2026-04-02', '2026-04-02 19:35:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:34:29'),
(1720, '1140f6904938c22b4ebaa91eefa540572d98f016c4b6b55499fe15b375b11c56', 3, '2026-04-02', '2026-04-02 19:36:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:35:01'),
(1721, 'd46a07ebe6774fb8642a00fa35b0ece235e161231218d18d9223dc361895f685', 3, '2026-04-02', '2026-04-02 19:36:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:35:32'),
(1722, '0002249c0081935148d6fd5bc97ab2830d9aaf41a56dc2fad422ad491c76d08c', 3, '2026-04-02', '2026-04-02 19:37:04', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:36:04'),
(1723, '017245bdacdec11d5cc2e204f833624ddfa91610455c176fe4fb831d33e4ab96', 3, '2026-04-02', '2026-04-02 19:37:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:36:35'),
(1724, '9815cea6e4cd93a56227bf48311c885c93a58493c7ea6bd5cb91145cd5bb4ca3', 3, '2026-04-02', '2026-04-02 19:38:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:37:06'),
(1725, '6ec4e312bbb8a2e7ee921c2b9b1d1c0ad76f421876ea0690fa6a024424b4c8c8', 3, '2026-04-02', '2026-04-02 19:38:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:37:37'),
(1726, '59c033db65a6c532d7b4019e99862f0931564331d903955612769be858439c36', 3, '2026-04-02', '2026-04-02 19:39:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:38:13'),
(1727, '0e2abcbd973280593af75aeecda67f3a78817d8344a47d3481f3170f7ea8753d', 3, '2026-04-02', '2026-04-02 19:39:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:38:44'),
(1728, '8fda8bde0f8e8de69b4b1ab33952fd23cbd344883ae4b7c711a25c8fd69bfce7', 3, '2026-04-02', '2026-04-02 19:40:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:39:15'),
(1729, 'aa858220f6ee99d9a4f92dcb173a8fda288e6ed9ffb6fa2737d1632c2a6834b9', 3, '2026-04-02', '2026-04-02 19:40:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:39:46'),
(1730, 'ba5ae2b2dc37c556a35b9b3ce7f68215c8ab062772f9fffc6c25a1738a3d883a', 3, '2026-04-02', '2026-04-02 19:41:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:40:17'),
(1731, '4558fb4ea07d49b63eef3aaec4d6fa23fe374d05171a8356f8390751c3482d81', 3, '2026-04-02', '2026-04-02 19:41:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:40:49'),
(1732, 'f1f671351b6294158639f6aaeeb0b79e7f8b3d34660b3ab0371a52b3af37a681', 3, '2026-04-02', '2026-04-02 19:42:20', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:41:20'),
(1733, 'ef27ca77704297bb3e881839a297e9c1af89edbae3a350365885975fa1b28c3f', 3, '2026-04-02', '2026-04-02 19:42:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:41:51'),
(1734, 'b2b44d8f2d7d4392483876d893cffe2b25eb7aa1de8985c9e5e8f9209e4d623a', 3, '2026-04-02', '2026-04-02 19:43:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:42:22'),
(1735, 'e0ad34f62cb2d59073b8f0aabd74f08ef6455058243fe8a555e456d5fdeac2c7', 3, '2026-04-02', '2026-04-02 19:43:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:42:53'),
(1736, '10f7b24c02c623401c96af1f1062d7012917aa0945d32706bbf310f71f01fc51', 3, '2026-04-02', '2026-04-02 19:44:25', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:43:25'),
(1737, 'fcabcfbe0cd50f94311bddb6c180e8485147079f07ac1139ff42539d44af712b', 3, '2026-04-02', '2026-04-02 19:44:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:43:57'),
(1738, '4adca1370fdf4f8641fab406009fe9e00d1d8cd6d9b3d6fd4a3202e1b0ca1ae8', 3, '2026-04-02', '2026-04-02 19:45:28', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:44:28'),
(1739, '4ff01705beb59d7414af42ae3bef1cf5ded25e5f03b847fb21753faff07c456c', 3, '2026-04-02', '2026-04-02 19:45:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:44:59'),
(1740, 'fe2c02a8c2c1e171eda1d947b7673b77e57dec7d02f44c007b0c1fd8163f96b7', 3, '2026-04-02', '2026-04-02 19:46:31', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:45:31'),
(1741, 'ce91ef8543e0b4ddc7e992c01300d47fc63ddca0fa4289a1097430aec5c9b667', 3, '2026-04-02', '2026-04-02 19:47:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:46:03'),
(1742, '596e48ebe23f6c8fd404bdb60568e8b79ed18e0097efc24603da663ff87372bb', 3, '2026-04-02', '2026-04-02 19:47:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:46:34'),
(1743, 'ce4c7f23fbd82325e28c0cbf0c29462ff2d7f75e2d90c6200170cd3e4a9ff35e', 3, '2026-04-02', '2026-04-02 19:48:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:47:05'),
(1744, '1c774f7dd484e5f9fcfbe22e2002cebe86a0e706b24ea422f64029ccda31af7d', 3, '2026-04-02', '2026-04-02 19:48:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:47:36'),
(1745, 'f5b1c48f83076db6bd5d62f39473055c6fedbb18f7c9590740a829327347679a', 3, '2026-04-02', '2026-04-02 19:49:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:48:08'),
(1746, 'f6b23dfbeef34f33e3aa4f7067ca52653b4862323a95c35295791b47066fa439', 3, '2026-04-02', '2026-04-02 19:49:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:48:40'),
(1747, '88d28f9e9963b8ef509563fa00df6b63682757bdac56de792fffad35d249ac7d', 3, '2026-04-02', '2026-04-02 19:50:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:49:12'),
(1748, '3c90e74dc6c33fb32869a59d158fe76119c63ac9742207b25c593dee9788ff57', 3, '2026-04-02', '2026-04-02 19:50:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:49:44'),
(1749, 'a17be35efe9ca74dc692d52bd9e61f3e459f5ff85fe7cf43363caaf2be319d6b', 3, '2026-04-02', '2026-04-02 19:51:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:50:15'),
(1750, 'f3c8ffa745946fdd2c754891d152fe1af1d1488218e015659cb0eda10516a27a', 3, '2026-04-02', '2026-04-02 19:51:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:50:47'),
(1751, '7741c9837b417be23b37d50dd7d4e6de98167b1d7a4eb7200252791b8222c326', 3, '2026-04-02', '2026-04-02 19:52:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:51:18'),
(1752, '924ebe8843d165d5026c4ff2fc4e8e8c382078cc7f4991d3c3564acbe70b27a1', 3, '2026-04-02', '2026-04-02 19:52:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:51:49'),
(1753, 'd6e2e445114cd37f25e3beb4a0ca7a5bb27278084e41ba0394ba1e490a4b0e63', 3, '2026-04-02', '2026-04-02 19:53:20', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:52:20'),
(1754, 'b391ad8c6aaac39d2d7b9431d1c378c23fe68c5e81b5a709007889d52c1cd5e2', 3, '2026-04-02', '2026-04-02 19:53:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:52:51'),
(1755, 'd11ebfa68ce5f9fa658462436fa4b90984742cfc7990e3e55c8751997d7d0f81', 3, '2026-04-02', '2026-04-02 19:54:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:53:22'),
(1756, 'b38c4e0ab6ff5aa655fd09b0deccdfdd4978d4a6bc79bedb887f9e6225f7a299', 3, '2026-04-02', '2026-04-02 19:54:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:53:55'),
(1757, 'c9a057d58d63d7d0b00d4f84b33a744dd9b3f6d7cf8a449f45470d4ee878a69b', 3, '2026-04-02', '2026-04-02 19:55:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:54:26'),
(1758, 'c4069d579c6528e2c1749764d2c71ef900b74549affc7b427524035075a519b6', 3, '2026-04-02', '2026-04-02 19:55:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:54:58'),
(1759, 'e6c12e30ccae6c5e7e03c64dc7aef2b8254beeab4e481f7b699dcc8400043131', 3, '2026-04-02', '2026-04-02 19:56:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:55:30'),
(1760, '5f73a4aed368491c6d504846ad7bace20c642996ac719b116d726737c2594de1', 3, '2026-04-02', '2026-04-02 19:57:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:56:01'),
(1761, 'f6a666549ca5b201d27256f091c7baaf0a9d341f55f67dc16958055016e017ed', 3, '2026-04-02', '2026-04-02 19:57:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:56:33'),
(1762, '6de40807509eb16471927ba3580b0e7c00045679fc0a36fe53b8a0dc106023cf', 3, '2026-04-02', '2026-04-02 19:58:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:57:05'),
(1763, '1081ed57b5a7e201527866edf889e0215c56176af17fc533a064b121b0c8e93c', 3, '2026-04-02', '2026-04-02 19:58:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:57:36'),
(1764, '40489e21526bf7b5978aa5458f2d8f8ec877ddfa4ade6dfd3c1bc4fabcf59cc3', 3, '2026-04-02', '2026-04-02 19:59:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:58:08'),
(1765, '0f7f39d5e62318bad023eb35c3348d062cf06fdec1746a738bed4699613c09a5', 3, '2026-04-02', '2026-04-02 19:59:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:58:39'),
(1766, 'b4ec374f78f5229ef0ad2f5dc8e270f08b11c680566688748f899326a609725c', 3, '2026-04-02', '2026-04-02 20:00:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:59:10'),
(1767, '8530027694c943d5422fc980ce099a77215042213e06b0ef479a67a4140c881f', 3, '2026-04-02', '2026-04-02 20:00:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 11:59:42'),
(1768, '508a86cb20363ecb2fa718d1a5521f2918aded627435b6e8c7e6331d311c60e3', 3, '2026-04-02', '2026-04-02 20:01:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:00:14'),
(1769, '63e57aadd0b75b25dcb295cad6d16e971b770b89954a5a21f2b7353a4a3abc3c', 3, '2026-04-02', '2026-04-02 20:01:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:00:47'),
(1770, 'fd9002c3f4480e1dded87bccaa47d5b34a22430cc17f3b8c012ff9326a885f9d', 3, '2026-04-02', '2026-04-02 20:02:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:01:18'),
(1771, '358c263eba949b863833a928f1888355e7dc94eca72ddab84808c818ab73a54d', 3, '2026-04-02', '2026-04-02 20:02:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:01:50'),
(1772, '4a6682558eb0d977c6641d31498c9373b0601a71cf2f0c88f050c7392db25616', 3, '2026-04-02', '2026-04-02 20:03:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:02:22'),
(1773, '8f4b39727f39a9eecba8b097c767d212500ed451594fa21676a3c55460038339', 3, '2026-04-02', '2026-04-02 20:03:54', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:02:54'),
(1774, '872f74d16f2c34ca94b94d1e565340b147f48c504ca0aca0dd76fa983ac64616', 3, '2026-04-02', '2026-04-02 20:04:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:03:26'),
(1775, '91b96cda587a10a3aae6da7bd90ed138cf0b8e3812474a7df2543ee9c9d3bdb0', 3, '2026-04-02', '2026-04-02 20:04:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:03:59');
INSERT INTO `ta_attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(1776, '789590a5807b7a51cf5392ad4f6e04acb8f3ee21e2d2898721cc08e6e28b54db', 3, '2026-04-02', '2026-04-02 20:05:30', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:04:30'),
(1777, 'cbca74d9aaf5840875bac4b0a145c816f5ccbb5a4e750d3845ee5634f47e7cbf', 3, '2026-04-02', '2026-04-02 20:06:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:05:01'),
(1778, '6ee35a028d4bf75e365cedd5d3a708a62bb1a2c78abfc1ddf0a695cb63f5b0db', 3, '2026-04-02', '2026-04-02 20:06:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:05:32'),
(1779, 'b6507e9f9ab88633d78d73b2951b2021f6529e00df20395b424189aa247abc6f', 3, '2026-04-02', '2026-04-02 20:07:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:06:03'),
(1780, '352e93ba23bab9575a487bb78e0ca343169ccd40dad38d401c85402c6a95e1aa', 3, '2026-04-02', '2026-04-02 20:07:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:06:34'),
(1781, '69bb275e9f13ae8ce63f6c21ddb98b32cc00fd27845543bc559a7cf0eaa8c801', 3, '2026-04-02', '2026-04-02 20:08:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:07:06'),
(1782, '810f73c0a2c3531b684ab79598cba60cb627c071bfe2cf8fcad94332df47681c', 3, '2026-04-02', '2026-04-02 20:08:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:07:43'),
(1783, '6a776f59b16e87177ec6e9547c9b4b05bc1ab993da9fd6d4c9cff72b533e3148', 3, '2026-04-02', '2026-04-02 20:09:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:08:19'),
(1784, '6a7ef94a6910a8e940d1bce2cedeafc8270a1373186df3c00108c3a60f8f9506', 3, '2026-04-02', '2026-04-02 20:10:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:09:00'),
(1785, '913842122f969f61b34b889bb8f3d39ea814ee8a9e2ccf58c56286b19ce5fd17', 3, '2026-04-02', '2026-04-02 20:10:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:09:33'),
(1786, '5b6502ab336b3907c0d6162899cca673192aa1e1d1aca3baed9e484a110edd9d', 3, '2026-04-02', '2026-04-02 20:11:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:10:05'),
(1787, '2105ff76a91d0e6980f5861ecdc30447e45eaed3c242fb2a03eb27eda8ee9d67', 3, '2026-04-02', '2026-04-02 20:11:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:10:36'),
(1788, 'cf1d76bb281ac37bdfc7d7133885e20c14a7db17a5cc129b2ba79cbf88fb8964', 3, '2026-04-02', '2026-04-02 20:12:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:11:07'),
(1789, '0aa608abed0f8687a29e23f8064a5e33c88f28b569034302870925d882616dcb', 3, '2026-04-02', '2026-04-02 20:12:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:11:38'),
(1790, '61eca16d213802fca723adf0a80adeb5a42037775eaac3a574c90cd83c4b1923', 3, '2026-04-02', '2026-04-02 20:13:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:12:09'),
(1791, 'fb69a1eca0cd01f82311289b549ec2a755bfa08d0ceb85f101c61dee61432987', 3, '2026-04-02', '2026-04-02 20:13:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:12:40'),
(1792, 'e2810047a2d07c392dfd18f12ddc940b16d1711c63cbab0068b078d85cb50e86', 3, '2026-04-02', '2026-04-02 20:14:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:13:13'),
(1793, '9db560cbc8f718fef1bc87cdd725a60506db9de54fafe17e5f3c42778ccf4b7a', 3, '2026-04-02', '2026-04-02 20:14:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:13:46'),
(1794, 'cb06859a4088dc5bf23b3818a0eebe0a1c2ad5c594bb3f4b2859a0c8332dda56', 3, '2026-04-02', '2026-04-02 20:15:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:14:18'),
(1795, '9f703863cb5bd0eb7b4e8034ed1c08367eccba739de7cddecf21c452c2e81d13', 3, '2026-04-02', '2026-04-02 20:15:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:14:57'),
(1796, 'b19567a210e5546db8724a276abce5d2e7817e8b61dd7ebb69419183a67d9370', 3, '2026-04-02', '2026-04-02 20:16:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:15:29'),
(1797, '0c2d8153f44563308de9c27c6a0af8eac8a086e5bcd8961284c2bb93370780dd', 3, '2026-04-02', '2026-04-02 20:17:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:16:03'),
(1798, '7b5fd0dd11b1adeef83b60dfe40faab061537763b61da41673ed5fe8d47736ca', 3, '2026-04-02', '2026-04-02 20:17:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:16:37'),
(1799, '2da4d3a5e2ef10b57372bfbf72799ffcf1102724bfddf8c8dcb71401edef1d27', 3, '2026-04-02', '2026-04-02 20:18:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:17:11'),
(1800, '390e8e9a228d27aadd8bea128fc6769c32d404dbbab2838495d2ad56e549bf8b', 3, '2026-04-02', '2026-04-02 20:18:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:17:42'),
(1801, '7e036f7becb89f5edd6dce3008fe986272de63339c7001b5f76361c82bd5b051', 3, '2026-04-02', '2026-04-02 20:19:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:18:16'),
(1802, 'e67edec0cf9bbdb51d68b12398cce74ea9747f3b68fe18e10df77501f31d1d8f', 3, '2026-04-02', '2026-04-02 20:19:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:18:48'),
(1803, '5546ad25b312a3a2efa8b0d5a59e7dc1964828383e6092066ea9460b277aaff2', 3, '2026-04-02', '2026-04-02 20:20:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:19:24'),
(1804, 'd53db5fc9aaec3b2cbb35159002546ef8762bf553c2629ad97651b31e5e42a70', 3, '2026-04-02', '2026-04-02 20:20:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:19:55'),
(1805, '0484b41d8df80c16ee35b94e6150a098f16cd8587cf65e97ba6e50995eb1d1c9', 3, '2026-04-02', '2026-04-02 20:21:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:20:27'),
(1806, 'b341da1a282558d1cd279d1e00d0c04209b884099d37eb42c6c68b3b25a5b6ed', 3, '2026-04-02', '2026-04-02 20:21:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:20:58'),
(1807, 'f2e2743cea1e3f5ccd4209b2ec7d3a70070561ebbe6a73cb669d3e2b107d6fe6', 3, '2026-04-02', '2026-04-02 20:22:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:21:39'),
(1808, '84e8fd2f25b57658e9efd86343cfe018db322fb024891d121c4ecff05240330f', 3, '2026-04-02', '2026-04-02 20:23:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:22:17'),
(1809, '6bcfed15aad4f0a786c18eb43c48b82115929f9c0d3f8f7922f1daee6f7d7f66', 3, '2026-04-02', '2026-04-02 20:24:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:23:00'),
(1810, 'be7c72f09e3fac71dd760a9b830cf573eee5d80c1aedf0301c5987a85048cfd8', 3, '2026-04-02', '2026-04-02 20:24:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:23:35'),
(1811, 'b05ec3605b469893261b3ee5664da2a85c463fbba84f79aee601f3b9669a0a8d', 3, '2026-04-02', '2026-04-02 20:25:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:24:07'),
(1812, 'ed2c7a8169f3d3a7a197abce672063aaa83ed815bbb6c1113a808ea3e52e5ebb', 3, '2026-04-02', '2026-04-02 20:25:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:24:42'),
(1813, '7f80039d43f0e32e1ada1b9cf7d76bf24f5a1379fcd90a73a6b3dbbf1aeddbfb', 3, '2026-04-02', '2026-04-02 20:26:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:25:13'),
(1814, '017466dd0d9c2ef8ef27feada602e71a3ca52ac7994eabf52ccd17d3c2657d81', 3, '2026-04-02', '2026-04-02 20:26:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:25:44'),
(1815, '042ae19bb46d16fa4a8b616a80c24d3fc13ea9b582c8af60b8811dfc36b583a4', 3, '2026-04-02', '2026-04-02 20:27:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:26:15'),
(1816, '132dc71f96aa6ce3eb9722970b7300a62a1fdb7663a9091b7625233b5d38fbb4', 3, '2026-04-02', '2026-04-02 20:27:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:26:48'),
(1817, '9457b9f22c74777f28931737033326813a1fc0188b438d0796b9e29960fcf344', 3, '2026-04-02', '2026-04-02 20:28:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:27:19'),
(1818, 'affb5af61a31a53c47491b00a67fbceaad6df3f7fed3063dfba828d4e71a80ba', 3, '2026-04-02', '2026-04-02 20:28:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:27:56'),
(1819, 'dbc4e2462327ca2ba862b783882930d656966d5c10c3cfa1b910de41199549ea', 3, '2026-04-02', '2026-04-02 20:29:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:28:29'),
(1820, '71c56d27ee8c5fdc463e5f8b0db2c22a9e9d5c7b32255bcdd45c75a276c76e5a', 3, '2026-04-02', '2026-04-02 20:30:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:29:01'),
(1821, '90b5e6bc3fc38128f7268916cfe7da6087c45b9c86437a86509e8c6c717413d2', 3, '2026-04-02', '2026-04-02 20:30:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:29:35'),
(1822, '9b3e2f2aea15f97bcf88360979e58a8559ff59daad38b7c9bc544c109d6a6ea8', 3, '2026-04-02', '2026-04-02 20:31:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:30:06'),
(1823, 'f7dbe89b0b0170ab67e62b32da6b3feb8819eaf6b59a8e70ff1131a2a21b0370', 3, '2026-04-02', '2026-04-02 20:31:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:30:37'),
(1824, 'f6cd57f42929cf8216e91f05378aca6bfcbef034e7d4c6610f3ce25912652466', 3, '2026-04-02', '2026-04-02 20:32:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:31:08'),
(1825, 'db5af590a13167ee6e6dd486956b2fb44833d34db33c32a7d727488d002d4e01', 3, '2026-04-02', '2026-04-02 20:32:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:31:39'),
(1826, '7c54e9b8d0f4b41a8f3c177d5dfad5e2cee709e3dedbcfab0add3f00a41b894c', 3, '2026-04-02', '2026-04-02 20:33:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:32:10'),
(1827, 'a71ad40e3d01274d1279eb660cccb5e875a114eaab8676bc6951ac7b7b342d6c', 3, '2026-04-02', '2026-04-02 20:33:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:32:43'),
(1828, '1fa8be345843d79a0b7a32530c3f5555fcd3679478e815c9beb194d375ee54f4', 3, '2026-04-02', '2026-04-02 20:34:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:33:14'),
(1829, '8d0ca088c09c4a568eca58f339a97dd8ff6216a93fcb1144e32dbfb523329bf7', 3, '2026-04-02', '2026-04-02 20:34:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:33:48'),
(1830, '6fee954d14323378b0e3f486e1d587d1867a331e5dda66a2d6edbd7de9160cbe', 3, '2026-04-02', '2026-04-02 20:35:23', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:34:23'),
(1831, 'd92b043740005b91533c9680f47e4cf67e4bd198966674f333c182b1d9d7f910', 3, '2026-04-02', '2026-04-02 20:35:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:34:57'),
(1832, 'f9d0a6110767dda368d870d4a6075dcb14ac482a1008e8b2412d4a0a302be330', 3, '2026-04-02', '2026-04-02 20:36:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:35:32'),
(1833, '00da779aab6ac916ecc75b72e29e44d018cef312df342501a0ed7e89a2d46e4c', 3, '2026-04-02', '2026-04-02 20:37:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:36:03'),
(1834, 'c4700d8735d6249772932d52e623c6e0bbb286212ab2ddad1d9dc698a5e7915a', 3, '2026-04-02', '2026-04-02 20:37:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:36:37'),
(1835, '26a2026c0e70321addc434fcf2dd0eb142e532991522013bd444ffce39889aed', 3, '2026-04-02', '2026-04-02 20:38:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:37:08'),
(1836, '377bcfb4dae2d55a891461fe64a8e42671defb2fe56908f07c84cfd6886f1012', 3, '2026-04-02', '2026-04-02 20:38:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:37:42'),
(1837, 'a46a8fb5eb16653640e940406239cca6c1a267d6bec2db8b01487de9f1606002', 3, '2026-04-02', '2026-04-02 20:39:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:38:13'),
(1838, 'a6fc87fb5a55fba9a6e0c8e5944bdd92de427edd72c9fad2de619cacf5de747d', 3, '2026-04-02', '2026-04-02 20:39:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:38:47'),
(1839, '776e91617c89c329ef4bf1ce3be4ae9269a76048e87eb81066202803641af0a3', 3, '2026-04-02', '2026-04-02 20:40:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:39:18'),
(1840, 'e8d1418a38795cfbcbcb3fbe9db4c9147073a9c7c185ac07ecba24e9ce5bc651', 3, '2026-04-02', '2026-04-02 20:40:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:39:51'),
(1841, '23274446b2450be846969b2559730002dfc4855a4b16e5ca2534395f044af9d8', 3, '2026-04-02', '2026-04-02 20:41:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:40:24'),
(1842, '3e2ff7116f94a16cf6b0432658552eed7406df684a2f6cb1498b3af0735c108e', 3, '2026-04-02', '2026-04-02 20:41:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:40:59'),
(1843, '40d362cee7b14c43c3fdb81d8dd73691cfed2b36d1cd758b45898b1a26b52246', 3, '2026-04-02', '2026-04-02 20:42:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:41:33'),
(1844, '854101637ed1dc029b31fa95667f0a348bf6a1168c1dbe96efc010a658f8f491', 3, '2026-04-02', '2026-04-02 20:43:04', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:42:04'),
(1845, 'fb46c2df79a376718caa3c4ceb1288b69f5b2d2575e513176544517d7c927bd3', 3, '2026-04-02', '2026-04-02 20:43:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:42:38'),
(1846, 'ed0437630bbce5b6c4e09a6e0bb422757f06e066a88fc8eb8fe2c027b837c8c9', 3, '2026-04-02', '2026-04-02 20:44:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:43:09'),
(1847, '949643ec2f317885cf3439643b3477bcab8d50c370c015da2726d3841ce8d68c', 3, '2026-04-02', '2026-04-02 20:44:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:43:44'),
(1848, '7e655fc36efef74e7e5155986188872d62e9cd2d83e46b72ccb25ddf4d36f8d4', 3, '2026-04-02', '2026-04-02 20:45:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:44:15'),
(1849, '4b95c62731c793cc9f3391bd82b2096038efb3e82eb790f7a5a6cb91233c8958', 3, '2026-04-02', '2026-04-02 20:45:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:44:50'),
(1850, 'bd339e0a062bce8b4d89a0536b671d3e2efccd0acf31c5f5228f7411ae4a9c40', 3, '2026-04-02', '2026-04-02 20:46:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:45:22'),
(1851, 'f932297d7f0486bfb4667b109080077fdca0f38147b9ac18966703621d24d790', 3, '2026-04-02', '2026-04-02 20:46:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:45:55'),
(1852, 'd1916ac9707e49b92b256cb66f06a987680b605de11f4666e53fb831f61e9d64', 3, '2026-04-02', '2026-04-02 20:47:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:46:29'),
(1853, 'e0d44549e8977bd5de886b15c65e92b2fe0d46085369559e797f396f391289db', 3, '2026-04-02', '2026-04-02 20:47:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:46:59'),
(1854, '6931a24e9bbd0596ed8aed22c527964edd480a7d940919cb7b556ba7f6fa309e', 3, '2026-04-02', '2026-04-02 20:48:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:47:32'),
(1855, 'c54ef4370c047c3e6d557e416f534569b3476bd552b13eb2dc366c29c1f6720c', 3, '2026-04-02', '2026-04-02 20:49:04', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:48:04'),
(1856, 'b84f39df3fe8acfae54715333e0a04b59e09834c1f6b6cbde8068fa6e9ecd62e', 3, '2026-04-02', '2026-04-02 20:49:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:48:38'),
(1857, '7937c84a52faf932a5a5db26d30d609908f3e1f19706c6f3ce7ba0eeb1fbe53e', 3, '2026-04-02', '2026-04-02 20:50:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:49:09'),
(1858, 'a14d46a576b37a4d51e61afee370b61f1134d03b6be91ea3ded64c7ad7f2df80', 3, '2026-04-02', '2026-04-02 20:50:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:49:44'),
(1859, 'f8922c3ea4ce207e73b9542b226abedf080daa9f24a281a6608a24efe635d654', 3, '2026-04-02', '2026-04-02 20:51:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:50:15'),
(1860, 'de4d5179c340f3550ac22b6aaeec381ad047b7b45bf5cb478fb2ef94463905d6', 3, '2026-04-02', '2026-04-02 20:51:50', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:50:50'),
(1861, 'e04fa0b076c58ac6bef96f241524c25593b29ac18c9f81720671d0c2fd641f46', 3, '2026-04-02', '2026-04-02 20:52:21', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:51:21'),
(1862, '1dcdaa29e261f25d36b179c3f1c3cdbc763c296a0357cfc20c58457a2bb22029', 3, '2026-04-02', '2026-04-02 20:52:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:51:53'),
(1863, '748a5cc644943ac3570d32e9b83cea714cefed7f87d21b8c2f31ff9446c82ef5', 3, '2026-04-02', '2026-04-02 20:53:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:52:24'),
(1864, '57b069920126eb4f6fe44e5ded7341ec63d99656fe0aed6e557f2f2ca8609937', 3, '2026-04-02', '2026-04-02 20:53:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:52:58'),
(1865, 'd1a6c405d17199dcebab480b4d1aa5cd539bd6d0ccb4667077822d4cc65b6bdd', 3, '2026-04-02', '2026-04-02 20:54:31', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:53:31'),
(1866, '8f12791cf5196953d16cac3ccca61d31fb8beb4272ceecc6f385a1e747f210bd', 3, '2026-04-02', '2026-04-02 20:55:02', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:54:02'),
(1867, '865931dc5151d565539c1fcfda09ab2e4d5e140e41b7d2355969752d62ed7f98', 3, '2026-04-02', '2026-04-02 20:55:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:54:39'),
(1868, '1b0019dd54ff8f9174e1f032df0933fcb8cdf9b3a0b8516b9b6e91625a0fbe46', 3, '2026-04-02', '2026-04-02 20:56:16', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:55:16'),
(1869, 'ec3f825fd40c22711ef51efc0adc0d3902509fe289f4ff65ea323545f6339617', 3, '2026-04-02', '2026-04-02 20:56:52', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:55:52'),
(1870, '018afe8d8c2af38241ab9d75cee229a6a7b10062b3bfdc2812189feb2c20996d', 3, '2026-04-02', '2026-04-02 20:57:25', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:56:25'),
(1871, '8bf0fd313216b12746bbba2b025d05ebbb0c3f8e1f22cad5755a99e2f6c3f384', 3, '2026-04-02', '2026-04-02 20:57:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:56:57'),
(1872, '7d33f772896580be4325f18d1876a9cbf8b361eef3b688b17396fb2501f55603', 3, '2026-04-02', '2026-04-02 20:58:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:57:36'),
(1873, 'cd9b15d0cb9b2134bf8f0c266041ee3ecec3c4e5178b11a5cfaea0015b79040c', 3, '2026-04-02', '2026-04-02 20:59:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:58:08'),
(1874, '04af3835b6e297fae16e527eceb1f77fa787467a2604f6070dcb8b852ea78fcc', 3, '2026-04-02', '2026-04-02 20:59:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:58:46'),
(1875, 'af6b21613deb51f7d59d6a9f50aa125f5a53427ea504611ee03e069ad7effead', 3, '2026-04-02', '2026-04-02 21:00:17', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:59:17'),
(1876, '6831da425961069a125875df9fe1f30b3ebe2e9e46a99bfe9bd02953f74cb6d5', 3, '2026-04-02', '2026-04-02 21:00:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 12:59:51'),
(1877, '24df4da5d00825616c3cb2c31f173dee086eb29d355904dea176d2bfac24f9df', 3, '2026-04-02', '2026-04-02 21:01:23', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:00:23'),
(1878, '5e395c6ec24ad879266d21a90c3c05563a058f57283450616fe80c62bd9a37ff', 3, '2026-04-02', '2026-04-02 21:01:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:00:55'),
(1879, '2b8b3bb4547b0a5a9c6197b389c3f88db27442d70a0898926ecc8aca7be3a204', 3, '2026-04-02', '2026-04-02 21:02:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:01:29'),
(1880, '5a5bfa2bcb5e15c84a8785a19ae379bc87acb9afafc025de31825114c88b25df', 3, '2026-04-02', '2026-04-02 21:02:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:01:59'),
(1881, '8d97e14cee52e75e6fc592f005f0bcc0b75b7a948675a1d7126893be4c9ba9d4', 3, '2026-04-02', '2026-04-02 21:03:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:02:33'),
(1882, 'd545264b658eb9ca222e3f7aeaff9759a91fb93e9a250ab6703aed2b84109e67', 3, '2026-04-02', '2026-04-02 21:04:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:03:05'),
(1883, 'fd06bd2e2e7e467f798e2e59a579ed4d95eb6ad70fc8384b2df0b5341173481d', 3, '2026-04-02', '2026-04-02 21:04:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:03:39'),
(1884, '95127ae81ac1a41b803c47b214235f410e8de53ad435ad015ac2d0e232379457', 3, '2026-04-02', '2026-04-02 21:05:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:04:10'),
(1885, 'fbe95b512e2afac0eaac06d1c8833a394858981983bd5d0407644755599559b5', 3, '2026-04-02', '2026-04-02 21:05:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:04:55'),
(1886, '9202b96072e57141b701d66fab5d9aba7eaca4fce5e3829895362ab1ff60f00d', 3, '2026-04-02', '2026-04-02 21:06:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:05:27'),
(1887, '35ba4fcce009b35a72d40865ec742a10e7e8a94e3412acd93db37db54333e6d5', 3, '2026-04-02', '2026-04-02 21:06:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:05:59'),
(1888, 'b44d24fecdec65f4fd2450ce47a60faadb9894e4f5eb765a2256e793997d9f0a', 3, '2026-04-02', '2026-04-02 21:07:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:06:32'),
(1889, '85f3e5b136b49bf3c7d1b0704a815aa8cb8608e8642d3f8a0d98d6f8ba82aec0', 3, '2026-04-02', '2026-04-02 21:08:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:07:03'),
(1890, 'b922336c5882c760488670cf232eba565654e40ff20b95ae617a0156aede31e6', 3, '2026-04-02', '2026-04-02 21:08:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:07:37'),
(1891, '23461a9863ffabf2bcf60863e9c4691466dec4cf4f3026144b11020e06fc3bbb', 3, '2026-04-02', '2026-04-02 21:09:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:08:09'),
(1892, '1ae06005c49aa597100545e60262d019c6e8460fe26a49fa6a0c318f46f08d9e', 3, '2026-04-02', '2026-04-02 21:09:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:08:42'),
(1893, '8357c7bf86a72b6ad71be16b81b738a03c14ba41c09675cea2e1dd1b0302117e', 3, '2026-04-02', '2026-04-02 21:10:13', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:09:13'),
(1894, 'b8d8e8e13851bb5dab42854cd400d76b7e2aea2cda4f7e78e90715804d15b5b7', 3, '2026-04-02', '2026-04-02 21:10:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:09:49'),
(1895, '876f7be76746d64a604a91ec9669d8562904a488ecd4ac69cc2c1e81957dcf1e', 3, '2026-04-02', '2026-04-02 21:11:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:10:19'),
(1896, 'c515c3c9152ee36c1b5570cfc5b7790688a0347ac40eecf2acdd7714df1b1adf', 3, '2026-04-02', '2026-04-02 21:11:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:10:53'),
(1897, 'aac33ae6c698b88e885f6150a7ed2786d40389364661672bee5f06ec181cf48d', 3, '2026-04-02', '2026-04-02 21:12:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:11:26'),
(1898, 'e2ee00afa0fa2df57e119e7b30dec4016ff08c7a30a5291e8705658b10f7be2c', 3, '2026-04-02', '2026-04-02 21:12:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:11:59'),
(1899, '1d95b93b111dc64822860cf5e86ec600a7b7d2189c040f6880b8674a2535e483', 3, '2026-04-02', '2026-04-02 21:13:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:12:36'),
(1900, 'eb4f1dea968cef27a035a9d128ad18e51d008d7a2043720c498999e5abc5e5ab', 3, '2026-04-02', '2026-04-02 21:14:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:13:08'),
(1901, 'a260c1cb2d872f171ee0dd9f8cdf476a59ce2364fb0915c7650c3b2d862bf700', 3, '2026-04-02', '2026-04-02 21:14:42', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:13:42'),
(1902, '794022714c7ac8a302a6eab198d0ca7bd0c90254827a8973dc7dd141d1f5445d', 3, '2026-04-02', '2026-04-02 21:15:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:14:14'),
(1903, '31ff58439abc440724a3c0b61c3ffeddb4ec279d37bcbfe675003f50a161577b', 3, '2026-04-02', '2026-04-02 21:15:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:14:49'),
(1904, '0b061478d15a3dec98376be8983490b280437ef810fce20d7d8170879f58ca17', 3, '2026-04-02', '2026-04-02 21:16:21', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:15:21'),
(1905, '0470a65e2fbc95a7be800e511979788803297e2c2378305abaa2cf225d2bf41b', 3, '2026-04-02', '2026-04-02 21:16:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:15:56'),
(1906, '7a29b6f4c1add230e1b709875cad421d879d52e120d7e7ddbaf5ea1c6116135f', 3, '2026-04-02', '2026-04-02 21:17:28', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:16:28'),
(1907, '8759f57b9061dd17ac6f94339ed08f0bcc11dc35223e40b1494a25e8eaf2101d', 3, '2026-04-02', '2026-04-02 21:18:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:17:01'),
(1908, 'dea5b4cdf980b21253c59e10b6a5b8b4efd96df10eb06d623c1537305b4dd4ed', 3, '2026-04-02', '2026-04-02 21:18:36', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:17:36'),
(1909, '48312862cabbf812d6fa8cfc4efa2c8c4d551f59d0ceb012ca003db0654f9039', 3, '2026-04-02', '2026-04-02 21:19:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:18:07'),
(1910, '745f0bde21091dc3821f8e7c78843b6f1a0c0afff2b5e6a74683870a902e61e1', 3, '2026-04-02', '2026-04-02 21:19:43', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:18:43'),
(1911, 'd5c4fd1dc90f24f77ff89514676068971d84b3a921e6419ee58f1785472c0bf4', 3, '2026-04-02', '2026-04-02 21:20:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:19:14'),
(1912, '4430969ec5ad110817d62703bbfb72b58b1b20182d54c4d6add053e20bcc5979', 3, '2026-04-02', '2026-04-02 21:20:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:19:48'),
(1913, 'bccfc019dc3cb67eddd329c700cba9d5c806ebf29153fb57fbdfbd59785bae71', 3, '2026-04-02', '2026-04-02 21:21:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:20:19'),
(1914, '949015db2fde55687e802d83a7b30f0c099e555e22c9985285a6dd4b586970e4', 3, '2026-04-02', '2026-04-02 21:21:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:20:53'),
(1915, '099e9310af9e071f96a3190ce92f3e7a5e48651209fcff4b87801dab8550dfe0', 3, '2026-04-02', '2026-04-02 21:22:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:21:24'),
(1916, '57a2c13fd7be01826703508b6e9ae967e0f230ae19934ab1c991bd0fa0a500ef', 3, '2026-04-02', '2026-04-02 21:22:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:21:58'),
(1917, 'd8f4e87118f5c94a36f939a30f9acbd11dd678c5dbce5e07b3b537618a938708', 3, '2026-04-02', '2026-04-02 21:23:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:22:34'),
(1918, '04cda31ff0cef497d7136382ca9121a444e48ae1e09ad966764e1ec9fa9457eb', 3, '2026-04-02', '2026-04-02 21:24:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:23:07'),
(1919, 'da42bbf2fe357a617a5fba3aea2e891365485407c33a670a695ca9ca78b1c9a3', 3, '2026-04-02', '2026-04-02 21:24:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:23:41'),
(1920, 'ed671427f72c50f8161ce7435fc3655f0acea87ac4c68958e9ebe191c21fdfe9', 3, '2026-04-02', '2026-04-02 21:25:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:24:12'),
(1921, '6dbaed53930f33ff94f9afb931884436040aa7b4f428a757e2b762cbdcb37172', 3, '2026-04-02', '2026-04-02 21:25:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:24:49'),
(1922, 'ed956606e60cf395ca1fe39ee3eddbaf63b19a797b1d0b1f20b57e08fe1d6943', 3, '2026-04-02', '2026-04-02 21:26:20', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:25:20'),
(1923, 'e7d55ca44c1a11b231f3b87db9b9e0c4032d47b68702e7a94fabfb4958dcac56', 3, '2026-04-02', '2026-04-02 21:26:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:25:55'),
(1924, '86a1a14ca8857d3f53e81aae189ab57e4e1be740300455c2769f2b2a5fae6236', 3, '2026-04-02', '2026-04-02 21:27:28', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:26:28'),
(1925, '5120d6d5941b4e547d1d81a982ade7db7d67e266a16500b37e124e0e732b29cb', 3, '2026-04-02', '2026-04-02 21:28:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:27:00'),
(1926, '4bec600c28daea025681227a4b682969000d205ae90f8e0428326a674f6e0c5c', 3, '2026-04-02', '2026-04-02 21:28:54', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:27:54'),
(1927, '416f05dcd74cb7207026aba8a9e58991e8e8d1ba1351f526904fc1f60a6d314a', 3, '2026-04-02', '2026-04-02 21:29:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:28:38'),
(1928, 'ac246b078ae0c34cc53950a4048f17b971b5f4f6ce842270ca905edb95a7cf5d', 3, '2026-04-02', '2026-04-02 21:30:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:29:18'),
(1929, 'ff9ea70c512c4b4c84939619b73c3be93e06d9c9d48a890050f467a866b27cd6', 3, '2026-04-02', '2026-04-02 21:30:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:29:58'),
(1930, 'fc0ebe914b88442ad599c59769fe2be48baf61d8e296d50907820c195d370ed4', 3, '2026-04-02', '2026-04-02 21:31:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:30:35'),
(1931, '8bb7aa34b9c7d4c8a504ac0c93aacdf16c892f83615023fff94a46459ae99cf9', 3, '2026-04-02', '2026-04-02 21:32:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:31:06'),
(1932, '5a4250a1e7587545e12c9db03ad46e54baa79f3c9c9a57276feac281643ceaa9', 3, '2026-04-02', '2026-04-02 21:32:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:31:41'),
(1933, '008e06d60ac2e8f5be28a46f1b3aa5787401d20b249ea9b42be7ffb6918767a9', 3, '2026-04-02', '2026-04-02 21:33:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:32:12'),
(1934, '4b616df225d6090056cde47719dd032ffd9c32e4dd9643733ed2d1124a3de2dd', 3, '2026-04-02', '2026-04-02 21:33:47', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:32:47'),
(1935, 'cac9f05a9fbc93a68c8dc16dc3920afd19e779408e0d846b307ad9757a73bd06', 3, '2026-04-02', '2026-04-02 21:34:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:33:18'),
(1936, '767c9fb3224c4a27ffb78eb02a896e69a35d06bf1c456d68f5151f451c2a3287', 3, '2026-04-02', '2026-04-02 21:34:55', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:33:55'),
(1937, 'fdbc31325178076ceabb5c87647dd5368977a6147dc589dd8c0b0047bc285383', 3, '2026-04-02', '2026-04-02 21:35:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:34:26'),
(1938, '0dcb4ed0ff969ec368e88f27bd4fae4ba599b94ea60d5d82192cb346224749a0', 3, '2026-04-02', '2026-04-02 21:35:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:34:59'),
(1939, 'e079c3d26c9e2a047e3b80fccc8fcccefb51d682317dd8d0cb5d98454aa90e87', 3, '2026-04-02', '2026-04-02 21:36:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:35:34'),
(1940, '47034a84dfce99dca3b6d29d3b607b805e97af71c953b85a1dbe45232c45b29a', 3, '2026-04-02', '2026-04-02 21:37:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:36:05'),
(1941, '584f1a6f90ac664be74ac9855d1b78b782209165f0e46257a8b66472f365ac11', 3, '2026-04-02', '2026-04-02 21:37:40', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:36:40'),
(1942, '7b0319c5fcd546d93b4c11139324d0ba4ef75b72672d72fab1dc86e8c276f08d', 3, '2026-04-02', '2026-04-02 21:38:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:37:11'),
(1943, 'a79475c8685b16faab8da03f2b463a28b27ea6f53353df9093a2e9faf22387c5', 3, '2026-04-02', '2026-04-02 21:38:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:37:48'),
(1944, '201d2a9f04a2b3064d4f54cbd02d7a7c072b37e14c077f8f63d887ebdb7f2eeb', 3, '2026-04-02', '2026-04-02 21:39:19', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:38:19'),
(1945, 'adcb3bdb6d95a47ec6622936bbf03b9943a32b4fde9151041c639ee740653a3d', 3, '2026-04-02', '2026-04-02 21:39:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:38:51'),
(1946, '2021ec7024f74de1d5b50c13752eceef6c4048bf7f5d7f9e53a00eb32f01f229', 3, '2026-04-02', '2026-04-02 21:40:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:39:22'),
(1947, '519550356b7fc05a21ae166b0d5fc4687d9bf1dc0ce720155313cbdce8c18ef6', 3, '2026-04-02', '2026-04-02 21:40:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:39:56'),
(1948, 'b8432996e01222aea51047425cc1cbeefdf702517514bb14d6445bdb95f6a9f6', 3, '2026-04-02', '2026-04-02 21:41:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:40:27'),
(1949, 'f02e55b6d8477901eb91c2c7207f926fb68e8f8fadc1929a2c64755a80eb350a', 3, '2026-04-02', '2026-04-02 21:42:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:41:00'),
(1950, '3bb948169083820776d7a4b98e3d44957a5fac7dcf1d986c1c96ce8ed7034a00', 3, '2026-04-02', '2026-04-02 21:42:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:41:34'),
(1951, '5b70288cd2b03201fe75c00a56417e761e7a7e7a2b45b0e651825424e70ecca7', 3, '2026-04-02', '2026-04-02 21:43:06', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:42:06'),
(1952, '4027cc96253e268f6fbcd15825206da0589e62a2952d503e86d4909e74d8a830', 3, '2026-04-02', '2026-04-02 21:43:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:42:39'),
(1953, '4ebf115a84a4735c2286ae996c59cdc28ea60b56ec5f8288fbf872a6701962d4', 3, '2026-04-02', '2026-04-02 21:44:11', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:43:11'),
(1954, '071db6da466e99ffd6ee7c561a6c17423de325b6641e4f6b2c8feb07a38f6207', 3, '2026-04-02', '2026-04-02 21:44:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:43:46'),
(1955, 'f42cd2207f7a894b52f012c7efbd6c89284f132f07bad73f27cc38c0506d3e2f', 3, '2026-04-02', '2026-04-02 21:45:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:44:18'),
(1956, 'cb2238debed785f99069bc2d7b474e651fe34296a2e44ea0c794530e31b08967', 3, '2026-04-02', '2026-04-02 21:45:54', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:44:54'),
(1957, 'f5f6796ea3d79a77cdbef1036d3dd528ab092c60418cdf9a37c636d41ae95829', 3, '2026-04-02', '2026-04-02 21:46:27', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:45:27'),
(1958, '8e0cd635d81fd86573153be85630fa0685c540707740d62b64cbc08f317f5889', 3, '2026-04-02', '2026-04-02 21:46:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:45:59'),
(1959, '2eb81d11ec41b319bfae767fa2fe96d599b0324c65b9e41707cfacca8a2a9150', 3, '2026-04-02', '2026-04-02 21:47:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:46:33'),
(1960, '4cc6140f43047de23e7466af623b44ffffb5c27a097183f639978d5db2f3d7ec', 3, '2026-04-02', '2026-04-02 21:48:05', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:47:05'),
(1961, '9559b7e940a0cda8945813a4009fd3c79d26c467016d6c20aedfaf6e96a199c5', 3, '2026-04-02', '2026-04-02 21:48:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:47:39'),
(1962, 'e6e28f5e02be0cb1a4a703de1b69f0ca0c1e7becf4e251108aef3dc66e9560bd', 3, '2026-04-02', '2026-04-02 21:49:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:48:10'),
(1963, '278847145397713be3c002ddb76b6de2f628dfffdc9b820c6dfee310ce9ef78a', 3, '2026-04-02', '2026-04-02 21:49:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:48:44'),
(1964, '9751debbe966717999db9ffecc62eca70eaf7681d465eaeb10dd68842905e793', 3, '2026-04-02', '2026-04-02 21:50:15', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:49:15'),
(1965, 'c587a4621abcfb72149fd7952b79b539e877c400e62ee0fe8f7740167e325f84', 3, '2026-04-02', '2026-04-02 21:50:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:49:49'),
(1966, '6be7868e38faf1622a1e452b45113ac332b31eeadd5e6238381bc16a916cb49c', 3, '2026-04-02', '2026-04-02 21:51:20', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:50:20'),
(1967, '5d0c53209d8641516832155eb025292081a7ba1e543921a1e688a2b858878ad3', 3, '2026-04-02', '2026-04-02 21:51:53', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:50:53'),
(1968, 'd68550a1ed94b883cefc1c0fed9c9687dcec5208cc1fd441b58edb5976c7afd0', 3, '2026-04-02', '2026-04-02 21:52:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:51:24'),
(1969, '928be75b7baa96465fdb7dc1ab006d4f23e892cf88129d92107e5985458f0b98', 3, '2026-04-02', '2026-04-02 21:52:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:51:56'),
(1970, 'f50fc17e10738fb8f79afd3127563eb8e6928e2c77e0de7d149e30ff19f3566c', 3, '2026-04-02', '2026-04-02 21:53:32', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:52:32'),
(1971, 'd19f68ed420f9739acdd63443c447f675ba09d62a55ba43f53e959adf6b7e53f', 3, '2026-04-02', '2026-04-02 21:54:02', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:53:02'),
(1972, '70bf84278363e7b358b37426f3313f05ae2205fe395151227435ffbf1909aecd', 3, '2026-04-02', '2026-04-02 21:54:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:53:38'),
(1973, '1510a77c2c21528f33cc98168ede01fb1a21a227ab2d06992aff880d4b5172f7', 3, '2026-04-02', '2026-04-02 21:55:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:54:37'),
(1974, '0cf05081f1435cdfe638eb75ad8be26b56631fa440502e941328170adc584d2a', 3, '2026-04-02', '2026-04-02 21:56:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:55:14'),
(1975, '9c689f563364f4ef08f78734f9b5ed01aff29623ba70d90d64115417e8108760', 3, '2026-04-02', '2026-04-02 21:56:48', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:55:48'),
(1976, 'b2c3d587114b85beec972ce5e620fb0a6c82868a8ab1fd09566091fb4ed5e6a1', 3, '2026-04-02', '2026-04-02 21:57:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:56:46'),
(1977, '2e90edb118deaaac360dfa83b60ac0fa40d56dc598eed5912f809831742ffab9', 3, '2026-04-02', '2026-04-02 21:58:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:57:37'),
(1978, 'c6525a7ad0652fe97d52946f5d6e4ac54afc96c97b45fe2fad7aa0651eb85ebd', 3, '2026-04-02', '2026-04-02 21:59:08', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:58:08'),
(1979, '6eefb7eaf98f9fa4f083ec51264f55b65d1435fcd90141b282d8a79ce74fd74a', 3, '2026-04-02', '2026-04-02 21:59:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:58:51'),
(1980, '30c29e7033f2714c59522633e3c84579f8999509e9f9bd921a8a86ec84761b1d', 3, '2026-04-02', '2026-04-02 22:00:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 13:59:34'),
(1981, 'cc82aa900d9b7732f51e130a924a60ed5cd5e258b32dd8e56b48418f2ba51e4f', 3, '2026-04-02', '2026-04-02 22:01:24', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:00:24'),
(1982, 'a572df4deba38f35be16cd88eb545261450059c7578b54e09532e53a173376bc', 3, '2026-04-02', '2026-04-02 22:01:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:00:59'),
(1983, '2503412428eab492a97d906cbd1dfe12a58ae305b34113d138bd85236fac3bd2', 3, '2026-04-02', '2026-04-02 22:02:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:01:34'),
(1984, '16d1988636c9ca7ef9cfd1298214d235e7e3b36efbe82a1b21818a60e0b5144a', 3, '2026-04-02', '2026-04-02 22:03:18', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:02:18'),
(1985, '129d2fe195f66bf84323e516f7d13e022c497df2b689b0271dc61c4f1381c7a6', 3, '2026-04-02', '2026-04-02 22:03:51', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:02:51'),
(1986, 'f2c6158d69cb68a8fc3326a8c4a1235c9984537e270cd39109eaa11c2eb29a45', 3, '2026-04-02', '2026-04-02 22:04:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:03:33'),
(1987, 'b980e9a850e29fa2cf050380124feff7576ccd91e95ada500619896ace44ad4c', 3, '2026-04-02', '2026-04-02 22:05:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:04:22'),
(1988, '7d183d7e92ce7bad367488e4370afd81a491b666448b8a9365b0bf1a6da806e5', 3, '2026-04-02', '2026-04-02 22:05:54', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:04:54'),
(1989, '07c791113de29d942a59772a8efaebdb2532985a83cae3ba782c99f9bdd1dac2', 3, '2026-04-02', '2026-04-02 22:06:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:05:41'),
(1990, 'f92f9a573cf5b24a794980b2c63deeb240317db76be41e9d35c0504673714df2', 3, '2026-04-02', '2026-04-02 22:07:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:06:12'),
(1991, 'a15e609aa7054d44db06fb46fadec6803f5472011f46a6fa678cedeec76686aa', 3, '2026-04-02', '2026-04-02 22:08:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:07:12'),
(1992, '15979658daa3307f2ae68f32226a08bc6858bd834ba12d192265c82811a40b1d', 3, '2026-04-02', '2026-04-02 22:08:49', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:07:49'),
(1993, '72c04b6735d6680baa5694482a2227aca8bf41c9b81b84e22e69f0f8b942612d', 3, '2026-04-02', '2026-04-02 22:09:20', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:08:20'),
(1994, '157a0dce4fe020fd9d83edefb762a966b20d48d412f6fd1b670f583630db9ce6', 3, '2026-04-02', '2026-04-02 22:09:59', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:08:59'),
(1995, '3224f6ce09e1c1a52f775af64e91b616435705a319ddbb285698562537dc75fd', 3, '2026-04-02', '2026-04-02 22:10:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:09:58'),
(1996, 'fb63301898d9bb8d0c46adf522443789956edd29713e4e9d27c0e1639d50f577', 3, '2026-04-02', '2026-04-02 22:11:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:10:35'),
(1997, '7e99b8813663b5cbcdefd8477b7e73a1358f3339cd0e2a7fdcf21981ead86287', 3, '2026-04-02', '2026-04-02 22:12:28', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:11:28'),
(1998, 'c9558310fe6b2411ebe33304d9264fdf13b5ff35e050a7300c12e7c66a8ca3c1', 3, '2026-04-02', '2026-04-02 22:13:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:12:00'),
(1999, '2a34bb7d56aced013be3b7248a8ef4d437871547280871bb430f6313fd6b60d8', 3, '2026-04-02', '2026-04-02 22:14:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:13:00'),
(2000, '2167f3d074fb13c4ca54cacc87840d0cbdfc13531811b5a4187aca038523d536', 3, '2026-04-02', '2026-04-02 22:14:56', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:13:56'),
(2001, 'f3397c32e0e39fc22bfbbfb97a9dfc60e23c628d96e860dd150ccfd07df7dc25', 3, '2026-04-02', '2026-04-02 22:15:46', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:14:46'),
(2002, '369974b1211cdbe05fc7061fdac97a2565f6be0aa77bca9ab05e3227d07ac743', 3, '2026-04-02', '2026-04-02 22:16:35', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:15:35'),
(2003, '7dd652f5b8f10e647dc75d2918df39a115d7bcb98e4a6d80a2db6dffe333e5d1', 3, '2026-04-02', '2026-04-02 22:17:34', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:16:34'),
(2004, '657d6ac0ea9d251fc855a64978a98104325fe0c406b711fc6672e4085367cdd4', 3, '2026-04-02', '2026-04-02 22:18:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:17:07'),
(2005, 'b79d1fd6683954548206c9a97fbfb59d8fd614d574eae3036cf74d546723eef9', 3, '2026-04-02', '2026-04-02 22:18:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:17:41'),
(2006, '704cd06e992729bc02e67c29c826597a734691d63a8421b93c32f2d258e55c0e', 3, '2026-04-02', '2026-04-02 22:19:26', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:18:26'),
(2007, 'f3df5903f0f67e9608232881f25a46b7d9421c1b0a29d3cf758c07f2c650ddb7', 3, '2026-04-02', '2026-04-02 22:20:00', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:19:00'),
(2008, 'a57c487e2e1c8a0a84b4e6c7491832cb4d44cddc1ea8e4396c839d8d742017a9', 3, '2026-04-02', '2026-04-02 22:20:58', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:19:58'),
(2009, '65cd9691b31640376dab254f4afd80fd376ace948ddbb078e3fe8de6b7b0b6e8', 3, '2026-04-02', '2026-04-02 22:21:39', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:20:39'),
(2010, '20bfeb5e30d641d85a81d3a3a3390301b7b360f33e8facd66959b9741d7b0b0a', 3, '2026-04-02', '2026-04-02 22:22:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:21:10'),
(2011, 'af4f2cf770915fbb4b4adafe5a7853ead4c6a381edb0f00d7a18972f4f841097', 3, '2026-04-02', '2026-04-02 22:23:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:22:03'),
(2012, '928fa8af6034f5c1c70a1a3d1a17d5a7d10111c28e2b3f392abb48cd25b96044', 3, '2026-04-02', '2026-04-02 22:23:37', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:22:37'),
(2013, '06730a12884754363ed583d533900cb133c4b0be5a0f6a857f644dd955e832b3', 3, '2026-04-02', '2026-04-02 22:24:09', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:23:09'),
(2014, 'ed3cea0a80bc2ad752589419e06f13f2aa5c79e51b0836f83c6c942ac24f6cc9', 3, '2026-04-02', '2026-04-02 22:25:07', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:24:07'),
(2015, '6c8a81cee59b3dd029c10e307c4e605d00626884c6c3ba7209cdb924c46e2be8', 3, '2026-04-02', '2026-04-02 22:25:41', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:24:41'),
(2016, '503e0b00e102acd320fc4e75f6284d6d7a5f767b3f3c19dfc8bbdafa10197df4', 3, '2026-04-02', '2026-04-02 22:26:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:25:12'),
(2017, '83044f9dafde65a4c440e130416b0501205f99a2178a82db5af134eb0519f4e4', 3, '2026-04-02', '2026-04-02 22:27:03', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:26:03'),
(2018, '9edc267bebd75395ab75200f34aabc8e859fda08a5c024d7e65790435b85f4c1', 3, '2026-04-02', '2026-04-02 22:27:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:26:38'),
(2019, 'd81dab042b3e4d01dd29e140d6149e48556363e785b397c14eecb2b1b775e042', 3, '2026-04-02', '2026-04-02 22:28:14', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:27:14'),
(2020, 'dd42093162746107b53b53929d9b19e5e24438aa34641123414417c04e9f8dd3', 3, '2026-04-02', '2026-04-02 22:29:12', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:28:12'),
(2021, 'eb2f074a64ec1959adfc33bc1dc7ed363dacacebffa2fd1cc9336950800f1656', 3, '2026-04-02', '2026-04-02 22:30:10', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:29:10'),
(2022, '35efd106ba2ec8f645fd6d8045b09c5ef7d46c613f2af2d41edc6002de385d27', 3, '2026-04-02', '2026-04-02 22:30:44', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:29:44'),
(2023, '52cc5f3a0c7704a608a069a71356c487197680decdd4fab2bb485ac97da32ea2', 3, '2026-04-02', '2026-04-02 22:31:22', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:30:22'),
(2024, '1d98b9a3aa684b6c4b2d47b8365d9747cd9b020410e845d73a4cfd381d2830da', 3, '2026-04-02', '2026-04-02 22:31:57', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:30:57'),
(2025, 'b8deb543bc92c3b3ece83d0824d8d820f7614759b1f294c101507dd35c87a907', 3, '2026-04-02', '2026-04-02 22:32:29', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:31:29'),
(2026, 'a346d549da4f3d0ce25bc2055f972ec1601bf5d7e82bae15ac853a2693f4aff7', 3, '2026-04-02', '2026-04-02 22:33:01', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:32:01'),
(2027, 'a5fff6faaa01d5f853ffe4c41559ae56bd3d0667dcba8140eed2c7ee3125731d', 3, '2026-04-02', '2026-04-02 22:33:45', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:32:45'),
(2028, '2551dea2bd4d8d0fdf4370f0c9b21d8eea915dc0bc20f42a772259ec289ee485', 3, '2026-04-02', '2026-04-02 22:34:38', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:33:38'),
(2029, 'eaf8403e44774edc46b55d260caa3c54c057945c8a40de45f0f33fa933092aef', 3, '2026-04-02', '2026-04-02 22:35:33', 0, NULL, NULL, '10.209.193.98', '2026-04-02 14:34:33'),
(2030, '8b4d260275d03f489939f556fa6b21f0a254da405f02bce0d3e88abfa4b67b76', 3, '2026-04-03', '2026-04-03 11:01:38', 0, NULL, NULL, '10.101.190.98', '2026-04-03 03:00:38'),
(2031, 'aafd4497a8a1f17f7fe8284e6983cfaf26729ab5c792e6741c974ed160bffe23', 3, '2026-04-03', '2026-04-03 11:28:22', 0, NULL, NULL, '10.101.190.98', '2026-04-03 03:27:22'),
(2032, '260ac88c7de34abb9edcbebd372f6e05007257c56772245714204a92f8ac0f13', 3, '2026-04-03', '2026-04-03 17:43:58', 0, NULL, NULL, '10.101.190.98', '2026-04-03 09:42:58'),
(2033, '1f25d08e282c3267a335d282a7fc0d8ef0ea672fe2cb8765286183d2f3e21b2e', 3, '2026-04-04', '2026-04-04 18:16:06', 0, NULL, NULL, '192.168.68.136', '2026-04-04 10:15:06'),
(2034, '23396d3a8b4e48bd86e7f7f96c25bb6608d04fd8df212c0655ab0d0ebd47d0cc', 3, '2026-04-05', '2026-04-05 13:46:50', 0, NULL, NULL, '::1', '2026-04-05 05:45:50'),
(2035, 'de8a5afc9768f5d7149da7b712c5c0259f3946d10e6bfc36a4c6b25b62e507b2', 3, '2026-04-05', '2026-04-05 17:59:29', 0, NULL, NULL, '::1', '2026-04-05 09:58:29'),
(2036, '73165bc023956855a72f9041611b56462d75d19d3219beafbb009c859e5738a2', 3, '2026-04-05', '2026-04-05 18:00:55', 0, NULL, NULL, '10.101.190.98', '2026-04-05 09:59:55'),
(2037, '849b107de8d8bac78a8179656958b5ddb240be76f3118cbdc15a24b314b533f9', 3, '2026-04-05', '2026-04-05 18:01:25', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:00:25'),
(2038, '15543ebe9c4a4a18254de2363ab2a9ba48498d252e19b96de181876f05a6c47c', 3, '2026-04-05', '2026-04-05 18:01:55', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:00:55'),
(2039, '01c8d5b98933cd88ecceaec3871a44fc02b8046d2a275b5f774de3d61a856601', 3, '2026-04-05', '2026-04-05 18:02:25', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:01:25'),
(2040, '9d1890a4025cb4d839dec3c8501f277b6d095d1cd1efbab43f4425eddb7283ae', 3, '2026-04-05', '2026-04-05 18:13:26', 0, NULL, NULL, '::1', '2026-04-05 10:12:26'),
(2041, '8fe5accbd71ef212a32ceae59197cec16ba68d5eeb9208326ed4fe0db44613c9', 3, '2026-04-05', '2026-04-05 18:13:50', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:12:50'),
(2042, '6bed8449b0f9a9644f4c563c64cc8bc0d45f2a38bb8e933d3ad98584aab855d4', 3, '2026-04-05', '2026-04-05 18:14:20', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:13:20'),
(2043, 'bee2423b427946746a12982f9a8b06e71dffccbebcf35b88b479f4634a3fc647', 3, '2026-04-05', '2026-04-05 18:14:51', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:13:51'),
(2044, '040fabef8fe5edeec8b9d8f9fc9dbaaa62b971e1c651bfa0cf79fc5554bcdbc5', 3, '2026-04-05', '2026-04-05 18:15:21', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:14:21'),
(2045, '022534a592432c4656c417c17966efc2f4d7abbbaf2743f80c075644d16229f8', 3, '2026-04-05', '2026-04-05 18:15:51', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:14:51'),
(2046, '7e3dc787f8903b7b9cdd20ea355cc76cad14e26371c4298ddb90aa5ca0ba7c51', 3, '2026-04-05', '2026-04-05 18:16:22', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:15:22'),
(2047, '2189d6855850c9bd94f40b463e2a046b42aa31c3abcbb800b56c0b2220c3bd81', 3, '2026-04-05', '2026-04-05 18:16:53', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:15:53'),
(2048, 'd4cf70117efcfa838a39b69c3802c83cbe6c907f7fbe786f3391b6f7ed531e29', 3, '2026-04-05', '2026-04-05 18:17:24', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:16:24'),
(2049, 'fe2edeba6e9772bfeb1d68c537e57f0bf434f713fd7bcf83d1eeb6bd9ca97379', 3, '2026-04-05', '2026-04-05 18:19:21', 0, NULL, NULL, '::1', '2026-04-05 10:18:21'),
(2050, '8af9da386e4deab3f831bd1433550364d970e93ea729c1bbceccb88cacf31b52', 3, '2026-04-05', '2026-04-05 18:20:04', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:19:04'),
(2051, 'f0e70c101e25414ef1c3f91dbfec3683e4916e203944b0420b98985c6f9ab860', 3, '2026-04-05', '2026-04-05 18:20:34', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:19:34'),
(2052, 'c9ffa88eb2a01dd02a3562996486645ade9e3554c358520607ba40905d5a14c0', 3, '2026-04-05', '2026-04-05 18:21:06', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:20:06'),
(2053, 'bb46fce92a2426adf2769084f39de653e6acfd0c63a55330b548398c09a1a2a5', 3, '2026-04-05', '2026-04-05 18:21:37', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:20:37'),
(2054, '355cf00cc6c543d0512fb95749ed4c3eb17b568c53da74500d350edc689d284b', 3, '2026-04-05', '2026-04-05 18:22:09', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:21:09'),
(2055, '5461a20f998bd8d2882c66cc682b368d67ee848bfd39786ae0eee0e1bb1ef7bc', 3, '2026-04-05', '2026-04-05 18:22:40', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:21:40'),
(2056, 'c9109ca0b1ea6c8d911ea6135f13620c40bcbf5ff5a2ed0f88826169f4fa18dd', 3, '2026-04-05', '2026-04-05 18:23:11', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:22:11'),
(2057, 'e053b91cf5920d5bbdf6f29db5ad8f14aaf335c6dc2f93ba307ef6db78a242ed', 3, '2026-04-05', '2026-04-05 18:23:42', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:22:42'),
(2058, '1ed55733015c967d9149ea0199d92261504de1d19286589f9276c73bcbbd04f0', 3, '2026-04-05', '2026-04-05 18:24:14', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:23:14'),
(2059, '3ee6031ba0828dbc6f6ea9ac88fbd9e4c701ad3adaf3c19b213b6c1097e3280a', 3, '2026-04-05', '2026-04-05 18:24:46', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:23:46'),
(2060, '5d6ec93ea2fcff2c7d1e6e9c39a07acd22fbd4a918f580fbf9d001c71905b4e8', 3, '2026-04-05', '2026-04-05 18:25:17', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:24:17'),
(2061, 'd87612351799e0fd899fdfbda9e57da5f1f322fe0989727427983dfad597c4ad', 3, '2026-04-05', '2026-04-05 18:25:48', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:24:48'),
(2062, '876c6b9e19a7e4899c45ad806e04cf56f4670f602821499e6acac68d4c790d2a', 3, '2026-04-05', '2026-04-05 18:26:19', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:25:19'),
(2063, 'a2a39d3c1e1523c06d944a3c6119b11593dd10e973f62d261ddd5a0a30cd4d64', 3, '2026-04-05', '2026-04-05 18:26:50', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:25:50'),
(2064, 'ff116e797312ca967398af3770fd4dc3fed9f8ff06934dbf52280d7b0bf60a93', 3, '2026-04-05', '2026-04-05 18:27:21', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:26:21'),
(2065, '7ad885ccb0cb17f95621f6f3f5bbe4b2f035ce9bfbd1a06a78398a3c472da8c0', 3, '2026-04-05', '2026-04-05 18:27:53', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:26:53'),
(2066, '1313c7e837b09c41fcaa68098ee679d14adc2ca77c35eca716cf8093797b1162', 3, '2026-04-05', '2026-04-05 18:28:24', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:27:24'),
(2067, '7bdbd525deed43404977e85ed83d5e0a0a502e626f2afff32bbb5f7d94c3bfeb', 3, '2026-04-05', '2026-04-05 18:28:55', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:27:55'),
(2068, 'a1e555b2581b148ae1a8aec87562d0e55df07019b1e332eb48d9f2f7221509ab', 3, '2026-04-05', '2026-04-05 18:29:26', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:28:26'),
(2069, '0daa42185f548f50034484b9b5c34f770fcb54babb11810150b4bae7304a35e6', 3, '2026-04-05', '2026-04-05 18:29:57', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:28:57'),
(2070, 'd58b300040df6d1f828ee9167a741634f82d85750a685c5ad11545e3d98b0df9', 3, '2026-04-05', '2026-04-05 18:30:28', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:29:28');
INSERT INTO `ta_attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(2071, '5a9fd747a668361e6e7a7de68f902c3d9b6f6f54cf31972a7be2cef43b5b2a47', 3, '2026-04-05', '2026-04-05 18:30:59', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:29:59'),
(2072, '48d477517f9ab95f70c7f80ed7a7784d684020c56fd15c496e7cc40fcba7a07a', 3, '2026-04-05', '2026-04-05 18:31:30', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:30:30'),
(2073, 'aba617a578b2992c5866448630f308e95bdf5f719e9a1c44f8ca5a7444c50b00', 3, '2026-04-05', '2026-04-05 18:32:01', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:31:01'),
(2074, 'f3446cecbb015178e983b11d1dcd91c730e2d207f9a8588f629e98f78a6f7a60', 3, '2026-04-05', '2026-04-05 18:32:32', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:31:32'),
(2075, 'f420bae77613c7dbce52548df732c896b3dbb14dea38da70376d95ef0f402259', 3, '2026-04-05', '2026-04-05 18:33:03', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:32:03'),
(2076, 'e468c93856b9d77e83aad9b1cadd05a2c04d9a54fe0d5866e750aa66abd776bb', 3, '2026-04-05', '2026-04-05 18:33:34', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:32:34'),
(2077, 'f31305a06ad304da50e9b18e02d66a14ca69035acb4b74841d655d568be97442', 3, '2026-04-05', '2026-04-05 18:34:05', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:33:05'),
(2078, 'df1d9be58cda3ec93d2042f8964789567101155660ec9afb11b7f246f130db8c', 3, '2026-04-05', '2026-04-05 18:34:36', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:33:36'),
(2079, 'c8ed9962e4f6b5b811e2ee4ae76d2e8977672b02fa17c0ae6721be7829646f4f', 3, '2026-04-05', '2026-04-05 18:35:07', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:34:07'),
(2080, '45516c3c337ae03c7f767979f2a6280b9301f67d1526cba9e279c0e87e4fc312', 3, '2026-04-05', '2026-04-05 18:35:38', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:34:38'),
(2081, 'ee18f67418a89404724d620647815218f8f387c9e22e6961826e8d41cee2df6f', 3, '2026-04-05', '2026-04-05 18:36:09', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:35:09'),
(2082, 'ef41e44b0e9621c0715e2e6f1bb9e849324cfb36060a0ed7fed8feec5bfdfc69', 3, '2026-04-05', '2026-04-05 18:36:40', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:35:40'),
(2083, '884fdb1229a19cbb1bb6ebd878dbfdd4d35e5904b7a64e7ada22a892e06e71f8', 3, '2026-04-05', '2026-04-05 18:37:11', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:36:11'),
(2084, '3f796f4efd100f272e4a246de0869294ec5ec815a593e37656740c9231ae9370', 3, '2026-04-05', '2026-04-05 18:37:42', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:36:42'),
(2085, 'f38c5d6163caa17dcd3e62ce44f4a22951dbecccc92454d4a7ef9b7f93a51e77', 3, '2026-04-05', '2026-04-05 18:38:13', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:37:13'),
(2086, '329a04436271f2bef6e0cb6e9f38114688a2abacf230ca4fc8504b6192e0ccc8', 3, '2026-04-05', '2026-04-05 18:38:45', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:37:45'),
(2087, '9d5345eefb283ec21ac7fc748a402740a507b278aeef7b64d73291470fdbbd97', 3, '2026-04-05', '2026-04-05 18:39:23', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:38:23'),
(2088, '12e9e269e9815883de491464496fa32fc5eb6b3fce40038b4e2600fee2e511a2', 3, '2026-04-05', '2026-04-05 18:39:53', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:38:53'),
(2089, '5b697454ce4db1ccbd4ae6912eee5a9a2c0fb28a80277d27f1c92bc66e6089d7', 3, '2026-04-05', '2026-04-05 18:40:26', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:39:26'),
(2090, '78321090870bbba875a00397d3cfb017031022051ac846ca95ac08b5d5e23fb0', 3, '2026-04-05', '2026-04-05 18:40:58', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:39:58'),
(2091, '57de5240cc23e71fc8da810cd0ba0084903f2190412785b2d33036e08bc50b67', 3, '2026-04-05', '2026-04-05 18:41:30', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:40:30'),
(2092, '5a0927cb0db6cf2aaf479524d56b196cadd495603b84eac5324e418662bbfbd0', 3, '2026-04-05', '2026-04-05 18:42:01', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:41:01'),
(2093, '9831ad86492e9b4ae1fd674163ac566005989058277384d0fdc03951435759bf', 3, '2026-04-05', '2026-04-05 18:42:32', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:41:32'),
(2094, 'bbbbac30531f353388cbc1158555dd7e274f176a49c182f5a5395478d12e28a2', 3, '2026-04-05', '2026-04-05 18:43:03', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:42:03'),
(2095, '8a609bb50a81b9f388d059966b504bd44421051b3a3813bec8f57bab179fa9cf', 3, '2026-04-05', '2026-04-05 18:43:34', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:42:34'),
(2096, '96efe6a4895b1ac699347fa3adfa247ebf377eaf085af3ba0eea77665237f008', 3, '2026-04-05', '2026-04-05 18:44:05', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:43:05'),
(2097, '180862ab57a024ad41760460b8927f919b9b128780ce44300b317c978390b171', 3, '2026-04-05', '2026-04-05 18:44:36', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:43:36'),
(2098, '32fbe99e887b2322c01f3d33d6cc35ff168a8f8378209eb094d69d0f49e81722', 3, '2026-04-05', '2026-04-05 18:45:07', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:44:07'),
(2099, '62654fff6bdca22fd79774a37c56278a8cd89a4646f13c6b1df6df1514a2d2a2', 3, '2026-04-05', '2026-04-05 18:45:38', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:44:38'),
(2100, '275924755012889266da24a163e52ef38c364e78eef8461cf8c00de824ad0eac', 3, '2026-04-05', '2026-04-05 18:45:59', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:44:59'),
(2101, '75292533f6e1e69425288db5ac313c6ac7311614f8403514eaacf6f5774212ce', 3, '2026-04-05', '2026-04-05 18:46:05', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:45:05'),
(2102, '31f160611bbd25a82bd8da25d9cf2a6e16cfaf242112a7f8083c2f6657d68589', 3, '2026-04-05', '2026-04-05 18:46:36', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:45:36'),
(2103, '5ec9fbbe5feea690626b00c8df106ae3327397da29d621c6a24c720d617205d3', 3, '2026-04-05', '2026-04-05 18:47:07', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:46:07'),
(2104, '6f46a8840e3d2fc49ea0b044e201a00d0ac974c6e7232afe41405940f0309edc', 3, '2026-04-05', '2026-04-05 18:47:38', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:46:38'),
(2105, 'ee55570418baba3dcb1ca6ad42410e4c41d4f8cefb6ad94f5b4e92d5a51e6047', 3, '2026-04-05', '2026-04-05 18:48:09', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:47:09'),
(2106, '1227bc6192b62e65efa30d7c63739ffe9b92d950d2de30615e96f0c2aca3ce22', 3, '2026-04-05', '2026-04-05 18:48:40', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:47:40'),
(2107, '32d06c4be6d81d101837112ff73d445af3654a108aea66893cbbfde61af20863', 3, '2026-04-05', '2026-04-05 18:49:11', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:48:11'),
(2108, 'f1fabcbd4bcb3850ead16b8d8818f3d96c2579eb06f654b318945d9b77f7e5f8', 3, '2026-04-05', '2026-04-05 18:49:42', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:48:42'),
(2109, 'cf7f64dd229a0d70ab0059d0985544be0c42b578137dc93bc1051437ee70f96b', 3, '2026-04-05', '2026-04-05 18:50:13', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:49:13'),
(2110, 'ffb8c3f60082b76819b0686298f0b5c95ea5b45a0689cbb2384592ca4a74f812', 3, '2026-04-05', '2026-04-05 18:50:44', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:49:44'),
(2111, '245721c0023430e7c4d2403749834dc541b7297be985af41a0bfedfc28098c88', 3, '2026-04-05', '2026-04-05 18:51:15', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:50:15'),
(2112, 'ef723ad447ed311b22e37543c3c8b814d22f70542f6a12bd3f3dae70e7e90748', 3, '2026-04-05', '2026-04-05 18:51:43', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:50:43'),
(2113, '999e96a4f374fc47bb29b09e3c99ef9eba1bdb74e2f1f7855cbb5a20354465e4', 3, '2026-04-05', '2026-04-05 18:52:13', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:51:13'),
(2114, 'b789e16c41f347d6820b96f2ded1ac58cc0a62641366f759546856fde3634084', 3, '2026-04-05', '2026-04-05 18:52:44', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:51:44'),
(2115, 'd3bc69b13034f1234c1bc2ededace9826f85e81e7387c8d6816c9d3b96fa456e', 3, '2026-04-05', '2026-04-05 18:53:15', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:52:15'),
(2116, '0b1e3c637bc0c41cee856ce9ab044c63818d976acfdb6ac5494b0b8b2be64a77', 3, '2026-04-05', '2026-04-05 18:53:46', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:52:46'),
(2117, '8fb26b6ad5b558e22d104fde7c472a9282fabade5fe8eafc0212022f520b2e8f', 3, '2026-04-05', '2026-04-05 18:54:17', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:53:17'),
(2118, 'bd580591f106516400c4e0cd3eb2b4fae434744094c3d4adb3c8d691d735cf9c', 3, '2026-04-05', '2026-04-05 18:54:48', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:53:48'),
(2119, 'facbf46eaeb313db690cce2e90fb1d5cdf303410085d254c5a015bcd94fc9d5f', 3, '2026-04-05', '2026-04-05 18:55:19', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:54:19'),
(2120, '3fdc44d923ef5c1de1915e22f45b6040c183183b53d077388b292207a2e1811d', 3, '2026-04-05', '2026-04-05 18:55:29', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:54:29'),
(2121, '25a6808369f750acff34d1eae7b6da9b69735a8950355c9ddee67b5749abdcf5', 3, '2026-04-05', '2026-04-05 18:56:00', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:55:00'),
(2122, '5d0917cdbbbe0e15ef1a55c5ca72c3ea15fb8ad2d46a745e2bddf2dc7868a4ac', 3, '2026-04-05', '2026-04-05 18:56:30', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:55:30'),
(2123, '7468f23c7b04936994b712de971d1932cbc3e1ab5ec954fbc7f67b715e5070ae', 3, '2026-04-05', '2026-04-05 18:57:00', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:56:00'),
(2124, '2295f59c25fff7ac8d5d649e17859b76863aca65a8ad310b2b67d6dc444ad7d3', 3, '2026-04-05', '2026-04-05 18:57:31', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:56:31'),
(2125, '9a46c89e284a26f636cf4c28dfd1f16be6746139ac4ba0efd3180c829e97ac0f', 3, '2026-04-05', '2026-04-05 18:58:02', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:57:02'),
(2126, 'cc642a8e04e20f7be0dcb52ac2bfce506089b5fdcc1625ed51c8b16e372c7358', 3, '2026-04-05', '2026-04-05 18:58:33', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:57:33'),
(2127, '0badb586c91123dd553485ab34d57b2d2c2beab4fed5868c30382d3ad44d9ac6', 3, '2026-04-05', '2026-04-05 18:59:04', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:58:04'),
(2128, 'bf45f0eca07654ce1b3808538e674196cbe08e7ba3011829295ba1ea991ae01e', 3, '2026-04-05', '2026-04-05 18:59:35', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:58:35'),
(2129, 'f326a1c887ac07d99bc81b421d7e3a3422d5f49c5732dcb6c7b7adf0f3e52475', 3, '2026-04-05', '2026-04-05 19:00:06', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:59:06'),
(2130, 'd4e3cf87cf88d1b641c49f4727e4d49f48d5f5670e1356c380720d935b84da4b', 3, '2026-04-05', '2026-04-05 19:00:36', 0, NULL, NULL, '10.101.190.98', '2026-04-05 10:59:36'),
(2131, 'a0d25982628b7dc257eba887412819746ecd9bd1d28378b0f187beab344aa7d4', 3, '2026-04-05', '2026-04-05 19:01:07', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:00:07'),
(2132, 'd7c1ef0d4f0b3a5319dcbec6abdd465c8b22c0ddccf8134fa9ff0eaf10c6c526', 3, '2026-04-05', '2026-04-05 19:01:37', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:00:37'),
(2133, '484a98574cb6ab9ed8d26af7c4f979ab4b114f432a3eae74f8910b81020a1cc4', 3, '2026-04-05', '2026-04-05 19:02:08', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:01:08'),
(2134, '77fa84652f01cf74c24da516b468077540e7ef33930ec160bca319fde063b612', 3, '2026-04-05', '2026-04-05 19:02:41', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:01:41'),
(2135, '66d7666ee5ff997a01ea3a4f108f22c19317224b15b68eb1ac290be458f73d46', 3, '2026-04-05', '2026-04-05 19:03:13', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:02:13'),
(2136, 'bd4b4ddf2db0ade636d68f15759826c4f5049df2ec8b9c34fbd1936bd8a64087', 3, '2026-04-05', '2026-04-05 19:03:44', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:02:44'),
(2137, 'e2e40033fca17097b40a60d2247b3488aa4d21c80df7a9912e6f89746e16f1c0', 3, '2026-04-05', '2026-04-05 19:04:15', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:03:15'),
(2138, 'd0f9b53c8d45366cbd9eb884c100592ada1efcfe427308b528d5c69c01714ffd', 3, '2026-04-05', '2026-04-05 19:04:46', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:03:46'),
(2139, '38e7db499a33865942d6de84c82fa43716f4a1e07b373723ee21721de7c18ab0', 3, '2026-04-05', '2026-04-05 19:05:17', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:04:17'),
(2140, '2500ea782520710c1f3e355a76c24a13bd0a1e67d98090f20cc73e6fd2a7bb65', 3, '2026-04-05', '2026-04-05 19:05:48', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:04:48'),
(2141, '6bca5e201361a1a788e61df3ace7de75dbfbd7806fd2d4189e832b167faee03c', 3, '2026-04-05', '2026-04-05 19:06:19', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:05:19'),
(2142, '15f04a26232018825a3d0a0ba394b68ff5315ecbd210cc348a50e8604a7b28f1', 3, '2026-04-05', '2026-04-05 19:06:50', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:05:50'),
(2143, '077ab49935d8732a68b9ed50da487cd00e566813a59feb69164eb69fb0a123c0', 3, '2026-04-05', '2026-04-05 19:07:21', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:06:21'),
(2144, 'b3a217ab12668e4243a8c38001292a8f75857a5d52beef11739b0f3b6146aebe', 3, '2026-04-05', '2026-04-05 19:07:51', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:06:51'),
(2145, '9fa55b1c7c55306b330853cec59918ff775c684467164557a4ca3527914ce0f9', 3, '2026-04-05', '2026-04-05 19:08:22', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:07:22'),
(2146, '7e22746cf2c4b588822cc57e492b001814d4e754246b79fadb55b9ceb6f666bb', 3, '2026-04-05', '2026-04-05 19:08:53', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:07:53'),
(2147, '54a2a24822d4bddc0166914b9a236ff3cb4dbff37b2e197dc1cea33fac5a3dba', 3, '2026-04-05', '2026-04-05 19:09:24', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:08:24'),
(2148, '9600dcc4d4908df04572bbb0c271e9c3a0bbd59a103d6b040074b08c7c97380b', 3, '2026-04-05', '2026-04-05 19:09:55', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:08:55'),
(2149, 'b1c2eb37fd836cdc40384f7255741eec01362e72bcbd2e3a5915b645701c41f4', 3, '2026-04-05', '2026-04-05 19:10:26', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:09:26'),
(2150, 'ad7021ffe48a6342179566a369f688529782dc3d3aaa1779ec4908992e338336', 3, '2026-04-05', '2026-04-05 19:10:38', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:09:38'),
(2151, 'c05619a4a21b8353009630ddc8f083e847b49bd60d747617c55bcd73cc13f387', 3, '2026-04-05', '2026-04-05 19:11:08', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:10:08'),
(2152, '1c16534f45e1b2d52678754702969b301c1f389538aef864e2d1844ce2499ea9', 3, '2026-04-05', '2026-04-05 19:11:38', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:10:38'),
(2153, '80281ad1357a552a5abea88f06c69457acb8ee082fb4f8f80bef2e91247b6a2f', 3, '2026-04-05', '2026-04-05 19:12:08', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:11:08'),
(2154, '5f58d817cb5e79f2589d9054e4327fe39e171545369eb2910937c329bf81e1cf', 3, '2026-04-05', '2026-04-05 19:12:39', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:11:39'),
(2155, '03512efad1d62094eb3e3cc383ef727156431734f8666d6f9bc8ccc8c0917757', 3, '2026-04-05', '2026-04-05 19:13:10', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:12:10'),
(2156, '9116bceebc7c7e604b38d797e3298337cb5560639b1093ad7e2dd93d1f616772', 3, '2026-04-05', '2026-04-05 19:13:41', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:12:41'),
(2157, 'a86fa8093e22fac18fabe222b54a708b9dd93b6c70f5ec42857da10e3b28feb3', 3, '2026-04-05', '2026-04-05 19:14:12', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:13:12'),
(2158, '576ac2fc6328ce9649fc22fc5e2cd192103484ac9f5d37ab2fa81d55f0f56882', 3, '2026-04-05', '2026-04-05 19:14:42', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:13:42'),
(2159, '03c663f5283a00b1273c9e1b6e217625039aad7f7baa85acd75e0b1243a5f13f', 3, '2026-04-05', '2026-04-05 19:15:13', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:14:13'),
(2160, 'a0302bea37635370b5ee0362be6dbd39111285caa6241e2d3344cdf918a781ee', 3, '2026-04-05', '2026-04-05 19:15:45', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:14:45'),
(2161, '3e4419e98145a79db1cd13c123d9acbcd677fc601e873dedc0472adf19672a55', 3, '2026-04-05', '2026-04-05 19:16:16', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:15:16'),
(2162, 'c9804c37f3cdc434e702c70a4bb735ef102a36f60d0618008126e50b2df12a75', 3, '2026-04-05', '2026-04-05 19:16:47', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:15:47'),
(2163, '5279e42e3e72b95967aecbf956a520d71d66af2559372883e94c400109e03261', 3, '2026-04-05', '2026-04-05 19:17:18', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:16:18'),
(2164, '65add4d3ff568dad1f041b52abac48be07b81e1b83e89106781af6eb5606d56d', 3, '2026-04-05', '2026-04-05 19:17:49', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:16:49'),
(2165, '8eb99d7cae3441fb2e82765667160b4050309615fe38f8d4eb8b656f954d91e0', 3, '2026-04-05', '2026-04-05 19:18:19', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:17:19'),
(2166, '83feb75b4ac2f9e87cfb25cf672d51ef01101baf0ea033da28a4dca7fbb81758', 3, '2026-04-05', '2026-04-05 19:18:50', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:17:50'),
(2167, '3b1fa165fa070538f3601541efc605e575ec05bd968c60ebd9d1e89241c4ee94', 3, '2026-04-05', '2026-04-05 19:19:22', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:18:22'),
(2168, 'c0537b30647c7c33e8f25fc05c4430a46fe126446c46b9dc111dca9acd5c1a02', 3, '2026-04-05', '2026-04-05 19:19:53', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:18:53'),
(2169, 'a1286c71e766bd3a8a449848be8dc6dc1bb2cd6184c8ac4d85740844d246febf', 3, '2026-04-05', '2026-04-05 19:20:25', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:19:25'),
(2170, '88bf6104e03ef1d5a2e9f07f787bb87f0d75392c68f07cd5e238352d9e236281', 3, '2026-04-05', '2026-04-05 19:20:57', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:19:57'),
(2171, '712d4ee85c5aa40ea488867ccf01dc4c33749c20c0ddd896b9b8724f12f98f56', 3, '2026-04-05', '2026-04-05 19:21:28', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:20:28'),
(2172, '5673de39c64a39ebdc803552fb124baf5ef3ace221868186e3ad80ee8b20e20e', 3, '2026-04-05', '2026-04-05 19:22:00', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:21:00'),
(2173, '0b09e31eae4e639b472101e78f85b8e46bdb4afe975d50cd27083d23b7494c3c', 3, '2026-04-05', '2026-04-05 19:22:31', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:21:31'),
(2174, 'ca5229bd6fea89df2264a0a76adb06727f50886bbbd3ec75bec413b1ba1571a2', 3, '2026-04-05', '2026-04-05 19:23:01', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:22:01'),
(2175, '5bcca509ec7ac923465d2c24ea4c5946e94ba2be698838ad36137fa3202d06de', 3, '2026-04-05', '2026-04-05 19:23:33', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:22:33'),
(2176, '6e0d71bd11d027b3bee74875b45b8641798f0fe63d39019145688ca8613d294a', 3, '2026-04-05', '2026-04-05 19:24:04', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:23:04'),
(2177, '16f5554486906971b194a0b09f7fe2975a6d656601303c834ba04a12fdb905a1', 3, '2026-04-05', '2026-04-05 19:24:35', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:23:35'),
(2178, 'ec5e4fd1fd2444ac72d89a76e78268051d839ce0ba3a111a384590475b923015', 3, '2026-04-05', '2026-04-05 19:25:06', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:24:06'),
(2179, '2e815bd53ba42b2c8f5d1a5d2553d04102a2710cfa84b4617e864f73c07c9801', 3, '2026-04-05', '2026-04-05 19:25:37', 1, 1, '2026-04-05 19:24:55', '10.101.190.98', '2026-04-05 11:24:37'),
(2180, 'd5bfd92ad2df5294e2fd67f347ed11f8ab14ae0ddda3f832c098b7edcaaeb81e', 3, '2026-04-05', '2026-04-05 19:26:08', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:25:08'),
(2181, '76d449dbf37a622d132bff335f212fe31e96bfd4e5ab01c831e5c5bea7aec3e5', 3, '2026-04-05', '2026-04-05 19:26:39', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:25:39'),
(2182, 'bb34fb35149b76b91babcd13147e22e13cd9ef7e068ee087a05559a83322f8c8', 3, '2026-04-05', '2026-04-05 19:27:10', 1, 1, '2026-04-05 19:26:31', '10.101.190.98', '2026-04-05 11:26:10'),
(2183, '28a94e82d065b88c07528ec8ee5557f4420a53da967fe16bfcf0a0a625184bb9', 3, '2026-04-05', '2026-04-05 19:27:40', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:26:40'),
(2184, '156c12bf925707d6666554638674ca5e34a369619b52dc44538ddab90da3f75e', 3, '2026-04-05', '2026-04-05 19:28:11', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:27:11'),
(2185, 'a9e424847b78f4634e1b8b7fd94ff46fbd9d78e5691983fb653e8213ae5ccd5d', 3, '2026-04-05', '2026-04-05 19:28:42', 0, NULL, NULL, '10.101.190.98', '2026-04-05 11:27:42'),
(0, '37565b1dc1f83d1b3c8487a91b24b9857f6b031c68062b0605dad040d823d0b4', 3, '2026-04-06', '2026-04-06 01:36:55', 0, NULL, NULL, '::1', '2026-04-05 17:35:55'),
(0, '22847e71b24ea08d87f0b09fef8a58d04b30b205a107b72be4360c2aa5dad58a', 3, '2026-04-06', '2026-04-06 01:37:25', 0, NULL, NULL, '::1', '2026-04-05 17:36:25'),
(0, 'f4708f3bc9ec1e201e66d5b9bf3b67e98a06220fe69de729ed98f85725182de5', 3, '2026-04-06', '2026-04-06 01:47:00', 0, NULL, NULL, '::1', '2026-04-05 17:46:00'),
(0, '7a53f1a6cf07cef97550a39e82c48eeaf10f5cfd4a58106ccd0fc405069473ba', 3, '2026-04-06', '2026-04-06 01:49:36', 0, NULL, NULL, '::1', '2026-04-05 17:48:36'),
(0, '210f3a120a82c0920fb00384766517b527ccabc37c6df4750048900f0c970731', 3, '2026-04-06', '2026-04-06 01:50:06', 0, NULL, NULL, '::1', '2026-04-05 17:49:06'),
(0, '941a54e80ab0cdbb5c3077b4ee495aa45d84d66ead84bce3a227f07f3aa20487', 3, '2026-04-06', '2026-04-06 01:56:55', 0, NULL, NULL, '::1', '2026-04-05 17:55:55'),
(0, 'a57c82ef2f7c54eb96809a36ec19f18916681fa032cfdc1d0d25a3c66fd240aa', 3, '2026-04-06', '2026-04-06 01:57:44', 0, NULL, NULL, '::1', '2026-04-05 17:56:44'),
(0, '77817e49c6ec73167ad8a53d206e3c189c2f91fe4cbc5d68b189f27efcf3977b', 14, '2026-04-06', '2026-04-06 04:14:48', 0, NULL, NULL, '::1', '2026-04-05 20:13:48'),
(0, '4eb2e639d8a9196f0378a7ed3049c23b98cbe34f033b0485799f9e9b76a349d6', 3, '2026-04-06', '2026-04-06 05:14:23', 0, NULL, NULL, '::1', '2026-04-05 21:13:23'),
(0, '1e38c1307f32530f3e87364f487b7b81eaa63798bc4e5cd5a38837d21c7b8039', 3, '2026-04-06', '2026-04-06 05:15:01', 1, 4, '2026-04-06 05:14:40', '192.168.68.136', '2026-04-05 21:14:01'),
(0, '3c5d4c6a14b0f061fc76cb430ab5acad362d89e1ec53e7ac24c9b874c4ea6ba6', 3, '2026-04-06', '2026-04-06 05:15:31', 0, NULL, NULL, '192.168.68.136', '2026-04-05 21:14:31'),
(0, '75297a5b49a07949f9833e135af9f381803f4f538285fada07ad372445da46cf', 3, '2026-04-06', '2026-04-06 05:16:01', 0, NULL, NULL, '192.168.68.136', '2026-04-05 21:15:01'),
(0, 'b2c1ddbad5f36b819a64c1618f754b8876a0fd4503f48dd69f8925df8dfe74ef', 3, '2026-04-06', '2026-04-06 05:16:31', 0, NULL, NULL, '192.168.68.136', '2026-04-05 21:15:31'),
(0, '1b12cb1a8112d471aca3d52ac636037c80410231b704bdb7b04b92cc154145e8', 3, '2026-04-06', '2026-04-06 05:17:01', 1, 5, '2026-04-06 05:16:31', '192.168.68.136', '2026-04-05 21:16:01'),
(0, '46d5901e2c6c24b078f627ea9d1c2a720acc2beeb22ceb3310c3677ad1c67974', 3, '2026-04-06', '2026-04-06 05:17:31', 0, NULL, NULL, '192.168.68.136', '2026-04-05 21:16:31'),
(0, '9fc1913373f58c463e94382db8c0e48fd8a12b7e71f915445445a28abdf1c0eb', 3, '2026-04-06', '2026-04-06 05:18:02', 0, NULL, NULL, '192.168.68.136', '2026-04-05 21:17:02'),
(0, '0e6de46cb9e96d25d0e7d3a646d7162527c777ddbe91321da6081ded164e5ef3', 3, '2026-04-06', '2026-04-06 05:18:32', 0, NULL, NULL, '192.168.68.136', '2026-04-05 21:17:32'),
(0, 'f1e31088be14b1350fa5bcf61eef45a053a14dbbd5a3c2c4e0c392c1b5aa2397', 3, '2026-04-06', '2026-04-06 05:19:02', 0, NULL, NULL, '192.168.68.136', '2026-04-05 21:18:02'),
(0, 'aa9f292f4dddaed0e900d576d7b0c656b968de14540b1189e496983875e44d72', 3, '2026-04-06', '2026-04-06 05:19:32', 0, NULL, NULL, '192.168.68.136', '2026-04-05 21:18:32'),
(0, '81482bd1fc7b1a9fd50d88cf5fc4b37dd2e6c777c31186bceb98eb2b0d00486a', 3, '2026-04-06', '2026-04-06 10:51:35', 0, NULL, NULL, '10.101.190.98', '2026-04-06 02:50:35'),
(0, '29c8a71edbeada757a2974a4eb3844c23d66616d7d426038a4cd48455e559d01', 3, '2026-04-06', '2026-04-06 10:52:05', 0, NULL, NULL, '10.101.190.98', '2026-04-06 02:51:05'),
(0, '0970a94d9429a5c637ff5cac7f270078a81a972810243ad59de37f0b8ee269cc', 3, '2026-04-06', '2026-04-06 10:52:35', 0, NULL, NULL, '10.101.190.98', '2026-04-06 02:51:35'),
(0, 'f3b55b03e0a926c9bfec28e9b50092d9b914112404f290e5cb10a8cab458fc9e', 3, '2026-04-06', '2026-04-06 10:53:05', 0, NULL, NULL, '10.101.190.98', '2026-04-06 02:52:05'),
(0, '75d89cea28d2868f7300f6a61e2a57bd62e498b4bf79aee93e479b2a47fe16ef', 3, '2026-04-06', '2026-04-06 10:53:49', 0, NULL, NULL, '10.101.190.98', '2026-04-06 02:52:49'),
(0, '6cf2b484b15eb3907ad62c9d3914b4572f17fe5c677ba2332497cda5924d5ba4', 3, '2026-04-06', '2026-04-06 12:24:17', 0, NULL, NULL, '10.101.190.98', '2026-04-06 04:23:17'),
(0, '4cbe5a623d19a07c16aabf77ab052e5b8ea886af2b46da3929c3d41294e92bb3', 3, '2026-04-06', '2026-04-06 12:38:18', 0, NULL, NULL, '10.101.190.98', '2026-04-06 04:37:18'),
(0, '8c466ef3475d238b662efc101f9be016ff47d2d4aca3863666c9a3028db68e2b', 3, '2026-04-06', '2026-04-06 12:38:48', 1, 3, '2026-04-06 12:38:54', '10.101.190.98', '2026-04-06 04:37:48'),
(0, '2fe9793f215489e0dfefb17d50fcf07cf5b05bc79d12767cbdeee036f128c67a', 3, '2026-04-06', '2026-04-06 12:39:18', 0, NULL, NULL, '10.101.190.98', '2026-04-06 04:38:18'),
(0, '458bbf43d7da0290c9993b7178d363a764cd7d407842b9ecda55f3b5db8be8c7', 3, '2026-04-06', '2026-04-06 12:39:48', 0, NULL, NULL, '10.101.190.98', '2026-04-06 04:38:48'),
(0, '1d897ec9c940baa1b6421407a448325fc71497fce15cf7cd6ebe79dfbe100cb5', 3, '2026-04-06', '2026-04-06 12:40:18', 0, NULL, NULL, '10.101.190.98', '2026-04-06 04:39:18'),
(0, '44388e5ad80d1f91de427447f48a040abbe381744d26b509a793f331576e54c6', 3, '2026-04-06', '2026-04-06 12:40:49', 0, NULL, NULL, '10.101.190.98', '2026-04-06 04:39:49'),
(0, '2f07bc44550a972e7529b92a17518d9859cfee793badb54e83b46878918e87c7', 3, '2026-04-06', '2026-04-06 12:41:20', 0, NULL, NULL, '10.101.190.98', '2026-04-06 04:40:20'),
(0, '4f459d8470b25463086778fb733527600109bbbe50d17adc280affb8f4c61a6e', 3, '2026-04-06', '2026-04-06 12:41:52', 0, NULL, NULL, '10.101.190.98', '2026-04-06 04:40:52'),
(0, 'ff81bccf7f5d7a3840b3028e4c4d5823b313b58459b20c49a9f2f6eee152ac3b', 3, '2026-04-06', '2026-04-06 12:42:23', 0, NULL, NULL, '10.101.190.98', '2026-04-06 04:41:23'),
(0, '8a76907e73b70d1bb46b1a6dcb76818909e2bb61afc044dd49ff5efefcbc35c3', 3, '2026-04-06', '2026-04-06 12:42:54', 0, NULL, NULL, '10.101.190.98', '2026-04-06 04:41:54'),
(0, 'eaeaea16717a61c2476ee1e121b6a5a2542e089911310f4107789f572cce6258', 3, '2026-04-06', '2026-04-06 12:43:25', 0, NULL, NULL, '10.101.190.98', '2026-04-06 04:42:25'),
(0, '73dfa6e2837f75bd4cb43e3d517af578f3c7d595e4867a9ae16f13c14aa230ff', 3, '2026-04-20', '2026-04-20 21:51:34', 0, NULL, NULL, '192.168.100.37', '2026-04-20 13:50:34'),
(0, 'd276370a3e37d1fdd3e71eeb044c9083e0f414eb323cc094a6d8b8149f52b05e', 3, '2026-04-20', '2026-04-20 21:51:50', 0, NULL, NULL, '192.168.100.37', '2026-04-20 13:50:50'),
(0, '2240affd5f6aab456ee0ead2af20d126a8fa53d4bad79abaf86784d5e362f603', 3, '2026-04-20', '2026-04-20 21:52:20', 0, NULL, NULL, '192.168.100.37', '2026-04-20 13:51:20'),
(0, 'ffaed32902f3b07980463af221182ced4c9248afa89a191b31c1ad3c232c31b0', 3, '2026-07-09', '2026-07-09 21:25:35', 0, NULL, NULL, '192.168.100.148', '2026-07-09 13:24:35'),
(0, 'fe0f54ba064965d4993e41c39662c0d8cd44273d8f800b4f94d5e799c6633a18', 3, '2026-07-09', '2026-07-09 21:25:41', 0, NULL, NULL, '192.168.100.148', '2026-07-09 13:24:41'),
(0, '795a00b583120a16f04d57a1bce05ebd57825bbc611853fa25d651a52280c86b', 3, '2026-07-09', '2026-07-09 21:54:22', 0, NULL, NULL, '192.168.100.148', '2026-07-09 13:53:22'),
(0, 'ba9283ee59979ea8ddaafa1f9546d9699eb21e36d3e31561cb5fd5dc21244832', 3, '2026-07-09', '2026-07-09 21:54:38', 0, NULL, NULL, '192.168.100.148', '2026-07-09 13:53:38'),
(0, '17bd37c5b626196b8679585b0c3d6e0c211a6268a2835e2837b3e97dd4e0896d', 3, '2026-07-09', '2026-07-09 21:55:15', 0, NULL, NULL, '192.168.100.148', '2026-07-09 13:54:15'),
(0, 'c54fec8fde73632fe6396d8c6fd7c1ee1b46e4ac76c2ec78b6b91a0ec00bc024', 3, '2026-07-09', '2026-07-09 22:16:32', 0, NULL, NULL, '192.168.100.148', '2026-07-09 14:15:32'),
(0, '1d3ee0bb396e273db20e28ac7ec219acd8666b38c2e5c1f514bfdd6c19ae74ab', 3, '2026-07-09', '2026-07-09 22:17:02', 0, NULL, NULL, '192.168.100.148', '2026-07-09 14:16:02'),
(0, 'f4b3e0dad04a571bb4ecb2add7e11de3a38a07c948d6b04d3bef40cec11f1e3b', 3, '2026-07-09', '2026-07-09 22:17:32', 0, NULL, NULL, '192.168.100.148', '2026-07-09 14:16:32'),
(0, 'd7a659904ec08ecf9a5ab9ec92a0a6fc33a4df53814d57aac1c2313e74aec0b3', 3, '2026-07-09', '2026-07-09 22:18:02', 0, NULL, NULL, '192.168.100.148', '2026-07-09 14:17:02'),
(0, '89a85ae5a4e658d07ac82a57f84e696b32c7b5016a5fec51eb251f4f3e7c1eba', 3, '2026-07-09', '2026-07-09 22:18:32', 0, NULL, NULL, '192.168.100.148', '2026-07-09 14:17:32'),
(0, '45dfc0ab0497592dd18b6bd71760cb7e63dd03b65e08a26386c72d5b8eeb2637', 3, '2026-07-09', '2026-07-09 23:16:59', 0, NULL, NULL, '192.168.100.148', '2026-07-09 15:15:59'),
(0, '53eb14c24fdb6e774ef780d3fda291671f20dc94131d92db582732a79fb61037', 3, '2026-07-09', '2026-07-09 23:17:29', 0, NULL, NULL, '192.168.100.148', '2026-07-09 15:16:29'),
(0, '21652dc42fa7d40a74c9de5371457ffe8e898248c1046a2873ed9e6e8c5038a0', 3, '2026-07-09', '2026-07-09 23:17:59', 0, NULL, NULL, '192.168.100.148', '2026-07-09 15:16:59'),
(0, '869a2993d73e9e09aca9513e6e59574b6e81d6781237dcce73ce81d92f777742', 3, '2026-07-09', '2026-07-09 23:18:29', 0, NULL, NULL, '192.168.100.148', '2026-07-09 15:17:29'),
(0, '8b4b0422884d4eddbede688d71e4b83fb82e53cf26919f67469b4ce4a77e7219', 3, '2026-07-09', '2026-07-09 23:18:59', 0, NULL, NULL, '192.168.100.148', '2026-07-09 15:17:59'),
(0, '936f6a9e9ce6195669f74970db38ec80dd094b0764b59136bfcfbf1e53fe4590', 3, '2026-07-09', '2026-07-09 23:19:29', 0, NULL, NULL, '192.168.100.148', '2026-07-09 15:18:29'),
(0, 'a798100685e9661e07aa3bc948d9e636801961bc4e5642610f771513d6b46937', 3, '2026-07-09', '2026-07-09 23:26:45', 0, NULL, NULL, '::1', '2026-07-09 15:25:45'),
(0, 'd0db9e5ff471ae64f4204ffa103664f271262fb3c43c5bdf91b6b301a9d2dace', 3, '2026-07-09', '2026-07-09 23:27:15', 0, NULL, NULL, '::1', '2026-07-09 15:26:15'),
(0, '7e982bcbf91bb1c1422878aa5b8ec442fec1ff2d018921f119b7422e6620cd68', 3, '2026-07-09', '2026-07-09 23:28:04', 0, NULL, NULL, '::1', '2026-07-09 15:27:04'),
(0, 'ecf18fe98082135655e0c8156573522123175c7158887f76c1c1fab12feff828', 3, '2026-07-09', '2026-07-09 23:28:41', 0, NULL, NULL, '::1', '2026-07-09 15:27:41'),
(0, '8bd07283880842ccddb692ae0bc9f123ddb928e698328b64244fcfa2fd4ea357', 3, '2026-07-09', '2026-07-09 23:29:21', 0, NULL, NULL, '::1', '2026-07-09 15:28:21'),
(0, '06d49b65a64bde29cb658ad18614a3534c93c4b0a7eb2bb97b8c95021c1c4325', 3, '2026-07-09', '2026-07-09 23:29:52', 0, NULL, NULL, '::1', '2026-07-09 15:28:52'),
(0, '9ff13da97bf906e36e5407436cf3ab1b08a3f42597751341075aa61ca71e8ce2', 3, '2026-07-09', '2026-07-09 23:30:52', 0, NULL, NULL, '::1', '2026-07-09 15:29:52'),
(0, '387afb2c6982fbfec05540f512ee6e1f28bfc41c10cc01193b8c4463581a0102', 3, '2026-07-09', '2026-07-09 23:31:29', 0, NULL, NULL, '::1', '2026-07-09 15:30:29'),
(0, '267d74c9376858ad4e5c26e18c1c8d5e31c76ae735a93cb62f812742d20042ea', 3, '2026-07-09', '2026-07-09 23:32:18', 0, NULL, NULL, '::1', '2026-07-09 15:31:18'),
(0, '22c519be4483ad55fdba0a2bd48a14f81201c9fdabc1b1fa996446721be5d226', 3, '2026-07-09', '2026-07-09 23:32:49', 0, NULL, NULL, '::1', '2026-07-09 15:31:49'),
(0, 'b8a340bc65e2101e9861809bcdbd90c16fc73b3f9e38cd7f013014c7603d89dc', 3, '2026-07-09', '2026-07-09 23:33:20', 0, NULL, NULL, '::1', '2026-07-09 15:32:20'),
(0, 'd25f8215a050277118735602a67ef97d3311385d91c50ef564acd64db69217e4', 3, '2026-07-09', '2026-07-09 23:33:51', 0, NULL, NULL, '::1', '2026-07-09 15:32:51'),
(0, 'ab3d70afae98d5c1ada9f9895e12de46f81fa94995f0160da54864dff7d26988', 3, '2026-07-09', '2026-07-09 23:34:38', 0, NULL, NULL, '::1', '2026-07-09 15:33:38'),
(0, '538f0a3179cf9a170fca791f2cec3708cc72f6992b25b9c3f23040e11307179e', 3, '2026-07-09', '2026-07-09 23:35:18', 0, NULL, NULL, '::1', '2026-07-09 15:34:18'),
(0, '5620b05220d85ac42f3738a046de4b9be4c7414ef097148b70efb0520f643986', 3, '2026-07-09', '2026-07-09 23:35:49', 0, NULL, NULL, '::1', '2026-07-09 15:34:49'),
(0, 'd53a9e35827bba9f316a40668428b3a470cafe83f0cc454f76b523333dbe0f44', 3, '2026-07-09', '2026-07-09 23:36:46', 0, NULL, NULL, '::1', '2026-07-09 15:35:46'),
(0, 'ef1fd7ea44c90992e75a94e8cfc1039e5916e47086e0d7a1c78ec565a23053ce', 3, '2026-07-09', '2026-07-09 23:37:42', 0, NULL, NULL, '::1', '2026-07-09 15:36:42'),
(0, '22437f4c17c1504b492aad824ffb187a5a558fcf4a42f5aedc9c80e5dc13cfb7', 3, '2026-07-09', '2026-07-09 23:38:13', 0, NULL, NULL, '::1', '2026-07-09 15:37:13'),
(0, '29b1967c482bf9032162c7ae47b538b8f192dfaeed9962b9dfdce851b8de233b', 3, '2026-07-09', '2026-07-09 23:39:13', 0, NULL, NULL, '::1', '2026-07-09 15:38:13'),
(0, '31204ddaacfff97f543de9ead8f08e0694d70bddc7b9ff3728bd96690ef9769a', 3, '2026-07-09', '2026-07-09 23:39:45', 0, NULL, NULL, '::1', '2026-07-09 15:38:45'),
(0, '554dbbe17e151549c60967d7ed5453d39d5e3de81473fe44d7f8d9da691fbba9', 3, '2026-07-09', '2026-07-09 23:40:45', 0, NULL, NULL, '::1', '2026-07-09 15:39:45'),
(0, 'd53fec77ebb99381de54c3f388b9d5c1080e716dd947fe15d9a9fa595e96f866', 3, '2026-07-09', '2026-07-09 23:41:16', 0, NULL, NULL, '::1', '2026-07-09 15:40:16'),
(0, 'b20227a8eeb6d39c1044fbecf1772b7ef8e8865525bb8873a1b5ffea30e9cb46', 3, '2026-07-09', '2026-07-09 23:42:08', 0, NULL, NULL, '::1', '2026-07-09 15:41:08'),
(0, 'e5f207029f9fdc981dc289c3f530b4b45831d803f88539755a70cc966f25b7c6', 3, '2026-07-09', '2026-07-09 23:42:21', 0, NULL, NULL, '::1', '2026-07-09 15:41:21'),
(0, 'e9427bfa1c76349120bfb696cda485926c6625c514d9796a377867d81a791ea5', 3, '2026-07-12', '2026-07-12 17:03:51', 0, NULL, NULL, '::1', '2026-07-12 09:02:51'),
(0, 'bfe9a60402552f994b6f0c96faf3c3fd5bd1ee5e4edeb34ae55bb39b785c9688', 3, '2026-07-12', '2026-07-12 17:37:00', 0, NULL, NULL, '::1', '2026-07-12 09:36:00'),
(0, '57e4ff127ccff769ce95b52de22a4600ae9d941e913f33f2771f69183c83d3ed', 3, '2026-07-12', '2026-07-12 17:37:31', 0, NULL, NULL, '::1', '2026-07-12 09:36:31'),
(0, 'b4eeba6d7f6ab1925a8d1289ad7af3f5a7cb3ad98eb0a2af3ae8a85bc66040f2', 3, '2026-07-12', '2026-07-12 23:33:09', 0, NULL, NULL, '::1', '2026-07-12 15:32:09'),
(0, 'b60a97947a65475ee9e882aa8e5a6a33267b4130ea478870e92e8a2dca49f301', 3, '2026-07-13', '2026-07-13 00:40:17', 0, NULL, NULL, '::1', '2026-07-12 16:39:17'),
(0, '4a33dc002687d66adc0e9ac81f0882ac57cbc3ae45a413aa73a29d2f8763c1be', 3, '2026-07-13', '2026-07-13 14:54:24', 0, NULL, NULL, '::1', '2026-07-13 06:53:24'),
(0, '822acbb790bbc951ca778b62a7cfd673aa71ba27dcbab1d4a98ce73ec348ee4a', 3, '2026-07-13', '2026-07-13 14:54:56', 0, NULL, NULL, '::1', '2026-07-13 06:53:56'),
(0, '1ebc174868d99121e93004f5dd28bdf7b5b53a460201ad69f63aa8a03f2f714c', 3, '2026-07-13', '2026-07-13 14:55:40', 0, NULL, NULL, '::1', '2026-07-13 06:54:40'),
(0, 'b8fd71232bd3d37c8b39d69a530b3042e35166c51a8e768966c855d893336930', 3, '2026-07-15', '2026-07-15 10:39:07', 0, NULL, NULL, '::1', '2026-07-15 02:38:07'),
(0, 'de32b45f6b7900a85f6b3355a6fa904e7296289b6b8023862fc08bcea7c37a4a', 3, '2026-07-21', '2026-07-21 16:00:42', 0, NULL, NULL, '::1', '2026-07-21 07:59:42'),
(0, 'a4aea620c1008d9e8452f7990a193bffe5a3ab067bda2defd271746e0907b4a0', 3, '2026-07-21', '2026-07-21 16:08:13', 0, NULL, NULL, '::1', '2026-07-21 08:07:13'),
(0, '99b76604e4bd6e69583dc37ce6d50fc822c107a3c0175c160f2190cb3fe0ca3d', 3, '2026-07-21', '2026-07-21 16:12:57', 0, NULL, NULL, '::1', '2026-07-21 08:11:57'),
(0, '2f32cf4332c18c380ef700916fac748d5d2112d749bf81e02cd55cc1fe8d6373', 3, '2026-07-21', '2026-07-21 16:31:42', 0, NULL, NULL, '::1', '2026-07-21 08:30:42'),
(0, '88081a7d8a35bb06772dd62518d679c5c264b5ae95e02c3559b12ab08e84757f', 3, '2026-07-21', '2026-07-21 20:14:24', 0, NULL, NULL, '::1', '2026-07-21 12:13:24'),
(0, '29412b07c2561c57eac5aff8173521ed4a3df4b540b018eecb3b75c3031f70d3', 3, '2026-07-21', '2026-07-21 20:14:58', 0, NULL, NULL, '192.168.100.47', '2026-07-21 12:13:58'),
(0, 'dba28b3f9b5f7a17cc0e8ea72d3c51b6c1371a83ba8e59ecf85f4790f9dbc872', 3, '2026-07-21', '2026-07-21 20:15:28', 0, NULL, NULL, '192.168.100.47', '2026-07-21 12:14:28'),
(0, '4e07bac472fcae1ee0e48c928091851b63e4d30455628c78e273227fc046d716', 3, '2026-07-21', '2026-07-21 20:15:37', 0, NULL, NULL, '192.168.100.47', '2026-07-21 12:14:37'),
(0, 'd8d041c657bb21f407cdb27268786608521a12db88204380ca706508221dc62b', 3, '2026-07-21', '2026-07-21 20:20:07', 0, NULL, NULL, '192.168.100.47', '2026-07-21 12:19:07');

-- --------------------------------------------------------

--
-- Table structure for table `ta_employee_shifts`
--

CREATE TABLE `ta_employee_shifts` (
  `employee_shift_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_employee_shifts`
--

INSERT INTO `ta_employee_shifts` (`employee_shift_id`, `employee_id`, `shift_id`, `effective_from`, `effective_to`, `is_active`, `created_at`, `updated_at`, `employee_id_new`) VALUES
(3, 5, 2, '2026-03-28', '2026-05-28', 0, '2026-03-28 15:01:20', '2026-04-02 12:30:58', NULL),
(5, 6, 2, '2026-03-02', '2026-06-02', 1, '2026-04-02 12:31:21', '2026-04-02 12:31:21', NULL),
(7, 2, 2, '2026-03-02', '2026-06-02', 1, '2026-04-02 15:03:15', '2026-04-02 15:03:15', NULL),
(8, 8, 2, '2026-03-02', '2026-06-02', 1, '2026-04-02 15:03:50', '2026-04-02 15:03:50', NULL),
(9, 5, 2, '2026-03-02', '2026-06-02', 0, '2026-04-02 15:05:22', '2026-04-04 15:37:05', NULL),
(10, 1, 2, '2026-03-02', '2026-06-02', 1, '2026-04-02 15:25:02', '2026-04-02 15:25:02', NULL),
(14, 5, 7, '2026-03-02', '2026-06-02', 0, '2026-04-05 03:55:42', '2026-04-05 04:23:24', NULL),
(15, 3, 7, '2026-03-02', '2026-06-02', 1, '2026-04-05 03:55:42', '2026-04-05 03:55:42', NULL),
(16, 7, 7, '2026-03-02', '2026-06-02', 1, '2026-04-05 03:57:23', '2026-04-05 03:57:23', NULL),
(0, 5, 2, '2026-07-09', '2026-07-30', 1, '2026-07-09 15:43:57', '2026-07-09 15:43:57', NULL),
(0, 42, 2, '2026-05-31', '2026-12-31', 1, '2026-07-31 08:31:30', '2026-07-31 08:31:30', NULL),
(0, 11, 2, '2026-07-31', '2026-12-31', 1, '2026-07-31 10:50:43', '2026-07-31 10:50:43', NULL),
(0, 40, 2, '2026-07-01', '2026-12-31', 1, '2026-08-03 10:49:37', '2026-08-03 10:49:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ta_flexible_schedules`
--

CREATE TABLE `ta_flexible_schedules` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `schedule_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `day_of_week` int(11) DEFAULT NULL,
  `repeat_until` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_flexible_schedules`
--

INSERT INTO `ta_flexible_schedules` (`id`, `employee_id`, `schedule_date`, `start_time`, `end_time`, `day_of_week`, `repeat_until`, `contract_end_date`, `notes`, `created_by`, `created_at`) VALUES
(31, 5, '2026-04-06', '06:00:00', '18:00:00', 1, '2026-06-02', '2026-06-02', '', 3, '2026-04-05 04:24:45'),
(32, 5, '2026-04-07', '06:00:00', '18:00:00', 2, '2026-06-02', '2026-06-02', '', 3, '2026-04-05 04:24:45'),
(33, 5, '2026-04-08', '06:00:00', '18:00:00', 3, '2026-06-02', '2026-06-02', '', 3, '2026-04-05 04:24:45'),
(34, 5, '2026-04-09', '06:00:00', '18:00:00', 4, '2026-06-02', '2026-06-02', '', 3, '2026-04-05 04:24:45'),
(35, 5, '2026-04-10', '06:00:00', '18:00:00', 5, '2026-06-02', '2026-06-02', '', 3, '2026-04-05 04:24:45'),
(0, 39, '2026-08-03', '07:00:00', '19:00:00', 1, '2026-12-31', NULL, '', 3, '2026-08-01 03:40:19'),
(0, 39, '2026-08-04', '07:00:00', '19:00:00', 2, '2026-12-31', NULL, '', 3, '2026-08-01 03:40:19'),
(0, 39, '2026-08-05', '07:00:00', '19:00:00', 3, '2026-12-31', NULL, '', 3, '2026-08-01 03:40:19'),
(0, 39, '2026-08-08', '08:00:00', '20:00:00', 6, '2026-12-31', NULL, '', 3, '2026-08-01 03:41:37');

-- --------------------------------------------------------

--
-- Table structure for table `ta_holidays`
--

CREATE TABLE `ta_holidays` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `holiday_date` date NOT NULL,
  `is_recurring` tinyint(1) DEFAULT 0,
  `country_code` varchar(10) DEFAULT 'PH',
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT 'national',
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ta_holidays`
--

INSERT INTO `ta_holidays` (`id`, `name`, `holiday_date`, `is_recurring`, `country_code`, `description`, `category`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(73, 'New Year\'s Day', '2026-01-01', 0, 'PH', 'Bagong Taon', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(74, 'Chinese New Year', '2026-02-17', 0, 'PH', 'Chinese New Year', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(75, 'Maundy Thursday', '2026-04-02', 0, 'PH', 'Huwebes Santo', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(76, 'Good Friday', '2026-04-03', 0, 'PH', 'Biyernes Santo', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(77, 'Holy Saturday', '2026-04-04', 0, 'PH', 'Sabado de Gloria', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(78, 'Day of Valor', '2026-04-09', 0, 'PH', 'Araw ng Kagitingan', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(79, 'Labour Day', '2026-05-01', 0, 'PH', 'Araw ng Paggawa', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(80, 'Independence Day', '2026-06-12', 0, 'PH', 'Araw ng Kalayaan', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(81, 'Ninoy Aquino Day', '2026-08-21', 0, 'PH', 'Araw ng Kamatayan ni Senador Benigno Simeon \"Ninoy\" Aquino Jr.', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(82, 'National Heroes Day', '2026-08-31', 0, 'PH', 'Araw ng mga Bayani', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(83, 'All Saints\' Day Eve', '2026-10-31', 0, 'PH', 'All Saints\' Day Eve', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(84, 'All Saints\' Day', '2026-11-01', 0, 'PH', 'Araw ng mga Santo', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(85, 'Bonifacio Day', '2026-11-30', 0, 'PH', 'Araw ni Gat Andres Bonifacio', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(86, 'Feast of the Immaculate Conception of Mary', '2026-12-08', 0, 'PH', 'Kapistahan ng Immaculada Concepcion', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(87, 'Christmas Eve', '2026-12-24', 0, 'PH', 'Christmas Eve', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(88, 'Christmas Day', '2026-12-25', 0, 'PH', 'Araw ng Pasko', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(89, 'Rizal Day', '2026-12-30', 0, 'PH', 'Araw ng Kamatayan ni Dr. Jose Rizal', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(90, 'Last Day of The Year', '2026-12-31', 0, 'PH', 'Huling Araw ng Taon', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(91, 'New Year\'s Day', '2027-01-01', 0, 'PH', 'Bagong Taon', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(92, 'Chinese New Year', '2027-02-06', 0, 'PH', 'Chinese New Year', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(93, 'Maundy Thursday', '2027-03-25', 0, 'PH', 'Huwebes Santo', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(94, 'Good Friday', '2027-03-26', 0, 'PH', 'Biyernes Santo', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(95, 'Holy Saturday', '2027-03-27', 0, 'PH', 'Sabado de Gloria', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(96, 'Day of Valor', '2027-04-09', 0, 'PH', 'Araw ng Kagitingan', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(97, 'Labour Day', '2027-05-01', 0, 'PH', 'Araw ng Paggawa', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(98, 'Independence Day', '2027-06-12', 0, 'PH', 'Araw ng Kalayaan', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(99, 'Ninoy Aquino Day', '2027-08-21', 0, 'PH', 'Araw ng Kamatayan ni Senador Benigno Simeon \"Ninoy\" Aquino Jr.', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(100, 'National Heroes Day', '2027-08-30', 0, 'PH', 'Araw ng mga Bayani', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(101, 'All Saints\' Day Eve', '2027-10-31', 0, 'PH', 'All Saints\' Day Eve', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(102, 'All Saints\' Day', '2027-11-01', 0, 'PH', 'Araw ng mga Santo', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(103, 'Bonifacio Day', '2027-11-30', 0, 'PH', 'Araw ni Gat Andres Bonifacio', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(104, 'Feast of the Immaculate Conception of Mary', '2027-12-08', 0, 'PH', 'Kapistahan ng Immaculada Concepcion', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(105, 'Christmas Eve', '2027-12-24', 0, 'PH', 'Christmas Eve', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(106, 'Christmas Day', '2027-12-25', 0, 'PH', 'Araw ng Pasko', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(107, 'Rizal Day', '2027-12-30', 0, 'PH', 'Araw ng Kamatayan ni Dr. Jose Rizal', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10'),
(108, 'Last Day of The Year', '2027-12-31', 0, 'PH', 'Huling Araw ng Taon', 'national', 1, 3, '2026-04-05 04:30:10', '2026-04-05 04:30:10');

-- --------------------------------------------------------

--
-- Table structure for table `ta_holiday_sync_log`
--

CREATE TABLE `ta_holiday_sync_log` (
  `id` int(11) NOT NULL,
  `sync_date` date DEFAULT NULL,
  `total_holidays` int(11) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `last_synced` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ta_holiday_sync_log`
--

INSERT INTO `ta_holiday_sync_log` (`id`, `sync_date`, `total_holidays`, `country_code`, `last_synced`) VALUES
(1, '2026-03-20', 36, 'PH', '2026-03-20 07:41:28'),
(3, '2026-04-05', 36, 'PH', '2026-04-05 04:30:10');

-- --------------------------------------------------------

--
-- Table structure for table `ta_leave_balances`
--

CREATE TABLE `ta_leave_balances` (
  `leave_balance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `opening_balance` decimal(5,2) DEFAULT 0.00,
  `used_balance` decimal(5,2) DEFAULT 0.00,
  `remaining_balance` decimal(5,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_leave_balances`
--

INSERT INTO `ta_leave_balances` (`leave_balance_id`, `employee_id`, `leave_type_id`, `year`, `opening_balance`, `used_balance`, `remaining_balance`, `notes`, `created_at`, `updated_at`, `employee_id_new`) VALUES
(0, 1, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 2, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 3, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 4, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 5, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 6, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 7, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 8, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 9, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 10, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 11, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 12, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 13, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 14, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 1, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 2, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 3, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 4, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 5, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 6, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 7, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 8, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 9, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 10, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 11, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 12, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 13, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 14, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 1, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 2, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 3, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 4, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 5, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 6, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 7, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 8, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 9, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 10, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 11, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 12, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 13, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 14, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy', '2026-04-05 20:53:54', '2026-04-05 20:53:54', NULL),
(0, 12, 5, 2026, 7.00, 0.00, 7.00, 'Paternity Leave - RA 8187', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 13, 5, 2026, 7.00, 0.00, 7.00, 'Paternity Leave - RA 8187', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 1, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 2, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 3, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 4, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 5, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 6, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 7, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 8, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 9, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 10, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 11, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 12, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 13, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 14, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 1, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 2, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 3, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 4, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 5, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 6, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 7, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 8, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 9, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 10, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 11, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 12, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 13, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL),
(0, 14, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave', '2026-04-05 20:59:11', '2026-04-05 20:59:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ta_leave_daily_records`
--

CREATE TABLE `ta_leave_daily_records` (
  `daily_record_id` int(11) NOT NULL,
  `leave_request_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_date` date NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `is_holiday` tinyint(1) DEFAULT 0,
  `balance_deducted` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_leave_requests`
--

CREATE TABLE `ta_leave_requests` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `details` varchar(255) DEFAULT NULL,
  `reason` text DEFAULT NULL COMMENT 'Detailed reason for leave request',
  `supporting_document` varchar(255) DEFAULT NULL,
  `documents` longtext DEFAULT NULL COMMENT 'JSON array of uploaded document file paths',
  `document_uploaded_at` timestamp NULL DEFAULT NULL,
  `reject_reason` varchar(255) DEFAULT NULL,
  `employee_id_new` int(11) DEFAULT NULL,
  `balance_deducted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_leave_requests`
--

INSERT INTO `ta_leave_requests` (`id`, `employee_id`, `leave_type_id`, `start_date`, `end_date`, `date_submitted`, `updated_at`, `status`, `details`, `reason`, `supporting_document`, `documents`, `document_uploaded_at`, `reject_reason`, `employee_id_new`, `balance_deducted`) VALUES
(3, 1, 4, '2026-03-29', '2026-04-01', '2026-03-29 08:42:09', '2026-03-29 08:55:46', '', 'wdawdaw', NULL, NULL, NULL, NULL, 'ok', NULL, 0),
(0, 9, 7, '2026-04-10', '2026-04-13', '2026-04-05 21:52:42', '2026-04-05 22:25:15', 'Approved', 'jaswhdawdjaw', 'sjnfasweda', NULL, '[\"uploads\\/leave_documents\\/1775425962_1395d830a5063abb0240981ffdda4ddd.docx\"]', NULL, 'Testing approval', NULL, 0),
(0, 4, 7, '2026-04-13', '2026-04-15', '2026-04-05 22:22:14', '2026-04-05 22:25:15', 'Approved', 'mamamo', 'hakdog', NULL, '[\"uploads\\/leave_documents\\/1775427734_6013a4c5dc9b8150083f13a898a787bf.png\"]', NULL, 'Testing approval', NULL, 0),
(0, 2, 3, '2026-04-06', '2026-04-07', '2026-04-05 22:29:15', NULL, 'Pending', 'manyakis', 'buntis', NULL, '[\"uploads\\/leave_documents\\/1775428155_2084d04d2c6a56f24807fc5128ac46b5.png\"]', NULL, NULL, NULL, 0),
(0, 2, 5, '2026-04-06', '2026-04-11', '2026-04-05 22:39:03', NULL, 'Pending', 'try docu', 'test drove vehicle', NULL, '[\"uploads\\/leave_documents\\/1775428743_0b96168510f18f0e4fdeecdedd9d5d92.png\"]', NULL, NULL, NULL, 0),
(0, 3, 4, '2026-04-07', '2026-04-09', '2026-04-06 05:00:48', NULL, 'Pending', 'Urgent', 'For emergency', NULL, '[\"uploads\\/leave_documents\\/1775451648_f54262b7163d4c5f1d392ccde6f78be7.jpg\"]', NULL, NULL, NULL, 0),
(0, 3, 2, '2026-04-06', '2026-04-10', '2026-04-06 05:02:54', NULL, 'Pending', 'Test', 'Testing', NULL, '[\"uploads\\/leave_documents\\/1775451774_83c43a0c18c2626b7e272806cef2ae62.jpg\"]', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ta_leave_types`
--

CREATE TABLE `ta_leave_types` (
  `leave_type_id` int(11) NOT NULL,
  `leave_type_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `days_per_year` int(11) NOT NULL DEFAULT 10,
  `is_deductible` tinyint(1) DEFAULT 1,
  `requires_approval` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_leave_types`
--

INSERT INTO `ta_leave_types` (`leave_type_id`, `leave_type_name`, `description`, `days_per_year`, `is_deductible`, `requires_approval`, `created_at`) VALUES
(1, 'Vacation Leave', NULL, 15, 1, 1, '2026-04-05 20:55:08'),
(2, 'Sick Leave', NULL, 10, 1, 1, '2026-04-05 20:55:08'),
(3, 'Maternity Leave', NULL, 5, 1, 1, '2026-04-05 20:55:08'),
(4, 'Emergency Leave', NULL, 3, 0, 0, '2026-04-05 20:55:08'),
(5, 'Paternity Leave', NULL, 7, 1, 1, '2026-04-05 20:55:08'),
(6, 'Birthday Leave', NULL, 1, 1, 1, '2026-04-05 20:55:08'),
(7, 'Bereavement Leave', NULL, 5, 1, 1, '2026-04-05 20:55:08');

-- --------------------------------------------------------

--
-- Table structure for table `ta_overtime_frequency`
--

CREATE TABLE `ta_overtime_frequency` (
  `frequency_id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `month_year` varchar(7) NOT NULL,
  `overtime_instances` int(11) DEFAULT 0,
  `total_overtime_hours` decimal(8,2) DEFAULT 0.00,
  `average_overtime_per_instance` decimal(5,2) DEFAULT 0.00,
  `max_overtime_in_single_day` decimal(5,2) DEFAULT 0.00,
  `overtime_frequency_rating` enum('LOW','MODERATE','HIGH','CRITICAL') DEFAULT 'LOW',
  `approved_overtime_hours` decimal(8,2) DEFAULT 0.00,
  `unapproved_overtime_hours` decimal(8,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_overtime_tracking`
--

CREATE TABLE `ta_overtime_tracking` (
  `tracking_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `attendance_id` int(11) DEFAULT NULL,
  `overtime_date` date NOT NULL,
  `overtime_hours` decimal(5,2) NOT NULL,
  `overtime_minutes` int(11) NOT NULL,
  `reason_category` enum('PROJECT_DEADLINE','STAFFING_SHORTAGE','WORKLOAD_HEAVY','SHIFT_CHANGE','VOLUNTARY','OTHER') DEFAULT 'OTHER',
  `reason_notes` text DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_punctuality_scores`
--

CREATE TABLE `ta_punctuality_scores` (
  `score_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month_year` varchar(7) NOT NULL,
  `total_late_incidents` int(11) DEFAULT 0,
  `total_late_minutes` int(11) DEFAULT 0,
  `average_late_minutes` decimal(5,2) DEFAULT 0.00,
  `punctuality_score` decimal(5,2) DEFAULT 100.00,
  `punctuality_grade` enum('A','B','C','D','F') DEFAULT 'A',
  `score_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`score_breakdown`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_shifts`
--

CREATE TABLE `ta_shifts` (
  `shift_id` int(11) NOT NULL,
  `shift_name` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_duration` int(11) DEFAULT 60 COMMENT 'Break duration in minutes',
  `description` text DEFAULT NULL,
  `include_saturday` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_shifts`
--

INSERT INTO `ta_shifts` (`shift_id`, `shift_name`, `start_time`, `end_time`, `break_duration`, `description`, `include_saturday`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'Morning Shift', '06:00:00', '17:00:00', 60, '\r\n\r\n', 0, 1, '2026-03-20 04:03:28', '2026-03-20 04:03:28'),
(7, 'No Saturday', '06:00:00', '18:00:00', 60, '12-1pm lunch break', 0, 1, '2026-04-05 03:55:12', '2026-04-05 03:55:12');

-- --------------------------------------------------------

--
-- Table structure for table `ta_shift_assignments`
--

CREATE TABLE `ta_shift_assignments` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_shift_exclusions`
--

CREATE TABLE `ta_shift_exclusions` (
  `exclusion_id` int(11) NOT NULL,
  `employee_shift_id` int(11) NOT NULL,
  `exclusion_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ta_shift_exclusions`
--

INSERT INTO `ta_shift_exclusions` (`exclusion_id`, `employee_shift_id`, `exclusion_date`, `reason`, `created_at`, `updated_at`) VALUES
(27, 14, '2026-03-07', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(28, 14, '2026-03-14', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(29, 14, '2026-03-21', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(30, 14, '2026-03-28', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(31, 14, '2026-04-04', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(32, 14, '2026-04-11', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(33, 14, '2026-04-18', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(34, 14, '2026-04-25', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(35, 14, '2026-05-02', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(36, 14, '2026-05-09', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(37, 14, '2026-05-16', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(38, 14, '2026-05-23', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(39, 14, '2026-05-30', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(40, 15, '2026-03-07', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(41, 15, '2026-03-14', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(42, 15, '2026-03-21', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(43, 15, '2026-03-28', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(44, 15, '2026-04-04', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(45, 15, '2026-04-11', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(46, 15, '2026-04-18', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(47, 15, '2026-04-25', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(48, 15, '2026-05-02', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(49, 15, '2026-05-09', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(50, 15, '2026-05-16', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(51, 15, '2026-05-23', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(52, 15, '2026-05-30', 'Saturday exclusion', '2026-04-05 03:55:42', '2026-04-05 03:55:42'),
(53, 16, '2026-03-07', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(54, 16, '2026-03-14', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(55, 16, '2026-03-21', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(56, 16, '2026-03-28', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(57, 16, '2026-04-04', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(58, 16, '2026-04-11', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(59, 16, '2026-04-18', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(60, 16, '2026-04-25', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(61, 16, '2026-05-02', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(62, 16, '2026-05-09', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(63, 16, '2026-05-16', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(64, 16, '2026-05-23', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(65, 16, '2026-05-30', 'Saturday exclusion', '2026-04-05 03:57:23', '2026-04-05 03:57:23'),
(0, 0, '2026-07-04', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-07-11', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-07-18', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-07-25', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-08-01', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-08-08', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-08-15', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-08-22', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-08-29', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-09-05', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-09-12', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-09-19', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-09-26', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-10-03', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-10-10', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-10-17', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-10-24', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-10-31', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-11-07', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-11-14', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-11-21', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-11-28', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-12-05', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-12-12', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-12-19', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37'),
(0, 0, '2026-12-26', 'Saturday exclusion', '2026-08-03 10:49:37', '2026-08-03 10:49:37');

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
  `account_status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `last_login` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `theme`, `created_at`, `profile_pic`, `employee_id`, `account_status`, `last_login`, `password_changed_at`, `failed_login_attempts`) VALUES
(0, 'carlo', '$2y$10$t4hkBFA0UERwt9hZZiY62uo1ypCdNhkvuXGGi/8yQ8/97SNnWHUBO', 'Jhon Carlo Garcia', 'employee', 'light', '2026-04-05 17:34:44', NULL, '35', 'Active', NULL, NULL, 0),
(1, 'hr_payroll', '$2y$10$YSkTSwrSdqSBsF2e.pfyq.mNCCIF7ijV4h/s1pAc8Q7KlQHzbQTmq', 'Russell Ike', 'payroll', 'light', '2026-03-06 21:13:06', NULL, NULL, 'Active', NULL, NULL, 0),
(2, 'hr_recruitment', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Administrator', 'recruitment', 'light', '2026-03-07 02:46:33', NULL, NULL, 'Active', NULL, NULL, 0),
(3, 'hr_time', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Jose Mari', 'time', 'light', '2026-03-07 02:47:07', NULL, NULL, 'Active', NULL, NULL, 0),
(4, 'hr_employee', '$2y$10$DDo9507ZVqsctCAKjpbtOO34yDPyekruKRuwbTk/ulWcF7q7E3qtS', 'someone', 'employee', 'light', '2026-03-07 02:47:55', NULL, '1', 'Active', NULL, NULL, 0),
(5, 'hr_compliance', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'comply', 'compliance', 'light', '2026-03-07 02:48:19', NULL, NULL, 'Active', NULL, NULL, 0),
(6, 'hr_workforce', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'force', 'workforce', 'light', '2026-03-07 02:48:43', NULL, NULL, 'Active', NULL, NULL, 0),
(7, 'hr_learning', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'learn', 'learning', 'light', '2026-03-07 02:49:22', NULL, NULL, 'Active', NULL, NULL, 0),
(8, 'hr_performance', '$2y$10$/aFKLVK.xloqiY31X4T.dOPKY2AnnkrpaME4f2z.l4LhQurY1/Zzy', 'Perform', 'performance', 'light', '2026-03-07 02:49:46', 'user_8.jpg', NULL, 'Active', NULL, NULL, 0),
(9, 'hr_engagement', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'engage', 'engagement_relations', 'light', '2026-03-07 02:50:37', NULL, NULL, 'Active', NULL, NULL, 0),
(10, 'hr_exit', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'exit', 'exit', 'light', '2026-03-07 02:51:04', NULL, NULL, 'Active', NULL, NULL, 0),
(11, 'hr_clinic', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'clinic', 'clinic', 'light', '2026-03-12 00:20:55', NULL, NULL, 'Active', NULL, NULL, 0),
(12, 'learning_admin', '\\.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Learning Administrator', 'learning', 'light', '2026-03-22 12:35:30', NULL, NULL, 'Active', NULL, NULL, 0),
(13, 'employee', '$2y$10$COg/dGZyfIfKjbHbFeh8LOLRCR797mtfau7xdSH8H4eOsp51eyFFe', 'Adolf Hitler', 'employee_portal', 'light', '2026-03-24 10:06:12', NULL, NULL, 'Active', NULL, NULL, 0),
(14, 'john_doe', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'John Doe', 'employee', 'light', '2026-04-05 18:36:06', NULL, NULL, 'Active', NULL, NULL, 0),
(15, 'jane_smith', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Jane Smith', 'employee', 'light', '2026-04-05 18:38:06', NULL, NULL, 'Active', NULL, NULL, 0),
(16, 'mike_johnson', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Mike Johnson', 'employee', 'light', '2026-04-05 18:38:06', NULL, NULL, 'Active', NULL, NULL, 0),
(17, 'sarah_williams', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Sarah Williams', 'employee', 'light', '2026-04-05 18:38:06', NULL, NULL, 'Active', NULL, NULL, 0),
(18, 'david_brown', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'David Brown', 'employee', 'light', '2026-04-05 18:38:06', NULL, NULL, 'Active', NULL, NULL, 0),
(19, 'emily_davis', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Emily Davis', 'employee', 'light', '2026-04-05 18:38:18', NULL, NULL, 'Active', NULL, NULL, 0),
(20, 'robert_wilson', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Robert Wilson', 'employee', 'light', '2026-04-05 18:38:18', NULL, NULL, 'Active', NULL, NULL, 0),
(21, 'jessica_martinez', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Jessica Martinez', 'employee', 'light', '2026-04-05 18:38:18', NULL, NULL, 'Active', NULL, NULL, 0),
(22, 'test_employee_9', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Test Employee', 'employee', 'light', '2026-04-05 18:38:18', NULL, NULL, 'Active', NULL, NULL, 0),
(23, 'test_employee_10', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Test Employee', 'employee', 'light', '2026-04-05 18:38:30', NULL, NULL, 'Active', NULL, NULL, 0),
(24, 'admin_user', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Admin User', 'employee', 'light', '2026-04-05 18:38:30', NULL, NULL, 'Active', NULL, NULL, 0),
(25, 'placer_jan_russel_c_12', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Placer, Jan Russel C.', 'employee', 'light', '2026-04-05 18:38:30', NULL, NULL, 'Active', NULL, NULL, 0),
(26, 'placer_jan_russel_c_13', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Placer, Jan Russel C.', 'employee', 'light', '2026-04-05 18:38:30', NULL, NULL, 'Active', NULL, NULL, 0),
(27, 'jhon_carlo_garcia', '$2y$10$NQzDkijmYDGPynmZnTT.TOoeyVZum9roPJb0vstsANyJBnO5cDubO', 'Jhon Carlo Garcia', 'employee', 'light', '2026-04-05 18:38:30', NULL, NULL, 'Active', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_medicine_status`
-- (See below for the actual view)
--
CREATE TABLE `v_medicine_status` (
`medicine_id` varchar(20)
,`medicine_name` varchar(200)
,`category` varchar(100)
,`current_stock` int(11)
,`reorder_level` int(11)
,`unit_cost` decimal(8,2)
,`expiry_date` date
,`stock_status` varchar(12)
,`expiry_status` varchar(13)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_patient_summary`
-- (See below for the actual view)
--
CREATE TABLE `v_patient_summary` (
`patient_id` varchar(50)
,`full_name` varchar(201)
,`patient_type` enum('Student','Staff','Faculty','Visitor')
,`status` enum('Active','Inactive')
,`total_visits` bigint(21)
,`visits_last_30_days` bigint(21)
,`last_visit_date` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `wfa_actions`
--

CREATE TABLE `wfa_actions` (
  `action_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `pip_id` int(11) DEFAULT NULL,
  `action_type` enum('Training','Warning','PIP','Mentoring','Counseling','Suspension') DEFAULT 'Training',
  `description` text NOT NULL,
  `status` enum('Pending','In Progress','Completed','Cancelled') DEFAULT 'Pending',
  `assigned_to` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_action_recommendations`
--

CREATE TABLE `wfa_action_recommendations` (
  `recommendation_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `detected_issues` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`detected_issues`)),
  `recommended_action` varchar(100) NOT NULL,
  `confidence_score` decimal(3,2) DEFAULT NULL,
  `acknowledged` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_age_distribution`
--

CREATE TABLE `wfa_age_distribution` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `age_group` varchar(50) NOT NULL COMMENT '18-25, 26-35, 36-45, 46-55, 56+',
  `employee_count` int(11) DEFAULT 0,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `average_performance_score` decimal(5,2) DEFAULT 0.00,
  `department_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`department_breakdown`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_attrition_tracking`
--

CREATE TABLE `wfa_attrition_tracking` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `separation_date` date NOT NULL,
  `separation_type` enum('resigned','retired','terminated','other') DEFAULT 'resigned',
  `department` varchar(100) DEFAULT NULL,
  `tenure_years` decimal(5,2) DEFAULT NULL,
  `reason_for_leaving` text DEFAULT NULL,
  `exit_interview_completed` tinyint(1) DEFAULT 0,
  `rehire_eligible` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_attrition_tracking_backup`
--

CREATE TABLE `wfa_attrition_tracking_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `employee_id` varchar(50) NOT NULL,
  `separation_date` date NOT NULL,
  `separation_type` enum('resigned','retired','terminated','other') DEFAULT 'resigned',
  `department` varchar(100) DEFAULT NULL,
  `tenure_years` decimal(5,2) DEFAULT NULL,
  `reason_for_leaving` text DEFAULT NULL,
  `exit_interview_completed` tinyint(1) DEFAULT 0,
  `rehire_eligible` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `wfa_at_risk_employees_summary`
-- (See below for the actual view)
--
CREATE TABLE `wfa_at_risk_employees_summary` (
`risk_level` enum('high','medium','low')
,`count` bigint(21)
,`avg_risk_score` decimal(6,2)
,`percentage` decimal(26,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `wfa_audit_log`
--

CREATE TABLE `wfa_audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'view_report, generate_report, export_data, update_filter',
  `resource_type` varchar(50) DEFAULT NULL COMMENT 'report, filter, metric',
  `resource_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_compensation_analysis`
--

CREATE TABLE `wfa_compensation_analysis` (
  `id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `current_avg_salary` decimal(12,2) DEFAULT 0.00,
  `market_median_salary` decimal(12,2) DEFAULT 0.00,
  `salary_competitiveness_ratio` decimal(5,2) DEFAULT NULL COMMENT 'Current/Market %',
  `employee_count` int(11) DEFAULT 0,
  `salary_range_min` decimal(12,2) DEFAULT NULL,
  `salary_range_max` decimal(12,2) DEFAULT NULL,
  `recommended_adjustment` decimal(12,2) DEFAULT NULL,
  `last_market_review` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_custom_filters`
--

CREATE TABLE `wfa_custom_filters` (
  `id` int(11) NOT NULL,
  `filter_name` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `filter_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Department, employment type, date range' CHECK (json_valid(`filter_config`)),
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_department_analytics`
--

CREATE TABLE `wfa_department_analytics` (
  `id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `employee_count` int(11) DEFAULT 0,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `average_performance_score` decimal(5,2) DEFAULT 0.00,
  `headcount_target` int(11) DEFAULT NULL,
  `vacancy_count` int(11) DEFAULT 0,
  `average_tenure_years` decimal(5,2) DEFAULT 0.00,
  `metric_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_department_diversity`
--

CREATE TABLE `wfa_department_diversity` (
  `metric_date` date DEFAULT NULL,
  `diversity_category` varchar(50) DEFAULT NULL,
  `category_value` varchar(100) DEFAULT NULL,
  `employee_count` int(11) DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `average_salary` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_diversity_metrics`
--

CREATE TABLE `wfa_diversity_metrics` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `diversity_category` varchar(50) NOT NULL COMMENT 'gender, age_group, department',
  `category_value` varchar(100) NOT NULL COMMENT 'Male/Female/Other, 18-25/26-35/etc, Department Name',
  `employee_count` int(11) DEFAULT 0,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `average_performance` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_employee_metrics`
--

CREATE TABLE `wfa_employee_metrics` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `total_employees` int(11) DEFAULT 0,
  `total_teachers` int(11) DEFAULT 0,
  `total_staff` int(11) DEFAULT 0,
  `new_hires_this_year` int(11) DEFAULT 0,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `average_performance_score` decimal(5,2) DEFAULT 0.00,
  `total_departments` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_gender_distribution`
--

CREATE TABLE `wfa_gender_distribution` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `gender` varchar(50) NOT NULL COMMENT 'Male, Female, Other',
  `employee_count` int(11) DEFAULT 0,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `average_performance_score` decimal(5,2) DEFAULT 0.00,
  `department_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`department_breakdown`)),
  `position_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`position_breakdown`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_headcount_planning`
--

CREATE TABLE `wfa_headcount_planning` (
  `id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `fiscal_year` year(4) NOT NULL,
  `planned_headcount` int(11) DEFAULT 0,
  `actual_headcount` int(11) DEFAULT 0,
  `variance` int(11) DEFAULT 0,
  `planned_salary_budget` decimal(15,2) DEFAULT 0.00,
  `actual_salary_budget` decimal(15,2) DEFAULT 0.00,
  `budget_variance` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_insights_history`
--

CREATE TABLE `wfa_insights_history` (
  `insight_id` int(11) NOT NULL,
  `insight_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_kpis` int(11) DEFAULT NULL,
  `critical_insights` int(11) DEFAULT NULL,
  `warning_insights` int(11) DEFAULT NULL,
  `info_insights` int(11) DEFAULT NULL,
  `insights_data` longtext DEFAULT NULL,
  `action_taken` varchar(500) DEFAULT NULL,
  `resolved_by` varchar(100) DEFAULT NULL,
  `resolution_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_monthly_attrition`
--

CREATE TABLE `wfa_monthly_attrition` (
  `id` int(11) NOT NULL,
  `year_month` date NOT NULL,
  `total_separations` int(11) DEFAULT 0,
  `voluntary_separations` int(11) DEFAULT 0,
  `involuntary_separations` int(11) DEFAULT 0,
  `attrition_rate_percent` decimal(5,2) DEFAULT 0.00,
  `average_tenure_departing` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_performance_actions`
--

CREATE TABLE `wfa_performance_actions` (
  `action_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `action_type` enum('PIP','TRAINING','COACHING','MENTORING','FEEDBACK','WARNING','ESCALATION') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `priority` enum('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'MEDIUM',
  `status` enum('pending','ongoing','completed','failed','cancelled') DEFAULT 'pending',
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wfa_performance_actions`
--

INSERT INTO `wfa_performance_actions` (`action_id`, `employee_id`, `action_type`, `title`, `description`, `reason`, `priority`, `status`, `created_date`, `created_by`, `start_date`, `target_date`, `completed_date`, `notes`) VALUES
(1, 3, 'COACHING', '30-Day Attendance Improvement Plan', 'Improve overall attendance and punctuality', 'High absenteeism rate', 'CRITICAL', 'pending', '2026-04-07 16:19:21', 1, '2026-04-08', '2026-05-08', NULL, 'Focus on consistency and reliability'),
(2, 3, 'TRAINING', 'Coaching Session', 'One-on-one coaching to address behavioral concerns', 'Behavioral feedback from assessment', 'CRITICAL', 'pending', '2026-04-07 16:19:21', 1, '2026-04-08', '2026-04-30', NULL, 'Schedule with HR manager'),
(3, 3, 'TRAINING', 'Customer Service Excellence Workshop', 'Improve customer interaction skills', 'Recent customer complaints', 'HIGH', 'pending', '2026-04-07 16:19:21', 1, '2026-04-08', '2026-05-15', NULL, 'Register in online course'),
(4, 3, 'COACHING', 'Communication Skills Development', 'Enhance written and verbal communication', 'Feedback from supervisor', 'HIGH', 'pending', '2026-04-07 16:19:21', 1, '2026-04-08', '2026-06-08', NULL, 'Monthly coaching sessions'),
(5, 3, '', 'Productivity Goals', 'Increase task completion rate by 20%', 'Below target productivity metrics', 'HIGH', 'pending', '2026-04-07 16:19:21', 1, '2026-04-08', '2026-05-08', NULL, 'Weekly check-ins'),
(6, 3, 'TRAINING', 'Time Management Course', 'Better prioritization and planning skills', 'Missing deadlines', 'HIGH', 'pending', '2026-04-07 16:19:21', 1, '2026-04-08', '2026-05-22', NULL, 'Online course - 4 weeks'),
(7, 3, 'COACHING', 'Conflict Resolution Workshop', 'Learn to handle workplace conflicts', 'Recent team conflict incident', 'MEDIUM', 'pending', '2026-04-07 16:19:21', 1, '2026-04-15', '2026-05-30', NULL, 'Group workshop'),
(8, 3, 'TRAINING', 'Professional Development Plan', 'Career path discussion and planning', 'Career advancement request', 'MEDIUM', 'pending', '2026-04-07 16:19:21', 1, '2026-04-08', '2026-06-30', NULL, 'Quarterly reviews'),
(9, 3, '', 'Performance Metrics Review', 'Monthly review of KPIs', 'Ongoing monitoring required', 'MEDIUM', 'pending', '2026-04-07 16:19:21', 1, '2026-04-08', '2026-05-08', NULL, 'Monthly reports'),
(10, 3, 'COACHING', 'Test Action - 1775580158913', 'This is a test action created at 4/8/2026, 12:42:38 AM', 'System test', 'HIGH', 'pending', '2026-04-07 16:42:38', 1, '2026-04-07', '2026-05-07', NULL, 'Auto-generated test'),
(11, 40, 'COACHING', 'Weekly Performance Monitoring', 'Weekly Performance Monitoring', 'From Performance Assessment & Action Center', 'HIGH', 'completed', '2026-04-07 16:43:00', 0, '2026-04-07', '2026-05-07', '2026-04-08', 'Auto-created from smart recommendations\n[2026-04-08 01:39] Action started\n[2026-04-08 01:39] Action completed successfully'),
(12, 40, 'COACHING', 'Weekly Performance Monitoring', 'Weekly Performance Monitoring', 'From Performance Assessment & Action Center', 'HIGH', 'completed', '2026-04-07 16:48:37', 0, '2026-04-07', '2026-05-07', '2026-04-08', 'Auto-created from smart recommendations\n[2026-04-08 01:40] Failed: No reason specified\n[2026-04-08 01:40] Failed: No reason specified\n[2026-04-08 01:40] Action completed successfully'),
(13, 6, 'COACHING', 'Schedule Coaching Session (Immediate)', 'Schedule Coaching Session (Immediate)', 'From Performance Assessment & Action Center', 'CRITICAL', 'pending', '2026-04-07 16:49:56', 0, '2026-04-07', '2026-05-07', NULL, 'Auto-created from smart recommendations'),
(14, 6, 'WARNING', 'Issue Formal Performance Warning', 'Issue Formal Performance Warning', 'From Performance Assessment & Action Center', 'CRITICAL', 'pending', '2026-04-07 16:50:03', 0, '2026-04-07', '2026-05-07', NULL, 'Auto-created from smart recommendations'),
(15, 6, 'PIP', 'Create Attendance Improvement Plan (30 days)', 'Create Attendance Improvement Plan (30 days)', 'From Performance Assessment & Action Center', 'CRITICAL', 'pending', '2026-04-07 16:50:26', 0, '2026-04-07', '2026-05-07', NULL, 'Auto-created from smart recommendations'),
(16, 6, 'PIP', 'Create Attendance Improvement Plan (30 days)', 'Create Attendance Improvement Plan (30 days)', 'From Performance Assessment & Action Center', 'CRITICAL', 'pending', '2026-04-07 16:50:41', 0, '2026-04-07', '2026-05-07', NULL, 'Auto-created from smart recommendations');

-- --------------------------------------------------------

--
-- Table structure for table `wfa_performance_distribution`
--

CREATE TABLE `wfa_performance_distribution` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `performance_level` varchar(50) NOT NULL COMMENT 'Excellent, Good, Average, Below Average, Poor',
  `score_range_min` decimal(5,2) DEFAULT NULL,
  `score_range_max` decimal(5,2) DEFAULT NULL,
  `employee_count` int(11) DEFAULT 0,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `department_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Department distribution' CHECK (json_valid(`department_breakdown`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_performance_improvement_plans`
--

CREATE TABLE `wfa_performance_improvement_plans` (
  `pip_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` varchar(255) NOT NULL,
  `action_plan` text NOT NULL,
  `status` enum('ONGOING','COMPLETED','FAILED') DEFAULT 'ONGOING',
  `performance_target` decimal(3,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_performance_issues`
--

CREATE TABLE `wfa_performance_issues` (
  `issue_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `issue_type` enum('Absenteeism','Tardiness','Low Performance','Skill Gap','Behavior','Other') NOT NULL,
  `severity` enum('Low','Medium','High','Critical') DEFAULT 'Medium',
  `description` text NOT NULL,
  `detected_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved` tinyint(1) DEFAULT 0,
  `resolution_date` date DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_pip_reviews`
--

CREATE TABLE `wfa_pip_reviews` (
  `review_id` int(11) NOT NULL,
  `pip_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `review_date` date NOT NULL,
  `reviewer_employee_id` int(11) DEFAULT NULL,
  `current_performance_score` decimal(3,1) DEFAULT NULL,
  `current_attendance_percentage` int(11) DEFAULT NULL,
  `current_feedback_score` decimal(3,1) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('on_track','at_risk','off_track','completed','failed') DEFAULT 'on_track',
  `progress_percentage` int(11) DEFAULT NULL,
  `recommendation` varchar(255) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_reports`
--

CREATE TABLE `wfa_reports` (
  `id` int(11) NOT NULL,
  `report_name` varchar(255) NOT NULL,
  `report_type` varchar(50) NOT NULL COMMENT 'dashboard, attrition, diversity, performance, salary, custom',
  `report_date` date NOT NULL,
  `generated_by` int(11) DEFAULT NULL,
  `filters_applied` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Department, date range, etc.' CHECK (json_valid(`filters_applied`)),
  `report_data` longtext DEFAULT NULL COMMENT 'JSON data snapshot',
  `file_path` varchar(255) DEFAULT NULL COMMENT 'Path to exported file if any',
  `export_format` varchar(20) DEFAULT NULL COMMENT 'CSV, PDF, Excel',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_report_history`
--

CREATE TABLE `wfa_report_history` (
  `report_id` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `report_title` varchar(255) DEFAULT NULL,
  `report_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(500) DEFAULT NULL,
  `file_format` varchar(20) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `is_favorite` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_report_snapshots`
--

CREATE TABLE `wfa_report_snapshots` (
  `snapshot_id` int(11) NOT NULL,
  `snapshot_type` varchar(50) NOT NULL,
  `snapshot_name` varchar(255) NOT NULL,
  `snapshot_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_employees` int(11) DEFAULT NULL,
  `active_employees` int(11) DEFAULT NULL,
  `inactive_employees` int(11) DEFAULT NULL,
  `attrition_rate` decimal(5,2) DEFAULT NULL,
  `average_tenure` decimal(5,1) DEFAULT NULL,
  `department_count` int(11) DEFAULT NULL,
  `position_count` int(11) DEFAULT NULL,
  `snapshot_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`snapshot_data`)),
  `created_by` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_risk_assessment`
--

CREATE TABLE `wfa_risk_assessment` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `risk_level` enum('high','medium','low') DEFAULT 'low',
  `risk_score` decimal(5,2) DEFAULT 0.00 COMMENT '0-100 score',
  `risk_factors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of risk factors' CHECK (json_valid(`risk_factors`)),
  `low_performance_flag` tinyint(1) DEFAULT 0 COMMENT 'Performance < 3.0',
  `high_absence_flag` tinyint(1) DEFAULT 0 COMMENT 'Absence days > 15',
  `low_engagement_flag` tinyint(1) DEFAULT 0,
  `recent_complaints_flag` tinyint(1) DEFAULT 0,
  `performance_score` decimal(5,2) DEFAULT NULL,
  `absence_days` int(11) DEFAULT 0,
  `tenure_months` int(11) DEFAULT 0,
  `last_assessment_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_risk_assessment_backup`
--

CREATE TABLE `wfa_risk_assessment_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `employee_id` varchar(50) NOT NULL,
  `risk_level` enum('high','medium','low') DEFAULT 'low',
  `risk_score` decimal(5,2) DEFAULT 0.00 COMMENT '0-100 score',
  `risk_factors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of risk factors' CHECK (json_valid(`risk_factors`)),
  `low_performance_flag` tinyint(1) DEFAULT 0 COMMENT 'Performance < 3.0',
  `high_absence_flag` tinyint(1) DEFAULT 0 COMMENT 'Absence days > 15',
  `low_engagement_flag` tinyint(1) DEFAULT 0,
  `recent_complaints_flag` tinyint(1) DEFAULT 0,
  `performance_score` decimal(5,2) DEFAULT NULL,
  `absence_days` int(11) DEFAULT 0,
  `tenure_months` int(11) DEFAULT 0,
  `last_assessment_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wfa_risk_assessment_backup`
--

INSERT INTO `wfa_risk_assessment_backup` (`id`, `employee_id`, `risk_level`, `risk_score`, `risk_factors`, `low_performance_flag`, `high_absence_flag`, `low_engagement_flag`, `recent_complaints_flag`, `performance_score`, `absence_days`, `tenure_months`, `last_assessment_date`, `notes`, `created_at`, `updated_at`, `employee_id_new`) VALUES
(1, 'EMP001', 'low', 25.50, '[\"none\"]', 0, 0, 0, 0, 4.50, 2, 36, NULL, NULL, '2026-03-21 01:23:29', '2026-03-21 23:00:21', 1),
(2, 'EMP002', 'medium', 55.75, '[\"high_absence\", \"low_engagement\"]', 0, 1, 0, 0, 3.50, 18, 24, NULL, NULL, '2026-03-21 01:23:29', '2026-03-21 23:00:21', 2),
(3, 'EMP003', 'high', 78.25, '[\"low_performance\", \"high_absence\", \"tenure\"]', 1, 1, 0, 0, 2.50, 20, 6, NULL, NULL, '2026-03-21 01:23:29', '2026-03-21 23:00:21', 3);

-- --------------------------------------------------------

--
-- Table structure for table `wfa_salary_statistics`
--

CREATE TABLE `wfa_salary_statistics` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `department` varchar(100) NOT NULL,
  `employee_count` int(11) DEFAULT 0,
  `min_salary` decimal(12,2) DEFAULT 0.00,
  `max_salary` decimal(12,2) DEFAULT 0.00,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `median_salary` decimal(12,2) DEFAULT 0.00,
  `total_payroll` decimal(15,2) DEFAULT 0.00,
  `salary_variance` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_skill_gap_analysis`
--

CREATE TABLE `wfa_skill_gap_analysis` (
  `id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `skill_name` varchar(255) NOT NULL,
  `required_proficiency` varchar(50) DEFAULT NULL COMMENT 'Basic, Intermediate, Advanced, Expert',
  `current_proficiency_avg` varchar(50) DEFAULT NULL,
  `employees_with_skill` int(11) DEFAULT 0,
  `employees_needing_training` int(11) DEFAULT 0,
  `skill_gap_percentage` decimal(5,2) DEFAULT 0.00,
  `priority_level` enum('critical','high','medium','low') DEFAULT 'medium',
  `training_recommendations` text DEFAULT NULL,
  `last_assessed` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_tenure_analysis`
--

CREATE TABLE `wfa_tenure_analysis` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `tenure_bracket` varchar(50) NOT NULL COMMENT '0-1yr, 1-3yr, 3-5yr, 5-10yr, 10+ yr',
  `employee_count` int(11) DEFAULT 0,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `average_performance_score` decimal(5,2) DEFAULT 0.00,
  `department_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`department_breakdown`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `v_medicine_status`
--
DROP TABLE IF EXISTS `v_medicine_status`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_medicine_status`  AS SELECT `cm_medicine_inventory`.`medicine_id` AS `medicine_id`, `cm_medicine_inventory`.`medicine_name` AS `medicine_name`, `cm_medicine_inventory`.`category` AS `category`, `cm_medicine_inventory`.`current_stock` AS `current_stock`, `cm_medicine_inventory`.`reorder_level` AS `reorder_level`, `cm_medicine_inventory`.`unit_cost` AS `unit_cost`, `cm_medicine_inventory`.`expiry_date` AS `expiry_date`, CASE WHEN `cm_medicine_inventory`.`expiry_date` < curdate() THEN 'Expired' WHEN `cm_medicine_inventory`.`current_stock` <= `cm_medicine_inventory`.`reorder_level` THEN 'Low Stock' WHEN `cm_medicine_inventory`.`current_stock` = 0 THEN 'Out of Stock' ELSE 'Available' END AS `stock_status`, CASE WHEN `cm_medicine_inventory`.`expiry_date` <= curdate() + interval 30 day THEN 'Expiring Soon' ELSE 'OK' END AS `expiry_status` FROM `cm_medicine_inventory` ;

-- --------------------------------------------------------

--
-- Structure for view `v_patient_summary`
--
DROP TABLE IF EXISTS `v_patient_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_patient_summary`  AS SELECT `p`.`patient_id` AS `patient_id`, concat(`p`.`first_name`,' ',`p`.`last_name`) AS `full_name`, `p`.`patient_type` AS `patient_type`, `p`.`status` AS `status`, count(`mr`.`record_id`) AS `total_visits`, count(case when `mr`.`visit_date` >= curdate() - interval 30 day then 1 end) AS `visits_last_30_days`, max(`mr`.`visit_date`) AS `last_visit_date` FROM (`cm_patients` `p` left join `cm_medical_records` `mr` on(`p`.`patient_id` = `mr`.`patient_id`)) GROUP BY `p`.`patient_id` ;

-- --------------------------------------------------------

--
-- Structure for view `wfa_at_risk_employees_summary`
--
DROP TABLE IF EXISTS `wfa_at_risk_employees_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `wfa_at_risk_employees_summary`  AS SELECT `wfa_risk_assessment`.`risk_level` AS `risk_level`, count(0) AS `count`, round(avg(`wfa_risk_assessment`.`risk_score`),2) AS `avg_risk_score`, round(count(0) * 100.0 / (select count(0) from `wfa_risk_assessment` where cast(`wfa_risk_assessment`.`updated_at` as date) = curdate()),2) AS `percentage` FROM `wfa_risk_assessment` WHERE cast(`wfa_risk_assessment`.`updated_at` as date) = curdate() GROUP BY `wfa_risk_assessment`.`risk_level` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `table_name` (`table_name`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `biometric_devices`
--
ALTER TABLE `biometric_devices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `biometric_enrollments`
--
ALTER TABLE `biometric_enrollments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `biometric_logs`
--
ALTER TABLE `biometric_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cm_clinic_reports`
--
ALTER TABLE `cm_clinic_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_report_type` (`report_type`),
  ADD KEY `idx_report_date` (`report_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `cm_departments`
--
ALTER TABLE `cm_departments`
  ADD PRIMARY KEY (`department_id`),
  ADD KEY `idx_department_name` (`department_name`);

--
-- Indexes for table `cm_document_attachments`
--
ALTER TABLE `cm_document_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `idx_record_id` (`record_id`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_document_type` (`document_type`);

--
-- Indexes for table `cm_emergency_cases`
--
ALTER TABLE `cm_emergency_cases`
  ADD PRIMARY KEY (`case_id`),
  ADD KEY `idx_incident_date` (`incident_date`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_case_status` (`case_status`),
  ADD KEY `idx_severity_level` (`severity_level`);

--
-- Indexes for table `cm_medical_records`
--
ALTER TABLE `cm_medical_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `idx_visit_date` (`visit_date`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_consultation_type` (`consultation_type`);

--
-- Indexes for table `cm_medicine_inventory`
--
ALTER TABLE `cm_medicine_inventory`
  ADD PRIMARY KEY (`medicine_id`),
  ADD KEY `idx_medicine_name` (`medicine_name`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_expiry_date` (`expiry_date`);

--
-- Indexes for table `cm_medicine_usage_logs`
--
ALTER TABLE `cm_medicine_usage_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `record_id` (`record_id`),
  ADD KEY `idx_medicine_id` (`medicine_id`),
  ADD KEY `idx_usage_date` (`usage_date`);

--
-- Indexes for table `cm_patients`
--
ALTER TABLE `cm_patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD KEY `idx_patient_type` (`patient_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_employee_id` (`employee_id`);

--
-- Indexes for table `cm_suppliers`
--
ALTER TABLE `cm_suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `idx_supplier_name` (`supplier_name`);

--
-- Indexes for table `cm_vital_signs`
--
ALTER TABLE `cm_vital_signs`
  ADD PRIMARY KEY (`vital_sign_id`),
  ADD KEY `idx_record_id` (`record_id`),
  ADD KEY `idx_recorded_at` (`recorded_at`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `fk_employees_user_id` (`user_id`),
  ADD KEY `idx_employee_id` (`employee_id`);

--
-- Indexes for table `employees_contributions`
--
ALTER TABLE `employees_contributions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_contribution` (`employee_id`,`contribution_type`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_employee_accounts_employees` (`employee_id`);

--
-- Indexes for table `employee_badges`
--
ALTER TABLE `employee_badges`
  ADD PRIMARY KEY (`employee_badge_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `badge_id` (`badge_id`);

--
-- Indexes for table `employee_certifications`
--
ALTER TABLE `employee_certifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_certifications_employee_id` (`employee_id`);

--
-- Indexes for table `employee_change_history`
--
ALTER TABLE `employee_change_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_change_type` (`change_type`),
  ADD KEY `idx_effective_date` (`effective_date`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `employee_contributions`
--
ALTER TABLE `employee_contributions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_contribution` (`employee_id`,`contribution_type`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `employee_dependents`
--
ALTER TABLE `employee_dependents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_dependents_employee_id` (`employee_id`);

--
-- Indexes for table `employee_education`
--
ALTER TABLE `employee_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_education_employee_id` (`employee_id`);

--
-- Indexes for table `employee_emergency_contacts`
--
ALTER TABLE `employee_emergency_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_emergency_employee_id` (`employee_id`);

--
-- Indexes for table `employee_languages`
--
ALTER TABLE `employee_languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_languages_employee_id` (`employee_id`);

--
-- Indexes for table `employee_skills`
--
ALTER TABLE `employee_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_skills_employee_id` (`employee_id`);

--
-- Indexes for table `employee_work_experience`
--
ALTER TABLE `employee_work_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_work_exp_employee_id` (`employee_id`);

--
-- Indexes for table `exit_archive`
--
ALTER TABLE `exit_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exit_employee_settlements`
--
ALTER TABLE `exit_employee_settlements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exit_interviews`
--
ALTER TABLE `exit_interviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exit_interview_hr_assessments`
--
ALTER TABLE `exit_interview_hr_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `interview_id` (`interview_id`);

--
-- Indexes for table `exit_knowledge_transfer_items`
--
ALTER TABLE `exit_knowledge_transfer_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exit_knowledge_transfer_plans`
--
ALTER TABLE `exit_knowledge_transfer_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exit_resignations`
--
ALTER TABLE `exit_resignations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exit_terminations`
--
ALTER TABLE `exit_terminations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_termination_employee` (`employee_id`),
  ADD KEY `fk_termination_submitted_by` (`submitted_by`),
  ADD KEY `fk_termination_approved_by` (`approved_by`);

--
-- Indexes for table `lc_activity_alerts`
--
ALTER TABLE `lc_activity_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_alert_type` (`alert_type`),
  ADD KEY `idx_severity_level` (`severity_level`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_assigned_to` (`assigned_to`),
  ADD KEY `idx_related_entity` (`related_entity_type`,`related_entity_id`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- Indexes for table `lc_activity_log`
--
ALTER TABLE `lc_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `lc_alerts`
--
ALTER TABLE `lc_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_compliance` (`compliance_item_id`),
  ADD KEY `idx_recipient` (`recipient_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_is_resolved` (`is_resolved`);

--
-- Indexes for table `lc_allowances`
--
ALTER TABLE `lc_allowances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lc_attendance`
--
ALTER TABLE `lc_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `lc_compliance_items`
--
ALTER TABLE `lc_compliance_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indexes for table `lc_deductions`
--
ALTER TABLE `lc_deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lc_disciplinary_actions`
--
ALTER TABLE `lc_disciplinary_actions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `action_reference` (`action_reference`),
  ADD UNIQUE KEY `uk_action_reference` (`action_reference`),
  ADD KEY `idx_incident_id` (`incident_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_action_type` (`action_type`);

--
-- Indexes for table `lc_employees`
--
ALTER TABLE `lc_employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_no` (`employee_no`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `employment_type_id` (`employment_type_id`);

--
-- Indexes for table `lc_employee_adjustments`
--
ALTER TABLE `lc_employee_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `payroll_period_id` (`payroll_period_id`);

--
-- Indexes for table `lc_employee_allowances`
--
ALTER TABLE `lc_employee_allowances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `allowance_id` (`allowance_id`);

--
-- Indexes for table `lc_employee_categories`
--
ALTER TABLE `lc_employee_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lc_employee_compliance_records`
--
ALTER TABLE `lc_employee_compliance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_compliance` (`compliance_item_id`);

--
-- Indexes for table `lc_employee_contributions`
--
ALTER TABLE `lc_employee_contributions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_contribution` (`employee_id`,`contribution_type`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `lc_employee_deductions`
--
ALTER TABLE `lc_employee_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `deduction_id` (`deduction_id`);

--
-- Indexes for table `lc_employee_risk_assessments`
--
ALTER TABLE `lc_employee_risk_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_risk_level` (`risk_level`);

--
-- Indexes for table `lc_employee_salary`
--
ALTER TABLE `lc_employee_salary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `salary_structure_id` (`salary_structure_id`);

--
-- Indexes for table `lc_employee_shifts`
--
ALTER TABLE `lc_employee_shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indexes for table `lc_employment_history`
--
ALTER TABLE `lc_employment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `lc_employment_types`
--
ALTER TABLE `lc_employment_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lc_holidays`
--
ALTER TABLE `lc_holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lc_incidents`
--
ALTER TABLE `lc_incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reporter_id` (`reporter_id`),
  ADD KEY `idx_respondent_id` (`respondent_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_incident_type` (`incident_type`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_assigned_to` (`assigned_to`),
  ADD KEY `idx_incident_date` (`incident_date`);

--
-- Indexes for table `lc_incident_comments`
--
ALTER TABLE `lc_incident_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_incident_id` (`incident_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `lc_incident_evidence`
--
ALTER TABLE `lc_incident_evidence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_incident_id` (`incident_id`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`);

--
-- Indexes for table `lc_incident_notifications`
--
ALTER TABLE `lc_incident_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_incident_id` (`incident_id`),
  ADD KEY `idx_recipient_id` (`recipient_id`);

--
-- Indexes for table `lc_incident_people_involved`
--
ALTER TABLE `lc_incident_people_involved`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_incident_id` (`incident_id`),
  ADD KEY `idx_employee_id` (`employee_id`);

--
-- Indexes for table `lc_incident_status_history`
--
ALTER TABLE `lc_incident_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_incident_id` (`incident_id`),
  ADD KEY `idx_changed_by` (`changed_by`);

--
-- Indexes for table `lc_incident_types`
--
ALTER TABLE `lc_incident_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type_name` (`type_name`);

--
-- Indexes for table `lc_labor_law_compliance`
--
ALTER TABLE `lc_labor_law_compliance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_assigned_to` (`assigned_to`);

--
-- Indexes for table `lc_labor_law_compliance_attachments`
--
ALTER TABLE `lc_labor_law_compliance_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_compliance_id` (`compliance_id`);

--
-- Indexes for table `lc_labor_law_compliance_history`
--
ALTER TABLE `lc_labor_law_compliance_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_compliance_id` (`compliance_id`),
  ADD KEY `idx_changed_at` (`changed_at`);

--
-- Indexes for table `lc_leaves`
--
ALTER TABLE `lc_leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `lc_leave_documents`
--
ALTER TABLE `lc_leave_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lc_leave_requests`
--
ALTER TABLE `lc_leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_leave_type` (`leave_type`);

--
-- Indexes for table `lc_overtime`
--
ALTER TABLE `lc_overtime`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `lc_part_time_hours`
--
ALTER TABLE `lc_part_time_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `payroll_period_id` (`payroll_period_id`);

--
-- Indexes for table `lc_payroll_periods`
--
ALTER TABLE `lc_payroll_periods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lc_payroll_runs`
--
ALTER TABLE `lc_payroll_runs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_period_id` (`payroll_period_id`);

--
-- Indexes for table `lc_payslips`
--
ALTER TABLE `lc_payslips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_run_id` (`payroll_run_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `lc_payslip_items`
--
ALTER TABLE `lc_payslip_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payslip_id` (`payslip_id`);

--
-- Indexes for table `lc_policy_acknowledgments`
--
ALTER TABLE `lc_policy_acknowledgments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_policy_employee` (`policy_id`,`employee_id`),
  ADD KEY `idx_policy_id` (`policy_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `lc_policy_audit_log`
--
ALTER TABLE `lc_policy_audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_policy_id` (`policy_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `lc_policy_documents`
--
ALTER TABLE `lc_policy_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_department` (`department_owner`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `lc_policy_notifications`
--
ALTER TABLE `lc_policy_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_policy_id` (`policy_id`),
  ADD KEY `idx_recipient_id` (`recipient_id`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- Indexes for table `lc_policy_reminders`
--
ALTER TABLE `lc_policy_reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_acknowledgment_id` (`acknowledgment_id`),
  ADD KEY `idx_sent_by` (`sent_by`);

--
-- Indexes for table `lc_policy_versions`
--
ALTER TABLE `lc_policy_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_policy_id` (`policy_id`),
  ADD KEY `idx_is_current` (`is_current`);

--
-- Indexes for table `lc_positions`
--
ALTER TABLE `lc_positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `lc_report_notes`
--
ALTER TABLE `lc_report_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_period_id` (`payroll_period_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lc_salary_structures`
--
ALTER TABLE `lc_salary_structures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lc_shifts`
--
ALTER TABLE `lc_shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lc_statutory_contributions`
--
ALTER TABLE `lc_statutory_contributions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lc_tax_tables`
--
ALTER TABLE `lc_tax_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lc_users`
--
ALTER TABLE `lc_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `ld_archive`
--
ALTER TABLE `ld_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `archived_by` (`archived_by`),
  ADD KEY `restored_by` (`restored_by`),
  ADD KEY `original_created_by` (`original_created_by`),
  ADD KEY `archive_type` (`archive_type`),
  ADD KEY `archived_at` (`archived_at`),
  ADD KEY `restored` (`restored`);

--
-- Indexes for table `ld_certification`
--
ALTER TABLE `ld_certification`
  ADD PRIMARY KEY (`ld_certification_id`);

--
-- Indexes for table `ld_courses`
--
ALTER TABLE `ld_courses`
  ADD PRIMARY KEY (`ld_courses_id`);

--
-- Indexes for table `ld_enrollments`
--
ALTER TABLE `ld_enrollments`
  ADD PRIMARY KEY (`ld_enrollment_id`);

--
-- Indexes for table `ld_training_programs`
--
ALTER TABLE `ld_training_programs`
  ADD PRIMARY KEY (`ld_training_programs_id`);

--
-- Indexes for table `ld_training_requests`
--
ALTER TABLE `ld_training_requests`
  ADD PRIMARY KEY (`ld_request_id`),
  ADD KEY `idx_performance_request_id` (`performance_request_id`),
  ADD KEY `idx_employee_user_id` (`employee_user_id`);

--
-- Indexes for table `leave_documents`
--
ALTER TABLE `leave_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_leave_type` (`leave_type`);

--
-- Indexes for table `payroll_clearances`
--
ALTER TABLE `payroll_clearances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_settlement_id` (`settlement_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `personal_information`
--
ALTER TABLE `personal_information`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_360_competency_feedback`
--
ALTER TABLE `pm_360_competency_feedback`
  ADD PRIMARY KEY (`feedback_competency_id`),
  ADD KEY `feedback_id` (`feedback_id`),
  ADD KEY `competency_id` (`competency_id`);

--
-- Indexes for table `pm_360_feedback`
--
ALTER TABLE `pm_360_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_anonymous_verification`
--
ALTER TABLE `pm_anonymous_verification`
  ADD PRIMARY KEY (`verification_id`),
  ADD UNIQUE KEY `unique_feedback_verification` (`feedback_id`),
  ADD KEY `feedback_id` (`feedback_id`);

--
-- Indexes for table `pm_appraisals`
--
ALTER TABLE `pm_appraisals`
  ADD PRIMARY KEY (`appraisal_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_calibration_participants`
--
ALTER TABLE `pm_calibration_participants`
  ADD PRIMARY KEY (`participant_id`),
  ADD KEY `calibration_id` (`calibration_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_calibration_sessions`
--
ALTER TABLE `pm_calibration_sessions`
  ADD PRIMARY KEY (`calibration_id`),
  ADD KEY `facilitator_id` (`facilitator_id`);

--
-- Indexes for table `pm_competencies`
--
ALTER TABLE `pm_competencies`
  ADD PRIMARY KEY (`competency_id`);

--
-- Indexes for table `pm_competency_frameworks`
--
ALTER TABLE `pm_competency_frameworks`
  ADD PRIMARY KEY (`competency_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `pm_competency_indicators`
--
ALTER TABLE `pm_competency_indicators`
  ADD PRIMARY KEY (`indicator_id`),
  ADD KEY `competency_id` (`competency_id`);

--
-- Indexes for table `pm_feedback_action_plans`
--
ALTER TABLE `pm_feedback_action_plans`
  ADD PRIMARY KEY (`action_plan_id`),
  ADD KEY `feedback_id` (`feedback_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `pm_feedback_analytics`
--
ALTER TABLE `pm_feedback_analytics`
  ADD PRIMARY KEY (`analytics_id`),
  ADD UNIQUE KEY `unique_employee_analysis` (`employee_id`,`analysis_date`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_goals`
--
ALTER TABLE `pm_goals`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `idx_priority` (`priority`);

--
-- Indexes for table `pm_job_roles`
--
ALTER TABLE `pm_job_roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `pm_kpi_training_requests`
--
ALTER TABLE `pm_kpi_training_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_goal_id` (`goal_id`);

--
-- Indexes for table `pm_reports`
--
ALTER TABLE `pm_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_review_templates`
--
ALTER TABLE `pm_review_templates`
  ADD PRIMARY KEY (`template_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `pm_training_recommendations`
--
ALTER TABLE `pm_training_recommendations`
  ADD PRIMARY KEY (`recommendation_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_users`
--
ALTER TABLE `pm_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

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
-- Indexes for table `pr_employee_deductions`
--
ALTER TABLE `pr_employee_deductions`
  ADD PRIMARY KEY (`employee_deduction_id`),
  ADD KEY `fk_emp_ded_employee` (`employee_id`);

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
-- Indexes for table `pr_periods`
--
ALTER TABLE `pr_periods`
  ADD PRIMARY KEY (`period_id`);

--
-- Indexes for table `pr_position_deduction_rates`
--
ALTER TABLE `pr_position_deduction_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `position_type` (`position_type`),
  ADD KEY `idx_position_type` (`position_type`);

--
-- Indexes for table `pr_runs`
--
ALTER TABLE `pr_runs`
  ADD PRIMARY KEY (`run_id`);

--
-- Indexes for table `pr_tax_tables`
--
ALTER TABLE `pr_tax_tables`
  ADD PRIMARY KEY (`tax_id`);

--
-- Indexes for table `pr_teacher_loads`
--
ALTER TABLE `pr_teacher_loads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_teacher_semester` (`employee_id`,`academic_year`,`semester`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_academic_year` (`academic_year`);

--
-- Indexes for table `pr_teacher_qualification_rates`
--
ALTER TABLE `pr_teacher_qualification_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `qualification` (`qualification`),
  ADD KEY `idx_qualification` (`qualification`);

--
-- Indexes for table `rao_applications`
--
ALTER TABLE `rao_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rao_education`
--
ALTER TABLE `rao_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `rao_experience`
--
ALTER TABLE `rao_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `rao_hired`
--
ALTER TABLE `rao_hired`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rao_hired_applicants`
--
ALTER TABLE `rao_hired_applicants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_no` (`employee_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_employee_job` (`job_id`),
  ADD KEY `idx_application_link` (`application_id`);

--
-- Indexes for table `rao_interviews`
--
ALTER TABLE `rao_interviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `rao_jobs`
--
ALTER TABLE `rao_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rao_offer`
--
ALTER TABLE `rao_offer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rao_offer_salary`
--
ALTER TABLE `rao_offer_salary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_application` (`application_id`),
  ADD KEY `fk_offer_job` (`job_id`);

--
-- Indexes for table `rao_onboarding`
--
ALTER TABLE `rao_onboarding`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rao_parsed_resume`
--
ALTER TABLE `rao_parsed_resume`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_application` (`application_id`);

--
-- Indexes for table `ta_absence_late_records`
--
ALTER TABLE `ta_absence_late_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_date` (`absence_date`),
  ADD KEY `idx_status` (`excuse_status`);

--
-- Indexes for table `ta_attendance`
--
ALTER TABLE `ta_attendance`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `ta_leave_types`
--
ALTER TABLE `ta_leave_types`
  ADD PRIMARY KEY (`leave_type_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_users_employee_id` (`employee_id`);

--
-- Indexes for table `wfa_actions`
--
ALTER TABLE `wfa_actions`
  ADD PRIMARY KEY (`action_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_pip_id` (`pip_id`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `wfa_action_recommendations`
--
ALTER TABLE `wfa_action_recommendations`
  ADD PRIMARY KEY (`recommendation_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_recommended_action` (`recommended_action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `wfa_performance_actions`
--
ALTER TABLE `wfa_performance_actions`
  ADD PRIMARY KEY (`action_id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_date` (`created_date`);

--
-- Indexes for table `wfa_performance_improvement_plans`
--
ALTER TABLE `wfa_performance_improvement_plans`
  ADD PRIMARY KEY (`pip_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `wfa_performance_issues`
--
ALTER TABLE `wfa_performance_issues`
  ADD PRIMARY KEY (`issue_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_issue_type` (`issue_type`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_resolved` (`resolved`);

--
-- Indexes for table `wfa_pip_reviews`
--
ALTER TABLE `wfa_pip_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `reviewer_employee_id` (`reviewer_employee_id`),
  ADD KEY `idx_pip` (`pip_id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_review_date` (`review_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `biometric_devices`
--
ALTER TABLE `biometric_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `biometric_enrollments`
--
ALTER TABLE `biometric_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `biometric_logs`
--
ALTER TABLE `biometric_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `employees_contributions`
--
ALTER TABLE `employees_contributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `employee_badges`
--
ALTER TABLE `employee_badges`
  MODIFY `employee_badge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_certifications`
--
ALTER TABLE `employee_certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_change_history`
--
ALTER TABLE `employee_change_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `employee_contributions`
--
ALTER TABLE `employee_contributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_dependents`
--
ALTER TABLE `employee_dependents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_education`
--
ALTER TABLE `employee_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_emergency_contacts`
--
ALTER TABLE `employee_emergency_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_languages`
--
ALTER TABLE `employee_languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_skills`
--
ALTER TABLE `employee_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_work_experience`
--
ALTER TABLE `employee_work_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exit_archive`
--
ALTER TABLE `exit_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `exit_employee_settlements`
--
ALTER TABLE `exit_employee_settlements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `exit_interviews`
--
ALTER TABLE `exit_interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `exit_interview_hr_assessments`
--
ALTER TABLE `exit_interview_hr_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exit_knowledge_transfer_items`
--
ALTER TABLE `exit_knowledge_transfer_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `exit_knowledge_transfer_plans`
--
ALTER TABLE `exit_knowledge_transfer_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `exit_resignations`
--
ALTER TABLE `exit_resignations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `exit_terminations`
--
ALTER TABLE `exit_terminations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lc_activity_alerts`
--
ALTER TABLE `lc_activity_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lc_activity_log`
--
ALTER TABLE `lc_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `lc_alerts`
--
ALTER TABLE `lc_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lc_allowances`
--
ALTER TABLE `lc_allowances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lc_attendance`
--
ALTER TABLE `lc_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lc_compliance_items`
--
ALTER TABLE `lc_compliance_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `lc_deductions`
--
ALTER TABLE `lc_deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lc_disciplinary_actions`
--
ALTER TABLE `lc_disciplinary_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lc_employees`
--
ALTER TABLE `lc_employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lc_employee_adjustments`
--
ALTER TABLE `lc_employee_adjustments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lc_employee_allowances`
--
ALTER TABLE `lc_employee_allowances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lc_employee_categories`
--
ALTER TABLE `lc_employee_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lc_employee_compliance_records`
--
ALTER TABLE `lc_employee_compliance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `lc_employee_contributions`
--
ALTER TABLE `lc_employee_contributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `lc_employee_deductions`
--
ALTER TABLE `lc_employee_deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lc_employee_risk_assessments`
--
ALTER TABLE `lc_employee_risk_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lc_employee_salary`
--
ALTER TABLE `lc_employee_salary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lc_employee_shifts`
--
ALTER TABLE `lc_employee_shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_employment_history`
--
ALTER TABLE `lc_employment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_employment_types`
--
ALTER TABLE `lc_employment_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lc_holidays`
--
ALTER TABLE `lc_holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_incidents`
--
ALTER TABLE `lc_incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lc_incident_comments`
--
ALTER TABLE `lc_incident_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_incident_evidence`
--
ALTER TABLE `lc_incident_evidence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lc_incident_notifications`
--
ALTER TABLE `lc_incident_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_incident_people_involved`
--
ALTER TABLE `lc_incident_people_involved`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_incident_status_history`
--
ALTER TABLE `lc_incident_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_incident_types`
--
ALTER TABLE `lc_incident_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lc_labor_law_compliance`
--
ALTER TABLE `lc_labor_law_compliance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `lc_labor_law_compliance_attachments`
--
ALTER TABLE `lc_labor_law_compliance_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lc_labor_law_compliance_history`
--
ALTER TABLE `lc_labor_law_compliance_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_leaves`
--
ALTER TABLE `lc_leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lc_leave_documents`
--
ALTER TABLE `lc_leave_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lc_leave_requests`
--
ALTER TABLE `lc_leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lc_overtime`
--
ALTER TABLE `lc_overtime`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_part_time_hours`
--
ALTER TABLE `lc_part_time_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lc_payroll_periods`
--
ALTER TABLE `lc_payroll_periods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `lc_payroll_runs`
--
ALTER TABLE `lc_payroll_runs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `lc_payslips`
--
ALTER TABLE `lc_payslips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `lc_payslip_items`
--
ALTER TABLE `lc_payslip_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `lc_policy_acknowledgments`
--
ALTER TABLE `lc_policy_acknowledgments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `lc_policy_audit_log`
--
ALTER TABLE `lc_policy_audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `lc_policy_documents`
--
ALTER TABLE `lc_policy_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lc_policy_notifications`
--
ALTER TABLE `lc_policy_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lc_policy_reminders`
--
ALTER TABLE `lc_policy_reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_policy_versions`
--
ALTER TABLE `lc_policy_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `lc_positions`
--
ALTER TABLE `lc_positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lc_report_notes`
--
ALTER TABLE `lc_report_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_salary_structures`
--
ALTER TABLE `lc_salary_structures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lc_shifts`
--
ALTER TABLE `lc_shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_statutory_contributions`
--
ALTER TABLE `lc_statutory_contributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lc_tax_tables`
--
ALTER TABLE `lc_tax_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_users`
--
ALTER TABLE `lc_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ld_archive`
--
ALTER TABLE `ld_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ld_certification`
--
ALTER TABLE `ld_certification`
  MODIFY `ld_certification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ld_courses`
--
ALTER TABLE `ld_courses`
  MODIFY `ld_courses_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `ld_enrollments`
--
ALTER TABLE `ld_enrollments`
  MODIFY `ld_enrollment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ld_training_programs`
--
ALTER TABLE `ld_training_programs`
  MODIFY `ld_training_programs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ld_training_requests`
--
ALTER TABLE `ld_training_requests`
  MODIFY `ld_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `leave_documents`
--
ALTER TABLE `leave_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payroll_clearances`
--
ALTER TABLE `payroll_clearances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `personal_information`
--
ALTER TABLE `personal_information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pm_360_competency_feedback`
--
ALTER TABLE `pm_360_competency_feedback`
  MODIFY `feedback_competency_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pm_360_feedback`
--
ALTER TABLE `pm_360_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `pm_anonymous_verification`
--
ALTER TABLE `pm_anonymous_verification`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pm_appraisals`
--
ALTER TABLE `pm_appraisals`
  MODIFY `appraisal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pm_calibration_participants`
--
ALTER TABLE `pm_calibration_participants`
  MODIFY `participant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pm_calibration_sessions`
--
ALTER TABLE `pm_calibration_sessions`
  MODIFY `calibration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pm_competencies`
--
ALTER TABLE `pm_competencies`
  MODIFY `competency_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pm_competency_frameworks`
--
ALTER TABLE `pm_competency_frameworks`
  MODIFY `competency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pm_competency_indicators`
--
ALTER TABLE `pm_competency_indicators`
  MODIFY `indicator_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `pm_feedback_action_plans`
--
ALTER TABLE `pm_feedback_action_plans`
  MODIFY `action_plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pm_feedback_analytics`
--
ALTER TABLE `pm_feedback_analytics`
  MODIFY `analytics_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pm_goals`
--
ALTER TABLE `pm_goals`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pm_job_roles`
--
ALTER TABLE `pm_job_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pm_kpi_training_requests`
--
ALTER TABLE `pm_kpi_training_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pm_reports`
--
ALTER TABLE `pm_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pm_review_templates`
--
ALTER TABLE `pm_review_templates`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pm_training_recommendations`
--
ALTER TABLE `pm_training_recommendations`
  MODIFY `recommendation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pm_users`
--
ALTER TABLE `pm_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `pr_periods`
--
ALTER TABLE `pr_periods`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pr_position_deduction_rates`
--
ALTER TABLE `pr_position_deduction_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pr_runs`
--
ALTER TABLE `pr_runs`
  MODIFY `run_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pr_teacher_loads`
--
ALTER TABLE `pr_teacher_loads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `pr_teacher_qualification_rates`
--
ALTER TABLE `pr_teacher_qualification_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rao_applications`
--
ALTER TABLE `rao_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rao_education`
--
ALTER TABLE `rao_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rao_experience`
--
ALTER TABLE `rao_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rao_hired`
--
ALTER TABLE `rao_hired`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `rao_hired_applicants`
--
ALTER TABLE `rao_hired_applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rao_interviews`
--
ALTER TABLE `rao_interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `rao_jobs`
--
ALTER TABLE `rao_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rao_offer`
--
ALTER TABLE `rao_offer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `rao_offer_salary`
--
ALTER TABLE `rao_offer_salary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rao_onboarding`
--
ALTER TABLE `rao_onboarding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rao_parsed_resume`
--
ALTER TABLE `rao_parsed_resume`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ta_absence_late_records`
--
ALTER TABLE `ta_absence_late_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `ta_attendance`
--
ALTER TABLE `ta_attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=428;

--
-- AUTO_INCREMENT for table `ta_leave_types`
--
ALTER TABLE `ta_leave_types`
  MODIFY `leave_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wfa_actions`
--
ALTER TABLE `wfa_actions`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_action_recommendations`
--
ALTER TABLE `wfa_action_recommendations`
  MODIFY `recommendation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_performance_actions`
--
ALTER TABLE `wfa_performance_actions`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `wfa_performance_improvement_plans`
--
ALTER TABLE `wfa_performance_improvement_plans`
  MODIFY `pip_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_performance_issues`
--
ALTER TABLE `wfa_performance_issues`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_pip_reviews`
--
ALTER TABLE `wfa_pip_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_user_fk` FOREIGN KEY (`user_id`) REFERENCES `pm_users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `cm_document_attachments`
--
ALTER TABLE `cm_document_attachments`
  ADD CONSTRAINT `cm_document_attachments_ibfk_1` FOREIGN KEY (`record_id`) REFERENCES `cm_medical_records` (`record_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cm_document_attachments_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `cm_patients` (`patient_id`) ON DELETE CASCADE;

--
-- Constraints for table `cm_emergency_cases`
--
ALTER TABLE `cm_emergency_cases`
  ADD CONSTRAINT `cm_emergency_cases_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `cm_patients` (`patient_id`) ON DELETE CASCADE;

--
-- Constraints for table `cm_medical_records`
--
ALTER TABLE `cm_medical_records`
  ADD CONSTRAINT `cm_medical_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `cm_patients` (`patient_id`) ON DELETE CASCADE;

--
-- Constraints for table `cm_medicine_usage_logs`
--
ALTER TABLE `cm_medicine_usage_logs`
  ADD CONSTRAINT `cm_medicine_usage_logs_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `cm_medicine_inventory` (`medicine_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cm_medicine_usage_logs_ibfk_2` FOREIGN KEY (`record_id`) REFERENCES `cm_medical_records` (`record_id`) ON DELETE SET NULL;

--
-- Constraints for table `cm_vital_signs`
--
ALTER TABLE `cm_vital_signs`
  ADD CONSTRAINT `cm_vital_signs_ibfk_1` FOREIGN KEY (`record_id`) REFERENCES `cm_medical_records` (`record_id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_employees_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `lc_activity_alerts`
--
ALTER TABLE `lc_activity_alerts`
  ADD CONSTRAINT `fk_activity_alerts_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `lc_employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_activity_alerts_created_by` FOREIGN KEY (`created_by`) REFERENCES `lc_employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `lc_activity_log`
--
ALTER TABLE `lc_activity_log`
  ADD CONSTRAINT `fk_activity_log_user` FOREIGN KEY (`user_id`) REFERENCES `lc_employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `lc_attendance`
--
ALTER TABLE `lc_attendance`
  ADD CONSTRAINT `lc_attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `lc_employees` (`id`);

--
-- Constraints for table `lc_employees`
--
ALTER TABLE `lc_employees`
  ADD CONSTRAINT `lc_employees_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `lc_employee_categories` (`id`),
  ADD CONSTRAINT `lc_employees_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `lc_positions` (`id`),
  ADD CONSTRAINT `lc_employees_ibfk_3` FOREIGN KEY (`employment_type_id`) REFERENCES `lc_employment_types` (`id`);

--
-- Constraints for table `lc_employee_adjustments`
--
ALTER TABLE `lc_employee_adjustments`
  ADD CONSTRAINT `lc_employee_adjustments_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `lc_employees` (`id`),
  ADD CONSTRAINT `lc_employee_adjustments_ibfk_2` FOREIGN KEY (`payroll_period_id`) REFERENCES `lc_payroll_periods` (`id`);

--
-- Constraints for table `lc_employee_allowances`
--
ALTER TABLE `lc_employee_allowances`
  ADD CONSTRAINT `lc_employee_allowances_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `lc_employees` (`id`),
  ADD CONSTRAINT `lc_employee_allowances_ibfk_2` FOREIGN KEY (`allowance_id`) REFERENCES `lc_allowances` (`id`);

--
-- Constraints for table `lc_employee_deductions`
--
ALTER TABLE `lc_employee_deductions`
  ADD CONSTRAINT `lc_employee_deductions_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `lc_employees` (`id`),
  ADD CONSTRAINT `lc_employee_deductions_ibfk_2` FOREIGN KEY (`deduction_id`) REFERENCES `lc_deductions` (`id`);

--
-- Constraints for table `lc_employee_salary`
--
ALTER TABLE `lc_employee_salary`
  ADD CONSTRAINT `lc_employee_salary_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `lc_employees` (`id`),
  ADD CONSTRAINT `lc_employee_salary_ibfk_2` FOREIGN KEY (`salary_structure_id`) REFERENCES `lc_salary_structures` (`id`);

--
-- Constraints for table `lc_employee_shifts`
--
ALTER TABLE `lc_employee_shifts`
  ADD CONSTRAINT `lc_employee_shifts_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `lc_employees` (`id`),
  ADD CONSTRAINT `lc_employee_shifts_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `lc_shifts` (`id`);

--
-- Constraints for table `lc_employment_history`
--
ALTER TABLE `lc_employment_history`
  ADD CONSTRAINT `lc_employment_history_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `lc_employees` (`id`),
  ADD CONSTRAINT `lc_employment_history_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `lc_positions` (`id`);

--
-- Constraints for table `lc_leaves`
--
ALTER TABLE `lc_leaves`
  ADD CONSTRAINT `lc_leaves_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `lc_employees` (`id`);

--
-- Constraints for table `lc_overtime`
--
ALTER TABLE `lc_overtime`
  ADD CONSTRAINT `lc_overtime_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `lc_employees` (`id`);

--
-- Constraints for table `lc_part_time_hours`
--
ALTER TABLE `lc_part_time_hours`
  ADD CONSTRAINT `lc_part_time_hours_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `lc_employees` (`id`),
  ADD CONSTRAINT `lc_part_time_hours_ibfk_2` FOREIGN KEY (`payroll_period_id`) REFERENCES `lc_payroll_periods` (`id`);

--
-- Constraints for table `lc_payroll_runs`
--
ALTER TABLE `lc_payroll_runs`
  ADD CONSTRAINT `lc_payroll_runs_ibfk_1` FOREIGN KEY (`payroll_period_id`) REFERENCES `lc_payroll_periods` (`id`);

--
-- Constraints for table `lc_payslips`
--
ALTER TABLE `lc_payslips`
  ADD CONSTRAINT `lc_payslips_ibfk_1` FOREIGN KEY (`payroll_run_id`) REFERENCES `lc_payroll_runs` (`id`),
  ADD CONSTRAINT `lc_payslips_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `lc_employees` (`id`);

--
-- Constraints for table `lc_payslip_items`
--
ALTER TABLE `lc_payslip_items`
  ADD CONSTRAINT `lc_payslip_items_ibfk_1` FOREIGN KEY (`payslip_id`) REFERENCES `lc_payslips` (`id`);

--
-- Constraints for table `lc_policy_acknowledgments`
--
ALTER TABLE `lc_policy_acknowledgments`
  ADD CONSTRAINT `lc_policy_acknowledgments_ibfk_1` FOREIGN KEY (`policy_id`) REFERENCES `lc_policy_documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lc_policy_acknowledgments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `lc_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lc_policy_versions`
--
ALTER TABLE `lc_policy_versions`
  ADD CONSTRAINT `lc_policy_versions_ibfk_1` FOREIGN KEY (`policy_id`) REFERENCES `lc_policy_documents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lc_positions`
--
ALTER TABLE `lc_positions`
  ADD CONSTRAINT `lc_positions_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `lc_employee_categories` (`id`);

--
-- Constraints for table `lc_report_notes`
--
ALTER TABLE `lc_report_notes`
  ADD CONSTRAINT `lc_report_notes_ibfk_1` FOREIGN KEY (`payroll_period_id`) REFERENCES `lc_payroll_periods` (`id`),
  ADD CONSTRAINT `lc_report_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `lc_users` (`id`);

--
-- Constraints for table `ld_archive`
--
ALTER TABLE `ld_archive`
  ADD CONSTRAINT `ld_archive_ibfk_1` FOREIGN KEY (`archived_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ld_archive_ibfk_2` FOREIGN KEY (`restored_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ld_archive_ibfk_3` FOREIGN KEY (`original_created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pm_360_competency_feedback`
--
ALTER TABLE `pm_360_competency_feedback`
  ADD CONSTRAINT `fk_competency_feedback` FOREIGN KEY (`competency_id`) REFERENCES `pm_competencies` (`competency_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_feedback_competency` FOREIGN KEY (`feedback_id`) REFERENCES `pm_360_feedback` (`feedback_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_360_feedback`
--
ALTER TABLE `pm_360_feedback`
  ADD CONSTRAINT `pm_360_feedback_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_anonymous_verification`
--
ALTER TABLE `pm_anonymous_verification`
  ADD CONSTRAINT `fk_verification_feedback` FOREIGN KEY (`feedback_id`) REFERENCES `pm_360_feedback` (`feedback_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_appraisals`
--
ALTER TABLE `pm_appraisals`
  ADD CONSTRAINT `pm_appraisals_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_calibration_participants`
--
ALTER TABLE `pm_calibration_participants`
  ADD CONSTRAINT `fk_participant_calibration` FOREIGN KEY (`calibration_id`) REFERENCES `pm_calibration_sessions` (`calibration_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_participant_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_calibration_sessions`
--
ALTER TABLE `pm_calibration_sessions`
  ADD CONSTRAINT `fk_calibration_facilitator` FOREIGN KEY (`facilitator_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_competency_indicators`
--
ALTER TABLE `pm_competency_indicators`
  ADD CONSTRAINT `fk_competency_indicator` FOREIGN KEY (`competency_id`) REFERENCES `pm_competencies` (`competency_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_feedback_action_plans`
--
ALTER TABLE `pm_feedback_action_plans`
  ADD CONSTRAINT `fk_action_plan_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_action_plan_feedback` FOREIGN KEY (`feedback_id`) REFERENCES `pm_360_feedback` (`feedback_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_feedback_analytics`
--
ALTER TABLE `pm_feedback_analytics`
  ADD CONSTRAINT `fk_analytics_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_goals`
--
ALTER TABLE `pm_goals`
  ADD CONSTRAINT `pm_goals_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_reports`
--
ALTER TABLE `pm_reports`
  ADD CONSTRAINT `performance_reports_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_review_templates`
--
ALTER TABLE `pm_review_templates`
  ADD CONSTRAINT `template_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON DELETE SET NULL;

--
-- Constraints for table `pm_training_recommendations`
--
ALTER TABLE `pm_training_recommendations`
  ADD CONSTRAINT `pm_training_recommendations_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

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

--
-- Constraints for table `pr_employee_deductions`
--
ALTER TABLE `pr_employee_deductions`
  ADD CONSTRAINT `fk_emp_ded_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `pr_teacher_loads`
--
ALTER TABLE `pr_teacher_loads`
  ADD CONSTRAINT `pr_teacher_loads_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `rao_education`
--
ALTER TABLE `rao_education`
  ADD CONSTRAINT `rao_education_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `rao_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rao_experience`
--
ALTER TABLE `rao_experience`
  ADD CONSTRAINT `rao_experience_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `rao_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rao_interviews`
--
ALTER TABLE `rao_interviews`
  ADD CONSTRAINT `rao_interviews_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `rao_applications` (`id`);

--
-- Constraints for table `rao_offer_salary`
--
ALTER TABLE `rao_offer_salary`
  ADD CONSTRAINT `fk_offer_job` FOREIGN KEY (`job_id`) REFERENCES `rao_jobs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `rao_parsed_resume`
--
ALTER TABLE `rao_parsed_resume`
  ADD CONSTRAINT `fk_application` FOREIGN KEY (`application_id`) REFERENCES `rao_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ta_absence_late_records`
--
ALTER TABLE `ta_absence_late_records`
  ADD CONSTRAINT `fk_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `wfa_actions`
--
ALTER TABLE `wfa_actions`
  ADD CONSTRAINT `wfa_actions_ibfk_1` FOREIGN KEY (`pip_id`) REFERENCES `wfa_performance_improvement_plans` (`pip_id`) ON DELETE SET NULL;

--
-- Constraints for table `wfa_performance_actions`
--
ALTER TABLE `wfa_performance_actions`
  ADD CONSTRAINT `wfa_performance_actions_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `wfa_pip_reviews`
--
ALTER TABLE `wfa_pip_reviews`
  ADD CONSTRAINT `wfa_pip_reviews_ibfk_1` FOREIGN KEY (`pip_id`) REFERENCES `wfa_performance_improvement_plans` (`pip_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wfa_pip_reviews_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wfa_pip_reviews_ibfk_3` FOREIGN KEY (`reviewer_employee_id`) REFERENCES `employees` (`employee_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
