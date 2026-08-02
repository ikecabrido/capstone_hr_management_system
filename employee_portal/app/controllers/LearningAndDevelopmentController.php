<?php

require_once __DIR__ . '/../models/LearningAndDevelopment.php';

class LearningAndDevelopmentController
{
    private $learningAndDevelopmentModel;

    public function __construct()
    {
        $this->learningAndDevelopmentModel = new LearningAndDevelopment();
    }

    public function index()
    {
        $user_id = $_SESSION['user_id'] ?? null;

        $employeeModel = new Employee();

        $employee = $employeeModel->findByUserId($user_id);

        $employee_id = $employee['employee_id'];

        $trainingRecords = $this->learningAndDevelopmentModel
            ->getTrainingRecords($employee_id);

        $title = "Learning and Development";

        $content = __DIR__ .
            '/../views/learning-and-development/main-content.php';

        require __DIR__ .
            '/../views/employee-portal/index.php';
    }
}
