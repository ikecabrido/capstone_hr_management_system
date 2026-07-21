<div class="content-wrapper w-full max-w-none" style="padding: 25px 30px;">
    <?php require __DIR__ . '/../partials/notif.php'; ?>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
        <div>
            <h1 style="margin: 0 0 5px 0; font-size: 28px; font-weight: 600; color: #003d82;">Dashboard</h1>
            <p style="margin: 0; color: #666; font-size: 14px;">Welcome back, <strong><?php echo isset($employee['full_name']) ? htmlspecialchars($employee['full_name']) : "none"; ?></strong>!</p>
        </div>
        <div class="live-clock" id="liveClock" style="font-size: 24px; font-weight: bold; color: #003d82; background: #f0f4f8; padding: 10px 20px; border-radius: 8px;">00:00:00</div>
    </div>
    <?php require __DIR__ . '/../partials/notif.php'; ?>

    <!-- Messages -->
    <?php if (!empty($message)): ?>
        <?php $type = $messageType ?? 'info'; ?>
        <div class="alert alert-<?php echo htmlspecialchars($type); ?>" style="margin-bottom: 15px;">
            <span class="alert-icon">
                <?php echo $type === 'success' ? '✔' : '✘'; ?>
            </span>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
    <?php endif; ?>

    <!-- Time In/Out Action Section -->
    <div class="time-action-section">
        <div class="time-action-header">
            <h3>Time In/Out</h3>
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
            <form id="timeInForm" method="POST" action="index.php?url=employee-time-in">
                <input type="hidden" value="<?= $employee_id ?>" name="employee_id">
                <input type="hidden" value="time_in" name="time_in">
                <button type="button" onclick="showTimeInModal()" class="btn-time-action btn-time-in">
                    Time In
                </button>
            </form>
        <?php elseif (empty($statusInfo['time_out'])): ?>
            <form id="timeOutForm" method="POST" action="index.php?url=employee-time-out">
                <input type="hidden" value="<?= $employee_id ?>" name="employee_id">
                <input type="hidden" value="time_out" name="time_out">
                <button type="button" onclick="showTimeOutModal()" class="btn-time-action btn-time-out">
                    Time Out
                </button>
            </form>
        <?php else: ?>
            <button type="submit" class="btn-time-action" disabled>
                Time In Completed
            </button>
        <?php endif; ?>
    </div>

    <!-- Leave Balance -->
    <div class="leave-balance-section">
        <div class="leave-balance-header">
            <h2 style="margin: 0; flex: 1;">Leave Balance</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#leaveRequestModal" style="width: auto; text-align: center; padding: 8px 16px;">
                ➕ Request Leave
            </button>
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

        <!-- Recent Attendance -->
        <div style="margin-top: 20px;">
            <h2 style="margin: 0 0 15px 0; font-size: 20px; color: #003d82;">Recent Attendance</h2>
            <div class="attendance-record">
                <?php if (!empty($monthly_attendance)): ?>
                    <?php foreach (array_slice($monthly_attendance, 0, 10) as $record): ?>
                        <?php
                        $statusClass = isset($record['status']) ? strtolower($record['status']) : 'none';
                        $timeInDate = !empty($record['time_in']) ? Helper::formatDate($record['time_in']) : 'none';
                        $timeInTime = !empty($record['time_in']) ? Helper::formatTime($record['time_in']) : 'none';
                        $timeOutTime = !empty($record['time_out']) ? Helper::formatTime($record['time_out']) : 'Not yet';
                        $statusText = $record['status'] ?? 'none';
                        $statusColor = ($statusText === 'ON_TIME') ? '#27ae60' : '#f39c12';
                        $hoursWorked = isset($record['total_hours_worked']) ? number_format($record['total_hours_worked'], 2) : '0.00';
                        ?>
                        <div class="record-item <?php echo $statusClass; ?>">
                            <strong><?php echo $timeInDate; ?></strong><br>
                            In: <?php echo $timeInTime; ?><br>
                            Out: <?php echo $timeOutTime; ?><br>
                            Status: <span style="color: <?php echo $statusColor; ?>;"><strong><?php echo $statusText; ?></strong></span><br>
                            Hours: <?php echo $hoursWorked; ?>h
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No attendance records this month.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Leave Requests History -->
        <div style="margin-top: 20px;">
            <h2 style="margin: 0 0 15px 0; font-size: 20px; color: #003d82;">📋 My Leave Requests</h2>
            <div class="leave-requests-section">
                <?php if (!empty($leave_requests)): ?>
                    <div style="display: grid; gap: 12px;">
                        <?php foreach ($leave_requests as $req): ?>
                            <?php
                            $leaveType = htmlspecialchars($req['leave_type_name'] ?? 'none');
                            $startDate = !empty($req['start_date']) ? date('M d, Y', strtotime($req['start_date'])) : 'none';
                            $endDate = !empty($req['end_date']) ? date('M d, Y', strtotime($req['end_date'])) : 'none';
                            $reason = !empty($req['reason']) ? htmlspecialchars(substr($req['reason'], 0, 60)) : 'none';
                            $reasonMore = strlen($req['reason'] ?? '') > 60 ? '...' : '';
                            $submitted = !empty($req['created_at']) ? date('M d, Y h:i A', strtotime($req['created_at'])) : 'none';
                            $status = htmlspecialchars($req['status'] ?? 'none');
                            $remarks = htmlspecialchars($req['remarks'] ?? 'none');
                            $statusColor = $status === 'Pending' ? ['bg' => '#fff3cd', 'text' => '#856404'] : ($status === 'Approved' || $status === 'Final-Approved' ? ['bg' => '#d4edda', 'text' => '#155724'] :
                                ['bg' => '#f8d7da', 'text' => '#721c24']);
                            $borderColor = $status === 'Pending' ? '#f39c12' : ($status === 'Approved' || $status === 'Final-Approved' ? '#27ae60' : '#e74c3c');
                            ?>
                            <div style="background: white; padding: 15px; border-radius: 8px; border-left: 4px solid <?php echo $borderColor; ?>; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 8px 0; color: #333;"><?php echo $leaveType; ?></h4>
                                        <p style="margin: 0 0 5px 0; color: #666; font-size: 13px;"><strong>Dates:</strong> <?php echo $startDate; ?> - <?php echo $endDate; ?></p>
                                        <p style="margin: 0 0 5px 0; color: #666; font-size: 13px;"><strong>Reason:</strong> <?php echo $reason . $reasonMore; ?></p>
                                        <p style="margin: 0; color: #999; font-size: 12px;">Submitted: <?php echo $submitted; ?></p>
                                    </div>
                                    <span style="background: <?php echo $statusColor['bg']; ?>; color: <?php echo $statusColor['text']; ?>; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; white-space: nowrap;">
                                        <?php echo $status; ?>
                                    </span>
                                </div>
                                <?php if (!empty($req['remarks'])): ?>
                                    <p style="margin: 8px 0 0 0; padding-top: 8px; border-top: 1px solid #eee; color: #666; font-size: 12px;">
                                        <strong>Remarks:</strong> <?php echo $remarks; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; color: #999; border: 1px solid #eee;">
                        <p>📭 No leave requests yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Time In Confirmation Modal -->
