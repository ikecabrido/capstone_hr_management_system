<div class="modal fade" id="createDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <form
                id="employee-documents-upload-form"
                method="POST"
                enctype="multipart/form-data"
                action="employee-documents-create">

                <div class="modal-header bg-primary text-white">

                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-upload me-2"></i>
                        Submit Employee Document
                    </h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Title
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="title"
                                placeholder="Enter document title"
                                required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Description
                            </label>

                            <textarea
                                class="form-control"
                                name="description"
                                rows="3"
                                placeholder="Enter description..."
                                required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Department
                            </label>

                            <select
                                class="form-select"
                                name="department"
                                required>

                                <option value="">Select Department</option>

                                <?php foreach ($departments as $dept): ?>

                                    <option value="<?= $dept['id']; ?>">
                                        <?= htmlspecialchars($dept['department_name']); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Submitted By
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($_SESSION['full_name']); ?>"
                                readonly>

                            <input
                                type="hidden"
                                name="submit_by"
                                value="<?= $_SESSION['user_id']; ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Attachment
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                name="attachment"
                                required>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-paper-plane me-1"></i>
                        Submit

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>