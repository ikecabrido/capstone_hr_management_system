<?php require_once __DIR__ . '/config.php';
require_once __DIR__ . '/header.php';

if (!in_array(current_role(), ['admin','manager'])) {
  http_response_code(403);
  echo '<div class="container" style="margin-top:90px;"><h2>Admin</h2><div class="alert alert-warning">You do not have permission to view this page.</div></div>';
  require_once __DIR__ . '/footer.php';
  exit;
}
?>
<div class="container" style="margin-top:90px; margin-bottom: 40px;">
    <div class="admin-toolbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="m-0">Administration Dashboard</h2>
            <p class="text-muted mt-2 mb-0">Manage all training and development programs</p>
        </div>
    </div>

    <!-- Admin Control Cards -->
    <div class="row g-3">
        <!-- Training Programs -->
        <div class="col-md-6">
            <a href="training.php" class="card h-100 border-0 shadow-sm text-decoration-none text-dark" style="transition: all 0.3s ease;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                    <i class="fas fa-graduation-cap" style="font-size: 48px; color: #007bff; margin-bottom: 15px;"></i>
                    <h4 class="card-title text-center mb-2">Training Programs</h4>
                    <p class="text-muted text-center small">Create and manage training programs</p>
                </div>
            </a>
        </div>

        <!-- Career Paths -->
        <div class="col-md-6">
            <a href="career.php" class="card h-100 border-0 shadow-sm text-decoration-none text-dark" style="transition: all 0.3s ease;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                    <i class="fas fa-chart-line" style="font-size: 48px; color: #28a745; margin-bottom: 15px;"></i>
                    <h4 class="card-title text-center mb-2">Career Paths</h4>
                    <p class="text-muted text-center small">Define and manage career development paths</p>
                </div>
            </a>
        </div>

        <!-- Leadership Programs -->
        <div class="col-md-6">
            <a href="leadership.php" class="card h-100 border-0 shadow-sm text-decoration-none text-dark" style="transition: all 0.3s ease;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                    <i class="fas fa-users" style="font-size: 48px; color: #ffc107; margin-bottom: 15px;"></i>
                    <h4 class="card-title text-center mb-2">Leadership Programs</h4>
                    <p class="text-muted text-center small">Create and manage leadership development</p>
                </div>
            </a>
        </div>

        <!-- Compliance Training -->
        <div class="col-md-6">
            <a href="compliance.php" class="card h-100 border-0 shadow-sm text-decoration-none text-dark" style="transition: all 0.3s ease;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                    <i class="fas fa-check-circle" style="font-size: 48px; color: #dc3545; margin-bottom: 15px;"></i>
                    <h4 class="card-title text-center mb-2">Compliance Training</h4>
                    <p class="text-muted text-center small">Manage mandatory compliance requirements</p>
                </div>
            </a>
        </div>

        <!-- Learning Management System -->
        <div class="col-md-6">
            <a href="lms.php" class="card h-100 border-0 shadow-sm text-decoration-none text-dark" style="transition: all 0.3s ease;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                    <i class="fas fa-book" style="font-size: 48px; color: #17a2b8; margin-bottom: 15px;"></i>
                    <h4 class="card-title text-center mb-2">Learning Management</h4>
                    <p class="text-muted text-center small">Create and manage online courses</p>
                </div>
            </a>
        </div>

        <!-- Performance Management -->
        <div class="col-md-6">
            <a href="performance.php" class="card h-100 border-0 shadow-sm text-decoration-none text-dark" style="transition: all 0.3s ease;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                    <i class="fas fa-star" style="font-size: 48px; color: #6f42c1; margin-bottom: 15px;"></i>
                    <h4 class="card-title text-center mb-2">Performance Reviews</h4>
                    <p class="text-muted text-center small">Conduct and manage performance reviews</p>
                </div>
            </a>
        </div>

        <!-- Organizational Development -->
        <div class="col-md-6">
            <a href="orgdev.php" class="card h-100 border-0 shadow-sm text-decoration-none text-dark" style="transition: all 0.3s ease;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                    <i class="fas fa-handshake" style="font-size: 48px; color: #fd7e14; margin-bottom: 15px;"></i>
                    <h4 class="card-title text-center mb-2">Team Activities</h4>
                    <p class="text-muted text-center small">Plan team building and development activities</p>
                </div>
            </a>
        </div>

        <!-- Analytics -->
        <div class="col-md-6">
            <a href="analytics.php" class="card h-100 border-0 shadow-sm text-decoration-none text-dark" style="transition: all 0.3s ease;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                    <i class="fas fa-chart-bar" style="font-size: 48px; color: #20c997; margin-bottom: 15px;"></i>
                    <h4 class="card-title text-center mb-2">Analytics</h4>
                    <p class="text-muted text-center small">View training analytics and reports</p>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
.admin-toolbar {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 20px;
}

a.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
