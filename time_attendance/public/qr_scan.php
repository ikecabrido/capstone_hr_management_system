<?php
/**
 * QR Scan Handler - Time & Attendance System
 * Validates QR token and redirects to login or processes attendance
 */

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/helpers/QRHelper.php';
require_once __DIR__ . '/../app/core/Session.php';

Session::start();

// Get token from query parameter
$token = trim($_GET['token'] ?? '');

if (empty($token)) {
    $_SESSION['qr_error'] = 'No token provided';
    header('Location: ' . dirname(__DIR__) . '/../../employee_dashboard.php');
    exit;
}

$qrHelper = new QRHelper();

// Validate token exists and is not expired
$tokenData = $qrHelper->validateToken($token);
if (!$tokenData) {
    $_SESSION['qr_error'] = 'Invalid or expired token';
    
    // Check if user is authenticated
    if (!AuthController::isAuthenticated()) {
        // Redirect to root login with error
        header('Location: ' . dirname(__DIR__) . '/../../login_form.php');
        exit;
    } else {
        // User is authenticated, send to dashboard with error
        if (AuthController::hasRole('time')) {
            header('Location: ' . dirname(__DIR__) . '/public/dashboard.php');
        } else {
            header('Location: ' . dirname(__DIR__) . '/../../employee_dashboard.php');
        }
        exit;
    }
}

// Check if user is authenticated
if (!AuthController::isAuthenticated()) {
    // Redirect to root login with the QR token
    header('Location: ' . dirname(__DIR__) . '/../../login_form.php?qr_token=' . urlencode($token));
    exit;
}

