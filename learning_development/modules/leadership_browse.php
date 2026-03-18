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
            // Enroll in leadership
            if ($action === 'enroll') {
                $programId = intval($_POST['id'] ?? 0);
                
                $stmt = $pdo->prepare('SELECT id FROM leadership_enrollments WHERE user_id = ? AND program_id = ?');
                $stmt->execute([$currentUserId, $programId]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing) {
                    $message = 'Already enrolled in this leadership program.';
                    $messageType = 'warning';
                } else {
                    $stmt = $pdo->prepare('
                        INSERT INTO leadership_enrollments (user_id, program_id, status)
                        VALUES (?, ?, ?)
                    ');
                    $stmt->execute([$currentUserId, $programId, 'pending']);
                    $message = 'Enrolled successfully.';
                    $messageType = 'success';
                }
            }
            
            // Unenroll from leadership
            if ($action === 'unenroll') {
                $programId = intval($_POST['id'] ?? 0);
                
                $stmt = $pdo->prepare('DELETE FROM leadership_enrollments WHERE user_id = ? AND program_id = ?');
                $stmt->execute([$currentUserId, $programId]);
                $message = 'Unenrolled.';
                $messageType = 'success';
            }
        } catch (Exception $e) {
            error_log('Leadership browse error: ' . $e->getMessage());
            $message = 'An error occurred. Please try again.';
            $messageType = 'danger';
        }
    }
}

// Fetch all leadership programs
$allPrograms = [];
try {
    $stmt = $pdo->query('SELECT * FROM leadership_programs ORDER BY created_at DESC LIMIT 1000');
    $allPrograms = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    error_log('Error fetching leadership programs: ' . $e->getMessage());
}

