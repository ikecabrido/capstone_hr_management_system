<div class="flex-grow-1 ml-16 transition-all">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../partials/navbar.php'; ?>


    <main class="flex-grow-1 bg-light p-4">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/notif.php'; ?>


            
                <?php require_once __DIR__ . '/table-employee.php'; ?>
            
        </div>
    </main>
    <?php require_once __DIR__ . '/create-employee-modal.php'; ?>
</div>