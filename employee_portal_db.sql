-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_type_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Cancelled','Completed') DEFAULT 'Pending',
  `admin_remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_types`
--

CREATE TABLE `request_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `requires_attachment` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_types`
--

INSERT INTO `request_types` (`id`, `name`, `description`, `icon`, `requires_attachment`, `is_active`, `created_at`) VALUES
(1, 'Leave Request', 'Request for vacation leave, sick leave, or emergency leave.', 'fa-calendar-days', 1, 1, '2026-03-01 08:54:25'),
(2, 'Training Request', 'Request approval to attend seminars, workshops, or professional training.', 'fa-graduation-cap', 1, 1, '2026-03-01 08:54:25'),
(3, 'Overtime Request', 'Request approval for overtime work beyond regular schedule.', 'fa-clock', 0, 1, '2026-03-01 08:54:25'),
(4, 'Certificate of Employment', 'Request issuance of Certificate of Employment (COE).', 'fa-file-lines', 0, 1, '2026-03-01 08:54:25'),
(5, 'Schedule Adjustment', 'Request change of teaching or working schedule.', 'fa-calendar-check', 0, 1, '2026-03-01 08:54:25'),
(7, 'sample_3', 'nigga nigga', 'fa-users', 1, 1, '2026-03-01 15:57:44');

-- --------------------------------------------------------