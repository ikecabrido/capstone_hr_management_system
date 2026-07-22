<style>
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 30px;
        font-weight: 600;
        font-size: .82rem;
        letter-spacing: .3px;
    }

    .status-approved {
        background: #198754;
        color: #fff;
    }

    .status-received {
        background: #0d6efd;
        color: #fff;
    }

    .status-new {
        background: #ff8c00;
        color: #fff;
    }

    .status-rejected {
        background: #dc3545;
        color: #fff;
    }

    .status-default {
        background: #495057;
        color: #fff;
    }
</style>
<div class="ml-16 flex-1 w-full">
    <?php require __DIR__ . '/../partials/notif.php'; ?>

    <div class="content-wrapper w-full max-w-none">
        <!-- header -->
        <div>
            <div style="display: flex; align-items: center; margin-bottom: 20px;">
                <div class="live-clock" id="liveClock">00:00:00</div>
            </div>
            <h1>Dashboard</h1>
            <p class="text-[24px]">Welcome back, <strong><?php echo isset($employee['full_name']) ? htmlspecialchars($employee['full_name']) : "none"; ?></strong>!</p>
            <?php require __DIR__ . '/../partials/notif.php'; ?>

            <!-- Messages -->
            <?php if (!empty($message)): ?>
                <?php $type = $messageType ?? 'info'; ?>
                <div class="alert alert-<?php echo htmlspecialchars($type); ?>">
                    <span class="alert-icon">
                        <?php echo $type === 'success' ? '✔' : '✘'; ?>
                    </span>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Time In/Out Action Section -->
        <div class="time-action-section" style="margin-left: 16px; width: 800px;">
            <div class="time-action-header">
                <h3> Time In/Out</h3>
                <span><?php echo date('l, F j, Y'); ?></span>
            </div>
            <div class="time-status">
                <div class="time-status-item">
                    <div class="time-status-label">Time In</div>
                    <div class="time-status-value">
                        <?php echo !empty($statusInfo['time_in'])
                            ? Helper::formatTime($statusInfo['time_in'])
                            : '00:00'; ?>
                    </div>
                </div>

                <div class="time-status-item">
                    <div class="time-status-label">Time Out</div>
                    <div class="time-status-value">
                        <?php echo !empty($statusInfo['time_out'])
                            ? Helper::formatTime($statusInfo['time_out'])
                            : '00:00'; ?>
                    </div>
                </div>

                <div class="time-status-item">
                    <div class="time-status-label">Duration</div>
                    <div class="time-status-value">
                        <?php echo !empty($statusInfo['duration'])
                            ? $statusInfo['duration']
                            : '00:00'; ?>
                    </div>
                </div>
            </div>

            <?php if (empty($statusInfo['time_in'])): ?>
                <form method="POST" class="btn btn-primary" action="index.php?url=employee-time-in">
                    <input type="hidden" value="<?= $employee_id ?>" name="employee_id">
                    <input type="hidden" value="time_in" name="time_in">
                    <button type="submit" name="submit" class="btn-time-action btn-time-in">
                        Time In
                    </button>
                    <span style="align-self: center; opacity: 0.9;" class="ml-2">Waiting for Time in</span>
                </form>
            <?php elseif (empty($statusInfo['time_out'])): ?>
                <form method="POST" class="btn btn-primary" action="index.php?url=employee-time-out">
                    <input type="hidden" value="<?= $employee_id ?>" name="employee_id">
                    <input type="hidden" value="time_out" name="time_out">
                    <button type="submit" name="submit" class="btn-time-action btn-time-out">
                        Time Out
                    </button>
                    <span style="align-self: center; opacity: 0.9;" class="ml-2">Already timed in</span>
                </form>
            <?php else: ?>
                <button type="submit" class="btn-time-action" disabled>
                    Time In Completed
                </button>
            <?php endif; ?>
        </div>

        <!-- Employee Information -->
        <div class="row">
            <div class="col-lg-6"></div>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle mr-2"></i>
                        Employee Information
                    </h5>
                </div>
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-5 text-muted font-weight-bold">
                            Employee No.
                        </div>
                        <div class="col-7">
                            <?= htmlspecialchars($employee['employee_no'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5 text-muted font-weight-bold">
                            Name
                        </div>
                        <div class="col-7">
                            <?= htmlspecialchars($employee['full_name'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5 text-muted font-weight-bold">
                            Department
                        </div>
                        <div class="col-7">
                            <?= htmlspecialchars($employee['department'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5 text-muted font-weight-bold">
                            Position
                        </div>
                        <div class="col-7">
                            <?= htmlspecialchars($employee['position_name'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5 text-muted font-weight-bold">
                            Date Hired
                        </div>
                        <div class="col-7">
                            <?= !empty($employee['date_hired'])
                                ? date('F d, Y', strtotime($employee['date_hired']))
                                : '-' ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-5 text-muted font-weight-bold">
                            Employment
                        </div>
                        <div class="col-7">
                            <span class="badge badge-success px-3 py-2">
                                <?= ucfirst($employee['employment_status'] ?? '-') ?>
                            </span>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Pending Request -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-hourglass-half mr-2"></i>
                            Pending Requests
                        </h5>
                    </div>
                    <div class="card-body">

                        <?php if (!empty($pendingRequests)): ?>

                            <?php foreach ($pendingRequests as $request): ?>

                                <?php
                                $badge = 'warning';

                                if ($request['status'] === 'Approved') {
                                    $badge = 'success';
                                } elseif ($request['status'] === 'Rejected') {
                                    $badge = 'danger';
                                }
                                ?>

                                <div class="d-flex justify-content-between align-items-center border-bottom py-3">

                                    <div>
                                        <strong><?= htmlspecialchars($request['leave_type_name']) ?></strong><br>

                                        <small class="text-muted">
                                            <?= date('M d, Y', strtotime($request['start_date'])) ?>
                                            -
                                            <?= date('M d, Y', strtotime($request['end_date'])) ?>
                                        </small>

                                        <br>

                                        <small>
                                            <?= htmlspecialchars($request['details']) ?>
                                        </small>
                                    </div>

                                    <span class="badge badge-<?= $badge ?> px-3 py-2">
                                        <?= htmlspecialchars($request['status']) ?>
                                    </span>

                                </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <div class="text-center text-muted py-3">
                                No pending requests.
                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- Company Announcements -->
        <div class="card shadow-sm border-0 mb-4">
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

        <!-- Recent Activities -->
        <div class="card shadow-sm border-0 mb-4">
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

        <!-- Upcoming Training -->
        <div class="card shadow-sm border-0 mb-4">
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

        <!-- Leave Balance -->
        <div class="leave-balance-section">
            <div class="leave-balance-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Leave Balance</h2>
                <a href="index.php?url=employee-leave-request" class="btn-primary" style="padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                    ➕ Request Leave
                </a>
            </div>

            <?php if (!empty($leave_balances)): ?>
                <div class="leave-balance-container">
                    <?php foreach ($leave_balances as $balance): ?>
                        <?php
                        $leaveType = htmlspecialchars($balance['leave_type_name'] ?? 'none');
                        $totalDays = $balance['total_days'] ?? 0;
                        $usedDays = $balance['used_days'] ?? 0;
                        $remainingDays = $balance['remaining_days'] ?? 0;
                        $usedPercent = $totalDays > 0 ? ($usedDays / $totalDays) * 100 : 0;
                        $remainingPercent = $totalDays > 0 ? ($remainingDays / $totalDays) * 100 : 0;
                        ?>
                        <div class="leave-balance-card">
                            <div class="leave-type-name"><?php echo $leaveType; ?></div>

                            <div class="leave-stats">
                                <div class="stat">
                                    <div class="stat-value"><?php echo $totalDays; ?></div>
                                    <div class="stat-label">Total</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-value" style="color: #e74c3c;"><?php echo $usedDays; ?></div>
                                    <div class="stat-label">Used</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-value" style="color: #27ae60;"><?php echo $remainingDays; ?></div>
                                    <div class="stat-label">Remaining</div>
                                </div>
                            </div>

                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $usedPercent; ?>%;"></div>
                            </div>
                            <div class="progress-label">
                                <span><?php echo round($usedPercent); ?>% Used</span>
                                <span><?php echo round($remainingPercent); ?>% Available</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="background: white; padding: 30px; border-radius: 12px; text-align: center; color: #666; border: 2px solid #e8eef7;">
                    <p style="margin: 0; font-size: 15px;">ℹ️ No leave balance information available for this year.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Notifications -->
        <div class="card shadow-sm border-0 mb-4">
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

        <!-- Recent Attendance -->
        <div class="card shadow-sm border-0 mb-4">
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

        <!-- Leave Request History -->
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt mr-2"></i>
                    My Leave Requests
                </h5>
            </div>

            <div class="card-body">

                <?php if (!empty($leave_requests)): ?>

                    <?php foreach ($leave_requests as $req): ?>

                        <?php

                        switch ($req['status']) {

                            case 'Approved':
                            case 'Final-Approved':
                                $statusClass = 'status-approved';
                                $icon = 'fa-check-circle';
                                break;

                            case 'Pending':
                                $statusClass = 'status-pending';
                                $icon = 'fa-hourglass-half';
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

                        <div class="border rounded p-3 mb-3">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="mb-1">
                                        <?= htmlspecialchars($req['leave_type_name']) ?>
                                    </h6>

                                    <small class="text-muted">

                                        <i class="far fa-calendar-alt"></i>

                                        <?= date('M d, Y', strtotime($req['start_date'])) ?>

                                        -

                                        <?= date('M d, Y', strtotime($req['end_date'])) ?>

                                    </small>

                                </div>

                                <span class="status-pill <?= $statusClass ?>">
                                    <i class="fas <?= $icon ?> mr-2"></i>
                                    <?= htmlspecialchars($req['status']) ?>
                                </span>

                            </div>

                            <hr>

                            <p class="mb-2">

                                <strong>Reason:</strong>

                                <?= htmlspecialchars($req['reason'] ?? $req['details'] ?? '-') ?>

                            </p>

                            <small class="text-muted">

                                Submitted:
                                <?= !empty($req['created_at'])
                                    ? date('M d, Y h:i A', strtotime($req['created_at']))
                                    : '-' ?>

                            </small>

                            <?php if (!empty($req['remarks'])): ?>

                                <div class="alert alert-light border mt-3 mb-0">

                                    <strong>HR Remarks</strong><br>

                                    <?= htmlspecialchars($req['remarks']) ?>

                                </div>

                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="text-center py-4 text-muted">

                        <i class="fas fa-inbox fa-2x mb-2"></i>

                        <p class="mb-0">No leave requests found.</p>

                    </div>

                <?php endif; ?>

            </div>

        </div>

        <!-- Quick Actions -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-bolt mr-2"></i>
                    Quick Actions
                </h5>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <a href="index.php?url=employee-leave-request"
                            class="btn btn-success btn-block py-3">
                            <i class="fas fa-calendar-plus mb-2 d-block fa-lg"></i>
                            Request Leave
                        </a>
                    </div>

                    <div class="col-md-4 mb-3">
                        <a href="index.php?url=employee-training-request"
                            class="btn btn-primary btn-block py-3">
                            <i class="fas fa-graduation-cap mb-2 d-block fa-lg"></i>
                            Request Training
                        </a>
                    </div>

                    <div class="col-md-4 mb-3">
                        <a href="index.php?url=employee-document-request"
                            class="btn btn-warning btn-block py-3">
                            <i class="fas fa-file-alt mb-2 d-block fa-lg"></i>
                            Request Document
                        </a>
                    </div>

                    <div class="col-md-4 mb-3">
                        <a href="index.php?url=employee-payslip"
                            class="btn btn-info btn-block py-3">
                            <i class="fas fa-money-check-alt mb-2 d-block fa-lg"></i>
                            View Payslips
                        </a>
                    </div>

                    <div class="col-md-4 mb-3">
                        <a href="index.php?url=employee-online-meeting"
                            class="btn btn-secondary btn-block py-3">
                            <i class="fas fa-video mb-2 d-block fa-lg"></i>
                            Join Meeting
                        </a>
                    </div>

                    <div class="col-md-4 mb-3">
                        <a href="index.php?url=employee-profile"
                            class="btn btn-dark btn-block py-3">
                            <i class="fas fa-user-edit mb-2 d-block fa-lg"></i>
                            Update Profile
                        </a>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>