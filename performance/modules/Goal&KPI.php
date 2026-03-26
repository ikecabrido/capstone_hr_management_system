<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../../auth/database.php";

$theme = $_SESSION['user']['theme'] ?? 'light';
$employee_id = $_SESSION['user']['id'];

// Get database connection
$database = Database::getInstance();
$pdo = $database->getConnection();

// Handle API requests for goal data
if (isset($_GET['action']) && $_GET['action'] === 'get_goal' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM goals WHERE goal_id = ? AND employee_id = ?");
    $stmt->execute([$_GET['id'], $employee_id]);
    $goal = $stmt->fetch();
    
    header('Content-Type: application/json');
    echo json_encode($goal);
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_goal':
                $stmt = $pdo->prepare("
                    INSERT INTO goals (employee_id, department, position, goal_title, goal_description, target_date, priority_level, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'not_started')
                ");
                $stmt->execute([
                    $employee_id,
                    $_POST['department'],
                    $_POST['position'],
                    $_POST['goal_title'],
                    $_POST['goal_description'],
                    $_POST['target_date'],
                    $_POST['priority_level']
                ]);
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Goal created successfully!'];
                break;
                
            case 'update_goal':
                $stmt = $pdo->prepare("
                    UPDATE goals 
                    SET goal_title = ?, goal_description = ?, target_date = ?, priority_level = ?, status = ?
                    WHERE goal_id = ? AND employee_id = ?
                ");
                $stmt->execute([
                    $_POST['goal_title'],
                    $_POST['goal_description'],
                    $_POST['target_date'],
                    $_POST['priority_level'],
                    $_POST['status'],
                    $_POST['goal_id'],
                    $employee_id
                ]);
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Goal updated successfully!'];
                break;
                
            case 'delete_goal':
                $stmt = $pdo->prepare("DELETE FROM goals WHERE goal_id = ? AND employee_id = ?");
                $stmt->execute([$_POST['goal_id'], $employee_id]);
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Goal deleted successfully!'];
                break;
                
            case 'update_kpi':
                $stmt = $pdo->prepare("
                    UPDATE goals 
                    SET kpi_current = ?, kpi_target = ?, kpi_unit = ?, status = ?
                    WHERE goal_id = ? AND employee_id = ?
                ");
                $status = $_POST['kpi_current'] >= $_POST['kpi_target'] ? 'completed' : 
                         ($_POST['kpi_current'] > 0 ? 'in_progress' : 'not_started');
                $stmt->execute([
                    $_POST['kpi_current'],
                    $_POST['kpi_target'],
                    $_POST['kpi_unit'],
                    $status,
                    $_POST['goal_id'],
                    $employee_id
                ]);
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'KPI updated successfully!'];
                break;
        }
        header("Location: Goal&KPI.php");
        exit;
    }
}

// Fetch goals
$stmt = $pdo->prepare("
    SELECT * FROM goals 
    WHERE employee_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$employee_id]);
$goals = $stmt->fetchAll();

// Calculate statistics
$stats = [
    'total' => count($goals),
    'not_started' => 0,
    'in_progress' => 0,
    'completed' => 0
];

foreach ($goals as $goal) {
    $status = $goal['status'] ?? 'not_started';
    if (isset($stats[$status])) {
        $stats[$status]++;
    }
}

