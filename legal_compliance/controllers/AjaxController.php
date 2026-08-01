<?php
/**
 * Ajax Controller
 * Handles AJAX requests for incident reporting module
 */

require_once __DIR__ . '/../models/Incident.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/DisciplinaryAction.php';
require_once __DIR__ . '/../models/FileUpload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AjaxController
{
    private Incident $incidentModel;
    private Employee $employeeModel;
    private DisciplinaryAction $disciplinaryModel;
    private FileUpload $fileUploadModel;

    public function __construct()
    {
        $this->incidentModel = new Incident();
        $this->employeeModel = new Employee();
        $this->disciplinaryModel = new DisciplinaryAction();
        $this->fileUploadModel = new FileUpload();
    }

    /**
     * Logged-in user id (employees.employee_id or users.id depending on auth path).
     */
    private function getCurrentUserId(): ?int
    {
        if (isset($_SESSION['user']['id']) && is_numeric($_SESSION['user']['id'])) {
            return (int) $_SESSION['user']['id'];
        }
        if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
            return (int) $_SESSION['user_id'];
        }
        return null;
    }

    /**
     * Handle AJAX request
     */
    public function handleRequest(): void
    {
        header('Content-Type: application/json');

        $action = $_GET['action'] ?? $_POST['action'] ?? '';

        try {
            switch ($action) {
                case 'get_incidents':
                    $this->getIncidents();
                    break;
                case 'get_incident':
                    $this->getIncident();
                    break;
                case 'create_incident':
                    $this->createIncident();
                    break;
                case 'update_incident':
                case 'update_incident_status':
                    $this->updateIncident();
                    break;
                case 'delete_incident':
                    $this->deleteIncident();
                    break;
                case 'get_incident_statistics':
                    $this->getIncidentStatistics();
                    break;
                case 'search_employees':
                    $this->searchEmployees();
                    break;
                case 'get_hr_employees':
                    $this->getHREmployees();
                    break;
                case 'get_employees':
                    $this->getEmployees();
                    break;
                case 'get_disciplinary_actions':
                    $this->getDisciplinaryActions();
                    break;
                case 'get_disciplinary_action':
                    $this->getDisciplinaryAction();
                    break;
                case 'create_disciplinary_action':
                    $this->createDisciplinaryAction();
                    break;
                case 'update_disciplinary_action':
                    $this->updateDisciplinaryAction();
                    break;
                case 'delete_disciplinary_action':
                    $this->deleteDisciplinaryAction();
                    break;
                case 'get_disciplinary_statistics':
                    $this->getDisciplinaryStatistics();
                    break;
                case 'get_disciplinary_actions_by_incident':
                    $this->getDisciplinaryActionsByIncident();
                    break;
                case 'get_disciplinary_actions_by_employee':
                    $this->getDisciplinaryActionsByEmployee();
                    break;
                case 'get_incidents_by_employee':
                    $this->getIncidentsByEmployee();
                    break;
                default:
                    echo json_encode(['success' => false, 'message' => 'Invalid action']);
            }
        } catch (Throwable $e) {
            error_log("AjaxController error: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'An error occurred: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Get incidents with filters
     */
    private function getIncidents(): void
    {
        $filters = [];
        
        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        if (!empty($_GET['severity'])) {
            $filters['severity'] = $_GET['severity'];
        }
        if (!empty($_GET['type'])) {
            $filters['type'] = $_GET['type'];
        }
        if (!empty($_GET['incident_type'])) {
            $filters['incident_type'] = $_GET['incident_type'];
        }
        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }
        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }
        if (!empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        if (!empty($_GET['assigned_to'])) {
            $filters['assigned_to'] = (int) $_GET['assigned_to'];
        }
        if (!empty($_GET['reporter_id'])) {
            $filters['reporter_id'] = (int) $_GET['reporter_id'];
        }
        if (!empty($_GET['respondent_id'])) {
            $filters['respondent_id'] = (int) $_GET['respondent_id'];
        }

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
        $offset = ($page - 1) * $limit;

        $incidents = $this->incidentModel->getAll($filters, $limit, $offset);
        $total = $this->incidentModel->count($filters);

        echo json_encode([
            'success' => true,
            'data' => $incidents,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * Get single incident
     */
    private function getIncident(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $incidentId = isset($_GET['incident_id']) ? $_GET['incident_id'] : '';
        
        if (!$id && !$incidentId) {
            echo json_encode(['success' => false, 'message' => 'Incident ID is required']);
            return;
        }

        if ($incidentId) {
            $incident = $this->incidentModel->getByIncidentId($incidentId);
        } else {
            $incident = $this->incidentModel->getById($id);
        }
        
        if ($incident) {
            $incident['evidence'] = $this->incidentModel->getEvidenceByIncidentId((int) $incident['id']);
            echo json_encode(['success' => true, 'data' => $incident]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incident not found']);
        }
    }

    /**
     * Create incident
     */
    private function createIncident(): void
    {
        // Validate required fields
        $required = ['title', 'description', 'incident_date', 'incident_type', 'severity'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                echo json_encode(['success' => false, 'message' => "Field '{$field}' is required"]);
                return;
            }
        }

        $reporterIdSession = $this->getCurrentUserId();
        $reporter = null;
        if ($reporterIdSession) {
            $reporter = $this->employeeModel->getById($reporterIdSession);
        }
        // Only set FK reporter_id when this user exists in employees (avoids FK errors for lc_users-only sessions)
        $reporterId = $reporter ? $reporterIdSession : null;

        // Respondent(s): store all names for display; FK respondent_id is first selected employee only
        $respondentIds = [];
        $respondentNames = [];
        if (!empty($_POST['respondent_id'])) {
            $raw = $_POST['respondent_id'];
            $respondentIds = is_array($raw) ? $raw : [(int) $raw];
            $respondentIds = array_values(array_filter(array_map('intval', $respondentIds)));
            foreach ($respondentIds as $rid) {
                $respondentRow = $this->employeeModel->getById($rid);
                if ($respondentRow) {
                    $respondentNames[] = $respondentRow['full_name'];
                }
            }
        }
        $primaryRespondent = null;
        if (!empty($respondentIds)) {
            $primaryRespondent = $this->employeeModel->getById((int) $respondentIds[0]);
        }
        $respondentDbId = $primaryRespondent ? (int) $respondentIds[0] : null;
        $respondentName = $respondentNames !== [] ? implode(', ', $respondentNames) : null;

        // Generate incident ID
        $incidentId = $this->incidentModel->generateIncidentId();

        // Prepare incident data
        $data = [
            'incident_id' => $incidentId,
            'reporter_id' => $reporterId,
            'reporter_employee_no' => $reporter ? ($reporter['employee_no'] ?? null) : null,
            'reporter_name' => $reporter
                ? trim($reporter['full_name'] ?? '')
                : (string) ($_SESSION['user']['name'] ?? $_SESSION['user_name'] ?? 'User'),
            'reporter_category_id' => $reporter ? ($reporter['department'] ?? null) : null,
            'reporter_position_id' => $reporter ? ($reporter['position'] ?? null) : null,
            'reporter_contact' => $_POST['reporter_contact'] ?? null,
            'reporter_role' => $_POST['reporter_role'] ?? 'reporter',
            'reporter_type' => $_POST['reporter_type'] ?? 'employee',
            'respondent_id' => $respondentDbId,
            'respondent_employee_no' => $primaryRespondent ? ($primaryRespondent['employee_no'] ?? null) : null,
            'respondent_name' => $respondentName,
            'respondent_category_id' => $primaryRespondent ? ($primaryRespondent['department'] ?? null) : null,
                'respondent_position_id' => $primaryRespondent ? ($primaryRespondent['position'] ?? null) : null,
            'respondent_relationship' => $_POST['respondent_relationship'] ?? 'co_worker',
            'incident_type' => $_POST['incident_type'],
            'type' => $_POST['type'] ?? 'other',
            'severity' => $_POST['severity'],
            'incident_date' => $_POST['incident_date'],
            'incident_time' => $_POST['incident_time'] ?? null,
            'location' => $_POST['location'] ?? null,
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'status' => 'submitted',
            'assigned_to' => !empty($_POST['assigned_to']) ? (int) $_POST['assigned_to'] : null,
            'created_by' => $reporterId
        ];

        // Create incident
        $incidentDbId = $this->incidentModel->create($data);

        $evidenceWarnings = [];
        if (!empty($_FILES['evidence'])) {
            $uploadedFiles = $this->fileUploadModel->uploadMultiple($_FILES['evidence'], $this->getCurrentUserId());
            $uploadedBy = $this->getCurrentUserId();
            foreach ($uploadedFiles as $file) {
                if (!empty($file['success'])) {
                    $evId = $this->incidentModel->addEvidence($incidentDbId, array_merge($file, [
                        'uploaded_by' => $uploadedBy,
                    ]));
                    if ($evId === 0) {
                        $evidenceWarnings[] = ($file['file_name'] ?: 'File') . ': could not save evidence record (run legal_compliance/sql/lc_incident_evidence.sql if the table is missing).';
                    }
                } else {
                    $msg = $file['message'] ?? 'Upload failed';
                    if ($msg !== 'No file was uploaded') {
                        $evidenceWarnings[] = ($file['file_name'] ?: 'File') . ': ' . $msg;
                    }
                }
            }
        }

        $response = [
            'success' => true,
            'message' => 'Incident reported successfully',
            'incident_id' => $incidentDbId,
            'incident_reference' => $incidentId,
        ];
        if ($evidenceWarnings !== []) {
            $response['evidence_warnings'] = $evidenceWarnings;
            $response['message'] .= ' ' . implode(' ', $evidenceWarnings);
        }
        echo json_encode($response);
    }

    /**
     * Update incident
     */
    private function updateIncident(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Incident ID is required']);
            return;
        }

        $incident = $this->incidentModel->getById($id);
        if (!$incident) {
            echo json_encode(['success' => false, 'message' => 'Incident not found']);
            return;
        }

        // Prepare update data
        $data = [];
        $allowedFields = [
            'incident_type', 'type', 'severity', 'incident_date', 'incident_time',
            'location', 'title', 'description', 'status', 'assigned_to',
            'respondent_id', 'respondent_relationship'
        ];

        foreach ($allowedFields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = $_POST[$field];
            }
        }

        // Handle respondent update
        if (isset($_POST['respondent_id']) && !empty($_POST['respondent_id'])) {
            $respondentId = (int) $_POST['respondent_id'];
            $respondent = $this->employeeModel->getById($respondentId);
            if ($respondent) {
                $data['respondent_employee_id'] = $respondent['employee_no'];
                $data['respondent_name'] = $respondent['full_name'];
                $data['respondent_department'] = $respondent['department'];
                $data['respondent_position'] = $respondent['position'];
            }
        }

        // Set resolved_at if status is resolved
        if (isset($data['status']) && $data['status'] === 'resolved') {
            $data['resolved_at'] = date('Y-m-d H:i:s');
        }

        $result = $this->incidentModel->update($id, $data);

        if ($result) {
            $evidenceWarnings = [];
            if (!empty($_FILES['evidence'])) {
                $uploadedFiles = $this->fileUploadModel->uploadMultiple($_FILES['evidence'], $this->getCurrentUserId());
                $uploadedBy = $this->getCurrentUserId();
                foreach ($uploadedFiles as $file) {
                    if (!empty($file['success'])) {
                        $evId = $this->incidentModel->addEvidence($id, array_merge($file, [
                            'uploaded_by' => $uploadedBy,
                        ]));
                        if ($evId === 0) {
                            $evidenceWarnings[] = ($file['file_name'] ?: 'File') . ': could not save evidence record.';
                        }
                    } else {
                        $msg = $file['message'] ?? 'Upload failed';
                        if ($msg !== 'No file was uploaded') {
                            $evidenceWarnings[] = ($file['file_name'] ?: 'File') . ': ' . $msg;
                        }
                    }
                }
            }

            $response = ['success' => true, 'message' => 'Incident updated successfully'];
            if ($evidenceWarnings !== []) {
                $response['evidence_warnings'] = $evidenceWarnings;
                $response['message'] .= ' ' . implode(' ', $evidenceWarnings);
            }
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update incident']);
        }
    }

    /**
     * Delete incident
     */
    private function deleteIncident(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Incident ID is required']);
            return;
        }

        $incident = $this->incidentModel->getById($id);
        if (!$incident) {
            echo json_encode(['success' => false, 'message' => 'Incident not found']);
            return;
        }

        $result = $this->incidentModel->delete($id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Incident deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete incident']);
        }
    }

    /**
     * Get incident statistics
     */
    private function getIncidentStatistics(): void
    {
        $stats = $this->incidentModel->getStatistics();
        echo json_encode(['success' => true, 'data' => $stats]);
    }

    /**
     * Search employees
     */
    private function searchEmployees(): void
    {
        $query = $_GET['query'] ?? '';
        
        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Search query is required']);
            return;
        }

        $employees = $this->employeeModel->search($query, 10);
        echo json_encode(['success' => true, 'data' => $employees]);
    }

    /**
     * Get HR employees
     */
    private function getHREmployees(): void
    {
        $employees = $this->employeeModel->getHREmployees();
        echo json_encode(['success' => true, 'data' => $employees]);
    }

    /**
     * Get all active employees
     */
    private function getEmployees(): void
    {
        $employees = $this->employeeModel->getAll();
        echo json_encode(['success' => true, 'data' => $employees]);
    }

    /**
     * Get disciplinary actions with filters
     */
    private function getDisciplinaryActions(): void
    {
        $filters = [];
        
        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        if (!empty($_GET['action_type'])) {
            $filters['action_type'] = $_GET['action_type'];
        }
        if (!empty($_GET['incident_id'])) {
            $filters['incident_id'] = (int) $_GET['incident_id'];
        }
        if (!empty($_GET['employee_id'])) {
            $filters['employee_id'] = (int) $_GET['employee_id'];
        }
        if (!empty($_GET['issued_by'])) {
            $filters['issued_by'] = (int) $_GET['issued_by'];
        }
        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }
        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }
        if (!empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
        $offset = ($page - 1) * $limit;

        $actions = $this->disciplinaryModel->getAll($filters, $limit, $offset);
        $total = $this->disciplinaryModel->count($filters);

        echo json_encode([
            'success' => true,
            'data' => $actions,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * Get single disciplinary action
     */
    private function getDisciplinaryAction(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Disciplinary action ID is required']);
            return;
        }

        $action = $this->disciplinaryModel->getById($id);
        
        if ($action) {
            echo json_encode(['success' => true, 'data' => $action]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Disciplinary action not found']);
        }
    }

    /**
     * Create disciplinary action
     */
    private function createDisciplinaryAction(): void
    {
        // Validate required fields
        $required = ['incident_id', 'employee_id', 'action_type', 'reason'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                echo json_encode(['success' => false, 'message' => "Field '{$field}' is required"]);
                return;
            }
        }

        $incidentId = (int) $_POST['incident_id'];
        $employeeId = (int) $_POST['employee_id'];

        // Verify incident exists
        $incident = $this->incidentModel->getById($incidentId);
        if (!$incident) {
            echo json_encode(['success' => false, 'message' => 'Incident not found']);
            return;
        }

        // Verify employee exists
        $employee = $this->employeeModel->getById($employeeId);
        if (!$employee) {
            echo json_encode(['success' => false, 'message' => 'Employee not found']);
            return;
        }

        // Generate action reference
        $actionReference = $this->disciplinaryModel->generateActionReference();

        $issuerId = $this->getCurrentUserId();

        // Prepare disciplinary action data
        $data = [
            'action_reference' => $actionReference,
            'incident_id' => $incidentId,
            'employee_id' => $employeeId,
            'action_type' => $_POST['action_type'],
            'reason' => $_POST['reason'],
            'violation_description' => $_POST['violation_description'] ?? null,
            'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
            'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
            'duration_days' => !empty($_POST['duration_days']) ? (int) $_POST['duration_days'] : null,
            'is_active' => 1,
            'issued_by' => $issuerId,
            'document_path' => null,
            'status' => 'pending'
        ];

        // Handle file upload
        if (!empty($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->fileUploadModel->upload($_FILES['document'], 'disciplinary_documents');
            if ($uploadResult['success']) {
                $data['document_path'] = $uploadResult['file_path'];
            }
        }

        // Create disciplinary action
        $actionId = $this->disciplinaryModel->create($data);

        echo json_encode([
            'success' => true,
            'message' => 'Disciplinary action created successfully',
            'action_id' => $actionId,
            'action_reference' => $actionReference
        ]);
    }

    /**
     * Update disciplinary action
     */
    private function updateDisciplinaryAction(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Disciplinary action ID is required']);
            return;
        }

        $action = $this->disciplinaryModel->getById($id);
        if (!$action) {
            echo json_encode(['success' => false, 'message' => 'Disciplinary action not found']);
            return;
        }

        // Prepare update data
        $data = [];
        $allowedFields = [
            'action_type', 'reason', 'violation_description', 'start_date', 'end_date',
            'duration_days', 'is_active', 'status'
        ];

        foreach ($allowedFields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = $_POST[$field];
            }
        }

        // Handle file upload
        if (!empty($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->fileUploadModel->upload($_FILES['document'], 'disciplinary_documents');
            if ($uploadResult['success']) {
                $data['document_path'] = $uploadResult['file_path'];
            }
        }

        $result = $this->disciplinaryModel->update($id, $data);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Disciplinary action updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update disciplinary action']);
        }
    }

    /**
     * Delete disciplinary action
     */
    private function deleteDisciplinaryAction(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Disciplinary action ID is required']);
            return;
        }

        $action = $this->disciplinaryModel->getById($id);
        if (!$action) {
            echo json_encode(['success' => false, 'message' => 'Disciplinary action not found']);
            return;
        }

        $result = $this->disciplinaryModel->delete($id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Disciplinary action deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete disciplinary action']);
        }
    }

    /**
     * Get disciplinary action statistics
     */
    private function getDisciplinaryStatistics(): void
    {
        $stats = $this->disciplinaryModel->getStatistics();
        echo json_encode(['success' => true, 'data' => $stats]);
    }

    /**
     * Get disciplinary actions by incident
     */
    private function getDisciplinaryActionsByIncident(): void
    {
        $incidentId = isset($_GET['incident_id']) ? (int) $_GET['incident_id'] : 0;
        
        if (!$incidentId) {
            echo json_encode(['success' => false, 'message' => 'Incident ID is required']);
            return;
        }

        $actions = $this->disciplinaryModel->getByIncident($incidentId);
        echo json_encode(['success' => true, 'data' => $actions]);
    }

    /**
     * Get disciplinary actions by employee
     */
    private function getDisciplinaryActionsByEmployee(): void
    {
        $employeeId = isset($_GET['employee_id']) ? (int) $_GET['employee_id'] : 0;
        
        if (!$employeeId) {
            echo json_encode(['success' => false, 'message' => 'Employee ID is required']);
            return;
        }

        $actions = $this->disciplinaryModel->getByEmployee($employeeId);
        echo json_encode(['success' => true, 'data' => $actions]);
    }

    /**
     * Get incidents by employee (as respondent)
     */
    private function getIncidentsByEmployee(): void
    {
        $employeeId = isset($_GET['employee_id']) ? (int) $_GET['employee_id'] : 0;
        
        if (!$employeeId) {
            echo json_encode(['success' => false, 'message' => 'Employee ID is required']);
            return;
        }

        $incidents = $this->incidentModel->getByRespondent($employeeId);
        echo json_encode(['success' => true, 'data' => $incidents]);
    }
}

try {
    // Initialize and handle the AJAX request
    $controller = new AjaxController();
    $controller->handleRequest();
} catch (Throwable $e) {
    error_log("AjaxController init error: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred during initialization: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
