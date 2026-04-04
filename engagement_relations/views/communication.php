<?php
session_start();
// require_once "auth.php";
require_once "../../auth/database.php";
require_once "../../auth/auth_check.php";

require_once __DIR__ . '/../autoload.php';

use App\Controllers\CommunicationController;

$theme = $_SESSION['user']['theme'] ?? 'light';

$communicationCtrl = new CommunicationController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';
    $currentEmployeeId = $_SESSION['user']['employee_id'] ?? null;
    $currentUserId = $_SESSION['user']['id'] ?? null;

    if (!in_array($formType, ['announcement', 'message'], true) && empty($currentEmployeeId)) {
      $_SESSION['flash_error'] = 'Your account is not linked to an employee record; action is blocked.';
    } else {
      if ($formType === 'announcement') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
          $_SESSION['flash_error'] = 'Title and content are required for announcements.';
        } else {
          $createdBy = $currentEmployeeId ?? $currentUserId;
          $communicationCtrl->postAnnouncement($title, $content, $createdBy);
          $_SESSION['flash_success'] = 'Announcement posted successfully.';
        }
      } elseif ($formType === 'message') {
        $receiverId = trim($_POST['receiver_id'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($receiverId === '' || $message === '') {
          $_SESSION['flash_error'] = 'Receiver ID and message text are required.';
        } else {
          $senderId = $currentEmployeeId ?? $currentUserId;
          if (empty($senderId)) {
            $_SESSION['flash_error'] = 'Unable to determine sender ID.';
          } else {
            $communicationCtrl->sendMessage($senderId, $receiverId, $message);
            $_SESSION['flash_success'] = 'Message sent successfully.';
          }
        }
      } elseif ($formType === 'event') {
        $title = trim($_POST['title'] ?? '');
        $date = trim($_POST['date'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '' || $date === '' || $description === '') {
          $_SESSION['flash_error'] = 'Title, date, and description are required for events.';
        } else {
          $communicationCtrl->postEvent($title, $date, $description, $currentEmployeeId);
          $_SESSION['flash_success'] = 'Event posted successfully.';
        }
      } elseif ($formType === 'policy_post') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
          $_SESSION['flash_error'] = 'Title and content are required for policy updates.';
        } else {
          $communicationCtrl->postPolicy($title, $content, $currentEmployeeId);
          $_SESSION['flash_success'] = 'Policy posted successfully.';
        }
      }
    }

    header('Location: communication.php');
    exit;
}

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);


$payload = $payload ?? [];
$userEmployeeId = (int)($_SESSION['user']['id'] ?? 0);
// Data will be loaded via API using JavaScript
$payload['announcements'] = [];
$payload['threads'] = [];


?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Employee Engagement and Relations Management</title>

  <!-- Google Font: Source Sans Pro -->
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link
    rel="stylesheet"
    href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link
    rel="stylesheet"
    href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />

  <link rel="stylesheet" href="../../layout/toast.css" />
  <link rel="stylesheet" href="css/communication.css" />
  <link rel="stylesheet" href="../custom.css" /> 
</head>

