<?php

require_once __DIR__ . '/../config/Database.php';

class TrainingRequest
{
    private $conn;
    private $table = "ld_training_requests";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function all()
    {
        $sql = "SELECT
                r.*,
                e.full_name,
                p.title AS training_title,
                p.trainer
            FROM {$this->table} r
            LEFT JOIN employees e
                ON r.employee_id = e.id
            LEFT JOIN ld_training_programs p
                ON r.ld_training_program_id = p.ld_training_programs_id
            ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data)
    {
        try {

            $sql = "INSERT INTO {$this->table} (
                    employee_user_id,
                    employee_id,
                    goal_id,
                    kpi_name,
                    request_reason,
                    requested_program,
                    requested_course,
                    ld_training_program_id,
                    ld_course_id,
                    request_status,
                    received_at,
                    created_at,
                    updated_at
                )
                VALUES (
                    :employee_user_id,
                    :employee_id,
                    :goal_id,
                    :kpi_name,
                    :request_reason,
                    :requested_program,
                    :requested_course,
                    :ld_training_program_id,
                    :ld_course_id,
                    'New',
                    NOW(),
                    NOW(),
                    NOW()
                )";

            $stmt = $this->conn->prepare($sql);

            $success = $stmt->execute([
                ':employee_user_id'       => $data['employee_user_id'],
                ':employee_id'            => $data['employee_id'],
                ':goal_id'                => $data['goal_id'],
                ':kpi_name'               => $data['kpi_name'],
                ':request_reason'         => $data['request_reason'],
                ':requested_program'      => $data['requested_program'],
                ':requested_course'       => $data['requested_course'],
                ':ld_training_program_id' => $data['ld_training_program_id'],
                ':ld_course_id'           => $data['ld_course_id']
            ]);

            if (!$success) {
                throw new Exception('Unable to save the training request.');
            }

            return true;
        } catch (PDOException $e) {

            throw new Exception('Database error: ' . $e->getMessage());
        }
    }
    public function update($id, $data)
    {
        $query = "UPDATE ld_training_requests SET
                requested_program = :requested_program,
                requested_course = :requested_course,
                goal_id = :goal_id,
                kpi_name = :kpi_name,
                request_reason = :request_reason,
                request_status = :request_status
              WHERE ld_request_id = :ld_request_id";


        $stmt = $this->conn->prepare($query);


        return $stmt->execute([

            ':requested_program' => $data['requested_program'],

            ':requested_course' => $data['requested_course'],

            ':goal_id' => $data['goal_id'],

            ':kpi_name' => $data['kpi_name'],

            ':request_reason' => $data['request_reason'],

            ':request_status' => $data['request_status'],

            ':ld_request_id' => $id

        ]);
    }
    public function delete($id)
    {
        $query = "DELETE FROM ld_training_requests
              WHERE ld_request_id = :id";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
    public function getByEmployeeId($employee_id)
    {
        $query = "
        SELECT *
        FROM ld_training_requests
        WHERE employee_id = :employee_id
        ORDER BY created_at DESC
    ";


        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':employee_id' => $employee_id
        ]);


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
