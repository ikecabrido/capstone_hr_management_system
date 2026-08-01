<?php
$page_title = $page_title ?? '';
$page_subtitle = $page_subtitle ?? '';
$page_icon = $page_icon ?? 'fa-columns';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <div class="content-header-title">
            <i class="fas <?= htmlspecialchars($page_icon) ?>"></i>
            <div>
              <h1 class="m-0"><?= htmlspecialchars($page_title) ?></h1>
              <?php if ($page_subtitle): ?>
              <p class="text-muted"><?= htmlspecialchars($page_subtitle) ?></p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
