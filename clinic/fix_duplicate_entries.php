<?php
// Script to fix duplicate entry errors in database
echo "<h2>🔧 Fix Duplicate Entry Errors</h2>";

require_once "../auth/database.php";

$database = Database::getInstance();
$db = $database->getConnection();

if ($db === null) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

echo "<p style='color: green;'>✅ Database connection successful</p>";

// Function to handle duplicate inserts safely
function safeInsert($db, $table, $data, $primaryKey) {
    try {
        // Check if record already exists
        $checkSql = "SELECT COUNT(*) as count FROM $table WHERE $primaryKey = :value";
        $stmt = $db->prepare($checkSql);
        $stmt->bindParam(':value', $data[$primaryKey]);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "<p style='color: orange;'>⚠️ Record with {$primaryKey} = '{$data[$primaryKey]}' already exists in $table</p>";
            return false;
        }
        
        // Build INSERT query
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $db->prepare($sql);
        $values = array_values($data);
        
        $result = $stmt->execute($values);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Successfully inserted record into $table</p>";
            return true;
        } else {
            echo "<p style='color: red;'>❌ Failed to insert record into $table</p>";
            return false;
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error inserting into $table: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Fix departments table
echo "<h3>📋 Fixing Departments Table:</h3>";

$departments = [
    ['department_id' => 'ACAD', 'department_name' => 'Academic Affairs', 'location' => 'Main Building'],
    ['department_id' => 'ADMIN', 'department_name' => 'Administration', 'location' => 'Admin Building'],
    ['department_id' => 'HR', 'department_name' => 'Human Resources', 'location' => 'Admin Building'],
    ['department_id' => 'FINANCE', 'department_name' => 'Finance', 'location' => 'Admin Building'],
    ['department_id' => 'IT', 'department_name' => 'Information Technology', 'location' => 'Tech Building'],
    ['department_id' => 'LIB', 'department_name' => 'Library', 'location' => 'Library Building'],
    ['department_id' => 'MED', 'department_name' => 'Medical Services', 'location' => 'Clinic Building'],
    ['department_id' => 'MAINT', 'department_name' => 'Maintenance', 'location' => 'Utility Building'],
    ['department_id' => 'SEC', 'department_name' => 'Security', 'location' => 'Gate House']
];

foreach ($departments as $dept) {
    safeInsert($db, 'cm_departments', $dept, 'department_id');
}

echo "<h3>👥 Fixing Sample Employees:</h3>";

$employees = [
    [
        'employee_id' => 'EMP001',
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'middle_name' => 'Santos',
        'email' => 'juan.delacruz@bcp.edu.ph',
        'phone' => '09123456789',
        'address' => 'Bulacan, Philippines',
        'birth_date' => '1990-05-15',
        'gender' => 'Male',
        'department' => 'ACAD',
        'position' => 'Teacher',
        'employee_type' => 'Faculty',
        'employment_status' => 'Active',
        'hire_date' => '2020-06-01',
        'salary' => '25000.00',
        'emergency_contact_name' => 'Maria Dela Cruz',
        'emergency_contact_phone' => '09123456788',
        'blood_type' => 'O+',
        'allergies' => 'None',
        'created_by' => 'Admin'
    ],
    [
        'employee_id' => 'EMP002',
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'middle_name' => 'Reyes',
        'email' => 'maria.santos@bcp.edu.ph',
        'phone' => '09234567890',
        'address' => 'Bulacan, Philippines',
        'birth_date' => '1988-08-22',
        'gender' => 'Female',
        'department' => 'ADMIN',
        'position' => 'Administrative Assistant',
        'employee_type' => 'Staff',
        'employment_status' => 'Active',
        'hire_date' => '2019-03-15',
        'salary' => '18000.00',
        'emergency_contact_name' => 'Jose Santos',
        'emergency_contact_phone' => '09234567891',
        'blood_type' => 'A+',
        'allergies' => 'Penicillin',
        'created_by' => 'Admin'
    ]
];

foreach ($employees as $emp) {
    safeInsert($db, 'cm_employees', $emp, 'employee_id');
}

echo "<h3>🏥 Fixing Sample Patients:</h3>";

$patients = [
    [
        'patient_id' => 'PAT001',
        'employee_id' => 'EMP001',
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'middle_name' => 'Santos',
        'birth_date' => '1990-05-15',
        'gender' => 'Male',
        'blood_type' => 'O+',
        'allergies' => 'None',
        'medical_history' => 'No significant medical history',
        'patient_type' => 'Employee',
        'status' => 'Active',
        'created_by' => 'Admin'
    ],
    [
        'patient_id' => 'PAT002',
        'employee_id' => 'EMP002',
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'middle_name' => 'Reyes',
        'birth_date' => '1988-08-22',
        'gender' => 'Female',
        'blood_type' => 'A+',
        'allergies' => 'Penicillin',
        'medical_history' => 'Hypertension, controlled with medication',
        'patient_type' => 'Employee',
        'status' => 'Active',
        'created_by' => 'Admin'
    ]
];

foreach ($patients as $patient) {
    safeInsert($db, 'cm_patients', $patient, 'patient_id');
}

echo "<h3>💊 Fixing Sample Medicines:</h3>";

$medicines = [
    [
        'medicine_id' => 'MED001',
        'medicine_name' => 'Paracetamol',
        'generic_name' => 'Acetaminophen',
        'description' => 'Pain reliever and fever reducer',
        'category' => 'Analgesic',
        'unit_of_measure' => 'Tablet',
        'current_stock' => '100',
        'reorder_level' => '20',
        'unit_price' => '5.00',
        'supplier' => 'Generic Pharma',
        'expiry_date' => '2024-12-31',
        'storage_location' => 'Medicine Cabinet A',
        'status' => 'Active',
        'created_by' => 'Admin'
    ],
    [
        'medicine_id' => 'MED002',
        'medicine_name' => 'Ibuprofen',
        'generic_name' => 'Ibuprofen',
        'description' => 'NSAID for pain and inflammation',
        'category' => 'Analgesic',
        'unit_of_measure' => 'Tablet',
        'current_stock' => '75',
        'reorder_level' => '15',
        'unit_price' => '8.00',
        'supplier' => 'Generic Pharma',
        'expiry_date' => '2024-11-30',
        'storage_location' => 'Medicine Cabinet B',
        'status' => 'Active',
        'created_by' => 'Admin'
    ]
];

foreach ($medicines as $medicine) {
    safeInsert($db, 'cm_medicine_inventory', $medicine, 'medicine_id');
}

echo "<h3>📊 Current Database Status:</h3>";

// Show current counts
$tables = ['cm_departments', 'cm_employees', 'cm_patients', 'cm_medicine_inventory'];

foreach ($tables as $table) {
    try {
        $result = $db->query("SELECT COUNT(*) as count FROM $table");
        $count = $result->fetchColumn();
        echo "<p><strong>$table:</strong> $count records</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>$table:</strong> Error - " . $e->getMessage() . "</p>";
    }
}

echo "<h3>🔧 Alternative SQL Solution:</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px;'>";
echo "<h4>To fix this directly in SQL, use INSERT IGNORE or REPLACE:</h4>";
echo "<pre style='background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ddd;'>";
echo "-- Option 1: INSERT IGNORE (skips duplicates)
INSERT IGNORE INTO cm_departments (department_id, department_name, location) VALUES
('ACAD', 'Academic Affairs', 'Main Building'),
('ADMIN', 'Administration', 'Admin Building');

-- Option 2: REPLACE (deletes and reinserts)
REPLACE INTO cm_departments (department_id, department_name, location) VALUES
('ACAD', 'Academic Affairs', 'Main Building'),
('ADMIN', 'Administration', 'Admin Building');

-- Option 3: ON DUPLICATE KEY UPDATE
INSERT INTO cm_departments (department_id, department_name, location) VALUES
('ACAD', 'Academic Affairs', 'Main Building')
ON DUPLICATE KEY UPDATE 
department_name = VALUES(department_name),
location = VALUES(location);";
echo "</pre>";
echo "</div>";

echo "<p><strong>✅ Duplicate entry errors have been resolved!</strong></p>";
echo "<p><a href='../clinic.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Back to Dashboard</a></p>";
?>
