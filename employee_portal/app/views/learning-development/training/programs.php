<section>
    <h3 class="section-title text-xl">
        <i class="fas fa-bookmark"></i> Featured Training Programs
    </h3>
    <small class="text-muted">Manage and explore all available programs</small>
</section>
<div class="row g-4">
    <?php foreach ($programs as $index => $program): ?>
        <?php
        $id = $program['ld_training_programs_id'];
        $title = $program['title'];
        $desc = $program['description'];
        $trainer = $program['trainer'];
        $start = $program['start_date'];
        $end = $program['end_date'];
        $max = $program['max_participants'];
        $status = $program['status'];

        $modalId = 'programModal_' . $id . '_' . $index;

        $enrolled = false;
        if (!empty($enrollmentDetails[$id]) && $currentUserId) {
            $userIds = array_column($enrollmentDetails[$id], 'user_id');
            $enrolled = in_array($currentUserId, $userIds);
        }
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 training-card clickable-card rounded-4 overflow-hidden"
                style="cursor: pointer;"
                data-bs-toggle="modal"
                data-bs-target="#<?= $modalId ?>">
                <?php require __DIR__ . '/random-gif.php'; ?>
                <img src="<?= htmlspecialchars($coverPhoto ?? $randomGif) ?>"
                    class="card-img-top"
                    style="height: 200px; object-fit: cover;"
                    alt="<?= htmlspecialchars($title) ?>">

                <div class="card-body d-flex flex-column">

                    <h5 class="card-title">
                        <?= htmlspecialchars($title) ?>
                    </h5>

                    <p class="card-text text-muted mb-2">
                        <?= htmlspecialchars(substr($desc, 0, 100)) ?>
                    </p>

                    <p class="text-center mb-2">
                        <small class="badge bg-info">Training</small>
                    </p>

                    <div class="d-flex justify-content-between mb-2">
                        <small><?= ucfirst($status) ?></small>
                        <small><?= $start ?></small>
                        <small><?= $end ?></small>
                    </div>

                    <small class="text-muted mb-3">
                        <i class="fa-solid fa-user-tie me-1"></i>
                        <?= htmlspecialchars($trainer) ?>
                    </small>

                    <div class="mt-auto">
                        <button
                            class="btn btn-primary w-100"
                            data-bs-toggle="modal"
                            data-bs-target="#<?= $modalId ?>">
                            View Course
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <?php
        $isEnrolled = false;

        if (!empty($enrollmentDetails[$id]) && $currentUserId) {
            $userIds = array_column($enrollmentDetails[$id], 'user_id');
            $isEnrolled = in_array($currentUserId, $userIds);
        }

        $showEnrollButton = true; 
        ?>
        <?php require __DIR__ . '/view-modal.php' ?>
        
    <?php endforeach; ?>
</div>