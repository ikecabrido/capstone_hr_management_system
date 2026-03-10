<?php
require_once __DIR__ . '/config.php';
if (!defined('NO_HEADER')) {
require_once __DIR__ . '/header.php';
}

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

?>

<div class="container" style="margin-top:90px; margin-bottom: 40px;">
    <div class="analytics-toolbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="m-0">Training Analytics</h2>
            <p class="text-muted mt-2 mb-0">Monitor training programs performance and engagement</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Summary -->
    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-2"><?php echo htmlspecialchars($stats['total_programs'] ?? 0); ?></h3>
                    <p class="text-muted mb-0">Total Programs</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-info mb-2"><?php echo htmlspecialchars($stats['total_enrolled'] ?? 0); ?></h3>
                    <p class="text-muted mb-0">Total Enrolled</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-success mb-2"><?php echo htmlspecialchars($stats['total_completed'] ?? 0); ?></h3>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-warning mb-2"><?php echo number_format($stats['avg_score'] ?? 0, 2); ?></h3>
                    <p class="text-muted mb-0">Avg Score</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Program Analytics Cards -->
    <div class="mb-5">
        <h3 class="mb-3">Program Analytics</h3>
        <?php if ($analytics): ?>
            <div class="row g-3">
                <?php $idx = 0; foreach ($analytics as $item): $idx++; $delay = ($idx - 1) * 0.08; ?>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm pop-in" style="animation-delay: <?php echo $delay; ?>s;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['program_name'] ?? 'Program ' . $item['program_id']); ?></h5>
                                <div class="row g-3 mt-3">
                                    <div class="col-6">
                                        <small class="text-secondary"><strong>Enrolled:</strong></small>
                                        <p class="mb-0"><?php echo htmlspecialchars($item['total_enrolled'] ?? 0); ?></p>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-secondary"><strong>Completed:</strong></small>
                                        <p class="mb-0"><?php echo htmlspecialchars($item['completed'] ?? 0); ?></p>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-secondary"><strong>In Progress:</strong></small>
                                        <p class="mb-0"><?php echo htmlspecialchars($item['in_progress'] ?? 0); ?></p>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-secondary"><strong>Dropped:</strong></small>
                                        <p class="mb-0"><?php echo htmlspecialchars($item['dropped'] ?? 0); ?></p>
                                    </div>
                                </div>
                                <div class="mt-3 pt-2 border-top">
                                    <small class="text-muted">Completion Rate: <strong><?php echo htmlspecialchars(round($item['completion_rate'] ?? 0, 1)); ?>%</strong></small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No analytics data available yet.</div>
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
