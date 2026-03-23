<?php
session_start();
require_once "../auth/auth_check.php";
$theme = $_SESSION['user']['theme'] ?? 'light';

// Redirect to create program page if modal=program parameter is present
if (isset($_GET['modal']) && $_GET['modal'] === 'program' && $_SESSION['user']['role'] === 'learning') {
    header("Location: views/create_training_program.php");
    exit;
}

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
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <li class="nav-item">
              <a href="#" class="nav-link active">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="views/browse_training_programs.php" class="nav-link">
                <i class="nav-icon fas fa-book"></i>
                <p>Browse Programs</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="views/browse_courses.php" class="nav-link">
                <i class="nav-icon fas fa-graduation-cap"></i>
                <p>Browse Courses</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="views/certification_management.php" class="nav-link">
                <i class="nav-icon fas fa-certificate"></i>
                <p>Certifications</p>
              </a>
            </li>
            <?php if ($_SESSION['user']['role'] === 'learning'): ?>
            <li class="nav-header">ADMIN FEATURES</li>
            <li class="nav-item">
              <a href="views/track_enrollments.php" class="nav-link">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Enrollments</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="views/create_training_program.php" class="nav-link">
                <i class="nav-icon fas fa-plus"></i>
                <p>Create Programs</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="views/create_course.php" class="nav-link">
                <i class="nav-icon fas fa-plus-circle"></i>
                <p>Create Course</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="views/archive.php" class="nav-link">
                <i class="nav-icon fas fa-archive"></i>
                <p>Archives</p>
              </a>
            </li>
            <?php endif; ?>
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
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Learning and Development Dashboard</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
              </ol>
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
          <?php
            require_once "controllers/TrainingProgramController.php";
            require_once "controllers/CourseController.php";
            require_once "controllers/EnrollmentController.php";
            require_once "controllers/CertificationController.php";

            $programController = new TrainingProgramController();
            $programs = $programController->index();
            $activePrograms = array_filter($programs, function($program) {
                return isset($program['status']) && $program['status'] !== 'inactive';
            });
            $inactivePrograms = array_filter($programs, function($program) {
                return isset($program['status']) && $program['status'] === 'inactive';
            });

            $courseController = new CourseController();
            $courses = $courseController->index();
            $activeCourses = array_filter($courses, function($course) {
                return isset($course['status']) && $course['status'] !== 'inactive';
            });
            $inactiveCourses = array_filter($courses, function($course) {
                return isset($course['status']) && $course['status'] === 'inactive';
            });

            $enrollmentController = new EnrollmentController();
            $enrollments = $_SESSION['user']['role'] === 'learning' ? $enrollmentController->index() : $enrollmentController->getByEmployee($_SESSION['user']['id']);
            $completedEnrollments = array_filter($enrollments, function($enroll) {
                return isset($enroll['status']) && $enroll['status'] === 'completed';
            });
            $ongoingEnrollments = array_filter($enrollments, function($enroll) {
                return isset($enroll['status']) && in_array($enroll['status'], ['active','ongoing'], true);
            });

            $certificationController = new CertificationController();
            $certifications = $_SESSION['user']['role'] === 'learning' ? $certificationController->index() : $certificationController->getByEmployee($_SESSION['user']['id']);
            $activeCertifications = array_filter($certifications, function($cert) {
                return isset($cert['status']) && in_array($cert['status'], ['active', 'issued']);
            });
            $revokedCertifications = array_filter($certifications, function($cert) {
                return isset($cert['status']) && $cert['status'] === 'revoked';
            });
            $expiredCertifications = array_filter($certifications, function($cert) {
                return isset($cert['status']) && $cert['status'] === 'expired';
            });

            // Prepare data for enrollment chart
            $enrollmentData = [];
            $courseData = [];
            $months = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = date('Y-m', strtotime("-$i months"));
                $months[] = date('M Y', strtotime("-$i months"));
                
                // Count enrollments for this month
                $enrollmentCount = count(array_filter($enrollments, function($enroll) use ($date) {
                    return isset($enroll['enrolled_at']) && substr($enroll['enrolled_at'], 0, 7) === $date;
                }));
                $enrollmentData[] = $enrollmentCount;
                
                // For courses, we'll use total active courses (since courses don't have creation dates in the data)
                $courseData[] = count($activeCourses);
            }
            $enrollmentDataJson = json_encode($enrollmentData);
            $courseDataJson = json_encode($courseData);
            $monthsJson = json_encode($months);

            // Calculate additional metrics
            $completionRate = count($enrollments) > 0 ? round((count($completedEnrollments) / count($enrollments)) * 100, 1) : 0;
            
            // Top courses by enrollment count
            $courseEnrollmentCount = [];
            foreach ($enrollments as $enroll) {
                $courseId = $enroll['ld_courses_id'] ?? null;
                if ($courseId) {
                    $courseEnrollmentCount[$courseId] = ($courseEnrollmentCount[$courseId] ?? 0) + 1;
                }
            }
            arsort($courseEnrollmentCount);
            $topCourses = array_slice($courseEnrollmentCount, 0, 5);
            
            // Get course titles for top courses
            $topCoursesData = [];
            foreach ($topCourses as $courseId => $count) {
                $course = array_values(array_filter($courses, function($c) use ($courseId) {
                    return $c['ld_courses_id'] == $courseId;
                }));
                if (!empty($course)) {
                    $topCoursesData[] = ['title' => substr($course[0]['title'] ?? 'Unknown', 0, 20), 'count' => $count];
                }
            }
            $topCoursesJson = json_encode($topCoursesData);
            $topCoursesLabels = json_encode(array_column($topCoursesData, 'title'));
            $topCoursesValues = json_encode(array_column($topCoursesData, 'count'));
            
            // Enrollment status distribution
            $statusData = [
                'completed' => count($completedEnrollments),
                'ongoing' => count($ongoingEnrollments),
                'pending' => count($enrollments) - count($completedEnrollments) - count($ongoingEnrollments)
            ];
            $statusLabels = json_encode(array_keys($statusData));
            $statusValues = json_encode(array_values($statusData));
          ?>

          <!-- Info boxes and Chart -->
          <div class="row">
            <!-- Left Column: Stacked Info Boxes -->
            <div class="col-md-5">
              <div class="row">
                <div class="col-12">
                  <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-book"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Training Programs</span>
                      <div class="info-box-grid">
                        <div class="grid-item">
                          <div class="grid-label">Active</div>
                          <div class="grid-value"><?= count($activePrograms) ?></div>
                        </div>
                        <div class="grid-item">
                          <div class="grid-label">Inactive</div>
                          <div class="grid-value"><?= count($inactivePrograms) ?></div>
                        </div>
                        <div class="grid-item">
                          <div class="grid-label">Total</div>
                          <div class="grid-value"><?= count($programs) ?></div>
                        </div>
                      </div>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
              </div>

              <div class="row">
                <div class="col-12">
                  <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-graduation-cap"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Available Courses</span>
                      <div class="info-box-grid">
                        <div class="grid-item">
                          <div class="grid-label">Active</div>
                          <div class="grid-value"><?= count($activeCourses) ?></div>
                        </div>
                        <div class="grid-item">
                          <div class="grid-label">Inactive</div>
                          <div class="grid-value"><?= count($inactiveCourses) ?></div>
                        </div>
                        <div class="grid-item">
                          <div class="grid-label">Total</div>
                          <div class="grid-value"><?= count($courses) ?></div>
                        </div>
                      </div>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
              </div>

              <div class="row">
                <div class="col-12">
                  <div class="info-box mb-3">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-certificate"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Certifications</span>
                      <div class="info-box-grid">
                        <div class="grid-item">
                          <div class="grid-label">Active</div>
                          <div class="grid-value"><?= count($activeCertifications) ?></div>
                        </div>
                        <div class="grid-item">
                          <div class="grid-label">Revoked</div>
                          <div class="grid-value"><?= count($revokedCertifications) ?></div>
                        </div>
                        <div class="grid-item">
                          <div class="grid-label">Expired</div>
                          <div class="grid-value"><?= count($expiredCertifications) ?></div>
                        </div>
                      </div>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
              </div>
            </div>

            <!-- Right Column: Enrollment Chart -->
            <div class="col-md-7">
              <div class="card chart-card">
                <div class="card-header">
                  <h5 class="card-title">Enrollment Trends</h5>
                </div>
                <div class="card-body">
                  <canvas id="enrollmentChart" style="max-height: 245px;"></canvas>
                  <div class="chart-data mt-3">
                    <div class="row">
                      <div class="col-sm-4">
                        <div class="small-box">
                          <div class="inner">
                            <h6>Total Enrollments</h6>
                            <p><?= array_sum($enrollmentData) ?></p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="small-box">
                          <div class="inner">
                            <h6>Average per Month</h6>
                            <p><?= round(array_sum($enrollmentData) / max(1, count($enrollmentData)), 1) ?></p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="small-box">
                          <div class="inner">
                            <h6>Most Recent</h6>
                            <p><?= end($enrollmentData) ?></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.row -->

          <!-- Key Metrics Row -->
          <div class="row mt-4">
            <div class="col-md-3">
              <div class="info-box mb-3" style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(100, 200, 120, 0.08)); border-color: rgba(76, 175, 80, 0.4);">
                <span class="info-box-icon" style="color: #2e7d32;"><i class="fas fa-percent"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Completion Rate</span>
                  <span class="info-box-number"><?= $completionRate ?>%</span>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="info-box mb-3" style="background: linear-gradient(135deg, rgba(255, 152, 0, 0.1), rgba(255, 180, 0, 0.08)); border-color: rgba(255, 152, 0, 0.4);">
                <span class="info-box-icon" style="color: #e65100;"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Enrollments</span>
                  <span class="info-box-number"><?= count($enrollments) ?></span>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="info-box mb-3" style="background: linear-gradient(135deg, rgba(244, 67, 54, 0.1), rgba(255, 100, 80, 0.08)); border-color: rgba(244, 67, 54, 0.4);">
                <span class="info-box-icon" style="color: #c62828;"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Expired Certs</span>
                  <span class="info-box-number"><?= count($expiredCertifications) ?></span>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="info-box mb-3" style="background: linear-gradient(135deg, rgba(63, 81, 181, 0.1), rgba(100, 130, 255, 0.08)); border-color: rgba(63, 81, 181, 0.4);">
                <span class="info-box-icon" style="color: #283593;"><i class="fas fa-chart-bar"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Avg Completion</span>
                  <span class="info-box-number"><?= count($enrollments) > 0 ? round(count($completedEnrollments) / count($enrollments) * 100, 0) : 0 ?></span>
                </div>
              </div>
            </div>
          </div>
          <!-- /.row -->

          <!-- Charts Row: Top Courses & Enrollment Status -->
          <div class="row mt-2">
            <div class="col-md-6">
              <div class="card chart-card">
                <div class="card-header">
                  <h5 class="card-title">Top 5 Courses</h5>
                </div>
                <div class="card-body">
                  <canvas id="topCoursesChart" style="max-height: 250px;"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card chart-card">
                <div class="card-header">
                  <h5 class="card-title">Enrollment Status</h5>
                </div>
                <div class="card-body">
                  <canvas id="statusChart" style="max-height: 250px;"></canvas>
                </div>
              </div>
            </div>
          </div>
          <!-- /.row -->
        </div>
        <!--/. container-fluid -->
      </section>
      <!-- /.content -->
    </div>