<div id="timeInModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="timeInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                <h5 class="modal-title" id="timeInModalLabel">
                    <i class="fas fa-clock" style="margin-right: 8px;"></i>Confirm Time In
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="text-align: center; padding: 30px;">
                <div style="margin-bottom: 20px;">
                    <p style="color: #666; font-size: 14px; margin-bottom: 10px;">You are about to clock in at:</p>
                    <div id="timeInDisplay" style="font-size: 32px; font-weight: bold; color: #667eea; margin: 15px 0;">
                        00:00:00
                    </div>
                    <p id="dateInDisplay" style="color: #999; font-size: 13px;">Loading...</p>
                </div>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
                    <p style="margin: 0; color: #666; font-size: 13px;">
                        <i class="fas fa-info-circle" style="margin-right: 8px; color: #667eea;"></i>
                        Make sure you are ready to start your work day.
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #eee;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times" style="margin-right: 8px;"></i>Cancel
                </button>
                <button type="button" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;" onclick="confirmTimeIn()">
                    <i class="fas fa-check" style="margin-right: 8px;"></i>Confirm Time In
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Time Out Confirmation Modal -->
<div id="timeOutModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="timeOutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none;">
                <h5 class="modal-title" id="timeOutModalLabel">
                    <i class="fas fa-clock" style="margin-right: 8px;"></i>Confirm Time Out
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="text-align: center; padding: 30px;">
                <div style="margin-bottom: 20px;">
                    <p style="color: #666; font-size: 14px; margin-bottom: 10px;">You are about to clock out at:</p>
                    <div id="timeOutDisplay" style="font-size: 32px; font-weight: bold; color: #f5576c; margin: 15px 0;">
                        00:00:00
                    </div>
                    <p id="dateOutDisplay" style="color: #999; font-size: 13px;">Loading...</p>
                </div>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
                    <p style="margin: 0; color: #666; font-size: 13px;">
                        <i class="fas fa-info-circle" style="margin-right: 8px; color: #f5576c;"></i>
                        Make sure you have finished all your work tasks.
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #eee;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times" style="margin-right: 8px;"></i>Cancel
                </button>
                <button type="button" class="btn" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none;" onclick="confirmTimeOut()">
                    <i class="fas fa-check" style="margin-right: 8px;"></i>Confirm Time Out
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Update time display in modals every second
function updateTimeDisplay() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour12: false });
    const dateStr = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    
    document.getElementById('timeInDisplay').textContent = timeStr;
    document.getElementById('timeOutDisplay').textContent = timeStr;
    document.getElementById('dateInDisplay').textContent = dateStr;
    document.getElementById('dateOutDisplay').textContent = dateStr;
}

