<?php
session_start();
// require_once "auth.php";
require_once "../../auth/database.php";
require_once "../../auth/auth_check.php";
require_once __DIR__ . '/../autoload.php';

use App\Controllers\SocialController;

$theme = $_SESSION['user']['theme'] ?? 'light';

$ctrl = new SocialController();
$payload = $payload ?? [];
$payload['feed'] = $ctrl->getPosts();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = (int)($_SESSION['user']['id'] ?? 0);

    if ($employeeId <= 0) {
        $_SESSION['flash_error'] = 'Your account is not linked to an employee record.';
    } else {
        if (!empty($_POST['content'])) {
            $ctrl->createPost($employeeId, $_POST['content']);
            $_SESSION['flash_success'] = 'Post published successfully.';
        } elseif (!empty($_POST['comment']) && !empty($_POST['post_id'])) {
            $commentText = trim($_POST['comment']);
            $replyTo = (int)($_POST['reply_to'] ?? 0);
            if ($replyTo > 0) {
                $commentText = '(Reply to #' . $replyTo . ') ' . $commentText;
            }
            $ctrl->addComment((int)$_POST['post_id'], $employeeId, $commentText);
            $_SESSION['flash_success'] = 'Comment added successfully.';
        } else {
            $_SESSION['flash_error'] = 'Post or comment content is required.';
        }
    }

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Employee Engagement and Relations Management</title>

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

  <link rel="stylesheet" href="../../layout/toast.css" />
  <link rel="stylesheet" href="css/social.css" />
  <link rel="stylesheet" href="../custom.css" /> 
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
          <a href="../engagement_relations.php" class="nav-link">Home</a>        </li>
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
      <a href="../engagement_relations.php" class="brand-link">

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
              <a href="dashboard.php" class="nav-link ">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="communication.php" class="nav-link">
                <i class="nav-icon fas fa-chart-pie"></i>
                <p>Communication</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="survey.php" class="nav-link">
                <i class="nav-icon fas fa-poll"></i>
                <p>Survey</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="recognition.php" class="nav-link">
                <i class="nav-icon fas fa-award"></i>
                <p>Recognition</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="grievance.php" class="nav-link">
                <i class="nav-icon fas fa-exclamation-triangle"></i>
                <p> Grievances</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="social.php" class="nav-link active">
                <i class="nav-icon fas fa-users"></i>
                <p> Social</p>
              </a>
            </li>
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


        <!-- MAIN CONTENT --
            <!-- HEADER -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Social</h1>
                <p class="text-muted">Employee social feed and interactions</p>
            </div>
          </div>
        </div>

    <div class="social-area">
      <div class="row">
        <div class="col-12">
          <?php if (!empty($flashSuccess)): ?>
            <div class="alert alert-success"><?=htmlspecialchars($flashSuccess)?></div>
          <?php endif; ?>
          <?php if (!empty($flashError)): ?>
            <div class="alert alert-danger"><?=htmlspecialchars($flashError)?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card card-info card-outline">
      <div class="card-header"><h3 class="card-title">Post Something</h3></div>
      <div class="card-body">
        <form method="post" class="post-form">
          <div class="form-group">
            <textarea class="form-control" name="content" rows="3" placeholder="Share something with your team..." required></textarea>
          </div>
          <button class="btn btn-primary" type="submit">Post</button>
        </form>
      </div>
    </div>

    <div class="card card-secondary card-outline">
      <div class="card-header"><h3 class="card-title">Activity Feed</h3></div>
      <div class="card-body">
        <?php if (empty($payload['feed'])): ?>
          <p class="text-muted">No posts yet. Be the first to post!</p>
        <?php endif; ?>

        <?php foreach ($payload['feed'] as $p): ?>
          <article class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
              <strong><?= htmlspecialchars($p['employee_name']) ?></strong>
              <small class="text-muted"><?= htmlspecialchars($p['created_at']) ?></small>
            </div>
            <div class="card-body">
              <p><?= nl2br(htmlspecialchars($p['content'])) ?></p>
              <div class="mb-3">
                <?php foreach ($p['comments'] as $c): ?>
                  <div class="pl-3 py-1 border-bottom">
                    <small>
                      <strong><?= htmlspecialchars($c['employee_name']) ?></strong>: <?= htmlspecialchars($c['comment']) ?>
                      <span class="text-muted"><?= htmlspecialchars($c['created_at'] ?? '') ?></span>
                    </small>
                    <button type="button" class="btn btn-link btn-sm reply-trigger" data-post-id="<?= (int)$p['eer_social_post_id'] ?>" data-reply-to="<?= (int)$c['eer_comment_id'] ?>" data-reply-to-name="<?= htmlspecialchars($c['employee_name']) ?>">Reply</button>
                  </div>
                <?php endforeach; ?>
              </div>
              <form method="post" class="reply-form">
                <input type="hidden" name="post_id" value="<?= (int)$p['eer_social_post_id'] ?>" />
                <input type="hidden" name="reply_to" class="reply_to_input" value="0" />
                <div class="form-group">
                  <input type="text" name="comment" class="form-control" placeholder="Reply to this post..." required />
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">Reply</button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>

    <script>
      document.querySelectorAll('.reply-trigger').forEach(function(btn) {
        btn.addEventListener('click', function() {
          var postId = btn.getAttribute('data-post-id');
          var replyTo = btn.getAttribute('data-reply-to');
          var replyName = btn.getAttribute('data-reply-to-name');
          var postCard = btn.closest('article');
          var replyInput = postCard.querySelector('.reply_to_input');
          if (replyInput) {
            replyInput.value = replyTo;
          }
          var textInput = postCard.querySelector('input[name="comment"]');
          if (textInput) {
            textInput.value = '@' + replyName + ' ';
            textInput.focus();
          }
        });
      });
    </script>
  </div>
  <!-- CONTENT -->

    </div>
    <?php include "../../layout/global_modal.php"; ?>
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

  <script></script>
    <script src="views/js/social.js"></script>
</body>

</html>
