<?php

require_once __DIR__ . '/../models/Grievance.php';
require_once __DIR__ . '/../models/Employee.php';

class GrievanceController
{
    private $grievanceModel;
    private $employeeModel;

    public function __construct()
    {
        $this->grievanceModel = new Grievance();
        $this->employeeModel = new Employee();
    }
    public function index()
    {
        $userId = $_SESSION['user_id'];

        $employeeGrievance = $this->employeeModel->findByUserId($userId);

        if (!$employeeGrievance) {
            $_SESSION['error'] = "Employee record not found.";
            Helper::redirect('index.php?url=dashboard');
            exit;
        }

        $grievances = $this->grievanceModel->getByEmployee($employeeGrievance['employee_id']);

        // Statistics
        $totalGrievances = count($grievances);

        $pending = 0;
        $resolved = 0;
        $escalated = 0;
        $closed = 0;

        foreach ($grievances as $grievance) {

            switch (strtolower($grievance['status'])) {

                case 'pending':
                    $pending++;
                    break;

                case 'resolved':
                    $resolved++;
                    break;

                case 'escalated':
                    $escalated++;
                    break;

                case 'closed':
                    $closed++;
                    break;
            }
        }

        $title = "Employee Grievance";
        $content = __DIR__ . '/../views/grievances/main-content.php';

        require __DIR__ . '/../views/employee-portal/index.php';
    }
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('index.php?url=employee-grievance');
            exit;
        }

        $employeeId = (int) $_POST['employee_id'];
        $userId     = $_SESSION['user_id'];

        // Upload attachment
        $attachmentPath = null;

        if (
            isset($_FILES['attachment_path']) &&
            $_FILES['attachment_path']['error'] === UPLOAD_ERR_OK
        ) {

            $uploadDir = 'uploads/grievances/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $extension = pathinfo(
                $_FILES['attachment_path']['name'],
                PATHINFO_EXTENSION
            );

            $filename = uniqid('grievance_') . '.' . $extension;

            move_uploaded_file(
                $_FILES['attachment_path']['tmp_name'],
                $uploadDir . $filename
            );

            $attachmentPath = $uploadDir . $filename;
        }

        $data = [

            'employee_id'             => $employeeId,
            'subject'                 => trim($_POST['subject']),
            'description'             => trim($_POST['description']),

            // Defaults
            'status'                  => 'pending',
            'resolution_of_complaint' => null,

            'priority'                => $_POST['priority'],
            'category'                => trim($_POST['category']),
            'anonymous'               => (int) $_POST['anonymous'],
            'attachment_path'         => $attachmentPath,
            'confidential'            => (int) $_POST['confidential'],

            // EER will update these later
            'action_taken'            => null,
            'satisfaction_rating'     => null,
            'satisfaction_comment'    => null,
            'resolved_at'             => null,
            'escalation_level'        => null,
            'escalation_reason'       => null,

            'created_by_user_id'      => $userId,

            // Payroll-related fields
            'payslip_id'              => null,
            'gross_pay'               => null,
            'total_deductions'        => null,
            'net_pay'                 => null,
            'payslip_information'     => null,
        ];

        if ($this->grievanceModel->create($data)) {

            $_SESSION['success'] = "Grievance submitted successfully.";
        } else {

            $_SESSION['error'] = "Unable to submit grievance.";
        }

        Helper::redirect('index.php?url=employee-grievance');
    }
}
