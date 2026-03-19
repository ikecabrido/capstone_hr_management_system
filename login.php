<?php
/**
 * Login System with Role-Based Access
 * For testing - allows login with any username and selected role
 * Now includes department-based authentication from employees table
 */

require_once "auth/Auth.php";
require_once "auth/database.php";
require_once "auth/EmployeeAuth.php";

$error = '';
$success = '';

// Get available roles
$roles = [
    'admin' => 'Administrator',
    'hr_admin' => 'HR Administrator',
    'payroll' => 'Payroll',
    'recruitment' => 'Recruitment',
    'time' => 'Time & Attendance',
    'clinic' => 'Clinic',
    'workforce' => 'Workforce',
    'employee' => 'Employee',
    'learning' => 'Learning & Development',
    'performance' => 'Performance',
    'engagement_relations' => 'Engagement Relations',
    'exit' => 'Exit Management',
    'compliance' => 'Legal & Compliance'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Please enter username and password";
    } else {
        // First, try to authenticate using employee table (department-based auth)
        $employeeAuth = new EmployeeAuth();
        $employeeData = $employeeAuth->authenticate($username, $password);
        
        if ($employeeData && is_array($employeeData) && !isset($employeeData['error'])) {
            // Employee authentication successful - use department-based routing
            $_SESSION['user'] = $employeeData;
            
            // Redirect to department-specific page
            $redirectPage = $employeeData['redirect_page'] ?? 'router.php';
            header("Location: " . $redirectPage);
            exit;
        } elseif ($employeeData && isset($employeeData['error'])) {
            // Employee found but error (e.g., inactive account)
            $error = $employeeData['error'];
        } else {
            // Fallback: Check database for user in users table
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Login successful - set session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'name' => $user['full_name'],
                    'role' => $user['role'],
                    'theme' => $user['theme'] ?? 'light'
                ];
                
                header("Location: router.php");
                exit;
            } elseif (!$user && $password === 'password123') {
                // Demo mode: create session for testing (any username with password 'password123')
                $_SESSION['user'] = [
                    'id' => 999,
                    'username' => $username,
                    'name' => ucfirst($username),
                    'role' => $role ?: 'employee',
                    'theme' => 'light'
                ];
                
                $success = "Logged in as " . ($roles[$role] ?? $role);
                header("Location: router.php");
                exit;
            } else {
                $error = "Invalid username or password";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bestlink College HR System</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="assets/docs/assets/plugins/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            width: 400px;
        }
        .role-select {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="card">
            <div class="card-body login-card-body">
                <div class="text-center mb-4">
                    <h4>Bestlink College</h4>
                    <p class="text-muted">HR Management System</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <select name="role" class="form-control" required>
                            <option value="">Select Role (for demo)</option>
                            <?php foreach ($roles as $key => $label): ?>
                                <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>
                
                <div class="mt-3 text-center">
                    <p class="text-muted" style="font-size: 12px;">
                        Demo: Use any username + password "password123"<br>
                        Or login with existing database credentials
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
