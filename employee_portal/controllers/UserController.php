<?php

require_once "../app/Models/User.php";

require_once "../app/Core/Controller.php";
require_once "../app/Core/Auth.php";
require_once "../app/Core/Validator.php";
require_once "../app/Core/Helpers.php";
require_once "../app/Core/Mailer.php";
require_once "../app/Core/EmailView.php";

class UserController extends Controller
{
    public function index()
    {
        Auth::requireAdmin();

        $userModel = new User();

        $users        = $userModel->all($_SESSION['user']['id']);
        $inactiveUsers = $userModel->allInactive();

        $this->view('admin/manage-users/index', [
            'title'         => 'Users Management | SEMSYS',
            'users'         => $users,
            'inactiveUsers' => $inactiveUsers
        ]);
    }

    public function create()
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=user-index");
            exit;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $isAdmin = ($_POST['role'] === '1') ? 1 : 0;

        $plainPassword = Helpers::randomPassword(10);

        $userModel = new User();
        $created = $userModel->create([
            'name'     => $name,
            'email'    => $email,
            'isAdmin'  => $isAdmin,
            'password' => password_hash($plainPassword, PASSWORD_DEFAULT)
        ]);

        if (!$created) {
            $_SESSION['error'] = "Email already exists.";
        } else {
            // Build email body
            $emailBody = EmailView::render('user-created-email', [
                'name'     => $name,
                'email'    => $email,
                'password' => $plainPassword,
                'role'     => $isAdmin == '1' ? 'Admin' : 'User'
            ]);

            // Send email
            Mailer::send($email, 'Your SEMSYS Account', $emailBody);

            $_SESSION['success'] = "User created successfully. Password sent via email.";
        }

        header("Location: index.php?url=user-index");
        exit;
    }

    public function update()
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=user-index");
            exit;
        }

        $id    = $_POST['id'] ?? null;
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role  = isset($_POST['role']) ? (int) $_POST['role'] : 0;

        if (!$id || !$name || !$email) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: index.php?url=user-index");
            exit;
        }

        if ($id == $_SESSION['user']['id']) {
            $_SESSION['error'] = "You cannot edit your own account.";
            header("Location: index.php?url=user-index");
            exit;
        }

        $userModel = new User();

        if (!$userModel->updateUser($id, $name, $email, $role)) {
            $_SESSION['error'] = "Email already exists.";
        } else {
            $_SESSION['success'] = "User updated successfully.";
        }

        header("Location: index.php?url=user-index");
        exit;
    }

    public function delete()
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=user-index");
            exit;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $_SESSION['error'] = "Invalid request.";
            header("Location: index.php?url=user-index");
            exit;
        }

        if ($id == $_SESSION['user']['id']) {
            $_SESSION['error'] = "You cannot deactivate your own account.";
            header("Location: index.php?url=user-index");
            exit;
        }

        $userModel = new User();

        if ($userModel->softDelete($id)) {
            $_SESSION['success'] = "User deactivated successfully.";
        } else {
            $_SESSION['error'] = "Failed to deactivate user.";
        }

        header("Location: index.php?url=user-index");
        exit;
    }

    public function activate()
    {
        Auth::requireAdmin();

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header("Location: index.php?url=user-index");
            exit;
        }

        (new User())->activate($id);

        $_SESSION['success'] = "User account activated.";
        header("Location: index.php?url=user-index");
        exit;
    }
}
