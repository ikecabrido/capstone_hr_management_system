<?php
require_once __DIR__ . '/../auth/database.php';

$db = Database::getInstance()->getConnection();

echo "Creating employee_settlements table...\n";

$createTableSQL = "
CREATE TABLE IF NOT EXISTS `employee_settlements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT NOT NULL,
  `resignation_id` INT,
  `basic_salary` DECIMAL(12,2) DEFAULT 0,
  `hra` DECIMAL(12,2) DEFAULT 0,
  `conveyance` DECIMAL(12,2) DEFAULT 0,
  `lta` DECIMAL(12,2) DEFAULT 0,
  `medical_allowance` DECIMAL(12,2) DEFAULT 0,
  `other_allowances` DECIMAL(12,2) DEFAULT 0,
  `provident_fund` DECIMAL(12,2) DEFAULT 0,
  `gratuity` DECIMAL(12,2) DEFAULT 0,
  `notice_pay` DECIMAL(12,2) DEFAULT 0,
  `outstanding_loans` DECIMAL(12,2) DEFAULT 0,
  `other_deductions` DECIMAL(12,2) DEFAULT 0,
  `net_payable` DECIMAL(12,2) DEFAULT 0,
  `settlement_date` DATE,
  `status` VARCHAR(50) DEFAULT 'draft',
  `approved_by` INT,
  `approved_at` TIMESTAMP NULL,
  `created_by` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
  FOREIGN KEY (resignation_id) REFERENCES resignations(id),
  INDEX idx_employee (employee_id),
  INDEX idx_status (status),
  INDEX idx_settlement_date (settlement_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $db->exec($createTableSQL);
    echo "✓ Table 'employee_settlements' created successfully!\n\n";
    
    // Check if table exists
    $result = $db->query("DESCRIBE employee_settlements");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Table structure:\n";
    echo "═════════════════════════════════════════════════════\n";
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")";
        if ($col['Null'] === 'NO') echo " NOT NULL";
        if ($col['Key']) echo " [" . $col['Key'] . "]";
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error creating table: " . $e->getMessage() . "\n";
}

?>
