<?php

require_once "../app/Models/User.php";

require_once "../app/Core/Controller.php";
require_once "../app/Core/Auth.php";

class ProfileController extends Controller
{
    public function index()
    {
        Auth::requireLogin();

        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            header("Location: index.php?url=login");
            exit;
        }

        $userModel = new User();
        $user = $userModel->findById($userId);

        $this->view('profile/index', [
            'title' => 'My Profile | SEMSYS',
            'user'  => $user
        ]);
    }

    public function employeeProfile()
    {
        Auth::requireLogin();

        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            header("Location: index.php?url=login");
            exit;
        }

        $userModel = new User();
        $user = $userModel->findById($userId);

        $this->view('/profile/index', [
            'title' => 'My Profile | SEMSYS',
            'user'  => $user
        ]);
    }

    public function savePassword()
    {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=profile");
            exit;
        }

        $password = trim($_POST['password'] ?? '');

        if (!$password || strlen($password) < 8) {
            $_SESSION['error'] = "Password must be at least 8 characters.";
            header("Location: index.php?url=profile");
            exit;
        }

        $userModel = new User();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $updated = $userModel->updatePassword($_SESSION['user']['id'], $hash);

        if ($updated) {
            $userModel->updateIsNew($_SESSION['user']['id'], 0);  

            $_SESSION['success'] = "Password updated successfully!";

            $_SESSION['user']['isNew'] = 0;
        } else {
            $_SESSION['error'] = "Failed to update password. Try again.";
        }

        header("Location: index.php?url=profile");
        exit;
    }
}
