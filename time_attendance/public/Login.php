<?php
/**
 * Login Page - Time & Attendance System
 * Secure login interface for HR and Employees
 */

require_once "../app/controllers/AuthController.php";
require_once "../app/core/Session.php";
require_once "../app/config/Database.php";
require_once "../app/helpers/QRHelper.php";

Session::start();

// Get QR token if it exists
$qrToken = trim($_GET['qr_token'] ?? '');

// Redirect if already logged in
if (AuthController::isAuthenticated()) {
    // If QR token exists, process attendance and redirect with success message
    if (!empty($qrToken)) {
        $qrHelper = new QRHelper();
        $tokenData = $qrHelper->validateToken($qrToken);
        
        if ($tokenData) {
            try {
                $db = new Database();
                $conn = $db->getConnection();
                
                $userId = $_SESSION['user_id'] ?? null;
                if ($userId) {
                    // Get employee ID from user ID
                    $query = "SELECT employee_id, first_name, last_name FROM employees WHERE user_id = :user_id LIMIT 1";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([':user_id' => $userId]);
                    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($employee) {
                        $today = date('Y-m-d');
                        $now = date('Y-m-d H:i:s');

                        // Check if there's already a record for today
                        $checkQuery = "SELECT attendance_id, time_in, time_out FROM attendance 
                                       WHERE employee_id = :emp_id AND attendance_date = :date";
                        $checkStmt = $conn->prepare($checkQuery);
                        $checkStmt->execute([':emp_id' => $employee['employee_id'], ':date' => $today]);
                        $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);

                        $message = '';
                        $result = false;

                        if ($existingRecord) {
                            if (!$existingRecord['time_in']) {
                                $updateQuery = "UPDATE attendance SET time_in = :time_in WHERE attendance_id = :id";
                                $updateStmt = $conn->prepare($updateQuery);
                                $result = $updateStmt->execute([':time_in' => $now, ':id' => $existingRecord['attendance_id']]);
                                $message = 'Time In';
                            } elseif (!$existingRecord['time_out']) {
                                $updateQuery = "UPDATE attendance SET time_out = :time_out WHERE attendance_id = :id";
                                $updateStmt = $conn->prepare($updateQuery);
                                $result = $updateStmt->execute([':time_out' => $now, ':id' => $existingRecord['attendance_id']]);
                                $message = 'Time Out';
                            }
                        } else {
                            $insertQuery = "INSERT INTO attendance (employee_id, attendance_date, time_in, status) 
                                           VALUES (:emp_id, :date, :time_in, 'PRESENT')";
                            $insertStmt = $conn->prepare($insertQuery);
                            $result = $insertStmt->execute([
                                ':emp_id' => $employee['employee_id'],
                                ':date' => $today,
                                ':time_in' => $now
                            ]);
                            $message = 'Time In';
                        }

                        // Mark token as used
                        if ($result) {
                            $markUsedQuery = "UPDATE attendance_tokens SET used = 1, used_by = :emp_id, used_at = NOW() WHERE token = :token";
                            $markStmt = $conn->prepare($markUsedQuery);
                            $markStmt->execute([':emp_id' => $employee['employee_id'], ':token' => $qrToken]);
                            
                            $_SESSION['qr_success'] = $message . ' recorded successfully for ' . $employee['first_name'] . ' ' . $employee['last_name'];
                        }
                    }
                }
            } catch (Exception $e) {
                // Log error but don't break
            }
        }
        
        // Clear the qr_token from URL and redirect to dashboard
        if (AuthController::hasRole('HR_ADMIN')) {
            header("Location: dashboard.php");
        } else {
            header("Location: employee_dashboard.php");
        }
        exit;
    }
    
    // Normal redirect without QR
    if (AuthController::hasRole('HR_ADMIN')) {
        header("Location: dashboard.php");
    } else {
        header("Location: employee_dashboard.php");
    }
    exit;
}

