<?php

// Load the model 
require_once __DIR__ . '/../models/Announcement.php';

class AnnouncementController
{
    // Instance of the model
    private $announcementModel;

    public function __construct()
    {
        // Initialize the model so we can access data
        $this->announcementModel = new Announcement();
    }

    /**
     * Display the list of announcements
     *
     * - Fetches all announcements from the database
     * - Passes data to the view
     * - Loads the main announcements layout
     */
    public function index()
    {
        // Get all announcements
        $announcements = $this->announcementModel->all();

        $title = "Employee Announcements";
        $content = __DIR__ . '/../views/engagement-relations/announcements/main-content.php';
        require __DIR__ . '/../views/index.php';
    }
}