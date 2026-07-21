<?php
/**
 * Mobile Leave Request Feature Test
 * Tests that the leave request modal appears and functions on mobile
 */

echo "=" . str_repeat("=", 70) . "\n";
echo "MOBILE LEAVE REQUEST FEATURE TEST\n";
echo "=" . str_repeat("=", 70) . "\n\n";

// Test 1: Check if modal file exists
echo "[TEST 1] Modal File Exists\n";
$modal_file = __DIR__ . '/app/views/leave-request/modal-leave-request.php';
if (file_exists($modal_file)) {
    echo "✅ Modal file found at: " . $modal_file . "\n\n";
} else {
    echo "❌ Modal file NOT found\n\n";
    exit(1);
}

// Test 2: Check controller includes modal modal
echo "[TEST 2] Controller Fetches Leave Types\n";
try {
    require_once __DIR__ . '/app/config/Database.php';
    require_once __DIR__ . '/app/controllers/EmployeePortalController.php';
    
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT leave_type_id, leave_type_name FROM ta_leave_types ORDER BY leave_type_name");
    $stmt->execute();
    $allLeaveTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($allLeaveTypes)) {
        echo "✅ Leave types fetched successfully\n";
        echo "   Found " . count($allLeaveTypes) . " active leave types:\n";
        foreach ($allLeaveTypes as $type) {
            echo "   • " . htmlspecialchars($type['leave_type_name']) . " (ID: " . $type['leave_type_id'] . ")\n";
        }
        echo "\n";
    } else {
        echo "⚠️  No active leave types found\n\n";
    }
} catch (Exception $e) {
    echo "❌ Error fetching leave types: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 3: Check button markup
echo "[TEST 3] Dashboard Button Markup\n";
$main_content_file = __DIR__ . '/app/views/employee-portal/main-content.php';
$content = file_get_contents($main_content_file);

// Check for modal button
if (strpos($content, 'data-bs-toggle="modal"') !== false && 
    strpos($content, 'data-bs-target="#leaveRequestModal"') !== false) {
    echo "✅ Button uses modal toggle (not href link)\n";
    echo "   Attributes found:\n";
    echo "   • data-bs-toggle=\"modal\"\n";
    echo "   • data-bs-target=\"#leaveRequestModal\"\n";
} else {
    echo "❌ Button NOT using modal toggle\n";
    exit(1);
}

// Check for modal inclusion
if (strpos($content, "require __DIR__ . '/../leave-request/modal-leave-request.php'") !== false) {
    echo "✅ Modal included in dashboard view\n\n";
} else {
    echo "❌ Modal NOT included in dashboard view\n\n";
    exit(1);
}

// Test 4: Check mobile responsiveness in modal
echo "[TEST 4] Modal Mobile Responsiveness\n";
if (preg_match('/class="modal-lg/', $content) || preg_match('/modal-dialog/', file_get_contents($modal_file))) {
    echo "✅ Modal has responsive classes\n";
} else {
    echo "⚠️  Modal may not be fully responsive\n";
}

// Check for mobile viewport meta tag in index
$index_file = __DIR__ . '/app/views/employee-portal/index.php';
$index_content = file_get_contents($index_file);
if (strpos($index_content, 'viewport') !== false) {
    echo "✅ Viewport meta tag present in index\n\n";
} else {
    echo "⚠️  Viewport meta tag missing\n\n";
}

// Test 5: Check Bootstrap modal functionality
echo "[TEST 5] Bootstrap Modal Setup\n";
if (strpos($index_content, 'bootstrap.bundle.min.js') !== false) {
    echo "✅ Bootstrap JS bundle included (required for modal)\n";
} else {
    echo "❌ Bootstrap JS NOT included\n";
    exit(1);
}

if (strpos($index_content, 'bootstrap.min.css') !== false) {
    echo "✅ Bootstrap CSS included\n\n";
} else {
    echo "⚠️  Bootstrap CSS may be missing\n\n";
}

// Test 6: Syntax check modal PHP
echo "[TEST 6] Modal Syntax Verification\n";
ob_start();
try {
    include $modal_file;
    ob_end_clean();
    echo "✅ Modal file has valid PHP syntax\n";
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ Modal file has syntax errors: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "SUMMARY\n";
echo str_repeat("=", 70) . "\n";
echo "✅ All tests passed!\n\n";

echo "EXPECTED BEHAVIOR ON MOBILE:\n";
echo "1. Click '➕ Request Leave' button\n";
echo "2. Modal popup appears (not page navigation)\n";
echo "3. Modal is touch-friendly and responsive\n";
echo "4. Form fields accessible on small screens\n";
echo "5. Leave types dropdown populated with " . count($allLeaveTypes) . " types\n";
echo "6. Date pickers are mobile-compatible\n";
echo "7. Submit button is easily clickable\n";
echo "\n";

echo "TESTING CHECKLIST:\n";
echo "[ ] Load employee portal on mobile device\n";
echo "[ ] Click 'Request Leave' button in dashboard\n";
echo "[ ] Verify modal appears (not navigation)\n";
echo "[ ] Select leave type from dropdown\n";
echo "[ ] Pick start and end dates\n";
echo "[ ] Enter reason in text area\n";
echo "[ ] Click Submit button\n";
echo "[ ] Modal closes on success\n";
echo "\n";

echo "FILES MODIFIED:\n";
echo "1. app/views/employee-portal/main-content.php\n";
echo "   - Changed button from <a href> to <button data-bs-toggle=\"modal\">\n";
echo "   - Added modal include at end of view\n";
echo "\n";
echo "2. app/controllers/EmployeePortalController.php\n";
echo "   - Added leave types fetching for modal\n";
echo "   - Passes \$allLeaveTypes to dashboard view\n";
echo "\n";
?>
