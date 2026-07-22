<?php
$totalEvaluations = count($feedbacks);

$totalRating = 0;
$lastEvaluation = null;
$anonymousCount = 0;

foreach ($feedbacks as $feedback) {
    $totalRating += $feedback['rating'];

    if ($feedback['is_anonymous']) {
        $anonymousCount++;
    }

    if (
        !$lastEvaluation ||
        strtotime($feedback['evaluation_date']) > strtotime($lastEvaluation)
    ) {
        $lastEvaluation = $feedback['evaluation_date'];
    }
}

$averageRating = $totalEvaluations
    ? round($totalRating / $totalEvaluations, 1)
    : 0;
?>

<div class="content-wrapper px-3">

    <section class="content pt-3">

        <!-- Header -->
        <div class="mb-4">
            <h2 class="font-weight-bold">
                360° Performance Evaluation
            </h2>
            <p class="text-muted mb-0">
                View your performance appraisal results and feedback from Managers, Peers, and Subordinates.
            </p>
        </div>

        <!-- Summary -->
        <div class="row mb-4">

            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>Overall Rating</h6>
                        <h2><?= $averageRating ?>/5</h2>
                        <div>
                            <?= str_repeat('⭐', round($averageRating)); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6>Total Evaluations</h6>
                        <h2><?= $totalEvaluations ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6>Last Evaluation</h6>
                        <h5>
                            <?= $lastEvaluation ? date('M d, Y', strtotime($lastEvaluation)) : '-' ?>
                        </h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <h6>Anonymous Feedback</h6>
                        <h2><?= $anonymousCount ?></h2>
                    </div>
                </div>
            </div>

        </div>

        <!-- Feedback Table -->
        <div class="card shadow-sm">

            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    Feedback History
                </h3>
            </div>

            <div class="card-body table-responsive p-0">

                <table class="table table-hover table-bordered mb-0">

                    <thead class="thead-light">

                        <tr>
                            <th width="140">Date</th>
                            <th width="150">Evaluator</th>
                            <th width="180">Category</th>
                            <th width="150">Rating</th>
                            <th>Comments</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($feedbacks)): ?>

                            <?php foreach ($feedbacks as $feedback): ?>

                                <?php
                                switch ($feedback['evaluator_type']) {

                                    case 'Manager':
                                        $badge = 'badge-primary';
                                        break;

                                    case 'Peer':
                                        $badge = 'badge-dark';
                                        break;

                                    case 'Subordinate':
                                        $style = 'background-color:#d35400; color:#fff;';
                                        break;

                                    default:
                                        $badge = 'badge-secondary';
                                }

                                ?>

                                <tr>

                                    <td>
                                        <?= date('M d, Y', strtotime($feedback['evaluation_date'])) ?>
                                    </td>

                                    <td>

                                        <span class="badge <?= $badge ?>">
                                            <?= htmlspecialchars($feedback['evaluator_type']) ?>
                                        </span>

                                        <?php if ($feedback['is_anonymous']) : ?>

                                            <br>

                                            <small class="text-muted">
                                                <i class="fas fa-user-secret"></i>
                                                Anonymous
                                            </small>

                                        <?php endif; ?>

                                    </td>

                                    <td>

                                        <span class="badge badge-dark px-3 py-2">
                                            <?= htmlspecialchars($feedback['category']) ?>
                                        </span>

                                    </td>

                                    <td>

                                        <strong>
                                            <?= str_repeat('⭐', $feedback['rating']) ?>
                                        </strong>

                                        <br>

                                        <small class="text-success">
                                            <?= $feedback['rating'] ?>/5
                                        </small>

                                    </td>

                                    <td>

                                        <?= nl2br(htmlspecialchars($feedback['comments'])) ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="5" class="text-center py-5">

                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>

                                    <h5>No Performance Feedback</h5>

                                    <p class="text-muted mb-0">
                                        Your performance evaluations will appear here once they have been submitted.
                                    </p>

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </section>

</div>