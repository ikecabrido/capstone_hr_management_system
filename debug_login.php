<?php
// Debug login issues - Remove this file after testing
session_start();

echo "<h2>Debug Login Request</h2>";
echo "<pre>";
echo "Request Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "Content-Type: " . $_SERVER['CONTENT_TYPE'] ?? 'Not set' . "\n";
echo "Content-Length: " . $_SERVER['CONTENT_LENGTH'] ?? '0' . "\n";
echo "\n--- POST Data ---\n";
var_dump($_POST);
echo "\n--- GET Data ---\n";
var_dump($_GET);
echo "\n--- Server Info ---\n";
echo "Client IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
echo "Server IP: " . $_SERVER['SERVER_ADDR'] . "\n";
echo "Host: " . $_SERVER['HTTP_HOST'] . "\n";
echo "</pre>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Submission Detected</h3>";
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    echo "<p>Username received: '" . htmlspecialchars($username) . "' (length: " . strlen($username) . ")</p>";
    echo "<p>Password received: " . (empty($password) ? "EMPTY" : "***") . "</p>";
    
    if (empty($username)) {
        echo "<p style='color:red;'><strong>ERROR: Username is empty!</strong></p>";
    }
    if (empty($password)) {
        echo "<p style='color:red;'><strong>ERROR: Password is empty!</strong></p>";
    }
}
?>
<form method="POST" action="debug_login.php">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Test Submit</button>
</form>
