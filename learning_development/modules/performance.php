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

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_review' && in_array($role, ['admin', 'manager', 'learning'])) {
        try {
            $stmt = $pdo->prepare('
                INSERT INTO performance_reviews (employee_id, reviewer_id, review_period_start, review_period_end, rating, comments, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ');
                $stmt->execute([
                $_POST['employee_id'],
                $userId,
                $_POST['review_period_start'],
                $_POST['review_period_end'],
                $_POST['rating'] ?? 0,
                $_POST['comments'] ?? '',
                $_POST['status'] ?? 'draft'
            ]);
            $message = 'Performance review created successfully!';
        } catch (Exception $e) {
            $message = 'Error creating review: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
    // Edit existing review
    if ($action === 'edit_review') {
        $reviewId = intval($_POST['review_id'] ?? 0);
        if ($reviewId <= 0) {
            $message = 'Invalid review id.';
            $messageType = 'danger';
        } else {
            try {
                $stmt = $pdo->prepare('SELECT reviewer_id FROM performance_reviews WHERE id = ?');
                $stmt->execute([$reviewId]);
                $orig = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$orig) {
                    $message = 'Review not found.';
                    $messageType = 'danger';
                } elseif (in_array($role, ['admin', 'manager', 'learning']) || ($userId && $userId == $orig['reviewer_id'])) {
                    $stmt = $pdo->prepare('UPDATE performance_reviews SET employee_id = ?, review_period_start = ?, review_period_end = ?, rating = ?, comments = ?, status = ? WHERE id = ?');
                    $stmt->execute([
                        $_POST['employee_id'] ?? null,
                        $_POST['review_period_start'] ?? null,
                        $_POST['review_period_end'] ?? null,
                        ($_POST['rating'] === "" ? null : ($_POST['rating'] ?? null)),
                        $_POST['comments'] ?? '',
                        $_POST['status'] ?? 'draft',
                        $reviewId
                    ]);
                    $message = 'Performance review updated successfully!';
                } else {
                    $message = 'Permission denied to edit this review.';
                    $messageType = 'danger';
                }
            } catch (Exception $e) {
                $message = 'Error updating review: ' . $e->getMessage();
                $messageType = 'danger';
            }
        }
    }
}

// Fetch performance reviews
$reviews = [];
try {
    $stmt = $pdo->prepare('
        SELECT pr.*, u1.full_name as employee_name, u2.full_name as reviewer_name
        FROM performance_reviews pr
        LEFT JOIN users u1 ON pr.employee_id = u1.id
        LEFT JOIN users u2 ON pr.reviewer_id = u2.id
        ORDER BY pr.created_at DESC
        LIMIT 20
    ');
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching reviews: ' . $e->getMessage());
}

// Fetch user's reviews
$userReviews = [];
if ($userId) {
    try {
        $stmt = $pdo->prepare('
            SELECT pr.*, u.full_name as reviewer_name
            FROM performance_reviews pr
            LEFT JOIN users u ON pr.reviewer_id = u.id
            WHERE pr.employee_id = ?
            ORDER BY pr.review_period_end DESC
        ');
        $stmt->execute([$userId]);
        $userReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error fetching user reviews: ' . $e->getMessage());
    }
}
    // Fetch users for selection in Create Review modal
    $allUsers = [];
    try {
        $stmt = $pdo->prepare('SELECT id, full_name, username, role FROM users ORDER BY full_name ASC');
        $stmt->execute();
        $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error fetching users for selection: ' . $e->getMessage());
    }


?>

<div class="container" style="margin-top:90px; margin-bottom: 40px;">
    <div class="performance-toolbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="m-0">Performance Management</h2>
            <p class="text-muted mt-2 mb-0">Track and manage employee performance reviews</p>
        </div>
        <?php if (in_array($role, ['admin', 'manager', 'learning'])): ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createReviewModal">Create Performance Review</button>
        <?php endif; ?>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- User's Reviews -->
    <?php if ($username && $userReviews): ?>
        <div class="mb-5">
            <h3 class="mb-3">My Performance Reviews</h3>
            <div class="row g-3">
                <?php foreach ($userReviews as $review): ?>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0">Review by <?php echo htmlspecialchars($review['reviewer_name']); ?></h5>
                                    <div class="d-flex align-items-center" style="gap:8px;">
                                        <span class="badge badge-outline badge-outline-<?php echo htmlspecialchars($review['status']); ?>"><?php echo ucfirst(htmlspecialchars($review['status'])); ?></span>
                                        <?php if ($userId && (in_array($role, ['admin','manager','learning']) || $userId == $review['reviewer_id'])): ?>
                                            <button type="button" class="btn btn-outline-secondary btn-sm edit-review-btn"
                                                data-id="<?php echo intval($review['id']); ?>"
                                                data-employee-id="<?php echo intval($review['employee_id']); ?>"
                                                data-start="<?php echo htmlspecialchars($review['review_period_start'], ENT_QUOTES); ?>"
                                                data-end="<?php echo htmlspecialchars($review['review_period_end'], ENT_QUOTES); ?>"
                                                data-rating="<?php echo htmlspecialchars($review['rating'] ?? '', ENT_QUOTES); ?>"
                                                data-comments="<?php echo htmlspecialchars($review['comments'] ?? '', ENT_QUOTES); ?>"
                                                data-status="<?php echo htmlspecialchars($review['status'] ?? '', ENT_QUOTES); ?>"
                                            >Edit</button>
                                        <?php endif; ?>

                                    </div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-secondary"><strong>Period:</strong></small>
                                    <p class="mb-0"><?php echo htmlspecialchars($review['review_period_start']); ?> to <?php echo htmlspecialchars($review['review_period_end']); ?></p>
                                </div>
                                <?php if ($review['rating']): ?>
                                    <div class="mb-3">
                                        <small class="text-secondary"><strong>Rating:</strong></small>
                                        <p class="mb-0"><?php echo number_format($review['rating'], 1); ?> / 5.0</p>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <small class="text-secondary"><strong>Comments:</strong></small>
                                    <p class="text-muted small mb-0"><?php echo htmlspecialchars(substr($review['comments'], 0, 100) . '...'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- All Reviews (Manager/Admin) -->
    <?php if (in_array($role, ['admin', 'manager', 'learning']) && !empty($reviews)): ?>
        <div class="mb-5">
            <h3 class="mb-3">Performance Reviews</h3>
            <div class="row g-3">
                <?php $idx = 0; foreach ($reviews as $review): $idx++; $delay = ($idx - 1) * 0.08; ?>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm pop-in" style="animation-delay: <?php echo $delay; ?>s;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($review['employee_name']); ?></h5>
                                    <div class="d-flex align-items-center" style="gap:8px;">
                                        <span class="badge badge-outline badge-outline-<?php echo htmlspecialchars($review['status']); ?>"><?php echo htmlspecialchars($review['status']); ?></span>
                                        <?php if ($userId && (in_array($role, ['admin','manager','learning']) || $userId == $review['reviewer_id'])): ?>
                                            <button type="button" class="btn btn-outline-secondary btn-sm edit-review-btn"
                                                data-id="<?php echo intval($review['id']); ?>"
                                                data-employee-id="<?php echo intval($review['employee_id']); ?>"
                                                data-employee-name="<?php echo htmlspecialchars($review['employee_name'], ENT_QUOTES); ?>"
                                                data-start="<?php echo htmlspecialchars($review['review_period_start'], ENT_QUOTES); ?>"
                                                data-end="<?php echo htmlspecialchars($review['review_period_end'], ENT_QUOTES); ?>"
                                                data-rating="<?php echo htmlspecialchars($review['rating'] ?? '', ENT_QUOTES); ?>"
                                                data-comments="<?php echo htmlspecialchars($review['comments'] ?? '', ENT_QUOTES); ?>"
                                                data-status="<?php echo htmlspecialchars($review['status'] ?? '', ENT_QUOTES); ?>"
                                            >Edit</button>
                                        <?php endif; ?>

                                    </div>
                                </div>
                                <p class="text-muted small mb-2">Reviewed by: <?php echo htmlspecialchars($review['reviewer_name']); ?></p>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-secondary"><strong>Period Start:</strong></small>
                                        <p class="small mb-0"><?php echo htmlspecialchars($review['review_period_start']); ?></p>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-secondary"><strong>Period End:</strong></small>
                                        <p class="small mb-0"><?php echo htmlspecialchars($review['review_period_end']); ?></p>
                                    </div>
                                </div>
                                <?php if ($review['rating']): ?>
                                    <div class="mb-3">
                                        <small class="text-secondary"><strong>Rating:</strong></small>
                                        <p class="mb-0">
                                            <strong><?php echo number_format($review['rating'], 1); ?>/5.0</strong>
                                            <?php for ($i = 0; $i < floor($review['rating']); $i++): ?>
                                                <span class="text-warning">★</span>
                                            <?php endfor; ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create Review Modal -->
<div class="modal fade" id="createReviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Create Performance Review</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" id="modal_action" value="create_review">
                <input type="hidden" id="review_id" name="review_id" value="">
                <div class="modal-body">
                    <div class="mb-3">
                            <label class="form-label">Employee (search by name or role)</label>
                            <div class="employee-combobox mb-2">
                                <div class="d-flex" style="gap:8px;">
                                    <button type="button" id="employee_toggle" class="btn btn-outline-secondary w-100 text-start" aria-expanded="false">-- Select employee --</button>
                                    <button type="button" id="employee_clear" class="btn btn-outline-secondary" title="Clear selection" style="display:none;">✕</button>
                                </div>
                                <input type="text" id="employee_search" class="form-control employee-search mt-2" placeholder="Type name or role to filter..." style="display:none;">
                                <input type="hidden" id="employee_id_input" name="employee_id" value="">
                                <div id="employee_list" class="employee-list list-group mt-2" role="listbox" aria-label="Employee list" style="display:none; max-height:220px; overflow:auto;">
                                    <?php foreach ($allUsers as $u): ?>
                                        <button type="button" class="list-group-item list-group-item-action employee-item" data-id="<?php echo intval($u['id']); ?>" data-name="<?php echo htmlspecialchars($u['full_name']); ?>" data-role="<?php echo htmlspecialchars($u['role']); ?>"><?php echo htmlspecialchars($u['full_name'] . ' (' . $u['role'] . ')'); ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="form-text">Click the selector, then type to filter; select the person to review.</div>
                        </div>
                        <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="review_period_start" class="form-label">Review Period Start</label>
                            <input type="date" id="review_period_start" name="review_period_start" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="review_period_end" class="form-label">Review Period End</label>
                            <input type="date" id="review_period_end" name="review_period_end" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating (1-5)</label>
                        <select id="rating" name="rating" class="form-select">
                            <option value="">-- Select Rating --</option>
                            <option value="1">1 - Poor</option>
                            <option value="2">2 - Below Average</option>
                            <option value="3">3 - Average</option>
                            <option value="4">4 - Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comments" class="form-label">Comments</label>
                        <textarea id="comments" name="comments" class="form-control" rows="4" placeholder="Provide detailed feedback"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" id="modal_submit_btn" class="btn btn-primary">Create Performance Review</button>
                </div>
            </form>
        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function(){
    var combobox = document.querySelector('.employee-combobox');
    var toggle = document.getElementById('employee_toggle');
    var clearBtn = document.getElementById('employee_clear');
    var search = document.getElementById('employee_search');
    var list = document.getElementById('employee_list');
    var hiddenInput = document.getElementById('employee_id_input');
    var items = list ? Array.from(list.querySelectorAll('.employee-item')) : [];
    if (!combobox || !toggle || !search || !list || !hiddenInput) return;

    function openCombobox(){
        combobox.classList.add('open');
        toggle.setAttribute('aria-expanded','true');
        search.style.display = '';
        list.style.display = 'block';
        list.setAttribute('aria-hidden','false');
        setTimeout(function(){ search.focus(); }, 10);
    }
    function closeCombobox(){
        combobox.classList.remove('open');
        toggle.setAttribute('aria-expanded','false');
        search.style.display = 'none';
        list.style.display = 'none';
        list.setAttribute('aria-hidden','true');
    }

    toggle.addEventListener('click', function(){
        if (combobox.classList.contains('open')) closeCombobox(); else openCombobox();
    });

    clearBtn.addEventListener('click', function(){
        hiddenInput.value = '';
        toggle.textContent = '-- Select employee --';
        clearBtn.style.display = 'none';
        items.forEach(function(it){ it.classList.remove('active'); it.style.display = ''; });
    });

    function filterItems(){
        var q = search.value.trim().toLowerCase();
        items.forEach(function(item){
            var name = (item.dataset.name || '').toLowerCase();
            var role = (item.dataset.role || '').toLowerCase();
            var text = (item.textContent || '').toLowerCase();
            if (q === '' || name.indexOf(q) !== -1 || role.indexOf(q) !== -1 || text.indexOf(q) !== -1) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
                item.classList.remove('active');
            }
        });
    }

    items.forEach(function(item){
        item.addEventListener('click', function(){
            items.forEach(function(it){ it.classList.remove('active'); });
            item.classList.add('active');
            hiddenInput.value = item.dataset.id;
            toggle.textContent = item.textContent.trim();
            clearBtn.style.display = '';
            closeCombobox();
        });
    });

    var modal = document.getElementById('createReviewModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(){
            // Only reset if opening in create mode — preserve fields for edit mode
            var actionInput = document.getElementById('modal_action');
            if (!actionInput || actionInput.value === 'create_review') {
                closeCombobox();
                search.value = '';
                hiddenInput.value = '';
                items.forEach(function(it){ it.style.display = ''; it.classList.remove('active'); });
                var modalTitle = document.getElementById('modalTitle');
                if (modalTitle) modalTitle.textContent = 'Create Performance Review';
                var submitBtn = document.getElementById('modal_submit_btn'); if (submitBtn) submitBtn.textContent = 'Create Performance Review';
                var reviewIdEl = document.getElementById('review_id'); if (reviewIdEl) reviewIdEl.value = '';
                var statusSel = document.getElementById('status'); if (statusSel) statusSel.value = 'draft';
                var ratingSel = document.getElementById('rating'); if (ratingSel) ratingSel.value = '';
                var commentsEl = document.getElementById('comments'); if (commentsEl) commentsEl.value = '';
            }
        });
        modal.addEventListener('shown.bs.modal', function(){
            // focus toggle so browser doesn't auto-open inputs
            setTimeout(function(){ toggle.focus(); }, 10);
        });
        modal.addEventListener('hidden.bs.modal', function(){
            closeCombobox();
        });
    }

    var form = document.querySelector('#createReviewModal form');
    if (form) {
        form.addEventListener('submit', function(e){
            if (!hiddenInput.value) {
                e.preventDefault();
                alert('Please select an employee to review.');
            }
        });
    }

    search.addEventListener('input', filterItems);

    // Edit buttons: populate modal and open in edit mode
    var editButtons = document.querySelectorAll('.edit-review-btn');
    Array.from(editButtons).forEach(function(btn){
        btn.addEventListener('click', function(){
            var id = btn.dataset.id;
            var empId = btn.dataset.employeeId || '';
            var empName = btn.dataset.employeeName || '';
            var start = btn.dataset.start || '';
            var end = btn.dataset.end || '';
            var ratingVal = btn.dataset.rating || '';
            var commentsVal = btn.dataset.comments || '';
            var statusVal = btn.dataset.status || 'draft';

            var actionInput = document.getElementById('modal_action');
            var reviewIdInput = document.getElementById('review_id');
            var ratingSel = document.getElementById('rating');
            var commentsEl = document.getElementById('comments');
            var startEl = document.getElementById('review_period_start');
            var endEl = document.getElementById('review_period_end');
            var statusSel = document.getElementById('status');

            if (actionInput) actionInput.value = 'edit_review';
            if (reviewIdInput) reviewIdInput.value = id;
            if (startEl) startEl.value = start;
            if (endEl) endEl.value = end;
            if (ratingSel) ratingSel.value = ratingVal;
            if (commentsEl) commentsEl.value = commentsVal;
            if (statusSel) statusSel.value = statusVal;

            // select employee in combobox (if present in list)
            hiddenInput.value = empId;
            var selectedItem = items.find(function(i){ return i.dataset.id == empId; });
            if (selectedItem) {
                items.forEach(function(it){ it.classList.remove('active'); });
                selectedItem.classList.add('active');
                toggle.textContent = selectedItem.textContent.trim();
            } else if (empName) {
                toggle.textContent = empName;
            } else {
                toggle.textContent = '-- Select employee --';
            }
            clearBtn.style.display = '';

            var modalTitle = document.getElementById('modalTitle');
            if (modalTitle) modalTitle.textContent = 'Edit Performance Review';
            var submitBtn = document.getElementById('modal_submit_btn'); if (submitBtn) submitBtn.textContent = 'Save Changes';

            // open modal
            var bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        });
    });

    // keyboard navigation via search input (only when open)
    search.addEventListener('keydown', function(e){
        if (!combobox.classList.contains('open')) return;
        var visible = items.filter(function(i){ return i.style.display !== 'none'; });
        if (!visible.length) return;
        var idx = visible.findIndex(function(i){ return i.classList.contains('active'); });
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            var next = (idx === -1) ? visible[0] : visible[(idx+1) % visible.length];
            visible.forEach(function(i){ i.classList.remove('active'); });
            next.classList.add('active');
            hiddenInput.value = next.dataset.id;
            next.scrollIntoView({block:'nearest'});
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            var prev = (idx === -1) ? visible[visible.length-1] : visible[(idx-1+visible.length) % visible.length];
            visible.forEach(function(i){ i.classList.remove('active'); });
            prev.classList.add('active');
            hiddenInput.value = prev.dataset.id;
            prev.scrollIntoView({block:'nearest'});
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (idx !== -1) {
                visible[idx].click();
            } else if (visible.length) {
                visible[0].click();
            }
        }
    });
});
</script>

<?php if (!defined('NO_FOOTER')) { require_once __DIR__ . '/footer.php'; } ?>
