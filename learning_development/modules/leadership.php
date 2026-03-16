<?php
require_once __DIR__ . '/config.php';
if (!defined('NO_HEADER')) {
require_once __DIR__ . '/header.php';
}

require_once __DIR__ . '/toast.php';
require_once __DIR__ . '/search_filter.php';
require_once __DIR__ . '/image_upload.php';

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

// Process form submissions (Create, Update, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_program' && in_array($role, ['admin', 'trainer', 'learning'])) {
        try {
            // Handle image upload
            $cover_photo = null;
            if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = uploadImage($_FILES['cover_photo'], 'leadership', 2 * 1024 * 1024);
                if ($uploadResult['success']) {
                    $cover_photo = $uploadResult['path'];
                } else {
                    throw new Exception('Image upload failed: ' . $uploadResult['error']);
                }
            }
            
            $stmt = $pdo->prepare('
                INSERT INTO leadership_programs (name, description, level, focus_area, duration_weeks, target_audience, outcomes, created_by, status, cover_photo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $outcomes = isset($_POST['outcomes']) ? json_encode(array_filter(array_map('trim', explode(',', $_POST['outcomes'])))) : '[]';
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['level'] ?? '',
                $_POST['focus_area'] ?? '',
                $_POST['duration_weeks'] ?? 0,
                $_POST['target_audience'] ?? '',
                $outcomes,
                $userId,
                $_POST['status'] ?? 'active',
                $cover_photo
            ]);
            $message = 'Leadership program created successfully!';
        } catch (Exception $e) {
            $message = 'Error creating program: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'update_program' && in_array($role, ['admin', 'trainer', 'learning'])) {
        try {
            // Handle image upload
            $cover_photo = null;
            if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                // Get existing image to delete old one
                $existing = $pdo->prepare('SELECT cover_photo FROM leadership_programs WHERE id = ?');
                $existing->execute([$_POST['program_id']]);
                $old_image = $existing->fetch(PDO::FETCH_ASSOC)['cover_photo'] ?? null;
                
                $uploadResult = uploadImage($_FILES['cover_photo'], 'leadership', 2 * 1024 * 1024);
                if ($uploadResult['success']) {
                    $cover_photo = $uploadResult['path'];
                    // Delete old image if new one uploaded
                    if ($old_image && file_exists($old_image)) {
                        deleteImage($old_image);
                    }
                } else {
                    throw new Exception('Image upload failed: ' . $uploadResult['error']);
                }
            }

            $outcomes = isset($_POST['outcomes']) ? json_encode(array_filter(array_map('trim', explode(',', $_POST['outcomes'])))) : '[]';
            
            if ($cover_photo) {
                $stmt = $pdo->prepare('
                    UPDATE leadership_programs 
                    SET name = ?, description = ?, level = ?, focus_area = ?, duration_weeks = ?, target_audience = ?, outcomes = ?, status = ?, cover_photo = ?
                    WHERE id = ?
                ');
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['level'] ?? '',
                    $_POST['focus_area'] ?? '',
                    $_POST['duration_weeks'] ?? 0,
                    $_POST['target_audience'] ?? '',
                    $outcomes,
                    $_POST['status'] ?? 'active',
                    $cover_photo,
                    $_POST['program_id']
                ]);
            } else {
                $stmt = $pdo->prepare('
                    UPDATE leadership_programs 
                    SET name = ?, description = ?, level = ?, focus_area = ?, duration_weeks = ?, target_audience = ?, outcomes = ?, status = ?
                    WHERE id = ?
                ');
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['level'] ?? '',
                    $_POST['focus_area'] ?? '',
                    $_POST['duration_weeks'] ?? 0,
                    $_POST['target_audience'] ?? '',
                    $outcomes,
                    $_POST['status'] ?? 'active',
                    $_POST['program_id']
                ]);
            }
            $message = 'Leadership program updated successfully!';
        } catch (Exception $e) {
            $message = 'Error updating program: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'delete_program' && in_array($role, ['admin', 'learning'])) {
        try {
            $stmt = $pdo->prepare('DELETE FROM leadership_programs WHERE id = ?');
            $stmt->execute([$_POST['program_id']]);
            $message = 'Leadership program deleted successfully!';
        } catch (Exception $e) {
            $message = 'Error deleting program: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'enroll_program' && $userId) {
        try {
            $stmt = $pdo->prepare('
                INSERT INTO leadership_enrollments (user_id, program_id, status)
                VALUES (?, ?, ?)
            ');
            $stmt->execute([$userId, $_POST['program_id'], 'pending']);
            $message = 'Enrolled in leadership program successfully!';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $message = 'You are already enrolled in this program.';
                $messageType = 'warning';
            } else {
                $message = 'Error enrolling in program: ' . $e->getMessage();
                $messageType = 'danger';
            }
        } catch (Exception $e) {
            $message = 'Error enrolling in program: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

// Fetch leadership programs
$programs = [];
try {
    $stmt = $pdo->prepare('
        SELECT lp.*, u.full_name as creator_name,
               COUNT(le.id) as enrollment_count
        FROM leadership_programs lp
        LEFT JOIN users u ON lp.created_by = u.id
        LEFT JOIN leadership_enrollments le ON lp.id = le.program_id AND le.status IN ("pending", "in_progress")
        GROUP BY lp.id
        ORDER BY COALESCE(lp.created_at, lp.id) DESC
        LIMIT 1000
    ');
    $stmt->execute();
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($programs as &$program) {
        if (isset($program['outcomes']) && $program['outcomes']) {
            $program['outcomes'] = json_decode($program['outcomes'], true) ?: [];
        } else {
            $program['outcomes'] = [];
        }
    }
} catch (Exception $e) {
    error_log('Error fetching leadership programs with join: ' . $e->getMessage());
    // Fallback to simple query if join fails
    try {
        $stmt = $pdo->prepare('SELECT * FROM leadership_programs ORDER BY COALESCE(created_at, id) DESC LIMIT 1000');
        $stmt->execute();
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e2) {
        error_log('Error fetching leadership programs fallback: ' . $e2->getMessage());
        $programs = [];
    }
}

// Fetch user's enrollments
$userEnrollments = [];
if ($userId) {
    try {
        $stmt = $pdo->prepare('
            SELECT le.*, lp.name as program_name 
            FROM leadership_enrollments le
            LEFT JOIN leadership_programs lp ON le.program_id = lp.id
            WHERE le.user_id = ?
            ORDER BY le.enrollment_date DESC
        ');
        $stmt->execute([$userId]);
        $userEnrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($userEnrollments as &$enrollment) {
            if (isset($enrollment['feedback']) && $enrollment['feedback']) {
                $enrollment['feedback'] = json_decode($enrollment['feedback'], true) ?: [];
            } else {
                $enrollment['feedback'] = [];
            }
        }
    } catch (Exception $e) {
        error_log('Error fetching user enrollments: ' . $e->getMessage());
    }
}

?>

<div class="container-fluid" style="margin-top:90px; margin-bottom: 40px; padding-left: 20px; padding-right: 20px;">
    <div class="leadership-toolbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="m-0" style="font-size: 20px;">Leadership Development</h3>
            <p class="text-muted mt-1 mb-0" style="font-size: 12px;">Build your leadership skills with our comprehensive programs</p>
        </div>
        <?php if (in_array($role, ['admin', 'trainer', 'learning'])): ?>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createProgramModal">Create Leadership Program</button>
        <?php elseif ($username): ?>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createProgramModal" style="display:none;">Create Leadership Program</button>
        <?php endif; ?>
    </div>

    <?php if ($message): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast(<?php echo json_encode($message); ?>, <?php echo json_encode($messageType); ?>, 4000);
    });
    </script>
    <?php endif; ?>

    <!-- Search & Filter Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3" style="display: flex; gap: 1rem; align-items: flex-end;">
                <input type="hidden" name="page" value="leadership">
                <div class="col-md-4">
                    <label class="form-label">Search Programs</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, description..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Level</label>
                    <select name="level" class="form-select">
                        <option value="">All Levels</option>
                        <option value="Basic" <?php echo ($_GET['level'] ?? '') === 'Basic' ? 'selected' : ''; ?>>Basic</option>
                        <option value="Intermediate" <?php echo ($_GET['level'] ?? '') === 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                        <option value="Advanced" <?php echo ($_GET['level'] ?? '') === 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
                <div class="col-md-2">
                    <a href="?page=leadership" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Leadership Programs Section -->
    <div class="mb-5">
        <h4 class="mb-2" style="font-size: 16px;">Available Programs</h4>
        <?php if ($programs): ?>
            <div class="row g-2">
                <?php $idx = 0; foreach ($programs as $program): $idx++; $delay = ($idx - 1) * 0.08; ?>
                    <div class="col-12">
                        <div class="card h-100 leadership-card pop-in" data-id="<?php echo htmlspecialchars($program['id']); ?>" data-title="<?php echo htmlspecialchars($program['name']); ?>" style="animation-delay: <?php echo $delay; ?>s;">
                            <img src="<?php echo htmlspecialchars(getImageUrl($program['cover_photo'] ?? null, 'img/placeholder.gif')); ?>" class="card-img-top" style="height:90px;object-fit:cover;" alt="<?php echo htmlspecialchars($program['name']); ?> cover">
                            <div class="card-body d-flex flex-column" style="padding: 0.75rem;">
                                <h6 class="card-title mb-1" style="font-size: 13px;"><?php echo htmlspecialchars($program['name']); ?></h6>
                                <p class="card-text text-muted mb-1" style="font-size: 11px;"><?php echo htmlspecialchars(substr($program['description'], 0, 60) . '...'); ?></p>
                                <div style="font-size: 10px; margin: 0.5rem 0;">
                                    <small class="text-secondary"><strong>Level:</strong> <?php echo htmlspecialchars($program['level'] ?? 'N/A'); ?></small>
                                </div>
                                <div style="font-size: 10px; margin: 0.5rem 0;">
                                    <small class="text-secondary"><strong>Duration:</strong> <?php echo htmlspecialchars($program['duration_weeks'] ?? 0); ?>w</small>
                                </div>
                                <div style="font-size: 10px; margin: 0.5rem 0;">
                                    <small class="text-secondary"><strong>Enrolled:</strong> <?php echo htmlspecialchars($program['enrollment_count'] ?? 0); ?></small>
                                </div>
                                <?php if (!empty($program['outcomes'])): ?>
                                    <div style="margin: 0.5rem 0;">
                                        <?php foreach (array_slice($program['outcomes'], 0, 1) as $outcome): ?>
                                            <span class="badge bg-info me-1 mb-1" style="font-size: 9px;"><?php echo htmlspecialchars($outcome); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-auto d-flex gap-1" style="margin-top: 0.5rem;">
                                    <?php if ($username): ?>
                                        <form method="POST" class="flex-grow-1">
                                            <input type="hidden" name="action" value="enroll_program">
                                            <input type="hidden" name="program_id" value="<?php echo htmlspecialchars($program['id']); ?>">
                                            <button type="submit" class="btn btn-sm btn-primary w-100" style="font-size: 11px; padding: 0.25rem 0.5rem;">Enroll</button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (in_array($role, ['admin', 'trainer', 'learning'])): ?>
                                        <button class="btn btn-sm btn-warning edit-program-btn" style="font-size: 11px; padding: 0.25rem 0.5rem;" onclick="editProgram(<?php echo htmlspecialchars(json_encode($program)); ?>)" data-toggle="modal" data-target="#editProgramModal">Edit</button>
                                        <?php if (in_array($role, ['admin', 'learning'])): ?>
                                          <form method="post" style="display:inline;" onsubmit="return confirm('Delete this leadership program?');"><input type="hidden" name="action" value="delete_program">
                                            <input type="hidden" name="program_id" value="<?php echo intval($program['id']); ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" style="font-size: 11px; padding: 0.25rem 0.5rem;">Delete</button>
                                          </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No leadership programs available yet.</div>
        <?php endif; ?>
    </div>

    <!-- Your Info Section -->
    <?php if ($username && !empty($userEnrollments)): ?>
        <div class="mb-5 p-3 bg-light rounded">
            <h4 class="mb-3" style="font-size: 16px; border-bottom: 2px solid #007bff; padding-bottom: 0.5rem;">Your Info</h4>
            
            <h5 style="font-size: 14px; margin-top: 1rem; margin-bottom: 1rem;">Your Leadership Enrollments</h5>
            <div class="row g-2">
                <?php foreach ($userEnrollments as $enrollment): ?>
                    <div class="col-12">
                        <div class="card h-100" style="border-left: 3px solid #007bff;">
                            <div class="card-body" style="padding: 0.75rem;">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="card-title mb-0" style="font-size: 13px;"><?php echo htmlspecialchars($enrollment['program_name']); ?></h6>
                                    <span class="badge bg-<?php echo $enrollment['status'] === 'completed' ? 'success' : 'warning'; ?>" style="font-size: 9px;">
                                        <?php echo ucfirst(htmlspecialchars($enrollment['status'])); ?>
                                    </span>
                                </div>
                                <p class="text-muted mb-1" style="font-size: 10px;">
                                    Enrolled: <?php echo htmlspecialchars($enrollment['enrollment_date']); ?>
                                </p>
                                <?php if ($enrollment['status'] === 'completed'): ?>
                                    <p class="text-muted mb-0" style="font-size: 10px;">
                                        Completed: <?php echo htmlspecialchars($enrollment['completion_date']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create/Edit Program Modal -->
<div class="modal fade" id="createProgramModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Leadership Program</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create_program">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Program Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="level" class="form-label">Level</label>
                            <select id="level" name="level" class="form-select">
                                <option value="">Select Level</option>
                                <option value="Foundation">Foundation</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                                <option value="Executive">Executive</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="focus_area" class="form-label">Focus Area</label>
                            <input type="text" id="focus_area" name="focus_area" class="form-control" placeholder="e.g., Team Management">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="duration_weeks" class="form-label">Duration (Weeks)</label>
                            <input type="number" id="duration_weeks" name="duration_weeks" class="form-control" min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="target_audience" class="form-label">Target Audience</label>
                            <input type="text" id="target_audience" name="target_audience" class="form-control" placeholder="e.g., Managers, Team Leads">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="outcomes" class="form-label">Key Outcomes (comma-separated)</label>
                        <input type="text" id="outcomes" name="outcomes" class="form-control" placeholder="e.g., Team Building, Strategic Thinking, Communication">
                    </div>
                    <div class="mb-3">
                        <label for="cover_photo" class="form-label">Cover Photo</label>
                        <input type="file" id="cover_photo" name="cover_photo" class="form-control" accept="image/*">
                        <small class="text-muted">JPG, PNG, GIF, or WebP (max 2MB)</small>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Leadership Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Program Modal -->
<div class="modal fade" id="editProgramModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Leadership Program</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_program">
                <input type="hidden" id="edit_program_id" name="program_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Program Name</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea id="edit_description" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_level" class="form-label">Level</label>
                            <select id="edit_level" name="level" class="form-select">
                                <option value="">Select Level</option>
                                <option value="Foundation">Foundation</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                                <option value="Executive">Executive</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_focus_area" class="form-label">Focus Area</label>
                            <input type="text" id="edit_focus_area" name="focus_area" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_duration_weeks" class="form-label">Duration (Weeks)</label>
                            <input type="number" id="edit_duration_weeks" name="duration_weeks" class="form-control" min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_target_audience" class="form-label">Target Audience</label>
                            <input type="text" id="edit_target_audience" name="target_audience" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_outcomes" class="form-label">Key Outcomes (comma-separated)</label>
                        <input type="text" id="edit_outcomes" name="outcomes" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="edit_cover_photo" class="form-label">Cover Photo</label>
                        <input type="file" id="edit_cover_photo" name="cover_photo" class="form-control" accept="image/*">
                        <small class="text-muted">JPG, PNG, GIF, or WebP (max 2MB). Leave empty to keep current image</small>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select id="edit_status" name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Program Modal -->
<div class="modal fade" id="viewProgramModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewProgramTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <img src="img/placeholder.gif" class="img-fluid mb-3" style="max-height:250px;object-fit:cover;width:100%;border-radius:8px;" alt="">
                <p id="viewProgramDescription" class="text-muted"></p>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <small class="text-secondary"><strong>Level:</strong></small>
                        <p id="viewProgramLevel" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-secondary"><strong>Duration:</strong></small>
                        <p id="viewProgramDuration" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-secondary"><strong>Focus Area:</strong></small>
                        <p id="viewProgramFocus" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-secondary"><strong>Target Audience:</strong></small>
                        <p id="viewProgramAudience" class="mb-0"></p>
                    </div>
                </div>
                <div id="viewProgramOutcomes" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="viewProgramEnrollBtn">Enroll Now</button>
            </div>
        </div>
    </div>
</div>

<style>
.pop-in {
    /* keep initial hidden state and retain final frame */
    opacity: 0;
    animation: popIn 0.5s ease-out forwards;
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

.leadership-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.leadership-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // Click handler for leadership program cards
    document.querySelectorAll('.leadership-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('form')) return;
            
            const programData = {
                id: card.getAttribute('data-id'),
                name: card.querySelector('.card-title')?.textContent || '',
                description: card.querySelector('.card-text')?.textContent || '',
                level: card.querySelector('strong:nth-of-type(1)')?.parentElement?.textContent?.replace('Level:', '').trim() || '',
                duration: card.querySelector('strong:nth-of-type(2)')?.parentElement?.textContent?.replace('Duration:', '').trim() || '',
                focus: card.querySelector('strong:nth-of-type(3)')?.parentElement?.textContent?.replace('Focus Area:', '').trim() || '',
                audience: card.querySelector('strong:nth-of-type(4)')?.parentElement?.textContent?.replace('Target Audience:', '').trim() || ''
            };
            
            const outcomes = Array.from(card.querySelectorAll('.badge')).map(b => b.textContent);
            
            document.getElementById('viewProgramTitle').textContent = programData.name;
            document.getElementById('viewProgramDescription').textContent = programData.description;
            document.getElementById('viewProgramLevel').textContent = programData.level || '—';
            document.getElementById('viewProgramDuration').textContent = programData.duration || '—';
            document.getElementById('viewProgramFocus').textContent = programData.focus || '—';
            document.getElementById('viewProgramAudience').textContent = programData.audience || '—';
            
            const outcomesContainer = document.getElementById('viewProgramOutcomes');
            if (outcomes.length > 0) {
                outcomesContainer.innerHTML = '<small class="text-secondary"><strong>Key Outcomes:</strong></small><div class="mt-2">' + 
                    outcomes.map(outcome => '<span class="badge bg-info">' + outcome + '</span>').join('') + 
                    '</div>';
            } else {
                outcomesContainer.innerHTML = '';
            }
            
            const enrollBtn = document.getElementById('viewProgramEnrollBtn');
            enrollBtn.onclick = function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="action" value="enroll_program"><input type="hidden" name="program_id" value="' + programData.id + '">';
                document.body.appendChild(form);
                form.submit();
            };
            
            const modal = new bootstrap.Modal(document.getElementById('viewProgramModal'));
            modal.show();
        });
    });
});

function editProgram(program) {
    try {
        document.getElementById('edit_program_id').value = program.id;
        document.getElementById('edit_name').value = program.name;
        document.getElementById('edit_description').value = program.description;
        document.getElementById('edit_level').value = program.level || '';
        document.getElementById('edit_focus_area').value = program.focus_area || '';
        document.getElementById('edit_duration_weeks').value = program.duration_weeks || '';
        document.getElementById('edit_target_audience').value = program.target_audience || '';
        document.getElementById('edit_outcomes').value = (program.outcomes || []).join(', ');
        document.getElementById('edit_status').value = program.status || 'active';
    } catch (e) {
        console.error('Error parsing program data:', e);
    }
}
</script>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="deleteConfirmText">Are you sure you want to delete this program?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" id="delete-edit-btn">Edit</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Decline</button>
        <button type="button" class="btn btn-danger" id="delete-confirm-btn">Confirm</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  // Delete buttons now use inline confirmations
});
</script>

<?php if (!defined('NO_FOOTER')) { require_once __DIR__ . '/footer.php'; } ?>
