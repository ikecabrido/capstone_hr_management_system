<div class="flex-grow-1 ml-16 transition-all">
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../partials/navbar.php'; ?>

    <?php
    $isNew = $_SESSION['user']['isNew'] ?? 0;
    ?>

    <main class="bg-light p-20">
        <?php if ($isNew == 1): ?>
            <?php require_once __DIR__ . '/../profile/change-password.php'; ?>
        <?php else: ?>
            <?php require_once __DIR__ . '/dashboard.php'; ?>
        <?php endif; ?>
    </main>
</div>