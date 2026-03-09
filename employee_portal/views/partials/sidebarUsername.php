<?php
$userName = $_SESSION['user']['name'] ?? 'User';
$initials = strtoupper(substr($userName, 0, 2));
?>

<div class="flex">
    <div class="w-10 h-10 bg-white text-center items-center rounded-full flex justify-center text-xl font-bold text-primary group-hover:flex hidden">
        <?php if (isset($_SESSION['user']['profile_image']) && $_SESSION['user']['profile_image'] !== ''): ?>
            <img src="<?= $_SESSION['user']['profile_image']; ?>" class="rounded-full w-full h-full object-cover" alt="User Image">
        <?php else: ?>
            <span class="text-black"><?= $initials ?></span>
        <?php endif; ?>
    </div>
    <h5 class="opacity-75 p-3 fw-bold mb-0 text-center text-xs group-hover:block hidden"><?= $userName ?></h5>
</div>