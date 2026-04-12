<div class="w-full ml-8">
    <div class="content-wrapper w-full">
        <div class="card shadow-lg border-0 rounded-4 w-full">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                <h1 class="font-bold text-gray-800 p-10 text-4xl">Training Programs</h1>




                <?php ?>

                <div class="container" style="margin-top:90px; margin-bottom: 40px;">
                    <div class="training-toolbar d-flex justify-content-between align-items-center mb-4">

                        <div>
                            <h2 class="m-0">Training Programs</h2>
                            <p class="text-muted small mt-2 mb-0">
                                Enhance your skills with our professional development programs
                            </p>

                            <?php if (!empty($role)): ?>
                                <p class="text-muted small mb-0">
                                    Your role: <strong><?php echo htmlspecialchars($role); ?></strong>
                                    <?php echo $isAuthorized ? '(management access)' : ''; ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <?php if ($isAuthorized): ?>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createProgramModal">
                                Create Training Program
                            </button>
                        <?php endif; ?>
                    </div>


                    <?php if ($message): ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                showToast(<?php echo json_encode($message); ?>, <?php echo json_encode($messageType); ?>, 4000);
                            });
                        </script>
                    <?php endif; ?>

                    <!-- Search & Filter Bar -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3" style="display: flex; gap: 1rem; align-items: flex-end;">
                                <input type="hidden" name="page" value="training">
                                <div class="col-md-4">
                                    <label class="form-label">Search Programs</label>
                                    <input type="text" name="search" class="form-control" placeholder="Search by name, description..."
                                        value="<?php echo htmlspecialchars($searchQuery); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        <option value="Active" <?php echo $statusFilter === 'Active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="Inactive" <?php echo $statusFilter === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Search</button>
                                </div>
                                <div class="col-md-2">
                                    <a href="?page=training" class="btn btn-secondary w-100">Clear</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if ($message): ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                showToast(<?php echo json_encode($message); ?>, <?php echo json_encode($messageType); ?>, 4000);
                            });
                        </script>
                    <?php endif; ?>





                </div>


















            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . "/action.php" ?>
<?php require __DIR__ . "/modal-create.php" ?>
<?php require __DIR__ . "/modal-edit.php" ?>
<?php require __DIR__ . "/modal-delete.php" ?>

<?php require __DIR__ . "/../image_upload.php" ?>
<?php require __DIR__ . "/../toast.php" ?>