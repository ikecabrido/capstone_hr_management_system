<?php
session_start();
require_once "../auth/auth_check.php";
$theme = $_SESSION['user']['theme'] ?? 'light';
$role = $_SESSION['user']['role'] ?? 'employee';

// determine which module page to show
$page = $_GET['page'] ?? 'analytics';
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Learning and Development Management</title>

  <!-- Google Font: Source Sans Pro -->
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link
    rel="stylesheet"
    href="../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link
    rel="stylesheet"
    href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="custom.css" />
  <link rel="stylesheet" href="../layout/toast.css" />
</head>

<body
  class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Preloader -->
    <div
      class="preloader flex-column justify-content-center align-items-center">
      <img
        class="animation__wobble"
        src="../assets/pics/bcpLogo.png"
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
          <a href="learning_development.php" class="nav-link">Home</a>
        </li>
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
      <a href="learning_development.php" class="brand-link">

        <img
          src="../assets/pics/bcpLogo.png"
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
            <a href="#" onclick="openGlobalModal('Profile Settings ','../user_profile/profile_form.php')" class="d-block">
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
            <li class="nav-item has-treeview <?= in_array($page, ['analytics', 'dashboard']) ? 'menu-open' : '' ?>">
              <a href="#" class="nav-link <?= in_array($page, ['analytics', 'dashboard']) ? 'active' : '' ?>">
                <i class="nav-icon fas fa-home"></i>
                <p>
                  Home
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=dashboard" class="nav-link <?= $page === 'dashboard' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-globe"></i>
                    <p>Homepage</p>
                  </a>
                </li>
                <?php if (in_array($role, ['admin', 'learning'])): ?>
                <li class="nav-item">
                  <a href="?page=analytics" class="nav-link <?= $page === 'analytics' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-chart-bar"></i>
                    <p>Analytics Dashboard</p>
                  </a>
                </li>
                <?php endif; ?>
              </ul>
            </li>
            
            <!-- MAIN MODULES SECTION -->
            <li class="nav-header">LEARNING MODULES</li>
            
            <li class="nav-item has-treeview <?= in_array($page, ['training', 'training-browse', 'training-create']) ? 'menu-open' : '' ?>">
              <a href="#" class="nav-link <?= in_array($page, ['training', 'training-browse', 'training-create']) ? 'active' : '' ?>">
                <i class="nav-icon fas fa-book"></i>
                <p>
                  Training
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=training-browse" class="nav-link <?= $page === 'training-browse' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-eye"></i>
                    <p>Browse Training</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=training-create" class="nav-link <?= $page === 'training-create' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-plus"></i>
                    <p>Create Training</p>
                  </a>
                </li>
              </ul>
            </li>
            
            <li class="nav-item has-treeview <?= in_array($page, ['career', 'career-browse', 'career-create']) ? 'menu-open' : '' ?>">
              <a href="#" class="nav-link <?= in_array($page, ['career', 'career-browse', 'career-create']) ? 'active' : '' ?>">
                <i class="nav-icon fas fa-rocket"></i>
                <p>
                  Career
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=career-browse" class="nav-link <?= $page === 'career-browse' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-eye"></i>
                    <p>Browse Career</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=career-create" class="nav-link <?= $page === 'career-create' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-plus"></i>
                    <p>Create Career</p>
                  </a>
                </li>
              </ul>
            </li>
            
            <li class="nav-item has-treeview <?= in_array($page, ['leadership', 'leadership-browse', 'leadership-create']) ? 'menu-open' : '' ?>">
              <a href="#" class="nav-link <?= in_array($page, ['leadership', 'leadership-browse', 'leadership-create']) ? 'active' : '' ?>">
                <i class="nav-icon fas fa-users"></i>
                <p>
                  Leadership
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=leadership-browse" class="nav-link <?= $page === 'leadership-browse' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-eye"></i>
                    <p>Browse Leadership</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=leadership-create" class="nav-link <?= $page === 'leadership-create' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-plus"></i>
                    <p>Create Leadership</p>
                  </a>
                </li>
              </ul>
            </li>
            
            <li class="nav-item has-treeview <?= in_array($page, ['orgdev', 'orgdev-browse', 'orgdev-create']) ? 'menu-open' : '' ?>">
              <a href="#" class="nav-link <?= in_array($page, ['orgdev', 'orgdev-browse', 'orgdev-create']) ? 'active' : '' ?>">
                <i class="nav-icon fas fa-sitemap"></i>
                <p>
                  Organizational Development
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=orgdev-browse" class="nav-link <?= $page === 'orgdev-browse' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-eye"></i>
                    <p>Browse Activities</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=orgdev" class="nav-link <?= $page === 'orgdev' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-plus"></i>
                    <p>Create Activity</p>
                  </a>
                </li>
              </ul>
            </li>
            
            <!-- UTILITY SECTION -->
            <li class="nav-header">UTILITIES</li>
            
            <li class="nav-item">
              <a href="../logout.php" class="nav-link">
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

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <?php
        switch ($page) {
            case 'dashboard':
            case '':
                // Homepage showing user's programs and personalized recommendations
                define('NO_HEADER', true);
                define('NO_FOOTER', true);
                include __DIR__ . '/modules/dashboard.php';
                break;
            case 'analytics':
                // Admin analytics dashboard - restricted to admin/learning role
                if (!in_array($role, ['admin', 'learning'])) {
                    header('Location: ?page=dashboard');
                    exit;
                }
                define('NO_HEADER', true);
                define('NO_FOOTER', true);
                include __DIR__ . '/modules/analytics.php';
                break;
            case 'training':
            case 'training-browse':
                define('NO_HEADER', true);
                define('NO_FOOTER', true);
                include __DIR__ . '/modules/training_browse.php';
                break;
            case 'training-create':
                define('NO_HEADER', true);
                define('NO_FOOTER', true);
                include __DIR__ . '/modules/training.php';
                break;
            case 'career':
            case 'career-browse':
                define('NO_HEADER', true);
                define('NO_FOOTER', true);
                include __DIR__ . '/modules/career_browse.php';
                break;
            case 'career-create':
                define('NO_HEADER', true);
                define('NO_FOOTER', true);
                include __DIR__ . '/modules/career.php';
                break;
            case 'leadership':
            case 'leadership-browse':
                define('NO_HEADER', true);
                define('NO_FOOTER', true);
                include __DIR__ . '/modules/leadership_browse.php';
                break;
            case 'leadership-create':
                define('NO_HEADER', true);
                define('NO_FOOTER', true);
                include __DIR__ . '/modules/leadership.php';
                break;
            case 'orgdev-browse':
                define('NO_HEADER', true);
                define('NO_FOOTER', true);
                include __DIR__ . '/modules/orgdev_browse.php';
                break;
            case 'orgdev':
                define('NO_HEADER', true);
                define('NO_FOOTER', true);
                include __DIR__ . '/modules/orgdev.php';
                break;
            default:
                ?>
                <div class="container-fluid">
                  <h2>Unknown Module</h2>
                </div>
                <?php
                break;
        }
        ?>
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php include "../layout/global_modal.php"; ?>
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
  <script src="../assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../assets/dist/js/adminlte.js"></script>

  <!-- PAGE PLUGINS -->
  <!-- jQuery Mapael -->
  <script src="../assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
  <script src="../assets/plugins/raphael/raphael.min.js"></script>
  <script src="../assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
  <script src="../assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
  <!-- ChartJS -->
  <script src="../assets/plugins/chart.js/Chart.min.js"></script>

  <!-- AdminLTE for demo purposes -->
  <!-- <script src="assets/dist/js/demo.js"></script> -->
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <!-- <script src="assets/dist/js/pages/dashboard2.js"></script> -->
  <script src="../assets/dist/js/theme.js"></script>
  <script src="../assets/dist/js/time.js"></script>
  <script src="../assets/dist/js/global_modal.js"></script>
  <script src="../assets/dist/js/profile.js"></script>


  <script>
  // Hide preloader when DOM is ready
  document.addEventListener('DOMContentLoaded', function(){
    const preloader = document.querySelector('.preloader');
    if (preloader) {
      preloader.style.display = 'none';
    }
  });
  </script>
</body>

</html>