<?php require_once __DIR__ . '/config.php';

// Real login hooked to database
$message = '';
$messageType = 'info';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $message = 'Please provide username and password.';
        $messageType = 'danger';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, username, full_name, password, role FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                header('Location: index.php');
                exit;
            } else {
                $message = 'Invalid username or password.';
                $messageType = 'danger';
            }
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            $message = 'Login failed. Please try again.';
            $messageType = 'danger';
        }
    }
}

require_once __DIR__ . '/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <?php if (
        isset($message) && $message
    ): ?>
      <div class="alert alert-<?php echo htmlspecialchars($messageType); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Login</h5>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input name="username" class="form-control" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control">
          </div>
          <button class="btn btn-primary">Login</button>
        </form>
        <p class="mt-3">Don't have an account? <a href="register.php">Register here</a>.</p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>