<div class="w-full ml-32 mt-4">
    <div class="content-wrapper w-full">

        <div class="justify-content-between mb-2 ">
            <h1 class="fw-bold mb-0 text-5xl p-4">Online Meeting</h1>
        </div>
        <?php require __DIR__ . '/../partials/notif.php' ?>
        <?php if (!empty($meetings)): ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <div>
                    <i class="fas fa-calendar-alt text-success" style="font-size: 60px;"></i>
                </div>


                <h5 class="fw-semibold text-3xl">
                    <div class="card border-0 shadow-sm rounded-4 mr-2"></div>Available Meeting List
                </h5>

                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Schedule</th>
                            <th>Meeting</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($meetings as $meeting): ?>
                            <tr>
                                <td><?= htmlspecialchars($meeting['title']) ?></td>

                                <td>
                                    <?= date('M d, Y h:i A', strtotime($meeting['scheduled_at'])) ?>
                                </td>

                                <td>
                                    <a href="<?= $meeting['meeting_link'] ?>"
                                        target="_blank"
                                        class="btn btn-success btn-sm">
                                        Join
                                    </a>
                                </td>

                                <td class="flex">
                                    <button
                                        class="btn btn-primary btn-outline-primary btn-sm"
                                        onclick="copyLink('<?= $meeting['meeting_link'] ?>')">
                                        <i class="fas fa-copy mr-1"></i> Copy
                                    </button>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="text-muted small mt-2">
                    Note: Share this link with participants. The meeting starts when someone opens the link.
                </p>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">

                <h5 class="fw-semibold mb-2">No meetings yet</h5>
                <p class="text-muted mb-3">Create your first online meeting to get started.</p>
            </div>
        <?php endif; ?>

    </div>

</div>