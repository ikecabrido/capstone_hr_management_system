<div class="w-full ml-8">
  <div class="content-wrapper w-full">
    <div class="card shadow-lg border-0 rounded-4 w-full">
      <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <h1 class="font-bold text-gray-800 p-4 text-6xl">Training Programs</h1>
        <?php ?>

        <div class="container" style="margin-bottom: 40px;">
          <div class="training-toolbar d-flex justify-content-between align-items-center mb-4">

            <div>
              <p class="text-muted small mb-0">
                Enhance your skills with our professional development programs
              </p>
            </div>

            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createProgramModal">
              Create Training Program
            </button>
          </div>

          <?php require __DIR__ . '/../../../partials/notif.php' ?>

          <?php require __DIR__ . '/search-and-filter.php' ?>


          <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div>
                <h3 class="fw-bold mb-0">Training Programs</h3>
                <small class="text-muted">Manage and explore all available programs</small>
              </div>
            </div>
            <?php require __DIR__ . '/programs.php' ?>
            <?php require __DIR__ . '/paginate.php' ?>
            <?php require __DIR__ . '/view-modal.php' ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . "/action.php" ?>
<?php require __DIR__ . "/modal-create.php" ?>
<?php require __DIR__ . "/modal-edit.php" ?>
<?php require __DIR__ . "/modal-delete.php" ?>

<?php require __DIR__ . "/../image_upload.php" ?>
<?php require __DIR__ . "/../toast.php" ?>