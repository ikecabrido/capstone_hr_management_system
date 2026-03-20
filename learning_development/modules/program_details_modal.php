<?php
// Program Details Modal View
// This file displays detailed information about a training program in a modal
require_once __DIR__ . '/config.php';

$programId = intval($_GET['id'] ?? 0);

if (!$programId) {
    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Invalid program ID</div>';
    exit;
}

// Fetch program details
$program = null;
try {
    $stmt = $pdo->prepare("
        SELECT tp.*, 
               COUNT(DISTINCT te.id) as total_enrollments,
               SUM(CASE WHEN te.status = 'Completed' THEN 1 ELSE 0 END) as completed_count
        FROM training_programs tp
        LEFT JOIN training_enrollments te ON tp.id = te.program_id
        WHERE tp.id = ?
        GROUP BY tp.id
    ");
    $stmt->execute([$programId]);
    $program = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching program details: " . $e->getMessage());
    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error loading program details</div>';
    exit;
}

if (!$program) {
    echo '<div class="alert alert-warning"><i class="fas fa-info-circle"></i> Program not found</div>';
    exit;
}

// Check if user is already enrolled
$currentUserId = get_current_user_id();
$isEnrolled = false;
$enrollment = null;
try {
    $stmt = $pdo->prepare("
        SELECT * FROM training_enrollments 
        WHERE program_id = ? AND user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$programId, $currentUserId]);
    $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
    $isEnrolled = !!$enrollment;
} catch (Exception $e) {
    error_log("Error checking enrollment: " . $e->getMessage());
}
?>

<div class="program-details-modal">
    <!-- Program Cover Image -->
    <div style="margin: -1.5rem -1.5rem 1.5rem -1.5rem;">
        <img src="<?= htmlspecialchars(isset($program['cover_photo']) && $program['cover_photo'] ? 'assets/pics/' . $program['cover_photo'] : 'assets/pics/placeholder.gif') ?>" 
             alt="<?= htmlspecialchars($program['name']) ?>" 
             style="width: 100%; height: 250px; object-fit: cover;">
    </div>

    <!-- Program Title and Status -->
    <div class="mb-3">
        <h5 class="mb-2"><?= htmlspecialchars($program['name']) ?></h5>
        <div class="mb-2">
            <span class="badge badge-<?= $program['status'] === 'Active' ? 'success' : 'secondary' ?>">
                <?= htmlspecialchars($program['status']) ?>
            </span>
            <?php if (!empty($program['category'])): ?>
                <span class="badge badge-info"><?= htmlspecialchars($program['category']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Description -->
    <div class="mb-3">
        <h6 class="text-muted mb-2">
            <i class="fas fa-align-left"></i> Description
        </h6>
        <p class="text-justify"><?= nl2br(htmlspecialchars($program['description'])) ?></p>
    </div>

    <!-- Program Details Grid -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="info-box mb-3" style="background-color: #f8f9fa; border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Duration</span>
                    <span class="info-box-number">
                        <i class="fas fa-clock"></i> <?= intval($program['duration']) ?> hours
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-box mb-3" style="background-color: #f8f9fa; border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Total Enrollments</span>
                    <span class="info-box-number">
                        <i class="fas fa-users"></i> <?= intval($program['total_enrollments']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Completion Rate -->
    <?php if ($program['total_enrollments'] > 0): ?>
        <div class="mb-3">
            <h6 class="text-muted mb-2">
                <i class="fas fa-chart-pie"></i> Completion Rate
            </h6>
            <div class="progress" style="height: 25px;">
                <?php 
                    $completionRate = ($program['completed_count'] / $program['total_enrollments']) * 100;
                ?>
                <div class="progress-bar bg-success" role="progressbar" 
                     style="width: <?= $completionRate ?>%;" 
                     aria-valuenow="<?= intval($completionRate) ?>" 
                     aria-valuemin="0" aria-valuemax="100">
                    <?= intval($completionRate) ?>%
                </div>
            </div>
            <small class="text-muted d-block mt-1">
                <?= intval($program['completed_count']) ?> of <?= intval($program['total_enrollments']) ?> enrollments completed
            </small>
        </div>
    <?php endif; ?>

    <!-- User's Enrollment Status -->
    <?php if ($isEnrolled && $enrollment): ?>
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle"></i>
            <strong>Your Progress:</strong>
            <p class="mb-2 mt-2">Progress: <?= intval($enrollment['progress_percentage']) ?>%</p>
            <div class="progress">
                <div class="progress-bar bg-info" role="progressbar" 
                     style="width: <?= intval($enrollment['progress_percentage']) ?>%;" 
                     aria-valuenow="<?= intval($enrollment['progress_percentage']) ?>" 
                     aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <p class="mb-0 mt-2">
                Status: <span class="badge badge-<?= $enrollment['status'] === 'Completed' ? 'success' : 'warning' ?>">
                    <?= htmlspecialchars($enrollment['status']) ?>
                </span>
            </p>
        </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="modal-actions mt-4">
        <?php if (!$isEnrolled && $program['status'] === 'Active'): ?>
            <button type="button" class="btn btn-primary w-100" onclick="enrollProgram(<?= intval($program['id']) ?>)">
                Enroll Now
            </button>
        <?php elseif ($isEnrolled): ?>
            <button type="button" class="btn btn-info w-100 mb-2" onclick="location.href='?page=training-browse&focus=<?= intval($program['id']) ?>'">
                Continue Learning
            </button>
            <button type="button" class="btn btn-outline-danger w-100" onclick="unenrollProgram(<?= intval($program['id']) ?>)">
                Unenroll
            </button>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-lock"></i> This program is not currently available for enrollment.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function enrollProgram(programId) {
    if (confirm('Are you sure you want to enroll in this program?')) {
        $.ajax({
            url: 'modules/enrollment_handler.php',
            type: 'POST',
            data: {
                action: 'enroll',
                id: programId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#globalModal').modal('hide');
                    // Reload the dashboard to show updated state
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error: Failed to process enrollment');
                console.error('AJAX Error:', error, xhr);
            }
        });
    }
}

function unenrollProgram(programId) {
    if (confirm('Are you sure you want to unenroll from this program?')) {
        $.ajax({
            url: 'modules/enrollment_handler.php',
            type: 'POST',
            data: {
                action: 'unenroll',
                id: programId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#globalModal').modal('hide');
                    // Reload the dashboard to show updated state
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error: Failed to process unenrollment');
                console.error('AJAX Error:', error, xhr);
            }
        });
    }
}
</script>

<style>
.program-details-modal {
    padding: 0;
}

.program-details-modal .info-box {
    margin-bottom: 0;
    box-shadow: none;
}

.program-details-modal .info-box-number {
    font-size: 1.25rem;
    font-weight: bold;
    color: #1976d2;
}

.program-details-modal .info-box-text {
    font-size: 0.85rem;
}

.program-details-modal .text-justify {
    text-align: justify;
}
</style>
