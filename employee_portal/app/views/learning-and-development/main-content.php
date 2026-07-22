    <div class="content-wrapper w-full">
        <?php require __DIR__ . '/../partials/notif.php'; ?>

        <div class="card shadow-sm border-0 rounded-4">

            <div class="card-header bg-white border-bottom">
                <h2 class="text-3xl font-bold mb-1">
                    Training & Seminar Records
                </h2>

                <p class="text-muted mb-0">
                    Monitor your completed and upcoming training activities.
                </p>
            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover table-bordered align-middle">

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>Training</th>

                            <th>Trainer</th>

                            <th>Start Date</th>

                            <th>End Date</th>

                            <th>Request Status</th>

                            <th>Training Status</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($trainingRecords)): ?>

                            <?php foreach ($trainingRecords as $index => $training): ?>

                                <?php

                                if (!empty($training['start_date']) && !empty($training['end_date'])) {

                                    if (strtotime($training['end_date']) < time()) {

                                        $trainingStatus = "Completed";
                                        $badge = "success";
                                    } elseif (strtotime($training['start_date']) > time()) {

                                        $trainingStatus = "Upcoming";
                                        $badge = "warning";
                                    } else {

                                        $trainingStatus = "Ongoing";
                                        $badge = "primary";
                                    }
                                } else {

                                    $trainingStatus = "Pending Schedule";
                                    $badge = "secondary";
                                }

                                ?>

                                <tr>

                                    <td><?= $index + 1 ?></td>

                                    <td><?= htmlspecialchars($training['title']) ?></td>

                                    <td><?= htmlspecialchars($training['trainer']) ?></td>

                                    <td><?= date('M d, Y', strtotime($training['start_date'])) ?></td>

                                    <td><?= date('M d, Y', strtotime($training['end_date'])) ?></td>

                                    <td>

                                        <?php

                                        switch ($training['request_status']) {

                                            case 'Approved':
                                                $requestBadge = 'success';
                                                break;

                                            case 'Received':
                                                $requestBadge = 'info';
                                                break;

                                            case 'Rejected':
                                                $requestBadge = 'danger';
                                                break;

                                            default:
                                                $requestBadge = 'secondary';
                                        }

                                        ?>

                                        <span class="badge bg-<?= $requestBadge ?>">
                                            <?= $training['request_status'] ?>
                                        </span>

                                    </td>

                                    <td>

                                        <span class="badge bg-<?= $badge ?>">
                                            <?= $trainingStatus ?>
                                        </span>

                                    </td>

                                    <td>

                                        <button
                                            class="btn btn-outline-primary btn-sm viewTrainingBtn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewTrainingModal"
                                            data-title="<?= htmlspecialchars($training['title']) ?>"
                                            data-trainer="<?= htmlspecialchars($training['trainer']) ?>"
                                            data-description="<?= htmlspecialchars($training['description']) ?>"
                                            data-start="<?= date('F d, Y', strtotime($training['start_date'])) ?>"
                                            data-end="<?= date('F d, Y', strtotime($training['end_date'])) ?>"
                                            data-status="<?= $trainingStatus ?>">
                                            <i class="fas fa-eye"></i> View
                                        </button>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="8" class="text-center py-5">

                                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>

                                    <h5>No Training Records</h5>

                                    <p class="text-muted mb-0">
                                        You have no completed or upcoming training activities.
                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require __DIR__ . '/view-modal.php'; ?>
    <script>
        document.querySelectorAll('.viewTrainingBtn').forEach(button => {

            button.addEventListener('click', function() {

                document.getElementById('view_title').textContent =
                    this.dataset.title;

                document.getElementById('view_trainer').textContent =
                    this.dataset.trainer;

                document.getElementById('view_description').textContent =
                    this.dataset.description;

                document.getElementById('view_start').textContent =
                    this.dataset.start;

                document.getElementById('view_end').textContent =
                    this.dataset.end;

                let badge = '';

                switch (this.dataset.status) {

                    case "Completed":
                        badge = '<span class="badge bg-success fs-6 px-3 py-2 rounded-pill"><i class="fas fa-check-circle me-1"></i>Completed</span>';
                        break;

                    case "Upcoming":
                        badge = '<span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill"><i class="fas fa-clock me-1"></i>Upcoming</span>';
                        break;

                    default:
                        badge = '<span class="badge bg-primary fs-6 px-3 py-2 rounded-pill"><i class="fas fa-spinner me-1"></i>Ongoing</span>';
                }

                document.getElementById('view_status').innerHTML = badge;

            });

        });
    </script>