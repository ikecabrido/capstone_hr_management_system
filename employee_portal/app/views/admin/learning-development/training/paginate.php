<div class="d-flex justify-content-center mt-5">
    <?php
    $prevPage = max(1, $page - 1);
    $nextPage = min($totalPages, $page + 1);
    ?>
    <nav>
        <ul class="pagination justify-content-center mt-4">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link"
                    href="?url=training-program-paginate&page=<?= $prevPage ?>&search=<?= urlencode($searchQuery) ?>&status=<?= urlencode($statusFilter) ?>">
                    Prev
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link"
                        href="?url=training-program-paginate&page=<?= $i ?>&search=<?= urlencode($searchQuery) ?>&status=<?= urlencode($statusFilter) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link"
                    href="?url=training-program-paginate&page=<?= $nextPage ?>&search=<?= urlencode($searchQuery) ?>&status=<?= urlencode($statusFilter) ?>">
                    Next
                </a>
            </li>
        </ul>
    </nav>
</div>