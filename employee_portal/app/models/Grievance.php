<?php

require_once __DIR__ . '/../config/Database.php';

class Grievance
{
    private $conn;
    private $table = "eer_grievances";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function all()
    {
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getByEmployee($employeeId)
    {
        $sql = "SELECT *
            FROM eer_grievances
            WHERE employee_id = :employee_id
            ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':employee_id', $employeeId);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data)
    {
        $sql = "INSERT INTO eer_grievances (

                employee_id,
                subject,
                description,
                status,
                resolution_of_complaint,
                priority,
                category,
                anonymous,
                attachment_path,
                confidential,
                action_taken,
                satisfaction_rating,
                satisfaction_comment,
                resolved_at,
                escalation_level,
                escalation_reason,
                created_by_user_id,
                payslip_id,
                gross_pay,
                total_deductions,
                net_pay,
                payslip_information

            ) VALUES (

                :employee_id,
                :subject,
                :description,
                :status,
                :resolution_of_complaint,
                :priority,
                :category,
                :anonymous,
                :attachment_path,
                :confidential,
                :action_taken,
                :satisfaction_rating,
                :satisfaction_comment,
                :resolved_at,
                :escalation_level,
                :escalation_reason,
                :created_by_user_id,
                :payslip_id,
                :gross_pay,
                :total_deductions,
                :net_pay,
                :payslip_information

            )";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($data);
    }
}
