<?php

require_once __DIR__ . '/../config/Database.php';

class LearningAndDevelopment
{
    private $conn;
    private $table = "ld_training_programs";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function getAll()
    {
        $sql = "SELECT *
                FROM {$this->table}
                ORDER BY start_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUpcoming()
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE start_date >= CURDATE()
                ORDER BY start_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getCompleted()
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE end_date < CURDATE()
                ORDER BY end_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTrainingRecords($employee_id)
    {
        $sql = "SELECT
                r.ld_request_id,
                r.employee_id,
                r.request_status,
                r.received_at,
                r.requested_program,
                r.requested_course,
                r.request_reason,

                p.title,
                p.trainer,
                p.start_date,
                p.end_date,
                p.description

            FROM ld_training_requests r

            LEFT JOIN ld_training_programs p
                ON r.ld_training_program_id = p.ld_training_programs_id

            WHERE r.employee_id = :employee_id

            ORDER BY p.start_date ASC, r.received_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':employee_id' => $employee_id
        ]);

        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($records as &$row) {

            // Use request information when no training program is linked
            if (empty($row['title'])) {
                $row['title'] = $row['requested_program'];
            }

            if (empty($row['trainer'])) {
                $row['trainer'] = $row['requested_course'];
            }

            if (empty($row['description'])) {
                $row['description'] = $row['request_reason'];
            }

            if (empty($row['start_date'])) {
                $row['start_date'] = $row['received_at'];
            }

            if (empty($row['end_date'])) {
                $row['end_date'] = $row['received_at'];
            }
        }

        return $records;
    }
}
