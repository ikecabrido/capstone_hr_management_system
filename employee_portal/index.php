<?php
session_start();

require 'app/controllers/EmployeePortalController.php';
require 'app/controllers/AuthController.php';

$url = $_GET['url'] ?? 'auth-index';

switch ($url) {

    case 'auth-index':
        (new AuthController)->index();
        break;

        case 'auth-login':
        (new AuthController)->login();
        break;

    case 'dashboard':
        (new EmployeePortalController)->index();
        break;

    default:
        $title = "Page Not Found";
        $content = __DIR__ . 'app/views/error-content.php';
        require __DIR__ . '/layout.php';
        break;
}
