<?php

require_once "auth.php";

$auth = new Auth();

// Check if it's an AJAX request (by action parameter or XMLHttpRequest header)
$isAjax = !empty($_GET['action']) || !empty($_POST['action']) ||
          (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

if (!$auth->check()) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Session expired', 'redirect' => '../login_form.php']);
        exit;
    } else {
        header("Location: ../login_form.php");
        exit;
    }
}
