<div class="w-full ml-16">
    <?php require __DIR__ . '/../partials/notif.php'; ?>
    <div class="content-wrapper w-full">
        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <div class="live-clock" id="liveClock">00:00:00</div>
        </div>
        <h1>Dashboard</h1>
        <p class="text-[24px]">Welcome back, <strong><?php echo isset($employee['first_name']) ? htmlspecialchars($employee['first_name']) : "none"; ?></strong>!</p>
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

        <!-- Time In/Out Action Section -->
        <div class="time-action-section">
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
                    <input type="hidden" value="<?= $employee_no ?>" name="employee_no">
                    <input type="hidden" value="time_in" name="time_in">
                    <button type="submit" name="submit" class="btn-time-action btn-time-in">
                        Time In
                    </button>
                    <span style="align-self: center; opacity: 0.9;" class="ml-2">Waiting for Time in</span>
                </form>
            <?php elseif (empty($statusInfo['time_out'])): ?>
                <form method="POST" class="btn btn-primary" action="index.php?url=employee-time-out">
                    <input type="hidden" value="<?= $employee_no ?>" name="employee_no">
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

        <!-- Quick Stats -->
        <div class="dashboard-grid">
            <div class="card present">
                <h3>Present This Month</h3>
                <div class="card-value">
                    <?php echo isset($present_count) ? $present_count : '00'; ?>
                </div>
                <div class="card-unit">days</div>
            </div>

            <div class="card late">
                <h3>Late Arrivals</h3>
                <div class="card-value">
                    <?php echo isset($late_count) ? $late_count : '00'; ?>
                </div>
                <div class="card-unit">times</div>
            </div>

            <div class="card hours">
                <h3>Hours Worked</h3>
                <div class="card-value">
                    <?php echo isset($total_hours)
                        ? number_format($total_hours, 1)
                        : '00'; ?>
                </div>
                <div class="card-unit">hours</div>
            </div>

            <div class="card overtime">
                <h3>Overtime Hours</h3>
                <div class="card-value">
                    <?php echo isset($total_overtime)
                        ? number_format($total_overtime, 1)
                        : '00'; ?>
                </div>
                <div class="card-unit">hours</div>
            </div>
        </div>

        <!-- Today's Shift Schedule Card -->
        <div class="dashboard-grid">
            <div class="card shift-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h3 style="color: white; margin-top: 0;">📅 Today's Shift</h3>
                <?php if (!empty($today_shift)): ?>
                    <div style="margin-top: 15px;">
                        <p style="font-size: 18px; font-weight: bold; margin: 10px 0;">
                            <?php echo htmlspecialchars($today_shift['shift_name'] ?? 'none'); ?>
                        </p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px;">
                            <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 6px; text-align: center;">
                                <p style="margin: 0; font-size: 12px; opacity: 0.9;">Start Time</p>
                                <p style="margin: 5px 0 0 0; font-size: 16px; font-weight: bold;">
                                    <?php echo !empty($today_shift['start_time'])
                                        ? date('h:i A', strtotime($today_shift['start_time']))
                                        : '--:--'; ?>
                                </p>
                            </div>
                            <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 6px; text-align: center;">
                                <p style="margin: 0; font-size: 12px; opacity: 0.9;">End Time</p>
                                <p style="margin: 5px 0 0 0; font-size: 16px; font-weight: bold;">
                                    <?php echo !empty($today_shift['end_time'])
                                        ? date('h:i A', strtotime($today_shift['end_time']))
                                        : '--:--'; ?>
                                </p>
                            </div>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 6px; margin-top: 10px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; opacity: 0.9;">Break Duration</p>
                            <p style="margin: 5px 0 0 0; font-weight: bold;">
                                <?php echo htmlspecialchars($today_shift['break_duration'] ?? 'none'); ?>
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <p style="color: rgba(255,255,255,0.9); font-style: italic; margin-top: 15px;">ℹ️ No shift assigned for today</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Attendance Percentage Chart -->
        <div class="chart-container">
            <h3>Attendance Percentage This Month</h3>
            <div style="position: relative; width: 100%; height: 100%; flex: 1;">
                <canvas id="attendanceChart" width="800" height="400"></canvas>
            </div>
        </div>

        <!-- 6-Month Trend Chart -->
        <div class="chart-container">
            <h3>6-Month Attendance Trend</h3>
            <div style="position: relative; width: 100%; height: 100%; flex: 1;">
                <canvas id="trendChart" width="800" height="400"></canvas>
            </div>
        </div>

        <!-- Leave Balance -->
        <div class="leave-balance-section">
            <div class="leave-balance-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Leave Balance</h2>
                <button onclick="openLeaveModal()" class="btn-primary" style="padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 8px;">
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
        <h2 style="margin-top: 40px;">Recent Attendance</h2>
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

        <!-- Leave Requests History -->
        <h2 style="margin-top: 40px;">📋 My Leave Requests</h2>
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