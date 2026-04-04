<?php
require_once __DIR__ . "/../auth/database.php";

try {
    $db = Database::getInstance()->getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS `pm_appraisals` (
      `appraisal_id` int(11) NOT NULL AUTO_INCREMENT,
      `employee_id` varchar(50) NOT NULL,
      `review_period` enum('Quarterly', 'Annual', 'Mid-Year') NOT NULL,
      `goals_kpis` text NOT NULL,
      `performance_ratings` text NOT NULL, -- Storing ratings as JSON or serialized array
      `manager_evaluation` text NOT NULL,
      `overall_score` decimal(5,2) NOT NULL,
      `comments` text DEFAULT NULL,
      `review_date` date NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`appraisal_id`),
      KEY `employee_id` (`employee_id`),
      CONSTRAINT `pm_appraisals_employee_fk` 
        FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) 
        ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    $db->exec($sql);
    echo "Table 'pm_appraisals' created or already exists successfully.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
