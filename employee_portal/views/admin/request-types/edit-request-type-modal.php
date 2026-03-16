<div class="modal fade edit-modal" id="editRequestTypeModal<?= $type['id']; ?>" 
     data-type-id="<?= $type['id']; ?>" tabindex="-1" aria-hidden="true">


    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">

            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fa-solid fa-pen-to-square me-2"></i>
                    Edit Request Type
                </h5>
                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <form method="POST"
                action="index.php?url=admin-request-types-update">

                <div class="modal-body">

                    <input type="hidden"
                        name="id"
                        value="<?= $type['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Request Name
                        </label>
                        <input type="text"
                            name="name"
                            class="form-control"
                            value="<?= htmlspecialchars($type['name']) ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Description
                        </label>
                        <textarea name="description"
                            class="form-control"
                            rows="3"><?= htmlspecialchars($type['description']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Select Icon
                        </label>

                        <input type="hidden" name="icon" id="selectedIcon<?= $type['id']; ?>" value="<?= htmlspecialchars($type['icon']); ?>">

                        <div class="mb-2">
                            Selected:
    <i id="iconPreview<?= $type['id']; ?>" class="fa-solid <?= htmlspecialchars($type['icon']); ?> fa-lg"></i>
                        </div>

                        <div class="border rounded p-3"
                            style="max-height:200px; overflow-y:auto;">

                            <div class="d-flex flex-wrap gap-3">

                                <?php
                                $editId = $type['id'];
                                require __DIR__ . '/icons.php';
                                ?>

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
                            <?= $type['requires_attachment'] ? 'checked' : '' ?>>
                        <label class="form-check-label">
                            Requires Attachment
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input"
                            type="checkbox"
                            name="is_active"
                            value="1"
                            <?= $type['is_active'] ? 'checked' : '' ?>>
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
                        class="btn btn-warning rounded-pill px-4 fw-semibold">
                        Update Request Type
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>