<?php
$pdo = new PDO('mysql:host=localhost;dbname=data_hr', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// First, get the list of active employees
$stmt = $pdo->query("SELECT employee_id, full_name, position FROM employees WHERE employment_status='Active'");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "=== Creating sample recruitment data ===\n";
echo "Found " . count($employees) . " active employees\n\n";

// Salary mapping based on position
$salaryMap = [
    'Software Developer' => 35000,
    'HR Manager' => 30000,
    'Accountant' => 28000,
    'Professor' => 40000,
    'Associate Professor' => 40000,
    'Administrative Officer' => 25000,
    'default' => 30000
];

try {
    // Create sample application_ids and insert records
    $appId = 1;
    foreach ($employees as $emp) {
        $salary = $salaryMap[$emp['position']] ?? $salaryMap['default'];
        
        // Insert into rao_hired_applicants
        $stmt = $pdo->prepare("
            INSERT INTO rao_hired_applicants 
            (application_id, employee_id, job_id, status, hired_at)
            VALUES (:app_id, :emp_id, 1, 'hired', NOW())
        ");
        $stmt->execute([
            ':app_id' => $appId,
            ':emp_id' => $emp['employee_id']
        ]);
        
        // Insert into rao_offer_salary
        $stmt = $pdo->prepare("
            INSERT INTO rao_offer_salary 
            (application_id, job_id, position, salary, offer_status, created_at)
            VALUES (:app_id, 1, :position, :salary, 'accepted', NOW())
        ");
        $stmt->execute([
            ':app_id' => $appId,
            ':position' => $emp['position'],
            ':salary' => $salary
        ]);
        
        echo "✓ {$emp['full_name']} - Position: {$emp['position']}, Salary: ₱{$salary}\n";
        $appId++;
    }
    
    echo "\n✅ Sample data inserted successfully!\n";
    
    // Verify the data
    echo "\n=== Verification ===\n";
    $stmt = $pdo->query("
        SELECT rha.employee_id, ros.position, ros.salary, ros.offer_status
        FROM rao_hired_applicants rha
        JOIN rao_offer_salary ros ON rha.application_id = ros.application_id
        WHERE ros.offer_status = 'accepted'
    ");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($results) . " accepted offers\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
