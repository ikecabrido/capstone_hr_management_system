<div class="w-[800px] card shadow-sm border-0 mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">
            <i class="fas fa-history mr-2"></i>
            Recent Activities
        </h5>
    </div>

    <div class="card-body">

        <?php if (!empty($recentActivities)): ?>

            <?php foreach ($recentActivities as $activity): ?>

                <div class="d-flex border-bottom py-3">

                    <div class="mr-3">
                        <i class="<?= $activity['icon'] ?> text-primary fa-lg"></i>
                    </div>

                    <div class="flex-grow-1">

                        <strong><?= htmlspecialchars($activity['title']) ?></strong>

                        <div class="text-muted">
                            <?= htmlspecialchars($activity['description']) ?>
                        </div>

                        <small class="text-secondary">
                            <?= date('M d, Y h:i A', strtotime($activity['date'])) ?>
                        </small>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="text-center text-muted py-4">
                No recent activities.
            </div>

        <?php endif; ?>

    </div>
</div>