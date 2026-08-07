<?php
require_once __DIR__ . '/../autoload.php';
use App\Models\Announcement;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    echo '<div class="alert alert-danger">Invalid announcement ID.</div>';
    exit;
}

$announcement = new Announcement();
$detail = $announcement->find($id);

if (!$detail) {
    echo '<div class="alert alert-danger">Announcement not found.</div>';
    exit;
}
?>

<link rel="stylesheet" href="css/announcement_detail.css" />

<div class="announcement-detail-content">
  <div class="mb-3">
    <h5 class="text-primary mb-2">
      <i class="fas fa-bullhorn mr-2"></i><?= htmlspecialchars($detail['title']) ?>
    </h5>
    <div class="text-muted small mb-3">
      <i class="fas fa-calendar"></i> Posted: <?= date('M d, Y H:i', strtotime($detail['created_at'])) ?>
      <?php if (!empty($detail['target_audience'])): ?>
        | <i class="fas fa-users"></i> Target: <?= htmlspecialchars($detail['target_audience']) ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="announcement-body">
    <?= nl2br(htmlspecialchars($detail['content'])) ?>
  </div>

</div>
