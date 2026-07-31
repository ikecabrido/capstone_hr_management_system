<div class="content-wrapper px-4 py-3">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

        <div>
            <h1 class="text-4xl font-bold mb-1">
                Training Requests
            </h1>

            <p class="text-muted mb-0">
                View and manage employee training requests.
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary shadow-sm mt-3 mt-md-0"
            data-bs-toggle="modal"
            data-bs-target="#trainingRequestModal">

            <i class="fas fa-plus mr-1"></i>
            New Training Request

        </button>

    </div>

    <?php require __DIR__ . '/../../../views/partials/notif.php'; ?>

    <!-- Training Requests Table -->
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-graduation-cap mr-2 text-primary"></i>
                Training Requests
            </h5>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead class="thead-light">

                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Requested Program</th>
                            <th>Requested Course</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Requested At</th>
                            <th width="120">Actions</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($trainingRequests)): ?>

                            <?php foreach ($trainingRequests as $request): ?>

                                <?php
                                switch ($request['request_status']) {
                                    case 'Approved':
                                        $badge = 'success';
                                        break;

                                    case 'Received':
                                        $badge = 'primary';
                                        break;

                                    case 'Rejected':
                                        $badge = 'danger';
                                        break;

                                    default:
                                        $badge = 'warning';
                                }
                                ?>

                                <tr>

                                    <td><?= $request['ld_request_id']; ?></td>

                                    <td>
                                        <?= htmlspecialchars($request['first_name'] ?? '-') ?> <?= htmlspecialchars($request['last_name'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($request['requested_program']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($request['requested_course']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($request['request_reason']) ?>
                                    </td>
                                    <td class="text-center">
                                        <button
                                            class="btn btn-sm badge badge-<?= $badge; ?> border-0"
                                            data-bs-toggle="modal"
                                            data-bs-target="#updateStatusModal"
                                            data-id="<?= $request['ld_request_id']; ?>"
                                            data-status="<?= $request['request_status']; ?>">
                                            <?= htmlspecialchars($request['request_status']) ?>
                                        </button>
                                    </td>

                                    <td>
                                        <?= date('M d, Y', strtotime($request['created_at'])) ?>
                                    </td>

                                    <td>

                                        <button
                                            class="btn btn-info btn-sm"
                                            title="View"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewTrainingModal<?= $request['ld_request_id']; ?>">

                                            <i class="fas fa-eye"></i>

                                        </button>

                                        <button
                                            class="btn btn-warning btn-sm"
                                            title="Edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editTrainingModal<?= $request['ld_request_id']; ?>">

                                            <i class="fas fa-edit"></i>

                                        </button>

                                        <button
                                            class="btn btn-danger btn-sm"
                                            title="Delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteTrainingModal<?= $request['ld_request_id']; ?>">

                                            <i class="fas fa-trash"></i>

                                        </button>

                                    </td>

                                </tr>
                                <?php require __DIR__ . '/view-modal.php'; ?>
                                <?php require __DIR__ . '/edit-modal.php'; ?>
                                <?php require __DIR__ . '/delete-modal.php'; ?>
                                <?php require __DIR__ . '/status.php'; ?>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="8" class="text-center py-4 text-muted">

                                    No training requests found.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php require __DIR__ . '/create-modal.php'; ?>
<script>
    const statusModal = document.getElementById('updateStatusModal');

    statusModal.addEventListener('show.bs.modal', function(event) {

        const button = event.relatedTarget;

        document.getElementById('status_request_id').value =
            button.getAttribute('data-id');

        document.getElementById('status_request').value =
            button.getAttribute('data-status');
    });
</script>