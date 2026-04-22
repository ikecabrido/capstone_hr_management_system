<div class="browse-section">
    <section>
        <h3 class="section-title text-xl">
            <i class="fas fa-bookmark"></i> My Training Programs
        </h3>
        <small class="text-muted">Manage and explore your enrolled programs</small>
    </section>

    <?php if (!empty($userEnrollments)): ?>
        <div class="carousel-container position-relative">
            <div class="row g-3 flex-nowrap overflow-auto">

                <?php foreach (array_slice($userEnrollments, 0, 3) as $index => $program): ?>
                    <?php
                    require __DIR__ . '/random-gif.php';

                    $id        = $program['ld_training_programs_id'];
                    $title     = $program['title'];
                    $desc      = $program['description'];
                    $trainer   = $program['trainer'] ?? 'N/A';
                    $start     = $program['start_date'] ?? 'N/A';
                    $end       = $program['end_date'] ?? 'N/A';
                    $max       = $program['max_participants'] ?? 'N/A';
                    $status    = ucfirst($program['status'] ?? 'active');
                    $image     = $program['cover_photo'] ?? $randomGif;

                    $modalId = 'programModal_' . $id;

                    $isEnrolled = true;
                    $showEnrollButton = false;
                    ?>

                    <div class="col-md-4" style="flex: 0 0 33.333%; min-width: 300px;">
                        <div class="card h-100 training-card clickable-card"
                            style="cursor: pointer;"
                            data-program-id="<?= $id ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#<?= $modalId ?>">

                            <img src="<?= htmlspecialchars($image) ?>"
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

                                <div class="text-center mb-2">
                                    <small class="badge bg-info">Training</small>
                                </div>

                                <div class="d-flex justify-content-between small text-muted mb-2">
                                    <span><?= $status ?></span>
                                    <span><?= $start ?></span>
                                </div>

                                <small class="text-muted mb-3">
                                    <i class="fa-solid fa-user-tie me-1"></i>
                                    <?= htmlspecialchars($trainer) ?>
                                </small>

                                <div class="mt-auto d-flex justify-content-between align-items-center">

                                    <small class="text-muted">
                                        Enrolled
                                    </small>

                                    <form method="post"
                                        action="index.php?url=training-program-unenroll"
                                        onclick="event.stopPropagation();">

                                        <input type="hidden" name="id" value="<?= $id ?>">

                                        <button type="submit"
                                            class="btn btn-sm btn-outline-warning">
                                            Unenroll
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    $isEnrolled = true;
                    $showEnrollButton = false;
                    ?>
                    <?php require __DIR__ . '/view-modal.php' ?>
                <?php endforeach; ?>

            </div>

            <?php if (count($userEnrollments) > 3): ?>
                <button class="carousel-nav carousel-prev"
                    style="position: absolute; left: -20px; top: 50%; transform: translateY(-50%);">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <button class="carousel-nav carousel-next"
                    style="position: absolute; right: -20px; top: 50%; transform: translateY(-50%);">
                    <i class="fas fa-chevron-right"></i>
                </button>
            <?php endif; ?>

        </div>

    <?php else: ?>
        <div class="alert alert-info">
            You haven't enrolled in any trainings yet.
        </div>
    <?php endif; ?>
</div>