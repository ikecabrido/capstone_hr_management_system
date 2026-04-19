<?php
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;

$prevPage = max(1, $currentPage - 1);
$nextPage = min($totalPages, $currentPage + 1);
?>

<div class="d-flex justify-content-center mt-5">
    <nav>
        <ul class="pagination justify-content-center mt-4">

            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                <a class="page-link"
                   href="?url=training-program-index&page=<?= $prevPage ?>&search=<?= urlencode($searchQuery) ?>&status=<?= urlencode($statusFilter) ?>">
                    Prev
                </a>
            </li>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                    <a class="page-link"
                       href="?url=training-program-index&page=<?= $i ?>&search=<?= urlencode($searchQuery) ?>&status=<?= urlencode($statusFilter) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link"
                   href="?url=training-program-index&page=<?= $nextPage ?>&search=<?= urlencode($searchQuery) ?>&status=<?= urlencode($statusFilter) ?>">
                    Next
                </a>
            </li>

        </ul>
    </nav>
</div>