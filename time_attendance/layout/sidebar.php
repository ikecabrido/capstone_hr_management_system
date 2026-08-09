<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = $current_page ?? basename($_SERVER['PHP_SELF']);
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'EMPLOYEE';
$userName = htmlspecialchars($_SESSION['user']['name'] ?? $_SESSION['email'] ?? 'User');
$roleLabel = $current_role === 'time' ? 'HR' : 'User';
?>
<nav class="main-header navbar navbar-expand navbar-dark">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="<?= $current_role === 'time' ? 'dashboard.php' : 'employee_dashboard.php' ?>" class="nav-link">Home</a>
    </li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <div class="nav-link" id="clock">--:--:--</div>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Toggle Fullscreen"><i class="fas fa-expand-arrows-alt"></i></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="javascript:void(0);" id="darkToggle" title="Toggle Dark Mode"><i class="fas fa-moon" id="themeIcon"></i></a>
    </li>
  </ul>
</nav>
<aside class="main-sidebar sidebar-dark-primary elevation-4" id="mainSidebar">
  <a href="<?= $current_role === 'time' ? 'dashboard.php' : 'employee_dashboard.php' ?>" class="brand-link">
    <img src="../bcp-logo2.png" alt="BCP Logo" class="brand-image" />
    <span class="brand-text">Time & Attendance</span>
  </a>
  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image"><i class="fas fa-user-circle"></i></div>
      <div class="info"><a href="#" class="d-block"><?= $roleLabel ?> <?= $userName ?></a></div>
    </div>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="dashboard.php" class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <?php if ($current_role === 'time'): ?>
        <li class="nav-item">
          <a href="absence_late_management.php" class="nav-link <?= $current_page === 'absence_late_management.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-calendar-times"></i>
            <p>Absence & Late Management</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="approve_attendance.php" class="nav-link <?= $current_page === 'approve_attendance.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-check-circle"></i>
            <p>Approve Manual Time</p>
          </a>
        </li>
        <!-- Biometric links removed -->
        <li class="nav-item">
          <a href="shifts.php" class="nav-link <?= $current_page === 'shifts.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-clock"></i>
            <p>Shift Management</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="qr_scanner.php" class="nav-link <?= $current_page === 'qr_scanner.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-video"></i>
            <p>Camera Scanner</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="employee_qr_list.php" class="nav-link <?= $current_page === 'employee_qr_list.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-qrcode"></i>
            <p>Employee QR</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="schedule_calendar.php" class="nav-link <?= $current_page === 'schedule_calendar.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-calendar"></i>
            <p>Schedule Calendar</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="holidays.php" class="nav-link <?= $current_page === 'holidays.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-calendar-alt"></i>
            <p>Holidays</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="leave_approvals.php" class="nav-link <?= $current_page === 'leave_approvals.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>Leave Management</p>
          </a>
        </li>
        <?php else: ?>
        <li class="nav-item">
          <a href="leave_request.php" class="nav-link <?= $current_page === 'leave_request.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-plus-circle"></i>
            <p>Submit Request</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="my_absence_appeals.php" class="nav-link <?= $current_page === 'my_absence_appeals.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-calendar-times"></i>
            <p>My Appeals</p>
          </a>
        </li>
        <?php endif; ?>
        <li class="nav-header">SETTINGS</li>
        <li class="nav-item">
          <a href="../../logout.php" class="nav-link text-danger">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Logout</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
