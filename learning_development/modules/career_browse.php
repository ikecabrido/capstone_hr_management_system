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
            // Start development plan
            if ($action === 'start_plan') {
                $careerPathId = intval($_POST['id'] ?? 0);
                
                $stmt = $pdo->prepare('SELECT id FROM individual_development_plans WHERE user_id = ? AND career_path_id = ?');
                $stmt->execute([$currentUserId, $careerPathId]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing) {
                    $message = 'Already have a development plan for this career path.';
                    $messageType = 'warning';
                } else {
                    $stmt = $pdo->prepare('
                        INSERT INTO individual_development_plans (user_id, career_path_id, status)
                        VALUES (?, ?, ?)
                    ');
                    $stmt->execute([$currentUserId, $careerPathId, 'in_progress']);
                    $message = 'Development plan started.';
                    $messageType = 'success';
                }
            }
        } catch (Exception $e) {
            error_log('Career browse error: ' . $e->getMessage());
            $message = 'An error occurred. Please try again.';
            $messageType = 'danger';
        }
    }
}

// Fetch all career paths
$allCareers = [];
try {
    $stmt = $pdo->query('SELECT * FROM career_paths ORDER BY created_at DESC LIMIT 1000');
    $allCareers = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    error_log('Error fetching career paths: ' . $e->getMessage());
}

