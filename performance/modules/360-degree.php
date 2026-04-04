<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../../auth/database.php";

$theme = $_SESSION['user']['theme'] ?? 'light';
$db = Database::getInstance()->getConnection();

// Handle form submission
$message = '';
$active_tab = $_GET['tab'] ?? 'feedback';

// Handle Feedback Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Insert Feedback
    if (isset($_POST['submit_feedback'])) {
        $employee_id = $_POST['employee_id'];
        $evaluator_type = $_POST['evaluator_type'];
        $rating_input = trim($_POST['rating'] ?? '');
        $rating = ctype_digit($rating_input) ? (int)$rating_input : 0;
        $category = $_POST['category'];
        $comments = $_POST['comments'];
        $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
        $evaluation_date = $_POST['evaluation_date'];
        $competency_ratings = $_POST['competency_ratings'] ?? [];
        $competency_strengths = $_POST['competency_strengths'] ?? [];
        $competency_improvements = $_POST['competency_improvements'] ?? [];
        $competency_examples = $_POST['competency_examples'] ?? [];
        
        // Validate required fields
        if (empty($employee_id)) {
            $message = '<div class="alert alert-danger">Error: Please select an employee.</div>';
        } elseif ($rating_input === '' || $rating < 1 || $rating > 5) {
            $message = '<div class="alert alert-danger">Error: Rating must be between 1 and 5. Please provide a valid rating.</div>';
        } elseif (empty($evaluation_date)) {
            $message = '<div class="alert alert-danger">Error: Please select an evaluation date.</div>';
        }
        
        // Only proceed if no validation errors
        if (!isset($message) || $message === '') {
            $rating = (int)$_POST['rating'];

            try {
            if ($rating !== null) {
                $db->beginTransaction();

                // Insert main feedback
                $sql = "INSERT INTO pm_360_feedback (employee_id, evaluator_type, rating, category, comments, is_anonymous, evaluation_date)
                        VALUES (:employee_id, :evaluator_type, :rating, :category, :comments, :is_anonymous, :evaluation_date)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'employee_id' => $employee_id,
                    'evaluator_type' => $evaluator_type,
                    'rating' => $rating,
                    'category' => $category,
                    'comments' => $comments,
                    'is_anonymous' => $is_anonymous,
                    'evaluation_date' => $evaluation_date
                ]);
                $feedback_id = $db->lastInsertId();

                // Insert competency feedback with validation
                if (!empty($competency_ratings)) {
                    $competency_sql = "INSERT INTO pm_360_competency_feedback (feedback_id, competency_id, rating, strengths, improvement_areas, examples)
                                     VALUES (:feedback_id, :competency_id, :rating, :strengths, :improvements, :examples)";
                    $competency_stmt = $db->prepare($competency_sql);

                    foreach ($competency_ratings as $competency_id => $comp_rating) {
                        $comp_rating = (int)$comp_rating;
                        // Validate competency rating
                        if ($comp_rating < 1 || $comp_rating > 5) {
                            continue; // Skip invalid ratings
                        }
                        $competency_stmt->execute([
                            'feedback_id' => $feedback_id,
                            'competency_id' => $competency_id,
                            'rating' => $comp_rating,
                            'strengths' => $competency_strengths[$competency_id] ?? '',
                            'improvements' => $competency_improvements[$competency_id] ?? '',
                            'examples' => $competency_examples[$competency_id] ?? ''
                        ]);
                    }
                }

                // Generate verification code for anonymous feedback
                if ($is_anonymous) {
                    $verification_code = bin2hex(random_bytes(16));
                    $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));

                    $verification_sql = "INSERT INTO pm_anonymous_verification (feedback_id, verification_code, expires_at)
                                       VALUES (:feedback_id, :code, :expires)";
                    $verification_stmt = $db->prepare($verification_sql);
                    $verification_stmt->execute([
                        'feedback_id' => $feedback_id,
                        'code' => $verification_code,
                        'expires' => $expires_at
                    ]);
                }

                $db->commit();
                $message = '<div class="alert alert-success">Feedback submitted successfully!' .
                          ($is_anonymous ? ' Verification code: <strong>' . $verification_code . '</strong> (save this for verification)' : '') .
                          '</div>';
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
        }
    }

    // Update Feedback
    if (isset($_POST['update_feedback'])) {
        $feedback_id = $_POST['feedback_id'];
        $evaluator_type = $_POST['evaluator_type'];
        $rating = (int)$_POST['rating'];
        $category = $_POST['category'];
        $comments = $_POST['comments'];
        $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
        $evaluation_date = $_POST['evaluation_date'];
        
        // Validate rating
        if ($rating < 1 || $rating > 5) {
            $message = '<div class="alert alert-danger">Error: Rating must be between 1 and 5.</div>';
            $rating = null;
        }

        try {
            if ($rating !== null) {
                $sql = "UPDATE pm_360_feedback SET
                        evaluator_type = :evaluator_type,
                        rating = :rating,
                        category = :category,
                        comments = :comments,
                        is_anonymous = :is_anonymous,
                        evaluation_date = :evaluation_date
                        WHERE feedback_id = :feedback_id";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'evaluator_type' => $evaluator_type,
                    'rating' => $rating,
                    'category' => $category,
                    'comments' => $comments,
                    'is_anonymous' => $is_anonymous,
                    'evaluation_date' => $evaluation_date,
                    'feedback_id' => $feedback_id
                ]);
                $message = '<div class="alert alert-success">Feedback updated successfully!</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }

    // Delete Feedback
    if (isset($_POST['delete_feedback'])) {
        $feedback_id = $_POST['feedback_id'];
        try {
            $sql = "DELETE FROM pm_360_feedback WHERE feedback_id = :feedback_id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['feedback_id' => $feedback_id]);
            $message = '<div class="alert alert-success">Feedback deleted successfully!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }

    // Create Action Plan
    if (isset($_POST['create_action_plan'])) {
        $feedback_id = $_POST['feedback_id'];
        $action_description = $_POST['action_description'];
        $priority = $_POST['priority'];
        $target_date = $_POST['target_date'];
        $assigned_to = $_POST['assigned_to'];

        try {
            $sql = "INSERT INTO pm_feedback_action_plans (feedback_id, action_description, priority, target_date, assigned_to)
                    VALUES (:feedback_id, :description, :priority, :target_date, :assigned_to)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'feedback_id' => $feedback_id,
                'description' => $action_description,
                'priority' => $priority,
                'target_date' => $target_date,
                'assigned_to' => $assigned_to
            ]);
            $message = '<div class="alert alert-success">Action plan created successfully!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }

    // Update Action Plan Status
    if (isset($_POST['update_action_status'])) {
        $action_plan_id = $_POST['action_plan_id'];
        $status = $_POST['status'];
        $progress_notes = $_POST['progress_notes'];

        try {
            $sql = "UPDATE pm_feedback_action_plans SET status = :status, progress_notes = :notes, updated_at = CURRENT_TIMESTAMP
                    WHERE action_plan_id = :action_id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'status' => $status,
                'notes' => $progress_notes,
                'action_id' => $action_plan_id
            ]);
            $message = '<div class="alert alert-success">Action plan updated successfully!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }

    // Verify Anonymous Feedback
    if (isset($_POST['verify_anonymous'])) {
        $verification_code = $_POST['verification_code'];

        try {
            $sql = "UPDATE pm_anonymous_verification SET is_verified = 1, verified_at = CURRENT_TIMESTAMP
                    WHERE verification_code = :code AND expires_at > CURRENT_TIMESTAMP AND is_verified = 0";
            $stmt = $db->prepare($sql);
            $stmt->execute(['code' => $verification_code]);

            if ($stmt->rowCount() > 0) {
                $message = '<div class="alert alert-success">Anonymous feedback verified successfully!</div>';
            } else {
                $message = '<div class="alert alert-warning">Invalid or expired verification code.</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }

    // Create Calibration Session
    if (isset($_POST['create_calibration'])) {
        $session_name = $_POST['session_name'];
        $session_date = $_POST['session_date'];
        $facilitator_id = $_POST['facilitator_id'];
        $department = $_POST['department'];
        $participant_ids = $_POST['participant_ids'] ?? [];

        try {
            $db->beginTransaction();

            $sql = "INSERT INTO pm_calibration_sessions (session_name, session_date, facilitator_id, department)
                    VALUES (:name, :date, :facilitator, :department)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'name' => $session_name,
                'date' => $session_date,
                'facilitator' => $facilitator_id,
                'department' => $department
            ]);
            $calibration_id = $db->lastInsertId();

            // Add participants
            if (!empty($participant_ids)) {
                $participant_sql = "INSERT INTO pm_calibration_participants (calibration_id, employee_id, role)
                                  VALUES (:calibration_id, :employee_id, 'Participant')";
                $participant_stmt = $db->prepare($participant_sql);

                foreach ($participant_ids as $employee_id) {
                    $participant_stmt->execute([
                        'calibration_id' => $calibration_id,
                        'employee_id' => $employee_id
                    ]);
                }
            }

            $db->commit();
            $message = '<div class="alert alert-success">Calibration session created successfully!</div>';
        } catch (PDOException $e) {
            $db->rollBack();
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// Fetch data for dropdowns and displays
// Fetch employees
$stmt = $db->query("SELECT employee_id as id, full_name FROM employees WHERE employment_status = 'Active' ORDER BY full_name");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch competencies
$stmt = $db->query("SELECT * FROM pm_competencies WHERE is_active = 1 ORDER BY competency_name");
$competencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch competency indicators
$competency_indicators = [];
foreach ($competencies as $competency) {
    $stmt = $db->prepare("SELECT * FROM pm_competency_indicators WHERE competency_id = ? AND is_active = 1 ORDER BY level");
    $stmt->execute([$competency['competency_id']]);
    $competency_indicators[$competency['competency_id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch feedback list with search and filters
$search = $_GET['search'] ?? '';
$filter_category = $_GET['filter_category'] ?? '';
$filter_evaluator = $_GET['filter_evaluator'] ?? '';
$filter_employee = $_GET['filter_employee'] ?? '';

$query = "
    SELECT f.*, e.full_name,
           COUNT(ap.action_plan_id) as action_plans_count,
           AVG(cf.rating) as avg_competency_rating,
           COUNT(DISTINCT cf.competency_id) as competencies_rated
    FROM pm_360_feedback f
    JOIN employees e ON f.employee_id = e.employee_id
    LEFT JOIN pm_feedback_action_plans ap ON f.feedback_id = ap.feedback_id
    LEFT JOIN pm_360_competency_feedback cf ON f.feedback_id = cf.feedback_id
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $query .= " AND e.full_name LIKE :search";
    $params['search'] = "%$search%";
}

if (!empty($filter_category)) {
    $query .= " AND f.category = :category";
    $params['category'] = $filter_category;
}

if (!empty($filter_evaluator)) {
    $query .= " AND f.evaluator_type = :evaluator";
    $params['evaluator'] = $filter_evaluator;
}

if (!empty($filter_employee)) {
    $query .= " AND f.employee_id = :employee";
    $params['employee'] = $filter_employee;
}

$query .= " GROUP BY f.feedback_id ORDER BY f.evaluation_date DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$feedback_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch action plans
$stmt = $db->query("
    SELECT ap.*, f.employee_id, e.full_name as employee_name, ae.full_name as assigned_name
    FROM pm_feedback_action_plans ap
    JOIN pm_360_feedback f ON ap.feedback_id = f.feedback_id
    JOIN employees e ON f.employee_id = e.employee_id
    JOIN employees ae ON ap.assigned_to = ae.employee_id
    ORDER BY ap.target_date ASC
");
$action_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch calibration sessions
$stmt = $db->query("
    SELECT cs.*, e.full_name as facilitator_name, COUNT(cp.participant_id) as participant_count
    FROM pm_calibration_sessions cs
    JOIN employees e ON cs.facilitator_id = e.employee_id
    LEFT JOIN pm_calibration_participants cp ON cs.calibration_id = cp.calibration_id
    GROUP BY cs.calibration_id
    ORDER BY cs.session_date DESC
");
$calibration_sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate feedback analytics
function generateFeedbackAnalytics($employee_id, $db) {
    // Get recent feedback (last 6 months)
    $six_months_ago = date('Y-m-d', strtotime('-6 months'));

    $stmt = $db->prepare("
        SELECT f.*, cf.rating as competency_rating, cf.strengths, cf.improvement_areas
        FROM pm_360_feedback f
        LEFT JOIN pm_360_competency_feedback cf ON f.feedback_id = cf.feedback_id
        WHERE f.employee_id = ? AND f.evaluation_date >= ?
        ORDER BY f.evaluation_date DESC
    ");
    $stmt->execute([$employee_id, $six_months_ago]);
    $recent_feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($recent_feedback)) {
        return null;
    }

    // Calculate analytics
    $total_ratings = count($recent_feedback);
    $avg_rating = array_sum(array_column($recent_feedback, 'rating')) / $total_ratings;

    // Calculate trend (simple linear regression on ratings over time)
    $ratings_by_date = [];
    foreach ($recent_feedback as $feedback) {
        $ratings_by_date[$feedback['evaluation_date']][] = $feedback['rating'];
    }
    ksort($ratings_by_date);

    $trend_data = [];
    foreach ($ratings_by_date as $date => $ratings) {
        $trend_data[] = array_sum($ratings) / count($ratings);
    }

    $trend = 'Stable';
    if (count($trend_data) >= 2) {
        $first_half = array_slice($trend_data, 0, floor(count($trend_data) / 2));
        $second_half = array_slice($trend_data, floor(count($trend_data) / 2));

        $first_avg = array_sum($first_half) / count($first_half);
        $second_avg = array_sum($second_half) / count($second_half);

        if ($second_avg > $first_avg + 0.2) {
            $trend = 'Improving';
        } elseif ($second_avg < $first_avg - 0.2) {
            $trend = 'Declining';
        }
    }

    // Count strengths and improvement areas
    $strengths_count = 0;
    $improvements_count = 0;
    $competency_scores = [];

    foreach ($recent_feedback as $feedback) {
        if (!empty($feedback['strengths'])) $strengths_count++;
        if (!empty($feedback['improvement_areas'])) $improvements_count++;
        if ($feedback['competency_rating']) {
            $competency_scores[] = $feedback['competency_rating'];
        }
    }

    // Calculate consistency score (lower variance = higher consistency)
    $consistency_score = 0;
    if (count($competency_scores) > 1) {
        $mean = array_sum($competency_scores) / count($competency_scores);
        $variance = 0;
        foreach ($competency_scores as $score) {
            $variance += pow($score - $mean, 2);
        }
        $variance /= count($competency_scores);
        $consistency_score = max(0, 100 - ($variance * 20)); // Convert to 0-100 scale
    }

    return [
        'overall_rating_trend' => $trend,
        'strengths_count' => $strengths_count,
        'improvement_areas_count' => $improvements_count,
        'consistency_score' => round($consistency_score, 2),
        'total_feedback' => $total_ratings,
        'average_rating' => round($avg_rating, 2)
    ];
}

// Get analytics for all employees with recent feedback
$analytics_data = [];
foreach ($employees as $employee) {
    $analytics = generateFeedbackAnalytics($employee['id'], $db);
    if ($analytics) {
        $analytics_data[$employee['id']] = $analytics;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>360-Degree Feedback | Performance Management</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- Select2 -->
  <link rel="stylesheet" href="../../assets/plugins/select2/css/select2.min.css" />
  <link rel="stylesheet" href="../../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <!-- jQuery UI -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="../custom.css" />
  <style>
    .ui-autocomplete {
      z-index: 1050;
      max-height: 200px;
      overflow-y: auto;
      overflow-x: hidden;
    }

    /* Enhanced 360-Degree Feedback Styles */
    .competency-card {
      transition: all 0.3s ease;
      border: 1px solid #dee2e6;
      border-radius: 8px;
    }

    .competency-card:hover {
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transform: translateY(-2px);
    }

    .rating-stars {
      color: #ffc107;
      font-size: 1.2rem;
    }

    .rating-stars .far {
      color: #dee2e6;
    }

    .analytics-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .trend-improving { color: #28a745; }
    .trend-stable { color: #ffc107; }
    .trend-declining { color: #dc3545; }

    .action-plan-card {
      border-left: 4px solid;
    }

    .action-plan-card.high { border-left-color: #dc3545; }
    .action-plan-card.medium { border-left-color: #ffc107; }
    .action-plan-card.low { border-left-color: #28a745; }

    .calibration-timeline {
      position: relative;
      padding-left: 30px;
    }

    .calibration-timeline::before {
      content: '';
      position: absolute;
      left: 15px;
      top: 0;
      bottom: 0;
      width: 2px;
      background: #dee2e6;
    }

    .calibration-item {
      position: relative;
      margin-bottom: 20px;
    }

    .calibration-item::before {
      content: '';
      position: absolute;
      left: -22px;
      top: 8px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #007bff;
    }

    .competency-rating {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .competency-rating .badge {
      min-width: 60px;
    }

    .feedback-verification {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
    }

    .tab-content {
      padding: 20px 0;
    }

    .nav-tabs .nav-link {
      border: none;
      border-bottom: 3px solid transparent;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .nav-tabs .nav-link.active {
      border-bottom-color: #007bff;
      color: #007bff;
      background-color: rgba(0, 123, 255, 0.1);
    }

    .card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .progress {
      height: 8px;
      border-radius: 4px;
    }

    .btn-group-sm .btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }
  </style>
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

      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <span class="navbar-text text-white">
            <i class="fas fa-brain mr-1"></i>AI-Powered 360° Feedback
          </span>
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
              <a href="360-degree.php" class="nav-link active">
                <i class="nav-icon fas fa-chart-pie"></i>
                <p>360° Feedback</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Appraisals&review.php" class="nav-link">
                <i class="nav-icon fas fa-edit"></i>
                <p>Appraisals & Review</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Goal&KPI.php" class="nav-link">
                <i class="nav-icon fas fa-target"></i>
                <p>Goals & KPI</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Performancereport.php" class="nav-link">
                <i class="nav-icon fas fa-file-alt"></i>
                <p>Performance Reports</p>
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
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">
                <i class="fas fa-users-cog mr-2"></i>360-Degree Feedback
                <small class="text-muted">Advanced AI-Powered System</small>
              </h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../performance.php">Performance</a></li>
                <li class="breadcrumb-item active">360° Feedback</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <?php if ($message): ?>
            <div class="row">
              <div class="col-12">
                <?= $message ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- Navigation Tabs -->
          <div class="row">
            <div class="col-12">
              <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                  <ul class="nav nav-tabs" id="feedback-tabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link <?= $active_tab === 'feedback' ? 'active' : '' ?>" id="feedback-tab" data-toggle="pill" href="#feedback" role="tab">
                        <i class="fas fa-comment mr-2"></i>Submit Feedback
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link <?= $active_tab === 'analytics' ? 'active' : '' ?>" id="analytics-tab" data-toggle="pill" href="#analytics" role="tab">
                        <i class="fas fa-chart-line mr-2"></i>Analytics & Trends
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link <?= $active_tab === 'action-plans' ? 'active' : '' ?>" id="action-plans-tab" data-toggle="pill" href="#action-plans" role="tab">
                        <i class="fas fa-tasks mr-2"></i>Action Plans
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link <?= $active_tab === 'calibration' ? 'active' : '' ?>" id="calibration-tab" data-toggle="pill" href="#calibration" role="tab">
                        <i class="fas fa-balance-scale mr-2"></i>Calibration
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link <?= $active_tab === 'verification' ? 'active' : '' ?>" id="verification-tab" data-toggle="pill" href="#verification" role="tab">
                        <i class="fas fa-shield-alt mr-2"></i>Verification
                      </a>
                    </li>
                  </ul>
                </div>

                <div class="card-body">
                  <div class="tab-content" id="feedback-tabContent">

                    <!-- Feedback Submission Tab -->
                    <div class="tab-pane fade <?= $active_tab === 'feedback' ? 'show active' : '' ?>" id="feedback" role="tabpanel">
                      <div class="row">
                        <div class="col-md-8">
                          <div class="card">
                            <div class="card-header">
                              <h3 class="card-title">
                                <i class="fas fa-plus-circle mr-2"></i>Submit 360° Feedback
                              </h3>
                            </div>
                            <form method="POST" id="feedbackForm">
                              <div class="card-body">
                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label for="employee_id">Employee to Evaluate</label>
                                      <select class="form-control select2" id="employee_id" name="employee_id" required>
                                        <option value="">Select Employee</option>
                                        <?php foreach ($employees as $employee): ?>
                                          <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['full_name']) ?></option>
                                        <?php endforeach; ?>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label for="evaluator_type">Your Relationship</label>
                                      <select class="form-control" id="evaluator_type" name="evaluator_type" required>
                                        <option value="">Select Relationship</option>
                                        <option value="Manager">Manager</option>
                                        <option value="Peer">Peer</option>
                                        <option value="Subordinate">Subordinate</option>
                                        <option value="Self">Self-Evaluation</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label for="category">Category</label>
                                      <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Communication">Communication</option>
                                        <option value="Teamwork">Teamwork</option>
                                        <option value="Leadership">Leadership</option>
                                        <option value="Performance">Performance</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label for="evaluation_date">Evaluation Date</label>
                                      <input type="date" class="form-control" id="evaluation_date" name="evaluation_date"
                                             value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label for="rating">Overall Rating</label>
                                  <div class="rating-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                      <i class="far fa-star" data-rating="<?= $i ?>"></i>
                                    <?php endfor; ?>
                                    <input type="hidden" id="rating" name="rating" required>
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label for="comments">Comments & Feedback</label>
                                  <textarea class="form-control" id="comments" name="comments" rows="4"
                                            placeholder="Provide detailed feedback, examples, and suggestions for improvement..."></textarea>
                                </div>

                                <!-- Competency-Based Feedback -->
                                <div class="form-group">
                                  <label>Competency Assessment</label>
                                  <div id="competency-feedback">
                                    <?php foreach ($competencies as $competency): ?>
                                      <div class="competency-card p-3 mb-3">
                                        <h6 class="mb-3">
                                          <i class="fas fa-certificate mr-2 text-primary"></i>
                                          <?= htmlspecialchars($competency['competency_name']) ?>
                                          <small class="text-muted">(<?= $competency['category'] ?>)</small>
                                        </h6>

                                        <div class="row">
                                          <div class="col-md-4">
                                            <label class="form-label">Rating (1-5)</label>
                                            <select class="form-control form-control-sm" name="competency_ratings[<?= $competency['competency_id'] ?>]">
                                              <option value="">Not Rated</option>
                                              <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <option value="<?= $i ?>"><?= $i ?> - <?= ['Beginner', 'Developing', 'Intermediate', 'Advanced', 'Expert'][$i-1] ?></option>
                                              <?php endfor; ?>
                                            </select>
                                          </div>
                                          <div class="col-md-4">
                                            <label class="form-label">Strengths</label>
                                            <textarea class="form-control form-control-sm" name="competency_strengths[<?= $competency['competency_id'] ?>]"
                                                      rows="2" placeholder="What are their strengths in this area?"></textarea>
                                          </div>
                                          <div class="col-md-4">
                                            <label class="form-label">Areas for Improvement</label>
                                            <textarea class="form-control form-control-sm" name="competency_improvements[<?= $competency['competency_id'] ?>]"
                                                      rows="2" placeholder="What could they improve?"></textarea>
                                          </div>
                                        </div>

                                        <div class="form-group mt-2">
                                          <label class="form-label">Examples/Observations</label>
                                          <textarea class="form-control form-control-sm" name="competency_examples[<?= $competency['competency_id'] ?>]"
                                                    rows="2" placeholder="Specific examples of their performance in this competency..."></textarea>
                                        </div>

                                        <!-- Behavioral Indicators -->
                                        <details class="mt-2">
                                          <summary class="text-primary" style="cursor: pointer;">
                                            <i class="fas fa-info-circle mr-1"></i>View Behavioral Indicators
                                          </summary>
                                          <div class="mt-2 pl-3">
                                            <?php if (isset($competency_indicators[$competency['competency_id']])): ?>
                                              <?php foreach ($competency_indicators[$competency['competency_id']] as $indicator): ?>
                                                <div class="mb-1">
                                                  <span class="badge badge-<?= $indicator['level'] === 'Expert' ? 'success' : ($indicator['level'] === 'Advanced' ? 'info' : ($indicator['level'] === 'Intermediate' ? 'warning' : 'secondary')) ?>">
                                                    <?= $indicator['level'] ?>
                                                  </span>
                                                  <small class="text-muted ml-2"><?= htmlspecialchars($indicator['indicator_text']) ?></small>
                                                </div>
                                              <?php endforeach; ?>
                                            <?php endif; ?>
                                          </div>
                                        </details>
                                      </div>
                                    <?php endforeach; ?>
                                  </div>
                                </div>

                                <div class="form-group">
                                  <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="is_anonymous" name="is_anonymous" value="1">
                                    <label for="is_anonymous" class="custom-control-label">
                                      Submit anonymously
                                      <small class="text-muted d-block">Your identity will be protected, but you'll receive a verification code</small>
                                    </label>
                                  </div>
                                </div>
                              </div>

                              <div class="card-footer">
                                <button type="submit" name="submit_feedback" class="btn btn-primary">
                                  <i class="fas fa-paper-plane mr-2"></i>Submit Feedback
                                </button>
                                <button type="reset" class="btn btn-secondary ml-2">
                                  <i class="fas fa-undo mr-2"></i>Reset Form
                                </button>
                              </div>
                            </form>
                          </div>
                        </div>

                        <div class="col-md-4">
                          <!-- Quick Stats -->
                          <div class="card">
                            <div class="card-header">
                              <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-2"></i>Feedback Overview
                              </h3>
                            </div>
                            <div class="card-body">
                              <div class="info-box bg-light">
                                <div class="info-box-content">
                                  <span class="info-box-text">Total Feedback</span>
                                  <span class="info-box-number"><?= count($feedback_list) ?></span>
                                </div>
                              </div>

                              <div class="info-box bg-primary">
                                <div class="info-box-content">
                                  <span class="info-box-text">Anonymous</span>
                                  <span class="info-box-number">
                                    <?= count(array_filter($feedback_list, fn($f) => $f['is_anonymous'])) ?>
                                  </span>
                                </div>
                              </div>

                              <div class="info-box bg-success">
                                <div class="info-box-content">
                                  <span class="info-box-text">With Action Plans</span>
                                  <span class="info-box-number">
                                    <?= count(array_filter($feedback_list, fn($f) => $f['action_plans_count'] > 0)) ?>
                                  </span>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Recent Activity -->
                          <div class="card mt-3">
                            <div class="card-header">
                              <h3 class="card-title">
                                <i class="fas fa-clock mr-2"></i>Recent Activity
                              </h3>
                            </div>
                            <div class="card-body p-0">
                              <?php
                              $recent_feedback = array_slice($feedback_list, 0, 5);
                              foreach ($recent_feedback as $feedback):
                              ?>
                                <div class="p-2 border-bottom">
                                  <div class="d-flex justify-content-between">
                                    <small class="text-muted">
                                      <strong><?= htmlspecialchars($feedback['full_name']) ?></strong>
                                    </small>
                                    <small class="text-muted">
                                      <?= date('M d', strtotime($feedback['evaluation_date'])) ?>
                                    </small>
                                  </div>
                                  <div class="rating-stars mt-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                      <i class="fa<?= $i <= $feedback['rating'] ? 's' : 'r' ?> fa-star"></i>
                                    <?php endfor; ?>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Analytics & Trends Tab -->
                    <div class="tab-pane fade <?= $active_tab === 'analytics' ? 'show active' : '' ?>" id="analytics" role="tabpanel">
                      <div class="row">
                        <?php foreach ($analytics_data as $employee_id => $analytics): ?>
                          <?php
                          $employee = array_filter($employees, fn($e) => $e['id'] == $employee_id);
                          $employee = reset($employee);
                          ?>
                          <div class="col-md-6 mb-4">
                            <div class="card analytics-card">
                              <div class="card-header">
                                <h5 class="card-title mb-0">
                                  <i class="fas fa-user mr-2"></i><?= htmlspecialchars($employee['full_name']) ?>
                                </h5>
                              </div>
                              <div class="card-body">
                                <div class="row">
                                  <div class="col-6">
                                    <div class="text-center mb-3">
                                      <h3 class="mb-0"><?= $analytics['average_rating'] ?>/5.0</h3>
                                      <small>Average Rating</small>
                                    </div>
                                  </div>
                                  <div class="col-6">
                                    <div class="text-center mb-3">
                                      <h3 class="mb-0 trend-<?= strtolower($analytics['overall_rating_trend']) ?>">
                                        <i class="fas fa-arrow-<?= $analytics['overall_rating_trend'] === 'Improving' ? 'up' : ($analytics['overall_rating_trend'] === 'Declining' ? 'down' : 'right') ?> mr-1"></i>
                                        <?= $analytics['overall_rating_trend'] ?>
                                      </h3>
                                      <small>Trend</small>
                                    </div>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-6">
                                    <div class="mb-2">
                                      <small class="text-white-50">Feedback Count</small>
                                      <div class="h5 mb-0"><?= $analytics['total_feedback'] ?></div>
                                    </div>
                                  </div>
                                  <div class="col-6">
                                    <div class="mb-2">
                                      <small class="text-white-50">Consistency Score</small>
                                      <div class="h5 mb-0"><?= $analytics['consistency_score'] ?>%</div>
                                    </div>
                                  </div>
                                </div>

                                <div class="progress mt-3" style="height: 6px;">
                                  <div class="progress-bar bg-white" style="width: <?= $analytics['consistency_score'] ?>%"></div>
                                </div>
                                <small class="text-white-50 mt-1 d-block">Rating Consistency</small>
                              </div>
                            </div>
                          </div>
                        <?php endforeach; ?>

                        <?php if (empty($analytics_data)): ?>
                          <div class="col-12">
                            <div class="alert alert-info">
                              <i class="fas fa-info-circle mr-2"></i>
                              <strong>No Analytics Available</strong><br>
                              Analytics will appear once employees have received multiple feedback submissions over time.
                            </div>
                          </div>
                        <?php endif; ?>
                      </div>

                      <!-- Feedback Trends Chart -->
                      <div class="row mt-4">
                        <div class="col-12">
                          <div class="card">
                            <div class="card-header">
                              <h3 class="card-title">
                                <i class="fas fa-chart-line mr-2"></i>Feedback Trends Overview
                              </h3>
                            </div>
                            <div class="card-body">
                              <canvas id="feedbackTrendsChart" style="height: 300px;"></canvas>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Action Plans Tab -->
                    <div class="tab-pane fade <?= $active_tab === 'action-plans' ? 'show active' : '' ?>" id="action-plans" role="tabpanel">
                      <div class="row">
                        <div class="col-md-8">
                          <div class="card">
                            <div class="card-header">
                              <h3 class="card-title">
                                <i class="fas fa-tasks mr-2"></i>Feedback Action Plans
                              </h3>
                            </div>
                            <div class="card-body">
                              <?php if (!empty($action_plans)): ?>
                                <?php foreach ($action_plans as $plan): ?>
                                  <div class="action-plan-card card mb-3 <?= strtolower($plan['priority']) ?>">
                                    <div class="card-body">
                                      <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">
                                          <?= htmlspecialchars($plan['action_description']) ?>
                                        </h6>
                                        <span class="badge badge-<?= $plan['priority'] === 'High' ? 'danger' : ($plan['priority'] === 'Medium' ? 'warning' : 'success') ?>">
                                          <?= $plan['priority'] ?> Priority
                                        </span>
                                      </div>

                                      <div class="row mb-2">
                                        <div class="col-md-6">
                                          <small class="text-muted">
                                            <i class="fas fa-user mr-1"></i>Assigned to: <?= htmlspecialchars($plan['assigned_name']) ?>
                                          </small>
                                        </div>
                                        <div class="col-md-6">
                                          <small class="text-muted">
                                            <i class="fas fa-calendar mr-1"></i>Due: <?= date('M d, Y', strtotime($plan['target_date'])) ?>
                                          </small>
                                        </div>
                                      </div>

                                      <div class="mb-2">
                                        <small class="text-muted">Status:</small>
                                        <span class="badge badge-<?= $plan['status'] === 'Completed' ? 'success' : ($plan['status'] === 'In Progress' ? 'primary' : 'secondary') ?>">
                                          <?= $plan['status'] ?>
                                        </span>
                                      </div>

                                      <?php if (!empty($plan['progress_notes'])): ?>
                                        <div class="alert alert-light border mt-2">
                                          <small><strong>Progress Notes:</strong> <?= htmlspecialchars($plan['progress_notes']) ?></small>
                                        </div>
                                      <?php endif; ?>

                                      <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editActionPlan(<?= $plan['action_plan_id'] ?>)">
                                          <i class="fas fa-edit mr-1"></i>Update
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" onclick="markCompleted(<?= $plan['action_plan_id'] ?>)">
                                          <i class="fas fa-check mr-1"></i>Mark Complete
                                        </button>
                                      </div>
                                    </div>
                                  </div>
                                <?php endforeach; ?>
                              <?php else: ?>
                                <div class="alert alert-info">
                                  <i class="fas fa-info-circle mr-2"></i>
                                  <strong>No Action Plans</strong><br>
                                  Action plans will appear here once they are created from feedback submissions.
                                </div>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-4">
                          <!-- Create Action Plan -->
                          <div class="card">
                            <div class="card-header">
                              <h3 class="card-title">
                                <i class="fas fa-plus-circle mr-2"></i>Create Action Plan
                              </h3>
                            </div>
                            <form method="POST">
                              <div class="card-body">
                                <div class="form-group">
                                  <label for="feedback_select">Select Feedback</label>
                                  <select class="form-control form-control-sm" id="feedback_select" name="feedback_id" required>
                                    <option value="">Choose feedback...</option>
                                    <?php foreach ($feedback_list as $feedback): ?>
                                      <option value="<?= $feedback['feedback_id'] ?>">
                                        <?= htmlspecialchars($feedback['full_name']) ?> - <?= $feedback['category'] ?> (<?= date('M d', strtotime($feedback['evaluation_date'])) ?>)
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>

                                <div class="form-group">
                                  <label for="action_description">Action Description</label>
                                  <textarea class="form-control form-control-sm" id="action_description" name="action_description"
                                            rows="3" placeholder="Describe the specific action to be taken..." required></textarea>
                                </div>

                                <div class="form-group">
                                  <label for="action_priority">Priority</label>
                                  <select class="form-control form-control-sm" id="action_priority" name="priority" required>
                                    <option value="Low">Low</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="High">High</option>
                                  </select>
                                </div>

                                <div class="form-group">
                                  <label for="target_date">Target Date</label>
                                  <input type="date" class="form-control form-control-sm" id="target_date" name="target_date"
                                         value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                                </div>

                                <div class="form-group">
                                  <label for="assigned_to">Assign To</label>
                                  <select class="form-control form-control-sm" id="assigned_to" name="assigned_to" required>
                                    <option value="">Select person...</option>
                                    <?php foreach ($employees as $employee): ?>
                                      <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['full_name']) ?></option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                              </div>

                              <div class="card-footer">
                                <button type="submit" name="create_action_plan" class="btn btn-primary btn-sm btn-block">
                                  <i class="fas fa-plus mr-2"></i>Create Action Plan
                                </button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Calibration Tab -->
                    <div class="tab-pane fade <?= $active_tab === 'calibration' ? 'show active' : '' ?>" id="calibration" role="tabpanel">
                      <div class="row">
                        <div class="col-md-8">
                          <div class="card">
                            <div class="card-header">
                              <h3 class="card-title">
                                <i class="fas fa-balance-scale mr-2"></i>Calibration Sessions
                              </h3>
                            </div>
                            <div class="card-body">
                              <div class="calibration-timeline">
                                <?php if (!empty($calibration_sessions)): ?>
                                  <?php foreach ($calibration_sessions as $session): ?>
                                    <div class="calibration-item">
                                      <div class="card">
                                        <div class="card-body">
                                          <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">
                                              <?= htmlspecialchars($session['session_name']) ?>
                                            </h6>
                                            <span class="badge badge-<?= $session['status'] === 'Completed' ? 'success' : ($session['status'] === 'In Progress' ? 'primary' : 'secondary') ?>">
                                              <?= $session['status'] ?>
                                            </span>
                                          </div>

                                          <div class="row mb-2">
                                            <div class="col-md-6">
                                              <small class="text-muted">
                                                <i class="fas fa-calendar mr-1"></i>Date: <?= date('M d, Y', strtotime($session['session_date'])) ?>
                                              </small>
                                            </div>
                                            <div class="col-md-6">
                                              <small class="text-muted">
                                                <i class="fas fa-user mr-1"></i>Facilitator: <?= htmlspecialchars($session['facilitator_name']) ?>
                                              </small>
                                            </div>
                                          </div>

                                          <div class="mb-2">
                                            <small class="text-muted">
                                              <i class="fas fa-users mr-1"></i>Participants: <?= $session['participant_count'] ?>
                                              <?php if (!empty($session['department'])): ?>
                                                | Department: <?= htmlspecialchars($session['department']) ?>
                                              <?php endif; ?>
                                            </small>
                                          </div>

                                          <?php if (!empty($session['notes'])): ?>
                                            <div class="alert alert-light border mt-2">
                                              <small><strong>Notes:</strong> <?= htmlspecialchars($session['notes']) ?></small>
                                            </div>
                                          <?php endif; ?>
                                        </div>
                                      </div>
                                    </div>
                                  <?php endforeach; ?>
                                <?php else: ?>
                                  <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>No Calibration Sessions</strong><br>
                                    Create your first calibration session to ensure rating consistency across evaluators.
                                  </div>
                                <?php endif; ?>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-4">
                          <!-- Create Calibration Session -->
                          <div class="card">
                            <div class="card-header">
                              <h3 class="card-title">
                                <i class="fas fa-plus-circle mr-2"></i>New Calibration Session
                              </h3>
                            </div>
                            <form method="POST">
                              <div class="card-body">
                                <div class="form-group">
                                  <label for="session_name">Session Name</label>
                                  <input type="text" class="form-control form-control-sm" id="session_name" name="session_name"
                                         placeholder="e.g., Q4 Performance Calibration" required>
                                </div>

                                <div class="form-group">
                                  <label for="session_date">Session Date</label>
                                  <input type="date" class="form-control form-control-sm" id="session_date" name="session_date"
                                         value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                                </div>

                                <div class="form-group">
                                  <label for="facilitator_id">Facilitator</label>
                                  <select class="form-control form-control-sm" id="facilitator_id" name="facilitator_id" required>
                                    <option value="">Select facilitator...</option>
                                    <?php foreach ($employees as $employee): ?>
                                      <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['full_name']) ?></option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>

                                <div class="form-group">
                                  <label for="department">Department (Optional)</label>
                                  <input type="text" class="form-control form-control-sm" id="department" name="department"
                                         placeholder="e.g., IT Department">
                                </div>

                                <div class="form-group">
                                  <label for="participants">Participants</label>
                                  <select class="form-control select2" id="participants" name="participant_ids[]" multiple required>
                                    <?php foreach ($employees as $employee): ?>
                                      <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['full_name']) ?></option>
                                    <?php endforeach; ?>
                                  </select>
                                  <small class="text-muted">Select managers and supervisors who will participate in calibration</small>
                                </div>
                              </div>

                              <div class="card-footer">
                                <button type="submit" name="create_calibration" class="btn btn-primary btn-sm btn-block">
                                  <i class="fas fa-plus mr-2"></i>Create Session
                                </button>
                              </div>
                            </form>
                          </div>

                          <!-- Calibration Guidelines -->
                          <div class="card mt-3">
                            <div class="card-header">
                              <h3 class="card-title">
                                <i class="fas fa-lightbulb mr-2"></i>Calibration Tips
                              </h3>
                            </div>
                            <div class="card-body p-0">
                              <div class="p-3 border-bottom">
                                <h6 class="text-primary mb-2">Ensure Consistency</h6>
                                <small class="text-muted">Review ratings together to align on performance standards</small>
                              </div>
                              <div class="p-3 border-bottom">
                                <h6 class="text-primary mb-2">Use Examples</h6>
                                <small class="text-muted">Discuss specific examples to calibrate understanding</small>
                              </div>
                              <div class="p-3">
                                <h6 class="text-primary mb-2">Document Decisions</h6>
                                <small class="text-muted">Record calibration discussions for future reference</small>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Verification Tab -->
                    <div class="tab-pane fade <?= $active_tab === 'verification' ? 'show active' : '' ?>" id="verification" role="tabpanel">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="card feedback-verification">
                            <div class="card-header">
                              <h3 class="card-title">
                                <i class="fas fa-shield-alt mr-2"></i>Anonymous Feedback Verification
                              </h3>
                            </div>
                            <div class="card-body">
                              <p class="mb-3">Verify anonymous feedback submissions to ensure authenticity and build trust in the system.</p>

                              <form method="POST">
                                <div class="form-group">
                                  <label for="verification_code" class="text-white">Verification Code</label>
                                  <input type="text" class="form-control" id="verification_code" name="verification_code"
                                         placeholder="Enter verification code" required>
                                  <small class="text-white-50">Enter the code you received when submitting anonymous feedback</small>
                                </div>

                                <button type="submit" name="verify_anonymous" class="btn btn-light btn-block">
                                  <i class="fas fa-check-circle mr-2"></i>Verify Feedback
                                </button>
                              </form>
                            </div>
                          </div>

                          <!-- Verification Stats -->
                          <div class="card mt-3">
                            <div class="card-header">
                              <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-2"></i>Verification Status
                              </h3>
                            </div>
                            <div class="card-body">
                              <?php
                              $total_anonymous = count(array_filter($feedback_list, fn($f) => $f['is_anonymous']));
                              $verified_count = 0; // Would need to query verification table
                              ?>

                              <div class="info-box bg-info">
                                <div class="info-box-content">
                                  <span class="info-box-text">Anonymous Feedback</span>
                                  <span class="info-box-number"><?= $total_anonymous ?></span>
                                </div>
                              </div>

                              <div class="info-box bg-success">
                                <div class="info-box-content">
                                  <span class="info-box-text">Verified</span>
                                  <span class="info-box-number"><?= $verified_count ?></span>
                                </div>
                              </div>

                              <div class="info-box bg-warning">
                                <div class="info-box-content">
                                  <span class="info-box-text">Pending Verification</span>
                                  <span class="info-box-number"><?= $total_anonymous - $verified_count ?></span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <!-- Feedback List with Verification Status -->
                          <div class="card">
                            <div class="card-header">
                              <h3 class="card-title">
                                <i class="fas fa-list mr-2"></i>Recent Anonymous Feedback
                              </h3>
                            </div>
                            <div class="card-body p-0">
                              <?php
                              $anonymous_feedback = array_filter($feedback_list, fn($f) => $f['is_anonymous']);
                              $recent_anonymous = array_slice($anonymous_feedback, 0, 10);

                              if (!empty($recent_anonymous)):
                                foreach ($recent_anonymous as $feedback):
                              ?>
                                <div class="p-3 border-bottom">
                                  <div class="d-flex justify-content-between align-items-start mb-2">
                                    <small class="text-muted">
                                      <strong>Anonymous</strong> → <?= htmlspecialchars($feedback['full_name']) ?>
                                    </small>
                                    <small class="text-muted">
                                      <?= date('M d, Y', strtotime($feedback['evaluation_date'])) ?>
                                    </small>
                                  </div>

                                  <div class="mb-2">
                                    <span class="badge badge-secondary mr-2"><?= $feedback['category'] ?></span>
                                    <div class="rating-stars d-inline">
                                      <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa<?= $i <= $feedback['rating'] ? 's' : 'r' ?> fa-star text-warning"></i>
                                      <?php endfor; ?>
                                    </div>
                                  </div>

                                  <p class="mb-2 small text-muted">
                                    <?= htmlspecialchars(substr($feedback['comments'], 0, 100)) ?>...
                                  </p>

                                  <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-warning">Pending Verification</span>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewFeedbackDetails(<?= $feedback['feedback_id'] ?>)">
                                      <i class="fas fa-eye mr-1"></i>View
                                    </button>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                              <?php else: ?>
                                <div class="text-center p-4">
                                  <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                  <p class="text-muted">No anonymous feedback submissions yet</p>
                                </div>
                              <?php endif; ?>
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
      </section>
    </div>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>

    <!-- Main Footer -->
    <footer class="main-footer">
      <div class="float-right d-none d-sm-block">
        <b>Version</b> 2.0.0
      </div>
      <strong>&copy; 2026 <a href="#">BCP Bulacan</a>.</strong> All rights reserved.
    </footer>
  </div>

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Select2 -->
  <script src="../../assets/plugins/select2/js/select2.full.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../../assets/dist/js/adminlte.js"></script>
  <!-- ChartJS -->
  <script src="../../assets/plugins/chart.js/Chart.min.js"></script>

  <script>
    $(document).ready(function() {
      // Initialize Select2
      $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Select an option...',
        allowClear: true
      });

      // Star Rating System
      $('.rating-stars .fa-star').on('click', function() {
        var rating = $(this).data('rating');
        var stars = $(this).parent().find('.fa-star');

        stars.removeClass('fas').addClass('far');
        for (var i = 0; i < rating; i++) {
          stars.eq(i).removeClass('far').addClass('fas');
        }

        $(this).parent().find('input[type="hidden"]').val(rating);
      });

      // Feedback Trends Chart
      var ctx = document.getElementById('feedbackTrendsChart');
      if (ctx) {
        var feedbackTrendsChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
              label: 'Average Rating',
              data: [3.8, 4.0, 3.9, 4.1, 4.2, 4.0],
              borderColor: '#007bff',
              backgroundColor: 'rgba(0, 123, 255, 0.1)',
              tension: 0.4
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: false,
                min: 3,
                max: 5
              }
            }
          }
        });
      }

      // Tab persistence
      $('.nav-tabs .nav-link').on('shown.bs.tab', function(e) {
        var tabId = $(e.target).attr('href').substring(1);
        history.replaceState(null, null, '?tab=' + tabId);
      });
    });

    // Action Plan Functions
    function editActionPlan(planId) {
      // Implementation for editing action plans
      alert('Edit Action Plan functionality would be implemented here for plan ID: ' + planId);
    }

    function markCompleted(planId) {
      if (confirm('Mark this action plan as completed?')) {
        // Implementation for marking action plan as completed
        alert('Action plan marked as completed. In a real implementation, this would update the database.');
      }
    }

    function viewFeedbackDetails(feedbackId) {
      // Implementation for viewing detailed feedback
      alert('View Feedback Details functionality would be implemented here for feedback ID: ' + feedbackId);
    }

    // Competency feedback toggle
    $('#competency-feedback-toggle').on('change', function() {
      if ($(this).is(':checked')) {
        $('#competency-feedback').show();
      } else {
        $('#competency-feedback').hide();
      }
    });
  </script>
</body>

</html>

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
              <a href="360-degree.php" class="nav-link active">
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
              <a href="Goal&KPI.php" class="nav-link">
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
              <h1 class="m-0">Multi-Source Feedback Collection</h1>
            </div>
            <div class="col-sm-6 text-right">
              <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addFeedbackModal">
                <i class="fas fa-plus"></i> Collect New Feedback
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <?= $message ?>

          <!-- Add Feedback Modal -->
          <div class="modal fade" id="addFeedbackModal" tabindex="-1" role="dialog" aria-labelledby="addFeedbackModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header bg-primary">
                  <h5 class="modal-title" id="addFeedbackModalLabel">Collect New Feedback</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form action="" method="POST">
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="employee_id">Employee Name / ID</label>
                          <select name="employee_id" id="employee_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            <?php foreach ($employees as $employee): ?>
                              <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['full_name']) ?> (ID: <?= $employee['id'] ?>)</option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="evaluator_type">Evaluator Type</label>
                          <select name="evaluator_type" id="evaluator_type" class="form-control" required>
                            <option value="Manager">Manager</option>
                            <option value="Peer">Peer</option>
                            <option value="Subordinate">Subordinate</option>
                            <option value="Self">Self</option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="category">Feedback Category</label>
                          <select name="category" id="category" class="form-control" required>
                            <option value="Communication">Communication</option>
                            <option value="Teamwork">Teamwork</option>
                            <option value="Leadership">Leadership</option>
                            <option value="Performance">Performance</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="rating">Rating (1-5)</label>
                          <input type="number" name="rating" id="rating" class="form-control" min="1" max="5" value="3" required>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="evaluation_date">Date of Evaluation</label>
                          <input type="date" name="evaluation_date" id="evaluation_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="comments">Comments / Remarks</label>
                      <textarea name="comments" id="comments" class="form-control" rows="3" placeholder="Enter feedback details..."></textarea>
                    </div>

                    <div class="form-check">
                      <input type="checkbox" name="is_anonymous" id="is_anonymous" class="form-check-input">
                      <label class="form-check-label" for="is_anonymous">Anonymous Feedback</label>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit_feedback" class="btn btn-primary">Submit Feedback</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Search and Filter Form -->
          <div class="card card-outline card-secondary mb-3">
            <div class="card-body">
              <form action="" method="GET">
                <div class="row align-items-end">
                  <div class="col-md-4">
                    <div class="form-group mb-0">
                      <label for="search">Search Employee</label>
                      <input type="text" name="search" id="search" class="form-control" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-0">
                      <label for="filter_category">Category</label>
                      <select name="filter_category" id="filter_category" class="form-control">
                        <option value="">All Categories</option>
                        <option value="Communication" <?= $filter_category == 'Communication' ? 'selected' : '' ?>>Communication</option>
                        <option value="Teamwork" <?= $filter_category == 'Teamwork' ? 'selected' : '' ?>>Teamwork</option>
                        <option value="Leadership" <?= $filter_category == 'Leadership' ? 'selected' : '' ?>>Leadership</option>
                        <option value="Performance" <?= $filter_category == 'Performance' ? 'selected' : '' ?>>Performance</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-0">
                      <label for="filter_evaluator">Evaluator</label>
                      <select name="filter_evaluator" id="filter_evaluator" class="form-control">
                        <option value="">All Evaluators</option>
                        <option value="Manager" <?= $filter_evaluator == 'Manager' ? 'selected' : '' ?>>Manager</option>
                        <option value="Peer" <?= $filter_evaluator == 'Peer' ? 'selected' : '' ?>>Peer</option>
                        <option value="Subordinate" <?= $filter_evaluator == 'Subordinate' ? 'selected' : '' ?>>Subordinate</option>
                        <option value="Self" <?= $filter_evaluator == 'Self' ? 'selected' : '' ?>>Self</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary btn-block">
                      <i class="fas fa-search"></i> Filter
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Feedback List -->
          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Submitted Multi-Source Feedback</h3>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover text-nowrap">
                <thead>
                  <tr>
                    <th>Employee</th>
                    <th>Evaluator</th>
                    <th>Category</th>
                    <th>Rating</th>
                    <th>Comments</th>
                    <th>Date</th>
                    <th>Anonymous</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($feedback_list)): ?>
                    <tr>
                      <td colspan="8" class="text-center">No feedback records found.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($feedback_list as $feedback): ?>
                      <tr>
                        <td><?= htmlspecialchars($feedback['full_name']) ?></td>
                        <td><?= htmlspecialchars($feedback['evaluator_type']) ?></td>
                        <td><?= htmlspecialchars($feedback['category']) ?></td>
                        <td>
                          <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= $feedback['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                          <?php endfor; ?>
                        </td>
                        <td><?= htmlspecialchars(substr($feedback['comments'], 0, 50)) . (strlen($feedback['comments']) > 50 ? '...' : '') ?></td>
                        <td><?= date('M d, Y', strtotime($feedback['evaluation_date'])) ?></td>
                        <td><?= $feedback['is_anonymous'] ? '<span class="badge badge-info">Yes</span>' : '<span class="badge badge-secondary">No</span>' ?></td>
                        <td>
                          <button type="button" class="btn btn-sm btn-info edit-btn" 
                                  data-id="<?= $feedback['feedback_id'] ?>"
                                  data-name="<?= htmlspecialchars($feedback['full_name']) ?>"
                                  data-evaluator="<?= $feedback['evaluator_type'] ?>"
                                  data-category="<?= $feedback['category'] ?>"
                                  data-rating="<?= $feedback['rating'] ?>"
                                  data-date="<?= $feedback['evaluation_date'] ?>"
                                  data-comments="<?= htmlspecialchars($feedback['comments']) ?>"
                                  data-anonymous="<?= $feedback['is_anonymous'] ?>">
                            <i class="fas fa-edit"></i>
                          </button>
                          <form action="" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                            <input type="hidden" name="feedback_id" value="<?= $feedback['feedback_id'] ?>">
                            <button type="submit" name="delete_feedback" class="btn btn-sm btn-danger">
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

  <!-- Edit Feedback Modal -->
  <div class="modal fade" id="editFeedbackModal" tabindex="-1" role="dialog" aria-labelledby="editFeedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h5 class="modal-title" id="editFeedbackModalLabel">Edit Feedback for <span id="edit_employee_name"></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="" method="POST">
          <div class="modal-body">
            <input type="hidden" name="feedback_id" id="edit_feedback_id">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="edit_evaluator_type">Evaluator Type</label>
                  <select name="evaluator_type" id="edit_evaluator_type" class="form-control" required>
                    <option value="Manager">Manager</option>
                    <option value="Peer">Peer</option>
                    <option value="Subordinate">Subordinate</option>
                    <option value="Self">Self</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="edit_category">Feedback Category</label>
                  <select name="category" id="edit_category" class="form-control" required>
                    <option value="Communication">Communication</option>
                    <option value="Teamwork">Teamwork</option>
                    <option value="Leadership">Leadership</option>
                    <option value="Performance">Performance</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="edit_rating">Rating (1-5)</label>
                  <input type="number" name="rating" id="edit_rating" class="form-control" min="1" max="5" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="edit_evaluation_date">Date of Evaluation</label>
                  <input type="date" name="evaluation_date" id="edit_evaluation_date" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="edit_comments">Comments / Remarks</label>
              <textarea name="comments" id="edit_comments" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-check">
              <input type="checkbox" name="is_anonymous" id="edit_is_anonymous" class="form-check-input">
              <label class="form-check-label" for="edit_is_anonymous">Anonymous Feedback</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" name="update_feedback" class="btn btn-info">Update Feedback</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery UI -->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <!-- Select2 -->
  <script src="../../assets/plugins/select2/js/select2.full.min.js"></script>
  <script src="../../assets/dist/js/adminlte.js"></script>
  <script>
    $(document).ready(function() {
      // Initialize Select2 Elements
      $('#employee_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select Employee',
        allowClear: true
      });

      // Search Autocomplete
      $('#search').autocomplete({
        source: function(request, response) {
          $.getJSON('../get_suggestions.php', {
            term: request.term,
            type: 'employee'
          }, response);
        },
        minLength: 1,
        select: function(event, ui) {
          $('#search').val(ui.item.label);
          $(this).closest('form').submit();
        }
      });

      $('.edit-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const evaluator = $(this).data('evaluator');
        const category = $(this).data('category');
        const rating = $(this).data('rating');
        const date = $(this).data('date');
        const comments = $(this).data('comments');
        const anonymous = $(this).data('anonymous');

        $('#edit_feedback_id').val(id);
        $('#edit_employee_name').text(name);
        $('#edit_evaluator_type').val(evaluator);
        $('#edit_category').val(category);
        $('#edit_rating').val(rating);
        $('#edit_evaluation_date').val(date);
        $('#edit_comments').val(comments);
        $('#edit_is_anonymous').prop('checked', anonymous == 1);

        $('#editFeedbackModal').modal('show');
      });
    });
  </script>
</body>

</html>
