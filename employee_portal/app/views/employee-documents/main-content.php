<div class="w-full ml-16">
    <div class="content-wrapper w-full">
        <div class="card shadow-sm border-0 rounded-4 w-full">
            <?php require __DIR__ . '/../partials/notif.php' ?>
            <div class="card-header bg-primary text-white rounded-top-4">
                <h5 class="mb-0 text-4xl">Submit Employee Document</h5>
            </div>
            <div class="card-body p-4">
                <form id="employee-documents-upload-form" enctype="multipart/form-data" method="POST" action="employee-documents-create">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" class="form-control rounded-3" name="title" placeholder="Enter document title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control rounded-3" name="description" rows="3" placeholder="Enter description..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Department</label>
                        <select class="form-select rounded-3" name="department" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept) : ?>
                                <option value="<?= $dept['id'] ?>">
                                    <?= htmlspecialchars($dept['department_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Submitted By</label>
                        <select class="form-select rounded-3" name="submit_by">
                            <option value="">-- None --</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['employee_id'] ?>" <?= ($_SESSION['employee_id'] ?? '') == $emp['employee_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($emp['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Attachment</label>
                        <input type="file" class="form-control rounded-3" name="attachment">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-3">
                            Submit
                        </button>
                        <a href="index.php?url=employee-documents-index" class="btn btn-outline-secondary w-100 rounded-3">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>