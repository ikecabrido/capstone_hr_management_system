<?php
session_start();

//classes
require_once "../app/Core/View.php";

//controllers
require_once "../app/Controllers/EmployeeController.php";
require_once "../app/Controllers/ProfileController.php";
require_once "../app/Controllers/EmployeePortalController.php";
require_once "../app/Controllers/RequestTypeController.php";
require_once "../app/Controllers/RequestController.php";

$url = $_GET['url'] ?? 'employee-portal';

// $publicRoutes = [
//     'home',
//     'login',
//     'session-expired'
// ];

/**
 * Session timeout check
 */
// if (
//     !in_array($url, $publicRoutes) &&
//     isset($_SESSION['last_activity']) &&
//     time() - $_SESSION['last_activity'] > 1800
// ) {
//     header("Location: index.php?url=session-expired");
//     exit;
// }

// Update activity time only if logged in
// if (Auth::check()) {
//     $_SESSION['last_activity'] = time();
// }

switch ($url) {

    case 'employee-portal':
        View::render("employee-portal/index", ['title' => 'Welcome | Employee Portal']);
        break;

        default:
        View::render("error", ['title' => '404 Not Found | Employee Portal']);
        break;
}
