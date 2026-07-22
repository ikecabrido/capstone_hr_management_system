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