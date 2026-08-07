<?php
session_start();
// require_once "auth.php";
require_once "../../auth/database.php";
require_once "../../auth/auth_check.php";
require_once __DIR__ . '/../autoload.php';


use App\Controllers\SocialController;
use App\Controllers\GrievanceController;
use App\Controllers\RecognitionController;
use App\Controllers\SurveyController;
use App\Controllers\FeedbackController;
use App\Controllers\CommunicationController;
use App\Controllers\ReactionController;
use App\Controllers\GroupController;
use App\Controllers\GroupMemberController;
use App\Models\SharedFile;

$theme = $_SESSION['user']['theme'] ?? 'light';
$role = strtolower(trim($_SESSION['user']['role'] ?? ''));
$isHrAdmin = $role === 'admin' || $role === 'hr_admin' || strpos($role, 'hr') !== false || strpos($role, 'admin') !== false;

$ctrl = new SocialController();
$grievanceCtrl = new GrievanceController();
$recognitionCtrl = new RecognitionController();
$surveyCtrl = new SurveyController();
$feedbackCtrl = new FeedbackController();
$communicationCtrl = new CommunicationController();
$reactionCtrl = new ReactionController();
$groupCtrl = new GroupController();
$groupMemberCtrl = new GroupMemberController();

$payload = $payload ?? [];
$payload['grievances'] = $grievanceCtrl->getGrievances();
$payload['recognitions'] = $recognitionCtrl->getRecognitions();
$payload['surveys'] = $surveyCtrl->index();
$payload['feedback'] = $feedbackCtrl->index();
$payload['announcements'] = $communicationCtrl->getAnnouncements();
$payload['groups'] = $groupCtrl->getGroups();
$payload['group_members'] = [];
foreach ($payload['groups'] as $group) {
    $groupId = (int)($group['eer_group_id'] ?? 0);
    $payload['group_members'][$groupId] = $groupMemberCtrl->getMembersByGroup($groupId);
}

