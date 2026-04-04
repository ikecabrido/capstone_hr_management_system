<?php
session_start();
require_once "../auth/user.php";

$userModel = new User();
$user = $userModel->findById($_SESSION['user']['id']);

if (!$user) {
    // Fallback: use session data if database lookup fails
    $user = [
        'full_name' => $_SESSION['user']['name'] ?? 'User',
        'id' => $_SESSION['user']['user_id'] ?? 0
    ];
}
?>

<form id="passwordForm" action="update_user.php" method="POST">

    <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" class="form-control"
            value="<?= htmlspecialchars($user['full_name']) ?>">
    </div>

    <div class="form-group">
        <label>Current Password</label>
        <input type="password" name="current_password" class="form-control">
    </div>

    <div class="form-group">
        <label>New Password</label>
        <input type="password" name="new_password" class="form-control">
    </div>

    <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control">
    </div>

    <button class="btn btn-success">Save Changes</button>

</form>