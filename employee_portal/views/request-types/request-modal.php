<?php foreach ($requestTypes as $type): ?>
    <div class="modal fade" id="requestModal<?= $type['id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="POST"
                    action="index.php?url=user-request-create"
                    enctype="multipart/form-data">

                    <div class="modal-header">
                        <h5 class="modal-title text-2xl">
                            <i class="fa-solid <?= $type['icon']; ?>"></i>
                            <?= htmlspecialchars($type['name']); ?>
                            Request
                        </h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden"
                            name="request_type_id"
                            value="<?= $type['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Title
                            </label>
                            <input type="text"
                                name="title"
                                class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Details
                            </label>
                            <textarea name="details"
                                class="form-control"
                                rows="4"
                                required></textarea>
                        </div>

                        <?php if ($type['requires_attachment']): ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Attachment <span class="text-danger">*</span>
                                </label>

                                <input type="file"
                                    name="attachment"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                    required>
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="modal-footer">
                        <button type="submit"
                            class="btn btn-primary">
                            Submit Request
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
<?php endforeach; ?>