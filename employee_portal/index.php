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
require 'app/controllers/LeaveRequestController.php';
require 'app/controllers/AnnouncementController.php';
require 'app/controllers/NotificationController.php';
require 'app/controllers/MedicalRecordController.php';
require 'app/controllers/OnlineMeetingController.php';
require 'app/controllers/EmployeePortalController.php';
require 'app/controllers/TrainingRequestController.php';
require 'app/controllers/EmployeeDocumentsController.php';
require 'app/controllers/EmployeeGrievanceController.php';
require 'app/controllers/PerformanceFeedbackController.php';
require 'app/controllers/BenefitsAndGovContribController.php';
require 'app/controllers/LearningAndDevelopmentController.php';

$url = $_GET['url'] ?? 'auth-index';

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

    case 'employee-documents-decision':
        (new EmployeeDocumentsController)->decision();
        break;

    case 'admin-documents-add-remarks':
        (new EmployeeDocumentsController)->addRemarks();
        break;

    // Employee Attendance
    case 'employee-time-in':
        (new AttendanceController)->timeIn();
        break;

    case 'employee-time-out':
        (new AttendanceController)->timeOut();
        break;

    // Employee Grievance
    case 'employee-grievance':
        (new EmployeeGrievanceController)->index();
        break;

    case 'employee-grievance-create':
        (new EmployeeGrievanceController)->create();
        break;

    // Employee Payslips
    case 'employee-payslip-items':
        (new PayslipController)->index();
        break;

    case 'view-payslip':
        (new PayslipController)->viewPayslip();
        break;

    case 'export-payslip-csv':
        (new PayslipController)->exportCsv();
        break;

    //Dashboard
    case 'dashboard':
        (new EmployeePortalController)->index();
        break;

    case 'admin-dashboard':
        (new EmployeePortalController)->adminIndex();
        break;

    //Leave Request 
    case 'employee-leave-request':
        (new LeaveRequestController)->index();
        break;

    case 'leave-request-store':
        (new LeaveRequestController)->store();
        break;

    case 'admin-leave-request':
        (new LeaveRequestController)->indexAdmin();
        break;

    //Online Meeting
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

    //Announcement
    case 'employee-announcements':
        (new AnnouncementController)->index();
        break;

    //Performance Feedback
    case 'admin-performance-feedback':
        (new PerformanceFeedbackController)->adminIndex();
        break;

    case 'performance-feedback':
        (new PerformanceFeedbackController)->index();
        break;

    case 'performance-feedback-create':
        (new PerformanceFeedbackController)->create();
        break;

    //Clinic / Medical Records
    case 'employee-medical-records':
        (new MedicalRecordController)->index();
        break;

    //Profile
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

    //Benefits and Gov Contrib
    case 'admin-benefits-and-gov-contrib':
        (new BenefitsAndGovContribController)->index();
        break;

    case 'benefits-and-gov-contrib-store':
        (new BenefitsAndGovContribController)->create();
        break;

    case 'benefits-and-gov-contrib-update':
        (new BenefitsAndGovContribController)->update();
        break;

    case 'benefits-and-gov-contrib-delete':
        (new BenefitsAndGovContribController)->delete();
        break;

    case 'benefits-and-gov-contrib':
        (new BenefitsAndGovContribController)->employeeBenefits();
        break;

    //Training and Development 
    case 'learning-and-development':
        (new LearningAndDevelopmentController)->index();
        break;

    //Training Request
    case 'admin-training-request':
        (new TrainingRequestController)->index();
        break;

    case 'admin-create-training-request':
        (new TrainingRequestController)->adminCreate();
        break;

    case 'admin-training-request-update':
        (new TrainingRequestController)->update();
        break;

    case 'admin-training-request-delete':
        (new TrainingRequestController)->delete();
        break;

    case 'training-request':
        (new TrainingRequestController)->employeeIndex();
        break;
        
    case 'training-request-store':
        (new TrainingRequestController)->employeeCreate();
        break;

    default:
        $title = "Page Not Found";
        $content = __DIR__ . 'app/views/error-content.php';
        require __DIR__ . '/layout.php';
        break;
}
