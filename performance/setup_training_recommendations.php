<?php
require_once __DIR__ . "/../auth/database.php";

try {
    $db = Database::getInstance()->getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS `pm_training_recommendations` (
      `recommendation_id` int(11) NOT NULL AUTO_INCREMENT,
      `employee_id` varchar(50) NOT NULL,
      `skill_gaps` text NOT NULL,
      `training_program` varchar(255) NOT NULL,
      `training_type` enum('Online Course', 'Workshop', 'Seminar', 'Internal Training') NOT NULL,
      `priority_level` enum('High', 'Medium', 'Low') NOT NULL,
      `suggested_completion_date` date NOT NULL,
      `remarks` text DEFAULT NULL,
      `status` enum('Proposed', 'In Progress', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Proposed',
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`recommendation_id`),
      KEY `employee_id` (`employee_id`),
      CONSTRAINT `pm_training_recommendations_employee_fk` 
        FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) 
        ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    $db->exec($sql);
    echo "Table 'pm_training_recommendations' created or already exists successfully.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
