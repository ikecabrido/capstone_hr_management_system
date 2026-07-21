<div class="flex-grow-1 ml-16 transition-all">

    <?php require_once __DIR__ . '/../../partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../partials/navbar.php'; ?>

    <main class="bg-light min-vh-100 py-5 px-4">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../../partials/notif.php'; ?>

            <?php require_once __DIR__ . '/table-user.php'; ?>

            <?php require_once __DIR__ . '/deactivated-accounts.php'; ?>

        </div>
    </main>

    <?php require_once __DIR__ . '/create-user-modal.php'; ?>

</div>