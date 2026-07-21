<form method="POST" action="index.php?url=admin-request-update-status">

    <input type="hidden" name="id" value="<?= $request['id']; ?>">

    <select name="status"
        class="form-select form-select-sm border-<?= $badge; ?> text-<?= $badge; ?>"
        onchange="this.form.submit()">

        <?php
        $statuses = ['Pending', 'Approved', 'Rejected', 'Cancelled', 'Completed'];
        foreach ($statuses as $status):
        ?>

            <option value="<?= $status; ?>"
                <?= $request['status'] === $status ? 'selected' : '' ?>>
                <?= $status; ?>

            </option>

        <?php endforeach; ?>

    </select>

</form>