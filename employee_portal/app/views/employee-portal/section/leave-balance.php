<div class="leave-balance-section w-[800px] ">
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