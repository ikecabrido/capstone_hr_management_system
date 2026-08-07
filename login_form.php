<?php
session_start();

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);

// Capture QR token if provided
$qrToken = isset($_GET['qr_token']) ? trim($_GET['qr_token']) : '';
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
        <!-- Hidden field to pass QR token if present -->
        <?php if (!empty($qrToken)): ?>
          <input type="hidden" name="qr_token" value="<?php echo htmlspecialchars($qrToken); ?>" />
        <?php endif; ?>
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
        <div id="loginMessage" class="login-message" aria-live="polite" style="margin-top: 1rem; color: #b02a37; font-weight: 600;"></div>
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
      const loginMessage = document.getElementById('loginMessage');
      let blockTimer = null;
      let debounceTimer = null;

      const formatTime = seconds => {
        const mins = Math.floor(seconds / 60).toString().padStart(2, '0');
        const secs = Math.floor(seconds % 60).toString().padStart(2, '0');
        return `${mins}:${secs}`;
      };

      const startBlockCountdown = seconds => {
        if (blockTimer) {
          clearInterval(blockTimer);
        }

        let remaining = seconds;
        loginBtn.disabled = true;
        loginBtn.textContent = `Locked (${formatTime(remaining)})`;

        if (loginMessage) {
          loginMessage.textContent = `Too many attempts. Try again in ${formatTime(remaining)}.`;
        }

        blockTimer = setInterval(() => {
          remaining -= 1;
          if (remaining <= 0) {
            clearInterval(blockTimer);
            blockTimer = null;
            loginBtn.disabled = false;
            loginBtn.textContent = 'Login';
            if (loginMessage) {
              loginMessage.textContent = 'You can try logging in again.';
            }
            return;
          }

          loginBtn.textContent = `Locked (${formatTime(remaining)})`;
          if (loginMessage) {
            loginMessage.textContent = `Too many attempts. Try again in ${formatTime(remaining)}.`;
          }
        }, 1000);
      };

      // Query block status from server for persistence across refreshes
      const queryBlockStatus = async (username = '') => {
        try {
          const url = new URL('auth/block_status.php', window.location.origin);
          if (username) url.searchParams.set('username', username);
          const res = await fetch(url.toString(), { cache: 'no-store' });
          if (!res.ok) return null;
          const data = await res.json();
          if (data && typeof data.blocked_seconds === 'number' && data.blocked_seconds > 0) {
            startBlockCountdown(data.blocked_seconds);
          }
          return data;
        } catch (e) {
          console.error('[Login] block status fetch error', e);
          return null;
        }
      };

      // On load, check for IP-based (or username-less) block
      queryBlockStatus('');

      // Also check when username field changes (debounced) to handle username-specific blocks
      const usernameInput = document.getElementById('username');
      if (usernameInput) {
        usernameInput.addEventListener('input', () => {
          if (debounceTimer) clearTimeout(debounceTimer);
          debounceTimer = setTimeout(() => {
            const val = usernameInput.value.trim();
            if (val.length > 0) {
              queryBlockStatus(val);
            }
          }, 500);
        });
      }

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
            const isBlocked = response.status === 429;
            const message = data.message || 'Login failed';
            console.error('[Login] Login failed:', message);
            // Show error message
            if (loginMessage) {
              loginMessage.textContent = message;
            }
            if (typeof toastr !== 'undefined') {
              toastr.error(message, isBlocked ? 'Login Blocked' : 'Error', {
                timeOut: 5000,
                positionClass: 'toast-top-center'
              });
            }
            // If the login is blocked, show countdown timer
            if (isBlocked) {
              const blockedSeconds = typeof data.blocked_seconds === 'number' ? data.blocked_seconds : null;
              if (blockedSeconds !== null && blockedSeconds > 0) {
                startBlockCountdown(blockedSeconds);
              } else {
                loginBtn.disabled = true;
                loginBtn.textContent = 'Locked';
              }
            } else {
              loginBtn.disabled = false;
              loginBtn.textContent = 'Login';
            }
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