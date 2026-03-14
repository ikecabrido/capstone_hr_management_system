<?php
require_once __DIR__ . '/../Models/User.php';

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Core/Auth.php';

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
}
