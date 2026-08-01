<?php
/**
 * Incident Controller
 * Handles incident-related HTTP requests and business logic
 */

require_once __DIR__ . '/../models/Incident.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/FileUpload.php';

class IncidentController
{
    private Incident $incidentModel;
    private Employee $employeeModel;
    private FileUpload $fileUploadModel;

    public function __construct()
    {
        $this->incidentModel = new Incident();
        $this->employeeModel = new Employee();
        $this->fileUploadModel = new FileUpload();
    }

    /**
     * Handle create incident request
     */
    public function create(): array
    {
        try {
            // Validate required fields
            $required = ['title', 'description', 'incident_date', 'incident_type', 'severity'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    return ['success' => false, 'message' => "Field '{$field}' is required"];
                }
            }

            // Get reporter info FROM lc_session
            $reporterId = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? null;
            $reporter = null;
            if ($reporterId) {
                $reporter = $this->employeeModel->getById($reporterId);
            }

            // Handle respondent name and IDs
            $respondentIds = $_POST['respondent_id'] ?? [];
            if (!is_array($respondentIds)) $respondentIds = [$respondentIds];
            
            $respondentNames = [];
            foreach ($respondentIds as $rid) {
                $resp = $this->employeeModel->getById((int)$rid);
                if ($resp) {
                    $respondentNames[] = $resp['full_name'];
                }
            }
            
            $respondentDbId = !empty($respondentIds) ? (int) $respondentIds[0] : null;
            $respondentName = !empty($respondentNames) ? implode(', ', $respondentNames) : null;
            $primaryRespondent = $respondentDbId ? $this->employeeModel->getById($respondentDbId) : null;

            // Generate incident ID
            $incidentId = $this->incidentModel->generateIncidentId();

            // Prepare incident data
            $incidentTime = null;
            if (!empty($_POST['incident_time_hour']) && !empty($_POST['incident_time_minute'])) {
                $incidentTime = $_POST['incident_time_hour'] . ':' . $_POST['incident_time_minute'];
            }
            
            $data = [
                'incident_id' => $incidentId,
                'reporter_id' => $reporterId,
                'reporter_employee_no' => $reporter['employee_no'] ?? null,
                'reporter_name' => $reporter['full_name'] ?? ($_SESSION['user']['name'] ?? 'Reporter'),
                'reporter_category_id' => $reporter['department'] ?? null,
                'reporter_position_id' => $reporter['position'] ?? null,
                'reporter_contact' => $_POST['reporter_contact'] ?? null,
                'reporter_role' => $_POST['reporter_role'] ?? 'reporter',
                'reporter_type' => $_POST['reporter_type'] ?? 'employee',
                'respondent_id' => $respondentDbId,
                'respondent_employee_no' => $primaryRespondent ? ($primaryRespondent['employee_no'] ?? null) : null,
                'respondent_name' => $respondentName,
                'respondent_category_id' => $primaryRespondent['department'] ?? null,
                'respondent_position_id' => $primaryRespondent['position'] ?? null,
                'respondent_relationship' => $_POST['respondent_relationship'] ?? 'co_worker',
                'incident_type' => $_POST['incident_type'],
                'type' => $_POST['type'] ?? 'other',
                'severity' => $_POST['severity'],
                'incident_date' => $_POST['incident_date'],
                'incident_time' => $incidentTime,
                'location' => $_POST['location'] ?? null,
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'status' => 'submitted',
                'assigned_to' => !empty($_POST['assigned_to']) ? (int) $_POST['assigned_to'] : null,
                'created_by' => $reporterId
            ];

            // Create incident
            $incidentDbId = $this->incidentModel->create($data);

            // Handle file uploads
            if (!empty($_FILES['evidence'])) {
                $uploadedFiles = $this->fileUploadModel->uploadMultiple($_FILES['evidence']);
                $uploadedBy = $_SESSION['user']['id'] ?? null;
                foreach ($uploadedFiles as $file) {
                    if ($file['success']) {
                        $this->incidentModel->addEvidence($incidentDbId, array_merge($file, [
                            'uploaded_by' => $uploadedBy,
                        ]));
                    }
                }
            }

            return [
                'success' => true,
                'message' => 'Incident reported successfully',
                'incident_id' => $incidentDbId,
                'incident_reference' => $incidentId
            ];

        } catch (Exception $e) {
            error_log("Error creating incident: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while creating the incident'];
        }
    }

