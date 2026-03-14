<?php
/**
 * Direct Login Test - No AJAX
 * Test if login works with simple form submission
 */

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once "auth/auth.php";
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $auth = new Auth();
    if ($auth->login($username, $password)) {
        echo "<h1 style='color: green;'>✓ Login Successful!</h1>";
        echo "<p>You are logged in as: <strong>" . $_SESSION['user']['name'] . "</strong></p>";
        echo "<p>Role: <strong>" . $_SESSION['user']['role'] . "</strong></p>";
        echo "<p><a href='router.php'>Go to Dashboard</a></p>";
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Direct Login Test</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .card { background: white; padding: 30px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 400px; }
        input { padding: 10px; margin: 10px 0; width: 100%; box-sizing: border-box; }
        button { padding: 10px 20px; background: #2196F3; color: white; border: none; border-radius: 3px; cursor: pointer; width: 100%; }
        button:hover { background: #1976D2; }
        .error { color: red; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 3px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Direct Login Test</h1>
        <div class="info">
            <strong>This is a simple test without AJAX.</strong> If this works, the backend is fine. If it doesn't, there's a backend issue.
        </div>
        
        <?php if (isset($error)): ?>
            <p class="error"><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required value="hr_time">
            <input type="password" name="password" placeholder="Password" required value="password">
            <button type="submit">Login (Direct)</button>
        </form>
        
        <hr>
        <p><small>Try with username: <strong>hr_time</strong> and password: <strong>password</strong></small></p>
    </div>
</body>
</html>
