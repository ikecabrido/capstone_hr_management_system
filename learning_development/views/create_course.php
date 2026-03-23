<?php
session_start();
require_once "../../auth/auth_check.php";
// Check permissions
if ($_SESSION['user']['role'] !== 'learning') {
    header("Location: ../learning_development.php");
    exit;
}
require_once "../controllers/TrainingProgramController.php";
require_once "../controllers/CourseController.php";
$programController = new TrainingProgramController();
$courseController = new CourseController();

$programs = $programController->index();
// Get courses and filter out inactive/archived ones
$allCourses = $courseController->getByCreator($_SESSION['user']['id']);
$myCourses = array_filter($allCourses, function($course) {
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
  <title>Courses - Learning and Development</title>

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
              <a href="create_training_program.php" class="nav-link">
                <i class="nav-icon fas fa-plus"></i>
                <p>Create Programs</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="create_course.php" class="nav-link active">
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
              <h1 class="m-0">Courses</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
              <div class="float-sm-right">
                <button type="button" onclick="openCreateCourseModal()" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Create Course
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

          <?php if (empty($myCourses)): ?>
            <div class="text-center py-5">
              <i class="fas fa-graduation-cap fa-4x text-muted mb-4"></i>
              <h3 class="text-muted">No Courses Found</h3>
              <p class="text-muted">You haven't created any courses yet.</p>
              <a href="create_course.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Your First Course
              </a>
            </div>
          <?php else: ?>
            <!-- Courses Grid -->
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
              $totalItems = count($myCourses);
              $totalPages = ceil($totalItems / $itemsPerPage);
              $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
              $currentPage = max(1, min($currentPage, $totalPages));

              // Get items for current page
              $startIndex = ($currentPage - 1) * $itemsPerPage;
              $pageCourses = array_slice($myCourses, $startIndex, $itemsPerPage);

              foreach ($pageCourses as $course):
                  // Use random placeholder for cover
                  $coverUrl = $placeholderImages[array_rand($placeholderImages)];
                  $statusColor = $course['status'] == 'active' ? 'success' : 'secondary';
                  $statusClass = getStatusClass($course['status']);
                  $contentTypeIcon = $course['content_type'] == 'online' ? 'fas fa-laptop' :
                                   ($course['content_type'] == 'hybrid' ? 'fas fa-users-cog' : 'fas fa-users');
                  $contentTypeColor = $course['content_type'] == 'online' ? 'primary' :
                                    ($course['content_type'] == 'hybrid' ? 'warning' : 'info');
              ?>
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="card course-card h-100"
                    data-course-id="<?php echo $course['ld_courses_id']; ?>"
                    data-course-title="<?php echo htmlspecialchars($course['title'], ENT_QUOTES); ?>"
                    data-course-description="<?php echo htmlspecialchars($course['description'], ENT_QUOTES); ?>"
                    data-course-instructor="<?php echo htmlspecialchars($course['instructor'], ENT_QUOTES); ?>"
                    data-course-duration_hours="<?php echo htmlspecialchars($course['duration_hours'], ENT_QUOTES); ?>"
                    data-course-training_program_id="<?php echo htmlspecialchars($course['ld_training_programs_id'], ENT_QUOTES); ?>"
                    data-course-content_type="<?php echo htmlspecialchars($course['content_type'], ENT_QUOTES); ?>"
                    data-course-status="<?php echo htmlspecialchars($course['status'], ENT_QUOTES); ?>"
                  >
                    <!-- Status Badge -->
                    <div class="status-badge">
                      <span class="badge-custom <?php echo $statusClass; ?>">
                        <?php echo ucfirst($course['status']); ?>
                      </span>
                    </div>

                    <!-- Cover Photo -->
                    <div class="card-img-top" style="height: 180px; overflow: hidden;">
                      <img src="<?php echo htmlspecialchars($coverUrl); ?>" alt="Course Cover" class="w-100 h-100" style="object-fit: cover;">
                    </div>

                    <!-- Card Body -->
                    <div class="card-body card-info">
                      <div class="card-content">
                        <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                        <hr class="title-separator">

                        <!-- Course Description -->
                        <div class="course-description mb-3">
                          <p class="description-text">Manage and edit your course details and content.</p>
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
                              <i class="<?php echo $contentTypeIcon; ?> text-<?php echo $contentTypeColor; ?>"></i>
                              <span class="info-label">Type</span>
                              <span class="info-value"><?php echo ucfirst($course['content_type']); ?></span>
                            </div>
                            <?php if (isset($course['program_title']) && $course['program_title']): ?>
                              <div class="info-item">
                                <i class="fas fa-graduation-cap text-warning"></i>
                                <span class="info-label">Program</span>
                                <span class="info-value"><?php echo htmlspecialchars($course['program_title']); ?></span>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>

                      <div class="card-actions mt-auto">
                        <button class="btn btn-primary btn-sm" onclick="editCourse(<?php echo $course['ld_courses_id']; ?>)">
                          <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="archiveCourse(<?php echo $course['ld_courses_id']; ?>)">
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
                <nav aria-label="Courses pagination">
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
    function editCourse(courseId) {
      var card = document.querySelector('.course-card[data-course-id="' + courseId + '"]');
      if (!card) {
        alert('Course data not found');
        return;
      }

      document.getElementById('createCourseModalLabel').textContent = 'Edit Course';
      var form = document.getElementById('createCourseForm');
      form.action = 'process_edit_course.php';
      document.getElementById('course_id').value = courseId;
      document.getElementById('course_title').value = card.dataset.courseTitle || '';
      document.getElementById('course_description').value = card.dataset.courseDescription || '';
      document.getElementById('course_instructor').value = card.dataset.courseInstructor || '';
      document.getElementById('course_duration_hours').value = card.dataset.courseDuration_hours || '';
      document.getElementById('course_training_program_id').value = card.dataset.courseTraining_program_id || '';
      document.getElementById('course_content_type').value = card.dataset.courseContent_type || 'in-person';
      document.getElementById('course_status').value = card.dataset.courseStatus || 'active';
      document.getElementById('createCourseSubmitButton').textContent = 'Update Course';
      $('#createCourseModal').modal('show');
    }

    function archiveCourse(courseId) {
      if (confirm('Are you sure you want to archive this course?')) {
        // Send AJAX request to archive the course
        fetch('archive_course.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'course_id=' + courseId
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
            alert('Course archived successfully!');
            location.reload();
          } else {
            alert('Error archiving course: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Archive error:', error);
          alert('Error archiving course: ' + error.message);
        });
      }
    }

    function openCreateCourseModal() {
      var form = document.getElementById('createCourseForm');
      form.reset();
      form.action = 'process_create_course.php';
      document.getElementById('course_id').value = '';
      document.getElementById('createCourseModalLabel').textContent = 'Create New Course';
      document.getElementById('createCourseSubmitButton').textContent = 'Create Course';
      $('#createCourseModal').modal('show');
    }

    // Handle form submissions with AJAX
    $('#createCourseForm').on('submit', function(e) {
      e.preventDefault();
      var formData = new FormData(this);

      $.ajax({
        url: 'process_create_course.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          $('#createCourseModal').modal('hide');
          // Show success message and refresh page
          alert('Course created successfully!');
          location.reload();
        },
        error: function(xhr, status, error) {
          alert('Error creating course: ' + error);
        }
      });
    });
  </script>

  <!-- Create/Edit Course Modal -->
  <div class="modal fade" id="createCourseModal" tabindex="-1" role="dialog" aria-labelledby="createCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createCourseModalLabel">Create New Course</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="createCourseForm" action="process_create_course.php" method="POST">
          <input type="hidden" id="course_id" name="course_id" value="">
          <div class="modal-body">
            <div class="form-group">
              <label for="course_title">Course Title</label>
              <input type="text" class="form-control" id="course_title" name="title" required>
            </div>
            <div class="form-group">
              <label for="course_description">Description</label>
              <textarea class="form-control" id="course_description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <label for="course_instructor">Instructor</label>
              <input type="text" class="form-control" id="course_instructor" name="instructor" required>
            </div>
            <div class="form-group">
              <label for="course_duration_hours">Duration (hours)</label>
              <input type="number" class="form-control" id="course_duration_hours" name="duration_hours" required>
            </div>
            <div class="form-group">
              <label for="course_training_program_id">Training Program</label>
              <select class="form-control" id="course_training_program_id" name="training_program_id" required>
                <option value="">Select Program</option>
                <?php foreach ($programs as $program): ?>
                  <option value="<?php echo $program['ld_training_programs_id']; ?>"><?php echo htmlspecialchars($program['title']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="course_content_type">Content Type</label>
              <select class="form-control" id="course_content_type" name="content_type">
                <option value="in-person">In-Person</option>
                <option value="online">Online</option>
                <option value="hybrid">Hybrid</option>
              </select>
            </div>
            <div class="form-group">
              <label for="course_status">Status</label>
              <select class="form-control" id="course_status" name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" id="createCourseSubmitButton" class="btn btn-primary">Create Course</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>

</html>