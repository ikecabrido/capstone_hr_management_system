<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/EnrollmentController.php";
require_once "../controllers/TrainingProgramController.php";
require_once "../controllers/CourseController.php";

$controller = new EnrollmentController();
$programController = new TrainingProgramController();
$courseController = new CourseController();

class EnrollmentView {
    private $controller;
    private $user;

    public function __construct($controller, $user) {
        $this->controller = $controller;
        $this->user = $user;
    }

    public function fetchEnrollments() {
        if ($this->user['role'] === 'learning' || $this->user['role'] === 'admin') {
            return $this->controller->index();
        }
        return $this->controller->getByEmployee($this->user['id']);
    }

    private function getStatusColor($status) {
        if ($status === 'completed') {
            return 'success';
        }
        if ($status === 'in-progress') {
            return 'primary';
        }
        return 'secondary';
    }

    private function getStatusClass($status) {
        $statusClasses = [
            'completed' => 'status-completed',
            'in-progress' => 'status-in-progress',
            'enrolled' => 'status-enrolled',
            'active' => 'status-active',
            'inactive' => 'status-inactive',
            'pending' => 'status-pending',
            'archived' => 'status-archived'
        ];
        return $statusClasses[$status] ?? 'status-default';
    }

    private function getCoverUrl() {
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
        return $placeholderImages[array_rand($placeholderImages)];
    }

    public function renderCards($enrollments) {
        if (empty($enrollments)) {
            return '';
        }

        $content = '';
        foreach ($enrollments as $enrollment) {
            $statusColor = $this->getStatusColor($enrollment['status']);
            $statusClass = $this->getStatusClass($enrollment['status']);
            $progressPercent = is_numeric($enrollment['progress_percentage']) ? (int)$enrollment['progress_percentage'] : 0;
            $coverUrl = $this->getCoverUrl();
            $courseTitle = !empty($enrollment['course_title']) ? $enrollment['course_title'] : 'Untitled Course';

            $content .= '<div class="col-md-4 mb-4">';
            $content .= '<div class="card enrollment-card">';
            $content .= '<div class="status-badge"><span class="badge-custom '.$statusClass.'">'.htmlspecialchars(ucfirst(str_replace('-', ' ', $enrollment['status']))).'</span></div>';
            $content .= '<div class="card-img-top" style="height: 200px; overflow: hidden;"><img src="'.htmlspecialchars($coverUrl).'" alt="Course Cover" class="w-100 h-100" style="object-fit: cover;"></div>';
            $content .= '<div class="card-body card-info">';
            $content .= '<div class="card-content">';
            $content .= '<h5 class="card-title">'.htmlspecialchars($courseTitle).'</h5>';

            // Enrollment Description
            $content .= '<div class="enrollment-description mb-3">';
            $content .= '<p class="description-text">Track your learning progress and manage your course enrollments.</p>';
            $content .= '</div>';

            // Enrollment Info Grid
            $content .= '<div class="info-grid">';

            if ($this->user['role'] === 'learning' && isset($enrollment['employee_name'])) {
                $content .= '<div class="info-row">';
                $content .= '<div class="info-item">';
                $content .= '<i class="fas fa-user text-primary"></i>';
                $content .= '<span class="info-label">Employee</span>';
                $content .= '<span class="info-value">'.htmlspecialchars($enrollment['employee_name']).'</span>';
                $content .= '</div>';
                $content .= '<div class="info-item">';
                $content .= '<i class="fas fa-user-tie text-info"></i>';
                $content .= '<span class="info-label">Instructor</span>';
                $content .= '<span class="info-value">'.htmlspecialchars($enrollment['instructor']).'</span>';
                $content .= '</div>';
                $content .= '</div>';
            } else {
                $content .= '<div class="info-row">';
                $content .= '<div class="info-item full-width">';
                $content .= '<i class="fas fa-user-tie text-info"></i>';
                $content .= '<span class="info-label">Instructor</span>';
                $content .= '<span class="info-value">'.htmlspecialchars($enrollment['instructor']).'</span>';
                $content .= '</div>';
                $content .= '</div>';
            }

            $programTitle = !empty($enrollment['program_title']) ? $enrollment['program_title'] : 'N/A';

            $content .= '<div class="info-row">';
            $content .= '<div class="info-item full-width">';
            $content .= '<i class="fas fa-book-reader text-secondary"></i>';
            $content .= '<span class="info-label">Program</span>';
            $content .= '<span class="info-value">'.htmlspecialchars($programTitle).'</span>';
            $content .= '</div>';
            $content .= '</div>';

            $content .= '<div class="info-row">';
            $content .= '<div class="info-item">';
            $content .= '<i class="fas fa-calendar-check text-success"></i>';
            $content .= '<span class="info-label">Enrolled</span>';
            $content .= '<span class="info-value">'.htmlspecialchars(date('M d, Y', strtotime($enrollment['enrolled_at']))).'</span>';
            $content .= '</div>';
            $content .= '<div class="info-item">';
            $content .= '<i class="fas fa-chart-line text-warning"></i>';
            $content .= '<span class="info-label">Progress</span>';
            $content .= '<span class="info-value">'.htmlspecialchars($progressPercent).'%</span>';
            $content .= '</div>';
            $content .= '</div>';

            $content .= '</div>'; // ./info-grid

            $content .= '<div class="progress mb-1" style="height: 1.25rem;">';
            $content .= '<div class="progress-bar bg-'.$statusColor.'" role="progressbar" style="width: '.$progressPercent.'%; min-width: 45px;" aria-valuenow="'.$progressPercent.'" aria-valuemin="0" aria-valuemax="100">'.htmlspecialchars($progressPercent).'%</div>';
            $content .= '</div>'; // ./progress
            $content .= '<div class="text-right font-weight-bold small">'.htmlspecialchars($progressPercent).'% completed</div>';
            $content .= '</div>'; // ./card-content

            $content .= '<div class="card-actions mt-auto">';
            if ($this->user['role'] === 'learning') {
                $content .= '<button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Update Progress</button>';
                $content .= '<button class="btn btn-outline-danger btn-sm"><i class="fas fa-times"></i> Remove</button>';
            } else {
                if ($enrollment['status'] !== 'completed') {
                    $content .= '<button class="btn btn-primary btn-sm"><i class="fas fa-play"></i> Continue Learning</button>';
                } else {
                    $content .= '<button class="btn btn-success btn-sm"><i class="fas fa-check"></i> Completed</button>';
                }
                $content .= '<button class="btn btn-outline-info btn-sm"><i class="fas fa-eye"></i> View Details</button>';
            }
            $content .= '</div>'; // ./card-actions
            $content .= '</div>'; // ./card-body
            $content .= '</div>'; // ./card
            $content .= '</div>'; // ./col
        }

        return $content;
    }
}

