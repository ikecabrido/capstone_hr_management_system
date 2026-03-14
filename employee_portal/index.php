<?php
session_start();

require 'controllers/RequestTypeController.php';
require 'controllers/EmployeePortalController.php';

$url = $_GET['url'] ?? 'dashboard';

switch ($url) {

    case 'dashboard':
        (new EmployeePortalController)->index();
        break;

    case 'request-types':
        (new RequestTypeController)->index();
        break;

    case 'request-types-create':
        (new RequestTypeController)->create();
        break;

    case 'request-types-update':
        (new RequestTypeController)->update();
        break;

    case 'request-types-delete':
        (new RequestTypeController)->delete();
        break;

    default:
        $title = "Page Not Found";
        $content = __DIR__ . '/views/error-content.php';
        require __DIR__ . '/layout.php';
        break;
}
