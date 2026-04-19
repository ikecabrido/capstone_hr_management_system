<div class="browse-section">
    <section>
        <h3 class="section-title text-xl">
            <i class="fas fa-bookmark"></i> My Training Programs
        </h3>
        <small class="text-muted">Manage and explore your enrolled programs</small>
    </section>
    <?php if (!empty($userEnrollments)): ?>
        <div class="carousel-container position-relative">
            <div class="row g-3" style="overflow-x: auto; display: flex; flex-wrap: nowrap;">
                <?php foreach (array_slice($userEnrollments, 0, 3) as $program): ?>
                    <div class="col-md-4" style="flex: 0 0 33.333%; min-width: 300px;">
                        <div class="card h-100 training-card clickable-card" style="cursor: pointer;"
                            data-program-id="<?php echo intval($program['id']); ?>"
                            data-name="<?php echo htmlspecialchars($program['name']); ?>"
                            data-description="<?php echo htmlspecialchars($program['description']); ?>"
                            data-cover-photo="<?php echo htmlspecialchars(getImageUrl($program['cover_photo'] ?? null, $placeholderImg)); ?>"
                            data-duration="<?php echo htmlspecialchars($program['duration'] ?? 'N/A'); ?>"
                            data-instructor="<?php echo htmlspecialchars($program['instructor'] ?? 'N/A'); ?>"
                            data-status="<?php echo htmlspecialchars($program['status'] ?? 'N/A'); ?>"
                            data-start-date="<?php echo htmlspecialchars($program['start_date'] ?? 'N/A'); ?>"
                            data-enrolled="<?php echo $enrollmentDetails[$program['id']] ?? 0; ?>">
                            <img src="<?php echo htmlspecialchars(getImageUrl($program['cover_photo'] ?? null, $placeholderImg)); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($program['name']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($program['name']); ?></h5>
                                <p class="card-text text-muted mb-2"><?php echo htmlspecialchars(substr($program['description'], 0, 100)); ?></p>
                                <p class="text-center mb-2" style="font-size: 0.9rem;">
                                    <small class="badge bg-info">Training</small>
                                </p>
                                <!-- Card meta grid: status | date | remaining -->
                                <div class="card-meta-grid mb-2">
                                    <div class="d-flex justify-content-between gap-1">
                                        <small class="meta-label">Active</small>
                                        <small class="meta-label">—</small>
                                        <small class="meta-label">Enrolled: <?php echo $enrollmentDetails[$program['id']] ?? 0; ?></small>
                                    </div>
                                </div>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Total: <?php echo $enrollmentDetails[$program['id']] ?? 0; ?></small>
                                    <div class="card-action-set">
                                        <form method="post" style="display:inline;" onclick="event.stopPropagation();">
                                            <input type="hidden" name="action" value="unenroll">
                                            <input type="hidden" name="id" value="<?php echo intval($program['id']); ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-warning">Unenroll</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($userEnrollments) > 3): ?>
                <button class="carousel-nav carousel-prev" style="position: absolute; left: -20px; top: 50%; transform: translateY(-50%);">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-nav carousel-next" style="position: absolute; right: -20px; top: 50%; transform: translateY(-50%);">
                    <i class="fas fa-chevron-right"></i>
                </button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">You haven't enrolled in any trainings yet.</div>
    <?php endif; ?>
</div>