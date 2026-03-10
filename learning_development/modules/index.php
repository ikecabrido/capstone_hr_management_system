<?php 
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/header.php';

$username = $_SESSION['username'] ?? null;
$userId = null;

if ($username) {
    try {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $userId = $user ? $user['id'] : null;
    } catch (Exception $e) {
        error_log('Error getting user: ' . $e->getMessage());
    }
}

// Fetch all training programs
$allPrograms = [];
try {
    $stmt = $pdo->prepare('
        SELECT tp.*, COUNT(te.id) as enrollment_count
        FROM training_programs tp
        LEFT JOIN training_enrollments te ON tp.id = te.program_id AND te.status IN ("pending", "in_progress")
        WHERE tp.status = "active"
        GROUP BY tp.id
        ORDER BY tp.created_at DESC
    ');
    $stmt->execute();
    $allPrograms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching programs: ' . $e->getMessage());
}

// Fetch user's training enrollments
$userTrainings = [];
if ($userId) {
    try {
        $stmt = $pdo->prepare('
            SELECT te.*, tp.name, tp.description
            FROM training_enrollments te
            LEFT JOIN training_programs tp ON te.program_id = tp.id
            WHERE te.user_id = ?
            ORDER BY te.enrollment_date DESC
        ');
        $stmt->execute([$userId]);
        $userTrainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error fetching user trainings: ' . $e->getMessage());
    }
}

// Fetch user's career paths
$userCareerPaths = [];
if ($userId) {
    try {
        $stmt = $pdo->prepare('
            SELECT cp.*, idp.id as idp_id, idp.status as idp_status
            FROM career_paths cp
            LEFT JOIN individual_development_plans idp ON cp.id = idp.career_path_id AND idp.user_id = ?
            ORDER BY cp.created_at DESC
        ');
        $stmt->execute([$userId]);
        $userCareerPaths = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error fetching career paths: ' . $e->getMessage());
    }
}

// Fetch user's leadership enrollments
$userLeadershipEnrollments = [];
if ($userId) {
    try {
        $stmt = $pdo->prepare('
            SELECT le.*, lp.name as program_name, lp.level
            FROM leadership_enrollments le
            LEFT JOIN leadership_programs lp ON le.program_id = lp.id
            WHERE le.user_id = ?
            ORDER BY le.enrollment_date DESC
        ');
        $stmt->execute([$userId]);
        $userLeadershipEnrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error fetching leadership enrollments: ' . $e->getMessage());
    }
}

// Fetch user's compliance assignments
$userComplianceAssignments = [];
if ($userId) {
    try {
        $stmt = $pdo->prepare('
            SELECT ca.*, ct.name as training_name
            FROM compliance_assignments ca
            LEFT JOIN compliance_trainings ct ON ca.training_id = ct.id
            WHERE ca.user_id = ?
            ORDER BY ca.assigned_date DESC
        ');
        $stmt->execute([$userId]);
        $userComplianceAssignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error fetching compliance assignments: ' . $e->getMessage());
    }
}

// Fetch user's LMS enrollments
$userLmsEnrollments = [];
if ($userId) {
    try {
        $stmt = $pdo->prepare('
            SELECT le.*, lc.title as course_name
            FROM lms_enrollments le
            LEFT JOIN lms_courses lc ON le.course_id = lc.id
            WHERE le.user_id = ?
            ORDER BY le.enrollment_date DESC
        ');
        $stmt->execute([$userId]);
        $userLmsEnrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error fetching LMS enrollments: ' . $e->getMessage());
    }
}

?>

<div class="container-fluid" style="padding: 0;">
  <!-- header removed, profile uses full width -->

  <!-- User Profile Section -->
  <div class="row mb-0">
    <div class="col-12 px-0">
      <div class="card shadow-sm border-0" style="width:100%; background: linear-gradient(180deg, rgba(26,26,46,0.6) 0%, rgba(16,16,35,0.6) 100%); border: 1px solid rgba(255,0,110,0.2);">
        <!-- Profile Header Background / cover photo -->
        <div style="position:relative; height: 120px; border-radius: 8px 8px 0 0; overflow:hidden;">
          <img src="img/placeholder.gif" class="cover-photo" style="width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0;" alt="Cover photo">
          <!-- gradient overlay to keep text readable -->
          <div style="background: linear-gradient(135deg, rgba(255,0,110,0.5) 0%, rgba(255,63,143,0.5) 100%); height:100%;"></div>
        </div>
        
        <div class="card-body" style="padding: 3rem 2rem 2rem; margin-top: -40px; position: relative;">
          <div class="row">
            <!-- Profile Avatar and Info -->
            <div class="col-md-3 text-center mb-3 mb-md-0">
              <div style="width: 120px; height: 120px; margin: 0 auto; background: linear-gradient(135deg, rgba(255,0,110,0.2), rgba(255,63,143,0.2)); border: 3px solid #ff006e; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(255,0,110,0.3);">
                <i class="fas fa-user" style="font-size: 48px; background: linear-gradient(135deg, #ff006e, #ff3f8f); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"></i>
              </div>
            </div>
            
            <!-- User Info -->
            <div class="col-md-9">
              <div class="row">
                <div class="col-12">
                  <?php if ($username): ?>
                    <h3 class="mb-1" style="font-weight: 600; color: #e0e0e0;"><?php echo htmlspecialchars($username); ?></h3>
                    <?php $roleLabel = current_role() ? ucfirst(current_role()) . ' Account' : 'Employee Account'; ?>
                    <p class="mb-3" style="font-size: 14px; background: linear-gradient(135deg, #ff006e, #ff3f8f); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 0;"><?php echo htmlspecialchars($roleLabel); ?></p>
                  <?php else: ?>
                    <h3 class="mb-1" style="font-weight: 600; color: #e0e0e0;">Guest User</h3>
                    <p class="mb-3" style="font-size: 14px;"><a href="login.php" style="color: #ff006e;">Log in</a> to view your profile</p>
                  <?php endif; ?>
                </div>
              </div>
              
              <!-- Enrollment Stats -->
              <?php if ($username): ?>
                <div class="row g-2">
                  <div class="col-6 col-md-4">
                    <div class="p-2 rounded text-center" style="background: rgba(255,0,110,0.1); border: 1px solid rgba(255,0,110,0.3);">
                      <p class="mb-1" style="font-size: 20px; font-weight: 600; color: #ff006e;">
                        <?php echo htmlspecialchars(count($userTrainings) + count($userLeadershipEnrollments) + count($userComplianceAssignments) + count($userLmsEnrollments)); ?>
                      </p>
                      <p class="mb-0" style="font-size: 11px; color: #b0b0b0;">Total Enrollments</p>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="p-2 rounded text-center" style="background: rgba(255,0,110,0.08); border: 1px solid rgba(255,0,110,0.2);">
                      <p class="mb-1" style="font-size: 20px; font-weight: 600; color: #ff3f8f;">
                        <?php echo htmlspecialchars(count($userTrainings)); ?>
                      </p>
                      <p class="mb-0" style="font-size: 11px; color: #b0b0b0;">Trainings</p>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="p-2 rounded text-center" style="background: rgba(255,0,110,0.08); border: 1px solid rgba(255,0,110,0.2);">
                      <p class="mb-1" style="font-size: 20px; font-weight: 600; color: #ff3f8f;">
                        <?php echo htmlspecialchars(count($userLeadershipEnrollments)); ?>
                      </p>
                      <p class="mb-0" style="font-size: 11px; color: #b0b0b0;">Leadership</p>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Your Enrollments Section -->
  <?php if ($username && (!empty($userTrainings) || !empty($userLeadershipEnrollments) || !empty($userComplianceAssignments) || !empty($userLmsEnrollments) || !empty($userCareerPaths))): ?>
  <div class="row mb-5">
    <div class="col-12">
      <h4 class="mb-3" style="color: #e0e0e0;">Your Enrollments</h4>
      <div class="row g-2">
              <?php if (!empty($userTrainings)): ?>
                <div class="col-12 col-md-6 col-lg-4">
                  <a href="training.php" class="text-decoration-none">
                    <div class="card border-0 enrollment-card" style="background: linear-gradient(180deg, rgba(26,26,46,0.6) 0%, rgba(16,16,35,0.6) 100%); border: 1px solid rgba(255,0,110,0.2); cursor: pointer;">
                      <div class="card-body" style="padding: 0.75rem;">
                        <h6 style="font-size: 12px; margin-bottom: 0.5rem; color: #e0e0e0;"><i class="fas fa-graduation-cap me-1" style="color: #ff006e;"></i>My Trainings</h6>
                        <p style="font-size: 11px; margin: 0; color: #b0b0b0;"><?php echo htmlspecialchars(count($userTrainings)); ?> enrolled</p>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endif; ?>
              
              <?php if (!empty($userLeadershipEnrollments)): ?>
                <div class="col-12 col-md-6 col-lg-4">
                  <a href="leadership.php" class="text-decoration-none">
                    <div class="card border-0 enrollment-card" style="background: linear-gradient(180deg, rgba(26,26,46,0.6) 0%, rgba(16,16,35,0.6) 100%); border: 1px solid rgba(255,0,110,0.2); cursor: pointer;">
                      <div class="card-body" style="padding: 0.75rem;">
                        <h6 style="font-size: 12px; margin-bottom: 0.5rem; color: #e0e0e0;"><i class="fas fa-users me-1" style="color: #ff006e;"></i>Leadership</h6>
                        <p style="font-size: 11px; margin: 0; color: #b0b0b0;"><?php echo htmlspecialchars(count($userLeadershipEnrollments)); ?> enrolled</p>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endif; ?>
              
              <?php if (!empty($userComplianceAssignments)): ?>
                <div class="col-12 col-md-6 col-lg-4">
                  <a href="compliance.php" class="text-decoration-none">
                    <div class="card border-0 enrollment-card" style="background: linear-gradient(180deg, rgba(26,26,46,0.6) 0%, rgba(16,16,35,0.6) 100%); border: 1px solid rgba(255,0,110,0.2); cursor: pointer;">
                      <div class="card-body" style="padding: 0.75rem;">
                        <h6 style="font-size: 12px; margin-bottom: 0.5rem; color: #e0e0e0;"><i class="fas fa-check-circle me-1" style="color: #ff006e;"></i>Compliance</h6>
                        <p style="font-size: 11px; margin: 0; color: #b0b0b0;"><?php echo htmlspecialchars(count($userComplianceAssignments)); ?> assigned</p>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endif; ?>
              
              <?php if (!empty($userLmsEnrollments)): ?>
                <div class="col-12 col-md-6 col-lg-4">
                  <a href="lms.php" class="text-decoration-none">
                    <div class="card border-0 enrollment-card" style="background: linear-gradient(180deg, rgba(26,26,46,0.6) 0%, rgba(16,16,35,0.6) 100%); border: 1px solid rgba(255,0,110,0.2); cursor: pointer;">
                      <div class="card-body" style="padding: 0.75rem;">
                        <h6 style="font-size: 12px; margin-bottom: 0.5rem; color: #e0e0e0;"><i class="fas fa-book me-1" style="color: #ff006e;"></i>Courses</h6>
                        <p style="font-size: 11px; margin: 0; color: #b0b0b0;"><?php echo htmlspecialchars(count($userLmsEnrollments)); ?> enrolled</p>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endif; ?>
              
              <?php if (!empty($userCareerPaths)): ?>
                <div class="col-12 col-md-6 col-lg-4">
                  <a href="career.php" class="text-decoration-none">
                    <div class="card border-0 enrollment-card" style="background: linear-gradient(180deg, rgba(26,26,46,0.6) 0%, rgba(16,16,35,0.6) 100%); border: 1px solid rgba(255,0,110,0.2); cursor: pointer;">
                      <div class="card-body" style="padding: 0.75rem;">
                        <h6 style="font-size: 12px; margin-bottom: 0.5rem; color: #e0e0e0;"><i class="fas fa-rocket me-1" style="color: #ff006e;"></i>Career Paths</h6>
                        <p style="font-size: 11px; margin: 0; color: #b0b0b0;"><?php echo htmlspecialchars(count($userCareerPaths)); ?> available</p>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endif; ?>
      </div>
      
      <?php if (empty($userTrainings) && empty($userLeadershipEnrollments) && empty($userComplianceAssignments) && empty($userLmsEnrollments)): ?>
        <p class="text-muted" style="font-size: 12px;">You haven't enrolled in any programs yet. Use the navigation menu to explore available programs.</p>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

</div>

<style>
.enrollment-card {
  transition: all 0.3s ease;
  height: 100%;
}

.enrollment-card:hover {
  transform: translateY(-4px);
  background: linear-gradient(180deg, rgba(26,26,46,0.8) 0%, rgba(16,16,35,0.8) 100%) !important;
  border-color: rgba(255,0,110,0.5) !important;
  box-shadow: 0 8px 16px rgba(255,0,110,0.2) !important;
}

.enrollment-card:hover h6 {
  color: #ff006e !important;
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>