<?php 
require_once __DIR__ . '/config.php';

if (!defined('NO_HEADER')) {
    require_once __DIR__ . '/header.php';
}

require_once __DIR__ . '/toast.php';
require_once __DIR__ . '/search_filter.php';
require_once __DIR__ . '/image_upload.php';

$currentUserId = get_current_user_id();
$currentUsername = $_SESSION['username'] ?? null;
$message = '';
$messageType = 'info';
$placeholderImg = '../img/placeholder.gif';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($currentUserId) {
        $action = $_POST['action'] ?? '';
        
        try {
            // Join activity
            if ($action === 'join') {
                $activityId = intval($_POST['id'] ?? 0);
                
                // Check if already joined
                $stmt = $pdo->prepare('SELECT id FROM team_activity_participants WHERE user_id = ? AND activity_id = ?');
                $stmt->execute([$currentUserId, $activityId]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing) {
                    $message = 'You are already participating in this activity.';
                    $messageType = 'warning';
                } else {
                    // Insert participant record
                    $stmt = $pdo->prepare('
                        INSERT INTO team_activity_participants (user_id, activity_id, status)
                        VALUES (?, ?, ?)
                    ');
                    $stmt->execute([$currentUserId, $activityId, 'confirmed']);
                    
                    // Update participant count
                    $stmt = $pdo->prepare('UPDATE team_activities SET participant_count = participant_count + 1 WHERE id = ?');
                    $stmt->execute([$activityId]);
                    
                    $message = 'Successfully joined activity.';
                    $messageType = 'success';
                }
            }
            
            // Leave activity
            if ($action === 'leave') {
                $activityId = intval($_POST['id'] ?? 0);
                
                // Delete participant record
                $stmt = $pdo->prepare('DELETE FROM team_activity_participants WHERE user_id = ? AND activity_id = ?');
                $stmt->execute([$currentUserId, $activityId]);
                
                // Update participant count
                $stmt = $pdo->prepare('UPDATE team_activities SET participant_count = GREATEST(0, participant_count - 1) WHERE id = ?');
                $stmt->execute([$activityId]);
                
                $message = 'Left activity.';
                $messageType = 'success';
            }
        } catch (Exception $e) {
            error_log('Org Dev browse error: ' . $e->getMessage());
            $message = 'An error occurred. Please try again.';
            $messageType = 'danger';
        }
    }
}

// Fetch all team activities
$allInitiatives = [];
try {
    $stmt = $pdo->query('SELECT * FROM team_activities ORDER BY activity_date DESC LIMIT 1000');
    $allInitiatives = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    error_log('Error fetching team activities: ' . $e->getMessage());
}

