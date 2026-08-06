<div class="w-full ml-2 mt-4">
    <div class="content-wrapper">
        <?php require __DIR__ . '/../partials/notif.php' ?>
        <!-- Header -->
        <div class="flex justify-between items-center px-10 pt-10">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    Employee Complaints
                </h1>
                <p class="text-gray-500 mt-1">
                    Submit concerns, track complaint status, and communicate with Human Resources.
                </p>
            </div>
            <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl shadow transition"
                data-bs-toggle="modal"
                data-bs-target="#createComplaintModal">
                <i class="fas fa-plus mr-2"></i>
                Submit Complaint
            </button>
        </div>
        <?php require __DIR__ . '/statistic.php'; ?>
        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 px-10 mt-8">

            <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-blue-500">
                <p class="text-gray-500 text-sm">Total Complaints</p>
                <h2 class="text-3xl font-bold mt-2">
                    <?= $totalComplaints ?>
                </h2>
            </div>

            <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-yellow-500">
                <p class="text-gray-500 text-sm">Pending</p>
                <h2 class="text-3xl font-bold mt-2 text-yellow-600">
                    <?= $pendingCount ?>
                </h2>
            </div>

            <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-green-500">
                <p class="text-gray-500 text-sm">Resolved</p>
                <h2 class="text-3xl font-bold mt-2 text-green-600">
                    <?= $resolvedCount ?>
                </h2>
            </div>

            <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-red-500">
                <p class="text-gray-500 text-sm">Rejected</p>
                <h2 class="text-3xl font-bold mt-2 text-red-600">
                    <?= $rejectedCount ?>
                </h2>
            </div>

        </div>

        <!-- Complaint History -->
        <div class="bg-white mx-10 mt-8 rounded-2xl shadow">

            <!-- Top Bar -->
            <div class="flex justify-between items-center p-6 border-b">

                <h2 class="text-xl font-semibold">
                    Complaint History
                </h2>

                <div class="flex gap-3">

                    <input
                        type="text"
                        placeholder="Search..."
                        class="border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 outline-none">

                    <select class="border rounded-lg px-4 py-2">
                        <option>All Status</option>
                        <option>Pending</option>
                        <option>Under Review</option>
                        <option>Resolved</option>
                        <option>Rejected</option>
                    </select>

                </div>

            </div>

            <!-- Table -->
            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-100">

                        <tr class="text-left text-gray-600 text-sm uppercase">

                            <th class="px-6 py-4">Reference No.</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4">Subject</th>
                            <th class="px-6 py-4">Date Submitted</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Action</th>

                        </tr>

                    </thead>

                    <tbody class="divide-y">

                        <?php if (!empty($complaints)): ?>

                            <?php foreach ($complaints as $complaint): ?>

                                <?php

                                switch ($complaint['status']) {

                                    case 'submitted':
                                        $badge = 'bg-yellow-100 text-yellow-700';
                                        $status = 'Submitted';
                                        break;

                                    case 'under_review':
                                        $badge = 'bg-blue-100 text-blue-700';
                                        $status = 'Under Review';
                                        break;

                                    case 'investigation':
                                        $badge = 'bg-purple-100 text-purple-700';
                                        $status = 'Investigation';
                                        break;

                                    case 'resolved':
                                        $badge = 'bg-green-100 text-green-700';
                                        $status = 'Resolved';
                                        break;

                                    case 'rejected':
                                        $badge = 'bg-red-100 text-red-700';
                                        $status = 'Rejected';
                                        break;

                                    default:
                                        $badge = 'bg-gray-100 text-gray-700';
                                        $status = ucfirst($complaint['status']);
                                }

                                ?>

                                <tr class="hover:bg-gray-50">

                                    <td class="px-6 py-4 font-medium">
                                        <?= htmlspecialchars($complaint['incident_id']) ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <?= ucwords(str_replace('_', ' ', htmlspecialchars($complaint['type']))) ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <?= htmlspecialchars($complaint['title']) ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <?= date('M d, Y', strtotime($complaint['created_at'])) ?>
                                    </td>

                                    <td class="px-6 py-4">

                                        <span class="<?= $badge ?> px-3 py-1 rounded-full text-xs font-semibold">

                                            <?= $status ?>

                                        </span>

                                    </td>

                                    <td class="px-6 py-4 text-center">

                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewComplaint<?= $complaint['id'] ?>">

                                            <i class="fas fa-eye"></i>

                                        </button>

                                    </td>
                                    <?php require __DIR__ . '/view.php'; ?>
                                </tr>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="6" class="text-center py-5 text-gray-500">

                                    No complaints submitted yet.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
</div>
<?php require __DIR__ . '/create.php'; ?>