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