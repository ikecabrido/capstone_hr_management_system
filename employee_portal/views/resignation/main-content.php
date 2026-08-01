<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">Resignation Requests</h3>
            <p class="text-muted mb-0">Submit a resignation request for HR review.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#resignationModal">
            <i class="fa-solid fa-plus me-1"></i> Submit Resignation
        </button>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Submitted</th>
                        <th>Type</th>
                        <th>Notice Date</th>
                        <th>Last Working Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($resignations)): ?>
                        <tr><td colspan="5" class="text-center text-muted">No resignation requests found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($resignations as $resignation): ?>
                            <tr>
                                <td><?= htmlspecialchars($resignation['created_at']); ?></td>
                                <td><?= htmlspecialchars(ucfirst($resignation['resignation_type'])); ?></td>
                                <td><?= htmlspecialchars($resignation['notice_date']); ?></td>
                                <td><?= htmlspecialchars($resignation['last_working_date']); ?></td>
                                <td><span class="badge bg-<?= $resignation['status'] === 'approved' ? 'success' : ($resignation['status'] === 'rejected' ? 'danger' : 'warning'); ?>"><?= htmlspecialchars(ucfirst($resignation['status'])); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="resignationModal" tabindex="-1" aria-labelledby="resignationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="resignationModalLabel">Submit Resignation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=resignation-create" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Resignation Type</label>
                            <select name="resignation_type" class="form-select" required>
                                <option value="voluntary">Voluntary</option>
                                <option value="involuntary">Involuntary</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Notice Date</label>
                            <input type="date" name="notice_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Working Date</label>
                            <input type="date" name="last_working_date" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason</label>
                            <textarea name="reason" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Comments <span class="text-muted">(optional)</span></label>
                            <textarea name="comments" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
