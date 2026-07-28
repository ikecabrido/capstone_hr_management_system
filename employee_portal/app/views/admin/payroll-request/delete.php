<div
    class="modal fade"
    id="deletePayrollRequestModal<?= (int) $request['id'] ?>"
    tabindex="-1"
    aria-labelledby="deletePayrollRequestModalLabel<?= (int) $request['id'] ?>"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header border-0">
                <h5
                    class="modal-title font-weight-bold"
                    id="deletePayrollRequestModalLabel<?= (int) $request['id'] ?>">

                    Delete Payroll Request

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>

            <div class="modal-body text-center px-4 pb-4">

                <div class="mb-3">
                    <i class="fas fa-trash-alt text-danger fa-3x"></i>
                </div>

                <h6 class="font-weight-bold mb-2">
                    Delete this request?
                </h6>

                <p class="text-muted mb-0">
                    Are you sure you want to delete the payroll request
                    submitted by
                    <strong>
                        <?= htmlspecialchars($request['full_name'] ?? 'this employee') ?>
                    </strong>?
                </p>

                <small class="text-muted d-block mt-2">
                    This action cannot be undone.
                </small>

            </div>

            <div class="modal-footer border-0 justify-content-center">

                <button
                    type="button"
                    class="btn btn-light border px-4"
                    data-bs-dismiss="modal">

                    Cancel

                </button>

                <form
                    action="index.php?url=admin-payroll-request-delete"
                    method="POST">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $request['id'] ?>">

                    <button
                        type="submit"
                        class="btn btn-danger px-4">

                        <i class="fas fa-trash-alt mr-1"></i>
                        Delete Request

                    </button>

                </form>

            </div>

        </div>
    </div>
</div>