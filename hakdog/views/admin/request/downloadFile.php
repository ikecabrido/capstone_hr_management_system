<?php if (!empty($request['attachment'])): ?>

    <?php
    $file = $request['attachment'];
    $path = "/hrmsys/public/uploads/requests/" . $file;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    ?>

    <?php if (in_array($ext, $imageTypes)): ?>

        <div class="d-flex flex-column align-items-center gap-1">

            <img src="<?= $path; ?>"
                style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">

            <a href="index.php?url=admin-download-request&id=<?= $request['id']; ?>"
                class="btn btn-sm btn-outline-primary py-0 px-2">
                Download
            </a>

        </div>

    <?php else: ?>

        <div class="d-flex flex-column align-items-center gap-1">

            <div class="small text-primary">
                📎 <?= htmlspecialchars($ext); ?> file
            </div>

            <a href="index.php?url=admin-download-request&id=<?= $request['id']; ?>"
                class="btn btn-sm btn-outline-primary py-0 px-2">
                Download
            </a>

        </div>

    <?php endif; ?>

<?php else: ?>

    <span class="text-muted small">——</span>

<?php endif; ?>