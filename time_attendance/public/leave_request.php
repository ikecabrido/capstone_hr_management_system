<?php
/**
 * Leave Request Page
 * Employees can submit leave requests for approval
 */

require_once "../app/config/Database.php";
require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/LeaveController.php";
require_once "../app/models/Employee.php";
require_once "../app/models/Leave.php";
require_once "../app/helpers/Helper.php";
require_once "../app/core/Session.php";

Session::start();

// Check if user is authenticated and is employee
if (!AuthController::isAuthenticated()) {
    header("Location: ../../login_form.php");
    exit;
}

if (!AuthController::hasRole('EMPLOYEE')) {
    header("Location: dashboard.php");
    exit;
}

$user_id = AuthController::getCurrentUserId();
$employeeModel = new Employee();
$leaveModel = new Leave();

$employee = $employeeModel->getByUserId($user_id);
$employee_id = $employee['employee_id'];

$message = "";
$messageType = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = trim($_POST['action'] ?? '');

    if ($action === 'submit_request') {
        $leave_type_id = (int)$_POST['leave_type_id'] ?? 0;
        $start_date = Helper::sanitize($_POST['start_date'] ?? '');
        $end_date = Helper::sanitize($_POST['end_date'] ?? '');
        $reason = Helper::sanitize($_POST['reason'] ?? '');

        // Validation
        $errors = [];
        if (!$leave_type_id) $errors[] = "Leave type is required";
        if (!$start_date) $errors[] = "Start date is required";
        if (!$end_date) $errors[] = "End date is required";
        if (strtotime($end_date) < strtotime($start_date)) $errors[] = "End date must be after start date";

        if (empty($errors)) {
            $total_days = Helper::calculateWorkingDays($start_date, $end_date);

            $data = [
                'employee_id' => $employee_id,
                'leave_type_id' => $leave_type_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'reason' => $reason,
                'total_days' => $total_days
            ];

            if ($leaveModel->createRequest($data)) {
                $message = "Leave request submitted successfully! Waiting for department head approval.";
                $messageType = "success";
            } else {
                $message = "Failed to submit leave request. Please try again.";
                $messageType = "error";
            }
        } else {
            $message = implode("<br>", $errors);
            $messageType = "error";
        }
    }
}

// Get leave types using Database class
require_once "../app/config/Database.php";
$database = new Database();
$db = $database->getConnection();
$query = "SELECT * FROM ta_leave_types WHERE is_deductible = 1 ORDER BY leave_type_name";
$stmt = $db->prepare($query);
$stmt->execute();
$leaveTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request - Time & Attendance System</title>
    <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/mobile-responsive.js" defer></script>
    <style>
        body {
            background: #f0f2f5;
            margin: 0;
            padding: 0;
            transition: margin-left 0.3s ease;
        }

        body.sidebar-collapsed {
            margin-left: 0;
        }

        .main-content {
            width: calc(100% - 250px);
            margin-left: 250px;
            margin-top: 60px;
            min-height: calc(100vh - 60px);
            padding: 30px 20px;
            transition: width 0.3s ease, margin-left 0.3s ease;
        }

        body.sidebar-collapsed .main-content {
            width: 100%;
            margin-left: 0;
        }
        .content-wrapper {
            max-width: 900px;
            margin: 0 auto;
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
        .btn-primary::before {
            content: "✓";
            font-size: 18px;
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
        .alert::before {
            font-size: 20px;
            flex-shrink: 0;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        .alert-success::before {
            content: "✓";
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        .alert-error::before {
            content: "⚠";
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
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-box h3::before {
            content: "ℹ";
            font-size: 18px;
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
            .main-content {
                margin-left: 0;
                padding: 20px 15px;
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
    <?php require_once "../app/components/Sidebar.php"; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <h2>Leave Request</h2>
                <p>Submit a leave request for approval by your department head and HR administration</p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

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
                    <textarea name="reason" placeholder="Please provide a brief reason for your leave request (optional)..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit Leave Request</button>
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
    </div>
</body>
</html>
