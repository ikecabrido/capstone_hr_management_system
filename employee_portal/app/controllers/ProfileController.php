<?php
require_once __DIR__ . '/../models/Profile.php';
class ProfileController
{
    private $profileModel;
    public function __construct()
    {
        $this->profileModel = new Profile();
    }
    public function index()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        $userInfos = $this->profileModel->findByUserId($user_id);

        $title = "Employee Profile";
        $content = __DIR__ . '/../views/profile/main-content.php';
        require __DIR__ . '/../views/profile/index.php';
    }
    public function updateName()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        $new_name = $_POST['full_name'] ?? null;

        try {
            if ($this->profileModel->updateName($user_id, $new_name)) {
                $_SESSION['success'] = "Full name updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update full name.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage() ?: "Something went wrong while updating the full name.";
        }

        $redirectTo = $_SERVER['HTTP_REFERER'] ?? "index.php?url=profile";
        header("Location: $redirectTo");
        exit;
    }
    public function changePassword()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        $password = $_POST['password'] ?? null;

        try {
            if (!$user_id || !$password) {
                $_SESSION['error'] = "Invalid request or missing password.";
            } elseif (strlen($password) < 6) {
                $_SESSION['error'] = "Password must be at least 6 characters.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                if ($this->profileModel->updatePassword($user_id, $hashed_password)) {
                    $_SESSION['success'] = "Password updated successfully.";
                } else {
                    $_SESSION['error'] = "Failed to update password.";
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage() ?: "Something went wrong while updating the password.";
        }

        $redirectTo = $_SERVER['HTTP_REFERER'] ?? "index.php?url=profile";
        header("Location: $redirectTo");
        exit;
    }
}