$db = Database::getInstance()->getConnection();
$employeeStmt = $db->query('SELECT employee_id, full_name FROM employees ORDER BY full_name');
$payload['employees'] = $employeeStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = $_SESSION['user']['employee_id'] ?? null;
    $userId = $_SESSION['user']['id'] ?? null;

    if (!empty($_POST['content']) || (!empty($_FILES['shared_file']['name']) && $_FILES['shared_file']['error'] === UPLOAD_ERR_OK)) {
        $authorId = $isHrAdmin ? $userId : ($employeeId ?? $userId);
        $userType = $isHrAdmin ? 'user' : ($employeeId ? 'employee' : 'user');
        $messages = [];
        $fileUploadPresent = !empty($_FILES['shared_file']['name']) && $_FILES['shared_file']['error'] === UPLOAD_ERR_OK;

        if ($authorId) {
            if ($fileUploadPresent) {
                $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt', 'xlsx', 'xls'];
                $file = $_FILES['shared_file'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) {
                    $_SESSION['flash_error'] = 'Invalid file type. Allowed: ' . implode(', ', $allowed);
                } elseif ($file['size'] > 10 * 1024 * 1024) {
                    $_SESSION['flash_error'] = 'File too large. Max size is 10MB.';
                } else {
                    $uploadDir = __DIR__ . '/../../uploads/social_files/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($file['name']));
                    $targetFileName = time() . '_' . $safeName;
                    $targetFile = $uploadDir . $targetFileName;

                    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                        $description = trim($_POST['description'] ?? '');
                        $sharedFileModel = new SharedFile();
                        $fileId = $sharedFileModel->createFile($safeName, 'uploads/social_files/' . $targetFileName, $file['size'], $ext, $authorId, $description);
                        if ($fileId) {
                            $messages[] = 'File shared successfully.';
                        } else {
                            unlink($targetFile);
                            $_SESSION['flash_error'] = 'Failed to save file information.';
                        }
                    } else {
                        $_SESSION['flash_error'] = 'Failed to store uploaded file.';
                    }
                }
            }

            if (!empty($_POST['content'])) {
                $ctrl->createPost($authorId, trim($_POST['content']), $userType);
                $messages[] = 'Post published successfully.';
            }

            if (empty($_SESSION['flash_error']) && !empty($messages)) {
                $_SESSION['flash_success'] = implode(' ', $messages);
            }
        } else {
            $_SESSION['flash_error'] = 'Unable to determine author.';
        }
    } elseif (!empty($_POST['comment']) && !empty($_POST['post_id'])) {
        $commentText = trim($_POST['comment']);
        $replyTo = (int)($_POST['reply_to'] ?? 0);
        if ($replyTo > 0) {
          $commentText = '(Comment to #' . $replyTo . ') ' . $commentText;
        }
        $authorId = $isHrAdmin ? $userId : ($employeeId ?? $userId);
        $userType = $isHrAdmin ? 'user' : ($employeeId ? 'employee' : 'user');
        if ($authorId) {
            $ctrl->addComment((int)$_POST['post_id'], $authorId, $commentText, $userType);
            $_SESSION['flash_success'] = 'Comment added successfully.';
        } else {
            $_SESSION['flash_error'] = 'Unable to determine author.';
        }
    }

    if (!empty($_POST['reaction_type']) && !empty($_POST['post_id'])) {
        $reactionType = $_POST['reaction_type'];
        $postId = (int)$_POST['post_id'];

        $authorId = $isHrAdmin ? $userId : ($employeeId ?? $userId);
        $userType = $isHrAdmin ? 'user' : ($employeeId ? 'employee' : 'user');
        if ($userType === 'employee') {
            $reactionCtrl->addReaction($postId, $authorId, null, $reactionType);
        } else {
            $reactionCtrl->addReaction($postId, null, $authorId, $reactionType);
        }
        $_SESSION['flash_success'] = 'Reaction added successfully.';
    }

    if (!empty($_POST['group_name'])) {
        $groupName = trim($_POST['group_name']);
        $createdBy = $isHrAdmin ? $userId : ($employeeId ?? $userId);
        $groupCtrl->createGroup($groupName, null, $createdBy);
        $_SESSION['flash_success'] = 'Group created successfully.';
    } elseif (!empty($_POST['group_id']) && !empty($_POST['employee_id'])) {
        $groupId = (int)$_POST['group_id'];
        $employeeIdValue = $_POST['employee_id'];
        $groupMemberCtrl->addMember($groupId, $employeeIdValue);
        $_SESSION['flash_success'] = 'Member added to group successfully.';
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
          class="brand-image elevation-3 brand-image-opacity" />
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


        <!-- MAIN CONTENT -->
            <!-- HEADER -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h3>Social Collaboration Tools</h3>
                <p class="text-muted">Encourage teamwork and collaboration</p>
            </div>
          </div>
        </div>
      </div> <!-- /.content-header -->
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

      <!-- Social Collaboration Tools Tabs -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header p-0">
              <ul class="nav nav-tabs" id="collaboration-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="feed-tab" data-toggle="tab" href="#feed" role="tab" aria-controls="feed" aria-selected="true">
                    <i class="fas fa-rss mr-2"></i>Employee Interaction Feed
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="forums-tab" data-toggle="tab" href="#forums" role="tab" aria-controls="forums" aria-selected="false">
                    <i class="fas fa-comments mr-2"></i>Discussion Forums
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="groups-tab" data-toggle="tab" href="#groups" role="tab" aria-controls="groups" aria-selected="false">
                    <i class="fas fa-users mr-2"></i>Team Groups
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="projects-tab" data-toggle="tab" href="#projects" role="tab" aria-controls="projects" aria-selected="false">
                    <i class="fas fa-project-diagram mr-2"></i>Project Collaboration Spaces
                  </a>
                </li>
              </ul>
            </div>
            <div class="card-body">
              <div class="tab-content" id="collaboration-tab-content">

                <!-- Employee Interaction Feed Tab -->
                <div class="tab-pane fade show active" id="feed" role="tabpanel" aria-labelledby="feed-tab">
                  <div class="row mb-4">
                    <div class="col-12">
                      <div class="card card-info card-outline">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-share-alt mr-2"></i>Share an Update</h3></div>
                        <div class="card-body">
                          <form method="post" enctype="multipart/form-data" class="share-form" onsubmit="return false;">
                            <div class="form-group">
                              <label for="content">Post / Update</label>
                              <textarea id="content" class="form-control" name="content" rows="4" placeholder="Share something with your team..." ></textarea>
                            </div>
                            <div class="form-row">
                              <div class="form-group col-md-6">
                                <label for="file-upload">Attach File</label>
                                <input id="file-upload" type="file" name="shared_file" class="form-control" />
                              </div>
                              <div class="form-group col-md-6">
                                <label for="file-description">Description (optional)</label>
                                <textarea id="file-description" name="description" class="form-control" rows="2" placeholder="Add a description for the file..."></textarea>
                              </div>
                            </div>
                            <div id="share-status" class="mb-3"></div>
                            <button class="btn btn-primary" type="submit">Share</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      <div class="card card-info card-outline">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-stream mr-2"></i>Social Feed</h3></div>
                        <div class="card-body">
                          <div id="social-feed">
                            <div class="alert alert-info">Loading social feed...</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Sentiment Analysis -->
                  <div class="row">
                    <div class="col-12">
                      <div class="card card-info card-outline">
                        <div class="card-header"><h3 class="card-title">Sentiment Analysis</h3></div>
                        <div class="card-body" id="sentiment-analysis">
                          <p class="text-muted">Sentiment analysis for posts and comments will be displayed here.</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Engagement Analytics -->
                  <div class="row">
                    <div class="col-12">
                      <div class="card card-secondary card-outline">
                        <div class="card-header"><h3 class="card-title">Engagement Analytics</h3></div>
                        <div class="card-body" id="engagement-analytics">
                          <p class="text-muted">Analytics for user engagement, reactions, and activity trends will be displayed here.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Discussion Forums Tab -->
                <div class="tab-pane fade" id="forums" role="tabpanel" aria-labelledby="forums-tab">
                  <div class="row">
                    <div class="col-12">
                      <div class="card card-warning card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-comments mr-2"></i>Discussion Forums</h3>
                          <div class="card-tools">
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#createForumModal">
                              <i class="fas fa-plus mr-1"></i>Create Forum
                            </button>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="forums-list">
                            <div class="alert alert-info">Loading forums...</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Team Groups Tab -->
                <div class="tab-pane fade" id="groups" role="tabpanel" aria-labelledby="groups-tab">
                  <div class="row">
                    <div class="col-12">
                      <div class="card card-primary card-outline">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-users mr-2"></i>Manage Groups</h3></div>
                        <div class="card-body">
                          <div class="row">
                            <div class="col-12">
                              <form method="post" class="group-create-form">
                                <div class="form-group">
                                  <label for="group-name">Group Name</label>
                                  <input id="group-name" type="text" name="group_name" class="form-control" placeholder="Enter group name" required>
                                </div>
                                <button class="btn btn-primary" type="submit">Create Group</button>
                              </form>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-12">
                              <?php if (!empty($payload['groups'])): ?>
                                <form id="group-member-form" class="group-member-form">
                                  <div class="form-group">
                                    <label for="group-id">Group</label>
                                    <select id="group-id" name="group_id" class="form-control" required>
                                      <option value="">Choose group</option>
                                      <?php foreach ($payload['groups'] as $group): ?>
                                        <option value="<?= htmlspecialchars($group['eer_group_id']) ?>"><?= htmlspecialchars($group['name'] . ' (ID: ' . $group['eer_group_id'] . ')') ?></option>
                                      <?php endforeach; ?>
                                    </select>
                                  </div>
                                  <div class="form-group">
                                    <label for="employee-id">Employee</label>
                                    <select id="employee-id" name="employee_id" class="form-control" required>
                                      <option value="">Choose employee</option>
                                      <?php foreach ($payload['employees'] as $employee): ?>
                                        <option value="<?= htmlspecialchars($employee['employee_id']) ?>"><?= htmlspecialchars($employee['employee_id'] . ' - ' . ($employee['full_name'] ?? 'No name')) ?></option>
                                      <?php endforeach; ?>
                                    </select>
                                  </div>
                                  <button class="btn btn-primary" type="submit">Add Member</button>
                                </form>
                              <?php else: ?>
                                <div class="alert alert-warning">
                                  Create a group first before adding members.
                                </div>
                              <?php endif; ?>
                            </div>
                          </div>

                          <?php if (!empty($payload['employees'])): ?>
                            <div class="mt-4">
                              <h5 class="mb-2">Employee list</h5>
                              <ul class="list-group">
                                <?php foreach ($payload['employees'] as $employee): ?>
                                  <li class="list-group-item py-1"><?= htmlspecialchars($employee['employee_id'] . ' - ' . ($employee['full_name'] ?? 'No name')) ?></li>
                                <?php endforeach; ?>
                              </ul>
                            </div>
                          <?php endif; ?>

                          <div class="mt-4">
                            <div class="card card-success card-outline">
                              <div class="card-header"><h3 class="card-title">Existing Groups</h3></div>
                              <div class="card-body">
                                <?php if (!empty($payload['groups'])): ?>
                                  <div class="list-group">
                                    <?php foreach ($payload['groups'] as $group): ?>
                                      <div class="list-group-item" data-group-id="<?= htmlspecialchars($group['eer_group_id']) ?>">
                                        <h5 class="mb-1"><?= htmlspecialchars($group['name'] ?? 'Untitled Group') ?></h5>
                                        <p class="mb-1 text-muted">ID: <?= htmlspecialchars($group['eer_group_id'] ?? 'N/A') ?></p>
                                        <?php $members = $payload['group_members'][(int)($group['eer_group_id'] ?? 0)] ?? []; ?>
                                        <p class="mb-1"><strong>Members:</strong></p>
                                        <div id="group-members-<?= htmlspecialchars($group['eer_group_id']) ?>">
                                          <?php if (!empty($members)): ?>
                                            <ul class="list-group list-group-flush">
                                              <?php foreach ($members as $member): ?>
                                                <li class="list-group-item py-1">
                                                  Employee ID: <?= htmlspecialchars($member['employee_id'] ?? 'N/A') ?>
                                                  <?php if (!empty($member['full_name'])): ?>
                                                    - <?= htmlspecialchars($member['full_name']) ?>
                                                  <?php endif; ?>
                                                </li>
                                              <?php endforeach; ?>
                                            </ul>
                                          <?php else: ?>
                                            <p class="text-muted mb-0">No members yet.</p>
                                          <?php endif; ?>
                                        </div>
                                      </div>
                                    <?php endforeach; ?>
                                  </div>
                                <?php else: ?>
                                  <p class="text-muted">No groups created yet.</p>
                                <?php endif; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Project Collaboration Spaces Tab -->
                <div class="tab-pane fade" id="projects" role="tabpanel" aria-labelledby="projects-tab">
                  <div class="row">
                    <div class="col-12">
                      <div class="card card-success card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-project-diagram mr-2"></i>Project Collaboration Spaces</h3>
                          <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#createProjectModal">
                              <i class="fas fa-plus mr-1"></i>Create Project Space
                            </button>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="projects-list">
                            <div class="alert alert-info">Loading project spaces...</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
      </div>
    


      
     <!-- Social Collaboration Tools -->

    </div> <!-- /.social-area -->
  </div> <!-- /.content-wrapper -->
  </div> <!-- /.wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <script src="../../assets/dist/js/adminlte.js"></script>

  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script src="../../assets/dist/js/profile.js"></script>
  <script src="js/live_updates.js"></script>
  <script src="js/social.js?v=2"></script>

  <!-- Create Forum Modal -->
  <div class="modal fade" id="createForumModal" tabindex="-1" role="dialog" aria-labelledby="createForumModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createForumModalLabel">Create Discussion Forum</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="createForumForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="forumTitle">Forum Title</label>
              <input type="text" class="form-control" id="forumTitle" name="title" required>
            </div>
            <div class="form-group">
              <label for="forumDescription">Description</label>
              <textarea class="form-control" id="forumDescription" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <label for="forumCategory">Category</label>
              <select class="form-control" id="forumCategory" name="category">
                <option value="general">General Discussion</option>
                <option value="technical">Technical Support</option>
                <option value="feedback">Feedback & Suggestions</option>
                <option value="announcements">Announcements</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning">Create Forum</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Forum Modal -->
  <div class="modal fade" id="viewForumModal" tabindex="-1" role="dialog" aria-labelledby="viewForumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewForumModalLabel">Forum Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <h5 id="forumViewTitle" class="mb-3"></h5>
          <p id="forumViewDescription" class="mb-4 text-muted"></p>
          <div class="row">
            <div class="col-sm-6 mb-2">
              <strong>Category:</strong> <span id="forumViewCategory"></span>
            </div>
            <div class="col-sm-6 mb-2">
              <strong>Created by:</strong> <span id="forumViewCreatedBy"></span>
            </div>
            <div class="col-sm-12 mb-2">
              <strong>Created at:</strong> <span id="forumViewCreatedAt"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Project Modal -->
  <div class="modal fade" id="viewProjectModal" tabindex="-1" role="dialog" aria-labelledby="viewProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewProjectModalLabel">Project Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <h5 id="projectViewName" class="mb-3"></h5>
          <p id="projectViewDescription" class="mb-4 text-muted"></p>
          <div class="row">
            <div class="col-sm-6 mb-2"><strong>Status:</strong> <span id="projectViewStatus"></span></div>
            <div class="col-sm-6 mb-2"><strong>Deadline:</strong> <span id="projectViewDeadline"></span></div>
            <div class="col-sm-6 mb-2"><strong>Created by:</strong> <span id="projectViewCreatedBy"></span></div>
            <div class="col-sm-6 mb-2"><strong>Created at:</strong> <span id="projectViewCreatedAt"></span></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Create Project Modal -->
  <div class="modal fade" id="createProjectModal" tabindex="-1" role="dialog" aria-labelledby="createProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createProjectModalLabel">Create Project Collaboration Space</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="createProjectForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="projectName">Project Name</label>
              <input type="text" class="form-control" id="projectName" name="name" required>
            </div>
            <div class="form-group">
              <label for="projectDescription">Description</label>
              <textarea class="form-control" id="projectDescription" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <label for="projectDeadline">Deadline (Optional)</label>
              <input type="date" class="form-control" id="projectDeadline" name="deadline">
            </div>
            <div class="form-group">
              <label for="projectStatus">Status</label>
              <select class="form-control" id="projectStatus" name="status">
                <option value="planning">Planning</option>
                <option value="active">Active</option>
                <option value="on-hold">On Hold</option>
                <option value="completed">Completed</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Create Project Space</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>
