<?php
session_start();

require 'controllers/RequestTypeController.php';
require 'controllers/EmployeePortalController.php';
require 'controllers/RequestController.php';
require 'controllers/LeaveRequestController.php';

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

    case 'request-index':
        (new RequestController)->index();
        break;

    case 'request-create':
        (new RequestController)->create();
        break;

    case 'request-status-update':
        (new RequestController)->updateRequestStatus();
        break;

    case 'request-download':
        (new RequestController)->download();
        break;

    case 'request-update-status':
        (new RequestController)->updateRequestStatus();
        break;

    case 'request-delete':
        (new RequestController)->delete();
        break;

    case 'leave-requests-index':
        (new LeaveRequestController)->index();
        break;

    case 'leave-requests-create':
        (new LeaveRequestController)->create();
        break;
    
    case 'leave-requests-update-status':
        (new LeaveRequestController)->updateStatus();
        break;

    case 'leave-requests-delete':
        (new LeaveRequestController)->delete(); 
        break;

    default:
        $title = "Page Not Found";
        $content = __DIR__ . '/views/error-content.php';
        require __DIR__ . '/layout.php';
        break;
}
