<style>
    .evaluation-badge {
        display: inline-block;
        min-width: 120px;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .3px;
        text-align: center;
        border: 2px solid transparent;
        transition: all .2s ease;
    }

    /* Manager */
    .evaluation-badge.manager {
        background: #e8f0ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    /* Peer */
    .evaluation-badge.peer {
        background: #ecfeff;
        color: #0f766e;
        border-color: #99f6e4;
    }

    /* Subordinate */
    .evaluation-badge.subordinate {
        background: #fff7ed;
        color: #c2410c;
        border-color: #fdba74;
    }

    /* Self */
    .evaluation-badge.self {
        background: #ecfdf5;
        color: #15803d;
        border-color: #86efac;
    }

    .evaluation-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, .12);
    }
</style>
<div class="content-wrapper px-4 py-3">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

        <div>
            <h1 class="text-4xl font-bold mb-1">
                Performance Feedback
            </h1>

            <p class="text-muted mb-0">
                Manage and review all submitted 360° performance feedback evaluations.
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary shadow-sm mt-3 mt-md-0"
            data-bs-toggle="modal"
            data-bs-target="#feedbackModal">

            <i class="fas fa-plus mr-1"></i>
            Submit Feedback

        </button>

    </div>
    <?php require __DIR__ . '/../../../views/partials/notif.php' ?>
    <!-- Feedback Table -->
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white">

            <h3 class="card-title font-weight-bold mb-0">
                360° Performance Feedback Records
            </h3>

        </div>

        <div class="card-body table-responsive p-0">

            <table class="table table-hover table-bordered mb-0">

                <thead class="bg-light">

                    <tr class="text-center">
                        <th width="60">#</th>
                        <th width="120">Employee</th>
                        <th width="130">Date</th>
                        <th width="140">Evaluator</th>
                        <th width="150">Category</th>
                        <th width="120">Rating</th>
                        <th>Comments</th>
                        <th width="120">Anonymous</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (!empty($feedbacks)): ?>

                        <?php foreach ($feedbacks as $index => $feedback): ?>


                            <tr>

                                <td class="text-center">
                                    <?= $index + 1 ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($feedback['first_name']) ?><?= htmlspecialchars($feedback['last_name']) ?><br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($feedback['employee_code']) ?>
                                    </small>
                                </td>

                                <td>
                                    <?= date('M d, Y', strtotime($feedback['evaluation_date'])) ?>
                                </td>

                                <?php
                                $type = strtolower(trim($feedback['evaluator_type']));
                                ?>

                                <td class="text-center">
                                    <span class="evaluation-badge <?= $type ?>">
                                        <?= htmlspecialchars($type) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($feedback['category']) ?>
                                </td>

                                <td class="text-center">

                                    <div class="text-warning" style="font-size:16px;">
                                        <?= str_repeat('★', (int)$feedback['rating']) ?>
                                    </div>

                                    <small class="text-muted">
                                        <?= $feedback['rating'] ?>/5
                                    </small>

                                </td>

                                <td>
                                    <?= nl2br(htmlspecialchars($feedback['comments'])) ?>
                                </td>

                                <td class="text-center">

                                    <?php if ($feedback['is_anonymous']): ?>

                                        <span class="badge badge-success px-3 py-2">
                                            Yes
                                        </span>

                                    <?php else: ?>

                                        <span class="badge badge-secondary px-3 py-2">
                                            No
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="8" class="text-center py-5">

                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>

                                <h5>No Feedback Records</h5>

                                <p class="text-muted mb-0">
                                    No performance feedback has been submitted yet.
                                </p>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php require __DIR__ . '/create-modal.php'; ?>