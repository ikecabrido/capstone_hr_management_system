<?php
session_start();
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../autoload.php';

use App\Controllers\SocialController;
use App\Models\Employee;

$socialCtrl = new SocialController();
$employeeModel = new Employee();

$posts = $socialCtrl->getPosts();
$employees = $employeeModel->all();
$employeeId = (int) ($_SESSION['user']['id'] ?? 0);
$theme = $_SESSION['user']['theme'] ?? 'light';
$userName = htmlspecialchars($_SESSION['user']['name'] ?? 'Employee');
$currentPage = basename($_SERVER['PHP_SELF']);

function navItem($page, $icon, $label, $currentPage) {
    $active = $currentPage === $page ? 'active' : '';
    return "<li class=\"nav-item\"><a href=\"{$page}\" class=\"nav-link {$active}\"><i class=\"nav-icon fas fa-{$icon}\"></i><p>{$label}</p></a></li>";
}

$flashSuccess = null;
$flashError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'post') {
        $contentPost = trim($_POST['content'] ?? '');
        $targetEmployeeId = $employeeId > 0 ? $employeeId : (int)($_POST['employee_id'] ?? 0);

        if ($contentPost === '') {
            $flashError = 'Content is required to post.';
        } else {
            $socialCtrl->createPost($targetEmployeeId, $contentPost);
            $flashSuccess = 'Post published into social feed.';
            $posts = $socialCtrl->getPosts();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'comment') {
        $postId = (int)($_POST['post_id'] ?? 0);
        $commentText = trim($_POST['comment'] ?? '');
        $authorId = $employeeId > 0 ? $employeeId : (int)($_POST['employee_id'] ?? 0);

        if ($postId <= 0 || $commentText === '') {
            $flashError = 'Comment text is required.';
        } else {
            $socialCtrl->addComment($postId, $authorId, $commentText);
            $flashSuccess = 'Comment added.';
            $posts = $socialCtrl->getPosts();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Employee Portal - Social</title>
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-dark">
      <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li><li class="nav-item d-none d-sm-inline-block"><a href="index.php" class="nav-link">Dashboard</a></li></ul>
      <ul class="navbar-nav ml-auto"><li class="nav-item"><div class="nav-link" id="clock">--:--:--</div></li><li class="nav-item"><a class="nav-link" data-widget="fullscreen" href="#" role="button"><i class="fas fa-expand-arrows-alt"></i></a></li><li class="nav-item"><a class="nav-link" href="#" id="darkToggle" role="button" title="Toggle Dark Mode"><i class="fas fa-moon" id="themeIcon"></i></a></li></ul>
    </nav>
    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="index.php" class="brand-link">
        <img src="../../assets/pics/bcpLogo.png" alt="Logo" class="brand-image elevation-3" style="opacity: .8" />
        <span class="brand-text font-weight-light">BCP Employee</span>
      </a>
      <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="info">
            <a href="#" class="d-block"><?= $userName ?></a>
          </div>
        </div>
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <?php echo navItem('index.php', 'bullhorn', 'Announcements', $currentPage); ?>
            <?php echo navItem('survey.php', 'poll', 'Survey', $currentPage); ?>
            <?php echo navItem('social.php', 'users', 'Social', $currentPage); ?>
            <?php echo navItem('grievance.php', 'exclamation-triangle', 'Grievances', $currentPage); ?>
            <?php echo navItem('../../logout.php', 'sign-out-alt', 'Logout', $currentPage); ?>
          </ul>
      </div>
    </aside>
    <div class="content-wrapper">
      <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1 class="m-0">Social Feed</h1></div></div></div></div>
      <section class="content"><div class="container-fluid">
        <?php if ($flashSuccess): ?><div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div><?php endif; ?>
        <?php if ($flashError): ?><div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>
        <div class="card card-primary card-outline"><div class="card-header"><h3 class="card-title">Create Social Post</h3></div><div class="card-body">
          <form method="post">
            <input type="hidden" name="action" value="post" />
            <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required></div>
            <div class="form-group"><label>Content</label><textarea name="content" class="form-control" rows="3" required></textarea></div>
            <?php if (!$employeeId): ?>
            <div class="form-group"><label>Employee</label><select name="employee_id" class="form-control" required><option value="">Choose</option><?php foreach ($employees as $emp): ?><option value="<?= (int)$emp['eer_employee_id'] ?>"><?= htmlspecialchars($emp['name']) ?></option><?php endforeach; ?></select></div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">Post</button>
          </form>
        </div></div>
        <div class="card card-secondary card-outline"><div class="card-header"><h3 class="card-title">Activity</h3></div><div class="card-body">
          <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
              <div class="card mb-3">
                <div class="card-body">
                  <h5><?= htmlspecialchars($post['title'] ?? 'Social Post') ?></h5>
                  <p><?= nl2br(htmlspecialchars($post['content'] ?? '')) ?></p>
                  <p class="text-muted">Posted by <?= htmlspecialchars($post['owner_name'] ?? 'Unknown') ?> on <?= htmlspecialchars($post['created_at'] ?? '') ?></p>
                </div>
                <div class="card-footer">
                  <?php if (!empty($post['comments'])): ?>
                  <ul class="list-group mb-2">
                    <?php foreach ($post['comments'] as $comment): ?>
                      <li class="list-group-item"><strong><?= htmlspecialchars($comment['employee_name'] ?? 'Unknown') ?>:</strong> <?= nl2br(htmlspecialchars($comment['comment'])) ?> <small class="text-muted">(<?= htmlspecialchars($comment['created_at']) ?>)</small></li>
                    <?php endforeach; ?>
                  </ul>
                  <?php endif; ?>
                  <form method="post" class="mt-2">
                    <input type="hidden" name="action" value="comment" />
                    <input type="hidden" name="post_id" value="<?= (int)$post['eer_social_post_id'] ?>" />
                    <div class="input-group">
                      <input type="text" name="comment" class="form-control" placeholder="Write a comment..." required />
                      <div class="input-group-append"><button class="btn btn-secondary" type="submit">Comment</button></div>
                    </div>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-muted">No posts yet.</p>
          <?php endif; ?>
        </div></div>
      </div></section>
    </div>
    <?php include "../../layout/global_modal.php"; ?>
    <aside class="control-sidebar control-sidebar-dark"></aside>
  </div>
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <script src="../../assets/dist/js/adminlte.min.js"></script>
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
</body>
</html>
