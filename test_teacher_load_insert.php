<?php
// Simulate posting form data to test the handler

// First, setup the session
session_start();
$_SESSION['user']['name'] = 'Test User';

// Simulate the POST data
$_POST = [
    'action' => 'add_load',
    'employee_id' => '4',  // Dr. Angela Ramos (Professor)
    'academic_year' => '2025-2026',
    'semester' => '1st',
    'total_units' => '30',
    'notes' => 'Test load'
];

// Include the handler
echo "=== Simulator: Posting Teacher Load Data ===\n";
echo "POST data: " . json_encode($_POST) . "\n\n";

// Manually execute the handler code
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/auth/database.php';

try {
    $db = Database::getInstance()->getConnection();
    $action = $_POST['action'] ?? null;

    if ($action === 'add_load') {
        $employeeId = (int)($_POST['employee_id'] ?? 0);
        $academicYear = $_POST['academic_year'] ?? '';
        $semester = $_POST['semester'] ?? '';
        $totalUnits = (float)($_POST['total_units'] ?? 0);

        echo "Parsed values:\n";
        echo "  employeeId: $employeeId (type: " . gettype($employeeId) . ")\n";
        echo "  academicYear: $academicYear\n";
        echo "  semester: $semester\n";
        echo "  totalUnits: $totalUnits (type: " . gettype($totalUnits) . ")\n";

        // Validate
        if (!$employeeId || !$academicYear || !$semester || $totalUnits <= 0) {
            echo "\n❌ Validation failed!\n";
            echo "  employeeId valid: " . ($employeeId ? "YES" : "NO") . "\n";
            echo "  academicYear valid: " . ($academicYear ? "YES" : "NO") . "\n";
            echo "  semester valid: " . ($semester ? "YES" : "NO") . "\n";
            echo "  totalUnits valid: " . ($totalUnits > 0 ? "YES" : "NO") . "\n";
            exit;
        }

        echo "\n✓ Validation passed\n";

        // Prepare INSERT
        $stmt = $db->prepare("
            INSERT INTO pr_teacher_loads 
            (employee_id, academic_year, semester, total_units, created_by)
            VALUES (:eid, :year, :sem, :units, :creator)
            ON DUPLICATE KEY UPDATE
            total_units = :units,
            updated_at = CURRENT_TIMESTAMP
        ");

        echo "\nAttempting INSERT with:\n";
        echo "  :eid => $employeeId\n";
        echo "  :year => $academicYear\n";
        echo "  :sem => $semester\n";
        echo "  :units => $totalUnits\n";
        echo "  :creator => " . ($_SESSION['user']['name'] ?? 'system') . "\n";

        $result = $stmt->execute([
            ':eid' => $employeeId,
            ':year' => $academicYear,
            ':sem' => $semester,
            ':units' => $totalUnits,
            ':creator' => $_SESSION['user']['name'] ?? 'system'
        ]);

        if ($result) {
            echo "\n✅ INSERT successful!\n";
            echo "Rows affected: " . $stmt->rowCount() . "\n";
        } else {
            $errors = $stmt->errorInfo();
            echo "\n❌ INSERT failed!\n";
            echo "Error Code: {$errors[0]}\n";
            echo "Error State: {$errors[1]}\n";
            echo "Error Message: {$errors[2]}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

// Verify the data was inserted
echo "\n=== Verifying Data ===\n";
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM pr_teacher_loads WHERE employee_id = 4 AND academic_year = '2025-2026' AND semester = '1st' ORDER BY id DESC LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        echo "✓ Found inserted record:\n";
        print_r($result);
    } else {
        echo "❌ No record found\n";
    }
} catch (Exception $e) {
    echo "Error checking: " . $e->getMessage();
}
?>
