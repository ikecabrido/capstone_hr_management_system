<?php
require_once __DIR__ . '/../autoload.php';
use App\Models\Policy;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    echo '<div class="alert alert-danger">Invalid policy ID.</div>';
    exit;
}

$policy = new Policy();
$detail = $policy->find($id);

if (!$detail) {
    echo '<div class="alert alert-danger">Policy not found.</div>';
    exit;
}
?>

<link rel="stylesheet" href="css/policy_detail.css" />

<div class="policy-detail-content">
  <div class="mb-3">
    <h5 class="text-primary mb-2">
      <i class="fas fa-file-contract mr-2"></i><?= htmlspecialchars($detail['title']) ?>
    </h5>
    <div class="text-muted small mb-3">
      <i class="fas fa-calendar"></i> Posted: <?= date('M d, Y H:i', strtotime($detail['created_at'])) ?>
      <?php if (!empty($detail['target_audience'])): ?>
        | <i class="fas fa-users"></i> Target: <?= htmlspecialchars($detail['target_audience']) ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="policy-body">
    <?= nl2br(htmlspecialchars($detail['content'])) ?>
  </div>

  <div class="mt-4 pt-3 border-top">
    <button class="btn btn-secondary btn-sm" onclick="closeGlobalModal()">
      <i class="fas fa-times"></i> Close
    </button>
  </div>
</div>