$view = new EnrollmentView($controller, $_SESSION['user']);
$theme = $_SESSION['user']['theme'] ?? 'light';

// Fetch training programs and courses for the sidebar
$allPrograms = $programController->index();
$programs = array_filter($allPrograms, function($program) {
    return $program['status'] !== 'inactive';
});

$allCourses = $courseController->index();
$courses = array_filter($allCourses, function($course) {
    return $course['status'] !== 'inactive';
});

// Helper function to get status class
function getStatusClass($status) {
    $statusClasses = [
        'active' => 'status-active',
        'inactive' => 'status-inactive',
        'archived' => 'status-archived',
        'pending' => 'status-pending'
    ];
    return $statusClasses[$status] ?? 'status-default';
}

$isAdmin = $_SESSION['user']['role'] === 'learning' || $_SESSION['user']['role'] === 'admin';
$programId = $_GET['program_id'] ?? null;

if ($isAdmin) {
    if ($programId) {
        $programEnrollments = $controller->getEnrollmentsByProgram($programId);
        $programDetails = $controller->getProgramsWithEnrollmentDetails();
        $selectedProgram = array_filter($programDetails, function($g) use ($programId) { return $g['ld_training_programs_id'] == $programId; });
        $selectedProgram = !empty($selectedProgram) ? reset($selectedProgram) : null;
    } else {
        $programs = $controller->getProgramsWithEnrollmentDetails();
        $programEnrollments = [];
        $selectedProgram = null;
    }
} else {
    $enrollments = $view->fetchEnrollments();
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Track Enrollments - Learning and Development</title>

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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" />
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
              <a href="track_enrollments.php" class="nav-link active">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Enrollments</p>
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
              <h1 class="m-0"><?php echo $_SESSION['user']['role'] === 'learning' ? 'Manage Enrollments' : 'My Enrollments'; ?></h1>
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
          <!-- Training Programs Section -->
          <div class="row mb-4">
            <div class="col-12">
              <h3 class="mb-3">
                <i class="fas fa-book"></i> Training Programs
              </h3>
            </div>
          </div>

          <div class="row mb-5">
            <?php if (empty($programs)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No training programs available.
                    </div>
                </div>
            <?php else: ?>
                <?php
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
                    $coverUrl = $placeholderImages[array_rand($placeholderImages)];
                    $status = isset($program['status']) && !empty($program['status']) ? $program['status'] : 'active';
                    $statusClass = getStatusClass($status);
                    $title = isset($program['title']) ? $program['title'] : (isset($program['program_title']) ? $program['program_title'] : 'Untitled Program');
                    $trainer = isset($program['trainer']) ? htmlspecialchars($program['trainer']) : 'N/A';
                    $maxParticipants = isset($program['max_participants']) ? $program['max_participants'] : 'N/A';
                    $startDate = isset($program['start_date']) ? $program['start_date'] : null;
                    $endDate = isset($program['end_date']) ? $program['end_date'] : null;
                    $duration = ($startDate && $endDate) ? date('M d, Y', strtotime($startDate)) . ' - ' . date('M d, Y', strtotime($endDate)) : 'N/A';
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card certification-card">
                            <!-- Status Badge -->
                            <div class="status-badge">
                                <span class="badge-custom <?php echo $statusClass; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </div>

                            <!-- Cover Photo -->
                            <div class="card-img-top" style="height: 200px; overflow: hidden;">
                                <img src="<?php echo htmlspecialchars($coverUrl); ?>" alt="Program Cover" class="w-100 h-100" style="object-fit: cover;">
                            </div>

                            <!-- Card Body with Information -->
                            <div class="card-body card-info">
                                <div class="card-content">
                                    <h5 class="card-title"><?php echo htmlspecialchars($title); ?></h5>

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
                                                <span class="info-value"><?php echo $trainer; ?></span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-users text-info"></i>
                                                <span class="info-label">Participants</span>
                                                <span class="info-value"><?php echo $maxParticipants; ?></span>
                                            </div>
                                        </div>

                                        <div class="info-row">
                                            <div class="info-item full-width">
                                                <i class="fas fa-calendar-alt text-success"></i>
                                                <span class="info-label">Duration</span>
                                                <span class="info-value"><?php echo $duration; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-actions mt-auto">
                                    <?php if (strtolower($status) === 'active'): ?>
                                        <button class="btn btn-primary btn-sm">
                                            <i class="fas fa-user-plus"></i> Enroll Now
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="fas fa-eye"></i> View Details
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Courses Section -->
          <div class="row mb-4">
            <div class="col-12">
              <h3 class="mb-3">
                <i class="fas fa-graduation-cap"></i> Courses
              </h3>
            </div>
          </div>

          <div class="row mb-5">
            <?php if (empty($courses)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No courses available.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <?php
                    $coverUrl = $placeholderImages[array_rand($placeholderImages)];
                    $statusClass = getStatusClass($course['status']);
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
                                                <i class="fas fa-book-open text-warning"></i>
                                                <span class="info-label">Program</span>
                                                <span class="info-value"><?php echo htmlspecialchars($course['program_title'] ?? 'N/A'); ?></span>
                                            </div>
                                        </div>
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
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Enrollments Section (if not admin) -->
          <?php if (!$isAdmin || ($isAdmin && !empty($enrollments))): ?>
          <div class="row mb-4">
            <div class="col-12">
              <h3 class="mb-3">
                <i class="fas fa-list"></i> <?php echo $_SESSION['user']['role'] === 'learning' ? 'Manage Enrollments' : 'My Enrollments'; ?>
              </h3>
            </div>
          </div>

          <div class="row">
            <?php if (!$isAdmin): ?>
                <?php if (empty($enrollments)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> No Enrollments Found</h5>
                            <p>You haven't enrolled in any courses yet.</p>
                            <a href="browse_courses.php" class="btn btn-primary">Browse Available Courses</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php echo $view->renderCards($enrollments); ?>
                <?php endif; ?>
            <?php endif; ?>
          </div>
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

</body>

</html>