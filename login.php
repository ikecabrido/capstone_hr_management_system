<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/auth/database.php';
require_once __DIR__ . '/auth/EmployeeAuth.php';
require_once __DIR__ . '/auth/Auth.php';

$response = [
    'success' => false,
    'message' => 'Invalid request',
    'redirect' => 'login_form.php',
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode($response);
    exit;
}

$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$qrToken = trim((string)($_POST['qr_token'] ?? ''));

if ($username === '' || $password === '') {
    $response['message'] = 'Please enter username and password';
    echo json_encode($response);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    // Try employee authentication first
    $employeeAuth = new EmployeeAuth();
    $employeeData = $employeeAuth->authenticate($username, $password);

    if ($employeeData && is_array($employeeData) && !isset($employeeData['error'])) {
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $employeeData['id'] ?? null,
            'employee_id' => $employeeData['employee_id'] ?? null,
            'employee_no' => $employeeData['employee_no'] ?? '',
            'username' => $employeeData['username'] ?? $username,
            'name' => $employeeData['name'] ?? '',
            'full_name' => $employeeData['full_name'] ?? '',
            'first_name' => $employeeData['first_name'] ?? '',
            'last_name' => $employeeData['last_name'] ?? '',
            'email' => $employeeData['email'] ?? '',
            'department' => $employeeData['department'] ?? '',
            'position' => $employeeData['position'] ?? '',
            'role' => $employeeData['role'] ?? 'employee',
            'theme' => 'light',
            'is_employee_auth' => true,
            'department_access' => $employeeData['department_access'] ?? ['employee_portal'],
            'redirect_page' => $employeeData['redirect_page'] ?? 'employee_portal/employee_portal.php',
        ];

        if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(16));
        }

        $response['success'] = true;
        $response['message'] = 'Login successful';
        $response['redirect'] = $employeeData['redirect_page'] ?? 'employee_portal/employee_portal.php';
        echo json_encode($response);
        exit;
    } elseif ($employeeData && isset($employeeData['error'])) {
        $response['message'] = $employeeData['error'];
        echo json_encode($response);
        exit;
    }

    // Fallback: users table authentication
    $auth = new Auth();
    if ($auth->login($username, $password)) {
        session_regenerate_id(true);

        $user = $auth->user();
        $redirectPage = 'router.php';

        if (!empty($qrToken)) {
            $redirectPage = 'qr_attendance.php?qr_token=' . urlencode($qrToken);
        } elseif (!empty($user['redirect_page'])) {
            $redirectPage = $user['redirect_page'];
        }

        $response['success'] = true;
        $response['message'] = 'Login successful';
        $response['redirect'] = $redirectPage;
        echo json_encode($response);
        exit;
    }

    $response['message'] = 'Invalid username or password';
    echo json_encode($response);
    exit;

} catch (Throwable $e) {
    error_log('Login error: ' . $e->getMessage());
    $response['message'] = 'System error. Please try again.';
    echo json_encode($response);
    exit;
}
