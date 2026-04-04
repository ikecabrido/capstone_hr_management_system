<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/EnrollmentController.php";

if (!in_array($_SESSION['user']['role'], ['learning', 'admin'])) {
    http_response_code(403);
    echo '<div class="alert alert-danger">Access denied.</div>';
    exit;
}

$enrollmentController = new EnrollmentController();

$program_id = isset($_GET['program_id']) ? (int)$_GET['program_id'] : 0;
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if ($program_id) {
    $enrollments = $enrollmentController->getEnrollmentsByProgram($program_id);
    $title = "Program Enrollees";
} elseif ($course_id) {
    $enrollments = $enrollmentController->getEnrollmentsByCourse($course_id);
    $title = "Course Enrollees";
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
    exit;
}

?>
<h4><?php echo htmlspecialchars($title); ?></h4>
<?php if (empty($enrollments)): ?>
    <p>No enrollments found.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Enrolled Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrollments as $enrollment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($enrollment['name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($enrollment['email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($enrollment['enrolled_at'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($enrollment['status'] ?? 'N/A')); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>