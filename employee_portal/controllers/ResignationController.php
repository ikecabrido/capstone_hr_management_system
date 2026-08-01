<?php

require_once __DIR__ . '/../models/Resignation.php';

class ResignationController
{
    public function index(): void
    {
        $user = $_SESSION['user'] ?? null;
        $employeeId = $user['employee_id'] ?? null;
        $resignations = $employeeId ? (new Resignation())->forEmployee($employeeId) : [];
        $title = 'Resignation Requests';
        $content = __DIR__ . '/../views/resignation/main-content.php';
        require __DIR__ . '/../layout.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method.';
            header('Location: index.php?url=resignation-index');
            exit;
        }

        $user = $_SESSION['user'] ?? null;
        $employeeId = $user['employee_id'] ?? null;
        $required = ['resignation_type', 'reason', 'notice_date', 'last_working_date'];

        if (!$employeeId) {
            $_SESSION['error'] = 'Your employee profile is not linked to this account.';
            header('Location: index.php?url=resignation-index');
            exit;
        }

        foreach ($required as $field) {
            if (empty(trim((string)($_POST[$field] ?? '')))) {
                $_SESSION['error'] = "Field '$field' is required.";
                header('Location: index.php?url=resignation-index');
                exit;
            }
        }

        if (!in_array($_POST['resignation_type'], ['voluntary', 'involuntary'], true)) {
            $_SESSION['error'] = 'Invalid resignation type.';
            header('Location: index.php?url=resignation-index');
            exit;
        }

        try {
            $id = (new Resignation())->create([
                'employee_id' => $employeeId,
                'resignation_type' => $_POST['resignation_type'],
                'reason' => trim($_POST['reason']),
                'notice_date' => $_POST['notice_date'],
                'last_working_date' => $_POST['last_working_date'],
                'comments' => trim($_POST['comments'] ?? ''),
                'submitted_by' => $user['id']
            ]);

            $_SESSION['success'] = "Resignation request #$id submitted with pending status.";
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'Unable to submit the resignation request.';
        }

        header('Location: index.php?url=resignation-index');
        exit;
    }
}
