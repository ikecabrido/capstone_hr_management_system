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

// Check for success redirect
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'created') {
        $message = 'Compliance training created successfully!';
    } elseif ($_GET['success'] === 'updated') {
        $message = 'Compliance training updated successfully!';
    } elseif ($_GET['success'] === 'deleted') {
        $message = 'Compliance training deleted successfully!';
    }
    $messageType = 'success';
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_training' && in_array($role, ['admin', 'trainer', 'learning'])) {
        try {
            $stmt = $pdo->prepare('
                INSERT INTO compliance_trainings (title, description, compliance_type, due_date, mandatory, created_by)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $_POST['title'],
                $_POST['description'] ?? '',
                $_POST['compliance_type'] ?? '',
                $_POST['due_date'] ?? null,
                isset($_POST['mandatory']) ? 1 : 0,
                $userId
            ]);
            // Success - training created
            $message = 'Compliance training created successfully!';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error creating training: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'edit_training' && in_array($role, ['admin', 'trainer', 'learning'])) {
        try {
            $stmt = $pdo->prepare('
                UPDATE compliance_trainings
                SET title = ?, description = ?, compliance_type = ?, due_date = ?, mandatory = ?
                WHERE id = ?
            ');
            $stmt->execute([
                $_POST['title'],
                $_POST['description'] ?? '',
                $_POST['compliance_type'] ?? '',
                $_POST['due_date'] ?? null,
                isset($_POST['mandatory']) ? 1 : 0,
                $_POST['training_id']
            ]);
            if ($stmt->rowCount() > 0) {
                $message = 'Compliance training updated successfully!';
                $messageType = 'success';
            } else {
                $message = 'Training not found.';
                $messageType = 'warning';
            }
        } catch (Exception $e) {
            $message = 'Error updating training: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'delete_training' && in_array($role, ['admin', 'trainer', 'learning'])) {
        try {
            $stmt = $pdo->prepare('DELETE FROM compliance_trainings WHERE id = ?');
            $stmt->execute([$_POST['training_id']]);
            if ($stmt->rowCount() > 0) {
                $message = 'Compliance training deleted successfully!';
                $messageType = 'success';
            } else {
                $message = 'Training not found.';
                $messageType = 'warning';
            }
        } catch (Exception $e) {
            $message = 'Error deleting training: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'acknowledge' && $userId) {
        try {
            $stmt = $pdo->prepare('
                UPDATE compliance_assignments 
                SET acknowledgment_date = NOW(), status = "in_progress"
                WHERE id = ? AND user_id = ?
            ');
            $stmt->execute([$_POST['assignment_id'], $userId]);
            $message = 'Training acknowledged!';
        } catch (Exception $e) {
            $message = 'Error acknowledging training: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'complete' && $userId) {
        try {
            $stmt = $pdo->prepare('
                UPDATE compliance_assignments 
                SET completion_date = NOW(), status = "completed"
                WHERE id = ? AND user_id = ?
            ');
            $stmt->execute([$_POST['assignment_id'], $userId]);
            $message = 'Training completed!';
        } catch (Exception $e) {
            $message = 'Error completing training: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

// Fetch compliance trainings
$trainings = [];
try {
    $stmt = $pdo->prepare('
        SELECT ct.*
        FROM compliance_trainings ct
        ORDER BY ct.created_at DESC
    ');
    $stmt->execute();
    $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching compliance trainings: ' . $e->getMessage());
}

// Fetch user's assignments
$userAssignments = [];
if ($userId) {
    try {
        $stmt = $pdo->prepare('
            SELECT ca.*, ct.title, ct.description, ct.compliance_type
            FROM compliance_assignments ca
            LEFT JOIN compliance_trainings ct ON ca.compliance_training_id = ct.id
            WHERE ca.user_id = ?
            ORDER BY ca.due_date ASC
        ');
        $stmt->execute([$userId]);
        $userAssignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error fetching user assignments: ' . $e->getMessage());
    }
}

?>

<div class="container" style="margin-top:90px; margin-bottom: 40px;">
    <div class="compliance-toolbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="m-0">Compliance Training</h2>
            <p class="text-muted mt-2 mb-0">Stay compliant with required training programs</p>
        </div>
        <?php if (in_array($role, ['admin', 'trainer', 'learning'])): ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createTrainingModal">Create Compliance Training</button>
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
                <input type="hidden" name="page" value="compliance">
                <div class="col-md-4">
                    <label class="form-label">Search Trainings</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by title, type..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo ($_GET['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="in_progress" <?php echo ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="completed" <?php echo ($_GET['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
                <div class="col-md-2">
                    <a href="?page=compliance" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- My Assignments -->
    <?php if ($username && $userAssignments): ?>
        <div class="mb-5">
            <h3 class="mb-3">My Required Trainings</h3>
            <div class="row g-3">
                <?php foreach ($userAssignments as $assignment): ?>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($assignment['title']); ?></h5>
                                    <span class="badge bg-<?php echo $assignment['status'] === 'completed' ? 'success' : ($assignment['status'] === 'in_progress' ? 'warning' : 'danger'); ?>">
                                        <?php echo ucfirst(htmlspecialchars($assignment['status'])); ?>
                                    </span>
                                </div>
                                <p class="card-text text-muted small mb-3"><?php echo htmlspecialchars(substr($assignment['description'], 0, 60) . '...'); ?></p>
                                <div class="mb-3">
                                    <small class="text-secondary"><strong>Due Date:</strong></small>
                                    <p class="mb-0"><?php echo htmlspecialchars($assignment['due_date']); ?></p>
                                </div>
                                <small class="text-secondary"><strong>Type:</strong></small>
                                <p class="mb-3"><?php echo htmlspecialchars($assignment['compliance_type'] ?? 'General'); ?></p>
                                <div class="mt-auto d-flex gap-2">
                                    <?php if ($assignment['status'] === 'assigned'): ?>
                                        <form method="POST" class="flex-grow-1">
                                            <input type="hidden" name="action" value="acknowledge">
                                            <input type="hidden" name="assignment_id" value="<?php echo htmlspecialchars($assignment['id']); ?>">
                                            <button type="submit" class="btn btn-sm btn-primary w-100">Acknowledge</button>
                                        </form>
                                    <?php elseif ($assignment['status'] === 'in_progress'): ?>
                                        <form method="POST" class="flex-grow-1">
                                            <input type="hidden" name="action" value="complete">
                                            <input type="hidden" name="assignment_id" value="<?php echo htmlspecialchars($assignment['id']); ?>">
                                            <button type="submit" class="btn btn-sm btn-success w-100">Mark Complete</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary w-100" disabled>Completed</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Available Trainings -->
    <?php if (in_array($role, ['admin', 'trainer', 'learning'])): ?>
        <div class="mb-5">
            <h3 class="mb-3">Compliance Trainings</h3>
            <?php if ($trainings): ?>
                <div class="row g-3">
                    <?php $idx = 0; foreach ($trainings as $training): $idx++; $delay = ($idx - 1) * 0.08; ?>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm pop-in compliance-card" style="animation-delay: <?php echo $delay; ?>s;"
                                     data-id="<?php echo intval($training['id']); ?>"
                                     data-title="<?php echo htmlspecialchars($training['title'], ENT_QUOTES); ?>"
                                     data-description="<?php echo htmlspecialchars($training['description'], ENT_QUOTES); ?>"
                                     data-type="<?php echo htmlspecialchars($training['compliance_type'], ENT_QUOTES); ?>"
                                     data-due="<?php echo htmlspecialchars($training['due_date'], ENT_QUOTES); ?>"
                                     data-mandatory="<?php echo $training['mandatory'] ? '1' : '0'; ?>">
                                <img src="img/placeholder.gif" class="card-img-top" style="height:160px;object-fit:cover;" alt="">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($training['title']); ?></h5>
                                    <p class="card-text text-muted small mb-2"><?php echo htmlspecialchars(substr($training['description'], 0, 60) . '...'); ?></p>
                                    <div class="mb-2">
                                        <small class="text-secondary"><strong>Type:</strong> <?php echo htmlspecialchars($training['compliance_type'] ?? 'General'); ?></small>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-secondary"><strong>Due:</strong> <?php echo htmlspecialchars($training['due_date'] ?? 'Ongoing'); ?></small>
                                    </div>
                                    <?php if ($training['mandatory']): ?>
                                        <span class="badge bg-danger mb-3">Mandatory</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning mb-3">Recommended</span>
                                    <?php endif; ?>
                                    <div class="mt-auto d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-training-btn"
                                            data-id="<?php echo intval($training['id']); ?>"
                                            data-title="<?php echo htmlspecialchars($training['title'], ENT_QUOTES); ?>"
                                            data-description="<?php echo htmlspecialchars($training['description'], ENT_QUOTES); ?>"
                                            data-type="<?php echo htmlspecialchars($training['compliance_type'], ENT_QUOTES); ?>"
                                            data-due="<?php echo htmlspecialchars($training['due_date'], ENT_QUOTES); ?>"
                                            data-mandatory="<?php echo $training['mandatory'] ? '1' : '0'; ?>">Edit</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-training-btn"
                                            data-id="<?php echo intval($training['id']); ?>"
                                            data-title="<?php echo htmlspecialchars($training['title'], ENT_QUOTES); ?>">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No compliance trainings created yet.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- View Training Modal -->
<div class="modal fade" id="viewComplianceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewComplianceModalTitle">Training Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <h4 id="viewComplianceTitle"></h4>
                <p id="viewComplianceDescription" class="text-muted"></p>
                <div class="mb-2"><small class="text-secondary"><strong>Type:</strong> <span id="viewComplianceType"></span></small></div>
                <div class="mb-2"><small class="text-secondary"><strong>Due:</strong> <span id="viewComplianceDue"></span></small></div>
                <div class="mb-2"><span id="viewComplianceMandatory" class="badge"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Training Modal -->
<div class="modal fade" id="createTrainingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trainingModalTitle">Create Compliance Training</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" id="trainingAction" value="create_training">
                <input type="hidden" name="training_id" id="trainingId" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Training Title</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="compliance_type" class="form-label">Compliance Type</label>
                            <input type="text" id="compliance_type" name="compliance_type" class="form-control" placeholder="e.g., GDPR, HIPAA">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" id="due_date" name="due_date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="mandatory" name="mandatory" checked>
                            <label class="form-check-label" for="mandatory">
                                Make this training mandatory
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="trainingSubmitBtn">Create Compliance Training</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Training Modal -->
<div class="modal fade" id="deleteTrainingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Training</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the training "<span id="trainingToDeleteName"></span>"?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete_training">
                    <input type="hidden" name="training_id" id="trainingIdToDelete" value="">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.pop-in {
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit training button handler
    document.querySelectorAll('.edit-training-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('trainingModalTitle').textContent = 'Edit Training';
            document.getElementById('trainingSubmitBtn').textContent = 'Update Training';
            document.getElementById('trainingAction').value = 'edit_training';
            document.getElementById('trainingId').value = this.dataset.id;
            document.getElementById('title').value = this.dataset.title;
            document.getElementById('description').value = this.dataset.description;
            document.getElementById('compliance_type').value = this.dataset.type;
            document.getElementById('due_date').value = this.dataset.due;
            document.getElementById('mandatory').checked = this.dataset.mandatory === '1';

            const modal = new bootstrap.Modal(document.getElementById('createTrainingModal'));
            modal.show();
        });
    });

    // Click on compliance card to view details
    document.querySelectorAll('.compliance-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('form') || e.target.closest('a')) return;
            document.getElementById('viewComplianceTitle').textContent = card.dataset.title || '';
            document.getElementById('viewComplianceDescription').textContent = card.dataset.description || '';
            document.getElementById('viewComplianceType').textContent = card.dataset.type || '';
            document.getElementById('viewComplianceDue').textContent = card.dataset.due || '';
            document.getElementById('viewComplianceMandatory').textContent = card.dataset.mandatory === '1' ? 'Mandatory' : 'Recommended';
            document.getElementById('viewComplianceMandatory').className = card.dataset.mandatory === '1' ? 'badge bg-danger' : 'badge bg-warning';
            const vmodal = new bootstrap.Modal(document.getElementById('viewComplianceModal'));
            vmodal.show();
        });
    });

    // Delete training button handler
    document.querySelectorAll('.delete-training-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('trainingToDeleteName').textContent = this.dataset.title;
            document.getElementById('trainingIdToDelete').value = this.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('deleteTrainingModal'));
            modal.show();
        });
    });

    // Reset form when create modal is closed, to prepare for new create
    document.getElementById('createTrainingModal').addEventListener('hide.bs.modal', function() {
        document.getElementById('trainingModalTitle').textContent = 'Create Compliance Training';
        document.getElementById('trainingSubmitBtn').textContent = 'Create Compliance Training';
        document.getElementById('trainingAction').value = 'create_training';
        document.getElementById('trainingId').value = '';
        document.getElementById('title').value = '';
        document.getElementById('description').value = '';
        document.getElementById('compliance_type').value = '';
        document.getElementById('due_date').value = '';
        document.getElementById('mandatory').checked = true;
    });
});
</script>

<?php if (!defined('NO_FOOTER')) { require_once __DIR__ . '/footer.php'; } ?>
