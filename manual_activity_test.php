<?php
/**
 * Manual test to insert and verify activity logs
 */

session_start();

$output = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'insert') {
    try {
        require_once "auth/database.php";
        $db = new Database();
        $conn = $db->connect();
        
        // Get current user info
        $userId = $_SESSION['user']['id'] ?? 999;
        $username = $_SESSION['user']['username'] ?? 'test_user';
        
        $sql = "INSERT INTO activity_logs (user_id, username, action, details, timestamp) 
                VALUES (:user_id, :username, :action, :details, NOW())";
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([
            ':user_id' => $userId,
            ':username' => $username,
            ':action' => 'LOGIN',
            ':details' => 'Manual test log from ' . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown')
        ]);
        
        if ($success) {
            $output = "✓ Successfully inserted test log\n";
            
            // Show recent logs
            $query = "SELECT * FROM activity_logs ORDER BY timestamp DESC LIMIT 5";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $output .= "\nRecent logs:\n";
            foreach ($logs as $log) {
                $output .= "- " . $log['username'] . " | " . $log['action'] . " | " . $log['timestamp'] . "\n";
            }
        } else {
            $output = "✗ Failed to insert log";
        }
    } catch (Exception $e) {
        $output = "✗ Error: " . $e->getMessage();
    }
} else {
    $output = "No action specified";
}

header('Content-Type: text/plain');
echo $output;
?>
