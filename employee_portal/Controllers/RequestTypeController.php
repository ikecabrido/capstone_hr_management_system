<?php
require_once __DIR__ . '/../models/RequestType.php';

class RequestTypeController
{
    public function index()
    {
        $model = new RequestType();
        $requestTypes = $model->all();

        $GLOBALS['page_content'] =
            __DIR__ . '/../views/request-types/main-content.php';

        require __DIR__ . '/../views/request-types/index.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Invalid request method.";
            header("Location: index.php?url=request-types");
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        $requires_attachment = isset($_POST['requires_attachment']) ? 1 : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '') {
            $_SESSION['error'] = "Request type name is required.";
            header("Location: index.php?url=request-types");
            exit;
        }

        try {
            $model = new RequestType();

            $data = [
                'name' => $name,
                'description' => $description,
                'icon' => $icon ?: null,
                'requires_attachment' => $requires_attachment,
                'is_active' => $is_active,
            ];

            $inserted = $model->create($data);
            if ($inserted) {
                $_SESSION['success'] = "Request type created successfully.";
            } else {
                $_SESSION['error'] = "Failed to create request type.";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "An error occurred while creating request type.";
        }

        header("Location: index.php?url=request-types");
        exit;
    }
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Invalid request method.";
            header("Location: index.php?url=request-types");
            exit;
        }

        if (empty($_POST['id'])) {
            $_SESSION['error'] = "Request type ID is required.";
            header("Location: index.php?url=request-types");
            exit;
        }

        try {
            $model = new RequestType();

            $data = [
                'id' => (int) $_POST['id'],
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'icon' => trim($_POST['icon'] ?? null),
                'requires_attachment' => isset($_POST['requires_attachment']) ? 1 : 0,
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
            ];

            if (empty($data['name'])) {
                throw new Exception("Request type name cannot be empty.");
            }

            $model->update($data);

            $_SESSION['success'] = "Request type updated successfully.";
        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "Failed to update request type: " . $e->getMessage();
        }

        header("Location: index.php?url=request-types");
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Invalid request method.";
            header("Location: index.php?url=request-types");
            exit;
        }

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

        header("Location: index.php?url=request-types");
        exit;
    }
}
