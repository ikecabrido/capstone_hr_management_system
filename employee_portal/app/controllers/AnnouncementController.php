<?php
require_once __DIR__ . '/../models/Announcement.php';
class AnnouncementController
{
    private $announcementModel;
        public function __construct()
    {
        $this->announcementModel = new Announcement();
    }
    public function index()
    {
        $announcements = $this->announcementModel->all();
        $title = "Employee Announcements";
        $content = __DIR__ . '/../views/announcements/main-content.php';
        require __DIR__ . '/../views/announcements/index.php';
    }
}
