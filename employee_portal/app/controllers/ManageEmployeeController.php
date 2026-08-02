<?php
require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../models/Employee.php';

class ManageEmployeeController
{
    private $userModel;
    private $employeeModel;

    public function __construct()
    {
        $this->userModel = new Users();
        $this->employeeModel = new Employee();
    }
    public function index() {}

    public function createProfile()
    {
        $userId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

        if (!$userId) {
            $_SESSION['error'] = "Invalid user.";
            Helper::redirect('index.php?url=admin-manage-user');
            exit;
        }
        $user = $this->userModel->findById($userId);

        if (!$user) {
            $_SESSION['error'] = "User not found.";
            Helper::redirect('index.php?url=admin-manage-user');
            exit;
        }

        $employee = $this->employeeModel->findByUserId($userId);

        if ($employee !== false && !empty($employee)) {
            $_SESSION['error'] = "This user already has an employee profile.";
            Helper::redirect('index.php?url=admin-manage-user');
            exit;
        }

        $title = "Create Employee Profile";
        $content = __DIR__ . '/../views/admin/employee/create.php';

        require __DIR__ . '/../views/admin/index.php';
    }
    public function store()
    {
        try {

            /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

            $firstName = trim($_POST['first_name'] ?? '');
            $middleName = trim($_POST['middle_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $suffix = trim($_POST['suffix'] ?? '');
            $birthDate = $_POST['birth_date'] ?? '';

            if ($firstName === '' || $lastName === '' || $birthDate === '') {
                throw new Exception("First Name, Last Name, and Birth Date are required.");
            }

            // Allow letters, spaces, apostrophes, and hyphens only
            $namePattern = "/^[a-zA-Z\s'-]+$/";

            if (!preg_match($namePattern, $firstName)) {
                throw new Exception("First Name contains invalid characters.");
            }

            if ($middleName !== '' && !preg_match($namePattern, $middleName)) {
                throw new Exception("Middle Name contains invalid characters.");
            }

            if ($lastName !== '' && !preg_match($namePattern, $lastName)) {
                throw new Exception("Last Name contains invalid characters.");
            }

            if ($suffix !== '' && !preg_match($namePattern, $suffix)) {
                throw new Exception("Suffix contains invalid characters.");
            }

            // Validate age (18+)
            $birth = new DateTime($birthDate);
            $today = new DateTime();

            $age = $today->diff($birth)->y;

            if ($age < 18) {
                throw new Exception("Employee must be at least 18 years old.");
            }

            /*
        |--------------------------------------------------------------------------
        | Prepare Data
        |--------------------------------------------------------------------------
        */

            $data = [
                'user_id'            => $_POST['user_id'],
                'first_name'         => $firstName,
                'middle_name'        => $middleName ?: null,
                'last_name'          => $lastName,
                'suffix'             => $suffix ?: null,
                'gender'             => $_POST['gender'] ?: null,
                'birth_date'         => $birthDate,
                'birth_place'        => trim($_POST['birth_place']) ?: null,
                'civil_status'       => $_POST['civil_status'] ?: null,
                'citizenship'        => trim($_POST['citizenship']) ?: null,
                'religion'           => trim($_POST['religion']) ?: null,
                'mobile_no'          => trim($_POST['mobile_no']) ?: null,
                'phone_no'           => trim($_POST['phone_no']) ?: null,
                'current_address'    => trim($_POST['current_address']) ?: null,
                'permanent_address'  => trim($_POST['permanent_address']) ?: null,
                'credentials'        => trim($_POST['credentials']) ?: null,
                'graduate_level'     => $_POST['graduate_level'] ?: null,
                'profile_image'      => null,
                'created_by'         => $_SESSION['user_id']
            ];

            /*
        |--------------------------------------------------------------------------
        | Upload Profile Image
        |--------------------------------------------------------------------------
        */

            if (
                isset($_FILES['profile_image']) &&
                $_FILES['profile_image']['error'] === UPLOAD_ERR_OK
            ) {
                $extension = strtolower(
                    pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION)
                );

                $filename = uniqid('employee_') . '.' . $extension;

                $destination = __DIR__ . '/../../public/uploads/profile/' . $filename;

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                    $data['profile_image'] = $filename;
                }
            }

            if (!$this->employeeModel->createProfile($data)) {
                throw new Exception("Failed to create employee profile.");
            }

            $_SESSION['success'] = "Employee profile created successfully.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            error_log($e->getMessage());
        }

        Helper::redirect('index.php?url=admin-manage-user');
    }
}
