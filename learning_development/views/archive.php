<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/ArchiveController.php";

// Check if user has learning/admin role
if (!in_array($_SESSION['user']['role'], ['learning', 'admin'])) {
    header("Location: ../learning_development.php");
    exit;
}

$archiveController = new ArchiveController();
$archives = $archiveController->index();
$theme = $_SESSION['user']['theme'] ?? 'light';

function getStatusBadgeClass($type) {
    $classes = [
        'course' => 'badge-primary',
        'program' => 'badge-info',
        'certification' => 'badge-success'
    ];
    return $classes[$type] ?? 'badge-secondary';
}

function getStatusBadgeText($type) {
    $texts = [
        'course' => 'Course',
        'program' => 'Program',
        'certification' => 'Certification'
    ];
    return $texts[$type] ?? 'Item';
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Archive - Learning and Development</title>

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
              <a href="browse_courses.php" class="nav-link">
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
              <a href="archive.php" class="nav-link active">
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
            <div class="col-sm-6">
              <h1 class="m-0">Archives</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
              <div class="float-sm-right">
                <a href="../learning_development.php" class="btn btn-secondary">
                  <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <hr style="border-top: 2px solid #dee2e6; margin: 10px 0;">
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <?php if (empty($archives)): ?>
            <div class="text-center py-5">
              <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
              <h3 class="text-muted">No Archives Found</h3>
              <p class="text-muted">You haven't archived any courses or programs yet.</p>
            </div>
          <?php else: ?>
            <!-- Archives Table -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Archived Items</h3>
              </div>
              <div class="card-body table-responsive">
                <table class="table table-hover table-striped">
                  <thead>
                    <tr>
                      <th style="width: 8%;">Type</th>
                      <th style="width: 20%;">Title</th>
                      <th style="width: 18%;">Details</th>
                      <th style="width: 18%;">Description</th>
                      <th style="width: 12%;">Archived On</th>
                      <th style="width: 12%;">Archived By</th>
                      <th style="width: 12%;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($archives as $archive): 
                      $itemData = json_decode($archive['archive_data'], true);
                    ?>
                      <tr>
                        <td>
                          <span class="badge <?php echo getStatusBadgeClass($archive['archive_type']); ?>">
                            <?php echo getStatusBadgeText($archive['archive_type']); ?>
                          </span>
                        </td>
                        <td>
                          <strong><?php echo htmlspecialchars($archive['title']); ?></strong>
                        </td>
                        <td>
                          <small>
                            <?php if ($archive['archive_type'] === 'course'): ?>
                              <strong>Instructor:</strong> <?php echo htmlspecialchars($itemData['instructor'] ?? 'N/A'); ?><br>
                              <strong>Duration:</strong> <?php echo htmlspecialchars($itemData['duration_hours'] ?? 'N/A'); ?> hours<br>
                              <strong>Type:</strong> <?php echo htmlspecialchars($itemData['content_type'] ?? 'N/A'); ?>
                            <?php elseif ($archive['archive_type'] === 'program'): ?>
                              <strong>Trainer:</strong> <?php echo htmlspecialchars($itemData['trainer'] ?? 'N/A'); ?><br>
                              <strong>Start:</strong> <?php echo date('M d, Y', strtotime($itemData['start_date'] ?? 'now')); ?><br>
                              <strong>End:</strong> <?php echo date('M d, Y', strtotime($itemData['end_date'] ?? 'now')); ?>
                            <?php else: ?>
                              <strong>Employee:</strong> <?php echo htmlspecialchars($itemData['employee_name'] ?? 'N/A'); ?><br>
                              <strong>Course:</strong> <?php echo htmlspecialchars($itemData['course_title'] ?? 'N/A'); ?><br>
                              <strong>Issued:</strong> <?php echo date('M d, Y', strtotime($itemData['issued_date'] ?? 'now')); ?>
                            <?php endif; ?>
                          </small>
                        </td>
                        <td>
                          <small><?php echo htmlspecialchars(substr($archive['description'], 0, 50)) . (strlen($archive['description']) > 50 ? '...' : ''); ?></small>
                        </td>
                        <td>
                          <small><?php echo date('M d, Y H:i', strtotime($archive['archived_at'])); ?></small>
                        </td>
                        <td>
                          <small><?php echo htmlspecialchars($archive['archived_by_name'] ?? 'Unknown'); ?></small>
                        </td>
                        <td>
                          <button class="btn btn-sm btn-success" onclick="restoreArchive(<?php echo $archive['id']; ?>, '<?php echo $archive['archive_type']; ?>')">
                            <i class="fas fa-undo"></i> Restore
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <!--/. container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php include "../../layout/global_modal.php"; ?>

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
  <!-- Theme, Time, and Global Modal Scripts -->
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script src="../../assets/dist/js/profile.js"></script>

  <script>
    function restoreArchive(archiveId, archiveType) {
      if (confirm('Are you sure you want to restore this archived ' + archiveType + '?')) {
        console.log('Restore request:', {archive_id: archiveId, archive_type: archiveType});
        fetch('restore_archive.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'archive_id=' + encodeURIComponent(archiveId) + '&archive_type=' + encodeURIComponent(archiveType)
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('HTTP ' + response.status + ': ' + response.statusText);
          }
          return response.text().then(text => {
            try {
              return JSON.parse(text);
            } catch (e) {
              throw new Error('Invalid JSON response: ' + text.substring(0, 200));
            }
          });
        })
        .then(data => {
          if (data.success) {
            alert('Archived ' + archiveType + ' restored successfully!');
            location.reload();
          } else {
            alert('Error restoring: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Archive error:', error);
          alert('Error restoring archived item: ' + error.message);
        });
      }
    }
  </script>

</body>

</html>
