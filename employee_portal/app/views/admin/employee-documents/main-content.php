<div class="w-full mt-4">
    <div class="content-wrapper">
        <div class="pt-5 pl-4">
            <h5 class="fw-bold mb-3 text-5xl">Employee Documents</h5>

            <table class="table table-striped table-hover align-middle mb-0">
                <?php require __DIR__ . '/../../../views/partials/notif.php'; ?>

                <thead class="table-primary text-nowrap">
                    <tr>
                        <th width="60">#</th>
                        <th>Title</th>
                        <th>Submitted By</th>
                        <th>Department</th>
                        <th>Approver</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Attachment</th>
                        <th>Date Approved</th>
                        <th class="text-center">Decision</th>
                        <th>Remarks</th>
                        <th class="text-center">Action</th>
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

                                <td class="fw-semibold">
                                    <?= htmlspecialchars($doc['title'] ?? 'N/A'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($doc['submitter_name'] ?? '-'); ?>
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        <?= htmlspecialchars($doc['department_name'] ?? '-'); ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($doc['approver_name'] ?? '-'); ?>
                                </td>

                                <td class="text-center">
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= ucfirst($decision); ?>
                                    </span>
                                </td>

                                <td class="text-center">

                                    <?php if (!empty($doc['file_path'])): ?>

                                        <a href="<?= $base . '/employee_portal/public/' . ltrim($doc['file_path'], '/') ?>"
                                            target="_blank"
                                            class="btn btn-sm btn-outline-primary">

                                            <i class="fas fa-eye"></i> View

                                        </a>

                                    <?php else: ?>

                                        <span class="text-muted">No File</span>

                                    <?php endif; ?>

                                </td>

                                <td class="text-nowrap">

                                    <?= !empty($doc['approved_at'])
                                        ? date('M d, Y', strtotime($doc['approved_at']))
                                        : '-'; ?>

                                </td>

                                <td class="text-center">

                                    <?php if ($decision == 'pending'): ?>

                                        <div class="btn-group btn-group-sm">

                                            <form method="POST"
                                                action="index.php?url=employee-documents-decision">

                                                <input type="hidden"
                                                    name="approval_id"
                                                    value="<?= $doc['approval_id']; ?>">

                                                <input type="hidden"
                                                    name="decision"
                                                    value="Approved">

                                                <button class="btn btn-sm mr-1 btn-success">
                                                    <i class="fas fa-check"></i>
                                                </button>

                                            </form>

                                            <form method="POST"
                                                action="index.php?url=employee-documents-decision">

                                                <input type="hidden"
                                                    name="approval_id"
                                                    value="<?= $doc['approval_id']; ?>">

                                                <input type="hidden"
                                                    name="decision"
                                                    value="Rejected">

                                                <button class="btn btn-sm btn-danger">
                                                    <i class="fas fa-times"></i>
                                                </button>

                                            </form>

                                        </div>

                                    <?php else: ?>

                                        <span class="text-success fw-semibold">
                                            Completed
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td style="max-width:220px;">

                                    <?php if (empty($doc['remarks'])): ?>

                                        <button class="btn btn-outline-secondary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#remarksModal<?= $doc['approval_id']; ?>">

                                            <i class="fas text-md fa-comment"></i>
                                            Add Remarks

                                        </button>

                                    <?php else: ?>

                                        <span class="text-muted">
                                            <?= htmlspecialchars($doc['remarks']); ?>
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td class="text-center">

                                    <form method="POST"
                                        action="index.php?url=employee-documents-delete">

                                        <input type="hidden"
                                            name="approval_id"
                                            value="<?= $doc['approval_id']; ?>">

                                        <button class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Delete this document?')">

                                            <i class="fas text-md fa-trash"></i>

                                        </button>

                                    </form>

                                </td>

                            </tr>

                            <?php require __DIR__ . '/modal-remarks.php'; ?>

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
    </div>
</div>