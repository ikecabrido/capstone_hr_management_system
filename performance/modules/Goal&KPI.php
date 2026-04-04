<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../../auth/database.php";

$theme = $_SESSION['user']['theme'] ?? 'light';
$db = Database::getInstance()->getConnection();

$message = '';

// Handle form submission (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Goal
    if (isset($_POST['add_goal'])) {
        $employee_id = $_POST['employee_id'];
        $goal_title = $_POST['goal_title'];
        $kpi_name = $_POST['kpi_name'];
        $target_value = $_POST['target_value'];
        $current_progress = $_POST['current_progress'];
        $status = $_POST['status'];
        $priority = $_POST['priority'] ?? 'Medium';
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        try {
            $sql = "INSERT INTO pm_goals (employee_id, goal_title, kpi_name, target_value, current_progress, status, priority, start_date, end_date) 
                    VALUES (:employee_id, :goal_title, :kpi_name, :target_value, :current_progress, :status, :priority, :start_date, :end_date)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'employee_id' => $employee_id,
                'goal_title' => $goal_title,
                'kpi_name' => $kpi_name,
                'target_value' => $target_value,
                'current_progress' => $current_progress,
                'status' => $status,
                'priority' => $priority,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
            $message = '<div class="alert alert-success">Goal added successfully!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }

    // Update Goal
    if (isset($_POST['update_goal'])) {
        $goal_id = $_POST['goal_id'];
        $employee_id = $_POST['employee_id'];
        $goal_title = $_POST['goal_title'];
        $kpi_name = $_POST['kpi_name'];
        $target_value = $_POST['target_value'];
        $current_progress = $_POST['current_progress'];
        $status = $_POST['status'];
        $priority = $_POST['priority'] ?? 'Medium';
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        try {
            $sql = "UPDATE pm_goals SET 
                    employee_id = :employee_id,
                    goal_title = :goal_title,
                    kpi_name = :kpi_name,
                    target_value = :target_value,
                    current_progress = :current_progress,
                    status = :status,
                    priority = :priority,
                    start_date = :start_date,
                    end_date = :end_date
                    WHERE goal_id = :goal_id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'employee_id' => $employee_id,
                'goal_title' => $goal_title,
                'kpi_name' => $kpi_name,
                'target_value' => $target_value,
                'current_progress' => $current_progress,
                'status' => $status,
                'priority' => $priority,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'goal_id' => $goal_id
            ]);
            $message = '<div class="alert alert-success">Goal updated successfully!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }

    // Delete Goal
    if (isset($_POST['delete_goal'])) {
        $goal_id = $_POST['goal_id'];
        try {
            $sql = "DELETE FROM pm_goals WHERE goal_id = :goal_id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['goal_id' => $goal_id]);
            $message = '<div class="alert alert-success">Goal deleted successfully!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// Fetch employees for dropdown
$stmt = $db->query("SELECT employee_id as id, full_name FROM employees WHERE employment_status = 'Active' ORDER BY full_name");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch distinct departments for filter
$stmt = $db->query("SELECT DISTINCT department FROM employees WHERE employment_status = 'Active' AND department IS NOT NULL ORDER BY department");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch goals list with search and filters
$search = $_GET['search'] ?? '';
$filter_status = $_GET['filter_status'] ?? '';
$filter_department = $_GET['filter_department'] ?? '';
$filter_priority = $_GET['filter_priority'] ?? '';
$filter_start_date = $_GET['filter_start_date'] ?? '';
$filter_end_date = $_GET['filter_end_date'] ?? '';

$query = "
    SELECT g.*, e.full_name, e.department
    FROM pm_goals g 
    JOIN employees e ON g.employee_id = e.employee_id 
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $query .= " AND (e.full_name LIKE :search OR g.goal_title LIKE :search)";
    $params['search'] = "%$search%";
}

if (!empty($filter_status)) {
    $query .= " AND g.status = :status";
    $params['status'] = $filter_status;
}

if (!empty($filter_department)) {
    $query .= " AND e.department = :department";
    $params['department'] = $filter_department;
}

if (!empty($filter_priority)) {
    $query .= " AND g.priority = :priority";
    $params['priority'] = $filter_priority;
}

if (!empty($filter_start_date)) {
    $query .= " AND g.start_date >= :start_date";
    $params['start_date'] = $filter_start_date;
}

if (!empty($filter_end_date)) {
    $query .= " AND g.end_date <= :end_date";
    $params['end_date'] = $filter_end_date;
}

$query .= " ORDER BY g.end_date ASC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$goals_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate statistics
$total_goals = count($goals_list);
$completed_goals = 0;
$on_track_goals = 0;
$delayed_goals = 0;
$overdue_goals = 0;
$near_due_goals = 0; // Goals ending within 7 days

$today = new DateTime();

foreach ($goals_list as $goal) {
    if ($goal['status'] == 'Completed') {
        $completed_goals++;
    } elseif ($goal['status'] == 'On Track') {
        $on_track_goals++;
    } elseif ($goal['status'] == 'Delayed') {
        $delayed_goals++;
    }
    
    // Check for overdue and near due
    $end_date = new DateTime($goal['end_date']);
    $days_remaining = $today->diff($end_date)->days;
    $is_overdue = $today > $end_date;
    
    if ($is_overdue && $goal['status'] != 'Completed') {
        $overdue_goals++;
    } else if ($days_remaining <= 7 && $days_remaining > 0) {
        $near_due_goals++;
    }
}

$completed_percentage = ($total_goals > 0) ? round(($completed_goals / $total_goals) * 100) : 0;
$on_track_percentage = ($total_goals > 0) ? round(($on_track_goals / $total_goals) * 100) : 0;
$delayed_percentage = ($total_goals > 0) ? round(($delayed_goals / $total_goals) * 100) : 0;
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Goal & KPI Progress Tracking | Performance Management</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- Select2 -->
  <link rel="stylesheet" href="../../assets/plugins/select2/css/select2.min.css" />
  <link rel="stylesheet" href="../../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="../custom.css" />
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
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
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="../performance.php" class="brand-link">
        <img src="../../assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan </span>
      </a>
      <div class="sidebar">
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
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
              <a href="Performancereport.php" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>Performance Report</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Training.php" class="nav-link">
                <i class="nav-icon fas fa-graduation-cap"></i>
                <p>Training</p>
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
              <h1 class="m-0">Goal & KPI Progress Tracking</h1>
            </div>
            <div class="col-sm-6 text-right">
              <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addGoalModal">
                <i class="fas fa-plus"></i> Add New Goal
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <?= $message ?>

          <!-- Dashboard Stats Cards -->
          <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?= $total_goals ?></h3>
                  <p>Total Goals</p>
                </div>
                <div class="icon">
                  <i class="fas fa-bullseye"></i>
                </div>
                <a href="#" class="small-box-footer">
                  All active goals
                </a>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?= $completed_goals ?><sup style="font-size: 20px">(<?= $completed_percentage ?>%)</sup></h3>
                  <p>Completed Goals</p>
                </div>
                <div class="icon">
                  <i class="fas fa-check-circle"></i>
                </div>
                <a href="#" class="small-box-footer">
                  Successfully completed
                </a>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><?= $on_track_goals ?><sup style="font-size: 20px">(<?= $on_track_percentage ?>%)</sup></h3>
                  <p>On Track Goals</p>
                </div>
                <div class="icon">
                  <i class="fas fa-tasks"></i>
                </div>
                <a href="#" class="small-box-footer">
                  Progressing well
                </a>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><?= $delayed_goals ?><sup style="font-size: 20px">(<?= $delayed_percentage ?>%)</sup></h3>
                  <p>Delayed Goals</p>
                </div>
                <div class="icon">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="#" class="small-box-footer">
                  Requires attention
                </a>
              </div>
            </div>
          </div>

          <!-- Alerts for Overdue & Near Due Goals -->
          <?php if ($overdue_goals > 0): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="fas fa-clock"></i> <strong><?= $overdue_goals ?> goal(s)</strong> are overdue and need immediate attention!
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>

          <?php if ($near_due_goals > 0 && $overdue_goals === 0): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <i class="fas fa-calendar-alt"></i> <strong><?= $near_due_goals ?> goal(s)</strong> are due within the next 7 days.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>

          <!-- Add Goal Modal -->
          <div class="modal fade" id="addGoalModal" tabindex="-1" role="dialog" aria-labelledby="addGoalModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header bg-primary">
                  <h5 class="modal-title" id="addGoalModalLabel">Add New Goal</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form action="" method="POST">
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="add_employee_id">Assigned Employee</label>
                          <select name="employee_id" id="add_employee_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            <?php foreach ($employees as $employee): ?>
                              <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['full_name']) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="goal_title">Goal Title</label>
                          <input type="text" name="goal_title" id="goal_title" class="form-control" placeholder="Enter goal title" required>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="kpi_name">KPI Name</label>
                          <input type="text" name="kpi_name" id="kpi_name" class="form-control" placeholder="Enter KPI name" required>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="target_value">Target Value (%)</label>
                          <input type="number" name="target_value" id="target_value" class="form-control" value="100" step="0.01" required>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="current_progress">Current Progress (%)</label>
                          <input type="number" name="current_progress" id="current_progress" class="form-control" value="0" step="0.01" required>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="status">Status</label>
                          <select name="status" id="status" class="form-control" required>
                            <option value="On Track">On Track</option>
                            <option value="Delayed">Delayed</option>
                            <option value="Completed">Completed</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="start_date">Start Date</label>
                          <input type="date" name="start_date" id="start_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="end_date">End Date</label>
                          <input type="date" name="end_date" id="end_date" class="form-control" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="priority">Priority Level</label>
                          <select name="priority" id="priority" class="form-control" required>
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="add_goal" class="btn btn-primary">Add Goal</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Search and Filter Form -->
          <div class="card card-outline card-secondary mb-3">
            <div class="card-body">
              <form action="" method="GET">
                <!-- Row 1: Search and Status Filter -->
                <div class="row mb-3">
                  <div class="col-md-5">
                    <div class="form-group mb-0">
                      <label for="search">Search Goal/Employee</label>
                      <input type="text" name="search" id="search" class="form-control" placeholder="Search by title or name..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group mb-0">
                      <label for="filter_status">Status</label>
                      <select name="filter_status" id="filter_status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="On Track" <?= $filter_status == 'On Track' ? 'selected' : '' ?>>On Track</option>
                        <option value="Delayed" <?= $filter_status == 'Delayed' ? 'selected' : '' ?>>Delayed</option>
                        <option value="Completed" <?= $filter_status == 'Completed' ? 'selected' : '' ?>>Completed</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-0">
                      <label for="filter_priority">Priority</label>
                      <select name="filter_priority" id="filter_priority" class="form-control">
                        <option value="">All Priorities</option>
                        <option value="Low" <?= $filter_priority == 'Low' ? 'selected' : '' ?>>Low</option>
                        <option value="Medium" <?= $filter_priority == 'Medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="High" <?= $filter_priority == 'High' ? 'selected' : '' ?>>High</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Row 2: Department and Date Range -->
                <div class="row mb-3">
                  <div class="col-md-3">
                    <div class="form-group mb-0">
                      <label for="filter_department">Department</label>
                      <select name="filter_department" id="filter_department" class="form-control">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                          <option value="<?= htmlspecialchars($dept['department']) ?>" <?= $filter_department == $dept['department'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['department']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-0">
                      <label for="filter_start_date">Start Date From</label>
                      <input type="date" name="filter_start_date" id="filter_start_date" class="form-control" value="<?= htmlspecialchars($filter_start_date) ?>">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-0">
                      <label for="filter_end_date">End Date To</label>
                      <input type="date" name="filter_end_date" id="filter_end_date" class="form-control" value="<?= htmlspecialchars($filter_end_date) ?>">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-0 d-flex gap-2">
                      <button type="submit" class="btn btn-secondary flex-grow-1">
                        <i class="fas fa-search"></i> Filter
                      </button>
                      <a href="Goal&KPI.php" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i> Reset
                      </a>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Goals List Table -->
          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Goals Progress</h3>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover text-nowrap">
                <thead>
                  <tr>
                    <th>Goal Title</th>
                    <th>KPI Name</th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Progress</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Dates</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($goals_list)): ?>
                    <tr>
                      <td colspan="7" class="text-center">No goals found.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($goals_list as $goal): ?>
                      <?php 
                        $percent = ($goal['target_value'] > 0) ? ($goal['current_progress'] / $goal['target_value']) * 100 : 0;
                        $percent = min(100, max(0, $percent));
                        
                        $badge_class = 'badge-primary';
                        if ($goal['status'] == 'Delayed') $badge_class = 'badge-danger';
                        if ($goal['status'] == 'Completed') $badge_class = 'badge-success';
                        
                        $priority_badge = 'badge-secondary';
                        if ($goal['priority'] == 'High') $priority_badge = 'badge-danger';
                        if ($goal['priority'] == 'Medium') $priority_badge = 'badge-warning';
                        if ($goal['priority'] == 'Low') $priority_badge = 'badge-info';
                      ?>
                      <tr>
                        <td><?= htmlspecialchars($goal['goal_title']) ?></td>
                        <td><?= htmlspecialchars($goal['kpi_name']) ?></td>
                        <td><?= htmlspecialchars($goal['full_name']) ?></td>
                        <td><?= htmlspecialchars($goal['department'] ?? 'N/A') ?></td>
                        <td style="width: 200px;">
                          <div class="progress progress-xs">
                            <div class="progress-bar progress-bar-<?= ($percent >= 100) ? 'success' : 'primary' ?>" style="width: <?= $percent ?>%"></div>
                          </div>
                          <small><?= number_format($goal['current_progress'], 2) ?> / <?= number_format($goal['target_value'], 2) ?> (<?= number_format($percent, 0) ?>%)</small>
                        </td>
                        <td><span class="badge <?= $priority_badge ?>"><?= htmlspecialchars($goal['priority'] ?? 'Medium') ?></span></td>
                        <td><span class="badge <?= $badge_class ?>"><?= $goal['status'] ?></span></td>
                        <td>
                          <small>
                            S: <?= date('M d, Y', strtotime($goal['start_date'])) ?><br>
                            E: <?= date('M d, Y', strtotime($goal['end_date'])) ?>
                          </small>
                        </td>
                        <td>
                          <button type="button" class="btn btn-sm btn-info edit-goal-btn" 
                                  data-id="<?= $goal['goal_id'] ?>"
                                  data-employee="<?= $goal['employee_id'] ?>"
                                  data-title="<?= htmlspecialchars($goal['goal_title']) ?>"
                                  data-kpi="<?= htmlspecialchars($goal['kpi_name']) ?>"
                                  data-target="<?= $goal['target_value'] ?>"
                                  data-current="<?= $goal['current_progress'] ?>"
                                  data-status="<?= $goal['status'] ?>"
                                  data-priority="<?= htmlspecialchars($goal['priority'] ?? 'Medium') ?>"
                                  data-start="<?= $goal['start_date'] ?>"
                                  data-end="<?= $goal['end_date'] ?>">
                            <i class="fas fa-edit"></i>
                          </button>
                          <form action="" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                            <input type="hidden" name="goal_id" value="<?= $goal['goal_id'] ?>">
                            <button type="submit" name="delete_goal" class="btn btn-sm btn-danger">
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <!-- Edit Goal Modal -->
  <div class="modal fade" id="editGoalModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h5 class="modal-title">Edit Goal</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="" method="POST">
          <div class="modal-body">
            <input type="hidden" name="goal_id" id="edit_goal_id">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Assigned Employee</label>
                  <select name="employee_id" id="edit_employee_id" class="form-control" required>
                    <?php foreach ($employees as $employee): ?>
                      <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['full_name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Goal Title</label>
                  <input type="text" name="goal_title" id="edit_goal_title" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>KPI Name</label>
                  <input type="text" name="kpi_name" id="edit_kpi_name" class="form-control" required>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Status</label>
                  <select name="status" id="edit_status" class="form-control" required>
                    <option value="On Track">On Track</option>
                    <option value="Delayed">Delayed</option>
                    <option value="Completed">Completed</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Priority</label>
                  <select name="priority" id="edit_priority" class="form-control" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Target (%)</label>
                  <input type="number" name="target_value" id="edit_target_value" class="form-control" step="0.01" required>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Current (%)</label>
                  <input type="number" name="current_progress" id="edit_current_progress" class="form-control" step="0.01" required>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Start Date</label>
                  <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>End Date</label>
                  <input type="date" name="end_date" id="edit_end_date" class="form-control" required>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" name="update_goal" class="btn btn-info">Update Goal</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Select2 -->
  <script src="../../assets/plugins/select2/js/select2.full.min.js"></script>
  <script src="../../assets/dist/js/adminlte.js"></script>
  <script>
    $(document).ready(function() {
      // Initialize Select2 Elements
      $('#add_employee_id, #edit_employee_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select Employee',
        allowClear: true
      });

      $('.edit-goal-btn').on('click', function() {
        $('#edit_goal_id').val($(this).data('id'));
        $('#edit_employee_id').val($(this).data('employee'));
        $('#edit_goal_title').val($(this).data('title'));
        $('#edit_kpi_name').val($(this).data('kpi'));
        $('#edit_target_value').val($(this).data('target'));
        $('#edit_current_progress').val($(this).data('current'));
        $('#edit_status').val($(this).data('status'));
        $('#edit_priority').val($(this).data('priority'));
        $('#edit_start_date').val($(this).data('start'));
        $('#edit_end_date').val($(this).data('end'));
        $('#editGoalModal').modal('show');
      });
    });
  </script>
</body>

</html>
