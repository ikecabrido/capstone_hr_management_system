<?php $isEnrolled = $program['isEnrolled']; ?>

<div class="modal fade" id="<?= $modalId ?>" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 overflow-hidden">

      <div class="p-4 text-white" style="background: linear-gradient(135deg, #0d6efd, #4dabf7);">

        <h4 class="fw-bold mb-2">
          <i class="fa-solid fa-book-open me-2"></i>
          <?= htmlspecialchars($title) ?>
        </h4>

        <div class="d-flex justify-content-between align-items-center">

          <small>
            <i class="fa-solid fa-user-tie me-1"></i>
            <?= htmlspecialchars($trainer) ?>
          </small>

          <span class="badge bg-light text-dark rounded-pill px-3 py-2">
            <?= ucfirst($status) ?>
          </span>

        </div>

      </div>

      <div class="modal-body p-4">

        <div class="mb-4">
          <h6 class="fw-bold mb-2">
            <i class="fa-solid fa-circle-info me-1 text-primary"></i>
            About this course
          </h6>
          <p class="text-muted mb-0">
            <?= htmlspecialchars($desc) ?>
          </p>
        </div>

        <div class="row g-3">

          <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 h-100">
              <small class="text-muted">
                <i class="fa-solid fa-calendar-days me-1"></i> Schedule
              </small>
              <div class="fw-semibold">
                <?= $start ?> → <?= $end ?>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 h-100">
              <small class="text-muted">
                <i class="fa-solid fa-users me-1"></i> Capacity
              </small>
              <div class="fw-semibold">
                <?= $max ?> participants
              </div>
            </div>
          </div>

        </div>

      </div>

      <div class="modal-footer border-0 px-4 pb-4">

        <button class="btn btn-light px-4" data-bs-dismiss="modal">
          <i class="fa-solid fa-xmark me-1"></i> Close
        </button>

        <form method="post"
          action="index.php?url=<?= $isEnrolled ? 'training-program-unenroll' : 'training-program-enroll' ?>">

          <input type="hidden" name="id" value="<?= $id ?>">

          <button class="btn <?= $isEnrolled ? 'btn-outline-warning' : 'btn-primary' ?>">
            <?= $isEnrolled ? 'Unenroll' : 'Enroll Now' ?>
          </button>
        </form>

      </div>

    </div>
  </div>
</div>