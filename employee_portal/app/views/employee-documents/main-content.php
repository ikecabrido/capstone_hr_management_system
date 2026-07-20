<div class="w-full ml-16">
    <div class="content-wrapper w-full">
        <?php require __DIR__ . '/../partials/notif.php'; ?>

        <div class="card shadow-sm border-0 rounded-4">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <div>
                    <h1 class="text-[50px] fw-bold text-primary mb-1">
                        <i class="fas fa-folder-open me-2"></i>
                        Employee Documents
                    </h1>

                    <p class="text-muted mb-0">
                        View and manage your submitted document requests.
                    </p>
                </div>

                <button
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#createDocumentModal">

                    <i class="fas fa-upload me-2"></i>
                    Submit Document

                </button>

            </div>

            <div class="card-body">

                <table class="table table-striped table-hover align-middle mb-0">
                    <?php require __DIR__ . '/../../views/partials/notif.php'; ?>

                    <thead class="table-primary">
                        <tr>
                            <th width="60">#</th>
                            <th>Document</th>
                            <th>Department</th>
                            <th>Approver</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Attachment</th>
                            <th>Date Submitted</th>
                            <th>Date Approved</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (!empty($empdocs)): ?>

                            <?php foreach ($empdocs as $doc): ?>

                                <?php
                                $decision = strtolower($doc['decision'] ?? 'pending');

                                $badgeClass = match ($decision) {
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-warning text-dark'
                                };
                                ?>

                                <tr>

                                    <td class="fw-bold text-primary">
                                        #<?= $doc['approval_id']; ?>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($doc['title']); ?>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-info text-dark px-3 py-2">
                                            <?= htmlspecialchars($doc['department_name'] ?? '-'); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($doc['approver_name'] ?? 'Pending'); ?>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge px-3 py-2 <?= $badgeClass ?>">
                                            <?= ucfirst($decision); ?>
                                        </span>
                                    </td>

                                    <td class="text-center">

                                        <?php if (!empty($doc['file_path'])): ?>

                                            <a href="<?= $base . '/employee_portal/public/' . ltrim($doc['file_path'], '/') ?>"
                                                target="_blank"
                                                class="btn btn-primary btn-sm me-1">

                                                <i class="fas fa-eye"></i>

                                            </a>

                                            <a href="<?= $base . '/employee_portal/public/' . ltrim($doc['file_path'], '/') ?>"
                                                download
                                                class="btn btn-success btn-sm">

                                                <i class="fas fa-download"></i>

                                            </a>

                                        <?php else: ?>

                                            <span class="text-muted">No File</span>

                                        <?php endif; ?>

                                    </td>

                                    <td>
                                        <?= !empty($doc['created_at'])
                                            ? date('M d, Y', strtotime($doc['created_at']))
                                            : '-'; ?>
                                    </td>

                                    <td>
                                        <?= !empty($doc['approved_at'])
                                            ? date('M d, Y', strtotime($doc['approved_at']))
                                            : '-'; ?>
                                    </td>

                                    <td style="max-width:220px;">
                                        <?= !empty($doc['remarks'])
                                            ? htmlspecialchars($doc['remarks'])
                                            : '<span class="text-muted">No remarks</span>'; ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="11" class="text-center text-muted py-5">

                                    <i class="fas fa-folder-open fa-2x mb-2"></i>

                                    <br>

                                    No employee document requests found.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>
            </div>
            <?php require __DIR__ . '/create-modal.php'; ?>
        </div>
    </div>
</div>