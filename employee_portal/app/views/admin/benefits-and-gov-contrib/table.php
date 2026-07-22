<div class="table-responsive">

    <table class="table table-hover table-bordered mb-0">

        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Employee ID</th>
                <th>Record Type</th>
                <th>Period</th>
                <th>Description</th>
                <th>File</th>
                <th>Uploaded By</th>
                <th>Uploaded At</th>
                <th width="150">Actions</th>
            </tr>
        </thead>

        <tbody>

            <?php if (!empty($benefitsAndGovContrib)): ?>

                <?php foreach ($benefitsAndGovContrib as $benefit): ?>

                    <tr>

                        <td><?= $benefit['benefit_id']; ?></td>

                        <td><?= htmlspecialchars($benefit['full_name']); ?></td>

                        <td>
                            <span class="badge badge-primary px-4 py-2">
                                <?= htmlspecialchars($benefit['record_type']); ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($benefit['period']); ?></td>

                        <td><?= htmlspecialchars($benefit['description']); ?></td>

                        <td>
                            <?php if (!empty($benefit['file_path'])): ?>
                                <a href="<?= htmlspecialchars($benefit['file_path']); ?>"
                                    target="_blank"
                                    class="btn btn-sm btn-info"> View
                                </a>
                                <a
                                    href="<?= htmlspecialchars($benefit['file_path']); ?>"
                                    download="<?= htmlspecialchars($benefit['file_name']); ?>"
                                    class="btn btn-success btn-sm">
                                    Download
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No File</span>
                            <?php endif; ?>
                        </td>

                        <td class="font-bold">Admin: <?= htmlspecialchars($benefit['uploaded_by']); ?></td>

                        <td>
                            <?= date('M d, Y | g:i A', strtotime($benefit['uploaded_at'])); ?>
                        </td>

                        <td class="align-middle text-center">

                            <button
                                class="btn btn-primary btn-xs px-2"
                                data-bs-toggle="modal"
                                data-bs-target="#viewBenefitModal"

                                data-employee="<?= htmlspecialchars($benefit['full_name']); ?>"
                                data-record="<?= htmlspecialchars($benefit['record_type']); ?>"
                                data-period="<?= htmlspecialchars($benefit['period']); ?>"
                                data-description="<?= htmlspecialchars($benefit['description']); ?>"
                                data-file="<?= htmlspecialchars($benefit['file_path']); ?>"
                                data-uploadedby="<?= htmlspecialchars($benefit['uploaded_by']); ?>"
                                data-uploadedat="<?= date('F d, Y • g:i A', strtotime($benefit['uploaded_at'])); ?>">

                                <i class="fas fa-eye fa-xs"></i>
                            </button>

                            <button
                                class="btn btn-warning btn-xs px-2"
                                data-bs-toggle="modal"
                                data-bs-target="#editBenefitModal"

                                data-id="<?= $benefit['benefit_id']; ?>"
                                data-employee="<?= htmlspecialchars($benefit['full_name']); ?>"
                                data-record="<?= htmlspecialchars($benefit['record_type']); ?>"
                                data-period="<?= htmlspecialchars($benefit['period']); ?>"
                                data-description="<?= htmlspecialchars($benefit['description']); ?>">

                                <i class="fas fa-edit fa-xs"></i>

                            </button>

                            <form
                                action="index.php?url=benefits-and-gov-contrib-delete"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this benefit record?');">

                                <input
                                    type="hidden"
                                    name="benefit_id"
                                    value="<?= $benefit['benefit_id']; ?>">

                                <button type="submit" class="btn btn-danger btn-xs px-2">
                                    <i class="fas fa-trash fa-xs"></i>
                                </button>

                            </form>

                        </td>

                    </tr>
                    <?php require __DIR__ . '/edit-modal.php'; ?>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="9" class="text-center py-4">
                        <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                        <br>
                        No benefits and government contribution records found.
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>