    /**
     * Handle update incident request
     */
    public function update(int $id): array
    {
        try {
            $incident = $this->incidentModel->getById($id);
            if (!$incident) {
                return ['success' => false, 'message' => 'Incident not found'];
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

            // Handle incident_time from hour and minute selects
            if (!empty($_POST['incident_time_hour']) && !empty($_POST['incident_time_minute'])) {
                $data['incident_time'] = $_POST['incident_time_hour'] . ':' . $_POST['incident_time_minute'];
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
                // Handle file uploads
                if (!empty($_FILES['evidence'])) {
                    $uploadedFiles = $this->fileUploadModel->uploadMultiple($_FILES['evidence']);
                    $uploadedBy = $_SESSION['user']['id'] ?? null;
                    foreach ($uploadedFiles as $file) {
                        if ($file['success']) {
                            $this->incidentModel->addEvidence($id, array_merge($file, [
                                'uploaded_by' => $uploadedBy,
                            ]));
                        }
                    }
                }
                return ['success' => true, 'message' => 'Incident updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update incident'];
            }

        } catch (Exception $e) {
            error_log("Error updating incident: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while updating the incident'];
        }
    }

    /**
     * Handle delete incident request
     */
    public function delete(int $id): array
    {
        try {
            $incident = $this->incidentModel->getById($id);
            if (!$incident) {
                return ['success' => false, 'message' => 'Incident not found'];
            }

            $result = $this->incidentModel->delete($id);

            if ($result) {
                return ['success' => true, 'message' => 'Incident deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete incident'];
            }

        } catch (Exception $e) {
            error_log("Error deleting incident: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while deleting the incident'];
        }
    }

    /**
     * Get incident by ID
     */
    public function getById(int $id): array
    {
        try {
            $incident = $this->incidentModel->getById($id);
            if (!$incident) {
                return ['success' => false, 'message' => 'Incident not found'];
            }

            return ['success' => true, 'data' => $incident];

        } catch (Exception $e) {
            error_log("Error getting incident: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while fetching the incident'];
        }
    }

    /**
     * Get all incidents with filters
     */
    public function getAll(): array
    {
        try {
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

            return [
                'success' => true,
                'data' => $incidents,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ];

        } catch (Exception $e) {
            error_log("Error getting incidents: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while fetching incidents'];
        }
    }

    /**
     * Get incident statistics
     */
    public function getStatistics(): array
    {
        try {
            $stats = $this->incidentModel->getStatistics();
            return ['success' => true, 'data' => $stats];

        } catch (Exception $e) {
            error_log("Error getting incident statistics: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while fetching statistics'];
        }
    }

    /**
     * Search employees for respondent selection
     */
    public function searchEmployees(): array
    {
        try {
            $query = $_GET['query'] ?? '';
            if (empty($query)) {
                return ['success' => false, 'message' => 'Search query is required'];
            }

            $employees = $this->employeeModel->search($query, 10);
            return ['success' => true, 'data' => $employees];

        } catch (Exception $e) {
            error_log("Error searching employees: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while searching employees'];
        }
    }

    /**
     * Get HR employees for assignment
     */
    public function getHREmployees(): array
    {
        try {
            $employees = $this->employeeModel->getHREmployees();
            return ['success' => true, 'data' => $employees];

        } catch (Exception $e) {
            error_log("Error getting HR employees: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while fetching HR employees'];
        }
    }
}
