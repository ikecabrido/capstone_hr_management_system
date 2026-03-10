<?php require_once __DIR__ . '/config.php';
// start output buffering so we can safely redirect after POST (PRG)
if (!headers_sent()) { ob_start(); }
if (!defined('NO_HEADER')) {
require_once __DIR__ . '/header.php';
}

$q = trim($_GET['q'] ?? '');
$currentUserId = get_current_user_id();
$currentUsername = $_SESSION['username'] ?? null;
$isAuthorized = can_manage();

$message = '';
$messageType = 'info';

// Protect POST actions - require login for enrollment, authorization required for management
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!is_logged_in()) {
    $message = 'You must be logged in to perform this action.';
    $messageType = 'danger';
  } elseif (in_array($_POST['action'] ?? '', ['create', 'edit', 'delete']) && !$isAuthorized) {
    $message = 'You do not have permission to manage programs. Only administrators and managers can create, edit, or delete programs.';
    $messageType = 'danger';
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  try {
    // Create training program
    if ($action === 'create' && in_array(current_role(), ['admin', 'manager'])) {
        if (!$currentUserId) {
            // sessions or DB state are inconsistent - require a logged-in user
            $message = 'Cannot create program: you must be logged in as an admin or manager.';
            $messageType = 'danger';
        } else {
            $name = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $image = trim($_POST['image'] ?? '');
            $date = trim($_POST['date'] ?? '');
            $capacity = intval($_POST['capacity'] ?? 0);
            $location = trim($_POST['location'] ?? '');
            $trainer = trim($_POST['trainer'] ?? '');
            $status = trim($_POST['status'] ?? 'Active');
            $sessions = intval($_POST['sessions'] ?? 1);

            $creatorId = $currentUserId;

            $stmt = $pdo->prepare('
                INSERT INTO training_programs (name, description, category, type, duration, created_by, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([$name, $description, 'General', 'Workshop', $sessions, $creatorId, $status]);
            $programId = $pdo->lastInsertId();
            
            // Store extra fields in a JSON meta field (we'll use description for now, but ideally add course_content)
            $message = 'Program created successfully.';
            $messageType = 'success';
        }
    }

    // Edit training program
    if ($action === 'edit' && in_array(current_role(), ['admin', 'manager'])) {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $date = trim($_POST['date'] ?? '');
        $capacity = intval($_POST['capacity'] ?? 0);
        $location = trim($_POST['location'] ?? '');
        $trainer = trim($_POST['trainer'] ?? '');
        $status = trim($_POST['status'] ?? 'Active');
        $sessions = intval($_POST['sessions'] ?? 1);
        
        $stmt = $pdo->prepare('
            UPDATE training_programs 
            SET name = ?, description = ?, category = ?, type = ?, duration = ?, status = ?
            WHERE id = ?
        ');
        $stmt->execute([$name, $description, 'General', $trainer, $sessions, $status, $id]);
        $message = 'Program updated.';
        $messageType = 'success';
    }

    // Delete training program
    if ($action === 'delete' && in_array(current_role(), ['admin', 'manager'])) {
        $id = intval($_POST['id'] ?? 0);
        
        // Delete enrollments first
        $stmt = $pdo->prepare('DELETE FROM training_enrollments WHERE program_id = ?');
        $stmt->execute([$id]);
        
        // Delete program
        $stmt = $pdo->prepare('DELETE FROM training_programs WHERE id = ?');
        $stmt->execute([$id]);
        $message = 'Program deleted.';
        $messageType = 'success';
    }

    // Enroll user
    if ($action === 'enroll' && $currentUserId) {
        $programId = intval($_POST['id'] ?? 0);
        
        // Check if already enrolled
        $stmt = $pdo->prepare('SELECT id FROM training_enrollments WHERE user_id = ? AND program_id = ?');
        $stmt->execute([$currentUserId, $programId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            $message = 'Already enrolled.';
            $messageType = 'warning';
        } else {
            $stmt = $pdo->prepare('
                INSERT INTO training_enrollments (user_id, program_id, status)
                VALUES (?, ?, ?)
            ');
            $stmt->execute([$currentUserId, $programId, 'pending']);
            $message = 'Enrolled successfully.';
            $messageType = 'success';
        }
    }

    // Unenroll user
    if ($action === 'unenroll' && $currentUserId) {
        $programId = intval($_POST['id'] ?? 0);
        
        $stmt = $pdo->prepare('DELETE FROM training_enrollments WHERE user_id = ? AND program_id = ?');
        $stmt->execute([$currentUserId, $programId]);
        $message = 'Unenrolled.';
        $messageType = 'success';
    }

    // Admin actions: ban/exempt/remove/unban
    if (in_array(current_role(), ['admin', 'manager', 'trainer'])) {
        if ($action === 'ban_user') {
            $programId = intval($_POST['id'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $stmt = $pdo->prepare('UPDATE training_enrollments SET status = ? WHERE user_id = ? AND program_id = ?');
                $stmt->execute(['banned', $user['id'], $programId]);
                $message = 'User banned.';
                $messageType = 'success';
            }
        }

        if ($action === 'unban_user') {
            $programId = intval($_POST['id'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $stmt = $pdo->prepare('UPDATE training_enrollments SET status = ? WHERE user_id = ? AND program_id = ?');
                $stmt->execute(['pending', $user['id'], $programId]);
                $message = 'User unbanned.';
                $messageType = 'success';
            }
        }

        if ($action === 'exempt_user') {
            $programId = intval($_POST['id'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $stmt = $pdo->prepare('UPDATE training_enrollments SET status = ? WHERE user_id = ? AND program_id = ?');
                $stmt->execute(['exempt', $user['id'], $programId]);
                $message = 'User exempted.';
                $messageType = 'success';
            }
        }

        if ($action === 'remove_user') {
            $programId = intval($_POST['id'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $stmt = $pdo->prepare('DELETE FROM training_enrollments WHERE user_id = ? AND program_id = ?');
                $stmt->execute([$user['id'], $programId]);
                $message = 'User removed from enrollment.';
                $messageType = 'success';
            }
        }
    }

    // Redirect after POST (skip redirect if we set a danger message so that it can be shown)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $messageType !== 'danger') {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
  } catch (Exception $e) {
    // log full exception for server-side diagnosis
    error_log('Training module error: ' . $e->getMessage());
    // expose basic exception message to user when in development
    $debugMsg = !empty($e->getMessage()) ? ' (' . htmlspecialchars($e->getMessage()) . ')' : '';
    $message = 'An error occurred. Please try again.' . $debugMsg;
    $messageType = 'danger';
  }
}

// Fetch training programs from database
$items = [];
try {
    $stmt = $pdo->query('SELECT * FROM training_programs ORDER BY created_at DESC');
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    error_log('Error fetching training programs: ' . $e->getMessage());
}

// Build enrollment details for each program
$enrollmentDetails = [];
try {
    foreach ($items as $program) {
        $programId = $program['id'];
        
        // Fetch all enrollments for this program with user details
        $stmt = $pdo->prepare('
            SELECT te.id, te.user_id, te.status, u.username, u.full_name, u.position, u.department, u.email
            FROM training_enrollments te
            JOIN users u ON te.user_id = u.id
            WHERE te.program_id = ?
        ');
        $stmt->execute([$programId]);
        $enrollmentDetails[$programId] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
} catch (Exception $e) {
    error_log('Error fetching enrollment details: ' . $e->getMessage());
}

// Get enrollment count for each program
$enrollmentCounts = [];
try {
    foreach ($items as $program) {
        $programId = $program['id'];
        $enrollmentCounts[$programId] = count($enrollmentDetails[$programId] ?? []);
    }
} catch (Exception $e) {
    error_log('Error calculating enrollment counts: ' . $e->getMessage());
}

?>

<div class="container" style="margin-top:90px; margin-bottom: 40px;">
  <div class="training-toolbar d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="m-0">Training Programs</h2>
      <p class="text-muted small mt-2 mb-0">Enhance your skills with our professional development programs</p>
    </div>
    <?php if (in_array(current_role(), ['admin','manager'])): ?>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProgramModal">Create Program</button>
    <?php endif; ?>
  </div>

  <?php if ($message): ?>
    <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php if ($messageType === 'danger' && stripos($message, 'must be logged in') !== false): ?>
      <p><a href="login.php" class="btn btn-sm btn-primary">Log in</a></p>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ($items): ?>
    <div class="row g-3 training-grid">
      <?php $__idx = 0; foreach ($items as $it): $__idx++; $delay = ($__idx - 1) * 0.12; ?>
        <?php
          // Map database fields for template compatibility
          $it['title'] = $it['name'] ?? '';
          $it['id'] = $it['id'] ?? '';
          $dataEnrolledArr = $enrollmentDetails[$it['id']] ?? [];
        ?>
        <div class="col-md-4">
             <div class="card h-100 training-card pop-in" style="position:relative; animation-delay: <?php echo $delay; ?>s;" 
              data-id="<?php echo intval($it['id']); ?>" 
              data-title="<?php echo htmlspecialchars($it['title']); ?>" 
              data-description="<?php echo htmlspecialchars($it['description']); ?>" 
              data-image="" 
              data-date="" 
              data-capacity="0" 
              data-location=""
              data-trainer="<?php echo htmlspecialchars($it['type'] ?? ''); ?>"
              data-sessions="<?php echo intval($it['duration'] ?? 1); ?>"
              data-enrolled='<?php echo htmlspecialchars(json_encode($dataEnrolledArr), ENT_QUOTES, "UTF-8"); ?>'
              data-status="<?php echo htmlspecialchars($it['status'] ?? 'Active'); ?>"
            >
            <img src="img/placeholder.gif" class="card-img-top" style="height:160px;object-fit:cover;" alt="">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?php echo htmlspecialchars($it['title']); ?></h5>
              <p class="card-text text-muted mb-2"><?php echo htmlspecialchars($it['description']); ?></p>
              <?php if (!empty($it['type']) || isset($it['duration'])): ?>
              <p class="text-center mb-2" style="font-size: 0.9rem;">
                <?php if (!empty($it['type'])): ?><small class="trainer-badge"><?php echo htmlspecialchars($it['type']); ?></small><?php endif; ?>
                <?php $sess = intval($it['duration'] ?? 1); ?>
                <small class="sessions-badge ms-2"><?php echo $sess; ?> session<?php echo $sess > 1 ? 's' : ''; ?></small>
              </p>
              <?php endif; ?>
              <!-- Card meta grid: status | date | remaining -->
              <div class="card-meta-grid mb-2">
                <div class="d-flex justify-content-between gap-1">
                  <small class="meta-label"><?php echo htmlspecialchars($it['status'] ?? 'Active'); ?></small>
                  <small class="meta-label">—</small>
                  <small class="meta-label">Rem: 0</small>
                </div>
              </div>
              <div class="mt-auto d-flex justify-content-between align-items-center">
                <small class="text-muted">Enrolled: <?php echo $enrollmentCounts[$it['id']] ?? 0; ?></small>
                <div class="card-action-set">
                  <?php if (in_array(current_role(), ['admin','manager'])): ?>
                    <button class="btn btn-sm btn-outline-secondary me-1 edit-program-btn" 
                      data-id="<?php echo intval($it['id']); ?>" 
                      data-title="<?php echo htmlspecialchars($it['title']); ?>" 
                      data-description="<?php echo htmlspecialchars($it['description']); ?>" 
                      data-image="" 
                      data-date="" 
                      data-capacity="0" 
                        data-location=""
                        data-trainer="<?php echo htmlspecialchars($it['type'] ?? ''); ?>"
                        data-sessions="<?php echo intval($it['duration'] ?? 1); ?>"
                        data-status="<?php echo htmlspecialchars($it['status'] ?? 'Active'); ?>"
                    >Edit</button>
                    <form method="post" style="display:inline" class="delete-form" data-id="<?php echo intval($it['id']); ?>">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo intval($it['id']); ?>">
                      <button type="button" class="btn btn-sm btn-danger delete-trigger">Delete</button>
                    </form>
                  <?php else: ?>
                    <?php $enrolled = false; if ($currentUserId) { foreach ($dataEnrolledArr as $ee) { if ($ee['user_id'] == $currentUserId) { $enrolled = true; break; } } } ?>
                    <?php if ($enrolled): ?>
                      <form method="post" style="display:inline">
                        <input type="hidden" name="action" value="unenroll">
                        <input type="hidden" name="id" value="<?php echo intval($it['id']); ?>">
                        <button class="btn btn-sm btn-outline-warning">Unenroll</button>
                      </form>
                    <?php else: ?>
                      <form method="post" style="display:inline">
                        <input type="hidden" name="action" value="enroll">
                        <input type="hidden" name="id" value="<?php echo intval($it['id']); ?>">
                        <button class="btn btn-sm btn-primary">Enroll</button>
                      </form>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
              <div class="d-none card-actions">
                <?php if (in_array(current_role(), ['admin','manager'])): ?>
                  <button class="btn btn-sm btn-outline-secondary me-1 edit-program-btn" 
                    data-id="<?php echo intval($it['id']); ?>" 
                    data-title="<?php echo htmlspecialchars($it['title']); ?>" 
                    data-description="<?php echo htmlspecialchars($it['description']); ?>" 
                    data-image="" 
                    data-date="" 
                    data-capacity="0" 
                      data-location=""
                      data-trainer="<?php echo htmlspecialchars($it['type'] ?? ''); ?>"
                      data-status="<?php echo htmlspecialchars($it['status'] ?? 'Active'); ?>"
                  >Edit</button>
                  <form method="post" style="display:inline" class="delete-form" data-id="<?php echo intval($it['id']); ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo intval($it['id']); ?>">
                    <button type="button" class="btn btn-sm btn-danger delete-trigger">Delete</button>
                  </form>
                <?php else: ?>
                  <?php $tmpEnrolled = false; if ($currentUserId) { foreach ($dataEnrolledArr as $ee) { if ($ee['user_id'] == $currentUserId) { $tmpEnrolled = true; break; } } } ?>
                  <?php if ($tmpEnrolled): ?>
                    <form method="post" style="display:inline">
                      <input type="hidden" name="action" value="unenroll">
                      <input type="hidden" name="id" value="<?php echo intval($it['id']); ?>">
                      <button class="btn btn-sm btn-outline-warning">Unenroll</button>
                    </form>
                  <?php else: ?>
                    <form method="post" style="display:inline">
                      <input type="hidden" name="action" value="enroll">
                      <input type="hidden" name="id" value="<?php echo intval($it['id']); ?>">
                      <button class="btn btn-sm btn-primary">Enroll</button>
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
    <p class="mb-0">No training content yet.</p>
  <?php endif; ?>

</div>

<!-- Edit modal -->
<div class="modal fade" id="editProgramModal" tabindex="-1" aria-labelledby="editProgramModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="post" id="editProgramForm" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="editProgramModalLabel">Edit Program</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="id" id="modal-program-id">
          <div class="row g-1">
            <div class="col-12 mb-1"><input id="modal-title" class="form-control" name="title" placeholder="Program title"></div>
            <div class="col-md-6"><input id="modal-trainer" class="form-control" name="trainer" placeholder="Trainer name (optional)"></div>
            <div class="col-md-6"><input id="modal-image" class="form-control" name="image" placeholder="Image URL (optional)"></div>
            <div class="col-md-3"><input id="modal-date" class="form-control" name="date" placeholder="YYYY-MM-DD (optional)"></div>
            <div class="col-md-3"><input id="modal-capacity" class="form-control" name="capacity" placeholder="Capacity" type="number"></div>
            <div class="col-md-3"><input id="modal-sessions" class="form-control" name="sessions" placeholder="Sessions" type="number" min="1"></div>
            <div class="col-md-3">
              <select id="modal-status" name="status" class="form-select">
                <option value="Active">Active</option>
                <option value="Upcoming">Upcoming</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
            <div class="col-md-6 mt-2"><input id="modal-location" class="form-control" name="location" placeholder="Location (optional)"></div>
            <div class="col-md-6 mt-2"><textarea id="modal-description" class="form-control" name="description" placeholder="Short description"></textarea></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var modalEl = document.getElementById('editProgramModal');
  if (!modalEl) return;
  var editButtons = document.querySelectorAll('.edit-program-btn');
  editButtons.forEach(function(btn){
    btn.addEventListener('click', function(e){
      var id = btn.getAttribute('data-id');
      document.getElementById('modal-program-id').value = id;
      document.getElementById('modal-title').value = btn.getAttribute('data-title') || '';
      document.getElementById('modal-description').value = btn.getAttribute('data-description') || '';
      document.getElementById('modal-image').value = btn.getAttribute('data-image') || '';
      document.getElementById('modal-date').value = btn.getAttribute('data-date') || '';
      document.getElementById('modal-capacity').value = btn.getAttribute('data-capacity') || '';
      document.getElementById('modal-location').value = btn.getAttribute('data-location') || '';
      document.getElementById('modal-trainer').value = btn.getAttribute('data-trainer') || '';
      document.getElementById('modal-sessions').value = btn.getAttribute('data-sessions') || '';
      document.getElementById('modal-status').value = btn.getAttribute('data-status') || '';
      var modal = new bootstrap.Modal(modalEl);
      modal.show();
    });
  });
});
</script>

<script>
// Reset create form when modal opens and provide simple client-side validation
document.addEventListener('DOMContentLoaded', function(){
  var createModalEl = document.getElementById('createProgramModal');
  if (!createModalEl) return;
  var createForm = document.getElementById('createProgramForm');
  createModalEl.addEventListener('show.bs.modal', function(){
    if (createForm) {
      createForm.reset();
      // ensure default sessions = 1
      var s = createForm.querySelector('input[name="sessions"]');
      if (s) s.value = 1;
    }
  });
  // basic required check
  if (createForm) {
    createForm.addEventListener('submit', function(e){
      var title = createForm.querySelector('input[name="title"]');
      if (!title || String(title.value || '').trim() === '') {
        e.preventDefault();
        title.focus();
        return false;
      }
    });
  }
});
</script>

<!-- Create modal -->
<div class="modal fade" id="createProgramModal" tabindex="-1" aria-labelledby="createProgramModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="post" id="createProgramForm" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="createProgramModalLabel">Create Program</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="create">
          <div class="row g-1">
            <div class="col-12 mb-1"><input id="create-title" class="form-control" name="title" placeholder="Program title" required></div>
            <div class="col-md-6"><input id="create-trainer" class="form-control" name="trainer" placeholder="Trainer name (optional)"></div>
            <div class="col-md-6"><input id="create-image" class="form-control" name="image" placeholder="Image URL (optional)"></div>
            <div class="col-md-3"><input id="create-date" class="form-control" name="date" placeholder="YYYY-MM-DD (optional)"></div>
            <div class="col-md-3"><input id="create-capacity" class="form-control" name="capacity" placeholder="Capacity" type="number" min="0"></div>
            <div class="col-md-3"><input id="create-sessions" class="form-control" name="sessions" placeholder="Sessions" type="number" min="1" value="1"></div>
            <div class="col-md-3">
              <select id="create-status" name="status" class="form-select">
                <option value="Active">Active</option>
                <option value="Upcoming">Upcoming</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
            <div class="col-12 mt-2"><textarea id="create-description" class="form-control" name="description" placeholder="Short description"></textarea></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-create">Create program</button>
        </div>
      </form>
    </div>
  </div>
</div>

    <!-- View modal (shows program details) -->
    <div class="modal fade" id="viewProgramModal" tabindex="-1" aria-labelledby="viewProgramModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="viewProgramModalLabel">Program Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <img id="view-image" src="img/placeholder.gif" class="img-fluid mb-3 view-image-anim" style="max-height:300px;object-fit:cover;width:100%;border-radius:8px;" alt="">
            <h4 id="view-title"></h4>
            <p id="view-description" class="text-muted"></p>
            <p id="view-trainer" class="text-muted mb-2" style="font-size: 0.9rem;"></p>
            <div id="view-meta-grid" class="mb-2">
              <div class="d-flex justify-content-between gap-1">
                <div id="meta-location" class="meta-label"></div>
                <div id="meta-status" class="meta-label"></div>
                <div id="meta-date" class="meta-label"></div>
                <div id="meta-capacity" class="meta-label"></div>
                <div id="meta-remaining" class="meta-label"></div>
              </div>
            </div>
            <div id="view-enrolled" class="mt-3" style="display:none;"></div>
          </div>
          <div class="modal-footer">
            <div id="view-actions" class="ms-auto"></div>
          </div>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      function escapeHtml(str) {
        if (!str) return '';
        return String(str)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;');
      }

      function openViewModalFromCard(card){
        var modalEl = document.getElementById('viewProgramModal');
        document.getElementById('view-title').textContent = card.dataset.title || '';
        document.getElementById('view-description').textContent = card.dataset.description || '';
        var trainerText = card.dataset.trainer || '';
        var sessionsCount = parseInt(card.dataset.sessions || 1, 10) || 1;
        var trainerEl = document.getElementById('view-trainer');
        if (trainerText || sessionsCount) {
          var sessLabel = sessionsCount + ' session' + (sessionsCount > 1 ? 's' : '');
          var trainerHtml = '';
          if (trainerText) trainerHtml += '<small class="trainer-badge">' + escapeHtml(trainerText) + '</small>';
          trainerHtml += ' <small class="sessions-badge ms-2">' + escapeHtml(sessLabel) + '</small>';
          trainerEl.innerHTML = trainerHtml;
          trainerEl.className = 'text-center mb-2';
          trainerEl.style.fontSize = '0.9rem';
        } else {
          trainerEl.textContent = '';
          trainerEl.className = 'text-muted mb-2';
        }
        var img = document.getElementById('view-image');
        var raw = card.dataset.image || '';
        img.src = (raw && raw.indexOf('placeholder.com') === -1) ? raw : 'img/placeholder.gif';
        // Populate meta grid: location | status  and date | capacity | remaining slots
        var locText = card.dataset.location || '';
        var statusText = card.dataset.status || 'Active';
        var dateText = card.dataset.date || '';
        var capacity = parseInt(card.dataset.capacity || 0, 10) || 0;
        var enrolledJson = card.dataset.enrolled || '[]';
        var enrolledList = [];
        try {
          enrolledList = JSON.parse(enrolledJson);
        } catch(e) {
          // try to decode HTML entities then parse (some browsers/html may entity-encode JSON in attributes)
          try {
            var ta = document.createElement('textarea');
            ta.innerHTML = enrolledJson;
            var dec = ta.value;
            enrolledList = JSON.parse(dec);
          } catch(err) {
            enrolledList = [];
          }
        }
        var enrolledCount = Array.isArray(enrolledList) ? enrolledList.length : 0;
        var remaining = capacity - enrolledCount;
        if (remaining < 0) remaining = 0;
        document.getElementById('meta-location').textContent = locText || '';
        document.getElementById('meta-location').className = 'meta-label';
        document.getElementById('meta-status').textContent = statusText || '';
        document.getElementById('meta-status').className = 'meta-label';
        document.getElementById('meta-date').textContent = dateText || '';
        document.getElementById('meta-date').className = 'meta-label';
        document.getElementById('meta-capacity').textContent = 'Cap: ' + capacity;
        document.getElementById('meta-capacity').className = 'meta-label';
        document.getElementById('meta-remaining').textContent = 'Rem: ' + remaining;
        document.getElementById('meta-remaining').className = 'meta-label';
        var actions = card.querySelector('.card-actions');
        var viewActionsEl = document.getElementById('view-actions');
        if (actions && viewActionsEl) {
          var clone = actions.cloneNode(true);
          // remove edit and delete controls from the cloned actions before showing in the view modal
          clone.querySelectorAll('.edit-program-btn, .delete-form').forEach(function(el){ el.remove(); });
          viewActionsEl.innerHTML = clone.innerHTML;
        } else if (viewActionsEl) {
          viewActionsEl.innerHTML = '';
        }

        // Show enrolled users to privileged roles (admin, manager, trainer)
        try {
          var enrolledContainer = document.getElementById('view-enrolled');
          enrolledContainer.style.display = 'none';
          enrolledContainer.innerHTML = '';
          var role = (document.body && document.body.dataset && document.body.dataset.role) ? String(document.body.dataset.role).toLowerCase().trim() : 'guest';
          if (['admin','manager','trainer'].indexOf(role) !== -1) {
              var enrolledJson = card.dataset.enrolled || '[]';
              var enrolledList = [];
              try { enrolledList = JSON.parse(enrolledJson); } catch(e) { enrolledList = []; }
              var count = enrolledList.length || 0;
              var html = '<h6 class="mb-2">Enrolled ('+count+')</h6>';
            if (count > 0) {
              html += '<div class="table-responsive"><table class="table table-sm table-striped mb-0">';
              html += '<thead><tr><th>Employee ID</th><th>Name</th><th>Position</th><th>Department</th><th>Contact</th><th>Status</th><th>Actions</th></tr></thead>';
              html += '<tbody>';
              enrolledList.forEach(function(u){
                if (u && typeof u === 'object') {
                  var idText = u.user_id ? escapeHtml(String(u.user_id)) : '';
                  var name = u.full_name ? escapeHtml(u.full_name) : (u.username ? escapeHtml(u.username) : '');
                  var pos = u.position ? escapeHtml(u.position) : '';
                  var dept = u.department ? escapeHtml(u.department) : '';
                  var email = u.email ? escapeHtml(u.email) : '';
                  var status = u.status || 'ok';
                  var statusHtml = status === 'banned' ? '<span class="badge bg-danger">Banned</span>' : (status === 'exempt' ? '<span class="badge bg-warning text-dark">Exempt</span>' : '<span class="badge bg-success">OK</span>');
                  // actions for privileged roles
                  var actionsHtml = '';
                  var theId = escapeHtml(card.dataset.id || '');
                  var theUser = u.username ? escapeHtml(u.username) : '';
                  if (['admin','manager','trainer'].indexOf(role) !== -1) {
                    // Remove -> destructive style
                    actionsHtml += '<form method="post" class="d-inline me-1"><input type="hidden" name="action" value="remove_user"><input type="hidden" name="id" value="'+theId+'"><input type="hidden" name="username" value="'+theUser+'"><button class="btn btn-sm btn-danger">Remove</button></form>';
                    // Ban / Unban -> use outline secondary or success for unban
                    if (status !== 'banned') {
                      actionsHtml += '<form method="post" class="d-inline me-1"><input type="hidden" name="action" value="ban_user"><input type="hidden" name="id" value="'+theId+'"><input type="hidden" name="username" value="'+theUser+'"><button class="btn btn-sm btn-outline-secondary">Ban</button></form>';
                    } else {
                      actionsHtml += '<form method="post" class="d-inline me-1"><input type="hidden" name="action" value="unban_user"><input type="hidden" name="id" value="'+theId+'"><input type="hidden" name="username" value="'+theUser+'"><button class="btn btn-sm btn-success">Unban</button></form>';
                    }
                    // Exempt / Clear -> warning outline or secondary
                    if (status !== 'exempt') {
                      actionsHtml += '<form method="post" class="d-inline me-1"><input type="hidden" name="action" value="exempt_user"><input type="hidden" name="id" value="'+theId+'"><input type="hidden" name="username" value="'+theUser+'"><button class="btn btn-sm btn-outline-warning">Exempt</button></form>';
                    } else {
                      actionsHtml += '<form method="post" class="d-inline me-1"><input type="hidden" name="action" value="unban_user"><input type="hidden" name="id" value="'+theId+'"><input type="hidden" name="username" value="'+theUser+'"><button class="btn btn-sm btn-outline-secondary">Clear</button></form>';
                    }
                  }
                  html += '<tr>' +
                          '<td>' + idText + '</td>' +
                          '<td>' + name + '</td>' +
                          '<td>' + pos + '</td>' +
                          '<td>' + dept + '</td>' +
                          '<td>' + (email ? '<a href="mailto:'+email+'">'+email+'</a>' : '') + '</td>' +
                          '<td>' + statusHtml + '</td>' +
                          '<td>' + actionsHtml + '</td>' +
                          '</tr>';
                } else {
                  var value = escapeHtml(String(u));
                  html += '<tr><td></td><td>' + value + '</td><td></td><td></td><td></td><td></td><td></td></tr>';
                }
              });
              html += '</tbody></table></div>';
            } else {
              html += '<p class="mb-0 text-muted">No enrollments yet.</p>';
            }
            enrolledContainer.innerHTML = html;
            enrolledContainer.style.display = '';
          }
        } catch(e) {
          console.error('Error rendering enrollments', e);
        }

        var modal = new bootstrap.Modal(modalEl);
        modal.show();
      }

      document.querySelectorAll('.training-card').forEach(function(card){
        card.addEventListener('click', function(e){
          if (e.target.closest('button') || e.target.closest('form') || e.target.closest('a')) return;
          openViewModalFromCard(card);
        });
      });

      // Scroll reveal: remove pop-in class initially, re-add on scroll into view
      var cards = document.querySelectorAll('.training-card.pop-in');
      cards.forEach(function(card) { card.classList.remove('pop-in'); });
      var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry){
          if (entry.isIntersecting) {
            entry.target.classList.add('pop-in');
          }
        });
      }, { threshold: 0.1 });
      cards.forEach(function(card) { observer.observe(card); });
    });
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
          var card = document.querySelector('.training-card[data-id="'+CSS.escape(pendingId)+'"]');
          if (card) title = card.dataset.title || '';
          document.getElementById('deleteConfirmText').textContent = title ? 'Delete "' + title + '"? This action cannot be undone.' : 'Delete this program?';
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
        // close delete modal then open edit modal for the same program
        deleteModal.hide();
        // find the edit button on the card and trigger click
        var editBtn = document.querySelector('.training-card[data-id="'+CSS.escape(pendingId)+'"] .edit-program-btn');
        if (editBtn) editBtn.click();
      });
    });
    </script>

    <?php if (!defined('NO_FOOTER')) { require_once __DIR__ . '/footer.php'; } ?>

