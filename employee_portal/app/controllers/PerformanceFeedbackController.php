<?php
require_once __DIR__ . '/../models/PerformanceFeedback.php';
require_once __DIR__ . '/../models/Employee.php';


class PerformanceFeedbackController
{
    private $performanceFeedbackModel;
    private $employeeModel;

    public function __construct()
    {
        $this->performanceFeedbackModel = new PerformanceFeedback();
        $this->employeeModel = new Employee();
    }

    public function index()
    {
        $user_id = $_SESSION['user_id'] ?? null;

        $employee = $this->employeeModel->findByUserId($user_id);
        $employee_id = $employee['id'] ?? null;


        $content = __DIR__ . '/../views/performance-feedback/main-content.php';
        require __DIR__ . '/../views/performance-feedback/index.php';
    }

    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=employee-grievance"));
                exit;
            }

            $employee_id  = $_POST['employee_id'] ?? null;
            $evaluator_type  = trim($_POST['evaluator_type'] ?? '');
            $rating   = $_POST['rating'] ?? null;
            $category   = $_POST['category'] ?? null;
            $comments   = $_POST['comments'] ?? null;
            $is_anonymous   = $_POST['is_anonymous'] ?? null;
            $evaluation_date = date('Y-m-d H:i:s');
            $created_at = date('Y-m-d H:i:s');

            $data = [
                'employee_id' => $employee_id,
                'evaluator_type' => $evaluator_type,
                'rating' => $rating,
                'category' => $category,
                'comments' => $comments,
                'is_anonymous' => $is_anonymous,
                'evaluation_date' => $evaluation_date,
                'created_at' => $created_at
            ];

            $this->performanceFeedbackModel = new PerformanceFeedback();
            $this->performanceFeedbackModel->create($data);

            $_SESSION['success'] = "Document submitted successfully.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage() ?: "Something went wrong while submitting.";
        }

        $redirectTo = $_SERVER['HTTP_REFERER'] ?? "index.php?url=employee-grievance";
        header("Location: $redirectTo");
        exit;
    }
}
