<div class="w-full ml-2 mt-4">
    <div class="content-wrapper text-3xl">
        <div class="pt-10 pl-10">
            <!-- Time In and Out Action -->
            <section>
                <?php require __DIR__ . '/../../views/partials/notif.php'; ?>
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">
                            <i class="fas fa-clock mr-2"></i>
                            Attendance Today
                        </h2>
                        <p class="text-blue-100 text-sm mt-1">
                            Record your daily attendance
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-sm opacity-90">Today</p>
                        <p class="font-semibold">
                            <?= date('l, F j, Y'); ?>
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-8">
                    <!-- Time In -->
                    <div class="bg-blue-50 rounded-xl p-6 text-center border border-blue-100">
                        <div class="w-14 h-14 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-sign-in-alt text-xl"></i>
                        </div>
                        <p class="text-sm text-gray-500 uppercase tracking-wide">
                            Time In
                        </p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">
                            <?= !empty($statusInfo['time_in'])
                                ? Helper::formatTime($statusInfo['time_in'])
                                : '--:--'; ?>
                        </h3>
                    </div>
                    <!-- Time Out -->
                    <div class="bg-red-50 rounded-xl p-6 text-center border border-red-100">
                        <div class="w-14 h-14 bg-red-600 text-white rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-sign-out-alt text-xl"></i>
                        </div>
                        <p class="text-sm text-gray-500 uppercase tracking-wide">
                            Time Out
                        </p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">
                            <?= !empty($statusInfo['time_out'])
                                ? Helper::formatTime($statusInfo['time_out'])
                                : '--:--'; ?>
                        </h3>
                    </div>
                    <!-- Duration -->
                    <div class="bg-green-50 rounded-xl p-6 text-center border border-green-100">
                        <div class="w-14 h-14 bg-green-600 text-white rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-hourglass-half text-xl"></i>
                        </div>
                        <p class="text-sm text-gray-500 uppercase tracking-wide">
                            Hours Worked
                        </p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">
                            <?= !empty($statusInfo['duration'])
                                ? $statusInfo['duration']
                                : '00:00'; ?>
                        </h3>
                    </div>
                </div>
                <div class="bg-gray-50 border-t px-8 py-6">
                    <?php if (empty($statusInfo['time_in'])): ?>
                        <form method="POST"
                            action="index.php?url=employee-time-in"
                            class="flex flex-col md:flex-row items-center justify-between gap-4">
                            <input type="hidden" name="employee_id" value="<?= $employee_id ?>">
                            <input type="hidden" name="time_in" value="time_in">
                            <input type="hidden" name="attendance_view" value="true">
                            <div>
                                <h5 class="font-semibold text-gray-800">
                                    Please time In!
                                </h5>
                                <p class="text-gray-500 text-sm">
                                    Click the button below to record your time in.
                                </p>
                            </div>
                            <button
                                type="submit"
                                name="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold shadow transition">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Time In
                            </button>
                        </form>
                    <?php elseif (empty($statusInfo['time_out'])): ?>
                        <form method="POST"
                            action="index.php?url=employee-time-out"
                            class="flex flex-col md:flex-row items-center justify-between gap-4">
                            <input type="hidden" name="employee_id" value="<?= $employee_id ?>">
                            <input type="hidden" name="time_out" value="time_out">
                            <input type="hidden" name="attendance_view" value="true">
                            <div>
                                <h5 class="font-semibold text-gray-800">
                                    You're currently timed in.
                                </h5>
                                <p class="text-gray-500 text-sm">
                                    Don't forget to record your time out before leaving.
                                </p>
                            </div>
                            <button
                                type="submit"
                                name="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-semibold shadow transition">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Time Out
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="font-semibold text-green-700">
                                    Attendance Completed
                                </h5>
                                <p class="text-gray-500 text-sm">
                                    Your attendance has been successfully recorded for today.
                                </p>
                            </div>
                            <button
                                class="bg-green-600 text-white px-8 py-3 rounded-xl font-semibold cursor-not-allowed"
                                disabled>
                                <i class="fas fa-check-circle mr-2"></i>
                                Completed
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Leave Balance -->
            <div class="max-w-5xl mx-auto mt-8">

                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-emerald-600 to-green-600 text-white px-8 py-6 flex justify-between items-center">

                        <div>
                            <h2 class="text-2xl font-bold">
                                <i class="fas fa-calendar-check mr-2"></i>
                                Leave Balance
                            </h2>
                            <p class="text-green-100 text-sm mt-1">
                                View your available leave credits
                            </p>
                        </div>

                        <a href="index.php?url=employee-leave-request"
                            class="bg-white text-green-700 hover:bg-gray-100 px-5 py-3 rounded-xl font-semibold shadow transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Request Leave
                        </a>

                    </div>

                    <div class="p-8">

                        <?php if (!empty($leave_balances)): ?>

                            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                                <?php foreach ($leave_balances as $balance): ?>

                                    <?php
                                    $leaveType = htmlspecialchars($balance['leave_type_name'] ?? 'None');
                                    $totalDays = $balance['total_days'] ?? 0;
                                    $usedDays = $balance['used_days'] ?? 0;
                                    $remainingDays = $balance['remaining_days'] ?? 0;
                                    $usedPercent = $totalDays > 0 ? ($usedDays / $totalDays) * 100 : 0;
                                    $remainingPercent = $totalDays > 0 ? ($remainingDays / $totalDays) * 100 : 0;
                                    ?>

                                    <div class="border rounded-2xl p-6 hover:shadow-lg transition duration-300">

                                        <!-- Leave Type -->
                                        <div class="flex justify-between items-center mb-5">

                                            <div>
                                                <h4 class="font-bold text-lg text-gray-800">
                                                    <?= $leaveType ?>
                                                </h4>

                                                <p class="text-sm text-gray-500">
                                                    Leave Credits
                                                </p>
                                            </div>

                                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                                <i class="fas fa-umbrella-beach text-green-600"></i>
                                            </div>

                                        </div>

                                        <!-- Stats -->
                                        <div class="grid grid-cols-3 gap-3 text-center mb-5">

                                            <div class="bg-gray-50 rounded-xl py-3">
                                                <div class="text-xl font-bold text-gray-800">
                                                    <?= $totalDays ?>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Total
                                                </div>
                                            </div>

                                            <div class="bg-red-50 rounded-xl py-3">
                                                <div class="text-xl font-bold text-red-600">
                                                    <?= $usedDays ?>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Used
                                                </div>
                                            </div>

                                            <div class="bg-green-50 rounded-xl py-3">
                                                <div class="text-xl font-bold text-green-600">
                                                    <?= $remainingDays ?>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Left
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Progress -->
                                        <div class="mb-3">

                                            <div class="flex justify-between text-sm mb-2">
                                                <span class="text-gray-600">
                                                    Usage
                                                </span>

                                                <span class="font-semibold">
                                                    <?= round($usedPercent) ?>%
                                                </span>
                                            </div>

                                            <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">

                                                <div
                                                    class="bg-green-600 h-3 rounded-full transition-all duration-500"
                                                    style="width: <?= $usedPercent ?>%;">
                                                </div>

                                            </div>

                                        </div>

                                        <!-- Footer -->
                                        <div class="flex justify-between text-sm mt-4">

                                            <span class="text-red-500">
                                                <?= round($usedPercent) ?>% Used
                                            </span>

                                            <span class="text-green-600 font-semibold">
                                                <?= round($remainingPercent) ?>% Available
                                            </span>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php else: ?>

                            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl py-12 text-center">

                                <div class="text-5xl text-gray-400 mb-3">
                                    <i class="fas fa-calendar-times"></i>
                                </div>

                                <h3 class="text-lg font-semibold text-gray-700">
                                    No Leave Balance
                                </h3>

                                <p class="text-gray-500 mt-2">
                                    No leave balance information is available for this year.
                                </p>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

            <!-- Recent Attendance -->
            <div class="max-w-5xl mx-auto mt-8 mb-10">

                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white px-8 py-6 flex justify-between items-center">

                        <div>
                            <h2 class="text-2xl font-bold">
                                <i class="fas fa-history mr-2"></i>
                                Recent Attendance
                            </h2>

                            <p class="text-cyan-100 text-sm mt-1">
                                Your latest attendance records
                            </p>
                        </div>

                        <div class="hidden md:block">
                            <i class="fas fa-calendar-alt text-4xl opacity-30"></i>
                        </div>

                    </div>

                    <div class="p-6">

                        <?php if (!empty($monthly_attendance)): ?>

                            <div class="space-y-4">

                                <?php foreach (array_slice($monthly_attendance, 0, 5) as $record): ?>

                                    <?php
                                    $status = $record['status'] ?? 'Unknown';

                                    switch ($status) {
                                        case 'ON_TIME':
                                            $badgeColor = 'bg-green-100 text-green-700';
                                            break;

                                        case 'LATE':
                                            $badgeColor = 'bg-yellow-100 text-yellow-700';
                                            break;

                                        case 'ABSENT':
                                            $badgeColor = 'bg-red-100 text-red-700';
                                            break;

                                        default:
                                            $badgeColor = 'bg-gray-100 text-gray-700';
                                    }
                                    ?>

                                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition">

                                        <div class="flex flex-col md:flex-row justify-between md:items-center gap-5">

                                            <!-- Left -->
                                            <div class="flex-1">

                                                <h4 class="text-lg font-bold text-gray-800">
                                                    <?= !empty($record['time_in'])
                                                        ? Helper::formatDate($record['time_in'])
                                                        : '-' ?>
                                                </h4>

                                                <div class="flex flex-wrap items-center gap-6 mt-3 text-gray-600">

                                                    <div class="flex items-center gap-2">
                                                        <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center">
                                                            <i class="fas fa-sign-in-alt text-green-600"></i>
                                                        </div>

                                                        <div>
                                                            <small class="block text-gray-500">
                                                                Time In
                                                            </small>

                                                            <span class="font-semibold">
                                                                <?= !empty($record['time_in'])
                                                                    ? Helper::formatTime($record['time_in'])
                                                                    : '--:--' ?>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-2">

                                                        <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center">
                                                            <i class="fas fa-sign-out-alt text-red-600"></i>
                                                        </div>

                                                        <div>
                                                            <small class="block text-gray-500">
                                                                Time Out
                                                            </small>

                                                            <span class="font-semibold">
                                                                <?= !empty($record['time_out'])
                                                                    ? Helper::formatTime($record['time_out'])
                                                                    : 'Not yet' ?>
                                                            </span>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="mt-4 text-sm text-gray-500">

                                                    <i class="fas fa-business-time text-blue-500 mr-1"></i>

                                                    Hours Worked:

                                                    <strong class="text-gray-700">
                                                        <?= number_format($record['total_hours_worked'] ?? 0, 2) ?> hrs
                                                    </strong>

                                                </div>

                                            </div>

                                            <!-- Status -->
                                            <div>

                                                <span class="<?= $badgeColor ?> px-5 py-2 rounded-full font-semibold text-sm">
                                                    <?= str_replace('_', ' ', $status) ?>
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php else: ?>

                            <div class="py-14 text-center">

                                <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">

                                    <i class="fas fa-calendar-times text-4xl text-gray-400"></i>

                                </div>

                                <h3 class="text-lg font-semibold text-gray-700">
                                    No Attendance Records
                                </h3>

                                <p class="text-gray-500 mt-2">
                                    There are no attendance records available for this month.
                                </p>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>