// Show Time In confirmation modal
function showTimeInModal() {
    updateTimeDisplay();
    
    // Try Bootstrap 5+ first, fallback to jQuery/Bootstrap 4
    const timeInModalElement = document.getElementById('timeInModal');
    
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modal = new bootstrap.Modal(timeInModalElement);
        modal.show();
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
        $(timeInModalElement).modal('show');
    } else {
        // Fallback: add visible classes manually
        timeInModalElement.classList.add('show');
        timeInModalElement.style.display = 'block';
        document.body.classList.add('modal-open');
        const backdrop = document.createElement('div');
        backdrop.classList.add('modal-backdrop', 'fade', 'show');
        document.body.appendChild(backdrop);
    }
    
    // Update time every second while modal is open
    window.timeInInterval = setInterval(updateTimeDisplay, 1000);
}

// Show Time Out confirmation modal
function showTimeOutModal() {
    updateTimeDisplay();
    
    // Try Bootstrap 5+ first, fallback to jQuery/Bootstrap 4
    const timeOutModalElement = document.getElementById('timeOutModal');
    
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modal = new bootstrap.Modal(timeOutModalElement);
        modal.show();
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
        $(timeOutModalElement).modal('show');
    } else {
        // Fallback: add visible classes manually
        timeOutModalElement.classList.add('show');
        timeOutModalElement.style.display = 'block';
        document.body.classList.add('modal-open');
        const backdrop = document.createElement('div');
        backdrop.classList.add('modal-backdrop', 'fade', 'show');
        document.body.appendChild(backdrop);
    }
    
    // Update time every second while modal is open
    window.timeOutInterval = setInterval(updateTimeDisplay, 1000);
}

// Confirm Time In
function confirmTimeIn() {
    clearInterval(window.timeInInterval);
    document.getElementById('timeInForm').submit();
}

// Confirm Time Out
function confirmTimeOut() {
    clearInterval(window.timeOutInterval);
    document.getElementById('timeOutForm').submit();
}

// Clear interval when modal is closed
if (document.addEventListener) {
    document.addEventListener('hidden.bs.modal', function (e) {
        if (e.target && e.target.id === 'timeInModal') {
            clearInterval(window.timeInInterval);
        }
        if (e.target && e.target.id === 'timeOutModal') {
            clearInterval(window.timeOutInterval);
        }
    });
}
</script>

<?php require __DIR__ . '/../leave-request/modal-leave-request.php'; ?>