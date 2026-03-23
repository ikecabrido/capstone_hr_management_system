<?php
session_start();
require_once "../../auth/auth_check.php";
// Check if user has permission to create training programs
if ($_SESSION['user']['role'] !== 'learning') {
    header("Location: ../learning_development.php");
    exit;
}

require_once "../controllers/TrainingProgramController.php";
$controller = new TrainingProgramController();

// Get programs and filter out inactive/archived ones
$allPrograms = $controller->getByCreator($_SESSION['user']['id']);
$myPrograms = array_filter($allPrograms, function($program) {
    return $program['status'] !== 'inactive';
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
  <title>Training Programs - Learning and Development</title>

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
              <a href="create_training_program.php" class="nav-link active">
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
            <div class="col-sm-6">
              <h1 class="m-0">Training Program</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
              <div class="float-sm-right">
                <button type="button" onclick="openCreateProgramModal()" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Create Training Program
                </button>
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

          <?php if (empty($myPrograms)): ?>
            <div class="text-center py-5">
              <i class="fas fa-book fa-4x text-muted mb-4"></i>
              <h3 class="text-muted">No Training Programs Found</h3>
              <p class="text-muted">You haven't created any training programs yet.</p>
              <a href="create_training_program.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Your First Program
              </a>
            </div>
          <?php else: ?>
            <!-- Programs Grid -->
            <div class="row">
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
                  '../img/cover-placeholder/cover-placeholder-10.gif'
              ];

              // Pagination variables
              $itemsPerPage = 30; // 3 columns x 10 rows
              $totalItems = count($myPrograms);
              $totalPages = ceil($totalItems / $itemsPerPage);
              $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
              $currentPage = max(1, min($currentPage, $totalPages));

              // Get items for current page
              $startIndex = ($currentPage - 1) * $itemsPerPage;
              $pagePrograms = array_slice($myPrograms, $startIndex, $itemsPerPage);

              foreach ($pagePrograms as $program):
                  // Use random placeholder for cover
                  $coverUrl = $placeholderImages[array_rand($placeholderImages)];
                  $statusColor = $program['status'] == 'active' ? 'success' : 'secondary';
                  $statusClass = getStatusClass($program['status']);
              ?>
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="card h-100 training-card"
                    data-program-id="<?php echo $program['ld_training_programs_id']; ?>"
                    data-program-title="<?php echo htmlspecialchars($program['title'], ENT_QUOTES); ?>"
                    data-program-description="<?php echo htmlspecialchars($program['description'], ENT_QUOTES); ?>"
                    data-program-trainer="<?php echo htmlspecialchars($program['trainer'], ENT_QUOTES); ?>"
                    data-program-start_date="<?php echo htmlspecialchars($program['start_date'], ENT_QUOTES); ?>"
                    data-program-end_date="<?php echo htmlspecialchars($program['end_date'], ENT_QUOTES); ?>"
                    data-program-max_participants="<?php echo htmlspecialchars($program['max_participants'], ENT_QUOTES); ?>"
                    data-program-status="<?php echo htmlspecialchars($program['status'], ENT_QUOTES); ?>"
                  >
                    <!-- Status Badge -->
                    <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                      <span class="badge-custom <?php echo $statusClass; ?>">
                        <?php echo ucfirst($program['status']); ?>
                      </span>
                    </div>

                    <!-- Cover Photo -->
                    <div style="height: 180px; overflow: hidden;">
                      <img src="<?php echo htmlspecialchars($coverUrl); ?>" alt="Program Cover" class="card-img-top w-100 h-100" style="object-fit: cover;">
                    </div>

                    <!-- Card Body -->
                    <div class="card-body card-info">
                      <div class="card-content">
                        <h5 class="card-title"><?php echo htmlspecialchars($program['title']); ?></h5>
                        <hr class="title-separator">

                        <!-- Program Description -->
                        <div class="program-description mb-3">
                          <p class="description-text">Manage and edit your training program details and participants.</p>
                        </div>

                        <!-- Program Info Grid -->
                        <div class="info-grid">
                          <div class="info-row">
                            <div class="info-item">
                              <i class="fas fa-user-tie text-primary"></i>
                              <span class="info-label">Trainer</span>
                              <span class="info-value"><?php echo htmlspecialchars($program['trainer']); ?></span>
                            </div>
                            <div class="info-item">
                              <i class="fas fa-calendar text-success"></i>
                              <span class="info-label">Duration</span>
                              <span class="info-value"><?php echo date('M d, Y', strtotime($program['start_date'])); ?> - <?php echo date('M d, Y', strtotime($program['end_date'])); ?></span>
                            </div>
                          </div>

                          <div class="info-row">
                            <div class="info-item full-width">
                              <i class="fas fa-users text-info"></i>
                              <span class="info-label">Max participants</span>
                              <span class="info-value"><?php echo $program['max_participants']; ?></span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="card-actions mt-auto">
                        <button class="btn btn-primary btn-sm" onclick="editProgram(<?php echo $program['ld_training_programs_id']; ?>)">
                          <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="archiveProgram(<?php echo $program['ld_training_programs_id']; ?>)">
                          <i class="fas fa-archive"></i> Archive
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
              <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Programs pagination">
                  <ul class="pagination">
                    <!-- Previous Button -->
                    <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                      <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                      </a>
                    </li>

                    <!-- Page Numbers -->
                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);

                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                      <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>

                    <!-- Next Button -->
                    <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                      <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                      </a>
                    </li>
                  </ul>
                </nav>
              </div>
            <?php endif; ?>
          <?php endif; ?>
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

  <script>
    function editProgram(programId) {
      var card = document.querySelector('.training-card[data-program-id="' + programId + '"]');
      if (!card) {
        window.location.href = 'create_training_program.php?id=' + programId;
        return;
      }

      document.getElementById('createProgramModalLabel').textContent = 'Edit Training Program';
      var form = document.getElementById('createProgramForm');
      form.action = 'process_edit_program.php';
      document.getElementById('program_id').value = programId;
      document.getElementById('program_title').value = card.dataset.programTitle || '';
      document.getElementById('program_description').value = card.dataset.programDescription || '';
      document.getElementById('program_trainer').value = card.dataset.programTrainer || '';
      document.getElementById('program_start_date').value = card.dataset.programStart_date || '';
      document.getElementById('program_end_date').value = card.dataset.programEnd_date || '';
      document.getElementById('program_max_participants').value = card.dataset.programMax_participants || '';
      document.getElementById('program_status').value = card.dataset.programStatus || 'active';
      document.getElementById('createProgramSubmitButton').textContent = 'Update Program';
      $('#createProgramModal').modal('show');
    }

    function openCreateProgramModal() {
      var form = document.getElementById('createProgramForm');
      form.reset();
      form.action = 'process_create_program.php';
      document.getElementById('program_id').value = '';
      document.getElementById('createProgramModalLabel').textContent = 'Create New Training Program';
      document.getElementById('createProgramSubmitButton').textContent = 'Create Program';
      $('#createProgramModal').modal('show');
    }

    function archiveProgram(programId) {
      if (confirm('Are you sure you want to archive this training program?')) {
        // Send AJAX request to archive the program
        fetch('archive_program.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'program_id=' + programId
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
            alert('Program archived successfully!');
            location.reload();
          } else {
            alert('Error archiving program: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Archive error:', error);
          alert('Error archiving program: ' + error.message);
        });
      }
    }
    $('#createProgramForm').on('submit', function(e) {
      e.preventDefault();
      var formData = new FormData(this);

      $.ajax({
        url: 'process_create_program.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          $('#createProgramModal').modal('hide');
          // Show success message and refresh page
          alert('Training program created successfully!');
          location.reload();
        },
        error: function(xhr, status, error) {
          alert('Error creating program: ' + error);
        }
      });
    });
  </script>

  <!-- Create Program Modal -->
  <div class="modal fade" id="createProgramModal" tabindex="-1" role="dialog" aria-labelledby="createProgramModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createProgramModalLabel">Create New Training Program</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="createProgramForm" action="process_create_program.php" method="POST">
          <input type="hidden" id="program_id" name="program_id" value="">
          <div class="modal-body">
            <div class="form-group">
              <label for="program_title">Program Title</label>
              <input type="text" class="form-control" id="program_title" name="title" required>
            </div>
            <div class="form-group">
              <label for="program_description">Description</label>
              <textarea class="form-control" id="program_description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <label for="program_trainer">Trainer</label>
              <input type="text" class="form-control" id="program_trainer" name="trainer" required>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="program_start_date">Start Date</label>
                  <input type="date" class="form-control" id="program_start_date" name="start_date" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="program_end_date">End Date</label>
                  <input type="date" class="form-control" id="program_end_date" name="end_date" required>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="program_max_participants">Max Participants</label>
              <input type="number" class="form-control" id="program_max_participants" name="max_participants" required>
            </div>
            <div class="form-group">
              <label for="program_status">Status</label>
              <select class="form-control" id="program_status" name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" id="createProgramSubmitButton" class="btn btn-primary">Create Program</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>

</html>