// Fetch user's participation (query from database)
$userParticipation = [];
if ($currentUserId) {
    try {
        $stmt = $pdo->prepare('
            SELECT ta.* FROM team_activities ta
            JOIN team_activity_participants tap ON ta.id = tap.activity_id
            WHERE tap.user_id = ?
            ORDER BY tap.created_at DESC
        ');
        $stmt->execute([$currentUserId]);
        $userParticipation = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        error_log('Error fetching user participation: ' . $e->getMessage());
    }
}

// Get participation counts (simplified)
$participationCounts = [];
try {
    foreach ($allInitiatives as $activity) {
        $participationCounts[$activity['id']] = $activity['participant_count'] ?? 0;
    }
} catch (Exception $e) {
    error_log('Error fetching participation counts: ' . $e->getMessage());
}

// Featured initiatives (first 3)
$featuredInitiatives = array_slice($allInitiatives, 0, 3);

// Search and pagination
$searchQuery = $_GET['search'] ?? '';
$pageNum = intval($_GET['page_num'] ?? 1);
$itemsPerPage = 15;

$filteredInitiatives = $allInitiatives;
if (!empty($searchQuery)) {
    $filteredInitiatives = filterBySearch($filteredInitiatives, $searchQuery, ['name', 'description']);
}

$paginatedInitiatives = paginateItems($filteredInitiatives, $pageNum, $itemsPerPage);
?>

<div style="margin-top: 80px; margin-bottom: 40px; padding: 0 20px;">

  <?php if ($message): ?>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    showToast(<?php echo json_encode($message); ?>, <?php echo json_encode($messageType); ?>, 4000);
  });
  </script>
  <?php endif; ?>

  <!-- SECTION 1: Search Bar (centered below header) -->
  <div class="browse-section search-section mb-5">
    <form method="GET" action="?page=orgdev-browse" class="d-flex gap-2">
      <input type="hidden" name="page" value="orgdev-browse">
      <input type="text" name="search" class="form-control" placeholder="Search initiatives..." 
        value="<?php echo htmlspecialchars($searchQuery); ?>">
      <button type="submit" class="btn btn-primary">Search</button>
      <a href="?page=orgdev-browse" class="btn btn-secondary">Clear</a>
    </form>
  </div>

  <!-- SECTION 2: My Initiatives (3x1 cards) -->
  <div class="browse-section mb-5">
    <h3 class="section-title mb-4">
      <i class="fas fa-bookmark"></i> My Initiatives
    </h3>
    <?php if (!empty($userParticipation)): ?>
      <div class="carousel-container position-relative">
        <div class="row g-3" style="overflow-x: auto; display: flex; flex-wrap: nowrap;">
          <?php foreach (array_slice($userParticipation, 0, 3) as $initiative): ?>
            <div class="col-md-4" style="flex: 0 0 33.333%; min-width: 300px;">
              <div class="card h-100 orgdev-card clickable-card" style="cursor: pointer;"
                data-activity-id="<?php echo intval($initiative['id']); ?>"
                data-name="<?php echo htmlspecialchars($initiative['name']); ?>"
                data-description="<?php echo htmlspecialchars($initiative['description']); ?>"
                data-cover-photo="<?php echo htmlspecialchars(getImageUrl($initiative['cover_photo'] ?? null)); ?>"
                data-department="<?php echo htmlspecialchars($initiative['department'] ?? 'N/A'); ?>"
                data-activity-date="<?php echo htmlspecialchars($initiative['activity_date'] ?? 'N/A'); ?>"
                data-location="<?php echo htmlspecialchars($initiative['location'] ?? 'N/A'); ?>"
                data-enrolled="<?php echo intval($initiative['participant_count'] ?? 0); ?>">
                <img src="<?php echo htmlspecialchars(getImageUrl($initiative['cover_photo'] ?? null)); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($initiative['name']); ?>">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title"><?php echo htmlspecialchars($initiative['name']); ?></h5>
                  <p class="card-text text-muted mb-2"><?php echo htmlspecialchars(substr($initiative['description'], 0, 100)); ?></p>
                  <p class="text-center mb-2" style="font-size: 0.9rem;">
                    <small class="trainer-badge">Activity</small>
                    <?php if (!empty($initiative['activity_date'])): ?>
                      <small class="sessions-badge ms-2"><?php echo date('M d', strtotime($initiative['activity_date'])); ?></small>
                    <?php endif; ?>
                  </p>
                  <!-- Card meta grid: status | date | remaining -->
                  <div class="card-meta-grid mb-2">
                    <div class="d-flex justify-content-between gap-1">
                      <small class="meta-label"><?php echo htmlspecialchars($initiative['department'] ?? 'N/A'); ?></small>
                      <small class="meta-label">—</small>
                      <small class="meta-label">Rem: 0</small>
                    </div>
                  </div>
                  <div class="mt-auto d-flex justify-content-between align-items-center">
                    <small class="text-muted">Joined: 0</small>
                    <div class="card-action-set">
                      <form method="post" style="display:inline;" onclick="event.stopPropagation();">
                        <input type="hidden" name="action" value="leave">
                        <input type="hidden" name="id" value="<?php echo intval($initiative['id']); ?>">
                        <button type="submit" class="btn btn-sm btn-outline-warning">Leave</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-info">You're not participating in any initiatives yet.</div>
    <?php endif; ?>
  </div>

  <!-- SECTION 3: Featured Initiatives (3x1 cards) -->
  <div class="browse-section mb-5">
    <h3 class="section-title mb-4">
      <i class="fas fa-star"></i> Featured Initiatives
    </h3>
    <div class="row g-3">
      <?php foreach ($featuredInitiatives as $initiative): ?>
        <div class="col-md-4">
          <div class="card h-100 orgdev-card clickable-card" style="cursor: pointer;"
            data-activity-id="<?php echo intval($initiative['id']); ?>"
            data-name="<?php echo htmlspecialchars($initiative['name']); ?>"
            data-description="<?php echo htmlspecialchars($initiative['description']); ?>"
            data-cover-photo="<?php echo htmlspecialchars(getImageUrl($initiative['cover_photo'] ?? null, 'modules/img/placeholder.gif')); ?>"
            data-department="<?php echo htmlspecialchars($initiative['department'] ?? 'N/A'); ?>"
            data-activity-date="<?php echo htmlspecialchars($initiative['activity_date'] ?? 'N/A'); ?>"
            data-location="<?php echo htmlspecialchars($initiative['location'] ?? 'N/A'); ?>"
            data-enrolled="<?php echo intval($initiative['participant_count'] ?? 0); ?>">
            <div style="position: relative;">
                <img src="<?php echo htmlspecialchars(getImageUrl($initiative['cover_photo'] ?? null, $placeholderImg)); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($initiative['name']); ?>">
              <span class="badge bg-warning" style="position: absolute; top: 10px; right: 10px;">Featured</span>
            </div>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?php echo htmlspecialchars($initiative['name']); ?></h5>
              <p class="card-text text-muted mb-2" style="font-size: 0.9rem;"><?php echo htmlspecialchars(substr($initiative['description'], 0, 80)) . '...'; ?></p>
              <p class="text-center mb-2" style="font-size: 0.9rem;">
                <small class="badge bg-primary">Activity</small>
                <?php if (!empty($initiative['activity_date'])): ?>
                  <small class="badge bg-secondary ms-2"><?php echo date('M d, Y', strtotime($initiative['activity_date'])); ?></small>
                <?php endif; ?>
              </p>
              <div class="card-meta-grid mb-2">
                <div class="d-flex justify-content-between gap-1">
                  <small class="meta-label" style="font-size: 0.85rem; color: #666;"><?php echo htmlspecialchars($initiative['department'] ?? 'N/A'); ?></small>
                  <small class="meta-label" style="font-size: 0.85rem; color: #666;">—</small>
                  <small class="meta-label" style="font-size: 0.85rem; color: #666;">Rem: <?php echo $participationCounts[$initiative['id']] ?? 0; ?></small>
                </div>
              </div>
              <div class="mt-auto d-flex justify-content-between align-items-center">
                <small class="text-muted">Joined: <?php echo $participationCounts[$initiative['id']] ?? 0; ?></small>
                <div class="card-action-set">
                  <?php 
                  $isParticipating = false;
                  if ($currentUserId) {
                      foreach ($userParticipation as $participation) {
                          if ($participation['id'] == $initiative['id']) {
                              $isParticipating = true;
                              break;
                          }
                      }
                  }
                  ?>
                  <form method="post" style="display: inline;" onclick="event.stopPropagation();">
                    <input type="hidden" name="action" value="<?php echo $isParticipating ? 'leave' : 'join'; ?>">
                    <input type="hidden" name="id" value="<?php echo intval($initiative['id']); ?>">
                    <button class="btn btn-sm <?php echo $isParticipating ? 'btn-outline-warning' : 'btn-primary'; ?>">
                      <?php echo $isParticipating ? 'Leave' : 'Join'; ?>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SECTION 4: All Initiatives (3x5 grid with pagination) -->
  <div class="browse-section mb-5">
    <h3 class="section-title mb-4">
      <i class="fas fa-list"></i> All Initiatives
    </h3>
    <?php if (!empty($paginatedInitiatives['items'])): ?>
      <div class="row g-3">
        <?php foreach ($paginatedInitiatives['items'] as $initiative): ?>
          <div class="col-md-4">
            <div class="card h-100 orgdev-card clickable-card" style="cursor: pointer;"
              data-activity-id="<?php echo intval($initiative['id']); ?>"
              data-name="<?php echo htmlspecialchars($initiative['name']); ?>"
              data-description="<?php echo htmlspecialchars($initiative['description']); ?>"
                data-cover-photo="<?php echo htmlspecialchars(getImageUrl($initiative['cover_photo'] ?? null, $placeholderImg)); ?>"
              data-department="<?php echo htmlspecialchars($initiative['department'] ?? 'N/A'); ?>"
              data-activity-date="<?php echo htmlspecialchars($initiative['activity_date'] ?? 'N/A'); ?>"
              data-location="<?php echo htmlspecialchars($initiative['location'] ?? 'N/A'); ?>"
              data-enrolled="<?php echo intval($initiative['participant_count'] ?? 0); ?>">
              <img src="<?php echo htmlspecialchars(getImageUrl($initiative['cover_photo'] ?? null, 'modules/img/placeholder.gif')); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($initiative['name']); ?>">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo htmlspecialchars($initiative['name']); ?></h5>
                <p class="card-text text-muted" style="font-size: 0.9rem; margin-bottom: 8px;"><?php echo htmlspecialchars(substr($initiative['description'], 0, 80)) . '...'; ?></p>
                
                <div class="small text-muted" style="margin-bottom: 10px;">
                  <?php if (!empty($initiative['activity_date'])): ?>
                    <div style="margin-bottom: 4px;"><strong>Date:</strong> <?php echo date('M d, Y', strtotime($initiative['activity_date'])); ?></div>
                  <?php endif; ?>
                  <?php if (!empty($initiative['department'])): ?>
                    <div style="margin-bottom: 4px;"><strong>Department:</strong> <?php echo htmlspecialchars($initiative['department']); ?></div>
                  <?php endif; ?>
                  <div><strong>Participants:</strong> <?php echo $participationCounts[$initiative['id']] ?? 0; ?> users</div>
                </div>
                
                <div class="mt-auto">
                  <?php 
                  $isParticipating = false;
                  if ($currentUserId) {
                      foreach ($userParticipation as $participation) {
                          if ($participation['id'] == $initiative['id']) {
                              $isParticipating = true;
                              break;
                          }
                      }
                  }
                  ?>
                  <form method="post" style="display: inline;" onclick="event.stopPropagation();">
                    <input type="hidden" name="action" value="<?php echo $isParticipating ? 'leave' : 'join'; ?>">
                    <input type="hidden" name="id" value="<?php echo intval($initiative['id']); ?>">
                    <button class="btn btn-sm <?php echo $isParticipating ? 'btn-outline-warning' : 'btn-primary'; ?> w-100">
                      <?php echo $isParticipating ? 'Leave' : 'Join'; ?>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination with numeric links -->
      <nav aria-label="Pagination" class="mt-5">
        <ul class="pagination justify-content-center">
          <?php if ($paginatedInitiatives['hasPrevPage']): ?>
            <li class="page-item">
              <a class="page-link" href="?page=orgdev-browse&search=<?php echo urlencode($searchQuery); ?>&page_num=<?php echo $paginatedInitiatives['currentPage'] - 1; ?>">
                <i class="fas fa-chevron-left"></i>
              </a>
            </li>
          <?php else: ?>
            <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
          <?php endif; ?>
          
          <?php for ($i = 1; $i <= $paginatedInitiatives['totalPages']; $i++): ?>
            <li class="page-item <?php echo $i === $paginatedInitiatives['currentPage'] ? 'active' : ''; ?>">
              <a class="page-link" href="?page=orgdev-browse&search=<?php echo urlencode($searchQuery); ?>&page_num=<?php echo $i; ?>">
                <?php echo $i; ?>
              </a>
            </li>
          <?php endfor; ?>
          
          <?php if ($paginatedInitiatives['hasNextPage']): ?>
            <li class="page-item">
              <a class="page-link" href="?page=orgdev-browse&search=<?php echo urlencode($searchQuery); ?>&page_num=<?php echo $paginatedInitiatives['currentPage'] + 1; ?>">
                <i class="fas fa-chevron-right"></i>
              </a>
            </li>
          <?php else: ?>
            <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php else: ?>
      <div class="alert alert-info">
        No initiatives available<?php echo !empty($searchQuery) ? ' matching your search.' : '.'; ?>
      </div>
    <?php endif; ?>
  </div>

