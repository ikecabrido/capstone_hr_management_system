<?php
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/TrainingRequest.php';
class TrainingRequestController
{
    private $employeeModel;
    private $trainingRequestModel;
    public function __construct()
    {
        $this->employeeModel = new Employee();
        $this->trainingRequestModel = new TrainingRequest();
    }
    public function index()
    {
        $employees = $this->employeeModel->all();
        $trainingRequests = $this->trainingRequestModel->all();

        $title = "Training Requests";

        $content = __DIR__ . '/../views/admin/training-request/main-content.php';

        require __DIR__ . '/../views/admin/index.php';
    }
    public function adminCreate()
    {
        try {

            if (empty($_POST['employee_id'])) {
                throw new Exception("Please select an employee.");
            }


            $employee = $this->employeeModel->getByEmployeeId(
                $_POST['employee_id']
            );


            if (!$employee) {
                throw new Exception("Employee not found.");
            }


            $data = [
                'employee_user_id'       => $employee['user_id'],
                'employee_id'            => $employee['id'],
                'goal_id'                => !empty($_POST['goal_id'])
                    ? $_POST['goal_id']
                    : null,
                'kpi_name'               => !empty($_POST['kpi_name'])
                    ? trim($_POST['kpi_name'])
                    : null,
                'request_reason'         => trim($_POST['request_reason']),
                'requested_program'      => trim($_POST['requested_program']),
                'requested_course'       => trim($_POST['requested_course']),
                'ld_training_program_id' => null,
                'ld_course_id'           => null
            ];


            if (!$this->trainingRequestModel->create($data)) {
                throw new Exception("Failed to create training request.");
            }


            $_SESSION['success'] = "Training request submitted successfully.";
        } catch (Exception $e) {

            $_SESSION['error'] = $e->getMessage();

            error_log($e->getMessage());
        }


        header(
            'Location: index.php?url=admin-training-request'
        );

        exit;
    }

    public function update()
    {
        try {

            if (empty($_POST['ld_request_id'])) {
                throw new Exception("Invalid training request.");
            }


            $data = [

                'requested_program' => trim($_POST['requested_program']),

                'requested_course' => trim($_POST['requested_course']),

                'goal_id' => !empty($_POST['goal_id'])
                    ? $_POST['goal_id']
                    : null,

                'kpi_name' => !empty($_POST['kpi_name'])
                    ? trim($_POST['kpi_name'])
                    : null,

                'request_reason' => trim($_POST['request_reason']),

                'request_status' => $_POST['request_status']

            ];


            if (!$this->trainingRequestModel->update(
                $_POST['ld_request_id'],
                $data
            )) {

                throw new Exception(
                    "Failed to update training request."
                );
            }


            $_SESSION['success'] =
                "Training request updated successfully.";
        } catch (Exception $e) {


            $_SESSION['error'] = $e->getMessage();

            error_log($e->getMessage());
        }


        header(
            'Location: index.php?url=admin-training-request'
        );

        exit;
    }
    public function delete()
    {
        try {

            if (empty($_POST['ld_request_id'])) {
                throw new Exception("Invalid training request.");
            }


            if (!$this->trainingRequestModel->delete(
                $_POST['ld_request_id']
            )) {

                throw new Exception(
                    "Failed to delete training request."
                );
            }


            $_SESSION['success'] =
                "Training request deleted successfully.";
        } catch (Exception $e) {

            $_SESSION['error'] = $e->getMessage();

            error_log($e->getMessage());
        }


        header(
            'Location: index.php?url=admin-training-request'
        );

        exit;
    }

    public function employeeIndex()
    {


        $employee = $this->employeeModel->findByUserId(
            $_SESSION['user_id']
        );


        $trainingRequests = [];

        if ($employee) {

            $trainingRequests = $this->trainingRequestModel
                ->getByEmployeeId($employee['id']);
        }

        $title = "Training Requests";
        $content = __DIR__ . '/../views/training-request/main-content.php';

        require __DIR__ . '/../views/employee-portal/index.php';
    }

    public function employeeCreate()
    {
        try {

            // Get logged-in employee
            $employee = $this->employeeModel->findByUserId(
                $_SESSION['user_id']
            );


            if (!$employee) {
                throw new Exception("Employee not found.");
            }


            $data = [

                'employee_user_id'       => $employee['user_id'],

                'employee_id'            => $employee['id'],

                'goal_id'                => !empty($_POST['goal_id'])
                    ? $_POST['goal_id']
                    : null,

                'kpi_name'               => !empty($_POST['kpi_name'])
                    ? trim($_POST['kpi_name'])
                    : null,

                'request_reason'         => trim($_POST['request_reason']),

                'requested_program'      => trim($_POST['requested_program']),

                'requested_course'       => trim($_POST['requested_course']),

                'ld_training_program_id' => null,

                'ld_course_id'           => null

            ];



            if (!$this->trainingRequestModel->create($data)) {

                throw new Exception(
                    "Failed to submit training request."
                );
            }



            $_SESSION['success'] =
                "Training request submitted successfully.";
        } catch (Exception $e) {


            $_SESSION['error'] =
                $e->getMessage();


            error_log($e->getMessage());
        }



        header(
            'Location: index.php?url=training-request'
        );


        exit;
    }
}