<body
  class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>"
  data-employee-id="<?= (int)$userEmployeeId ?>">
  <div class="wrapper">
    <!-- Preloader -->
    <div
      class="preloader flex-column justify-content-center align-items-center">
      <img
        class="animation__wobble"
        src="../../assets/pics/bcpLogo.png"
        alt="AdminLTELogo"
        height="60"
        width="60" />
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="../engagement_relations.php" class="nav-link">Home</a>        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
          <div class="nav-link" id="clock">--:--:--</div>
        </li>

        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>

        <li class="nav-item">
          <a
            class="nav-link"
            href="#"
            id="darkToggle"
            role="button"
            title="Toggle Dark Mode">
            <i class="fas fa-moon" id="themeIcon"></i>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="../engagement_relations.php" class="brand-link">

        <img
          src="../../assets/pics/bcpLogo.png"
          alt="AdminLTE Logo"
          class="brand-image elevation-3"
          style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan </span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
          <div class="image">
          </div>
          <div class="info">
            <a href="#" onclick="openGlobalModal('Profile Settings ','../../user_profile/profile_form.php')" class="d-block">
              Admin <?= htmlspecialchars($_SESSION['user']['name']) ?>
            </a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul
            class="nav nav-pills nav-sidebar flex-column"
            data-widget="treeview"
            role="menu"
            data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
 <li class="nav-item">
              <a href="dashboard.php" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="communication.php" class="nav-link active">
                <i class="nav-icon fas fa-chart-pie"></i>
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
                <p> Grievances</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="social.php" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p> Social</p>
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
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>


        <!-- MAIN CONTENT --
            <!-- HEADER -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Communication</h1>
              <p class="text-muted">Announcements and Messaging</p>
            </div>
            <!-- /.col -->

            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>

    <div class="communication-area">
      <?php if (!empty($flashSuccess)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($flashSuccess); ?></div>
      <?php endif; ?>
      <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($flashError); ?></div>
      <?php endif; ?>
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title">Post Announcement (Admin)</h3>
        </div>
        <div class="card-body">
          <form method="post">
            <input type="hidden" name="form_type" value="announcement" />
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="announce-title">Title</label>
                <input id="announce-title" class="form-control" type="text" name="title" placeholder="Enter announcement title" required>
              </div>
              <div class="form-group col-md-6">
                <label for="announce-content">Content</label>
                <textarea id="announce-content" class="form-control" name="content" rows="2" placeholder="Enter announcement details" required></textarea>
              </div>
            </div>
            <button class="btn btn-primary" type="submit">Post Announcement</button>
          </form>
        </div>
      </div>

      
      <div class="card card-success card-outline">
        <div class="card-header">
          <h3 class="card-title">Announcements</h3>
        </div>
        <div class="card-body">
          <div class="list-group" id="announcementsList">
            <div class="list-group-item text-muted">Loading announcements...</div>
          </div>
        </div>
      </div>

      <div class="card card-info card-outline">
        <div class="card-header">
          <h3 class="card-title">Send Message</h3>
        </div>
        <div class="card-body">
          <form method="post">
            <input type="hidden" name="form_type" value="message" />
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="receiver-id">Receiver</label>
                <select id="receiver-id" class="form-control" name="receiver_id" required>
                  <option value="">Select employee...</option>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label for="message-text">Message</label>
                <textarea id="message-text" class="form-control" name="message" rows="2" placeholder="Type your message" required></textarea>
              </div>
            </div>
            <button class="btn btn-info" type="submit">Send Message</button>
          </form>
        </div>
      </div>

      <div class="card card-warning card-outline">
        <div class="card-header">
          <h3 class="card-title">Message Threads</h3>
        </div>
        <div class="card-body">
          <div class="timeline" id="messagesList">
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i> Loading messages...
            </div>
          </div>
        </div>
      </div>


    </div>

      </div>
    </div>
  <!-- CONTENT -->

    </div>
    <?php include "../../layout/global_modal.php"; ?>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->

  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../../assets/dist/js/adminlte.js"></script>

  <!-- PAGE PLUGINS -->
  <!-- jQuery Mapael -->
  <script src="../../assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
  <script src="../../assets/plugins/raphael/raphael.min.js"></script>
  <script src="../../assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
  <script src="../../assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
  <!-- ChartJS -->
  <script src="../../assets/plugins/chart.js/Chart.min.js"></script>

  <!-- AdminLTE for demo purposes -->
  <!-- <script src="../../assets/dist/js/demo.js"></script> -->
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <!-- <script src="../../assets/dist/js/pages/dashboard2.js"></script> -->
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script src="../../assets/dist/js/profile.js"></script>

  <script src="js/communication.js"></script>
</body>

</html>