</div>

<!-- Organizational Development Activity Details Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="activityModalLabel">Activity Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Cover Photo -->
        <div class="mb-3">
          <img id="modalCoverPhoto" src="" class="img-fluid rounded" style="width: 100%; max-height: 300px; object-fit: cover;" alt="Activity Cover">
        </div>

        <!-- Title -->
        <h4 id="modalActivityName" class="mb-3"></h4>

        <!-- Description -->
        <div class="mb-3">
          <h6 class="text-muted">Description</h6>
          <p id="modalActivityDescription"></p>
        </div>

        <hr>

        <!-- Activity Details Grid -->
        <div class="row g-3">
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="text-muted">Department</h6>
              <p id="modalDepartment" class="mb-0"><strong></strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="text-muted">Activity Date</h6>
              <p id="modalActivityDate" class="mb-0"><strong></strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="text-muted">Location</h6>
              <p id="modalLocation" class="mb-0"><strong></strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="text-muted">Participants</h6>
              <p id="modalActivityParticipants" class="mb-0"><strong></strong></p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  try {
    var clickableCards = document.querySelectorAll('.clickable-card');
    var modalElement = document.getElementById('activityModal');
    
    if (modalElement && clickableCards.length > 0) {
      var activityModal = new (typeof bootstrap !== 'undefined' ? bootstrap.Modal : function(el) {
        this.show = function() { if (typeof jQuery !== 'undefined') jQuery(el).modal('show'); };
      })(modalElement);

      clickableCards.forEach(function(card) {
        card.addEventListener('click', function() {
          var name = this.getAttribute('data-name');
          var description = this.getAttribute('data-description');
          var coverPhoto = this.getAttribute('data-cover-photo');
          var department = this.getAttribute('data-department');
          var activityDate = this.getAttribute('data-activity-date');
          var location = this.getAttribute('data-location');
          var participants = this.getAttribute('data-enrolled');

          document.getElementById('modalCoverPhoto').src = coverPhoto;
          document.getElementById('modalActivityName').textContent = name;
          document.getElementById('modalActivityDescription').textContent = description;
          document.getElementById('modalDepartment').querySelector('strong').textContent = department;
          document.getElementById('modalActivityDate').querySelector('strong').textContent = activityDate;
          document.getElementById('modalLocation').querySelector('strong').textContent = location;
          document.getElementById('modalActivityParticipants').querySelector('strong').textContent = participants + ' participants';

          activityModal.show();
        });
      });
    }
  } catch (e) {
    console.error('Error initializing modal:', e);
  }
});
</script>

<style>
.orgdev-card {
  transition: transform 0.3s, box-shadow 0.3s;
  border: 1px solid #ecf0f1;
}

.orgdev-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.clickable-card:hover {
  background-color: #f8f9fa;
}
</style>
