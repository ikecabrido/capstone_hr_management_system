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

CREATE TABLE ep_notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM(
        'announcement',
        'payroll',
        'leave',
        'training',
        'performance',
        'document',
        'meeting',
        'compliance',
        'general'
    ) DEFAULT 'general',
    priority ENUM(
        'normal',
        'important',
        'urgent'
    ) DEFAULT 'normal',
    target_url VARCHAR(255) DEFAULT NULL,
    created_by_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ep_notification_recipients (
    recipient_id INT AUTO_INCREMENT PRIMARY KEY,
    notification_id INT NOT NULL,
    employee_id INT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_employee (employee_id),
    INDEX idx_notification (notification_id),
    INDEX idx_is_read (is_read),
    CONSTRAINT fk_notification
        FOREIGN KEY (notification_id)
        REFERENCES ep_notifications(notification_id)
        ON DELETE CASCADE
);


-- DUMMY DATA

INSERT INTO ep_notifications
(title, message, type, priority, target_url, created_by_user_id)
VALUES
('Company Orientation', 'Welcome to the company orientation scheduled on July 20, 2026.', 'announcement', 'normal', 'employee-announcements', 1),
('Payslip Available', 'Your payslip for June 2026 is now available.', 'payroll', 'important', 'employee-payslip', 1),
('Leave Request Approved', 'Your leave request has been approved.', 'leave', 'important', 'employee-leave-request', 2),
('Mandatory Cybersecurity Training', 'You have been enrolled in the Cybersecurity Awareness Training.', 'training', 'urgent', 'employee-training-programs', 2),
('Performance Evaluation', 'Your quarterly performance evaluation is now available.', 'performance', 'important', 'employee-performance', 1),
('Document Submission', 'Please submit your updated government IDs.', 'document', 'urgent', 'employee-documents', 3),
('Team Meeting', 'A department meeting is scheduled tomorrow at 10:00 AM.', 'meeting', 'normal', 'employee-meetings', 2),
('Compliance Reminder', 'Complete the annual compliance training before the deadline.', 'compliance', 'urgent', 'employee-compliance', 1),
('Holiday Announcement', 'The office will be closed on National Heroes Day.', 'announcement', 'normal', 'employee-announcements', 1),
('Payroll Reminder', 'Payroll processing will begin tomorrow.', 'payroll', 'normal', 'employee-payslip', 2),
('Training Certificate', 'Your training certificate is now available.', 'training', 'normal', 'employee-training-programs', 3),
('Performance Feedback', 'Your manager has submitted new performance feedback.', 'performance', 'important', 'employee-performance', 2),
('Document Verified', 'Your submitted employment documents have been verified.', 'document', 'normal', 'employee-documents', 1),
('Meeting Rescheduled', 'The monthly staff meeting has been moved to Friday.', 'meeting', 'important', 'employee-meetings', 2),
('Leave Request Pending', 'Your leave request is currently under review.', 'leave', 'normal', 'employee-leave-request', 1),
('Company Picnic', 'Join us for the annual company outing this Saturday.', 'announcement', 'normal', 'employee-announcements', 3),
('Salary Adjustment Notice', 'Your salary adjustment has been processed.', 'payroll', 'important', 'employee-payslip', 1),
('Training Reminder', 'Your enrolled training starts tomorrow.', 'training', 'important', 'employee-training-programs', 2),
('Compliance Deadline', 'Your compliance documents must be submitted this week.', 'compliance', 'urgent', 'employee-compliance', 3),
('Employee Survey', 'Please complete the annual employee satisfaction survey.', 'general', 'normal', 'employee-dashboard', 1);

INSERT INTO ep_notification_recipients
(notification_id, employee_id, is_read, read_at)
VALUES
(1, 1, 0, NULL),
(2, 2, 1, NOW()),
(3, 3, 0, NULL),
(4, 4, 0, NULL),
(5, 5, 1, NOW()),
(6, 6, 0, NULL),
(7, 7, 0, NULL),
(8, 8, 1, NOW()),
(9, 9, 0, NULL),
(10, 10, 0, NULL),
(11, 11, 1, NOW()),
(12, 12, 0, NULL),
(13, 13, 0, NULL),
(14, 14, 1, NOW()),
(15, 15, 0, NULL),
(16, 16, 0, NULL),
(17, 17, 1, NOW()),
(18, 18, 0, NULL),
(19, 19, 0, NULL),
(20, 20, 1, NOW());
