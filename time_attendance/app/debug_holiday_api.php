<?php
// Debug script to test the Holiday API
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing Holiday API Setup...\n\n";

// Test 1: Database connection
echo "1. Testing Database Connection:\n";
try {
    require_once "../../auth/database.php";
    $db = Database::getInstance()->getConnection();
    echo "✓ Database connected\n\n";
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n\n";
    exit;
}

// Test 2: Holiday Model
echo "2. Testing Holiday Model:\n";
try {
    require_once "./models/Holiday.php";
    $holiday = new \App\Models\Holiday($db);
    echo "✓ Holiday model loaded\n\n";
} catch (Exception $e) {
    echo "✗ Holiday model error: " . $e->getMessage() . "\n\n";
}

// Test 3: Check if ta_holidays table exists
echo "3. Checking ta_holidays table:\n";
try {
    $result = $db->query("SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'hr_management' AND TABLE_NAME = 'ta_holidays'");
    $exists = $result->fetch();
    if ($exists) {
        echo "✓ ta_holidays table exists\n\n";
    } else {
        echo "✗ ta_holidays table does NOT exist\n";
        echo "   Run migration: 003_create_holidays_table.sql\n\n";
    }
} catch (Exception $e) {
    echo "✗ Table check error: " . $e->getMessage() . "\n\n";
}

// Test 4: Try to get holidays
echo "4. Testing Holiday::getAllHolidays():\n";
try {
    $holidays = $holiday->getAllHolidays();
    echo "✓ Got " . count($holidays) . " holidays\n\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 5: Holiday Controller
echo "5. Testing Holiday Controller:\n";
try {
    require_once "./controllers/HolidayController.php";
    $controller = new \App\Controllers\HolidayController($db);
    echo "✓ Holiday controller loaded\n\n";
} catch (Exception $e) {
    echo "✗ Controller error: " . $e->getMessage() . "\n\n";
}

echo "All tests completed!\n";
?>
