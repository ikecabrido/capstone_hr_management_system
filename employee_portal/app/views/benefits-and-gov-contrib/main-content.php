<div class="w-full ml-2 mt-4">
    <div class="content-wrapper text-3xl">
        <div class="pt-10 pl-10">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h1 class="fw-bold text-primary mb-1 display-5">
                        <i class="fas fa-hand-holding-heart me-2"></i>
                        Benefits & Government Contributions
                    </h1>

                    <p class="text-muted mb-0 fs-5">
                        Upload and manage employee benefits and government contribution records.
                    </p>

                </div>

                <button
                    class="btn btn-primary btn-lg"
                    data-bs-toggle="modal"
                    data-bs-target="#createBenefitModal">
                    <i class="fas fa-upload me-2"></i>
                    Upload Record

                </button>

            </div>

            <?php require __DIR__ . '/../../views/partials/notif.php'; ?>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th width="70">#</th>
                                <th>Record Type</th>
                                <th>Period</th>
                                <th>Description</th>
                                <th>Uploaded</th>
                                <th width="160" class="text-center">Attachment</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (!empty($benefits)): ?>

                                <?php foreach ($benefits as $index => $benefit): ?>

                                    <tr>

                                        <td><?= $index + 1; ?></td>

                                        <td>
                                            <span class="badge bg-primary px-3 py-2">
                                                <?= htmlspecialchars($benefit['record_type']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= !empty($benefit['period'])
                                                ? date('F Y', strtotime($benefit['period'] . '-01'))
                                                : '-'; ?>
                                            <small class="text-muted d-block">(Month &amp; Year)</small>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($benefit['description']); ?>
                                        </td>

                                        <td>
                                            <?= date('F d, Y', strtotime($benefit['uploaded_at'])); ?>
                                        </td>

                                        <td class="text-center flex">

                                            <?php if (!empty($benefit['file_path'])): ?>

                                                <a
                                                    href="<?= htmlspecialchars($benefit['file_path']); ?>"
                                                    target="_blank"
                                                    class="btn btn-primary btn-sm me-1">

                                                    <i class="fas fa-eye me-1"></i>

                                                </a>

                                                <a
                                                    href="<?= htmlspecialchars($benefit['file_path']); ?>"
                                                    download="<?= htmlspecialchars($benefit['file_name']); ?>"
                                                    class="btn btn-success btn-sm">

                                                    <i class="fas fa-download me-1"></i>

                                                </a>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    No File
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>

                                    <td colspan="6" class="text-center py-5 text-muted">

                                        <i class="fas fa-folder-open fa-3x mb-3"></i>

                                        <h5>No Records Found</h5>

                                        <p class="mb-0">
                                            There are currently no benefit records available.
                                        </p>

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
    <?php require __DIR__ . '/create-modal.php' ?>
</div>