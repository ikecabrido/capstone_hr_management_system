<?php
session_start();

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Human Resource Managment</title>
  <link rel="stylesheet" href="login.css" />
</head>

<body>
  <div class="container">
    <div class="box1">
      <h1>
        Human Resource <br />
        Management <br />
        System
      </h1>
    </div>
    <div class="box2">
      <form action="login.php" method="POST">
        <div class="header">
          <img
            src="assets/pics/bcpLogo.png"
            alt="AdminLTE Logo"
            class="brand-image"
            style="opacity: 0.9" />
          <h1>Login</h1>
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
          <input
            type="password"
            name="password"
            placeholder="Your Password.."
            required />
        </div>
        <!-- <button>Login</button> -->
        <button type="submit" name="login">Login</button>
      </form>
      <div id="toast" class="toast"></div>
    </div>
  </div>
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
</body>

</html>