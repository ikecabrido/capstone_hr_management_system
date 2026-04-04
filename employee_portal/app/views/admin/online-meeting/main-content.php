<div class="w-full ml-32 mt-4">
    <div class="content-wrapper w-full">

        <div class="justify-content-between mb-2 ">
            <h1 class="fw-bold mb-0 text-5xl p-4">Online Meeting</h1>
        </div>
        <?php require __DIR__ . '/../../../views/partials/notif.php' ?>
        <div class="flex justify-end">
            <button class="btn btn-success shadow-sm px-3"
                data-bs-toggle="modal"
                data-bs-target="#createMeetingModal">
                ➕ Create Meeting
            </button>
        </div>
        <?php if (!empty($meetings)): ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <div>
                    <i class="fas fa-calendar-alt text-success" style="font-size: 60px;"></i>
                </div>


                <h5 class="fw-semibold text-3xl">
                    <div class="card border-0 shadow-sm rounded-4 mr-2"></div>Meeting List
                </h5>

                <div class="table-responsive">
                    <?php require __DIR__ . '/table-online-meeting.php'; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">

                <h5 class="fw-semibold mb-2">No meetings yet</h5>
                <p class="text-muted mb-3">Create your first online meeting to get started.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require __DIR__ . '/modal-online-meeting.php'; ?>