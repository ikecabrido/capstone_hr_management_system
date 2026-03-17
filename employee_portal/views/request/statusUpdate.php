<form method="POST" action="index.php?url=request-status-update">

    <input type="hidden" name="id" value="<?= $request['id']; ?>">
    <input type="hidden" name="admin_remarks" value="">

    <select name="status"
        class="form-select form-select-sm border-<?= $badge ?> text-<?= $badge ?>"
        onchange="this.form.submit()">

        <?php
        $statuses = ['Pending','Approved','Rejected','Cancelled','Completed'];
        foreach ($statuses as $status):
        ?>

        <option value="<?= $status ?>"
            <?= $request['status'] === $status ? 'selected' : '' ?>>
            <?= $status ?>
        </option>

        <?php endforeach; ?>

    </select>

</form>