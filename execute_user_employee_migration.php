<?php
/**
 * Execute the user_id foreign key migration for employees table
 * This adds the user_id column and creates proper linking to users table
 */

// Direct database connection
try {
    $conn = new PDO(
        'mysql:host=localhost;dbname=hr_management;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

echo "<h2>User-Employee Linking Migration</h2>";

try {
    // Step 1: Check if user_id column already exists
    $checkStmt = $conn->query("SHOW COLUMNS FROM employees WHERE Field = 'user_id'");
    $columnExists = $checkStmt->rowCount() > 0;
    
    if ($columnExists) {
        echo "<p style='color: blue;'>✓ user_id column already exists</p>";
    } else {
        echo "<p>Adding user_id column to employees table...</p>";
        $conn->exec("ALTER TABLE `employees` ADD `user_id` INT NULL DEFAULT NULL AFTER `employee_id`");
        echo "<p style='color: green;'>✓ user_id column added</p>";
    }
    
    // Step 2: Link existing employees to users
    echo "<p>Linking existing employees to users...</p>";
    $conn->exec("UPDATE `employees` SET `user_id` = `employee_id` WHERE `user_id` IS NULL");
    echo "<p style='color: green;'>✓ Existing employees linked</p>";
    
    // Step 3: Check if foreign key already exists
    $fkCheck = $conn->query("
        SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'employees' AND COLUMN_NAME = 'user_id' 
        AND REFERENCED_TABLE_NAME = 'users'
    ");
    $fkExists = $fkCheck->rowCount() > 0;
    
    if ($fkExists) {
        echo "<p style='color: blue;'>✓ Foreign key constraint already exists</p>";
    } else {
        echo "<p>Adding foreign key constraint...</p>";
        $conn->exec("
            ALTER TABLE `employees` 
            ADD CONSTRAINT `fk_employees_user_id` 
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ");
        echo "<p style='color: green;'>✓ Foreign key constraint added</p>";
    }
    
    // Step 4: Create/update employee record for user 29
    echo "<p>Creating/updating employee record for user 29...</p>";
    $stmt = $conn->prepare("
        SELECT employee_id FROM employees WHERE user_id = ? LIMIT 1
    ");
    $stmt->execute([29]);
    
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: blue;'>✓ Employee record for user 29 already exists</p>";
    } else {
        // Check if there's an employee_id 29
        $stmt = $conn->prepare("SELECT * FROM employees WHERE employee_id = 29");
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            // Update existing employee_id 29
            $updateStmt = $conn->prepare("UPDATE employees SET user_id = 29 WHERE employee_id = 29");
            $updateStmt->execute();
            echo "<p style='color: green;'>✓ Updated existing employee 29 with user_id</p>";
        } else {
            // Create new employee record
            $insertStmt = $conn->prepare("
                INSERT INTO employees (employee_id, user_id, full_name, address, contact_number, email, department, position, date_hired, employment_status)
                VALUES (29, 29, 'Employee 29', 'Test Address', '09999999999', 'emp29@example.com', 'Testing', 'Employee', CURDATE(), 'Active')
            ");
            $insertStmt->execute();
            echo "<p style='color: green;'>✓ Created employee record for user 29</p>";
        }
    }
    
    // Step 5: Verify the setup
    echo "<p><strong>Verification:</strong></p>";
    
    // Check column
    $verifyCol = $conn->query("SHOW COLUMNS FROM employees WHERE Field = 'user_id'");
    echo "<p>user_id column: " . ($verifyCol->rowCount() > 0 ? "✓ Present" : "✗ Missing") . "</p>";
    
    // Check FK
    $verifyFK = $conn->query("
        SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'employees' AND COLUMN_NAME = 'user_id' 
        AND REFERENCED_TABLE_NAME = 'users'
    ");
    echo "<p>Foreign key constraint: " . ($verifyFK->rowCount() > 0 ? "✓ Present" : "✗ Missing") . "</p>";
    
    // Check user 29 employee
    $stmt = $conn->prepare("SELECT * FROM employees WHERE user_id = 29");
    $stmt->execute();
    $emp29 = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Employee for user 29: " . ($emp29 ? "✓ Found (ID: " . $emp29['employee_id'] . ")" : "✗ Not found") . "</p>";
    
    echo "<hr>";
    echo "<h3 style='color: green;'>✓ Migration completed successfully!</h3>";
    echo "<p>The employees table now has proper user_id foreign key linking.</p>";
    echo "<p><a href='employee_portal/time_attendance_portal/index.php'>Go to Time & Attendance Portal</a></p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>✗ Migration failed</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
