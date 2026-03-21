<?php
session_start();
require_once "auth/auth.php";

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation: Check if credentials are provided
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Username and password are required";
        header("Location: login_form.php");
        exit;
    }

    if ($auth->login($username, $password)) {
        header("Location: router.php");
        exit;
    } else {
        sendResponse(false, 'Invalid username or password', 401);
    }

} catch (Exception $e) {
    error_log('Login Exception: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
    sendResponse(false, 'Server error: ' . $e->getMessage(), 500);

}

?>
