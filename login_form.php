<?php
/**
 * Login Form - Self Processing
 * Supports role-based login for all modules
 * Now includes department-based authentication from employees table
 */

session_start();

require_once "auth/Auth.php";
require_once "auth/database.php";
require_once "auth/EmployeeAuth.php";

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);

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
    'employee_portal' => 'Employee Portal',
    'learning' => 'Learning & Development',
    'performance' => 'Performance',
    'engagement_relations' => 'Engagement Relations',
    'exit' => 'Exit Management',
    'compliance' => 'Legal & Compliance'
];

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Please enter username and password";
        header("Location: login_form.php");
        exit;
    }
    
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
        $_SESSION['login_error'] = $employeeData['error'];
        header("Location: login_form.php");
        exit;
    }
    
    // Fallback: Check database for user in users table
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Login successful with database password
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['full_name'],
            'role' => $user['role'],
            'theme' => $user['theme'] ?? 'light'
        ];
        
        header("Location: router.php");
        exit;
    } elseif (strtolower($password) === 'password123') {
        // Demo mode: accept any username with password 'password123'
        $_SESSION['user'] = [
            'id' => 999,
            'username' => $username,
            'name' => ucfirst($username),
            'role' => $role ?: 'employee',
            'theme' => 'light'
        ];
        
        header("Location: router.php");
        exit;
    } else {
        $_SESSION['login_error'] = "Invalid username or password";
        header("Location: login_form.php");
        exit;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Human Resource Management - Login</title>
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/plugins/toastr/toastr.min.css">
  <link rel="stylesheet" href="login.css" />
</head>

<body>
  <div class="bigbox">
    <div class="box1">
      <h1>
        Human Resource <br />
        Management <br />
        System
      </h1>
    </div>
    <div class="box2">
      <form action="login_form.php" method="POST">
        <div class="header">
          <img
            src="assets/pics/bcpLogo.png"
            alt="AdminLTE Logo"
            class="brand-image"
            style="opacity: 0.9" />
          <h1>Enter your login details</h1>
          <div></div>
        </div>
        <div class="label">
          <label for="">Username</label>
          <input
            type="text"
            name="username"
            placeholder="Your Username..."
            required />
        </div>
        <div class="label">
          <label for="">Password</label>
          <div class="password-input-group">
            <input
              type="password"
              name="password"
              id="password"
              placeholder="Your Password.."
              required />
            <button type="button" class="toggle-password" onclick="togglePassword()">
              <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>
        
        <div class="role-select">
          <label for="role">Select Role (Demo Mode)</label>
          <select name="role" class="form-control" id="role">
            <option value="">-- Select Role --</option>
            <?php foreach ($roles as $key => $label): ?>
              <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <button type="submit" name="login">Login</button>
        <p class="para mt-3 d-flex justify-content-center">Looking for Portal?   <span><a class="link" href="index.php"> Click Here!  </a></span></p>
      </form>

    </div>
  </div>
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/plugins/toastr/toastr.min.js"></script>
  <script src="assets/dist/js/adminlte.js"></script>
  <?php if ($error): ?>
    <script>
      $(document).Toasts('create', {
        class: 'bg-danger',
        title: 'Login Failed',
        body: <?= json_encode($error) ?>,
        autohide: true,
        delay: 3000
      });
    </script>
  <?php endif; ?>

  <script>
    function togglePassword() {
      var passwordInput = document.getElementById('password');
      var eyeIcon = document.getElementById('eyeIcon');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      }
    }
  </script>

</body>

</html>
