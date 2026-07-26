<div class="w-[800px] card shadow-sm border-0 mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-clock mr-2"></i>
            Recent Attendance
        </h5>
    </div>

    <div class="card-body">

        <?php if (!empty($monthly_attendance)): ?>

            <?php foreach (array_slice($monthly_attendance, 0, 5) as $record): ?>

                <?php
                $status = $record['status'] ?? 'Unknown';

                switch ($status) {
                    case 'ON_TIME':
                        $badge = 'success';
                        break;

                    case 'LATE':
                        $badge = 'warning';
                        break;

                    case 'ABSENT':
                        $badge = 'danger';
                        break;

                    default:
                        $badge = 'secondary';
                }
                ?>

                <div class="border-bottom py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <strong>
                                <?= !empty($record['time_in'])
                                    ? Helper::formatDate($record['time_in'])
                                    : '-' ?>
                            </strong>

                            <div class="text-muted mt-1">

                                <i class="fas fa-sign-in-alt text-success"></i>
                                <?= !empty($record['time_in'])
                                    ? Helper::formatTime($record['time_in'])
                                    : '--:--' ?>

                                &nbsp;&nbsp;

                                <i class="fas fa-sign-out-alt text-danger"></i>
                                <?= !empty($record['time_out'])
                                    ? Helper::formatTime($record['time_out'])
                                    : 'Not yet' ?>

                            </div>

                            <small class="text-secondary">
                                Hours Worked:
                                <?= number_format($record['total_hours_worked'] ?? 0, 2) ?> hrs
                            </small>

                        </div>

                        <span class="badge badge-<?= $badge ?> px-3 py-2">
                            <?= str_replace('_', ' ', $status) ?>
                        </span>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="text-center py-4 text-muted">
                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                <p class="mb-0">No attendance records this month.</p>
            </div>

        <?php endif; ?>

    </div>
</div>