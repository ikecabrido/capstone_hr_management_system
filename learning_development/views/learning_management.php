<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/TrainingProgramController.php";
require_once "../controllers/CourseController.php";

// Role guard
if (!in_array($_SESSION['user']['role'], ['learning', 'admin'])) {
    header("Location: ../learning_development.php");
    exit;
}

$theme = $_SESSION['user']['theme'] ?? 'light';

$section = isset($_GET['section']) ? $_GET['section'] : 'programs';
if (!in_array($section, ['programs', 'courses'])) {
    $section = 'programs';
}

$programController = new TrainingProgramController();
$courseController = new CourseController();

$programs = $programController->index();
$courses = $courseController->index();

function getStatusClass($status) {
    $classes = [
        'active' => 'status-active',
        'inactive' => 'status-inactive',
        'archived' => 'status-archived',
        'pending' => 'status-pending',
    ];
    return $classes[strtolower($status)] ?? 'status-default';
}

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

function pickCoverUrl() {
    global $placeholderImages;
    return $placeholderImages[array_rand($placeholderImages)];
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Learning Management - Learning and Development</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="../custom.css" />
  <link rel="stylesheet" href="../../layout/toast.css" />
  <style>
    .card-actions .btn { min-width: 95px; }
    .learning-card .status-badge { position: absolute; top: 12px; right: 12px; z-index: 3; }
    .learning-card .status-badge .badge-custom { width: 14px; height: 14px; border-radius: 50%; display:inline-block; }
    .learning-card .btn-blocks .btn { margin-right: 8px; margin-bottom: 6px; }
    .learning-panel-heading { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; }
    .section-tab { border: 1px solid #007bff; border-radius: .25rem; background: #fff; width: 100%; display:flex; align-items:center; }
    .section-tab .tab-button { flex: 1; border:none; background:none; color:#007bff; font-weight:700; padding:10px; cursor:pointer; }
    .section-tab .tab-button.active { background:#007bff; color:#fff; }
    .section-tab .action-circle { width:32px; height:32px; border-radius:50%; border:2px solid #007bff; color:#007bff; display:flex; align-items:center; justify-content:center; text-decoration:none; font-size:18px; }
    .section-tab .action-circle:hover { background:#007bff; color:#fff; }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
        <li class="nav-item d-none d-sm-inline-block"><a href="../learning_development.php" class="nav-link">Home</a></li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item"><div class="nav-link" id="clock">--:--:--</div></li>
        <li class="nav-item"><a class="nav-link" data-widget="fullscreen" href="#" role="button"><i class="fas fa-expand-arrows-alt"></i></a></li>
        <li class="nav-item"><a class="nav-link" href="#" id="darkToggle" role="button" title="Toggle Dark Mode"><i class="fas fa-moon" id="themeIcon"></i></a></li>
      </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="../learning_development.php" class="brand-link">
        <img src="../../assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: .9">
        <span class="brand-text font-weight-light">BCP Bulacan</span>
      </a>
      <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
          <div class="info"><a href="#" onclick="openGlobalModal('Profile Settings','../../user_profile/profile_form.php')" class="d-block"><?= htmlspecialchars($_SESSION['user']['name']) ?></a></div>
        </div>
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"><a href="../learning_development.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
            <li class="nav-item">
              <a href="browse.php" class="nav-link">
                <i class="nav-icon fas fa-book"></i>
                <p>Browse</p>
              </a>
            </li>
            <li class="nav-item"><a href="certification_management.php" class="nav-link"><i class="nav-icon fas fa-certificate"></i><p>Certification</p></a></li>
            <?php if ($_SESSION['user']['role'] === 'learning'): ?>
            <li class="nav-item"><a href="learning_management.php" class="nav-link active"><i class="nav-icon fas fa-tasks"></i><p>Learning Management</p></a></li>
            <li class="nav-item"><a href="archive.php" class="nav-link"><i class="nav-icon fas fa-archive"></i><p>Archive</p></a></li>
            <?php endif; ?>
            <li class="nav-item"><a href="../../logout.php" class="nav-link"><i class="nav-icon fas fa-sign-out-alt"></i><p>Logout</p></a></li>
          </ul>
        </nav>
      </div>
    </aside>

    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <h1 class="m-0">Learning Management</h1>
          <hr style="margin: 10px 0;"/>
        </div>
      </div>

      <section class="content">
        <div class="container-fluid">
          <div class="learning-panel-heading mb-3">
            <div class="section-tab">
              <a href="learning_management.php?section=programs" class="tab-button <?= $section === 'programs' ? 'active' : '' ?>">Training Programs</a>
              <a href="learning_management.php?section=courses" class="tab-button <?= $section === 'courses' ? 'active' : '' ?>">Courses</a>
            </div>

          </div>

          <div class="row mb-3">
            <div class="col-12">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <input id="learningSearch" type="text" class="form-control" placeholder="Search training programs or courses..." aria-label="Search">
              </div>
            </div>
          </div>

          <div class="row" id="learningCards">
            <?php if ($section === 'programs'): ?>
              <?php if (empty($programs)): ?>
                <div class="col-12"><div class="alert alert-warning">No training programs available yet.</div></div>
              <?php endif; ?>
              <?php foreach ($programs as $program): ?>
                <?php
                  $coverUrl = pickCoverUrl();
                  $status = $program['status'] ?? 'active';
                  $statusClass = getStatusClass($status);
                ?>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4 learning-item" data-title="<?= htmlspecialchars(strtolower($program['title'])) ?>">
                  <div class="card learning-card">
                    <div class="status-badge"><span class="badge-custom <?= $statusClass ?>" title="<?= htmlspecialchars(ucfirst($status)) ?>"></span></div>
                    <div style="height:180px; overflow:hidden;"><img src="<?= htmlspecialchars($coverUrl) ?>" class="w-100 h-100" style="object-fit:cover;" alt="Program cover"></div>
                    <div class="card-body">
                      <h4 class="card-title"><?= htmlspecialchars($program['title']) ?></h4>
                      <p><?= htmlspecialchars(substr($program['description'],0,110)) ?>...</p>
                      <div class="row text-center mb-3">
                        <div class="col-6 border-right">
                          <div><i class="fas fa-user-tie text-primary"></i></div>
                          <small>Trainer</small>
                          <div><?= htmlspecialchars($program['trainer'] ?: 'N/A') ?></div>
                        </div>
                        <div class="col-6">
                          <div><i class="fas fa-users text-info"></i></div>
                          <small>Participants</small>
                          <div><?= htmlspecialchars($program['max_participants'] ?: '0') ?></div>
                        </div>
                      </div>
                      <div class="btn-group btn-blocks" role="group">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewProgram(<?= $program['ld_training_programs_id'] ?>)">View</button>
                        <button class="btn btn-sm btn-outline-success" onclick="editProgram(<?= $program['ld_training_programs_id'] ?>)">Edit</button>
                        <button class="btn btn-sm btn-outline-info" onclick="viewProgramEnrollments(<?= $program['ld_training_programs_id'] ?>)">Enrollees</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="archiveProgram(<?= $program['ld_training_programs_id'] ?>)">Archive</button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <?php if (empty($courses)): ?>
                <div class="col-12"><div class="alert alert-warning">No courses available yet.</div></div>
              <?php endif; ?>
              <?php foreach ($courses as $course): ?>
                <?php
                  $coverUrl = pickCoverUrl();
                  $status = $course['status'] ?? 'active';
                  $statusClass = getStatusClass($status);
                ?>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4 learning-item" data-title="<?= htmlspecialchars(strtolower($course['title'])) ?>">
                  <div class="card learning-card">
                    <div class="status-badge"><span class="badge-custom <?= $statusClass ?>" title="<?= htmlspecialchars(ucfirst($status)) ?>"></span></div>
                    <div style="height:180px; overflow:hidden;"><img src="<?= htmlspecialchars($coverUrl) ?>" class="w-100 h-100" style="object-fit:cover;" alt="Course cover"></div>
                    <div class="card-body">
                      <h4 class="card-title"><?= htmlspecialchars($course['title']) ?></h4>
                      <p><?= htmlspecialchars(substr($course['description'],0,110)) ?>...</p>
                      <div class="row text-center mb-3">
                        <div class="col-6 border-right">
                          <div><i class="fas fa-user text-primary"></i></div>
                          <small>Instructor</small>
                          <div><?= htmlspecialchars($course['instructor'] ?: 'N/A') ?></div>
                        </div>
                        <div class="col-6">
                          <div><i class="fas fa-clock text-info"></i></div>
                          <small>Duration</small>
                          <div><?= htmlspecialchars($course['duration_hours'] ?: '0') ?> hrs</div>
                        </div>
                      </div>
                      <div class="btn-group btn-blocks" role="group">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewCourse(<?= $course['ld_courses_id'] ?>)">View</button>
                        <button class="btn btn-sm btn-outline-success" onclick="editCourse(<?= $course['ld_courses_id'] ?>)">Edit</button>
                        <button class="btn btn-sm btn-outline-info" onclick="viewCourseEnrollments(<?= $course['ld_courses_id'] ?>)">Enrollees</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="archiveCourse(<?= $course['ld_courses_id'] ?>)">Archive</button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </div>

    <aside class="control-sidebar control-sidebar-dark"></aside>
  </div>

  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/dist/js/adminlte.min.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script>
    document.getElementById('learningSearch').addEventListener('input', function() {
      var query = this.value.toLowerCase();
      document.querySelectorAll('.learning-item').forEach(function(card) {
        var title = card.dataset.title.toLowerCase();
        card.style.display = title.indexOf(query) !== -1 ? 'block' : 'none';
      });
    });

    function openCreateProgramModal() {
      openGlobalModal('Create Training Program', 'manage_learning_modal.php?type=program');
    }

    function openCreateCourseModal() {
      openGlobalModal('Create Course', 'manage_learning_modal.php?type=course');
    }

    function viewProgram(id) {
      openGlobalModal('Program Details', 'manage_learning_modal.php?type=program&view_id=' + id);
    }

    function viewCourse(id) {
      openGlobalModal('Course Details', 'manage_learning_modal.php?type=course&view_id=' + id);
    }

    function viewProgramEnrollments(id) {
      openGlobalModal('Program Enrollees', 'enrollments.php?program_id=' + id);
    }

    function viewCourseEnrollments(id) {
      openGlobalModal('Course Enrollees', 'enrollments.php?course_id=' + id);
    }

    function archiveProgram(id) {
      if (!confirm('Archive this training program?')) return;
      fetch('archive_program.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'program_id=' + id })
        .then(response => response.json())
        .then(data => { if (data.success) location.reload(); else alert(data.message); })
        .catch(error => alert('Error: ' + error.message));
    }

    function archiveCourse(id) {
      if (!confirm('Archive this course?')) return;
      fetch('archive_course.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'course_id=' + id })
        .then(response => response.json())
        .then(data => { if (data.success) location.reload(); else alert(data.message); })
        .catch(error => alert('Error: ' + error.message));
    }
  </script>
</body>

</html>
