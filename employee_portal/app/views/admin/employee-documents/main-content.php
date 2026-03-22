<div class="w-full ml-16">
    <div class="content-wrapper">
            <?php require __DIR__ . '/../../../views/partials/notif.php' ?>
            <div class="card p-3 shadow-sm rounded-4">
                <h5 class="fw-bold mb-3">Employee Documents</h5>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-[12px]">
                        <thead class="table-dark text-nowrap">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Submitted By</th>
                                <th>Department</th>
                                <th>Approver</th>
                                <th>Status</th>
                                <th>Attachment</th>
                                <th>Approved At</th>
                                <th>Decision</th>
                                <th>Remarks</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($empdocs)) : ?>
                                <?php foreach ($empdocs as $doc) : ?>
                                    <tr data-id="<?= htmlspecialchars($doc['approval_id']) ?>">
                                        <td><?= htmlspecialchars($doc['approval_id']) ?></td>
                                        <td><strong><?= htmlspecialchars($doc['title'] ?? 'N/A') ?></strong></td>
                                        <td><?= htmlspecialchars($doc['submitter_name'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
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
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($decision) ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($doc['file_path'])): ?>
                                                <a href="<?= $base . '/public/' . ltrim($doc['file_path'], '/') ?>"
                                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                                    View File
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">No file</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <?= !empty($doc['approved_at']) ? date('M d, Y', strtotime($doc['approved_at'])) : '-' ?>
                                        </td>
                                        <td class="text-center text-nowrap"><?= ucfirst($doc['decision'] ?? '-') ?></td>
                                        <td class="text-center text-nowrap"><?= htmlspecialchars($doc['remarks'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-success btn-sm btn-decision"
                                                    data-id="<?= $doc['approval_id'] ?>" data-action="approved">
                                                    Approve
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm btn-decision"
                                                    data-id="<?= $doc['approval_id'] ?>" data-action="rejected">
                                                    Reject
                                                </button>
                                                <form method="POST" action="employee-documents-delete" class="d-inline">
                                                    <input type="hidden" name="approval_id" value="<?= htmlspecialchars($doc['approval_id']) ?>">
                                                    <button type="submit" class="btn btn-secondary btn-sm">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted">No documents found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</div>