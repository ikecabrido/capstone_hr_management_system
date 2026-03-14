<?php
/**
 * Comprehensive Debug Dashboard
 * Check all components of the system
 */

session_start();

// Check if user is logged in
$isLoggedIn = !empty($_SESSION['user']);
$userRole = $_SESSION['user']['role'] ?? 'N/A';

?>
<!DOCTYPE html>
<html>
<head>
    <title>System Debug Dashboard</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .status-good { color: green; }
        .status-bad { color: red; }
        .status-warn { color: orange; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; font-weight: bold; }
        .code { background: #f4f4f4; padding: 10px; border-left: 3px solid #4CAF50; margin: 10px 0; font-family: monospace; overflow-x: auto; }
        .test-btn { padding: 10px 20px; background: #2196F3; color: white; border: none; border-radius: 3px; cursor: pointer; }
        .test-btn:hover { background: #1976D2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 System Debug Dashboard</h1>
        
        <!-- Session Check -->
        <div class="card">
            <h2>1. Session Status</h2>
            <p>Logged In: <span class="<?php echo $isLoggedIn ? 'status-good' : 'status-bad'; ?>">
                <?php echo $isLoggedIn ? '✓ YES' : '✗ NO'; ?>
            </span></p>
            <?php if ($isLoggedIn): ?>
                <p>User: <strong><?php echo $_SESSION['user']['name'] ?? 'Unknown'; ?></strong></p>
                <p>Role: <strong><?php echo $userRole; ?></strong></p>
                <p>Username: <strong><?php echo $_SESSION['user']['username'] ?? 'N/A'; ?></strong></p>
            <?php else: ?>
                <p><strong>Please log in first, then access this page.</strong></p>
            <?php endif; ?>
        </div>
        
        <!-- Database Check -->
        <div class="card">
            <h2>2. Database Connection</h2>
            <?php
            try {
                require_once "auth/database.php";
                $db = new Database();
                $conn = $db->connect();
                echo "<p><span class='status-good'>✓ Database connection successful</span></p>";
                
                // Check activity_logs table
                $check = "SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'hr_management' AND TABLE_NAME = 'activity_logs'";
                $stmt = $conn->prepare($check);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result['count'] > 0) {
                    echo "<p><span class='status-good'>✓ activity_logs table exists</span></p>";
                } else {
                    echo "<p><span class='status-bad'>✗ activity_logs table NOT found</span></p>";
                }
            } catch (Exception $e) {
                echo "<p><span class='status-bad'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</span></p>";
            }
            ?>
        </div>
        
        <!-- Activity Logs -->
        <div class="card">
            <h2>3. Activity Logs</h2>
            <?php
            if ($isLoggedIn) {
                try {
                    require_once "auth/database.php";
                    $db = new Database();
                    $conn = $db->connect();
                    
                    $query = "SELECT * FROM activity_logs WHERE action = 'LOGIN' ORDER BY timestamp DESC LIMIT 10";
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($logs) > 0) {
                        echo "<p><span class='status-good'>✓ Found " . count($logs) . " login events</span></p>";
                        echo "<table>";
                        echo "<tr><th>Username</th><th>Action</th><th>Timestamp</th><th>Details</th></tr>";
                        foreach ($logs as $log) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($log['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($log['action']) . "</td>";
                            echo "<td>" . htmlspecialchars($log['timestamp']) . "</td>";
                            echo "<td>" . htmlspecialchars(substr($log['details'], 0, 50)) . "...</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p><span class='status-warn'>⚠ No login events found</span></p>";
                    }
                } catch (Exception $e) {
                    echo "<p><span class='status-bad'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</span></p>";
                }
            } else {
                echo "<p>Please log in first to see activity logs.</p>";
            }
            ?>
        </div>
        
        <!-- API Test -->
        <div class="card">
            <h2>4. Real-time Updates API</h2>
            <button class="test-btn" onclick="testAPI()">Test API</button>
            <div id="apiResult" style="margin-top: 10px;"></div>
            <script>
                function testAPI() {
                    const resultDiv = document.getElementById('apiResult');
                    resultDiv.innerHTML = '<p>Testing...</p>';
                    
                    fetch('../time_attendance/app/api/realtime_updates.php?limit=5')
                        .then(response => response.json())
                        .then(data => {
                            let html = '<pre style="background: #f4f4f4; padding: 10px; border-radius: 3px; overflow-x: auto;">';
                            html += JSON.stringify(data, null, 2);
                            html += '</pre>';
                            
                            if (data.success) {
                                html += '<p><span class="status-good">✓ API returned ' + (data.count || 0) + ' events</span></p>';
                            } else {
                                html += '<p><span class="status-bad">✗ API error: ' + (data.error || 'Unknown') + '</span></p>';
                            }
                            resultDiv.innerHTML = html;
                        })
                        .catch(error => {
                            resultDiv.innerHTML = '<p><span class="status-bad">✗ Network error: ' + error.message + '</span></p>';
                        });
                }
            </script>
        </div>
        
        <!-- Manual Test -->
        <div class="card">
            <h2>5. Manual Activity Log Insert</h2>
            <button class="test-btn" onclick="manualLog()">Insert Test Log</button>
            <div id="manualResult" style="margin-top: 10px;"></div>
            <script>
                function manualLog() {
                    const resultDiv = document.getElementById('manualResult');
                    resultDiv.innerHTML = '<p>Inserting test log...</p>';
                    
                    fetch('manual_activity_test.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'action=insert'
                    })
                    .then(response => response.text())
                    .then(data => {
                        resultDiv.innerHTML = '<pre style="background: #f4f4f4; padding: 10px; border-radius: 3px;">' + data + '</pre>';
                    })
                    .catch(error => {
                        resultDiv.innerHTML = '<p><span class="status-bad">Error: ' + error.message + '</span></p>';
                    });
                }
            </script>
        </div>
        
        <!-- Instructions -->
        <div class="card">
            <h2>6. Troubleshooting Steps</h2>
            <ol>
                <li><strong>Log in</strong> to the system first (on Android or desktop)</li>
                <li><strong>Refresh this page</strong> to see if your login was recorded</li>
                <li><strong>Click "Test API"</strong> to check if the real-time API is working</li>
                <li><strong>Check PHP error log</strong> at: C:/xampp/php/logs/error.log</li>
                <li><strong>Check MySQL</strong>: Verify activity_logs table has data</li>
            </ol>
        </div>
    </div>
</body>
</html>