// Fetch user's enrollments
$userEnrollments = [];
if ($currentUserId) {
    try {
        $stmt = $pdo->prepare('
            SELECT lp.* FROM leadership_programs lp
            JOIN leadership_enrollments le ON lp.id = le.program_id
            WHERE le.user_id = ?
            ORDER BY le.enrollment_date DESC
        ');
        $stmt->execute([$currentUserId]);
        $userEnrollments = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        error_log('Error fetching user enrollments: ' . $e->getMessage());
    }
}

// Get enrollment counts
$enrollmentDetails = [];
try {
    foreach ($allPrograms as $program) {
        $stmt = $pdo->prepare('
            SELECT COUNT(*) as count FROM leadership_enrollments
            WHERE program_id = ?
        ');
        $stmt->execute([$program['id']]);
        $enrollmentDetails[$program['id']] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    }
} catch (Exception $e) {
    error_log('Error fetching enrollment counts: ' . $e->getMessage());
}

// Featured programs (first 3)
$featuredPrograms = array_slice($allPrograms, 0, 3);

// Search and pagination
$searchQuery = $_GET['search'] ?? '';
$pageNum = intval($_GET['page_num'] ?? 1);
$itemsPerPage = 15;

$filteredPrograms = $allPrograms;
if (!empty($searchQuery)) {
    $filteredPrograms = filterBySearch($filteredPrograms, $searchQuery, ['name', 'description']);
}

$paginatedPrograms = paginateItems($filteredPrograms, $pageNum, $itemsPerPage);
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
    <form method="GET" action="?page=leadership-browse" class="d-flex gap-2">
      <input type="hidden" name="page" value="leadership-browse">
      <input type="text" name="search" class="form-control" placeholder="Search leadership programs..." 
        value="<?php echo htmlspecialchars($searchQuery); ?>">
      <button type="submit" class="btn btn-primary">Search</button>
      <a href="?page=leadership-browse" class="btn btn-secondary">Clear</a>
    </form>
  </div>

  <!-- SECTION 2: My Leadership Programs (3x1 cards) -->
  <div class="browse-section mb-5">
    <h3 class="section-title mb-4">
      <i class="fas fa-bookmark"></i> My Leadership Programs
    </h3>
    <?php if (!empty($userEnrollments)): ?>
      <div class="carousel-container position-relative">
        <div class="row g-3" style="overflow-x: auto; display: flex; flex-wrap: nowrap;">
          <?php foreach (array_slice($userEnrollments, 0, 3) as $program): ?>
            <div class="col-md-4" style="flex: 0 0 33.333%; min-width: 300px;">
              <div class="card h-100 leadership-card clickable-card" style="cursor: pointer;"
                data-program-id="<?php echo intval($program['id']); ?>"
                data-name="<?php echo htmlspecialchars($program['name']); ?>"
                data-description="<?php echo htmlspecialchars($program['description']); ?>"
                data-cover-photo="<?php echo htmlspecialchars(getImageUrl($program['cover_photo'] ?? null)); ?>"
                data-level="<?php echo htmlspecialchars($program['level'] ?? 'N/A'); ?>"
                data-duration="<?php echo htmlspecialchars($program['duration_weeks'] ?? 'N/A'); ?>"
                data-instructor="<?php echo htmlspecialchars($program['instructor'] ?? 'N/A'); ?>"
                data-start-date="<?php echo htmlspecialchars($program['start_date'] ?? 'N/A'); ?>"
                data-enrolled="<?php echo $enrollmentDetails[$program['id']] ?? 0; ?>">
                <img src="<?php echo htmlspecialchars(getImageUrl($program['cover_photo'] ?? null)); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($program['name']); ?>">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title"><?php echo htmlspecialchars($program['name']); ?></h5>
                  <p class="card-text text-muted mb-2"><?php echo htmlspecialchars(substr($program['description'], 0, 100)); ?></p>
                  <p class="text-center mb-2" style="font-size: 0.9rem;">
                    <?php if (!empty($program['level'])): ?>
                      <small class="trainer-badge"><?php echo htmlspecialchars($program['level']); ?></small>
                    <?php endif; ?>
                    <?php if (!empty($program['duration_weeks'])): ?>
                      <small class="sessions-badge ms-2"><?php echo intval($program['duration_weeks']); ?> weeks</small>
                    <?php endif; ?>
                  </p>
                  <!-- Card meta grid: status | date | remaining -->
                  <div class="card-meta-grid mb-2">
                    <div class="d-flex justify-content-between gap-1">
                      <small class="meta-label">Active</small>
                      <small class="meta-label">—</small>
                      <small class="meta-label">Rem: 0</small>
                    </div>
                  </div>
                  <div class="mt-auto d-flex justify-content-between align-items-center">
                    <small class="text-muted">Enrolled: 0</small>
                    <div class="card-action-set">
                      <form method="post" style="display:inline;" onclick="event.stopPropagation();">
                        <input type="hidden" name="action" value="unenroll">
                        <input type="hidden" name="id" value="<?php echo intval($program['id']); ?>">
                        <button type="submit" class="btn btn-sm btn-outline-warning">Unenroll</button>
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
      <div class="alert alert-info">You haven't enrolled in any leadership programs yet.</div>
    <?php endif; ?>
  </div>

  <!-- SECTION 3: Featured Leadership Programs (3x1 cards) -->
  <div class="browse-section mb-5">
    <h3 class="section-title mb-4">
      <i class="fas fa-star"></i> Featured Leadership Programs
    </h3>
    <div class="row g-3">
      <?php foreach ($featuredPrograms as $program): ?>
        <div class="col-md-4">
          <div class="card h-100 leadership-card clickable-card" style="cursor: pointer;"
            data-program-id="<?php echo intval($program['id']); ?>"
            data-name="<?php echo htmlspecialchars($program['name']); ?>"
            data-description="<?php echo htmlspecialchars($program['description']); ?>"
            data-cover-photo="<?php echo htmlspecialchars(getImageUrl($program['cover_photo'] ?? null, 'modules/img/placeholder.gif')); ?>"
            data-level="<?php echo htmlspecialchars($program['level'] ?? 'N/A'); ?>"
            data-duration="<?php echo htmlspecialchars($program['duration_weeks'] ?? 'N/A'); ?>"
            data-instructor="<?php echo htmlspecialchars($program['instructor'] ?? 'N/A'); ?>"
            data-start-date="<?php echo htmlspecialchars($program['start_date'] ?? 'N/A'); ?>"
            data-enrolled="<?php echo $enrollmentDetails[$program['id']] ?? 0; ?>">
            <div style="position: relative;">
                <img src="<?php echo htmlspecialchars(getImageUrl($program['cover_photo'] ?? null, $placeholderImg)); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($program['name']); ?>">
              <span class="badge bg-warning" style="position: absolute; top: 10px; right: 10px;">Featured</span>
            </div>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?php echo htmlspecialchars($program['name']); ?></h5>
              <p class="card-text text-muted mb-2"><?php echo htmlspecialchars(substr($program['description'], 0, 100)); ?></p>
              <p class="text-center mb-2" style="font-size: 0.9rem;">
                <?php if (!empty($program['level'])): ?>
                  <small class="trainer-badge"><?php echo htmlspecialchars($program['level']); ?></small>
                <?php endif; ?>
                <?php if (!empty($program['duration_weeks'])): ?>
                  <small class="sessions-badge ms-2"><?php echo intval($program['duration_weeks']); ?> weeks</small>
                <?php endif; ?>
              </p>
              <!-- Card meta grid: status | date | remaining -->
              <div class="card-meta-grid mb-2">
                <div class="d-flex justify-content-between gap-1">
                  <small class="meta-label">Active</small>
                  <small class="meta-label">—</small>
                  <small class="meta-label">Rem: <?php echo $enrollmentDetails[$program['id']] ?? 0; ?></small>
                </div>
              </div>
              <div class="mt-auto d-flex justify-content-between align-items-center">
                <small class="text-muted">Enrolled: <?php echo $enrollmentDetails[$program['id']] ?? 0; ?></small>
                <div class="card-action-set">
                  <?php 
                  $isEnrolled = false;
                  if ($currentUserId) {
                      foreach ($userEnrollments as $enrolled) {
                          if ($enrolled['id'] == $program['id']) {
                              $isEnrolled = true;
                              break;
                          }
                      }
                  }
                  ?>
                  <form method="post" style="display:inline;" onclick="event.stopPropagation();">
                    <input type="hidden" name="action" value="<?php echo $isEnrolled ? 'unenroll' : 'enroll'; ?>">
                    <input type="hidden" name="id" value="<?php echo intval($program['id']); ?>">
                    <button type="submit" class="btn btn-sm <?php echo $isEnrolled ? 'btn-outline-warning' : 'btn-primary'; ?>">
                      <?php echo $isEnrolled ? 'Unenroll' : 'Enroll'; ?>
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

  <!-- SECTION 4: All Leadership Programs (3x5 grid with pagination) -->
  <div class="browse-section mb-5">
    <h3 class="section-title mb-4">
      <i class="fas fa-list"></i> All Leadership Programs
    </h3>
    <?php if (!empty($paginatedPrograms['items'])): ?>
      <div class="row g-3">
        <?php foreach ($paginatedPrograms['items'] as $program): ?>
          <div class="col-md-4">
            <div class="card h-100 leadership-card clickable-card" style="cursor: pointer;"
              data-program-id="<?php echo intval($program['id']); ?>"
              data-name="<?php echo htmlspecialchars($program['name']); ?>"
              data-description="<?php echo htmlspecialchars($program['description']); ?>"
                data-cover-photo="<?php echo htmlspecialchars(getImageUrl($program['cover_photo'] ?? null, $placeholderImg)); ?>"
              data-level="<?php echo htmlspecialchars($program['level'] ?? 'N/A'); ?>"
              data-duration="<?php echo htmlspecialchars($program['duration_weeks'] ?? 'N/A'); ?>"
              data-instructor="<?php echo htmlspecialchars($program['instructor'] ?? 'N/A'); ?>"
              data-start-date="<?php echo htmlspecialchars($program['start_date'] ?? 'N/A'); ?>"
              data-enrolled="<?php echo $enrollmentDetails[$program['id']] ?? 0; ?>">
              <img src="<?php echo htmlspecialchars(getImageUrl($program['cover_photo'] ?? null, 'modules/img/placeholder.gif')); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($program['name']); ?>">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo htmlspecialchars($program['name']); ?></h5>
                <p class="card-text text-muted mb-2"><?php echo htmlspecialchars(substr($program['description'], 0, 100)); ?></p>
                <p class="text-center mb-2" style="font-size: 0.9rem;">
                  <?php if (!empty($program['level'])): ?>
                    <small class="trainer-badge"><?php echo htmlspecialchars($program['level']); ?></small>
                  <?php endif; ?>
                  <?php if (!empty($program['duration_weeks'])): ?>
                    <small class="sessions-badge ms-2"><?php echo intval($program['duration_weeks']); ?> weeks</small>
                  <?php endif; ?>
                </p>
                <!-- Card meta grid: status | date | remaining -->
                <div class="card-meta-grid mb-2">
                  <div class="d-flex justify-content-between gap-1">
                    <small class="meta-label">Active</small>
                    <small class="meta-label">—</small>
                    <small class="meta-label">Enrolled: <?php echo $enrollmentDetails[$program['id']] ?? 0; ?></small>
                  </div>
                </div>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                  <small class="text-muted">Total: <?php echo $enrollmentDetails[$program['id']] ?? 0; ?></small>
                  <div class="card-action-set">
                    <?php 
                    $isEnrolled = false;
                    if ($currentUserId) {
                        foreach ($userEnrollments as $enrolled) {
                            if ($enrolled['id'] == $program['id']) {
                                $isEnrolled = true;
                                break;
                            }
                        }
                    }
                    ?>
                    <form method="post" style="display:inline;" onclick="event.stopPropagation();">
                      <input type="hidden" name="action" value="<?php echo $isEnrolled ? 'unenroll' : 'enroll'; ?>">
                      <input type="hidden" name="id" value="<?php echo intval($program['id']); ?>">
                      <button type="submit" class="btn btn-sm <?php echo $isEnrolled ? 'btn-outline-warning' : 'btn-primary'; ?>">
                        <?php echo $isEnrolled ? 'Unenroll' : 'Enroll'; ?>
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination with numeric links -->
      <nav aria-label="Pagination" class="mt-5">
        <ul class="pagination justify-content-center">
          <?php if ($paginatedPrograms['hasPrevPage']): ?>
            <li class="page-item">
              <a class="page-link" href="?page=leadership-browse&search=<?php echo urlencode($searchQuery); ?>&page_num=<?php echo $paginatedPrograms['currentPage'] - 1; ?>">
                <i class="fas fa-chevron-left"></i>
              </a>
            </li>
          <?php else: ?>
            <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
          <?php endif; ?>
          
          <?php for ($i = 1; $i <= $paginatedPrograms['totalPages']; $i++): ?>
            <li class="page-item <?php echo $i === $paginatedPrograms['currentPage'] ? 'active' : ''; ?>">
              <a class="page-link" href="?page=leadership-browse&search=<?php echo urlencode($searchQuery); ?>&page_num=<?php echo $i; ?>">
                <?php echo $i; ?>
              </a>
            </li>
          <?php endfor; ?>
          
          <?php if ($paginatedPrograms['hasNextPage']): ?>
            <li class="page-item">
              <a class="page-link" href="?page=leadership-browse&search=<?php echo urlencode($searchQuery); ?>&page_num=<?php echo $paginatedPrograms['currentPage'] + 1; ?>">
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
        No leadership programs available<?php echo !empty($searchQuery) ? ' matching your search.' : '.'; ?>
      </div>
    <?php endif; ?>
  </div>

</div>

<!-- Leadership Program Details Modal -->
<div class="modal fade" id="leadershipModal" tabindex="-1" role="dialog" aria-labelledby="leadershipModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="leadershipModalLabel">Leadership Program Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Cover Photo -->
        <div class="mb-3">
          <img id="modalCoverPhoto" src="" class="img-fluid rounded" style="width: 100%; max-height: 300px; object-fit: cover;" alt="Program Cover">
        </div>

        <!-- Title -->
        <h4 id="modalLeadershipName" class="mb-3"></h4>

        <!-- Description -->
        <div class="mb-3">
          <h6 class="text-muted">Description</h6>
          <p id="modalLeadershipDescription"></p>
        </div>

        <hr>

        <!-- Program Details Grid -->
        <div class="row g-3">
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="text-muted">Level</h6>
              <p id="modalLeadershipLevel" class="mb-0"><strong></strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="text-muted">Duration</h6>
              <p id="modalDuration" class="mb-0"><strong></strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="text-muted">Instructor</h6>
              <p id="modalInstructor" class="mb-0"><strong></strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="text-muted">Start Date</h6>
              <p id="modalStartDate" class="mb-0"><strong></strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="text-muted">Enrolled Users</h6>
              <p id="modalLeadershipEnrolled" class="mb-0"><strong></strong></p>
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
    var modalElement = document.getElementById('leadershipModal');
    
    if (modalElement && clickableCards.length > 0) {
      var leadershipModal = new (typeof bootstrap !== 'undefined' ? bootstrap.Modal : function(el) {
        this.show = function() { if (typeof jQuery !== 'undefined') jQuery(el).modal('show'); };
      })(modalElement);

      clickableCards.forEach(function(card) {
        card.addEventListener('click', function() {
          var name = this.getAttribute('data-name');
          var description = this.getAttribute('data-description');
          var coverPhoto = this.getAttribute('data-cover-photo');
          var level = this.getAttribute('data-level');
          var duration = this.getAttribute('data-duration');
          var instructor = this.getAttribute('data-instructor');
          var startDate = this.getAttribute('data-start-date');
          var enrolled = this.getAttribute('data-enrolled');

          document.getElementById('modalCoverPhoto').src = coverPhoto;
          document.getElementById('modalLeadershipName').textContent = name;
          document.getElementById('modalLeadershipDescription').textContent = description;
          document.getElementById('modalLeadershipLevel').querySelector('strong').textContent = level;
          document.getElementById('modalDuration').querySelector('strong').textContent = duration + ' weeks';
          document.getElementById('modalInstructor').querySelector('strong').textContent = instructor;
          document.getElementById('modalStartDate').querySelector('strong').textContent = startDate;
          document.getElementById('modalLeadershipEnrolled').querySelector('strong').textContent = enrolled + ' users';

          leadershipModal.show();
        });
      });
    }
  } catch (e) {
    console.error('Error initializing modal:', e);
  }
});
</script>

<style>
.leadership-card {
  transition: transform 0.3s, box-shadow 0.3s;
  border: 1px solid #ecf0f1;
}

.leadership-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.clickable-card:hover {
  background-color: #f8f9fa
}

.leadership-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
