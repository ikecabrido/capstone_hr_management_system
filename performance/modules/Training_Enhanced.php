<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../../auth/database.php";

$theme = $_SESSION['user']['theme'] ?? 'light';
$db = Database::getInstance()->getConnection();
$current_user_id = $_SESSION['user']['id'] ?? null;
$current_user_role = $_SESSION['user']['role'] ?? 'staff';

try {
    // Create new tables for enhanced features
    $db->exec("CREATE TABLE IF NOT EXISTS pm_job_roles (
      role_id int(11) NOT NULL AUTO_INCREMENT,
      role_name varchar(255) NOT NULL,
      department varchar(255) DEFAULT NULL,
      job_description text,
      required_skills text,
      is_active tinyint(1) NOT NULL DEFAULT 1,
      created_at timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (role_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

    $db->exec("CREATE TABLE IF NOT EXISTS pm_competency_frameworks (
      competency_id int(11) NOT NULL AUTO_INCREMENT,
      role_id int(11) DEFAULT NULL,
      competency_name varchar(255) NOT NULL,
      description text,
      criticality enum('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
      performance_impact enum('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
      weight decimal(5,2) NOT NULL DEFAULT 1.00,
      is_active tinyint(1) NOT NULL DEFAULT 1,
      created_at timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (competency_id),
      KEY role_id (role_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

    // Add new migration columns for enhanced features
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS job_role_id int(11) DEFAULT NULL AFTER priority_level");
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS employee_competencies text AFTER skill_gaps");
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS gap_score decimal(5,2) DEFAULT NULL AFTER employee_competencies");
    
    // NEW: Approval workflow columns
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS approval_status enum('Pending','Approved','Rejected') DEFAULT 'Pending' AFTER status");
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS approved_by int(11) DEFAULT NULL AFTER approval_status");
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS approved_at datetime DEFAULT NULL AFTER approved_by");
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS approval_comments text DEFAULT NULL AFTER approved_at");
    
    // NEW: Employee acknowledgement
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS employee_acknowledged tinyint(1) DEFAULT 0 AFTER approval_comments");
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS acknowledged_at datetime DEFAULT NULL AFTER employee_acknowledged");
    
    // NEW: LD Program linking
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS ld_training_program_id int(11) DEFAULT NULL AFTER acknowledged_at");
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS ld_course_id int(11) DEFAULT NULL AFTER ld_training_program_id");
    
    // NEW: Overdue tracking
    $db->exec("ALTER TABLE pm_training_recommendations ADD COLUMN IF NOT EXISTS is_overdue tinyint(1) DEFAULT 0 AFTER ld_course_id");

    // Initialize default job roles
    $jobRoleCount = $db->query("SELECT COUNT(*) FROM pm_job_roles")->fetchColumn();
    if ($jobRoleCount == 0) {
        $db->exec("INSERT INTO pm_job_roles (role_name, department, job_description, required_skills) VALUES
            ('HR Manager', 'Human Resources', 'Leads HR operations, employee relations, and performance development.', 'Talent Development,Employee Engagement,HR Policies,Coaching,Conflict Resolution'),
            ('Sales Executive', 'Sales', 'Manages customer relationships, closes deals, and drives revenue.', 'Presentation,Negotiation,CRM,Product Knowledge,Prospecting'),
            ('IT Support Specialist', 'IT', 'Resolves technical issues and maintains system reliability.', 'Troubleshooting,Customer Service,Network Support,System Administration,Documentation')");
    }

    // Initialize competency frameworks
    $frameworkCount = $db->query("SELECT COUNT(*) FROM pm_competency_frameworks")->fetchColumn();
    if ($frameworkCount == 0) {
        $db->exec("INSERT INTO pm_competency_frameworks (role_id, competency_name, description, criticality, performance_impact, weight) VALUES
            (1, 'Talent Development', 'Creates development plans and supports career growth.', 'High', 'High', 1.50),
            (1, 'Employee Engagement', 'Drives a positive workplace culture.', 'High', 'High', 1.25),
            (1, 'HR Policies', 'Ensures policies are followed consistently.', 'Medium', 'Medium', 1.00),
            (1, 'Coaching', 'Coaches employees to achieve performance goals.', 'High', 'High', 1.40),
            (2, 'Presentation', 'Delivers compelling client presentations.', 'High', 'High', 1.40),
            (2, 'Negotiation', 'Closes deals with high-value terms.', 'High', 'High', 1.50),
            (2, 'CRM', 'Maintains accurate customer records and follow-up.', 'Medium', 'Medium', 1.10),
            (2, 'Product Knowledge', 'Understands solutions and can articulate value.', 'High', 'High', 1.35),
            (3, 'Troubleshooting', 'Diagnoses and resolves technical issues quickly.', 'High', 'High', 1.50),
            (3, 'Customer Service', 'Communicates clearly with end users.', 'Medium', 'Medium', 1.10),
            (3, 'Network Support', 'Maintains network availability and security.', 'High', 'High', 1.40),
            (3, 'Documentation', 'Keeps accurate technical records.', 'Low', 'Medium', 0.90)");
    }
} catch (PDOException $e) {
    // Schema creation is best-effort
}

// Helper function to calculate gap score
function computeGapScore($db, $jobRoleId, $employeeCompetencies) {
    if (!$jobRoleId) return null;
    
    $roleStmt = $db->prepare("SELECT required_skills FROM pm_job_roles WHERE role_id = ? LIMIT 1");
    $roleStmt->execute([$jobRoleId]);
    $role = $roleStmt->fetch(PDO::FETCH_ASSOC);
    if (!$role) return null;

    $requiredSkills = array_filter(array_map('trim', explode(',', $role['required_skills'])));
    $employeeSkills = array_filter(array_map('trim', explode(',', strtolower($employeeCompetencies))));
    $employeeSkills = array_map('strtolower', $employeeSkills);

    $missingSkills = [];
    foreach ($requiredSkills as $skill) {
        $normalized = strtolower(trim($skill));
        if ($normalized === '' || in_array($normalized, $employeeSkills, true)) continue;
        $missingSkills[] = $normalized;
    }

    $frameworkStmt = $db->prepare("SELECT competency_name, weight FROM pm_competency_frameworks WHERE role_id = ? AND is_active = 1");
    $frameworkStmt->execute([$jobRoleId]);
    $frameworks = $frameworkStmt->fetchAll(PDO::FETCH_ASSOC);

    $totalWeight = 0.0;
    $missedWeight = 0.0;
    foreach ($frameworks as $framework) {
        $weight = max(0.0, floatval($framework['weight']));
        $totalWeight += $weight;
        if (in_array(strtolower($framework['competency_name']), $missingSkills, true)) {
            $missedWeight += $weight;
        }
    }

    if ($totalWeight <= 0) {
        $totalWeight = max(1, count($requiredSkills));
        $missedWeight = count($missingSkills);
    }

    return round(min(100, ($missedWeight / $totalWeight) * 100), 2);
}

// Helper function to update overdue status
function updateOverdueStatus($db) {
    $today = date('Y-m-d');
    $db->exec("UPDATE pm_training_recommendations SET is_overdue = 1 WHERE suggested_completion_date < '$today' AND status NOT IN ('Completed', 'Cancelled')");
}

// Helper function to fetch learning programs
function getLearningPrograms($db) {
    try {
        $stmt = $db->query("SELECT ld_training_programs_id as id, title FROM ld_training_programs WHERE status = 'active' ORDER BY title");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Helper to fetch learning courses
function getLearningCourses($db, $program_id = null) {
    try {
        if ($program_id) {
            $stmt = $db->prepare("SELECT ld_courses_id as id, title FROM ld_courses WHERE ld_training_programs_id = ? ORDER BY title");
            $stmt->execute([$program_id]);
        } else {
            $stmt = $db->query("SELECT ld_courses_id as id, title FROM ld_courses ORDER BY title");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

define('RECORDS_PER_PAGE', 15);
$message = '';

// Update overdue status
updateOverdueStatus($db);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Invalid CSRF token. Please reload the page.</div>';
    } else {

        // Add Recommendation
        if (isset($_POST['add_recommendation'])) {
            $employee_id = $_POST['employee_id'];
            $job_role_id = $_POST['job_role_id'] ?? null;
            $skill_gaps = $_POST['skill_gaps'];
            $employee_competencies = trim($_POST['employee_competencies'] ?? '');
            $training_program = $_POST['training_program'];
            $training_type = $_POST['training_type'];
            $priority_level = $_POST['priority_level'];
            $completion_date = $_POST['completion_date'];
            $remarks = $_POST['remarks'];
            $ld_program_id = $_POST['ld_program_id'] ?? null;
            $ld_course_id = $_POST['ld_course_id'] ?? null;

            $gap_score = null;
            if (!empty($job_role_id)) {
                $gap_score = computeGapScore($db, $job_role_id, $employee_competencies);
            }

            $today = date('Y-m-d');
            if (strtotime($completion_date) < strtotime($today)) {
                $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">Suggested completion date cannot be in the past.</div>';
            } else {
                try {
                    $sql = "INSERT INTO pm_training_recommendations (employee_id, job_role_id, skill_gaps, employee_competencies, gap_score, training_program, training_type, priority_level, suggested_completion_date, remarks, status, approval_status, ld_training_program_id, ld_course_id) 
                    VALUES (:employee_id, :job_role_id, :skill_gaps, :employee_competencies, :gap_score, :training_program, :training_type, :priority_level, :completion_date, :remarks, 'Proposed', 'Pending', :ld_program_id, :ld_course_id)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([
                        'employee_id' => $employee_id,
                        'job_role_id' => $job_role_id,
                        'skill_gaps' => $skill_gaps,
                        'employee_competencies' => $employee_competencies,
                        'gap_score' => $gap_score,
                        'training_program' => $training_program,
                        'training_type' => $training_type,
                        'priority_level' => $priority_level,
                        'completion_date' => $completion_date,
                        'remarks' => $remarks,
                        'ld_program_id' => $ld_program_id,
                        'ld_course_id' => $ld_course_id
                    ]);
                    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">✓ Recommendation added successfully and awaiting manager approval!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } catch (PDOException $e) {
                    $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: ' . $e->getMessage() . '</div>';
                }
            }
        }

        // Manager: Approve Recommendation
        if (isset($_POST['approve_recommendation']) && in_array($current_user_role, ['hr', 'manager', 'performance'])) {
            $id = $_POST['recommendation_id'];
            $comments = $_POST['approval_comments'] ?? '';
            try {
                $sql = "UPDATE pm_training_recommendations SET approval_status = 'Approved', approved_by = :user_id, approved_at = NOW(), approval_comments = :comments WHERE recommendation_id = :id";
                $stmt = $db->prepare($sql);
                $stmt->execute(['user_id' => $current_user_id, 'comments' => $comments, 'id' => $id]);
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">✓ Recommendation approved!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } catch (PDOException $e) {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: ' . $e->getMessage() . '</div>';
            }
        }

        // Manager: Reject Recommendation
        if (isset($_POST['reject_recommendation']) && in_array($current_user_role, ['hr', 'manager', 'performance'])) {
            $id = $_POST['recommendation_id'];
            $comments = $_POST['approval_comments'] ?? '';
            try {
                $sql = "UPDATE pm_training_recommendations SET approval_status = 'Rejected', approved_by = :user_id, approved_at = NOW(), approval_comments = :comments WHERE recommendation_id = :id";
                $stmt = $db->prepare($sql);
                $stmt->execute(['user_id' => $current_user_id, 'comments' => $comments, 'id' => $id]);
                $message = '<div class="alert alert-info alert-dismissible fade show" role="alert">Recommendation rejected with feedback.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } catch (PDOException $e) {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: ' . $e->getMessage() . '</div>';
            }
        }

        // Employee: Acknowledge Recommendation
        if (isset($_POST['acknowledge_recommendation'])) {
            $id = $_POST['recommendation_id'];
            try {
                $sql = "UPDATE pm_training_recommendations SET employee_acknowledged = 1, acknowledged_at = NOW() WHERE recommendation_id = :id";
                $stmt = $db->prepare($sql);
                $stmt->execute(['id' => $id]);
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">✓ Thank you! You have acknowledged this training recommendation.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } catch (PDOException $e) {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: ' . $e->getMessage() . '</div>';
            }
        }

        // Update Recommendation
        if (isset($_POST['update_recommendation']) && in_array($current_user_role, ['hr', 'manager', 'performance'])) {
            $id = $_POST['recommendation_id'];
            $employee_id = $_POST['employee_id'];
            $job_role_id = $_POST['job_role_id'] ?? null;
            $skill_gaps = $_POST['skill_gaps'];
            $employee_competencies = trim($_POST['employee_competencies'] ?? '');
            $training_program = $_POST['training_program'];
            $training_type = $_POST['training_type'];
            $priority_level = $_POST['priority_level'];
            $completion_date = $_POST['completion_date'];
            $remarks = $_POST['remarks'];
            $status = $_POST['status'];
            $ld_program_id = $_POST['ld_program_id'] ?? null;
            $ld_course_id = $_POST['ld_course_id'] ?? null;

            $gap_score = null;
            if (!empty($job_role_id)) {
                $gap_score = computeGapScore($db, $job_role_id, $employee_competencies);
            }

            $today = date('Y-m-d');
            if (strtotime($completion_date) < strtotime($today)) {
                $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">Suggested completion date cannot be in the past.</div>';
            } else {
                try {
                    $sql = "UPDATE pm_training_recommendations SET employee_id = :employee_id, job_role_id = :job_role_id, skill_gaps = :skill_gaps, employee_competencies = :employee_competencies, gap_score = :gap_score, training_program = :training_program, training_type = :training_type, priority_level = :priority_level, suggested_completion_date = :completion_date, remarks = :remarks, status = :status, ld_training_program_id = :ld_program_id, ld_course_id = :ld_course_id WHERE recommendation_id = :id";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([
                        'employee_id' => $employee_id,
                        'job_role_id' => $job_role_id,
                        'skill_gaps' => $skill_gaps,
                        'employee_competencies' => $employee_competencies,
                        'gap_score' => $gap_score,
                        'training_program' => $training_program,
                        'training_type' => $training_type,
                        'priority_level' => $priority_level,
                        'completion_date' => $completion_date,
                        'remarks' => $remarks,
                        'status' => $status,
                        'ld_program_id' => $ld_program_id,
                        'ld_course_id' => $ld_course_id,
                        'id' => $id
                    ]);
                    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">✓ Recommendation updated successfully!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } catch (PDOException $e) {
                    $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: ' . $e->getMessage() . '</div>';
                }
            }
        }

        // Delete Recommendation
        if (isset($_POST['delete_recommendation']) && in_array($current_user_role, ['hr', 'manager', 'performance'])) {
            $id = $_POST['recommendation_id'];
            try {
                $sql = "DELETE FROM pm_training_recommendations WHERE recommendation_id = :id";
                $stmt = $db->prepare($sql);
                $stmt->execute(['id' => $id]);
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">✓ Recommendation deleted successfully!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } catch (PDOException $e) {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: ' . $e->getMessage() . '</div>';
            }
        }

        // Bulk Delete
        if (isset($_POST['bulk_delete']) && in_array($current_user_role, ['hr', 'manager', 'performance'])) {
            $ids = $_POST['recommendation_ids'] ?? [];
            if (!empty($ids) && is_array($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                try {
                    $sql = "DELETE FROM pm_training_recommendations WHERE recommendation_id IN ($placeholders)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute($ids);
                    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">✓ Selected recommendations deleted!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } catch (PDOException $e) {
                    $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: ' . $e->getMessage() . '</div>';
                }
            }
        }
    }
}

// Fetch data for dropdowns
$stmt = $db->query("SELECT employee_id as id, full_name FROM employees WHERE employment_status = 'Active' ORDER BY full_name");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->query("SELECT * FROM pm_job_roles WHERE is_active = 1 ORDER BY role_name");
$job_roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$jobRoleSkills = [];
foreach ($job_roles as $role) {
    $skills = array_filter(array_map('trim', explode(',', $role['required_skills'])));
    $jobRoleSkills[$role['role_id']] = ['skills' => array_values($skills)];
}

$job_frameworks = [];
$stmt = $db->query("SELECT * FROM pm_competency_frameworks WHERE is_active = 1 ORDER BY role_id, weight DESC");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $framework) {
    $job_frameworks[$framework['role_id']][] = $framework;
}

$ld_programs = getLearningPrograms($db);
$ld_courses = getLearningCourses($db);

// Fetch recommendations with filters
$search = trim($_GET['search'] ?? '');
$filter_priority = $_GET['filter_priority'] ?? '';
$filter_status = $_GET['filter_status'] ?? '';
$filter_approval = $_GET['filter_approval'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * RECORDS_PER_PAGE;
$view = $_GET['view'] ?? 'all'; // all, my_recommendations, pending_approval, overdue

$whereSQL = " WHERE 1=1";
$params = [];

if (!empty($search)) {
    $whereSQL .= " AND (e.full_name LIKE :search OR r.training_program LIKE :search OR r.remarks LIKE :search)";
    $params['search'] = "%$search%";
}

if (!empty($filter_priority)) {
    $whereSQL .= " AND r.priority_level = :priority";
    $params['priority'] = $filter_priority;
}

if (!empty($filter_status)) {
    $whereSQL .= " AND r.status = :status";
    $params['status'] = $filter_status;
}

if (!empty($filter_approval)) {
    $whereSQL .= " AND r.approval_status = :approval";
    $params['approval'] = $filter_approval;
}

if (!empty($date_from)) {
    $whereSQL .= " AND r.suggested_completion_date >= :date_from";
    $params['date_from'] = $date_from;
}

if (!empty($date_to)) {
    $whereSQL .= " AND r.suggested_completion_date <= :date_to";
    $params['date_to'] = $date_to;
}

// View-specific filters
if ($view === 'my_recommendations') {
    $whereSQL .= " AND r.employee_id = :emp_id";
    $params['emp_id'] = $_SESSION['user']['employee_id'] ?? 0;
} elseif ($view === 'pending_approval') {
    $whereSQL .= " AND r.approval_status = 'Pending'";
} elseif ($view === 'overdue') {
    $whereSQL .= " AND r.is_overdue = 1 AND r.status NOT IN ('Completed', 'Cancelled')";
}

// Export CSV
if (isset($_GET['action']) && $_GET['action'] === 'export' && in_array($current_user_role, ['hr', 'manager', 'performance'])) {
    $exportSql = "SELECT r.*, e.full_name, jr.role_name FROM pm_training_recommendations r JOIN employees e ON r.employee_id = e.employee_id LEFT JOIN pm_job_roles jr ON r.job_role_id = jr.role_id" . $whereSQL . " ORDER BY r.suggested_completion_date ASC";
    $exportStmt = $db->prepare($exportSql);
    $exportStmt->execute($params);
    $rows = $exportStmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="training_recommendations_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Employee', 'Program', 'Role', 'Gap Score', 'Type', 'Priority', 'Status', 'Approval', 'Overdue', 'Completion Date', 'Remarks']);
    foreach ($rows as $row) {
        fputcsv($out, [
            $row['recommendation_id'],
            $row['full_name'],
            $row['training_program'],
            $row['role_name'] ?? 'N/A',
            $row['gap_score'] ?? 'N/A',
            $row['training_type'],
            $row['priority_level'],
            $row['status'],
            $row['approval_status'],
            $row['is_overdue'] ? 'Yes' : 'No',
            $row['suggested_completion_date'],
            $row['remarks']
        ]);
    }
    fclose($out);
    exit;
}

$countSql = "SELECT COUNT(*) FROM pm_training_recommendations r JOIN employees e ON r.employee_id = e.employee_id" . $whereSQL;
$countStmt = $db->prepare($countSql);
$countStmt->execute($params);
$totalRecords = (int)$countStmt->fetchColumn();
$totalPages = ceil($totalRecords / RECORDS_PER_PAGE);

$query = "SELECT r.*, e.full_name, e.department, jr.role_name FROM pm_training_recommendations r JOIN employees e ON r.employee_id = e.employee_id LEFT JOIN pm_job_roles jr ON r.job_role_id = jr.role_id" . $whereSQL . " ORDER BY r.is_overdue DESC, r.priority_level = 'High' DESC, r.suggested_completion_date ASC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue(':' . $key, $val);
}
$stmt->bindValue(':limit', RECORDS_PER_PAGE, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate dashboard stats
$statsStmt = $db->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN approval_status = 'Pending' THEN 1 ELSE 0 END) as pending_approval,
    SUM(CASE WHEN approval_status = 'Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN is_overdue = 1 AND status NOT IN ('Completed', 'Cancelled') THEN 1 ELSE 0 END) as overdue,
    SUM(CASE WHEN priority_level = 'High' THEN 1 ELSE 0 END) as high_priority
FROM pm_training_recommendations");
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

// Department stats
$deptStmt = $db->query("SELECT e.department, COUNT(r.recommendation_id) as count FROM pm_training_recommendations r JOIN employees e ON r.employee_id = e.employee_id GROUP BY e.department ORDER BY count DESC");
$dept_stats = $deptStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Training Recommendations | Performance Management</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="../custom.css" />
  <style>
    .ui-autocomplete { z-index: 1050; max-height: 200px; overflow-y: auto; }
    .stat-card { border-left: 4px solid #007bff; }
    .stat-card.warning { border-left-color: #ffc107; }
    .stat-card.danger { border-left-color: #dc3545; }
    .stat-card.success { border-left-color: #28a745; }
    .approval-pending { background-color: #fff3cd; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
    .overdue-badge { background-color: #dc3545; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
    .acknowledged { background-color: #d4edda; }
    .view-tabs { margin-bottom: 15px; }
    .view-tabs button { margin-right: 5px; margin-bottom: 5px; }
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
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="../performance.php" class="brand-link">
        <img src="../../assets/pics/bcpLogo.png" alt="Logo" class="brand-image elevation-3" style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan</span>
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
              <a href="Training.php" class="nav-link active">
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
              <h1 class="m-0">Training & Development Recommendations</h1>
            </div>
            <div class="col-sm-6 text-right">
              <?php if (in_array($current_user_role, ['hr', 'manager', 'performance'])): ?>
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#addTrainingForm">
                  <i class="fas fa-plus"></i> Add Recommendation
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <?= $message ?>

          <!-- Dashboard Stats -->
          <div class="row mb-4">
            <div class="col-md-2">
              <div class="card stat-card">
                <div class="card-body">
                  <h5><?= $stats['total'] ?? 0 ?></h5>
                  <p class="text-muted">Total Recommendations</p>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card stat-card warning">
                <div class="card-body">
                  <h5><?= $stats['pending_approval'] ?? 0 ?></h5>
                  <p class="text-muted">Pending Approval</p>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card stat-card success">
                <div class="card-body">
                  <h5><?= $stats['approved'] ?? 0 ?></h5>
                  <p class="text-muted">Approved</p>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card stat-card success">
                <div class="card-body">
                  <h5><?= $stats['completed'] ?? 0 ?></h5>
                  <p class="text-muted">Completed</p>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card stat-card danger">
                <div class="card-body">
                  <h5><?= $stats['overdue'] ?? 0 ?></h5>
                  <p class="text-muted">Overdue</p>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card stat-card danger">
                <div class="card-body">
                  <h5><?= $stats['high_priority'] ?? 0 ?></h5>
                  <p class="text-muted">High Priority</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Department Distribution -->
          <?php if (!empty($dept_stats)): ?>
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="card card-outline card-secondary">
                <div class="card-header">
                  <h3 class="card-title">Training Needs by Department</h3>
                </div>
                <div class="card-body">
                  <ul class="list-group">
                    <?php foreach ($dept_stats as $dept): ?>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($dept['department'] ?? 'Unassigned') ?>
                        <span class="badge badge-primary badge-pill"><?= $dept['count'] ?></span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <!-- View Tabs -->
          <div class="view-tabs">
            <a href="?view=all" class="btn btn-sm <?= $view === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">
              <i class="fas fa-list"></i> All Recommendations
            </a>
            <a href="?view=my_recommendations" class="btn btn-sm <?= $view === 'my_recommendations' ? 'btn-info' : 'btn-outline-info' ?>">
              <i class="fas fa-user"></i> My Recommendations
            </a>
            <?php if (in_array($current_user_role, ['hr', 'manager', 'performance'])): ?>
              <a href="?view=pending_approval" class="btn btn-sm <?= $view === 'pending_approval' ? 'btn-warning' : 'btn-outline-warning' ?>">
                <i class="fas fa-hourglass-half"></i> Pending Approval (<?= $stats['pending_approval'] ?? 0 ?>)
              </a>
              <a href="?view=overdue" class="btn btn-sm <?= $view === 'overdue' ? 'btn-danger' : 'btn-outline-danger' ?>">
                <i class="fas fa-exclamation-triangle"></i> Overdue (<?= $stats['overdue'] ?? 0 ?>)
              </a>
            <?php endif; ?>
          </div>

          <!-- Add Recommendation Form -->
          <?php if (in_array($current_user_role, ['hr', 'manager', 'performance'])): ?>
          <div class="collapse mb-3" id="addTrainingForm">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Add Training Recommendation</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#addTrainingForm">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <form action="" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="employee_id">Employee</label>
                        <select name="employee_id" id="employee_id" class="form-control" required>
                          <option value="">Select Employee</option>
                          <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['full_name']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="training_program">Training Program</label>
                        <input type="text" name="training_program" id="training_program" class="form-control" placeholder="e.g., Leadership Training" required>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="training_type">Type</label>
                        <select name="training_type" id="training_type" class="form-control" required>
                          <option value="Online Course">Online Course</option>
                          <option value="Workshop">Workshop</option>
                          <option value="Seminar">Seminar</option>
                          <option value="Internal Training">Internal Training</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="priority_level">Priority</label>
                        <select name="priority_level" id="priority_level" class="form-control" required>
                          <option value="High">High</option>
                          <option value="Medium" selected>Medium</option>
                          <option value="Low">Low</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="completion_date">Completion Date</label>
                        <input type="date" name="completion_date" id="completion_date" class="form-control" required>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="job_role_id">Job Role</label>
                        <select name="job_role_id" id="job_role_id" class="form-control">
                          <option value="">Select Role</option>
                          <?php foreach ($job_roles as $role): ?>
                            <option value="<?= $role['role_id'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="ld_program_id">Link to Learning Program (Optional)</label>
                        <select name="ld_program_id" id="ld_program_id" class="form-control" onchange="loadLDCourses()">
                          <option value="">None</option>
                          <?php foreach ($ld_programs as $prog): ?>
                            <option value="<?= $prog['id'] ?>"><?= htmlspecialchars($prog['title']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="ld_course_id">Link to Specific Course (Optional)</label>
                        <select name="ld_course_id" id="ld_course_id" class="form-control">
                          <option value="">None</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="employee_competencies">Current Competencies</label>
                        <textarea name="employee_competencies" id="employee_competencies" class="form-control" rows="2" placeholder="Comma-separated skills..."></textarea>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="skill_gaps">Skill Gaps</label>
                        <textarea name="skill_gaps" id="skill_gaps" class="form-control" rows="2" placeholder="Describe gaps..." required></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="remarks">Notes</label>
                    <textarea name="remarks" id="remarks" class="form-control" rows="2" placeholder="Additional notes..."></textarea>
                  </div>
                </div>
                <div class="card-footer">
                  <button type="submit" name="add_recommendation" class="btn btn-primary">Save Recommendation</button>
                </div>
              </form>
            </div>
          </div>
          <?php endif; ?>

          <!-- Search & Filter -->
          <div class="card card-outline card-secondary mb-3">
            <div class="card-body">
              <form action="" method="GET" class="row">
                <input type="hidden" name="view" value="<?= htmlspecialchars($view) ?>">
                <div class="col-md-3">
                  <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                  <select name="filter_priority" class="form-control">
                    <option value="">Priority</option>
                    <option value="High" <?= $filter_priority === 'High' ? 'selected' : '' ?>>High</option>
                    <option value="Medium" <?= $filter_priority === 'Medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="Low" <?= $filter_priority === 'Low' ? 'selected' : '' ?>>Low</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <select name="filter_status" class="form-control">
                    <option value="">Status</option>
                    <option value="Proposed" <?= $filter_status === 'Proposed' ? 'selected' : '' ?>>Proposed</option>
                    <option value="In Progress" <?= $filter_status === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= $filter_status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <select name="filter_approval" class="form-control">
                    <option value="">Approval</option>
                    <option value="Pending" <?= $filter_approval === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Approved" <?= $filter_approval === 'Approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="Rejected" <?= $filter_approval === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                  </select>
                </div>
                <div class="col-md-1">
                  <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-search"></i></button>
                </div>
                <div class="col-md-2">
                  <a href="?action=export&view=<?= urlencode($view) ?>&search=<?= urlencode($search) ?>&filter_priority=<?= urlencode($filter_priority) ?>&filter_status=<?= urlencode($filter_status) ?>&filter_approval=<?= urlencode($filter_approval) ?>" class="btn btn-success w-100">
                    <i class="fas fa-download"></i> Export
                  </a>
                </div>
              </form>
            </div>
          </div>

          <!-- Recommendations Table -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Recommendations</h3>
            </div>
            <div class="card-body table-responsive p-0">
              <form action="" method="POST" id="bulkActionForm">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th width="30"><input type="checkbox" id="selectAll"></th>
                      <th>Employee</th>
                      <th>Program</th>
                      <th>Priority</th>
                      <th>Status</th>
                      <th>Approval</th>
                      <th>Completion</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($recommendations)): ?>
                      <tr><td colspan="8" class="text-center">No recommendations found.</td></tr>
                    <?php else: ?>
                      <?php foreach ($recommendations as $rec): 
                        $priority_class = $rec['priority_level'] === 'High' ? 'danger' : ($rec['priority_level'] === 'Medium' ? 'warning' : 'secondary');
                        $status_class = $rec['status'] === 'Completed' ? 'success' : ($rec['status'] === 'In Progress' ? 'info' : 'primary');
                        $approval_class = $rec['approval_status'] === 'Approved' ? 'success' : ($rec['approval_status'] === 'Rejected' ? 'danger' : 'warning');
                      ?>
                        <tr <?= $rec['is_overdue'] ? 'class="table-danger"' : '' ?>>
                          <td><input type="checkbox" name="recommendation_ids[]" class="rowCheckbox" value="<?= $rec['recommendation_id'] ?>"></td>
                          <td>
                            <strong><?= htmlspecialchars($rec['full_name']) ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($rec['department'] ?? '') ?></small>
                            <?php if ($rec['employee_acknowledged']): ?>
                              <br><span class="badge badge-success badge-sm">Acknowledged <?= date('M d', strtotime($rec['acknowledged_at'])) ?></span>
                            <?php endif; ?>
                          </td>
                          <td><?= htmlspecialchars($rec['training_program']) ?></td>
                          <td><span class="badge badge-<?= $priority_class ?>"><?= $rec['priority_level'] ?></span></td>
                          <td><span class="badge badge-<?= $status_class ?>"><?= $rec['status'] ?></span></td>
                          <td>
                            <span class="badge badge-<?= $approval_class ?>"><?= $rec['approval_status'] ?></span>
                            <?php if ($rec['is_overdue']): ?>
                              <br><span class="overdue-badge">⚠ OVERDUE</span>
                            <?php endif; ?>
                          </td>
                          <td><?= date('M d, Y', strtotime($rec['suggested_completion_date'])) ?></td>
                          <td style="white-space: nowrap;">
                            <button type="button" class="btn btn-sm btn-info view-rec-btn" data-id="<?= $rec['recommendation_id'] ?>" title="View Details">
                              <i class="fas fa-eye"></i>
                            </button>
                            <?php if (in_array($current_user_role, ['hr', 'manager', 'performance'])): ?>
                              <button type="button" class="btn btn-sm btn-primary edit-rec-btn" data-id="<?= $rec['recommendation_id'] ?>" title="Edit">
                                <i class="fas fa-edit"></i>
                              </button>
                              <button type="button" class="btn btn-sm btn-danger delete-rec-btn" data-id="<?= $rec['recommendation_id'] ?>" title="Delete">
                                <i class="fas fa-trash"></i>
                              </button>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
                <?php if (in_array($current_user_role, ['hr', 'manager', 'performance']) && !empty($recommendations)): ?>
                  <div class="p-3">
                    <button type="submit" name="bulk_delete" class="btn btn-danger" onclick="return confirm('Delete selected?');">
                      <i class="fas fa-trash-alt"></i> Delete Selected
                    </button>
                  </div>
                <?php endif; ?>
              </form>
            </div>
          </div>

          <!-- Pagination -->
          <?php if ($totalPages > 1): ?>
            <div class="row mt-3">
              <div class="col-md-12 text-center">
                <nav>
                  <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                      <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&view=<?= urlencode($view) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                      </li>
                    <?php endfor; ?>
                  </ul>
                </nav>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </section>
    </div>
  </div>

  <!-- View Details Modal -->
  <div class="modal fade" id="viewRecModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h5 class="modal-title">Training Recommendation Details</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" id="viewRecContent">
          <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit/Approval Modal -->
  <div class="modal fade" id="editRecModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title">Edit Recommendation</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="" method="POST">
          <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
          <div class="modal-body">
            <input type="hidden" name="recommendation_id" id="edit_id">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Employee</label>
                  <select name="employee_id" id="edit_employee_id" class="form-control" required>
                    <?php foreach ($employees as $emp): ?>
                      <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['full_name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Training Program</label>
                  <input type="text" name="training_program" id="edit_training_program" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Type</label>
                  <select name="training_type" id="edit_training_type" class="form-control" required>
                    <option value="Online Course">Online Course</option>
                    <option value="Workshop">Workshop</option>
                    <option value="Seminar">Seminar</option>
                    <option value="Internal Training">Internal Training</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Priority</label>
                  <select name="priority_level" id="edit_priority_level" class="form-control" required>
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Status</label>
                  <select name="status" id="edit_status" class="form-control" required>
                    <option value="Proposed">Proposed</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Completion Date</label>
                  <input type="date" name="completion_date" id="edit_completion_date" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Skill Gaps</label>
                  <textarea name="skill_gaps" id="edit_skill_gaps" class="form-control" rows="2" required></textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Current Competencies</label>
                  <textarea name="employee_competencies" id="edit_employee_competencies" class="form-control" rows="2"></textarea>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Notes</label>
              <textarea name="remarks" id="edit_remarks" class="form-control" rows="2"></textarea>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Job Role</label>
                  <select name="job_role_id" id="edit_job_role_id" class="form-control">
                    <option value="">Select Role</option>
                    <?php foreach ($job_roles as $role): ?>
                      <option value="<?= $role['role_id'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Link to LD Program</label>
                  <select name="ld_program_id" id="edit_ld_program_id" class="form-control">
                    <option value="">None</option>
                    <?php foreach ($ld_programs as $prog): ?>
                      <option value="<?= $prog['id'] ?>"><?= htmlspecialchars($prog['title']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" name="update_recommendation" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <script src="../../assets/dist/js/adminlte.js"></script>
  <script>
    <?php
      $ldCoursesMap = [];
      foreach ($ld_programs as $prog) {
        $ldCoursesMap[$prog['id']] = getLearningCourses($db, $prog['id']);
      }
    ?>
    const ldCourses = <?= json_encode($ldCoursesMap) ?>;

    function loadLDCourses() {
      const programId = $('#ld_program_id').val();
      const select = $('#ld_course_id');
      select.html('<option value="">None</option>');
      if (programId && ldCourses[programId]) {
        ldCourses[programId].forEach(course => {
          select.append(`<option value="${course.id}">${course.title}</option>`);
        });
      }
    }

    $(document).ready(function() {
      $('#selectAll').on('change', function() {
        $('.rowCheckbox').prop('checked', $(this).is(':checked'));
      });

      $('.rowCheckbox').on('change', function() {
        $('#selectAll').prop('checked', $('.rowCheckbox:checked').length === $('.rowCheckbox').length);
      });

      $('.view-rec-btn').click(function() {
        const id = $(this).data('id');
        $('#viewRecContent').html('<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</p>');
        // Fetch via AJAX (implement in separate script file)
        $('#viewRecModal').modal('show');
      });

      $('.edit-rec-btn').click(function() {
        const id = $(this).data('id');
        // Populate edit form based on data
        $('#editRecModal').modal('show');
      });

      $('.delete-rec-btn').click(function() {
        if (confirm('Delete this recommendation?')) {
          // Submit delete form
        }
      });
    });
  </script>
</body>
</html>