// Fetch user's development plans
$userPlans = [];
if ($currentUserId) {
    try {
        $stmt = $pdo->prepare('
            SELECT cp.*, idp.status as plan_status FROM career_paths cp
            JOIN individual_development_plans idp ON cp.id = idp.career_path_id
            WHERE idp.user_id = ?
            ORDER BY idp.created_at DESC
        ');
        $stmt->execute([$currentUserId]);
        $userPlans = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        error_log('Error fetching user plans: ' . $e->getMessage());
    }
}

// Get plan counts for each career path
$enrollmentDetails = [];
try {
    foreach ($allCareers as $career) {
        $stmt = $pdo->prepare('
            SELECT COUNT(*) as count FROM individual_development_plans
            WHERE career_path_id = ?
        ');
        $stmt->execute([$career['id']]);
        $enrollmentDetails[$career['id']] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    }
} catch (Exception $e) {
    error_log('Error fetching plan counts: ' . $e->getMessage());
}

// Featured careers (first 3)
$featuredCareers = array_slice($allCareers, 0, 3);

// Search and pagination
$searchQuery = $_GET['search'] ?? '';
$pageNum = intval($_GET['page_num'] ?? 1);
$itemsPerPage = 15;

$filteredCareers = $allCareers;
if (!empty($searchQuery)) {
    $filteredCareers = filterBySearch($filteredCareers, $searchQuery, ['name', 'description']);
}

$paginatedCareers = paginateItems($filteredCareers, $pageNum, $itemsPerPage);
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
    <form method="GET" action="?page=career-browse" class="d-flex gap-2">
      <input type="hidden" name="page" value="career-browse">
      <input type="text" name="search" class="form-control" placeholder="Search career paths..." 
        value="<?php echo htmlspecialchars($searchQuery); ?>">
      <button type="submit" class="btn btn-primary">Search</button>
      <a href="?page=career-browse" class="btn btn-secondary">Clear</a>
    </form>
  </div>

  <!-- SECTION 2: My Career Plans (3x1 cards) -->
  <div class="browse-section mb-5">
    <h3 class="section-title mb-4">
      <i class="fas fa-bookmark"></i> My Career Plans
    </h3>
    <?php if (!empty($userPlans)): ?>
      <div class="carousel-container position-relative">
        <div class="row g-3" style="overflow-x: auto; display: flex; flex-wrap: nowrap;">
          <?php foreach (array_slice($userPlans, 0, 3) as $career): ?>
            <div class="col-md-4" style="flex: 0 0 33.333%; min-width: 300px;">
              <div class="card h-100 career-card clickable-card" style="cursor: pointer;"
                data-career-id="<?php echo intval($career['id']); ?>"
                data-name="<?php echo htmlspecialchars($career['name']); ?>"
                data-description="<?php echo htmlspecialchars($career['description']); ?>"
                data-cover-photo="<?php echo htmlspecialchars(getImageUrl($career['cover_photo'] ?? null)); ?>"
                data-target-position="<?php echo htmlspecialchars($career['target_position'] ?? 'N/A'); ?>"
                data-duration="<?php echo htmlspecialchars($career['duration_months'] ?? 'N/A'); ?>"
                data-enrolled="<?php echo $enrollmentDetails[$career['id']] ?? 0; ?>"
                data-skills="<?php echo htmlspecialchars($career['required_skills'] ?? 'N/A'); ?>">>
                <img src="<?php echo htmlspecialchars(getImageUrl($career['cover_photo'] ?? null)); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($career['name']); ?>">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title"><?php echo htmlspecialchars($career['name']); ?></h5>
                  <p class="card-text text-muted mb-2" style="font-size: 0.9rem;"><?php echo htmlspecialchars(substr($career['description'], 0, 80)) . '...'; ?></p>
                  <p class="text-center mb-2" style="font-size: 0.9rem;">
                    <small class="badge bg-info">Career Path</small>
                    <?php if (!empty($career['duration_months'])): ?>
                      <small class="badge bg-secondary ms-2"><?php echo intval($career['duration_months']); ?> months</small>
                    <?php endif; ?>
                  </p>
                  <div class="card-meta-grid mb-2">
                    <div class="d-flex justify-content-between gap-1">
                      <small class="meta-label" style="font-size: 0.85rem; color: #666;"><?php echo ucfirst(str_replace('_', ' ', $career['plan_status'] ?? 'Active')); ?></small>
                      <small class="meta-label" style="font-size: 0.85rem; color: #666;">—</small>
                      <small class="meta-label" style="font-size: 0.85rem; color: #666;"><?php if (!empty($career['target_position'])): ?><?php echo htmlspecialchars(substr($career['target_position'], 0, 15)); ?><?php endif; ?></small>
                    </div>
                  </div>
                  <div class="mt-auto d-flex justify-content-between align-items-center">
                    <small class="text-muted">Plans: <?php echo $enrollmentDetails[$career['id']] ?? 0; ?></small>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-info">You haven't started any career plans yet.</div>
    <?php endif; ?>
  </div>

  <!-- SECTION 3: Featured Career Paths (3x1 cards) -->
  <div class="browse-section mb-5">
    <h3 class="section-title mb-4">
      <i class="fas fa-star"></i> Featured Career Paths
    </h3>
    <div class="row g-3">
      <?php foreach ($featuredCareers as $career): ?>
        <div class="col-md-4">
          <div class="card h-100 career-card clickable-card" style="cursor: pointer;"
            data-career-id="<?php echo intval($career['id']); ?>"
            data-name="<?php echo htmlspecialchars($career['name']); ?>"
            data-description="<?php echo htmlspecialchars($career['description']); ?>"
            data-cover-photo="<?php echo htmlspecialchars(getImageUrl($career['cover_photo'] ?? null, 'modules/img/placeholder.gif')); ?>"
            data-target-position="<?php echo htmlspecialchars($career['target_position'] ?? 'N/A'); ?>"
            data-duration="<?php echo htmlspecialchars($career['duration_months'] ?? 'N/A'); ?>"
            data-enrolled="<?php echo $enrollmentDetails[$career['id']] ?? 0; ?>"
            data-skills="<?php echo htmlspecialchars($career['required_skills'] ?? 'N/A'); ?>">>
            <div style="position: relative;">
              <img src="<?php echo htmlspecialchars(getImageUrl($career['cover_photo'] ?? null, 'modules/img/placeholder.gif')); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($career['name']); ?>">
              <span class="badge bg-warning" style="position: absolute; top: 10px; right: 10px;">Featured</span>
            </div>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?php echo htmlspecialchars($career['name']); ?></h5>
              <p class="card-text text-muted mb-2" style="font-size: 0.9rem;"><?php echo htmlspecialchars(substr($career['description'], 0, 80)) . '...'; ?></p>
              <p class="text-center mb-2" style="font-size: 0.9rem;">
                <small class="badge bg-info">Career Path</small>
                <?php if (!empty($career['duration_months'])): ?>
                  <small class="badge bg-secondary ms-2"><?php echo intval($career['duration_months']); ?> months</small>
                <?php endif; ?>
              </p>
              <div class="card-meta-grid mb-2">
                <div class="d-flex justify-content-between gap-1">
                  <small class="meta-label" style="font-size: 0.85rem; color: #666;">Active</small>
                  <small class="meta-label" style="font-size: 0.85rem; color: #666;">—</small>
                  <small class="meta-label" style="font-size: 0.85rem; color: #666;">Rem: <?php echo $enrollmentDetails[$career['id']] ?? 0; ?></small>
                </div>
              </div>
              <div class="mt-auto d-flex justify-content-between align-items-center">
                <small class="text-muted">Plans: <?php echo $enrollmentDetails[$career['id']] ?? 0; ?></small>
                <div class="card-action-set">
                  <?php 
                  $hasplan = false;
                  if ($currentUserId) {
                      foreach ($userPlans as $plan) {
                          if ($plan['id'] == $career['id']) {
                              $hasplan = true;
                              break;
                          }
                      }
                  }
                  ?>
                  <form method="post" style="display: inline;" onclick="event.stopPropagation();">
                    <input type="hidden" name="action" value="start_plan">
                    <input type="hidden" name="id" value="<?php echo intval($career['id']); ?>">
                    <button class="btn btn-sm <?php echo $hasplan ? 'btn-secondary disabled' : 'btn-primary'; ?>" <?php echo $hasplan ? 'disabled' : ''; ?>>
                      <?php echo $hasplan ? 'Started' : 'Start Plan'; ?>
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

  <!-- SECTION 4: All Career Paths (3x5 grid with pagination) -->
  <div class="browse-section mb-5">
    <h3 class="section-title mb-4">
      <i class="fas fa-list"></i> All Career Paths
    </h3>
    <?php if (!empty($paginatedCareers['items'])): ?>
      <div class="row g-3">
        <?php foreach ($paginatedCareers['items'] as $career): ?>
          <div class="col-md-4">
            <div class="card h-100 career-card clickable-card" style="cursor: pointer;"
              data-career-id="<?php echo intval($career['id']); ?>"
              data-name="<?php echo htmlspecialchars($career['name']); ?>"
              data-description="<?php echo htmlspecialchars($career['description']); ?>"
              data-cover-photo="<?php echo htmlspecialchars(getImageUrl($career['cover_photo'] ?? null, $placeholderImg)); ?>"
              data-target-position="<?php echo htmlspecialchars($career['target_position'] ?? 'N/A'); ?>"
              data-duration="<?php echo htmlspecialchars($career['duration_months'] ?? 'N/A'); ?>"
              data-enrolled="<?php echo $enrollmentDetails[$career['id']] ?? 0; ?>"
              data-skills="<?php echo htmlspecialchars($career['required_skills'] ?? 'N/A'); ?>">
              <img src="<?php echo htmlspecialchars(getImageUrl($career['cover_photo'] ?? null, 'modules/img/placeholder.gif')); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($career['name']); ?>">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo htmlspecialchars($career['name']); ?></h5>
                <p class="card-text text-muted mb-2" style="font-size: 0.9rem;"><?php echo htmlspecialchars(substr($career['description'], 0, 80)) . '...'; ?></p>
                <p class="text-center mb-2" style="font-size: 0.9rem;">
                  <small class="badge bg-info">Career Path</small>
                  <?php if (!empty($career['duration_months'])): ?>
                    <small class="badge bg-secondary ms-2"><?php echo intval($career['duration_months']); ?> months</small>
                  <?php endif; ?>
                </p>
                <div class="card-meta-grid mb-2">
                  <div class="d-flex justify-content-between gap-1">
                    <small class="meta-label" style="font-size: 0.85rem; color: #666;">Active</small>
                    <small class="meta-label" style="font-size: 0.85rem; color: #666;">—</small>
                    <small class="meta-label" style="font-size: 0.85rem; color: #666;">Rem: <?php echo $enrollmentDetails[$career['id']] ?? 0; ?></small>
                  </div>
                </div>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                  <small class="text-muted">Plans: <?php echo $enrollmentDetails[$career['id']] ?? 0; ?></small>
                  <div class="card-action-set">
                    <?php 
                    $hasplan = false;
                    if ($currentUserId) {
                        foreach ($userPlans as $plan) {
                            if ($plan['id'] == $career['id']) {
                                $hasplan = true;
                                break;
                            }
                        }
                    }
                    ?>
                    <form method="post" style="display: inline;" onclick="event.stopPropagation();">
                      <input type="hidden" name="action" value="start_plan">
                      <input type="hidden" name="id" value="<?php echo intval($career['id']); ?>">
                      <button class="btn btn-sm <?php echo $hasplan ? 'btn-secondary disabled' : 'btn-primary'; ?>" <?php echo $hasplan ? 'disabled' : ''; ?>>
                        <?php echo $hasplan ? 'Started' : 'Start Plan'; ?>
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
          <?php if ($paginatedCareers['hasPrevPage']): ?>
            <li class="page-item">
              <a class="page-link" href="?page=career-browse&search=<?php echo urlencode($searchQuery); ?>&page_num=<?php echo $paginatedCareers['currentPage'] - 1; ?>">
                <i class="fas fa-chevron-left"></i>
              </a>
            </li>
          <?php else: ?>
            <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
          <?php endif; ?>
          
          <?php for ($i = 1; $i <= $paginatedCareers['totalPages']; $i++): ?>
            <li class="page-item <?php echo $i === $paginatedCareers['currentPage'] ? 'active' : ''; ?>">
              <a class="page-link" href="?page=career-browse&search=<?php echo urlencode($searchQuery); ?>&page_num=<?php echo $i; ?>">
                <?php echo $i; ?>
              </a>
            </li>
          <?php endfor; ?>
          
          <?php if ($paginatedCareers['hasNextPage']): ?>
            <li class="page-item">
              <a class="page-link" href="?page=career-browse&search=<?php echo urlencode($searchQuery); ?>&page_num=<?php echo $paginatedCareers['currentPage'] + 1; ?>">
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
        No career paths available<?php echo !empty($searchQuery) ? ' matching your search.' : '.'; ?>
      </div>
    <?php endif; ?>
  </div>
<!-- Career Details Modal -->
<div class="modal fade" id="careerModal" tabindex="-1" role="dialog" aria-labelledby="careerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="careerModalLabel">Career Path Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Cover Photo -->
        <div class="mb-3">
          <img id="modalCoverPhoto" src="" class="img-fluid rounded" style="width: 100%; max-height: 300px; object-fit: cover;" alt="Career Cover">
        </div>

        <!-- Title -->
        <h4 id="modalCareerName" class="mb-3"></h4>

        <!-- Description -->
        <div class="mb-3">
          <h6 class="text-muted">Description</h6>
          <p id="modalCareerDescription"></p>
        </div>

        <hr>

        <!-- Career Details Grid -->
        <div class="row g-3">
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="text-muted">Target Position</h6>
              <p id="modalTargetPosition" class="mb-0"><strong></strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="text-muted">Duration</h6>
              <p id="modalDuration" class="mb-0"><strong></strong></p>
            </div>
          </div>
          <div class="col-12">
            <div class="mb-3">
              <h6 class="text-muted">Required Skills</h6>
              <p id="modalSkills" class="mb-0"><strong></strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="text-muted">Active Development Plans</h6>
              <p id="modalEnrolled" class="mb-0"><strong></strong></p>
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
    var modalElement = document.getElementById('careerModal');
    
    if (modalElement && clickableCards.length > 0) {
      var careerModal = new (typeof bootstrap !== 'undefined' ? bootstrap.Modal : function(el) {
        this.show = function() { if (typeof jQuery !== 'undefined') jQuery(el).modal('show'); };
      })(modalElement);
      
      clickableCards.forEach(function(card) {
        card.addEventListener('click', function() {
          var name = this.getAttribute('data-name');
          var description = this.getAttribute('data-description');
          var coverPhoto = this.getAttribute('data-cover-photo');
          var targetPosition = this.getAttribute('data-target-position');
          var duration = this.getAttribute('data-duration');
          var skills = this.getAttribute('data-skills');
          var enrolled = this.getAttribute('data-enrolled');

          document.getElementById('modalCoverPhoto').src = coverPhoto;
          document.getElementById('modalCareerName').textContent = name;
          document.getElementById('modalCareerDescription').textContent = description;
          document.getElementById('modalTargetPosition').querySelector('strong').textContent = targetPosition;
          document.getElementById('modalDuration').querySelector('strong').textContent = duration + ' months';
          document.getElementById('modalSkills').querySelector('strong').textContent = skills;
          document.getElementById('modalEnrolled').querySelector('strong').textContent = enrolled + ' users';

          careerModal.show();
        });
      });
    }
  } catch (e) {
    console.error('Error initializing modal:', e);
  }
});
</script>
