<?php
require_once __DIR__ . '/config.php';
// NO HEADER - Parent learning_development.php handles the layout

$currentUserId = get_current_user_id();
$currentUsername = $_SESSION['username'] ?? null;

// Fetch user's upcoming enrollments (programs they're enrolled in)
$upcomingPrograms = [];
try {
    $stmt = $pdo->prepare('
        SELECT tp.*, te.id as enrollment_id, te.status, te.enrollment_date,
               COUNT(DISTINCT te.user_id) as total_enrolled
        FROM training_programs tp
        LEFT JOIN training_enrollments te ON tp.id = te.program_id
        WHERE te.user_id = ? AND te.status IN ("pending", "in_progress")
        GROUP BY tp.id
        ORDER BY tp.created_at DESC
        LIMIT 10
    ');
    $stmt->execute([$currentUserId]);
    $upcomingPrograms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching upcoming programs: ' . $e->getMessage());
}

// Fetch user's completed programs
$completedPrograms = [];
try {
    $stmt = $pdo->prepare('
        SELECT tp.*, te.id as enrollment_id, te.status, te.enrollment_date, te.score
        FROM training_programs tp
        LEFT JOIN training_enrollments te ON tp.id = te.program_id
        WHERE te.user_id = ? AND te.status = "completed"
        GROUP BY tp.id
        ORDER BY te.enrollment_date DESC
        LIMIT 5
    ');
    $stmt->execute([$currentUserId]);
    $completedPrograms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching completed programs: ' . $e->getMessage());
}

// Fetch user's career paths
$careerPaths = [];
try {
    $stmt = $pdo->prepare('
        SELECT cp.*, idp.id as idp_id, idp.status as idp_status
        FROM career_paths cp
        LEFT JOIN individual_development_plans idp ON cp.id = idp.career_path_id AND idp.user_id = ?
        ORDER BY cp.created_at DESC
        LIMIT 5
    ');
    $stmt->execute([$currentUserId]);
    $careerPaths = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching career paths: ' . $e->getMessage());
}

// Fetch user statistics
$userStats = [];
try {
    $stmt = $pdo->prepare('
        SELECT 
            COUNT(CASE WHEN te.status = "pending" THEN 1 END) as pending_count,
            COUNT(CASE WHEN te.status = "in_progress" THEN 1 END) as in_progress_count,
            COUNT(CASE WHEN te.status = "completed" THEN 1 END) as completed_count,
            AVG(CASE WHEN te.status = "completed" THEN te.score END) as avg_score
        FROM training_enrollments te
        WHERE te.user_id = ?
    ');
    $stmt->execute([$currentUserId]);
    $userStats = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching user stats: ' . $e->getMessage());
}
?>

<div class="container-fluid" style="padding: 20px; margin-top: 20px;">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-1">Welcome, <?= htmlspecialchars($currentUsername ?? 'User') ?>!</h2>
            <p class="text-muted">Your Learning & Development Dashboard</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= htmlspecialchars($userStats['pending_count'] ?? 0); ?></h3>
                    <small>Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= htmlspecialchars($userStats['in_progress_count'] ?? 0); ?></h3>
                    <small>In Progress</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= htmlspecialchars($userStats['completed_count'] ?? 0); ?></h3>
                    <small>Completed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= number_format($userStats['avg_score'] ?? 0, 1); ?>%</h3>
                    <small>Avg Score</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Programs Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3">📅 Your Upcoming Programs</h4>
            <?php if ($upcomingPrograms): ?>
                <div class="row g-3">
                    <?php foreach ($upcomingPrograms as $program): ?>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <?php if ($program['cover_photo']): ?>
                                    <img src="<?= htmlspecialchars($program['cover_photo']); ?>" class="card-img-top" alt="<?= htmlspecialchars($program['name']); ?>" style="height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="fas fa-book fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($program['name']); ?></h5>
                                    <p class="card-text text-muted small"><?= htmlspecialchars(substr($program['description'] ?? '', 0, 100)); ?>...</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-<?= $program['status'] === 'completed' ? 'success' : ($program['status'] === 'in_progress' ? 'info' : 'warning'); ?>">
                                            <?= ucfirst($program['status']); ?>
                                        </span>
                                        <small class="text-muted">Enrolled: <?= htmlspecialchars($program['total_enrolled']); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No upcoming programs. <a href="?page=training-browse" class="alert-link">Browse training programs</a> to get started!
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recently Completed Section -->
    <?php if ($completedPrograms): ?>
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3">✅ Recently Completed</h4>
            <div class="row g-3">
                <?php foreach ($completedPrograms as $program): ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100 bg-light">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($program['name']); ?></h5>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="badge bg-success">Completed</span>
                                    <span class="badge bg-light text-dark">Score: <?= htmlspecialchars($program['score'] ?? 'N/A'); ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Career Development Section -->
    <?php if ($careerPaths): ?>
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">🚀 Career Development Paths</h4>
            <div class="row g-3">
                <?php foreach ($careerPaths as $path): ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($path['title'] ?? $path['name']); ?></h5>
                                <p class="card-text text-muted small"><?= htmlspecialchars(substr($path['description'] ?? '', 0, 100)); ?>...</p>
                                <div class="mt-3">
                                    <span class="badge bg-<?= $path['idp_status'] === 'completed' ? 'success' : 'secondary'; ?>">
                                        <?= ucfirst($path['idp_status'] ?? 'Available'); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<style>
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
