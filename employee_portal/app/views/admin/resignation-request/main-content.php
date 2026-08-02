<div class="w-auto ml-1 mt-4">
    <div class="content-wrapper">
        <div class="px-4 py-8">
            <?php require __DIR__ . '/../../partials/notif.php'; ?>
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800">
                        Resignation Requests
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage employee resignation requests.
                    </p>
                </div>

                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#createResignationModal">
                    <i class="fa-solid fa-plus me-2"></i>
                    Submit Resignation
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 border-b">
                            <tr class="text-gray-600 uppercase text-xs">

                                <th class="px-6 py-4 text-left">
                                    Employee Code
                                </th>

                                <th class="px-6 py-4 text-left">
                                    Employee
                                </th>

                                <th class="px-6 py-4 text-left">
                                    Type
                                </th>

                                <th class="px-6 py-4 text-left">
                                    Last Working Day
                                </th>

                                <th class="px-6 py-4 text-center">
                                    Status
                                </th>

                                <th class="px-6 py-4 text-center">
                                    Action
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">

                            <?php if (!empty($resignationRequests)): ?>

                                <?php foreach ($resignationRequests as $request): ?>

                                    <?php
                                    $badge = match ($request['status']) {
                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                        'Approved' => 'bg-green-100 text-green-800',
                                        'Rejected' => 'bg-red-100 text-red-800',
                                        'Cancelled' => 'bg-gray-100 text-gray-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                    ?>

                                    <tr class="hover:bg-gray-50 transition">

                                        <td class="px-6 py-4 font-medium text-gray-700">
                                            <?= htmlspecialchars($request['employee_code']) ?>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-800">
                                                <?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <?= htmlspecialchars($request['resignation_type']) ?>
                                        </td>

                                        <td class="px-6 py-4">
                                            <?= date('M d, Y', strtotime($request['intended_last_working_day'])) ?>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <?php
                                            $statuses = ['Pending', 'Approved', 'Rejected', 'Cancelled'];

                                            $btnClass = match ($request['status']) {
                                                'Pending'   => 'btn-warning',
                                                'Approved'  => 'btn-success',
                                                'Rejected'  => 'btn-danger',
                                                'Cancelled' => 'btn-secondary',
                                                default     => 'btn-light'
                                            };
                                            ?>

                                            <div class="btn-group">
                                                <button
                                                    type="button"
                                                    class="btn <?= $btnClass ?> btn-sm dropdown-toggle"
                                                    onclick="this.nextElementSibling.classList.toggle('show')">
                                                    <?= htmlspecialchars($request['status']) ?>
                                                </button>

                                                <ul class="dropdown-menu">
                                                    <?php foreach ($statuses as $status): ?>
                                                        <li>
                                                            <form action="index.php?url=admin-resignation-update-status" method="POST" class="m-0 form-label">
                                                                <input type="hidden" name="resignation_id" value="<?= $request['resignation_id'] ?>">
                                                                <input type="hidden" name="status" value="<?= $status ?>">

                                                                <button type="submit" class="dropdown-item">
                                                                    <?= $status ?>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </td>

                                        <td>
                                            <button
                                                type="button"
                                                class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewModal<?= $request['resignation_id']; ?>">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="px-3 py-1.5 rounded-lg bg-yellow-400 hover:bg-yellow-500 text-white text-xs font-medium"
                                                data-bs-toggle="modal"
                                                data-bs-target="#statusModal<?= $request['resignation_id']; ?>">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </td>

                                    </tr>
                                    <?php require __DIR__ . '/remarks.php'; ?>
                                    <?php require __DIR__ . '/view.php'; ?>
                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>

                                    <td colspan="8" class="py-10 text-center text-gray-500">
                                        No resignation requests found.
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
</div>