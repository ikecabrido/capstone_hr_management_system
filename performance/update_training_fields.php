<?php
require_once __DIR__ . "/../auth/database.php";

try {
    $db = Database::getInstance()->getConnection();
    
    // Add new columns if they don't exist
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS estimated_cost DECIMAL(10,2) DEFAULT 0.00 AFTER training_type");
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS training_provider VARCHAR(255) DEFAULT NULL AFTER estimated_cost");
    
    echo "Table 'pm_training_recommendations' updated successfully.";
} catch (PDOException $e) {
    echo "Error updating table: " . $e->getMessage();
}
?>
