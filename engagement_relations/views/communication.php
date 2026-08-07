<?php
session_start();
// require_once "auth.php";
require_once "../../auth/database.php";
require_once "../../auth/auth_check.php";

require_once __DIR__ . '/../autoload.php';

use App\Controllers\CommunicationController;
use App\Controllers\MessageController;
use App\Controllers\EmployeeController;

$theme = $_SESSION['user']['theme'] ?? 'light';

$communicationCtrl = new CommunicationController();
$messageCtrl = new MessageController();
$employeeCtrl = new EmployeeController();

$payload = $payload ?? [];
$payload['announcements'] = $communicationCtrl->getAnnouncements();
$payload['department_updates'] = $communicationCtrl->getDepartmentUpdates();
$payload['notifications'] = $communicationCtrl->getNotifications();
$payload['policies'] = $communicationCtrl->getPolicyUpdates();

// Get current user's info
$currentEmployeeId = $_SESSION['user']['employee_id'] ?? null;
$currentUserId = $_SESSION['user']['id'] ?? null;

if ($currentEmployeeId) {
    $payload['messageThreads'] = $messageCtrl->messageThreads($currentEmployeeId);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'announcement' && $currentUserId) {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = $_POST['category'] ?? 'general';
        $priority = $_POST['priority'] ?? 'normal';
        $targetAudience = $_POST['target_audience'] ?? 'all';

        if ($title && $content) {
            $communicationCtrl->postAnnouncement($title, $content, $currentUserId, $category, $priority, $targetAudience);
            $_SESSION['flash_success'] = 'Announcement posted successfully.';
        } else {
            $_SESSION['flash_error'] = 'Title and content are required.';
        }
    }

    elseif ($formType === 'hr_notification' && $currentUserId) {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $notificationType = $_POST['notification_type'] ?? 'info';
        $targetEmployees = $_POST['target_employees'] ?? [];

        if ($title && $content) {
            $communicationCtrl->postHRNotification($title, $content, $notificationType, $targetEmployees, $currentUserId);
            $_SESSION['flash_success'] = 'HR notification sent successfully.';
        } else {
            $_SESSION['flash_error'] = 'Title and content are required.';
        }
    }

    elseif ($formType === 'department_update' && $currentUserId) {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $department = $_POST['department'] ?? '';
        $priority = $_POST['priority'] ?? 'normal';

        if ($title && $content && $department) {
            $communicationCtrl->postDepartmentUpdate($title, $content, $department, $priority, $currentUserId);
            $_SESSION['flash_success'] = 'Department update posted successfully.';
        } else {
            $_SESSION['flash_error'] = 'All fields are required.';
        }
    }

    elseif ($formType === 'policy' && $currentUserId) {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = $_POST['category'] ?? 'general';
        $effectiveDate = $_POST['effective_date'] ?? null;
        $attachmentPath = null;

        if (!empty($_FILES['attachment']['name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $allowedExtensions = ['pdf', 'doc', 'docx'];
            $fileName = basename($_FILES['attachment']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (in_array($fileExtension, $allowedExtensions, true)) {
                $uploadDir = __DIR__ . '/../../../uploads/policies/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $safeFileName = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $fileName);
                $targetFile = $uploadDir . $safeFileName;

                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
                    $attachmentPath = 'uploads/policies/' . $safeFileName;
                }
            }
        }

        if ($title && $content) {
            $communicationCtrl->postPolicy($title, $content, $category, $effectiveDate, $attachmentPath, $currentUserId);
            $_SESSION['flash_success'] = 'Policy posted successfully.';
        } else {
            $_SESSION['flash_error'] = 'Title and content are required.';
        }
    }

    elseif ($formType === 'message' && $currentEmployeeId) {
        $receiverId = trim($_POST['receiver_id'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($receiverId && $message) {
            $messageCtrl->sendMessage($currentEmployeeId, $receiverId, $message);
            $_SESSION['flash_success'] = 'Message sent successfully.';
        } else {
            $_SESSION['flash_error'] = 'Receiver and message are required.';
        }
    }

    elseif ($formType === 'mark_read' && isset($_POST['notification_id'])) {
        $communicationCtrl->markNotificationAsRead($_POST['notification_id']);
        $_SESSION['flash_success'] = 'Notification marked as read.';
    }

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Helper functions
function getPriorityBadgeClass($priority) {
    switch ($priority) {
        case 'urgent': return 'danger';
        case 'high': return 'warning';
        case 'normal': return 'info';
        case 'low': return 'secondary';
        default: return 'light';
    }
}

function getNotificationTypeIcon($type) {
    switch ($type) {
        case 'info': return 'fas fa-info-circle text-info';
        case 'warning': return 'fas fa-exclamation-triangle text-warning';
        case 'success': return 'fas fa-check-circle text-success';
        case 'danger': return 'fas fa-times-circle text-danger';
        default: return 'fas fa-bell text-primary';
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Communication Portal - BCP Bulacan</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />

  <link rel="stylesheet" href="../../layout/toast.css" />
  <link rel="stylesheet" href="../custom.css" />
  <!-- Select2 CSS -->
  <link rel="stylesheet" href="css/communication.css" />

</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__wobble" src="../../assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60" />
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="../engagement_relations.php" class="nav-link">Home</a>
        </li>
      </ul>

      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <div class="nav-link" id="clock">--:--:--</div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" id="darkToggle" role="button" title="Toggle Dark Mode">
            <i class="fas fa-moon" id="themeIcon"></i>
          </a>
        </li>
      </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="../engagement_relations.php" class="brand-link">
        <img src="../../assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan</span>
      </a>

      <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
          <div class="image"></div>
          <div class="info">
            <a href="#" onclick="openGlobalModal('Profile Settings','../../user_profile/profile_form.php')" class="d-block">
              Admin <?= htmlspecialchars($_SESSION['user']['name']) ?>
            </a>
          </div>
        </div>

        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
              <a href="dashboard.php" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="communication.php" class="nav-link active">
                <i class="nav-icon fas fa-bullhorn"></i>
                <p>Communication</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="survey.php" class="nav-link">
                <i class="nav-icon fas fa-poll"></i>
                <p>Survey</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="recognition.php" class="nav-link">
                <i class="nav-icon fas fa-award"></i>
                <p>Recognition</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="grievance.php" class="nav-link">
                <i class="nav-icon fas fa-exclamation-triangle"></i>
                <p>Grievances</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="social.php" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p>Social</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../../logout.php" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>Logout</p>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1><i class="fas fa-bullhorn text-primary"></i> Communication Portal</h1>
              <p class="text-muted">Organized internal communication for better engagement 📢</p>
            </div>
          </div>
        </div>
      </div>

      <section class="content communication-area">
        <div class="container-fluid">
          <!-- Flash Messages -->
          <?php if ($flashSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="fas fa-check-circle"></i> <?= htmlspecialchars($flashSuccess) ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>
          <?php if ($flashError): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($flashError) ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>

          <!-- Communication Portal Tabs -->
          <div class="card shadow-sm border-0 communication-tabs-card">
            <div class="card-header p-0 border-0">
              <ul class="nav nav-tabs" id="communication-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="announcements-tab" data-toggle="pill" href="#announcements" role="tab">
                    <i class="fas fa-bullhorn"></i> Company Announcements
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="notifications-tab" data-toggle="pill" href="#notifications" role="tab">
                    <i class="fas fa-bell"></i> HR Notifications
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="updates-tab" data-toggle="pill" href="#updates" role="tab">
                    <i class="fas fa-building"></i> Department Updates
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="policies-tab" data-toggle="pill" href="#policies" role="tab">
                    <i class="fas fa-file-contract"></i> Policy Sharing
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="messaging-tab" data-toggle="pill" href="#messaging" role="tab">
                    <i class="fas fa-comments"></i> HR-Employee Messaging
                  </a>
                </li>
              </ul>
            </div>

            <div class="card-body communication-tabs-body">
              <div class="tab-content" id="communication-tabs-content">

                <!-- Company Announcements Tab -->
                <div class="tab-pane fade show active" id="announcements" role="tabpanel">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="card card-info">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-plus-circle"></i> Post Announcement</h3>
                        </div>
                        <div class="card-body">
                          <form method="POST" action="">
                            <input type="hidden" name="form_type" value="announcement">
                            <div class="form-group">
                              <label>Title <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" name="title" required placeholder="Announcement title">
                            </div>
                            <div class="form-group">
                              <label>Category</label>
                              <select class="form-control" name="category">
                                <option value="general">General</option>
                                <option value="company">Company News</option>
                                <option value="events">Events</option>
                                <option value="achievements">Achievements</option>
                                <option value="changes">Organizational Changes</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Priority</label>
                              <select class="form-control" name="priority">
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Target Audience</label>
                              <select class="form-control" name="target_audience">
                                <option value="all">All Employees</option>
                                <option value="management">Management Only</option>
                                <option value="staff">Staff Only</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Content <span class="text-danger">*</span></label>
                              <textarea class="form-control" name="content" rows="4" required placeholder="Announcement details..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-info btn-block">
                              <i class="fas fa-paper-plane"></i> Post Announcement
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-8">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-list"></i> Recent Announcements</h3>
                          <div class="card-tools">
                            <button class="btn btn-sm btn-outline-primary" onclick="refreshAnnouncements()">
                              <i class="fas fa-sync"></i> Refresh
                            </button>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="announcements-container">
                            <?php if (!empty($payload['announcements'])): ?>
                              <?php foreach (array_slice($payload['announcements'], 0, 5) as $announcement): ?>
                                <div class="announcement-card card mb-3">
                                  <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                      <h6 class="card-title text-primary mb-1">
                                        <?= htmlspecialchars($announcement['title']) ?>
                                      </h6>
                                      <span class="badge badge-<?= getPriorityBadgeClass($announcement['priority'] ?? 'normal') ?>">
                                        <?= ucfirst($announcement['priority'] ?? 'normal') ?>
                                      </span>
                                    </div>
                                    <p class="card-text text-muted small mb-2">
                                      <i class="fas fa-calendar"></i> <?= date('M d, Y H:i', strtotime($announcement['created_at'])) ?> |
                                      <i class="fas fa-tag"></i> <?= ucfirst($announcement['category'] ?? 'general') ?> |
                                      <i class="fas fa-user"></i> <?= htmlspecialchars($announcement['author_name'] ?? 'Admin') ?>
                                    </p>
                                    <p class="card-text"><?= nl2br(htmlspecialchars(substr($announcement['content'], 0, 200))) ?>...</p>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewFullAnnouncement(<?= $announcement['eer_announcements_id'] ?>)">
                                      <i class="fas fa-eye"></i> Read More
                                    </button>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <div class="text-center text-muted py-4">
                                <i class="fas fa-bullhorn fa-3x mb-3"></i>
                                <h5>No announcements yet</h5>
                                <p>Company announcements will appear here.</p>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- HR Notifications Tab -->
                <div class="tab-pane fade" id="notifications" role="tabpanel">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="card card-warning">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-plus-circle"></i> Send HR Notification</h3>
                        </div>
                        <div class="card-body">
                          <form method="POST" action="">
                            <input type="hidden" name="form_type" value="hr_notification">
                            <div class="form-group">
                              <label>Title <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" name="title" required placeholder="Notification title">
                            </div>
                            <div class="form-group">
                              <label>Type</label>
                              <select class="form-control" name="notification_type">
                                <option value="info">Information</option>
                                <option value="warning">Warning</option>
                                <option value="success">Success</option>
                                <option value="danger">Important</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Target Employees</label>
                              <select class="form-control" name="notification_type">
                                <option value="all">All Employees</option>
                                <option value="management">Management</option>
                                <option value="hr">HR Department</option>
                                <option value="it">IT Department</option>
                                <option value="finance">Finance Department</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Message <span class="text-danger">*</span></label>
                              <textarea class="form-control" name="content" rows="4" required placeholder="Notification content..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning btn-block">
                              <i class="fas fa-paper-plane"></i> Send Notification
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-8">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-bell"></i> HR Notifications</h3>
                        </div>
                        <div class="card-body">
                          <div id="notifications-container">
                            <?php if (!empty($payload['notifications'])): ?>
                              <?php foreach ($payload['notifications'] as $notification): ?>
                                <div class="notification-item <?= $notification['is_read'] ? 'notification-read' : 'notification-unread' ?>">
                                  <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                      <div class="d-flex align-items-center mb-1">
                                        <i class="<?= getNotificationTypeIcon($notification['type'] ?? 'info') ?> mr-2"></i>
                                        <h6 class="mb-0"><?= htmlspecialchars($notification['message'] ?? 'Notification') ?></h6>
                                        <?php if (!$notification['is_read']): ?>
                                          <span class="badge badge-primary ml-2">New</span>
                                        <?php endif; ?>
                                      </div>
                                      <p class="text-muted small mb-1">
                                        <i class="fas fa-calendar"></i> <?= date('M d, Y H:i', strtotime($notification['created_at'] ?? date('Y-m-d H:i:s'))) ?> |
                                        <i class="fas fa-user-tie"></i> HR Department
                                      </p>
                                      <p class="mb-2"><?= htmlspecialchars($notification['message'] ?? 'No details') ?></p>
                                    </div>
                                    <div class="ml-3">
                                      <?php if (!$notification['is_read']): ?>
                                        <form method="POST" class="d-inline">
                                          <input type="hidden" name="form_type" value="mark_read">
                                          <input type="hidden" name="notification_id" value="<?= $notification['id'] ?>">
                                          <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-check"></i> Mark Read
                                          </button>
                                        </form>
                                      <?php endif; ?>
                                    </div>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <div class="text-center text-muted py-4">
                                <i class="fas fa-bell fa-3x mb-3"></i>
                                <h5>No notifications</h5>
                                <p>HR notifications will appear here.</p>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Department Updates Tab -->
                <div class="tab-pane fade" id="updates" role="tabpanel">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="card card-success">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-plus-circle"></i> Post Department Update</h3>
                        </div>
                        <div class="card-body">
                          <form method="POST" action="">
                            <input type="hidden" name="form_type" value="department_update">
                            <div class="form-group">
                              <label>Title <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" name="title" required placeholder="Update title">
                            </div>
                            <div class="form-group">
                              <label>Department <span class="text-danger">*</span></label>
                              <select class="form-control" name="department" required>
                                <option value="">Select Department</option>
                                <option value="hr">Human Resources</option>
                                <option value="it">Information Technology</option>
                                <option value="finance">Finance</option>
                                <option value="operations">Operations</option>
                                <option value="marketing">Marketing</option>
                                <option value="sales">Sales</option>
                                <option value="all">All Departments</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Priority</label>
                              <select class="form-control" name="priority">
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Content <span class="text-danger">*</span></label>
                              <textarea class="form-control" name="content" rows="4" required placeholder="Department update details..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                              <i class="fas fa-paper-plane"></i> Post Update
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-8">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-building"></i> Department Updates</h3>
                          <div class="card-tools">
                            <select class="form-control form-control-sm" id="dept-filter">
                              <option value="">All Departments</option>
                              <option value="hr">HR</option>
                              <option value="it">IT</option>
                              <option value="finance">Finance</option>
                              <option value="operations">Operations</option>
                              <option value="marketing">Marketing</option>
                              <option value="sales">Sales</option>
                            </select>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="updates-container">
                            <?php if (!empty($payload['department_updates'])): ?>
                              <?php foreach (array_slice($payload['department_updates'], 0, 10) as $update): ?>
                                <div class="dept-update-item card mb-3" data-dept="<?= htmlspecialchars($update['department'] ?? '') ?>">
                                  <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                      <h6 class="card-title text-success mb-1">
                                        <?= htmlspecialchars($update['title']) ?>
                                      </h6>
                                      <span class="badge badge-<?= getPriorityBadgeClass($update['priority'] ?? 'normal') ?>">
                                        <?= ucfirst($update['priority'] ?? 'normal') ?>
                                      </span>
                                    </div>
                                    <p class="card-text text-muted small mb-2">
                                      <i class="fas fa-calendar"></i> <?= date('M d, Y H:i', strtotime($update['created_at'])) ?> |
                                      <i class="fas fa-building"></i> <?= ucfirst(str_replace('_', ' ', $update['department'] ?? 'General')) ?> |
                                      <i class="fas fa-user"></i> <?= htmlspecialchars($update['author_name'] ?? 'Admin') ?>
                                    </p>
                                    <p class="card-text"><?= nl2br(htmlspecialchars(substr($update['content'], 0, 200))) ?>...</p>
                                    <button class="btn btn-sm btn-outline-success" onclick="viewFullAnnouncement(<?= $update['eer_announcements_id'] ?>)">
                                      <i class="fas fa-eye"></i> Read More
                                    </button>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <div class="text-center text-muted py-4">
                                <i class="fas fa-building fa-3x mb-3"></i>
                                <h5>Department Updates</h5>
                                <p>Updates from different departments will appear here.</p>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Policy Sharing Tab -->
                <div class="tab-pane fade" id="policies" role="tabpanel">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="card card-primary">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-plus-circle"></i> Share Policy</h3>
                        </div>
                        <div class="card-body">
                          <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="form_type" value="policy">
                            <div class="form-group">
                              <label>Title <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" name="title" required placeholder="Policy title">
                            </div>
                            <div class="form-group">
                              <label>Category</label>
                              <select class="form-control" name="category">
                                <option value="general">General</option>
                                <option value="hr">HR Policies</option>
                                <option value="it">IT Policies</option>
                                <option value="finance">Finance Policies</option>
                                <option value="safety">Safety Policies</option>
                                <option value="conduct">Code of Conduct</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Effective Date</label>
                              <input type="date" class="form-control" name="effective_date">
                            </div>
                            <div class="form-group">
                              <label>Attachment (Optional)</label>
                              <input type="file" class="form-control-file" name="attachment" accept=".pdf,.doc,.docx">
                              <small class="form-text text-muted">Upload policy document</small>
                            </div>
                            <div class="form-group">
                              <label>Content <span class="text-danger">*</span></label>
                              <textarea class="form-control" name="content" rows="4" required placeholder="Policy details..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                              <i class="fas fa-paper-plane"></i> Share Policy
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-8">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-file-contract"></i> Company Policies</h3>
                          <div class="card-tools">
                            <select class="form-control form-control-sm" id="policy-filter">
                              <option value="">All Categories</option>
                              <option value="hr">HR Policies</option>
                              <option value="it">IT Policies</option>
                              <option value="finance">Finance Policies</option>
                              <option value="safety">Safety Policies</option>
                              <option value="conduct">Code of Conduct</option>
                            </select>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="policies-container">
                            <?php if (!empty($payload['policies'])): ?>
                              <?php foreach ($payload['policies'] as $policy): ?>
                                <div class="card mb-3 policy-card" data-category="<?= htmlspecialchars($policy['category'] ?? 'general') ?>">
                                  <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                      <h6 class="card-title text-primary mb-1">
                                        <i class="fas fa-file-contract text-primary mr-2"></i>
                                        <?= htmlspecialchars($policy['title']) ?>
                                      </h6>
                                      <span class="badge badge-<?php 
                                        $cat = $policy['category'] ?? 'general';
                                        switch($cat) {
                                          case 'hr': echo 'primary'; break;
                                          case 'it': echo 'secondary'; break;
                                          case 'finance': echo 'warning'; break;
                                          case 'safety': echo 'danger'; break;
                                          case 'conduct': echo 'success'; break;
                                          default: echo 'info'; break;
                                        }
                                      ?>">
                                        <?= ucfirst($policy['category'] ?? 'general') ?>
                                      </span>
                                    </div>
                                    <p class="text-muted small mb-2">
                                      <i class="fas fa-calendar"></i> Posted: <?= date('M d, Y', strtotime($policy['created_at'] ?? date('Y-m-d H:i:s'))) ?>
                                      <?php if (!empty($policy['effective_date'])): ?>
                                        | Effective: <?= date('M d, Y', strtotime($policy['effective_date'])) ?>
                                      <?php endif; ?>
                                    </p>
                                    <p class="card-text"><?= nl2br(htmlspecialchars(substr($policy['content'], 0, 150))) ?>...</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                      <button class="btn btn-sm btn-outline-primary" onclick="viewFullPolicy(<?= $policy['eer_policy_id'] ?>)">
                                        <i class="fas fa-eye"></i> Read Full Policy
                                      </button>
                                      <?php if (!empty($policy['attachment_path'])): ?>
                                        <a href="policy_download.php?id=<?= $policy['eer_policy_id'] ?>" class="btn btn-sm btn-outline-secondary" download>
                                          <i class="fas fa-download"></i> Download
                                        </a>
                                      <?php endif; ?>
                                    </div>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <div class="text-center text-muted py-4">
                                <i class="fas fa-file-contract fa-3x mb-3"></i>
                                <h5>No policies shared yet</h5>
                                <p>Company policies will appear here.</p>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- HR-Employee Messaging Tab -->
                <div class="tab-pane fade" id="messaging" role="tabpanel">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="card card-info">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-comments"></i> Send Message</h3>
                        </div>
                        <div class="card-body">
                          <form method="POST" action="">
                            <input type="hidden" name="form_type" value="message">
                            <div class="form-group">
                              <label>To <span class="text-danger">*</span></label>
                              <select class="form-control" name="receiver_id" required>
                                <option value="">Select recipient...</option>
                                <!-- Employee options will be populated dynamically -->
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Message <span class="text-danger">*</span></label>
                              <textarea class="form-control" name="message" rows="4" required placeholder="Type your message..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-info btn-block">
                              <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                          </form>
                        </div>
                      </div>

                      <!-- Quick Contacts -->
                      <div class="card mt-3">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-address-book"></i> Quick Contacts</h3>
                        </div>
                        <div class="card-body p-0">
                          <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                              <a href="#" onclick="selectRecipient('hr_manager')" class="text-decoration-none">
                                <i class="fas fa-user-tie text-primary mr-2"></i> HR Manager
                              </a>
                            </li>
                            <li class="list-group-item">
                              <a href="#" onclick="selectRecipient('it_support')" class="text-decoration-none">
                                <i class="fas fa-desktop text-success mr-2"></i> IT Support
                              </a>
                            </li>
                            <li class="list-group-item">
                              <a href="#" onclick="selectRecipient('supervisor')" class="text-decoration-none">
                                <i class="fas fa-user-cog text-warning mr-2"></i> My Supervisor
                              </a>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-8">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-inbox"></i> Message Threads</h3>
                          <div class="card-tools">
                            <button class="btn btn-sm btn-outline-primary" onclick="refreshMessages()">
                              <i class="fas fa-sync"></i> Refresh
                            </button>
                          </div>
                        </div>
                        <div class="card-body message-thread">
                          <div id="messages-container">
                            <?php if ($currentEmployeeId && !empty($payload['messageThreads'])): ?>
                              <?php foreach ($payload['messageThreads'] as $message): ?>
                                <div class="message-bubble <?= $message['sender_id'] == $currentEmployeeId ? 'sent' : 'received' ?>">
                                  <div class="p-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                      <small class="text-muted">
                                        <i class="fas fa-user"></i> <?= htmlspecialchars($message['sender_name'] ?? $message['sender_id']) ?>
                                      </small>
                                      <small class="text-muted">
                                        <i class="fas fa-clock"></i> <?= date('H:i', strtotime($message['timestamp'])) ?>
                                      </small>
                                    </div>
                                    <p class="mb-0"><?= htmlspecialchars($message['message']) ?></p>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <div class="text-center text-muted py-4">
                                <i class="fas fa-comments fa-3x mb-3"></i>
                                <h5>No messages yet</h5>
                                <p>Your conversations with HR will appear here.</p>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include "../../layout/global_modal.php"; ?>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>

  </div>

  <!-- REQUIRED SCRIPTS -->
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <script src="../../assets/dist/js/adminlte.js"></script>

  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script src="../../assets/dist/js/profile.js"></script>
  <script src="js/live_updates.js"></script>

  <script>
  // Communication Portal JavaScript
  window.currentEmployeeId = <?= json_encode($currentEmployeeId) ?>;

  $(document).ready(function() {
    // Load employees for messaging
    loadEmployees();

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
  });

  function refreshAnnouncements() {
    if (window.LiveUpdates && typeof window.LiveUpdates.refreshAnnouncements === 'function') {
      window.LiveUpdates.refreshAnnouncements();
      return;
    }
    location.reload();
  }

  function refreshMessages() {
    if (window.LiveUpdates && typeof window.LiveUpdates.refreshMessages === 'function') {
      window.LiveUpdates.refreshMessages();
      return;
    }
    $('#messages-container').load(window.location.href + ' #messages-container > *');
  }

  function viewFullAnnouncement(id) {
    // Open modal with full announcement
    openGlobalModal('Announcement Details', 'announcement_detail.php?id=' + id);
  }

  function viewFullPolicy(id) {
    // Open modal with full policy
    openGlobalModal('Policy Details', 'policy_detail.php?id=' + id);
  }

  function selectRecipient(type) {
    // Pre-select recipient based on type
    const select = $('select[name="receiver_id"]');
    // This would need to be implemented based on actual employee data
    console.log('Selected recipient type:', type);
  }

  function loadEmployees() {
    // Load employee list for messaging
    $.getJSON('../api/index.php?resource=employee_list', function(data) {
      const select = $('select[name="receiver_id"]');
      select.empty();
      select.append('<option value="">Select recipient...</option>');

      if (data && data.length) {
        data.forEach(function(employee) {
          select.append('<option value="' + employee.employee_id + '">' + employee.full_name + '</option>');
        });
      }
    }).fail(function() {
      console.log('Could not load employees');
    });
  }

  // Filter functionality
  $('#dept-filter').on('change', function() {
    const filter = $(this).val();
    $('.dept-update-item').each(function() {
      if (!filter || $(this).data('dept') === filter) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  $('#policy-filter').on('change', function() {
    const filter = $(this).val();
    $('.policy-card').each(function() {
      if (!filter || $(this).data('category') === filter) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });
  </script>

  <!-- Select2 JS -->
  <script src="../../assets/plugins/select2/js/select2.full.min.js"></script>
  <script>
    // Initialize Select2 for Target Employees
    $(document).ready(function() {
      $('select[name="target_employees[]"]').select2({
        theme: 'bootstrap4',
        width: '100%',
        allowClear: true,
        placeholder: 'Select target employees...'
      });
    });
  </script>
</body>

</html>