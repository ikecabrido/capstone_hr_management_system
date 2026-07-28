<div
    class="modal fade"
    id="statusPayrollRequestModal<?= (int) $request['id'] ?>"
    tabindex="-1"
    aria-labelledby="statusPayrollRequestModalLabel<?= (int) $request['id'] ?>"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header">
                <div>
                    <h5
                        class="modal-title font-weight-bold mb-1"
                        id="statusPayrollRequestModalLabel<?= (int) $request['id'] ?>">
                        Update Status
                    </h5>

                    <small class="text-muted">
                        Change the status of this payroll request.
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>

            <form
                action="index.php?url=admin-payroll-request-update-status"
                method="POST">

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $request['id'] ?>">

                    <div class="form-group mb-0">

                        <label
                            for="status<?= (int) $request['id'] ?>"
                            class="font-weight-bold">
                            Status
                        </label>

                        <select
                            name="status"
                            id="status<?= (int) $request['id'] ?>"
                            class="form-control"
                            required>

                            <?php
                            $statuses = [
                                'Pending',
                                'Processing',
                                'Approved',
                                'Rejected',
                                'Completed',
                                'Cancelled'
                            ];
                            ?>

                            <?php foreach ($statuses as $status): ?>
                                <option
                                    value="<?= $status ?>"
                                    <?= ($request['status'] ?? 'Pending') === $status ? 'selected' : '' ?>>
                                    <?= $status ?>
                                </option>
                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light border"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Update Status
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>