<div class="w-full ml-8">
  <div class="content-wrapper w-full">
    <div class="card shadow-lg border-0 rounded-4 w-full">
      <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <h1 class="font-bold text-gray-800 p-10 text-4xl">Training Programs</h1>




        <?php ?>

        <div class="container" style="margin-top:90px; margin-bottom: 40px;">
          <div class="training-toolbar d-flex justify-content-between align-items-center mb-4">

            <div>
              <h2 class="m-0">Training Programs</h2>
              <p class="text-muted small mt-2 mb-0">
                Enhance your skills with our professional development programs
              </p>

              <?php if (!empty($role)): ?>
                <p class="text-muted small mb-0">
                  Your role: <strong><?php echo htmlspecialchars($role); ?></strong>
                  <?php echo $isAuthorized ? '(management access)' : ''; ?>
                </p>
              <?php endif; ?>
            </div>

            <?php if ($isAuthorized): ?>
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createProgramModal">
                Create Training Program
              </button>
            <?php endif; ?>
          </div>


          <?php $message = $message ?? null; ?>
          <?php $messageType = $messageType ?? 'info'; ?>

          <?php if ($message): ?>
            <script>
              document.addEventListener('DOMContentLoaded', function() {
                showToast(
                  <?= json_encode($message) ?>,
                  <?= json_encode($messageType) ?>,
                  4000
                );
              });
            </script>
          <?php endif; ?>

          <!-- Search & Filter Bar -->
          <div class="card mb-4">
            <div class="card-body">
              <form method="GET" class="row g-3" style="display: flex; gap: 1rem; align-items: flex-end;">
                <input type="hidden" name="page" value="training">
                <div class="col-md-4">
                  <label class="form-label">Search Programs</label>
                  <input type="text" name="search" class="form-control" placeholder="Search by name, description..."
                    value="<?php echo htmlspecialchars($searchQuery); ?>">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Status</label>
                  <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Active" <?php echo $statusFilter === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $statusFilter === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
                <div class="col-md-2">
                  <a href="?page=training" class="btn btn-secondary w-100">Clear</a>
                </div>
              </form>
            </div>
          </div>

          <?php if ($message): ?>
            <script>
              document.addEventListener('DOMContentLoaded', function() {
                showToast(<?php echo json_encode($message); ?>, <?php echo json_encode($messageType); ?>, 4000);
              });
            </script>
          <?php endif; ?>


















          <div class="container py-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
              <div>
                <h3 class="fw-bold mb-0">Training Programs</h3>
                <small class="text-muted">Manage and explore all available programs</small>
              </div>
            </div>

            <div class="row g-4">

              <?php foreach ($programs as $program): ?>
                <?php
                $id = $program['ld_training_programs_id'];
                $title = $program['title'];
                $desc = $program['description'];
                $trainer = $program['trainer'];
                $start = $program['start_date'];
                $end = $program['end_date'];
                $max = $program['max_participants'];
                $status = $program['status'];
                ?>

                <div class="col-md-6 col-lg-4">

                  <div class="card border-0 rounded-4 h-100 shadow-sm program-card">

                    <div class="card-body d-flex flex-column p-4">

                      <h5 class="fw-bold mb-2 text-dark">
                        <?= htmlspecialchars($title) ?>
                      </h5>

                      <p class="text-muted small mb-3" style="min-height: 60px;">
                        <?= htmlspecialchars($desc) ?>
                      </p>

                      <div class="mb-3 small text-secondary">
                        <div><strong>Trainer:</strong> <?= htmlspecialchars($trainer) ?></div>
                        <div><strong>Start:</strong> <?= $start ?></div>
                        <div><strong>End:</strong> <?= $end ?></div>
                        <div><strong>Slots:</strong> <?= $max ?></div>
                      </div>

                      <div class="d-flex justify-content-between align-items-center mb-3">

                        <span class="badge px-3 py-2 rounded-pill
                <?= $status === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                          <?= ucfirst($status) ?>
                        </span>

                        <small class="text-muted">ID: #<?= $id ?></small>

                      </div>

                      <div class="mt-auto">
                        <button class="btn btn-primary w-100 rounded-3">
                          View Details
                        </button>
                      </div>

                    </div>
                  </div>

                </div>

              <?php endforeach; ?>

            </div>

            <div class="d-flex justify-content-center mt-5">

              <nav>
                <ul class="pagination pagination-sm justify-content-center">

                  <?php
                  $prevPage = max(1, $page - 1);
                  $nextPage = min($totalPages, $page + 1);
                  ?>

                  <!-- PREV -->
                  <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $prevPage ?>">
                      Prev
                    </a>
                  </li>

                  <!-- PAGE NUMBERS -->
                  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                      <a class="page-link" href="?page=<?= $i ?>">
                        <?= $i ?>
                      </a>
                    </li>
                  <?php endfor; ?>

                  <!-- NEXT -->
                  <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $nextPage ?>">
                      Next
                    </a>
                  </li>

                </ul>
              </nav>

            </div>

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