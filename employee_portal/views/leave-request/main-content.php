<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 p-3 bg-white shadow-sm rounded">
        <div class="mb-3 mb-md-0">
            <h2 class="text-center fw-bold display-4">Leave Request Management</h2>
        </div>
    </div>

    <?php require __DIR__ . '/../partials/notif.php'; ?>

    <div class="flex justify-end w-full">
        <button class="btn btn-primary rounded-md px-4 fw-semibold shadow-sm"
            data-bs-toggle="modal"
            data-bs-target="#createLeaveRequestModal">
            ➕ Create
        </button>
        <a href="index.php?url=dashboard" class="ml-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
            Back
        </a>
    </div>
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5">
        <div class="card-body">
            <div class="table-responsive">
                <?php require __DIR__ . '/table-leave-request.php'; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/create-leave-request-modal.php'; ?>