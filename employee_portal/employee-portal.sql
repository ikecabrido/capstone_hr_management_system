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