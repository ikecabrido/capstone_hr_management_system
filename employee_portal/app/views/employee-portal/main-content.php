
<div class="ml-4 flex-1 w-full">
    <div class="content-wrapper w-full max-w-none">
        <!-- header -->
        <div>
            <div style="display: flex; align-items: center; margin-bottom: 20px;">
                <div class="live-clock" id="liveClock">00:00:00</div>
            </div>
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
        </div>


        <!-- Time In/Out Action Section -->
        <?php require __DIR__ . '/section/time-in-out-action.php'; ?>

        <div class="flex flex-col md:flex-row gap-4 mt-4">
            <!-- Employee Information -->
            <?php require __DIR__ . '/section/employee-information.php'; ?>


            <!-- Pending Request -->
            <?php require __DIR__ . '/section/pending-request.php'; ?>
        </div>

        <!-- Company Announcements -->
        <?php require __DIR__ . '/section/company-announcements.php'; ?>

        <!-- Recent Activities -->
        <?php require __DIR__ . '/section/recent-activities.php'; ?>

        <!-- Upcoming Training -->
        <?php require __DIR__ . '/section/upcoming-training.php'; ?>

        <!-- Leave Balance -->
        <?php require __DIR__ . '/section/leave-balance.php'; ?>

        <!-- Notifications -->
        <?php require __DIR__ . '/section/notification.php'; ?>

        <!-- Recent Attendance -->
        <?php require __DIR__ . '/section/recent-attendance.php'; ?>

        <!-- Leave Request History -->
        <?php require __DIR__ . '/section/leave-request-history.php'; ?>

        <!-- Quick Actions -->
        <?php require __DIR__ . '/section/quick-actions.php'; ?>

    </div>
</div>