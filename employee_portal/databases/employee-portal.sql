CREATE TABLE `ep_employee_documents` (
  `approval_id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `remarks` TEXT DEFAULT NULL,
  `submitted_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `submit_by` INT(10) UNSIGNED DEFAULT NULL,
  `department` INT(10) UNSIGNED DEFAULT NULL,
  `approver_id` INT(10) UNSIGNED DEFAULT NULL,
  `approved_at` DATETIME DEFAULT NULL,
  `decision` ENUM('Approved','Rejected','Pending') DEFAULT 'Pending',
  `file_path` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`approval_id`),
  KEY `approver_id` (`approver_id`),
  KEY `department` (`department`),
  KEY `submit_by` (`submit_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE ep_online_meetings (
    meetings_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    meeting_link TEXT,
    created_by INT,
    employee_id INT,
    scheduled_at DATETIME,
    status ENUM('scheduled','completed','cancelled') DEFAULT 'scheduled'
);

-- NEW TABLES

CREATE TABLE `ep_notifications` (
  `notification_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('announcement','payroll','leave','training','performance','document','meeting','compliance','general') DEFAULT 'general',
  `priority` enum('normal','important','urgent') DEFAULT 'normal',
  `target_url` varchar(255) DEFAULT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ep_notifications`
--

INSERT INTO `ep_notifications` (`notification_id`, `title`, `message`, `type`, `priority`, `target_url`, `created_by_user_id`, `created_at`) VALUES
(1, 'Company Orientation', 'Welcome to the company orientation scheduled on July 20, 2026.', 'announcement', 'normal', 'employee-announcements', 1, '2026-07-15 14:13:02'),
(2, 'Payslip Available', 'Your payslip for June 2026 is now available.', 'payroll', 'important', 'employee-payslip', 1, '2026-07-15 14:13:02'),
(3, 'Leave Request Approved', 'Your leave request has been approved.', 'leave', 'important', 'employee-leave-request', 2, '2026-07-15 14:13:02'),
(4, 'Mandatory Cybersecurity Training', 'You have been enrolled in the Cybersecurity Awareness Training.', 'training', 'urgent', 'employee-training-programs', 2, '2026-07-15 14:13:02'),
(5, 'Performance Evaluation', 'Your quarterly performance evaluation is now available.', 'performance', 'important', 'employee-performance', 1, '2026-07-15 14:13:02'),
(6, 'Document Submission', 'Please submit your updated government IDs.', 'document', 'urgent', 'employee-documents', 3, '2026-07-15 14:13:02'),
(7, 'Team Meeting', 'A department meeting is scheduled tomorrow at 10:00 AM.', 'meeting', 'normal', 'employee-meetings', 2, '2026-07-15 14:13:02'),
(8, 'Compliance Reminder', 'Complete the annual compliance training before the deadline.', 'compliance', 'urgent', 'employee-compliance', 1, '2026-07-15 14:13:02'),
(9, 'Holiday Announcement', 'The office will be closed on National Heroes Day.', 'announcement', 'normal', 'employee-announcements', 1, '2026-07-15 14:13:02'),
(10, 'Payroll Reminder', 'Payroll processing will begin tomorrow.', 'payroll', 'normal', 'employee-payslip', 2, '2026-07-15 14:13:02'),
(11, 'Training Certificate', 'Your training certificate is now available.', 'training', 'normal', 'employee-training-programs', 3, '2026-07-15 14:13:02'),
(12, 'Performance Feedback', 'Your manager has submitted new performance feedback.', 'performance', 'important', 'employee-performance', 2, '2026-07-15 14:13:02'),
(13, 'Document Verified', 'Your submitted employment documents have been verified.', 'document', 'normal', 'employee-documents', 1, '2026-07-15 14:13:02'),
(14, 'Meeting Rescheduled', 'The monthly staff meeting has been moved to Friday.', 'meeting', 'important', 'employee-meetings', 2, '2026-07-15 14:13:02'),
(15, 'Leave Request Pending', 'Your leave request is currently under review.', 'leave', 'normal', 'employee-leave-request', 1, '2026-07-15 14:13:02'),
(16, 'Company Picnic', 'Join us for the annual company outing this Saturday.', 'announcement', 'normal', 'employee-announcements', 3, '2026-07-15 14:13:02'),
(17, 'Salary Adjustment Notice', 'Your salary adjustment has been processed.', 'payroll', 'important', 'employee-payslip', 1, '2026-07-15 14:13:02'),
(18, 'Training Reminder', 'Your enrolled training starts tomorrow.', 'training', 'important', 'employee-training-programs', 2, '2026-07-15 14:13:02'),
(19, 'Compliance Deadline', 'Your compliance documents must be submitted this week.', 'compliance', 'urgent', 'employee-compliance', 3, '2026-07-15 14:13:02'),
(20, 'Employee Survey', 'Please complete the annual employee satisfaction survey.', 'general', 'normal', 'employee-dashboard', 1, '2026-07-15 14:13:02'),


--
-- Indexes for dumped tables
--

--
-- Indexes for table `ep_notifications`
--
ALTER TABLE `ep_notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ep_notifications`
--
ALTER TABLE `ep_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;


CREATE TABLE `ep_notification_recipients` (
  `recipient_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ep_notification_recipients`
--

INSERT INTO `ep_notification_recipients` (`recipient_id`, `notification_id`, `employee_id`, `is_read`, `read_at`) VALUES
(1, 1, 1, 0, NULL),
(2, 2, 2, 1, '2026-07-15 14:13:02'),
(3, 3, 3, 0, NULL),
(4, 4, 4, 0, NULL),
(5, 5, 5, 1, '2026-07-15 14:13:02'),
(6, 6, 6, 0, NULL),
(7, 7, 7, 1, '2026-07-16 14:43:19'),
(8, 8, 8, 1, '2026-07-15 14:13:02'),
(9, 9, 9, 0, NULL),
(10, 10, 10, 0, NULL),
(11, 11, 11, 1, '2026-07-15 14:13:02'),
(12, 12, 12, 0, NULL),
(13, 13, 13, 0, NULL),
(14, 14, 14, 1, '2026-07-15 14:13:02'),
(15, 15, 15, 0, NULL),
(16, 16, 16, 0, NULL),
(17, 17, 17, 1, '2026-07-15 14:13:02'),
(18, 18, 18, 0, NULL),
(19, 19, 19, 0, NULL),
(20, 20, 20, 1, '2026-07-15 14:13:02'),


--
-- Indexes for dumped tables
--

--
-- Indexes for table `ep_notification_recipients`
--
ALTER TABLE `ep_notification_recipients`
  ADD PRIMARY KEY (`recipient_id`),
  ADD UNIQUE KEY `uq_notification_employee` (`notification_id`,`employee_id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_notification` (`notification_id`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ep_notification_recipients`
--
ALTER TABLE `ep_notification_recipients`
  MODIFY `recipient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ep_notification_recipients`
--
ALTER TABLE `ep_notification_recipients`
  ADD CONSTRAINT `fk_notification` FOREIGN KEY (`notification_id`) REFERENCES `ep_notifications` (`notification_id`) ON DELETE CASCADE;
COMMIT;

CREATE TABLE ep_employee_benefits (
    benefit_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,

    record_type ENUM(
        'SSS',
        'PhilHealth',
        'Pag-IBIG',
        'Withholding Tax',
        'BIR Form 2316'
    ) NOT NULL,

    period VARCHAR(20) NOT NULL,
    description TEXT NULL,

    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,

    uploaded_by INT NOT NULL,

    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE ep_employee_benefits
ADD CONSTRAINT fk_employee_benefits_employee
FOREIGN KEY (employee_id)
REFERENCES employees(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


-- NEW COLUMN ADDED
ALTER TABLE users
ADD COLUMN password_reset_token VARCHAR(255) NULL,
ADD COLUMN password_reset_expires DATETIME NULL;

--New table added

CREATE TABLE hr_management.ep_payroll_request (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,

    request_type VARCHAR(100) NOT NULL,
    purpose VARCHAR(255) NULL,
    remarks TEXT NULL,

    payroll_period_start DATE NULL,
    payroll_period_end DATE NULL,

    status ENUM(
        'Pending',
        'Processing',
        'Approved',
        'Rejected',
        'Completed',
        'Cancelled'
    ) NOT NULL DEFAULT 'Pending',

    requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL DEFAULT NULL,

    processed_by INT NULL,
    rejection_reason TEXT NULL,

    document_path VARCHAR(500) NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_employee_id (employee_id),
    INDEX idx_status (status),
    INDEX idx_requested_at (requested_at)
);

USE hr_management;

INSERT INTO ep_payroll_request (
    employee_id,
    request_type,
    purpose,
    remarks,
    payroll_period_start,
    payroll_period_end,
    status,
    requested_at,
    processed_at,
    processed_by,
    rejection_reason,
    document_path
) VALUES

-- 1. Ana Dela Cruz
(
    1,
    'Payslip',
    'Personal record',
    'Requesting a copy of my latest payslip.',
    '2026-07-01',
    '2026-07-15',
    'Completed',
    '2026-07-16 08:30:00',
    '2026-07-16 10:15:00',
    3,
    NULL,
    'uploads/payroll/payslip_001.pdf'
),

-- 2. Mark Dela Cruz
(
    2,
    'Payslip',
    'Loan application',
    'I need my payslip for a personal loan application.',
    '2026-07-01',
    '2026-07-15',
    'Approved',
    '2026-07-17 09:20:00',
    '2026-07-17 11:00:00',
    3,
    NULL,
    NULL
),

-- 3. John Dela Cruz
(
    3,
    'Certificate of Compensation',
    'Bank requirement',
    'Required for bank documentation.',
    NULL,
    NULL,
    'Processing',
    '2026-07-18 13:45:00',
    NULL,
    3,
    NULL,
    NULL
),

-- 4. Liza Dela Cruz
(
    4,
    'BIR Form 2316',
    'Tax filing',
    'Requesting a copy of BIR Form 2316 for tax filing.',
    '2025-01-01',
    '2025-12-31',
    'Completed',
    '2026-07-19 08:10:00',
    '2026-07-19 14:30:00',
    3,
    NULL,
    'uploads/payroll/bir_2316_004.pdf'
),

-- 5. Pedro Dela Cruz
(
    5,
    'Annual Payroll Summary',
    'Personal record',
    'Requesting annual payroll summary for personal documentation.',
    '2025-01-01',
    '2025-12-31',
    'Pending',
    '2026-07-20 10:25:00',
    NULL,
    NULL,
    NULL,
    NULL
),

-- 6. Karen Dela Cruz
(
    6,
    'Certificate of Employment with Compensation',
    'Visa application',
    'Required for visa application.',
    NULL,
    NULL,
    'Processing',
    '2026-07-21 14:15:00',
    NULL,
    3,
    NULL,
    NULL
),

-- 7. Robert Campos
(
    7,
    'Payslip',
    'Personal record',
    'Requesting payslip for the current payroll period.',
    '2026-07-16',
    '2026-07-31',
    'Pending',
    '2026-07-22 09:05:00',
    NULL,
    NULL,
    NULL,
    NULL
),

-- 38. Juan Dela Cruz
(
    38,
    'Other Payroll Document',
    'Employment documentation',
    'Requesting a payroll-related document for employment documentation.',
    NULL,
    NULL,
    'Rejected',
    '2026-07-23 15:40:00',
    '2026-07-24 09:10:00',
    3,
    'Requested document is not available through the payroll office.',
    NULL
),

-- 39. Maria Santos
(
    39,
    'Payslip',
    'Personal record',
    'Requesting a copy of my latest payslip.',
    '2026-07-16',
    '2026-07-31',
    'Pending',
    '2026-07-24 08:30:00',
    NULL,
    NULL,
    NULL,
    NULL
),

-- 40. Pedro Reyes
(
    40,
    'Certificate of Compensation',
    'Loan application',
    'Required for a loan application.',
    NULL,
    NULL,
    'Approved',
    '2026-07-24 10:15:00',
    '2026-07-24 13:20:00',
    3,
    NULL,
    NULL
),

-- 41. Ana Garcia
(
    41,
    'BIR Form 2316',
    'Tax filing',
    'Requesting BIR Form 2316 for tax documentation.',
    '2025-01-01',
    '2025-12-31',
    'Completed',
    '2026-07-25 09:00:00',
    '2026-07-25 11:30:00',
    3,
    NULL,
    'uploads/payroll/bir_2316_041.pdf'
),

-- 42. Carlos Mendoza
(
    42,
    'Annual Payroll Summary',
    'Personal record',
    'Requesting annual payroll information.',
    '2025-01-01',
    '2025-12-31',
    'Processing',
    '2026-07-25 10:45:00',
    NULL,
    3,
    NULL,
    NULL
),

-- 43. Sofia Ramirez
(
    43,
    'Payslip',
    'Personal record',
    'Requesting my latest payslip.',
    '2026-07-16',
    '2026-07-31',
    'Pending',
    '2026-07-26 08:20:00',
    NULL,
    NULL,
    NULL,
    NULL
),

-- 44. Michael Torres
(
    44,
    'Certificate of Employment with Compensation',
    'Loan application',
    'Required for a bank loan application.',
    NULL,
    NULL,
    'Approved',
    '2026-07-26 09:30:00',
    '2026-07-26 14:00:00',
    3,
    NULL,
    NULL
),

-- 45. Elizabeth Cruz
(
    45,
    'Payslip',
    'Personal record',
    'Requesting a copy of my latest payslip.',
    '2026-07-16',
    '2026-07-31',
    'Completed',
    '2026-07-26 13:15:00',
    '2026-07-27 09:00:00',
    3,
    NULL,
    'uploads/payroll/payslip_045.pdf'
),

-- 46. Daniel Flores
(
    46,
    'Other Payroll Document',
    'Employment requirement',
    'Requesting a payroll-related document for an employment requirement.',
    NULL,
    NULL,
    'Pending',
    '2026-07-27 10:20:00',
    NULL,
    NULL,
    NULL,
    NULL
),

-- 47. Patricia Aquino
(
    47,
    'BIR Form 2316',
    'Tax filing',
    'Requesting a copy for tax filing purposes.',
    '2025-01-01',
    '2025-12-31',
    'Rejected',
    '2026-07-27 14:10:00',
    '2026-07-28 08:30:00',
    3,
    'The requested tax document is not yet available.',
    NULL
);

INSERT INTO `employees` (`id`, `user_id`, `employee_no`, `full_name`, `category_id`, `position_id`, `employment_type_id`, `employment_status`, `department`, `date_hired`) VALUES
(4, 4, 'S002', 'Liza Dela Cruz', 2, 4, 2, 'active', NULL, '2022-02-20'),
(5, 1, 'S003', 'Pedro Dela Cruz', 2, 5, 1, 'active', NULL, '2019-11-05'),
(6, 3, 'T003', 'Karen Dela Cruz', 1, 1, 3, 'active', NULL, '2024-08-01'),
(7, 2, 'T004', 'Robert Campos', 1, 1, 3, 'active', NULL, '2024-08-01'),
(38, 1, 'T011', 'Juan Dela Cruz', 1, 1, 1, 'active', 'College Department', '2021-06-01'),
(39, 2, 'T012', 'Maria Santos', 1, 1, 1, 'active', 'College Department', '2021-08-15'),
(40, 3, 'T013', 'Pedro Reyes', 1, 1, 1, 'active', 'College Department', '2022-01-10'),
(41, 4, 'T014', 'Ana Garcia', 1, 1, 1, 'active', 'College Department', '2022-06-01'),
(42, 5, 'T005', 'Carlos Mendoza', 1, 1, 1, 'active', 'College Department', '2022-08-15'),
(43, 6, 'T006', 'Sofia Ramirez', 1, 1, 1, 'active', 'College Department', '2023-01-10'),
(44, 7, 'T007', 'Michael Torres', 1, 1, 1, 'active', 'College Department', '2023-06-01'),
(45, 8, 'T008', 'Elizabeth Cruz', 1, 1, 1, 'active', 'College Department', '2023-08-15'),
(46, 9, 'T009', 'Daniel Flores', 1, 1, 1, 'active', 'College Department', '2024-01-10'),
(47, 10, 'T010', 'Patricia Aquino', 1, 1, 1, 'active', 'College Department', '2024-06-01');