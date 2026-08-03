<div class="w-full ml-12">
    <div class="content-wrapper w-full ">
        <div class="card shadow-lg border-0 rounded-4 w-[80%] mx-auto my-5">
            <?php require __DIR__ . '/../partials/notif.php' ?>
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

                <!-- Profile Header -->
                <div class="flex justify-between">
                    <h1 class="text-4xl font-bold p-2">User Profile</h1>
                    <button
                        type="button"
                        class="btn btn-primary m-2"
                        data-bs-toggle="modal"
                        data-bs-target="#employeeInfoModal">

                        <i class="fas fa-user me-1"></i>
                        Edit Employee Info

                    </button>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-8 flex items-center gap-6">

                    <!-- Avatar -->
                    <div class="w-20 h-20 rounded-full bg-white text-blue-600 flex items-center justify-center text-3xl font-bold shadow-md overflow-hidden">

                        <?php if (!empty($employeeProfileInfo['profile_image'])): ?>

                            <img
                                src="/capstone_hr_management_system/employee_portal/public/uploads/profile/<?= htmlspecialchars($employeeProfileInfo['profile_image']); ?>"
                                class="w-full h-full object-cover"
                                alt="Profile Photo">

                        <?php else: ?>

                            <?= strtoupper(substr($userInfos['username'], 0, 1)); ?>

                        <?php endif; ?>

                    </div>

                    <!-- Name + Username -->
                    <div>
                        <h2 class="text-2xl font-bold">
                            <?= htmlspecialchars(trim(
                                $userInfos['first_name'] . ' ' .
                                    ($userInfos['middle_name'] ? $userInfos['middle_name'] . ' ' : '') .
                                    $userInfos['last_name'] .
                                    ($userInfos['suffix'] ? ' ' . $userInfos['suffix'] : '')
                            )); ?>
                        </h2>
                        <p class="text-blue-100">
                            @<?= htmlspecialchars($userInfos['username']); ?>
                        </p>
                    </div>

                </div>

                <!-- Profile Details -->
                <div class="p-8">

                    <?php
                    $labels = [
                        'id' => 'User ID',
                        'username' => 'Username',
                        'full_name' => 'Full Name',
                        'role' => 'Role',
                        'theme' => 'Theme',
                        'created_at' => 'Created At'
                    ];
                    ?>

                    <div class="divide-y divide-gray-200">

                        <?php foreach ($userInfos as $key => $value): ?>
                            <?php
                            $hiddenFields = [
                                'first_name',
                                'middle_name',
                                'last_name',
                                'password',
                                'role',
                                'is_admin',
                                'theme',
                                'password_reset_token',
                                'password_reset_expires'
                            ];

                            if (in_array($key, $hiddenFields)) continue;
                            ?>

                            <div class="flex justify-between items-center py-3">
                                <span class="text-black text-md">
                                    <?= $labels[$key] ?? ucfirst(str_replace('_', ' ', $key)); ?>
                                </span>

                                <span class="font-semibold text-gray-800 flex items-center gap-2">

                                    <?php if ($key === 'role'): ?>
                                        <span class="px-4 py-3 full_name bg-blue-100 text-blue-600 rounded-full text-xs">
                                            <?= htmlspecialchars($value); ?>
                                        </span>

                                    <?php elseif ($key === 'theme'): ?>
                                        <span class="px-4 py-3 full_name bg-gray-200 text-gray-700 rounded-full text-xs">
                                            <?= ucfirst($value); ?>
                                        </span>

                                    <?php elseif ($key === 'created_at'): ?>
                                        <section class="flex-col items-end text-right">
                                            <p>
                                                <i class="far fa-calendar-alt text-blue-500"></i>
                                                <?= date('F d, Y', strtotime($value)); ?> <br>
                                            </p>
                                            <p class="text-xs">
                                                <?= date('h:i A', strtotime($value)); ?>
                                            </p>
                                        </section>
                                    <?php else: ?>
                                        <?= htmlspecialchars($value); ?>
                                    <?php endif; ?>

                                </span>

                            </div>

                        <?php endforeach; ?>

                    </div>
                    <!-- Action Buttons -->
                    <div class="mt-2 flex gap-4">
                        <a class="btn btn-primary d-inline-flex align-items-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#changeNameModal">
                            <i class="fas fa-user-edit"></i>
                            Change Username
                        </a>
                        <a class="btn btn-dark d-inline-flex align-items-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#changePasswordModal">
                            <i class="fas fa-key"></i>
                            Change Password
                        </a>
                    </div>
                </div>

                <?php require __DIR__ . '/edit.php'; ?>
                <?php require __DIR__ . '/employee-profile.php'; ?>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/modal-change-name.php'; ?>
<?php require __DIR__ . '/modal-change-password.php'; ?>