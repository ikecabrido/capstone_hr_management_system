<?php
session_start();
require_once "../auth/auth_check.php";
require_once "../auth/database.php";

echo "<h1>Database Table Check</h1>";

try {
    $db = Database::getInstance()->getConnection();

    // Check if payroll_clearances table exists
    $stmt = $db->query("SHOW TABLES LIKE 'payroll_clearances'");
    $tableExists = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tableExists) {
        echo "<p>✅ payroll_clearances table exists</p>";

        // Show table structure
        $stmt = $db->query("DESCRIBE payroll_clearances");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h3>Table Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "<td>{$col['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";

        // Show sample data
        $stmt = $db->query("SELECT * FROM payroll_clearances LIMIT 5");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($data) {
            echo "<h3>Sample Data:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Settlement ID</th><th>Status</th><th>Requested At</th><th>Comments</th></tr>";
            foreach ($data as $row) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['settlement_id']}</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td>{$row['requested_at']}</td>";
                echo "<td>{$row['comments']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No data in payroll_clearances table</p>";
        }

    } else {
        echo "<p>❌ payroll_clearances table does not exist</p>";

        // Try to create it
        echo "<h3>Attempting to create table...</h3>";
        $createSQL = "
            CREATE TABLE IF NOT EXISTS `payroll_clearances` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `settlement_id` int(11) NOT NULL,
              `requested_by` int(11) DEFAULT NULL,
              `requested_at` datetime NOT NULL DEFAULT current_timestamp(),
              `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
              `approved_by` int(11) DEFAULT NULL,
              `approved_at` datetime DEFAULT NULL,
              `comments` text DEFAULT NULL,
              `last_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`),
              KEY `idx_settlement_id` (`settlement_id`),
              KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Payroll clearance requests linked to exit settlements'
        ";

        try {
            $db->exec($createSQL);
            echo "<p>✅ Table created successfully</p>";

            // Set AUTO_INCREMENT to 1 if it's 0
            $db->exec("ALTER TABLE payroll_clearances AUTO_INCREMENT = 1");

            // Add foreign key constraint
            try {
                $db->exec("ALTER TABLE `payroll_clearances` ADD CONSTRAINT `fk_clearance_settlement` FOREIGN KEY (`settlement_id`) REFERENCES `exit_employee_settlements` (`id`) ON DELETE CASCADE");
                echo "<p>✅ Foreign key constraint added</p>";
            } catch (Exception $fkError) {
                echo "<p>⚠️ Foreign key constraint may already exist or settlements table missing: " . $fkError->getMessage() . "</p>";
            }
        } catch (Exception $createError) {
            echo "<p>❌ Failed to create table: " . $createError->getMessage() . "</p>";
        }
    }

} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='exit_management.php'>Back to Exit Management</a></p>";
echo "<p><a href='test_payroll_clearance.php'>Test Payroll Clearance Integration</a></p>";
?>