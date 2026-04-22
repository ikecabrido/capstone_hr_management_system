<?php
// Load the model 
// require_once __DIR__ . '/../models/Announcement.php';

class CareerPathController
{
    // Instance of the model
    // private $carerPathModel;

    public function __construct()
    {
        // Initialize the model so we can access data
        // $this->careerPathModel = new CareerPath();
    }

    public function index()
    {
        $title = "Employee Career Paths";
        $content = __DIR__ . '/../views/learning-development/career/main-content.php';
        require __DIR__ . '/../views/index.php';
    }
}