// Get user info for form defaults - using a simpler approach since users table doesn't have dept/position
$user_info = [
    'department' => 'Not specified',
    'position' => 'Not specified'
];

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Goal & KPI Management</title>
  
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="../custom.css" />
  <link rel="stylesheet" href="../../layout/toast.css" />
  
  <style>
    .goal-card {
      transition: all 0.3s ease;
      border-left: 4px solid transparent;
    }
    .goal-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .goal-card.not-started { border-left-color: #6c757d; }
    .goal-card.in-progress { border-left-color: #ffc107; }
    .goal-card.completed { border-left-color: #28a745; }
    
    .progress-ring {
      transform: rotate(-90deg);
    }
    
    .kpi-progress {
      background: linear-gradient(90deg, #e9ecef 0%, #e9ecef var(--progress), #28a745 var(--progress), #28a745 100%);
    }
    
    .stat-card {
      transition: all 0.3s ease;
    }
    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__wobble" src="../../assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60" />
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="../performance.php" class="nav-link">Home</a>
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

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="../performance.php" class="brand-link">
        <img src="../../assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan</span>
      </a>

      <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
          <div class="info">
            <a href="#" onclick="openGlobalModal('Profile Settings','../../user_profile/profile_form.php')" class="d-block">
              <?= htmlspecialchars($_SESSION['user']['name']) ?>
            </a>
          </div>
        </div>

        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
              <a href="../performance.php" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="360-degree.php" class="nav-link">
                <i class="nav-icon fas fa-chart-pie"></i>
                <p>360-Degree Feedback</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Appraisals&review.php" class="nav-link">
                <i class="nav-icon fas fa-edit"></i>
                <p>Appraisals & Review</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Goal&KPI.php" class="nav-link active">
                <i class="nav-icon fas fa-tree"></i>
                <p>Goal & KPI</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../logout.php" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>Logout</p>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Goal & KPI Management</h1>
            </div>
            <div class="col-sm-6">
              <button class="btn btn-primary float-right" onclick="openGoalModal()">
                <i class="fas fa-plus"></i> New Goal
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Statistics Cards -->
          <div class="row mb-4">
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info stat-card">
                <div class="inner">
                  <h3><?= $stats['total'] ?></h3>
                  <p>Total Goals</p>
                </div>
                <div class="icon">
                  <i class="fas fa-bullseye"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-secondary stat-card">
                <div class="inner">
                  <h3><?= $stats['not_started'] ?></h3>
                  <p>Not Started</p>
                </div>
                <div class="icon">
                  <i class="fas fa-clock"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-warning stat-card">
                <div class="inner">
                  <h3><?= $stats['in_progress'] ?></h3>
                  <p>In Progress</p>
                </div>
                <div class="icon">
                  <i class="fas fa-spinner"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-success stat-card">
                <div class="inner">
                  <h3><?= $stats['completed'] ?></h3>
                  <p>Completed</p>
                </div>
                <div class="icon">
                  <i class="fas fa-check-circle"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Goals List -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Your Goals</h3>
                  <div class="card-tools">
                    <div class="btn-group">
                      <button type="button" class="btn btn-default btn-sm" onclick="filterGoals('all')">All</button>
                      <button type="button" class="btn btn-default btn-sm" onclick="filterGoals('not_started')">Not Started</button>
                      <button type="button" class="btn btn-default btn-sm" onclick="filterGoals('in_progress')">In Progress</button>
                      <button type="button" class="btn btn-default btn-sm" onclick="filterGoals('completed')">Completed</button>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <?php if (empty($goals)): ?>
                    <div class="text-center py-4">
                      <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>
                      <h5>No goals yet</h5>
                      <p class="text-muted">Start by creating your first goal!</p>
                    </div>
                  <?php else: ?>
                    <div class="row" id="goalsContainer">
                      <?php foreach ($goals as $goal): ?>
                        <?php 
                        $status = $goal['status'] ?? 'not_started';
                        $statusClass = str_replace('_', '-', $status);
                        $progress = 0;
                        if (!empty($goal['kpi_target']) && $goal['kpi_target'] > 0) {
                          $progress = min(100, ($goal['kpi_current'] ?? 0) / $goal['kpi_target'] * 100);
                        }
                        ?>
                        <div class="col-md-6 col-lg-4 mb-4 goal-item" data-status="<?= $status ?>">
                          <div class="card goal-card <?= $statusClass ?>">
                            <div class="card-header">
                              <div class="d-flex justify-content-between align-items-center">
                                <span class="badge badge-<?= $status === 'completed' ? 'success' : ($status === 'in_progress' ? 'warning' : 'secondary') ?>">
                                  <?= ucwords(str_replace('_', ' ', $status)) ?>
                                </span>
                                <div class="btn-group">
                                  <button class="btn btn-sm btn-outline-primary" onclick="editGoal(<?= $goal['goal_id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-danger" onclick="deleteGoal(<?= $goal['goal_id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                  </button>
                                </div>
                              </div>
                            </div>
                            <div class="card-body">
                              <h5 class="card-title"><?= htmlspecialchars($goal['goal_title']) ?></h5>
                              <p class="card-text text-muted"><?= htmlspecialchars($goal['goal_description'] ?? '') ?></p>
                              
                              <div class="mb-2">
                                <small class="text-muted">Target Date:</small>
                                <span class="float-right"><?= date('M d, Y', strtotime($goal['target_date'])) ?></span>
                              </div>
                              
                              <div class="mb-2">
                                <small class="text-muted">Priority:</small>
                                <span class="float-right badge badge-<?= $goal['priority_level'] === 'critical' ? 'danger' : ($goal['priority_level'] === 'high' ? 'warning' : 'info') ?>">
                                  <?= ucfirst($goal['priority_level']) ?>
                                </span>
                              </div>
                              
                              <?php if (!empty($goal['kpi_target'])): ?>
                                <div class="mt-3">
                                  <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">KPI Progress</small>
                                    <small><?= round($progress) ?>%</small>
                                  </div>
                                  <div class="progress">
                                    <div class="progress-bar bg-<?= $progress >= 100 ? 'success' : ($progress >= 50 ? 'info' : 'warning') ?>" 
                                         style="width: <?= $progress ?>%"></div>
                                  </div>
                                  <div class="text-center mt-1">
                                    <small><?= $goal['kpi_current'] ?? 0 ?> / <?= $goal['kpi_target'] ?> <?= $goal['kpi_unit'] ?? '' ?></small>
                                  </div>
                                </div>
                              <?php endif; ?>
                              
                              <button class="btn btn-sm btn-outline-info mt-2 btn-block" onclick="updateKPI(<?= $goal['goal_id'] ?>)">
                                <i class="fas fa-chart-line"></i> Update KPI
                              </button>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include "../../layout/global_modal.php"; ?>
  </div>

  <!-- Goal Modal -->
  <div class="modal fade" id="goalModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="goalModalTitle">Create New Goal</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <form id="goalForm" method="POST">
          <input type="hidden" name="action" value="create_goal">
          <input type="hidden" name="goal_id" id="goalId">
          <div class="modal-body">
            <div class="form-group">
              <label>Goal Title</label>
              <input type="text" class="form-control" name="goal_title" required>
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea class="form-control" name="goal_description" rows="3"></textarea>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Department</label>
                  <input type="text" class="form-control" name="department" value="<?= htmlspecialchars($user_info['department'] ?? '') ?>" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Position</label>
                  <input type="text" class="form-control" name="position" value="<?= htmlspecialchars($user_info['position'] ?? '') ?>" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Target Date</label>
                  <input type="date" class="form-control" name="target_date" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Priority Level</label>
                  <select class="form-control" name="priority_level">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Goal</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- KPI Update Modal -->
  <div class="modal fade" id="kpiModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update KPI Progress</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <form id="kpiForm" method="POST">
          <input type="hidden" name="action" value="update_kpi">
          <input type="hidden" name="goal_id" id="kpiGoalId">
          <div class="modal-body">
            <div class="form-group">
              <label>KPI Target</label>
              <input type="number" class="form-control" name="kpi_target" step="0.01" required>
            </div>
            <div class="form-group">
              <label>KPI Unit</label>
              <input type="text" class="form-control" name="kpi_unit" placeholder="e.g., %, units, hours">
            </div>
            <div class="form-group">
              <label>Current Progress</label>
              <input type="number" class="form-control" name="kpi_current" step="0.01" required>
            </div>
            <div class="form-group">
              <label>Status</label>
              <select class="form-control" name="status" id="kpiStatus">
                <option value="not_started">Not Started</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update KPI</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- REQUIRED SCRIPTS -->
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <script src="../../assets/dist/js/adminlte.js"></script>
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>

  <script>
    // Goal management functions
    function openGoalModal() {
      document.getElementById('goalModalTitle').textContent = 'Create New Goal';
      document.getElementById('goalForm').reset();
      document.querySelector('#goalForm input[name="action"]').value = 'create_goal';
      $('#goalModal').modal('show');
    }

    function editGoal(goalId) {
      // Load goal data and populate form
      fetch('Goal&KPI.php?action=get_goal&id=' + goalId)
        .then(response => response.json())
        .then(data => {
          document.getElementById('goalModalTitle').textContent = 'Edit Goal';
          document.querySelector('#goalForm input[name="action"]').value = 'update_goal';
          document.getElementById('goalId').value = data.goal_id;
          document.querySelector('#goalForm input[name="goal_title"]').value = data.goal_title;
          document.querySelector('#goalForm textarea[name="goal_description"]').value = data.goal_description || '';
          document.querySelector('#goalForm input[name="target_date"]').value = data.target_date;
          document.querySelector('#goalForm select[name="priority_level"]').value = data.priority_level;
          $('#goalModal').modal('show');
        });
    }

    function deleteGoal(goalId) {
      if (confirm('Are you sure you want to delete this goal?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="action" value="delete_goal">
          <input type="hidden" name="goal_id" value="${goalId}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    }

    function updateKPI(goalId) {
      document.getElementById('kpiGoalId').value = goalId;
      document.getElementById('kpiForm').reset();
      $('#kpiModal').modal('show');
    }

    function filterGoals(status) {
      const items = document.querySelectorAll('.goal-item');
      items.forEach(item => {
        if (status === 'all' || item.dataset.status === status) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    }

    // Auto-update status based on KPI progress
    document.querySelector('#kpiForm input[name="kpi_current"]')?.addEventListener('input', function() {
      const current = parseFloat(this.value) || 0;
      const target = parseFloat(document.querySelector('#kpiForm input[name="kpi_target"]').value) || 0;
      const statusSelect = document.getElementById('kpiStatus');
      
      if (current >= target && target > 0) {
        statusSelect.value = 'completed';
      } else if (current > 0) {
        statusSelect.value = 'in_progress';
      } else {
        statusSelect.value = 'not_started';
      }
    });

    // Show toast notifications
    <?php if (isset($_SESSION['toast'])): ?>
      showToast('<?= $_SESSION['toast']['type'] ?>', '<?= $_SESSION['toast']['message'] ?>');
      <?php unset($_SESSION['toast']); ?>
    <?php endif; ?>
  </script>
</body>
</html>