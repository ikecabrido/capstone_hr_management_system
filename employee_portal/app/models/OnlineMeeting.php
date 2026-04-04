<?php
require_once __DIR__ . '/../config/Database.php';


class OnlineMeeting
{

    private $conn;
    private $table = 'ep_online_meetings';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function getAll()
    {
        $query = "SELECT * FROM {$this->table} ORDER BY scheduled_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data)
    {
        $query = "INSERT INTO {$this->table}  
        (title, meeting_link, created_by, employee_id, scheduled_at)
        VALUES 
        (:title, :meeting_link, :created_by, :employee_id, :scheduled_at)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':title' => $data['title'],
            ':meeting_link' => $data['meeting_link'],
            ':created_by' => $data['created_by'],
            ':employee_id' => $data['employee_id'],
            ':scheduled_at' => $data['scheduled_at']
        ]);
    }

    public function update($data)
    {
        $query = "UPDATE {$this->table}  
              SET title = :title, 
                  scheduled_at = :scheduled_at
              WHERE meetings_id = :id";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':id' => $data['id'],
            ':title' => $data['title'],
            ':scheduled_at' => $data['scheduled_at']
        ]);
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table}  WHERE meetings_id = :id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}
