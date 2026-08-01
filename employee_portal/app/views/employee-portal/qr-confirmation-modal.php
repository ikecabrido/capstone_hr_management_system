<?php
/**
 * QR Attendance Confirmation Modal - AJAX Version
 * Shows timestamp and action for employee to confirm before recording
 * Mobile-optimized and works standalone
 * Uses AJAX to submit to /capstone_hr_management_system/qr-process-api.php
 */

date_default_timezone_set('Asia/Manila');

// Verify required variables are set
$qrToken = $qrToken ?? '';
$currentTime = $currentTime ?? date('H:i:s');
$currentDate = $currentDate ?? date('Y-m-d');
$action = $action ?? 'TIME_IN';
$employee = $employee ?? [];
$employee_id = $employee['employee_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>QR Attendance Confirmation</title>
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
            margin: 0;
            width: 100%;
        }

        .modal-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 30px 20px;
            text-align: center;
            animation: slideIn 0.3s ease-out;
        }

        /* Header */
        .modal-header {
            margin-bottom: 25px;
        }

        .modal-header h2 {
            color: #003d82;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .modal-header p {
            color: #666;
            font-size: 13px;
        }

        /* Details Section */
        .details-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 20px;
            border: 2px solid #e9ecef;
        }

        /* Employee Info */
        .info-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            color: #666;
            font-size: 11px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: #003d82;
            font-size: 16px;
            font-weight: 600;
        }

        /* Grid for Date and Time */
        .date-time-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .date-time-grid .info-item {
            margin: 0;
            padding: 0;
            border: none;
        }

        /* Action Alert */
        .action-alert {
            background: #e5f5e5;
            border-radius: 6px;
            padding: 12px;
            border-left: 4px solid #28a745;
            margin-top: 10px;
        }

        .action-alert.timeout {
            background: #ffe5e5;
            border-left-color: #dc3545;
        }

        .action-alert p {
            color: #28a745;
            font-size: 11px;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .action-alert.timeout p {
            color: #dc3545;
        }

        .action-alert .action-text {
            color: #28a745;
            font-size: 18px;
            font-weight: 700;
            margin: 5px 0;
        }

        .action-alert.timeout .action-text {
            color: #dc3545;
        }

        .action-alert .action-desc {
            color: #28a745;
            font-size: 11px;
            margin-top: 5px;
        }

        .action-alert.timeout .action-desc {
            color: #dc3545;
        }

        /* Confirmation Message */
        .confirmation-message {
            background: #e3f2fd;
            border-radius: 8px;
            padding: 12px;
            margin: 18px 0;
            border: 1px solid #90caf9;
            color: #1565c0;
            font-size: 12px;
            line-height: 1.4;
        }

        /* Buttons */
        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 20px;
        }

        button {
            padding: 12px 16px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-confirm {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-confirm:active {
            transform: translateY(0);
        }

        .btn-cancel {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
        }

        .btn-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .btn-cancel:active {
            transform: translateY(0);
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Footer */
        .modal-footer {
            color: #999;
            font-size: 11px;
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid #eee;
        }

        /* Animation */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .modal-container {
                padding: 20px 15px;
            }

            .modal-header h2 {
                font-size: 20px;
            }

            .details-section {
                padding: 15px;
            }

            .info-value {
                font-size: 15px;
            }

            .action-alert .action-text {
                font-size: 16px;
            }

            button {
                font-size: 14px;
                padding: 11px 14px;
            }
        }
    </style>
</head>
<body>
    <div id="qr-toast-placeholder"></div>
    <div class="modal-container">
        <!-- Header -->
        <div class="modal-header">
            <h2>QR Attendance Confirmation</h2>
            <p>Please confirm the scanned time before recording</p>
        </div>

        <!-- Details Section -->
        <div class="details-section">
            <!-- Employee Info -->
            <div class="info-item">
                <div class="info-label">Employee</div>
                <div class="info-value">
                    <?php echo htmlspecialchars($employee['full_name'] ?? 'N/A'); ?>
                </div>
            </div>

            <!-- Date and Time -->
            <div class="date-time-grid">
                <div class="info-item">
                    <div class="info-label">Date</div>
                    <div class="info-value">
                        <?php echo date('M d, Y', strtotime($currentDate)); ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Time</div>
                    <div class="info-value">
                        <?php echo $currentTime; ?>
                    </div>
                </div>
            </div>

            <!-- Action Info -->
            <div class="action-alert <?php echo $action === 'TIME_OUT' ? 'timeout' : ''; ?>">
                <p><?php echo $action === 'TIME_OUT' ? 'Time Out' : 'Time In'; ?></p>
                <div class="action-text">
                    <?php echo $action; ?>
                </div>
                <div class="action-desc">
                    <?php 
                        if ($action === 'TIME_OUT') {
                            echo 'Recording time out for the day';
                        } else {
                            echo 'Starting your work day';
                        }
                    ?>
                </div>
            </div>
        </div>

        <!-- Confirmation Message -->
        <div class="confirmation-message">
            ✓ Is this correct? Click "Confirm" to record your attendance.
        </div>

        <!-- Hidden Form for AJAX -->
        <form id="qrForm" style="display: none;">
            <input type="hidden" id="token" value="<?php echo htmlspecialchars($qrToken); ?>">
            <input type="hidden" id="employee_id" value="<?php echo htmlspecialchars($employee_id); ?>">
            <input type="hidden" id="action" value="<?php echo htmlspecialchars($action); ?>">
        </form>

        <!-- Buttons -->
        <div class="button-group">
            <!-- Confirm Button -->
            <button type="button" id="confirmBtn" class="btn-confirm" onclick="submitAttendance()">
                <span id="spinner" style="display: none; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; vertical-align: middle; margin-right: 6px;"></span>
                <span id="buttonText">✓ Confirm</span>
            </button>

            <!-- Cancel Button -->
            <button type="button" class="btn-cancel" id="cancelBtn" onclick="cancelAttendance()">
                ✕ Cancel
            </button>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
            QR Token expires in 1 minute • Do not share this screen
        </div>
    </div>

    <script>
        function submitAttendance() {
            const token = document.getElementById('token').value;
            const employee_id = document.getElementById('employee_id').value;
            const action = document.getElementById('action').value;
            const confirmBtn = document.getElementById('confirmBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const spinner = document.getElementById('spinner');
            const buttonText = document.getElementById('buttonText');

            if (!token || !employee_id) {
                alert('Missing required information. Please refresh and try again.');
                return;
            }

            // Show loading state
            confirmBtn.disabled = true;
            cancelBtn.disabled = true;
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Processing...';

            // Use absolute path to API endpoint (root level)
            const apiUrl = window.location.origin + '/capstone_hr_management_system/qr-process-api.php';
            console.log('QR: Sending request to:', apiUrl);
            console.log('QR: Employee ID:', employee_id);
            console.log('QR: Token:', token);

            // Send AJAX request to QR API
            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    employee_id: parseInt(employee_id),
                    token: token,
                    action: action
                })
            })
            .then(response => {
                return response.text().then(text => {
                    let data = null;
                    try {
                        data = JSON.parse(text);
                    } catch (parseError) {
                        data = null;
                    }

                    if (!response.ok) {
                        const errorMessage = data?.message || ('HTTP ' + response.status);
                        throw new Error(errorMessage);
                    }

                    return data;
                });
            })
            .then(data => {
                console.log('QR: Success response:', data);
                if (data.success) {
                    showSuccess(data.message);
                } else {
                    console.error('QR: API returned failure:', data);
                    showError(data.message || 'Failed to process attendance');
                    confirmBtn.disabled = false;
                    cancelBtn.disabled = false;
                    spinner.style.display = 'none';
                    buttonText.textContent = '✓ Confirm';
                }
            })
            .catch(error => {
                console.error('QR: Fetch error:', error);
                showError(error.message || 'Error processing attendance');
                confirmBtn.disabled = false;
                cancelBtn.disabled = false;
                spinner.style.display = 'none';
                buttonText.textContent = '✓ Confirm';
            });
        }

        function cancelAttendance() {
            if (confirm('Cancel attendance recording?')) {
                window.location.href = '../../../employee_portal/index.php?url=dashboard';
            }
        }

        function showToast(message, type = 'error') {
            const existingToast = document.getElementById('qrToast');
            if (existingToast) {
                existingToast.remove();
            }

            const toast = document.createElement('div');
            toast.id = 'qrToast';
            toast.className = 'qr-toast ' + type;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('visible');
            }, 10);

            setTimeout(() => {
                toast.classList.remove('visible');
                setTimeout(() => toast.remove(), 300);
            }, 6000);
        }

        function createToastStyles() {
            const styleId = 'qr-toast-styles';
            if (document.getElementById(styleId)) {
                return;
            }

            const style = document.createElement('style');
            style.id = styleId;
            style.textContent = `
                .qr-toast {
                    position: fixed;
                    bottom: 24px;
                    left: 50%;
                    transform: translateX(-50%) translateY(20px);
                    min-width: 280px;
                    max-width: 90%;
                    padding: 14px 18px;
                    border-radius: 10px;
                    color: #fff;
                    font-size: 14px;
                    text-align: center;
                    opacity: 0;
                    transition: opacity 0.25s ease, transform 0.25s ease;
                    z-index: 9999;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                }
                .qr-toast.visible {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0);
                }
                .qr-toast.error {
                    background: #ef4444;
                }
                .qr-toast.success {
                    background: #16a34a;
                }
            `;
            document.head.appendChild(style);
        }

        createToastStyles();

        function showError(message) {
            showToast(message, 'error');
            const confirmBtn = document.getElementById('confirmBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const spinner = document.getElementById('spinner');
            const buttonText = document.getElementById('buttonText');

            confirmBtn.disabled = false;
            cancelBtn.disabled = false;
            spinner.style.display = 'none';
            buttonText.textContent = '✓ Confirm';
        }

        function showSuccess(message) {
            const container = document.querySelector('.modal-container');
            container.innerHTML = `
                <div style="padding: 40px 20px; text-align: center;">
                    <div style="font-size: 64px; margin-bottom: 20px;">✓</div>
                    <h2 style="color: #28a745; font-size: 24px; margin-bottom: 12px;">Success!</h2>
                    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">${escapeHtml(message)}</p>
                    <p style="color: #999; font-size: 12px;">Redirecting to dashboard...</p>
                </div>
            `;
            
            // Redirect after 2 seconds using absolute path
            setTimeout(() => {
                window.location.href = window.location.origin + '/capstone_hr_management_system/employee_portal/index.php?url=dashboard';
            }, 2000);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>

    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</body>
</html>
