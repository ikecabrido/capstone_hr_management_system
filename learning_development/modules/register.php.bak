<?php
require_once __DIR__ . '/config.php';

// handle registration form submission
$message = '';
$messageType = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $role = $_POST['role'] ?? 'employee';

    if ($username === '' || $fullName === '' || $email === '' || $password === '' || $confirm === '') {
        $message = 'All fields are required.';
        $messageType = 'danger';
    } elseif ($password !== $confirm) {
        $message = 'Passwords do not match.';
        $messageType = 'danger';
    } else {
        try {
            // ensure username/email uniqueness
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                $message = 'Username or email already in use.';
                $messageType = 'danger';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$username, $email, $hash, $fullName, $role]);
                $message = 'Registration successful. You can now log in.';
                $messageType = 'success';
                // optionally redirect to login
                header('Location: login.php');
                exit;
            }
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            $message = 'An error occurred during registration.';
            $messageType = 'danger';
        }
    }
}

require_once __DIR__ . '/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <?php if ($message): ?>
      <div class="alert alert-<?php echo htmlspecialchars($messageType); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Register</h5>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input name="username" class="form-control" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input name="full_name" class="form-control" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
              <option value="employee"<?php echo (($_POST['role'] ?? '') === 'employee') ? ' selected' : ''; ?>>Employee</option>
              <option value="manager"<?php echo (($_POST['role'] ?? '') === 'manager') ? ' selected' : ''; ?>>Manager</option>
              <option value="admin"<?php echo (($_POST['role'] ?? '') === 'admin') ? ' selected' : ''; ?>>Admin</option>
              <option value="trainer"<?php echo (($_POST['role'] ?? '') === 'trainer') ? ' selected' : ''; ?>>Trainer</option>
            </select>
          </div>
          <button class="btn btn-primary">Register</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php';
