<div class="w-[800px] card shadow-sm border-0 mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-graduation-cap mr-2"></i>
            Upcoming Training
        </h5>
    </div>

    <div class="card-body">

        <?php if (!empty($upcomingTrainings)): ?>

            <?php foreach ($upcomingTrainings as $training): ?>

                <?php

                switch ($training['request_status']) {

                    case 'Approved':
                        $statusClass = 'status-approved';
                        $icon = 'fa-check-circle';
                        break;

                    case 'Received':
                        $statusClass = 'status-received';
                        $icon = 'fa-inbox';
                        break;

                    case 'New':
                        $statusClass = 'status-new';
                        $icon = 'fa-clock';
                        break;

                    case 'Rejected':
                        $statusClass = 'status-rejected';
                        $icon = 'fa-times-circle';
                        break;

                    default:
                        $statusClass = 'status-default';
                        $icon = 'fa-circle';
                }
                ?>

                <div class="border-bottom py-3">

                    <strong>
                        <?= htmlspecialchars($training['title']) ?>
                    </strong>

                    <br>

                    <small class="text-muted">
                        <?= htmlspecialchars($training['trainer']) ?>
                    </small>

                    <br>

                    <small>
                        <?= date('M d, Y', strtotime($training['start_date'])) ?>
                        -
                        <?= date('M d, Y', strtotime($training['end_date'])) ?>
                    </small>

                    <br>

                    <span class="status-pill <?= $statusClass ?>">
                        <i class="fas <?= $icon ?> mr-1"></i>
                        <?= htmlspecialchars($training['request_status']) ?>
                    </span>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="text-center text-muted py-3">
                No upcoming training.
            </div>

        <?php endif; ?>

    </div>
</div>