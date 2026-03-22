<div class="modal fade" id="createRequestTypeModal" tabindex="-1" aria-labelledby="createRequestTypeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createRequestTypeLabel">
                    <i class="fa-solid fa-plus me-2"></i>
                    Create Request Type
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="index.php?url=admin-request-types-create" method="POST">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Request Name</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               placeholder="e.g. Leave Request"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Short description..."></textarea>
                    </div>
    
                                                <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Select Icon
                                </label>

                                <!-- Hidden input (saved to database) -->
                                <input type="hidden" name="icon" id="selectedIcon">

                                <!-- Icon Preview -->
                                <div class="mb-2">
                                    Selected:
                                    <i id="iconPreview" class="fa-solid fa-question fa-lg"></i>
                                </div>

                                <!-- Icon Picker -->
                                <div class="border rounded p-3"
                                    style="max-height:200px; overflow-y:auto;">

                                    <div class="d-flex flex-wrap gap-3">

                                        <?php require __DIR__ . '/icons.php'; ?>

                                    </div>
                                </div>

                                <small class="text-muted">
                                    Click an icon to select
                                </small>
                            </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input"
                               type="checkbox"
                               name="requires_attachment"
                               value="1"
                               id="requiresAttachment">
                        <label class="form-check-label">
                            Requires Attachment
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input"
                               type="checkbox"
                               name="is_active"
                               value="1"
                               id="isActive"
                               checked>
                        <label class="form-check-label">
                            Active Request Type
                        </label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary rounded-pill px-4 fw-semibold">
                        Save Request Type
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>