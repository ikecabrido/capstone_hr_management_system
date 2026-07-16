<?php
require_once __DIR__ . '/../config/Database.php';

class Notification
{
    private $conn;
    private $table = "ep_notifications";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function all()
    {
        $query = "SELECT
                n.*,
                COUNT(r.recipient_id) AS recipient_count
              FROM ep_notifications n
              LEFT JOIN ep_notification_recipients r
                ON n.notification_id = r.notification_id
              GROUP BY n.notification_id
              ORDER BY n.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO {$this->table}
            (
                title,
                message,
                type,
                priority,
                created_by_user_id
            )
            VALUES
            (
                :title,
                :message,
                :type,
                :priority,
                :created_by_user_id
            )";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':title' => $data['title'],
            ':message' => $data['message'],
            ':type' => $data['type'],
            ':priority' => $data['priority'],
            ':created_by_user_id' => $data['created_by_user_id']
        ]);

        return $this->conn->lastInsertId();
    }

    public function find($notificationId)
    {
        $query = "SELECT *
              FROM {$this->table}
              WHERE notification_id = :notification_id
              LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':notification_id' => $notificationId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data)
    {
        $query = "UPDATE {$this->table}
                SET
                    title = :title,
                    message = :message,
                    type = :type,
                    priority = :priority
              WHERE notification_id = :notification_id";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':title' => $data['title'],
            ':message' => $data['message'],
            ':type' => $data['type'],
            ':priority' => $data['priority'],
            ':notification_id' => $data['notification_id']
        ]);
    }

    public function delete($notificationId)
    {
        $query = "DELETE
              FROM {$this->table}
              WHERE notification_id = :notification_id";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':notification_id' => $notificationId
        ]);
    }
}
