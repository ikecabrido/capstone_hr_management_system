<div class="w-full ml-4">
  <div class="content-wrapper w-full">
    <div class="card shadow-lg border-0 rounded-4 w-full">
      <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <h1 class="font-bold text-gray-800 p-2 text-6xl">Training Programs</h1>
        <?php ?>

        <div class="container">
          <div class="training-toolbar d-flex justify-content-between align-items-center">

            <div>
              <p class="text-muted small">
                Enhance your skills with our professional development programs
              </p>
            </div>
          </div>

          <?php require __DIR__ . '/../../partials/notif.php' ?>

          <?php require __DIR__ . '/search-and-filter.php' ?>


          <div class="container py-2">
            <div id="program-container">
              <?php include __DIR__ . '/my-training.php'; ?>
              <?php include __DIR__ . '/programs.php'; ?>
              <?php include __DIR__ . '/pagination.php'; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>