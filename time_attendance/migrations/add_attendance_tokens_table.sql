-- Add attendance_tokens table to hr_management database
-- This table is required for QR code token generation and validation

CREATE TABLE IF NOT EXISTS `attendance_tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `generated_by` int(11) NOT NULL,
  `generated_for_date` date NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `used_by` int(11) DEFAULT NULL,
  `used_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`token_id`),
  UNIQUE KEY `token` (`token`),
  KEY `generated_by` (`generated_by`),
  KEY `used_by` (`used_by`),
  KEY `idx_token` (`token`),
  KEY `idx_used` (`used`),
  KEY `idx_expires_at` (`expires_at`),
  CONSTRAINT `attendance_tokens_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`),
  CONSTRAINT `attendance_tokens_ibfk_2` FOREIGN KEY (`used_by`) REFERENCES `employees` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