// User is authenticated - process attendance immediately
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../app/models/Attendance.php';

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Get current logged-in user's employee ID
    $userId = $_SESSION['user_id'] ?? null;
    
    if (!$userId) {
        $_SESSION['qr_error'] = 'User session invalid';
        header('Location: ' . dirname(__DIR__) . '/../../login_form.php');
        exit;
    }

    // Get employee ID from user ID
    $query = "SELECT employee_id, full_name FROM employees WHERE user_id = :user_id LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        $_SESSION['qr_error'] = 'Employee record not found';
        
        if (AuthController::hasRole('time')) {
            header('Location: ' . dirname(__DIR__) . '/public/dashboard.php');
        } else {
            header('Location: ' . dirname(__DIR__) . '/../../employee_dashboard.php');
        }
        exit;
    }

    // Record attendance
    $today = date('Y-m-d');
    $now = date('Y-m-d H:i:s');

    // Check if there's already a record for today
    $checkQuery = "SELECT attendance_id, time_in, time_out FROM ta_attendance 
                   WHERE employee_id = :emp_id AND attendance_date = :date";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute([':emp_id' => $employee['employee_id'], ':date' => $today]);
    $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);

    $result = false;
    $message = '';

    if ($existingRecord) {
        if (empty($existingRecord['time_in']) && isset($existingRecord['status']) && $existingRecord['status'] === 'ABSENT') {
            $_SESSION['qr_error'] = 'Your attendance has already been marked ABSENT for today. Please contact HR for assistance.';
            if (AuthController::hasRole('time')) {
                header('Location: ' . dirname(__DIR__) . '/public/dashboard.php');
            } else {
                header('Location: ' . dirname(__DIR__) . '/../../employee_dashboard.php');
            }
            exit;
        }

        if (!$existingRecord['time_in']) {
            // Record time_in
            $updateQuery = "UPDATE ta_attendance SET time_in = :time_in, status = 'PRESENT', recorded_by = 'QR' WHERE attendance_id = :id";
            $updateStmt = $conn->prepare($updateQuery);
            $result = $updateStmt->execute([':time_in' => $now, ':id' => $existingRecord['attendance_id']]);
            $message = 'Time In recorded successfully!';
        } elseif (!$existingRecord['time_out']) {
            // Record time_out
            $updateQuery = "UPDATE ta_attendance SET time_out = :time_out, recorded_by = 'QR' WHERE attendance_id = :id";
            $updateStmt = $conn->prepare($updateQuery);
            $result = $updateStmt->execute([':time_out' => $now, ':id' => $existingRecord['attendance_id']]);
            $message = 'Time Out recorded successfully!';
        } else {
            // Attendance already complete for today
            $_SESSION['qr_error'] = 'Attendance already recorded for today';
            
            if (AuthController::hasRole('time')) {
                header('Location: ' . dirname(__DIR__) . '/public/dashboard.php');
            } else {
                header('Location: ' . dirname(__DIR__) . '/../../employee_dashboard.php');
            }
            exit;
        }
    } else {
        // Create new attendance record
        $insertQuery = "INSERT INTO ta_attendance (employee_id, attendance_date, time_in, status) 
                       VALUES (:emp_id, :date, :time_in, 'PRESENT')";
        $insertStmt = $conn->prepare($insertQuery);
        $result = $insertStmt->execute([
            ':emp_id' => $employee['employee_id'],
            ':date' => $today,
            ':time_in' => $now
        ]);
        $message = 'Time In recorded successfully!';
    }

    // Mark token as used
    if ($result) {
            $markUsedQuery = "UPDATE ta_attendance_tokens SET used = 1, used_by = :emp_id, used_at = NOW() WHERE token = :token";

        // Store success message in session and redirect to dashboard
        $_SESSION['qr_success'] = $message . ' for ' . $employee['full_name'];
        
        if (AuthController::hasRole('time')) {
            header('Location: ' . dirname(__DIR__) . '/public/dashboard.php');
        } else {
            header('Location: ' . dirname(__DIR__) . '/../../employee_dashboard.php');
        }
        exit;
    } else {
        http_response_code(500);
        $_SESSION['qr_error'] = 'Failed to record attendance';
        
        if (AuthController::hasRole('time')) {
            header('Location: ' . dirname(__DIR__) . '/public/dashboard.php');
        } else {
            header('Location: ' . dirname(__DIR__) . '/../../employee_dashboard.php');
        }
        exit;
    }

} catch (Exception $e) {
    $_SESSION['qr_error'] = 'Error: ' . $e->getMessage();
    
    if (AuthController::isAuthenticated()) {
        if (AuthController::hasRole('time')) {
            header('Location: ' . dirname(__DIR__) . '/public/dashboard.php');
        } else {
            header('Location: ' . dirname(__DIR__) . '/../../employee_dashboard.php');
        }
    } else {
        header('Location: ' . dirname(__DIR__) . '/../../login_form.php');
    }
    exit;
}
?>
<?php
$current_page = 'qr_scan.php';
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'guest';
$page_title = 'QR Scan';
$page_head_extra = <<<HTML
<link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #003d82 0%, #0066cc 100%); min-height: 100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
    .container { background: white; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,61,130,0.15); max-width: 500px; width: 100%; padding: 40px; }
    .header { text-align: center; margin-bottom: 30px; }
    .header h1 { color: #003d82; font-size: 28px; font-weight:700; margin-bottom:10px; }
    .header p { color:#666; font-size:14px; }
    .form-group { margin-bottom:20px; }
    label { display:block; margin-bottom:8px; color:#003d82; font-weight:600; font-size:14px; }
    input[type="text"],
HTML;

require_once __DIR__ . '/../layout/page_start.php';
require_once __DIR__ . '/../layout/sidebar.php';
require_once __DIR__ . '/../layout/content_header.php';
?>
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #003d82;
            box-shadow: 0 0 0 3px rgba(0, 61, 130, 0.1);
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #003d82 0%, #0066cc 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 61, 130, 0.3);
        }

        button:active {
            transform: translateY(0);
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 10px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #003d82;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #003d82;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .info-box p {
            color: #555;
            font-size: 13px;
            margin: 0;
        }

        @media (max-width: 480px) {
            .container {
                padding: 25px;
                border-radius: 8px;
                max-width: 100%;
                margin: 0 10px;
            }

            .header h1 {
                font-size: 22px;
            }

            input[type="text"],
            input[type="number"],
            button {
                font-size: 16px;
                padding: 14px;
                min-height: 44px;
            }

            body {
                padding: 10px;
            }
        }
        /* AdminLTE Preloader Styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0d47a1 0%, #0b3c91 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 99999;
        }

        .preloader.flex-column {
            flex-direction: column;
        }

        .preloader.justify-content-center {
            justify-content: center;
        }

        .preloader.align-items-center {
            align-items: center;
        }

        .preloader img {
            max-width: 100px;
            height: auto;
            display: block;
        }

        .animation__wobble {
            animation: wobble 2.5s infinite ease-in-out;
        }

        @keyframes wobble {
            0% {
                transform: translateX(0);
            }
            15% {
                transform: translateX(-5px) rotate(-5deg);
            }
            30% {
                transform: translateX(3px) rotate(3deg);
            }
            45% {
                transform: translateX(-3px) rotate(-3deg);
            }
            60% {
                transform: translateX(2px) rotate(2deg);
            }
            75% {
                transform: translateX(-1px) rotate(-1deg);
            }
            100% {
                transform: translateX(0);
            }
        }

    </style>
</head>
<body>
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__wobble" src="../../assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60" />
    </div>
    <div class="container">
        <div class="header">
            <h1>Time & Attendance</h1>
            <p>QR Scan Check-in</p>
        </div>

        <div class="info-box">
            <p>✓ QR code scanned successfully. Please enter your Employee ID or Number to proceed.</p>
        </div>

        <form id="scanForm" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="form-group">
                <label for="employee_id">Employee ID or Number:</label>
                <input 
                    type="text" 
                    id="employee_id" 
                    name="employee_id" 
                    placeholder="Enter your Employee ID or Number"
                    required
                    autofocus
                >
            </div>

            <button type="submit" id="submitBtn">Record Attendance</button>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p style="margin-top: 10px; color: #003d82;">Processing...</p>
            </div>
        </form>

        <div class="message" id="message"></div>
    </div>

    <script>
        document.getElementById('scanForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const employeeId = document.getElementById('employee_id').value.trim();
            const submitBtn = document.getElementById('submitBtn');
            const loading = document.getElementById('loading');
            const messageDiv = document.getElementById('message');

            if (!employeeId) {
                showMessage('Please enter your Employee ID or Number', 'error');
                return;
            }

            // Disable button and show loading
            submitBtn.disabled = true;
            loading.style.display = 'block';
            messageDiv.style.display = 'none';

            try {
                const formData = new FormData();
                formData.append('token', document.querySelector('input[name="token"]').value);
                formData.append('employee_id', employeeId);

                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                loading.style.display = 'none';

                if (data.success) {
                    showMessage('✓ ' + data.message + '\n\nWelcome, ' + data.employee + '!', 'success');
                    document.getElementById('employee_id').value = '';
                    
                    // Auto-refresh after 2 seconds for next scan
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showMessage('✗ ' + data.message, 'error');
                    submitBtn.disabled = false;
                }
            } catch (error) {
                loading.style.display = 'none';
                showMessage('An error occurred: ' + error.message, 'error');
                submitBtn.disabled = false;
            }
        });

        function showMessage(msg, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.className = 'message ' + type;
            messageDiv.textContent = msg;
            messageDiv.style.display = 'block';
        }
    </script>
    <!-- Preloader Management Script -->
    <script>
        function hidePreloader() {
            const preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'none';
                preloader.style.visibility = 'hidden';
            }
        }

        function showPreloader() {
            const preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'flex';
                preloader.style.visibility = 'visible';
            }
        }

        window.addEventListener('load', hidePreloader);

        document.addEventListener('DOMContentLoaded', function() {
            // Delay hiding preloader so animation is visible
            setTimeout(hidePreloader, 6000);
            const navLinks = document.querySelectorAll('a');

            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const href = this.getAttribute('href');
                    if (href && !href.includes('logout') && !href.startsWith('javascript') && !href.startsWith('#') && !href.startsWith('mailto:') && !href.startsWith('tel:')) {
                        showPreloader();
                    }
                });
            });
        });
    </script>
    // Public QR scan - need employee identification
    // For now, we'll show a form to enter employee ID
    ?>
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__wobble" src="../../assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60" />
    </div>
    <div class="container">
        <h1>✓ QR Code Valid</h1>
        <p class="subtitle">Your QR code has been scanned successfully</p>

        <div class="info">
            This attendance will be recorded to your employee profile. A notification will be sent to your registered email.
        </div>

        <div id="message"></div>

        <form id="attendanceForm" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="form-group">
                <label for="employee_id">Employee ID / Number</label>
                <input 
                    type="text" 
                    id="employee_id" 
                    name="employee_id" 
                    placeholder="Enter your employee ID or scan your badge" 
                    autocomplete="off"
                    autofocus
                    required
                >
            </div>

            <div class="buttons">
                <button type="submit" class="btn btn-submit">Confirm Attendance</button>
                <button type="button" class="btn btn-cancel" onclick="window.location.href='qr_display_kiosk.php'">Cancel</button>
            </div>
        </form>

        <div id="loading" class="loading">
            <div class="spinner"></div>
            <p>Recording your attendance...</p>
        </div>
    </div>

    <script>
        document.getElementById('attendanceForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const loading = document.getElementById('loading');
            const form = document.getElementById('attendanceForm');
            const messageDiv = document.getElementById('message');

            loading.style.display = 'block';
            form.style.display = 'none';
            messageDiv.innerHTML = '';

            try {
                const response = await fetch('<?php echo $_SERVER['REQUEST_URI']; ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    messageDiv.innerHTML = `<div class="message success">${data.message}</div>`;
                    setTimeout(() => {
                        window.location.href = 'qr_display_kiosk.php';
                    }, 2000);
                } else {
                    messageDiv.innerHTML = `<div class="message error">${data.message}</div>`;
                    loading.style.display = 'none';
                    form.style.display = 'block';
                }
            } catch (error) {
                messageDiv.innerHTML = `<div class="message error">Error: ${error.message}</div>`;
                loading.style.display = 'none';
                form.style.display = 'block';
            }
        });
    </script>
<!-- Preloader Management Script -->
<script>
    function hidePreloader() {
        const preloader = document.querySelector('.preloader');
        if (preloader) {
            preloader.style.display = 'none';
            preloader.style.visibility = 'hidden';
        }
    }

    function showPreloader() {
        const preloader = document.querySelector('.preloader');
        if (preloader) {
            preloader.style.display = 'flex';
            preloader.style.visibility = 'visible';
        }
    }

    window.addEventListener('load', hidePreloader);

    document.addEventListener('DOMContentLoaded', function() {
        // Delay hiding preloader so animation is visible
        setTimeout(hidePreloader, 6000);
        const navLinks = document.querySelectorAll('a');

        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                const href = this.getAttribute('href');
                if (href && !href.includes('logout') && !href.startsWith('javascript') && !href.startsWith('#') && !href.startsWith('mailto:') && !href.startsWith('tel:')) {
                    showPreloader();
                }
            });
        });
    });
</script>

<?php require_once __DIR__ . '/../layout/content_footer.php'; ?>
<?php require_once __DIR__ . '/../layout/page_end.php'; ?>

