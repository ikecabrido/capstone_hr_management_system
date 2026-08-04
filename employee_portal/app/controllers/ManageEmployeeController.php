<?php
require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Departments.php';

class ManageEmployeeController
{
    private $userModel;
    private $employeeModel;
    private $departmentModel;

    public function __construct()
    {
        $this->userModel = new Users();
        $this->employeeModel = new Employee();
        $this->departmentModel = new Departments();
    }
    public function adminIndex()
    {
        $currentUserId = $_SESSION['user_id'];

        $departmentHRInfos = $this->departmentModel->getDepartments();
        $positionHRInfos = $this->employeeModel->getPositions();

        $existingEmployees = $this->employeeModel->all();
        $existingUsers = $this->userModel->filterSelf($currentUserId);

        $statistics = $this->calculateEmployeeStatistics($existingEmployees);

        $totalEmployees  = $statistics['totalEmployees'];
        $activeEmployees = $statistics['activeEmployees'];
        $pendingProfiles = $statistics['pendingProfiles'];
        $newEmployees    = $statistics['newEmployees'];

        $title = "Employee Management";
        $content = __DIR__ . '/../views/admin/employee-management/main-content.php';

        require __DIR__ . '/../views/admin/index.php';
    }
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
    public function hrCreate()
    {
        $employeeId = filter_input(INPUT_GET, 'employee_id', FILTER_VALIDATE_INT);

        if (!$employeeId) {
            $_SESSION['error'] = "Invalid employee.";
            Helper::redirect('index.php?url=admin-manage-user');
            exit;
        }

        $employeeHRInfo = $this->employeeModel->find($employeeId);

        if (!$employeeHRInfo) {
            $_SESSION['error'] = "Employee not found.";
            Helper::redirect('index.php?url=admin-manage-user');
            exit;
        }


        // Dropdown data
        $departmentHRInfos = $this->departmentModel->getDepartments();
        $positionHRInfos = $this->employeeModel->getPositions();

        $title = "Fill Employment Information";
        $content = __DIR__ . '/../views/admin/employee/hr-create.php';

        require __DIR__ . '/../views/admin/index.php';
    }
    public function hrStore()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('index.php?url=admin-manage-user');
            exit;
        }

        $data = [
            'employee_id'         => (int) ($_POST['employee_id'] ?? 0),
            'employee_code'       => trim(("EMP-" . $_POST['employee_code']) ?? ''),
            'department_id'       => !empty($_POST['department_id']) ? (int) $_POST['department_id'] : null,
            'position_id'         => !empty($_POST['position_id']) ? (int) $_POST['position_id'] : null,
            'position_title_enum' => !empty($_POST['position_title_enum']) ? trim($_POST['position_title_enum']) : null,
            'employment_status'   => trim($_POST['employment_status'] ?? ''),
            'employment_type'     => trim($_POST['employment_type'] ?? ''),
            'hire_date'           => trim($_POST['hire_date'] ?? ''),
            'regular_date'        => !empty($_POST['regular_date']) ? $_POST['regular_date'] : null,
            'unit_load'           => !empty($_POST['unit_load']) ? (int) $_POST['unit_load'] : null,
            'faculty_notes'       => !empty($_POST['faculty_notes']) ? trim($_POST['faculty_notes']) : null,
        ];

        if ($this->employeeModel->hrStore($data)) {
            $_SESSION['success'] = 'Employment information has been saved successfully.';
        } else {
            $_SESSION['error'] = 'Failed to save employment information.';
        }

        Helper::redirect('index.php?url=admin-manage-user');
        exit;
    }
    private function calculateEmployeeStatistics(array $employees): array
    {
        $statistics = [
            'totalEmployees'  => count($employees),
            'activeEmployees' => 0,
            'pendingProfiles' => 0,
            'newEmployees'    => 0,
        ];

        foreach ($employees as $employee) {

            // Active Employees
            if (
                !empty($employee['employment_status']) &&
                in_array($employee['employment_status'], [
                    'Regular',
                    'Probationary',
                    'Contractual'
                ])
            ) {
                $statistics['activeEmployees']++;
            }

            // HR Profile Complete
            $isComplete =
                !empty($employee['employee_code']) &&
                !empty($employee['department_id']) &&
                !empty($employee['position_id']) &&
                !empty($employee['position_title_enum']) &&
                !empty($employee['employment_status']) &&
                !empty($employee['employment_type']) &&
                !empty($employee['hire_date']);

            if (!$isComplete) {
                $statistics['pendingProfiles']++;
            }

            // New Hires This Month
            if (!empty($employee['hire_date'])) {

                $hireDate = strtotime($employee['hire_date']);

                if (
                    date('Y', $hireDate) == date('Y') &&
                    date('m', $hireDate) == date('m')
                ) {
                    $statistics['newEmployees']++;
                }
            }
        }

        return $statistics;
    }
    public function hrStorePending()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('index.php?url=employee-management');
            exit;
        }

        $data = [
            'employee_id'         => (int) ($_POST['employee_id'] ?? 0),
            'employee_code'       => !empty($_POST['employee_code']) ? 'EMP-' . trim($_POST['employee_code']) : null,
            'department_id'       => !empty($_POST['department_id']) ? (int) $_POST['department_id'] : null,
            'position_id'         => !empty($_POST['position_id']) ? (int) $_POST['position_id'] : null,
            'position_title_enum' => !empty($_POST['position_title_enum']) ? trim($_POST['position_title_enum']) : null,
            'employment_status'   => !empty($_POST['employment_status']) ? trim($_POST['employment_status']) : null,
            'employment_type'     => !empty($_POST['employment_type']) ? trim($_POST['employment_type']) : null,
            'hire_date'           => !empty($_POST['hire_date']) ? $_POST['hire_date'] : null,
            'regular_date'        => !empty($_POST['regular_date']) ? $_POST['regular_date'] : null,
            'unit_load'           => !empty($_POST['unit_load']) ? (int) $_POST['unit_load'] : null,
            'faculty_notes'       => !empty($_POST['faculty_notes']) ? trim($_POST['faculty_notes']) : null,
        ];
        
        if (empty($data['employee_id'])) {
            $_SESSION['error'] = 'Invalid employee.';
            Helper::redirect('index.php?url=employee-management');
            exit;
        }

        if ($this->employeeModel->hrStorePending($data)) {

            $_SESSION['success'] = 'Employment information updated successfully.';
        } else {

            $_SESSION['error'] = 'No changes were made or update failed.';
        }

        Helper::redirect('index.php?url=employee-hr-index');
    }
    public function hrUpdate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('index.php?url=admin-manage-user');
            exit;
        }

        $data = [
            'employee_id'         => (int) ($_POST['employee_id'] ?? 0),
            'employee_code'       => !empty($_POST['employee_code']) ? trim('EMP-' . $_POST['employee_code']) : null,
            'department_id'       => !empty($_POST['department_id']) ? (int) $_POST['department_id'] : null,
            'position_id'         => !empty($_POST['position_id']) ? (int) $_POST['position_id'] : null,
            'position_title_enum' => !empty($_POST['position_title_enum']) ? trim($_POST['position_title_enum']) : null,
            'employment_status'   => !empty($_POST['employment_status']) ? trim($_POST['employment_status']) : null,
            'employment_type'     => !empty($_POST['employment_type']) ? trim($_POST['employment_type']) : null,
            'hire_date'           => !empty($_POST['hire_date']) ? $_POST['hire_date'] : null,
            'regular_date'        => !empty($_POST['regular_date']) ? $_POST['regular_date'] : null,
            'unit_load'           => $_POST['unit_load'] !== '' ? (int) $_POST['unit_load'] : null,
            'faculty_notes'       => !empty($_POST['faculty_notes']) ? trim($_POST['faculty_notes']) : null,
        ];

        $errors = [];

        if ($data['employee_id'] <= 0) {
            $errors[] = 'Invalid employee.';
        }

        if (!$this->employeeModel->find($data['employee_id'])) {
            $errors[] = 'Employee record not found.';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            Helper::redirect('index.php?url=employee-hr-index');
            exit;
        }

        if ($this->employeeModel->hrUpdate($data)) {
            $_SESSION['success'] = 'Employment information updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update employment information.';
        }

        Helper::redirect('index.php?url=employee-hr-index');
    }
    public function employeeProfileUpdate()
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

            $namePattern = "/^[a-zA-Z\s'-]+$/";

            if (!preg_match($namePattern, $firstName)) {
                throw new Exception("First Name contains invalid characters.");
            }

            if ($middleName !== '' && !preg_match($namePattern, $middleName)) {
                throw new Exception("Middle Name contains invalid characters.");
            }

            if (!preg_match($namePattern, $lastName)) {
                throw new Exception("Last Name contains invalid characters.");
            }

            if ($suffix !== '' && !preg_match($namePattern, $suffix)) {
                throw new Exception("Suffix contains invalid characters.");
            }

            $birth = new DateTime($birthDate);
            $today = new DateTime();

            if ($today->diff($birth)->y < 18) {
                throw new Exception("Employee must be at least 18 years old.");
            }

            /*
        |--------------------------------------------------------------------------
        | Existing Employee
        |--------------------------------------------------------------------------
        */

            $employee = $this->employeeModel->findByUserId($_POST['user_id']);

            if (!$employee) {
                throw new Exception("Employee profile not found.");
            }

            /*
        |--------------------------------------------------------------------------
        | Prepare Data
        |--------------------------------------------------------------------------
        */

            $data = [
                'user_id'           => $_POST['user_id'],
                'first_name'        => $firstName,
                'middle_name'       => $middleName ?: null,
                'last_name'         => $lastName,
                'suffix'            => $suffix ?: null,
                'gender'            => $_POST['gender'] ?: null,
                'birth_date'        => $birthDate,
                'birth_place'       => trim($_POST['birth_place']) ?: null,
                'civil_status'      => $_POST['civil_status'] ?: null,
                'citizenship'       => trim($_POST['citizenship']) ?: null,
                'religion'          => $_POST['religion'] ?: null,
                'mobile_no'         => trim($_POST['mobile_no']) ?: null,
                'phone_no'          => trim($_POST['phone_no']) ?: null,
                'current_address'   => trim($_POST['current_address']) ?: null,
                'permanent_address' => trim($_POST['permanent_address']) ?: null,
                'credentials'       => trim($_POST['credentials']) ?: null,
                'graduate_level'    => $_POST['graduate_level'] ?: null,
                'profile_image'     => $employee['profile_image'] // keep existing image
            ];

            /*
|--------------------------------------------------------------------------
| Upload New Image
|--------------------------------------------------------------------------
*/

            if (!empty($_FILES['profile_image']['name'])) {

                $allowed = ['jpg', 'jpeg', 'png', 'webp'];

                $extension = strtolower(
                    pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION)
                );

                if (!in_array($extension, $allowed)) {
                    throw new Exception("Only JPG, JPEG, PNG, and WEBP images are allowed.");
                }

                $filename = uniqid('employee_') . '.' . $extension;

                $uploadDir = __DIR__ . '/../../public/uploads/profile/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $destination = $uploadDir . $filename;

                if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                    throw new Exception("Failed to upload profile image.");
                }

                // Delete old image
                if (!empty($employee['profile_image'])) {

                    $oldImage = $uploadDir . $employee['profile_image'];

                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }

                $data['profile_image'] = $filename;
            }

            /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

            if (!$this->employeeModel->updateProfile($data)) {
                throw new Exception("Failed to update employee profile.");
            }

            $_SESSION['success'] = "Employee profile updated successfully.";
        } catch (Exception $e) {

            $_SESSION['error'] = $e->getMessage();
            error_log($e->getMessage());
        }

        Helper::redirect('index.php?url=user-profile');
    }
}
