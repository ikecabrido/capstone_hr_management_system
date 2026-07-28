<?php

require_once __DIR__ . '/../models/PayrollRequest.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../core/Helper.php';

class PayrollRequestController
{
    private $payrollRequestModel;
    private $employeeModel;
    private $userModel;

    public function __construct()
    {
        $this->payrollRequestModel = new PayrollRequest();
        $this->employeeModel = new Employee();
        $this->userModel = new User();
    }
    public function store()
    {
        Session::start();

        if (isset($_SESSION['user']) && !isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = $_SESSION['user']['id'];
            $_SESSION['employee_id'] = $_SESSION['user']['employee_id'] ?? null;
            $_SESSION['username'] = $_SESSION['user']['username'] ?? null;
            $_SESSION['name'] = $_SESSION['user']['name'] ?? null;
            $_SESSION['full_name'] = $_SESSION['user']['name'] ?? null;
            $_SESSION['role'] = $_SESSION['user']['role'] ?? null;
            $_SESSION['theme'] = $_SESSION['user']['theme'] ?? null;
        }

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            Helper::redirect('index.php?url=auth-index');
            exit;
        }

        try {
            $user = $this->userModel->findById($userId);

            if (!$user) {
                throw new Exception('User account could not be found.');
            }

            $isAdmin = (int)($user['is_admin'] ?? 0) === 1;

            $requestType = trim($_POST['request_type'] ?? '');
            $purpose = trim($_POST['purpose'] ?? '');
            $remarks = trim($_POST['remarks'] ?? '');

            $payrollPeriodStart = !empty($_POST['payroll_period_start'])
                ? $_POST['payroll_period_start']
                : null;

            $payrollPeriodEnd = !empty($_POST['payroll_period_end'])
                ? $_POST['payroll_period_end']
                : null;

            if (empty($requestType)) {
                throw new Exception('Please select a payroll document.');
            }

            if (empty($purpose)) {
                throw new Exception('Please provide the purpose of your request.');
            }

            if (
                $payrollPeriodStart &&
                $payrollPeriodEnd &&
                $payrollPeriodStart > $payrollPeriodEnd
            ) {
                throw new Exception(
                    'Payroll period start date cannot be later than the end date.'
                );
            }

            if ($isAdmin) {
                $employeeId = (int)($_POST['employee_id'] ?? 0);

                if (!$employeeId) {
                    throw new Exception('Please select an employee.');
                }

                $employee = $this->employeeModel->find($employeeId);

                if (!$employee) {
                    throw new Exception('Selected employee could not be found.');
                }
            } else {
                $employeeId = $_SESSION['employee_id'] ?? null;

                if (!$employeeId) {
                    $employee = $this->employeeModel->findByUserId($userId);

                    if (!$employee) {
                        throw new Exception('Employee record could not be found.');
                    }

                    $employeeId = $employee['id'];
                }
            }

            $saved = $this->payrollRequestModel->create([
                'employee_id' => $employeeId,
                'request_type' => $requestType,
                'purpose' => $purpose,
                'remarks' => $remarks ?: null,
                'payroll_period_start' => $payrollPeriodStart,
                'payroll_period_end' => $payrollPeriodEnd
            ]);

            if (!$saved) {
                throw new Exception('Unable to submit payroll request.');
            }

            Session::set(
                'success',
                'Payroll request submitted successfully.'
            );

            if ($isAdmin) {
                Helper::redirect('index.php?url=admin-payroll-request-view');
            } else {
                Helper::redirect('index.php?url=employee-payroll-request');
            }

            exit;
        } catch (Exception $e) {
            Session::set('error', $e->getMessage());

            if ($isAdmin ?? false) {
                Helper::redirect('index.php?url=admin-payroll-request-view');
            } else {
                Helper::redirect('index.php?url=employee-payroll-request');
            }

            exit;
        }
    }
    public function adminIndex()
    {
        Session::start();

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            Helper::redirect('index.php?url=auth-index');
            exit;
        }

        $requests = $this->payrollRequestModel->all();
        $employees = $this->employeeModel->all();

        $totalRequests = $this->payrollRequestModel->countAll();
        $pendingRequests = $this->payrollRequestModel->countByStatus('Pending');
        $processingRequests = $this->payrollRequestModel->countByStatus('Processing');
        $approvedRequests = $this->payrollRequestModel->countByStatus('Approved');
        $rejectedRequests = $this->payrollRequestModel->countByStatus('Rejected');
        $completedRequests = $this->payrollRequestModel->countByStatus('Completed');

        $title = "Payroll Requests";
        $content = __DIR__ . '/../views/admin/payroll-request/main-content.php';

        require __DIR__ . '/../views/admin/index.php';
    }
    public function updateStatus()
    {
        Session::start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('index.php?url=admin-payroll-request');
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            Helper::redirect('index.php?url=auth-index');
            exit;
        }

        try {
            $requestId = (int) ($_POST['id'] ?? 0);
            $status = trim($_POST['status'] ?? '');

            $allowedStatuses = [
                'Pending',
                'Processing',
                'Approved',
                'Rejected',
                'Completed',
                'Cancelled'
            ];

            if ($requestId <= 0) {
                throw new Exception('Invalid payroll request.');
            }

            if (!in_array($status, $allowedStatuses, true)) {
                throw new Exception('Invalid request status.');
            }

            $updated = $this->payrollRequestModel->updateStatus(
                $requestId,
                $status,
                $userId
            );

            if (!$updated) {
                throw new Exception('Unable to update the payroll request status.');
            }

            Session::set(
                'success',
                'Payroll request status updated successfully.'
            );
        } catch (Exception $e) {
            Session::set('error', $e->getMessage());
        }

        Helper::redirect('index.php?url=admin-payroll-request');
        exit;
    }
    public function delete()
    {
        Session::start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('index.php?url=admin-payroll-request');
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            Helper::redirect('index.php?url=auth-index');
            exit;
        }

        try {
            $requestId = (int) ($_POST['id'] ?? 0);

            if ($requestId <= 0) {
                throw new Exception('Invalid payroll request.');
            }

            $deleted = $this->payrollRequestModel->delete($requestId);

            if (!$deleted) {
                throw new Exception('Unable to delete the payroll request.');
            }

            Session::set(
                'success',
                'Payroll request deleted successfully.'
            );
        } catch (Exception $e) {
            Session::set('error', $e->getMessage());
        }

        Helper::redirect('index.php?url=admin-payroll-request');
        exit;
    }
    public function employeeIndex()
    {
        Session::start();

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            Helper::redirect('index.php?url=auth-index');
            exit;
        }

        $employee = $this->employeeModel->findByUserId($userId);

        if (!$employee) {
            Session::set('error', 'Employee record could not be found.');
            Helper::redirect('index.php?url=dashboard');
            exit;
        }

        $employeeId = $employee['id'];
        $requests = $this->payrollRequestModel->getByEmployeeId($employeeId);

        $title = "Payroll Requests";
        $content = __DIR__ . '/../views/payroll-request/main-content.php';

        require __DIR__ . '/../views/employee-portal/index.php';
    }
}
