<?php

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

    default:
        echo "Page not found";
}