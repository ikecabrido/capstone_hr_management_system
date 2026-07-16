<?php
require_once __DIR__ . '/../models/NotificationRecipient.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/Employee.php';
class NotificationController
{
    private $notificationModel;
    private $recipientModel;
    private $employeeModel;
    public function __construct()
    {
        $this->recipientModel = new NotificationRecipient();
        $this->notificationModel = new Notification();
        $this->employeeModel = new Employee();
    }
    public function index()
    {
        $employeeList = $this->employeeModel->all();
        $notification = $this->notificationModel->all();

        $recipientList = [];

        if (!empty($_GET['view'])) {

            $viewNotification = $this->notificationModel->find($_GET['view']);

            $recipientList = $this->recipientModel
                ->getRecipients($_GET['view']);
        }

        $title = "Employee Notification";
        $content = __DIR__ . '/../views/admin/notification/main-content.php';
        require __DIR__ . '/../views/admin/notification/index.php';
    }
    public function create()
    {
        try {

            $title = trim($_POST['title'] ?? '');
            $message = trim($_POST['message'] ?? '');
            $type = $_POST['type'] ?? 'general';
            $priority = $_POST['priority'] ?? 'normal';
            $employeeIds = $_POST['employee_ids'] ?? [];

            if (empty($title) || empty($message)) {
                throw new Exception("Please complete all required fields.");
            }

            if (empty($employeeIds)) {
                throw new Exception("Please select at least one employee.");
            }

            // Create notification
            $notificationId = $this->notificationModel->create([
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'priority' => $priority,
                'created_by_user_id' => Session::get('user_id')
            ]);

            if (!$notificationId) {
                throw new Exception("Failed to create notification.");
            }

            // Create recipients
            $this->recipientModel->createRecipients(
                $notificationId,
                $employeeIds
            );

            Session::set('success', 'Notification sent successfully.');
        } catch (Exception $e) {

            Session::set('error', $e->getMessage());
        }

        Helper::redirect('index.php?url=admin-notification');
    }
    public function view()
    {
        $id = $_GET['id'] ?? 0;

        $notification = $this->notificationModel->find($id);

        header('Content-Type: application/json');
        echo json_encode($notification);
        exit;
    }
    public function update()
    {
        Session::start();

        try {

            $notificationId = $_POST['notification_id'] ?? 0;
            $title = trim($_POST['title'] ?? '');
            $message = trim($_POST['message'] ?? '');
            $type = $_POST['type'] ?? 'general';
            $priority = $_POST['priority'] ?? 'normal';
            $employeeIds = $_POST['employee_ids'] ?? [];

            if (
                empty($notificationId) ||
                empty($title) ||
                empty($message)
            ) {
                throw new Exception("Please complete all required fields.");
            }

            if (empty($employeeIds)) {
                throw new Exception("Please select at least one employee.");
            }

            // Update notification
            $this->notificationModel->update([
                'notification_id' => $notificationId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'priority' => $priority
            ]);

            // Replace recipients
            $this->recipientModel->deleteRecipients($notificationId);

            $this->recipientModel->createRecipients(
                $notificationId,
                $employeeIds
            );

            Session::set(
                'success',
                'Notification updated successfully.'
            );
        } catch (Exception $e) {

            Session::set(
                'error',
                $e->getMessage()
            );
        }

        Helper::redirect(
            'index.php?url=admin-notification'
        );
    }
    public function delete()
    {
        Session::start();

        try {

            $notificationId = $_POST['notification_id'] ?? 0;

            if (empty($notificationId)) {
                throw new Exception("Notification not found.");
            }

            if (!$this->notificationModel->delete($notificationId)) {
                throw new Exception("Unable to delete notification.");
            }

            Session::set(
                'success',
                'Notification deleted successfully.'
            );
        } catch (Exception $e) {

            Session::set(
                'error',
                $e->getMessage()
            );
        }

        Helper::redirect('index.php?url=admin-notification');
    }
}
