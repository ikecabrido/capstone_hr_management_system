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

    public function find($id)
    {
        $stmt = $this->conn->prepare("
        SELECT *
        FROM ep_notifications
        WHERE notification_id = :id
    ");

        $stmt->execute([
            ':id' => $id
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

    public function countUnread($employeeId)
    {
        $query = "
        SELECT COUNT(*) AS total
        FROM ep_notification_recipients
        WHERE employee_id = :employee_id
        AND is_read = 0
    ";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    public function latest($employeeId)
    {
        $query = "
        SELECT 
            n.notification_id,
            n.title,
            n.message,
            n.type,
            n.priority,
            n.created_at,
            r.is_read
        FROM ep_notifications n

        INNER JOIN ep_notification_recipients r
        ON n.notification_id = r.notification_id

        WHERE r.employee_id = :employee_id

        ORDER BY n.created_at DESC

        LIMIT 5
    ";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findForEmployee($notificationId, $employeeId)
    {
        $query = "
        SELECT n.*
        FROM ep_notifications n
        INNER JOIN ep_notification_recipients r
            ON r.notification_id = n.notification_id
        WHERE n.notification_id = :notification_id
        AND r.employee_id = :employee_id
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':notification_id' => $notificationId,
            ':employee_id' => $employeeId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getEmployeeNotifications($employeeId)
    {
        $query = "
        SELECT
            n.*,
            r.is_read,
            r.read_at
        FROM ep_notifications n
        INNER JOIN ep_notification_recipients r
            ON n.notification_id = r.notification_id
        WHERE r.employee_id = :employee_id
        ORDER BY n.created_at DESC
    ";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getRecipients($notificationId)
    {
        $sql = "
        SELECT
            e.employee_code,
            e.first_name,
            e.last_name,
            e.department
        FROM ep_notification_recipients r
        INNER JOIN employees e
            ON r.employee_id = e.employee_id
        WHERE r.notification_id = :id
        ORDER BY e.last_name, e.first_name
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id' => $notificationId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
