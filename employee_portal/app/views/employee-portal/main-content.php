<div class="ml-4 flex-1 w-full">
    <div class="content-wrapper w-full max-w-none">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 mb-2">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        Welcome back,
                        <span class="text-blue-600">
                            <?= htmlspecialchars($employee['first_name'] ?? 'Employee'); ?>
                        </span>
                    </h2>
                </div>


                <!-- Live Clock -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl px-5 py-3 text-center shadow-sm">
                    <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        Current Time
                    </p>

                    <div id="liveClock"
                        class="text-3xl font-bold text-blue-600 mt-1">
                        00:00:00
                    </div>
                </div>

            </div>


            <!-- Notifications -->
            <div class="mt-5">
                <?php require __DIR__ . '/../partials/notif.php'; ?>
            </div>


            <!-- Messages -->
            <?php if (!empty($message)): ?>
                <?php $type = $messageType ?? 'info'; ?>

                <div class="
            mt-4 flex items-center gap-3 
            rounded-xl px-4 py-3
            shadow-sm border
            <?= $type === 'success'
                    ? 'bg-green-50 border-green-200 text-green-700'
                    : 'bg-red-50 border-red-200 text-red-700'
            ?>
        ">

                    <span class="text-xl">
                        <?= $type === 'success' ? '✔' : '✘'; ?>
                    </span>

                    <span class="font-medium">
                        <?= htmlspecialchars($message); ?>
                    </span>

                </div>

            <?php endif; ?>

        </div>
        <!-- Time In/Out Action Section -->
        <?php require __DIR__ . '/section/time-in-out-action.php'; ?>

        <!-- Employee Information -->
        <?php require __DIR__ . '/section/employee-information.php'; ?>

        <!-- Pending Request -->
        <?php require __DIR__ . '/section/pending-request.php'; ?>

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