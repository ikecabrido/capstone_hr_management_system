<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once "../../auth/auth_check.php";
header('Location: browse.php?section=programs');
exit;

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Browse Training Programs - Learning and Development</title>

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
  <link rel="stylesheet" href="../custom.css" />
  <link rel="stylesheet" href="../../layout/toast.css" />
</head>

<body
  class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
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
          <a href="../learning_development.php" class="nav-link">Home</a>
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
      <a href="../learning_development.php" class="brand-link">

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
              <?= htmlspecialchars($_SESSION['user']['name']) ?>
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
              <a href="../learning_development.php" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="browse_training_programs.php" class="nav-link active">
                <i class="nav-icon fas fa-book"></i>
                <p>Browse</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="certification_management.php" class="nav-link">
                <i class="nav-icon fas fa-certificate"></i>
                <p>Certification</p>
              </a>
            </li>
            <?php if ($_SESSION['user']['role'] === 'learning'): ?>
            <li class="nav-item">
              <a href="manage_learning.php" class="nav-link">
                <i class="nav-icon fas fa-tasks"></i>
                <p>Learning Management</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="archive.php" class="nav-link">
                <i class="nav-icon fas fa-archive"></i>
                <p>Archive</p>
              </a>
            </li>
            <?php endif; ?>
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

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-12">
              <h1 class="m-0">Browse Training Programs</h1>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <?php if (empty($programs)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> No Training Programs Available</h5>
                        <p>There are currently no training programs available for enrollment.</p>
                        <?php if ($_SESSION['user']['role'] === 'learning'): ?>
                            <a href="create_training_program.php" class="btn btn-primary">View My Programs</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php
                // Array of local placeholder GIFs
                $placeholderImages = [
                    '../img/cover-placeholder/cover-placeholder-1.gif',
                    '../img/cover-placeholder/cover-placeholder-2.gif',
                    '../img/cover-placeholder/cover-placeholder-3.gif',
                    '../img/cover-placeholder/cover-placeholder-4.gif',
                    '../img/cover-placeholder/cover-placeholder-5.gif',
                    '../img/cover-placeholder/cover-placeholder-6.gif',
                    '../img/cover-placeholder/cover-placeholder-7.gif',
                    '../img/cover-placeholder/cover-placeholder-8.gif',
                    '../img/cover-placeholder/cover-placeholder-9.gif',
                    '../img/cover-placeholder/cover-placeholder-10.gif',
                    '../img/cover-placeholder/cover-placeholder-11.gif',
                    '../img/cover-placeholder/cover-placeholder-12.gif',
                    '../img/cover-placeholder/cover-placeholder-13.gif',
                    '../img/cover-placeholder/cover-placeholder-14.gif',
                    '../img/cover-placeholder/cover-placeholder-15.gif',
                    '../img/cover-placeholder/cover-placeholder-16.gif',
                    '../img/cover-placeholder/cover-placeholder-17.gif',
                    '../img/cover-placeholder/cover-placeholder-18.gif',
                    '../img/cover-placeholder/cover-placeholder-19.gif',
                    '../img/cover-placeholder/cover-placeholder-20.gif'
                ];
                ?>
                <?php foreach ($programs as $program): ?>
                    <?php
                    // Use random placeholder for cover
                    $coverUrl = $placeholderImages[array_rand($placeholderImages)];
                    $statusColor = $program['status'] == 'active' ? 'success' : ($program['status'] == 'completed' ? 'info' : 'secondary');
                    $statusClass = getStatusClass($program['status']);
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card certification-card">
                            <!-- Status Badge -->
                            <div class="status-badge">
                                <span class="badge-custom <?php echo $statusClass; ?>">
                                    <?php echo ucfirst($program['status']); ?>
                                </span>
                            </div>

                            <!-- Cover Photo -->
                            <div class="card-img-top" style="height: 200px; overflow: hidden;">
                                <img src="<?php echo htmlspecialchars($coverUrl); ?>" alt="Program Cover" class="w-100 h-100" style="object-fit: cover;">
                            </div>

                            <!-- Card Body with Information -->
                            <div class="card-body card-info">
                                <div class="card-content">
                                    <h5 class="card-title"><?php echo htmlspecialchars($program['title']); ?></h5>

                                    <!-- Program Description -->
                                    <div class="program-description mb-3">
                                        <p class="description-text">Comprehensive training program designed to develop advanced skills and professional competencies.</p>
                                    </div>

                                    <!-- Program Info Grid -->
                                    <div class="info-grid">
                                        <div class="info-row">
                                            <div class="info-item">
                                                <i class="fas fa-chalkboard-teacher text-primary"></i>
                                                <span class="info-label">Trainer</span>
                                                <span class="info-value"><?php echo htmlspecialchars($program['trainer']); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-users text-info"></i>
                                                <span class="info-label">Max Participants</span>
                                                <span class="info-value"><?php echo $program['max_participants']; ?></span>
                                            </div>
                                        </div>

                                        <div class="info-row">
                                            <div class="info-item full-width">
                                                <i class="fas fa-calendar-alt text-success"></i>
                                                <span class="info-label">Duration</span>
                                                <span class="info-value"><?php echo date('M d, Y', strtotime($program['start_date'])) . ' - ' . date('M d, Y', strtotime($program['end_date'])); ?></span>
                                            </div>
                                        </div>

                                        <div class="info-row">
                                            <div class="info-item">
                                                <i class="fas fa-user-edit text-secondary"></i>
                                                <span class="info-label">Created by</span>
                                                <span class="info-value"><?php echo htmlspecialchars($program['creator_name'] ?? 'Admin'); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-calendar-plus text-muted"></i>
                                                <span class="info-label">Created</span>
                                                <span class="info-value"><?php echo date('M d, Y', strtotime($program['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-actions mt-auto">
                                    <?php if ($program['status'] === 'active'): ?>
                                        <button class="btn btn-primary btn-sm">
                                            <i class="fas fa-user-plus"></i> Enroll Now
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="fas fa-eye"></i> View Details
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($_SESSION['user']['role'] === 'learning'): ?>
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
        <!--/. container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php include "../../layout/global_modal.php"; ?>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

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
  <!-- <script src="assets/dist/js/demo.js"></script> -->
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <!-- <script src="assets/dist/js/pages/dashboard2.js"></script> -->
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script src="../../assets/dist/js/profile.js"></script>

</body>

</html>