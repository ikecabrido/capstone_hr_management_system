<?php

require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../core/Helper.php';
require_once __DIR__ . '/../core/AuditLog.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController
{
    private $userModel;
    private $auditLog;
    private $employeeModel;
    public function __construct()
    {
        $this->userModel = new User();
        $this->auditLog  = new AuditLog();
        $this->employeeModel = new Employee();
    }
    public function login()
    {
        Session::start();

        try {
            $employee_no = Helper::sanitize($_POST['employee_no'] ?? '');
            $password   = trim($_POST['password'] ?? '');

            if (empty($employee_no) || empty($password)) {
                throw new Exception("Please fill in all fields");
            }

            $user = $this->userModel->login($employee_no);

            if (!$user || empty($user['user_id'])) {
                throw new Exception("Invalid credentials");
            }

            if (!password_verify($password, $user['password'])) {
                throw new Exception("Invalid credentials");
            }

            Session::set('user_id', $user['user_id']);
            Session::set('employee_no', $user['employee_no']);
            Session::set('username', $user['username']);
            Session::set('role', $user['role']);
            Session::set('full_name', $user['full_name']);
            Session::set('success', "Login successful!");

            Helper::redirect('index.php?url=dashboard');
        } catch (Exception $e) {
            Session::set('error', $e->getMessage());
            Helper::redirect('index.php?url=auth-index');
        }
    }
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            Session::start();
        }
        $user_id = $_SESSION['user'] ?? null;

        if (!empty($user_id)) {
            $this->auditLog->log('LOGOUT', $user_id, null, null, [], 'SUCCESS');
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        Helper::redirect('index.php');
        exit;
    }
    public function index()
    {
        $title = "Employee Portal Login";
        require __DIR__ . '/../views/auth/login.php';
    }
    public static function hasRole($role)
    {
        Session::start();

        // Check time_attendance session format
        $sessionRole = Session::get('role');
        if ($sessionRole === $role) {
            return true;
        }

        // Check global login session format
        if (isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['role'])) {
            return $_SESSION['user']['role'] === $role;
        }

        return false;
    }
    public static function getCurrentUserId()
    {
        Session::start();
        return Session::get('user_id');
    }
    public static function requireAuth()
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /index.php');
            exit();
        }

        return $_SESSION['user_id'];
    }
    public function checkUserEmployee($user_id)
    {
        $employee = $this->employeeModel->findByUserId($user_id);

        if (!$user_id) {
            die('User not logged in.');
        }

        if (!$employee) {
            Session::set('error', 'Employee data not found');
            header("Location: index.php?url=auth-index");
            exit;
        }

        return $employee;
    }
    public function send()
    {
        Session::start();

        try {

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Helper::redirect('index.php?url=auth-index');
                exit;
            }

            $email = Helper::sanitize($_POST['email'] ?? '');

            // Validate email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Please enter a valid Gmail address.');
            }

            // Only allow Gmail
            if (!str_ends_with(strtolower($email), '@gmail.com')) {
                throw new Exception('Please enter a valid Gmail address.');
            }

            // Find user in users.email
            $user = $this->userModel->findByEmail($email);

            if (!$user) {
                throw new Exception(
                    'No account was found with that Gmail address.'
                );
            }

            // Generate secure reset token
            $token = bin2hex(random_bytes(32));

            // Token expires in 1 hour
            $expires = date(
                'Y-m-d H:i:s',
                time() + 3600
            );

            // Save token
            $saved = $this->userModel->savePasswordResetToken(
                $user['id'],
                $token,
                $expires
            );

            if (!$saved) {
                throw new Exception(
                    'Unable to process password reset. Please try again.'
                );
            }

            // Reset password URL
            $resetLink =
                'http://localhost/capstone_hr_management_system/employee_portal/index.php'
                . '?url=auth-reset-password&token='
                . urlencode($token);

            // PHPMailer
            $mail = new PHPMailer(true);

            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) $_ENV['MAIL_PORT'];

            // Sender
            $mail->setFrom(
                $_ENV['MAIL_FROM_ADDRESS'],
                $_ENV['MAIL_FROM_NAME']
            );

            // Recipient
            $mail->addAddress($email);

            // HTML email
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset - HR Employee Portal';

            $mail->Body = '
            <div style="
                font-family: Arial, sans-serif;
                max-width: 600px;
                margin: 0 auto;
                padding: 30px;
                border: 1px solid #ddd;
                border-radius: 10px;
            ">

                <h2 style="color: #333;">
                    Password Reset Request
                </h2>

                <p>Hello ' . htmlspecialchars($user['full_name']) . ',</p>

                <p>
                    We received a request to reset the password
                    for your HR Employee Portal account.
                </p>

                <p>
                    Click the button below to reset your password:
                </p>

                <div style="text-align: center; margin: 30px 0;">

                    <a href="' . htmlspecialchars($resetLink) . '"
                       style="
                            background-color: #007bff;
                            color: #ffffff;
                            padding: 12px 25px;
                            text-decoration: none;
                            border-radius: 6px;
                            display: inline-block;
                       ">
                        Reset Password
                    </a>

                </div>

                <p>
                    This link will expire in
                    <strong>1 hour</strong>.
                </p>

                <p>
                    If you did not request a password reset,
                    you can safely ignore this email.
                </p>

                <hr>

                <p style="color: #777; font-size: 12px;">
                    HR Employee Portal<br>
                    This is an automated email.
                    Please do not reply.
                </p>

            </div>
        ';

            // Plain text fallback
            $mail->AltBody =
                "Password Reset Request\n\n" .
                "Hello {$user['full_name']},\n\n" .
                "Click this link to reset your password:\n" .
                $resetLink .
                "\n\nThis link will expire in 1 hour.";

            // Send
            $mail->send();

            Session::set(
                'success',
                'A password reset link has been sent to your Gmail address.'
            );
        } catch (Exception $e) {

            // Log technical error
            error_log(
                'Password Reset Error: ' . $e->getMessage()
            );

            Session::set(
                'error',
                $e->getMessage()
            );
        }

        Helper::redirect('index.php?url=auth-index');
        exit;
    }
    public function resetPassword()
    {
        Session::start();

        $token = $_GET['token'] ?? '';

        if (empty($token)) {

            Session::set(
                'error',
                'Invalid password reset link.'
            );

            Helper::redirect('index.php?url=auth-index');
            exit;
        }

        $user = $this->userModel->findByResetToken($token);

        if (!$user) {

            Session::set(
                'error',
                'This password reset link is invalid or has expired.'
            );

            Helper::redirect('index.php?url=auth-index');
            exit;
        }

        $title = "Reset Password";

        $content =
            __DIR__ . '/../views/auth/reset-password.php';

        require __DIR__ . '/../views/auth/index.php';
    }

    public function updatePassword()
    {
        Session::start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('index.php?url=auth-index');
            exit;
        }

        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        // Check token
        if (empty($token)) {
            Session::set(
                'error',
                'Invalid password reset request.'
            );

            Helper::redirect('index.php?url=auth-index');
            exit;
        }

        // Check password fields
        if (empty($password) || empty($passwordConfirmation)) {
            Session::set(
                'error',
                'Please fill in all fields.'
            );

            Helper::redirect(
                'index.php?url=auth-reset-password&token=' . urlencode($token)
            );
            exit;
        }

        // Check password length
        if (strlen($password) < 8) {
            Session::set(
                'error',
                'Password must be at least 8 characters.'
            );

            Helper::redirect(
                'index.php?url=auth-reset-password&token=' . urlencode($token)
            );
            exit;
        }

        // Check passwords match
        if ($password !== $passwordConfirmation) {
            Session::set(
                'error',
                'Passwords do not match.'
            );

            Helper::redirect(
                'index.php?url=auth-reset-password&token=' . urlencode($token)
            );
            exit;
        }

        // Verify token
        $user = $this->userModel->findByResetToken($token);

        if (!$user) {
            Session::set(
                'error',
                'This password reset link is invalid or has expired.'
            );

            Helper::redirect('index.php?url=auth-index');
            exit;
        }

        // Hash password
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        // Update password
        $updated = $this->userModel->updatePassword(
            $user['id'],
            $hashedPassword
        );

        if (!$updated) {
            Session::set(
                'error',
                'Unable to reset your password. Please try again.'
            );

            Helper::redirect(
                'index.php?url=auth-reset-password&token=' . urlencode($token)
            );
            exit;
        }

        // Clear reset token
        $this->userModel->clearPasswordResetToken($user['id']);

        Session::set(
            'success',
            'Your password has been reset successfully. You can now log in.'
        );

        Helper::redirect('index.php?url=auth-index');
        exit;
    }
}
