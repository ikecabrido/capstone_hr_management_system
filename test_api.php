<?php
/**
 * Test if realtime_updates API is working
 */

// Simulate being logged in as HR_ADMIN
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'HR_ADMIN';
$_SESSION['user'] = [
    'id' => 1,
    'role' => 'HR_ADMIN'
];

// Now call the API
ob_start();
include 'time_attendance/app/api/realtime_updates.php';
$output = ob_get_clean();

echo "<pre>";
echo "Raw API Response:\n";
echo htmlspecialchars($output);
echo "</pre>";

// Try to decode as JSON
$data = json_decode($output, true);
if ($data) {
    echo "<h2>Decoded JSON:</h2>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    
    if ($data['success']) {
        echo "<p style='color: green;'>✓ API is working! Found " . $data['count'] . " events.</p>";
    }
} else {
    echo "<p style='color: red;'>✗ API returned invalid JSON</p>";
}
?>
