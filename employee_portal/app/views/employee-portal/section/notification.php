<div class="w-[800px] card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-bell mr-2"></i>
            Notifications
        </h5>

        <a href="index.php?url=employee-notification"
            class="btn btn-sm btn-success">
            View All
        </a>
    </div>

    <div class="card-body">

        <?php if (!empty($notifications)): ?>

            <?php foreach ($notifications as $notification): ?>

                <?php
                $badge = $notification['is_read']
                    ? 'secondary'
                    : 'primary';
                ?>

                <div class="border-bottom py-3">

                    <div class="d-flex justify-content-between">

                        <strong>
                            <?= htmlspecialchars($notification['title']) ?>
                        </strong>

                        <span class="badge badge-<?= $badge ?>">
                            <?= $notification['is_read'] ? 'Read' : 'Unread' ?>
                        </span>

                    </div>

                    <small class="text-muted">
                        <?= date('M d, Y h:i A', strtotime($notification['created_at'])) ?>
                    </small>

                    <p class="mb-0 mt-2">
                        <?= htmlspecialchars(substr($notification['message'], 0, 80)) ?>
                        <?= strlen($notification['message']) > 80 ? '...' : '' ?>
                    </p>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="text-center text-muted py-4">
                <i class="fas fa-bell-slash fa-2x mb-2"></i>
                <p class="mb-0">No notifications available.</p>
            </div>

        <?php endif; ?>

    </div>
</div>