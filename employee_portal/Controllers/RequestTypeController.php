<?php

require_once __DIR__ . '/../Models/RequestType.php';
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Core/Auth.php';

class RequestTypeController extends Controller
{
    public function index()
    {
        Auth::requireAdmin();

        $requestTypeModel = new RequestType();
        $requestTypes = $requestTypeModel->all();

        $this->view("admin/request-types/index", [
            'title' => 'Manage Request Types | HRMSYS',
            'requestTypes' => $requestTypes
        ]);
    }

    public function create()
    {
        Auth::requireAdmin();

        try {
            $model = new RequestType();

            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'icon' => $_POST['icon'] ?? null,
                'requires_attachment' => isset($_POST['requires_attachment']) ? 1 : 0,
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
            ];

            $model->create($data);

            $_SESSION['success'] = "Request type created successfully.";
        } catch (Exception $e) {

            error_log($e->getMessage());

            $_SESSION['error'] = "Failed to create request type.";
        }

        header("Location: index.php?url=admin-request-types");
        exit;
    }

    public function update()
    {
        Auth::requireAdmin();

        try {
            if (!isset($_POST['id'])) {
                throw new Exception("Invalid request type ID.");
            }

            $model = new RequestType();

            $data = [
                'id' => $_POST['id'],
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'icon' => $_POST['icon'],
                'requires_attachment' => isset($_POST['requires_attachment']) ? 1 : 0,
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
            ];

            $model->update($data);

            $_SESSION['success'] = "Request type updated successfully.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Failed to update request type: " . $e->getMessage();
        }

        header("Location: index.php?url=admin-request-types");
        exit;
    }

    public function delete()
    {
        Auth::requireAdmin(); 

        try {
            if (!isset($_POST['id'])) {
                throw new Exception("Request type ID is missing.");
            }

            $id = $_POST['id'];

            $requestTypeModel = new RequestType();
            $deleted = $requestTypeModel->delete($id);

            if ($deleted) {
                $_SESSION['success'] = "Request type deleted successfully.";
            } else {
                $_SESSION['error'] = "Request type could not be deleted.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }

        header("Location: index.php?url=admin-request-types");
        exit;
    }
}
