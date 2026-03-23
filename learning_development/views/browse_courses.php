<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/CourseController.php";

$controller = new CourseController();
// Get all courses and filter out inactive/archived ones
$allCourses = $controller->index();
$courses = array_filter($allCourses, function($course) {
    return $course['status'] !== 'inactive';
});
$theme = $_SESSION['user']['theme'] ?? 'light';

function getStatusClass($status) {
    $statusClasses = [
        'active' => 'status-active',
        'inactive' => 'status-inactive',
        'archived' => 'status-archived',
        'pending' => 'status-pending'
    ];
    return $statusClasses[$status] ?? 'status-default';
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Browse Courses - Learning and Development</title>

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
              <a href="browse_training_programs.php" class="nav-link">
                <i class="nav-icon fas fa-book"></i>
                <p>Browse Programs</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="browse_courses.php" class="nav-link active">
                <i class="nav-icon fas fa-graduation-cap"></i>
                <p>Browse Courses</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="certification_management.php" class="nav-link">
                <i class="nav-icon fas fa-certificate"></i>
                <p>Certifications</p>
              </a>
            </li>
            <?php if ($_SESSION['user']['role'] === 'learning'): ?>
            <li class="nav-header">ADMIN FEATURES</li>
            <li class="nav-item">
              <a href="track_enrollments.php" class="nav-link">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Enrollments</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="create_training_program.php" class="nav-link">
                <i class="nav-icon fas fa-plus"></i>
                <p>Create Programs</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="create_course.php" class="nav-link">
                <i class="nav-icon fas fa-plus-circle"></i>
                <p>Create Course</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="archive.php" class="nav-link">
                <i class="nav-icon fas fa-archive"></i>
                <p>Archives</p>
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
              <h1 class="m-0">Browse Courses</h1>
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
            <?php if (empty($courses)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> No Courses Available</h5>
                        <p>There are currently no courses available for enrollment.</p>
                        <?php if ($_SESSION['user']['role'] === 'learning'): ?>
                            <a href="create_course.php" class="btn btn-primary">View My Courses</a>
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
                <?php foreach ($courses as $course): ?>
                    <?php
                    // Use random placeholder for cover
                    $coverUrl = $placeholderImages[array_rand($placeholderImages)];
                    $statusColor = $course['status'] == 'active' ? 'success' : 'secondary';
                    $statusClass = getStatusClass($course['status']);
                    $contentTypeIcon = $course['content_type'] == 'online' ? 'fas fa-laptop' :
                                     ($course['content_type'] == 'hybrid' ? 'fas fa-users-cog' : 'fas fa-users');
                    $contentTypeColor = $course['content_type'] == 'online' ? 'primary' :
                                      ($course['content_type'] == 'hybrid' ? 'warning' : 'info');
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card certification-card">
                            <!-- Status Badge -->
                            <div class="status-badge">
                                <span class="badge-custom <?php echo $statusClass; ?>">
                                    <?php echo ucfirst($course['status']); ?>
                                </span>
                            </div>

                            <!-- Cover Photo -->
                            <div class="card-img-top" style="height: 200px; overflow: hidden;">
                                <img src="<?php echo htmlspecialchars($coverUrl); ?>" alt="Course Cover" class="w-100 h-100" style="object-fit: cover;">
                            </div>

                            <!-- Card Body with Information -->
                            <div class="card-body card-info">
                                <div class="card-content">
                                    <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>

                                    <!-- Course Description -->
                                    <div class="course-description mb-3">
                                        <p class="description-text">Learn essential skills and knowledge in this comprehensive course designed to enhance your professional development.</p>
                                    </div>

                                    <!-- Course Info Grid -->
                                    <div class="info-grid">
                                        <div class="info-row">
                                            <div class="info-item">
                                                <i class="fas fa-user-tie text-primary"></i>
                                                <span class="info-label">Instructor</span>
                                                <span class="info-value"><?php echo htmlspecialchars($course['instructor']); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-clock text-success"></i>
                                                <span class="info-label">Duration</span>
                                                <span class="info-value"><?php echo $course['duration_hours']; ?> hours</span>
                                            </div>
                                        </div>

                                        <div class="info-row">
                                            <div class="info-item">
                                                <i class="fas fa-laptop text-info"></i>
                                                <span class="info-label">Type</span>
                                                <span class="info-value"><?php echo ucfirst($course['content_type']); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-user-edit text-secondary"></i>
                                                <span class="info-label">Created by</span>
                                                <span class="info-value"><?php echo htmlspecialchars($course['creator_name'] ?? 'Admin'); ?></span>
                                            </div>
                                        </div>

                                        <?php if ($course['program_title']): ?>
                                        <div class="info-row">
                                            <div class="info-item full-width">
                                                <i class="fas fa-folder-open text-warning"></i>
                                                <span class="info-label">Program</span>
                                                <span class="info-value"><?php echo htmlspecialchars($course['program_title']); ?></span>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card-actions mt-auto">
                                    <?php if ($course['status'] === 'active'): ?>
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