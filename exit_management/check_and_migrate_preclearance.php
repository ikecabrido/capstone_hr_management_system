<?php
/**
 * Check if preclearance_desk_person column exists in exit_resignations table
 * If not, execute the migration to add it
 */

require_once '../auth/database.php';

$db = Database::getInstance()->getConnection();
$dbName = 'hr_management';
$tableName = 'exit_resignations';
$columnName = 'preclearance_desk_person';

try {
    // Check if column exists
    $checkQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = ? 
                   AND TABLE_NAME = ? 
                   AND COLUMN_NAME = ?";
    
    $stmt = $db->prepare($checkQuery);
    $stmt->execute([$dbName, $tableName, $columnName]);
    $columnExists = $stmt->rowCount() > 0;
    
    if ($columnExists) {
        echo "✓ Column '{$columnName}' already exists in '{$tableName}' table.";
        exit(0);
    }
    
    // Column doesn't exist, execute migration
    echo "Column '{$columnName}' does not exist. Executing migration...\n\n";
    
    // Add the column
    $alterQuery = "ALTER TABLE `exit_resignations` 
                   ADD COLUMN `preclearance_desk_person` INT DEFAULT NULL AFTER `submitted_by`";
    
    $db->exec($alterQuery);
    echo "✓ Added '{$columnName}' column to '{$tableName}' table\n";
    
    // Add index
    $indexQuery = "ALTER TABLE `exit_resignations` 
                   ADD KEY `fk_resignation_preclearance_desk` (`preclearance_desk_person`)";
    
    $db->exec($indexQuery);
    echo "✓ Added index on '{$columnName}' column\n";
    
    // Add foreign key constraint
    $fkQuery = "ALTER TABLE `exit_resignations` 
                ADD CONSTRAINT `fk_resignation_preclearance_desk` 
                FOREIGN KEY (`preclearance_desk_person`) 
                REFERENCES `users` (`id`) 
                ON DELETE SET NULL";
    
    $db->exec($fkQuery);
    echo "✓ Added foreign key constraint\n\n";
    
    echo "Migration completed successfully!";
    exit(0);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit(1);
}
