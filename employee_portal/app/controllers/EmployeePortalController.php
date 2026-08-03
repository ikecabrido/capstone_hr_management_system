<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Leave.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/LeaveType.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/AttendanceController.php';
require_once __DIR__ . '/../models/Announcement.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/LearningAndDevelopment.php';


class EmployeePortalController
{
    private $leaveModel;
    private $employeeModel;
    private $trainingModel;
    private $leaveTypeModel;
    private $authController;
    private $attendanceModel;
    private $announcementModel;
    private $notificationModel;
    private $attendanceController;


    public function __construct()
    {
        $this->leaveModel = new Leave();
        $this->employeeModel = new Employee();
        $this->leaveTypeModel = new LeaveType();
        $this->attendanceModel = new Attendance();
        $this->authController = new AuthController();
        $this->announcementModel = new Announcement();
        $this->notificationModel = new Notification();
        $this->trainingModel = new LearningAndDevelopment();
        $this->attendanceController = new AttendanceController();
    }

    public function index()
    {
        if (Session::get('is_admin') == 1) {

            Session::set('success', null);

            Session::set(
                'error',
                'Administrator accounts cannot access the Employee Portal. Please log in to the HR Management System.'
            );

            header("Location: http://localhost/capstone_hr_management_system/login_form.php");
            exit;
        }
        $title = "Employee Portal";

        $user_id = Session::get('user_id');
        $employee = $this->authController->checkUserEmployee($user_id);
        $employeeProfileInfo = $this->employeeModel->findByUserId($user_id);

        $employee_id = $employee['employee_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];

            if ($action === 'time_in') {
                $this->attendanceController->timeIn($employee_id);
            } elseif ($action === 'time_out') {
                $this->attendanceController->timeOut($employee_id);
            }
            exit;
        }

        if (!$employee) {
            Session::set('error', 'Employee not found');
            header("Location: index.php?url=auth-index");
            exit;
        }


        $statusInfo = $this->attendanceController->getStatus($employee_id);

        $message = Session::get('success') ?? Session::get('error') ?? null;

        $messageType = Session::get('success') ? 'success' : (Session::get('error') ? 'danger' : 'info');

        Session::set('success', null);
        Session::set('error', null);

        $leave_balances = $this->leaveModel->getLeaveBalances($employee_id);
        $monthly_attendance = $this->attendanceModel->getMonthlyAttendance($employee_id);
        $leave_requests = $this->leaveModel->getLeaveRequestsByEmployee($employee_id);

        $pendingRequests = array_filter($leave_requests, function ($leave) {
            return $leave['status'] === 'Pending';
        });

        $allLeaveTypes = $this->leaveTypeModel->getAllLeaveTypes();

        $leaveTypeMap = [];
        foreach ($allLeaveTypes as $type) {
            $leaveTypeMap[$type['leave_type_id']] = $type['leave_type_name'];
        }

        foreach ($pendingRequests as &$request) {
            $request['leave_type_name'] = $leaveTypeMap[$request['leave_type_id']] ?? 'Unknown';
        }
        unset($request);

        $announcements = $this->announcementModel->all();

        $recentActivities = [];

        foreach ($leave_requests as $leave) {
            $recentActivities[] = [
                'icon' => 'fas fa-calendar-check',
                'title' => 'Leave Request Submitted',
                'description' => $leave['leave_type_name'],
                'date' => $leave['date_submitted']
            ];
        }

        foreach ($monthly_attendance as $attendance) {
            if (!empty($attendance['time_in'])) {
                $recentActivities[] = [
                    'icon' => 'fas fa-clock',
                    'title' => 'Timed In',
                    'description' => Helper::formatTime($attendance['time_in']),
                    'date' => $attendance['time_in']
                ];
            }
        }

        usort($recentActivities, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        $recentActivities = array_slice($recentActivities, 0, 5);

        $upcomingTrainings = $this->trainingModel->getTrainingRecords($employee_id);
        $upcomingTrainings = array_filter($upcomingTrainings, function ($training) {

            // Show only requests that are still active
            return in_array($training['request_status'], [
                'New',
                'Received',
                'Approved'
            ]);
        });

        $notifications = $this->notificationModel->getEmployeeNotifications($employee_id);
        $employeeInfo = $this->authController->checkUserEmployee($user_id);


        $content = __DIR__ . '/../views/employee-portal/main-content.php';
        require __DIR__ . '/../views/employee-portal/index.php';
    }
    public function adminIndex()
    {
        $employees = $this->employeeModel->getNonAdminEmployees();

        $content = __DIR__ . '/../views/admin/main-content.php';
        require __DIR__ . '/../views/admin/index.php';
    }
    public function employeeList()
    {
        $employees = $this->employeeModel->getNonAdminEmployees();

        $content = __DIR__ . '/../views/admin/employee-list/main-content.php';
        require __DIR__ . '/../views/admin/index.php';
    }
    public function viewAttendance()
    {
        $attendance = $this->attendanceModel->all();

        $title = "Attendance";

        $content = __DIR__ . '/../views/admin/view-attendance/main-content.php';
        require __DIR__ . "/../views/admin/index.php";
    }
}
