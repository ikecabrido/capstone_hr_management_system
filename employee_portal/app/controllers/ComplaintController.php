<?php

require_once __DIR__ . '/../models/Incidents.php';
require_once __DIR__ . '/../models/Employee.php';


class ComplaintController
{
    private $employeeModel;

    private $incidentModel;

    public function __construct()
    {
        $this->employeeModel = new Employee();

        $this->incidentModel = new Incidents();
    }

    public function index()
    {
        $employees = $this->employeeModel->getAll();

        $complaints = $this->incidentModel->getByUser($_SESSION['user_id']);

        $title = "Employee Complaint";
        $content = __DIR__ . '/../views/complaint/main-content.php';

        require __DIR__ . '/../views/employee-portal/index.php';
    }
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('index.php?url=employee-complaints');
            exit;
        }

        $userId     = $_SESSION['user_id'];
        $employee = $this->employeeModel->findByUserId($userId);
        $employeeId = $employee['employee_id'];

        // Logged-in employee
        $reporter = $this->employeeModel->find($employeeId);

        if (!$reporter) {
            $_SESSION['error'] = "Employee record not found.";
            Helper::redirect('index.php?url=employee-complaints');
            exit;
        }

        // Respondent (optional)
        $respondent = null;

        if (!empty($_POST['respondent_id'])) {
            $respondent = $this->employeeModel->find($_POST['respondent_id']);
        }

        // Generate incident number
        $incidentId = $this->generateIncidentId();

        $data = [

            'incident_id' => $incidentId,

            // Reporter
            'reporter_id'             => $reporter['employee_id'],
            'reporter_employee_id'    => $reporter['employee_code'],
            'reporter_name'           => trim($reporter['first_name'] . ' ' . $reporter['last_name']),
            'reporter_department'     => $reporter['department'],
            'reporter_position'       => $reporter['position'], // or position_title_enum
            'reporter_contact'        => $reporter['mobile_no'],
            'reporter_role'           => $_POST['reporter_role'],
            'reporter_type'           => 'employee',

            // Respondent
            'respondent_id'           => $respondent['employee_id'] ?? null,
            'respondent_employee_id'  => $respondent['employee_code'] ?? null,
            'respondent_name'         => $respondent
                ? trim($respondent['first_name'] . ' ' . $respondent['last_name'])
                : null,
            'respondent_department'   => $respondent['department'] ?? null,
            'respondent_position'     => $respondent['position'] ?? null, // or position_title_enum
            'respondent_relationship' => $_POST['respondent_relationship'] ?: null,

            // Complaint
            'incident_type' => trim($_POST['incident_type']),
            'type'          => $_POST['type'],
            'severity'      => 'medium', // default
            'incident_date' => $_POST['incident_date'],
            'incident_time' => !empty($_POST['incident_time']) ? $_POST['incident_time'] : null,
            'location'      => trim($_POST['location']),
            'title'         => trim($_POST['title']),
            'description'   => trim($_POST['description']),

            // Workflow
            'status'      => 'submitted',
            'assigned_to' => null,
            'created_by'  => $userId,
            'reported_by' => $userId,
            'resolved_at' => null,
        ];

        if ($this->incidentModel->create($data)) {

            $_SESSION['success'] = "Complaint submitted successfully.";
        } else {

            $_SESSION['error'] = "Unable to submit complaint.";
        }

        Helper::redirect('index.php?url=employee-complaint-index');
    }
    private function generateIncidentId()
    {
        $year = date('Y');

        $latest = $this->incidentModel->getLatestIncidentId($year);

        if ($latest) {

            $number = (int) substr($latest['incident_id'], -4) + 1;
        } else {

            $number = 1;
        }

        return sprintf('INC-%s-%04d', $year, $number);
    }
}
