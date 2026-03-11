<?php
/**
 * Shift Management Page
 * HR-only page for managing shifts and employee assignments
 */

// Start session and check authentication
session_start();
require_once(__DIR__ . '/../app/config/Database.php');
require_once(__DIR__ . '/../app/core/Session.php');
require_once(__DIR__ . '/../app/controllers/ShiftController.php');
require_once(__DIR__ . '/../app/helpers/Helper.php');

// Verify user is logged in and is HR
if (empty($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: Login.php');
    exit();
}

// Check if user has HR permissions
$user_role = $_SESSION['role'] ?? '';
if ($user_role !== 'HR_ADMIN' && $user_role !== 'SYSTEM_ADMIN') {
    header('Location: dashboard.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$shiftController = new ShiftController($db);

$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_shift'])) {
        $result = $shiftController->createShift([
            'shift_name' => $_POST['shift_name'],
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'break_duration' => $_POST['break_duration'] ?? 60,
            'description' => $_POST['description'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);
        
        if ($result['success']) {
            $message = $result['message'];
            $action = 'list';
        } else {
            $error = $result['message'];
        }
    }

    if (isset($_POST['update_shift'])) {
        $result = $shiftController->updateShift($_POST['shift_id'], [
            'shift_name' => $_POST['shift_name'],
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'break_duration' => $_POST['break_duration'] ?? 60,
            'description' => $_POST['description'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);
        
        if ($result['success']) {
            $message = $result['message'];
            $action = 'list';
        } else {
            $error = $result['message'];
        }
    }

    if (isset($_POST['delete_shift'])) {
        $result = $shiftController->deleteShift($_POST['shift_id']);
        
        if ($result['success']) {
            $message = $result['message'];
            $action = 'list';
        } else {
            $error = $result['message'];
        }
    }

    if (isset($_POST['assign_shift'])) {
        $result = $shiftController->assignShiftToEmployee(
            $_POST['employee_id'],
            $_POST['shift_id'],
            $_POST['effective_from'],
            $_POST['effective_to'] ?? null
        );
        
        if ($result['success']) {
            $message = $result['message'];
            $action = 'assignments';
        } else {
            $error = $result['message'];
        }
    }
}

// Get data based on action
$shifts = $shiftController->getAllShifts();
$stats = $shiftController->getShiftStatistics();

// For edit action, get specific shift
$editShift = null;
if ($action === 'edit' && isset($_GET['shift_id'])) {
    $editShift = $shiftController->getShiftById($_GET['shift_id']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Management</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background: #f5f5f5;
        }

        .shift-container {
            margin-left: 250px;
            margin-top: 60px;
            flex: 1;
            padding: 30px 20px;
            transition: margin-left 0.3s ease, background-color 0.3s ease, color 0.3s ease;
        }

        .shift-container.sidebar-collapsed {
            margin-left: 0;
        }

        .page-header {
            margin-bottom: 35px;
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 61, 130, 0.15);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .page-title i {
            font-size: 36px;
            opacity: 0.95;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            position: relative;
            z-index: 1;
        }

        body.dark-mode .page-title {
            color: #5fa3ff;
        }

        .shift-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        body.dark-mode .shift-tabs {
            background: #1e1e1e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .shift-tab {
            padding: 13px 24px;
            background: #f8f9fa;
            border: 2px solid transparent;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            color: #666;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .shift-tab i {
            font-size: 17px;
        }

        .shift-tab:hover {
            background: #e8f1ff;
            color: #003d82;
            border-color: #003d82;
            transform: translateY(-1px);
        }

        .shift-tab.active {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 6px 20px rgba(0, 61, 130, 0.3);
            transform: translateY(-2px);
        }

        body.dark-mode .shift-tab {
            background: #2a2a2a;
            color: #b0b0b0;
            border-color: rgba(95, 163, 255, 0.1);
        }

        body.dark-mode .shift-tab:hover {
            background: #333;
            color: #5fa3ff;
            border-color: #5fa3ff;
        }

        body.dark-mode .shift-tab.active {
            background: linear-gradient(135deg, #003d82, #005ba8);
            color: white;
            border-color: transparent;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .shifts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 28px;
            margin-bottom: 35px;
        }

        .shift-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            padding: 28px;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .shift-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #003d82, #005ba8);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .shift-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #003d82, #005ba8);
            border-radius: 50%;
            opacity: 0.05;
            transition: all 0.3s ease;
        }

        .shift-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 12px 32px rgba(0, 61, 130, 0.2);
            border-color: rgba(0, 61, 130, 0.15);
        }

        .shift-card:hover::before {
            transform: scaleX(1);
        }

        .shift-card:hover::after {
            opacity: 0.08;
        }

        body.dark-mode .shift-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e2a3a 100%);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
            border-color: rgba(95, 163, 255, 0.15);
        }

        body.dark-mode .shift-card:hover {
            box-shadow: 0 12px 32px rgba(95, 163, 255, 0.15);
            border-color: rgba(95, 163, 255, 0.2);
        }

        .shift-card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .shift-card-title {
            font-size: 16px;
            font-weight: 600;
            color: #003d82;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        body.dark-mode .shift-card-title {
            color: #5fa3ff;
        }

        .shift-status {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 20px;
            background: #d4edda;
            color: #155724;
            font-weight: 500;
        }

        .shift-status.inactive {
            background: #f8d7da;
            color: #721c24;
        }

        body.dark-mode .shift-status {
            background: #1e5631;
        }

        body.dark-mode .shift-status.inactive {
            background: #5c2a2a;
        }

        .shift-time {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        body.dark-mode .shift-time {
            color: #b0b0b0;
        }

        .shift-details {
            margin-bottom: 12px;
        }

        .shift-details p {
            font-size: 12px;
            color: #999;
            margin: 5px 0;
        }

        body.dark-mode .shift-details p {
            color: #888;
        }

        .shift-card-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .shift-card-actions button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .shift-form {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.1);
            position: relative;
            overflow: hidden;
        }

        .shift-form::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(0, 61, 130, 0.05), rgba(0, 91, 168, 0.02));
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .shift-form:hover::before {
            opacity: 1;
        }

        body.dark-mode .shift-form {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e2a3a 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border-color: rgba(95, 163, 255, 0.15);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #003d82;
            font-size: 14px;
            letter-spacing: 0.3px;
        }

        body.dark-mode .form-group label {
            color: #5fa3ff;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 13px 16px;
            border: 2px solid rgba(0, 61, 130, 0.1);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            background: white;
            color: #333;
            transition: all 0.3s ease;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            font-family: inherit;
        }

        body.dark-mode .form-group input,
        body.dark-mode .form-group select,
        body.dark-mode .form-group textarea {
            background-color: #2a2a2a;
            border-color: #404040;
            color: #e0e0e0;
        }

        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .form-buttons button {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #003d82;
            color: white;
        }

        .btn-primary:hover {
            background: #002a5a;
        }

        .btn-secondary {
            background: #e8eef7;
            color: #003d82;
        }

        .btn-secondary:hover {
            background: #d4dff0;
        }

        body.dark-mode .btn-secondary {
            background: #2a2a2a;
            color: #5fa3ff;
        }

        body.dark-mode .btn-secondary:hover {
            background: #333;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        body.dark-mode .alert-success {
            background: #1e5631;
            color: #81d97d;
            border-color: #2a7a38;
        }

        body.dark-mode .alert-danger {
            background: #5c2a2a;
            color: #f08080;
            border-color: #7a3838;
        }

        .shift-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #003d82, #005ba8);
            border-radius: 50%;
            opacity: 0.08;
            transition: all 0.3s ease;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #003d82, #005ba8);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 61, 130, 0.2);
            border-color: rgba(0, 61, 130, 0.2);
        }

        .stat-card:hover::before {
            opacity: 0.12;
        }

        .stat-card:hover::after {
            transform: scaleX(1);
        }

        body.dark-mode .stat-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e2a3a 100%);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
            border-color: rgba(95, 163, 255, 0.15);
        }

        body.dark-mode .stat-card:hover {
            box-shadow: 0 15px 40px rgba(95, 163, 255, 0.15);
            border-color: rgba(95, 163, 255, 0.3);
        }

        .stat-icon {
            font-size: 40px;
            background: linear-gradient(135deg, #003d82, #005ba8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .stat-number {
            font-size: 42px;
            font-weight: 900;
            background: linear-gradient(135deg, #003d82, #005ba8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        body.dark-mode .stat-number {
            background: linear-gradient(135deg, #5fa3ff, #7bb8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 15px;
            color: #555;
            font-weight: 600;
            letter-spacing: 0.3px;
            position: relative;
            z-index: 1;
        }

        body.dark-mode .stat-label {
            color: #a0a0a0;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #e8eef7;
            margin-bottom: 30px;
        }

        body.dark-mode .table-container {
            background: #1e1e1e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            border-color: #404040;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        body.dark-mode thead {
            background: #2a2a2a;
            border-bottom-color: #404040;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 13px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        body.dark-mode th {
            color: #e0e0e0;
        }

        td {
            padding: 14px 15px;
            border-bottom: 1px solid #e9ecef;
            color: #555;
        }

        body.dark-mode td {
            border-bottom-color: #404040;
            color: #b0b0b0;
        }

        tbody tr:hover {
            background: #f9fbfd;
        }

        body.dark-mode tbody tr:hover {
            background: #2a2a2a;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .shift-container {
                padding: 20px;
                margin-left: 0;
            }

            .page-title {
                font-size: 24px;
            }

            .shift-tabs {
                gap: 10px;
                padding: 12px;
            }

            .shift-tab {
                padding: 10px 16px;
                font-size: 12px;
            }

            .shift-form {
                padding: 20px;
            }

            .shifts-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .shift-stats {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 15px;
            }

            .form-buttons {
                flex-direction: column;
            }

            .form-buttons button {
                width: 100%;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px;
            }

            .shift-card-actions {
                flex-direction: column;
            }
        }
    </style>
    <script src="../assets/mobile-responsive.js" defer></script>
</head>
<body>
    <?php include(__DIR__ . '/../app/components/Sidebar.php'); ?>

    <div class="shift-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-clock"></i>
                Shift Management
            </h1>
            <p class="page-subtitle">
                <i class="fas fa-info-circle"></i> Create and manage shifts, assign employees, and view shift statistics
            </p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <div class="shift-tabs">
            <button class="shift-tab active" onclick="switchTab('shifts')">
                <i class="fas fa-list"></i>
                All Shifts
            </button>
            <button class="shift-tab" onclick="switchTab('create')">
                <i class="fas fa-plus-circle"></i>
                Create Shift
            </button>
            <button class="shift-tab" onclick="switchTab('assignments')">
                <i class="fas fa-user-check"></i>
                Employee Assignments
            </button>
            <button class="shift-tab" onclick="switchTab('statistics')">
                <i class="fas fa-chart-bar"></i>
                Statistics
            </button>
        </div>

        <!-- Shifts List Tab -->
        <div id="shifts" class="tab-content active">
            
            <?php if (!empty($shifts)): ?>
                <div class="shifts-grid">
                    <?php foreach ($shifts as $shift): ?>
                        <div class="shift-card">
                            <div class="shift-card-header">
                                <div>
                                    <div class="shift-card-title">
                                        <i class="fas fa-briefcase"></i> 
                                        <?php echo htmlspecialchars($shift['shift_name']); ?>
                                    </div>
                                </div>
                                <div class="shift-status <?php echo $shift['is_active'] ? '' : 'inactive'; ?>">
                                    <?php echo $shift['is_active'] ? '● Active' : '● Inactive'; ?>
                                </div>
                            </div>
                            
                            <div class="shift-time">
                                <i class="fas fa-clock"></i>
                                <?php echo date('g:i A', strtotime($shift['start_time'])); ?> - 
                                <?php echo date('g:i A', strtotime($shift['end_time'])); ?>
                            </div>

                            <div class="shift-details">
                                <?php if ($shift['description']): ?>
                                    <div class="shift-details-item">
                                        <i class="fas fa-info-circle"></i>
                                        <span><strong>Description:</strong><br>
                                        <?php echo htmlspecialchars($shift['description']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="shift-details-item">
                                    <i class="fas fa-hourglass-half"></i>
                                    <span><strong>Break Duration:</strong> <?php echo $shift['break_duration']; ?> minutes</span>
                                </div>
                            </div>

                            <div class="shift-card-actions">
                                <a href="?action=edit&shift_id=<?php echo $shift['shift_id']; ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <form method="POST" style="flex: 1;">
                                    <input type="hidden" name="shift_id" value="<?php echo $shift['shift_id']; ?>">
                                    <button type="submit" name="delete_shift" class="btn btn-danger" onclick="return confirm('Are you sure?');" style="width: 100%;">
                                        <i class="fas fa-trash-alt"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 40px;">
                    <i class="fas fa-inbox" style="font-size: 32px; display: block; margin-bottom: 15px;"></i>
                    No shifts found. <a href="?action=create" style="color: #667eea; font-weight: 600;">Create one now</a>
                </p>
            <?php endif; ?>
        </div>

        <!-- Create/Edit Shift Tab -->
        <div id="create" class="tab-content <?php echo $action === 'create' || $action === 'edit' ? 'active' : ''; ?>">
            <form method="POST" class="shift-form">
                <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 25px; color: #2c3e50; display: flex; align-items: center; gap: 12px;">
                    <i class="fas <?php echo $editShift ? 'fa-edit' : 'fa-plus-circle'; ?>"></i>
                    <?php echo $editShift ? 'Edit Shift' : 'Create New Shift'; ?>
                </h2>

                <?php if ($editShift): ?>
                    <input type="hidden" name="shift_id" value="<?php echo $editShift['shift_id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="shift_name">
                        <i class="fas fa-briefcase"></i> Shift Name *
                    </label>
                    <input type="text" id="shift_name" name="shift_name" required 
                           value="<?php echo $editShift ? htmlspecialchars($editShift['shift_name']) : ''; ?>" 
                           placeholder="e.g., Morning Shift">
                </div>

                <div class="form-group">
                    <label for="start_time">
                        <i class="fas fa-sign-in-alt"></i> Start Time *
                    </label>
                    <input type="time" id="start_time" name="start_time" required 
                           value="<?php echo $editShift ? $editShift['start_time'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="end_time">
                        <i class="fas fa-sign-out-alt"></i> End Time *
                    </label>
                    <input type="time" id="end_time" name="end_time" required 
                           value="<?php echo $editShift ? $editShift['end_time'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="break_duration">
                        <i class="fas fa-hourglass-half"></i> Break Duration (minutes)
                    </label>
                    <input type="number" id="break_duration" name="break_duration" min="0" max="480" 
                           value="<?php echo $editShift ? $editShift['break_duration'] : '60'; ?>">
                </div>

                <div class="form-group">
                    <label for="description">
                        <i class="fas fa-file-alt"></i> Description
                    </label>
                    <textarea id="description" name="description" 
                              placeholder="Enter shift description (optional)"><?php echo $editShift ? htmlspecialchars($editShift['description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label class="checkbox-group">
                        <input type="checkbox" name="is_active" 
                               <?php echo (!$editShift || $editShift['is_active']) ? 'checked' : ''; ?>>
                        <span><i class="fas fa-check"></i> Active</span>
                    </label>
                </div>

                <div class="form-buttons">
                    <button type="submit" name="<?php echo $editShift ? 'update_shift' : 'create_shift'; ?>" class="btn btn-primary">
                        <i class="fas <?php echo $editShift ? 'fa-save' : 'fa-plus'; ?>"></i>
                        <?php echo $editShift ? 'Update Shift' : 'Create Shift'; ?>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="switchTab('shifts')">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Assignments Tab -->
        <div id="assignments" class="tab-content">
            
            <form method="POST" class="shift-form" style="max-width: 600px;">
                <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 25px; color: #2c3e50; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-user-check"></i>
                    Assign Shift to Employee
                </h2>

                <div class="form-group">
                    <label for="employee_id">
                        <i class="fas fa-user"></i> Employee *
                    </label>
                    <select id="employee_id" name="employee_id" required>
                        <option value="">Select an employee...</option>
                        <?php
                        $stmt = $db->query("SELECT employee_id, first_name, last_name, employee_number FROM employees ORDER BY first_name, last_name");
                        while ($emp = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . $emp['employee_id'] . '">' . htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) . ' (' . $emp['employee_number'] . ')</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="shift_id">
                        <i class="fas fa-briefcase"></i> Shift *
                    </label>
                    <select id="shift_id" name="shift_id" required>
                        <option value="">Select a shift...</option>
                        <?php foreach ($shifts as $shift): ?>
                            <option value="<?php echo $shift['shift_id']; ?>">
                                <?php echo htmlspecialchars($shift['shift_name']); ?> 
                                (<?php echo date('g:i A', strtotime($shift['start_time'])); ?> - 
                                <?php echo date('g:i A', strtotime($shift['end_time'])); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="effective_from">
                        <i class="fas fa-calendar-check"></i> Effective From *
                    </label>
                    <input type="date" id="effective_from" name="effective_from" required value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="effective_to">
                        <i class="fas fa-calendar-times"></i> Effective To (Optional)
                    </label>
                    <input type="date" id="effective_to" name="effective_to">
                </div>

                <div class="form-buttons">
                    <button type="submit" name="assign_shift" class="btn btn-primary">
                        <i class="fas fa-check"></i>
                        Assign Shift
                    </button>
                </div>
            </form>

            <h3 style="margin-top: 40px; font-size: 22px; font-weight: 700; color: #2c3e50;">
                <i class="fas fa-list"></i> Current Assignments
            </h3>
            <?php
            $allAssignments = $shiftController->getEmployeesOnShift(null);
            if (!empty($allAssignments)):
            ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Shift</th>
                                <th>Time</th>
                                <th>From</th>
                                <th>To</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allAssignments as $assign): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($assign['first_name'] . ' ' . $assign['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($assign['shift_name']); ?></td>
                                    <td><?php echo date('g:i A', strtotime($assign['start_time'])); ?> - <?php echo date('g:i A', strtotime($assign['end_time'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($assign['effective_from'])); ?></td>
                                    <td><?php echo $assign['effective_to'] ? date('M d, Y', strtotime($assign['effective_to'])) : 'Ongoing'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No shift assignments found yet.</p>
            <?php endif; ?>
        </div>

        <!-- Statistics Tab -->
        <div id="statistics" class="tab-content">
            
            <div class="shift-stats">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-number"><?php echo count($shifts); ?></div>
                    <div class="stat-label">Total Shifts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-number"><?php echo count(array_filter($shifts, fn($s) => $s['is_active'])); ?></div>
                    <div class="stat-label">Active Shifts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number"><?php echo count($allAssignments ?? []); ?></div>
                    <div class="stat-label">Total Assignments</div>
                </div>
            </div>

            <h3 style="font-size: 22px; font-weight: 700; margin: 30px 0 20px; color: #2c3e50;">
                <i class="fas fa-bar-chart-o"></i> Shift Breakdown
            </h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-briefcase"></i> Shift Name</th>
                            <th><i class="fas fa-clock"></i> Time</th>
                            <th><i class="fas fa-users"></i> Employees Assigned</th>
                            <th><i class="fas fa-info-circle"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats as $stat): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stat['shift_name']); ?></td>
                                <td><?php echo date('g:i A', strtotime($stat['start_time'])); ?> - <?php echo date('g:i A', strtotime($stat['end_time'])); ?></td>
                                <td><?php echo $stat['employee_count']; ?></td>
                                <td>
                                    <span class="shift-status <?php echo $stat['is_active'] ? '' : 'inactive'; ?>">
                                        <?php echo $stat['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Remove active class from all buttons
            const buttons = document.querySelectorAll('.shift-tab');
            buttons.forEach(btn => btn.classList.remove('active'));

            // Show selected tab
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked button
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
