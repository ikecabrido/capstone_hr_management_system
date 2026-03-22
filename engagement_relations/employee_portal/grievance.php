<?php
session_start();
require_once __DIR__ . '/../../auth/database.php';

require_once __DIR__ . '/../autoload.php';

use App\Controllers\GrievanceController;
use App\Models\Employee;

$grievanceCtrl = new GrievanceController();
$employeeModel = new Employee();
$grievances = $grievanceCtrl->getGrievances();
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
    $action = $_POST['action'] ?? 'create_grievance';
    $selectedEmployeeId = $employeeId > 0 ? $employeeId : (int)($_POST['employee_id'] ?? 0);

    if ($action === 'create_grievance') {
        $subject = trim($_POST['subject'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? 'Workplace Conflict');
        $anonymous = !empty($_POST['anonymous']) ? 1 : 0;

        // File upload
        $attachmentPath = null;
        if (!empty($_FILES['attachment']['name'])) {
            $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt'];
            $uploadDir = __DIR__ . '/../../uploads/grievances/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = basename($_FILES['attachment']['name']);
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) {
                $flashError = 'Invalid attachment type. Allowed: ' . implode(', ', $allowed);
            } elseif ($_FILES['attachment']['size'] > 5 * 1024 * 1024) {
                $flashError = 'Attachment too large (max 5MB).';
            } else {
                $targetFile = $uploadDir . time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $fileName);
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
                    $attachmentPath = 'uploads/grievances/' . basename($targetFile);
                } else {
                    $flashError = 'Failed to upload attachment.';
                }
            }
        }

        if (!$flashError) {
            if ($subject === '') {
                $flashError = 'Subject is required.';
            } elseif ($description === '') {
                $flashError = 'Description is required.';
            } elseif ($selectedEmployeeId <= 0) {
                $flashError = 'Please select an employee.';
            } elseif (!$employeeModel->find($selectedEmployeeId)) {
                $flashError = 'The selected employee does not exist in the system.';
            } else {
                try {
                    $grievanceCtrl->fileGrievance($selectedEmployeeId, $subject, $description, $category, $anonymous, $attachmentPath);
                    $flashSuccess = 'Grievance submitted successfully.';
                    $grievances = $grievanceCtrl->getGrievances();
                } catch (\Exception $e) {
                    $flashError = $e->getMessage();
                }
            }
        }
    } elseif ($action === 'assign_grievance') {
        $id = (int)($_POST['grievance_id'] ?? 0);
        $assignTo = (int)($_POST['assign_to'] ?? 0);
        if ($id > 0 && $assignTo > 0) {
            $grievanceCtrl->assignTo($id, $assignTo);
            $flashSuccess = 'Grievance assigned.';
            $grievances = $grievanceCtrl->getGrievances();
        }
    } elseif ($action === 'add_update') {
        $id = (int)($_POST['grievance_id'] ?? 0);
        $comment = trim($_POST['update_comment'] ?? '');
        $employee = (int)($_SESSION['user']['id'] ?? 0);
        if ($id > 0 && $comment !== '' && $employee > 0) {
            $grievanceCtrl->addUpdate($id, $comment, $employee);
            $flashSuccess = 'Update added to discussion.';
            $grievances = $grievanceCtrl->getGrievances();
        }
    } elseif ($action === 'satisfaction') {
        $id = (int)($_POST['grievance_id'] ?? 0);
        $rating = (int)($_POST['satisfaction_rating'] ?? 0);
        $comment = trim($_POST['satisfaction_comment'] ?? '');
        if ($id > 0 && $rating >= 1 && $rating <= 5) {
            $grievanceCtrl->submitSatisfaction($id, $rating, $comment);
            $flashSuccess = 'Thank you for your feedback.';
            $grievances = $grievanceCtrl->getGrievances();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Employee Portal - Grievances</title>
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <style>
    .main-sidebar {
      height: 100vh;
      overflow-y: auto;
    }
  </style>

</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button">
            <i class="fas fa-bars"></i>
          </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="index.php" class="nav-link">Dashboard</a>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <div class="nav-link" id="clock">--:--:--</div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" id="darkToggle" role="button" title="Toggle Dark Mode">
            <i class="fas fa-moon" id="themeIcon"></i>
          </a>
        </li>
      </ul>
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

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Grievances</h1>
              <p class="text-muted">Submit your grievances here</p>
            </div>
          </div>
        </div>
      </div>
      <section class="content"><div class="container-fluid">
        <?php if ($flashSuccess): ?><div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div><?php endif; ?>
        <?php if ($flashError): ?><div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>
        <div class="card card-secondary card-outline"><div class="card-header"><h3 class="card-title">Submit Complaint</h3></div><div class="card-body">
            <form method="post" enctype="multipart/form-data">
              <input type="hidden" name="action" value="create_grievance">
              <div class="form-group"><label for="subject">Subject</label><input type="text" id="subject" name="subject" class="form-control" required></div>
              <div class="form-group"><label for="description">Description</label><textarea id="description" name="description" class="form-control" rows="4" required></textarea></div>
              <div class="form-group"><label for="category">Category</label><select id="category" name="category" class="form-control" required>
                  <option value="Workplace Conflict">Workplace Conflict</option>
                  <option value="Harassment / Bullying">Harassment / Bullying</option>
                  <option value="Payroll Concern">Payroll Concern</option>
                  <option value="Work Environment">Work Environment</option>
                  <option value="Management Issue">Management Issue</option>
                  <option value="Other">Other</option>
              </select></div>
              <div class="form-group form-check"><input type="checkbox" id="anonymous" name="anonymous" class="form-check-input" value="1"><label class="form-check-label" for="anonymous">Submit anonymously</label></div>
              <div class="form-group"><label for="attachment">Attachment (optional)</label><input type="file" id="attachment" name="attachment" class="form-control-file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt" /></div>
              <?php if (!$employeeId): ?>
                <div class="form-group"><label for="employee_id">Employee</label><select id="employee_id" name="employee_id" class="form-control" required><option value="">Select employee</option><?php foreach ($employees as $emp): ?><option value="<?= (int)$emp['eer_employee_id'] ?>"><?= htmlspecialchars($emp['name']) ?></option><?php endforeach; ?></select></div>
              <?php endif; ?>
              <button type="submit" class="btn btn-warning">Submit Complaint</button>
            </form>
        </div></div>
        <div class="card card-info card-outline"><div class="card-header"><h3 class="card-title">Complaints Log</h3></div><div class="card-body">
          <?php if (!empty($grievances)): ?>
            <ul class="list-group">
              <?php foreach ($grievances as $item): ?>
                <?php $updates = $grievanceCtrl->history($item['eer_grievance_id']); ?>
                <li class="list-group-item">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <strong><?= htmlspecialchars(($item['anonymous'] ?? 0) ? 'Anonymous' : ($item['employee_name'] ?? 'Unknown')) ?></strong>
                      <span class="badge badge-secondary"><?= htmlspecialchars($item['category'] ?? 'Uncategorized') ?></span>
                      <h5 class="mt-2"><?= htmlspecialchars($item['subject']) ?></h5>
                      <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                      <small>Status: <strong><?= htmlspecialchars($item['status']) ?></strong> • Submitted: <?= htmlspecialchars($item['created_at']) ?></small><br>
                      <small>Assigned to: <?= htmlspecialchars($item['assigned_name'] ?? 'Unassigned') ?></small>
                      <?php if (!empty($item['attachment_path'])): ?>
                        <div class="mt-2"><a href="../../<?= htmlspecialchars($item['attachment_path']) ?>" target="_blank" class="badge badge-info"><i class="fas fa-download"></i> View Attachment</a></div>
                      <?php endif; ?>
                    </div>
                    <div class="btn-group" role="group" aria-label="Action Buttons">

                    </div>
                  </div>

                  <div class="mt-3">
                    <strong>Mediation Thread</strong>
                    <?php if (!empty($updates)): ?>
                      <ul class="list-group list-group-flush">
                        <?php foreach ($updates as $update): ?>
                          <li class="list-group-item py-1"><strong><?= htmlspecialchars($update['updated_by_name'] ?? 'Unknown') ?></strong> (<?= htmlspecialchars($update['updated_at']) ?>): <?= nl2br(htmlspecialchars($update['update_text'])) ?></li>
                        <?php endforeach; ?>
                      </ul>
                    <?php else: ?>
                      <p class="text-muted">No thread entries yet.</p>
                    <?php endif; ?>
                    <form method="post" class="mt-2">
                      <input type="hidden" name="action" value="add_update">
                      <input type="hidden" name="grievance_id" value="<?= (int)$item['eer_grievance_id'] ?>">
                      <div class="form-group">
                        <textarea name="update_comment" class="form-control form-control-sm" rows="2" placeholder="Add mediation note / HR comment"></textarea>
                      </div>
                      <button type="submit" class="btn btn-sm btn-outline-secondary">Add Comment</button>
                    </form>
                  </div>

                  <?php if ($item['status'] === 'Resolved' && empty($item['satisfaction_rating'])): ?>
                    <div class="mt-3">
                      <form method="post">
                        <input type="hidden" name="action" value="satisfaction">
                        <input type="hidden" name="grievance_id" value="<?= (int)$item['eer_grievance_id'] ?>">
                        <div class="form-group mb-1"><label>How satisfied are you with the outcome?</label>
                          <select name="satisfaction_rating" class="form-control form-control-sm" required>
                            <option value="">Choose rating</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                              <option value="<?= $i ?>"><?= $i ?>/5</option>
                            <?php endfor; ?>
                          </select>
                        </div>
                        <div class="form-group mb-1"><textarea name="satisfaction_comment" class="form-control form-control-sm" rows="2" placeholder="Optional comment"></textarea></div>
                        <button type="submit" class="btn btn-sm btn-outline-info">Submit Satisfaction</button>
                      </form>
                    </div>
                  <?php elseif (!empty($item['satisfaction_rating'])): ?>
                    <div class="mt-3"><small>Satisfaction: <?= (int)$item['satisfaction_rating'] ?>/5 - <?= htmlspecialchars($item['satisfaction_comment'] ?: 'No comment') ?></small></div>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-muted">No complaints yet.</p>
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
