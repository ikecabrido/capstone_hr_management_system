<?php
/**
 * Test script to check if login activity is being logged
 */

require_once "auth/database.php";

try {
    $db = new Database();
    $conn = $db->connect();
    
    echo "<h2>Activity Logs Test</h2>";
    
    // Check if table exists
    $check_table = "SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'hr_management' AND TABLE_NAME = 'activity_logs'";
    $stmt = $conn->prepare($check_table);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        echo "<p style='color: green;'>✓ activity_logs table EXISTS</p>";
    } else {
        echo "<p style='color: red;'>✗ activity_logs table NOT FOUND</p>";
        exit;
    }
    
    // Count records
    $count_query = "SELECT COUNT(*) as total FROM activity_logs";
    $stmt = $conn->prepare($count_query);
    $stmt->execute();
    $count_result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total records in activity_logs: <strong>" . $count_result['total'] . "</strong></p>";
    
    // Show recent logs
    echo "<h3>Recent Login Events (last hour):</h3>";
    $recent_query = "SELECT * FROM activity_logs WHERE action = 'LOGIN' AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY timestamp DESC LIMIT 10";
    $stmt = $conn->prepare($recent_query);
    $stmt->execute();
    $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($recent) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Username</th><th>Action</th><th>Timestamp</th><th>Details</th></tr>";
        foreach ($recent as $log) {
            echo "<tr>";
            echo "<td>" . $log['id'] . "</td>";
            echo "<td>" . $log['user_id'] . "</td>";
            echo "<td>" . $log['username'] . "</td>";
            echo "<td>" . $log['action'] . "</td>";
            echo "<td>" . $log['timestamp'] . "</td>";
            echo "<td>" . $log['details'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p style='color: green;'>✓ Logins are being recorded!</p>";
    } else {
        echo "<p style='color: orange;'>⚠ No login events found in the last hour</p>";
        echo "<p>This means either:</p>";
        echo "<ol>";
        echo "<li>No logins have occurred yet - Try logging in and then refresh this page</li>";
        echo "<li>The login logging code isn't working</li>";
        echo "</ol>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
?>
