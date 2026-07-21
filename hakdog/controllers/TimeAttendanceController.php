<?php

class TimeAttendanceController
{
    /**
     * Display the time and attendance dashboard
     * Loads the employee dashboard from within the employee portal
     */
    public function index()
    {
        // Load the dashboard logic file
        $title = "Time & Attendance";
        $content = __DIR__ . '/time_attendance/dashboard-logic.php';
        require __DIR__ . '/../layout.php';
    }

    /**
     * Get employee attendance data via API
     */
    public function getAttendanceData()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user']['employee_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // TODO: Implement fetching from ta_attendance table
        $data = [
            'success' => true,
            'employee_id' => $_SESSION['user']['employee_id'],
            'message' => 'Attendance data fetched successfully'
        ];

        echo json_encode($data);
    }

    /**
     * Get leave balance data via API
     */
    public function getLeaveBalance()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user']['employee_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // TODO: Implement fetching from ta_leave_balances table
        $data = [
            'success' => true,
            'employee_id' => $_SESSION['user']['employee_id'],
            'message' => 'Leave balance fetched successfully'
        ];

        echo json_encode($data);
    }
}
