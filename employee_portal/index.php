<?php
session_start();

require 'app/controllers/EmployeePortalController.php';

$url = $_GET['url'] ?? 'dashboard';

switch ($url) {

    case 'dashboard':
        (new EmployeePortalController)->index();
        break;

    default:
        $title = "Page Not Found";
        $content = __DIR__ . 'app/views/error-content.php';
        require __DIR__ . '/layout.php';
        break;
}
