<?php
require_once __DIR__ . '/config.php';
// NO HEADER - Parent learning_development.php handles the layout

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
        $message = 'Activity created successfully!';
    } elseif ($_GET['success'] === 'updated') {
        $message = 'Activity updated successfully!';
    } elseif ($_GET['success'] === 'deleted') {
        $message = 'Activity deleted successfully!';
    }
    $messageType = 'success';
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_activity' && in_array($role, ['admin', 'manager', 'learning'])) {
        try {
            $stmt = $pdo->prepare('
                INSERT INTO team_activities (name, description, activity_date, department, organizer_id, budget, participant_count, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $_POST['name'],
                $_POST['description'] ?? '',
                $_POST['activity_date'],
                $_POST['department'] ?? '',
                $userId,
                $_POST['budget'] ?? 0,
                $_POST['participant_count'] ?? 0,
                'planned'
            ]);
            // Success - activity created
            $message = 'Team activity created successfully!';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error creating activity: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'edit_activity' && in_array($role, ['admin', 'manager', 'learning'])) {
        try {
            $stmt = $pdo->prepare('
                UPDATE team_activities
                SET name = ?, description = ?, activity_date = ?, department = ?, budget = ?, participant_count = ?
                WHERE id = ?
            ');
            $stmt->execute([
                $_POST['name'],
                $_POST['description'] ?? '',
                $_POST['activity_date'],
                $_POST['department'] ?? '',
                $_POST['budget'] ?? 0,
                $_POST['participant_count'] ?? 0,
                $_POST['activity_id']
            ]);
            if ($stmt->rowCount() > 0) {
                $message = 'Team activity updated successfully!';
                $messageType = 'success';
            } else {
                $message = 'Activity not found.';
                $messageType = 'warning';
            }
        } catch (Exception $e) {
            $message = 'Error updating activity: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'delete_activity' && in_array($role, ['admin', 'manager', 'learning'])) {
        try {
            $stmt = $pdo->prepare('DELETE FROM team_activities WHERE id = ?');
            $stmt->execute([$_POST['activity_id']]);
            if ($stmt->rowCount() > 0) {
                $message = 'Team activity deleted successfully!';
                $messageType = 'success';
            } else {
                $message = 'Activity not found.';
                $messageType = 'warning';
            }
        } catch (Exception $e) {
            $message = 'Error deleting activity: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

// Fetch team activities
$activities = [];
try {
    $stmt = $pdo->prepare('
        SELECT ta.*, u.full_name as organizer_name
        FROM team_activities ta
        LEFT JOIN users u ON ta.organizer_id = u.id
        ORDER BY ta.activity_date ASC
    ');
    $stmt->execute();
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching activities: ' . $e->getMessage());
}

?>

<div class="container" style="margin-top:90px; margin-bottom: 40px;">
    <div class="orgdev-toolbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="m-0">Organizational Development</h2>
            <p class="text-muted mt-2 mb-0">Team building activities and organizational initiatives</p>
        </div>
        <?php if (in_array($role, ['admin', 'manager', 'learning'])): ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createActivityModal">Create Org Dev Activity</button>
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
                <input type="hidden" name="page" value="orgdev">
                <div class="col-md-4">
                    <label class="form-label">Search Activities</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, department..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="planned" <?php echo ($_GET['status'] ?? '') === 'planned' ? 'selected' : ''; ?>>Planned</option>
                        <option value="in_progress" <?php echo ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="completed" <?php echo ($_GET['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
                <div class="col-md-2">
                    <a href="?page=orgdev" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Team Activities -->
    <div class="mb-5">
        <h3 class="mb-3">Team Activities</h3>
        <?php if ($activities): ?>
            <div class="row g-3">
                <?php $idx = 0; foreach ($activities as $activity): $idx++; $delay = ($idx - 1) * 0.08; ?>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm pop-in activity-card" style="animation-delay: <?php echo $delay; ?>s;"
                                 data-id="<?php echo intval($activity['id']); ?>"
                                 data-name="<?php echo htmlspecialchars($activity['name'], ENT_QUOTES); ?>"
                                 data-description="<?php echo htmlspecialchars($activity['description'], ENT_QUOTES); ?>"
                                 data-date="<?php echo htmlspecialchars($activity['activity_date'], ENT_QUOTES); ?>"
                                 data-department="<?php echo htmlspecialchars($activity['department'], ENT_QUOTES); ?>"
                                 data-budget="<?php echo htmlspecialchars($activity['budget'], ENT_QUOTES); ?>"
                                 data-participants="<?php echo intval($activity['participant_count']); ?>"
                                 data-status="<?php echo htmlspecialchars($activity['status'], ENT_QUOTES); ?>">
                            <img src="img/placeholder.gif" class="card-img-top" style="height:160px;object-fit:cover;" alt="">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($activity['name']); ?></h5>
                                <p class="card-text text-muted small mb-2"><?php echo htmlspecialchars(substr($activity['description'], 0, 60) . '...'); ?></p>
                                <div class="mb-2">
                                    <small class="text-secondary"><strong>Date:</strong> <?php echo htmlspecialchars($activity['activity_date']); ?></small>
                                </div>
                                <div class="mb-2">
                                    <small class="text-secondary"><strong>Department:</strong> <?php echo htmlspecialchars($activity['department'] ?? 'All'); ?></small>
                                </div>
                                <div class="mb-2">
                                    <small class="text-secondary"><strong>Participants:</strong> <?php echo htmlspecialchars($activity['participant_count'] ?? 0); ?></small>
                                </div>
                                <div class="mb-3">
                                    <small class="text-secondary"><strong>Budget:</strong> $<?php echo htmlspecialchars(number_format($activity['budget'] ?? 0, 2)); ?></small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-auto" style="gap:8px;">
                                    <span class="badge bg-<?php echo $activity['status'] === 'completed' ? 'success' : ($activity['status'] === 'ongoing' ? 'warning' : 'info'); ?>">
                                        <?php echo ucfirst(htmlspecialchars($activity['status'])); ?>
                                    </span>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-activity-btn"
                                            data-id="<?php echo intval($activity['id']); ?>"
                                            data-name="<?php echo htmlspecialchars($activity['name'], ENT_QUOTES); ?>"
                                            data-description="<?php echo htmlspecialchars($activity['description'], ENT_QUOTES); ?>"
                                            data-date="<?php echo htmlspecialchars($activity['activity_date'], ENT_QUOTES); ?>"
                                            data-department="<?php echo htmlspecialchars($activity['department'], ENT_QUOTES); ?>"
                                            data-budget="<?php echo htmlspecialchars($activity['budget'], ENT_QUOTES); ?>"
                                            data-participants="<?php echo intval($activity['participant_count']); ?>">Edit</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-activity-btn"
                                            data-id="<?php echo intval($activity['id']); ?>"
                                            data-name="<?php echo htmlspecialchars($activity['name'], ENT_QUOTES); ?>">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No team activities scheduled yet.</div>
        <?php endif; ?>
    </div>
</div>

<!-- View Activity Modal -->
<div class="modal fade" id="viewActivityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewActivityModalTitle">Activity Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <h4 id="viewActivityName"></h4>
                <p id="viewActivityDescription" class="text-muted"></p>
                <div class="mb-2"><small class="text-secondary"><strong>Date:</strong> <span id="viewActivityDate"></span></small></div>
                <div class="mb-2"><small class="text-secondary"><strong>Department:</strong> <span id="viewActivityDepartment"></span></small></div>
                <div class="mb-2"><small class="text-secondary"><strong>Participants:</strong> <span id="viewActivityParticipants"></span></small></div>
                <div class="mb-2"><small class="text-secondary"><strong>Budget:</strong> $<span id="viewActivityBudget"></span></small></div>
                <div class="mb-2"><small class="text-secondary"><strong>Status:</strong> <span id="viewActivityStatus"></span></small></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Activity Modal -->
<div class="modal fade" id="createActivityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activityModalTitle">Create Team Activity</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" id="activityAction" value="create_activity">
                <input type="hidden" name="activity_id" id="activityId" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Activity Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="activity_date" class="form-label">Activity Date</label>
                            <input type="date" id="activity_date" name="activity_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" id="department" name="department" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="budget" class="form-label">Budget ($)</label>
                            <input type="number" id="budget" name="budget" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="participant_count" class="form-label">Expected Participants</label>
                            <input type="number" id="participant_count" name="participant_count" class="form-control" min="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="activitySubmitBtn">Create Org Dev Activity</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Activity Modal -->
<div class="modal fade" id="deleteActivityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Activity</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the activity "<span id="activityToDeleteName"></span>"?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete_activity">
                    <input type="hidden" name="activity_id" id="activityIdToDelete" value="">
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
    // Edit activity button handler
    document.querySelectorAll('.edit-activity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('activityModalTitle').textContent = 'Edit Team Activity';
            document.getElementById('activitySubmitBtn').textContent = 'Update Activity';
            document.getElementById('activityAction').value = 'edit_activity';
            document.getElementById('activityId').value = this.dataset.id;
            document.getElementById('name').value = this.dataset.name;
            document.getElementById('description').value = this.dataset.description;
            document.getElementById('activity_date').value = this.dataset.date;

    // Click on card to view details
    document.querySelectorAll('.activity-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('form') || e.target.closest('a')) return;
            document.getElementById('viewActivityName').textContent = card.dataset.name || '';
            document.getElementById('viewActivityDescription').textContent = card.dataset.description || '';
            document.getElementById('viewActivityDate').textContent = card.dataset.date || '';
            document.getElementById('viewActivityDepartment').textContent = card.dataset.department || '';
            document.getElementById('viewActivityParticipants').textContent = card.dataset.participants || '';
            document.getElementById('viewActivityBudget').textContent = card.dataset.budget || '';
            document.getElementById('viewActivityStatus').textContent = card.dataset.status || '';
            const vmodal = new bootstrap.Modal(document.getElementById('viewActivityModal'));
            vmodal.show();
        });
    });
            document.getElementById('department').value = this.dataset.department;
            document.getElementById('budget').value = this.dataset.budget;
            document.getElementById('participant_count').value = this.dataset.participants;

            const modal = new bootstrap.Modal(document.getElementById('createActivityModal'));
            modal.show();
        });
    });

    // Delete activity button handler
    document.querySelectorAll('.delete-activity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('activityToDeleteName').textContent = this.dataset.name;
            document.getElementById('activityIdToDelete').value = this.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('deleteActivityModal'));
            modal.show();
        });
    });

    // Reset form when create modal is closed
    document.getElementById('createActivityModal').addEventListener('hide.bs.modal', function() {
        document.getElementById('activityModalTitle').textContent = 'Create Team Activity';
        document.getElementById('activitySubmitBtn').textContent = 'Create Org Dev Activity';
        document.getElementById('activityAction').value = 'create_activity';
        document.getElementById('activityId').value = '';
        document.getElementById('name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('activity_date').value = '';
        document.getElementById('department').value = '';
        document.getElementById('budget').value = '';
        document.getElementById('participant_count').value = '';
    });
});
</script>

<?php if (!defined('NO_FOOTER')) { require_once __DIR__ . '/footer.php'; } ?>
