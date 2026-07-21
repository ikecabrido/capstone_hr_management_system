<?php
/**
 * Leave Request Page
 * Employees can submit leave requests for approval
 * 
 * NOTE: Authentication is handled by the router (index.php)
 * This file assumes user is already authenticated
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../lib/models/Leave.php';
require_once __DIR__ . '/../lib/helpers/Helper.php';

use TimeAttendancePortal\Config\Database;

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user and employee information from session
// IMPORTANT: Prioritize $_SESSION['user']['id'] which is set by the auth system
// $_SESSION['user_id'] might be set to admin account, not the logged-in employee
$user_id = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? null;

// DEBUG: Log session info
error_log("Leave Request - Session Debug:");
error_log("  _SESSION['user_id']: " . ($_SESSION['user_id'] ?? 'NOT SET'));
error_log("  _SESSION['user']['id']: " . ($_SESSION['user']['id'] ?? 'NOT SET'));
error_log("  Final user_id: " . ($user_id ?? 'NULL'));
error_log("  Full _SESSION: " . json_encode($_SESSION));

// If no user_id, show an error message
if (!$user_id) {
    echo '<div class="alert alert-danger">';
    echo '<h4>Authentication Error</h4>';
    echo '<p>User ID not found in session. Session data:</p>';
    echo '<pre>' . json_encode($_SESSION, JSON_PRETTY_PRINT) . '</pre>';
    echo '<p><a href="' . $_SERVER['HTTP_REFERER'] . '">Go Back</a></p>';
    echo '</div>';
    exit;
}

// Get the ACTUAL employee_id from the employees table using user_id
// This matches how the time attendance portal does it
$employee_id = null;
$database = new Database();
$db = $database->getConnection();
$query = "SELECT employee_id FROM employees WHERE user_id = :user_id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$employee = $stmt->fetch(PDO::FETCH_ASSOC);
error_log("  Employee Query - user_id: {$user_id}, Result: " . json_encode($employee));
if ($employee && $employee['employee_id']) {
    $employee_id = (int)$employee['employee_id'];
}

// If still no employee_id, query all employees to debug
if (!$employee_id) {
    $debug_query = "SELECT id, username, full_name FROM users WHERE id = :user_id";
    $debug_stmt = $db->prepare($debug_query);
    $debug_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $debug_stmt->execute();
    $debug_user = $debug_stmt->fetch(PDO::FETCH_ASSOC);
    error_log("  DEBUG - User lookup from users table: " . json_encode($debug_user));
    
    $all_employees = "SELECT user_id, employee_id FROM employees ORDER BY user_id";
    $all_stmt = $db->prepare($all_employees);
    $all_stmt->execute();
    $all_emps = $all_stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("  DEBUG - All employee-user mappings: " . json_encode($all_emps));
}

$leaveModel = new Leave();
$message = "";
$messageType = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if employee_id is available
    if (!$employee_id) {
        $message = "Error: Employee ID not found. Please log in again. (Debug: user_id={$user_id}, employee_id={$employee_id})";
        $messageType = "error";
    } else {
        $action = trim($_POST['action'] ?? '');

        if ($action === 'submit_request') {
            $leave_type_id = (int)($_POST['leave_type_id'] ?? 0);
            $start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
            $end_date = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';
            $details = isset($_POST['details']) ? trim($_POST['details']) : '';

            // Validation
            $errors = [];
            if (!$leave_type_id) $errors[] = "Leave type is required";
            if (!$start_date) $errors[] = "Start date is required";
            if (!$end_date) $errors[] = "End date is required";
            if (strtotime($end_date) < strtotime($start_date)) $errors[] = "End date must be after start date";

            // Verify employee exists in employees table
            if (empty($errors)) {
                $database = new Database();
                $db = $database->getConnection();
                $query = "SELECT employee_id FROM employees WHERE employee_id = :employee_id LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
                $stmt->execute();
                $employee = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$employee) {
                    $errors[] = "Employee record not found. Contact HR to create your employee profile.";
                }
            }

            if (empty($errors)) {
                $total_days = Helper::calculateWorkingDays($start_date, $end_date);

                $data = [
                    'employee_id' => $employee_id,
                    'leave_type_id' => $leave_type_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'details' => $details,
                    'total_days' => $total_days
                ];

                if ($leaveModel->createRequest($data)) {
                    // Store success message in session and redirect
                    $_SESSION['leave_message'] = "Leave request submitted successfully! Waiting for department head approval.";
                    $_SESSION['leave_messageType'] = "success";
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                } else {
                    $message = "Failed to submit leave request. Please try again. Check your leave balance and ensure no conflicts exist.";
                    $messageType = "error";
                }
            } else {
                $message = implode("<br>", $errors);
                $messageType = "error";
            }
        }
    }
}

// Check for session message (from successful redirect)
if (isset($_SESSION['leave_message'])) {
    $message = $_SESSION['leave_message'];
    $messageType = $_SESSION['leave_messageType'] ?? 'info';
    unset($_SESSION['leave_message']);
    unset($_SESSION['leave_messageType']);
}

// Get leave types using Database class
$database = new Database();
$db = $database->getConnection();
$query = "SELECT * FROM ta_leave_types WHERE is_deductible = 1 OR leave_type_name = 'Emergency Leave' ORDER BY leave_type_name";
$stmt = $db->prepare($query);
$stmt->execute();
$leaveTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request - Employee Portal</title>
    <link rel="icon" href="../../assets/favicon.ico" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-header h2 {
            color: #003d82;
            margin-bottom: 10px;
            font-size: 32px;
            font-weight: bold;
        }

        .page-header p {
            color: #666;
            font-size: 15px;
        }

        .form-section {
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 61, 130, 0.08);
            border: 1px solid #e8eef7;
            margin-bottom: 30px;
        }

        .form-section-title {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f2f5;
        }

        .form-section-title h3 {
            margin: 0;
            color: #003d82;
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 10px;
            color: #003d82;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e8eef7;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f9fbfd;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0066cc;
            background: white;
            box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #003d82 0%, #0066cc 100%);
            color: white;
            padding: 14px 40px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 102, 204, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            padding: 16px 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            border-left: 4px solid;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }

        .info-box {
            background: linear-gradient(135deg, #f0f7ff 0%, #f9fbfd 100%);
            padding: 25px;
            border-radius: 12px;
            margin-top: 35px;
            border: 2px solid #e8eef7;
            border-left: 4px solid #0066cc;
        }

        .info-box h3 {
            margin: 0 0 15px 0;
            color: #003d82;
            font-size: 16px;
        }

        .info-box ul {
            line-height: 2;
            margin-left: 20px;
            color: #555;
        }

        .info-box li {
            margin-bottom: 8px;
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .page-header h2 {
                font-size: 24px;
            }

            .form-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2>Leave Request</h2>
            <p>Submit a leave request for approval by your department head and HR administration</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Diagnostic Info -->
        <div style="background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <h4 style="margin-bottom: 10px; font-size: 14px;">Debug Info:</h4>
            <p><strong>User ID:</strong> <?php echo $user_id ?? 'NOT SET'; ?></p>
            <p><strong>Employee ID:</strong> <?php echo $employee_id ?? 'NOT FOUND'; ?></p>
            <p><strong>Session User:</strong> <?php echo $_SESSION['user']['id'] ?? 'NOT SET'; ?></p>
        </div>

        <form method="POST" class="form-section">
            <div class="form-section-title">
                <h3>Request Details</h3>
            </div>

            <input type="hidden" name="action" value="submit_request">

            <div class="form-group">
                <label>Leave Type *</label>
                <select name="leave_type_id" required>
                    <option value="">-- Select Leave Type --</option>
                    <?php foreach ($leaveTypes as $type): ?>
                        <option value="<?php echo $type['leave_type_id']; ?>">
                            <?php echo htmlspecialchars($type['leave_type_name']); ?> (<?php echo $type['days_per_year']; ?> days/year)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Start Date *</label>
                    <input type="date" name="start_date" required>
                </div>

                <div class="form-group">
                    <label>End Date *</label>
                    <input type="date" name="end_date" required>
                </div>
            </div>

            <div class="form-group">
                <label>Reason for Leave</label>
                <textarea name="details" placeholder="Please provide a brief reason for your leave request (optional)..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">Submit Leave Request</button>
        </form>

        <div class="info-box">
            <h3>Important Information</h3>
            <ul>
                <li>Leave requests are subject to approval by your department head</li>
                <li>HR administration will conduct final review of all leave requests</li>
                <li>Ensure your start and end dates do not exceed your available leave balance</li>
                <li>You will receive notification once your request has been processed</li>
                <li>For urgent requests, please contact your department head directly</li>
            </ul>
        </div>
    </div>

    <script>
        // Handle form submission with loading state
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            
            // Reset button state after 30 seconds if server doesn't respond
            const timeout = setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                alert('Request timeout. Your network may be slow. Please try again.');
            }, 30000);
            
            // Clear timeout when form is actually submitted (page will navigate away)
            window.addEventListener('beforeunload', () => {
                clearTimeout(timeout);
            });
            
            // If the page reloads/redirects, the timeout will be cleared
            // If there's an error and page doesn't redirect, button will reset after 30 seconds
        });
        
        // If page loads with an error message, reset button state
        document.addEventListener('DOMContentLoaded', function() {
            const errorMessage = document.querySelector('.alert-error');
            if (errorMessage) {
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Leave Request';
            }
        });
    </script>
</body>
</html>
