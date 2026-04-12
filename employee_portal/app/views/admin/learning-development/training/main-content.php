<div class="w-full ml-8">
    <div class="content-wrapper w-full">
        <div class="card shadow-lg border-0 rounded-4 w-full">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                <h1 class="text-[100%] font-bold text-gray-800">Training Programs</h1>























<div class="container" style="margin-top:90px; margin-bottom: 40px;">

    <div class="training-toolbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="m-0">Training Programs</h2>
            <p class="text-muted small mt-2 mb-0">
                Enhance your skills with our professional development programs
            </p>

            <?php if ($role = current_role()): ?>
                <p class="text-muted small mb-0">
                    Your role: <strong><?php echo htmlspecialchars($role); ?></strong>
                    <?php echo can_manage() ? '(management access)' : ''; ?>
                </p>
            <?php endif; ?>
        </div>

        <?php if (can_manage()): ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createProgramModal">
                Create Training Program
            </button>
        <?php endif; ?>
    </div>

    <!-- SEARCH -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3" style="display:flex; gap:1rem; align-items:flex-end;">
                <input type="hidden" name="page" value="training">

                <div class="col-md-4">
                    <label class="form-label">Search Programs</label>
                    <input type="text" name="search" class="form-control"
                           value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="Active" <?php echo ($statusFilter ?? '') === 'Active' ? 'selected' : ''; ?>>Active</option>
                        <option value="Inactive" <?php echo ($statusFilter ?? '') === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Search</button>
                </div>

                <div class="col-md-2">
                    <a href="?page=training" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <?php $items = $paginatedPrograms['items'] ?? []; ?>

    <?php if (!empty($items)): ?>
        <div class="row g-3 training-grid">

            <?php foreach ($items as $i => $it): ?>

                <?php
                // FIX ID + TITLE
                $it['id'] = $it['ld_training_programs_id'] ?? $it['id'];
                $it['title'] = $it['name'] ?? $it['title'] ?? '';
                $it['description'] = $it['description'] ?? '';

                $dataEnrolledArr = $enrollmentDetails[$it['id']] ?? [];
                $enrolledCount = $enrollmentCounts[$it['id']] ?? 0;

                $delay = $i * 0.12;
                ?>

                <div class="col-md-4">
                    <div class="card h-100 training-card pop-in"
                         style="animation-delay: <?php echo $delay; ?>s;"
                         data-id="<?php echo $it['id']; ?>">

                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title">
                                <?php echo htmlspecialchars($it['title']); ?>
                            </h5>

                            <p class="card-text text-muted">
                                <?php echo htmlspecialchars($it['description']); ?>
                            </p>

                            <div class="mt-auto d-flex justify-content-between">
                                <small>Enrolled: <?php echo $enrolledCount; ?></small>

                                <?php if (can_manage()): ?>
                                    <button class="btn btn-sm btn-outline-secondary">Edit</button>
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                <?php else: ?>
                                    <form method="post">
                                        <input type="hidden" name="id" value="<?php echo $it['id']; ?>">

                                        <?php
                                        $enrolled = false;
                                        foreach ($dataEnrolledArr as $e) {
                                            if ($e['user_id'] == $currentUserId) {
                                                $enrolled = true;
                                                break;
                                            }
                                        }
                                        ?>

                                        <?php if ($enrolled): ?>
                                            <button name="action" value="unenroll" class="btn btn-warning btn-sm">
                                                Unenroll
                                            </button>
                                        <?php else: ?>
                                            <button name="action" value="enroll" class="btn btn-primary btn-sm">
                                                Enroll
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>

        <!-- PAGINATION -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">

                <?php if ($paginatedPrograms['hasPrevPage']): ?>
                    <li class="page-item">
                        <a class="page-link"
                           href="?page=training&program_page=<?php echo $paginatedPrograms['currentPage'] - 1; ?>">
                            Prev
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $paginatedPrograms['totalPages']; $p++): ?>
                    <li class="page-item <?php echo $p == $paginatedPrograms['currentPage'] ? 'active' : ''; ?>">
                        <a class="page-link"
                           href="?page=training&program_page=<?php echo $p; ?>">
                            <?php echo $p; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($paginatedPrograms['hasNextPage']): ?>
                    <li class="page-item">
                        <a class="page-link"
                           href="?page=training&program_page=<?php echo $paginatedPrograms['currentPage'] + 1; ?>">
                            Next
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </nav>

    <?php else: ?>
        <div class="alert alert-info">
            No training programs found.
        </div>
    <?php endif; ?>

</div>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../toast.php'; ?>
<?php require_once __DIR__ . '/../search_filter.php'; ?>
<?php require_once __DIR__ . '/../image_upload.php'; ?>
<?php require_once __DIR__ . '/modal-create.php'; ?>
<?php require_once __DIR__ . '/modal-edit.php'; ?>
<?php require_once __DIR__ . '/modal-delete.php'; ?>