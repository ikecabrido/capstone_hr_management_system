<?php
session_start();
require_once "../auth/user.php";

// Debugging output for session data
if (!isset($_SESSION['user']['id'])) {
    echo "<p>Error: User session ID is not set. Please log in again.</p>";
    exit;
}

$userModel = new User();
$user = $userModel->findById($_SESSION['user']['id']);

if (!$user) {
    echo "<p>Error: User not found. Please contact the administrator.</p>";
    echo "<p>Debug: User ID from session: " . htmlspecialchars($_SESSION['user']['id']) . "</p>";
    exit;
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