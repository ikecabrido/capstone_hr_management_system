<div class="w-full ml-2 mt-4">
    <div class="content-wrapper text-3xl">
        <div class="pt-10 pl-10">
            <h5 class="fw-bold mb-3 text-primary">Employee Documents</h5>

            <table class="table table-sm table-hover mb-0 border border-primary-subtle rounded-3 overflow-hidden">
                <?php require __DIR__ . '/../../../views/partials/notif.php' ?>

                <thead class="bg-primary color-blue-500 text-white text-nowrap small">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Submitted By</th>
                        <th>Department</th>
                        <th>Approver</th>
                        <th>Status</th>
                        <th>Attachment</th>
                        <th>Date Approved</th>
                        <th>Decision</th>
                        <th>Remarks</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody class="small">
                    <?php if (!empty($empdocs)) : ?>
                        <?php foreach ($empdocs as $doc) : ?>
                            <tr>

                                <td class="text-primary fw-semibold">
                                    <?= $doc['approval_id'] ?>
                                </td>

                                <td class="fw-semibold">
                                    <?= htmlspecialchars($doc['title'] ?? 'N/A') ?>
                                </td>

                                <td><?= htmlspecialchars($doc['submitter_name'] ?? '-') ?></td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        <?= htmlspecialchars($doc['department_name'] ?? '-') ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($doc['approver_name'] ?? '-') ?></td>

                                <td>
                                    <?php
                                    $decision = strtolower($doc['decision'] ?? 'pending');
                                    $badgeClass = match ($decision) {
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-warning text-dark'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?> small">
                                        <?= ucfirst($decision) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if (!empty($doc['file_path'])): ?>
                                        <a href="<?= $base . '/public/' . ltrim($doc['file_path'], '/') ?>"
                                            class="btn btn-outline-primary btn-sm py-0 px-2"
                                            target="_blank">
                                            View
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-nowrap text-muted">
                                    <?= !empty($doc['approved_at']) ? date('M d', strtotime($doc['approved_at'])) : '-' ?>
                                </td>
                                <td class="text-primary text-[12px] fw-semibold">
                                    <?= ucfirst($doc['decision'] ?? '-') ?>

                                    <?php if (empty($doc['decision'] !== 'Pending')): ?>
                                        <div class="d-flex gap-1 mt-1">
                                            <form method="POST" action="index.php?url=employee-documents-decision" class="d-inline">
                                                <input type="hidden" name="approval_id" value="<?= $doc['approval_id'] ?>">
                                                <input type="hidden" name="decision" value="Approved">
                                                <button type="submit" class="btn btn-outline-success btn-sm">✔</button>
                                            </form>

                                            <form method="POST" action="index.php?url=employee-documents-decision" class="d-inline">
                                                <input type="hidden" name="approval_id" value="<?= $doc['approval_id'] ?>">
                                                <input type="hidden" name="decision" value="Rejected">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">✖</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-truncate text-muted" style="max-width: 120px;">
                                    <?php if (empty($doc['remarks'])): ?>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#remarksModal<?= (int)$doc['approval_id'] ?>">
                                            Add
                                        </button>
                                    <?php else: ?>
                                        <?= htmlspecialchars($doc['remarks'], ENT_QUOTES) ?>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">


                                        <form method="POST" action="employee-documents-delete">
                                            <input type="hidden" name="approval_id" value="<?= $doc['approval_id'] ?>">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm">🗑</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php require __DIR__ . '/modal-remarks.php'; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted small">
                                No documents found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>