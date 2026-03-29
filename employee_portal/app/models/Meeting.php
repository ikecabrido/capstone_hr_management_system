<?php
require_once __DIR__ . '/../config/Database.php';


class Meeting
{

    private $conn;
    private $table = 'meetings';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function getAll()
    {
        $query = "SELECT * FROM meetings ORDER BY scheduled_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data)
    {
        $query = "INSERT INTO meetings 
        (title, meeting_link, created_by, employee_no, scheduled_at)
        VALUES 
        (:title, :meeting_link, :created_by, :employee_no, :scheduled_at)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':title' => $data['title'],
            ':meeting_link' => $data['meeting_link'],
            ':created_by' => $data['created_by'],
            ':employee_no' => $data['employee_no'],
            ':scheduled_at' => $data['scheduled_at']
        ]);
    }

    public function update($data)
    {
        $query = "UPDATE meetings 
              SET title = :title, 
                  scheduled_at = :scheduled_at
              WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':id' => $data['id'],
            ':title' => $data['title'],
            ':scheduled_at' => $data['scheduled_at']
        ]);
    }

    public function delete($id)
    {
        $query = "DELETE FROM meetings WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}
