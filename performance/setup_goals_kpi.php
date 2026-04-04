<?php
require_once __DIR__ . "/../auth/database.php";

try {
    $db = Database::getInstance()->getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS `pm_goals` (
      `goal_id` int(11) NOT NULL AUTO_INCREMENT,
      `employee_id` varchar(50) NOT NULL,
      `goal_title` varchar(255) NOT NULL,
      `kpi_name` varchar(255) NOT NULL,
      `target_value` decimal(10,2) NOT NULL DEFAULT 100.00,
      `current_progress` decimal(10,2) NOT NULL DEFAULT 0.00,
      `status` enum('On Track', 'Delayed', 'Completed') NOT NULL DEFAULT 'On Track',
      `start_date` date NOT NULL,
      `end_date` date NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`goal_id`),
      KEY `employee_id` (`employee_id`),
      CONSTRAINT `pm_goals_employee_fk` 
        FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) 
        ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    $db->exec($sql);
    echo "Table 'pm_goals' created or already exists successfully.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
