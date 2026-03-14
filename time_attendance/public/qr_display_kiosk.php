<?php
/**
 * QR Code Display Kiosk
 * Public display for attendance QR codes
 * Continuously generates new codes every 30 seconds
 */

// Set timezone to Philippines (UTC+8)
date_default_timezone_set('Asia/Manila');

require_once "../app/controllers/AuthController.php";
require_once "../app/helpers/QRHelper.php";
require_once "../app/helpers/Helper.php";
require_once "../app/core/Session.php";

Session::start();

// Check authentication (must be HR Admin to access this)
if (!AuthController::isAuthenticated() || !AuthController::hasRole('HR_ADMIN')) {
    header("Location: Login.php");
    exit;
}

$user_id = AuthController::getCurrentUserId();
$qrHelper = new QRHelper();

// Generate a single QR code for this view
$token = $qrHelper->generateToken($user_id, Helper::getCurrentDate());

if (!$token) {
    die("Failed to generate QR token");
}

// Get token IP from database for QR generation
$query = "SELECT ip_address FROM attendance_tokens WHERE token = :token LIMIT 1";
$stmt = $GLOBALS['conn'] ?? null;

// Fallback to getting IP directly
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

// Use the detected IP from the token or detect it now
$host = $qrHelper->getServerIP() ?? '172.20.10.6';

// Add port if specified and not default
$port = $_SERVER['SERVER_PORT'] ?? 80;
if ($port != 80 && $port != 443) {
    $host .= ':' . $port;
}

$qr_url = $protocol . "://" . $host . "/Time_and_Attendance/public/qr_scan.php?token=" . $token;
$qr_image = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=" . urlencode($qr_url);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance QR - Scan to Record Time</title>
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
            background-image: url('../bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .kiosk-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            text-align: center;
            max-width: 600px;
            width: 100%;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(10px);
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 36px;
            color: #003d82;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header p {
            font-size: 18px;
            color: #666;
        }

        .time-info {
            background: #f0f4ff;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 30px;
            font-size: 16px;
            color: #555;
        }

        .qr-container {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 3px solid #667eea;
        }

        .qr-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            background: white;
            padding: 10px;
        }

        .instructions {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 5px solid #2196F3;
        }

        .instructions h3 {
            color: #1565c0;
            margin-bottom: 12px;
            font-size: 18px;
        }

        .instructions ol {
            text-align: left;
            color: #333;
            margin-left: 20px;
            line-height: 1.8;
        }

        .instructions li {
            margin-bottom: 8px;
        }

        .refresh-status {
            font-size: 12px;
            color: #999;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .refresh-timer {
            font-size: 14px;
            color: #667eea;
            font-weight: bold;
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            .kiosk-container {
                padding: 20px;
            }

            .header h1 {
                font-size: 28px;
            }

            .qr-container {
                padding: 20px;
            }
        }

        /* Pulsing animation for new code indicator */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .new-code {
            animation: pulse 2s infinite;
        }

        .back-button-container {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: center;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: #f0f0f0;
            color: #333;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid #ddd;
            cursor: pointer;
        }

        .back-button:hover {
            background: #e0e0e0;
            border-color: #bbb;
            transform: translateX(-3px);
        }

        .back-button:active {
            transform: translateX(-1px);
        }

        .back-icon {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="kiosk-container">
        <!-- Header -->
        <div class="header">
            <h1>Attendance Check-in</h1>
            <p>Scan QR Code to Record Your Time in & Time out</p>
        </div>

        <!-- Current Time -->
        <div class="time-info">
            <strong>Current Time:</strong> <span id="current-time"><?php echo date("H:i:s"); ?></span> | 
            <strong>Date:</strong> <span id="current-date"><?php echo date("D, M d, Y"); ?></span>
        </div>

        <!-- QR Code Display -->
        <div class="qr-container new-code">
            <img src="<?php echo htmlspecialchars($qr_image); ?>" alt="Attendance QR Code" class="qr-image" id="qr-image">
            <small style="display: block; margin-top: 10px; color: #999; font-size: 10px;">URL: <?php echo htmlspecialchars($qr_url); ?></small>
        </div>

        <!-- Instructions -->
        <div class="instructions">
            <h3>How to Use:</h3>
            <ol>
                <li><strong>Open your phone camera</strong></li>
                <li><strong>Point at the QR code</strong> displayed above</li>
                <li><strong>Tap the notification</strong> that appears</li>
                <li><strong>First scan:</strong> Records Time In</li>
                <li><strong>Second scan (same day):</strong> Records Time Out</li>
            </ol>
        </div>

        <!-- Status -->
        <div class="refresh-status">
            <p>QR Code auto-refreshes for security</p>
            <div class="refresh-timer">
                New code in: <span id="countdown">30</span> seconds
            </div>
        </div>

        <!-- Back Button -->
        <div class="back-button-container">
            <a href="javascript:history.back()" class="back-button">
                <span class="back-icon">←</span> Back
            </a>
        </div>
    </div>

    <script>
        // Update current time every second
        function updateTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString();
            const dateStr = now.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
            document.getElementById('current-time').textContent = timeStr;
            document.getElementById('current-date').textContent = dateStr;
        }

        // Countdown timer
        let countdown = 30;
        function updateCountdown() {
            countdown--;
            document.getElementById('countdown').textContent = countdown;
            
            if (countdown <= 0) {
                // Refresh the page to get a new QR code
                location.reload();
            }
        }

        // Initialize
        updateTime();
        setInterval(updateTime, 1000);
        setInterval(updateCountdown, 1000);

        // Optional: Add fullscreen support
        document.addEventListener('keydown', function(e) {
            if (e.key === 'f' || e.key === 'F') {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.log('Fullscreen request failed:', err);
                    });
                } else {
                    document.exitFullscreen();
                }
            }
        });
    </script>
</body>
</html>
