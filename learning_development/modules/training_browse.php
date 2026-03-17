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
$isAuthorized = can_manage();
$message = '';
$messageType = 'info';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($currentUserId) {
        $action = $_POST['action'] ?? '';
        
        try {
            // Enroll user
            if ($action === 'enroll') {
                $programId = intval($_POST['id'] ?? 0);
                
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
            if ($action === 'unenroll') {
                $programId = intval($_POST['id'] ?? 0);
                
                $stmt = $pdo->prepare('DELETE FROM training_enrollments WHERE user_id = ? AND program_id = ?');
                $stmt->execute([$currentUserId, $programId]);
                $message = 'Unenrolled.';
                $messageType = 'success';
            }
        } catch (Exception $e) {
            error_log('Training browse error: ' . $e->getMessage());
            $message = 'An error occurred. Please try again.';
            $messageType = 'danger';
        }
    }
}

// Fetch all training programs
$allPrograms = [];
try {
    $stmt = $pdo->query('SELECT * FROM training_programs WHERE status = "Active" ORDER BY created_at DESC LIMIT 1000');
    $allPrograms = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    error_log('Error fetching training programs: ' . $e->getMessage());
}

// Fetch user's enrollments
$userEnrollments = [];
if ($currentUserId) {
    try {
        $stmt = $pdo->prepare('
            SELECT tp.* FROM training_programs tp
            JOIN training_enrollments te ON tp.id = te.program_id
            WHERE te.user_id = ?
            ORDER BY te.enrollment_date DESC
        ');
        $stmt->execute([$currentUserId]);
        $userEnrollments = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        error_log('Error fetching user enrollments: ' . $e->getMessage());
    }
}

// Get enrollment details for all programs
$enrollmentDetails = [];
try {
    foreach ($allPrograms as $program) {
        $stmt = $pdo->prepare('
            SELECT COUNT(*) as count FROM training_enrollments
            WHERE program_id = ?
        ');
        $stmt->execute([$program['id']]);
        $enrollmentDetails[$program['id']] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    }
} catch (Exception $e) {
    error_log('Error fetching enrollment details: ' . $e->getMessage());
}

// Featured programs (first 3)
$featuredPrograms = array_slice($allPrograms, 0, 3);

// Search and pagination
$searchQuery = $_GET['search'] ?? '';
$pageNum = intval($_GET['page_num'] ?? 1);
$itemsPerRow = 3;
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
    <form method="GET" action="?page=training-browse" class="d-flex gap-2">
      <input type="hidden" name="page" value="training-browse">
      <input type="text" name="search" class="form-control" placeholder="Search trainings..." 
        value="<?php echo htmlspecialchars($searchQuery); ?>">
      <button type="submit" class="btn btn-primary">Search</button>
      <a href="?page=training-browse" class="btn btn-secondary">Clear</a>
    </form>
  </div>

  <!-- SECTION 2: My Trainings (3x1 cards) -->
  <div class="browse-section mb-5">
    <h3 class="section-title mb-4">
      <i class="fas fa-bookmark"></i> My Trainings
    </h3>
    <?php if (!empty($userEnrollments)): ?>
      <div class="carousel-container position-relative">
        <div class="row g-3" style="overflow-x: auto; display: flex; flex-wrap: nowrap;">
          <?php foreach (array_slice($userEnrollments, 0, 3) as $program): ?>
            <div class="col-md-4" style="flex: 0 0 33.333%; min-width: 300px;">
              <div class="card h-100 training-card clickable-card" style="cursor: pointer;"
                data-program-id="<?php echo intval($program['id']); ?>"
                data-name="<?php echo htmlspecialchars($program['name']); ?>"
                data-description="<?php echo htmlspecialchars($program['description']); ?>"
                data-enrolled="<?php echo $enrollmentDetails[$program['id']] ?? 0; ?>">
                <img src="<?php echo htmlspecialchars(getImageUrl($program['cover_photo'] ?? null, 'modules/img/placeholder.gif')); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($program['name']); ?>">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title"><?php echo htmlspecialchars($program['name']); ?></h5>
                  <p class="card-text text-muted" style="font-size: 0.9rem; margin-bottom: 8px;"><?php echo htmlspecialchars(substr($program['description'], 0, 80)) . '...'; ?></p>
                  
                  <div class="small text-muted" style="margin-bottom: 10px;">
                    <div><strong>Enrolled:</strong> <?php echo $enrollmentDetails[$program['id']] ?? 0; ?> users</div>
                  </div>
                  
                  <div class="mt-auto">
                    <form method="post" style="display: inline;" onclick="event.stopPropagation();">
                      <input type="hidden" name="action" value="unenroll">
                      <input type="hidden" name="id" value="<?php echo intval($program['id']); ?>">
                      <button class="btn btn-sm btn-outline-warning w-100">Unenroll</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php if (count($userEnrollments) > 3): ?>
          <button class="carousel-nav carousel-prev" style="position: absolute; left: -20px; top: 50%; transform: translateY(-50%);">
            <i class="fas fa-chevron-left"></i>
          </button>
          <button class="carousel-nav carousel-next" style="position: absolute; right: -20px; top: 50%; transform: translateY(-50%);">
            <i class="fas fa-chevron-right"></i>
          </button>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info">You haven't enrolled in any trainings yet.</div>
    <?php endif; ?>
  </div>

  <!-- SECTION 3: Featured Trainings (3x1 cards) -->
  <div class="browse-section mb-5">
    <h3 class="section-title mb-4">
      <i class="fas fa-star"></i> Featured Trainings
    </h3>
    <div class="row g-3">
      <?php foreach ($featuredPrograms as $program): ?>
        <div class="col-md-4">
          <div class="card h-100 training-card clickable-card" style="cursor: pointer;"
            data-program-id="<?php echo intval($program['id']); ?>"
            data-name="<?php echo htmlspecialchars($program['name']); ?>"
            data-description="<?php echo htmlspecialchars($program['description']); ?>"
            data-enrolled="<?php echo $enrollmentDetails[$program['id']] ?? 0; ?>">
            <div style="position: relative;">
              <img src="<?php echo htmlspecialchars(getImageUrl($program['cover_photo'] ?? null, 'modules/img/placeholder.gif')); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($program['name']); ?>">
              <span class="badge bg-warning" style="position: absolute; top: 10px; right: 10px;">Featured</span>
            </div>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?php echo htmlspecialchars($program['name']); ?></h5>
              <p class="card-text text-muted" style="font-size: 0.9rem; margin-bottom: 8px;"><?php echo htmlspecialchars(substr($program['description'], 0, 80)) . '...'; ?></p>
              
              <div class="small text-muted" style="margin-bottom: 10px;">
                <div><strong>Enrolled:</strong> <?php echo $enrollmentDetails[$program['id']] ?? 0; ?> users</div>
              </div>
              
              <div class="mt-auto">
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
                <form method="post" style="display: inline;" onclick="event.stopPropagation();">
                  <input type="hidden" name="action" value="<?php echo $isEnrolled ? 'unenroll' : 'enroll'; ?>">
                  <input type="hidden" name="id" value="<?php echo intval($program['id']); ?>">
                  <button class="btn btn-sm <?php echo $isEnrolled ? 'btn-outline-warning' : 'btn-primary'; ?> w-100">
                    <?php echo $isEnrolled ? 'Unenroll' : 'Enroll'; ?>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SECTION 4: All Trainings (3x5 grid with pagination) -->
  <div class="browse-section mb-5">
    <h3 class="section-title mb-4">
      <i class="fas fa-list"></i> All Trainings
    </h3>
    <?php if (!empty($paginatedPrograms['items'])): ?>
      <div class="row g-3">
        <?php foreach ($paginatedPrograms['items'] as $program): ?>
          <div class="col-md-4">
            <div class="card h-100 training-card clickable-card" style="cursor: pointer;"
              data-program-id="<?php echo intval($program['id']); ?>"
              data-name="<?php echo htmlspecialchars($program['name']); ?>"
              data-description="<?php echo htmlspecialchars($program['description']); ?>"
              data-enrolled="<?php echo $enrollmentDetails[$program['id']] ?? 0; ?>">
              <img src="<?php echo htmlspecialchars(getImageUrl($program['cover_photo'] ?? null, 'modules/img/placeholder.gif')); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($program['name']); ?>">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo htmlspecialchars($program['name']); ?></h5>
                <p class="card-text text-muted" style="font-size: 0.9rem; margin-bottom: 8px;"><?php echo htmlspecialchars(substr($program['description'], 0, 80)) . '...'; ?></p>
                
                <div class="small text-muted" style="margin-bottom: 10px;">
                  <div><strong>Enrolled:</strong> <?php echo $enrollmentDetails[$program['id']] ?? 0; ?> users</div>
                </div>
                
                <div class="mt-auto">
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
                  <form method="post" style="display: inline;" onclick="event.stopPropagation();">
                    <input type="hidden" name="action" value="<?php echo $isEnrolled ? 'unenroll' : 'enroll'; ?>">
                    <input type="hidden" name="id" value="<?php echo intval($program['id']); ?>">
                    <button class="btn btn-sm <?php echo $isEnrolled ? 'btn-outline-warning' : 'btn-primary'; ?> w-100">
                      <?php echo $isEnrolled ? 'Unenroll' : 'Enroll'; ?>
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
          <?php if ($paginatedPrograms['hasPrevPage']): ?>
            <li class="page-item">
              <a class="page-link" href="?page=training-browse&search=<?php echo urlencode($searchQuery); ?>&page_num=<?php echo $paginatedPrograms['currentPage'] - 1; ?>">
                <i class="fas fa-chevron-left"></i>
              </a>
            </li>
          <?php else: ?>
            <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
          <?php endif; ?>
          
          <?php for ($i = 1; $i <= $paginatedPrograms['totalPages']; $i++): ?>
            <li class="page-item <?php echo $i === $paginatedPrograms['currentPage'] ? 'active' : ''; ?>">
              <a class="page-link" href="?page=training-browse&search=<?php echo urlencode($searchQuery); ?>&page_num=<?php echo $i; ?>">
                <?php echo $i; ?>
              </a>
            </li>
          <?php endfor; ?>
          
          <?php if ($paginatedPrograms['hasNextPage']): ?>
            <li class="page-item">
              <a class="page-link" href="?page=training-browse&search=<?php echo urlencode($searchQuery); ?>&page_num=<?php echo $paginatedPrograms['currentPage'] + 1; ?>">
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
        No trainings available<?php echo !empty($searchQuery) ? ' matching your search.' : '.'; ?>
      </div>
    <?php endif; ?>
  </div>

</div>

<!-- Program Details Modal -->
<div class="modal fade" id="programModal" tabindex="-1" role="dialog" aria-labelledby="programModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="programModalLabel">Training Program Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h4 id="modalProgramName"></h4>
        <p id="modalProgramDescription"></p>
        <hr>
        <p><strong>Enrolled Users:</strong> <span id="modalEnrolled"></span></p>
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
    // Handle card clicks for modal
    var clickableCards = document.querySelectorAll('.clickable-card');
    var modalElement = document.getElementById('programModal');
    
    if (modalElement && clickableCards.length > 0) {
      var programModal = new (typeof bootstrap !== 'undefined' ? bootstrap.Modal : function(el) {
        this.show = function() { if (typeof jQuery !== 'undefined') jQuery(el).modal('show'); };
      })(modalElement);

      clickableCards.forEach(function(card) {
        card.addEventListener('click', function() {
          var name = this.getAttribute('data-name');
          var description = this.getAttribute('data-description');
          var enrolled = this.getAttribute('data-enrolled');

          document.getElementById('modalProgramName').textContent = name;
          document.getElementById('modalProgramDescription').textContent = description;
          document.getElementById('modalEnrolled').textContent = enrolled;

          programModal.show();
        });
      });
    }
  } catch (e) {
    console.error('Error initializing modal:', e);
  }
});
</script>

<style>
.training-card {
  transition: transform 0.3s, box-shadow 0.3s;
  border: 1px solid #ecf0f1;
}

.training-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.clickable-card:hover {
  background-color: #f8f9fa;
}
</style>