x`    <!-- /.content-wrapper -->
    <?php include "../layout/global_modal.php"; ?>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

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
    // Handle form submissions with AJAX
    // Removed modal functionality from main dashboard

    // Enrollment Chart
    const ctx = document.getElementById('enrollmentChart').getContext('2d');
    const enrollmentChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= $monthsJson ?>,
        datasets: [{
          label: 'Enrollments',
          data: <?= $enrollmentDataJson ?>,
          borderColor: 'rgb(75, 192, 192)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          tension: 0.1
        }, {
          label: 'Available Courses',
          data: <?= $courseDataJson ?>,
          borderColor: 'rgb(255, 99, 132)',
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          tension: 0.1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Enrollment Trends Over Last 6 Months'
          }
        }
      }
    });

    // Top Courses Chart
    const ctxCourses = document.getElementById('topCoursesChart').getContext('2d');
    const topCoursesChart = new Chart(ctxCourses, {
      type: 'bar',
      data: {
        labels: <?= $topCoursesLabels ?>,
        datasets: [{
          label: 'Enrollments',
          data: <?= $topCoursesValues ?>,
          backgroundColor: [
            'rgba(63, 81, 181, 0.7)',
            'rgba(33, 150, 243, 0.7)',
            'rgba(76, 175, 80, 0.7)',
            'rgba(255, 152, 0, 0.7)',
            'rgba(244, 67, 54, 0.7)'
          ],
          borderColor: [
            'rgb(63, 81, 181)',
            'rgb(33, 150, 243)',
            'rgb(76, 175, 80)',
            'rgb(255, 152, 0)',
            'rgb(244, 67, 54)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        scales: {
          x: {
            beginAtZero: true
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });

    // Enrollment Status Chart
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctxStatus, {
      type: 'doughnut',
      data: {
        labels: ['Completed', 'Ongoing', 'Pending'],
        datasets: [{
          data: <?= $statusValues ?>,
          backgroundColor: [
            'rgba(76, 175, 80, 0.8)',
            'rgba(33, 150, 243, 0.8)',
            'rgba(255, 152, 0, 0.8)'
          ],
          borderColor: [
            'rgb(76, 175, 80)',
            'rgb(33, 150, 243)',
            'rgb(255, 152, 0)'
          ],
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  </script>

  <script>
    // Hide preloader when page is fully loaded
    $(document).ready(function() {
      $('.preloader').fadeOut('slow');
      $('body').removeClass('preloader-active');
    });

    // Fallback: hide preloader after 3 seconds
    setTimeout(function() {
      $('.preloader').fadeOut('slow');
      $('body').removeClass('preloader-active');
    }, 3000);
  </script>
</body>

</html>