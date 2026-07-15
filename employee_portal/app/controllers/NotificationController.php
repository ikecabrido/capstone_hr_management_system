<?php
require_once __DIR__ . '/../models/Notification.php';
class NotificationController
{
    private $notificationModel;
    public function __construct()
    {
        $this->notificationModel = new Notification();
    }
    // public function index()
    // {
    //     $announcements = $this->announcementModel->all();
    //     $title = "Employee Announcements";
    //     $content = __DIR__ . '/../views/announcements/main-content.php';
    //     require __DIR__ . '/../views/announcements/index.php';
    // }
    public function index()
    {

        $notification = $this->notificationModel->all();

        $title = "Employee Notification";
        $content = __DIR__ . '/../views/admin/notification/main-content.php';
        require __DIR__ . '/../views/admin/notification/index.php';
    }
}
