<?php
session_start();

if (isset($_SESSION['user']) && !isset($_SESSION['user_id'])) {

    $_SESSION['user_id'] = $_SESSION['user']['id'];
    $_SESSION['employee_id'] = $_SESSION['user']['employee_id'];
    $_SESSION['username'] = $_SESSION['user']['username'];
    $_SESSION['name'] = $_SESSION['user']['name'];
    $_SESSION['full_name'] = $_SESSION['user']['name'];
    $_SESSION['role'] = $_SESSION['user']['role'];
    $_SESSION['theme'] = $_SESSION['user']['theme'];
}

require 'app/controllers/AuthController.php';
require 'app/controllers/ProfileController.php';
require 'app/controllers/PayslipController.php';
require 'app/controllers/GrievanceController.php';
require 'app/controllers/DocumentsController.php';
require 'app/controllers/CareerPathController.php';
require 'app/controllers/LeaveRequestController.php';
require 'app/controllers/AnnouncementController.php';
require 'app/controllers/NotificationController.php';
require 'app/controllers/MedicalRecordController.php';
require 'app/controllers/OnlineMeetingController.php';
require 'app/controllers/EmployeePortalController.php';
require 'app/controllers/TrainingProgramController.php';
require 'app/controllers/EnrollmentProgramController.php';
require 'app/controllers/PerformanceFeedbackController.php';

/*
|--------------------------------------------------------------------------
| Login out user that has not yet logged in Routes
|--------------------------------------------------------------------------
|
*/
$url = $_GET['url'] ?? 'auth-index';
if (in_array($url, $protectedRoutes)) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?url=auth-login");
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| These routes handle user authentication, including login and logout functionality.
|
*/
switch ($url) {
    //Authentication
    case 'auth-index':
        (new AuthController)->index();
        break;

    case 'auth-login':
        (new AuthController)->login();
        break;

    case 'auth-logout':
        (new AuthController)->logout();
        break;

    /*
|--------------------------------------------------------------------------
| Employee and Admin Documents Routes
|--------------------------------------------------------------------------
| These routes handle employee and admin-specific document functionality.
|
*/
    case 'employee-documents-index':
        (new DocumentsController)->index();
        break;

    case 'employee-documents-create':
        (new DocumentsController)->create();
        break;

    case 'admin-documents-index':
        (new DocumentsController)->adminDocsIndex();
        break;

    case 'employee-documents-decision':
        (new DocumentsController)->decision();
        break;

    case 'admin-documents-add-remarks':
        (new DocumentsController)->addRemarks();
        break;
    case 'employee-documents-delete':
        (new DocumentsController)->delete();
        break;
    /*
|--------------------------------------------------------------------------
| Time and Attendance Routes
|--------------------------------------------------------------------------
| These routes handle employee time-in and time-out functionality.
|
*/
    case 'employee-time-in':
        (new AttendanceController)->timeIn();
        break;

    case 'employee-time-out':
        (new AttendanceController)->timeOut();
        break;

    /*
|--------------------------------------------------------------------------
| Grievance Routes
|--------------------------------------------------------------------------
| These routes handle employee grievance submission.
|
*/
    case 'employee-grievance':
        (new GrievanceController)->index();
        break;

    case 'employee-grievance-create':
        (new GrievanceController)->create();
        break;

    /*
|--------------------------------------------------------------------------
| Payslips Routes
|--------------------------------------------------------------------------
| These routes handle employee payslip functionality.
|
*/
    case 'employee-payslip-items':
        (new PayslipController)->index();
        break;

    case 'view-payslip':
        (new PayslipController)->viewPayslip();
        break;

    case 'export-payslip-csv':
        (new PayslipController)->exportCsv();
        break;

    /*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
| These routes handle the main dashboard functionality for employees and admins.
|
*/
    case 'dashboard':
        (new EmployeePortalController)->index();
        break;

    case 'admin-dashboard':
        (new EmployeePortalController)->adminIndex();
        break;

    /*
|--------------------------------------------------------------------------
| Leave Request Routes
|--------------------------------------------------------------------------
| These routes handle employee leave request functionality.
|
*/
    case 'employee-leave-request':
        (new LeaveRequestController)->index();
        break;

    case 'leave-request-store':
        (new LeaveRequestController)->store();
        break;

    case 'admin-leave-request':
        (new LeaveRequestController)->indexAdmin();
        break;

    /*
|--------------------------------------------------------------------------
| Online Meeting Routes
|--------------------------------------------------------------------------
| These routes handle employee online meeting functionality.
|
*/
    case 'online-meeting':
        (new OnlineMeetingController)->index();
        break;

    case 'admin-online-meeting':
        (new OnlineMeetingController)->adminIndex();
        break;

    case 'admin-online-meeting-store':
        (new OnlineMeetingController)->store();
        break;

    case 'admin-online-meeting-update':
        (new OnlineMeetingController)->update();
        break;

    case 'admin-online-meeting-delete':
        (new OnlineMeetingController)->delete();
        break;

    /*
|--------------------------------------------------------------------------
| Announcement Routes
|--------------------------------------------------------------------------
| These routes handle employee announcement functionality.
|
*/
    case 'employee-announcements':
        (new AnnouncementController)->index();
        break;

    /*
|--------------------------------------------------------------------------
| Performance Feedback Routes
|--------------------------------------------------------------------------
| These routes handle employee performance feedback functionality.
|
*/
    case 'performance-feedback':
        (new PerformanceFeedbackController)->index();
        break;

    case 'performance-feedback-create':
        (new PerformanceFeedbackController)->create();
        break;

    /*
|--------------------------------------------------------------------------
| Clinic and Medical Records Routes
|--------------------------------------------------------------------------
| These routes handle employee clinic and medical records functionality.
|
*/
    case 'employee-medical-records':
        (new MedicalRecordController)->index();
        break;

    /*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
| These routes handle employee profile management functionality.
|
*/
    case 'user-profile':
        (new ProfileController)->index();
        break;

    case 'update-name':
        (new ProfileController)->updateName();
        break;

    case 'update-password':
        (new ProfileController)->changePassword();
        break;

    //Notification
    case 'admin-notification':
        (new NotificationController)->index();
        break;

    case 'notification-store':
        (new NotificationController)->create();
        break;

    case 'notification-view':
        (new NotificationController)->view();
        break;

    case 'notification-update':
        (new NotificationController)->update();
        break;

    case 'notification-delete':
        (new NotificationController)->delete();
        break;

    case 'employee-notifications':
        (new NotificationController)->employeeNotifications();
        break;

    case 'employee-notification-mark-read':
        (new NotificationController)->markRead();
        break;

    case 'employee-notification-mark-all-read':
        (new NotificationController)->markAllRead();
        break;

    default:
        $title = "Page Not Found";
        $content = __DIR__ . 'app/views/error-content.php';
        require __DIR__ . '/layout.php';
        break;
}
