<?php
// Dashboard - Home page for L&D Management
require_once __DIR__ . '/config.php';

// Get current user info
$currentUserId = get_current_user_id();
$user_dept = $_SESSION['user']['department'] ?? null;
$user_role = $_SESSION['user']['role'] ?? null;

// Fetch my upcoming programs (user's enrollments)
$myPrograms = [];
try {
    $stmt = $pdo->prepare("
        SELECT tp.id, tp.name, tp.description, te.status, te.progress_percentage, 
               te.enrollment_date, tp.duration
        FROM training_programs tp
        JOIN training_enrollments te ON tp.id = te.program_id
        WHERE te.user_id = ?
        ORDER BY te.enrollment_date DESC
        LIMIT 6
    ");
    $stmt->execute([$currentUserId]);
    $myPrograms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching user programs: " . $e->getMessage());
}

// Fetch upcoming programs (all active programs)
$upcomingPrograms = [];
try {
    $stmt = $pdo->prepare("
        SELECT id, name, description, status, created_at
        FROM training_programs
        WHERE status = 'Active'
        ORDER BY created_at DESC
        LIMIT 3
    ");
    $stmt->execute();
    $upcomingPrograms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching upcoming programs: " . $e->getMessage());
}

// Fetch most enrolled programs
$mostEnrolledPrograms = [];
try {
    $stmt = $pdo->prepare("
        SELECT tp.id, tp.name, tp.description, COUNT(te.id) as enrollment_count
        FROM training_programs tp
        LEFT JOIN training_enrollments te ON tp.id = te.program_id
        WHERE tp.status = 'Active'
        GROUP BY tp.id, tp.name, tp.description
        ORDER BY enrollment_count DESC
        LIMIT 3
    ");
    $stmt->execute();
    $mostEnrolledPrograms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching most enrolled programs: " . $e->getMessage());
}

// Fetch recommended programs based on user role/department
$recommendedPrograms = [];
try {
    // Build query based on user department
    $deptKeywords = [];
    if (strpos(strtolower($user_dept), 'it') !== false || strpos(strtolower($user_dept), 'tech') !== false) {
        $deptKeywords = ['Technical', 'Cloud', 'Agile', 'Data'];
    } elseif (strpos(strtolower($user_dept), 'hr') !== false) {
        $deptKeywords = ['Leadership', 'Communication', 'Management'];
    } elseif (strpos(strtolower($user_dept), 'finance') !== false) {
        $deptKeywords = ['Financial', 'Analysis', 'Business'];
    } elseif (strpos(strtolower($user_dept), 'sales') !== false) {
        $deptKeywords = ['Communication', 'Negotiation', 'Customer'];
    } else {
        $deptKeywords = ['Leadership', 'Communication', 'Management'];
    }

    $params = array_merge($deptKeywords, [$currentUserId]);
    $placeholders = implode(',', array_fill(0, count($deptKeywords), '?'));
    
    $stmt = $pdo->prepare("
        SELECT DISTINCT tp.id, tp.name, tp.description, tp.category
        FROM training_programs tp
        LEFT JOIN training_enrollments te ON tp.id = te.program_id AND te.user_id = ?
        WHERE tp.status = 'Active' 
        AND te.id IS NULL
        AND (tp.name LIKE ? OR tp.name LIKE ? OR tp.name LIKE ? OR tp.name LIKE ?)
        ORDER BY tp.created_at DESC
        LIMIT 3
    ");
    $stmt->execute($params);
    $recommendedPrograms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching recommended programs: " . $e->getMessage());
}
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div style="margin-top: 1.5rem; margin-bottom: 2rem;">
        <h3 style="margin: 0 0 0.5rem 0; font-weight: 600; color: #333;">
            <i class="fas fa-chart-line" style="color: #1976d2; margin-right: 0.5rem;"></i>
            Learning Dashboard
        </h3>
        <p style="margin: 0; color: #666; font-size: 0.9rem;">Track your training progress and discover new learning opportunities</p>
    </div>

    <!-- My Upcoming Programs Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3">
                <i class="fas fa-hourglass-start text-primary"></i>
                My Upcoming Programs
            </h4>
        </div>
    </div>

    <!-- My Programs Grid (3x1 cards matching browse page style) -->
    <div class="row g-3 mb-5">
        <?php if (!empty($myPrograms)): ?>
            <?php foreach (array_slice($myPrograms, 0, 3) as $program): ?>
            <div class="col-md-4">
                <div class="card h-100 training-card clickable-card" style="cursor: pointer;">
                    <img src="<?= htmlspecialchars(isset($program['cover_photo']) && $program['cover_photo'] ? 'assets/pics/' . $program['cover_photo'] : 'assets/pics/placeholder.gif') ?>" 
                         class="card-img-top" style="height: 200px; object-fit: cover;" 
                         alt="<?= htmlspecialchars($program['name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($program['name']) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars(substr($program['description'], 0, 100)) ?>...</p>
                        <div class="mt-auto">
                            <div class="mb-2">
                                <small class="text-muted d-block mb-2">Progress: <?= $program['progress_percentage'] ?>%</small>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: <?= $program['progress_percentage'] ?>%" 
                                         aria-valuenow="<?= $program['progress_percentage'] ?>" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <form method="post" style="margin-top: 10px;" onclick="event.stopPropagation();">
                                <input type="hidden" name="action" value="unenroll">
                                <input type="hidden" name="id" value="<?= intval($program['id']) ?>">
                                <button class="btn btn-sm btn-outline-warning w-100">Unenroll</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    You haven't enrolled in any programs yet. 
                    <a href="?page=training-browse">Browse available trainings</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Upcoming Programs Section (3x1 landscape cards) -->
    <div class="row mb-5">
        <div class="col-12">
            <h4 class="mb-3">
                <i class="fas fa-calendar-alt text-info"></i>
                Upcoming Programs
            </h4>
        </div>
    </div>
    <div class="row g-3 mb-5">
        <?php if (!empty($upcomingPrograms)): ?>
            <?php foreach (array_slice($upcomingPrograms, 0, 3) as $program): ?>
            <div class="col-md-4">
                <div class="card h-100 training-card clickable-card" style="cursor: pointer;">
                    <img src="<?= htmlspecialchars(isset($program['cover_photo']) && $program['cover_photo'] ? 'assets/pics/' . $program['cover_photo'] : 'assets/pics/placeholder.gif') ?>" 
                         class="card-img-top" style="height: 200px; object-fit: cover;" 
                         alt="<?= htmlspecialchars($program['name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($program['name']) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars(substr($program['description'], 0, 100)) ?>...</p>
                        <div class="mt-auto">
                            <small class="text-muted d-block mb-2">
                                <i class="far fa-calendar"></i> <?= date('M d, Y', strtotime($program['created_at'])) ?>
                            </small>
                            <a href="?page=training-browse" class="btn btn-sm btn-outline-info w-100">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">No upcoming programs</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Most Enrolled Programs Section (3x1 landscape cards) -->
    <div class="row mb-5">
        <div class="col-12">
            <h4 class="mb-3">
                <i class="fas fa-fire text-success"></i>
                Most Enrolled Programs
            </h4>
        </div>
    </div>
    <div class="row g-3 mb-5">
        <?php if (!empty($mostEnrolledPrograms)): ?>
            <?php foreach (array_slice($mostEnrolledPrograms, 0, 3) as $program): ?>
            <div class="col-md-4">
                <div class="card h-100 training-card clickable-card" style="cursor: pointer;">
                    <img src="<?= htmlspecialchars(isset($program['cover_photo']) && $program['cover_photo'] ? 'assets/pics/' . $program['cover_photo'] : 'assets/pics/placeholder.gif') ?>" 
                         class="card-img-top" style="height: 200px; object-fit: cover;" 
                         alt="<?= htmlspecialchars($program['name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($program['name']) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars(substr($program['description'], 0, 100)) ?>...</p>
                        <div class="mt-auto">
                            <small class="text-muted d-block mb-2">
                                <i class="fas fa-users"></i> <?= intval($program['enrollment_count']) ?> learners enrolled
                            </small>
                            <a href="?page=training-browse" class="btn btn-sm btn-outline-success w-100">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">No program data available</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recommended Programs Section (3x1 landscape cards) -->
    <div class="row mb-5">
        <div class="col-12">
            <h4 class="mb-3">
                <i class="fas fa-star text-warning"></i>
                Recommended For You
            </h4>
        </div>
    </div>
    <div class="row g-3 mb-5">
        <?php if (!empty($recommendedPrograms)): ?>
            <?php foreach (array_slice($recommendedPrograms, 0, 3) as $program): ?>
            <div class="col-md-4">
                <div class="card h-100 training-card clickable-card" style="cursor: pointer;">
                    <img src="<?= htmlspecialchars(isset($program['cover_photo']) && $program['cover_photo'] ? 'assets/pics/' . $program['cover_photo'] : 'assets/pics/placeholder.gif') ?>" 
                         class="card-img-top" style="height: 200px; object-fit: cover;" 
                         alt="<?= htmlspecialchars($program['name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($program['name']) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars(substr($program['description'], 0, 100)) ?>...</p>
                        <div class="mt-auto">
                            <small class="text-muted d-block mb-2">
                                <span class="badge badge-light"><?= htmlspecialchars($program['category'] ?? 'General') ?></span>
                            </small>
                            <form method="post" style="margin: 0;" onclick="event.stopPropagation();">
                                <input type="hidden" name="action" value="enroll">
                                <input type="hidden" name="id" value="<?= intval($program['id']) ?>">
                                <button class="btn btn-sm btn-primary w-100">Enroll Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">No recommendations available</div>
            </div>
        <?php endif; ?>
    </div>
</div>
