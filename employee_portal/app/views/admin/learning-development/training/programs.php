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

            <div class="card border-0 rounded-4 overflow-hidden program-card h-100">

                <div class="p-3 text-white" style="background: linear-gradient(135deg, #0d6efd, #4dabf7);">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="opacity-75">
                            <i class="fa-solid fa-graduation-cap me-1"></i> Course
                        </small>
                        <span class="badge bg-light text-dark px-2 py-1 rounded-pill">
                            <?= ucfirst($status) ?>
                        </span>
                    </div>
                </div>

                <div class="card-body d-flex flex-column p-4">

                    <h5 class="fw-bold text-dark mb-2">
                        <?= htmlspecialchars($title) ?>
                    </h5>

                    <p class="text-muted small mb-3 line-clamp">
                        <?= htmlspecialchars($desc) ?>
                    </p>

                    <div class="text-muted small mb-4">

                        <div class="d-flex align-items-center mb-2">
                            <i class="fa-solid fa-user-tie me-2 text-primary"></i>
                            <?= htmlspecialchars($trainer) ?>
                        </div>

                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-calendar-days me-2 text-primary"></i>
                            <?= $start ?> - <?= $end ?>
                        </div>

                    </div>

                    <div class="mt-auto">
                        <button
                            class="btn btn-primary w-100 rounded-3 fw-semibold"
                            data-bs-toggle="modal"
                            data-bs-target="#programModal<?= $id ?>">
                            <i class="fa-solid fa-eye me-2"></i> View Course
                        </button>
                    </div>

                </div>

            </div>

        </div>
    <?php endforeach; ?>
</div>