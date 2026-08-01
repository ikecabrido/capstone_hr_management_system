<?php
/**
 * Disciplinary Action Controller
 * Handles disciplinary action-related HTTP requests and business logic
 */

require_once __DIR__ . '/../models/DisciplinaryAction.php';
require_once __DIR__ . '/../models/Incident.php';
require_once __DIR__ . '/../models/Employee.php';

class DisciplinaryController
{
    private DisciplinaryAction $disciplinaryModel;
    private Incident $incidentModel;
    private Employee $employeeModel;

    public function __construct()
    {
        $this->disciplinaryModel = new DisciplinaryAction();
        $this->incidentModel = new Incident();
        $this->employeeModel = new Employee();
    }

    /**
     * Handle create disciplinary action request
     */
    public function create(): array
    {
        try {
            // Validate required fields
            $required = ['incident_id', 'employee_id', 'action_type', 'reason'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    return ['success' => false, 'message' => "Field '{$field}' is required"];
                }
            }

            $incidentId = (int) $_POST['incident_id'];
            $employeeId = (int) $_POST['employee_id'];

            // Verify incident exists
            $incident = $this->incidentModel->getById($incidentId);
            if (!$incident) {
                return ['success' => false, 'message' => 'Incident not found'];
            }

            // Verify employee exists
            $employee = $this->employeeModel->getById($employeeId);
            if (!$employee) {
                return ['success' => false, 'message' => 'Employee not found'];
            }

            // Generate action reference
            $actionReference = $this->disciplinaryModel->generateActionReference();

            // Get issuer info FROM lc_session
            $issuerId = $_SESSION['user_id'] ?? null;

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

            return [
                'success' => true,
                'message' => 'Disciplinary action created successfully',
                'action_id' => $actionId,
                'action_reference' => $actionReference
            ];

        } catch (Exception $e) {
            error_log("Error creating disciplinary action: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while creating the disciplinary action'];
        }
    }

    /**
     * Handle update disciplinary action request
     */
    public function update(int $id): array
    {
        try {
            $action = $this->disciplinaryModel->getById($id);
            if (!$action) {
                return ['success' => false, 'message' => 'Disciplinary action not found'];
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
                return ['success' => true, 'message' => 'Disciplinary action updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update disciplinary action'];
            }

        } catch (Exception $e) {
            error_log("Error updating disciplinary action: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while updating the disciplinary action'];
        }
    }

    /**
     * Handle delete disciplinary action request
     */
    public function delete(int $id): array
    {
        try {
            $action = $this->disciplinaryModel->getById($id);
            if (!$action) {
                return ['success' => false, 'message' => 'Disciplinary action not found'];
            }

            $result = $this->disciplinaryModel->delete($id);

            if ($result) {
                return ['success' => true, 'message' => 'Disciplinary action deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete disciplinary action'];
            }

        } catch (Exception $e) {
            error_log("Error deleting disciplinary action: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while deleting the disciplinary action'];
        }
    }

    /**
     * Get disciplinary action by ID
     */
    public function getById(int $id): array
    {
        try {
            $action = $this->disciplinaryModel->getById($id);
            if (!$action) {
                return ['success' => false, 'message' => 'Disciplinary action not found'];
            }

            return ['success' => true, 'data' => $action];

        } catch (Exception $e) {
            error_log("Error getting disciplinary action: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while fetching the disciplinary action'];
        }
    }

    /**
     * Get all disciplinary actions with filters
     */
    public function getAll(): array
    {
        try {
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

            return [
                'success' => true,
                'data' => $actions,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ];

        } catch (Exception $e) {
            error_log("Error getting disciplinary actions: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while fetching disciplinary actions'];
        }
    }

    /**
     * Get disciplinary actions by incident
     */
    public function getByIncident(int $incidentId): array
    {
        try {
            $actions = $this->disciplinaryModel->getByIncident($incidentId);
            return ['success' => true, 'data' => $actions];

        } catch (Exception $e) {
            error_log("Error getting disciplinary actions by incident: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while fetching disciplinary actions'];
        }
    }

    /**
     * Get disciplinary actions by employee
     */
    public function getByEmployee(int $employeeId): array
    {
        try {
            $actions = $this->disciplinaryModel->getByEmployee($employeeId);
            return ['success' => true, 'data' => $actions];

        } catch (Exception $e) {
            error_log("Error getting disciplinary actions by employee: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while fetching disciplinary actions'];
        }
    }

    /**
     * Get disciplinary action statistics
     */
    public function getStatistics(): array
    {
        try {
            $stats = $this->disciplinaryModel->getStatistics();
            return ['success' => true, 'data' => $stats];

        } catch (Exception $e) {
            error_log("Error getting disciplinary action statistics: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while fetching statistics'];
        }
    }
}
