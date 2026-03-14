<?php
session_start();

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Human Resource Managment</title>
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="assets/plugins/toastr/toastr.min.css">
  <link rel="stylesheet" href="login.css" />
  <style>
    /* Mobile responsive overrides */
    @media (max-width: 768px) {
      .bigbox {
        grid-template-columns: 1fr;
        height: auto;
        min-height: 100vh;
      }

      .box1 {
        grid-column: 1;
        display: none;
      }

      .box2 {
        grid-column: 1;
        padding: 20px;
      }

      form {
        padding: 20px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
      }

      input,
      select {
        width: 100% !important;
        box-sizing: border-box;
      }

      button {
        width: 100%;
        box-sizing: border-box;
      }
    }
  </style>
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
          <label for="username">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            placeholder="Your Username..."
            required
            autocomplete="username" />
        </div>
        <div class="label">
          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Your Password.."
            required
            autocomplete="current-password" />
        </div>
        <button type="submit" name="login" id="loginBtn">Login</button>
        <p class="para mt-3 d-flex justify-content-center">Looking for Portal?<span><a class="link" href="index.php"> Click Here!</a></span></p>
      </form>

    </div>
  </div>
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/plugins/toastr/toastr.min.js"></script>
  <script src="assets/dist/js/adminlte.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const loginForm = document.querySelector('form');
      const loginBtn = document.getElementById('loginBtn');

      loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('[Login] Form submitted');

        if (loginBtn.disabled) {
          console.log('[Login] Button already disabled, preventing double submit');
          return false;
        }

        loginBtn.disabled = true;
        loginBtn.textContent = 'Logging in...';

        try {
          const formData = new FormData(loginForm);
          const username = formData.get('username');
          const password = formData.get('password');
          console.log('[Login] Attempting login for user:', username);

          const response = await fetch('login.php', {
            method: 'POST',
            body: formData
          });

          console.log('[Login] Response status:', response.status);
          console.log('[Login] Response headers:', response.headers.get('content-type'));

          const text = await response.text();
          console.log('[Login] Response text:', text.substring(0, 200));

          let data;
          try {
            data = JSON.parse(text);
          } catch (e) {
            console.error('[Login] Failed to parse JSON:', e);
            throw new Error('Server returned invalid response: ' + text.substring(0, 100));
          }

          console.log('[Login] Parsed data:', data);

          if (data.success) {
            console.log('[Login] Login successful, redirecting...');
            // Show success message
            if (typeof toastr !== 'undefined') {
              toastr.success('Login successful!', 'Success', {
                timeOut: 1000
              });
            }
            // Redirect after a short delay
            setTimeout(() => {
              console.log('[Login] Redirecting to:', data.redirect);
              window.location.href = data.redirect;
            }, 500);
          } else {
            console.error('[Login] Login failed:', data.message);
            // Show error message
            if (typeof toastr !== 'undefined') {
              toastr.error(data.message || 'Login failed', 'Error', {
                timeOut: 3000,
                positionClass: 'toast-top-center'
              });
            } else {
              alert(data.message || 'Login failed');
            }
            // Re-enable button
            loginBtn.disabled = false;
            loginBtn.textContent = 'Login';
          }
        } catch (error) {
          console.error('[Login] Network/parsing error:', error);
          if (typeof toastr !== 'undefined') {
            toastr.error('Error: ' + error.message, 'Error', {
              timeOut: 3000,
              positionClass: 'toast-top-center'
            });
          } else {
            alert('Error: ' + error.message);
          }
          // Re-enable button
          loginBtn.disabled = false;
          loginBtn.textContent = 'Login';
        }
      });
    });
  </script>
  <?php if ($error): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (typeof toastr !== 'undefined') {
          toastr.error(<?= json_encode($error) ?>, 'Login Failed', {
            timeOut: 3000,
            positionClass: 'toast-top-center'
          });
        } else {
          alert(<?= json_encode($error) ?>);
        }
        // Re-enable login button if error
        const loginBtn = document.getElementById('loginBtn');
        if (loginBtn) {
          loginBtn.disabled = false;
          loginBtn.textContent = 'Login';
        }
      });
    </script>
  <?php endif; ?>
</body>

</html>