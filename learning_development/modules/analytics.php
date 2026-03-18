<?php
require_once __DIR__ . '/config.php';
// NO HEADER - Parent learning_development.php handles the layout

$message = '';
$messageType = 'success';
$username = $_SESSION['username'] ?? null;
$userId = null;
$role = 'employee';

try {
    if ($username) {
        $stmt = $pdo->prepare('SELECT id, role FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $userId = $user['id'];
            $role = $user['role'];
        }
    }
} catch (Exception $e) {
    error_log('Error getting user info: ' . $e->getMessage());
}

// Fetch training analytics
$analytics = [];
try {
    $stmt = $pdo->prepare('
        SELECT ta.*, tp.name as program_name
        FROM training_analytics ta
        LEFT JOIN training_programs tp ON ta.program_id = tp.id
        ORDER BY ta.created_date DESC
        LIMIT 12
    ');
    $stmt->execute();
    $analytics = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching analytics: ' . $e->getMessage());
}

// Fetch overall statistics
$stats = [];
try {
    $stmt = $pdo->prepare('
        SELECT 
            COUNT(DISTINCT tp.id) as total_programs,
            COUNT(DISTINCT te.user_id) as total_enrolled,
            COUNT(CASE WHEN te.status = "completed" THEN 1 END) as total_completed,
            AVG(te.score) as avg_score
        FROM training_programs tp
        LEFT JOIN training_enrollments te ON tp.id = te.program_id
    ');
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching stats: ' . $e->getMessage());
}

// Fetch program counts by module
$moduleCounts = ['training' => 0, 'career' => 0, 'leadership' => 0, 'orgdev' => 0];
try {
    // Training programs
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM training_programs');
    $stmt->execute();
    $moduleCounts['training'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Career paths
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM career_paths');
    $stmt->execute();
    $moduleCounts['career'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Leadership programs
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM leadership_programs');
    $stmt->execute();
    $moduleCounts['leadership'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Organizational development activities
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM team_activities');
    $stmt->execute();
    $moduleCounts['orgdev'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (Exception $e) {
    error_log('Error fetching module counts: ' . $e->getMessage());
}

// Fetch enrollment statistics by module
$enrollmentStats = [];
try {
    // Training enrollments
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM training_enrollments');
    $stmt->execute();
    $enrollmentStats['training'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Career development plans
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM individual_development_plans');
    $stmt->execute();
    $enrollmentStats['career'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Leadership enrollments
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM leadership_enrollments');
    $stmt->execute();
    $enrollmentStats['leadership'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Org dev participation
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM team_activity_participants');
    $stmt->execute();
    $enrollmentStats['orgdev'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (Exception $e) {
    error_log('Error fetching enrollment stats: ' . $e->getMessage());
}

// Fetch most popular programs
$popularPrograms = [];
try {
    $stmt = $pdo->prepare('
        SELECT tp.id, tp.name, COUNT(te.id) as enrollment_count
        FROM training_programs tp
        LEFT JOIN training_enrollments te ON tp.id = te.program_id
        GROUP BY tp.id, tp.name
        ORDER BY enrollment_count DESC
        LIMIT 5
    ');
    $stmt->execute();
    $popularPrograms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching popular programs: ' . $e->getMessage());
}

// Fetch total unique users
$totalUsers = 0;
try {
    $stmt = $pdo->prepare('
        SELECT COUNT(DISTINCT user_id) as count FROM (
            SELECT DISTINCT user_id FROM training_enrollments
            UNION
            SELECT DISTINCT user_id FROM individual_development_plans
            UNION
            SELECT DISTINCT user_id FROM leadership_enrollments
            UNION
            SELECT DISTINCT user_id FROM team_activity_participants
        ) as users
    ');
    $stmt->execute();
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
} catch (Exception $e) {
    error_log('Error fetching total users: ' . $e->getMessage());
}

?>

<div class="container" style="margin-top:90px; margin-bottom: 40px;">
    <div class="analytics-toolbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="m-0">Analytics Dashboard</h2>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>

    <!-- Module Program Counts -->
    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #007bff;">
                <div class="card-body text-center">
                    <div style="font-size: 2.5rem; color: #007bff;" class="mb-2">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="text-primary mb-2"><?php echo htmlspecialchars($moduleCounts['training'] ?? 0); ?></h3>
                    <p class="text-muted mb-0">Training Programs</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745;">
                <div class="card-body text-center">
                    <div style="font-size: 2.5rem; color: #28a745;" class="mb-2">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-success mb-2"><?php echo htmlspecialchars($moduleCounts['career'] ?? 0); ?></h3>
                    <p class="text-muted mb-0">Career Paths</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #ffc107;">
                <div class="card-body text-center">
                    <div style="font-size: 2.5rem; color: #ffc107;" class="mb-2">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-warning mb-2"><?php echo htmlspecialchars($moduleCounts['leadership'] ?? 0); ?></h3>
                    <p class="text-muted mb-0">Leadership Programs</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #17a2b8;">
                <div class="card-body text-center">
                    <div style="font-size: 2.5rem; color: #17a2b8;" class="mb-2">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <h3 class="text-info mb-2"><?php echo htmlspecialchars($moduleCounts['orgdev'] ?? 0); ?></h3>
                    <p class="text-muted mb-0">Org Development</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Engagement Statistics -->
    <div class="row g-3 mb-5">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-chart-bar text-primary"></i> Engagement by Module</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">Training</small>
                            <h4 class="text-primary mb-0"><?php echo htmlspecialchars($enrollmentStats['training'] ?? 0); ?></h4>
                        </div>
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">Career</small>
                            <h4 class="text-success mb-0"><?php echo htmlspecialchars($enrollmentStats['career'] ?? 0); ?></h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Leadership</small>
                            <h4 class="text-warning mb-0"><?php echo htmlspecialchars($enrollmentStats['leadership'] ?? 0); ?></h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Org Dev</small>
                            <h4 class="text-info mb-0"><?php echo htmlspecialchars($enrollmentStats['orgdev'] ?? 0); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-users text-success"></i> User Engagement</h5>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-success mb-3"><?php echo htmlspecialchars($totalUsers); ?></h2>
                    <p class="text-muted mb-0">Users Engaged in Programs</p>
                    <hr>
                    <small class="text-muted">
                        Total enrollments and participations across all modules
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Popular Programs -->
    <div class="mb-5">
        <h4 class="mb-3"><i class="fas fa-star text-warning"></i> Most Popular Training Programs</h4>
        <?php if ($popularPrograms): ?>
            <div class="row g-3">
                <?php foreach ($popularPrograms as $program): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="card-title mb-2"><?php echo htmlspecialchars(substr($program['name'], 0, 25)); ?><?php echo strlen($program['name']) > 25 ? '...' : ''; ?></h6>
                                    <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                        <strong><?php echo htmlspecialchars($program['enrollment_count'] ?? 0); ?></strong> enrollments
                                    </p>
                                </div>
                                <div style="font-size: 2rem; color: #ffc107;" class="text-center">
                                    <i class="fas fa-users-class"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info mb-5"><i class="fas fa-info-circle"></i> No program data available yet.</div>
        <?php endif; ?>
    </div>
</div>

<style>
.pop-in {
    animation: popIn 0.5s ease-out;
}

@keyframes popIn {
    0% {
        opacity: 0;
        transform: scale(0.85);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}
</style>

<?php if (!defined('NO_FOOTER')) { require_once __DIR__ . '/footer.php'; } ?>
