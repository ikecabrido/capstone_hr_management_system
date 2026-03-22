<?php require_once "../app/Core/Image.php"; ?>

<div class="flex-grow-1 ml-16 transition-all">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../partials/navbar.php'; ?>


    <main class="flex-grow-1 bg-light">

        <?php
        $coverImage = Image::get(
            $employee['cover_image'],
            'images',
            'assets/images/default-cover.jpg'
        );

        $profileImage = Image::get(
            $employee['profile_image'],
            'images',
            'assets/images/default-profile.jpg'
        );
        ?>

        <div class="relative mb-1">
            <div class="w-full h-64 rounded-xl shadow-md overflow-hidden">
                <img
                    src="<?= $coverImage ?>"
                    alt="Cover Image"
                    class="w-full h-full object-cover">
            </div>
            <div class="absolute left-[10%] transform -translate-x-1/2 -bottom-24">
                <div class="rounded-full p-1 bg-white shadow-lg">
                    <img
                        src="<?= $profileImage ?>"
                        alt="Profile Image"
                        class="rounded-full w-48 h-48 object-cover border-4 border-white">
                </div>
            </div>
        </div>

        <div class="container-fluid mt-1 pt-2">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div class="text-center mt-28">
                    <h2 class="fw-bold text-2xl"><?= htmlspecialchars($employee['name'] ?? 'Employee Name') ?></h2>
                    <p class="text-muted"><?= htmlspecialchars($employee['position'] ?? '') ?></p>
                </div>
                <a href="index.php?url=employee-index" class="btn btn-outline-secondary btn-sm">
                    ← Back
                </a>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-xl border-2 h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-4 text-primary">Employee Details</h5>

                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><strong>ID:</strong> <?= $employee['employee_id'] ?></li>
                                <li class="mb-2"><strong>Status:</strong> <?= $employee['employment_status'] ?></li>
                                <li class="mb-2"><strong>Campus:</strong> <?= $employee['campus'] ?></li>
                                <li class="mb-2"><strong>Position:</strong> <?= $employee['position'] ?></li>
                                <li class="mb-0"><strong>Date Hired:</strong> <?= $employee['date_hired'] ?></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-xl border-2 h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-4 text-primary">Account Information</h5>

                            <ul class="list-unstyled mb-3">
                                <li class="mb-2"><strong>Email:</strong> <?= $employee['email'] ?></li>
                                <li class="mb-0">
                                    <strong>Account Status:</strong>
                                    <span class="badge <?= $employee['isActive'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $employee['isActive'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </li>
                            </ul>

                            <div class="mt-4 d-flex gap-2 flex-wrap">
                                <a href="index.php?url=employee-edit&id=<?= $employee['user_id'] ?>" class="btn btn-outline-primary btn-sm">
                                    ✏️ Edit Profile
                                </a>
                                <a href="index.php?url=employee-deactivate&id=<?= $employee['user_id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Deactivate this employee?');">
                                    🔒 Deactivate
                                </a>
                                <a href="index.php?url=employee-reset-password&id=<?= $employee['user_id'] ?>" class="btn btn-outline-warning btn-sm">
                                    🔑 Reset Password
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>