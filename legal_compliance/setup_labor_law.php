<?php
/**
 * Labor Law Compliance Setup Script
 * Creates the required database tables for the Labor Law Compliance module
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../auth/database.php';

$db = Database::getInstance()->getConnection();

echo "<h1>Labor Law Compliance Database Setup</h1>";

// SQL to create labor_law_compliance table
$sql1 = "CREATE TABLE IF NOT EXISTS `lc_labor_law_compliance` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `compliance_id` VARCHAR(50) DEFAULT NULL COMMENT 'Unique compliance code',
    `requirement_name` VARCHAR(255) NOT NULL,
    `category` ENUM('Government Contributions','Employee Benefits','Legal Documentation','Mandatory Reports','Workplace Safety','Tax Compliance') NOT NULL DEFAULT 'Government Contributions',
    `subcategory` VARCHAR(100) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `legal_basis` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('Compliant','Pending','Overdue','Not Applicable') DEFAULT 'Pending',
    `due_date` DATE DEFAULT NULL,
    `frequency` ENUM('Monthly','Quarterly','Semi-Annual','Yearly','One-time') DEFAULT 'Monthly',
    `assigned_to` INT(11) DEFAULT NULL,
    `attachments` TEXT DEFAULT NULL,
    `remarks` TEXT DEFAULT NULL,
    `last_checked` DATETIME DEFAULT NULL,
    `next_due_date` DATE DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_category` (`category`),
    KEY `idx_status` (`status`),
    KEY `idx_due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

try {
    $db->exec($sql1);
    echo "<p style='color: green;'>✓ Table 'lc_labor_law_compliance' created successfully!</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error creating lc_labor_law_compliance table: " . $e->getMessage() . "</p>";
}

// Check if table has data
$stmt = $db->query("SELECT COUNT(*) as count FROM lc_labor_law_compliance");
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['count'] == 0) {
    echo "<p>No data found. Inserting sample data...</p>";
    
    // Insert sample data
    $insertSql = "INSERT INTO `lc_labor_law_compliance` (`compliance_id`, `requirement_name`, `category`, `subcategory`, `description`, `legal_basis`, `status`, `due_date`, `frequency`, `assigned_to`, `last_checked`) VALUES
    ('SSS-001', 'Monthly SSS Contribution', 'Government Contributions', 'Social Security', 'Monthly SSS contribution remittance for all employees', 'RA 8282', 'Compliant', '2026-03-31', 'Monthly', 1, NOW()),
    ('SSS-002', 'Quarterly SSS ER Incentive', 'Government Contributions', 'Social Security', 'Quarterly SSS employer incentive filing', 'RA 8282', 'Pending', '2026-06-30', 'Quarterly', 1, NOW()),
    ('PH-001', 'Monthly PhilHealth Contribution', 'Government Contributions', 'Health Insurance', 'Monthly PhilHealth contribution remittance', 'RA 11223', 'Compliant', '2026-03-31', 'Monthly', 1, NOW()),
    ('PIBIG-001', 'Monthly Pag-IBIG Contribution', 'Government Contributions', 'Housing', 'Monthly Pag-IBIG contribution remittance', 'RA 9679', 'Compliant', '2026-03-31', 'Monthly', 1, NOW()),
    ('BIR-001', 'Monthly Withholding Tax', 'Tax Compliance', 'Income Tax', 'Monthly withholding tax on employee compensation', 'NTRC', 'Compliant', '2026-03-31', 'Monthly', 2, NOW()),
    ('BIR-002', 'Quarterly BIR Returns', 'Tax Compliance', 'Income Tax', 'Quarterly BIR returns filing', 'NTRC', 'Pending', '2026-04-15', 'Quarterly', 2, NOW()),
    ('DOLE-001', 'Annual Labor Inspection', 'Legal Documentation', 'Compliance', 'Annual labor standards compliance inspection', 'Labor Code', 'Compliant', '2026-12-31', 'Yearly', 1, NOW()),
    ('DOLE-002', 'Seminal PEME Report', 'Mandatory Reports', 'Health', 'Pre-Employment Medical Examination report', 'DOLE Department Order', 'Pending', '2026-06-30', 'Semi-Annual', 1, NOW()),
    ('SSS-003', 'Annual SSS Census', 'Government Contributions', 'Social Security', 'Annual employee census report to SSS', 'RA 8282', 'Overdue', '2026-01-31', 'Yearly', 1, NOW()),
    ('OSH-001', 'Quarterly Safety Training', 'Workplace Safety', 'Training', 'Quarterly occupational safety and health training', 'RA 11058', 'Compliant', '2026-03-31', 'Quarterly', 3, NOW())";
    
    try {
        $db->exec($insertSql);
        echo "<p style='color: green;'>✓ Sample data inserted successfully!</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Error inserting sample data: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: blue;'>✓ Table already has " . $result['count'] . " records.</p>";
}

// Display current data
echo "<h2>Current Data</h2>";
try {
    $stmt = $db->query("SELECT * FROM lc_labor_law_compliance ORDER BY id LIMIT 10");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($data) > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Compliance ID</th><th>Requirement</th><th>Category</th><th>Status</th><th>Due Date</th></tr>";
        foreach ($data as $row) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['compliance_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['requirement_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td>" . $row['due_date'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No records found.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error fetching data: " . $e->getMessage() . "</p>";
}

echo "<h2>Setup Complete!</h2>";
echo "<p><a href='labor_law_compliance.php'>Go to Labor Law Compliance Module</a></p>";
?>
