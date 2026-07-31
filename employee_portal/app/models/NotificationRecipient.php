<?php

require_once __DIR__ . '/../config/Database.php';

class NotificationRecipient
{
    private $conn;
    private $table = "ep_notification_recipients";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createRecipients($notificationId, array $employeeIds)
    {
        $employeeIds = array_unique($employeeIds);

        $query = "
        INSERT INTO {$this->table}
        (notification_id, employee_id)
        VALUES (:notification_id, :employee_id)
    ";

        $stmt = $this->conn->prepare($query);

        foreach ($employeeIds as $employeeId) {

            $stmt->execute([
                ':notification_id' => $notificationId,
                ':employee_id' => $employeeId
            ]);
        }

        return true;
    }

    public function getEmployeeIdsByNotification($notificationId)
    {
        $query = "SELECT employee_id
              FROM {$this->table}
              WHERE notification_id = :notification_id";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':notification_id' => $notificationId
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function deleteRecipients($notificationId)
    {
        $query = "DELETE FROM {$this->table}
              WHERE notification_id = :notification_id";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':notification_id' => $notificationId
        ]);
    }

    public function getRecipients($notificationId)
    {
        $query = "SELECT
                e.employee_code,
                e.first_name,
                e.last_name,
                e.department
              FROM ep_notification_recipients r
              INNER JOIN employees e
                ON r.employee_id = e.employee_id
              WHERE r.notification_id = :notification_id
              ORDER BY e.first_name ASC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':notification_id' => $notificationId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function markAsRead($notificationId, $employeeId)
    {
        $query = "
        UPDATE ep_notification_recipients
        SET is_read = 1,
            read_at = NOW()
        WHERE notification_id = :notification_id
        AND employee_id = :employee_id
    ";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':notification_id' => $notificationId,
            ':employee_id' => $employeeId
        ]);
    }

    public function markAllAsRead($employeeId)
    {
        $query = "
        UPDATE ep_notification_recipients
        SET is_read = 1,
            read_at = NOW()
        WHERE employee_id = :employee_id
        AND is_read = 0
    ";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':employee_id' => $employeeId
        ]);
    }
}
