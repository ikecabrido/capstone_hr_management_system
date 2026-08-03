<?php
require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../models/Employee.php';
class ManageUserController
{
    private $userModel;
    private $employeeModel;
    public function __construct()
    {
        $this->userModel = new Users();
        $this->employeeModel = new Employee();
    }
    public function adminIndex()
    {
        $currentUserId = $_SESSION['user_id'];
        $ifEmployeeExist = $this->employeeModel->getByUserId($currentUserId);
        $users = $this->userModel->filterSelf($currentUserId);

        $title = "User Management";

        $content = __DIR__ . '/../views/admin/user-management/main-content.php';
        require __DIR__ . '/../views/admin/index.php';
    }
    public function create()
    {
        try {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $checkEmail = $this->userModel->findByEmail($email);

            if (!empty($checkEmail)) {
                $_SESSION['error'] = "Email already exists.";
                Helper::redirect('index.php?url=admin-manage-user');
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $data = [
                'role'     => $_POST['role'],
                'username' => trim($_POST['username']),
                'email'    => $email,
                'password' => $hashedPassword
            ];

            $userId = $this->userModel->create($data);
            
            if (!$userId) {
                throw new Exception("Failed to create user.");
            }

            // Admin wants to create the employee profile immediately
            if (!empty($_POST['create_employee_profile'])) {

                $_SESSION['success'] = "User account created. Please complete the employee profile.";

                Helper::redirect("index.php?url=employee-create&user_id={$userId}");
                exit;
            }

            // Employee will complete the profile after first login
            $_SESSION['success'] = "User account created successfully. The employee can complete their profile after their first login.";

            Helper::redirect('index.php?url=admin-manage-user');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            error_log($e->getMessage());

            Helper::redirect('index.php?url=admin-manage-user');
            exit;
        }
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
