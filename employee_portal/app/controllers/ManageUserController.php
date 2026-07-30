<?php
require_once __DIR__ . '/../models/Users.php';
class ManageUserController
{
    private $userModel;
    public function __construct()
    {
        $this->userModel = new User();
    }
    public function adminIndex()
    {
        $currentUserId = $_SESSION['user_id'];
        $users = $this->userModel->filterSelf($currentUserId);

        $title = "User Management";

        $content = __DIR__ . '/../views/admin/user-management/main-content.php';
        require __DIR__ . '/../views/admin/index.php';
    }
    public function create()
    {
        try {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $checkEmail = $this->userModel->findByEmail($email);
            if (!empty($checkEmail)) {
                $_SESSION['error'] = "Email already exists...";
                Helper::redirect('index.php?url=admin-manage-user');
                exit;
            }

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );
            $data = [
                'role' => $_POST['role'],
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $hashedPassword
            ];

            if (!$this->userModel->create($data)) {
                throw new Exception("Failed to save the record to the database.");
            }

            $_SESSION['success'] = "New user created successfully.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            error_log($e->getMessage());
        }
        header('Location: index.php?url=admin-manage-user');
        exit;
    }
    public function update()
    {
        $id = $_POST['id'] ?? null;
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');

        try {

            if (empty($id) || empty($username)) {
                throw new Exception("Username is required.");
            }

            if ($this->userModel->update($id, $username, $email)) {

                $_SESSION['success'] = "User updated successfully.";
            } else {

                $_SESSION['error'] = "Failed to update user.";
            }
        } catch (Exception $e) {

            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: index.php?url=admin-manage-user");
        exit;
    }
    public function toggleAdmin()
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = "Invalid user.";
            header("Location: index.php?url=admin-manage-user");
            exit;
        }

        if ($this->userModel->toggleAdmin($id)) {
            $_SESSION['success'] = "Administrator privileges updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update administrator privileges.";
        }

        header("Location: index.php?url=admin-manage-user");
        exit;
    }
}
