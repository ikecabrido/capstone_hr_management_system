<div class="w-[800px] card shadow-sm border-0 mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-bullhorn mr-2"></i>
            Company Announcements
        </h5>

        <a href="index.php?url=employee-announcements"
            class="btn btn-sm btn-primary">
            View All
        </a>
    </div>

    <div class="card-body">

        <?php if (!empty($announcements)): ?>

            <?php foreach (array_slice($announcements, 0, 3) as $announcement): ?>

                <div class="mb-3 pb-3 border-bottom">

                    <h6 class="font-weight-bold mb-1">
                        <?= htmlspecialchars($announcement['title']) ?>
                    </h6>

                    <small class="text-muted">
                        <?= date('F d, Y', strtotime($announcement['created_at'])) ?>
                    </small>

                    <p class="mb-0 mt-2 text-muted">
                        <?= htmlspecialchars(substr($announcement['content'], 0, 100)) ?>
                        <?= strlen($announcement['content']) > 100 ? '...' : '' ?>
                    </p>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="text-center text-muted py-4">
                <i class="fas fa-bullhorn fa-2x mb-2"></i>
                <p class="mb-0">No announcements available.</p>
            </div>

        <?php endif; ?>

    </div>
</div>