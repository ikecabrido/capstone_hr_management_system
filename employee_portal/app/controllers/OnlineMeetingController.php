<?php
require_once __DIR__ . '/../models/Meeting.php';
require_once __DIR__ . '/../models/Employee.php';


class OnlineMeetingController
{
    private $meetingModel;
    private $employeeModel;

    public function __construct()
    {
        $this->meetingModel = new Meeting();
        $this->employeeModel = new Employee();
    }
    public function index()
    {
        Auth::requireAuth();

        $meetings = $this->meetingModel->getAll();

        $title = "Admin Online Meeting";
        $content = __DIR__ . '/../views/online-meeting/main-content.php';
        require __DIR__ . '/../views/online-meeting/index.php';
    }
    public function adminIndex()
    {
        Auth::requireAuth();

        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            die('User not logged in.');
        }
        $meetings = $this->meetingModel->getAll();
        $employee = $this->employeeModel->findByUserId($user_id);
        $employee_no = $employee['employee_no'] ?? null;

        $title = "Admin Online Meeting";
        $content = __DIR__ . '/../views/admin/online-meeting/main-content.php';
        require __DIR__ . '/../views/admin/online-meeting/index.php';
    }
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=online-meeting");
            exit;
        }

        $title = $_POST['title'] ?? '';
        $employee_no = $_POST['employee_no'] ?? '';
        $scheduled_at = $_POST['scheduled_at'] ?? '';
        $user = $_POST['created_by'] ?? '';

        if (!$title || !$employee_no || !$scheduled_at) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: index.php?url=admin-dashboard");
            exit;
        }

        $meeting_id = uniqid("hr_meeting_");
        $meeting_link = "https://meet.jit.si/" . $meeting_id;

        try {
            $this->meetingModel->create([
                'title' => $title,
                'meeting_link' => $meeting_link,
                'created_by' => $_POST['created_by'] ?? '',
                'employee_no' => $employee_no,
                'scheduled_at' => $scheduled_at
            ]);

            $_SESSION['success'] = "Meeting created successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Failed to create meeting.";
        }

        header("Location: index.php?url=admin-online-meeting");
    }
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=admin-online-meeting");
            exit;
        }

        $id = $_POST['id'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $scheduled_at = $_POST['scheduled_at'] ?? '';

        if (!$id || !$title || !$scheduled_at) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: index.php?url=admin-online-meeting");
            exit;
        }

        try {
            $this->meetingModel->update([
                'id' => $id,
                'title' => $title,
                'scheduled_at' => $scheduled_at
            ]);

            $_SESSION['success'] = "Meeting updated successfully!";
        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "Failed to update meeting.";
        }

        header("Location: index.php?url=admin-online-meeting");
        exit;
    }
    public function delete()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=admin-online-meeting");
            exit;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $_SESSION['error'] = "Invalid request.";
            header("Location: index.php?url=admin-online-meeting");
            exit;
        }

        try {
            $this->meetingModel->delete($id);
            $_SESSION['success'] = "Meeting deleted successfully!";
        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "Failed to delete meeting.";
        }

        header("Location: index.php?url=admin-online-meeting");
        exit;
    }
}
