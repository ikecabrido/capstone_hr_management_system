<?php
/**
 * QR Code Scanner for Time In/Out
 * Employee scans QR code to perform time in/out
 */

require_once "../app/config/Database.php";
require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/AttendanceController.php";
require_once "../app/helpers/QRHelper.php";
require_once "../app/models/Attendance.php";
require_once "../app/models/Employee.php";
require_once "../app/core/Session.php";

Session::start();

$is_authenticated = AuthController::isAuthenticated();
$page_title = $is_authenticated ? "QR Time Tracker" : "QR Login";

// Handle QR token validation for time in/out
if ($is_authenticated && $_SERVER["REQUEST_METHOD"] === "POST") {
    header('Content-Type: application/json');
    
    $token = trim($_POST['token'] ?? '');
    
    if (empty($token)) {
        echo json_encode(['success' => false, 'message' => 'No token provided']);
        exit;
    }
    
    $qrHelper = new QRHelper();
    $attendanceController = new AttendanceController();
    $user_id = AuthController::getCurrentUserId();
    
    // Get employee ID
    $employeeModel = new Employee();
    $employee = $employeeModel->getByUserId($user_id);
    $employee_id = $employee['employee_id'];
    
    // Validate token
    $tokenDetails = $qrHelper->validateToken($token, $employee_id);
    
    if (!$tokenDetails) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired QR token']);
        exit;
    }
    
    // Check if employee already timed in
    $attendanceModel = new Attendance();
    $statusInfo = $attendanceController->getStatus($employee_id);
    
    if (!empty($statusInfo['time_in']) && empty($statusInfo['time_out'])) {
        // Time out
        $result = $attendanceController->timeOut($employee_id);
        if ($result['success']) {
            echo json_encode([
                'success' => true, 
                'action' => 'time_out',
                'message' => 'Successfully timed out',
                'time' => date('h:i A'),
                'employee_name' => $employee['full_name']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
    } else {
        // Time in
        $result = $attendanceController->timeIn($employee_id, 'QR');
        if ($result['success']) {
            echo json_encode([
                'success' => true, 
                'action' => 'time_in',
                'message' => 'Successfully timed in',
                'time' => date('h:i A'),
                'employee_name' => $employee['full_name']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
    }
    exit;
}

// If not authenticated, show login form
if (!$is_authenticated) {
    // Handle QR redirect from mobile
    $redirect_url = isset($_GET['redirect']) ? base64_decode($_GET['redirect']) : null;
    if ($redirect_url) {
        $_SESSION['qr_redirect'] = $redirect_url;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Time & Attendance</title>
    <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }

        .header {
            margin-bottom: 30px;
        }

        .header-icon {
            font-size: 60px;
            margin-bottom: 15px;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .video-container {
            background: #f0f0f0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
            position: relative;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #scanner {
            width: 100%;
            height: 100%;
        }

        .scanner-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            aspect-ratio: 1;
            border: 3px solid #667eea;
            border-radius: 12px;
            box-shadow: inset 0 0 10px rgba(102, 126, 234, 0.3),
                        0 0 0 2000px rgba(0, 0, 0, 0.5);
        }

        .scanner-corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 3px solid #667eea;
        }

        .scanner-corner.top-left {
            top: -15px;
            left: -15px;
            border-right: none;
            border-bottom: none;
        }

        .scanner-corner.top-right {
            top: -15px;
            right: -15px;
            border-left: none;
            border-bottom: none;
        }

        .scanner-corner.bottom-left {
            bottom: -15px;
            left: -15px;
            border-right: none;
            border-top: none;
        }

        .scanner-corner.bottom-right {
            bottom: -15px;
            right: -15px;
            border-left: none;
            border-top: none;
        }

        .status-message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            animation: slideIn 0.3s ease;
        }

        .status-message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .status-message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        .status-message.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            display: block;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 14px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
            border-left: 4px solid #667eea;
            font-size: 13px;
            color: #555;
        }

        .info-box strong {
            color: #333;
        }

        .login-form {
            display: none;
        }

        .login-form.active {
            display: block;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .confirmation-modal.active {
            display: flex;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .confirmation-content {
            background: white;
            padding: 40px;
            border-radius: 16px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.4s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .confirmation-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .confirmation-content h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .confirmation-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: left;
        }

        .confirmation-details p {
            margin: 10px 0;
            color: #666;
            font-size: 14px;
        }

        .confirmation-details strong {
            color: #333;
            display: block;
            font-size: 18px;
            margin-top: 5px;
        }

        .confirmation-action {
            color: #27ae60;
            font-size: 14px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .loading-spinner {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 22px;
            }

            .btn {
                padding: 10px 16px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($is_authenticated): ?>
            <!-- Authenticated QR Scanner -->
            <div class="header">
                <div class="header-icon">📱</div>
                <h1>QR Time Tracker</h1>
                <p>Point your phone's camera at the QR code</p>
            </div>

            <div id="statusMessage" class="status-message"></div>

            <div class="video-container">
                <video id="scanner" playsinline></video>
                <div class="scanner-overlay">
                    <div class="scanner-corner top-left"></div>
                    <div class="scanner-corner top-right"></div>
                    <div class="scanner-corner bottom-left"></div>
                    <div class="scanner-corner bottom-right"></div>
                </div>
            </div>

            <div class="info-box">
                <strong>Instructions:</strong><br>
                Position the QR code within the frame to scan. Your time will be automatically recorded.
            </div>

            <div class="button-group">
                <a href="employee_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
                <button onclick="toggleCamera()" class="btn btn-primary" id="cameraToggle">Stop Camera</button>
            </div>

            <!-- Time In Confirmation Modal -->
            <div id="timeInModal" class="confirmation-modal">
                <div class="confirmation-content">
                    <div class="confirmation-icon">✓</div>
                    <h2>Timed In Successfully</h2>
                    <div class="confirmation-details">
                        <p>Employee:</p>
                        <strong id="confirmInEmployee"></strong>
                        <p style="margin-top: 15px;">Date & Time:</p>
                        <strong id="confirmInDateTime"></strong>
                    </div>
                    <div class="confirmation-action">✓ Your time in has been recorded</div>
                    <button onclick="closeConfirmation('in')" class="btn btn-primary" style="width: 100%;">OK</button>
                </div>
            </div>

            <!-- Time Out Confirmation Modal -->
            <div id="timeOutModal" class="confirmation-modal">
                <div class="confirmation-content">
                    <div class="confirmation-icon" style="color: #e67e22;">✓</div>
                    <h2 style="color: #e67e22;">Timed Out Successfully</h2>
                    <div class="confirmation-details">
                        <p>Employee:</p>
                        <strong id="confirmOutEmployee"></strong>
                        <p style="margin-top: 15px;">Date & Time:</p>
                        <strong id="confirmOutDateTime"></strong>
                    </div>
                    <div class="confirmation-action" style="color: #e67e22;">✓ Your time out has been recorded</div>
                    <button onclick="closeConfirmation('out')" class="btn btn-primary" style="width: 100%; background: #e67e22;" onmouseover="this.style.background='#d35400'" onmouseout="this.style.background='#e67e22'">OK</button>
                </div>
            </div>

        <?php else: ?>
            <!-- Login Form for QR Redirect -->
            <div class="header">
                <div class="header-icon">🔐</div>
                <h1>Secure Login</h1>
                <p>Please log in to use QR Time Tracker</p>
            </div>

            <form method="POST" action="../../login.php" class="login-form active">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <input type="hidden" name="redirect" value="<?php echo isset($_SESSION['qr_redirect']) ? htmlspecialchars($_SESSION['qr_redirect']) : base64_encode('qr_scanner.php'); ?>">

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px;">Log In</button>
            </form>

            <div class="info-box" style="margin-top: 30px;">
                <strong>Note:</strong> You need to log in with your employee account to use the QR Time Tracker. This ensures accurate time tracking.
            </div>
        <?php endif; ?>
    </div>

    <?php if ($is_authenticated): ?>
        <!-- QR Code Scanner Library -->
        <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

        <script>
            let cameraActive = true;
            let videoStream = null;

            async function startScanner() {
                try {
                    const video = document.getElementById('scanner');
                    
                    // Request camera access
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'environment' }
                    });
                    
                    videoStream = stream;
                    video.srcObject = stream;
                    
                    // Start scanning
                    scanQRCode();
                } catch (error) {
                    showStatus('Error: Cannot access camera. ' + error.message, 'error');
                    console.error('Camera error:', error);
                }
            }

            function scanQRCode() {
                const video = document.getElementById('scanner');
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                const scan = () => {
                    if (cameraActive) {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height);

                        if (code) {
                            processQRCode(code.data);
                        } else {
                            requestAnimationFrame(scan);
                        }
                    }
                };

                scan();
            }

            function processQRCode(data) {
                // Extract token from QR data
                console.log('QR Code detected:', data);
                
                // Assume QR contains the token directly
                const token = data;
                
                // Send to backend
                submitQRToken(token);
            }

            function submitQRToken(token) {
                cameraActive = false;
                showStatus('<div class="loading-spinner"></div> Processing...', 'info');

                const formData = new FormData();
                formData.append('token', token);

                fetch('qr_scanner.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showConfirmation(data.action, data.employee_name, data.time);
                        // Resume scanning after 3 seconds
                        setTimeout(() => {
                            cameraActive = true;
                            scanQRCode();
                        }, 3000);
                    } else {
                        showStatus('Error: ' + data.message, 'error');
                        cameraActive = true;
                        scanQRCode();
                    }
                })
                .catch(error => {
                    showStatus('Error: ' + error.message, 'error');
                    cameraActive = true;
                    scanQRCode();
                });
            }

            function showStatus(message, type) {
                const messageDiv = document.getElementById('statusMessage');
                messageDiv.className = 'status-message ' + type;
                messageDiv.innerHTML = message;
            }

            function showConfirmation(action, employeeName, time) {
                const now = new Date();
                const dateTime = now.toLocaleDateString('en-US', { 
                    weekday: 'short',
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });

                if (action === 'time_in') {
                    document.getElementById('confirmInEmployee').textContent = employeeName;
                    document.getElementById('confirmInDateTime').textContent = dateTime;
                    document.getElementById('timeInModal').classList.add('active');
                } else if (action === 'time_out') {
                    document.getElementById('confirmOutEmployee').textContent = employeeName;
                    document.getElementById('confirmOutDateTime').textContent = dateTime;
                    document.getElementById('timeOutModal').classList.add('active');
                }
            }

            function closeConfirmation(type) {
                if (type === 'in') {
                    document.getElementById('timeInModal').classList.remove('active');
                } else if (type === 'out') {
                    document.getElementById('timeOutModal').classList.remove('active');
                }
            }

            function toggleCamera() {
                const btn = document.getElementById('cameraToggle');
                if (cameraActive) {
                    cameraActive = false;
                    btn.textContent = 'Start Camera';
                } else {
                    cameraActive = true;
                    btn.textContent = 'Stop Camera';
                    scanQRCode();
                }
            }

            // Start scanner when page loads
            document.addEventListener('DOMContentLoaded', startScanner);

            // Stop camera when leaving page
            window.addEventListener('beforeunload', () => {
                if (videoStream) {
                    videoStream.getTracks().forEach(track => track.stop());
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>
