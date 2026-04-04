<?php
/**
 * QR Scan Handler - Time & Attendance System
 * Validates QR token and redirects to login or processes attendance
 */

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once "../app/controllers/AuthController.php";
require_once "../app/helpers/QRHelper.php";
require_once "../app/core/Session.php";

Session::start();

// Handle confirmation action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'confirm') {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['qr_pending'])) {
        echo json_encode(['success' => false, 'message' => 'No pending confirmation']);
        exit;
    }

    require_once "../app/config/Database.php";
    require_once "../app/models/ShiftValidator.php";
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $pending = $_SESSION['qr_pending'];
        $employee_id = $pending['employee_id'];
        $action_type = $pending['action_type'];
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        
        // Validate shift assignment again
        $shiftValidator = new ShiftValidator();
        $shiftAssignment = $shiftValidator->hasShiftAssignedToday($employee_id);
        
        if (!$shiftAssignment) {
            echo json_encode([
                'success' => false,
                'message' => 'No shift assigned for today. Attendance cannot be recorded.'
            ]);
            unset($_SESSION['qr_pending']);
            exit;
        }
        
        // Check if there's already a record for today
        $checkQuery = "SELECT attendance_id, time_in, time_out FROM ta_attendance 
                       WHERE employee_id = :emp_id AND attendance_date = :date";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([':emp_id' => $employee_id, ':date' => $today]);
        $record = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        $result = false;
        
        if ($action_type === 'time_in') {
            if (!$record) {
                // Create new record with time_in
                $insertQuery = "INSERT INTO ta_attendance (employee_id, attendance_date, time_in, status, recorded_by) 
                               VALUES (:emp_id, :date, :time_in, 'PRESENT', 'QR')";
                $insertStmt = $conn->prepare($insertQuery);
                $result = $insertStmt->execute([
                    ':emp_id' => $employee_id,
                    ':date' => $today,
                    ':time_in' => $now
                ]);
            } else {
                // Update with time_in
                $updateQuery = "UPDATE ta_attendance SET time_in = :time_in, recorded_by = 'QR' WHERE attendance_id = :id";
                $updateStmt = $conn->prepare($updateQuery);
                $result = $updateStmt->execute([':time_in' => $now, ':id' => $record['attendance_id']]);
            }
        } elseif ($action_type === 'time_out') {
            // Update with time_out
            $updateQuery = "UPDATE ta_attendance SET time_out = :time_out, recorded_by = 'QR' WHERE attendance_id = :id";
            $updateStmt = $conn->prepare($updateQuery);
            $result = $updateStmt->execute([':time_out' => $now, ':id' => $record['attendance_id']]);
        }
        
        if ($result) {
            // Mark token as used
            $markQuery = "UPDATE attendance_tokens SET used = 1, used_by = :emp_id, used_at = NOW() WHERE token = :token";
            $markStmt = $conn->prepare($markQuery);
            $markStmt->execute([':emp_id' => $employee_id, ':token' => $_POST['token'] ?? '']);
            
            echo json_encode([
                'success' => true,
                'message' => $pending['message'] . ' recorded successfully for ' . $pending['full_name']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to record attendance']);
        }
        
        // Clear the pending data
        unset($_SESSION['qr_pending']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Get token from query parameter
$token = trim($_GET['token'] ?? '');

if (empty($token)) {
    $_SESSION['qr_error'] = 'No token provided';
    // Redirect to new employee portal instead of old dashboard
    header("Location: ../../employee_portal/index.php?url=time-attendance");
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
        header("Location: ../../login_form.php");
        exit;
    } else {
        // User is authenticated, send to new employee portal
        header("Location: ../../employee_portal/index.php?url=time-attendance");
        exit;
    }
}

// Check if user is authenticated
if (!AuthController::isAuthenticated()) {
    // Redirect to unified QR handler with the QR token
    header("Location: ../../qr_handler.php?qr_token=" . urlencode($token));
    exit;
}

// User is authenticated - process attendance immediately
require_once "../app/config/Database.php";
require_once "../app/models/Attendance.php";
require_once "../app/models/ShiftValidator.php";

$db = new Database();
$conn = $db->getConnection();

try {
    // Get current logged-in user's employee ID
    // Support both session formats: $_SESSION['user']['id'] (new) and $_SESSION['user_id'] (old)
    $userId = null;
    if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
        $userId = $_SESSION['user']['id'];
    } elseif (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    }
    
    if (!$userId) {
        $_SESSION['qr_error'] = 'User session invalid';
        header("Location: ../../login_form.php");
        exit;
    }

    // Get employee ID from user ID
    $query = "SELECT employee_id, full_name FROM employees WHERE user_id = :user_id LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        $_SESSION['qr_error'] = 'Employee record not found';
        // Redirect to new employee portal
        header("Location: ../../employee_portal/index.php?url=time-attendance");
        exit;
    }

    // Validate shift assignment FIRST
    $shiftValidator = new ShiftValidator();
    $employee_id = $employee['employee_id'];
    $shiftAssignment = $shiftValidator->hasShiftAssignedToday($employee_id);
    
    if (!$shiftAssignment) {
        $_SESSION['qr_error'] = 'No shift assigned for today. Please contact HR to assign a shift before clocking in.';
        // Redirect to new employee portal
        header("Location: ../../employee_portal/index.php?url=time-attendance");
        exit;
    }

    // Record attendance
    $today = date('Y-m-d');
    $now = date('Y-m-d H:i:s');

    // Check if there's already a record for today
    $checkQuery = "SELECT attendance_id, time_in, time_out FROM ta_attendance 
                   WHERE employee_id = :emp_id AND attendance_date = :date";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute([':emp_id' => $employee_id, ':date' => $today]);
    $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);

    $result = false;
    $message = '';
    $action_type = '';

    if ($existingRecord) {
        if (empty($existingRecord['time_in'])) {
            // Should record time_in
            $action_type = 'time_in';
            $message = 'Time In';
        } elseif (empty($existingRecord['time_out'])) {
            // Should record time_out
            $action_type = 'time_out';
            $message = 'Time Out';
        } else {
            // Attendance already complete for today
            $_SESSION['qr_error'] = 'Attendance already recorded for today';
            header("Location: ../../employee_portal/index.php?url=time-attendance");
            exit;
        }
    } else {
        // New attendance record - will be time in
        $action_type = 'time_in';
        $message = 'Time In';
    }

    // Store data in session for confirmation
    $_SESSION['qr_pending'] = [
        'employee_id' => $employee_id,
        'action_type' => $action_type,
        'message' => $message,
        'current_time' => date('H:i:s'),
        'full_name' => $employee['full_name'],
        'token' => $token
    ];

    // Don't process yet - show confirmation page first
    // The page below will display the modal
    $showConfirmation = true;

} catch (Exception $e) {
    $_SESSION['qr_error'] = 'Error: ' . $e->getMessage();
    header("Location: ../../employee_portal/index.php?url=time-attendance");
    exit;
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time & Attendance - QR Scan</title>
    <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #003d82 0%, #0066cc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 61, 130, 0.15);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #003d82;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #003d82;
            font-weight: 600;
            font-size: 14px;
        }

        input[type="text"],
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

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            overflow: hidden;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #003d82 0%, #0066cc 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .modal-body {
            padding: 25px;
            text-align: center;
        }

        .modal-body p {
            margin: 10px 0;
            color: #333;
            font-size: 15px;
            line-height: 1.6;
        }

        .modal-footer {
            padding: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
            border-top: 1px solid #eee;
        }

        .btn-cancel,
        .btn-confirm {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
        }

        .btn-cancel {
            background: #f0f0f0;
            color: #333;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        .btn-confirm {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }
    </style>
</head>
<body>
    <?php if (isset($showConfirmation) && $showConfirmation && isset($_SESSION['qr_pending'])): ?>
        <!-- Confirmation Modal -->
        <div class="modal-overlay" id="confirmationModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Confirm <?php echo $_SESSION['qr_pending']['message']; ?></h2>
                </div>
                <div class="modal-body">
                    <p>Employee: <strong><?php echo htmlspecialchars($_SESSION['qr_pending']['full_name']); ?></strong></p>
                    <p style="margin-top: 15px;">Are you sure you want to record <strong><?php echo strtolower($_SESSION['qr_pending']['message']); ?></strong>?</p>
                    <p style="color: #666; font-size: 13px; margin-top: 10px;">
                        Current time: <strong><?php echo $_SESSION['qr_pending']['current_time']; ?></strong>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="cancelQR()">Cancel</button>
                    <button type="button" class="btn-confirm" onclick="confirmQR()">Yes, Confirm</button>
                </div>
            </div>
        </div>

        <script>
            function confirmQR() {
                // Send AJAX request to process attendance
                fetch('qr_scan.php?action=confirm', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'token=<?php echo urlencode($_SESSION['qr_pending']['token']); ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        localStorage.setItem('qr_success', data.message);
                        window.location.href = '../../employee_portal/index.php?url=time-attendance';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred');
                });
            }

            function cancelQR() {
                // Redirect back to employee portal
                window.location.href = '../../employee_portal/index.php?url=time-attendance';
            }

            // Show modal on page load
            window.addEventListener('load', function() {
                const modal = document.getElementById('confirmationModal');
                if (modal) {
                    modal.style.display = 'flex';
                }
            });
        </script>
    <?php else: ?>
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
</body>
</html>
    // Public QR scan - need employee identification
    // For now, we'll show a form to enter employee ID
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirm Attendance - Time & Attendance System</title>
        <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
        <script src="../assets/mobile-responsive.js" defer></script>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #003d82 0%, #0066cc 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .container {
                background: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 8px 32px rgba(0, 61, 130, 0.2);
                max-width: 500px;
                width: 100%;
            }

            h1 {
                color: #003d82;
                margin-bottom: 10px;
                font-size: 28px;
                font-weight: 700;
            }

            .subtitle {
                color: #666;
                margin-bottom: 30px;
                font-size: 14px;
            }

            .success-icon {
                font-size: 60px;
                margin-bottom: 20px;
            }

            .info {
                background: #f0f7ff;
                padding: 15px;
                border-left: 4px solid #0066cc;
                border-radius: 4px;
                margin-bottom: 20px;
                color: #003d82;
                font-size: 14px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                color: #333;
            }

            .form-group input,
            .form-group select {
                width: 100%;
                padding: 12px;
                border: 2px solid #e8eef7;
                border-radius: 6px;
                font-size: 14px;
                font-family: inherit;
            }

            .form-group input:focus,
            .form-group select:focus {
                outline: none;
                border-color: #0066cc;
                box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
            }

            .buttons {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-top: 30px;
            }

            .btn {
                padding: 12px 20px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-submit {
                background: linear-gradient(135deg, #003d82 0%, #0066cc 100%);
                color: white;
            }

            .btn-submit:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 61, 130, 0.3);
            }

            .btn-cancel {
                background: #f0f0f0;
                color: #333;
            }

            .btn-cancel:hover {
                background: #e0e0e0;
            }

            .loading {
                display: none;
                text-align: center;
            }

            .spinner {
                border: 4px solid #f3f3f3;
                border-top: 4px solid #0066cc;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                animation: spin 1s linear infinite;
                margin: 0 auto 20px;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            .message {
                padding: 15px;
                border-radius: 6px;
                margin-bottom: 20px;
                text-align: center;
            }

            .message.success {
                background: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }

            .message.error {
                background: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }

            @media (max-width: 480px) {
                .container {
                    padding: 20px;
                }

                h1 {
                    font-size: 22px;
                }

                .buttons {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
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
    </body>
    </html>
