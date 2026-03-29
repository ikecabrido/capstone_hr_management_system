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
                    <button class="btn btn-warning btn-sm ml-1"
                        data-bs-toggle="modal"
                        data-bs-target="#editMeetingModal<?= $meeting['id'] ?>">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                    <form action="index.php?url=admin-online-meeting-delete"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this meeting?');">

                        <input type="hidden" name="id" value="<?= $meeting['id'] ?>">

                        <button type="submit" class="btn btn-danger btn-sm ml-1">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>

            <?php require __DIR__ . '/modal-edit-online-meeting.php'; ?>
        <?php endforeach; ?>
    </tbody>
</table>