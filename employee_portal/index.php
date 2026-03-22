<?php
session_start();

require 'app/controllers/EmployeePortalController.php';
require 'app/controllers/AuthController.php';
require 'app/controllers/EmployeeDocumentsController.php';


$url = $_GET['url'] ?? 'auth-index';

switch ($url) {

    case 'auth-index':
        (new AuthController)->index();
        break;

    case 'auth-login':
        (new AuthController)->login();
        break;

    case 'auth-logout':
        (new AuthController)->logout();
        break;

    // Employee Documents (Employee Side)
    case 'employee-documents-index':
        (new EmployeeDocumentsController)->employeeIndex();
        break;

    case 'employee-documents-create':
        (new EmployeeDocumentsController)->create();
        break;

    // Employee Documents (Admin Side)
    case 'admin-documents-index':
        (new EmployeeDocumentsController)->adminDocsIndex();
        break;







    case 'dashboard':
        (new EmployeePortalController)->index();
        break;

    case 'admin-dashboard':
        (new EmployeePortalController)->adminIndex();
        break;

    default:
        $title = "Page Not Found";
        $content = __DIR__ . 'app/views/error-content.php';
        require __DIR__ . '/layout.php';
        break;
}
