<?php
session_start();
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../autoload.php';

use App\Controllers\CommunicationController;

$communicationCtrl = new CommunicationController();
$announcements = $communicationCtrl->getAnnouncements();
$messageThreads = $communicationCtrl->messageThreads(0);
$theme = $_SESSION['user']['theme'] ?? 'light';
$userName = htmlspecialchars($_SESSION['user']['name'] ?? 'Employee');
$currentPage = basename($_SERVER['PHP_SELF']);

function navItem($page, $icon, $label, $currentPage) {
    $active = $currentPage === $page ? 'active' : '';
    return "<li class=\"nav-item\"><a href=\"{$page}\" class=\"nav-link {$active}\"><i class=\"nav-icon fas fa-{$icon}\"></i><p>{$label}</p></a></li>";
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Employee Portal - Announcements</title>
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="../views/css/communication.css" />

</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button">
            <i class="fas fa-bars"></i>
          </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="index.php" class="nav-link">Dashboard</a>
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

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="index.php" class="brand-link">
        <img src="../../assets/pics/bcpLogo.png" alt="Logo" class="brand-image elevation-3" style="opacity: .8" />
        <span class="brand-text font-weight-light">BCP Employee</span>
      </a>
      <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="info">
            <a href="#" class="d-block"><?= $userName ?></a>
          </div>
        </div>
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <?php echo navItem('index.php', 'bullhorn', 'Announcements', $currentPage); ?>
            <?php echo navItem('survey.php', 'poll', 'Survey', $currentPage); ?>
            <?php echo navItem('social.php', 'users', 'Social', $currentPage); ?>
            <?php echo navItem('grievance.php', 'exclamation-triangle', 'Grievances', $currentPage); ?>
            <?php echo navItem('../../logout.php', 'sign-out-alt', 'Logout', $currentPage); ?>
          </ul>
        </nav>
      </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Announcements</h1>
              <p class="text-muted">Stay updated with company announcements</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <section class="content">
        <div class="container-fluid">
          <?php if (!empty($announcements)): ?>
            <div class="row">
              <?php foreach ($announcements as $announcement): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                  <div class="card card-warning card-outline">
                    <div class="card-header">
                      <h5 class="card-title"><?= htmlspecialchars($announcement['title']) ?></h5>
                      <div class="card-tools">
                        <small class="text-muted">
                          <i class="far fa-calendar"></i> 
                          <?= htmlspecialchars($announcement['created_at']) ?>
                        </small>
                      </div>
                    </div>
                    <div class="card-body">
                      <p><?= nl2br(htmlspecialchars($announcement['content'])) ?></p>
                    </div>
                    <div class="card-footer bg-light">
                      <small class="text-muted">
                        <i class="fas fa-user"></i> 
                        <?= htmlspecialchars($announcement['author_name'] ?? 'Admin') ?>
                      </small>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i> No announcements available at this time.
            </div>
          <?php endif; ?>
        </div>
      </section>

      <!-- Message Threads Section -->
      <section class="content">
        <div class="container-fluid">
          <div class="card card-warning card-outline">
            <div class="card-header">
              <h3 class="card-title">Message Threads</h3>
            </div>
            <div class="card-body">
              <?php if (!empty($messageThreads)): ?>
                <div class="timeline">
                  <?php foreach ($messageThreads as $t): ?>
                    <div class="timeline-item">
                      <span class="time"><i class="far fa-clock"></i> <?= htmlspecialchars($t['timestamp'] ?? '') ?></span>
                      <h5 class="timeline-header"><?= htmlspecialchars($t['sender_name'] ?? 'N/A') ?> → <?= htmlspecialchars($t['receiver_name'] ?? 'N/A') ?></h5>
                      <div class="timeline-body text-clamp-3"><?= nl2br(htmlspecialchars($t['message'])) ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="alert alert-info">
                  <i class="fas fa-info-circle"></i> No messages yet.
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include "../../layout/global_modal.php"; ?>
  </div>

  <!-- Scripts -->
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/dist/js/adminlte.min.js"></script>
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
</body>
</html>
