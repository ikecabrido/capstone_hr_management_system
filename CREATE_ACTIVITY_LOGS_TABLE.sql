-- Create activity_logs table for tracking login and system events
USE `hr_management`;

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `username` VARCHAR(255),
  `action` VARCHAR(100) COMMENT 'LOGIN, TIME_IN, TIME_OUT, etc',
  `details` TEXT,
  `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `timestamp` (`timestamp`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create index for faster queries
CREATE INDEX idx_activity_logs_timestamp ON activity_logs(timestamp DESC);
CREATE INDEX idx_activity_logs_action ON activity_logs(action);
CREATE INDEX idx_activity_logs_user_id ON activity_logs(user_id);

-- Verify table was created
SELECT TABLE_NAME FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'hr_management' AND TABLE_NAME = 'activity_logs';
