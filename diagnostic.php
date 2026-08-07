<?php
echo "=== TEACHER LOAD HANDLER DIAGNOSTIC ===\n\n";

$viewsDir = "c:\\xampp\\htdocs\\capstone_hr_management_system\\payroll\\views\\";

// Check 1: Handler file exists
echo "1. Handler file:\n";
$handlerFile = $viewsDir . "teacherLoadHandler.php";
if (file_exists($handlerFile)) {
    echo "   ✓ EXISTS: $handlerFile\n";
    echo "   Size: " . filesize($handlerFile) . " bytes\n";
    echo "   Last modified: " . date('Y-m-d H:i:s', filemtime($handlerFile)) . "\n";
} else {
    echo "   ✗ MISSING: $handlerFile\n";
}

// Check 2: Debug files
echo "\n2. Debug files created by handler:\n";
$debugFiles = ['handler_called.txt', 'teacherLoadHandler.log'];
foreach ($debugFiles as $file) {
    $path = $viewsDir . $file;
    if (file_exists($path)) {
        echo "   ✓ EXISTS: $file\n";
        echo "     Size: " . filesize($path) . " bytes\n";
        echo "     Last modified: " . date('Y-m-d H:i:s', filemtime($path)) . "\n";
        echo "     Content (last 500 chars):\n";
        $content = file_get_contents($path);
        echo "     " . substr($content, -500) . "\n";
    } else {
        echo "   ✗ NOT FOUND: $file (handler hasn't been called yet)\n";
    }
}

// Check 3: Database records
echo "\n3. Teacher loads in database:\n";
try {
    $pdo = new PDO('mysql:host=localhost;dbname=data_hr', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pr_teacher_loads");
    $count = $stmt->fetchColumn();
    echo "   Total records: $count\n";
    
    $stmt = $pdo->query("SELECT id, employee_id, academic_year, semester, created_at FROM pr_teacher_loads ORDER BY id DESC LIMIT 5");
    $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "   Latest 5 records:\n";
    foreach ($recent as $rec) {
        $created = strtotime($rec['created_at']);
        $age = time() - $created;
        $ageText = ($age < 60) ? "$age seconds ago" : ($age < 3600 ? intval($age/60) . " minutes ago" : "older");
        echo "     ID {$rec['id']}: Emp {$rec['employee_id']}, {$rec['academic_year']} {$rec['semester']} ({$ageText})\n";
    }
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
}

// Check 4: Form analysis
echo "\n4. Form in teacherLoadManagement.php:\n";
$mgmtFile = $viewsDir . "teacherLoadManagement.php";
if (file_exists($mgmtFile)) {
    $content = file_get_contents($mgmtFile);
    if (strpos($content, 'action="teacherLoadHandler.php"') !== false) {
        echo "   ✓ Form action found: action=\"teacherLoadHandler.php\"\n";
    } else {
        echo "   ✗ Form action NOT found or incorrect\n";
    }
    
    if (preg_match('/name="action".*value="add_load"/', $content)) {
        echo "   ✓ Hidden action=add_load field found\n";
    } else {
        echo "   ✗ Hidden action=add_load field NOT found\n";
    }
} else {
    echo "   ✗ Management file not found\n";
}

// Check 5: Recommended next steps
echo "\n5. NEXT STEPS:\n";
if (!file_exists($viewsDir . 'handler_called.txt')) {
    echo "   ⚠️  The handler hasn't been called yet!\n";
    echo "   This means:\n";
    echo "   - The form is not submitting\n";
    echo "   - OR the form is submitting but not reaching the handler\n";
    echo "   - OR there's a URL path issue\n\n";
    echo "   ACTION: Try submitting a teacher load and then run this diagnostic again\n";
} else {
    echo "   ✓ Handler HAS been called\n";
    echo "   Check the teacherLoadHandler.log file for details\n";
}
?>
