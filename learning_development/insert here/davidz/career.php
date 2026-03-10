<?php require_once __DIR__ . '/config.php';
if (!headers_sent()) { ob_start(); }
if (!defined('NO_HEADER')) {
require_once __DIR__ . '/header.php';
}

$role = current_role();
$userId = null;
$username = $_SESSION['username'] ?? null;


// Get user ID from username
if ($username) {
    try {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $userId = $user ? $user['id'] : null;
    } catch (Exception $e) {
        error_log('Error fetching user: ' . $e->getMessage());
    }
}

$message = '';
$messageType = 'info';

// ensure 'prerequisites' and 'skills_required' columns exist for legacy installations
try {
    $pdo->exec('ALTER TABLE career_paths ADD COLUMN IF NOT EXISTS prerequisites VARCHAR(255)');
    $pdo->exec('ALTER TABLE career_paths ADD COLUMN IF NOT EXISTS skills_required JSON');
} catch (Exception $e) {
    // quietly ignore if something goes wrong (columns may already exist or JSON not supported)
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        // Admin: Create career path
        if ($action === 'create_path' && in_array($role, ['admin', 'manager'])) {
            $name = trim($_POST['path_name'] ?? '');
            $description = trim($_POST['path_description'] ?? '');
            $target_position = trim($_POST['target_position'] ?? '');
            $duration_months = intval($_POST['duration_months'] ?? 12);
            $prerequisites = trim($_POST['prerequisites'] ?? '');
            $skills_required = json_encode(array_filter(array_map('trim', explode(',', $_POST['skills_required'] ?? ''))));

            // use the current user id as creator if available
            $creator = $userId ?: null;
            if (!$creator) {
                // fallback to first admin if for some reason session is missing
                $tmp = $pdo->query('SELECT id FROM users WHERE role = "admin" LIMIT 1')->fetch(PDO::FETCH_ASSOC);
                $creator = $tmp ? $tmp['id'] : null;
            }
            if (!$creator) {
                throw new Exception('No valid creator user available for career path');
            }

            $stmt = $pdo->prepare('
                INSERT INTO career_paths (name, description, target_position, prerequisites, skills_required, duration_months, status, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([$name, $description, $target_position, $prerequisites, $skills_required, $duration_months, 'active', $creator]);
            $message = 'Career path created successfully!';
            $messageType = 'success';
        }

        // Admin: Edit career path
        if ($action === 'edit_path' && in_array($role, ['admin', 'manager'])) {
            $path_id = intval($_POST['path_id'] ?? 0);
            $name = trim($_POST['path_name'] ?? '');
            $description = trim($_POST['path_description'] ?? '');
            $target_position = trim($_POST['target_position'] ?? '');
            $duration_months = intval($_POST['duration_months'] ?? 12);
            $status = trim($_POST['status'] ?? 'active');
            $prerequisites = trim($_POST['prerequisites'] ?? '');
            $skills_required = json_encode(array_filter(array_map('trim', explode(',', $_POST['skills_required'] ?? ''))));

            $stmt = $pdo->prepare('
                UPDATE career_paths 
                SET name = ?, description = ?, target_position = ?, prerequisites = ?, skills_required = ?, duration_months = ?, status = ?
                WHERE id = ?
            ');
            $stmt->execute([$name, $description, $target_position, $prerequisites, $skills_required, $duration_months, $status, $path_id]);
            $message = 'Career path updated successfully!';
            $messageType = 'success';
        }

        // Admin: Delete career path
        if ($action === 'delete_path' && in_array($role, ['admin', 'manager'])) {
            $path_id = intval($_POST['path_id'] ?? 0);
            
            // Delete associated IDPs first
            $stmt = $pdo->prepare('DELETE FROM individual_development_plans WHERE career_path_id = ?');
            $stmt->execute([$path_id]);
            
            // Delete career path
            $stmt = $pdo->prepare('DELETE FROM career_paths WHERE id = ?');
            $stmt->execute([$path_id]);
            $message = 'Career path deleted.';
            $messageType = 'success';
        }

        // Employee: Create IDP
        if ($action === 'create_idp' && $userId) {
            $career_path_id = !empty($_POST['path_id']) ? intval($_POST['path_id']) : null;
            $start_date = trim($_POST['start_date'] ?? date('Y-m-d'));
            $end_date = trim($_POST['end_date'] ?? date('Y-m-d', strtotime('+12 months')));
            $objectives = trim($_POST['objectives'] ?? '');
            $milestones = json_encode(array_filter(array_map('trim', explode("\n", $_POST['milestones'] ?? ''))));

            // build query depending on column presence
            $hasMilestones = false;
            try {
                $chk = $pdo->prepare('SHOW COLUMNS FROM individual_development_plans LIKE ?');
                $chk->execute(['milestones']);
                $hasMilestones = $chk->fetch() ? true : false;
            } catch (Exception $e) {
                // assume exists, we'll ignore
                $hasMilestones = true;
            }

            if ($hasMilestones) {
                $stmt = $pdo->prepare('
                    INSERT INTO individual_development_plans (user_id, career_path_id, start_date, end_date, objectives, milestones, status, created_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ');
                $stmt->execute([$userId, $career_path_id, $start_date, $end_date, $objectives, $milestones, 'active', $userId]);
            } else {
                $stmt = $pdo->prepare('
                    INSERT INTO individual_development_plans (user_id, career_path_id, start_date, end_date, objectives, status, created_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ');
                $stmt->execute([$userId, $career_path_id, $start_date, $end_date, $objectives, 'active', $userId]);
            }
            $message = 'Development plan created successfully!';
            $messageType = 'success';
        }

        // Employee: Update IDP
        if ($action === 'update_idp' && $userId) {
            $idp_id = intval($_POST['idp_id'] ?? 0);
            $start_date = trim($_POST['start_date'] ?? date('Y-m-d'));
            $end_date = trim($_POST['end_date'] ?? date('Y-m-d', strtotime('+12 months')));
            $objectives = trim($_POST['objectives'] ?? '');
            $status = trim($_POST['status'] ?? 'active');
            $milestones = json_encode(array_filter(array_map('trim', explode("\n", $_POST['milestones'] ?? ''))));

            // check milestones column
            $hasMilestones = false;
            try {
                $chk = $pdo->prepare('SHOW COLUMNS FROM individual_development_plans LIKE ?');
                $chk->execute(['milestones']);
                $hasMilestones = $chk->fetch() ? true : false;
            } catch (Exception $e) {
                $hasMilestones = true;
            }

            if ($hasMilestones) {
                $stmt = $pdo->prepare('
                    UPDATE individual_development_plans 
                    SET start_date = ?, end_date = ?, objectives = ?, status = ?, milestones = ?
                    WHERE id = ? AND user_id = ?
                ');
                $stmt->execute([$start_date, $end_date, $objectives, $status, $milestones, $idp_id, $userId]);
            } else {
                $stmt = $pdo->prepare('
                    UPDATE individual_development_plans 
                    SET start_date = ?, end_date = ?, objectives = ?, status = ?
                    WHERE id = ? AND user_id = ?
                ');
                $stmt->execute([$start_date, $end_date, $objectives, $status, $idp_id, $userId]);
            }
            $message = 'Development plan updated!';
            $messageType = 'success';
        }

        // Employee: Delete IDP
        if ($action === 'delete_idp' && $userId) {
            $idp_id = intval($_POST['idp_id'] ?? 0);
            
            $stmt = $pdo->prepare('DELETE FROM individual_development_plans WHERE id = ? AND user_id = ?');
            $stmt->execute([$idp_id, $userId]);
            $message = 'Development plan deleted.';
            $messageType = 'success';
        }

        // Redirect after POST
        // Redirect after POST (don't redirect if error so message can be seen)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $messageType !== 'danger') {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } catch (Exception $e) {
        error_log('Career module error: ' . $e->getMessage());
        $debug = !empty($e->getMessage()) ? ' (' . htmlspecialchars($e->getMessage()) . ')' : '';
        $message = 'An error occurred. Please try again.' . $debug;
        $messageType = 'danger';
    }
}

// Fetch career paths from database
$paths = [];
try {
    $stmt = $pdo->query('SELECT * FROM career_paths ORDER BY created_at DESC');
    $paths = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($paths as &$path) {
        if (isset($path['skills_required']) && $path['skills_required']) {
            $path['skills_required'] = json_decode($path['skills_required'], true) ?: [];
        } else {
            $path['skills_required'] = [];
        }
    }
} catch (Exception $e) {
    error_log('Error fetching career paths: ' . $e->getMessage());
}

// Fetch user's IDPs
$userIdps = [];
if ($userId) {
    try {
        $stmt = $pdo->prepare('
            SELECT idp.*, cp.name as path_name 
            FROM individual_development_plans idp
            LEFT JOIN career_paths cp ON idp.career_path_id = cp.id
            WHERE idp.user_id = ?
            ORDER BY idp.created_at DESC
        ');
        $stmt->execute([$userId]);
        $userIdps = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($userIdps as &$idp) {
            if (isset($idp['milestones']) && $idp['milestones']) {
                $idp['milestones'] = json_decode($idp['milestones'], true) ?: [];
            } else {
                $idp['milestones'] = [];
            }
        }
    } catch (Exception $e) {
        error_log('Error fetching IDPs: ' . $e->getMessage());
    }
}

?>

<div class="container" style="margin-top:90px; margin-bottom: 40px;">
    <div class="career-toolbar d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0">Career Development</h2>
        <?php if (in_array($role, ['admin', 'manager'])): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPathModal">Create Career Path</button>
        <?php elseif ($username): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createIdpModal">Create Development Plan</button>
        <?php endif; ?>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Career Paths Section -->
    <div class="mb-5">
        <h3 class="mb-3">Career Paths</h3>
        <?php if ($paths): ?>
            <div class="row g-3">
                <?php $idx = 0; foreach ($paths as $path): $idx++; $delay = ($idx - 1) * 0.08; ?>
                    <div class="col-md-4">
                        <div class="card h-100 career-card pop-in" data-id="<?php echo htmlspecialchars($path['id']); ?>" data-title="<?php echo htmlspecialchars($path['name']); ?>" style="animation-delay: <?php echo $delay; ?>s;">
                            <img src="img/placeholder.gif" class="card-img-top" style="height:160px;object-fit:cover;" alt="">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($path['name']); ?></h5>
                                <p class="card-text text-muted mb-2"><?php echo htmlspecialchars($path['description']); ?></p>
                                <div class="mb-2">
                                    <small class="text-secondary"><strong>Target:</strong> <?php echo htmlspecialchars($path['target_position']); ?></small>
                                </div>
                                <div class="mb-2">
                                    <small class="text-secondary"><strong>Duration:</strong> <?php echo htmlspecialchars($path['duration_months']); ?> months</small>
                                </div>
                                <?php if (!empty($path['prerequisites'])): ?>
                                    <div class="mb-2">
                                        <small class="text-secondary"><strong>Prerequisites:</strong> <?php echo htmlspecialchars($path['prerequisites']); ?></small>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($path['skills_required'])): ?>
                                    <div class="mb-2">
                                        <small class="text-secondary"><strong>Skills:</strong></small>
                                        <div class="mt-1">
                                            <?php foreach ($path['skills_required'] as $skill): ?>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($skill); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-auto d-flex gap-2 flex-wrap">
                                    <?php if ($username): ?>
                                        <button class="btn btn-sm btn-outline-primary" onclick="selectPathForIdp('<?php echo intval($path['id']); ?>')">
                                            Start Plan
                                        </button>
                                    <?php endif; ?>
                                    <?php if (in_array($role, ['admin', 'manager'])): ?>
                                        <button class="btn btn-sm btn-outline-secondary edit-path-btn" data-bs-toggle="modal" data-bs-target="#editPathModal" 
                                            onclick="editPath(<?php echo htmlspecialchars(json_encode($path)); ?>)">Edit</button>
                                        <form method="POST" style="display:inline" class="delete-form" data-id="<?php echo intval($path['id']); ?>">
                                            <input type="hidden" name="action" value="delete_path">
                                            <input type="hidden" name="path_id" value="<?php echo intval($path['id']); ?>">
                                            <button type="button" class="btn btn-sm btn-danger delete-trigger">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No career paths available yet.</div>
        <?php endif; ?>
    </div>

    <!-- My Development Plans Section -->
    <?php if ($username && !empty($userIdps)): ?>
        <div class="mb-5">
            <h3 class="mb-3">My Development Plans</h3>
            <div class="row g-3">
                <?php $idx = 0; foreach ($userIdps as $idp): $idx++; $delay = ($idx - 1) * 0.08; 
                    $pathName = $idp['path_name'] ?? 'Custom Plan';
                ?>
                    <div class="col-md-4">
                        <div class="card h-100 pop-in" style="animation-delay: <?php echo $delay; ?>s;">
                            <img src="img/placeholder.gif" class="card-img-top" style="height:160px;object-fit:cover;" alt="">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($pathName); ?></h5>
                                    <span class="badge bg-<?php echo ($idp['status'] === 'active') ? 'success' : 'secondary'; ?>">
                                        <?php echo htmlspecialchars(ucfirst($idp['status'])); ?>
                                    </span>
                                </div>
                                <p class="card-text text-muted small mb-2">
                                    <strong><?php echo htmlspecialchars($idp['start_date']); ?> → <?php echo htmlspecialchars($idp['end_date']); ?></strong>
                                </p>
                                <p class="card-text mb-2"><?php echo htmlspecialchars($idp['objectives']); ?></p>
                                <?php if (!empty($idp['milestones'])): ?>
                                    <div class="mb-2">
                                        <small class="text-secondary"><strong>Milestones:</strong></small>
                                        <ul class="small mt-1 mb-0">
                                            <?php foreach ($idp['milestones'] as $milestone): ?>
                                                <li><?php echo htmlspecialchars($milestone); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-auto d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editIdpModal"
                                        onclick="editIdp(<?php echo htmlspecialchars(json_encode($idp)); ?>)">Edit</button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this development plan?');">
                                        <input type="hidden" name="action" value="delete_idp">
                                        <input type="hidden" name="idp_id" value="<?php echo intval($idp['id']); ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create Career Path Modal (Admin) -->
<div class="modal fade" id="createPathModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Create Career Path</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_path">
                    <div class="mb-3">
                        <label class="form-label">Path Name</label>
                        <input type="text" name="path_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="path_description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Position</label>
                        <input type="text" name="target_position" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (months)</label>
                        <input type="number" name="duration_months" class="form-control" value="12" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prerequisites</label>
                        <input type="text" name="prerequisites" class="form-control" placeholder="e.g., 2 years experience">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Skills Required (comma-separated)</label>
                        <input type="text" name="skills_required" class="form-control" placeholder="e.g., Leadership, Communication, Analysis">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Career Path Modal (Admin) -->
<div class="modal fade" id="editPathModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Career Path</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_path">
                    <input type="hidden" name="path_id" id="edit_path_id">
                    <div class="mb-3">
                        <label class="form-label">Path Name</label>
                        <input type="text" name="path_name" class="form-control" id="edit_path_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="path_description" class="form-control" id="edit_path_description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Position</label>
                        <input type="text" name="target_position" class="form-control" id="edit_target_position" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (months)</label>
                        <input type="number" name="duration_months" class="form-control" id="edit_duration_months" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" id="edit_status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prerequisites</label>
                        <input type="text" name="prerequisites" class="form-control" id="edit_prerequisites">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Skills Required (comma-separated)</label>
                        <input type="text" name="skills_required" class="form-control" id="edit_skills_required">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create IDP Modal -->
<div class="modal fade" id="createIdpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Create Development Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_idp">
                    <div class="mb-3">
                        <label class="form-label">Career Path</label>
                        <select name="path_id" class="form-control" id="select_path">
                            <option value="">-- Select or Custom Plan --</option>
                            <?php foreach ($paths as $path): ?>
                                <option value="<?php echo intval($path['id']); ?>">
                                    <?php echo htmlspecialchars($path['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo date('Y-m-d', strtotime('+12 months')); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Development Objectives</label>
                        <textarea name="objectives" class="form-control" rows="3" placeholder="What do you want to achieve?" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Milestones (one per line)</label>
                        <textarea name="milestones" class="form-control" rows="4" placeholder="Month 3: Complete X training&#10;Month 6: Lead Y project"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit IDP Modal -->
<div class="modal fade" id="editIdpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Development Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_idp">
                    <input type="hidden" name="idp_id" id="edit_idp_id">
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" id="edit_start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" id="edit_end_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Development Objectives</label>
                        <textarea name="objectives" class="form-control" id="edit_objectives" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" id="edit_idp_status">
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Milestones (one per line)</label>
                        <textarea name="milestones" class="form-control" id="edit_milestones" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Career Path Modal -->
<div class="modal fade" id="viewPathModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPathTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img src="img/placeholder.gif" class="img-fluid mb-3" style="max-height:250px;object-fit:cover;width:100%;border-radius:8px;" alt="">
                <p id="viewPathDescription" class="text-muted"></p>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <small class="text-secondary"><strong>Target Position:</strong></small>
                        <p id="viewPathTarget" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-secondary"><strong>Duration:</strong></small>
                        <p id="viewPathDuration" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-secondary"><strong>Prerequisites:</strong></small>
                        <p id="viewPathPrerequisites" class="mb-0"></p>
                    </div>
                </div>
                <div id="viewPathSkills" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="viewPathStartBtn">Start Development Plan</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // Click handler for career path cards
    document.querySelectorAll('.card h5.card-title').forEach(function(titleEl) {
        const card = titleEl.closest('.career-card');
        if (card) {
            card.style.cursor = 'pointer';
            card.addEventListener('click', function(e) {
                if (e.target.closest('button') || e.target.closest('form')) return;
                
                const pathName = titleEl.textContent;
                const pathDescription = card.querySelector('.card-text.text-muted')?.textContent || '';
                const pathInfo = Array.from(card.querySelectorAll('small.text-secondary')).map(el => el.textContent).join('\n');
                
                // Extract target position, duration, prerequisites, skills
                let targetPos = '';
                let duration = '';
                let prereqs = '';
                let skills = [];
                
                const smallTexts = Array.from(card.querySelectorAll('small'));
                smallTexts.forEach(el => {
                    const text = el.textContent;
                    if (text.includes('Target:')) targetPos = el.parentElement.textContent.replace('Target:', '').trim();
                    if (text.includes('Duration:')) duration = el.parentElement.textContent.replace('Duration:', '').trim();
                    if (text.includes('Prerequisites:')) prereqs = el.parentElement.textContent.replace('Prerequisites:', '').trim();
                });
                
                const skillBadges = card.querySelectorAll('.badge');
                skillBadges.forEach(badge => skills.push(badge.textContent));
                
                // Populate modal
                document.getElementById('viewPathTitle').textContent = pathName;
                document.getElementById('viewPathDescription').textContent = pathDescription;
                document.getElementById('viewPathTarget').textContent = targetPos || '—';
                document.getElementById('viewPathDuration').textContent = duration || '—';
                document.getElementById('viewPathPrerequisites').textContent = prereqs || '—';
                
                // Skills
                const skillsContainer = document.getElementById('viewPathSkills');
                if (skills.length > 0) {
                    skillsContainer.innerHTML = '<small class="text-secondary"><strong>Required Skills:</strong></small><div class="mt-2">' + 
                        skills.map(skill => '<span class="badge bg-info">' + skill + '</span>').join('') + 
                        '</div>';
                } else {
                    skillsContainer.innerHTML = '';
                }
                
                // Start button
                const pathId = card.getAttribute('data-id');
                const startBtn = document.getElementById('viewPathStartBtn');
                startBtn.onclick = function() {
                    selectPathForIdp(pathId);
                };
                
                const modal = new bootstrap.Modal(document.getElementById('viewPathModal'));
                modal.show();
            });
        }
    });
});

function editPath(pathJson) {
    try {
        const path = pathJson;
        document.getElementById('edit_path_id').value = path.id;
        document.getElementById('edit_path_name').value = path.name;
        document.getElementById('edit_path_description').value = path.description;
        document.getElementById('edit_target_position').value = path.target_position;
        document.getElementById('edit_duration_months').value = path.duration_months;
        document.getElementById('edit_status').value = path.status || 'active';
        document.getElementById('edit_prerequisites').value = path.prerequisites || '';
        document.getElementById('edit_skills_required').value = (path.skills_required || []).join(', ');
    } catch (e) {
        console.error('Error parsing path data:', e);
    }
}

function editIdp(idpJson) {
    try {
        const idp = idpJson;
        document.getElementById('edit_idp_id').value = idp.id;
        document.getElementById('edit_start_date').value = idp.start_date;
        document.getElementById('edit_end_date').value = idp.end_date;
        document.getElementById('edit_objectives').value = idp.objectives;
        document.getElementById('edit_idp_status').value = idp.status || 'active';
        document.getElementById('edit_milestones').value = (idp.milestones || []).join('\n');
    } catch (e) {
        console.error('Error parsing IDP data:', e);
    }
}

function selectPathForIdp(pathId) {
    document.getElementById('select_path').value = pathId;
    const modal = new bootstrap.Modal(document.getElementById('createIdpModal'));
    modal.show();
}
</script>

<style>
.pop-in {
    animation: popIn 0.5s ease-out forwards;
    opacity: 0;
}
@keyframes popIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="deleteConfirmText">Are you sure you want to delete this item?</p>
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
  var pendingDeleteForm = null;
  var pendingId = null;
  var deleteModalEl = document.getElementById('deleteConfirmModal');
  var deleteModal = new bootstrap.Modal(deleteModalEl);

  document.querySelectorAll('.delete-trigger').forEach(function(btn){
    btn.addEventListener('click', function(e){
      var form = btn.closest('.delete-form');
      if (!form) return;
      pendingDeleteForm = form;
      pendingId = form.dataset.id || form.querySelector('input[name="id"]').value;
      var title = '';
      var card = document.querySelector('.career-card[data-id="'+CSS.escape(pendingId)+'"]');
      if (card) title = card.dataset.title || '';
      document.getElementById('deleteConfirmText').textContent = title ? 'Delete "' + title + '"? This action cannot be undone.' : 'Delete this item?';
      deleteModal.show();
    });
  });

  document.getElementById('delete-confirm-btn').addEventListener('click', function(){
    if (pendingDeleteForm) {
      pendingDeleteForm.submit();
      pendingDeleteForm = null;
      pendingId = null;
      deleteModal.hide();
    }
  });

  document.getElementById('delete-edit-btn').addEventListener('click', function(){
    if (!pendingId) return;
    deleteModal.hide();
    var editBtn = document.querySelector('.career-card[data-id="'+CSS.escape(pendingId)+'"] .edit-path-btn');
    if (editBtn) editBtn.click();
  });
});
</script>

    <?php if (!defined('NO_FOOTER')) { require_once __DIR__ . '/footer.php'; } ?>
