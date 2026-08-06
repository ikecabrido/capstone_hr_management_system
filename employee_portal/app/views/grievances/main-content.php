<div class="w-full ml-32">

    <div class="content-wrapper w-full">

        <?php require __DIR__ . '/../partials/notif.php'; ?>

        <!-- Header -->
        <div class="bg-white rounded-4 shadow-sm border p-4 mb-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>

                    <h2 class="fw-bold mb-1">
                        Employee Grievance
                    </h2>

                    <p class="text-muted mb-0">
                        Submit, monitor, and review the status of your workplace grievances.
                    </p>

                </div>

                <button
                    type="button"
                    class="btn btn-primary rounded-pill px-4"
                    data-bs-toggle="modal"
                    data-bs-target="#createGrievanceModal">

                    <i class="fas fa-plus-circle me-2"></i>

                    Submit Grievance

                </button>

            </div>

        </div>

        <!-- Statistics -->
        <div class="row g-4 mb-4">

            <!-- Total -->
            <div class="col-md-3">

                <div class="card border-0 shadow-sm h-100 rounded-4">

                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div>

                            <small class="text-muted text-uppercase fw-semibold">
                                Total Grievances
                            </small>

                            <h2 class="fw-bold mt-2 mb-1">
                                <?= $totalGrievances ?>
                            </h2>

                            <small class="text-muted">
                                All submitted grievances
                            </small>

                        </div>

                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">

                            <i class="fas fa-folder-open fs-3 text-primary"></i>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Pending -->
            <div class="col-md-3">

                <div class="card border-0 shadow-sm h-100 rounded-4">

                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div>

                            <small class="text-muted text-uppercase fw-semibold">
                                Pending
                            </small>

                            <h2 class="fw-bold text-warning mt-2 mb-1">
                                <?= $pending ?>
                            </h2>

                            <small class="text-muted">
                                Waiting for review
                            </small>

                        </div>

                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">

                            <i class="fas fa-hourglass-half fs-3 text-warning"></i>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Resolved -->
            <div class="col-md-3">

                <div class="card border-0 shadow-sm h-100 rounded-4">

                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div>

                            <small class="text-muted text-uppercase fw-semibold">
                                Resolved
                            </small>

                            <h2 class="fw-bold text-success mt-2 mb-1">
                                <?= $resolved ?>
                            </h2>

                            <small class="text-muted">
                                Successfully completed
                            </small>

                        </div>

                        <div class="bg-success bg-opacity-10 rounded-circle p-3">

                            <i class="fas fa-check-circle fs-3 text-success"></i>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Escalated -->
            <div class="col-md-3">

                <div class="card border-0 shadow-sm h-100 rounded-4">

                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div>

                            <small class="text-muted text-uppercase fw-semibold">
                                Escalated
                            </small>

                            <h2 class="fw-bold text-danger mt-2 mb-1">
                                <?= $escalated ?>
                            </h2>

                            <small class="text-muted">
                                Higher-level review
                            </small>

                        </div>

                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">

                            <i class="fas fa-arrow-up-right-dots fs-3 text-danger"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Recent Grievances -->
        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-header bg-white border-bottom">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="fw-bold mb-0">

                            My Grievances

                        </h5>

                        <small class="text-muted">

                            View all grievance requests submitted to Employee Engagement & Relations.

                        </small>

                    </div>

                    <div class="d-flex gap-2">

                        <input
                            type="text"
                            class="form-control"
                            placeholder="Search subject...">

                        <select class="form-select">

                            <option value="">All Status</option>
                            <option>Pending</option>
                            <option>Escalated</option>
                            <option>Resolved</option>

                        </select>

                    </div>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>Reference</th>
                            <th>Category</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date Submitted</th>
                            <th class="text-center">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($grievances)): ?>

                            <?php foreach ($grievances as $grievance): ?>

                                <?php

                                // Status Badge
                                switch (strtolower($grievance['status'])) {

                                    case 'pending':
                                        $statusClass = 'bg-warning text-dark';
                                        break;

                                    case 'resolved':
                                        $statusClass = 'bg-success';
                                        break;

                                    case 'escalated':
                                        $statusClass = 'bg-danger';
                                        break;

                                    case 'closed':
                                        $statusClass = 'bg-secondary';
                                        break;

                                    default:
                                        $statusClass = 'bg-primary';
                                        break;
                                }

                                // Priority Badge
                                switch (strtolower($grievance['priority'])) {

                                    case 'low':
                                        $priorityClass = 'bg-success';
                                        break;

                                    case 'medium':
                                        $priorityClass = 'bg-info';
                                        break;

                                    case 'high':
                                        $priorityClass = 'bg-warning text-dark';
                                        break;

                                    case 'urgent':
                                        $priorityClass = 'bg-danger';
                                        break;

                                    case 'critical':
                                        $priorityClass = 'bg-dark';
                                        break;

                                    default:
                                        $priorityClass = 'bg-secondary';
                                }

                                ?>

                                <tr>

                                    <td class="fw-semibold">
                                        #GRV-<?= str_pad($grievance['eer_grievance_id'], 5, '0', STR_PAD_LEFT) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($grievance['category']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($grievance['subject']) ?>
                                    </td>

                                    <td>
                                        <span class="badge <?= $priorityClass ?>">
                                            <?= ucfirst($grievance['priority']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucwords(str_replace('_', ' ', $grievance['status'])) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= date('M d, Y', strtotime($grievance['created_at'])) ?>
                                    </td>

                                    <td class="text-center">

                                        <button
                                            class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewGrievance<?= $grievance['eer_grievance_id'] ?>">

                                            <i class="fas fa-eye"></i>

                                        </button>

                                    </td>

                                </tr>

                                <?php require __DIR__ . '/view.php'; ?>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="7" class="text-center py-5 text-muted">

                                    <i class="fas fa-inbox fa-3x mb-3 d-block text-secondary"></i>

                                    <h6 class="mb-1">No grievances found</h6>

                                    <small>
                                        You haven't submitted any grievance requests yet.
                                    </small>

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

        <!-- Timeline -->
        <div class="card shadow-sm border-0 rounded-4 mt-4">

            <div class="card-header bg-white border-bottom">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="fw-bold mb-0">
                            Recent Activity
                        </h5>

                        <small class="text-muted">
                            Latest updates on your grievance requests.
                        </small>

                    </div>

                </div>

            </div>

            <div class="card-body">

                <?php if (!empty($grievances)): ?>

                    <?php
                    $recentActivities = array_slice($grievances, 0, 5);

                    foreach ($recentActivities as $activity):

                        switch (strtolower($activity['status'])) {

                            case 'pending':
                                $bg = 'bg-warning';
                                $icon = 'fa-paper-plane';
                                $message = 'Grievance submitted and awaiting review.';
                                break;

                            case 'resolved':
                                $bg = 'bg-success';
                                $icon = 'fa-check-circle';
                                $message = 'Your grievance has been resolved.';
                                break;

                            case 'escalated':
                                $bg = 'bg-danger';
                                $icon = 'fa-arrow-up';
                                $message = 'Your grievance has been escalated.';
                                break;

                            case 'closed':
                                $bg = 'bg-secondary';
                                $icon = 'fa-folder-check';
                                $message = 'Your grievance has been closed.';
                                break;

                            default:
                                $bg = 'bg-primary';
                                $icon = 'fa-file-circle-exclamation';
                                $message = 'Status updated.';
                                break;
                        }
                    ?>

                        <div class="d-flex mb-4">

                            <div class="me-3">

                                <div
                                    class="<?= $bg ?> rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                    style="width:48px;height:48px;">

                                    <i class="fas <?= $icon ?> text-white"></i>

                                </div>

                            </div>

                            <div class="flex-grow-1">

                                <div class="d-flex justify-content-between align-items-start">

                                    <div>

                                        <h6 class="fw-bold mb-1">

                                            <?= htmlspecialchars($activity['subject']) ?>

                                        </h6>

                                        <small class="text-muted">

                                            <?= htmlspecialchars($message) ?>

                                        </small>

                                    </div>

                                    <small class="text-muted">

                                        <?= date('M d, Y', strtotime($activity['created_at'])) ?>

                                    </small>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="text-center py-5">

                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>

                        <h6 class="fw-bold">

                            No Recent Activity

                        </h6>

                        <p class="text-muted mb-0">

                            Your grievance activities will appear here once you submit a grievance.

                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>
<?php require __DIR__ . '/create.php'; ?>