$error = "";
$qrSuccess = $_SESSION['qr_success'] ?? "";
unset($_SESSION['qr_success']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Username and password are required";
    } else {
        $auth = new AuthController();
        $error = $auth->login($username, $password);
        
        // If login successful and QR token exists, process it
        if (empty($error) && !empty($qrToken)) {
            header("Location: Login.php?qr_token=" . urlencode($qrToken));
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Time & Attendance System</title>
    <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/mobile-responsive.js" defer></script>
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <style>
        /* Preloader Styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }

        .flex-column {
            display: flex;
            flex-direction: column;
        }

        .justify-content-center {
            justify-content: center;
        }

        .align-items-center {
            align-items: center;
        }

        /* Continuous wobble animation for preloader */
        .animation__wobble {
            animation: wobble-login 1.5s infinite ease-in-out;
        }

        @keyframes wobble-login {
            0% {
                transform: none;
            }
            15% {
                transform: translate3d(-25%, 0, 0) rotate3d(0, 0, 1, -5deg);
            }
            30% {
                transform: translate3d(20%, 0, 0) rotate3d(0, 0, 1, 3deg);
            }
            45% {
                transform: translate3d(-15%, 0, 0) rotate3d(0, 0, 1, -3deg);
            }
            60% {
                transform: translate3d(10%, 0, 0) rotate3d(0, 0, 1, 2deg);
            }
            75% {
                transform: translate3d(-5%, 0, 0) rotate3d(0, 0, 1, -1deg);
            }
            100% {
                transform: none;
            }
        }

        body.login-page {
            background-image: url('../bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            position: relative;
        }

        .login-container {
            position: relative;
            z-index: 2;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .login-header h1 {
            font-size: 24px;
            color: #003d82;
            margin: 0;
        }

        /* Floating Label Styles */
        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-group label {
            position: absolute;
            top: 12px;
            left: 12px;
            font-size: 14px;
            color: #666;
            pointer-events: none;
            transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            transform-origin: left center;
            padding: 0;
            z-index: 10;
            background: white;
            padding: 0 4px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 12px 12px 12px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
            background: white;
            position: relative;
            z-index: 1;
        }

        .form-group input:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        /* When input is hovered, focused, or has value */
        .form-group input:hover ~ label,
        .form-group input:focus ~ label,
        .form-group input:not(:placeholder-shown) ~ label {
            top: -8px;
            font-size: 12px;
            color: #0066cc;
            font-weight: 600;
            background: white;
        }

        .form-group {
            display: flex;
            flex-direction: column-reverse;
        }
    </style>
</head>
<body class="login-page">
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img
            class="animation__wobble"
            src="../assets/pics/bcpLogo.png"
            alt="AdminLTELogo"
            height="60"
            width="60" />
    </div>

    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="../bcp-logo2.png" alt="Bestlink College Logo">
                <h1>Bestlink College of the Philippines</h1>
            </div>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <input type="text" id="username" name="username" placeholder=" " required autofocus>
                    <label for="username">Username</label>
                </div>

                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder=" " required>
                    <label for="password">Password</label>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </div>
            </form>

            <?php if (!empty($qrSuccess)): ?>
                <div class="alert alert-success">
                    <strong>Success!</strong> <?php echo htmlspecialchars($qrSuccess); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <strong>Login Failed:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Handle floating labels on page load and input changes
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-group input');
            
            inputs.forEach(input => {
                // Check if input has value on page load
                if (input.value) {
                    input.classList.add('has-value');
                }
                
                // Update label position on input
                input.addEventListener('input', function() {
                    if (this.value) {
                        this.classList.add('has-value');
                    } else {
                        this.classList.remove('has-value');
                    }
                });

                // Update label position on focus
                input.addEventListener('focus', function() {
                    this.classList.add('focused');
                });

                // Update label position on blur
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.classList.remove('focused');
                    }
                });
            });
        });
    </script>

    <!-- Preloader Management Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const preloader = document.querySelector('.preloader');
            const loginForm = document.querySelector('.login-form');

            // Hide preloader after initial page load (800ms)
            setTimeout(function() {
                if (preloader) {
                    preloader.style.display = 'none';
                }
            }, 800);

            // Show preloader on form submission
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    if (preloader) {
                        preloader.style.display = 'flex';
                    }
                });
            }
        });
    </script>
</body>
</html>
