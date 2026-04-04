<div class="w-full ml-32 mt-4">
    <div class="content-wrapper w-full">
        <div class="card shadow-sm border-0 rounded-4 w-full">
            <?php require __DIR__ . '/../partials/notif.php' ?>
            <div class="card-header bg-primary text-white rounded-top-4">
                <h5 class="mb-0 text-4xl">Announcements 📢</h5>
            </div>
            <div class="card-body p-4">

                <?php if (!empty($announcements)): ?>
                    <div class="d-flex flex-column gap-3">

                        <?php foreach ($announcements as $announcement): ?>
                            <?php
                            $title = htmlspecialchars($announcement['title'] ?? 'No Title');
                            $content = nl2br(htmlspecialchars($announcement['content'] ?? ''));
                            $date = !empty($announcement['created_at'])
                                ? date('F d, Y h:i A', strtotime($announcement['created_at']))
                                : 'Unknown date';
                            ?>

                            <div class="announcement-card p-3 rounded-3 shadow-sm border-start border-4 border-primary bg-light">

                                <!-- Header -->
                                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-bullhorn"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark">
                                                <?= $title ?>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i><?= $date ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Divider -->
                                <hr class="my-0">

                                <!-- Content -->
                                <div class="card-body">
                                    <p class="mb-0 text-secondary" style="line-height: 1.6; font-size: 14.5px;">
                                        <?= $content ?>
                                    </p>
                                </div>

                            </div>
                        <?php endforeach; ?>

                    </div>

                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <h6>📭 No announcements available</h6>
                        <p class="mb-0">Stay tuned for updates from HR.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>