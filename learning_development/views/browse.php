<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/TrainingProgramController.php";
require_once "../controllers/CourseController.php";
require_once "../controllers/EnrollmentController.php";

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../learning_development.php");
    exit;
}

$section = isset($_GET['section']) ? $_GET['section'] : 'programs';
if (!in_array($section, ['programs', 'courses'])) {
    $section = 'programs';
}

$programController = new TrainingProgramController();
$courseController = new CourseController();

$allPrograms = $programController->index();
$allCourses = $courseController->index();

$availablePrograms = array_filter($allPrograms, function($p) {
    return $p['status'] !== 'inactive';
});

$availableCourses = array_filter($allCourses, function($c) {
    return $c['status'] !== 'inactive';
});

$theme = $_SESSION['user']['theme'] ?? 'light';

function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'active':
            return 'status-circle status-active';
        case 'inactive':
            return 'status-circle status-inactive';
        case 'archived':
            return 'status-circle status-archived';
        case 'pending':
            return 'status-circle status-pending';
        default:
            return 'status-circle status-default';
    }
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
    '../img/cover-placeholder/cover-placeholder-10.gif',
];
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Browse - Learning and Development</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="../custom.css" />
  <link rel="stylesheet" href="../../layout/toast.css" />
  <style>
    .manage-section-tabs { display:flex; gap:0; margin-bottom:20px; border-bottom:2px solid #dee2e6; }
    .manage-section-tabs .tab-button { flex:1; padding:12px 20px; background:#f8f9fa; border:none; border-bottom:3px solid transparent; cursor:pointer; font-weight:500; color:#6c757d; transition:all .3s; }
    .manage-section-tabs .tab-button.active { background:#0056b3; color:#fff; border-bottom-color:#0056b3; }
    .manage-card {
      background:#fff;
      border-radius:16px;
      border:1px solid #e2e8f0;
      overflow:hidden;
      box-shadow:0 8px 18px rgba(2,12,27,0.08);
      transition:transform .25s ease, box-shadow .25s ease;
      display:flex;
      flex-direction:column;
      height:100%;
    }
    .manage-card:hover {
      box-shadow:0 10px 30px rgba(2,12,27,0.18);
      transform:translateY(-3px);
    }
    .manage-card-image {
      height:220px;
      overflow:hidden;
      position:relative;
    }
    .manage-card-image img {
      width:100%;
      height:100%;
      object-fit:cover;
      filter:saturate(1.03) brightness(1.05);
    }
    .manage-card-status {
      position: absolute;
      top: 12px;
      right: 12px;
      z-index: 10;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(255,255,255,0.18);
      border: 1px solid rgba(255,255,255,0.58);
      backdrop-filter: blur(9px);
      border-radius: 999px;
      padding: 5px 12px;
      box-shadow: 0 10px 18px rgba(5, 22, 52, 0.24);
      color: transparent;
    }
    .status-circle {
      display: inline-block;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      border: 1.8px solid rgba(255,255,255,0.96);
      box-shadow: 0 3px 7px rgba(3,12,35,0.24);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .status-circle:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 12px rgba(3,12,35,0.28);
    }
    .status-active { background: linear-gradient(135deg, #10b981, #06b6d4); }
    .status-inactive { background: linear-gradient(135deg, #6b7280, #4b5563); }
    .status-archived { background: linear-gradient(135deg, #f97316, #ea580c); }
    .status-pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .status-default { background: linear-gradient(135deg, #94a3b8, #64748b); }
    .status-inactive { background: linear-gradient(135deg, #6b7280, #4b5563); }
    .status-archived { background: linear-gradient(135deg, #f97316, #ea580c); }
    .status-pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .status-default { background: linear-gradient(135deg, #94a3b8, #64748b); }
    .manage-card-body {
      padding:18px 20px 18px;
      flex:1;
      display:flex;
      flex-direction:column;
    }
    .manage-card-title {
      font-size:20px;
      font-weight:700;
      color:#1a3e85;
      margin:0 0 10px;
    }
    .manage-card-description {
      font-size:14px;
      color:#475569;
      margin-bottom:16px;
      line-height:1.6;
      min-height:4.2rem;
    }
    .manage-card-info { display:flex; justify-content:space-between; padding:12px 0; border-top:1px solid #e9ecef; margin-bottom:15px; }
    .manage-card-info-item { text-align:center; flex:1; }
    .manage-card-info-label { font-size:12px; color:#6c757d; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
    .manage-card-info-value { font-size:16px; font-weight:600; color:#003d82; }
    .manage-card-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:auto; }
    .manage-card-actions .btn { flex:1; min-width:96px; padding:8px 12px; font-size:13px; }
    .manage-card-actions .btn-success { background:#28a745; border-color:#28a745; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__wobble" src="../../assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60" />
    </div>

    <?php include '../../layout/global_modal.php'; ?>

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

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="../learning_development.php" class="brand-link"><img src="../../assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity:.9"><span class="brand-text font-weight-light">BCP Bulacan</span></a>
      <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
          <div class="info"><a href="#" onclick="openGlobalModal('Profile Settings','../../user_profile/profile_form.php')" class="d-block"><?= htmlspecialchars($_SESSION['user']['name']) ?></a></div>
        </div>

        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"><a href="../learning_development.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
            <li class="nav-item"><a href="browse.php" class="nav-link active"><i class="nav-icon fas fa-book"></i><p>Browse</p></a></li>
            <li class="nav-item"><a href="certification_management.php" class="nav-link"><i class="nav-icon fas fa-certificate"></i><p>Certification</p></a></li>
            <?php if ($_SESSION['user']['role'] === 'learning'): ?>
            <li class="nav-item"><a href="manage_learning.php" class="nav-link"><i class="nav-icon fas fa-tasks"></i><p>Learning Management</p></a></li>
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
          <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0">Browse Training</h1></div>
            <div class="col-sm-6">
              <div class="input-group input-group-sm float-sm-right" style="max-width: 300px;">
                <input id="browseSearch" type="text" class="form-control" placeholder="Search programs/courses..." aria-label="Search">
                <div class="input-group-append">
                  <button class="btn btn-info" type="button" onclick="filterBrowse()"><i class="fas fa-search"></i></button>
                </div>
              </div>
            </div>
          </div>
          <hr style="border-top:2px solid #dee2e6;margin:10px 0;">
        </div>
      </div>

      <section class="content">
        <div class="container-fluid">
          <div class="manage-section-tabs">
            <button class="tab-button <?= $section === 'programs' ? 'active' : '' ?>" onclick="window.location.href='?section=programs'">Training Programs</button>
            <button class="tab-button <?= $section === 'courses' ? 'active' : '' ?>" onclick="window.location.href='?section=courses'">Courses</button>
          </div>

          <?php if ($section === 'programs'): ?>
            <div class="tab-header-wrapper"><h3 style="margin:0;">Training Programs</h3></div>
            <?php if (empty($availablePrograms)): ?>
              <div class="text-center py-5"><i class="fas fa-book fa-4x text-muted mb-4"></i><h3 class="text-muted">No Training Programs Available</h3><p class="text-muted">Please check back later.</p></div>
            <?php else: ?>
              <div class="row">
                <?php foreach ($availablePrograms as $program): $cover = $placeholderImages[array_rand($placeholderImages)]; $statusClass = getStatusClass($program['status']); ?>
                  <div class="col-lg-4 col-md-6 mb-4">
                    <div class="manage-card" data-search="<?= strtolower(htmlspecialchars($program['title'] . ' ' . $program['trainer'] . ' ' . $program['description'])) ?>">
                      <div class="manage-card-image"><img src="<?= htmlspecialchars($cover) ?>" alt="Program Cover"><div class="manage-card-status" aria-label="<?= ucfirst($program['status']) ?>"><span class="<?= $statusClass ?>"></span></div></div>
                      <div class="manage-card-body">
                        <h4 class="manage-card-title"><?= htmlspecialchars($program['title']) ?></h4>
                        <p class="manage-card-description"><?= htmlspecialchars(substr($program['description'] ?? '', 0, 100)); ?><?= strlen($program['description'] ?? '') > 100 ? '...' : '' ?></p>
                        <div class="manage-card-info"><div class="manage-card-info-item"><div class="manage-card-info-label">Trainer</div><div class="manage-card-info-value"><?= htmlspecialchars($program['trainer'] ?? 'N/A') ?></div></div><div class="manage-card-info-item"><div class="manage-card-info-label">Participants</div><div class="manage-card-info-value"><?= $program['max_participants'] ?></div></div></div>
                        <div class="manage-card-actions">
                          <button class="btn btn-primary btn-sm" onclick="openGlobalModal('Program Details', 'manage_learning_modal.php?type=program&view_id=' + encodeURIComponent(<?= $program['ld_training_programs_id'] ?>))"><i class="fas fa-eye"></i> View</button>
                          <button class="btn btn-success btn-sm" onclick="enrollProgram(<?= $program['ld_training_programs_id'] ?>)"><i class="fas fa-check"></i> Enroll</button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          <?php endif; ?>

          <?php if ($section === 'courses'): ?>
            <div class="tab-header-wrapper"><h3 style="margin:0;">Courses</h3></div>
            <?php if (empty($availableCourses)): ?>
              <div class="text-center py-5"><i class="fas fa-graduation-cap fa-4x text-muted mb-4"></i><h3 class="text-muted">No Courses Available</h3><p class="text-muted">Please check back later.</p></div>
            <?php else: ?>
              <div class="row">
                <?php foreach ($availableCourses as $course): $cover = $placeholderImages[array_rand($placeholderImages)]; $statusClass = getStatusClass($course['status']); ?>
                  <div class="col-lg-4 col-md-6 mb-4">
                    <div class="manage-card" data-search="<?= strtolower(htmlspecialchars($course['title'] . ' ' . $course['instructor'] . ' ' . $course['description'])) ?>">
                      <div class="manage-card-image"><img src="<?= htmlspecialchars($cover) ?>" alt="Course Cover"><div class="manage-card-status" aria-label="<?= ucfirst($course['status']) ?>"><span class="<?= $statusClass ?>"></span></div></div>
                      <div class="manage-card-body">
                        <h4 class="manage-card-title"><?= htmlspecialchars($course['title']) ?></h4>
                        <p class="manage-card-description"><?= htmlspecialchars(substr($course['description'] ?? '', 0, 100)); ?><?= strlen($course['description'] ?? '') > 100 ? '...' : '' ?></p>
                        <div class="manage-card-info"><div class="manage-card-info-item"><div class="manage-card-info-label">Instructor</div><div class="manage-card-info-value"><?= htmlspecialchars($course['instructor'] ?? 'N/A') ?></div></div><div class="manage-card-info-item"><div class="manage-card-info-label">Duration</div><div class="manage-card-info-value"><?= $course['duration_hours'] ?>h</div></div></div>
                        <div class="manage-card-actions">
                          <button class="btn btn-primary btn-sm" onclick="openGlobalModal('Course Details', 'manage_learning_modal.php?type=course&view_id=' + encodeURIComponent(<?= $course['ld_courses_id'] ?>))"><i class="fas fa-eye"></i> View</button>
                          <button class="btn btn-success btn-sm" onclick="enrollCourse(<?= $course['ld_courses_id'] ?>)"><i class="fas fa-check"></i> Enroll</button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          <?php endif; ?>

        </div>
      </section>
    </div>

    <?php include "../../layout/global_modal.php"; ?>
  </div>

  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/dist/js/adminlte.min.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script>
    function enrollProgram(id) {
      if (!confirm('Enroll in this training program?')) return;
      fetch('enroll.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'program_id=' + encodeURIComponent(id)
      })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        if (data.success) location.reload();
      })
      .catch(err => alert('Error enrolling: ' + err.message));
    }

    function filterBrowse() {
      const query = document.getElementById('browseSearch').value.trim().toLowerCase();
      const cards = document.querySelectorAll('.manage-card');
      cards.forEach(card => {
        const text = card.getAttribute('data-search') || '';
        if (!query || text.indexOf(query) !== -1) {
          card.closest('.col-lg-4').style.display = '';
        } else {
          card.closest('.col-lg-4').style.display = 'none';
        }
      });
    }

    document.getElementById('browseSearch').addEventListener('input', filterBrowse);

    function enrollCourse(id) {
      if (!confirm('Enroll in this course?')) return;
      fetch('enroll.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'course_id=' + encodeURIComponent(id)
      })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        if (data.success) location.reload();
      })
      .catch(err => alert('Error enrolling: ' + err.message));
    }
  </script>
</body>
</html>