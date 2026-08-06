<?php
require_once __DIR__ . '/../config/Database.php';

class Incidents
{
    private $conn;
    private $table = "lc_incidents";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function getLatestIncidentId($year)
    {
        $stmt = $this->conn->prepare("
        SELECT incident_id
        FROM {$this->table}
        WHERE incident_id LIKE ?
        ORDER BY id DESC
        LIMIT 1
    ");

        $stmt->execute(["INC-$year-%"]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data)
    {
        $sql = "
        INSERT INTO {$this->table} (

            incident_id,

            reporter_id,
            reporter_employee_id,
            reporter_name,
            reporter_department,
            reporter_position,
            reporter_contact,
            reporter_role,
            reporter_type,

            respondent_id,
            respondent_employee_id,
            respondent_name,
            respondent_department,
            respondent_position,
            respondent_relationship,

            incident_type,
            type,
            severity,

            incident_date,
            incident_time,
            location,

            title,
            description,

            status,
            assigned_to,

            created_by,
            reported_by,
            resolved_at

        ) VALUES (

            :incident_id,

            :reporter_id,
            :reporter_employee_id,
            :reporter_name,
            :reporter_department,
            :reporter_position,
            :reporter_contact,
            :reporter_role,
            :reporter_type,

            :respondent_id,
            :respondent_employee_id,
            :respondent_name,
            :respondent_department,
            :respondent_position,
            :respondent_relationship,

            :incident_type,
            :type,
            :severity,

            :incident_date,
            :incident_time,
            :location,

            :title,
            :description,

            :status,
            :assigned_to,

            :created_by,
            :reported_by,
            :resolved_at
        )
    ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($data);
    }
    public function getByUser($userId)
    {
        $sql = "SELECT *
            FROM lc_incidents
            WHERE created_by = :user_id
            ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
